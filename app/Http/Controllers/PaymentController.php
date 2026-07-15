<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Transaction;
use App\Services\DigiflazzService;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected DigiflazzService $digiflazz;
    protected MidtransService $midtrans;

    public function __construct(DigiflazzService $digiflazz, MidtransService $midtrans)
    {
        $this->digiflazz = $digiflazz;
        $this->midtrans = $midtrans;
    }

    public function notificationHandler(Request $request)
    {
        try {
            $notification = $this->midtrans->getNotification();

            $transaction = $notification->transaction_status;
            $type = $notification->payment_type;
            $orderId = $notification->order_id;
            $fraud = $notification->fraud_status;

            $order = Order::where('order_id', $orderId)->firstOrFail();
            $orderTransaction = $order->transaction;

            $orderTransaction->update([
                'transaction_id' => $notification->transaction_id,
                'payment_type' => $type,
                'status' => $transaction,
                'fraud_status' => $fraud,
                'raw_response' => $notification->getResponse(),
            ]);

            if ($transaction === 'settlement' || $transaction === 'capture') {
                $order->update(['status' => 'processing']);

                $result = $this->digiflazz->topUp(
                    $order->buyer_sku_code,
                    $order->customer_number,
                    $order->order_id
                );

                if (isset($result['data']['status']) && $result['data']['status'] === 'Sukses') {
                    $order->update([
                        'status' => 'success',
                        'note' => $result['data']['sn'] ?? null,
                    ]);
                    $orderTransaction->update(['status' => 'success']);
                } else {
                    $order->update([
                        'status' => 'failed',
                        'note' => $result['data']['message'] ?? 'Gagal diproses Digiflazz',
                    ]);
                    $orderTransaction->update(['status' => 'failed']);
                }
            } elseif ($transaction === 'deny' || $transaction === 'expire' || $transaction === 'cancel') {
                $order->update(['status' => 'failed']);
                $orderTransaction->update(['status' => 'failed']);
            }

            return response()->json(['status' => 'ok']);
        } catch (\Exception $e) {
            Log::error('Payment notification error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function success(Order $order)
    {
        return view('payment.success', compact('order'));
    }

    public function status(Order $order)
    {
        return response()->json([
            'status' => $order->status,
            'note' => $order->note,
        ]);
    }
}
