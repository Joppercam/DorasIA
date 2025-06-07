@extends('layouts.app')

@section('title', 'Editar Perfil')

@section('content')
<div style="padding-top: 120px; min-height: 100vh;">
    <div class="content-section">
        <div class="profile-edit-container" style="max-width: 600px; margin: 0 auto;">
            <div style="margin-bottom: 2rem;">
                <h1 style="color: white; margin: 0 0 1rem 0; font-size: 2rem; text-align: center;">Editar Perfil</h1>
                <div style="text-align: center;">
                    <a href="{{ route('profile.show') }}" style="color: #00d4ff; text-decoration: none; font-size: 0.9rem;">
                        ← Volver al Perfil
                    </a>
                </div>
            </div>

            @if ($errors->any())
                <div style="background: rgba(220, 53, 69, 0.1); border: 1px solid rgba(220, 53, 69, 0.3); border-radius: 8px; padding: 1rem; margin-bottom: 1.5rem;">
                    <h4 style="color: #dc3545; margin: 0 0 0.5rem 0; font-size: 1rem;">Errores en el formulario:</h4>
                    <ul style="color: #dc3545; margin: 0; padding-left: 1.5rem;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" style="background: rgba(20,20,20,0.8); border-radius: 12px; padding: 2rem; border: 1px solid rgba(255,255,255,0.1);">
                @csrf
                @method('PUT')

                <!-- Información Básica -->
                <div style="margin-bottom: 2rem;">
                    <h3 style="color: white; margin: 0 0 1rem 0; font-size: 1.2rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 0.5rem;">
                        📝 Información Básica
                    </h3>
                    
                    <div style="margin-bottom: 1rem;">
                        <label for="name" style="display: block; color: white; margin-bottom: 0.5rem; font-weight: 600;">Nombre</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                               style="width: 100%; padding: 0.8rem; border: 1px solid rgba(255,255,255,0.2); border-radius: 6px; background: rgba(0,0,0,0.3); color: white; font-size: 1rem;">
                    </div>
                    
                    <div style="margin-bottom: 1rem;">
                        <label for="email" style="display: block; color: white; margin-bottom: 0.5rem; font-weight: 600;">Correo Electrónico</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                               style="width: 100%; padding: 0.8rem; border: 1px solid rgba(255,255,255,0.2); border-radius: 6px; background: rgba(0,0,0,0.3); color: white; font-size: 1rem;">
                    </div>
                    
                    <div style="margin-bottom: 1rem;">
                        <label for="bio" style="display: block; color: white; margin-bottom: 0.5rem; font-weight: 600;">Biografía</label>
                        <textarea id="bio" name="bio" rows="3" placeholder="Cuéntanos sobre ti..."
                                  style="width: 100%; padding: 0.8rem; border: 1px solid rgba(255,255,255,0.2); border-radius: 6px; background: rgba(0,0,0,0.3); color: white; font-size: 1rem; resize: vertical;">{{ old('bio', $profile->bio ?? '') }}</textarea>
                        <small style="color: #ccc; font-size: 0.8rem;">Máximo 500 caracteres</small>
                    </div>
                    
                    <div style="margin-bottom: 1rem;">
                        <label for="location" style="display: block; color: white; margin-bottom: 0.5rem; font-weight: 600;">Ubicación</label>
                        <input type="text" id="location" name="location" value="{{ old('location', $profile->location ?? '') }}" placeholder="Ej: Santiago, Chile"
                               style="width: 100%; padding: 0.8rem; border: 1px solid rgba(255,255,255,0.2); border-radius: 6px; background: rgba(0,0,0,0.3); color: white; font-size: 1rem;">
                    </div>
                </div>

                <!-- Géneros Favoritos -->
                @if($genres->count() > 0)
                <div style="margin-bottom: 2rem;">
                    <h3 style="color: white; margin: 0 0 1rem 0; font-size: 1.2rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 0.5rem;">
                        🎭 Géneros Favoritos
                    </h3>
                    
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 0.5rem;">
                        @php
                            $currentFavorites = $profile && $profile->favorite_genres ? json_decode($profile->favorite_genres, true) : [];
                        @endphp
                        @foreach($genres as $genre)
                            <label style="display: flex; align-items: center; gap: 0.5rem; color: white; cursor: pointer; padding: 0.5rem; border-radius: 6px; transition: background-color 0.3s;">
                                <input type="checkbox" name="favorite_genres[]" value="{{ $genre->id }}" 
                                       {{ in_array($genre->id, old('favorite_genres', $currentFavorites)) ? 'checked' : '' }}
                                       style="margin: 0;">
                                <span>{{ $genre->name_es }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Imágenes -->
                <div style="margin-bottom: 2rem;">
                    <h3 style="color: white; margin: 0 0 1rem 0; font-size: 1.2rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 0.5rem;">
                        🖼️ Imágenes
                    </h3>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <label for="avatar" style="display: block; color: white; margin-bottom: 0.5rem; font-weight: 600;">Avatar</label>
                            @if($user->avatar)
                                <div style="margin-bottom: 0.5rem;">
                                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar actual" 
                                         style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 2px solid rgba(0, 212, 255, 0.5);">
                                </div>
                            @endif
                            <input type="file" id="avatar" name="avatar" accept="image/*"
                                   style="width: 100%; padding: 0.5rem; border: 1px solid rgba(255,255,255,0.2); border-radius: 6px; background: rgba(0,0,0,0.3); color: white;">
                            <small style="color: #ccc; font-size: 0.8rem; display: block; margin-top: 0.3rem;">Máximo 2MB</small>
                        </div>
                        
                        <div>
                            <label for="banner" style="display: block; color: white; margin-bottom: 0.5rem; font-weight: 600;">Banner</label>
                            @if($profile && $profile->banner_path)
                                <div style="margin-bottom: 0.5rem;">
                                    <img src="{{ asset('storage/' . $profile->banner_path) }}" alt="Banner actual" 
                                         style="width: 120px; height: 60px; border-radius: 6px; object-fit: cover; border: 1px solid rgba(255,255,255,0.2);">
                                </div>
                            @endif
                            <input type="file" id="banner" name="banner" accept="image/*"
                                   style="width: 100%; padding: 0.5rem; border: 1px solid rgba(255,255,255,0.2); border-radius: 6px; background: rgba(0,0,0,0.3); color: white;">
                            <small style="color: #ccc; font-size: 0.8rem; display: block; margin-top: 0.3rem;">Máximo 5MB</small>
                        </div>
                    </div>
                </div>

                <!-- Privacidad -->
                <div style="margin-bottom: 2rem;">
                    <h3 style="color: white; margin: 0 0 1rem 0; font-size: 1.2rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 0.5rem;">
                        🔒 Configuración de Privacidad
                    </h3>
                    
                    @php
                        $privacySettings = $profile && $profile->privacy_settings ? json_decode($profile->privacy_settings, true) : [];
                    @endphp
                    
                    <div style="display: flex; flex-direction: column; gap: 0.8rem;">
                        <label style="display: flex; align-items: center; gap: 0.5rem; color: white; cursor: pointer;">
                            <input type="checkbox" name="privacy_show_watchlist" value="1" 
                                   {{ old('privacy_show_watchlist', $privacySettings['show_watchlist'] ?? true) ? 'checked' : '' }}>
                            <span>Mostrar mi lista de seguimiento públicamente</span>
                        </label>
                        
                        <label style="display: flex; align-items: center; gap: 0.5rem; color: white; cursor: pointer;">
                            <input type="checkbox" name="privacy_show_ratings" value="1" 
                                   {{ old('privacy_show_ratings', $privacySettings['show_ratings'] ?? true) ? 'checked' : '' }}>
                            <span>Mostrar mis calificaciones públicamente</span>
                        </label>
                        
                        <label style="display: flex; align-items: center; gap: 0.5rem; color: white; cursor: pointer;">
                            <input type="checkbox" name="privacy_show_comments" value="1" 
                                   {{ old('privacy_show_comments', $privacySettings['show_comments'] ?? true) ? 'checked' : '' }}>
                            <span>Mostrar mis comentarios públicamente</span>
                        </label>
                    </div>
                </div>

                <!-- Cambio de Contraseña -->
                <div style="margin-bottom: 2rem;">
                    <h3 style="color: white; margin: 0 0 1rem 0; font-size: 1.2rem; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 0.5rem;">
                        🔑 Cambiar Contraseña
                    </h3>
                    
                    <div style="margin-bottom: 1rem;">
                        <label for="current_password" style="display: block; color: white; margin-bottom: 0.5rem; font-weight: 600;">Contraseña Actual</label>
                        <input type="password" id="current_password" name="current_password"
                               style="width: 100%; padding: 0.8rem; border: 1px solid rgba(255,255,255,0.2); border-radius: 6px; background: rgba(0,0,0,0.3); color: white; font-size: 1rem;">
                        <small style="color: #ccc; font-size: 0.8rem;">Déjalo vacío si no quieres cambiar la contraseña</small>
                    </div>
                    
                    <div style="margin-bottom: 1rem;">
                        <label for="new_password" style="display: block; color: white; margin-bottom: 0.5rem; font-weight: 600;">Nueva Contraseña</label>
                        <input type="password" id="new_password" name="new_password"
                               style="width: 100%; padding: 0.8rem; border: 1px solid rgba(255,255,255,0.2); border-radius: 6px; background: rgba(0,0,0,0.3); color: white; font-size: 1rem;">
                    </div>
                    
                    <div style="margin-bottom: 1rem;">
                        <label for="new_password_confirmation" style="display: block; color: white; margin-bottom: 0.5rem; font-weight: 600;">Confirmar Nueva Contraseña</label>
                        <input type="password" id="new_password_confirmation" name="new_password_confirmation"
                               style="width: 100%; padding: 0.8rem; border: 1px solid rgba(255,255,255,0.2); border-radius: 6px; background: rgba(0,0,0,0.3); color: white; font-size: 1rem;">
                    </div>
                </div>

                <!-- Botones -->
                <div style="display: flex; gap: 1rem; justify-content: center;">
                    <a href="{{ route('profile.show') }}" 
                       style="background: rgba(108, 117, 125, 0.3); color: white; padding: 0.8rem 1.5rem; border-radius: 25px; text-decoration: none; font-weight: 600; transition: all 0.3s;">
                        Cancelar
                    </a>
                    <button type="submit" 
                            style="background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%); color: white; padding: 0.8rem 1.5rem; border-radius: 25px; border: none; font-weight: 600; cursor: pointer; transition: all 0.3s;">
                        Guardar Cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
input:focus, textarea:focus, select:focus {
    outline: none;
    border-color: rgba(0, 212, 255, 0.5);
    box-shadow: 0 0 0 2px rgba(0, 212, 255, 0.1);
}

label:hover {
    background-color: rgba(255,255,255,0.05);
}

button:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 212, 255, 0.3);
}

@media (max-width: 768px) {
    .content-section {
        padding: 0 1rem;
    }
    
    .profile-edit-container {
        max-width: 100%;
    }
    
    .profile-edit-container form {
        padding: 1.5rem;
    }
    
    div[style*="grid-template-columns: 1fr 1fr"] {
        grid-template-columns: 1fr;
    }
    
    div[style*="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr))"] {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection