<x-app-layout>
    <x-slot name="title">Mi Dashboard</x-slot>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header del dashboard -->
        <div class="mb-8">
            @if(auth()->user()->getActiveProfile())
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-red-500 to-pink-500 flex items-center justify-center mr-4">
                            <span class="text-white text-2xl font-bold">
                                {{ substr(auth()->user()->getActiveProfile()->name, 0, 1) }}
                            </span>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold">
                                Hola, {{ auth()->user()->getActiveProfile()->name }}
                            </h1>
                            <p class="text-gray-400">
                                {{ now()->format('l, j F Y') }}
                            </p>
                        </div>
                    </div>
                    
                    <a href="{{ route('user-profiles.selector') }}" 
                       class="text-gray-400 hover:text-white">
                        <i class="fas fa-user-circle text-2xl"></i>
                    </a>
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-xl mb-4">Necesitas seleccionar un perfil para continuar</p>
                    <a href="{{ route('user-profiles.selector') }}" 
                       class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg">
                        Seleccionar Perfil
                    </a>
                </div>
            @endif
        </div>
        
        @if(auth()->user()->getActiveProfile())
            <!-- Sección Continuar Viendo -->
            <x-continue-watching />
            
            <!-- Estadísticas rápidas -->
            <section class="mb-8">
                <h2 class="text-2xl font-bold mb-4">Tu Actividad</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-gray-800 rounded-lg p-6 text-center">
                        <div class="text-3xl font-bold text-red-500">
                            {{ auth()->user()->getActiveProfile()->watchlist()->count() }}
                        </div>
                        <p class="text-gray-400 mt-2">En tu lista</p>
                    </div>
                    
                    <div class="bg-gray-800 rounded-lg p-6 text-center">
                        <div class="text-3xl font-bold text-blue-500">
                            {{ auth()->user()->getActiveProfile()->ratings()->count() }}
                        </div>
                        <p class="text-gray-400 mt-2">Valoraciones</p>
                    </div>
                    
                    <div class="bg-gray-800 rounded-lg p-6 text-center">
                        <div class="text-3xl font-bold text-green-500">
                            {{ auth()->user()->getActiveProfile()->comments()->count() }}
                        </div>
                        <p class="text-gray-400 mt-2">Comentarios</p>
                    </div>
                    
                    <div class="bg-gray-800 rounded-lg p-6 text-center">
                        <div class="text-3xl font-bold text-yellow-500">
                            {{ auth()->user()->getActiveProfile()->watchHistory()->count() }}
                        </div>
                        <p class="text-gray-400 mt-2">Vistos</p>
                    </div>
                </div>
            </section>
            
            <!-- Recomendaciones personalizadas -->
            <section class="mb-8">
                <h2 class="text-2xl font-bold mb-4">Recomendado para ti</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
                    @php
                        $recommendations = \App\Models\Title::inRandomOrder()
                            ->limit(5)
                            ->get();
                    @endphp
                    
                    @foreach($recommendations as $title)
                        <x-netflix-modern-card :title="$title" />
                    @endforeach
                </div>
            </section>
            
            <!-- Actividad reciente de la comunidad -->
            <section class="mb-8">
                <h2 class="text-2xl font-bold mb-4">Actividad de la Comunidad</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Comentarios recientes -->
                    <div class="bg-gray-800 rounded-lg p-6">
                        <h3 class="text-lg font-bold mb-4">Comentarios Recientes</h3>
                        <div class="space-y-4">
                            @php
                                $recentComments = \App\Models\Comment::with(['profile', 'commentable'])
                                    ->whereNull('parent_id')
                                    ->latest()
                                    ->limit(3)
                                    ->get();
                            @endphp
                            
                            @foreach($recentComments as $comment)
                                <div class="flex items-start space-x-3">
                                    <div class="w-10 h-10 rounded-full bg-gray-700 flex items-center justify-center">
                                        <span class="text-white font-bold">
                                            {{ substr($comment->profile->name, 0, 1) }}
                                        </span>
                                    </div>
                                    <div class="flex-1">
                                        <div class="text-sm">
                                            <span class="font-medium">{{ $comment->profile->name }}</span>
                                            <span class="text-gray-500">en</span>
                                            @if($comment->commentable)
                                                <a href="{{ route('titles.show', $comment->commentable->slug) }}" 
                                                   class="text-red-500 hover:underline">
                                                    {{ $comment->commentable->title }}
                                                </a>
                                            @endif
                                        </div>
                                        <p class="text-gray-400 text-sm mt-1 line-clamp-2">
                                            {{ $comment->content }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <!-- Valoraciones recientes -->
                    <div class="bg-gray-800 rounded-lg p-6">
                        <h3 class="text-lg font-bold mb-4">Valoraciones Recientes</h3>
                        <div class="space-y-4">
                            @php
                                $recentRatings = \App\Models\Rating::with(['profile', 'title'])
                                    ->latest()
                                    ->limit(3)
                                    ->get();
                            @endphp
                            
                            @foreach($recentRatings as $rating)
                                <div class="flex items-center space-x-3">
                                    <img src="{{ $rating->title->poster_url }}" 
                                         alt="{{ $rating->title->title }}"
                                         class="w-10 h-14 object-cover rounded">
                                    <div class="flex-1">
                                        <div class="text-sm">
                                            <span class="font-medium">{{ $rating->profile->name }}</span>
                                            valoró
                                            <a href="{{ route('titles.show', $rating->title->slug) }}" 
                                               class="text-red-500 hover:underline">
                                                {{ $rating->title->title }}
                                            </a>
                                        </div>
                                        <div class="flex items-center mt-1">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= ($rating->score / 2) ? 'text-yellow-400' : 'text-gray-600' }} text-xs"></i>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- Enlaces rápidos -->
            <section>
                <h2 class="text-2xl font-bold mb-4">Accesos Rápidos</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <a href="{{ route('watchlist.index') }}" 
                       class="bg-gray-800 hover:bg-gray-700 rounded-lg p-4 text-center transition">
                        <i class="fas fa-list text-2xl text-red-500 mb-2"></i>
                        <p>Mi Lista</p>
                    </a>
                    
                    <a href="{{ route('catalog.index') }}" 
                       class="bg-gray-800 hover:bg-gray-700 rounded-lg p-4 text-center transition">
                        <i class="fas fa-film text-2xl text-blue-500 mb-2"></i>
                        <p>Catálogo</p>
                    </a>
                    
                    <a href="{{ route('comments.index') }}" 
                       class="bg-gray-800 hover:bg-gray-700 rounded-lg p-4 text-center transition">
                        <i class="fas fa-comments text-2xl text-green-500 mb-2"></i>
                        <p>Comunidad</p>
                    </a>
                    
                    <a href="{{ route('profile.edit') }}" 
                       class="bg-gray-800 hover:bg-gray-700 rounded-lg p-4 text-center transition">
                        <i class="fas fa-cog text-2xl text-yellow-500 mb-2"></i>
                        <p>Configuración</p>
                    </a>
                </div>
            </section>
        @endif
    </div>
</x-app-layout>