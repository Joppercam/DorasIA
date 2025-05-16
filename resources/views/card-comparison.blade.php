@extends('layouts.netflix')

@section('title', 'Comparación de Tarjetas')

@section('content')
<div class="min-h-screen bg-gray-900 py-16">
    <div class="max-w-7xl mx-auto px-4">
        <h1 class="text-3xl font-bold text-white mb-8">Comparación de estilos de tarjetas</h1>
        
        <!-- Sample titles for testing -->
        @php
            $sampleTitles = \App\Models\Title::romantic()
                ->take(3)
                ->get();
        @endphp
        
        <!-- Improved Netflix Card Style -->
        <div class="mb-16">
            <h2 class="text-2xl font-semibold text-white mb-6">Opción 1: Estilo Detallado (improved-netflix-card)</h2>
            <p class="text-gray-400 mb-6">Características: Información completa visible, botones de acción, descripción expandida</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($sampleTitles as $title)
                    <x-improved-netflix-card :title="$title" />
                @endforeach
            </div>
        </div>
        
        <!-- Modern Netflix Card Style -->
        <div class="mb-16">
            <h2 class="text-2xl font-semibold text-white mb-6">Opción 2: Estilo Moderno (netflix-modern-card)</h2>
            <p class="text-gray-400 mb-6">Características: Diseño minimalista, información en hover, similar a Netflix actual</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($sampleTitles as $title)
                    <x-netflix-modern-card :title="$title" />
                @endforeach
            </div>
        </div>
        
        <!-- Original Netflix Card Style (for reference) -->
        <div class="mb-16">
            <h2 class="text-2xl font-semibold text-white mb-6">Opción 3: Estilo Original (netflix-card)</h2>
            <p class="text-gray-400 mb-6">Características: Diseño básico actual</p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($sampleTitles as $title)
                    <x-netflix-card :title="$title" />
                @endforeach
            </div>
        </div>
        
        <!-- Comparison Table -->
        <div class="mt-12 bg-gray-800 rounded-lg p-6">
            <h3 class="text-xl font-semibold text-white mb-4">Comparación de características</h3>
            <table class="w-full text-white">
                <thead>
                    <tr class="border-b border-gray-700">
                        <th class="text-left py-2">Característica</th>
                        <th class="text-center py-2">Detallado</th>
                        <th class="text-center py-2">Moderno</th>
                        <th class="text-center py-2">Original</th>
                    </tr>
                </thead>
                <tbody class="text-gray-300">
                    <tr class="border-b border-gray-700">
                        <td class="py-2">Imagen de póster</td>
                        <td class="text-center py-2">✓</td>
                        <td class="text-center py-2">✓</td>
                        <td class="text-center py-2">✓</td>
                    </tr>
                    <tr class="border-b border-gray-700">
                        <td class="py-2">Título y año</td>
                        <td class="text-center py-2">✓</td>
                        <td class="text-center py-2">✓</td>
                        <td class="text-center py-2">✓</td>
                    </tr>
                    <tr class="border-b border-gray-700">
                        <td class="py-2">Calificación</td>
                        <td class="text-center py-2">✓</td>
                        <td class="text-center py-2">✓</td>
                        <td class="text-center py-2">✓</td>
                    </tr>
                    <tr class="border-b border-gray-700">
                        <td class="py-2">Sinopsis</td>
                        <td class="text-center py-2">✓</td>
                        <td class="text-center py-2">En hover</td>
                        <td class="text-center py-2">-</td>
                    </tr>
                    <tr class="border-b border-gray-700">
                        <td class="py-2">Botones de acción</td>
                        <td class="text-center py-2">✓</td>
                        <td class="text-center py-2">En hover</td>
                        <td class="text-center py-2">✓</td>
                    </tr>
                    <tr class="border-b border-gray-700">
                        <td class="py-2">Géneros</td>
                        <td class="text-center py-2">✓</td>
                        <td class="text-center py-2">En hover</td>
                        <td class="text-center py-2">-</td>
                    </tr>
                    <tr class="border-b border-gray-700">
                        <td class="py-2">Plataforma</td>
                        <td class="text-center py-2">✓</td>
                        <td class="text-center py-2">✓</td>
                        <td class="text-center py-2">-</td>
                    </tr>
                    <tr class="border-b border-gray-700">
                        <td class="py-2">Efecto hover</td>
                        <td class="text-center py-2">Básico</td>
                        <td class="text-center py-2">Avanzado</td>
                        <td class="text-center py-2">Básico</td>
                    </tr>
                    <tr class="border-b border-gray-700">
                        <td class="py-2">Diseño móvil</td>
                        <td class="text-center py-2">Adaptable</td>
                        <td class="text-center py-2">Optimizado</td>
                        <td class="text-center py-2">Básico</td>
                    </tr>
                    <tr class="border-b border-gray-700">
                        <td class="py-2">Espacio en pantalla</td>
                        <td class="text-center py-2">Más</td>
                        <td class="text-center py-2">Menos</td>
                        <td class="text-center py-2">Medio</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Recommendation -->
        <div class="mt-12 bg-blue-900 bg-opacity-30 rounded-lg p-6 border border-blue-700">
            <h3 class="text-xl font-semibold text-white mb-4">Recomendación</h3>
            <p class="text-gray-300 mb-4">
                Para la sección de doramas románticos, recomiendo el <strong class="text-white">Estilo Moderno (netflix-modern-card)</strong> por las siguientes razones:
            </p>
            <ul class="list-disc list-inside text-gray-300 space-y-2">
                <li>Diseño más limpio y moderno, similar al actual de Netflix</li>
                <li>Mejor uso del espacio en carrusel horizontales</li>
                <li>Información revelada al hacer hover evita abrumar al usuario</li>
                <li>Mejor experiencia móvil con diseño responsivo</li>
                <li>Carga más rápida al mostrar menos información inicialmente</li>
            </ul>
            <p class="text-gray-300 mt-4">
                Sin embargo, si prefieres mostrar más información de inmediato, el <strong class="text-white">Estilo Detallado</strong> 
                es una excelente opción para páginas de búsqueda o listados donde los usuarios necesitan comparar títulos rápidamente.
            </p>
        </div>
        
        <!-- Action buttons -->
        <div class="mt-8 flex justify-center space-x-4">
            <a href="{{ route('romantic-dramas.index') }}" class="bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 transition">
                Volver a Doramas Románticos
            </a>
        </div>
    </div>
</div>
@endsection