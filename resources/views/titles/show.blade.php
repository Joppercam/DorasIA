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
                    
                    <p class="text-gray-300 mb-6">{{ $title->synopsis }}</p>
                    
                    <!-- Botones de acción -->
                    <div class="flex flex-wrap gap-3">
                        @if($title->type === 'movie')
                            <a href="{{ route('titles.watch', $title->slug) }}" class="inline-flex items-center px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-md">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path>
                                </svg>
                                Ver ahora
                            </a>
                        @elseif($title->seasons->count() > 0 && $title->seasons->first()->episodes->count() > 0)
                            <a href="{{ route('titles.watch', [$title->slug, $title->seasons->first()->number, $title->seasons->first()->episodes->first()->number]) }}" class="inline-flex items-center px-5 py-2.5 bg-red-600 hover:bg-red-700 text-white font-medium rounded-md">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path>
                                </svg>
                                Ver primer episodio
                            </a>
                        @endif
                        
                        @auth
                            <button
                                type="button"
                                class="watchlist-toggle inline-flex items-center px-4 py-2.5 bg-gray-800 hover:bg-gray-700 text-white font-medium rounded-md"
                                data-title-id="{{ $title->id }}"
                                onclick="toggleWatchlist({{ $title->id }}, this)">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    @if($watchStatus)
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    @else
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    @endif
                                </svg>
                                Mi Lista
                            </button>
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
                                    class="px-4 py-2 rounded-md text-sm font-medium whitespace-nowrap">
                                    Temporada {{ $season->number }}
                                </button>
                            @endforeach
                        </div>
                        
                        <!-- Listado de episodios -->
                        @foreach($title->seasons as $season)
                            <div x-show="activeTab === {{ $season->number }}" class="space-y-4">
                                <h3 class="text-xl font-semibold mb-4">Temporada {{ $season->number }}: {{ $season->title }}</h3>
                                
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
                                                    <h4 class="font-semibold mb-1">{{ $episode->number }}. {{ $episode->title }}</h4>
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
                
                <!-- Sección de comentarios -->
                <div class="mb-8">
                    <h2 class="text-2xl font-bold mb-6">Comentarios</h2>
                    
                    @auth
                        <div class="bg-gray-900 rounded-lg p-4 mb-6">
                            <form id="comment-form" action="{{ route('comments.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="commentable_type" value="App\Models\Title">
                                <input type="hidden" name="commentable_id" value="{{ $title->id }}">
                                
                                <textarea 
                                    name="content" 
                                    rows="3" 
                                    class="w-full bg-gray-800 border border-gray-700 rounded-lg p-3 text-white focus:outline-none focus:ring-1 focus:ring-red-500 resize-none"
                                    placeholder="Escribe un comentario..."></textarea>
                                
                                <div class="mt-2 flex justify-end">
                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm">
                                        Publicar
                                    </button>
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="bg-gray-900 rounded-lg p-4 mb-6 text-center">
                            <p class="text-gray-400 mb-2">Para comentar, debes iniciar sesión.</p>
                            <a href="{{ route('login') }}" class="text-red-600 hover:text-red-500 font-medium">Iniciar sesión</a>
                        </div>
                    @endauth
                    
                    <!-- Lista de comentarios -->
                    <div class="space-y-4" id="comments-container">
                        @forelse($title->comments->where('parent_id', null)->sortByDesc('created_at') as $comment)
                            <div class="bg-gray-900 rounded-lg p-4" id="comment-{{ $comment->id }}">
                                <div class="flex items-start gap-3">
                                    <div class="h-10 w-10 rounded-full overflow-hidden bg-gray-800 flex-shrink-0">
                                        <img src="{{ asset('images/profiles/' . ($comment->profile->avatar ?? 'default.jpg')) }}" alt="{{ $comment->profile->name }}" class="h-full w-full object-cover">
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex justify-between items-center mb-1">
                                            <span class="font-medium">{{ $comment->profile->name }}</span>
                                            <span class="text-xs text-gray-400">{{ $comment->created_at->diffForHumans() }}</span>
                                        </div>
                                        <p class="text-sm text-gray-300">{{ $comment->content }}</p>
                                        
                                        <!-- Acciones -->
                                        <div class="mt-2 flex items-center gap-4 text-sm text-gray-400">
                                            @auth
                                                <button onclick="toggleReplyForm({{ $comment->id }})" class="hover:text-white">Responder</button>
                                                
                                                @if(auth()->user()->getActiveProfile() && $comment->profile_id === auth()->user()->getActiveProfile()->id)
                                                    <button onclick="toggleEditForm({{ $comment->id }})" class="hover:text-white">Editar</button>
                                                    
                                                    <form action="{{ route('comments.destroy', $comment) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="hover:text-white" onclick="return confirm('¿Estás seguro de que quieres eliminar este comentario?')">Eliminar</button>
                                                    </form>
                                                @endif
                                            @endauth
                                        </div>
                                        
                                        <!-- Formulario de respuesta (oculto por defecto) -->
                                        @auth
                                            <div id="reply-form-{{ $comment->id }}" class="mt-3 hidden">
                                                <form action="{{ route('comments.reply', $comment) }}" method="POST">
                                                    @csrf
                                                    <textarea 
                                                        name="content" 
                                                        rows="2" 
                                                        class="w-full bg-gray-800 border border-gray-700 rounded-lg p-2 text-white text-sm focus:outline-none focus:ring-1 focus:ring-red-500 resize-none"
                                                        placeholder="Escribe una respuesta..."></textarea>
                                                    
                                                    <div class="mt-2 flex justify-end space-x-2">
                                                        <button type="button" onclick="toggleReplyForm({{ $comment->id }})" class="text-gray-400 hover:text-white text-sm">Cancelar</button>
                                                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm">Responder</button>
                                                    </div>
                                                </form>
                                            </div>
                                        @endauth
                                        
                                        <!-- Formulario de edición (oculto por defecto) -->
                                        @auth
                                            @if(auth()->user()->getActiveProfile() && $comment->profile_id === auth()->user()->getActiveProfile()->id)
                                                <div id="edit-form-{{ $comment->id }}" class="mt-3 hidden">
                                                    <form action="{{ route('comments.update', $comment) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <textarea 
                                                            name="content" 
                                                            rows="2" 
                                                            class="w-full bg-gray-800 border border-gray-700 rounded-lg p-2 text-white text-sm focus:outline-none focus:ring-1 focus:ring-red-500 resize-none">{{ $comment->content }}</textarea>
                                                        
                                                        <div class="mt-2 flex justify-end space-x-2">
                                                            <button type="button" onclick="toggleEditForm({{ $comment->id }})" class="text-gray-400 hover:text-white text-sm">Cancelar</button>
                                                            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm">Guardar</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            @endif
                                        @endauth
                                        
                                        <!-- Respuestas al comentario -->
                                        @if($comment->replies->count() > 0)
                                            <div class="mt-4 space-y-3">
                                                @foreach($comment->replies as $reply)
                                                    <div class="bg-gray-800 rounded-lg p-3" id="comment-{{ $reply->id }}">
                                                        <div class="flex items-start gap-2">
                                                            <div class="h-8 w-8 rounded-full overflow-hidden bg-gray-800 flex-shrink-0">
                                                                <img src="{{ asset('images/profiles/' . ($reply->profile->avatar ?? 'default.jpg')) }}" alt="{{ $reply->profile->name }}" class="h-full w-full object-cover">
                                                            </div>
                                                            <div class="flex-1">
                                                                <div class="flex justify-between items-center mb-1">
                                                                    <span class="font-medium text-sm">{{ $reply->profile->name }}</span>
                                                                    <span class="text-xs text-gray-400">{{ $reply->created_at->diffForHumans() }}</span>
                                                                </div>
                                                                <p class="text-sm text-gray-300">{{ $reply->content }}</p>
                                                                
                                                                <!-- Acciones para respuestas -->
                                                                @auth
                                                                    @if(auth()->user()->getActiveProfile() && $reply->profile_id === auth()->user()->getActiveProfile()->id)
                                                                        <div class="mt-1 flex items-center gap-4 text-xs text-gray-400">
                                                                            <button onclick="toggleEditForm({{ $reply->id }})" class="hover:text-white">Editar</button>
                                                                            
                                                                            <form action="{{ route('comments.destroy', $reply) }}" method="POST" class="inline">
                                                                                @csrf
                                                                                @method('DELETE')
                                                                                <button type="submit" class="hover:text-white" onclick="return confirm('¿Estás seguro de que quieres eliminar esta respuesta?')">Eliminar</button>
                                                                            </form>
                                                                        </div>
                                                                        
                                                                        <!-- Formulario de edición para respuestas -->
                                                                        <div id="edit-form-{{ $reply->id }}" class="mt-2 hidden">
                                                                            <form action="{{ route('comments.update', $reply) }}" method="POST">
                                                                                @csrf
                                                                                @method('PUT')
                                                                                <textarea 
                                                                                    name="content" 
                                                                                    rows="2" 
                                                                                    class="w-full bg-gray-700 border border-gray-600 rounded-lg p-2 text-white text-sm focus:outline-none focus:ring-1 focus:ring-red-500 resize-none">{{ $reply->content }}</textarea>
                                                                                
                                                                                <div class="mt-1 flex justify-end space-x-2">
                                                                                    <button type="button" onclick="toggleEditForm({{ $reply->id }})" class="text-gray-400 hover:text-white text-xs">Cancelar</button>
                                                                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-2 py-1 rounded text-xs">Guardar</button>
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    @endif
                                                                @endauth
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-gray-400 py-8">
                                <p>Aún no hay comentarios. ¡Sé el primero en comentar!</p>
                            </div>
                        @endforelse
                    </div>
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
                    <div class="bg-gray-900 rounded-lg p-4">
                        @auth
                            <div class="mb-4">
                                <h3 class="text-lg font-semibold mb-2">Tu valoración</h3>
                                <form id="rating-form" action="{{ route('ratings.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="title_id" value="{{ $title->id }}">
                                    
                                    <div class="flex items-center mb-3" x-data="{ rating: {{ $userRating ? $userRating->rating : 0 }} }">
                                        @for($i = 1; $i <= 10; $i++)
                                            <button 
                                                type="button"
                                                @click="rating = {{ $i }}; document.getElementById('rating-value').value = {{ $i }}"
                                                :class="rating >= {{ $i }} ? 'text-red-500' : 'text-gray-600'"
                                                class="text-2xl hover:text-red-500 focus:outline-none">
                                                ★
                                            </button>
                                        @endfor
                                        <input type="hidden" id="rating-value" name="rating" :value="rating">
                                        <span class="ml-2 text-lg" x-text="rating ? rating + '/10' : ''"></span>
                                    </div>
                                    
                                    <textarea 
                                        name="review" 
                                        rows="2" 
                                        class="w-full bg-gray-800 border border-gray-700 rounded-lg p-3 text-white text-sm focus:outline-none focus:ring-1 focus:ring-red-500 resize-none"
                                        placeholder="Escribe una reseña (opcional)">{{ $userRating ? $userRating->review : '' }}</textarea>
                                    
                                    <div class="mt-2 flex justify-end">
                                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm">
                                            {{ $userRating ? 'Actualizar' : 'Enviar' }}
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @else
                            <div class="mb-4 text-center">
                                <p class="text-gray-400 mb-2">Para valorar, debes iniciar sesión.</p>
                                <a href="{{ route('login') }}" class="text-red-600 hover:text-red-500 font-medium">Iniciar sesión</a>
                            </div>
                        @endauth
                        
                        <!-- Valoración promedio -->
                        <div class="flex items-center justify-between py-3 border-t border-gray-800">
                            <div>
                                <span class="text-xl font-bold">{{ number_format($title->ratings->avg('rating') ?? 0, 1) }}</span>
                                <span class="text-gray-400 text-sm">/10</span>
                            </div>
                            <div class="text-gray-400 text-sm">
                                {{ $title->ratings->count() }} valoraciones
                            </div>
                        </div>
                    </div>
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
        
        // Funciones para mi lista
        function toggleWatchlist(titleId, button) {
            fetch('{{ route('watchlist.toggle') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    title_id: titleId
                })
            })
            .then(response => response.json())
            .then(data => {
                // Actualizar la UI según el resultado
                if (data.status === 'added') {
                    button.querySelector('svg').innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>';
                } else {
                    button.querySelector('svg').innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
        
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