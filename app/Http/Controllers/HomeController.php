<?php

namespace App\Http\Controllers;

use App\Models\AccountListing;
use App\Models\AccountOrder;
use App\Models\Brand;
use App\Models\ContactInquiry;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Services\GameAccountService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

    /**
     * Deteksi akun game real-time (User ID + Zone ID).
     * Dipakai halaman game detail untuk indikator hijau saat akun ditemukan.
     */
    public function checkAccount(Request $request, GameAccountService $gameAccount)
    {
        $validated = $request->validate([
            'brand' => 'required|string|max:100',
            'user_id' => 'required|string|max:32',
            'zone_id' => 'nullable|string|max:20|regex:/^[A-Za-z0-9]+$/',
        ]);

        return response()->json(
            $gameAccount->check($validated['brand'], $validated['user_id'], $validated['zone_id'] ?? null)
        );
    }

    public function checkOrder(Request $request)
    {
        $q = trim((string) $request->input('q', ''));
        $q = ltrim($q, '#');

        if ($q === '') {
            return response()->json(['message' => 'Masukkan ID transaksi atau email'], 422);
        }

        $orders = Order::where(function ($query) use ($q) {
                $query->where('order_id', $q)
                    ->orWhere('customer_number', $q)
                    ->orWhere('email', $q)
                    ->orWhereHas('user', fn ($uq) => $uq->where('email', $q))
                    ->orWhereHas('transaction', fn ($tq) => $tq->where('transaction_id', $q));
            })
            ->orderByDesc('created_at')
            ->get();

        if ($orders->isEmpty()) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        return response()->json([
            'transactions' => $orders->map(fn ($o) => [
                'order_id' => $o->order_id,
                'product_name' => $o->product_name,
                'customer_number' => $o->customer_number,
                'price' => (float) $o->price,
                'status' => $o->status,
                'processed_at' => $o->updated_at?->format('d M Y H:i'),
            ])->values(),
        ]);
    }

    public function checkTransaction()
    {
        return view('check-transaction');
    }

    public function leaderboard(Request $request)
    {
        $popularBrands = Brand::where('is_active', true)
            ->where('is_popular', true)
            ->orderBy('sort_order')
            ->get();

        $games = $popularBrands->pluck('name')->toArray();

        $gameFilter = $request->input('game', 'all');
        $minNominal = $request->input('min_nominal');
        $maxNominal = $request->input('max_nominal');
        $period = $request->input('period');
        $sort = $request->input('sort', 'largest');

        $baseQuery = Order::select(
                'orders.user_id',
                'orders.email',
                DB::raw('MAX(COALESCE(users.name, orders.customer_name, orders.email, "Guest")) as name'),
                DB::raw('SUM(orders.price) as total_amount'),
                DB::raw('COUNT(orders.id) as total_count')
            )
            ->leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->where('orders.status', 'success')
            ->where(function ($q2) {
                $q2->whereNotNull('orders.user_id')
                   ->orWhereNotNull('orders.email')
                   ->orWhereNotNull('orders.customer_name');
            });

        if ($gameFilter !== 'all') {
            $baseQuery->where('orders.brand', $gameFilter);
        }

        if (is_numeric($minNominal) && $minNominal > 0) {
            $baseQuery->where('orders.price', '>=', (float) $minNominal);
        }
        if (is_numeric($maxNominal) && $maxNominal > 0) {
            $baseQuery->where('orders.price', '<=', (float) $maxNominal);
        }

        $orderBy = $sort === 'most' ? 'total_count' : 'total_amount';

        $run = function ($query) use ($orderBy) {
            return $query->groupBy('orders.user_id', 'orders.email')
                ->orderByDesc($orderBy)
                ->limit(10)
                ->get()
                ->map(fn($item, $i) => ['rank' => $i + 1, 'name' => $item->name, 'amount' => (int) $item->total_amount])
                ->toArray();
        };

        $periodMap = [
            'daily' => ['key' => 'today', 'query' => (clone $baseQuery)->whereDate('orders.created_at', Carbon::today())],
            'weekly' => ['key' => 'week', 'query' => (clone $baseQuery)->whereBetween('orders.created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])],
            'monthly' => ['key' => 'month', 'query' => (clone $baseQuery)->whereYear('orders.created_at', Carbon::now()->year)->whereMonth('orders.created_at', Carbon::now()->month)],
        ];

        if ($request->wantsJson() && array_key_exists($period, $periodMap)) {
            return response()->json([
                'key' => $periodMap[$period]['key'],
                'data' => $run($periodMap[$period]['query']),
            ]);
        }

        $today = $run($periodMap['daily']['query']);
        $week = $run($periodMap['weekly']['query']);
        $month = $run($periodMap['monthly']['query']);

        $leaderboard = compact('today', 'week', 'month');

        if ($request->wantsJson()) {
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
        $period = $request->input('period', 'daily');
        $gameFilter = $request->input('game', 'all');
        $minNominal = $request->input('min_nominal');
        $maxNominal = $request->input('max_nominal');

        $query = Order::select(
                'orders.user_id',
                'orders.email',
                'users.name as account_name',
                DB::raw('MAX(COALESCE(users.name, orders.customer_name, orders.email, "Guest")) as customer'),
                'orders.brand as game',
                DB::raw('SUM(orders.price) as total_purchase'),
                DB::raw('COUNT(orders.id) as total_transactions'),
                DB::raw('MAX(orders.created_at) as last_transaction')
            )
            ->leftJoin('users', 'orders.user_id', '=', 'users.id')
            ->where('orders.status', 'success')
            ->where(function ($q2) {
                $q2->whereNotNull('orders.user_id')
                   ->orWhereNotNull('orders.email')
                   ->orWhereNotNull('orders.customer_name');
            });

        if ($gameFilter !== 'all') {
            $query->where('orders.brand', $gameFilter);
        }

        if (is_numeric($minNominal) && $minNominal > 0) {
            $query->where('orders.price', '>=', (float) $minNominal);
        }
        if (is_numeric($maxNominal) && $maxNominal > 0) {
            $query->where('orders.price', '<=', (float) $maxNominal);
        }

        if ($period === 'daily') {
            $query->whereDate('orders.created_at', Carbon::today());
        } elseif ($period === 'weekly') {
            $query->whereBetween('orders.created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($period === 'monthly') {
            $query->whereYear('orders.created_at', Carbon::now()->year)
                  ->whereMonth('orders.created_at', Carbon::now()->month);
        }

        $allData = $query->groupBy('orders.user_id', 'orders.email', 'users.name', 'orders.brand')
            ->orderByDesc('total_purchase')
            ->get()
            ->map(fn($item, $i) => [
                'rank' => $i + 1,
                'customer' => $item->customer,
                'game' => $item->game,
                'total_purchase' => (int) $item->total_purchase,
                'total_transactions' => (int) $item->total_transactions,
                'last_transaction' => Carbon::parse($item->last_transaction)->format('d M Y H:i') . ' WIB',
            ])
            ->toArray();

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

        $testimonials = array_values(array_filter(static::getTestimonials(), fn($t) => ($t['layanan'] ?? '') === 'jual-beli-akun'));

        return view('pages.jual-beli-akun', compact('popularGames', 'listings', 'testimonials'));
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
            ['name' => 'User Free Fire', 'game' => 'Top Up - Free Fire', 'avatar' => '🙂', 'layanan' => 'topup', 'quote' => 'Top up Diamond Free Fire di sini cepat banget. Setelah pembayaran berhasil, diamond langsung masuk ke akun tanpa perlu menunggu lama.', 'date' => $fmt((clone $now)->subMinutes(3))],
            ['name' => 'User Mobile Legends', 'game' => 'Top Up - Mobile Legends', 'avatar' => '😄', 'layanan' => 'topup', 'quote' => 'Top up Diamond MLBB cuma beberapa menit langsung masuk. Harganya juga lebih murah dibanding tempat lain. Sudah langganan dari lama dan selalu aman.', 'date' => $fmt((clone $now)->subMinutes(17))],
            ['name' => 'User PUBG Mobile', 'game' => 'Top Up - PUBG Mobile', 'avatar' => '🎮', 'layanan' => 'topup', 'quote' => 'Top up UC PUBG Mobile menit langsung masuk ke akun. Harganya bersaing, prosesnya cepat, dan sejauh ini tanpa kendala. Sudah beberapa kali top up di sini dan hasilnya selalu memuaskan.', 'date' => $fmt((clone $now)->subHours(2))],
            ['name' => 'User Valorant', 'game' => 'Top Up - Valorant', 'avatar' => '🎯', 'layanan' => 'topup', 'quote' => 'Poin Valorant masuk instan setelah bayar QRIS. Prosesnya jelas dan ada notifikasi tiap tahap. Recommended buat yang males ribet.', 'date' => $fmt((clone $now)->subHours(5))],
            ['name' => 'User Genshin Impact', 'game' => 'Top Up - Genshin Impact', 'avatar' => '💎', 'layanan' => 'topup', 'quote' => 'Genesis Crystal masuk kurang dari 5 menit. CS-nya responsif kalau ada kendala. Harga juga bersahabat buat dompet pelajar seperti saya.', 'date' => $fmt((clone $now)->subHours(8))],
            ['name' => 'User Honor of Kings', 'game' => 'Top Up - Honor of Kings', 'avatar' => '⚔️', 'layanan' => 'topup', 'quote' => 'Top up Token HOK super cepat, kurang dari 2 menit langsung masuk. Harganya juga kompetitif, jadi saya sering top up di sini tiap season baru.', 'date' => $fmt((clone $now)->subDay())],
            ['name' => 'User FIFA Mobile', 'game' => 'Top Up - EA Sports FC', 'avatar' => '⚽', 'layanan' => 'topup', 'quote' => 'FIFA Points masuk secepat kilat. Pertama kali coba agak ragu, tapi setelah bukti sendiri sekarang jadi langganan. Pokoknya recommended!', 'date' => $fmt((clone $now)->subDays(2))],
            ['name' => 'User Steam Wallet', 'game' => 'Top Up - Steam Wallet', 'avatar' => '🕹️', 'layanan' => 'topup', 'quote' => 'Saldo Steam masuk dalam hitungan menit. Harganya bersahabat, prosesnya juga transparan dengan bukti pengisian yang dikirim.', 'date' => $fmt((clone $now)->subDays(4))],
            ['name' => 'User Call of Duty', 'game' => 'Top Up - Call of Duty Mobile', 'avatar' => '🔫', 'layanan' => 'topup', 'quote' => 'CP CODM langsung nambah setelah bayar. Proses cepat tanpa ribet, selalu jadi andalan buat top up mingguan.', 'date' => $fmt((clone $now)->subWeek())],
            ['name' => 'User Mobile Legends', 'game' => 'Joki Rank Mobile Legends', 'avatar' => '🏆', 'layanan' => 'joki', 'quote' => 'Jasa joki rank MLBB profesional banget. Dari Legend ke Mythic dalam 3 hari, aman, fast respon, dan harganya worth it!', 'date' => $fmt((clone $now)->subWeeks(2))],
            ['name' => 'User Free Fire', 'game' => 'Jual Akun - Free Fire', 'avatar' => '🙂', 'layanan' => 'jual-beli-akun', 'quote' => 'Beli akun Free Fire di sini aman dan terpercaya. Akun sesuai deskripsi, harga reasonable, dan proses transaksinya jelas. Recommended buat yang cari akun second.', 'date' => $fmt((clone $now)->subDays(3))],
            ['name' => 'User Mobile Legends', 'game' => 'Jual Akun - Mobile Legends', 'avatar' => '😄', 'layanan' => 'jual-beli-akun', 'quote' => 'Jual akun MLBB saya laku dalam 2 hari. Admin fast respon dan membantu proses negosiasi dengan pembeli. Sangat membantu!', 'date' => $fmt((clone $now)->subDays(6))],
            ['name' => 'User PUBG Mobile', 'game' => 'Jual Akun - PUBG Mobile', 'avatar' => '🎮', 'layanan' => 'jual-beli-akun', 'quote' => 'Pengalaman jual akun PUBG pertama kali dan ternyata gampang. Admin menjelaskan prosedur dengan jelas. Pembayaran cepat cair.', 'date' => $fmt((clone $now)->subDays(10))],
        ];
    }

    public function testimoni()
    {
        $all = static::getTestimonials();

        $reviews = \App\Models\Review::where('status', 'approved')
            ->whereNotNull('comment')
            ->latest()
            ->limit(12)
            ->get()
            ->map(function (\App\Models\Review $r) {
                $name = $r->user?->name
                    ?: ($r->email ? str($r->email)->before('@')->toString() : 'User ' . ($r->game ?: 'Johen'));
                return [
                    'name' => $name,
                    'game' => ($r->game ? 'Top Up - ' . $r->game : 'Top Up'),
                    'avatar' => match ((int) $r->rating) {
                        5 => '🌟🌟🌟🌟🌟',
                        4 => '🌟🌟🌟🌟',
                        3 => '🌟🌟🌟',
                        2 => '🌟🌟',
                        default => '🌟',
                    },
                    'layanan' => 'topup',
                    'quote' => $r->comment,
                    'date' => $r->created_at ? $r->created_at->format('d-m-Y H:i:s') : now()->format('d-m-Y H:i:s'),
                ];
            })
            ->toArray();

        $all = array_merge($reviews, $all);

        $layanan = request('layanan');
        $testimonials = $layanan ? array_filter($all, fn($t) => ($t['layanan'] ?? '') === $layanan) : $all;
        $activeLayanan = $layanan;
        return view('pages.testimoni', compact('testimonials', 'activeLayanan'));
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

        $data = $validated;
        $data['user_id'] = auth()->check() ? auth()->id() : null;

        ContactInquiry::create($data);

        return redirect()->route('kontak')
            ->with('success', 'Pesan berhasil dikirim! Tim CS kami akan menghubungi anda segera.');
    }

    public function myInquiries()
    {
        $inquiries = ContactInquiry::where('user_id', auth()->id())
            ->latest()
            ->paginate(15);

        return view('pages.my-inquiries', compact('inquiries'));
    }
}
