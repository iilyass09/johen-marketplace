<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * Menangani pembuatan charge ke Xendit berdasarkan metode pembayaran yang dipilih
 * (QRIS / Virtual Account / E-wallet / Retail Outlet / Invoice).
 *
 * Seluruh metode ditampilkan & diproses di halaman sendiri (self-hosted), kecuali
 * e-wallet charge yang oleh Xendit diwajibkan redirect ke URL checkout-nya.
 */
class PaymentGatewayService
{
    protected XenditService $xendit;

    public function __construct(XenditService $xendit)
    {
        $this->xendit = $xendit;
    }

    /**
     * Mapping code metode pembayaran -> info gateway.
     */
    public function resolve(string $method): array
    {
        return match ($method) {
            'qris' => ['gateway_type' => 'qris'],
            'gopay', 'dana', 'ovo', 'shopeepay' => ['gateway_type' => 'ewallet', 'channel_code' => $this->ewalletChannelCode($method)],
            'bca_va', 'bca' => ['gateway_type' => 'va', 'bank_code' => 'BCA', 'label' => 'BCA Virtual Account'],
            'bni_va', 'bni' => ['gateway_type' => 'va', 'bank_code' => 'BNI', 'label' => 'BNI Virtual Account'],
            'mandiri_va', 'mandiri' => ['gateway_type' => 'va', 'bank_code' => 'MANDIRI', 'label' => 'Mandiri Virtual Account'],
            'alfamart' => ['gateway_type' => 'retail', 'retail_outlet_name' => 'ALFAMART', 'label' => 'Alfamart'],
            'indomaret' => ['gateway_type' => 'retail', 'retail_outlet_name' => 'INDOMARET', 'label' => 'Indomaret'],
            default => ['gateway_type' => 'invoice'],
        };
    }

    public function ewalletChannelCode(string $method): string
    {
        return match ($method) {
            'gopay' => 'GOPAY',
            'ovo' => 'ID_OVO',
            'dana' => 'ID_DANA',
            'shopeepay' => 'ID_SHOPEEPAY',
            default => 'ID_DANA',
        };
    }

    /**
     * Siapkan payment method yang benar berdasarkan code yang dipilih untuk
     * diteruskan sebagai reference pembayaran (disimpan di order.payment_method).
     */
    public function normalizeMethodCode(string $method): string
    {
        return match ($method) {
            'bca', 'bni', 'mandiri' => $method.'_va',
            default => $method,
        };
    }

    /**
     * Buat charge gateway untuk order & isi kolom gateway di order.
     * Return true bila berhasil, false bila gagal.
     */
    public function charge(Order $order, string $method, array $gatewayMeta): bool
    {
        $resolved = $this->resolve($method);
        $type = $resolved['gateway_type'];

        $subtotal = (int) $order->price;
        $currentTime = now();
        $device = request()->input('device_type');
        $customerEmail = Auth::check() ? Auth::user()->email : ($order->email ?: 'guest@johengaming.id');
        $customerName = Auth::check() ? Auth::user()->name : ($order->customer_name ?: $order->customer_number);

        // Kunci referensi yang dipakai untuk mencocokkan webhook = order.order_id.
        $referenceId = $order->order_id;

        $order->update([
            'payment_method' => $this->normalizeMethodCode($method),
            'gateway_type' => $type,
        ]);

        switch ($type) {
            case 'qris':
                return $this->chargeQris($order, $subtotal, $referenceId, $gatewayMeta);

            case 'va':
                return $this->chargeVa($order, $subtotal, $referenceId, $resolved, $customerName, $customerEmail);

            case 'ewallet':
                return $this->chargeEwallet($order, $subtotal, $referenceId, $resolved, $device, $customerEmail);

            case 'retail':
                return $this->chargeRetail($order, $subtotal, $referenceId, $resolved);

            default:
                return $this->chargeInvoice($order, $subtotal, $referenceId, $gatewayMeta, $customerName, $customerEmail);
        }
    }

    protected function chargeQris(Order $order, int $amount, string $referenceId, array $gatewayMeta): bool
    {
        $itemName = $gatewayMeta['item_name'] ?? $order->product_name;
        if (($order->quantity ?? 1) > 1) {
            $itemName .= ' x'.$order->quantity;
        }

        $result = $this->xendit->createQr([
            'reference_id' => $referenceId,
            'type' => 'DYNAMIC',
            'currency' => 'IDR',
            'amount' => $amount,
            'expires_at' => now()->addHours(24)->toIso8601String(),
            'description' => $itemName.' - '.$order->customer_number,
            'metadata' => [
                'order_id' => $referenceId,
                'product' => $itemName,
                'customer_number' => $order->customer_number,
            ],
        ]);

        if (! $result['success']) {
            Log::warning('QRIS charge gagal', ['order_id' => $referenceId, 'error' => $result]);

            return false;
        }

        $order->update([
            'gateway_invoice_id' => $result['qr_id'],
            'qr_string' => $result['qr_string'],
        ]);

        return true;
    }

