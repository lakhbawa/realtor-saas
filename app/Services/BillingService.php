<?php

namespace App\Services;

use App\Models\User;
use Stripe\StripeClient;

class BillingService
{
    private StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }

    public function createPortalSession(User $user, string $returnUrl): string
    {
        $session = $this->stripe->billingPortal->sessions->create([
            'customer' => $user->stripe_customer_id,
            'return_url' => $returnUrl,
        ]);

        return $session->url;
    }

    public function createCheckoutSession(User $user, string $successUrl, string $cancelUrl): string
    {
        $this->ensureCustomer($user);

        $session = $this->stripe->checkout->sessions->create([
            'customer' => $user->stripe_customer_id,
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price' => config('services.stripe.price_id'),
                'quantity' => 1,
            ]],
            'mode' => 'subscription',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'subscription_data' => [
                'trial_period_days' => 14,
                'metadata' => ['user_id' => $user->id],
            ],
        ]);

        return $session->url;
    }

    public function cancelSubscription(User $user): void
    {
        $this->stripe->subscriptions->update($user->stripe_subscription_id, [
            'cancel_at_period_end' => true,
        ]);

        $user->update(['subscription_status' => 'canceled']);
    }

    private function ensureCustomer(User $user): void
    {
        if ($user->stripe_customer_id) {
            return;
        }

        $customer = $this->stripe->customers->create([
            'email' => $user->email,
            'name' => $user->name,
            'metadata' => [
                'user_id' => $user->id,
                'subdomain' => $user->subdomain,
            ],
        ]);

        $user->update(['stripe_customer_id' => $customer->id]);
    }
}
