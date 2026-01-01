<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    product: {
        type: Object,
        default: null
    }
});
</script>

<template>
    <div class="relative bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 text-white overflow-hidden">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4wNSI+PHBhdGggZD0iTTM2IDE0YzYuNjI3IDAgMTIgNS4zNzMgMTIgMTJzLTUuMzczIDEyLTEyIDEyLTEyLTUuMzczLTEyLTEyIDUuMzczLTEyIDEyLTEyek0yNCA0NGM2LjYyNyAwIDEyIDUuMzczIDEyIDEycy01LjM3MyAxMi0xMiAxMi0xMi01LjM3My0xMi0xMiA1LjM3My0xMiAxMi0xMnoiLz48L2c+PC9nPjwvc3ZnPg==')] opacity-20"></div>
        
        <div class="max-w-7xl mx-auto px-4 py-20 relative z-10">
            <div class="text-center">
                <h1 class="text-5xl md:text-6xl font-extrabold mb-6 tracking-tight">
                    Belanja Cerdas,<br>Harga Hemat
                </h1>
                
                <p class="text-xl text-blue-100 max-w-2xl mx-auto mb-8">
                    <span v-if="product">
                        Promo Spesial: <span class="font-bold text-white">{{ product.name }}</span>
                    </span>
                    <span v-else>
                        Temukan ribuan produk berkualitas dengan harga terbaik.
                    </span>
                </p>
                
                <div class="flex flex-wrap gap-4 justify-center">
                    <Link :href="product ? route('product.show', product.id) : '#products'" 
                       class="bg-white text-blue-600 font-bold px-8 py-4 rounded-xl hover:bg-gray-50 transition shadow-xl hover:scale-105 transform duration-200">
                        {{ product ? 'Beli Sekarang' : 'Mulai Belanja' }}
                    </Link>

                    <a href="#promo" class="bg-blue-500/30 backdrop-blur-sm text-white font-bold px-8 py-4 rounded-xl hover:bg-blue-500/50 transition border-2 border-white/30">
                        Lihat Promo
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>