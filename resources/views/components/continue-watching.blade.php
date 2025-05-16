@props(['profile' => null])

@php
    $continueWatching = \App\Models\WatchHistory::with('title')
        ->where('profile_id', $profile?->id ?? auth()->user()->getActiveProfile()->id)
        ->where('progress', '>', 5)
        ->where('progress', '<', 95)
        ->orderBy('updated_at', 'desc')
        ->limit(10)
        ->get();
@endphp

@if($continueWatching->count() > 0)
<section class="mb-8">
    <h2 class="text-2xl font-bold mb-4 flex items-center">
        <i class="fas fa-play-circle mr-3 text-red-500"></i>
        Continuar Viendo
    </h2>
    
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
        @foreach($continueWatching as $item)
            <div class="relative group">
                <a href="{{ route('titles.show', $item->title->slug) }}">
                    <div class="relative">
                        <img src="{{ $item->title->poster_url }}" 
                             alt="{{ $item->title->title }}"
                             class="w-full rounded-lg shadow-lg group-hover:opacity-75 transition">
                        
                        <!-- Barra de progreso -->
                        <div class="absolute bottom-0 left-0 right-0 h-1 bg-gray-700 rounded-b-lg">
                            <div class="h-full bg-red-600 rounded-b-lg" style="width: {{ $item->progress }}%"></div>
                        </div>
                        
                        <!-- Play button overlay -->
                        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                            <div class="bg-white rounded-full p-3 shadow-lg">
                                <svg class="w-8 h-8 text-black" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </a>
                
                <div class="mt-2">
                    <h3 class="font-medium truncate">{{ $item->title->title }}</h3>
                    <p class="text-sm text-gray-400">
                        @if($item->episode_id)
                            Episodio {{ $item->episode->episode_number }}
                        @elseif($item->progress > 0)
                            {{ gmdate("H:i:s", $item->last_position ?? 0) }} / {{ gmdate("H:i:s", $item->title->runtime * 60) }}
                        @else
                            {{ $item->progress }}% visto
                        @endif
                    </p>
                </div>
            </div>
        @endforeach
    </div>
</section>
@endif