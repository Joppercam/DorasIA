<x-app-layout>
    <x-slot name="title">{{ $metaTitle ?? $title->title }}</x-slot>
    <x-slot name="metaDescription">{{ $metaDescription }}</x-slot>
    <x-slot name="metaImage">{{ $metaImage }}</x-slot>
    <x-slot name="pageClass">titles-detail-page</x-slot>
    <x-slot name="transitionType">slide-up</x-slot>
    
    <!-- Hero con backdrop -->
    <div class="relative h-[500px] md:h-[80vh] bg-gradient-to-b from-transparent to-dorasia-bg-dark overflow-hidden">
        <!-- Imagen de fondo -->
        <div class="absolute inset-0 z-0">
            @if(!empty($title->backdrop))
                <img src="{{ asset($title->backdrop) }}" alt="{{ $title->title }}" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='{{ asset('backdrops/placeholder.jpg') }}'">
            @else
                <div class="w-full h-full bg-gradient-to-r from-gray-900 to-black flex items-center justify-center">
                    <span class="text-gray-600 text-xl">Sin imagen de fondo</span>
                </div>
            @endif
            
            <!-- Gradiente encima de la imagen -->
            <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/50 to-transparent"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-dorasia-bg-dark via-transparent to-transparent"></div>
        </div>
        
        <!-- Contenido -->
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full flex items-center">
            <div class="flex flex-col md:flex-row gap-8">
                <!-- Poster -->
                <div class="hidden md:block shrink-0 w-[220px] h-[330px] rounded-lg overflow-hidden shadow-xl bg-gray-800">
                    @if(!empty($title->poster))
                        <img src="{{ asset($title->poster) }}" alt="{{ $title->title }}" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='{{ asset('posters/placeholder.jpg') }}'">
                    @else
                        <div class="w-full h-full bg-gray-800 flex items-center justify-center">
                            <span class="text-gray-600">Sin poster</span>
                        </div>
                    @endif
                </div>
                
                <!-- Información -->
                <div class="max-w-2xl">
                    <h1 class="text-3xl sm:text-4xl md:text-5xl font-bold mb-2">{{ $title->title }}</h1>
                    
                    @if($title->original_title && $title->original_title !== $title->title)
                        <p class="text-gray-400 text-lg mb-2">{{ $title->original_title }}</p>
                    @endif
                    
                    <div class="flex flex-wrap items-center gap-3 mb-4">
                        <span class="text-sm font-medium bg-red-600 px-2 py-0.5 rounded">{{ $title->type === 'movie' ? 'Película' : 'Serie' }}</span>
                        <span class="text-sm">{{ $title->release_year }}</span>
                        @if($title->type === 'movie' && $title->duration)
                            <span class="text-sm">{{ $title->duration }} min</span>
                        @elseif($title->type === 'series' && $title->seasons->count() > 0)
                            <span class="text-sm">{{ $title->seasons->count() }} temporada(s)</span>
                        @endif
                        <span class="text-sm">{{ $title->country }}</span>
                    </div>
                    
                    <!-- Géneros -->
                    <div class="flex flex-wrap gap-2 mb-4">
                        @foreach($title->genres as $genre)
                            <a href="{{ route('catalog.genre', $genre->slug) }}" class="text-xs bg-gray-800 hover:bg-gray-700 px-2 py-1 rounded-full text-gray-300">{{ $genre->name }}</a>
                        @endforeach
                    </div>
                    
                    <!-- Valoración -->
                    <div class="mb-4">
                        <x-rating-stars :title-id="$title->id" :show-count="true" size="lg" />
                    </div>
                    
                    <!-- Sinopsis / Resumen -->
                    @if($title->synopsis)
                        <div class="mb-6 bg-gray-900/50 rounded-lg p-4">
                            <h3 class="text-lg font-semibold mb-3 text-white">Sinopsis</h3>
                            <p class="text-gray-300 leading-relaxed text-base">
                                {{ $title->synopsis }}
                            </p>
                        </div>
                    @else
                        <div class="mb-6 bg-gray-900/50 rounded-lg p-4">
                            <p class="text-gray-500 italic">No hay sinopsis disponible para este título.</p>
                        </div>
                    @endif
                    
                    <!-- Botones de acción -->
                    <div class="flex flex-wrap gap-3">
                        
                        @auth
                            @if(auth()->user()->getActiveProfile())
                                <form action="{{ route('watchlist.toggle') }}" method="POST" class="inline-block" id="watchlist-form-{{ $title->id }}">
                                    @csrf
                                    <input type="hidden" name="title_id" value="{{ $title->id }}">
                                    <button
                                        type="submit"
                                        class="watchlist-toggle inline-flex items-center px-4 py-2.5 font-medium rounded-md transition-colors
                                            @if($watchStatus)
                                                bg-red-600 hover:bg-red-700 text-white
                                            @else
                                                bg-gray-800 hover:bg-gray-700 text-white
                                            @endif">
                                        <svg class="w-5 h-5 mr-2" fill="@if($watchStatus) currentColor @else none @endif" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            @if($watchStatus)
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                            @endif
                                        </svg>
                                        @if($watchStatus)
                                            En mi lista
                                        @else
                                            Agregar a mi lista
                                        @endif
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('user-profiles.selector') }}" 
                                   class="inline-flex items-center px-4 py-2.5 bg-gray-800 hover:bg-gray-700 text-white font-medium rounded-md">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    Crear Perfil
                                </a>
                            @endif
                        @endauth
                        
                        @if($title->trailer_url)
                            <button
                                type="button"
                                class="inline-flex items-center px-4 py-2.5 bg-gray-800 hover:bg-gray-700 text-white font-medium rounded-md"
                                onclick="showTrailer('{{ $title->trailer_url }}')">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Ver trailer
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Contenido principal -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Sección de resumen y críticas profesionales -->
        <div class="mb-10 bg-gray-900 rounded-xl shadow-lg overflow-hidden">
            <div class="p-6 md:p-8">
                <h2 class="text-2xl font-bold mb-4 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    Acerca de {{ $title->title }}
                </h2>
                
                <div class="prose prose-invert max-w-none text-gray-300">
                    <!-- Descripción detallada -->
                    <p class="text-lg">{{ $title->synopsis }}</p>
                    
                    <!-- Detalles adicionales en forma de lista -->
                    <div class="mt-6 grid md:grid-cols-2 gap-4">
                        <div>
                            <h3 class="text-xl font-semibold mb-2">Detalles</h3>
                            <ul class="space-y-2">
                                <li><span class="text-gray-400">Director:</span> {{ $title->directors->implode('name', ', ') ?: 'No disponible' }}</li>
                                <li><span class="text-gray-400">País:</span> {{ $title->country }}</li>
                                <li><span class="text-gray-400">Año:</span> {{ $title->release_year }}</li>
                                @if($title->type === 'movie')
                                    <li><span class="text-gray-400">Duración:</span> {{ $title->duration }} minutos</li>
                                @else
                                    <li><span class="text-gray-400">Temporadas:</span> {{ $title->seasons->count() }}</li>
                                    <li><span class="text-gray-400">Episodios totales:</span> {{ $title->episodes_count }}</li>
                                @endif
                                <li><span class="text-gray-400">Géneros:</span> {{ $title->genres->implode('name', ', ') }}</li>
                            </ul>
                        </div>
                        
                        <div>
                            <h3 class="text-xl font-semibold mb-2">Valoración crítica</h3>
                            <div class="flex items-center gap-2 mb-3">
                                <div class="flex">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= round($title->vote_average / 2))
                                            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        @else
                                            <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        @endif
                                    @endfor
                                </div>
                                <span class="text-lg font-semibold">{{ number_format($title->vote_average, 1) }}/10</span>
                                <span class="text-sm text-gray-400">({{ $title->vote_count }} votos)</span>
                            </div>
                            
                            <!-- Críticas profesionales -->
                            @if($title->professionalReviews->count() > 0)
                                <div class="space-y-3">
                                    @foreach($title->professionalReviews as $review)
                                        <div class="border-l-4 @if($review->rating >= 8) border-red-500 @elseif($review->rating >= 6) border-blue-500 @else border-gray-500 @endif pl-4">
                                            <div class="italic text-gray-300">"{{ $review->content }}"</div>
                                            <div class="mt-2 flex items-center justify-between">
                                                <div class="text-sm text-gray-400">
                                                    - {{ $review->reviewer_name }}, {{ $review->reviewer_source }}
                                                </div>
                                                @if($review->rating)
                                                    <div class="flex items-center gap-1">
                                                        <span class="text-sm font-medium">{{ number_format($review->rating, 1) }}/10</span>
                                                        <div class="flex">
                                                            @for($i = 1; $i <= 5; $i++)
                                                                @if($i <= round($review->rating / 2))
                                                                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                                    </svg>
                                                                @else
                                                                    <svg class="w-4 h-4 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                                    </svg>
                                                                @endif
                                                            @endfor
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <!-- Críticas por defecto si no hay críticas reales -->
                                <div class="space-y-3">
                                    <div class="border-l-4 border-red-500 pl-4 italic text-gray-300">
                                        "{{ $title->title }} es una obra maestra del {{ $title->type === 'movie' ? 'cine' : 'drama' }} asiático, que combina perfectamente elementos dramáticos y culturales."
                                        <div class="text-sm text-gray-400 mt-1">- CinePanorama</div>
                                    </div>
                                    
                                    <div class="border-l-4 border-blue-500 pl-4 italic text-gray-300">
                                        "Una narrativa cautivadora que muestra lo mejor de la cultura {{ strtolower($title->country) }}. Imprescindible para los amantes del género."
                                        <div class="text-sm text-gray-400 mt-1">- Asian Drama Reviews</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Plataformas de streaming -->
                @if(!empty($title->streaming_platforms))
                <div class="mt-6">
                    <h3 class="text-xl font-semibold mb-3">Disponible en</h3>
                    <div class="flex flex-wrap gap-3">
                        @php
                            $platforms = is_string($title->streaming_platforms) ? json_decode($title->streaming_platforms) : $title->streaming_platforms;
                        @endphp
                        @foreach($platforms as $platform)
                            <div class="flex items-center px-3 py-2 bg-gray-800 rounded-lg">
                                @if($platform == 'netflix')
                                    <div class="w-8 h-8 bg-red-600 rounded flex items-center justify-center mr-2 text-white font-bold">N</div>
                                @elseif($platform == 'disney')
                                    <div class="w-8 h-8 bg-blue-600 rounded flex items-center justify-center mr-2 text-white font-bold">D+</div>
                                @elseif($platform == 'prime')
                                    <div class="w-8 h-8 bg-blue-400 rounded flex items-center justify-center mr-2 text-white font-bold">P</div>
                                @elseif($platform == 'hbo')
                                    <div class="w-8 h-8 bg-purple-800 rounded flex items-center justify-center mr-2 text-white font-bold">HBO</div>
                                @elseif($platform == 'apple')
                                    <div class="w-8 h-8 bg-gray-700 rounded flex items-center justify-center mr-2 text-white font-bold">TV+</div>
                                @elseif($platform == 'viki')
                                    <div class="w-8 h-8 bg-green-600 rounded flex items-center justify-center mr-2 text-white font-bold">V</div>
                                @elseif($platform == 'crunchyroll')
                                    <div class="w-8 h-8 bg-orange-500 rounded flex items-center justify-center mr-2 text-white font-bold">CR</div>
                                @else
                                    <div class="w-8 h-8 bg-gray-600 rounded flex items-center justify-center mr-2 text-white font-bold">{{ strtoupper(substr($platform, 0, 1)) }}</div>
                                @endif
                                <span class="text-white">{{ ucfirst($platform) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            <!-- Columna principal -->
            <div class="lg:col-span-8">
                
                <!-- Temporadas y episodios (para series) -->
                @if($title->type === 'series' && $title->seasons->count() > 0)
                    <div class="mb-12" x-data="{ activeTab: {{ $title->seasons->first()->number }} }">
                        <h2 class="text-2xl font-bold mb-6">Episodios</h2>
                        
                        <!-- Tabs de temporadas -->
                        <div class="flex overflow-x-auto space-x-2 mb-6 pb-2">
                            @foreach($title->seasons as $season)
                                <button 
                                    @click="activeTab = {{ $season->number }}" 
                                    :class="activeTab === {{ $season->number }} ? 'bg-red-600 text-white' : 'bg-gray-800 text-gray-300 hover:bg-gray-700'"
                                    class="px-4 py-2 rounded-md text-sm font-medium whitespace-nowrap"
                                    title="{{ $season->name ?? 'Temporada ' . $season->number }}">
                                    <span>T{{ $season->number }}</span>
                                    @if($season->name)
                                        <span class="ml-1 text-xs">{{ Str::limit($season->name, 20) }}</span>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                        
                        <!-- Listado de episodios -->
                        @foreach($title->seasons as $season)
                            <div x-show="activeTab === {{ $season->number }}" class="space-y-4">
                                <!-- Información de la temporada -->
                                <div class="mb-6 bg-gray-900/50 rounded-lg p-4">
                                    <div class="flex gap-6">
                                        @if($season->poster)
                                            <div class="w-32 flex-shrink-0">
                                                <img src="{{ asset($season->poster) }}" 
                                                     alt="Póster Temporada {{ $season->number }}"
                                                     class="w-full rounded-lg shadow-lg"
                                                     onerror="this.style.display='none'">
                                            </div>
                                        @endif
                                        
                                        <div class="flex-1">
                                            <h3 class="text-xl font-semibold mb-2">
                                                Temporada {{ $season->number }}{{ $season->name ? ': ' . $season->name : '' }}
                                            </h3>
                                            @if($season->overview)
                                                <p class="text-gray-400 text-sm mb-3">{{ $season->overview }}</p>
                                            @endif
                                            <div class="flex flex-wrap gap-4 text-sm">
                                                @if($season->air_date)
                                                    <div>
                                                        <span class="text-gray-600">Fecha de estreno:</span> 
                                                        <span class="text-gray-400">{{ \Carbon\Carbon::parse($season->air_date)->format('d/m/Y') }}</span>
                                                    </div>
                                                @endif
                                                <div>
                                                    <span class="text-gray-600">Episodios:</span> 
                                                    <span class="text-gray-400">{{ $season->episodes->count() }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                @foreach($season->episodes->sortBy('number') as $episode)
                                    <div class="bg-gray-900 rounded-lg p-4 hover:bg-gray-800 transition duration-200">
                                        <a href="{{ route('titles.watch', [$title->slug, $season->number, $episode->number]) }}" class="flex items-start gap-4">
                                            <div class="w-32 h-20 flex-shrink-0 rounded overflow-hidden bg-gray-800">
                                                @if(!empty($episode->thumbnail))
                                                    <img src="{{ asset($episode->thumbnail) }}" alt="Episodio {{ $episode->number }}" class="w-full h-full object-cover" onerror="this.onerror=null; this.src='{{ asset('backdrops/placeholder.jpg') }}'">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center">
                                                        <span class="text-3xl">{{ $episode->number }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex justify-between">
                                                    <h4 class="font-semibold mb-1">{{ $episode->number }}. {{ $episode->name ?? 'Episodio '.$episode->number }}</h4>
                                                    <span class="text-sm text-gray-400">{{ $episode->duration }} min</span>
                                                </div>
                                                <p class="text-sm text-gray-400 line-clamp-2">{{ $episode->synopsis }}</p>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                @endif
                
                <!-- Sistema de comentarios mejorado -->
                <div class="mb-8">
                    <x-enhanced-comments :title="$title" />
                </div>
            </div>
            
            <!-- Columna lateral -->
            <div class="lg:col-span-4">
                <!-- Reparto -->
                @if($title->actors->count() > 0)
                    <div class="mb-8">
                        <h2 class="text-xl font-bold mb-4">Reparto</h2>
                        <div class="bg-gray-900 rounded-lg overflow-hidden">
                            <div class="divide-y divide-gray-800">
                                @foreach($title->actors as $actor)
                                    <div class="flex items-center p-3 hover:bg-gray-800">
                                        <div class="h-12 w-12 rounded-full overflow-hidden bg-gray-800 mr-3">
                                            @if(!empty($actor->photo))
                                                <img src="{{ asset($actor->photo) }}" alt="{{ $actor->name }}" class="h-full w-full object-cover" onerror="this.onerror=null; this.src='{{ asset('images/profiles/default.jpg') }}'">
                                            @else
                                                <div class="h-full w-full flex items-center justify-center bg-gray-700 text-gray-500">
                                                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="font-medium">{{ $actor->name }}</p>
                                            @if($actor->pivot->character)
                                                <p class="text-sm text-gray-400">{{ $actor->pivot->character }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
                
                <!-- Valoraciones -->
                <div class="mb-8">
                    <h2 class="text-xl font-bold mb-4">Valoraciones</h2>
                    <x-rating-statistics :title-id="$title->id" />
                </div>
                
                <!-- Títulos similares -->
                @if($similarTitles->count() > 0)
                    <div class="mb-8">
                        <h2 class="text-xl font-bold mb-4">Títulos similares</h2>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach($similarTitles as $similarTitle)
                                <a href="{{ route('titles.show', $similarTitle->slug) }}" class="dorasia-card">
                                    <div class="relative pb-[150%] rounded overflow-hidden shadow-lg mb-2 bg-gray-800">
                                        @if(!empty($similarTitle->poster))
                                            <img src="{{ asset($similarTitle->poster) }}" alt="{{ $similarTitle->title }}" 
                                                 class="absolute inset-0 h-full w-full object-cover"
                                                 onerror="this.onerror=null; this.src='{{ asset('posters/placeholder.jpg') }}'">
                                        @else
                                            <div class="absolute inset-0 flex items-center justify-center bg-gray-900 text-gray-600">
                                                <span>Sin imagen</span>
                                            </div>
                                        @endif
                                    </div>
                                    <h3 class="text-sm truncate">{{ $similarTitle->title }}</h3>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Modal de Trailer -->
    <div id="trailer-modal" class="fixed inset-0 bg-black/90 z-50 flex items-center justify-center hidden">
        <div class="relative w-full max-w-3xl mx-4">
            <button 
                onclick="hideTrailer()"
                class="absolute -top-10 right-0 text-white hover:text-gray-300">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            
            <div class="relative pb-[56.25%] h-0 overflow-hidden rounded-lg">
                <iframe id="trailer-iframe" class="absolute top-0 left-0 w-full h-full" src="" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
            </div>
        </div>
    </div>
    
    @push('scripts')
    <script>
        // Funciones para comentarios
        function toggleReplyForm(commentId) {
            const replyForm = document.getElementById(`reply-form-${commentId}`);
            replyForm.classList.toggle('hidden');
        }
        
        function toggleEditForm(commentId) {
            const editForm = document.getElementById(`edit-form-${commentId}`);
            editForm.classList.toggle('hidden');
        }
        
        // Opcional: Añadir comportamiento AJAX al formulario de watchlist
        document.addEventListener('DOMContentLoaded', function() {
            const watchlistForm = document.querySelector('[id^="watchlist-form-"]');
            if (watchlistForm) {
                watchlistForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const button = this.querySelector('button');
                    const svg = button.querySelector('svg');
                    const formData = new FormData(this);
                    
                    fetch(this.action, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': formData.get('_token'),
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin',
                        body: JSON.stringify({
                            title_id: formData.get('title_id')
                        })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Actualizar la UI según el resultado
                        if (data.status === 'added') {
                            // Cambiar a estado "En mi lista"
                            button.classList.remove('bg-gray-800', 'hover:bg-gray-700');
                            button.classList.add('bg-red-600', 'hover:bg-red-700');
                            svg.setAttribute('fill', 'currentColor');
                            svg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>';
                            
                            // Actualizar el texto del botón
                            const textNode = Array.from(button.childNodes).find(node => node.nodeType === Node.TEXT_NODE);
                            if (textNode) {
                                textNode.textContent = 'En mi lista';
                            } else {
                                button.insertAdjacentText('beforeend', 'En mi lista');
                            }
                        } else {
                            // Cambiar a estado "Agregar a mi lista"
                            button.classList.remove('bg-red-600', 'hover:bg-red-700');
                            button.classList.add('bg-gray-800', 'hover:bg-gray-700');
                            svg.setAttribute('fill', 'none');
                            svg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>';
                            
                            // Actualizar el texto del botón
                            const textNode = Array.from(button.childNodes).find(node => node.nodeType === Node.TEXT_NODE);
                            if (textNode) {
                                textNode.textContent = 'Agregar a mi lista';
                            } else {
                                button.insertAdjacentText('beforeend', 'Agregar a mi lista');
                            }
                        }
                        
                        // Mostrar mensaje de confirmación
                        if (data.message) {
                            // Podrías usar una notificación más elegante aquí
                            console.log(data.message);
                            
                            // Opcional: mostrar un toast o notificación temporal
                            const toast = document.createElement('div');
                            toast.className = 'fixed bottom-4 right-4 bg-gray-900 text-white px-4 py-2 rounded-lg shadow-lg z-50';
                            toast.textContent = data.message;
                            document.body.appendChild(toast);
                            
                            setTimeout(() => {
                                toast.remove();
                            }, 3000);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        // Si hay error, enviar el formulario normalmente
                        this.submit();
                    });
                });
            }
        });
        
        // Funciones para el trailer
        function showTrailer(url) {
            // Convertir URL de YouTube si es necesario
            if (url.includes('youtube.com/watch')) {
                const videoId = url.split('v=')[1].split('&')[0];
                url = `https://www.youtube.com/embed/${videoId}?autoplay=1`;
            } else if (url.includes('youtu.be')) {
                const videoId = url.split('youtu.be/')[1];
                url = `https://www.youtube.com/embed/${videoId}?autoplay=1`;
            }
            
            const iframe = document.getElementById('trailer-iframe');
            iframe.src = url;
            
            const modal = document.getElementById('trailer-modal');
            modal.classList.remove('hidden');
            
            // Deshabilitar el scroll en el body
            document.body.style.overflow = 'hidden';
        }
        
        function hideTrailer() {
            const iframe = document.getElementById('trailer-iframe');
            iframe.src = '';
            
            const modal = document.getElementById('trailer-modal');
            modal.classList.add('hidden');
            
            // Habilitar el scroll en el body
            document.body.style.overflow = 'auto';
        }
        
        // Cerrar el modal al hacer clic fuera del contenido
        document.getElementById('trailer-modal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideTrailer();
            }
        });
    </script>
    @endpush
</x-app-layout>