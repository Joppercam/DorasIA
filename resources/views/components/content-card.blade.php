@props(['item'])

<div class="carousel-item">
    <div class="content-card bg-gray-800 rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition group relative">
        <!-- Poster Image -->
        <div class="relative aspect-[2/3] overflow-hidden">
            <img 
                src="{{ $item->poster_path ?? asset('images/poster-placeholder.jpg') }}" 
                alt="{{ $item->title }}"
                class="w-full h-full object-cover transform group-hover:scale-105 transition duration-500"
                loading="lazy"
            >
            
            <!-- Hover Overlay -->
            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-4">
                <!-- Platform Badges (if available) -->
                @if(!empty($item->available_on) && count($item->available_on) > 0)
                    <div class="flex flex-wrap gap-1 mb-3">
                        @foreach($item->available_on as $platform)
                            <img 
                                src="{{ $platform->logo_url }}" 
                                alt="{{ $platform->name }}" 
                                class="h-5 w-auto rounded"
                                title="{{ $platform->name }}"
                            >
                        @endforeach
                    </div>
                @endif
                
                <!-- Action Buttons -->
                <div class="flex space-x-2">
                    <a 
                        href="{{ route($item->type === 'movie' ? 'movies.show' : 'series.show', $item->id) }}" 
                        class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded py-1.5 text-center transition"
                    >
                        Detalles
                    </a>
                    <button 
                        @click.stop="$store.watchlist.toggle('{{ $item->id }}', '{{ $item->type }}')" 
                        class="bg-gray-700 hover:bg-gray-600 text-white w-9 rounded flex items-center justify-center transition"
                        x-data
                        x-bind:class="$store.watchlist.exists('{{ $item->id }}') ? 'bg-gray-600' : 'bg-gray-700'"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-bind:stroke-width="$store.watchlist.exists('{{ $item->id }}') ? 2.5 : 1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" x-show="!$store.watchlist.exists('{{ $item->id }}')" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" x-show="$store.watchlist.exists('{{ $item->id }}')" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Content Info -->
        <div class="p-4">
            <!-- Badges -->
            <div class="flex items-center space-x-2 mb-2">
                @if($item->origin_country)
                    <span class="text-xs font-semibold px-1.5 py-0.5 bg-indigo-600/70 rounded text-white">
                        {{ $item->origin_country }}
                    </span>
                @endif
                
                @if($item->year)
                    <span class="text-xs text-gray-400">{{ $item->year }}</span>
                @endif
                
                @if($item->rating)
                    <div class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        <span class="ml-1 text-xs text-gray-400">{{ $item->rating }}</span>
                    </div>
                @endif
            </div>
            
            <!-- Title -->
            <h3 class="font-semibold text-white truncate">{{ $item->title }}</h3>
            
            <!-- Genres -->
            @if(!empty($item->genres) && count($item->genres) > 0)
                <p class="text-xs text-gray-400 mt-1 truncate">
                    {{ collect($item->genres)->pluck('name')->join(', ') }}
                </p>
            @endif
        </div>
    </div>
</div>