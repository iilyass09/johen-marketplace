<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Order;
use App\Models\Product;
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
        $isDemo = $isSimulation || !$this->xendit->isConfigured() || empty($order->gateway_invoice_id);
        $isQris = $order->gateway_type === 'qris' || (!empty($order->qr_string));
        $qrString = $order->qr_string;
        $gatewayType = $order->gateway_type ?: ($isQris ? 'qris' : 'invoice');
        $vaNumber = $order->va_number;
        $paymentCode = $order->payment_code;
        $checkoutUrl = $order->checkout_url;
        $gatewayExtra = $order->gateway_extra ?: [];
        $brand = Brand::where('name', $order->brand)->first();
        $hasReviewed = \App\Models\Review::where('order_id', $order->order_id)->exists();
        $reorderProduct = Product::where('buyer_sku_code', $order->buyer_sku_code)
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->first();

        return view('payment.detail', compact(
            'order', 'invoiceUrl', 'isDemo', 'isSimulation', 'brand', 'isQris', 'qrString', 'gatewayType', 'hasReviewed', 'reorderProduct',
            'vaNumber', 'paymentCode', 'checkoutUrl', 'gatewayExtra'
        ));
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
            $event = (string) ($payload['event'] ?? '');

            // Webhook QR Code ("qr.payment") — body berisi data bersarang.
            if (str_contains($event, 'qr.')) {
                return $this->handleQrCallback($payload);
            }

            // Webhook Virtual Account (fva.paid) — pakai payment_id/external_id di level atas.
            if (str_contains($event, 'fva.') || isset($payload['payment_id']) && !empty($payload['account_number'])) {
                return $this->handleVACallback($payload);
            }

            // Webhook Retail Outlet (ro_fpc.paid) — payment_id + payment_code/fixed_payment_code_id.
            if (str_contains($event, 'ro_fpc.') || isset($payload['payment_id']) && !empty($payload['fixed_payment_code_id'])) {
                return $this->handleRetailCallback($payload);
            }

            // Webhook E-wallet (ewallet.capture / ewallet.charge) — reference_id di dalam data.reference_id.
            if (str_contains($event, 'ewallet.')) {
                return $this->handleEwalletCallback($payload);
            }

            // Webhook Invoice (V2) — status & external_id di level teratas.
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

    private function handleQrCallback(array $payload)
    {
        $data = $payload['data'] ?? $payload['qr_code'] ?? $payload;

        $referenceId = $data['reference_id'] ?? $data['external_id'] ?? null;
        $status = strtoupper($data['status'] ?? $payload['status'] ?? '');

        $order = Order::where('order_id', $referenceId)->first();

        if (!$order) {
            Log::warning('Xendit QR webhook: order tidak ditemukan', ['reference_id' => $referenceId]);
            return response()->json(['status' => 'ok']);
        }

        $orderTransaction = $order->transaction;

        $orderTransaction?->update([
            'transaction_id' => $data['id'] ?? ($payload['id'] ?? null),
            'payment_type' => 'QRIS - ' . (($data['payment_detail']['source'] ?? $data['channel_code'] ?? 'QRIS') ?: 'QRIS'),
            'status' => strtolower($status),
            'raw_response' => $payload,
        ]);

        // QR Code dibayar → status SUCCEEDED pada QR webhook / COMPLETED pada v1.
        if (in_array($status, ['SUCCEEDED', 'COMPLETED', 'PAID', 'SETTLED'])) {
            $this->handlePaid($order);
        } elseif (in_array($status, ['FAILED', 'EXPIRED'])) {
            $order->update(['status' => 'failed']);
            $orderTransaction?->update(['status' => 'failed']);
        }

        return response()->json(['status' => 'ok']);
    }

    private function handleVACallback(array $payload)
    {
        $externalId = $payload['external_id'] ?? $payload['payment_id'] ?? null;
        $paymentStatus = strtoupper((string) ($payload['payment_status'] ?? ''));

        $order = $this->findOrderByReference($externalId);

        if (!$order) {
            Log::warning('Xendit VA webhook: order tidak ditemukan', ['external_id' => $externalId]);
            return response()->json(['status' => 'ok']);
        }

        $this->recordPayment($order, [
            'transaction_id' => $payload['id'] ?? ($payload['payment_id'] ?? null),
            'payment_type' => 'Virtual Account - ' . strtoupper((string) ($payload['bank_code'] ?? 'VA')),
            'status' => $paymentStatus === 'PAID' ? 'paid' : 'pending',
            'raw_response' => $payload,
        ]);

        if ($paymentStatus === 'PAID') {
            $this->handlePaid($order);
        }

        return response()->json(['status' => 'ok']);
    }

    private function handleRetailCallback(array $payload)
    {
        $externalId = $payload['external_id'] ?? null;
        $paymentStatus = strtoupper((string) ($payload['status'] ?? $payload['payment_status'] ?? ''));

        $order = $this->findOrderByReference($externalId);

        if (!$order) {
            Log::warning('Xendit Retail webhook: order tidak ditemukan', ['external_id' => $externalId]);
            return response()->json(['status' => 'ok']);
        }

        $this->recordPayment($order, [
            'transaction_id' => $payload['id'] ?? ($payload['payment_id'] ?? null),
            'payment_type' => 'Minimarket - ' . strtoupper((string) ($payload['retail_outlet_name'] ?? 'Retail')),
            'status' => $paymentStatus === 'PAID' ? 'paid' : 'pending',
            'raw_response' => $payload,
        ]);

        if ($paymentStatus === 'PAID') {
            $this->handlePaid($order);
        }

        return response()->json(['status' => 'ok']);
    }

    private function handleEwalletCallback(array $payload)
    {
        $data = $payload['data'] ?? $payload;
        $referenceId = $data['reference_id'] ?? $payload['reference_id'] ?? null;
        $status = strtoupper((string) ($data['status'] ?? $payload['status'] ?? ''));
        $channel = (string) ($data['channel_code'] ?? $payload['channel_code'] ?? 'E-Wallet');

        $order = $this->findOrderByReference($referenceId);

        if (!$order) {
            Log::warning('Xendit E-wallet webhook: order tidak ditemukan', ['reference_id' => $referenceId]);
            return response()->json(['status' => 'ok']);
        }

        $this->recordPayment($order, [
            'transaction_id' => $data['id'] ?? ($payload['id'] ?? null),
            'payment_type' => $channel . ' (E-wallet)',
            'status' => in_array($status, ['CAPTURED', 'SUCCEEDED', 'COMPLETED', 'PAID']) ? 'paid' : 'pending',
            'raw_response' => $payload,
        ]);

        if (in_array($status, ['CAPTURED', 'SUCCEEDED', 'COMPLETED', 'PAID'])) {
            $this->handlePaid($order);
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * Temukan order dari referensi yang direkam saat charge
     * (order_id → gateway_invoice_id dikirim sebagai external_id / reference_id).
     */
    private function findOrderByReference(?string $reference): ?Order
    {
        if (!$reference) {
            return null;
        }

        return Order::where('order_id', $reference)
            ->orWhere('gateway_invoice_id', $reference)
            ->first();
    }

    /**
     * Tulis record transaksi pembayaran terbaru dari webhook/polling.
     */
    private function recordPayment(Order $order, array $info): void
    {
        if (!$order->transaction) {
            return;
        }

        $order->transaction->update([
            'transaction_id' => $info['transaction_id'] ?? null,
            'payment_type' => $info['payment_type'] ?? $order->payment_method,
            'status' => $info['status'] ?? 'pending',
            'raw_response' => $info['raw_response'] ?? null,
        ]);
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

        $type = $order->gateway_type ?? 'invoice';

        // QRIS (embedded) disinkronkan lewat Get QR Code.
        if ($type === 'qris' || !empty($order->qr_string)) {
            $qr = $this->xendit->getQr($order->gateway_invoice_id);

            if (!$qr || empty($qr['status'])) {
                return;
            }

            $status = strtoupper($qr['status']);

            $this->recordPayment($order, [
                'transaction_id' => $qr['id'] ?? null,
                'payment_type' => 'QRIS - ' . (($qr['channel_code'] ?? 'QRIS') ?: 'QRIS'),
                'status' => 'pending',
                'raw_response' => $qr,
            ]);

            // DYNAMIC QR yang sudah dibayar menjadi INACTIVE di sisi Xendit.
            if ($status === 'INACTIVE') {
                $order->transaction?->update(['status' => 'paid']);
                $this->handlePaid($order);
            }

            return;
        }

        // Virtual Account (self-hosted) — status VA INACTIVE setelah dibayar.
        if ($type === 'va') {
            $va = $this->xendit->getVirtualAccount($order->gateway_invoice_id);

            if (!$va || empty($va['status'])) {
                return;
            }

            $status = strtoupper($va['status']);

            $this->recordPayment($order, [
                'transaction_id' => $va['id'] ?? null,
                'payment_type' => 'Virtual Account - ' . strtoupper((string) ($va['bank_code'] ?? 'VA')),
                'status' => $status === 'INACTIVE' ? 'paid' : 'pending',
                'raw_response' => $va,
            ]);

            if ($status === 'INACTIVE') {
                $this->handlePaid($order);
            }

            return;
        }

        // Retail Outlet (self-hosted) — status ACTIVE/INACTIVE.
        if ($type === 'retail') {
            $retail = $this->xendit->getRetailOutlet($order->gateway_invoice_id);

            if (!$retail || empty($retail['status'])) {
                return;
            }

            $status = strtoupper($retail['status']);

            $this->recordPayment($order, [
                'transaction_id' => $retail['id'] ?? null,
                'payment_type' => 'Minimarket - ' . strtoupper((string) ($retail['retail_outlet_name'] ?? 'Retail')),
                'status' => $status === 'INACTIVE' ? 'paid' : 'pending',
                'raw_response' => $retail,
            ]);

            if ($status === 'INACTIVE') {
                $this->handlePaid($order);
            }

            return;
        }

        // E-wallet charge — status COMPLETED/CAPTURED setelah dibayar.
        if ($type === 'ewallet') {
            $charge = $this->xendit->getEwalletCharge($order->gateway_invoice_id);

            if (!$charge || empty($charge['status'])) {
                return;
            }

            $status = strtoupper($charge['status']);

            $this->recordPayment($order, [
                'transaction_id' => $charge['id'] ?? null,
                'payment_type' => (string) ($charge['channel_code'] ?? 'E-Wallet') . ' (E-wallet)',
                'status' => in_array($status, ['SUCCEEDED', 'COMPLETED', 'CAPTURED']) ? 'paid' : 'pending',
                'raw_response' => $charge,
            ]);

            if (in_array($status, ['SUCCEEDED', 'COMPLETED', 'CAPTURED'])) {
                $this->handlePaid($order);
            }

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

            $product = \App\Models\Product::where('buyer_sku_code', $order->buyer_sku_code)->first();
            if ($product) {
                $product->consumeStock((int) ($order->quantity ?? 1));
            }
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
