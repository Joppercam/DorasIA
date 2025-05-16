@props(['titleId'])

@php
    $title = \App\Models\Title::find($titleId);
    if (!$title) return;
    
    $profile = auth()->user()?->getActiveProfile();
    $userRating = null;
    
    if ($profile) {
        $userRating = \App\Models\Rating::where('profile_id', $profile->id)
            ->where('title_id', $titleId)
            ->first();
    }
    
    // Get rating distribution from 1-10 scale converted to 1-5
    $distribution = [
        5 => 0,
        4 => 0,
        3 => 0,
        2 => 0,
        1 => 0
    ];
    
    $ratings = $title->ratings()
        ->selectRaw('ROUND(score/2) as rating_scale, count(*) as count')
        ->groupBy('rating_scale')
        ->get();
    
    foreach ($ratings as $rating) {
        if (isset($distribution[$rating->rating_scale])) {
            $distribution[$rating->rating_scale] = $rating->count;
        }
    }
    
    $totalRatings = array_sum($distribution);
    $averageRating = ($title->vote_average ?? 0) / 2;
@endphp

<div class="bg-gray-800 rounded-lg p-6">
    <h3 class="text-xl font-bold text-white mb-4">Valoraciones</h3>
    
    <!-- Resumen general -->
    <div class="flex items-center space-x-6 mb-6">
        <div class="text-center">
            <div class="text-5xl font-bold text-white">{{ number_format($averageRating, 1) }}</div>
            <div class="flex items-center justify-center mt-1">
                @for($i = 1; $i <= 5; $i++)
                    <i class="fas fa-star {{ $i <= round($averageRating) ? 'text-yellow-400' : 'text-gray-600' }}"></i>
                @endfor
            </div>
            <p class="text-sm text-gray-400 mt-1">{{ $totalRatings }} valoraciones</p>
        </div>
        
        <!-- Tu valoración -->
        @auth
            <div class="flex-1 text-center border-l border-gray-700 pl-6">
                <p class="text-sm text-gray-400 mb-2">Tu valoración</p>
                @if($userRating)
                    <div class="text-3xl font-bold text-white">{{ $userRating->score / 2 }}</div>
                    <div class="flex items-center justify-center mt-1">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star {{ $i <= ($userRating->score / 2) ? 'text-yellow-400' : 'text-gray-600' }} text-sm"></i>
                        @endfor
                    </div>
                @else
                    <x-rating-stars :title-id="$titleId" :show-count="false" size="lg" />
                @endif
            </div>
        @endauth
    </div>
    
    <!-- Distribución de valoraciones -->
    <div class="space-y-2">
        @for($score = 5; $score >= 1; $score--)
            @php
                $count = $distribution[$score];
                $percentage = $totalRatings > 0 ? ($count / $totalRatings) * 100 : 0;
            @endphp
            <div class="flex items-center space-x-3">
                <div class="flex items-center space-x-1 w-16">
                    <span class="text-white font-medium">{{ $score }}</span>
                    <i class="fas fa-star text-yellow-400 text-sm"></i>
                </div>
                <div class="flex-1">
                    <div class="bg-gray-700 rounded-full h-2 overflow-hidden">
                        <div class="bg-yellow-400 h-full transition-all duration-500" 
                             style="width: {{ $percentage }}%"></div>
                    </div>
                </div>
                <span class="text-gray-400 text-sm w-12 text-right">{{ $count }}</span>
            </div>
        @endfor
    </div>
    
    <!-- Botón para valorar (si no ha valorado) -->
    @auth
        @if(!$userRating)
            <div class="mt-6 text-center">
                <p class="text-gray-400 mb-3">¿Has visto este título? ¡Valóralo!</p>
                <x-rating-stars :title-id="$titleId" :show-count="false" size="xl" />
            </div>
        @else
            <div class="mt-6 text-center">
                <p class="text-gray-400 text-sm">Puedes cambiar tu valoración en cualquier momento</p>
            </div>
        @endif
    @endauth
    
    @guest
        <div class="mt-6 text-center">
            <p class="text-gray-400">
                <a href="{{ route('login') }}" class="text-red-500 hover:underline">Inicia sesión</a> 
                para dejar tu valoración
            </p>
        </div>
    @endguest
</div>