<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Administrar Perfiles') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-center">
                        <h2 class="text-2xl font-semibold">{{ __('Gestiona tus perfiles') }}</h2>
                        <a href="{{ route('user-profiles.create') }}" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded transition duration-300 ease-in-out transform hover:scale-105">
                            {{ __('Crear Perfil') }}
                        </a>
                    </div>
                    
                    @if (session('success'))
                        <div class="bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-300 px-4 py-3 rounded relative mt-4 animate-fadeIn" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif
                    
                    @if (session('error'))
                        <div class="bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-300 px-4 py-3 rounded relative mt-4 animate-fadeIn" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

                    <div class="mt-8 grid sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                        @forelse ($profiles as $profile)
                            <div class="profile-card border rounded-lg p-6 transition duration-300 ease-in-out transform hover:scale-105 {{ $activeProfileId == $profile->id ? 'border-red-500 bg-red-50 dark:bg-red-900/20' : 'hover:shadow-lg dark:border-gray-700' }}">
                                <div class="flex flex-col items-center">
                                    <img src="{{ asset('images/profiles/' . $profile->avatar) }}" 
                                         alt="{{ $profile->name }}" 
                                         class="w-32 h-32 rounded-md object-cover shadow-md transition duration-300 ease-in-out hover:shadow-lg">
                                    
                                    <div class="mt-4 text-center">
                                        <h3 class="text-xl font-semibold">{{ $profile->name }}</h3>
                                        
                                        @if ($activeProfileId == $profile->id)
                                            <span class="inline-block bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-300 text-xs px-2 py-1 rounded mt-2">
                                                {{ __('Perfil Activo') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="mt-6 flex justify-center space-x-4">
                                    <a href="{{ route('user-profiles.edit', $profile) }}" 
                                       class="text-gray-600 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400 transition duration-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    
                                    @if ($profiles->count() > 1)
                                        <form action="{{ route('user-profiles.destroy', $profile) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                   class="text-gray-600 dark:text-gray-300 hover:text-red-600 dark:hover:text-red-400 transition duration-300"
                                                   onclick="return confirm('¿Estás seguro de eliminar este perfil?')">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                                
                                @if ($activeProfileId != $profile->id)
                                    <div class="mt-4 text-center">
                                        <form action="{{ route('user-profiles.set-active', $profile) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white py-2 px-4 rounded transition duration-300 ease-in-out transform hover:scale-105">
                                                {{ __('Usar Perfil') }}
                                            </button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="col-span-4 text-center py-12">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <p class="text-gray-500 dark:text-gray-400 text-xl mt-4">{{ __('No tienes perfiles creados aún.') }}</p>
                                <a href="{{ route('user-profiles.create') }}" class="mt-4 inline-block bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded transition duration-300 ease-in-out transform hover:scale-105">
                                    {{ __('Crea tu primer perfil') }}
                                </a>
                            </div>
                        @endforelse
                    </div>
                    
                    <div class="mt-12 text-center">
                        <a href="{{ route('user-profiles.selector') }}" class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-50 dark:hover:bg-gray-700 transition duration-300 ease-in-out">
                            Volver al selector de perfiles
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fadeIn {
            animation: fadeIn 0.5s ease-out forwards;
        }
        
        .profile-card {
            animation: fadeIn 0.5s ease-out forwards;
            animation-fill-mode: both;
        }
        
        .profile-card:nth-child(1) { animation-delay: 0.1s; }
        .profile-card:nth-child(2) { animation-delay: 0.2s; }
        .profile-card:nth-child(3) { animation-delay: 0.3s; }
        .profile-card:nth-child(4) { animation-delay: 0.4s; }
        .profile-card:nth-child(5) { animation-delay: 0.5s; }
    </style>
</x-app-layout>