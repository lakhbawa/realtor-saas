<?php

namespace App\Filament\Tenant\Pages;

use App\Services\BillingService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class Billing extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static string $view = 'filament.tenant.pages.billing';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?int $navigationSort = 2;

    public function __construct(private BillingService $billing)
    {
        parent::__construct();
    }

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
        return auth()->user()->trial_ends_at?->format('F j, Y');
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
                ->visible(fn() => auth()->user()->stripe_customer_id !== null),

            Action::make('subscribe')
                ->label('Subscribe Now')
                ->icon('heroicon-o-credit-card')
                ->color('success')
                ->action('redirectToCheckout')
                ->visible(fn() => !$this->hasActiveSubscription()),
        ];
    }

    public function redirectToCustomerPortal(): void
    {
        $user = auth()->user();

        if (!$user->stripe_customer_id) {
            Notification::make()->title('No billing account found')->danger()->send();
            return;
        }

        try {
            $url = $this->billing->createPortalSession($user, route('filament.tenant.pages.billing'));
            $this->redirect($url);
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
        try {
            $url = $this->billing->createCheckoutSession(
                auth()->user(),
                route('filament.tenant.pages.billing').'?success=true',
                route('filament.tenant.pages.billing').'?canceled=true'
            );

            $this->redirect($url);
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
            Notification::make()->title('No active subscription found')->danger()->send();
            return;
        }

        try {
            $this->billing->cancelSubscription($user);

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
