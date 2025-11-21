<?php

namespace App\Filament\Tenant\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Http;

class Billing extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static string $view = 'filament.tenant.pages.billing';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 2;

    public function getSubscriptionStatus(): string
    {
        return auth()->user()->subscription_status ?? 'incomplete';
    }

    public function getSubscriptionStatusColor(): string
    {
        return match ($this->getSubscriptionStatus()) {
            'active' => 'success',
            'trialing' => 'warning',
            'past_due' => 'danger',
            'canceled' => 'gray',
            default => 'gray',
        };
    }

    public function getTrialEndsAt(): ?string
    {
        $trialEnds = auth()->user()->trial_ends_at;
        return $trialEnds ? $trialEnds->format('F j, Y') : null;
    }

    public function hasActiveSubscription(): bool
    {
        return in_array($this->getSubscriptionStatus(), ['active', 'trialing']);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('manageBilling')
                ->label('Manage Billing')
                ->icon('heroicon-o-cog-6-tooth')
                ->action('redirectToCustomerPortal')
                ->visible(fn () => auth()->user()->stripe_customer_id !== null),

            Action::make('subscribe')
                ->label('Subscribe Now')
                ->icon('heroicon-o-credit-card')
                ->color('success')
                ->action('redirectToCheckout')
                ->visible(fn () => !$this->hasActiveSubscription()),
        ];
    }

    public function redirectToCustomerPortal(): void
    {
        $user = auth()->user();

        if (!$user->stripe_customer_id) {
            Notification::make()
                ->title('No billing account found')
                ->danger()
                ->send();
            return;
        }

        try {
            $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));

            $session = $stripe->billingPortal->sessions->create([
                'customer' => $user->stripe_customer_id,
                'return_url' => route('filament.tenant.pages.billing'),
            ]);

            $this->redirect($session->url);
        } catch (\Exception $e) {
            Notification::make()
                ->title('Unable to access billing portal')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function redirectToCheckout(): void
    {
        $user = auth()->user();

        try {
            $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));

            // Create or retrieve customer
            if (!$user->stripe_customer_id) {
                $customer = $stripe->customers->create([
                    'email' => $user->email,
                    'name' => $user->name,
                    'metadata' => [
                        'user_id' => $user->id,
                        'subdomain' => $user->subdomain,
                    ],
                ]);
                $user->update(['stripe_customer_id' => $customer->id]);
            }

            $session = $stripe->checkout->sessions->create([
                'customer' => $user->stripe_customer_id,
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price' => config('services.stripe.price_id'),
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'success_url' => route('filament.tenant.pages.billing') . '?success=true',
                'cancel_url' => route('filament.tenant.pages.billing') . '?canceled=true',
                'subscription_data' => [
                    'trial_period_days' => 14,
                    'metadata' => [
                        'user_id' => $user->id,
                    ],
                ],
            ]);

            $this->redirect($session->url);
        } catch (\Exception $e) {
            Notification::make()
                ->title('Unable to start checkout')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function cancelSubscription(): void
    {
        $user = auth()->user();

        if (!$user->stripe_subscription_id) {
            Notification::make()
                ->title('No active subscription found')
                ->danger()
                ->send();
            return;
        }

        try {
            $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));

            $stripe->subscriptions->update($user->stripe_subscription_id, [
                'cancel_at_period_end' => true,
            ]);

            $user->update(['subscription_status' => 'canceled']);

            Notification::make()
                ->title('Subscription canceled')
                ->body('Your subscription will remain active until the end of the billing period.')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Unable to cancel subscription')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
