<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Order;
use App\Services\DigiflazzService;
use App\Services\XenditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected DigiflazzService $digiflazz;
    protected XenditService $xendit;

    public function __construct(DigiflazzService $digiflazz, XenditService $xendit)
    {
        $this->digiflazz = $digiflazz;
        $this->xendit = $xendit;
    }

    public function detail(Order $order)
    {
        $invoiceUrl = $order->gateway_invoice_url;
        $isSimulation = (bool) config('services.payment.simulation');
        $isDemo = $isSimulation || !$this->xendit->isConfigured() || empty($invoiceUrl);
        $brand = Brand::where('name', $order->brand)->first();

        return view('payment.detail', compact('order', 'invoiceUrl', 'isDemo', 'isSimulation', 'brand'));
    }

    /**
     * Simulasi pembayaran (PAYMENT_SIMULATION=true):
     * menandai order lunas lalu memicu topUp Digiflazz seperti webhook PAID Xendit.
     */
    public function simulatePay(Request $request, Order $order)
    {
        abort_unless(config('services.payment.simulation'), 404);

        if ($order->status !== 'pending') {
            return response()->json(['status' => $order->status, 'note' => $order->note]);
        }

        $order->transaction?->update([
            'payment_type' => 'Simulasi (QRIS)',
            'status' => 'settled',
            'raw_response' => [
                'simulation' => true,
                'paid_at' => now()->toDateTimeString(),
            ],
        ]);

        Log::info('Pembayaran simulasi diproses', ['order_id' => $order->order_id]);

        $this->handlePaid($order);
        $order->refresh();

        return response()->json([
            'status' => $order->status,
            'note' => $order->note,
        ]);
    }

    public function notificationHandler(Request $request)
    {
        if (!$this->xendit->verifyCallbackToken($request->header('x-callback-token'))) {
            return response()->json(['status' => 'error', 'message' => 'Invalid callback token'], 401);
        }

        try {
            $payload = $request->all();
            $status = strtoupper($payload['status'] ?? '');
            $externalId = $payload['external_id'] ?? null;

            $order = Order::where('order_id', $externalId)->first();

            if (!$order) {
                Log::warning('Xendit webhook: order tidak ditemukan', ['external_id' => $externalId]);
                return response()->json(['status' => 'ok']);
            }

            $orderTransaction = $order->transaction;

            $orderTransaction?->update([
                'transaction_id' => $payload['id'] ?? null,
                'payment_type' => $this->formatPaymentType($payload),
                'status' => strtolower($status),
                'raw_response' => $payload,
            ]);

            if (in_array($status, ['PAID', 'SETTLED'])) {
                $this->handlePaid($order);
            } elseif ($status === 'EXPIRED') {
                $order->update(['status' => 'failed']);
                $orderTransaction?->update(['status' => 'failed']);
            }

            return response()->json(['status' => 'ok']);
        } catch (\Exception $e) {
            Log::error('Xendit webhook error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function success(Order $order)
    {
        return view('payment.success', compact('order'));
    }

    public function status(Order $order)
    {
        $this->syncFromGateway($order);
        $order->refresh();

        // Fallback: order masih processing → tanyakan status final ke Digiflazz
        // (jaga-jaga webhook Digiflazz tidak sampai, misal saat testing di localhost).
        if ($order->status === 'processing') {
            $this->syncFromDigiflazz($order);
            $order->refresh();
        }

        return response()->json([
            'status' => $order->status,
            'note' => $order->note,
        ]);
    }

    private function syncFromGateway(Order $order): void
    {
        if ($order->status !== 'pending' || empty($order->gateway_invoice_id)) {
            return;
        }

        if (!$this->xendit->isConfigured()) {
            return;
        }

        $invoice = $this->xendit->getInvoice($order->gateway_invoice_id);

        if (!$invoice || empty($invoice['status'])) {
            return;
        }

        $status = strtoupper($invoice['status']);

        $order->transaction?->update([
            'transaction_id' => $invoice['id'] ?? null,
            'payment_type' => $this->formatPaymentType($invoice),
            'raw_response' => $invoice,
        ]);

        if (in_array($status, ['PAID', 'SETTLED'])) {
            $order->transaction?->update(['status' => strtolower($status)]);
            $this->handlePaid($order);
        } elseif ($status === 'EXPIRED') {
            $order->update(['status' => 'failed']);
            $order->transaction?->update(['status' => 'failed']);
        }
    }

    private function handlePaid(Order $order): void
    {
        // Guard idempotency: webhook Xendit bisa terkirim lebih dari sekali.
        // Hanya proses jika order masih pending agar topUp tidak terkirim ganda.
        if ($order->status !== 'pending') {
            return;
        }

        $order->update(['status' => 'processing']);
        $order->transaction?->update(['status' => 'processing']);

        $result = $this->digiflazz->topUp(
            $order->buyer_sku_code,
            $order->customer_number,
            $order->order_id,
            $order->effective_zone_id
        );

        Log::info('Digiflazz topUp response', ['order_id' => $order->order_id, 'response' => $result]);

        $this->applyDigiflazzResult($order, $result);
    }

    /**
     * Terapkan hasil respons Digiflazz ke order.
     * "Pending" berarti transaksi sedang diproses — JANGAN ditandai gagal;
     * status final akan datang via webhook Digiflazz atau polling checkStatus.
     */
    public function applyDigiflazzResult(Order $order, array $result): void
    {
        if ($order->status === 'success') {
            return;
        }

        $data = $result['data'] ?? [];
        $status = strtolower(trim((string) ($data['status'] ?? '')));

        if ($status === 'sukses') {
            $order->update([
                'status' => 'success',
                'note' => $data['sn'] ?? null,
            ]);
            $order->transaction?->update(['status' => 'success', 'raw_response' => $result]);
        } elseif ($status === 'pending') {
            $order->update(['status' => 'processing', 'note' => null]);
            $order->transaction?->update(['status' => 'processing', 'raw_response' => $result]);
        } else {
            $order->update([
                'status' => 'failed',
                'note' => $data['message'] ?? ($data['status'] ?? 'Gagal diproses Digiflazz'),
            ]);
            $order->transaction?->update(['status' => 'failed', 'raw_response' => $result]);
        }
    }

    /**
     * Webhook dari Digiflazz: push status final transaksi (Sukses/Gagal/Pending).
     * Diverifikasi via header X-Hub-Signature (HMAC-SHA256 body dengan API key).
     */
    public function digiflazzCallback(Request $request)
    {
        $signature = (string) $request->header('X-Hub-Signature');
        $expected = 'sha256=' . hash_hmac('sha256', $request->getContent(), $this->digiflazz->getKey());

        if ($signature === '' || !hash_equals($expected, $signature)) {
            Log::warning('Digiflazz callback: signature tidak valid');
            return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 401);
        }

        try {
            $data = $request->json('data');

            // Notifikasi deposit/saldo tidak punya ref_id → abaikan.
            if (empty($data) || empty($data['ref_id'])) {
                return response()->json(['status' => 'ok']);
            }

            $order = Order::where('order_id', $data['ref_id'])->first();

            if (!$order) {
                Log::warning('Digiflazz callback: order tidak ditemukan', ['ref_id' => $data['ref_id']]);
                return response()->json(['status' => 'ok']);
            }

            Log::info('Digiflazz callback received', ['order_id' => $order->order_id, 'data' => $data]);

            $this->applyDigiflazzResult($order, ['data' => $data]);

            return response()->json(['status' => 'ok']);
        } catch (\Exception $e) {
            Log::error('Digiflazz callback error: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Polling status transaksi ke Digiflazz untuk order yang masih processing.
     */
    private function syncFromDigiflazz(Order $order): void
    {
        try {
            $result = $this->digiflazz->checkStatus(
                $order->buyer_sku_code,
                $order->customer_number,
                $order->order_id,
                $order->effective_zone_id
            );

            $status = strtolower(trim((string) ($result['data']['status'] ?? '')));

            if (in_array($status, ['sukses', 'gagal'], true)) {
                $this->applyDigiflazzResult($order, $result);
            }
        } catch (\Exception $e) {
            Log::error('Digiflazz status poll failed: ' . $e->getMessage());
        }
    }

    private function formatPaymentType(array $invoice): string
    {
        $method = $invoice['payment_method'] ?? null;
        $channel = $invoice['payment_channel'] ?? null;

        if ($method && $channel) {
            return $method . ' - ' . $channel;
        }

        return $method ?? $channel ?? 'unknown';
    }
}
