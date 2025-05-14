@props(['categories'])

<div class="py-10 bg-black bg-opacity-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold mb-8 text-center">Explora por categorías</h2>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach($categories as $category)
                <a href="{{ route('catalog.category', $category->slug) }}" 
                   class="block group relative rounded overflow-hidden shadow-lg h-40 bg-gradient-to-r from-gray-900 to-black transform transition duration-300 hover:scale-105">
                    
                    @if($category->image)
                        <img src="{{ asset($category->image) }}" alt="{{ $category->name }}" 
                             class="absolute inset-0 h-full w-full object-cover opacity-70 group-hover:opacity-50 transition duration-300">
                    @else
                        <img src="{{ asset('images/categories/' . $category->slug . '.jpg') }}" alt="{{ $category->name }}" 
                             class="absolute inset-0 h-full w-full object-cover opacity-70 group-hover:opacity-50 transition duration-300">
                    @endif
                    
                    <div class="absolute inset-0 bg-gradient-to-t from-black via-transparent to-transparent"></div>
                    
                    <div class="absolute bottom-0 left-0 p-4 w-full">
                        <h3 class="text-lg font-bold">{{ $category->name }}</h3>
                        @if($category->titles_count)
                            <p class="text-sm text-gray-300">{{ $category->titles_count }} títulos</p>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    </div>
</div>