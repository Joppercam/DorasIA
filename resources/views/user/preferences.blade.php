// En resources/views/user/preferences.blade.php

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Preferencias</h4>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('profile.preferences.update') }}">
                        @csrf
                        @method('PUT')

                        <h5 class="mb-3">Preferencias de Contenido</h5>
                        
                        <div class="mb-4">
                            <label class="form-label">Géneros Favoritos</label>
                            <div class="row">
                                @foreach($genres as $genre)
                                    <div class="col-md-4 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="favorite_genres[]" 
                                                id="genre{{ $genre->id }}" value="{{ $genre->id }}"
                                                {{ in_array($genre->id, old('favorite_genres', $user->preferences->favorite_genres ?? [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="genre{{ $genre->id }}">
                                                {{ $genre->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Países Favoritos</label>
                            <div class="row">
                                @foreach($countries as $country)
                                    <div class="col-md-4 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="favorite_countries[]" 
                                                id="country{{ $country->id }}" value="{{ $country->id }}"
                                                {{ in_array($country->id, old('favorite_countries', $user->preferences->favorite_countries ?? [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="country{{ $country->id }}">
                                                {{ $country->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="content_language" class="form-label">Idioma de Contenido</label>
                            <select class="form-select" id="content_language" name="content_language">
                                <option value="es" {{ (old('content_language', $user->preferences->content_language ?? 'es') == 'es') ? 'selected' : '' }}>Español</option>
                                <option value="en" {{ (old('content_language', $user->preferences->content_language ?? '') == 'en') ? 'selected' : '' }}>Inglés</option>
                                <option value="jp" {{ (old('content_language', $user->preferences->content_language ?? '') == 'jp') ? 'selected' : '' }}>Japonés</option>
                                <option value="kr" {{ (old('content_language', $user->preferences->content_language ?? '') == 'kr') ? 'selected' : '' }}>Coreano</option>
                                <option value="cn" {{ (old('content_language', $user->preferences->content_language ?? '') == 'cn') ? 'selected' : '' }}>Chino</option>
                            </select>
                        </div>

                        <h5 class="mb-3">Apariencia</h5>
                        
                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="dark_mode" name="dark_mode" 
                                    {{ old('dark_mode', $user->preferences->dark_mode ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="dark_mode">
                                    Modo Oscuro
                                </label>
                            </div>
                        </div>

                        <h5 class="mb-3">Notificaciones</h5>
                        
                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="email_notifications" name="email_notifications" 
                                    {{ old('email_notifications', $user->preferences->email_notifications ?? true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="email_notifications">
                                    Recibir notificaciones por correo electrónico
                                </label>
                            </div>
                            <div class="form-text">
                                Recibirás actualizaciones sobre nuevos estrenos, novedades y recomendaciones.
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary">Volver al Perfil</a>
                            <button type="submit" class="btn btn-primary">Guardar Preferencias</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection