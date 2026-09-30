<?php

namespace Tests\Feature;

use App\Models\BalanceTransaction;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use App\Services\BalanceService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * `digiflazz:reconcile` — jaring pengaman untuk order topup yang menggantung.
 */
class ReconcileDigiflazzOrdersTest extends TestCase
{
    use RefreshDatabase;

    private array $digiflazz = [];

    private array $requests = [];

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'digiflazz.reconcile.grace_minutes' => 30,
            'digiflazz.reconcile.max_polls' => 12,
            'digiflazz.reconcile.chunk' => 50,
        ]);

        $this->digiflazz = ['status' => ['data' => ['status' => 'Pending']]];
        $this->requests = [];

        Http::fake(function (Request $request) {
            $cmd = $request['cmd'] ?? '';
            $this->requests[] = $cmd;

            return Http::response($this->digiflazz[$cmd] ?? ['data' => ['status' => 'Pending']], 200);
        });
    }

    private function reply(string $cmd, array $body): void
    {
        $this->digiflazz[$cmd] = $body;
    }

    private function user(): User
    {
        return User::factory()->create(['username' => 'u'.Str::lower(Str::random(8))]);
    }

    private function pending(User $user, float $price, array $overrides = []): Order
    {
        $order = Order::create(array_merge([
            'user_id' => $user->id,
            'order_id' => 'TUP-'.strtoupper(Str::random(10)),
            'buyer_sku_code' => 'mlbb-5-diamond',
            'customer_number' => '08123456789',
            'product_name' => '5 Diamond',
            'brand' => 'MOBILE LEGENDS',
            'category' => 'Game',
            'price' => $price,
            'quantity' => 1,
            'status' => 'processing',
            'digiflazz_started_at' => now(),
        ], $overrides));

        Transaction::create([
            'order_id' => $order->id,
            'gross_amount' => $price,
            'status' => 'processing',
        ]);

        // Simulasikan pembayaran sudah masuk dan saldo sudah dikunci.
        app(BalanceService::class)->markPaid($order);

        return $order->refresh();
    }

    public function test_it_does_nothing_when_there_is_no_stuck_order(): void
    {
        $this->artisan('digiflazz:reconcile')
            ->expectsOutputToContain('Tidak ada topup yang perlu direkonsiliasi.')
            ->assertSuccessful();

        $this->assertSame([], $this->requests);
    }

    public function test_it_ignores_orders_that_are_not_processing(): void
    {
        $user = $this->user();
        $this->pending($user, 50000, ['status' => 'success', 'digiflazz_started_at' => now()->subHours(5)]);

        $this->artisan('digiflazz:reconcile')->assertSuccessful();

        $this->assertSame([], $this->requests);
    }

    public function test_order_within_grace_period_is_left_alone(): void
    {
        $user = $this->user();
        $order = $this->pending($user, 50000, ['digiflazz_started_at' => now()->subMinutes(5)]);

        $this->artisan('digiflazz:reconcile')
            ->expectsOutputToContain('1 masih diproses')
            ->assertSuccessful();

        $order->refresh();
        $this->assertSame('processing', $order->status);
        $this->assertSame('held', $order->saldo_status);
        $this->assertSame(50000.0, app(BalanceService::class)->balanceFor($user->id));
    }

    public function test_order_past_grace_period_is_refunded(): void
    {
        $user = $this->user();
        $order = $this->pending($user, 50000, ['digiflazz_started_at' => now()->subMinutes(90)]);

        $this->artisan('digiflazz:reconcile')
            ->expectsOutputToContain('lewat batas waktu, saldo dikembalikan')
            ->assertSuccessful();

        $order->refresh();
        $this->assertSame('failed', $order->status);
        $this->assertSame('refunded', $order->saldo_status);
        $this->assertNotNull($order->saldo_released_at);
        $this->assertStringContainsString('batas waktu', $order->note);
        $this->assertSame(50000.0, app(BalanceService::class)->balanceFor($user->id));
    }

    public function test_order_past_poll_limit_is_refunded(): void
    {
        $user = $this->user();
        $order = $this->pending($user, 40000, ['digiflazz_poll_count' => 20]);

        $this->artisan('digiflazz:reconcile')->assertSuccessful();

        $order->refresh();
        $this->assertSame('refunded', $order->saldo_status);
        $this->assertSame(40000.0, app(BalanceService::class)->balanceFor($user->id));
    }

    public function test_it_polls_before_refunding_so_success_is_never_missed(): void
    {
        $user = $this->user();
        $order = $this->pending($user, 50000, ['digiflazz_started_at' => now()->subMinutes(90)]);

        // Ternyata topup-nya sukses, cuma webhook-nya yang telat.
        $this->reply('status', ['data' => ['status' => 'Sukses', 'sn' => 'SN-LATE']]);

        $this->artisan('digiflazz:reconcile')
            ->expectsOutputToContain('topup berhasil, saldo terpakai')
            ->assertSuccessful();

        $order->refresh();
        $this->assertSame('success', $order->status);
        $this->assertSame('consumed', $order->saldo_status);
        $this->assertSame('SN-LATE', $order->note);
        $this->assertSame(0.0, app(BalanceService::class)->balanceFor($user->id));
        $this->assertSame(['status'], $this->requests);
    }

    public function test_failed_poll_settles_immediately(): void
    {
        $user = $this->user();
        $order = $this->pending($user, 50000, ['digiflazz_started_at' => now()->subMinutes(90)]);

        $this->reply('status', ['data' => ['status' => 'Gagal', 'message' => 'Stok habis']]);

        $this->artisan('digiflazz:reconcile')
            ->expectsOutputToContain('gagal, saldo dikembalikan')
            ->assertSuccessful();

        $order->refresh();
        $this->assertSame('failed', $order->status);
        $this->assertSame('refunded', $order->saldo_status);
        $this->assertSame('Stok habis', $order->note);
    }

    public function test_refund_only_mode_skips_polling(): void
    {
        $user = $this->user();
        $order = $this->pending($user, 50000, ['digiflazz_started_at' => now()->subMinutes(90)]);

        $this->artisan('digiflazz:reconcile --refund-only')->assertSuccessful();

        $order->refresh();
        $this->assertSame('refunded', $order->saldo_status);
        $this->assertSame([], $this->requests, '--refund-only tidak boleh memanggil Digiflazz.');
    }

    public function test_refund_only_mode_skips_orders_inside_the_grace_period(): void
    {
        $user = $this->user();
        $order = $this->pending($user, 50000, ['digiflazz_started_at' => now()->subMinutes(5)]);

        $this->artisan('digiflazz:reconcile --refund-only')->assertSuccessful();

        $this->assertSame('held', $order->refresh()->saldo_status);
    }

    public function test_limit_caps_how_many_orders_are_processed(): void
    {
        $user = $this->user();
        $orders = collect(range(1, 3))->map(fn () => $this->pending($user, 10000, [
            'digiflazz_started_at' => now()->subMinutes(90),
        ]));

        $this->artisan('digiflazz:reconcile --limit=2 --refund-only')->assertSuccessful();

        $refunded = $orders->filter(fn (Order $o) => $o->refresh()->saldo_status === 'refunded');
        $this->assertCount(2, $refunded);

        // Order ketiga belum disentuh sama sekali.
        $stillHeld = $orders->filter(fn (Order $o) => $o->refresh()->saldo_status === 'held');
        $this->assertCount(1, $stillHeld);
        $this->assertSame(0, $stillHeld->first()->digiflazz_poll_count);

        // Saldo tetap utuh: `release` tidak pernah menarik uang.
        $this->assertSame(30000.0, app(BalanceService::class)->balanceFor($user->id));
        $this->assertSame(2, BalanceTransaction::where('type', BalanceTransaction::TYPE_RELEASE)->count());
    }

    public function test_running_twice_does_not_refund_the_same_order_twice(): void
    {
        $user = $this->user();
        $order = $this->pending($user, 50000, ['digiflazz_started_at' => now()->subMinutes(90)]);

        $this->artisan('digiflazz:reconcile --refund-only')->assertSuccessful();
        $this->artisan('digiflazz:reconcile --refund-only')
            ->expectsOutputToContain('Tidak ada topup yang perlu direkonsiliasi.')
            ->assertSuccessful();

        $order->refresh();
        $this->assertSame('refunded', $order->saldo_status);
        $this->assertSame(1, BalanceTransaction::where('order_id', $order->id)->where('type', BalanceTransaction::TYPE_RELEASE)->count());
        $this->assertSame(50000.0, app(BalanceService::class)->balanceFor($user->id));
    }

    public function test_mixed_batch_is_settled_per_order(): void
    {
        $user = $this->user();

        $stuck = $this->pending($user, 10000, ['digiflazz_started_at' => now()->subMinutes(90)]);
        $safe = $this->pending($user, 20000, ['digiflazz_started_at' => now()->subMinutes(5)]);
        $exhausted = $this->pending($user, 30000, ['digiflazz_poll_count' => 20]);

        $this->artisan('digiflazz:reconcile')->assertSuccessful();

        $this->assertSame('refunded', $stuck->refresh()->saldo_status);
        $this->assertSame('held', $safe->refresh()->saldo_status);
        $this->assertSame('refunded', $exhausted->refresh()->saldo_status);

        // Tidak ada uang yang hilang: 60.000 masuk, semua utuh di user.
        $this->assertSame(60000.0, app(BalanceService::class)->balanceFor($user->id));
    }

    public function test_command_is_registered_in_the_scheduler(): void
    {
        $events = collect(app(Schedule::class)->events())
            ->filter(fn ($e) => str_contains($e->command ?? '', 'digiflazz:reconcile'));

        $this->assertCount(1, $events, 'digiflazz:reconcile harus terdaftar di routes/console.php.');
        $this->assertSame('*/5 * * * *', $events->first()->expression);
    }
}
