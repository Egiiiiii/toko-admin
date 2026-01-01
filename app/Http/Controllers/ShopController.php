<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia; // <--- Import Penting

class ShopController extends Controller
{
    /**
     * Menampilkan halaman utama (Home)
     * Mengarah ke: resources/js/Pages/Home/Index.vue
     */
    public function index()
    {
        // 1. DATA HERO (Bisa Null)
        // Kita load 'category' agar jika komponen Hero butuh nama kategori, datanya ada.
        $heroProduct = Product::with('category')
            ->where('is_active', true)
            ->latest()
            ->first();

        // 2. DATA FLASH SALE
        $flashSaleProducts = Product::with('category')
            ->where('is_active', true)
            ->inRandomOrder()
            ->take(4)
            ->get();

        // 3. DATA KATEGORI
        $categories = Category::limit(6)->get();

        // 4. DATA REKOMENDASI
        $recommendations = Product::with('category')
            ->where('is_active', true)
            ->oldest() // Asumsi logika rekomendasi sederhana
            ->take(4)
            ->get();

        // 5. DATA UTAMA (Pagination)
        // with('category') sangat penting untuk ProductCard.vue
        $products = Product::with('category')
            ->where('is_active', true)
            ->when($heroProduct, function ($query) use ($heroProduct) {
                // Hindari duplikasi produk jika Hero product sudah tampil di atas
                return $query->where('id', '!=', $heroProduct->id);
            })
            ->latest()
            ->paginate(12);

        // Render Vue Component
        return Inertia::render('Home/Index', [
            'heroProduct' => $heroProduct,
            'flashSaleProducts' => $flashSaleProducts,
            'categories' => $categories,
            'recommendations' => $recommendations,
            'products' => $products
        ]);
    }

    /**
     * Menampilkan halaman detail produk
     * Mengarah ke: resources/js/Pages/Product/Show.vue
     */
    public function show(Product $product)
    {
        // Load relasi kategori agar bisa ditampilkan di detail
        // Jika ada review, tambahkan 'reviews' disini: $product->load('category', 'reviews');
        $product->load('category');

        // Ambil 4 produk terkait dari kategori yang sama
        $relatedProducts = Product::with('category')
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id) // Jangan tampilkan produk yang sedang dilihat
            ->where('is_active', true)
            ->inRandomOrder()
            ->take(4)
            ->get();

        return Inertia::render('Product/Show', [
            'product' => $product,
            'relatedProducts' => $relatedProducts
        ]);
    }
}