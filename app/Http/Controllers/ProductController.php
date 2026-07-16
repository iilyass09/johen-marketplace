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
        $result = $this->digiflazz->syncProducts();

        if ($result['success']) {
            return redirect()->route('products.index')->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }
}
