<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\SiteSetting;
use App\Models\User;
use App\Services\DigiflazzService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    protected DigiflazzService $digiflazz;

    public function __construct(DigiflazzService $digiflazz)
    {
        $this->digiflazz = $digiflazz;
    }

    public function dashboard()
    {
        $stats = [
            'total_products' => Product::count(),
            'active_products' => Product::where('is_active', true)->count(),
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'success_orders' => Order::where('status', 'success')->count(),
            'total_users' => User::count(),
            'total_revenue' => Order::where('status', 'success')->sum('price'),
        ];

        $recentOrders = Order::with('user')->latest()->take(10)->get();

        return view('admin.dashboard', compact('stats', 'recentOrders'));
    }

    // ---- PRODUCTS ----
    public function products()
    {
        $products = Product::orderBy('brand')->orderBy('product_name')->paginate(20);
        return view('admin.products.index', compact('products'));
    }

    public function productsCreate()
    {
        return view('admin.products.create');
    }

    public function productsStore(Request $request)
    {
        $request->validate([
            'buyer_sku_code' => 'required|string|unique:products',
            'brand' => 'required|string',
            'category' => 'required|string',
            'product_name' => 'required|string',
            'price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'type' => 'required|string',
            'stock' => 'nullable|integer|min:0',
            'region' => 'nullable|string|in:ID,MY,PH',
        ]);

        Product::create([
            'buyer_sku_code' => $request->buyer_sku_code,
            'brand' => $request->brand,
            'category' => $request->category,
            'product_name' => $request->product_name,
            'price' => $request->price,
            'selling_price' => $request->selling_price ?: $request->price,
            'type' => $request->type,
            'stock' => $request->stock ?? 0,
            'region' => $request->region,
            'is_active' => true,
        ]);

        return redirect()->route('admin.products')->with('success', 'Produk berhasil ditambahkan');
    }

    public function productsEdit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    public function productsUpdate(Request $request, Product $product)
    {
        $request->validate([
            'buyer_sku_code' => 'required|string|unique:products,buyer_sku_code,' . $product->id,
            'brand' => 'required|string',
            'category' => 'required|string',
            'product_name' => 'required|string',
            'price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'type' => 'required|string',
            'stock' => 'nullable|integer|min:0',
            'region' => 'nullable|string|in:ID,MY,PH',
            'is_active' => 'boolean',
        ]);

        $product->update([
            'buyer_sku_code' => $request->buyer_sku_code,
            'brand' => $request->brand,
            'category' => $request->category,
            'product_name' => $request->product_name,
            'price' => $request->price,
            'selling_price' => $request->selling_price,
            'type' => $request->type,
            'stock' => $request->stock ?? 0,
            'region' => $request->region,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.products')->with('success', 'Produk berhasil diperbarui');
    }

    public function productsToggle(Product $product)
    {
        $product->update(['is_active' => !$product->is_active]);
        return back()->with('success', 'Status produk berhasil diubah');
    }

    public function productsDestroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products')->with('success', 'Produk berhasil dihapus');
    }

    public function productsSync()
    {
        $data = $this->digiflazz->getPriceList();

        if (empty($data)) {
            return back()->with('error', 'Gagal mengambil data dari Digiflazz');
        }

        $count = 0;
        foreach ($data as $item) {
            Product::updateOrCreate(
                ['buyer_sku_code' => $item['buyer_sku_code']],
                [
                    'brand' => $item['brand'],
                    'category' => $item['category'],
                    'product_name' => $item['product_name'],
                    'price' => $item['price'],
                    'selling_price' => $item['price'] + ($item['price'] * 0.05),
                    'type' => $item['type'],
                    'is_active' => $item['buyer_product_status'] === true,
                ]
            );
            $count++;
        }

        return redirect()->route('admin.products')->with('success', "$count produk berhasil disinkronisasi dari Digiflazz");
    }

    // ---- BRANDS ----
    public function brands()
    {
        $brands = Brand::orderBy('sort_order')->orderBy('name')->paginate(20);
        return view('admin.brands.index', compact('brands'));
    }

    public function brandsCreate()
    {
        return view('admin.brands.create');
    }

    public function brandsStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:brands',
            'category' => 'required|string|max:50',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'carousel_bg' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'is_popular' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data = [
            'name' => $request->name,
            'category' => $request->category,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
            'is_popular' => $request->boolean('is_popular', false),
            'sort_order' => $request->integer('sort_order', 0),
        ];

        if ($request->hasFile('thumbnail') && $request->file('thumbnail')->isValid()) {
            $data['thumbnail'] = $request->file('thumbnail')->store('brands', 'public');
        }

        if ($request->hasFile('carousel_bg') && $request->file('carousel_bg')->isValid()) {
            $data['carousel_bg'] = $request->file('carousel_bg')->store('brands/bg', 'public');
        }

        Brand::create($data);

        return redirect()->route('admin.brands')->with('success', 'Game berhasil ditambahkan');
    }

    public function brandsEdit(Brand $brand)
    {
        return view('admin.brands.edit', compact('brand'));
    }

    public function brandsUpdate(Request $request, Brand $brand)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:brands,name,' . $brand->id,
            'category' => 'required|string|max:50',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'carousel_bg' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'is_popular' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $data = [
            'name' => $request->name,
            'category' => $request->category,
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
            'is_popular' => $request->boolean('is_popular', false),
            'sort_order' => $request->integer('sort_order', 0),
        ];

        if ($request->hasFile('thumbnail') && $request->file('thumbnail')->isValid()) {
            if ($brand->thumbnail) {
                Storage::disk('public')->delete($brand->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail')->store('brands', 'public');
        }

        if ($request->hasFile('carousel_bg') && $request->file('carousel_bg')->isValid()) {
            if ($brand->carousel_bg) {
                Storage::disk('public')->delete($brand->carousel_bg);
            }
            $data['carousel_bg'] = $request->file('carousel_bg')->store('brands/bg', 'public');
        }

        $brand->update($data);

        return redirect()->route('admin.brands')->with('success', 'Game berhasil diperbarui');
    }

    public function brandsToggle(Brand $brand)
    {
        $brand->update(['is_active' => !$brand->is_active]);
        return back()->with('success', 'Status game berhasil diubah');
    }

    public function brandsDestroy(Brand $brand)
    {
        if ($brand->thumbnail) {
            Storage::disk('public')->delete($brand->thumbnail);
        }
        $brand->delete();
        return redirect()->route('admin.brands')->with('success', 'Game berhasil dihapus');
    }

    // ---- ORDERS ----
    public function orders()
    {
        $orders = Order::with('user')->latest()->paginate(20);
        return view('admin.orders.index', compact('orders'));
    }

    public function ordersShow(Order $order)
    {
        $order->load('user', 'transaction');
        return view('admin.orders.show', compact('order'));
    }

    public function ordersUpdateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,success,failed,cancelled',
        ]);

        $order->update(['status' => $request->status]);

        return back()->with('success', 'Status pesanan berhasil diperbarui menjadi ' . $request->status);
    }

    // ---- USERS ----
    public function users()
    {
        $users = User::latest()->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function usersEdit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function usersUpdate(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'is_admin' => 'boolean',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'is_admin' => $request->boolean('is_admin', false),
        ]);

        return redirect()->route('admin.users')->with('success', 'Pengguna berhasil diperbarui');
    }

    // ---- PAYMENT METHODS ----
    public function paymentMethods()
    {
        $paymentMethods = PaymentMethod::orderBy('name')->paginate(20);
        return view('admin.payment-methods.index', compact('paymentMethods'));
    }

    public function paymentMethodsCreate()
    {
        return view('admin.payment-methods.create');
    }

    public function paymentMethodsStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:payment_methods',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active' => 'boolean',
        ]);

        $data = [
            'name' => $request->name,
            'code' => $request->code,
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            $data['photo'] = $request->file('photo')->store('payments', 'public');
        }

        PaymentMethod::create($data);

        return redirect()->route('admin.payment-methods')->with('success', 'Metode pembayaran berhasil ditambahkan');
    }

    public function paymentMethodsEdit(PaymentMethod $paymentMethod)
    {
        return view('admin.payment-methods.edit', compact('paymentMethod'));
    }

    public function paymentMethodsUpdate(Request $request, PaymentMethod $paymentMethod)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:payment_methods,code,' . $paymentMethod->id,
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active' => 'boolean',
        ]);

        $data = [
            'name' => $request->name,
            'code' => $request->code,
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            if ($paymentMethod->photo) {
                Storage::disk('public')->delete($paymentMethod->photo);
            }
            $data['photo'] = $request->file('photo')->store('payments', 'public');
        }

        $paymentMethod->update($data);

        return redirect()->route('admin.payment-methods')->with('success', 'Metode pembayaran berhasil diperbarui');
    }

    public function paymentMethodsToggle(PaymentMethod $paymentMethod)
    {
        $paymentMethod->update(['is_active' => !$paymentMethod->is_active]);
        return back()->with('success', 'Status metode pembayaran berhasil diubah');
    }

    public function paymentMethodsDestroy(PaymentMethod $paymentMethod)
    {
        $paymentMethod->delete();
        return redirect()->route('admin.payment-methods')->with('success', 'Metode pembayaran berhasil dihapus');
    }

    public function settings()
    {
        $settings = \App\Models\SiteSetting::allKeyValue();
        return view('admin.settings', compact('settings'));
    }

    public function settingsUpdate(Request $request)
    {
        $request->validate([
            'site_name' => 'required|string|max:255',
            'site_tagline' => 'nullable|string|max:255',
            'site_description' => 'nullable|string',
            'contact_email' => 'nullable|email|max:255',
            'contact_whatsapp' => 'nullable|string|max:50',
            'contact_instagram' => 'nullable|string|max:255',
            'footer_text' => 'nullable|string|max:500',
        ]);

        $textKeys = ['site_name', 'site_tagline', 'site_description', 'contact_email', 'contact_whatsapp', 'contact_instagram', 'footer_text', 'min_balance_alert'];

        foreach ($textKeys as $key) {
            if ($request->has($key)) {
                \App\Models\SiteSetting::set($key, $request->input($key, ''));
            }
        }

        if ($request->hasFile('site_logo') && $request->file('site_logo')->isValid()) {
            $oldLogo = \App\Models\SiteSetting::get('site_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }
            $path = $request->file('site_logo')->store('settings', 'public');
            \App\Models\SiteSetting::set('site_logo', $path, 'image');
        }

        return redirect()->route('admin.settings')->with('success', 'Pengaturan berhasil disimpan');
    }
}
