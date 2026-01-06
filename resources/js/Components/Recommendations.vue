<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    products: {
        type: Array,
        default: () => []
    }
});

// --- PERBAIKAN DI SINI ---
// Ambil URL API dari Environment Variable
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL || '';

// Helper untuk generate URL gambar yang benar (mengarah ke API)
const getImageUrl = (path) => {
    if (!path) return null;
    // Jika path sudah full URL (misal dari S3), biarkan
    if (path.startsWith('http')) return path;
    
    // Jika path lokal, gabungkan dengan domain API
    return `${apiBaseUrl}/storage/${path}`;
};
// -------------------------

const formatRupiah = (number) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
};
</script>

<template>
    <section>
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-3xl font-bold text-gray-900">Rekomendasi Untuk Anda</h2>
                <p class="text-gray-600 mt-1">Dipilih khusus berdasarkan preferensi Anda</p>
            </div>
            <a href="#products" class="text-blue-600 font-semibold hover:text-blue-700 flex items-center gap-2 group">
                Lihat Semua
                <svg class="w-5 h-5 group-hover:translate-x-1 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
        
        <div class="relative">
            <div class="flex gap-6 overflow-x-auto pb-8 pt-2 px-1 snap-x snap-mandatory no-scrollbar">
                <div v-for="product in products" :key="product.id" class="flex-none w-72 snap-start">
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 flex flex-col h-full group hover:-translate-y-2">
                        <Link :href="route('product.show', product.id)" class="relative aspect-square overflow-hidden bg-gradient-to-br from-gray-100 to-gray-200 rounded-t-2xl block">
                            
                            <img v-if="product.image" :src="getImageUrl(product.image)" class="object-cover w-full h-full group-hover:scale-110 transition duration-500">
                            
                            <div v-else class="flex items-center justify-center h-full text-gray-300">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            
                            <div class="absolute top-3 left-3 bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-lg">
                                -10%
                            </div>
                        </Link>
                        
                        <div class="p-5 flex-grow flex flex-col">
                            <span class="text-xs font-semibold text-blue-600 mb-2">{{ product.category ? product.category.name : 'Umum' }}</span>
                            <Link :href="route('product.show', product.id)" class="text-lg font-bold text-gray-900 line-clamp-2 mb-3 hover:text-blue-600">
                                {{ product.name }}
                            </Link>
                            
                            <div class="mt-auto">
                                <div class="flex items-end justify-between">
                                    <div>
                                        <div class="text-sm text-gray-400 line-through">{{ formatRupiah(product.price * 1.1) }}</div>
                                        <div class="text-2xl font-bold text-gray-900">{{ formatRupiah(product.price) }}</div>
                                    </div>
                                    <Link :href="route('product.show', product.id)" class="bg-blue-600 text-white p-3 rounded-xl hover:bg-blue-700 transition shadow-lg shadow-blue-600/30">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
</style>