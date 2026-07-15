<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $brands = Brand::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $popularBrands = Brand::where('is_active', true)
            ->where('is_popular', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('home', compact('brands', 'popularBrands'));
    }

    public function dashboard()
    {
        $user = Auth::user();

        $totalOrders = Order::where('user_id', $user->id)->count();

        $totalSpent = Order::where('user_id', $user->id)
            ->where('status', 'success')
            ->sum('price');

        $recentOrders = Order::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard', compact('totalOrders', 'totalSpent', 'recentOrders'));
    }

    public function getApiProducts(Request $request)
    {
        $query = Product::where('is_active', true);

        if ($request->filled('brand')) {
            $query->where('brand', $request->brand);
        }

        $products = $query->get();

        return response()->json($products);
    }

    public function gameDetail(Brand $brand)
    {
        $products = Product::where('brand', $brand->name)
            ->where('is_active', true)
            ->orderBy('type')
            ->orderBy('selling_price')
            ->get();

        $paymentMethods = PaymentMethod::where('is_active', true)->get();

        return view('game-detail', compact('brand', 'products', 'paymentMethods'));
    }

    public function searchBrands(Request $request)
    {
        $q = $request->input('q', '');

        $brands = Brand::where('is_active', true)
            ->where('name', 'like', "%{$q}%")
            ->orderBy('name')
            ->limit(10)
            ->get(['name as brand']);

        return response()->json($brands);
    }

    public function getPaymentMethods()
    {
        $methods = PaymentMethod::where('is_active', true)->get(['name', 'code', 'photo']);
        return response()->json($methods);
    }

    public function checkOrder(Request $request)
    {
        $q = $request->input('q', '');

        $order = Order::where('order_id', $q)
            ->orWhere('customer_number', $q)
            ->orWhereHas('user', function ($query) use ($q) {
                $query->where('email', $q);
            })
            ->first();

        if (!$order) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        return response()->json([
            'order_id' => $order->order_id,
            'product_name' => $order->product_name,
            'customer_number' => $order->customer_number,
            'price' => $order->price,
            'status' => $order->status,
            'processed_at' => $order->updated_at ? $order->updated_at->format('d M Y H:i') : null,
        ]);
    }
}
