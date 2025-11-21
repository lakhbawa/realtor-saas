<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        try {
            $event = Webhook::constructEvent(
                $payload,
                $sigHeader,
                $webhookSecret
            );
        } catch (SignatureVerificationException $e) {
            Log::error('Stripe webhook signature verification failed', [
                'error' => $e->getMessage(),
            ]);
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        Log::info('Stripe webhook received', ['type' => $event->type]);

        return match ($event->type) {
            'checkout.session.completed' => $this->handleCheckoutSessionCompleted($event->data->object),
            'customer.subscription.created' => $this->handleSubscriptionCreated($event->data->object),
            'customer.subscription.updated' => $this->handleSubscriptionUpdated($event->data->object),
            'customer.subscription.deleted' => $this->handleSubscriptionDeleted($event->data->object),
            'invoice.payment_succeeded' => $this->handleInvoicePaymentSucceeded($event->data->object),
            'invoice.payment_failed' => $this->handleInvoicePaymentFailed($event->data->object),
            'customer.subscription.trial_will_end' => $this->handleTrialWillEnd($event->data->object),
            default => response()->json(['message' => 'Webhook received']),
        };
    }

    protected function handleCheckoutSessionCompleted($session)
    {
        $user = User::where('stripe_customer_id', $session->customer)->first();

        if (!$user) {
            Log::warning('Checkout completed but user not found', [
                'customer_id' => $session->customer,
            ]);
            return response()->json(['error' => 'User not found'], 404);
        }

        if ($session->subscription) {
            $user->update([
                'stripe_subscription_id' => $session->subscription,
                'subscription_status' => 'active',
            ]);
        }

        Log::info('Checkout completed for user', ['user_id' => $user->id]);

        return response()->json(['message' => 'Checkout processed']);
    }

    protected function handleSubscriptionCreated($subscription)
    {
        $user = User::where('stripe_customer_id', $subscription->customer)->first();

        if (!$user) {
            Log::warning('Subscription created but user not found', [
                'customer_id' => $subscription->customer,
            ]);
            return response()->json(['error' => 'User not found'], 404);
        }

        $status = $this->mapStripeStatus($subscription->status);

        $user->update([
            'stripe_subscription_id' => $subscription->id,
            'subscription_status' => $status,
            'trial_ends_at' => $subscription->trial_end
                ? \Carbon\Carbon::createFromTimestamp($subscription->trial_end)
                : null,
        ]);

        // Create subscription record
        Subscription::updateOrCreate(
            ['user_id' => $user->id],
            [
                'stripe_subscription_id' => $subscription->id,
                'stripe_price_id' => $subscription->items->data[0]->price->id ?? null,
                'status' => $status,
                'current_period_start' => \Carbon\Carbon::createFromTimestamp($subscription->current_period_start),
                'current_period_end' => \Carbon\Carbon::createFromTimestamp($subscription->current_period_end),
                'trial_ends_at' => $subscription->trial_end
                    ? \Carbon\Carbon::createFromTimestamp($subscription->trial_end)
                    : null,
            ]
        );

        Log::info('Subscription created for user', [
            'user_id' => $user->id,
            'subscription_id' => $subscription->id,
        ]);

        return response()->json(['message' => 'Subscription created']);
    }

    protected function handleSubscriptionUpdated($subscription)
    {
        $user = User::where('stripe_customer_id', $subscription->customer)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $status = $this->mapStripeStatus($subscription->status);

        $user->update([
            'subscription_status' => $status,
            'trial_ends_at' => $subscription->trial_end
                ? \Carbon\Carbon::createFromTimestamp($subscription->trial_end)
                : null,
        ]);

        Subscription::where('user_id', $user->id)->update([
            'status' => $status,
            'current_period_start' => \Carbon\Carbon::createFromTimestamp($subscription->current_period_start),
            'current_period_end' => \Carbon\Carbon::createFromTimestamp($subscription->current_period_end),
            'canceled_at' => $subscription->canceled_at
                ? \Carbon\Carbon::createFromTimestamp($subscription->canceled_at)
                : null,
        ]);

        Log::info('Subscription updated for user', [
            'user_id' => $user->id,
            'status' => $status,
        ]);

        return response()->json(['message' => 'Subscription updated']);
    }

    protected function handleSubscriptionDeleted($subscription)
    {
        $user = User::where('stripe_customer_id', $subscription->customer)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user->update([
            'subscription_status' => 'canceled',
        ]);

        Subscription::where('user_id', $user->id)->update([
            'status' => 'canceled',
            'canceled_at' => now(),
        ]);

        Log::info('Subscription deleted for user', ['user_id' => $user->id]);

        return response()->json(['message' => 'Subscription deleted']);
    }

    protected function handleInvoicePaymentSucceeded($invoice)
    {
        $user = User::where('stripe_customer_id', $invoice->customer)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Update subscription status to active if it was past_due
        if ($user->subscription_status === 'past_due') {
            $user->update(['subscription_status' => 'active']);

            Subscription::where('user_id', $user->id)->update([
                'status' => 'active',
            ]);
        }

        Log::info('Invoice payment succeeded', [
            'user_id' => $user->id,
            'invoice_id' => $invoice->id,
        ]);

        return response()->json(['message' => 'Payment recorded']);
    }

    protected function handleInvoicePaymentFailed($invoice)
    {
        $user = User::where('stripe_customer_id', $invoice->customer)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user->update(['subscription_status' => 'past_due']);

        Subscription::where('user_id', $user->id)->update([
            'status' => 'past_due',
        ]);

        Log::warning('Invoice payment failed', [
            'user_id' => $user->id,
            'invoice_id' => $invoice->id,
        ]);

        // TODO: Send email notification to user about failed payment

        return response()->json(['message' => 'Payment failure recorded']);
    }

    protected function handleTrialWillEnd($subscription)
    {
        $user = User::where('stripe_customer_id', $subscription->customer)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        Log::info('Trial ending soon for user', [
            'user_id' => $user->id,
            'trial_ends_at' => $subscription->trial_end,
        ]);

        // TODO: Send email notification to user about trial ending

        return response()->json(['message' => 'Trial ending notification processed']);
    }

    protected function mapStripeStatus(string $stripeStatus): string
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
}
