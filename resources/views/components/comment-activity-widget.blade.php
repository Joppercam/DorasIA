@php
    $recentActivity = \App\Models\Comment::with(['user', 'title'])
        ->latest()
        ->limit(3)
        ->get();
@endphp

<div id="comment-activity-widget" 
     class="fixed bottom-4 right-4 w-96 bg-gray-900 rounded-lg shadow-2xl border border-gray-800 z-50 transform translate-y-full transition-transform duration-300"
     x-data="{ open: false }"
     x-init="
        // Mostrar automáticamente cuando hay actividad nueva
        setInterval(() => {
            if (!open && Math.random() > 0.7) {
                open = true;
                setTimeout(() => { open = false }, 5000);
            }
        }, 30000)
     "
     :class="{ 'translate-y-0': open, 'translate-y-full': !open }">
    
    <!-- Header -->
    <div class="flex items-center justify-between p-4 border-b border-gray-800">
        <h3 class="text-white font-bold flex items-center">
            <span class="relative">
                <i class="far fa-comment mr-2"></i>
                <span class="absolute -top-1 -right-1 w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
            </span>
            Actividad Reciente
        </h3>
        <button @click="open = false" class="text-gray-400 hover:text-white">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <!-- Contenido -->
    <div class="p-4 space-y-3 max-h-96 overflow-y-auto">
        @foreach($recentActivity as $comment)
            <div class="bg-gray-800 rounded p-3 hover:bg-gray-700 transition-colors cursor-pointer"
                 onclick="window.location.href='{{ route('titles.show', $comment->title->slug) }}#comment-{{ $comment->id }}'">
                <div class="flex items-start space-x-3">
                    <div class="w-8 h-8 bg-red-600 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-white text-sm font-bold">
                            {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                        </span>
                    </div>
                    <div class="flex-1">
                        <div class="text-sm">
                            <span class="text-white font-medium">{{ $comment->user->name }}</span>
                            <span class="text-gray-400">comentó en</span>
                            <span class="text-red-500">{{ $comment->title->title }}</span>
                        </div>
                        <p class="text-gray-300 text-sm mt-1 line-clamp-2">
                            {{ $comment->content }}
                        </p>
                        <span class="text-gray-500 text-xs">
                            {{ $comment->created_at->diffForHumans() }}
                        </span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    <!-- Footer -->
    <div class="p-4 border-t border-gray-800">
        <a href="{{ route('comments.index') }}" 
           class="block text-center text-red-500 hover:text-red-400 transition-colors">
            Ver toda la actividad →
        </a>
    </div>
</div>

<!-- Botón flotante para abrir el widget -->
<button id="comment-activity-toggle"
        @click="open = !open"
        class="fixed bottom-4 right-4 w-14 h-14 bg-red-600 text-white rounded-full shadow-lg flex items-center justify-center hover:bg-red-700 transition-colors z-40"
        :class="{ 'opacity-0 pointer-events-none': open }">
    <i class="far fa-comment text-xl"></i>
    @if($recentActivity->count() > 0)
        <span class="absolute -top-1 -right-1 w-6 h-6 bg-white text-red-600 rounded-full text-xs flex items-center justify-center font-bold animate-bounce">
            {{ $recentActivity->count() }}
        </span>
    @endif
</button>