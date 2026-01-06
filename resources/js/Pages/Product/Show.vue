<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ProductCard from '@/Components/ProductCard.vue';
import { computed } from 'vue';

const props = defineProps({
    product: Object,
    relatedProducts: Array
});

// --- PERBAIKAN DI SINI ---
const apiBaseUrl = import.meta.env.VITE_API_BASE_URL || '';

const formatRupiah = (val) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(val);

// Logic Image URL yang aman untuk API/Kubernetes
const imageUrl = computed(() => {
    if (!props.product.image) return null;
    
    // Jika url gambar sudah lengkap (http...), biarkan
    if (props.product.image.startsWith('http')) return props.product.image;

    // Jika path lokal, sambungkan dengan API Base URL
    return `${apiBaseUrl}/storage/${props.product.image}`;
});
// -------------------------
</script>

<template>
    <Head :title="product.name" />

    <AppLayout>
        <div class="max-w-7xl mx-auto px-4 py-12">
            <nav class="flex mb-8 text-sm text-gray-500">
                <Link href="/" class="hover:text-blue-600">Beranda</Link>
                <span class="mx-2">/</span>
                <span class="font-medium text-gray-900">{{ product.category ? product.category.name : 'Produk' }}</span>
                <span class="mx-2">/</span>
                <span class="truncate max-w-xs text-gray-400">{{ product.name }}</span>
            </nav>

            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden mb-16">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-0">
                    <div class="p-8 bg-gray-50 flex items-center justify-center border-b md:border-b-0 md:border-r border-gray-100">
                        <div class="relative w-full aspect-square bg-white rounded-xl shadow-sm overflow-hidden group">
                             <img v-if="imageUrl" :src="imageUrl" class="object-cover w-full h-full transition duration-500 group-hover:scale-105" :alt="product.name">
                             <div v-else class="flex items-center justify-center h-full text-gray-300 bg-gray-100">
                                <svg class="w-24 h-24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                             </div>
                        </div>
                    </div>

                    <div class="p-8 md:p-12 flex flex-col">
                        <div class="mb-auto">
                            <span v-if="product.category" class="inline-block bg-blue-100 text-blue-800 text-xs font-bold px-3 py-1 rounded-full mb-4">
                                {{ product.category.name }}
                            </span>
                            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">{{ product.name }}</h1>
                            
                            <div class="flex items-end gap-3 mb-6">
                                <div class="text-3xl font-bold text-blue-600">
                                    {{ formatRupiah(product.price) }}
                                </div>
                                <div class="text-lg text-gray-400 line-through mb-1">
                                    {{ formatRupiah(product.price * 1.1) }}
                                </div>
                            </div>
                            
                            <div class="prose prose-blue text-gray-600 mb-8 max-w-none leading-relaxed" v-html="product.description || 'Tidak ada deskripsi produk.'"></div>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-4 border-t border-gray-100 pt-8 mt-8">
                            <button class="flex-1 bg-blue-600 text-white font-bold py-4 px-6 rounded-xl hover:bg-blue-700 transition shadow-lg shadow-blue-600/30 flex items-center justify-center gap-2 group">
                                <svg class="w-5 h-5 group-hover:scale-110 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>
                                Tambah ke Keranjang
                            </button>
                            <button class="flex-1 bg-white text-gray-900 border-2 border-gray-200 font-bold py-4 px-6 rounded-xl hover:border-blue-600 hover:text-blue-600 transition">
                                Beli Sekarang
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="relatedProducts && relatedProducts.length > 0">
                <h3 class="text-2xl font-bold text-gray-900 mb-6">Produk Terkait</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                     <ProductCard v-for="related in relatedProducts" :key="related.id" :product="related" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>