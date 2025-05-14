@props(['genres'])

<div class="py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-2xl font-bold mb-8">Géneros populares</h2>
        
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @foreach($genres as $genre)
                <a href="{{ route('catalog.genre', $genre->slug) }}" 
                   class="block text-center p-4 rounded-lg bg-gray-800 hover:bg-gray-700 transition duration-300">
                    <span class="text-red-500 block mb-2">
                        @switch(strtolower($genre->name))
                            @case('romance')
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                </svg>
                                @break
                            @case('comedia')
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm0 18c-4.411 0-8-3.589-8-8s3.589-8 8-8 8 3.589 8 8-3.589 8-8 8zm-5-9a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm5 0a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm-5 6c3 0 4-2 4-2H8s1 2 4 2z"/>
                                </svg>
                                @break
                            @case('acción')
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 16V6H5v10h14m0-12c1.1 0 2 .9 2 2v10c0 1.1-.9 2-2 2H5c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2h14M7 8h2v2H7V8m0 4h2v2H7v-2m4-4h2v2h-2V8m0 4h2v2h-2v-2m4-4h2v2h-2V8m0 4h2v2h-2v-2z"/>
                                </svg>
                                @break
                            @case('drama')
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.9 2 2 2zm6-6v-5c0-3.07-1.63-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.64 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2zm-2 1H8v-6c0-2.48 1.51-4.5 4-4.5s4 2.02 4 4.5v6z"/>
                                </svg>
                                @break
                            @case('histórico')
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-.5-13H11v6l5.2 3.2.8-1.3-4.5-2.7z"/>
                                </svg>
                                @break
                            @default
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 3c-4.97 0-9 4.03-9 9s4.03 9 9 9 9-4.03 9-9-4.03-9-9-9zm0 16c-3.86 0-7-3.14-7-7s3.14-7 7-7 7 3.14 7 7-3.14 7-7 7zm-1-11h2v2h-2zm0 4h2v6h-2z"/>
                                </svg>
                        @endswitch
                    </span>
                    <span class="block font-medium">{{ $genre->name }}</span>
                </a>
            @endforeach
        </div>
    </div>
</div>