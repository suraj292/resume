<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Webhook;
use Stripe\Exception\SignatureVerificationException;

class StripeWebhookController extends Controller
{
    /**
     * Handle Stripe webhook events
     */
    public function handleWebhook(Request $request): JsonResponse
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
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            Log::error('Stripe webhook invalid payload', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            // Invalid signature
            Log::error('Stripe webhook invalid signature', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $this->handlePaymentIntentSucceeded($event->data->object);
                break;

            case 'payment_intent.payment_failed':
                $this->handlePaymentIntentFailed($event->data->object);
                break;

            case 'customer.subscription.created':
            case 'customer.subscription.updated':
                $this->handleSubscriptionUpdated($event->data->object);
                break;

            case 'customer.subscription.deleted':
                $this->handleSubscriptionDeleted($event->data->object);
                break;

            default:
                Log::info('Unhandled Stripe webhook event', ['type' => $event->type]);
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Handle successful payment intent
     */
    protected function handlePaymentIntentSucceeded($paymentIntent): void
    {
        Log::info('Payment intent succeeded', ['payment_intent_id' => $paymentIntent->id]);

        // Update transaction status
        $transaction = Transaction::where('payment_id', $paymentIntent->id)->first();
        
        if ($transaction) {
            $transaction->update([
                'status' => 'completed',
                'payment_response' => json_encode($paymentIntent)
            ]);

            // Update user's plan
            $user = User::find($transaction->user_id);
            if ($user) {
                $user->update([
                    'plan_id' => $transaction->plan_id,
                    'subscription_status' => 'active',
                    'subscription_ends_at' => now()->addMonth()
                ]);
            }
        }
    }

    /**
     * Handle failed payment intent
     */
    protected function handlePaymentIntentFailed($paymentIntent): void
    {
        Log::warning('Payment intent failed', ['payment_intent_id' => $paymentIntent->id]);

        $transaction = Transaction::where('payment_id', $paymentIntent->id)->first();
        
        if ($transaction) {
            $transaction->update([
                'status' => 'failed',
                'payment_response' => json_encode($paymentIntent)
            ]);
        }
    }

    /**
     * Handle subscription updates
     */
    protected function handleSubscriptionUpdated($subscription): void
    {
        Log::info('Subscription updated', ['subscription_id' => $subscription->id]);

        // Find user by Stripe customer ID
        $user = User::where('stripe_customer_id', $subscription->customer)->first();
        
        if ($user) {
            $user->update([
                'subscription_status' => $subscription->status,
                'subscription_ends_at' => $subscription->current_period_end 
                    ? now()->createFromTimestamp($subscription->current_period_end)
                    : null
            ]);
        }
    }

    /**
     * Handle subscription deletion
     */
    protected function handleSubscriptionDeleted($subscription): void
    {
        Log::info('Subscription deleted', ['subscription_id' => $subscription->id]);

        $user = User::where('stripe_customer_id', $subscription->customer)->first();
        
        if ($user) {
            $user->update([
                'subscription_status' => 'cancelled',
                'plan_id' => null
            ]);
        }
    }
}
