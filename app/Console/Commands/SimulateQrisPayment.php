<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\XenditService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SimulateQrisPayment extends Command
{
    protected $signature = 'xendit:simulate-qris {order_id : Order ID (contoh: JM20260101ABCD)}';

    protected $description = 'Simulasikan pembayaran QRIS di mode test Xendit & picu webhook qr.payment untuk order tertentu';

    public function handle(XenditService $xendit): int
    {
        if ($xendit->isConfigured() === false) {
            $this->error('Xendit belum dikonfigurasi (XENDIT_SECRET_KEY kosong).');

            return self::FAILURE;
        }

        if (config('xendit.is_production')) {
            $this->error('command ini hanya untuk MODE TEST. XENDIT_IS_PRODUCTION saat ini true.');

            return self::FAILURE;
        }

        $order = Order::where('order_id', $this->argument('order_id'))->first();

        if (! $order) {
            $this->error('Order tidak ditemukan: '.$this->argument('order_id'));

            return self::FAILURE;
        }

        if (empty($order->gateway_invoice_id) || ($order->gateway_type ?? 'invoice') !== 'qris') {
            $this->error('Order '.$order->order_id.' tidak punya QRIS Xendit (gateway_type='.($order->gateway_type ?? 'null').', gateway_invoice_id='.($order->gateway_invoice_id ?? 'null').').');
            $this->line('Pastikan PAYMENT_SIMULATION=false saat order dibuat agar QRIS sungguhan terbentuk.');

            return self::FAILURE;
        }

        if ($order->status !== 'pending') {
            $this->warn('Order status = '.$order->status.', bukan pending. Simulasi hanya bermakna untuk order pending.');

            return self::SUCCESS;
        }

        $qrId = $order->gateway_invoice_id;
        $this->info('Simulasi pembayaran QRIS untuk order '.$order->order_id.' (QR id: '.$qrId.')...');

        try {
            $response = Http::withBasicAuth(config('xendit.secret_key'), '')
                ->acceptJson()
                ->withHeaders(['api-version' => '2022-07-31'])
                ->post('https://api.xendit.co/qr_codes/'.$qrId.'/payments/simulate');

            if ($response->successful()) {
                $data = $response->json();
                $this->info('Simulasi berhasil dipicu. Status: '.($data['status'] ?? '?'));
                $this->line('Webhook qr.payment akan dikirim ke endpoint callback yang terdaftar.');

                Log::info('Xendit QRIS simulate triggered', ['order_id' => $order->order_id, 'qr_id' => $qrId, 'response' => $data]);

                $this->line('');
                $this->line('Berikutnya, buka https://dashboard.xendit.co/callbacks utk cek webhook qr.payment,');
                $this->line('lalu cek status order via GET /payment/status/{order}.');

                return self::SUCCESS;
            }

            $this->error('Simulasi gagal (HTTP '.$response->status().'): '.$response->body());

            Log::error('Xendit QRIS simulate failed', ['order_id' => $order->order_id, 'body' => $response->body()]);

            return self::FAILURE;
        } catch (\Exception $e) {
            $this->error('Error: '.$e->getMessage());

            Log::error('Xendit QRIS simulate error', ['order_id' => $order->order_id, 'error' => $e->getMessage()]);

            return self::FAILURE;
        }
    }
}
