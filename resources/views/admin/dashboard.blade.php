@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Panel de Control')

@section('content')
<div class="space-y-6">
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div class="bg-gray-800 rounded-lg border border-gray-700 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-blue-900 bg-opacity-50">
                    <i data-feather="tv" class="w-8 h-8 text-blue-400"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-400">Total Series</p>
                    <p class="text-2xl font-bold text-white">{{ number_format($stats['total_series']) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-gray-800 rounded-lg border border-gray-700 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-purple-900 bg-opacity-50">
                    <i data-feather="film" class="w-8 h-8 text-purple-400"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-400">Total Películas</p>
                    <p class="text-2xl font-bold text-white">{{ number_format($stats['total_movies']) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-gray-800 rounded-lg border border-gray-700 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-green-900 bg-opacity-50">
                    <i data-feather="users" class="w-8 h-8 text-green-400"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-400">Total Usuarios</p>
                    <p class="text-2xl font-bold text-white">{{ number_format($stats['total_users']) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-gray-800 rounded-lg border border-gray-700 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-yellow-900 bg-opacity-50">
                    <i data-feather="message-circle" class="w-8 h-8 text-yellow-400"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-400">Total Comentarios</p>
                    <p class="text-2xl font-bold text-white">{{ number_format($stats['total_comments']) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-gray-800 rounded-lg border border-gray-700 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-red-900 bg-opacity-50">
                    <i data-feather="star" class="w-8 h-8 text-red-400"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-400">Total Calificaciones</p>
                    <p class="text-2xl font-bold text-white">{{ number_format($stats['total_ratings']) }}</p>
                </div>
            </div>
        </div>
        
        <div class="bg-gray-800 rounded-lg border border-gray-700 p-6">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-indigo-900 bg-opacity-50">
                    <i data-feather="bookmark" class="w-8 h-8 text-indigo-400"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-400">Items en Listas</p>
                    <p class="text-2xl font-bold text-white">{{ number_format($stats['total_watchlist_items']) }}</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Series Populares -->
        <div class="bg-gray-800 rounded-lg border border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-white mb-4">Series Más Populares</h3>
            <div class="space-y-3">
                @forelse($popularSeries as $series)
                    <div class="flex items-center justify-between py-2">
                        <div class="flex items-center">
                            <img src="{{ $series->poster_path ? 'https://image.tmdb.org/t/p/w92' . $series->poster_path : 'https://via.placeholder.com/46x69/333/666?text=No+Image' }}" 
                                 alt="{{ $series->display_title }}" 
                                 class="w-8 h-12 rounded object-cover mr-3">
                            <div>
                                <p class="text-white font-medium">{{ Str::limit($series->display_title ?? $series->title ?? 'Sin título', 30) }}</p>
                                <p class="text-gray-400 text-sm">{{ $series->country_flag ?? '🌏' }} {{ $series->drama_type_formatted ?? 'Drama' }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-white font-medium">{{ $series->ratings_count }} ⭐</p>
                            <p class="text-gray-400 text-sm">{{ $series->comments_count }} 💬</p>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-400">No hay datos disponibles</p>
                @endforelse
            </div>
        </div>
        
        <!-- Doramas por País -->
        <div class="bg-gray-800 rounded-lg border border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-white mb-4">Doramas por País</h3>
            <div class="space-y-3">
                @forelse($dramasByCountry as $country)
                    <div class="flex items-center justify-between py-2">
                        <div class="flex items-center">
                            <span class="text-2xl mr-3">
                                @switch($country->country_code)
                                    @case('KR') 🇰🇷 @break
                                    @case('CN') 🇨🇳 @break
                                    @case('JP') 🇯🇵 @break
                                    @case('TH') 🇹🇭 @break
                                    @case('TW') 🇹🇼 @break
                                    @default 🌏
                                @endswitch
                            </span>
                            <div>
                                <p class="text-white font-medium">{{ $country->country_name }}</p>
                                <p class="text-gray-400 text-sm">{{ $country->country_code }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-white font-medium">{{ $country->total }}</p>
                            <p class="text-gray-400 text-sm">series</p>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-400">No hay datos disponibles</p>
                @endforelse
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Usuarios Más Activos -->
        <div class="bg-gray-800 rounded-lg border border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-white mb-4">Usuarios Más Activos</h3>
            <div class="space-y-3">
                @forelse($activeUsers as $user)
                    <div class="flex items-center justify-between py-2">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                <span class="text-white text-sm font-medium">{{ substr($user->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <p class="text-white font-medium">{{ $user->name }}</p>
                                <p class="text-gray-400 text-sm">{{ $user->email }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-white font-medium">{{ $user->title_ratings_count }} ⭐</p>
                            <p class="text-gray-400 text-sm">{{ $user->comments_count }} 💬</p>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-400">No hay usuarios activos</p>
                @endforelse
            </div>
        </div>
        
        <!-- Actividad Reciente -->
        <div class="bg-gray-800 rounded-lg border border-gray-700 p-6">
            <h3 class="text-lg font-semibold text-white mb-4">Comentarios Recientes</h3>
            <div class="space-y-3 max-h-80 overflow-y-auto">
                @forelse($recentComments as $comment)
                    <div class="py-2 border-b border-gray-700 last:border-b-0">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <p class="text-white font-medium text-sm">{{ $comment->user ? $comment->user->name : 'Usuario eliminado' }}</p>
                                <p class="text-gray-300 text-sm mt-1">{{ Str::limit($comment->content, 80) }}</p>
                                <p class="text-gray-500 text-xs mt-1">
                                    {{ $comment->created_at->diffForHumans() }} en 
                                    @if($comment->series)
                                        <span class="text-blue-400">{{ Str::limit($comment->series->display_title, 20) }}</span>
                                    @elseif($comment->commentable && $comment->commentable instanceof App\Models\Movie)
                                        <span class="text-purple-400">{{ Str::limit($comment->commentable->display_title, 20) }}</span>
                                    @else
                                        <span class="text-gray-400">Contenido eliminado</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-gray-400">No hay comentarios recientes</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
    // Actualizar íconos después de cargar el contenido
    document.addEventListener('DOMContentLoaded', function() {
        feather.replace();
    });
</script>
@endsection