<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    products: {
        type: Array,
        default: () => []
    }
});

const countdown = ref('02:45:30');
let timer = null;

const formatRupiah = (number) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
};

// Generate random number untuk progress bar (mirip rand(40,90) di PHP)
const getRandomProgress = () => Math.floor(Math.random() * (90 - 40 + 1)) + 40;

onMounted(() => {
    let timeLeft = 2 * 3600 + 45 * 60 + 30; // 2:45:30
    timer = setInterval(() => {
        if (timeLeft > 0) {
            timeLeft--;
            const hours = Math.floor(timeLeft / 3600).toString().padStart(2, '0');
            const minutes = Math.floor((timeLeft % 3600) / 60).toString().padStart(2, '0');
            const seconds = (timeLeft % 60).toString().padStart(2, '0');
            countdown.value = `${hours}:${minutes}:${seconds}`;
        }
    }, 1000);
});

onUnmounted(() => {
    if (timer) clearInterval(timer);
});
</script>

<template>
    <div class="bg-gradient-to-r from-red-500 to-pink-600 text-white py-8 mb-8">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex flex-col md:flex-row items-center justify-between mb-6 gap-4">
                <div class="flex items-center gap-3">
                    <svg class="w-8 h-8 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z"/>
                    </svg>
                    <div>
                        <h3 class="font-bold text-2xl">FLASH SALE</h3>
                        <p class="text-white/80 text-sm">Berakhir dalam:</p>
                    </div>
                </div>
                
                <div class="flex items-center gap-2 bg-white/20 px-6 py-3 rounded-xl backdrop-blur-sm border border-white/20">
                    <span class="font-mono font-bold text-xl tracking-widest">{{ countdown }}</span>
                </div>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div v-for="product in products" :key="product.id" class="bg-white rounded-xl p-3 text-gray-900 shadow-lg hover:-translate-y-1 transition duration-300">
                    <Link :href="route('product.show', product.id)" class="block">
                        <div class="aspect-square bg-gray-100 rounded-lg mb-3 overflow-hidden relative">
                             <img v-if="product.image" :src="`/storage/${product.image}`" class="object-cover w-full h-full">
                            <div v-else class="w-full h-full bg-gray-200 flex items-center justify-center text-gray-400 text-xs">No Image</div>
                            <span class="absolute top-2 right-2 bg-red-600 text-white text-xs font-bold px-2 py-1 rounded-md animate-pulse">-50%</span>
                        </div>
                        
                        <h4 class="font-bold text-sm truncate mb-1">{{ product.name }}</h4>
                        
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-red-600">{{ formatRupiah(product.price * 0.5) }}</span>
                        </div>
                        <span class="text-xs text-gray-400 line-through">{{ formatRupiah(product.price) }}</span>
                        
                        <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
                            <div class="bg-red-500 h-1.5 rounded-full" :style="{ width: getRandomProgress() + '%' }"></div>
                        </div>
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>