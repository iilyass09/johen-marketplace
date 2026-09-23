<?php

namespace App\Console\Commands;

use App\Models\PaymentMethod;
use App\Services\PaymentGatewayService;
use App\Services\XenditService;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CheckXenditChannels extends Command
{
    protected $signature = 'xendit:check-channels
                            {--method= : Cek hanya satu metode (code, contoh: bca_va)}
                            {--amount=10000 : Nominal uji (IDR)}';

    protected $description = 'Cek koneksi setiap metode pembayaran ke Xendit (membuat charge test sungguhan)';

    public function handle(XenditService $xendit, PaymentGatewayService $gateway): int
    {
        if (! $xendit->isConfigured()) {
            $this->error('Xendit belum dikonfigurasi (XENDIT_SECRET_KEY kosong). Isi .env dulu.');

            return self::FAILURE;
        }

        $mode = config('xendit.is_production') ? 'LIVE/PRODUCTION' : 'TEST/DEVELOPMENT';
        $amount = max(1000, (int) $this->option('amount'));

        $this->info('Memeriksa koneksi metode pembayaran ke Xendit (mode: '.$mode.', amount: Rp '.number_format($amount, 0, ',', '.').')');
        $this->newLine();

        $query = PaymentMethod::where('is_active', true)->orderBy('category')->orderBy('name');
        if ($method = $this->option('method')) {
            $query->where('code', $method);
        }

        $methods = $query->get();

        if ($methods->isEmpty()) {
            $this->warn('Tidak ada metode pembayaran aktif untuk diuji.');

            return self::FAILURE;
        }

        $rows = [];
        $ok = 0;

        foreach ($methods as $pm) {
            $resolved = $gateway->resolve($pm->code);
            $type = $resolved['gateway_type'];
            $reference = 'CHK-'.strtoupper(Str::random(10));
            $now = now()->addHours(24)->toIso8601String();
            $redirectUrl = route('home');

            switch ($type) {
                case 'qris':
                    $result = $xendit->createQr([
                        'reference_id' => $reference,
                        'type' => 'DYNAMIC',
                        'currency' => 'IDR',
                        'amount' => $amount,
                        'expires_at' => $now,
                        'description' => 'Channel check '.$pm->code.' - '.$reference,
                        'metadata' => ['purpose' => 'channel-check'],
                    ]);
                    $channel = 'QRIS';
                    break;

                case 'va':
                    $result = $xendit->createVirtualAccount([
                        'external_id' => $reference,
                        'bank_code' => $resolved['bank_code'],
                        'name' => 'JOHEM',
                        'is_single_use' => true,
                        'is_closed' => true,
                        'expected_amount' => $amount,
                        'expiration_date' => $now,
                        'customer' => ['given_names' => 'Check Channel'],
                        'currency' => 'IDR',
                        'country' => 'ID',
                    ]);
                    $channel = $resolved['bank_code'];
                    break;

                case 'ewallet':
                    $channelProperties = [
                        'success_redirect_url' => $redirectUrl,
                        'failure_redirect_url' => $redirectUrl,
                        'cancel_redirect_url' => $redirectUrl,
                    ];

                    // OVO: mobile_number wajib; app_id wajib bila dikonfigurasi.
                    if ($resolved['channel_code'] === 'ID_OVO') {
                        $channelProperties['mobile_number'] = $resolved['channel_code'] === 'ID_OVO' ? '6281234567890' : null;
                        $appId = (string) config('xendit.ovo_app_id', '');
                        if ($appId !== '') {
                            $channelProperties['app_id'] = $appId;
                        }
                    }

                    $result = $xendit->createEwalletCharge([
                        'reference_id' => $reference,
                        'currency' => 'IDR',
                        'amount' => $amount,
                        'checkout_method' => 'ONE_TIME_PAYMENT',
                        'channel_code' => $resolved['channel_code'],
                        'channel_properties' => $channelProperties,
                        'callback_url' => route('payment.notification'),
                        'metadata' => ['order_id' => $reference],
                    ]);
                    $channel = $resolved['channel_code'];
                    break;

                case 'retail':
                    $result = $xendit->createRetailOutlet([
                        'external_id' => $reference,
                        'retail_outlet_name' => $resolved['retail_outlet_name'],
                        'name' => 'JOHEM',
                        'expected_amount' => $amount,
                        'expiration_date' => $now,
                        'is_single_use' => true,
                    ]);
                    $channel = $resolved['retail_outlet_name'];
                    break;

                default:
                    $result = $xendit->createInvoice([
                        'external_id' => $reference,
                        'amount' => $amount,
                        'description' => 'Channel check '.$pm->code.' - '.$reference,
                        'payer_email' => 'check@johengaming.id',
                        'customer' => ['given_names' => 'Check Channel', 'email' => 'check@johengaming.id'],
                        'invoice_duration' => 86400,
                        'currency' => 'IDR',
                        'items' => [
                            ['name' => 'Channel Check', 'quantity' => 1, 'price' => $amount, 'category' => 'Xendit Check'],
                        ],
                    ]);
                    $channel = 'INVOICE';
                    break;
            }

            $detail = $this->describeResult($type, $result);

            if ($result['success'] ?? false) {
                $ok++;
                $rows[] = [$pm->code, $channel, 'OK', $detail];
            } else {
                $rows[] = [$pm->code, $channel, 'GAGAL', $detail];
            }

            $this->line('  - '.$pm->code.': '.($result['success'] ?? false ? '<info>OK</info>' : '<error>GAGAL</error>').'  '.$detail);
        }

        $this->newLine();
        $this->table(['Metode', 'Channel', 'Koneksi', 'Detail'], $rows);
        $this->newLine();
        $this->components->info('Hasil: '.$ok.'/'.$methods->count().' metode berhasil terkoneksi ke Xendit.');

        return $ok === $methods->count() ? self::SUCCESS : self::FAILURE;
    }

    private function describeResult(string $type, array $result): string
    {
        if (empty($result['success'])) {
            return (string) ($result['message'] ?? 'Error tidak diketahui');
        }

        return match ($type) {
            'qris' => 'QR string tersedia ('.($result['qr_id'] ?? '-').')',
            'va' => 'VA number: '.($result['account_number'] ?? '-'),
            'ewallet' => 'checkout_url: '.($result['checkout_url'] ?? '-'),
            'retail' => 'payment_code: '.($result['payment_code'] ?? '-'),
            default => 'invoice_url: '.($result['invoice_url'] ?? '-'),
        };
    }
}