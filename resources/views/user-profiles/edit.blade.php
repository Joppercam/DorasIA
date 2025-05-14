<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar Perfil') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="mb-4">
                        <a href="{{ route('user-profiles.index') }}" class="text-blue-500 hover:text-blue-700">
                            <i class="fas fa-arrow-left mr-2"></i>{{ __('Volver a la lista de perfiles') }}
                        </a>
                    </div>

                    <h2 class="text-lg font-semibold mb-6">{{ __('Editar perfil: ') . $profile->name }}</h2>

                    <form action="{{ route('user-profiles.update', $profile) }}" method="POST" class="max-w-md">
                        @csrf
                        @method('PUT')

                        <div class="mb-6">
                            <label for="name" class="block text-gray-700 font-semibold mb-2">
                                {{ __('Nombre del perfil') }}
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name', $profile->name) }}" 
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-200"
                                required autofocus>
                                
                            @error('name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                {{ __('Actualizar Perfil') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>