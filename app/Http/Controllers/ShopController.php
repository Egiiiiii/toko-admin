<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    /**
     * Menampilkan halaman utama (Home)
     */
    public function index()
    {
        // 1. DATA HERO (Bisa Null)
        $heroProduct = Product::where('is_active', true)->latest()->first();

        // 2. DATA FLASH SALE
        $flashSaleProducts = Product::where('is_active', true)->inRandomOrder()->take(4)->get();

        // 3. DATA KATEGORI
        $categories = Category::limit(6)->get();

        // 4. DATA REKOMENDASI
        $recommendations = Product::where('is_active', true)->oldest()->take(4)->get();

        // 5. DATA UTAMA
        $products = Product::where('is_active', true)
            ->when($heroProduct, function ($query) use ($heroProduct) {
                return $query->where('id', '!=', $heroProduct->id);
            })
            ->latest()
            ->paginate(12);

        return view('index', compact(
            'heroProduct',
            'flashSaleProducts',
            'categories',
            'recommendations',
            'products'
        ));
    }

    /**
     * Menampilkan halaman detail produk
     */
    public function show(Product $product)
    {
        // Ambil 4 produk terkait dari kategori yang sama
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id) // Jangan tampilkan produk yang sedang dilihat
            ->where('is_active', true)
            ->inRandomOrder()
            ->take(4)
            ->get();

        return view('shop.show', compact('product', 'relatedProducts'));
    }
}