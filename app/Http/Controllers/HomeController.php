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
        if (Auth::guard('admin')->check() && !Auth::guard('web')->check()) {
            return redirect()->route('admin.dashboard');
        }

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
        if (Auth::guard('admin')->check() && !Auth::guard('web')->check()) {
            return redirect()->route('admin.dashboard');
        }

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
        $methods = PaymentMethod::where('is_active', true)->get(['name', 'code', 'icon', 'photo']);
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

    public function checkTransaction()
    {
        return view('check-transaction');
    }

    public function leaderboard(Request $request)
    {
        $names = [
            'Muhammad Ilyas', 'Muhammad Fadlan', 'Muhammad Taopik', 'Ahmad Rizaldi',
            'Siti Nurhaliza', 'Dimas Pratama', 'Rina Marlina', 'Fajar Ramadhan',
            'Dewi Lestari', 'Bambang Suprapto', 'Rizky_Ace', 'Dinda.ML',
            'ProGamerID', 'FaizFF07', 'ValoQueen', 'Iky_Sniper',
            'Nadia_Gaming', 'Rafi_Supremasi', 'Citra_Queen', 'Aldo_Boss',
        ];

        $popularBrands = Brand::where('is_active', true)
            ->where('is_popular', true)
            ->orderBy('sort_order')
            ->get();

        $games = $popularBrands->pluck('name')->toArray();

        $today = [];
        $week = [];
        $month = [];

        $shuffled = $names;
        shuffle($shuffled);
        for ($i = 0; $i < 10; $i++) {
            $today[] = ['rank' => $i + 1, 'name' => $shuffled[$i], 'amount' => rand(100000, 3000000)];
        }
        usort($today, fn($a, $b) => $b['amount'] - $a['amount']);
        foreach ($today as $i => &$t) { $t['rank'] = $i + 1; } unset($t);

        $shuffled = $names;
        shuffle($shuffled);
        for ($i = 0; $i < 10; $i++) {
            $week[] = ['rank' => $i + 1, 'name' => $shuffled[$i], 'amount' => rand(200000, 5000000)];
        }
        usort($week, fn($a, $b) => $b['amount'] - $a['amount']);
        foreach ($week as $i => &$w) { $w['rank'] = $i + 1; } unset($w);

        $shuffled = $names;
        shuffle($shuffled);
        for ($i = 0; $i < 10; $i++) {
            $month[] = ['rank' => $i + 1, 'name' => $shuffled[$i], 'amount' => rand(500000, 10000000)];
        }
        usort($month, fn($a, $b) => $b['amount'] - $a['amount']);
        foreach ($month as $i => &$m) { $m['rank'] = $i + 1; } unset($m);

        $leaderboard = compact('today', 'week', 'month');

        if ($request->wantsJson()) {
            $serviceType = $request->input('service_type', 'topup');
            $game = $request->input('game', 'all');
            return response()->json($leaderboard);
        }

        return view('leaderboard', compact('leaderboard', 'games', 'popularBrands'));
    }

    public function leaderboardDetail($period)
    {
        $popularBrands = Brand::where('is_active', true)
            ->where('is_popular', true)
            ->orderBy('sort_order')
            ->get();

        $periods = ['daily', 'weekly', 'monthly'];
        if (!in_array($period, $periods)) {
            $period = 'daily';
        }

        $title = match ($period) {
            'daily' => 'Leaderboard Hari Ini',
            'weekly' => 'Leaderboard Minggu Ini',
            'monthly' => 'Leaderboard Bulan Ini',
        };

        return view('leaderboard-detail', compact('period', 'title', 'popularBrands'));
    }

    public function leaderboardApi(Request $request)
    {
        $names = [
            'Muhammad Ilyas', 'Muhammad Fadlan', 'Muhammad Taopik', 'Ahmad Rizaldi',
            'Siti Nurhaliza', 'Dimas Pratama', 'Rina Marlina', 'Fajar Ramadhan',
            'Dewi Lestari', 'Bambang Suprapto', 'Rizky_Ace', 'Dinda.ML',
            'ProGamerID', 'FaizFF07', 'ValoQueen', 'Iky_Sniper',
            'Nadia_Gaming', 'Rafi_Supremasi', 'Citra_Queen', 'Aldo_Boss',
        ];

        $games = ['Mobile Legends', 'PUBG Mobile', 'Valorant', 'Free Fire', 'Honor of Kings'];
        $times = ['08:15 WIB', '10:30 WIB', '13:45 WIB', '15:20 WIB', '18:00 WIB', '20:10 WIB', '21:35 WIB', '22:50 WIB', '07:05 WIB', '11:25 WIB'];

        $allData = [];
        for ($i = 0; $i < 50; $i++) {
            $gameFilter = $request->input('game', 'all');
            $game = $games[array_rand($games)];
            if ($gameFilter !== 'all' && $game !== $gameFilter) {
                continue;
            }
            $allData[] = [
                'rank' => 0,
                'customer' => $names[array_rand($names)],
                'game' => $game,
                'service' => $request->input('service', 'topup') === 'topup' ? 'Top Up' : 'Joki',
                'total_purchase' => rand(100000, 10000000),
                'last_transaction' => $times[array_rand($times)],
            ];
        }

        if (count($allData) < 50) {
            $allData = [];
            for ($i = 0; $i < 50; $i++) {
                $game = $games[array_rand($games)];
                $allData[] = [
                    'rank' => 0,
                    'customer' => $names[array_rand($names)],
                    'game' => $game,
                    'service' => $request->input('service', 'topup') === 'topup' ? 'Top Up' : 'Joki',
                    'total_purchase' => rand(100000, 10000000),
                    'last_transaction' => $times[array_rand($times)],
                ];
            }
        }

        usort($allData, fn($a, $b) => $b['total_purchase'] - $a['total_purchase']);
        foreach ($allData as $i => &$d) { $d['rank'] = $i + 1; } unset($d);

        $perPage = (int) $request->input('per_page', 10);
        $page = (int) $request->input('page', 1);
        $total = count($allData);
        $offset = ($page - 1) * $perPage;
        $items = array_slice($allData, $offset, $perPage);

        return response()->json([
            'current_page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'last_page' => (int) ceil($total / $perPage),
            'data' => $items,
        ]);
    }
}
