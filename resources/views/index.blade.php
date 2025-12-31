<x-layout>
    <x-home.hero :product="$heroProduct ?? null" />

    <x-home.flash-sale :products="$flashSaleProducts ?? []" />

    <x-home.categories :categories="$categories ?? []" />
    
    <div class="max-w-7xl mx-auto px-4 py-12 space-y-16">
        
        <x-home.promo-cards />
        
        <x-home.recommendations :products="$recommendations ?? []" />
        
        <section id="products" class="scroll-mt-20">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-3xl font-bold text-gray-900">Produk Terbaru</h2>
                    <p class="text-gray-600 mt-1">Produk terbaru dengan kualitas terbaik</p>
                </div>
                <div class="flex gap-2">
                    <button class="p-2 border-2 border-gray-200 rounded-lg hover:border-blue-600 hover:text-blue-600 transition">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <button class="p-2 border-2 border-blue-600 text-blue-600 rounded-lg">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @forelse($products as $product)
                    <x-home.product-card :product="$product" />
                @empty
                    <div class="col-span-full text-center py-12 text-gray-500">
                        Belum ada produk yang tersedia.
                    </div>
                @endforelse
            </div>

            <div class="mt-12">
                {{ $products->links() }}
            </div>
        </section>

        <section class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
            <h2 class="text-2xl font-bold text-center text-gray-900 mb-8">Brand Partner Kami</h2>
            <div class="grid grid-cols-3 md:grid-cols-6 gap-8 items-center opacity-60 grayscale hover:grayscale-0 transition">
                @for($i = 0; $i < 6; $i++)
                <div class="flex items-center justify-center h-16 bg-gray-100 rounded-lg group hover:bg-blue-50 transition cursor-pointer">
                    <span class="font-bold text-gray-400 group-hover:text-blue-600">BRAND {{ $i + 1 }}</span>
                </div>
                @endfor
            </div>
        </section>
    </div>

    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</x-layout>