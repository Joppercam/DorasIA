<x-app-layout>
    <x-slot name="title">Comunidad de Comentarios</x-slot>
    
    <div class="min-h-screen bg-black py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-12">
                <h1 class="text-4xl font-bold text-white mb-4">Comunidad de Comentarios</h1>
                <p class="text-xl text-gray-400">Únete a la conversación sobre tus títulos favoritos</p>
            </div>
            
            <!-- Estadísticas -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-12">
                <div class="bg-gray-800 rounded-lg p-6 text-center">
                    <i class="far fa-comments text-4xl text-red-500 mb-3"></i>
                    <h3 class="text-2xl font-bold text-white">{{ \App\Models\Comment::count() }}</h3>
                    <p class="text-gray-400">Comentarios totales</p>
                </div>
                
                <div class="bg-gray-800 rounded-lg p-6 text-center">
                    <i class="far fa-user text-4xl text-blue-500 mb-3"></i>
                    <h3 class="text-2xl font-bold text-white">{{ \App\Models\Profile::has('comments')->count() }}</h3>
                    <p class="text-gray-400">Perfiles activos</p>
                </div>
                
                <div class="bg-gray-800 rounded-lg p-6 text-center">
                    <i class="far fa-clock text-4xl text-green-500 mb-3"></i>
                    <h3 class="text-2xl font-bold text-white">{{ \App\Models\Comment::whereDate('created_at', today())->count() }}</h3>
                    <p class="text-gray-400">Comentarios hoy</p>
                </div>
                
                <div class="bg-gray-800 rounded-lg p-6 text-center">
                    <i class="far fa-fire text-4xl text-orange-500 mb-3"></i>
                    <h3 class="text-2xl font-bold text-white">{{ \App\Models\Title::has('comments')->count() }}</h3>
                    <p class="text-gray-400">Títulos discutidos</p>
                </div>
            </div>
            
            <!-- Contenido principal -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Columna principal - Comentarios recientes -->
                <div class="lg:col-span-2">
                    <div class="bg-gray-800 rounded-lg p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-bold text-white">Comentarios Recientes</h2>
                            <select class="bg-gray-700 text-white rounded px-3 py-2">
                                <option>Más recientes</option>
                                <option>Más populares</option>
                                <option>Más respondidos</option>
                            </select>
                        </div>
                        
                        <div class="space-y-6">
                            @php
                                $comments = \App\Models\Comment::with(['profile', 'commentable'])
                                    ->whereNull('parent_id')
                                    ->latest()
                                    ->paginate(10);
                            @endphp
                            
                            @foreach($comments as $comment)
                                <div class="border-b border-gray-700 pb-6 last:border-0">
                                    <div class="flex items-start space-x-4">
                                        <!-- Avatar -->
                                        <div class="w-12 h-12 bg-red-600 rounded-full flex items-center justify-center flex-shrink-0">
                                            <span class="text-white font-bold text-lg">
                                                {{ strtoupper(substr($comment->profile->name, 0, 1)) }}
                                            </span>
                                        </div>
                                        
                                        <!-- Contenido -->
                                        <div class="flex-1">
                                            <div class="flex items-start justify-between">
                                                <div>
                                                    <h4 class="font-semibold text-white">{{ $comment->profile->name }}</h4>
                                                    @if($comment->commentable_type === 'App\\Models\\Title')
                                                        <p class="text-sm text-gray-400">
                                                            en <a href="{{ route('titles.show', $comment->commentable->slug) }}" 
                                                                  class="text-red-500 hover:underline">
                                                                {{ $comment->commentable->title }}
                                                            </a>
                                                        </p>
                                                    @endif
                                                </div>
                                                <span class="text-sm text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                                            </div>
                                            
                                            <p class="mt-2 text-gray-300">{{ $comment->content }}</p>
                                            
                                            <!-- Interacciones -->
                                            <div class="mt-3 flex items-center space-x-4 text-sm">
                                                <button class="text-gray-400 hover:text-white flex items-center space-x-1">
                                                    <i class="far fa-heart"></i>
                                                    <span>12</span>
                                                </button>
                                                <button class="text-gray-400 hover:text-white flex items-center space-x-1">
                                                    <i class="far fa-comment"></i>
                                                    <span>{{ $comment->replies->count() }}</span>
                                                </button>
                                                <button class="text-gray-400 hover:text-white">
                                                    <i class="far fa-share"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <!-- Paginación -->
                        <div class="mt-6">
                            {{ $comments->links() }}
                        </div>
                    </div>
                </div>
                
                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Top comentaristas -->
                    <div class="bg-gray-800 rounded-lg p-6">
                        <h3 class="text-xl font-bold text-white mb-4">Top Comentaristas</h3>
                        <div class="space-y-3">
                            @php
                                $topProfiles = \App\Models\Profile::withCount('comments')
                                    ->orderBy('comments_count', 'desc')
                                    ->limit(5)
                                    ->get();
                            @endphp
                            
                            @foreach($topProfiles as $index => $profile)
                                <div class="flex items-center space-x-3">
                                    <span class="text-2xl font-bold {{ $index == 0 ? 'text-yellow-500' : ($index == 1 ? 'text-gray-400' : ($index == 2 ? 'text-orange-600' : 'text-gray-600')) }}">
                                        {{ $index + 1 }}
                                    </span>
                                    <div class="w-10 h-10 bg-red-600 rounded-full flex items-center justify-center">
                                        <span class="text-white font-bold">
                                            {{ strtoupper(substr($profile->name, 0, 1)) }}
                                        </span>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-white font-medium">{{ $profile->name }}</h4>
                                        <p class="text-gray-400 text-sm">{{ $profile->comments_count }} comentarios</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Trending topics -->
                    <div class="bg-gray-800 rounded-lg p-6">
                        <h3 class="text-xl font-bold text-white mb-4">Temas Trending</h3>
                        <div class="flex flex-wrap gap-2">
                            <span class="px-3 py-1 bg-gray-700 text-gray-300 rounded-full text-sm">#KDrama</span>
                            <span class="px-3 py-1 bg-gray-700 text-gray-300 rounded-full text-sm">#Romance</span>
                            <span class="px-3 py-1 bg-gray-700 text-gray-300 rounded-full text-sm">#Drama2024</span>
                            <span class="px-3 py-1 bg-gray-700 text-gray-300 rounded-full text-sm">#Thriller</span>
                            <span class="px-3 py-1 bg-gray-700 text-gray-300 rounded-full text-sm">#Comedia</span>
                        </div>
                    </div>
                    
                    <!-- Títulos más comentados -->
                    <div class="bg-gray-800 rounded-lg p-6">
                        <h3 class="text-xl font-bold text-white mb-4">Más Comentados</h3>
                        <div class="space-y-3">
                            @php
                                $mostCommented = \App\Models\Title::withCount('comments')
                                    ->orderBy('comments_count', 'desc')
                                    ->limit(5)
                                    ->get();
                            @endphp
                            
                            @foreach($mostCommented as $title)
                                <a href="{{ route('titles.show', $title->slug) }}" 
                                   class="flex items-center space-x-3 hover:bg-gray-700 p-2 rounded">
                                    <img src="{{ asset($title->poster_path) }}" 
                                         alt="{{ $title->title }}" 
                                         class="w-10 h-14 object-cover rounded"
                                         onerror="this.src='{{ asset('posters/placeholder.jpg') }}'">
                                    <div class="flex-1">
                                        <h4 class="text-white font-medium text-sm">{{ $title->title }}</h4>
                                        <p class="text-gray-400 text-xs">{{ $title->comments_count }} comentarios</p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Invitación a participar -->
            @guest
                <div class="mt-12 bg-gradient-to-r from-red-900 to-red-700 rounded-lg p-8 text-center">
                    <h2 class="text-3xl font-bold text-white mb-4">Únete a la conversación</h2>
                    <p class="text-lg text-gray-200 mb-6">
                        Inicia sesión para compartir tus opiniones sobre tus series y películas favoritas
                    </p>
                    <div class="flex justify-center space-x-4">
                        <a href="{{ route('login') }}" 
                           class="bg-white text-red-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition duration-200">
                            Iniciar sesión
                        </a>
                        <a href="{{ route('register') }}" 
                           class="bg-red-800 text-white px-6 py-3 rounded-lg font-semibold hover:bg-red-900 transition duration-200">
                            Registrarse
                        </a>
                    </div>
                </div>
            @endguest
        </div>
    </div>
</x-app-layout>