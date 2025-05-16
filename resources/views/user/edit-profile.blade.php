// En resources/views/user/edit-profile.blade.php

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Editar Perfil</h4>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="text-center mb-4">
                            <div class="avatar-upload">
                                <div class="avatar-preview">
                                    <img id="avatar-preview" src="{{ $user->avatar ? asset('storage/' . $user->avatar) : asset('images/default-avatar.png') }}" 
                                        alt="{{ $user->name }}" class="rounded-circle img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;">
                                </div>
                                <div class="mt-2">
                                    <label for="avatar" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-image"></i> Cambiar Imagen
                                    </label>
                                    <input type="file" id="avatar" name="avatar" class="d-none" onchange="previewAvatar(this)">
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label for="username" class="form-label">Nombre de usuario</label>
                                <div class="input-group">
                                    <span class="input-group-text">@</span>
                                    <input type="text" class="form-control" id="username" name="username" value="{{ old('username', $user->username) }}">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="bio" class="form-label">Biografía</label>
                            <textarea class="form-control" id="bio" name="bio" rows="4">{{ old('bio', $user->profile->bio ?? '') }}</textarea>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="location" class="form-label">Ubicación</label>
                                <input type="text" class="form-control" id="location" name="location" value="{{ old('location', $user->profile->location ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label for="country_id" class="form-label">País</label>
                                <select class="form-select" id="country_id" name="country_id">
                                    <option value="">Selecciona un país</option>
                                    @foreach($countries as $country)
                                        <option value="{{ $country->id }}" {{ (old('country_id', $user->profile->country_id ?? '') == $country->id) ? 'selected' : '' }}>
                                            {{ $country->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="website" class="form-label">Sitio web</label>
                            <input type="url" class="form-control" id="website" name="website" value="{{ old('website', $user->profile->website ?? '') }}">
                        </div>

                        <div class="mb-3">
                            <label for="birth_date" class="form-label">Fecha de nacimiento</label>
                            <input type="date" class="form-control" id="birth_date" name="birth_date" value="{{ old('birth_date', $user->profile->birth_date ? $user->profile->birth_date->format('Y-m-d') : '') }}">
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function previewAvatar(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function(e) {
                document.getElementById('avatar-preview').src = e.target.result;
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection