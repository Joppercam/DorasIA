// En resources/views/user/profile.blade.php

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Mi Perfil</h4>
                    <a href="{{ route('profile.edit') }}" class="btn btn-primary btn-sm">Editar Perfil</a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="text-center mb-4">
                        <img src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('images/default-avatar.png') }}" 
                             alt="{{ $user->name }}" class="rounded-circle img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;">
                        <h3 class="mt-3">{{ $user->name }}</h3>
                        @if ($user->username)
                            <p class="text-muted">{{ '@' . $user->username }}</p>
                        @endif
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Información Básica</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="text-muted">Correo:</span>
                                    <span>{{ $user->email }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="text-muted">Miembro desde:</span>
                                    <span>{{ $user->created_at->format('d/m/Y') }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between">
                                    <span class="text-muted">Último acceso:</span>
                                    <span>{{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'N/A' }}</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5>Información Adicional</h5>
                            <ul class="list-group list-group-flush">
                                @if ($user->profile)
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span class="text-muted">Ubicación:</span>
                                        <span>{{ $user->profile->location ?? 'No especificada' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span class="text-muted">País:</span>
                                        <span>{{ $user->profile->country->name ?? 'No especificado' }}</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between">
                                        <span class="text-muted">Sitio web:</span>
                                        <span>{{ $user->profile->website ?? 'No especificado' }}</span>
                                    </li>
                                @else
                                    <li class="list-group-item text-center text-muted">
                                        No hay información adicional disponible
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>

                    @if ($user->profile && $user->profile->bio)
                        <div class="mb-4">
                            <h5>Biografía</h5>
                            <div class="card">
                                <div class="card-body">
                                    {{ $user->profile->bio }}
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Preferencias</h5>
                            <a href="{{ route('profile.preferences') }}" class="btn btn-outline-primary btn-sm">Gestionar Preferencias</a>
                        </div>
                        
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>Preferencias de Contenido</h6>
                                        <ul class="list-unstyled">
                                            <li>
                                                <i class="bi bi-globe"></i> 
                                                Idioma: {{ $user->preferences ? $user->preferences->content_language : 'es' }}
                                            </li>
                                            <li>
                                                <i class="bi bi-moon-stars{{ $user->preferences && $user->preferences->dark_mode ? '-fill' : '' }}"></i> 
                                                Tema: {{ $user->preferences && $user->preferences->dark_mode ? 'Oscuro' : 'Claro' }}
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <h6>Notificaciones</h6>
                                        <ul class="list-unstyled">
                                            <li>
                                                <i class="bi bi-envelope{{ $user->preferences && $user->preferences->email_notifications ? '-fill' : '' }}"></i>
                                                Correo: {{ $user->preferences && $user->preferences->email_notifications ? 'Activadas' : 'Desactivadas' }}
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection