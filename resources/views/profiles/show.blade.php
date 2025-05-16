<x-app-layout>
    <x-slot name="title">{{ $profile->name }} - Perfil</x-slot>
    
    <!-- Encabezado del perfil -->
    <div class="relative bg-gradient-to-b from-gray-900 to-black">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
                <!-- Avatar -->
                <div class="relative">
                    <img src="{{ $profile->avatar_url ?? asset('images/profiles/default.jpg') }}" 
                         alt="{{ $profile->name }}"
                         class="w-32 h-32 rounded-full object-cover border-4 border-gray-800">
                    @if($profile->id === auth()->user()?->getActiveProfile()?->id)
                        <a href="{{ route('profiles.edit') }}" 
                           class="absolute bottom-0 right-0 bg-red-600 hover:bg-red-700 text-white p-2 rounded-full transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                            </svg>
                        </a>
                    @endif
                </div>
                
                <!-- Información del perfil -->
                <div class="flex-1 text-center sm:text-left">
                    <h1 class="text-3xl font-bold mb-2">{{ $profile->name }}</h1>
                    
                    @if($profile->location)
                        <p class="text-gray-400 mb-2 flex items-center justify-center sm:justify-start">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            {{ $profile->location }}
                        </p>
                    @endif
                    
                    @if($profile->bio)
                        <p class="text-gray-300 mb-4 max-w-2xl">{{ $profile->bio }}</p>
                    @endif
                    
                    <!-- Estadísticas -->
                    <div class="flex flex-wrap gap-6 justify-center sm:justify-start mb-4">
                        <div class="text-center">
                            <div class="text-2xl font-bold">{{ $profile->ratings_count }}</div>
                            <div class="text-sm text-gray-400">Valoraciones</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold">{{ $profile->watchlist_count }}</div>
                            <div class="text-sm text-gray-400">En mi lista</div>
                        </div>
                        <div class="text-center">
                            <div class="text-2xl font-bold">{{ $profile->comments_count }}</div>
                            <div class="text-sm text-gray-400">Comentarios</div>
                        </div>
                        <a href="{{ route('profiles.followers', $profile) }}" class="text-center hover:text-red-500 transition">
                            <div class="text-2xl font-bold">{{ $profile->followers_count }}</div>
                            <div class="text-sm text-gray-400">Seguidores</div>
                        </a>
                        <a href="{{ route('profiles.following', $profile) }}" class="text-center hover:text-red-500 transition">
                            <div class="text-2xl font-bold">{{ $profile->following_count }}</div>
                            <div class="text-sm text-gray-400">Siguiendo</div>
                        </a>
                    </div>
                    
                    <!-- Botones de acción -->
                    <div class="flex flex-wrap gap-3 justify-center sm:justify-start">
                        @if($profile->id !== auth()->user()?->getActiveProfile()?->id)
                            @auth
                                <button id="follow-btn"
                                        onclick="toggleFollow({{ $profile->id }})"
                                        class="px-6 py-2 rounded-lg font-medium transition
                                               {{ $isFollowing ? 'bg-gray-700 hover:bg-gray-600 text-white' : 'bg-red-600 hover:bg-red-700 text-white' }}">
                                    <span id="follow-text">{{ $isFollowing ? 'Siguiendo' : 'Seguir' }}</span>
                                </button>
                                
                                @if($profile->allow_messages || $isFollowing)
                                    <a href="{{ route('profiles.messages.conversation', $profile) }}" 
                                       class="px-6 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg font-medium transition">
                                        Mensaje
                                    </a>
                                @endif
                            @endauth
                        @else
                            <a href="{{ route('profile.statistics') }}" 
                               class="px-6 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg font-medium transition">
                                Ver estadísticas
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Géneros favoritos -->
            @if($profile->favorite_genres && count($profile->favorite_genres) > 0)
                <div class="mt-6">
                    <h3 class="text-sm font-medium text-gray-400 mb-2">Géneros favoritos</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach($profile->favorite_genres as $genreId)
                            @php
                                $genre = \App\Models\Genre::find($genreId);
                            @endphp
                            @if($genre)
                                <span class="bg-gray-800 px-3 py-1 rounded-full text-sm">{{ $genre->name }}</span>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Contenido principal -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Actividad reciente -->
            <div class="lg:col-span-2">
                <h2 class="text-2xl font-bold mb-6">Actividad reciente</h2>
                
                @if($recentActivity->count() > 0)
                    <div class="space-y-4">
                        @foreach($recentActivity as $activity)
                            <div class="bg-gray-900 rounded-lg p-4">
                                <div class="flex items-start gap-4">
                                    <!-- Ícono del tipo de actividad -->
                                    <div class="flex-shrink-0">
                                        @switch($activity['type'])
                                            @case('rating')
                                                <div class="w-10 h-10 bg-yellow-500/10 rounded-full flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                                </div>
                                                @break
                                            @case('comment')
                                                <div class="w-10 h-10 bg-blue-500/10 rounded-full flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M18 13V5a2 2 0 00-2-2H4a2 2 0 00-2 2v8a2 2 0 002 2h3l3 3 3-3h3a2 2 0 002-2zM5 7a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1zm1 3a1 1 0 100 2h3a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                                @break
                                            @case('watchlist')
                                                <div class="w-10 h-10 bg-green-500/10 rounded-full flex items-center justify-center">
                                                    <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                                @break
                                        @endswitch
                                    </div>
                                    
                                    <!-- Contenido de la actividad -->
                                    <div class="flex-1">
                                        <div class="flex items-start justify-between">
                                            <div>
                                                @switch($activity['type'])
                                                    @case('rating')
                                                        <p class="text-sm text-gray-400">
                                                            Valoró 
                                                            <a href="{{ route('titles.show', $activity['title']->slug) }}" 
                                                               class="text-white hover:text-red-500 font-medium transition">
                                                                {{ $activity['title']->title }}
                                                            </a>
                                                        </p>
                                                        <div class="flex items-center gap-2 mt-1">
                                                            <div class="flex items-center">
                                                                @for($i = 1; $i <= 5; $i++)
                                                                    <svg class="w-4 h-4 {{ $i <= $activity['score']/2 ? 'text-yellow-400' : 'text-gray-600' }}" 
                                                                         fill="currentColor" viewBox="0 0 20 20">
                                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                                    </svg>
                                                                @endfor
                                                            </div>
                                                            <span class="text-sm text-gray-400">{{ number_format($activity['score']/2, 1) }}/5</span>
                                                        </div>
                                                        @if($activity['review'])
                                                            <p class="mt-2 text-sm text-gray-300">{{ Str::limit($activity['review'], 150) }}</p>
                                                        @endif
                                                        @break
                                                    @case('comment')
                                                        <p class="text-sm text-gray-400">
                                                            Comentó en 
                                                            <a href="{{ route('titles.show', $activity['title']->slug) }}" 
                                                               class="text-white hover:text-red-500 font-medium transition">
                                                                {{ $activity['title']->title }}
                                                            </a>
                                                        </p>
                                                        <p class="mt-2 text-sm text-gray-300">{{ Str::limit($activity['content'], 150) }}</p>
                                                        @break
                                                    @case('watchlist')
                                                        <p class="text-sm text-gray-400">
                                                            Añadió a su lista 
                                                            <a href="{{ route('titles.show', $activity['title']->slug) }}" 
                                                               class="text-white hover:text-red-500 font-medium transition">
                                                                {{ $activity['title']->title }}
                                                            </a>
                                                        </p>
                                                        @break
                                                @endswitch
                                            </div>
                                            
                                            <span class="text-xs text-gray-500">{{ $activity['created_at']->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-gray-900 rounded-lg p-8 text-center">
                        <p class="text-gray-400">No hay actividad reciente.</p>
                    </div>
                @endif
            </div>
            
            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Listas públicas -->
                <div>
                    <h3 class="text-lg font-bold mb-4">Listas públicas</h3>
                    <div class="bg-gray-900 rounded-lg p-4">
                        <p class="text-gray-400 text-sm text-center">Próximamente</p>
                    </div>
                </div>
                
                <!-- Reseñas destacadas -->
                <div>
                    <h3 class="text-lg font-bold mb-4">Reseñas destacadas</h3>
                    <div class="space-y-3">
                        @php
                            $featuredReviews = $profile->ratings()
                                ->whereNotNull('review')
                                ->where('review', '!=', '')
                                ->with('title')
                                ->orderByDesc('created_at')
                                ->limit(3)
                                ->get();
                        @endphp
                        
                        @forelse($featuredReviews as $review)
                            <div class="bg-gray-900 rounded-lg p-3">
                                <a href="{{ route('titles.show', $review->title->slug) }}" 
                                   class="flex items-start gap-3 hover:opacity-80 transition">
                                    <img src="{{ $review->title->poster_url }}" 
                                         alt="{{ $review->title->title }}"
                                         class="w-12 h-18 object-cover rounded">
                                    <div class="flex-1">
                                        <h4 class="font-medium text-sm mb-1">{{ $review->title->title }}</h4>
                                        <div class="flex items-center gap-2 mb-1">
                                            <div class="flex items-center">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="w-3 h-3 {{ $i <= $review->score/2 ? 'text-yellow-400' : 'text-gray-600' }}" 
                                                         fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                                @endfor
                                            </div>
                                        </div>
                                        <p class="text-xs text-gray-400 line-clamp-2">{{ $review->review }}</p>
                                    </div>
                                </a>
                            </div>
                        @empty
                            <p class="text-gray-400 text-sm text-center">No hay reseñas todavía.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    function toggleFollow(profileId) {
        const btn = document.getElementById('follow-btn');
        const text = document.getElementById('follow-text');
        const isFollowing = text.textContent === 'Siguiendo';
        
        btn.disabled = true;
        
        fetch(`/profiles/${profileId}/${isFollowing ? 'unfollow' : 'follow'}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (isFollowing) {
                    btn.classList.remove('bg-gray-700', 'hover:bg-gray-600');
                    btn.classList.add('bg-red-600', 'hover:bg-red-700');
                    text.textContent = 'Seguir';
                } else {
                    btn.classList.remove('bg-red-600', 'hover:bg-red-700');
                    btn.classList.add('bg-gray-700', 'hover:bg-gray-600');
                    text.textContent = 'Siguiendo';
                }
                
                // Update follower count
                const followerCount = document.querySelector('.followers-count');
                if (followerCount) {
                    const currentCount = parseInt(followerCount.textContent);
                    followerCount.textContent = isFollowing ? currentCount - 1 : currentCount + 1;
                }
            }
        })
        .finally(() => {
            btn.disabled = false;
        });
    }
    </script>
    @endpush
</x-app-layout>