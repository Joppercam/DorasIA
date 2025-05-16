@props(['title'])

@php
    $commentCount = $title->comments()->count();
    $latestComment = $title->comments()->with('user')->latest()->first();
@endphp

<div class="group relative">
    <a href="{{ route('titles.show', $title->slug) }}" class="block">
        <div class="relative aspect-[2/3] bg-gray-900 rounded-lg overflow-hidden">
            @if($title->poster_path)
                <img src="{{ $title->poster_url }}" 
                     alt="{{ $title->title }}"
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                     loading="lazy"
                     onerror="this.onerror=null; this.src='/posters/placeholder.jpg'">
            @else
                <img src="/posters/placeholder.jpg" 
                     alt="{{ $title->title }}"
                     class="w-full h-full object-cover">
            @endif
            
            <!-- Overlay con información de comentarios -->
            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black/90 to-transparent p-3">
                <h3 class="text-white font-bold text-sm mb-1 line-clamp-1">{{ $title->title }}</h3>
                
                @if($commentCount > 0)
                    <div class="flex items-center text-xs text-gray-300 mb-2">
                        <i class="far fa-comment mr-1"></i>
                        <span>{{ $commentCount }} {{ $commentCount == 1 ? 'comentario' : 'comentarios' }}</span>
                    </div>
                    
                    @if($latestComment)
                        <div class="bg-black/50 rounded p-2">
                            <div class="flex items-center mb-1">
                                <span class="text-gray-400 text-xs">{{ $latestComment->user->name }}:</span>
                            </div>
                            <p class="text-gray-300 text-xs line-clamp-2">
                                {{ $latestComment->content }}
                            </p>
                        </div>
                    @endif
                @else
                    <p class="text-gray-400 text-xs">Sé el primero en comentar</p>
                @endif
            </div>
        </div>
    </a>
    
    <!-- Botón flotante de comentarios -->
    <button onclick="window.location.href='{{ route('titles.show', $title->slug) }}#comments'"
            class="absolute top-2 right-2 bg-red-600 text-white rounded-full w-8 h-8 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
        <i class="far fa-comment text-sm"></i>
        @if($commentCount > 0)
            <span class="absolute -top-1 -right-1 bg-white text-red-600 text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold">
                {{ $commentCount > 99 ? '99+' : $commentCount }}
            </span>
        @endif
    </button>
</div>