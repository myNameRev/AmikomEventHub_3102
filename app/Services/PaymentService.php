<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;
use App\Models\Transaction as TransactionModel;
use Illuminate\Support\Str;

class PaymentService
{
    protected $isDemo;

    public function __construct()
    {
        $this->isDemo = config('midtrans.is_demo');
        
        // Set Midtrans Configuration (only if not in demo mode)
        if (!$this->isDemo) {
            Config::$serverKey = config('midtrans.server_key');
            Config::$clientKey = config('midtrans.client_key');
            Config::$isProduction = config('midtrans.is_production');
            Config::$isSanitized = true;
            Config::$is3ds = true;
        }
    }

    /**
     * Generate Snap Token untuk payment UI
     */
    public function createSnapToken(TransactionModel $transaction)
    {
        try {
            if ($this->isDemo) {
                // Demo mode: return mock token
                $mockToken = 'DEMO-' . Str::random(20);
                $transaction->update(['snap_token' => $mockToken]);
                return $mockToken;
            }

            $params = [
                'transaction_details' => [
                    'order_id' => $transaction->order_id,
                    'gross_amount' => $transaction->total_price,
                ],
                'customer_details' => [
                    'first_name' => $transaction->customer_name,
                    'email' => $transaction->customer_email,
                    'phone' => $transaction->customer_phone,
                ],
                'item_details' => [
                    [
                        'id' => $transaction->event->id,
                        'price' => $transaction->event->price,
                        'quantity' => 1,
                        'name' => $transaction->event->title,
                    ]
                ],
            ];

            $snapToken = Snap::getSnapToken($params);
            $transaction->update(['snap_token' => $snapToken]);
            
            return $snapToken;
        } catch (\Exception $e) {
            \Log::error('Snap token creation failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Check transaction status dari Midtrans
     */
    public function checkTransactionStatus($orderId)
    {
        try {
            if ($this->isDemo) {
                // Demo mode: return mock status
                return [
                    'order_id' => $orderId,
                    'transaction_status' => 'settlement',
                    'fraud_status' => 'accept'
                ];
            }

            return Transaction::status($orderId);
        } catch (\Exception $e) {
            \Log::error('Check transaction status failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle webhook dari Midtrans
     */
    public function handleWebhook($notificationData)
    {
        try {
            $transaction = TransactionModel::where('order_id', $notificationData['order_id'])->first();
            
            if (!$transaction) {
                throw new \Exception('Transaction not found');
            }

            $transactionStatus = $notificationData['transaction_status'];
            $fraudStatus = $notificationData['fraud_status'] ?? null;

            // Update status berdasarkan response Midtrans
            if ($transactionStatus === 'capture' && $fraudStatus === 'accept') {
                $transaction->update(['status' => 'settlement']);
            } elseif ($transactionStatus === 'settlement') {
                $transaction->update(['status' => 'settlement']);
            } elseif ($transactionStatus === 'pending') {
                $transaction->update(['status' => 'pending']);
            } elseif ($transactionStatus === 'deny' || $transactionStatus === 'cancel' || $transactionStatus === 'expire') {
                $transaction->update(['status' => 'failed']);
            }

            return [
                'success' => true,
                'message' => 'Transaction status updated',
                'transaction' => $transaction
            ];
        } catch (\Exception $e) {
            \Log::error('Webhook handling failed: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Demo mode: simulate payment
     */
    public function simulatePayment(TransactionModel $transaction, $status = 'settlement')
    {
        $validStatuses = ['settlement', 'pending', 'failed'];
        
        if (!in_array($status, $validStatuses)) {
            $status = 'settlement';
        }

        $transaction->update(['status' => $status]);

        return [
            'success' => true,
            'status' => $status,
            'message' => "Payment simulated as: " . ucfirst($status)
        ];
    }
}
