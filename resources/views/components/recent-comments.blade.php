@props(['limit' => 6])

@php
    $recentComments = \App\Models\Comment::with(['user', 'title'])
        ->latest()
        ->limit($limit)
        ->get();
@endphp

<div class="bg-gray-900 rounded-lg p-6">
    <h2 class="text-2xl font-bold text-white mb-6">Comentarios Recientes</h2>
    
    <div class="grid gap-4">
        @foreach($recentComments as $comment)
            <div class="bg-gray-800 rounded-lg p-4 hover:bg-gray-700 transition-colors">
                <div class="flex items-start space-x-4">
                    <!-- Avatar del usuario -->
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-full bg-red-600 flex items-center justify-center">
                            <span class="text-white font-bold">
                                {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Contenido del comentario -->
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <span class="text-white font-medium">{{ $comment->user->name }}</span>
                                <span class="text-gray-400 text-sm mx-2">en</span>
                                <a href="{{ route('titles.show', $comment->title->slug) }}" 
                                   class="text-red-500 hover:underline text-sm">
                                    {{ $comment->title->title }}
                                </a>
                            </div>
                            <span class="text-gray-500 text-xs">
                                {{ $comment->created_at->diffForHumans() }}
                            </span>
                        </div>
                        
                        <p class="text-gray-300 line-clamp-2">
                            {{ $comment->content }}
                        </p>
                        
                        <!-- Interacciones -->
                        <div class="flex items-center space-x-4 mt-3">
                            <button class="text-gray-400 hover:text-white text-sm transition-colors">
                                <i class="far fa-heart mr-1"></i>
                                {{ $comment->likes_count ?? 0 }}
                            </button>
                            <a href="{{ route('titles.show', $comment->title->slug) }}#comment-{{ $comment->id }}"
                               class="text-gray-400 hover:text-white text-sm transition-colors">
                                <i class="far fa-comment mr-1"></i>
                                Responder
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    <div class="mt-6 text-center">
        <a href="{{ route('comments.index') }}" 
           class="text-red-500 hover:text-red-400 font-medium transition-colors">
            Ver todos los comentarios →
        </a>
    </div>
</div>