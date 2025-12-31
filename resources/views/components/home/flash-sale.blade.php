@props(['products' => []])

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
                <span class="font-mono font-bold text-xl tracking-widest" id="countdown">02:45:30</span>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($products as $product)
            <div class="bg-white rounded-xl p-3 text-gray-900 shadow-lg hover:-translate-y-1 transition duration-300">
                <a href="{{ route('product.show', $product) }}" class="block">
                    <div class="aspect-square bg-gray-100 rounded-lg mb-3 overflow-hidden relative">
                         @if($product->image)
                            <img src="{{ Storage::url($product->image) }}" class="object-cover w-full h-full">
                        @else
                            <div class="w-full h-full bg-gray-200 flex items-center justify-center text-gray-400 text-xs">No Image</div>
                        @endif
                        <span class="absolute top-2 right-2 bg-red-600 text-white text-xs font-bold px-2 py-1 rounded-md animate-pulse">-50%</span>
                    </div>
                    
                    <h4 class="font-bold text-sm truncate mb-1">{{ $product->name }}</h4>
                    
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-red-600">Rp {{ number_format($product->price * 0.5, 0, ',', '.') }}</span>
                    </div>
                    <span class="text-xs text-gray-400 line-through">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                    
                    <div class="w-full bg-gray-200 rounded-full h-1.5 mt-2">
                        <div class="bg-red-500 h-1.5 rounded-full" style="width: {{ rand(40, 90) }}%"></div>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</div>

<script>
// Simple countdown timer logic
let timeLeft = 2 * 3600 + 45 * 60 + 30; // 2:45:30
setInterval(() => {
    if (timeLeft > 0) {
        timeLeft--;
        const hours = Math.floor(timeLeft / 3600).toString().padStart(2, '0');
        const minutes = Math.floor((timeLeft % 3600) / 60).toString().padStart(2, '0');
        const seconds = (timeLeft % 60).toString().padStart(2, '0');
        document.getElementById('countdown').textContent = `${hours}:${minutes}:${seconds}`;
    }
}, 1000);
</script>