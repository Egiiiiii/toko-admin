<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Hero from '@/Components/Hero.vue';           // dari 3.php
import FlashSale from '@/Components/FlashSale.vue'; // dari 2.php
import CategoryBar from '@/Components/CategoryBar.vue'; // dari 1.php
import PromoCards from '@/Components/PromoCards.vue';   // dari 5.php
import Recommendations from '@/Components/Recommendations.vue'; // dari 6.php
import ProductCard from '@/Components/ProductCard.vue'; // dari 4.php (dipakai di Grid Utama)

defineProps({
    heroProduct: Object,
    flashSaleProducts: Array,
    categories: Array,
    recommendations: Array,
    products: Object // Paginator Object dari Laravel
});
</script>

<template>
    <Head title="Beranda" />

    <AppLayout>
        <!-- 3.php: Hero Section -->
        <Hero :product="heroProduct" />

        <!-- 2.php: Flash Sale -->
        <FlashSale :products="flashSaleProducts" />

        <!-- 1.php: Categories Sticky Bar -->
        <CategoryBar :categories="categories" />
        
        <div class="max-w-7xl mx-auto px-4 py-12 space-y-16">
            
            <!-- 5.php: Promo Cards -->
            <PromoCards />
            
            <!-- 6.php: Recommendations (Horizontal Scroll) -->
            <Recommendations :products="recommendations" />
            
            <!-- Grid Produk Utama (Logic asli dari index.blade.php) -->
            <section id="products" class="scroll-mt-20">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900">Produk Terbaru</h2>
                        <p class="text-gray-600 mt-1">Produk terbaru dengan kualitas terbaik</p>
                    </div>
                </div>

                <!-- Menggunakan Component ProductCard (4.php) -->
                <div v-if="products.data.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <ProductCard v-for="product in products.data" :key="product.id" :product="product" />
                </div>
                
                <!-- Empty State -->
                <div v-else class="col-span-full text-center py-12 text-gray-500 bg-white rounded-xl border border-gray-100 p-12">
                    <p class="text-lg">Belum ada produk yang tersedia.</p>
                </div>

                <!-- Pagination Laravel -->
                <div class="mt-12 flex justify-center flex-wrap gap-1">
                     <Link v-for="(link, k) in products.links" 
                        :key="k" 
                        :href="link.url ?? '#'" 
                        v-html="link.label"
                        class="px-4 py-2 border rounded-lg text-sm transition-colors"
                        :class="[
                            link.active ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50',
                            !link.url ? 'opacity-50 cursor-not-allowed' : ''
                        ]"
                     />
                </div>
            </section>

            <!-- Brand Partners (Static section dari index.blade.php asli Anda) -->
            <section class="bg-white rounded-2xl shadow-lg border border-gray-100 p-8">
                <h2 class="text-2xl font-bold text-center text-gray-900 mb-8">Brand Partner Kami</h2>
                <div class="grid grid-cols-2 md:grid-cols-6 gap-6 items-center opacity-60 grayscale hover:grayscale-0 transition duration-500">
                    <div v-for="i in 6" :key="i" class="flex items-center justify-center h-16 bg-gray-100 rounded-lg group hover:bg-blue-50 transition cursor-pointer border border-transparent hover:border-blue-200">
                        <span class="font-bold text-gray-400 group-hover:text-blue-600 transition">BRAND {{ i }}</span>
                    </div>
                </div>
            </section>
        </div>
    </AppLayout>
</template>