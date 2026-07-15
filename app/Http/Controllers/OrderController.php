<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Transaction;
use App\Services\DigiflazzService;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    protected DigiflazzService $digiflazz;
    protected MidtransService $midtrans;

    public function __construct(DigiflazzService $digiflazz, MidtransService $midtrans)
    {
        $this->digiflazz = $digiflazz;
        $this->midtrans = $midtrans;
    }

    public function create(Product $product)
    {
        return view('orders.create', compact('product'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'customer_number' => 'required|string',
            'customer_name' => 'nullable|string',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->stock < 1) {
            return back()->with('error', 'Maaf, stok produk ini sedang kosong');
        }

        $orderId = 'TUP-' . strtoupper(Str::random(10));

        $order = Order::create([
            'user_id' => Auth::id(),
            'order_id' => $orderId,
            'buyer_sku_code' => $product->buyer_sku_code,
            'customer_number' => $request->customer_number,
            'customer_name' => $request->customer_name,
            'product_name' => $product->product_name,
            'brand' => $product->brand,
            'category' => $product->category,
            'price' => $product->selling_price,
            'status' => 'pending',
        ]);

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => (int) $product->selling_price,
            ],
            'customer_details' => [
                'first_name' => Auth::user()->name,
                'email' => Auth::user()->email,
            ],
            'item_details' => [
                [
                    'id' => $product->id,
                    'price' => (int) $product->selling_price,
                    'quantity' => 1,
                    'name' => $product->product_name . ' - ' . $request->customer_number,
                ],
            ],
        ];

        $snapResult = $this->midtrans->createSnapTransaction($params);

        if (!$snapResult['success']) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $snapResult['message']], 500);
            }
            return back()->with('error', 'Gagal memproses pembayaran: ' . $snapResult['message']);
        }

        Transaction::create([
            'order_id' => $order->id,
            'gross_amount' => $product->selling_price,
            'status' => 'pending',
        ]);

        session(['snap_token' => $snapResult['token'], 'redirect_url' => null]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'redirect' => route('orders.show', $order),
            ]);
        }

        return redirect()->route('orders.show', $order);
    }

    public function show(Order $order)
    {
        $snapResult = [
            'token' => session('snap_token'),
            'redirect_url' => session('redirect_url'),
        ];
        return view('orders.show', compact('order', 'snapResult'));
    }

    public function myOrders()
    {
        $orders = Order::where('user_id', Auth::id())->latest()->get();
        return view('orders.index', compact('orders'));
    }
}
