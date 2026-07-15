<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\DigiflazzService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected DigiflazzService $digiflazz;

    public function __construct(DigiflazzService $digiflazz)
    {
        $this->digiflazz = $digiflazz;
    }

    public function index()
    {
        $products = Product::where('is_active', true)->orderBy('brand')->get()->groupBy('brand');
        return view('products.index', compact('products'));
    }

    public function sync()
    {
        $data = $this->digiflazz->getPriceList();

        if (empty($data)) {
            return back()->with('error', 'Gagal mengambil data dari Digiflazz');
        }

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
                    'stock' => 999,
                ]
            );
        }

        return redirect()->route('products.index')->with('success', 'Produk berhasil disinkronisasi');
    }
}
