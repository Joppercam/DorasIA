@extends('layouts.app')

@section('title', $user->name . ' - Perfil - DORASIA')

@section('content')
<div style="min-height: 100vh; background: linear-gradient(135deg, #141414 0%, #2a2a2a 100%); padding-top: 120px; padding-bottom: 50px;">
    <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
        
        <!-- Profile Header -->
        <div style="background: rgba(20,20,20,0.95); border: 1px solid rgba(0, 212, 255, 0.3); border-radius: 12px; padding: 2rem; margin-bottom: 2rem; backdrop-filter: blur(10px);">
            <div style="display: flex; align-items: center; gap: 2rem; flex-wrap: wrap;">
                
                <!-- Avatar -->
                <div style="width: 120px; height: 120px; border-radius: 50%; background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%); display: flex; align-items: center; justify-content: center; font-size: 3rem; color: white; font-weight: bold; border: 3px solid rgba(255,255,255,0.2);">
                    @if($user->avatar)
                        <img src="{{ $user->avatar }}" alt="{{ $user->name }}" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                    @elseif($profile && $profile->avatar_path)
                        <img src="{{ Storage::url($profile->avatar_path) }}" alt="{{ $user->name }}" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                    @else
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    @endif
                </div>

                <!-- Profile Info -->
                <div style="flex: 1;">
                    <h1 style="color: white; font-size: 2.5rem; font-weight: bold; margin-bottom: 0.5rem;">
                        {{ $user->name }}
                        @if($user->is_admin)
                            <span style="background: linear-gradient(135deg, #ffd700 0%, #ff8c00 100%); color: black; padding: 0.2rem 0.5rem; border-radius: 12px; font-size: 0.7rem; font-weight: 600; margin-left: 1rem;">ADMIN</span>
                        @endif
                    </h1>
                    
                    @if($profile && $profile->bio)
                        <p style="color: #ccc; font-size: 1.1rem; margin-bottom: 1rem; line-height: 1.5;">{{ $profile->bio }}</p>
                    @endif
                    
                    @if($profile && $profile->location)
                        <p style="color: #999; margin-bottom: 1rem;">
                            📍 {{ $profile->location }}
                        </p>
                    @endif

                    <div style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                        <span style="color: #00d4ff; font-weight: 600;">Miembro desde:</span>
                        <span style="color: #ccc;">{{ $user->created_at->format('M Y') }}</span>
                    </div>

                    @if($isOwnProfile)
                        <a href="{{ route('profile.edit') }}" style="display: inline-block; background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%); color: white; padding: 0.5rem 1rem; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.3s ease;">
                            ✏️ Editar Perfil
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
            <div style="background: rgba(0, 212, 255, 0.1); border: 1px solid rgba(0, 212, 255, 0.3); border-radius: 12px; padding: 1.5rem; text-align: center;">
                <div style="font-size: 2rem; font-weight: bold; color: #00d4ff; margin-bottom: 0.5rem;">{{ $stats['series_watched'] ?? 0 }}</div>
                <div style="color: #ccc; font-size: 0.9rem;">Series Vistas</div>
            </div>
            
            <div style="background: rgba(123, 104, 238, 0.1); border: 1px solid rgba(123, 104, 238, 0.3); border-radius: 12px; padding: 1.5rem; text-align: center;">
                <div style="font-size: 2rem; font-weight: bold; color: #7b68ee; margin-bottom: 0.5rem;">{{ $stats['total_episodes'] ?? 0 }}</div>
                <div style="color: #ccc; font-size: 0.9rem;">Episodios Vistos</div>
            </div>
            
            <div style="background: rgba(157, 78, 221, 0.1); border: 1px solid rgba(157, 78, 221, 0.3); border-radius: 12px; padding: 1.5rem; text-align: center;">
                <div style="font-size: 2rem; font-weight: bold; color: #9d4edd; margin-bottom: 0.5rem;">{{ $stats['ratings_given'] ?? 0 }}</div>
                <div style="color: #ccc; font-size: 0.9rem;">Calificaciones</div>
            </div>
            
            <div style="background: rgba(255, 105, 180, 0.1); border: 1px solid rgba(255, 105, 180, 0.3); border-radius: 12px; padding: 1.5rem; text-align: center;">
                <div style="font-size: 2rem; font-weight: bold; color: #ff69b4; margin-bottom: 0.5rem;">{{ $stats['watchlist_items'] ?? 0 }}</div>
                <div style="color: #ccc; font-size: 0.9rem;">En Mi Lista</div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
            
            <!-- Recently Watched -->
            <div style="background: rgba(20,20,20,0.95); border: 1px solid rgba(0, 212, 255, 0.3); border-radius: 12px; padding: 2rem; backdrop-filter: blur(10px);">
                <h2 style="color: white; font-size: 1.5rem; font-weight: 600; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                    📺 Visto Recientemente
                </h2>
                
                @if(isset($recentWatched) && $recentWatched->count() > 0)
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        @foreach($recentWatched->take(3) as $history)
                            <div style="display: flex; align-items: center; gap: 1rem; padding: 0.5rem; border-radius: 8px; background: rgba(40,40,40,0.5);">
                                <div style="width: 60px; height: 80px; background-size: cover; background-position: center; border-radius: 6px; background-image: url('{{ $history->series->poster_path ?? '/fix-poster-1.jpg' }}');">
                                </div>
                                <div style="flex: 1;">
                                    <h3 style="color: white; font-size: 0.9rem; font-weight: 600; margin-bottom: 0.25rem;">{{ $history->series->title ?? 'Serie' }}</h3>
                                    <p style="color: #ccc; font-size: 0.8rem;">Episodio {{ $history->episodes_watched ?? 1 }}</p>
                                    <p style="color: #999; font-size: 0.7rem;">{{ $history->updated_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p style="color: #666; text-align: center; padding: 2rem;">
                        Aún no has visto ninguna serie
                    </p>
                @endif
            </div>

            <!-- Recent Ratings -->
            <div style="background: rgba(20,20,20,0.95); border: 1px solid rgba(123, 104, 238, 0.3); border-radius: 12px; padding: 2rem; backdrop-filter: blur(10px);">
                <h2 style="color: white; font-size: 1.5rem; font-weight: 600; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.5rem;">
                    ⭐ Calificaciones Recientes
                </h2>
                
                @if(isset($recentRatings) && $recentRatings->count() > 0)
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        @foreach($recentRatings->take(3) as $rating)
                            <div style="display: flex; align-items: center; gap: 1rem; padding: 0.5rem; border-radius: 8px; background: rgba(40,40,40,0.5);">
                                <div style="width: 60px; height: 80px; background-size: cover; background-position: center; border-radius: 6px; background-image: url('{{ $rating->series->poster_path ?? '/fix-poster-1.jpg' }}');">
                                </div>
                                <div style="flex: 1;">
                                    <h3 style="color: white; font-size: 0.9rem; font-weight: 600; margin-bottom: 0.25rem;">{{ $rating->series->title ?? 'Serie' }}</h3>
                                    <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.25rem;">
                                        @for($i = 1; $i <= 5; $i++)
                                            <span style="color: {{ $i <= ($rating->rating ?? 0) ? '#ffd700' : '#333' }};">★</span>
                                        @endfor
                                        <span style="color: #ccc; font-size: 0.8rem;">({{ $rating->rating ?? 0 }}/5)</span>
                                    </div>
                                    <p style="color: #999; font-size: 0.7rem;">{{ $rating->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p style="color: #666; text-align: center; padding: 2rem;">
                        Aún no has calificado ninguna serie
                    </p>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        @if($isOwnProfile)
            <div style="margin-top: 2rem; display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                <a href="{{ route('profile.watchlist') }}" style="background: rgba(0, 212, 255, 0.1); border: 1px solid rgba(0, 212, 255, 0.3); color: #00d4ff; padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.3s ease;">
                    📺 Mi Lista de Seguimiento
                </a>
                <a href="{{ route('profile.ratings') }}" style="background: rgba(123, 104, 238, 0.1); border: 1px solid rgba(123, 104, 238, 0.3); color: #7b68ee; padding: 0.75rem 1.5rem; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.3s ease;">
                    ⭐ Mis Calificaciones
                </a>
            </div>
        @endif
    </div>
</div>

<style>
    @media (max-width: 768px) {
        div[style*="grid-template-columns: 1fr 1fr"] {
            grid-template-columns: 1fr !important;
        }
        
        div[style*="display: flex; align-items: center; gap: 2rem"] {
            flex-direction: column !important;
            text-align: center !important;
        }
    }
</style>
@endsection