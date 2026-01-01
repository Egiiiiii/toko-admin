<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';

const isMobileMenuOpen = ref(false);
const currentYear = new Date().getFullYear();

const toggleMobileMenu = () => {
    isMobileMenuOpen.value = !isMobileMenuOpen.value;
};
</script>

<template>
    <div class="flex flex-col min-h-screen bg-gray-50 font-sans text-gray-900">
        
        <!-- Navigation -->
        <nav class="bg-white/80 backdrop-blur-md border-b border-gray-200/50 sticky top-0 z-50 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16 items-center">
                    <!-- Logo -->
                    <Link href="/" class="flex items-center gap-3 group">
                        <div class="bg-gradient-to-br from-blue-600 to-blue-700 text-white p-2.5 rounded-xl group-hover:scale-110 group-hover:shadow-lg group-hover:shadow-blue-500/30 transition-all duration-300">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        <span class="font-bold text-xl tracking-tight bg-gradient-to-r from-gray-900 to-gray-600 bg-clip-text text-transparent">
                            Toko<span class="bg-gradient-to-r from-blue-600 to-blue-700 bg-clip-text text-transparent">Admin</span>
                        </span>
                    </Link>

                    <!-- Main Navigation Menu -->
                    <div class="hidden md:flex items-center gap-1">
                        <Link href="/" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 transition-colors rounded-lg hover:bg-gray-50">
                            Beranda
                        </Link>
                        <Link href="/orders" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 transition-colors rounded-lg hover:bg-gray-50">
                            Pesanan
                        </Link>
                        <Link href="/promo" class="px-4 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 transition-colors rounded-lg hover:bg-gray-50">
                            Promo
                        </Link>
                    </div>

                    <!-- Right Side Actions -->
                    <div class="flex items-center gap-4">
                        <!-- Login Admin (Arahkan ke route Laravel biasa /admin, bukan Inertia Link) -->
                        <a href="/admin" class="group flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg hover:shadow-lg hover:shadow-blue-500/30 hover:scale-105 transition-all duration-200">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                            <span class="hidden lg:inline">Admin</span>
                        </a>

                        <!-- Mobile Menu Button -->
                        <button @click="toggleMobileMenu" class="md:hidden p-2 text-gray-600 hover:text-blue-600 hover:bg-gray-100 rounded-lg transition-colors">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path v-if="!isMobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div v-show="isMobileMenuOpen" class="md:hidden border-t border-gray-100 bg-white">
                <div class="px-2 pt-2 pb-3 space-y-1">
                    <Link href="/" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">Beranda</Link>
                    <Link href="/orders" class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:text-blue-600 hover:bg-gray-50">Pesanan</Link>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="flex-grow">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="bg-white/80 backdrop-blur-sm border-t border-gray-200/50 mt-auto">
            <div class="max-w-7xl mx-auto py-8 px-4">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-2">
                        <div class="bg-gradient-to-br from-blue-600 to-blue-700 text-white p-1.5 rounded-lg">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                        </div>
                        <span class="font-semibold text-gray-700">Toko<span class="text-blue-600">Admin</span></span>
                    </div>
                    <p class="text-gray-500 text-sm">
                        &copy; {{ currentYear }} Toko Admin. Built with <span class="text-red-500">❤</span> using Laravel & Vue.
                    </p>
                </div>
            </div>
        </footer>
    </div>
</template>