    protected function chargeVa(Order $order, int $amount, string $referenceId, array $resolved, string $name, string $email): bool
    {
        $result = $this->xendit->createVirtualAccount([
            'external_id' => $referenceId,
            'bank_code' => $resolved['bank_code'],
            'name' => strtoupper(substr(preg_replace('/[^A-Za-z0-9 ]/', '', $name), 0, 45)) ?: 'JOHEM',
            'is_single_use' => true,
            'is_closed' => true,
            'expected_amount' => $amount,
            'expiration_date' => now()->addHours(24)->toIso8601String(),
            'customer' => [
                'given_names' => $name,
                'email' => $email,
            ],
            'currency' => 'IDR',
            'country' => 'ID',
        ]);

        if (! $result['success']) {
            Log::warning('VA charge gagal', ['order_id' => $referenceId, 'error' => $result]);

            return false;
        }

        $order->update([
            'gateway_invoice_id' => $result['id'],
            'va_number' => $result['account_number'],
            'gateway_extra' => [
                'bank_code' => $resolved['bank_code'],
                'expected_amount' => $result['expected_amount'],
                'label' => $resolved['label'] ?? null,
            ],
        ]);

        return true;
    }

    protected function chargeEwallet(Order $order, int $amount, string $referenceId, array $resolved, ?string $device, string $email): bool
    {
        $channelProperties = [];
        if ($resolved['channel_code'] === 'ID_OVO') {
            $channelProperties['mobile_number'] = $order->customer_number;
        }

        $result = $this->xendit->createEwalletCharge([
            'reference_id' => $referenceId,
            'currency' => 'IDR',
            'amount' => $amount,
            'checkout_method' => 'ONE_TIME_PAYMENT',
            'channel_code' => $resolved['channel_code'],
            'channel_properties' => $channelProperties,
            'metadata' => [
                'order_id' => $referenceId,
            ],
        ]);

        if (! $result['success']) {
            Log::warning('Ewallet charge gagal', ['order_id' => $referenceId, 'error' => $result]);

            return false;
        }

        $order->update([
            'gateway_invoice_id' => $result['id'],
            'checkout_url' => $result['checkout_url'],
            'gateway_extra' => [
                'channel_code' => $result['channel_code'],
                'is_redirect_required' => $result['is_redirect_required'],
                'actions' => $result['actions'] ?? null,
            ],
        ]);

        return true;
    }

    protected function chargeRetail(Order $order, int $amount, string $referenceId, array $resolved): bool
    {
        $result = $this->xendit->createRetailOutlet([
            'external_id' => $referenceId,
            'retail_outlet_name' => $resolved['retail_outlet_name'],
            'name' => substr(preg_replace('/[^A-Za-z0-9 ]/', '', (string) ($order->customer_name ?: $order->customer_number)), 0, 40) ?: 'JOHEM',
            'expected_amount' => $amount,
            'expiration_date' => now()->addHours(24)->toIso8601String(),
            'is_single_use' => true,
        ]);

        if (! $result['success']) {
            Log::warning('Retail charge gagal', ['order_id' => $referenceId, 'error' => $result]);

            return false;
        }

        $order->update([
            'gateway_invoice_id' => $result['id'],
            'payment_code' => $result['payment_code'],
            'gateway_extra' => [
                'retail_outlet_name' => $resolved['retail_outlet_name'],
                'expected_amount' => $result['expected_amount'],
                'label' => $resolved['label'] ?? null,
            ],
        ]);

        return true;
    }

    protected function chargeInvoice(Order $order, int $amount, string $referenceId, array $gatewayMeta, string $name, string $email): bool
    {
        $itemName = $gatewayMeta['item_name'] ?? $order->product_name;
        if (($order->quantity ?? 1) > 1) {
            $itemName .= ' x'.$order->quantity;
        }

        $result = $this->xendit->createInvoice([
            'external_id' => $referenceId,
            'amount' => $amount,
            'description' => $itemName.' - '.$order->customer_number,
            'payer_email' => $email,
            'customer' => [
                'given_names' => $name,
                'email' => $email,
            ],
            'invoice_duration' => 86400,
            'currency' => 'IDR',
            'items' => [
                [
                    'name' => $itemName,
                    'quantity' => (int) ($order->quantity ?: 1),
                    'price' => (int) ($gatewayMeta['unit_price'] ?? $amount),
                    'category' => $order->category,
                ],
            ],
            'success_redirect_url' => route('payment.detail', $order),
            'failure_redirect_url' => route('payment.detail', $order),
        ]);

        if (! $result['success']) {
            Log::warning('Invoice charge gagal', ['order_id' => $referenceId, 'error' => $result]);

            return false;
        }

        $order->update([
            'gateway_invoice_id' => $result['id'],
            'gateway_invoice_url' => $result['invoice_url'],
        ]);

        return true;
    }
}
