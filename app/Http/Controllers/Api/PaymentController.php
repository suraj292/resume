<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Razorpay\Api\Api;

class PaymentController extends Controller
{
    protected $razorpay;

    public function __construct()
    {
        $this->razorpay = new Api(
            env('RAZORPAY_KEY_ID'),
            env('RAZORPAY_KEY_SECRET')
        );
    }

    /**
     * Create Razorpay order
     */
    public function createOrder(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
            'billing_cycle' => 'required|in:monthly,yearly',
        ]);

        $user = auth()->user();
        $plan = Plan::findOrFail($request->plan_id);

        // Determine amount based on billing cycle
        $amount = $request->billing_cycle === 'yearly' 
            ? $plan->yearly_price 
            : $plan->monthly_price;

        // Convert to smallest currency unit (paise for INR, cents for USD)
        $amountInSmallestUnit = (int)($amount * 100);

        try {
            // Create Razorpay order
            $order = $this->razorpay->order->create([
                'amount' => $amountInSmallestUnit,
                'currency' => $plan->currency_code,
                'receipt' => 'order_' . time() . '_' . $user->id,
                'notes' => [
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                    'billing_cycle' => $request->billing_cycle,
                ]
            ]);

            // Store transaction
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'plan_id' => $plan->id,
                'razorpay_order_id' => $order['id'],
                'amount' => $amount,
                'currency' => $plan->currency_code,
                'status' => 'pending',
                'metadata' => [
                    'billing_cycle' => $request->billing_cycle,
                    'plan_name' => $plan->name,
                ],
            ]);

            return response()->json([
                'success' => true,
                'order_id' => $order['id'],
                'amount' => $amountInSmallestUnit,
                'currency' => $plan->currency_code,
                'key' => env('RAZORPAY_KEY_ID'),
                'transaction_id' => $transaction->id,
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create order: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Verify payment and update subscription
     */
    public function verifyPayment(Request $request)
    {
        $request->validate([
            'razorpay_order_id' => 'required',
            'razorpay_payment_id' => 'required',
            'razorpay_signature' => 'required',
        ]);

        // Find transaction
        $transaction = Transaction::where('razorpay_order_id', $request->razorpay_order_id)
            ->firstOrFail();

        // Verify signature
        $attributes = [
            'razorpay_order_id' => $request->razorpay_order_id,
            'razorpay_payment_id' => $request->razorpay_payment_id,
            'razorpay_signature' => $request->razorpay_signature,
        ];

        try {
            $this->razorpay->utility->verifyPaymentSignature($attributes);

            // Update transaction
            $transaction->update([
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature,
                'status' => 'success',
            ]);

            // Update user subscription
            $user = $transaction->user;
            $billingCycle = $transaction->metadata['billing_cycle'] ?? 'monthly';
            
            $expiryDays = $billingCycle === 'yearly' ? 365 : 30;
            
            $user->update([
                'plan_id' => $transaction->plan_id,
                'plan_started_at' => now(),
                'plan_expiry' => now()->addDays($expiryDays),
            ]);

            // Reload user with plan
            $user->load('plan');

            return response()->json([
                'success' => true,
                'message' => 'Payment verified successfully',
                'plan' => $user->plan,
                'expiry' => $user->plan_expiry->format('Y-m-d'),
            ]);

        } catch (\Exception $e) {
            // Payment verification failed
            $transaction->update(['status' => 'failed']);

            return response()->json([
                'success' => false,
                'message' => 'Payment verification failed: ' . $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get user's transaction history
     */
    public function getTransactions(Request $request)
    {
        $user = auth()->user();
        
        $transactions = Transaction::where('user_id', $user->id)
            ->with('plan')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'transactions' => $transactions,
        ]);
    }
}
