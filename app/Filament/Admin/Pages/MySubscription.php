<?php

namespace App\Filament\Admin\Pages;

use App\Models\Plan;
use App\Models\Subscription;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\BillingPortal\Session as BillingPortalSession;
use Stripe\Checkout\Session as CheckoutSession;

class MySubscription extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static string $view = 'filament.admin.pages.my-subscription';

    protected static ?string $navigationGroup = 'My Site';

    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'Subscription';

    protected static ?string $navigationLabel = 'Subscription';

    public ?string $selectedPlanId = null;
    public ?string $selectedBillingCycle = 'monthly';

    public static function canAccess(): bool
    {
        return auth()->user()?->isTenant() ?? false;
    }

    public function mount(): void
    {
        $subscription = $this->getSubscription();
        if ($subscription) {
            $this->selectedPlanId = (string) $subscription->plan_id;
            $this->selectedBillingCycle = $subscription->billing_cycle ?? 'monthly';
        }
    }

    protected function getSubscription(): ?Subscription
    {
        return auth()->user()->subscription;
    }

    public function getSubscriptionStatus(): string
    {
        return auth()->user()->subscription_status ?? 'incomplete';
    }

    public function getSubscriptionStatusColor(): string
    {
        return match ($this->getSubscriptionStatus()) {
            'active' => 'success',
            'trialing' => 'info',
            'past_due' => 'warning',
            'canceled', 'unpaid' => 'danger',
            default => 'gray',
        };
    }

    public function hasActiveSubscription(): bool
    {
        return in_array($this->getSubscriptionStatus(), ['active', 'trialing']);
    }

    public function getPlans()
    {
        return Plan::active()->ordered()->get();
    }

    public function getCurrentPlan(): ?Plan
    {
        return $this->getSubscription()?->plan;
    }

    public function selectPlan(int $planId, string $billingCycle = 'monthly'): void
    {
        $this->selectedPlanId = (string) $planId;
        $this->selectedBillingCycle = $billingCycle;
    }

    public function redirectToCheckout(): void
    {
        $plan = Plan::find($this->selectedPlanId);

        if (!$plan) {
            Notification::make()
                ->title('Please select a plan')
                ->danger()
                ->send();
            return;
        }

        $priceId = $plan->getStripePriceId($this->selectedBillingCycle);

        if (!$priceId) {
            Notification::make()
                ->title('This billing cycle is not available for this plan')
                ->danger()
                ->send();
            return;
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $user = auth()->user();

            // Create or retrieve Stripe customer
            if (!$user->stripe_customer_id) {
                $customer = \Stripe\Customer::create([
                    'email' => $user->email,
                    'name' => $user->name,
                    'metadata' => [
                        'user_id' => $user->id,
                        'subdomain' => $user->subdomain,
                    ],
                ]);

                $user->update(['stripe_customer_id' => $customer->id]);
            }

            $checkoutSession = CheckoutSession::create([
                'customer' => $user->stripe_customer_id,
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price' => $priceId,
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'subscription_data' => [
                    'trial_period_days' => $plan->trial_days,
                    'metadata' => [
                        'user_id' => $user->id,
                        'plan_id' => $plan->id,
                        'billing_cycle' => $this->selectedBillingCycle,
                    ],
                ],
                'success_url' => route('filament.admin.pages.my-subscription') . '?success=1',
                'cancel_url' => route('filament.admin.pages.my-subscription') . '?canceled=1',
                'metadata' => [
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                    'billing_cycle' => $this->selectedBillingCycle,
                ],
            ]);

            $this->redirect($checkoutSession->url);

        } catch (\Exception $e) {
            Log::error('Stripe checkout error: ' . $e->getMessage());

            Notification::make()
                ->title('Error creating checkout session')
                ->body('Please try again later.')
                ->danger()
                ->send();
        }
    }

    public function redirectToCustomerPortal(): void
    {
        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $user = auth()->user();

            if (!$user->stripe_customer_id) {
                Notification::make()
                    ->title('No billing account found')
                    ->danger()
                    ->send();
                return;
            }

            $session = BillingPortalSession::create([
                'customer' => $user->stripe_customer_id,
                'return_url' => route('filament.admin.pages.my-subscription'),
            ]);

            $this->redirect($session->url);

        } catch (\Exception $e) {
            Log::error('Stripe portal error: ' . $e->getMessage());

            Notification::make()
                ->title('Error accessing billing portal')
                ->body('Please try again later.')
                ->danger()
                ->send();
        }
    }

    public function cancelSubscription(): void
    {
        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $user = auth()->user();
            $subscription = $this->getSubscription();

            if (!$subscription || !$subscription->stripe_subscription_id) {
                Notification::make()
                    ->title('No active subscription found')
                    ->danger()
                    ->send();
                return;
            }

            $stripeSubscription = \Stripe\Subscription::update(
                $subscription->stripe_subscription_id,
                ['cancel_at_period_end' => true]
            );

            $subscription->update([
                'canceled_at' => now(),
                'ends_at' => \Carbon\Carbon::createFromTimestamp($stripeSubscription->current_period_end),
            ]);

            Notification::make()
                ->title('Subscription canceled')
                ->body('Your subscription will remain active until the end of the current billing period.')
                ->success()
                ->send();

        } catch (\Exception $e) {
            Log::error('Stripe cancel error: ' . $e->getMessage());

            Notification::make()
                ->title('Error canceling subscription')
                ->body('Please try again later.')
                ->danger()
                ->send();
        }
    }

    public function resumeSubscription(): void
    {
        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $subscription = $this->getSubscription();

            if (!$subscription || !$subscription->stripe_subscription_id) {
                Notification::make()
                    ->title('No subscription found')
                    ->danger()
                    ->send();
                return;
            }

            \Stripe\Subscription::update(
                $subscription->stripe_subscription_id,
                ['cancel_at_period_end' => false]
            );

            $subscription->update([
                'canceled_at' => null,
                'ends_at' => null,
            ]);

            Notification::make()
                ->title('Subscription resumed')
                ->body('Your subscription will continue as normal.')
                ->success()
                ->send();

        } catch (\Exception $e) {
            Log::error('Stripe resume error: ' . $e->getMessage());

            Notification::make()
                ->title('Error resuming subscription')
                ->body('Please try again later.')
                ->danger()
                ->send();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('manageBilling')
                ->label('Manage Billing')
                ->icon('heroicon-o-cog-6-tooth')
                ->action('redirectToCustomerPortal')
                ->visible(fn () => auth()->user()->stripe_customer_id),
        ];
    }
}
