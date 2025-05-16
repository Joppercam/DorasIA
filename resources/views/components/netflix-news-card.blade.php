@props(['news'])

<div class="netflix-card news-card-container group">
    <a href="{{ route('news.show', $news->slug) }}" class="block h-full">
        <div class="h-full flex flex-col relative overflow-hidden">
            <!-- Featured image banner or gradient -->
            <div class="relative h-56 bg-gradient-to-t from-gray-900 to-gray-800">
                @if($news->people->isNotEmpty() && $news->people->first()->profile_path)
                    <img src="{{ asset($news->people->first()->profile_path) }}" 
                         alt="Imagen destacada" 
                         class="news-card-profile-featured portrait-banner">
                    <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/80 to-transparent"></div>
                @else
                    <div class="absolute inset-0 bg-gradient-to-br from-dorasia-red/20 to-gray-900/95"></div>
                @endif
                
                <!-- Header with date and badge -->
                <div class="absolute top-4 left-4 right-4 flex items-start justify-between">
                    <span class="px-3 py-1 bg-dorasia-red text-white text-xs font-bold rounded-sm uppercase shadow-lg">
                        Noticia
                    </span>
                    <span class="text-white bg-black/60 px-2 py-1 rounded text-sm">
                        {{ $news->published_at->format('d/m/Y') }}
                    </span>
                </div>
                
                <!-- Actor circles overlay on banner -->
                @if($news->people->isNotEmpty())
                <div class="absolute bottom-4 left-4 flex items-center -space-x-3">
                    @foreach($news->people->take(4) as $person)
                    <div class="relative">
                        @if($person->profile_path)
                        <img src="{{ asset($person->profile_path) }}" 
                             alt="{{ $person->name }}" 
                             class="news-card-profile portrait-circle">
                        @else
                        <div class="w-16 h-16 rounded-full bg-gray-700 border-3 border-gray-900 flex items-center justify-center text-white font-bold text-lg shadow-lg">
                            {{ strtoupper(substr($person->name, 0, 1)) }}
                        </div>
                        @endif
                    </div>
                    @endforeach
                    @if($news->people->count() > 4)
                    <div class="w-16 h-16 rounded-full bg-gray-800 border-3 border-gray-900 flex items-center justify-center text-white font-bold text-lg shadow-lg">
                        +{{ $news->people->count() - 4 }}
                    </div>
                    @endif
                </div>
                @endif
            </div>
            
            <!-- Content section -->
            <div class="p-6 flex-grow flex flex-col h-[calc(100%-14rem)]">
                <!-- Title - sin subrayado, solo cambio de color -->
                <h3 class="text-white font-bold text-xl mb-3 line-clamp-2 group-hover:text-dorasia-red transition-colors">
                    {{ $news->title }}
                </h3>
                
                <!-- Content preview - sin subrayado -->
                <p class="text-gray-300 text-sm mb-4 line-clamp-2 leading-relaxed">
                    {{ $news->content }}
                </p>
                
                <!-- Footer mejorado - con espacio garantizado -->
                <div class="mt-auto flex flex-col">
                    <!-- Actor names -->
                    @if($news->people->isNotEmpty())
                    <div class="mb-3">
                        <span class="text-gray-500 text-xs uppercase tracking-wide">Protagonistas:</span>
                        <p class="text-gray-200 text-sm mt-1 line-clamp-2">
                            @foreach($news->people->take(2) as $index => $person)
                                {{ $person->name }}@if(!$loop->last), @endif
                            @endforeach
                            @if($news->people->count() > 2)
                                y {{ $news->people->count() - 2 }} más
                            @endif
                        </p>
                    </div>
                    @endif
                    
                    <!-- Source and action -->
                    <div class="flex items-center justify-between pt-3 border-t border-gray-800">
                        <span class="text-gray-500 text-xs italic">
                            {{ $news->source_name ?? 'Dorasia News' }}
                        </span>
                        <span class="text-dorasia-red text-sm font-medium group-hover:text-red-400 flex items-center transition-colors">
                            Leer noticia
                            <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                            </svg>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </a>
</div>