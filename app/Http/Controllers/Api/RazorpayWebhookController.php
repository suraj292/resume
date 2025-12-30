<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;

class RazorpayWebhookController extends Controller
{
    /**
     * Handle Razorpay webhook events
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        $webhookSecret = config('services.razorpay.webhook_secret');
        $webhookSignature = $request->header('X-Razorpay-Signature');
        $webhookBody = $request->getContent();

        // Verify webhook signature
        try {
            $expectedSignature = hash_hmac('sha256', $webhookBody, $webhookSecret);
            
            if ($webhookSignature !== $expectedSignature) {
                Log::error('Razorpay webhook signature verification failed');
                return response()->json(['error' => 'Invalid signature'], 400);
            }
        } catch (\Exception $e) {
            Log::error('Razorpay webhook signature error', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Signature verification failed'], 400);
        }

        $payload = $request->all();
        $event = $payload['event'] ?? null;

        if (!$event) {
            return response()->json(['error' => 'No event type'], 400);
        }

        // Handle the event
        switch ($event) {
            case 'payment.captured':
                $this->handlePaymentCaptured($payload['payload']['payment']['entity'] ?? []);
                break;

            case 'payment.failed':
                $this->handlePaymentFailed($payload['payload']['payment']['entity'] ?? []);
                break;

            case 'subscription.activated':
            case 'subscription.charged':
                $this->handleSubscriptionActivated($payload['payload']['subscription']['entity'] ?? []);
                break;

            case 'subscription.cancelled':
                $this->handleSubscriptionCancelled($payload['payload']['subscription']['entity'] ?? []);
                break;

            default:
                Log::info('Unhandled Razorpay webhook event', ['type' => $event]);
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Handle successful payment capture
     */
    protected function handlePaymentCaptured(array $payment): void
    {
        if (empty($payment)) {
            return;
        }

        Log::info('Razorpay payment captured', ['payment_id' => $payment['id'] ?? null]);

        $transaction = Transaction::where('payment_id', $payment['id'] ?? '')->first();
        
        if ($transaction) {
            $transaction->update([
                'status' => 'completed',
                'payment_response' => json_encode($payment)
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
     * Handle failed payment
     */
    protected function handlePaymentFailed(array $payment): void
    {
        if (empty($payment)) {
            return;
        }

        Log::warning('Razorpay payment failed', ['payment_id' => $payment['id'] ?? null]);

        $transaction = Transaction::where('payment_id', $payment['id'] ?? '')->first();
        
        if ($transaction) {
            $transaction->update([
                'status' => 'failed',
                'payment_response' => json_encode($payment)
            ]);
        }
    }

    /**
     * Handle subscription activation
     */
    protected function handleSubscriptionActivated(array $subscription): void
    {
        if (empty($subscription)) {
            return;
        }

        Log::info('Razorpay subscription activated', ['subscription_id' => $subscription['id'] ?? null]);

        // Find user by Razorpay customer ID or subscription notes
        $customerId = $subscription['customer_id'] ?? null;
        if ($customerId) {
            $user = User::where('razorpay_customer_id', $customerId)->first();
            
            if ($user) {
                $user->update([
                    'subscription_status' => 'active',
                    'subscription_ends_at' => isset($subscription['current_end']) 
                        ? now()->createFromTimestamp($subscription['current_end'])
                        : now()->addMonth()
                ]);
            }
        }
    }

    /**
     * Handle subscription cancellation
     */
    protected function handleSubscriptionCancelled(array $subscription): void
    {
        if (empty($subscription)) {
            return;
        }

        Log::info('Razorpay subscription cancelled', ['subscription_id' => $subscription['id'] ?? null]);

        $customerId = $subscription['customer_id'] ?? null;
        if ($customerId) {
            $user = User::where('razorpay_customer_id', $customerId)->first();
            
            if ($user) {
                $user->update([
                    'subscription_status' => 'cancelled',
                    'plan_id' => null
                ]);
            }
        }
    }
}
