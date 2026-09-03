<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\XenditService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SimulateVirtualAccountPayment extends Command
{
    protected $signature = 'xendit:simulate-va {order_id : Order ID (contoh: TUP-ABC123XYZ)}';

    protected $description = 'Simulasikan pembayaran Virtual Account di mode test Xendit untuk order tertentu';

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

        if (empty($order->gateway_invoice_id) || ($order->gateway_type ?? 'invoice') !== 'va') {
            $this->error('Order '.$order->order_id.' tidak punya Virtual Account Xendit (gateway_type='.($order->gateway_type ?? 'null').').');
            $this->line('Pastikan payment_method BCA/BNI/Mandiri & SIMULASI=false saat order dibuat.');

            return self::FAILURE;
        }

        if ($order->status !== 'pending') {
            $this->warn('Order status = '.$order->status.', bukan pending. Simulasi hanya bermakna untuk order pending.');

            return self::SUCCESS;
        }

        $vaId = $order->gateway_invoice_id;
        $amount = (int) ($order->price ?: 0);
        $this->info('Simulasi pembayaran VA untuk order '.$order->order_id.' (VA id: '.$vaId.', amount: '.$amount.').');

        try {
            $response = Http::withBasicAuth(config('xendit.secret_key'), '')
                ->acceptJson()
                ->post('https://api.xendit.co/callback_virtual_accounts/external_id='.$order->order_id.'/simulate_payment', [
                    'amount' => $amount,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->info('Simulasi berhasil dipicu. Response: '.json_encode($data));
                $this->line('Webhook fva.paid akan dikirim ke endpoint callback yang terdaftar.');

                Log::info('Xendit VA simulate triggered', ['order_id' => $order->order_id, 'va_id' => $vaId, 'response' => $data]);

                return self::SUCCESS;
            }

            $this->error('Simulasi gagal (HTTP '.$response->status().'): '.$response->body());

            Log::error('Xendit VA simulate failed', ['order_id' => $order->order_id, 'body' => $response->body()]);

            return self::FAILURE;
        } catch (\Exception $e) {
            $this->error('Error: '.$e->getMessage());

            Log::error('Xendit VA simulate error', ['order_id' => $order->order_id, 'error' => $e->getMessage()]);

            return self::FAILURE;
        }
    }
}
