<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    product: {
        type: Object,
        required: true
    }
});

const formatRupiah = (number) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(number);
};

const imageUrl = computed(() => {
    return props.product.image ? `/storage/${props.product.image}` : null;
});
</script>

<template>
    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 flex flex-col h-full group hover:-translate-y-2">
        <!-- Menggunakan route() global -->
        <Link :href="product.id ? route('product.show', product.id) : '#'" 
           class="relative aspect-square overflow-hidden bg-gradient-to-br from-gray-100 to-gray-200 rounded-t-2xl block">
            
            <img v-if="imageUrl" :src="imageUrl" class="object-cover w-full h-full group-hover:scale-110 transition duration-500">
            <div v-else class="flex items-center justify-center h-full text-gray-300">
                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            
            <span v-if="product.category" class="absolute top-3 left-3 bg-white/90 backdrop-blur-sm text-gray-900 text-xs font-bold px-3 py-1.5 rounded-full shadow-lg">
                {{ product.category.name }}
            </span>
        </Link>
        
        <div class="p-5 flex-grow flex flex-col">
            <Link :href="product.id ? route('product.show', product.id) : '#'" 
               class="text-lg font-bold text-gray-900 hover:text-blue-600 line-clamp-2 mb-3 block">
                {{ product.name }}
            </Link>
            
            <div class="mt-auto">
                <div class="flex items-center gap-2 mb-3">
                    <div class="flex text-yellow-400">
                        <svg v-for="i in 5" :key="i" class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                    </div>
                    <span class="text-sm text-gray-600">(4.8)</span>
                </div>
                
                <div class="flex items-end justify-between">
                    <div>
                        <div class="text-2xl font-bold text-gray-900">
                            {{ formatRupiah(product.price) }}
                        </div>
                    </div>
                    <Link :href="product.id ? route('product.show', product.id) : '#'" 
                       class="bg-blue-600 text-white p-3 rounded-xl hover:bg-blue-700 transition shadow-lg shadow-blue-600/30">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                        </svg>
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>