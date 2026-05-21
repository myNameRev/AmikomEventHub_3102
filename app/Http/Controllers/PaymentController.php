<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Transaction;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Show checkout form dengan event details
     */
    public function checkout(Request $request, Event $event)
    {
        $categories = \App\Models\Category::all();
        return view('checkout', compact('event', 'categories'));
    }


    /**
     * Process checkout dan generate Snap Token
     */
    public function processCheckout(Request $request, Event $event)
    {
        // Validasi input
        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email',
            'customer_phone' => 'required|string|max:20',
            'quantity' => 'required|integer|min:1',
            'payment_status' => 'nullable|in:success,pending,failed',
            'payment_method' => 'required|in:qris'
        ]);


        // Generate unique order ID
        $orderId = 'ORDER-' . date('YmdHis') . '-' . Str::random(6);

        // Kalkulasi total harga
        $totalPrice = $event->price * $validated['quantity'];

        // Create transaction record
        $transaction = Transaction::create([
            'event_id' => $event->id,
            'order_id' => $orderId,
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'],
            'customer_phone' => $validated['customer_phone'],
            'total_price' => $totalPrice,
            'status' => 'pending'
        ]);

        try {
            // Check if demo mode
            if (config('midtrans.is_demo')) {
                // Demo mode: simulate payment result
                $paymentStatus = $validated['payment_status'] ?? 'success';
                
                if ($paymentStatus === 'success') {
                    $this->paymentService->simulatePayment($transaction, 'settlement');
                } else {
                    $this->paymentService->simulatePayment($transaction, $paymentStatus === 'pending' ? 'pending' : 'failed');
                }

                return response()->json([
                    'success' => true,
                    'snap_token' => 'DEMO-TOKEN-' . Str::random(20),
                    'transaction_id' => $transaction->id,
                    'is_demo' => true,
                    'payment_status' => $paymentStatus
                ]);
            }

            // Generate Snap Token (production)
            $snapToken = $this->paymentService->createSnapToken($transaction);

            return response()->json([
                'success' => true,
                'snap_token' => $snapToken,
                'redirect_url' => route('payment.status', $transaction->id)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cek status pembayaran
     */
    public function paymentStatus(Request $request, Transaction $transaction)
    {
        try {
            $status = $this->paymentService->checkTransactionStatus($transaction->order_id);
            $categories = \App\Models\Category::all();

            return view('payment-status', [
                'transaction' => $transaction,
                'status' => $status,
                'categories' => $categories,
            ]);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }


    /**
     * Webhook handler dari Midtrans
     */
    public function webhook(Request $request)
    {
        try {
            $json = file_get_contents('php://input');
            $notificationData = json_decode($json, true);

            // Validasi signature (optional but recommended)
            // Anda bisa menambahkan validasi signature Midtrans di sini

            $result = $this->paymentService->handleWebhook($notificationData);

            \Log::info('Midtrans Webhook:', $notificationData);

            return response()->json([
                'success' => true,
                'message' => 'Webhook processed successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Webhook error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
