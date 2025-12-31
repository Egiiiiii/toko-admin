@props(['product'])

<div class="bg-white rounded-2xl shadow-lg border border-gray-100 hover:shadow-2xl transition-all duration-300 flex flex-col h-full group hover:-translate-y-2">
    <a href="{{ ($product && $product->id) ? route('product.show', $product) : '#' }}" 
       class="relative aspect-square overflow-hidden bg-gradient-to-br from-gray-100 to-gray-200 rounded-t-2xl block">
        
        @if($product->image)
            <img src="{{ Storage::url($product->image) }}" class="object-cover w-full h-full group-hover:scale-110 transition duration-500">
        @else
            <div class="flex items-center justify-center h-full text-gray-300">
                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        @endif
        
        @if($product->category)
            <span class="absolute top-3 left-3 bg-white/90 backdrop-blur-sm text-gray-900 text-xs font-bold px-3 py-1.5 rounded-full shadow-lg">
                {{ $product->category->name }}
            </span>
        @endif
    </a>
    
    <div class="p-5 flex-grow flex flex-col">
        <a href="{{ ($product && $product->id) ? route('product.show', $product) : '#' }}" 
           class="text-lg font-bold text-gray-900 hover:text-blue-600 line-clamp-2 mb-3 block">
            {{ $product->name }}
        </a>
        
        <div class="mt-auto">
            <div class="flex items-center gap-2 mb-3">
                <div class="flex text-yellow-400">
                    @for($i = 0; $i < 5; $i++)
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M10 15l-5.878 3.09 1.123-6.545L.489 6.91l6.572-.955L10 0l2.939 5.955 6.572.955-4.756 4.635 1.123 6.545z"/></svg>
                    @endfor
                </div>
                <span class="text-sm text-gray-600">(4.8)</span>
            </div>
            
            <div class="flex items-end justify-between">
                <div>
                    <div class="text-2xl font-bold text-gray-900">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </div>
                </div>
                <a href="{{ ($product && $product->id) ? route('product.show', $product) : '#' }}" 
                   class="bg-blue-600 text-white p-3 rounded-xl hover:bg-blue-700 transition shadow-lg shadow-blue-600/30">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</div>