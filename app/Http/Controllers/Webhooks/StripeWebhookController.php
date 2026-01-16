<?php

namespace App\Http\Controllers\Webhooks;

use App\Http\Controllers\Controller;
use App\Services\StripeWebhookService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function __construct(private StripeWebhookService $service)
    {
    }

    public function handle(Request $request)
    {
        try {
            $event = $this->verifyWebhook($request);
        } catch (SignatureVerificationException $e) {
            Log::error('Stripe webhook signature verification failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        Log::info('Stripe webhook received', ['type' => $event->type]);

        return $this->dispatch($event);
    }

    private function verifyWebhook(Request $request)
    {
        return Webhook::constructEvent(
            $request->getContent(),
            $request->header('Stripe-Signature'),
            config('services.stripe.webhook_secret')
        );
    }

    private function dispatch($event)
    {
        try {
            return match ($event->type) {
                'checkout.session.completed' => $this->success(fn() => $this->service->handleCheckoutCompleted($event->data->object)),
                'customer.subscription.created' => $this->success(fn() => $this->service->handleSubscriptionCreated($event->data->object)),
                'customer.subscription.updated' => $this->success(fn() => $this->service->handleSubscriptionUpdated($event->data->object)),
                'customer.subscription.deleted' => $this->success(fn() => $this->service->handleSubscriptionDeleted($event->data->object)),
                'invoice.payment_succeeded' => $this->success(fn() => $this->service->handleInvoicePaymentSucceeded($event->data->object)),
                'invoice.payment_failed' => $this->success(fn() => $this->service->handleInvoicePaymentFailed($event->data->object)),
                'customer.subscription.trial_will_end' => $this->success(fn() => $this->service->handleTrialWillEnd($event->data->object)),
                default => response()->json(['message' => 'Webhook received']),
            };
        } catch (\Exception $e) {
            Log::error('Stripe webhook processing failed', [
                'type' => $event->type,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['error' => 'Processing failed'], 500);
        }
    }

    private function success(callable $handler)
    {
        $handler();
        return response()->json(['message' => 'Processed']);
    }
}
