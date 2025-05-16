@extends('layouts.app')

@section('title', $movie->title)
@section('meta_description', Str::limit($movie->overview, 160))

@section('hero')
<div class="relative">
    <!-- Backdrop Image -->
    <div class="absolute inset-0 bg-black">
        @if($movie->backdrop_path)
            <img 
                src="{{ $movie->backdrop_path }}" 
                alt="{{ $movie->title }}" 
                class="w-full h-full object-cover opacity-40"
            >
            <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/70 to-black/60"></div>
        @endif
    </div>
    
    <div class="container mx-auto px-4 py-12 md:py-16 relative z-10">
        <div class="flex flex-col md:flex-row gap-8">
            <!-- Poster -->
            <div class="w-full md:w-1/3 lg:w-1/4 flex-shrink-0">
                <div class="bg-gray-800 rounded-lg overflow-hidden shadow-lg aspect-[2/3] relative group">
                    <img 
                        src="{{ $movie->poster_path ?? asset('images/poster-placeholder.jpg') }}" 
                        alt="{{ $movie->title }}" 
                        class="w-full h-full object-cover"
                    >
                    
                    <!-- Platform Badges (if available) -->
                    @if(!empty($movie->available_on) && count($movie->available_on) > 0)
                        <div class="absolute bottom-0 left-0 right-0 bg-black/80 p-4">
                            <div class="flex flex-wrap gap-3 justify-center">
                                @foreach($movie->available_on as $platform)
                                    <a 
                                        href="{{ $platform->url }}" 
                                        target="_blank" 
                                        class="flex flex-col items-center hover:opacity-80 transition"
                                        title="Ver en {{ $platform->name }}"
                                    >
                                        <img 
                                            src="{{ $platform->logo_url }}" 
                                            alt="{{ $platform->name }}" 
                                            class="h-7 w-auto rounded mb-1"
                                        >
                                        <span class="text-xs text-gray-300">Ver ahora</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
                
                <!-- Action Buttons -->
                <div class="flex mt-4 gap-2">
                    <button 
                        @click="$store.watchlist.toggle('{{ $movie->id }}', 'movie')" 
                        class="flex-1 py-2.5 rounded-lg font-medium text-center transition"
                        x-data
                        x-bind:class="$store.watchlist.exists('{{ $movie->id }}') ? 'bg-gray-700 text-white' : 'bg-gray-800 text-white hover:bg-gray-700'"
                    >
                        <span x-show="!$store.watchlist.exists('{{ $movie->id }}')">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Mi Lista
                        </span>
                        <span x-show="$store.watchlist.exists('{{ $movie->id }}')">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                            En Mi Lista
                        </span>
                    </button>
                    
                    <button 
                        @click="$store.favorites.toggle('{{ $movie->id }}', 'movie')" 
                        class="flex-1 py-2.5 rounded-lg font-medium text-center transition"
                        x-data
                        x-bind:class="$store.favorites.exists('{{ $movie->id }}') ? 'bg-pink-700 text-white' : 'bg-gray-800 text-white hover:bg-gray-700'"
                    >
                        <span x-show="!$store.favorites.exists('{{ $movie->id }}')">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            Favorito
                        </span>
                        <span x-show="$store.favorites.exists('{{ $movie->id }}')">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1.5" fill="currentColor" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                            Favorito
                        </span>
                    </button>
                </div>
                
                <!-- Extra Movie Details -->
                <div class="mt-6 bg-gray-800 rounded-lg p-4 space-y-4">
                    @if($movie->release_date)
                        <div>
                            <h3 class="text-sm font-semibold text-gray-400">Fecha de estreno</h3>
                            <p class="text-white">{{ \Carbon\Carbon::parse($movie->release_date)->format('d M, Y') }}</p>
                        </div>
                    @endif
                    
                    @if($movie->runtime)
                        <div>
                            <h3 class="text-sm font-semibold text-gray-400">Duración</h3>
                            <p class="text-white">{{ floor($movie->runtime / 60) }}h {{ $movie->runtime % 60 }}min</p>
                        </div>
                    @endif
                    
                    @if($movie->original_language)
                        <div>
                            <h3 class="text-sm font-semibold text-gray-400">Idioma original</h3>
                            <p class="text-white">{{ $movie->original_language }}</p>
                        </div>
                    @endif
                    
                    @if($movie->budget)
                        <div>
                            <h3 class="text-sm font-semibold text-gray-400">Presupuesto</h3>
                            <p class="text-white">${{ number_format($movie->budget) }}</p>
                        </div>
                    @endif
                    
                    @if(!empty($movie->keywords) && count($movie->keywords) > 0)
                        <div>
                            <h3 class="text-sm font-semibold text-gray-400">Palabras clave</h3>
                            <div class="flex flex-wrap gap-2 mt-2">
                                @foreach($movie->keywords as $keyword)
                                    <a href="{{ route('catalog.index', ['keyword' => $keyword->slug]) }}" class="text-xs bg-gray-700 hover:bg-gray-600 px-2 py-1 rounded-md text-gray-300 transition">
                                        {{ $keyword->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Movie Details -->
            <div class="w-full md:w-2/3 lg:w-3/4">
                <!-- Header Info -->
                <div class="mb-8">
                    <div class="flex items-center space-x-3 mb-4">
                        @if($movie->origin_country)
                            <span class="text-sm font-semibold px-2.5 py-1 bg-indigo-700 rounded-md text-white">
                                {{ $movie->origin_country }}
                            </span>
                        @endif
                        
                        @if($movie->rating)
                            <div class="flex items-center bg-gray-800 px-3 py-1 rounded-md">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                <span class="ml-1.5 text-white">{{ $movie->rating }} / 10</span>
                            </div>
                        @endif
                        
                        @if($movie->year)
                            <span class="text-gray-400">{{ $movie->year }}</span>
                        @endif
                    </div>
                    
                    <h1 class="text-4xl md:text-5xl font-bold">{{ $movie->title }}</h1>
                    
                    @if($movie->original_title && $movie->original_title !== $movie->title)
                        <h2 class="text-xl text-gray-400 mt-2">{{ $movie->original_title }}</h2>
                    @endif
                    
                    @if(!empty($movie->genres) && count($movie->genres) > 0)
                        <div class="mt-4">
                            @foreach($movie->genres as $genre)
                                <a href="{{ route('catalog.index', ['genre' => $genre->slug]) }}" class="inline-block bg-gray-800 hover:bg-gray-700 text-gray-300 rounded-md px-3 py-1 text-sm mr-2 mb-2 transition">
                                    {{ $genre->name }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
                
                <!-- Tabs Navigation -->
                <div class="border-b border-gray-700 mb-6" x-data="{ activeTab: 'overview' }">
                    <nav class="flex space-x-8">
                        <button 
                            @click="activeTab = 'overview'" 
                            :class="{ 'border-indigo-500 text-white': activeTab === 'overview', 'border-transparent text-gray-400 hover:text-gray-300': activeTab !== 'overview' }"
                            class="py-4 px-1 font-medium text-sm border-b-2 transition focus:outline-none"
                        >
                            Sinopsis
                        </button>
                        
                        <button 
                            @click="activeTab = 'cast'" 
                            :class="{ 'border-indigo-500 text-white': activeTab === 'cast', 'border-transparent text-gray-400 hover:text-gray-300': activeTab !== 'cast' }"
                            class="py-4 px-1 font-medium text-sm border-b-2 transition focus:outline-none"
                        >
                            Reparto y Equipo
                        </button>
                        
                        <button 
                            @click="activeTab = 'media'" 
                            :class="{ 'border-indigo-500 text-white': activeTab === 'media', 'border-transparent text-gray-400 hover:text-gray-300': activeTab !== 'media' }"
                            class="py-4 px-1 font-medium text-sm border-b-2 transition focus:outline-none"
                        >
                            Galería
                        </button>
                        
                        <button 
                            @click="activeTab = 'reviews'" 
                            :class="{ 'border-indigo-500 text-white': activeTab === 'reviews', 'border-transparent text-gray-400 hover:text-gray-300': activeTab !== 'reviews' }"
                            class="py-4 px-1 font-medium text-sm border-b-2 transition focus:outline-none"
                        >
                            Reseñas <span class="bg-gray-700 rounded-full px-2 py-0.5 text-xs">{{ count($movie->reviews ?? []) }}</span>
                        </button>
                    </nav>
                </div>
                
                <!-- Tab Content -->
                <div x-data="{ activeTab: 'overview' }">
                    <!-- Overview Tab -->
                    <div x-show="activeTab === 'overview'">
                        <p class="text-lg leading-relaxed mb-8">{{ $movie->overview }}</p>
                        
                        @if(!empty($movie->tagline))
                            <div class="bg-gray-800 rounded-lg p-4 italic text-gray-300 mb-8">
                                "{{ $movie->tagline }}"
                            </div>
                        @endif
                        @if(!empty($movie->production_companies) && count($movie->production_companies) > 0)
                            <div class="mb-8">
                                <h3 class="text-lg font-semibold mb-3">Compañías de Producción</h3>
                                <div class="flex flex-wrap gap-6">
                                    @foreach($movie->production_companies as $company)
                                        <div class="flex flex-col items-center">
                                            @if($company->logo_path)
                                                <img 
                                                    src="{{ $company->logo_path }}" 
                                                    alt="{{ $company->name }}" 
                                                    class="h-12 object-contain bg-gray-800 p-2 rounded mb-2"
                                                >
                                            @endif
                                            <span class="text-sm text-gray-300">{{ $company->name }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        
                        <!-- Where to Watch Section -->
                        @if(!empty($movie->available_on) && count($movie->available_on) > 0)
                            <div class="bg-gray-800 rounded-lg p-6 mb-8">
                                <h3 class="text-xl font-semibold mb-4">Dónde ver "{{ $movie->title }}"</h3>
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                    @foreach($movie->available_on as $platform)
                                        <a 
                                            href="{{ $platform->url }}" 
                                            target="_blank" 
                                            class="flex flex-col items-center bg-gray-700 hover:bg-gray-600 p-4 rounded-lg transition"
                                        >
                                            <img 
                                                src="{{ $platform->logo_url }}" 
                                                alt="{{ $platform->name }}" 
                                                class="h-10 w-auto object-contain mb-3"
                                            >
                                            <span class="text-sm font-medium text-white">Ver ahora</span>
                                            @if($platform->price)
                                                <span class="text-xs text-gray-300 mt-1">{{ $platform->price }}</span>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="bg-gray-800 rounded-lg p-6 mb-8">
                                <h3 class="text-xl font-semibold mb-2">Dónde ver "{{ $movie->title }}"</h3>
                                <p class="text-gray-400">No hay información de disponibilidad en plataformas en este momento.</p>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Cast Tab -->
                    <div x-show="activeTab === 'cast'">
                        @if(!empty($movie->cast) && count($movie->cast) > 0)
                            <h3 class="text-xl font-semibold mb-4">Reparto Principal</h3>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4 mb-8">
                                @foreach($movie->cast as $person)
                                    <a href="{{ route('people.show', $person->id) }}" class="bg-gray-800 rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition group">
                                        <div class="aspect-[2/3] overflow-hidden">
                                            <img 
                                                src="{{ $person->profile_path ?? asset('images/profile-placeholder.jpg') }}" 
                                                alt="{{ $person->name }}"
                                                class="w-full h-full object-cover transform group-hover:scale-105 transition duration-500"
                                                loading="lazy"
                                            >
                                        </div>
                                        <div class="p-3">
                                            <h4 class="font-semibold text-white">{{ $person->name }}</h4>
                                            @if($person->character)
                                                <p class="text-sm text-gray-400 mt-1">{{ $person->character }}</p>
                                            @endif
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                        
                        @if(!empty($movie->crew) && count($movie->crew) > 0)
                            <h3 class="text-xl font-semibold mb-4">Equipo Destacado</h3>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6">
                                @foreach($movie->crew as $person)
                                    <div class="flex items-center space-x-4">
                                        <a href="{{ route('people.show', $person->id) }}">
                                            <img 
                                                src="{{ $person->profile_path ?? asset('images/profile-placeholder.jpg') }}" 
                                                alt="{{ $person->name }}"
                                                class="w-12 h-12 rounded-full object-cover"
                                            >
                                        </a>
                                        <div>
                                            <a href="{{ route('people.show', $person->id) }}" class="font-medium text-white hover:text-indigo-400 transition">
                                                {{ $person->name }}
                                            </a>
                                            <p class="text-sm text-gray-400">{{ $person->job }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    
                    <!-- Media Tab -->
                    <div x-show="activeTab === 'media'">
                        @if(!empty($movie->images) && count($movie->images) > 0)
                            <div class="mb-8">
                                <h3 class="text-xl font-semibold mb-4">Imágenes</h3>
                                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4" x-data="{ lightbox: false, currentImage: '' }">
                                    @foreach($movie->images as $image)
                                        <div class="aspect-video bg-gray-800 rounded-lg overflow-hidden cursor-pointer" @click="lightbox = true; currentImage = '{{ $image->file_path }}'">
                                            <img 
                                                src="{{ $image->file_path }}" 
                                                alt="{{ $movie->title }} - Imagen"
                                                class="w-full h-full object-cover hover:opacity-75 transition"
                                                loading="lazy"
                                            >
                                        </div>
                                    @endforeach
                                    
                                    <!-- Lightbox -->
                                    <div 
                                        x-show="lightbox" 
                                        @click="lightbox = false"
                                        class="fixed inset-0 bg-black/95 flex items-center justify-center z-50 p-4"
                                        x-transition:enter="transition ease-out duration-300"
                                        x-transition:enter-start="opacity-0"
                                        x-transition:enter-end="opacity-100"
                                        x-transition:leave="transition ease-in duration-200"
                                        x-transition:leave-start="opacity-100"
                                        x-transition:leave-end="opacity-0"
                                    >
                                        <div @click.stop class="relative max-w-4xl max-h-[80vh] overflow-hidden">
                                            <img :src="currentImage" alt="{{ $movie->title }}" class="max-w-full max-h-[80vh] object-contain">
                                            <button @click="lightbox = false" class="absolute top-4 right-4 text-white bg-black/50 hover:bg-black/70 rounded-full p-2 transition">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                        
                        @if(!empty($movie->videos) && count($movie->videos) > 0)
                            <div>
                                <h3 class="text-xl font-semibold mb-4">Videos</h3>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    @foreach($movie->videos as $video)
                                        <div class="bg-gray-800 rounded-lg overflow-hidden">
                                            <div class="aspect-video">
                                                <iframe 
                                                    src="https://www.youtube.com/embed/{{ $video->key }}" 
                                                    frameborder="0" 
                                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                                    allowfullscreen
                                                    class="w-full h-full"
                                                ></iframe>
                                            </div>
                                            <div class="p-4">
                                                <h4 class="font-medium">{{ $video->name }}</h4>
                                                <p class="text-sm text-gray-400 mt-1">{{ $video->type }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        
                        @if(empty($movie->images) && empty($movie->videos))
                            <div class="bg-gray-800 rounded-lg p-6 text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-500 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <h3 class="text-xl font-semibold mb-2">No hay contenido multimedia</h3>
                                <p class="text-gray-400">No hay imágenes o videos disponibles para esta película.</p>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Reviews Tab -->
                    <div x-show="activeTab === 'reviews'">
                        @if(Auth::check())
                            <div class="mb-8">
                                <h3 class="text-xl font-semibold mb-4">Deja tu reseña</h3>
                                <form action="{{ route('movies.review', $movie->id) }}" method="POST">
                                    @csrf
                                    <div class="mb-4">
                                        <label for="rating" class="block text-gray-300 mb-2">Tu puntuación</label>
                                        <div class="flex items-center space-x-1">
                                            @for($i = 1; $i <= 10; $i++)
                                                <label class="cursor-pointer">
                                                    <input type="radio" name="rating" value="{{ $i }}" class="sr-only peer" {{ old('rating') == $i ? 'checked' : '' }}>
                                                    <span class="flex items-center justify-center w-8 h-8 text-sm rounded-full peer-checked:bg-indigo-600 peer-checked:text-white bg-gray-700 hover:bg-gray-600 transition">{{ $i }}</span>
                                                </label>
                                            @endfor
                                        </div>
                                        @error('rating')
                                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label for="comment" class="block text-gray-300 mb-2">Tu comentario</label>
                                        <textarea 
                                            name="comment" 
                                            id="comment" 
                                            rows="4" 
                                            class="w-full bg-gray-700 border border-gray-600 rounded-lg py-2 px-3 text-white focus:outline-none focus:ring focus:ring-indigo-500"
                                            placeholder="Comparte tu opinión sobre esta película..."
                                        >{{ old('comment') }}</textarea>
                                        @error('comment')
                                            <span class="text-red-500 text-sm mt-1">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    
                                    <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition">
                                        Publicar reseña
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="bg-gray-800 rounded-lg p-6 text-center mb-8">
                                <p class="mb-4">Inicia sesión para dejar tu reseña</p>
                                <a href="{{ route('login') }}" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition inline-block">
                                    Iniciar sesión
                                </a>
                            </div>
                        @endif
                        
                        @if(!empty($movie->reviews) && count($movie->reviews) > 0)
                            <div>
                                <h3 class="text-xl font-semibold mb-4">Reseñas de usuarios</h3>
                                <div class="space-y-6">
                                    @foreach($movie->reviews as $review)
                                        <div class="bg-gray-800 rounded-lg p-5">
                                            <div class="flex justify-between mb-4">
                                                <div class="flex items-center">
                                                    <img 
                                                        src="{{ $review->user->profile_photo_url }}" 
                                                        alt="{{ $review->user->name }}" 
                                                        class="h-10 w-10 rounded-full object-cover mr-3"
                                                    >
                                                    <div>
                                                        <h4 class="font-medium">{{ $review->user->name }}</h4>
                                                        <p class="text-sm text-gray-400">{{ $review->created_at->format('d M, Y') }}</p>
                                                    </div>
                                                </div>
                                                <div class="flex items-center bg-gray-700 px-3 py-1 rounded-md">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                    <span class="ml-1.5 text-white">{{ $review->rating }} / 10</span>
                                                </div>
                                            </div>
                                            
                                            <p class="text-gray-300">{{ $review->comment }}</p>
                                            
                                            @if(Auth::check() && Auth::id() === $review->user_id)
                                                <div class="flex justify-end mt-4 space-x-3">
                                                    <button 
                                                        onclick="editReview('{{ $review->id }}')" 
                                                        class="text-sm text-gray-400 hover:text-white transition"
                                                    >
                                                        Editar
                                                    </button>
                                                    
                                                    <form action="{{ route('reviews.destroy', $review->id) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-sm text-red-400 hover:text-red-300 transition">
                                                            Eliminar
                                                        </button>
                                                    </form>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="bg-gray-800 rounded-lg p-6 text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-500 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                </svg>
                                <h3 class="text-xl font-semibold mb-2">Sin reseñas</h3>
                                <p class="text-gray-400 mb-4">Sé el primero en dejar una reseña para esta película.</p>
                                
                                @if(!Auth::check())
                                    <a href="{{ route('login') }}" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition inline-block">
                                        Iniciar sesión para reseñar
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<!-- Similar Movies -->
<section class="mb-12">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold">Películas similares</h2>
        <a href="{{ route('catalog.index', ['similar_to' => $movie->id]) }}" class="text-indigo-400 hover:text-indigo-300 text-sm font-medium">Ver más</a>
    </div>
    
    @if(!empty($similarMovies) && count($similarMovies) > 0)
        <div class="carousel-container">
            <div class="carousel-wrapper">
                @foreach($similarMovies as $item)
                    <x-content-card :item="$item" />
                @endforeach
            </div>
        </div>
    @else
        <div class="bg-gray-800 rounded-lg p-6 text-center">
            <p class="text-gray-400">No hay películas similares disponibles.</p>
        </div>
    @endif
</section>

<!-- From Same Director -->
@if(isset($directorMovies) && count($directorMovies) > 0)
    <section>
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Más de {{ $director->name }}</h2>
            <a href="{{ route('catalog.index', ['director' => $director->id]) }}" class="text-indigo-400 hover:text-indigo-300 text-sm font-medium">Ver más</a>
        </div>
        
        <div class="carousel-container">
            <div class="carousel-wrapper">
                @foreach($directorMovies as $item)
                    <x-content-card :item="$item" />
                @endforeach
            </div>
        </div>
    </section>
@endif
@endsection

@push('scripts')
<script>
    function editReview(reviewId) {
        // Implementar lógica para editar reseña
        console.log('Editar reseña', reviewId);
    }
</script>
@endpush