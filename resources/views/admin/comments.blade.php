@extends('layouts.admin')

@section('title', 'Moderación de Comentarios')
@section('page-title', 'Moderación de Comentarios')

@section('content')
<div class="space-y-6">
    <!-- Header con filtros -->
    <div class="bg-gray-800 rounded-lg border border-gray-700 p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-4">
            <h3 class="text-lg font-semibold text-white mb-4 lg:mb-0">Filtrar Comentarios</h3>
            <a href="{{ route('admin.comments') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                <i data-feather="refresh-cw" class="w-4 h-4 mr-2"></i>
                Limpiar Filtros
            </a>
        </div>
        
        <form method="GET" action="{{ route('admin.comments') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-300 mb-2">Buscar en Contenido</label>
                <input type="text" 
                       id="search" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Buscar en el contenido del comentario..."
                       class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                    <i data-feather="search" class="w-4 h-4 inline mr-2"></i>
                    Buscar
                </button>
            </div>
        </form>
    </div>
    
    <!-- Resultados -->
    <div class="bg-gray-800 rounded-lg border border-gray-700">
        <div class="p-6 border-b border-gray-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-white">
                    Comentarios ({{ $comments->total() }} resultados)
                </h3>
                <div class="text-sm text-gray-400">
                    Ordenados por fecha (más recientes primero)
                </div>
            </div>
        </div>
        
        <div class="divide-y divide-gray-700">
            @forelse($comments as $comment)
                <div class="p-6 hover:bg-gray-700 transition-colors">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <!-- Header del comentario -->
                            <div class="flex items-center mb-3">
                                @if($comment->user)
                                    @if($comment->user->avatar)
                                        <img src="{{ $comment->user->avatar }}" alt="{{ $comment->user->name }}" class="w-8 h-8 rounded-full mr-3">
                                    @else
                                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center mr-3">
                                            <span class="text-white text-xs font-medium">{{ substr($comment->user->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2">
                                            <span class="text-white font-medium">{{ $comment->user->name }}</span>
                                            @if($comment->user->is_admin)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-900 text-red-200">
                                                    <i data-feather="shield" class="w-3 h-3 mr-1"></i>
                                                    Admin
                                                </span>
                                            @endif
                                        </div>
                                        <div class="text-sm text-gray-400">{{ $comment->user->email }}</div>
                                    </div>
                                @else
                                    <div class="w-8 h-8 bg-gray-500 rounded-full flex items-center justify-center mr-3">
                                        <span class="text-white text-xs font-medium">?</span>
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2">
                                            <span class="text-white font-medium">Usuario eliminado</span>
                                        </div>
                                        <div class="text-sm text-gray-400">usuario@eliminado.com</div>
                                    </div>
                                @endif
                                <div class="text-right">
                                    <div class="text-sm text-gray-300">{{ $comment->created_at->format('d/m/Y H:i') }}</div>
                                    <div class="text-xs text-gray-400">{{ $comment->created_at->diffForHumans() }}</div>
                                </div>
                            </div>
                            
                            <!-- Contenido del comentario -->
                            <div class="bg-gray-900 rounded-lg p-4 mb-3">
                                <p class="text-gray-200 leading-relaxed">{{ $comment->content }}</p>
                            </div>
                            
                            <!-- Información del contenido comentado -->
                            <div class="flex items-center text-sm text-gray-400 mb-3">
                                <i data-feather="message-circle" class="w-4 h-4 mr-2"></i>
                                <span>Comentario en: </span>
                                @if($comment->series)
                                    <a href="{{ route('series.show', $comment->series->id) }}" 
                                       target="_blank"
                                       class="text-blue-400 hover:text-blue-300 ml-1 flex items-center">
                                        <i data-feather="tv" class="w-4 h-4 mr-1"></i>
                                        {{ $comment->series->display_title }}
                                        <i data-feather="external-link" class="w-3 h-3 ml-1"></i>
                                    </a>
                                @elseif($comment->commentable && $comment->commentable instanceof App\Models\Movie)
                                    <a href="{{ route('movies.show', $comment->commentable->id) }}" 
                                       target="_blank"
                                       class="text-purple-400 hover:text-purple-300 ml-1 flex items-center">
                                        <i data-feather="film" class="w-4 h-4 mr-1"></i>
                                        {{ $comment->commentable->display_title }}
                                        <i data-feather="external-link" class="w-3 h-3 ml-1"></i>
                                    </a>
                                @else
                                    <span class="text-gray-500 ml-1">Contenido eliminado</span>
                                @endif
                            </div>
                            
                            <!-- Metadatos adicionales -->
                            <div class="flex items-center space-x-4 text-xs text-gray-500">
                                <span>ID: {{ $comment->id }}</span>
                                @if($comment->updated_at->gt($comment->created_at))
                                    <span>Editado: {{ $comment->updated_at->diffForHumans() }}</span>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Acciones -->
                        <div class="ml-6 flex flex-col space-y-2">
                            <a href="{{ $comment->series ? route('series.show', $comment->series->id) : ($comment->commentable && $comment->commentable instanceof App\Models\Movie ? route('movies.show', $comment->commentable->id) : '#') }}" 
                               target="_blank"
                               class="inline-flex items-center px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded transition-colors {{ !$comment->series && !($comment->commentable && $comment->commentable instanceof App\Models\Movie) ? 'opacity-50 cursor-not-allowed' : '' }}"
                               {{ !$comment->series && !($comment->commentable && $comment->commentable instanceof App\Models\Movie) ? 'onclick="return false;"' : '' }}>
                                <i data-feather="external-link" class="w-3 h-3 mr-1"></i>
                                Ver Contexto
                            </a>
                            
                            <form method="POST" action="{{ route('admin.comments.delete', $comment) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        onclick="return confirm('¿Estás seguro de que quieres eliminar este comentario?')"
                                        class="inline-flex items-center px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded transition-colors">
                                    <i data-feather="trash-2" class="w-3 h-3 mr-1"></i>
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <div class="text-gray-400">
                        <i data-feather="message-circle" class="w-12 h-12 mx-auto mb-4 opacity-50"></i>
                        <p class="text-lg font-medium">No se encontraron comentarios</p>
                        <p class="text-sm">No hay comentarios que coincidan con los filtros aplicados</p>
                    </div>
                </div>
            @endforelse
        </div>
        
        @if($comments->hasPages())
            <div class="px-6 py-4 border-t border-gray-700">
                {{ $comments->links() }}
            </div>
        @endif
    </div>
</div>

<script>
// Actualizar íconos después de cargar el contenido
document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
});
</script>
@endsection