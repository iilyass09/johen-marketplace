<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Order;
use App\Models\Product;
use App\Models\Transaction;
use App\Services\DigiflazzService;
use App\Services\PaymentGatewayService;
use App\Services\XenditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    protected DigiflazzService $digiflazz;
    protected XenditService $xendit;
    protected PaymentGatewayService $gateway;

    public function __construct(DigiflazzService $digiflazz, XenditService $xendit, PaymentGatewayService $gateway)
    {
        $this->digiflazz = $digiflazz;
        $this->xendit = $xendit;
        $this->gateway = $gateway;
    }

    public function create(Product $product)
    {
        $paymentMethods = \App\Models\PaymentMethod::where('is_active', true)->get();

        return view('orders.create', compact('product', 'paymentMethods'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'customer_number' => 'required|string|max:100',
            'zone_id' => 'nullable|string|max:20|regex:/^[A-Za-z0-9]+$/',
            'customer_name' => 'nullable|string|max:100',
            'email' => 'nullable|email',
            'quantity' => 'nullable|integer|min:1|max:99',
            'payment_method' => 'nullable|string|max:50',
        ], [
            'zone_id.regex' => 'Zone ID hanya boleh berisi huruf dan angka.',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->stock < 1) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Maaf, stok produk ini sedang kosong'], 422);
            }
            return back()->with('error', 'Maaf, stok produk ini sedang kosong');
        }

        // Game yang membutuhkan Zone ID wajib mengisinya (divalidasi di server).
        $brandRequiresZone = Brand::where('name', $product->brand)->value('requires_zone_id');
        $zoneId = $request->filled('zone_id') ? trim($request->zone_id) : null;

        if ($brandRequiresZone && empty($zoneId)) {
            $message = 'Zone ID wajib diisi untuk game ini.';
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            return back()->withErrors(['zone_id' => $message])->withInput();
        }

        $quantity = (int) ($request->quantity ?? 1);

        $order = $this->createTopupOrder($product, [
            'customer_number' => trim($request->customer_number),
            'zone_id' => $zoneId,
            'customer_name' => $request->customer_name,
            'email' => $request->email,
            'quantity' => $quantity,
            'promo_code' => $request->promo_code,
            'payment_method' => $request->payment_method,
        ]);

        $demo = $order->gateway_invoice_id ? false : true;

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'redirect' => route('payment.detail', $order),
                'demo' => $demo,
            ]);
        }

        return redirect()->route('orders.show', $order);
    }

    /**
     * "Beli Lagi": buat ulang order dari order sebelumnya langsung ke halaman pembayaran.
     */
    public function reorder(Request $request, Order $source)
    {
        $product = Product::where('buyer_sku_code', $source->buyer_sku_code)
            ->where('is_active', true)
            ->first();

        if (!$product || $product->stock < 1) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Stok produk sedang kosong.'], 422);
            }
            return back()->with('error', 'Maaf, stok produk ini sedang kosong');
        }

        $order = $this->createTopupOrder($product, [
            'customer_number' => $source->customer_number,
            'zone_id' => $source->effective_zone_id,
            'customer_name' => $source->customer_name,
            'email' => $source->email,
            'quantity' => (int) ($source->quantity ?: 1),
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'redirect' => route('payment.detail', $order), 'demo' => true]);
        }

        return redirect()->route('payment.detail', $order);
    }

    /**
     * Membuat order top up baru lengkap dengan charge gateway + transaksi.
     */
    private function createTopupOrder(Product $product, array $input): Order
    {
        $quantity = (int) ($input['quantity'] ?? 1);
        $customerNumber = $input['customer_number'];
        $zoneId = $input['zone_id'] ?? null;
        $customerName = $input['customer_name'] ?? null;
        $email = $input['email'] ?? null;

        $subtotal = (int) $product->selling_price * $quantity;

        if (!empty($input['promo_code'])) {
            $code = strtoupper(trim((string) $input['promo_code']));
            if ($code === 'JOHENI10' || $code === 'JOHENGAMING10') {
                $subtotal = (int) round($subtotal * 0.9);
            }
        }

        $orderId = 'TUP-' . strtoupper(Str::random(10));

        $order = Order::create([
            'user_id' => Auth::id(),
            'order_id' => $orderId,
            'buyer_sku_code' => $product->buyer_sku_code,
            'customer_number' => $customerNumber,
            'zone_id' => $zoneId,
            'customer_name' => $customerName,
            'email' => $email,
            'product_name' => $product->product_name,
            'brand' => $product->brand,
            'category' => $product->category,
            'price' => $subtotal,
            'quantity' => $quantity,
            'status' => 'pending',
        ]);

        if (!config('services.payment.simulation') && $this->xendit->isConfigured()) {
            $method = !empty($input['payment_method']) ? $input['payment_method'] : config('services.payment.channel', 'qris');

            $this->gateway->charge($order, $method, [
                'item_name' => $product->product_name,
                'unit_price' => (int) $product->selling_price,
            ]);
        }

        Transaction::create([
            'order_id' => $order->id,
            'gross_amount' => $subtotal,
            'status' => 'pending',
        ]);

        return $order;
    }

    public function show(Order $order)
    {
        $brand = Brand::where('name', $order->brand)->first();
        $product = Product::where('buyer_sku_code', $order->buyer_sku_code)->first();

        $recommendedProducts = Product::where('brand', $order->brand)
            ->where('is_active', true)
            ->where('type', 'instant')
            ->orderBy('selling_price')
            ->limit(3)
            ->get();

        return view('orders.show', compact('order', 'brand', 'product', 'recommendedProducts'));
    }

    public function myOrders()
    {
        $user = Auth::user();

        $orders = Order::where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhere('email', $user->email);
            })
            ->latest()
            ->paginate(10);

        $orders->load('transaction');

        $brandNames = $orders->pluck('brand')->unique()->filter();
        $brands = Brand::whereIn('name', $brandNames)
            ->orWhereIn(DB::raw('UPPER(name)'), $brandNames->map(fn($n) => strtoupper((string) $n)))
            ->get()
            ->keyBy(fn($b) => strtolower($b->name));

        $skuCodes = $orders->pluck('buyer_sku_code')->unique()->filter();
        $products = Product::whereIn('buyer_sku_code', $skuCodes)->get()->keyBy('buyer_sku_code');

        return view('orders.index', compact('orders', 'brands', 'products'));
    }
}
