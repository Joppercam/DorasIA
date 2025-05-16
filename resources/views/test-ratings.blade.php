<x-app-layout>
    <x-slot name="title">Test de Sistema de Valoraciones</x-slot>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-3xl font-bold mb-8">Test de Sistema de Valoraciones</h1>
        
        @php
            // Obtener algunos títulos para probar
            $titles = \App\Models\Title::take(5)->get();
        @endphp
        
        <!-- Test 1: Componente de estrellas simple -->
        <div class="mb-12">
            <h2 class="text-2xl font-semibold mb-4">1. Componente de Estrellas Simple</h2>
            @foreach($titles as $title)
                <div class="mb-4 bg-gray-900 rounded-lg p-4">
                    <h3 class="text-lg font-medium mb-2">{{ $title->title }}</h3>
                    <x-rating-stars :title-id="$title->id" />
                </div>
            @endforeach
        </div>
        
        <!-- Test 2: Componente de estadísticas -->
        <div class="mb-12">
            <h2 class="text-2xl font-semibold mb-4">2. Componente de Estadísticas de Valoración</h2>
            @foreach($titles->take(2) as $title)
                <div class="mb-6">
                    <h3 class="text-lg font-medium mb-2">{{ $title->title }}</h3>
                    <x-rating-statistics :title-id="$title->id" />
                </div>
            @endforeach
        </div>
        
        <!-- Test 3: Diferentes tamaños de estrellas -->
        <div class="mb-12">
            <h2 class="text-2xl font-semibold mb-4">3. Diferentes Tamaños de Estrellas</h2>
            @php $testTitle = $titles->first(); @endphp
            <div class="space-y-4 bg-gray-900 rounded-lg p-4">
                <div>
                    <p class="text-sm text-gray-400 mb-1">Tamaño: sm</p>
                    <x-rating-stars :title-id="$testTitle->id" size="sm" />
                </div>
                <div>
                    <p class="text-sm text-gray-400 mb-1">Tamaño: md (default)</p>
                    <x-rating-stars :title-id="$testTitle->id" size="md" />
                </div>
                <div>
                    <p class="text-sm text-gray-400 mb-1">Tamaño: lg</p>
                    <x-rating-stars :title-id="$testTitle->id" size="lg" />
                </div>
                <div>
                    <p class="text-sm text-gray-400 mb-1">Tamaño: xl</p>
                    <x-rating-stars :title-id="$testTitle->id" size="xl" />
                </div>
            </div>
        </div>
        
        <!-- Test 4: Sin mostrar contador -->
        <div class="mb-12">
            <h2 class="text-2xl font-semibold mb-4">4. Sin Mostrar Contador</h2>
            @foreach($titles->take(3) as $title)
                <div class="mb-4 bg-gray-900 rounded-lg p-4">
                    <h3 class="text-lg font-medium mb-2">{{ $title->title }}</h3>
                    <x-rating-stars :title-id="$title->id" :show-count="false" />
                </div>
            @endforeach
        </div>
        
        <!-- Test 5: Mensaje de estado -->
        <div class="mb-12">
            <h2 class="text-2xl font-semibold mb-4">5. Test de Mensajes de Estado</h2>
            <div class="bg-gray-900 rounded-lg p-4">
                <p class="mb-4">Intenta valorar un título y observa los mensajes de confirmación/error.</p>
                <x-rating-stars :title-id="$titles->first()->id" />
            </div>
        </div>
        
        <!-- Test 6: Estado de autenticación -->
        <div class="mb-12">
            <h2 class="text-2xl font-semibold mb-4">6. Estado de Autenticación</h2>
            <div class="bg-gray-900 rounded-lg p-4">
                @auth
                    <p class="text-green-400 mb-4">✓ Estás autenticado - Puedes valorar títulos</p>
                    @if(auth()->user()->getActiveProfile())
                        <p class="text-green-400 mb-4">✓ Tienes un perfil activo: {{ auth()->user()->getActiveProfile()->name }}</p>
                    @else
                        <p class="text-red-400 mb-4">✗ No tienes un perfil activo</p>
                    @endif
                @else
                    <p class="text-yellow-400 mb-4">⚠ No estás autenticado - Solo puedes ver valoraciones</p>
                @endauth
                <x-rating-stars :title-id="$titles->first()->id" />
            </div>
        </div>
    </div>
</x-app-layout>