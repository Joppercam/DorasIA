@props(['limit' => 10])

@php
    $recentRatings = \App\Models\Rating::with(['profile', 'title'])
        ->whereNotNull('score')
        ->orderBy('created_at', 'desc')
        ->take($limit)
        ->get();
@endphp

<div class="recent-ratings bg-gray-900 rounded-lg p-6">
    <h3 class="text-xl font-bold mb-4">Valoraciones Recientes</h3>
    
    @if($recentRatings->count() > 0)
        <div class="space-y-4">
            @foreach($recentRatings as $rating)
                <div class="flex items-start space-x-3">
                    <!-- Avatar -->
                    <div class="flex-shrink-0 w-10 h-10 rounded-full bg-gradient-to-br from-red-500 to-pink-500 flex items-center justify-center">
                        <span class="text-white font-bold text-sm">
                            {{ substr($rating->profile->name, 0, 1) }}
                        </span>
                    </div>
                    
                    <!-- Contenido -->
                    <div class="flex-1">
                        <div class="flex items-center justify-between">
                            <h4 class="font-medium">{{ $rating->profile->name }}</h4>
                            <x-rating-stars 
                                :title-id="$rating->title_id" 
                                :current-rating="$rating->score / 2" 
                                :show-count="false" 
                                size="sm" />
                        </div>
                        
                        <p class="text-sm text-gray-400">
                            valoró <a href="{{ route('titles.show', $rating->title->slug) }}" 
                                     class="text-white hover:text-red-500 font-medium">
                                {{ $rating->title->title }}
                            </a>
                        </p>
                        
                        @if($rating->review)
                            <p class="text-sm mt-1 text-gray-300 line-clamp-2">
                                "{{ $rating->review }}"
                            </p>
                        @endif
                        
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $rating->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="mt-6 text-center">
            <a href="{{ route('community.ratings') }}" 
               class="text-red-500 hover:text-red-400 text-sm font-medium">
                Ver todas las valoraciones →
            </a>
        </div>
    @else
        <p class="text-gray-400 text-center py-8">
            No hay valoraciones recientes todavía.
        </p>
    @endif
</div>