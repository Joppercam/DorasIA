@extends('layouts.admin')

@section('title', 'Gestión de Usuarios')
@section('page-title', 'Gestión de Usuarios')

@section('content')
<div class="space-y-6">
    <!-- Header con filtros -->
    <div class="bg-gray-800 rounded-lg border border-gray-700 p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-4">
            <h3 class="text-lg font-semibold text-white mb-4 lg:mb-0">Filtrar Usuarios</h3>
            <a href="{{ route('admin.users') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                <i data-feather="refresh-cw" class="w-4 h-4 mr-2"></i>
                Limpiar Filtros
            </a>
        </div>
        
        <form method="GET" action="{{ route('admin.users') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-300 mb-2">Buscar</label>
                <input type="text" 
                       id="search" 
                       name="search" 
                       value="{{ request('search') }}"
                       placeholder="Nombre, email..."
                       class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            
            <div>
                <label for="is_admin" class="block text-sm font-medium text-gray-300 mb-2">Tipo de Usuario</label>
                <select id="is_admin" 
                        name="is_admin"
                        class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">Todos los usuarios</option>
                    <option value="1" {{ request('is_admin') === '1' ? 'selected' : '' }}>Solo administradores</option>
                    <option value="0" {{ request('is_admin') === '0' ? 'selected' : '' }}>Solo usuarios normales</option>
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
                    Usuarios ({{ $users->total() }} resultados)
                </h3>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Usuario</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Actividad</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Registro</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Último Acceso</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-700 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($user->avatar)
                                        <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full mr-4">
                                    @else
                                        <div class="w-10 h-10 bg-blue-500 rounded-full flex items-center justify-center mr-4">
                                            <span class="text-white text-sm font-medium">{{ substr($user->name, 0, 1) }}</span>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="text-sm font-medium text-white">{{ $user->name }}</div>
                                        <div class="text-sm text-gray-400">{{ $user->email }}</div>
                                        <div class="text-xs text-gray-500">ID: {{ $user->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($user->is_admin)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-900 text-red-200">
                                        <i data-feather="shield" class="w-3 h-3 mr-1"></i>
                                        Administrador
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-900 text-green-200">
                                        <i data-feather="user" class="w-3 h-3 mr-1"></i>
                                        Usuario
                                    </span>
                                @endif
                                
                                @if($user->google_id)
                                    <div class="mt-1">
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-900 text-blue-200">
                                            Google
                                        </span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-white">
                                    <div>⭐ {{ $user->title_ratings_count }} calificaciones</div>
                                    <div class="text-gray-400">📝 {{ $user->comments_count }} comentarios</div>
                                    <div class="text-gray-400">📚 {{ $user->watchlists_count }} en listas</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                {{ $user->created_at->format('d/m/Y') }}
                                <div class="text-xs text-gray-400">{{ $user->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                @if($user->updated_at->gt($user->created_at))
                                    {{ $user->updated_at->format('d/m/Y') }}
                                    <div class="text-xs text-gray-400">{{ $user->updated_at->diffForHumans() }}</div>
                                @else
                                    <span class="text-gray-500">Nunca</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    @if($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.users.toggle-admin', $user) }}" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="text-blue-400 hover:text-blue-300 transition-colors"
                                                    title="{{ $user->is_admin ? 'Quitar admin' : 'Hacer admin' }}">
                                                @if($user->is_admin)
                                                    <i data-feather="shield-off" class="w-4 h-4"></i>
                                                @else
                                                    <i data-feather="shield" class="w-4 h-4"></i>
                                                @endif
                                            </button>
                                        </form>
                                    @endif
                                    
                                    <a href="{{ route('user.profile', $user) }}" 
                                       target="_blank"
                                       class="text-green-400 hover:text-green-300 transition-colors"
                                       title="Ver perfil">
                                        <i data-feather="external-link" class="w-4 h-4"></i>
                                    </a>
                                    
                                    @if($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.users.delete', $user) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="text-red-400 hover:text-red-300 transition-colors"
                                                    title="Eliminar usuario"
                                                    onclick="return confirm('¿Estás seguro de que quieres eliminar al usuario \"{{ $user->name }}\"? Esta acción no se puede deshacer.')">
                                                <i data-feather="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="text-gray-400">
                                    <i data-feather="search" class="w-12 h-12 mx-auto mb-4 opacity-50"></i>
                                    <p class="text-lg font-medium">No se encontraron usuarios</p>
                                    <p class="text-sm">Intenta ajustar los filtros de búsqueda</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-700">
                {{ $users->links() }}
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