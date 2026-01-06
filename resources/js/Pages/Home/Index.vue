<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Hero from '@/Components/Hero.vue';           
import FlashSale from '@/Components/FlashSale.vue'; 
import CategoryBar from '@/Components/CategoryBar.vue'; 
import PromoCards from '@/Components/PromoCards.vue';   
import Recommendations from '@/Components/Recommendations.vue'; 
import ProductCard from '@/Components/ProductCard.vue'; 

defineProps({
    heroProduct: Object,
    flashSaleProducts: Array,
    categories: Array,
    recommendations: Array,
    products: Object 
});

// --- PERBAIKAN: Helper untuk Pagination ---
// Fungsi ini memastikan link pagination tetap berjalan di Frontend
// meskipun Backend men-generate URL dengan domain API.
const getPaginationUrl = (url) => {
    if (!url) return null;
    try {
        // Kita hanya ambil Query String-nya saja (contoh: "?page=2")
        // Ini memaksa Inertia tetap di halaman sekarang, hanya ganti parameter page.
        const urlObj = new URL(url);
        return urlObj.search;
    } catch (e) {
        return '#';
    }
};
// ------------------------------------------
</script>

<template>
    <Head title="Beranda" />

    <AppLayout>
        <Hero :product="heroProduct" />
        <FlashSale :products="flashSaleProducts" />
        <CategoryBar :categories="categories" />
        
        <div class="max-w-7xl mx-auto px-4 py-12 space-y-16">
            
            <PromoCards />
            <Recommendations :products="recommendations" />
            
            <section id="products" class="scroll-mt-20">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900">Produk Terbaru</h2>
                        <p class="text-gray-600 mt-1">Produk terbaru dengan kualitas terbaik</p>
                    </div>
                </div>

                <div v-if="products.data.length" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <ProductCard v-for="product in products.data" :key="product.id" :product="product" />
                </div>
                
                <div v-else class="col-span-full text-center py-12 text-gray-500 bg-white rounded-xl border border-gray-100 p-12">
                    <p class="text-lg">Belum ada produk yang tersedia.</p>
                </div>

                <div class="mt-12 flex justify-center flex-wrap gap-1">
                     <Link v-for="(link, k) in products.links" 
                        :key="k" 
                        :href="getPaginationUrl(link.url) ?? '#'" 
                        v-html="link.label"
                        class="px-4 py-2 border rounded-lg text-sm transition-colors"
                        :class="[
                            link.active ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50',
                            !link.url ? 'opacity-50 cursor-not-allowed' : ''
                        ]"
                        :preserve-scroll="true" 
                     />
                     </div>
            </section>

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