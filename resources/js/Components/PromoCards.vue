<script setup>
// Static Component
</script>

<template>
    <section id="promo" class="scroll-mt-20">
        <h2 class="text-3xl font-bold text-gray-900 mb-6">Event & Promo Spesial</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="relative bg-gradient-to-br from-purple-500 to-pink-600 rounded-2xl overflow-hidden shadow-xl hover:shadow-2xl transition group cursor-pointer">
                <div class="absolute inset-0 bg-black/10 group-hover:bg-black/0 transition"></div>
                <div class="relative p-8 text-white">
                    <div class="inline-block bg-white/20 backdrop-blur-sm px-3 py-1 rounded-full text-sm font-semibold mb-4">HOT DEAL</div>
                    <h3 class="text-2xl font-bold mb-2">Gratis Ongkir</h3>
                    <p class="text-white/90 mb-4">Min. belanja Rp50.000</p>
                    <button class="bg-white text-purple-600 font-bold px-6 py-2 rounded-lg hover:bg-gray-100 transition">Belanja Sekarang</button>
                </div>
            </div>

            <div class="relative bg-gradient-to-br from-orange-500 to-red-600 rounded-2xl overflow-hidden shadow-xl hover:shadow-2xl transition group cursor-pointer">
                <div class="absolute inset-0 bg-black/10 group-hover:bg-black/0 transition"></div>
                <div class="relative p-8 text-white">
                    <div class="inline-block bg-white/20 backdrop-blur-sm px-3 py-1 rounded-full text-sm font-semibold mb-4">CASHBACK</div>
                    <h3 class="text-2xl font-bold mb-2">Cashbackk 50%</h3>
                    <p class="text-white/90 mb-4">Maks. cashback Rp100.000</p>
                    <button class="bg-white text-orange-600 font-bold px-6 py-2 rounded-lg hover:bg-gray-100 transition">Klaim Sekarang</button>
                </div>
            </div>

            <div class="relative bg-gradient-to-br from-green-500 to-teal-600 rounded-2xl overflow-hidden shadow-xl hover:shadow-2xl transition group cursor-pointer">
                <div class="absolute inset-0 bg-black/10 group-hover:bg-black/0 transition"></div>
                <div class="relative p-8 text-white">
                    <div class="inline-block bg-white/20 backdrop-blur-sm px-3 py-1 rounded-full text-sm font-semibold mb-4">NEW USER</div>
                    <h3 class="text-2xl font-bold mb-2">Diskon 30%</h3>
                    <p class="text-white/90 mb-4">Khusus pengguna baru</p>
                    <button class="bg-white text-green-600 font-bold px-6 py-2 rounded-lg hover:bg-gray-100 transition">Dapatkan Kupon</button>
                </div>
            </div>
        </div>
    </section>
</template>