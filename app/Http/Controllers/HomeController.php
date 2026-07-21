<?php

namespace App\Http\Controllers;

use App\Models\AccountListing;
use App\Models\AccountOrder;
use App\Models\Brand;
use App\Models\ContactInquiry;
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
            ->get(['name as brand', 'thumbnail', 'icon']);

        return response()->json($brands);
    }

    public function getPaymentMethods()
    {
        $methods = PaymentMethod::where('is_active', true)->get(['name', 'code', 'icon', 'photo', 'photo_light']);
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

    public function jualBeliAkun()
    {
        $listings = AccountListing::where(function($q) {
                $q->where('is_active', true)->orWhere('is_sold', true);
            })
            ->orderBy('is_sold', 'asc')
            ->orderBy('game')
            ->orderBy('product_name')
            ->get()
            ->groupBy('game');

        $popularGames = Brand::where('is_popular', true)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('pages.jual-beli-akun', compact('popularGames', 'listings'));
    }

    public function jualBeliAkunDetail(AccountListing $listing)
    {
        if (!$listing->is_active && !$listing->is_sold) {
            abort(404);
        }

        $related = AccountListing::where(function($q) {
                $q->where('is_active', true)->orWhere('is_sold', true);
            })
            ->where('game', $listing->game)
            ->where('id', '!=', $listing->id)
            ->orderBy('is_sold', 'asc')
            ->orderBy('product_name')
            ->get();

        return view('pages.jual-beli-akun-detail', compact('listing', 'related'));
    }

    public function jualBeliAkunCheckout(AccountListing $listing)
    {
        if ($listing->is_sold || !$listing->is_active) {
            return redirect()->route('jual-beli-akun.detail', $listing)
                ->with('error', 'Produk ini sudah tidak tersedia.');
        }

        $paymentMethods = PaymentMethod::where('is_active', true)->get();

        return view('pages.jual-beli-akun-checkout', compact('listing', 'paymentMethods'));
    }

    public function jualBeliAkunCheckoutStore(Request $request, AccountListing $listing)
    {
        if ($listing->is_sold || !$listing->is_active) {
            return redirect()->route('jual-beli-akun.detail', $listing)
                ->with('error', 'Produk ini sudah tidak tersedia.');
        }

        $validated = $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'payment_method' => 'required|string|max:100',
            'notes' => 'nullable|string|max:1000',
        ]);

        $order = AccountOrder::create([
            'account_listing_id' => $listing->id,
            'user_id' => auth()->id(),
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'],
            'customer_phone' => $validated['customer_phone'],
            'payment_method' => $validated['payment_method'],
            'status' => 'pending',
            'total_price' => $listing->price,
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('jual-beli-akun.payment', $order)
            ->with('success', 'Pesanan berhasil dibuat! Silakan lakukan pembayaran.');
    }

    public function jualBeliAkunPayment(AccountOrder $accountOrder)
    {
        $listing = $accountOrder->listing;

        if (!$listing) {
            abort(404);
        }

        return view('pages.jual-beli-akun-payment', compact('accountOrder', 'listing'));
    }

    public static function getTestimonials(): array
    {
        $now = now()->setTimezone('Asia/Jakarta');
        $fmt = fn($d) => $d->format('d-m-Y H:i:s');
        return [
            ['name' => 'User Free Fire', 'game' => 'Top Up - Free Fire', 'avatar' => '🙂', 'quote' => 'Top up Diamond Free Fire di sini cepat banget. Setelah pembayaran berhasil, diamond langsung masuk ke akun tanpa perlu menunggu lama.', 'date' => $fmt((clone $now)->subMinutes(3))],
            ['name' => 'User Mobile Legends', 'game' => 'Top Up - Mobile Legends', 'avatar' => '😄', 'quote' => 'Top up Diamond MLBB cuma beberapa menit langsung masuk. Harganya juga lebih murah dibanding tempat lain. Sudah langganan dari lama dan selalu aman.', 'date' => $fmt((clone $now)->subMinutes(17))],
            ['name' => 'User PUBG Mobile', 'game' => 'Top Up - PUBG Mobile', 'avatar' => '🎮', 'quote' => 'Top up UC PUBG Mobile menit langsung masuk ke akun. Harganya bersaing, prosesnya cepat, dan sejauh ini tanpa kendala. Sudah beberapa kali top up di sini dan hasilnya selalu memuaskan.', 'date' => $fmt((clone $now)->subHours(2))],
            ['name' => 'User Valorant', 'game' => 'Top Up - Valorant', 'avatar' => '🎯', 'quote' => 'Poin Valorant masuk instan setelah bayar QRIS. Prosesnya jelas dan ada notifikasi tiap tahap. Recommended buat yang males ribet.', 'date' => $fmt((clone $now)->subHours(5))],
            ['name' => 'User Genshin Impact', 'game' => 'Top Up - Genshin Impact', 'avatar' => '💎', 'quote' => 'Genesis Crystal masuk kurang dari 5 menit. CS-nya responsif kalau ada kendala. Harga juga bersahabat buat dompet pelajar seperti saya.', 'date' => $fmt((clone $now)->subHours(8))],
            ['name' => 'User Honor of Kings', 'game' => 'Top Up - Honor of Kings', 'avatar' => '⚔️', 'quote' => 'Top up Token HOK super cepat, kurang dari 2 menit langsung masuk. Harganya juga kompetitif, jadi saya sering top up di sini tiap season baru.', 'date' => $fmt((clone $now)->subDay())],
            ['name' => 'User FIFA Mobile', 'game' => 'Top Up - EA Sports FC', 'avatar' => '⚽', 'quote' => 'FIFA Points masuk secepat kilat. Pertama kali coba agak ragu, tapi setelah bukti sendiri sekarang jadi langganan. Pokoknya recommended!', 'date' => $fmt((clone $now)->subDays(2))],
            ['name' => 'User Steam Wallet', 'game' => 'Top Up - Steam Wallet', 'avatar' => '🕹️', 'quote' => 'Saldo Steam masuk dalam hitungan menit. Harganya bersahabat, prosesnya juga transparan dengan bukti pengisian yang dikirim.', 'date' => $fmt((clone $now)->subDays(4))],
            ['name' => 'User Call of Duty', 'game' => 'Top Up - Call of Duty Mobile', 'avatar' => '🔫', 'quote' => 'CP CODM langsung nambah setelah bayar. Proses cepat tanpa ribet, selalu jadi andalan buat top up mingguan.', 'date' => $fmt((clone $now)->subWeek())],
            ['name' => 'User Mobile Legends', 'game' => 'Joki Rank Mobile Legends', 'avatar' => '🏆', 'quote' => 'Jasa joki rank MLBB profesional banget. Dari Legend ke Mythic dalam 3 hari, aman, fast respon, dan harganya worth it!', 'date' => $fmt((clone $now)->subWeeks(2))],
        ];
    }

    public function testimoni()
    {
        $testimonials = static::getTestimonials();
        return view('pages.testimoni', compact('testimonials'));
    }

    public function kontak()
    {
        return view('pages.kontak');
    }

    public function faq()
    {
        return view('pages.faq');
    }

    public function privacy()
    {
        return view('pages.privacy');
    }

    public function terms()
    {
        return view('pages.terms');
    }

    public function kontakStore(Request $request)
    {
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'phone'   => 'required|string|max:20',
            'category'=> 'required|string|in:topup,jual-beli-akun,pembayaran,keluhan,saran,lainnya',
            'message' => 'required|string|max:5000',
        ]);

        ContactInquiry::create($validated);

        return redirect()->route('kontak')
            ->with('success', 'Pesan berhasil dikirim! Tim CS kami akan menghubungi anda segera.');
    }
}
