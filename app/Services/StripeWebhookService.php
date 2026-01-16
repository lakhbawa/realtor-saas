<?php

namespace App\Services;

use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class StripeWebhookService
{
    public function handleCheckoutCompleted($session): void
    {
        $user = $this->findUserByCustomer($session->customer);

        if ($session->subscription) {
            $user->update([
                'stripe_subscription_id' => $session->subscription,
                'subscription_status' => 'active',
            ]);
        }

        Log::info('Checkout completed', ['user_id' => $user->id]);
    }

    public function handleSubscriptionCreated($subscription): void
    {
        $user = $this->findUserByCustomer($subscription->customer);
        $status = $this->mapStatus($subscription->status);

        $user->update([
            'stripe_subscription_id' => $subscription->id,
            'subscription_status' => $status,
            'trial_ends_at' => $this->parseTimestamp($subscription->trial_end),
        ]);

        Subscription::updateOrCreate(
            ['user_id' => $user->id],
            [
                'stripe_subscription_id' => $subscription->id,
                'stripe_price_id' => $subscription->items->data[0]->price->id ?? null,
                'status' => $status,
                'current_period_start' => Carbon::createFromTimestamp($subscription->current_period_start),
                'current_period_end' => Carbon::createFromTimestamp($subscription->current_period_end),
                'trial_ends_at' => $this->parseTimestamp($subscription->trial_end),
            ]
        );

        Log::info('Subscription created', ['user_id' => $user->id]);
    }

    public function handleSubscriptionUpdated($subscription): void
    {
        $user = $this->findUserByCustomer($subscription->customer);
        $status = $this->mapStatus($subscription->status);

        $user->update([
            'subscription_status' => $status,
            'trial_ends_at' => $this->parseTimestamp($subscription->trial_end),
        ]);

        Subscription::where('user_id', $user->id)->update([
            'status' => $status,
            'current_period_start' => Carbon::createFromTimestamp($subscription->current_period_start),
            'current_period_end' => Carbon::createFromTimestamp($subscription->current_period_end),
            'canceled_at' => $this->parseTimestamp($subscription->canceled_at),
        ]);

        Log::info('Subscription updated', ['user_id' => $user->id, 'status' => $status]);
    }

    public function handleSubscriptionDeleted($subscription): void
    {
        $user = $this->findUserByCustomer($subscription->customer);

        $user->update(['subscription_status' => 'canceled']);

        Subscription::where('user_id', $user->id)->update([
            'status' => 'canceled',
            'canceled_at' => now(),
        ]);

        Log::info('Subscription deleted', ['user_id' => $user->id]);
    }

    public function handleInvoicePaymentSucceeded($invoice): void
    {
        $user = $this->findUserByCustomer($invoice->customer);

        if ($user->subscription_status === 'past_due') {
            $user->update(['subscription_status' => 'active']);
            Subscription::where('user_id', $user->id)->update(['status' => 'active']);
        }

        Log::info('Invoice payment succeeded', ['user_id' => $user->id]);
    }

    public function handleInvoicePaymentFailed($invoice): void
    {
        $user = $this->findUserByCustomer($invoice->customer);

        $user->update(['subscription_status' => 'past_due']);
        Subscription::where('user_id', $user->id)->update(['status' => 'past_due']);

        Log::warning('Invoice payment failed', ['user_id' => $user->id]);
    }

    public function handleTrialWillEnd($subscription): void
    {
        $user = $this->findUserByCustomer($subscription->customer);

        Log::info('Trial ending soon', ['user_id' => $user->id]);
    }

    private function findUserByCustomer(string $customerId): User
    {
        return User::where('stripe_customer_id', $customerId)->firstOrFail();
    }

    private function mapStatus(string $stripeStatus): string
    {
        return match ($stripeStatus) {
            'active' => 'active',
            'trialing' => 'trialing',
            'past_due' => 'past_due',
            'canceled', 'unpaid' => 'canceled',
            'incomplete', 'incomplete_expired' => 'incomplete',
            default => 'inactive',
        };
    }

    private function parseTimestamp(?int $timestamp): ?Carbon
    {
        return $timestamp ? Carbon::createFromTimestamp($timestamp) : null;
    }
}
