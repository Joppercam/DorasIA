@extends('layouts.app')

@section('title', 'Calificaciones de ' . $user->name)

@section('content')
<div style="padding-top: 120px; min-height: 100vh;">
    <div class="content-section">
        <div class="profile-header" style="margin-bottom: 2rem;">
            <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1rem;">
                @if($user->avatar)
                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" 
                         style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover; border: 2px solid rgba(0, 212, 255, 0.5);">
                @else
                    <div style="width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%); display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 1.5rem;">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                @endif
                <div>
                    <h1 style="color: white; margin: 0; font-size: 1.8rem;">Calificaciones de {{ $user->name }}</h1>
                    <p style="color: #ccc; margin: 0.5rem 0 0 0;">{{ $ratings->total() }} calificaciones en total</p>
                </div>
            </div>
            
            <div style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                <a href="{{ route('profile.show', $user) }}" style="color: #ccc; text-decoration: none; padding: 0.5rem 1rem; border-radius: 20px; transition: all 0.3s;">
                    📊 Perfil
                </a>
                <a href="{{ route('profile.watchlist', $user) }}" style="color: #ccc; text-decoration: none; padding: 0.5rem 1rem; border-radius: 20px; transition: all 0.3s;">
                    📺 Lista de Seguimiento
                </a>
                <span style="color: white; background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%); padding: 0.5rem 1rem; border-radius: 20px; font-weight: 600;">
                    ⭐ Calificaciones
                </span>
            </div>
        </div>

        @if($ratings->count() > 0)
            <div class="ratings-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;">
                @foreach($ratings as $rating)
                    <div class="rating-card" style="background: rgba(20,20,20,0.8); border-radius: 12px; overflow: hidden; border: 1px solid rgba(255,255,255,0.1); transition: all 0.3s ease;">
                        <div style="display: flex; gap: 1rem; padding: 1rem;">
                            @if($rating->series->poster_path)
                                <img src="https://image.tmdb.org/t/p/w92{{ $rating->series->poster_path }}" 
                                     alt="{{ $rating->series->title_es }}"
                                     style="width: 60px; height: 90px; border-radius: 6px; object-fit: cover; flex-shrink: 0;">
                            @else
                                <div style="width: 60px; height: 90px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 6px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.5rem; flex-shrink: 0;">
                                    📺
                                </div>
                            @endif
                            
                            <div style="flex: 1; min-width: 0;">
                                <h3 style="color: white; margin: 0 0 0.5rem 0; font-size: 1rem; line-height: 1.3;">
                                    <a href="{{ route('series.show', $rating->series) }}" style="color: white; text-decoration: none;">
                                        {{ $rating->series->title_es ?? $rating->series->title }}
                                    </a>
                                </h3>
                                
                                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                                    <div class="rating-display" style="display: flex; align-items: center; gap: 0.3rem;">
                                        @if($rating->rating_type === 'love')
                                            <span style="color: #ff69b4; font-size: 1.2rem;">❤️</span>
                                            <span style="color: #ff69b4; font-weight: 600;">Me Encanta</span>
                                        @elseif($rating->rating_type === 'like')
                                            <span style="color: #00d4ff; font-size: 1.2rem;">👍</span>
                                            <span style="color: #00d4ff; font-weight: 600;">Me Gusta</span>
                                        @else
                                            <span style="color: #dc3545; font-size: 1.2rem;">👎</span>
                                            <span style="color: #dc3545; font-weight: 600;">No Me Gusta</span>
                                        @endif
                                        <span style="color: #ffd700; margin-left: 0.5rem;">
                                            ({{ $rating->rating_value }}/5)
                                        </span>
                                    </div>
                                </div>
                                
                                <div style="color: #999; font-size: 0.8rem;">
                                    Calificado el {{ $rating->created_at->format('d/m/Y') }}
                                </div>
                                
                                @if($rating->series->year)
                                    <div style="color: #ccc; font-size: 0.8rem; margin-top: 0.3rem;">
                                        Año: {{ $rating->series->year }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Paginación -->
            @if($ratings->hasPages())
                <div style="margin-top: 2rem; display: flex; justify-content: center;">
                    <div style="background: rgba(20,20,20,0.8); border-radius: 25px; padding: 0.5rem 1rem; border: 1px solid rgba(255,255,255,0.1);">
                        {{ $ratings->links() }}
                    </div>
                </div>
            @endif
        @else
            <div class="empty-state" style="text-align: center; padding: 3rem; background: rgba(20,20,20,0.5); border-radius: 12px; border: 1px solid rgba(255,255,255,0.1);">
                <div style="font-size: 3rem; margin-bottom: 1rem;">⭐</div>
                <h3 style="color: white; margin-bottom: 1rem;">No hay calificaciones aún</h3>
                <p style="color: #ccc; margin-bottom: 1.5rem;">
                    @if(Auth::id() === $user->id)
                        ¡Comienza a calificar series haciendo hover sobre las cards y seleccionando tu calificación!
                    @else
                        {{ $user->name }} no ha calificado ninguna serie aún.
                    @endif
                </p>
                @if(Auth::id() === $user->id)
                    <a href="{{ route('home') }}" style="background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%); color: white; padding: 0.8rem 1.5rem; border-radius: 25px; text-decoration: none; font-weight: 600; transition: all 0.3s;">
                        Explorar Series
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>

<style>
.rating-card:hover {
    transform: translateY(-5px);
    border-color: rgba(0, 212, 255, 0.3);
    background: rgba(30,30,30,0.9);
}

.profile-header a:hover {
    background: rgba(255,255,255,0.1);
    color: white;
}

@media (max-width: 768px) {
    .ratings-grid {
        grid-template-columns: 1fr;
    }
    
    .content-section {
        padding: 0 1rem;
    }
}
</style>
@endsection