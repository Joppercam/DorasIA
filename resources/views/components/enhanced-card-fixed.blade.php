@props(['title'])

<div class="w-[180px] md:w-[200px] flex-shrink-0 px-1 md:px-2 scroll-snap-align-start" style="scroll-snap-align: start;">
    <a href="{{ route('titles.show', $title->slug) }}" class="block dorasia-card h-full">
        <div class="relative pb-[150%] rounded overflow-hidden shadow-lg mb-2 bg-gray-800">
            @if(!empty($title->poster))
                @php
                    // Normalize the poster path for correct asset resolution
                    $posterPath = $title->poster;
                    
                    // If path starts with storage/ but doesn't have a leading slash, it's relative to public
                    if (Str::startsWith($posterPath, 'storage/')) {
                        $posterPath = $posterPath; // Keep as is, asset() will handle it
                    }
                    // If path starts with /storage/, remove the leading slash
                    elseif (Str::startsWith($posterPath, '/storage/')) {
                        $posterPath = substr($posterPath, 1);
                    }
                    // Other relative paths may need to be adjusted
                    elseif (Str::startsWith($posterPath, '/')) {
                        $posterPath = substr($posterPath, 1);
                    }
                    
                    // Uncomment for debugging
                    // $originalPath = $title->poster;
                    // $normalizedPath = $posterPath;
                @endphp
                
                <img src="{{ asset($posterPath) }}" alt="{{ $title->title }}" 
                     class="absolute inset-0 h-full w-full object-cover">
                
                {{-- Uncomment for debugging path issues
                <div class="absolute inset-0 flex flex-col items-center justify-center bg-black/80 text-white text-xs p-1 opacity-0 hover:opacity-100">
                    <span>Original: {{ $originalPath }}</span>
                    <span>Normalized: {{ $normalizedPath }}</span>
                    <span>Asset: {{ asset($posterPath) }}</span>
                </div>
                --}}
            @else
                <!-- Imagen de poster predeterminada -->
                <img src="{{ asset('posters/placeholder.jpg') }}" alt="{{ $title->title }}" 
                     class="absolute inset-0 h-full w-full object-cover">
                <div class="absolute inset-0 flex items-center justify-center bg-black/50 text-white text-center p-2">
                    <span>{{ $title->title }}</span>
                </div>
            @endif
            
            <!-- Etiqueta de país en la esquina superior -->
            <div class="absolute top-0 right-0 bg-black/80 px-1.5 py-0.5 text-xs uppercase font-semibold
                        @if($title->country == 'Corea del Sur') text-cyan-400
                        @elseif($title->country == 'Japón') text-red-400
                        @elseif(in_array($title->country, ['China', 'Taiwán', 'Hong Kong'])) text-amber-400
                        @else text-white @endif">
                {{ Str::limit($title->country, 5, '') }}
            </div>
            
            <!-- Valoración TMDB en la esquina superior izquierda -->
            @if(!empty($title->vote_average))
            <div class="absolute top-0 left-0 bg-black/80 rounded-br px-1.5 py-0.5 text-xs font-semibold 
                        @if($title->vote_average >= 7) text-green-400
                        @elseif($title->vote_average >= 5) text-yellow-400
                        @else text-red-400 @endif">
                <div class="flex items-center">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                    {{ number_format($title->vote_average, 1) }}
                </div>
            </div>
            @endif
            
            <!-- Overlay con información -->
            <div class="absolute inset-0 bg-black/80 opacity-0 hover:opacity-100 transition-opacity duration-300 flex flex-col justify-between p-3 text-xs">
                <div>
                    <p class="font-bold">{{ $title->title }}</p>
                    <p class="text-gray-400">{{ $title->release_year }}</p>
                    
                    <!-- Géneros -->
                    <div class="flex flex-wrap mt-1 gap-1">
                        @foreach($title->genres->take(3) as $genre)
                            <span class="bg-red-600 px-1.5 py-0.5 rounded-sm text-xs">{{ $genre->name }}</span>
                        @endforeach
                    </div>
                </div>
                
                <!-- Plataformas de streaming (si están disponibles) -->
                @if(!empty($title->streaming_platforms))
                <div class="mt-2">
                    <p class="text-gray-300 mb-1 text-xs">Disponible en:</p>
                    <div class="flex flex-wrap gap-1">
                        @php
                            $platforms = explode(',', $title->streaming_platforms);
                        @endphp
                        
                        @foreach($platforms as $platform)
                            <span class="bg-gray-800 px-1.5 py-0.5 rounded-sm text-xs">{{ trim($platform) }}</span>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
        <h3 class="text-sm truncate">{{ $title->title }}</h3>
    </a>
</div>