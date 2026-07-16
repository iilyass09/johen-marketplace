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
            'digiflazz_configured' => $this->digiflazz->isConfigured(),
            'digiflazz_last_sync' => \App\Models\SiteSetting::get('digiflazz_last_sync'),
            'digiflazz_product_count' => \App\Models\SiteSetting::get('digiflazz_product_count', '0'),
        ];

        $recentOrders = Order::with('user')->latest()->take(10)->get();

        return view('admin.dashboard', compact('stats', 'recentOrders'));
    }

    // ---- PRODUCTS ----
    public function products(Request $request)
    {
        $query = Product::orderBy('brand')->orderBy('product_name');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $products = $query->paginate(20);

        return view('admin.products.index', compact('products'));
    }

    public function productsCreate()
    {
        return view('admin.products.create');
    }

    public function productsStore(Request $request)
    {
        $validator = validator($request->all(), [
            'buyer_sku_code' => 'required|string|unique:products',
            'brand' => 'required|string',
            'category' => 'required|string',
            'product_name' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'type' => 'required|string',
            'stock' => 'nullable|integer|min:0',
            'region' => 'nullable|string|in:ID,MY,PH',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = [
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
        ];

        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            $data['photo'] = $request->file('photo')->store('products', 'public');
        }

        Product::create($data);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Produk berhasil ditambahkan']);
        }

        return redirect()->route('admin.products')->with('success', 'Produk berhasil ditambahkan');
    }

    public function productsEdit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    public function productsUpdate(Request $request, Product $product)
    {
        $validator = validator($request->all(), [
            'buyer_sku_code' => 'required|string|unique:products,buyer_sku_code,' . $product->id,
            'brand' => 'required|string',
            'category' => 'required|string',
            'product_name' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'type' => 'required|string',
            'stock' => 'nullable|integer|min:0',
            'region' => 'nullable|string|in:ID,MY,PH',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = [
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
        ];

        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            if ($product->photo) {
                Storage::disk('public')->delete($product->photo);
            }
            $data['photo'] = $request->file('photo')->store('products', 'public');
        }

        $product->update($data);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Produk berhasil diperbarui']);
        }

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
        $result = $this->digiflazz->syncProducts();

        if ($result['success']) {
            return redirect()->route('admin.products')->with('success', $result['message']);
        }

        return redirect()->route('admin.products')->with('error', $result['message']);
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
        $validator = validator($request->all(), [
            'name' => 'required|string|max:255|unique:brands',
            'category' => 'required|string|max:50',
            'service_type' => 'required|string|in:topup,joki,both',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'featured_thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'featured_img_1' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'featured_img_2' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'featured_img_3' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'carousel_bg' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'detail_bg' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'detail_bg_position' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'is_popular' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = [
            'name' => $request->name,
            'category' => $request->category,
            'service_type' => $request->input('service_type', 'topup'),
            'description' => $request->description,
            'is_active' => $request->boolean('is_active', true),
            'is_popular' => $request->boolean('is_popular', false),
            'sort_order' => $request->integer('sort_order', 0),
            'detail_bg_position' => $request->input('detail_bg_position', 'center'),
        ];

        if ($request->hasFile('thumbnail') && $request->file('thumbnail')->isValid()) {
            $data['thumbnail'] = $request->file('thumbnail')->store('brands', 'public');
        }

        if ($request->hasFile('featured_thumbnail') && $request->file('featured_thumbnail')->isValid()) {
            $data['featured_thumbnail'] = $request->file('featured_thumbnail')->store('brands', 'public');
        }

        $data = array_merge($data, $this->handleFeaturedImages($request));

        if ($request->hasFile('carousel_bg') && $request->file('carousel_bg')->isValid()) {
            $data['carousel_bg'] = $request->file('carousel_bg')->store('brands/bg', 'public');
        }

        if ($request->hasFile('detail_bg') && $request->file('detail_bg')->isValid()) {
            $data['detail_bg'] = $request->file('detail_bg')->store('brands/bg', 'public');
        }

        Brand::create($data);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Game berhasil ditambahkan']);
        }

        return redirect()->route('admin.brands')->with('success', 'Game berhasil ditambahkan');
    }

    public function brandsEdit(Brand $brand)
    {
        return view('admin.brands.edit', compact('brand'));
    }

    public function brandsUpdate(Request $request, Brand $brand)
    {
        $validator = validator($request->all(), [
            'name' => 'required|string|max:255|unique:brands,name,' . $brand->id,
            'category' => 'required|string|max:50',
            'service_type' => 'required|string|in:topup,joki,both',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'featured_thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'featured_img_1' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'featured_img_2' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'featured_img_3' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'carousel_bg' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'detail_bg' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'detail_bg_position' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'is_popular' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = [
            'name' => $request->name,
            'category' => $request->category,
            'service_type' => $request->input('service_type', 'topup'),
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

        if ($request->hasFile('featured_thumbnail') && $request->file('featured_thumbnail')->isValid()) {
            if ($brand->featured_thumbnail) {
                Storage::disk('public')->delete($brand->featured_thumbnail);
            }
            $data['featured_thumbnail'] = $request->file('featured_thumbnail')->store('brands', 'public');
        }

        $data = array_merge($data, $this->handleFeaturedImages($request, $brand));

        if ($request->hasFile('carousel_bg') && $request->file('carousel_bg')->isValid()) {
            if ($brand->carousel_bg) {
                Storage::disk('public')->delete($brand->carousel_bg);
            }
            $data['carousel_bg'] = $request->file('carousel_bg')->store('brands/bg', 'public');
        }

        if ($request->hasFile('detail_bg') && $request->file('detail_bg')->isValid()) {
            if ($brand->detail_bg) {
                Storage::disk('public')->delete($brand->detail_bg);
            }
            $data['detail_bg'] = $request->file('detail_bg')->store('brands/bg', 'public');
        }

        $data['detail_bg_position'] = $request->input('detail_bg_position', 'center');

        $brand->update($data);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Game berhasil diperbarui']);
        }

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
        if ($brand->featured_thumbnail) {
            Storage::disk('public')->delete($brand->featured_thumbnail);
        }
        foreach (['featured_img_1', 'featured_img_2', 'featured_img_3'] as $f) {
            if ($brand->$f) {
                Storage::disk('public')->delete($brand->$f);
            }
        }
        if ($brand->detail_bg) {
            Storage::disk('public')->delete($brand->detail_bg);
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
        $validator = validator($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:payment_methods',
            'category' => 'required|string|in:qris,ewallet,va,convenience_store',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = [
            'name' => $request->name,
            'code' => $request->code,
            'category' => $request->category,
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            $data['photo'] = $request->file('photo')->store('payments', 'public');
        }

        PaymentMethod::create($data);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Metode pembayaran berhasil ditambahkan']);
        }

        return redirect()->route('admin.payment-methods')->with('success', 'Metode pembayaran berhasil ditambahkan');
    }

    public function paymentMethodsEdit(PaymentMethod $paymentMethod)
    {
        return view('admin.payment-methods.edit', compact('paymentMethod'));
    }

    public function paymentMethodsUpdate(Request $request, PaymentMethod $paymentMethod)
    {
        $validator = validator($request->all(), [
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:payment_methods,code,' . $paymentMethod->id,
            'category' => 'required|string|in:qris,ewallet,va,convenience_store',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = [
            'name' => $request->name,
            'code' => $request->code,
            'category' => $request->category,
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            if ($paymentMethod->photo) {
                Storage::disk('public')->delete($paymentMethod->photo);
            }
            $data['photo'] = $request->file('photo')->store('payments', 'public');
        }

        $paymentMethod->update($data);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Metode pembayaran berhasil diperbarui']);
        }

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

        if ($request->hasFile('site_hero_banner') && $request->file('site_hero_banner')->isValid()) {
            $oldBanner = \App\Models\SiteSetting::get('site_hero_banner');
            if ($oldBanner && Storage::disk('public')->exists($oldBanner)) {
                Storage::disk('public')->delete($oldBanner);
            }
            $path = $request->file('site_hero_banner')->store('settings', 'public');
            \App\Models\SiteSetting::set('site_hero_banner', $path, 'image');
        }

        return redirect()->route('admin.settings')->with('success', 'Pengaturan berhasil disimpan');
    }

    public function digiflazzTest()
    {
        $result = $this->digiflazz->testConnection();
        return response()->json($result);
    }

    private function handleFeaturedImages(Request $request, ?Brand $brand = null): array
    {
        $data = [];
        foreach (['featured_img_1', 'featured_img_2', 'featured_img_3'] as $field) {
            if ($request->hasFile($field) && $request->file($field)->isValid()) {
                if ($brand && $brand->$field) {
                    Storage::disk('public')->delete($brand->$field);
                }
                $data[$field] = $request->file($field)->store('brands', 'public');
            }
        }
        return $data;
    }
}
