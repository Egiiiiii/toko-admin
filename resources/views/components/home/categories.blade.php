@props(['categories' => []])

<div class="bg-white border-b sticky top-16 z-40 shadow-sm">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex gap-2 overflow-x-auto py-4 no-scrollbar">
            <a href="{{ route('home') }}" class="px-6 py-2.5 rounded-full bg-blue-600 text-white font-semibold whitespace-nowrap shadow-lg shadow-blue-600/30 hover:bg-blue-700 transition">
                Semua
            </a>

            @foreach($categories as $category)
                <button class="px-6 py-2.5 rounded-full bg-white text-gray-700 font-semibold whitespace-nowrap border-2 border-gray-200 hover:border-blue-600 hover:text-blue-600 transition">
                    {{ $category->name }}
                </button>
            @endforeach
        </div>
    </div>
</div>