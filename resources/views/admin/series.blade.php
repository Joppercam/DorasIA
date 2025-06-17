@extends('layouts.admin')

@section('title', 'Gestión de Series')
@section('page-title', 'Gestión de Series')

@section('content')
<div class="space-y-6">
    <!-- Header con filtros -->
    <div class="bg-gray-800 rounded-lg border border-gray-700 p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-4">
            <h3 class="text-lg font-semibold text-white mb-4 lg:mb-0">Filtrar Series</h3>
            <a href="{{ route('admin.series') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                <i data-feather="refresh-cw" class="w-4 h-4 mr-2"></i>
                Limpiar Filtros
            </a>
        </div>
        
        <form method="GET" action="{{ route('admin.series') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-300 mb-2">Buscar</label>
                <input type="text" 
                       id="search" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Título, título en español..."
                       class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            
            <div>
                <label for="drama_type" class="block text-sm font-medium text-gray-300 mb-2">Tipo de Drama</label>
                <select id="drama_type" 
                        name="drama_type"
                        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Todos los tipos</option>
                    @foreach($dramaTypes as $type)
                        <option value="{{ $type }}" {{ request('drama_type') == $type ? 'selected' : '' }}>
                            {{ ucfirst($type) }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="status" class="block text-sm font-medium text-gray-300 mb-2">Estado</label>
                <select id="status" 
                        name="status"
                        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Todos los estados</option>
                    @foreach($statuses as $statusOption)
                        <option value="{{ $statusOption }}" {{ request('status') == $statusOption ? 'selected' : '' }}>
                            {{ ucfirst($statusOption) }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                    <i data-feather="search" class="w-4 h-4 inline mr-2"></i>
                    Buscar
                </button>
            </div>
        </form>
    </div>
    
    <!-- Resultados -->
    <div class="bg-gray-800 rounded-lg border border-gray-700">
        <div class="p-6 border-b border-gray-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-white">
                    Series ({{ $series->total() }} resultados)
                </h3>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Serie</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Rating</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Episodios</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($series as $serie)
                        <tr class="hover:bg-gray-700 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <img src="{{ $serie->poster_path ? 'https://image.tmdb.org/t/p/w92' . $serie->poster_path : 'https://via.placeholder.com/46x69/333/666?text=No+Image' }}" 
                                         alt="{{ $serie->display_title }}" 
                                         class="w-12 h-18 rounded object-cover mr-4">
                                    <div>
                                        <div class="text-sm font-medium text-white">{{ Str::limit($serie->display_title, 40) }}</div>
                                        @if($serie->title_es && $serie->title_es !== $serie->title)
                                            <div class="text-sm text-gray-400">{{ Str::limit($serie->title, 40) }}</div>
                                        @endif
                                        <div class="text-xs text-gray-500">ID: {{ $serie->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-900 text-blue-200">
                                    {{ $serie->country_flag }} {{ $serie->drama_type_formatted }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    @if($serie->status === 'Ended') bg-gray-900 text-gray-200
                                    @elseif($serie->status === 'Returning Series') bg-green-900 text-green-200
                                    @else bg-yellow-900 text-yellow-200 @endif">
                                    {{ $serie->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-white">
                                    @if($serie->vote_average > 0)
                                        ⭐ {{ number_format($serie->vote_average, 1) }}
                                        <span class="text-gray-400">({{ $serie->vote_count }})</span>
                                    @else
                                        <span class="text-gray-400">Sin rating</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-white">
                                {{ $serie->number_of_episodes ?? 'N/A' }}
                                @if($serie->number_of_seasons)
                                    <span class="text-gray-400">({{ $serie->number_of_seasons }} temp.)</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                {{ $serie->first_air_date ? $serie->first_air_date->format('Y') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('series.show', $serie->id) }}" 
                                       target="_blank"
                                       class="text-blue-400 hover:text-blue-300 transition-colors"
                                       title="Ver en sitio">
                                        <i data-feather="external-link" class="w-4 h-4"></i>
                                    </a>
                                    <a href="{{ route('admin.series.edit', $serie) }}" 
                                       class="text-yellow-400 hover:text-yellow-300 transition-colors"
                                       title="Editar">
                                        <i data-feather="edit" class="w-4 h-4"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.series.delete', $serie) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-red-400 hover:text-red-300 transition-colors"
                                                title="Eliminar"
                                                onclick="return confirm('¿Estás seguro de que quieres eliminar la serie \"{{ $serie->display_title }}\"? Esta acción no se puede deshacer.')">
                                            <i data-feather="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="text-gray-400">
                                    <i data-feather="search" class="w-12 h-12 mx-auto mb-4 opacity-50"></i>
                                    <p class="text-lg font-medium">No se encontraron series</p>
                                    <p class="text-sm">Intenta ajustar los filtros de búsqueda</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($series->hasPages())
            <div class="px-6 py-4 border-t border-gray-700">
                {{ $series->links() }}
            </div>
        @endif
    </div>
</div>

<script>
// Actualizar íconos después de cargar el contenido
document.addEventListener('DOMContentLoaded', function() {
    feather.replace();
});
</script>
@endsection