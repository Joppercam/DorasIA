@props(['titleId', 'currentRating' => null, 'showCount' => true, 'size' => 'md'])

@php
    $profile = auth()->user()?->getActiveProfile();
    $userRating = null;
    $averageRating = 0;
    $totalRatings = 0;
    
    if ($profile) {
        $userRating = \App\Models\Rating::where('profile_id', $profile->id)
            ->where('title_id', $titleId)
            ->first();
    }
    
    $title = \App\Models\Title::find($titleId);
    if ($title) {
        // Convert from 1-10 scale to 1-5 scale for display
        $averageRating = ($title->vote_average ?? 0) / 2;
        $totalRatings = $title->vote_count ?? 0;
    }
    
    // Convert user rating from 1-10 to 1-5 scale
    $displayRating = $currentRating ?? ($userRating ? $userRating->score / 2 : 0);
    
    $sizeClasses = [
        'sm' => 'text-sm',
        'md' => 'text-xl',
        'lg' => 'text-2xl',
        'xl' => 'text-3xl'
    ];
    
    $starSize = $sizeClasses[$size] ?? $sizeClasses['md'];
@endphp

<div class="rating-stars" 
     x-data="{ 
        rating: {{ $displayRating }},
        hoverRating: 0,
        titleId: {{ $titleId }},
        isRating: false,
        userRating: {{ $userRating ? $userRating->score / 2 : 'null' }},
        averageRating: {{ $averageRating }},
        totalRatings: {{ $totalRatings }},
        setRating(score) {
            this.isRating = true;
            fetch(`/titles/${this.titleId}/rate`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                },
                body: JSON.stringify({
                    rating: score
                })
            })
            .then(response => response.json())
            .then(data => {
                this.rating = score;
                this.userRating = score;
                this.averageRating = data.averageRating;
                this.totalRatings = data.totalRatings;
                this.showSuccess();
            })
            .catch(error => {
                console.error('Error:', error);
                this.showError();
            })
            .finally(() => {
                this.isRating = false;
            });
        },
        showSuccess() {
            // Mostrar notificación de éxito
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded shadow-lg z-50';
            notification.textContent = '¡Valoración guardada!';
            document.body.appendChild(notification);
            setTimeout(() => notification.remove(), 3000);
        },
        showError() {
            // Mostrar notificación de error
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-red-500 text-white px-4 py-2 rounded shadow-lg z-50';
            notification.textContent = 'Error al guardar la valoración';
            document.body.appendChild(notification);
            setTimeout(() => notification.remove(), 3000);
        }
     }">
    
    <div class="flex items-center space-x-2">
        <!-- Estrellas interactivas -->
        <div class="flex items-center">
            @auth
                @for($i = 1; $i <= 5; $i++)
                    <button type="button"
                            @click="setRating({{ $i }})"
                            @mouseenter="hoverRating = {{ $i }}"
                            @mouseleave="hoverRating = 0"
                            :disabled="isRating"
                            class="star-button {{ $starSize }} transition-all duration-150 disabled:cursor-not-allowed"
                            :class="{
                                'text-yellow-400': (hoverRating > 0 ? hoverRating : rating) >= {{ $i }},
                                'text-gray-400': (hoverRating > 0 ? hoverRating : rating) < {{ $i }},
                                'hover:scale-110': !isRating,
                                'animate-pulse': isRating
                            }">
                        <i class="fas fa-star"></i>
                    </button>
                @endfor
            @else
                @for($i = 1; $i <= 5; $i++)
                    <span class="{{ $starSize }} {{ $displayRating >= $i ? 'text-yellow-400' : 'text-gray-400' }}">
                        <i class="fas fa-star"></i>
                    </span>
                @endfor
            @endauth
        </div>
        
        <!-- Información de valoración -->
        @if($showCount)
            <div class="text-sm text-gray-400">
                <span x-text="averageRating.toFixed(1)" class="font-bold">{{ number_format($averageRating, 1) }}</span>
                (<span x-text="totalRatings">{{ $totalRatings }}</span> valoraciones)
                @if($userRating)
                    <span class="text-xs ml-2 text-green-400">
                        Tu valoración: <span x-text="userRating">{{ $userRating->score / 2 }}</span>
                    </span>
                @endif
            </div>
        @endif
    </div>
    
    @guest
        <p class="text-xs text-gray-500 mt-1">
            <a href="{{ route('login') }}" class="text-red-500 hover:underline">Inicia sesión</a> para valorar
        </p>
    @endguest
</div>

<style>
.star-button {
    background: none;
    border: none;
    cursor: pointer;
    padding: 0 2px;
}

.star-button:focus {
    outline: none;
}
</style>