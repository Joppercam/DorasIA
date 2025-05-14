<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(isset($profile))
                        <div class="flex items-center mb-4">
                            <img src="{{ asset('images/profiles/' . $profile->avatar) }}" alt="{{ $profile->name }}" class="w-10 h-10 rounded-full mr-3">
                            <span>{{ __("Bienvenido a tu perfil") }}: <strong>{{ $profile->name }}</strong></span>
                        </div>
                        
                        @if(auth()->user()->avatar)
                            <div class="flex items-center mb-4 bg-gray-100 p-3 rounded">
                                <img src="{{ auth()->user()->avatar }}" alt="{{ auth()->user()->name }}" class="w-8 h-8 rounded-full mr-3">
                                <div>
                                    <span class="text-sm text-gray-600">{{ __("Conectado como") }}: <strong>{{ auth()->user()->name }}</strong></span>
                                    @if(auth()->user()->provider)
                                        <span class="block text-xs text-gray-500">{{ __("vía") }} {{ ucfirst(auth()->user()->provider) }}</span>
                                    @endif
                                </div>
                            </div>
                        @endif
                        
                        <p>{{ __('¡Estás conectado con tu perfil y listo para disfrutar del mejor contenido coreano!') }}</p>
                    @else
                        {{ __("You're logged in!") }}
                    @endif
                    
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold mb-3">{{ __('Administra tus perfiles') }}</h3>
                        <a href="{{ route('user-profiles.index') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            {{ __('Gestionar perfiles') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
