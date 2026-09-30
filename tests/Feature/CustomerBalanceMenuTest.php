<?php

namespace Tests\Feature;

use App\Models\BalanceTransaction;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use App\Services\BalanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Menu customer: halaman /orders harus menampilkan saldo, riwayat mutasi, dan
 * status refund per order.
 */
class CustomerBalanceMenuTest extends TestCase
{
    use RefreshDatabase;

    private function user(array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'username' => 'u'.Str::lower(Str::random(8)),
        ], $overrides));
    }

    private function order(User $user, array $overrides = []): Order
    {
        $order = Order::create(array_merge([
            'user_id' => $user->id,
            'email' => $user->email,
            'order_id' => 'TUP-'.strtoupper(Str::random(10)),
            'buyer_sku_code' => 'mlbb-5-diamond',
            'customer_number' => '08123456789',
            'product_name' => '5 Diamond',
            'brand' => 'MOBILE LEGENDS',
            'category' => 'Game',
            'price' => 50000,
            'quantity' => 1,
            'status' => 'pending',
        ], $overrides));

        Transaction::create(['order_id' => $order->id, 'gross_amount' => $overrides['price'] ?? 50000]);

        return $order;
    }

    public function test_orders_page_requires_login(): void
    {
        $this->get('/orders')->assertRedirect('/login');
    }

    /**
     * Regresi: view dulu mendeklarasikan fungsi PHP `paymentLogo()` di dalam
     *
     * @php, sehingga render kedua pada proses yang sama menambah fatal error
     * "Cannot redeclare paymentLogo()". View harus aman di-render berulang.
     */
    public function test_orders_page_can_be_rendered_repeatedly_in_one_process(): void
    {
        $user = $this->user();

        $this->actingAs($user)->get('/orders')->assertOk();
        $this->actingAs($user)->get('/orders')->assertOk();
        $this->actingAs($user)->get(route('orders.my'))->assertOk();
    }

    public function test_orders_page_shows_the_balance_panel(): void
    {
        $user = $this->user();

        $this->actingAs($user)->get('/orders')
            ->assertOk()
            ->assertSee('Saldo Saya');
    }

    public function test_orders_page_lists_the_balance_history(): void
    {
        $user = $this->user();
        $order = $this->order($user, ['price' => 50000]);

        app(BalanceService::class)->markPaid($order);

        $this->actingAs($user)->get('/orders')
            ->assertOk()
            ->assertSee('Saldo Saya')
            ->assertSee('50.000', escape: false);

        $this->assertDatabaseHas('balance_transactions', [
            'user_id' => $user->id,
            'type' => BalanceTransaction::TYPE_DEPOSIT,
        ]);
    }

    public function test_orders_page_marks_refunded_orders(): void
    {
        $user = $this->user();
        $order = $this->order($user, ['price' => 50000]);

        $balance = app(BalanceService::class);
        $balance->markPaid($order);
        $balance->markRefunded($order->refresh(), 'Stok habis');

        $this->actingAs($user)->get('/orders')->assertOk();

        $order->refresh();
        $this->assertTrue($order->isRefunded());
        $this->assertSame('refunded', $order->saldo_status);
    }

    public function test_orders_page_does_not_leak_another_users_balance(): void
    {
        $mine = $this->user();
        $other = $this->user();

        app(BalanceService::class)->markPaid($this->order($other, ['price' => 999000]));

        $this->actingAs($mine)->get('/orders')
            ->assertOk()
            ->assertDontSee('999.000', escape: false);
    }

    public function test_status_endpoint_reports_saldo_fields(): void
    {
        $user = $this->user();
        $order = $this->order($user, ['price' => 50000, 'status' => 'success']);

        $this->actingAs($user)->getJson(route('payment.status', $order))
            ->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('saldo_status', 'none')
            ->assertJsonPath('saldo_refunded', false);
    }

    public function test_status_endpoint_reports_a_refunded_order(): void
    {
        $user = $this->user();
        $order = $this->order($user, ['price' => 50000]);

        $balance = app(BalanceService::class);
        $balance->markPaid($order);
        $balance->markRefunded($order->refresh());

        $this->actingAs($user)->getJson(route('payment.status', $order->refresh()))
            ->assertOk()
            ->assertJsonPath('saldo_status', 'refunded')
            ->assertJsonPath('saldo_amount', 50000)
            ->assertJsonPath('saldo_refunded', true);
    }
}
