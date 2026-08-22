<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Order;
use App\Models\Product;
use App\Models\Transaction;
use App\Services\DigiflazzService;
use App\Services\XenditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    protected DigiflazzService $digiflazz;
    protected XenditService $xendit;

    public function __construct(DigiflazzService $digiflazz, XenditService $xendit)
    {
        $this->digiflazz = $digiflazz;
        $this->xendit = $xendit;
    }

    public function create(Product $product)
    {
        return view('orders.create', compact('product'));
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
        $subtotal = (int) $product->selling_price * $quantity;

        if ($request->filled('promo_code')) {
            $code = strtoupper(trim($request->promo_code));
            if ($code === 'JOHENI10' || $code === 'JOHENGAMING10') {
                $subtotal = (int) round($subtotal * 0.9);
            }
        }

        $orderId = 'TUP-' . strtoupper(Str::random(10));

        $order = Order::create([
            'user_id' => Auth::id(),
            'order_id' => $orderId,
            'buyer_sku_code' => $product->buyer_sku_code,
            'customer_number' => trim($request->customer_number),
            'zone_id' => $zoneId,
            'customer_name' => $request->customer_name,
            'email' => $request->email,
            'product_name' => $product->product_name,
            'brand' => $product->brand,
            'category' => $product->category,
            'price' => $subtotal,
            'quantity' => $quantity,
            'status' => 'pending',
        ]);

        $demo = true;

        // PAYMENT_SIMULATION aktif → lewati pembuatan invoice Xendit (mode demo penuh).
        if (!config('services.payment.simulation') && $this->xendit->isConfigured()) {
            $itemName = $product->product_name;
            if ($quantity > 1) {
                $itemName .= ' x' . $quantity;
            }

            $payerEmail = Auth::check()
                ? Auth::user()->email
                : ($request->email ?: 'guest@johengaming.id');
            $payerName = Auth::check()
                ? Auth::user()->name
                : ($request->customer_name ?: $request->customer_number);

            $invoiceResult = $this->xendit->createInvoice([
                'external_id' => $orderId,
                'amount' => $subtotal,
                'description' => $itemName . ' - ' . $request->customer_number,
                'payer_email' => $payerEmail,
                'customer' => [
                    'given_names' => $payerName,
                    'email' => $payerEmail,
                ],
                'invoice_duration' => 86400,
                'currency' => 'IDR',
                'items' => [
                    [
                        'name' => $itemName,
                        'quantity' => $quantity,
                        'price' => (int) $product->selling_price,
                        'category' => $product->category,
                    ],
                ],
                'success_redirect_url' => route('payment.detail', $order),
                'failure_redirect_url' => route('payment.detail', $order),
            ]);

            if ($invoiceResult['success']) {
                $demo = false;
                $order->update([
                    'gateway_invoice_id' => $invoiceResult['id'],
                    'gateway_invoice_url' => $invoiceResult['invoice_url'],
                ]);
            }
        }

        Transaction::create([
            'order_id' => $order->id,
            'gross_amount' => $subtotal,
            'status' => 'pending',
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'redirect' => route('payment.detail', $order),
                'demo' => $demo,
            ]);
        }

        return redirect()->route('orders.show', $order);
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
        $orders = Order::where('user_id', Auth::id())->latest()->paginate(10);

        $orders->load('transaction');

        $brandNames = $orders->pluck('brand')->unique()->filter();
        $brands = Brand::whereIn('name', $brandNames)->get()->keyBy('name');

        $skuCodes = $orders->pluck('buyer_sku_code')->unique()->filter();
        $products = Product::whereIn('buyer_sku_code', $skuCodes)->get()->keyBy('buyer_sku_code');

        return view('orders.index', compact('orders', 'brands', 'products'));
    }
}
