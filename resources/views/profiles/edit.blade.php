@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto">
        <h1 class="text-3xl font-bold mb-8">Editar Perfil</h1>
        
        <form action="{{ route('profiles.update', $profile) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <!-- Información básica -->
            <div class="bg-gray-900 rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-6">Información Básica</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-medium mb-2">Nombre del Perfil</label>
                        <input type="text" id="name" value="{{ $profile->name }}" disabled
                               class="w-full px-3 py-2 bg-gray-800 text-gray-400 rounded-md cursor-not-allowed">
                        <p class="text-xs text-gray-500 mt-1">El nombre del perfil no se puede cambiar</p>
                    </div>
                    
                    <div>
                        <label for="location" class="block text-sm font-medium mb-2">Ubicación</label>
                        <input type="text" name="location" id="location" value="{{ old('location', $profile->location) }}"
                               class="w-full px-3 py-2 bg-gray-800 text-white rounded-md focus:ring-2 focus:ring-red-500">
                    </div>
                </div>
                
                <div class="mt-6">
                    <label for="bio" class="block text-sm font-medium mb-2">Biografía</label>
                    <textarea name="bio" id="bio" rows="4" 
                              class="w-full px-3 py-2 bg-gray-800 text-white rounded-md focus:ring-2 focus:ring-red-500">{{ old('bio', $profile->bio) }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">Máximo 500 caracteres</p>
                </div>
            </div>
            
            <!-- Preferencias -->
            <div class="bg-gray-900 rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-6">Preferencias</h2>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">Géneros Favoritos</label>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach($genres as $genre)
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="favorite_genres[]" value="{{ $genre->id }}"
                                           {{ in_array($genre->id, $profile->favorite_genres ?? []) ? 'checked' : '' }}
                                           class="rounded bg-gray-800 border-gray-600 text-red-600 focus:ring-red-500">
                                    <span class="text-sm">{{ $genre->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Privacidad -->
            <div class="bg-gray-900 rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-6">Privacidad</h2>
                
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-medium">Perfil Público</h3>
                            <p class="text-sm text-gray-400">Permite que otros usuarios vean tu perfil y actividad</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_public" value="1" 
                                   {{ old('is_public', $profile->is_public) ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                        </label>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-medium">Permitir Mensajes</h3>
                            <p class="text-sm text-gray-400">Permite que otros usuarios te envíen mensajes directos</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="allow_messages" value="1" 
                                   {{ old('allow_messages', $profile->allow_messages) ? 'checked' : '' }}
                                   class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-800 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                        </label>
                    </div>
                </div>
            </div>
            
            <!-- Botones -->
            <div class="flex justify-end space-x-4">
                <a href="{{ route('profiles.show', $profile) }}" 
                   class="px-6 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-md transition">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md transition">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>
@endsection