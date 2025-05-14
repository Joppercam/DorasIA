<x-app-layout>
    <x-slot name="title">Mi Lista</x-slot>
    
    <!-- CSS personalizado para animaciones -->
    <style>
        .dorasia-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .dorasia-card:hover {
            transform: scale(1.03);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.7), 0 4px 6px -2px rgba(0, 0, 0, 0.5);
            z-index: 10;
        }
        
        .grid-view {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 1rem;
        }
        
        .list-view {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .list-view .list-item {
            display: flex;
            background: rgba(20, 20, 20, 0.7);
            border-radius: 0.375rem;
            overflow: hidden;
            transition: background 0.2s ease;
        }
        
        .list-view .list-item:hover {
            background: rgba(30, 30, 30, 0.9);
        }
        
        .list-item .poster {
            width: 80px;
            flex-shrink: 0;
        }
        
        .list-item .content {
            padding: 0.75rem 1rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            flex: 1;
        }
        
        .list-item .title {
            font-weight: bold;
            font-size: 1rem;
        }
        
        .list-item .meta {
            color: #9ca3af;
            font-size: 0.875rem;
        }
        
        .list-item .actions {
            display: flex;
            gap: 0.5rem;
            align-items: center;
        }
        
        .category-badge {
            display: inline-block;
            border-radius: 9999px;
            padding: 0.25rem 0.75rem;
            font-size: 0.75rem;
            font-weight: bold;
            margin-right: 0.5rem;
        }
        
        .category-badge.default { background-color: #4b5563; }
        .category-badge.watch_soon { background-color: #dc2626; }
        .category-badge.watch_later { background-color: #2563eb; }
        .category-badge.favorites { background-color: #7c3aed; }
        .category-badge.in_progress { background-color: #059669; }
        .category-badge.completed { background-color: #0284c7; }
        
        .priority-badge {
            display: inline-block;
            border-radius: 9999px;
            padding: 0.15rem 0.5rem;
            font-size: 0.7rem;
            font-weight: normal;
        }
        
        .priority-badge.high { background-color: #dc2626; }
        .priority-badge.medium { background-color: #d97706; }
        .priority-badge.low { background-color: #2563eb; }
        
        .like-button {
            color: #6b7280;
            transition: color 0.3s ease, transform 0.3s ease;
            cursor: pointer;
        }
        
        .like-button.active {
            color: #ef4444;
        }
        
        .like-button:hover {
            transform: scale(1.2);
        }
        
        .category-indicator {
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
        }
        
        .category-indicator.default { background-color: #4b5563; }
        .category-indicator.watch_soon { background-color: #dc2626; }
        .category-indicator.watch_later { background-color: #2563eb; }
        .category-indicator.favorites { background-color: #7c3aed; }
        .category-indicator.in_progress { background-color: #059669; }
        .category-indicator.completed { background-color: #0284c7; }
        
        .item-enter-active,
        .item-leave-active {
            transition: all 0.5s ease;
        }

        .item-enter-from,
        .item-leave-to {
            opacity: 0;
            transform: translateY(30px);
        }

        .toast-notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #111827;
            color: white;
            border-left: 4px solid #dc2626;
            padding: 1rem;
            border-radius: 0.375rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3);
            z-index: 50;
            max-width: 300px;
            transform: translateY(100px);
            opacity: 0;
            transition: transform 0.3s ease, opacity 0.3s ease;
        }
        
        .toast-notification.show {
            transform: translateY(0);
            opacity: 1;
        }
        
        .sortable-ghost {
            opacity: 0.4;
        }
        
        .sortable-chosen {
            background-color: rgba(30, 30, 30, 0.9);
        }
        
        .notes-popup {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            z-index: 100;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }
        
        .notes-popup.active {
            opacity: 1;
            pointer-events: auto;
        }
        
        .notes-popup .content {
            background-color: #1f2937;
            border-radius: 0.5rem;
            padding: 1.5rem;
            width: 90%;
            max-width: 500px;
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .notes-popup textarea {
            width: 100%;
            min-height: 150px;
            background-color: #111827;
            border: 1px solid #374151;
            color: white;
            border-radius: 0.375rem;
            padding: 0.75rem;
            resize: vertical;
        }
    </style>
    
    <div class="bg-black border-b border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h1 class="text-3xl font-bold">Mi Lista</h1>
            <p class="mt-2 text-gray-400">Organiza tus títulos para ver más tarde</p>
        </div>
    </div>
    
    <!-- Filtros y controles -->
    <div class="bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex flex-col lg:flex-row gap-4 lg:items-center justify-between">
                <!-- Filtros -->
                <div class="flex flex-wrap gap-3 items-center">
                    <!-- Selector de categoría -->
                    <div>
                        <select id="categoryFilter" class="bg-gray-800 text-white border-gray-700 rounded-md text-sm">
                            <option value="all" {{ $currentCategory === 'all' ? 'selected' : '' }}>Todas las categorías</option>
                            @foreach($categories as $key => $name)
                                <option value="{{ $key }}" {{ $currentCategory === $key ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Selector de género -->
                    <div>
                        <select id="genreFilter" class="bg-gray-800 text-white border-gray-700 rounded-md text-sm">
                            <option value="">Todos los géneros</option>
                            @foreach($genres as $genre)
                                <option value="{{ $genre->id }}" {{ $currentGenre == $genre->id ? 'selected' : '' }}>{{ $genre->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Selector de año -->
                    <div>
                        <select id="yearFilter" class="bg-gray-800 text-white border-gray-700 rounded-md text-sm">
                            <option value="">Todos los años</option>
                            @foreach($years as $year)
                                <option value="{{ $year }}" {{ $currentYear == $year ? 'selected' : '' }}>{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Selector de tipo -->
                    <div>
                        <select id="typeFilter" class="bg-gray-800 text-white border-gray-700 rounded-md text-sm">
                            <option value="">Todos los tipos</option>
                            <option value="movie" {{ $currentType === 'movie' ? 'selected' : '' }}>Películas</option>
                            <option value="series" {{ $currentType === 'series' ? 'selected' : '' }}>Series</option>
                        </select>
                    </div>
                    
                    <!-- Selector de prioridad -->
                    <div>
                        <select id="priorityFilter" class="bg-gray-800 text-white border-gray-700 rounded-md text-sm">
                            <option value="">Todas las prioridades</option>
                            @foreach($priorities as $key => $name)
                                <option value="{{ $key }}" {{ $currentPriority === $key ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <!-- Ordenación y Vista -->
                <div class="flex flex-wrap gap-3 items-center">
                    <!-- Selector de ordenación -->
                    <div class="flex items-center gap-2">
                        <select id="sortBy" class="bg-gray-800 text-white border-gray-700 rounded-md text-sm">
                            <option value="position" {{ $currentSortBy === 'position' ? 'selected' : '' }}>Posición</option>
                            <option value="title" {{ $currentSortBy === 'title' ? 'selected' : '' }}>Título</option>
                            <option value="added_date" {{ $currentSortBy === 'added_date' ? 'selected' : '' }}>Fecha de adición</option>
                            <option value="release_year" {{ $currentSortBy === 'release_year' ? 'selected' : '' }}>Año de estreno</option>
                            <option value="priority" {{ $currentSortBy === 'priority' ? 'selected' : '' }}>Prioridad</option>
                        </select>
                        
                        <button id="toggleSortOrder" class="p-1 bg-gray-800 rounded">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 {{ $currentSortOrder === 'desc' ? 'rotate-180' : '' }}" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Toggle vista grid/lista -->
                    <div class="flex items-center bg-gray-800 rounded overflow-hidden">
                        <button id="gridViewBtn" class="p-2 {{ $currentViewType === 'grid' ? 'bg-gray-700' : '' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM11 13a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                        </button>
                        <button id="listViewBtn" class="p-2 {{ $currentViewType === 'list' ? 'bg-gray-700' : '' }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Botón para mostrar/ocultar filtros en móvil -->
                    <button id="toggleFilters" class="lg:hidden p-2 bg-gray-800 rounded">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Grid de títulos -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if($watchlist->count() > 0)
            <div id="watchlistContainer" class="{{ $currentViewType === 'grid' ? 'grid-view' : 'list-view' }}">
                @foreach($watchlist as $item)
                    @if($currentViewType === 'grid')
                        <!-- Vista de cuadrícula -->
                        <div class="dorasia-card" data-id="{{ $item->id }}">
                            <div class="relative pb-[150%] rounded overflow-hidden shadow-lg mb-2 bg-gray-800">
                                <!-- Indicador de categoría -->
                                <div class="category-indicator {{ $item->category }}"></div>
                                
                                @if(!empty($item->title->poster))
                                    <img src="{{ asset('storage/' . $item->title->poster) }}" alt="{{ $item->title->title }}" 
                                         class="absolute inset-0 h-full w-full object-cover">
                                @else
                                    <div class="absolute inset-0 flex items-center justify-center bg-gray-900 text-gray-600">
                                        <span>Sin imagen</span>
                                    </div>
                                @endif
                                
                                <!-- Badge de prioridad -->
                                <div class="absolute top-2 left-2">
                                    <span class="priority-badge {{ $item->priority }}">
                                        {{ $item->priority_name }}
                                    </span>
                                </div>
                                
                                <!-- Badge de tipo -->
                                <div class="absolute top-2 right-2">
                                    <span class="text-xs bg-gray-800 px-1.5 py-0.5 rounded-sm">
                                        {{ $item->title->type === 'movie' ? 'Película' : 'Serie' }}
                                    </span>
                                </div>
                                
                                <!-- Overlay con acciones -->
                                <div class="absolute inset-0 bg-black/70 opacity-0 hover:opacity-100 transition-opacity duration-300 flex flex-col justify-between p-3">
                                    <!-- Info superior -->
                                    <div>
                                        <span class="category-badge {{ $item->category }}">
                                            {{ $item->category_name }}
                                        </span>
                                        
                                        @if($item->notes)
                                            <button class="view-notes p-1 text-sm bg-gray-800 rounded ml-1" data-id="{{ $item->id }}" data-notes="{{ $item->notes }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                    
                                    <!-- Acciones -->
                                    <div class="flex flex-col gap-2">
                                        <a href="{{ $item->title->type === 'movie' 
                                                    ? route('titles.watch', $item->title->slug) 
                                                    : route('titles.show', $item->title->slug) }}" 
                                            class="w-full bg-red-600 hover:bg-red-700 text-white text-center py-1 rounded-sm text-sm">
                                            {{ $item->title->type === 'movie' ? 'Ver ahora' : 'Ver detalles' }}
                                        </a>
                                        
                                        <div class="flex gap-2">
                                            <button class="toggle-like flex-1 bg-gray-800 hover:bg-gray-700 text-white text-center py-1 rounded-sm text-sm flex items-center justify-center"
                                                    data-id="{{ $item->title->id }}" data-liked="{{ $item->liked ? 'true' : 'false' }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 like-button {{ $item->liked ? 'active' : '' }} mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                                                </svg>
                                                <span>Me gusta</span>
                                            </button>
                                            
                                            <button class="edit-notes flex-1 bg-gray-800 hover:bg-gray-700 text-white text-center py-1 rounded-sm text-sm"
                                                    data-id="{{ $item->title->id }}" data-notes="{{ $item->notes }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
                                                    <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd" />
                                                </svg>
                                                Notas
                                            </button>
                                        </div>
                                        
                                        <div class="flex gap-2">
                                            <select class="change-category flex-1 bg-gray-800 hover:bg-gray-700 text-white py-1 rounded-sm text-sm"
                                                    data-id="{{ $item->title->id }}" data-category="{{ $item->category }}">
                                                @foreach($categories as $key => $name)
                                                    <option value="{{ $key }}" {{ $item->category === $key ? 'selected' : '' }}>{{ $name }}</option>
                                                @endforeach
                                            </select>
                                            
                                            <select class="change-priority flex-1 bg-gray-800 hover:bg-gray-700 text-white py-1 rounded-sm text-sm"
                                                    data-id="{{ $item->title->id }}" data-priority="{{ $item->priority }}">
                                                @foreach($priorities as $key => $name)
                                                    <option value="{{ $key }}" {{ $item->priority === $key ? 'selected' : '' }}>{{ $name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <button class="remove-item w-full bg-gray-800 hover:bg-red-700 text-white text-center py-1 rounded-sm text-sm"
                                                data-id="{{ $item->title->id }}">
                                            Quitar de mi lista
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <h3 class="text-sm truncate">{{ $item->title->title }}</h3>
                            <p class="text-xs text-gray-400 truncate">{{ $item->title->release_year }}</p>
                        </div>
                    @else
                        <!-- Vista de lista -->
                        <div class="list-item" data-id="{{ $item->id }}">
                            <div class="category-indicator {{ $item->category }}"></div>
                            
                            <div class="poster">
                                @if(!empty($item->title->poster))
                                    <img src="{{ asset('storage/' . $item->title->poster) }}" alt="{{ $item->title->title }}" 
                                         class="h-full w-full object-cover">
                                @else
                                    <div class="h-full w-full flex items-center justify-center bg-gray-900 text-gray-600">
                                        <span>Sin imagen</span>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="content">
                                <div>
                                    <h3 class="title">{{ $item->title->title }}</h3>
                                    <div class="meta flex items-center flex-wrap gap-y-1">
                                        <span class="mr-2">{{ $item->title->release_year }}</span>
                                        <span class="mr-2">{{ $item->title->type === 'movie' ? 'Película' : 'Serie' }}</span>
                                        <span class="category-badge {{ $item->category }} mr-2">{{ $item->category_name }}</span>
                                        <span class="priority-badge {{ $item->priority }}">{{ $item->priority_name }}</span>
                                        
                                        @if($item->notes)
                                            <button class="view-notes p-1 text-sm bg-gray-800 rounded ml-1" data-id="{{ $item->id }}" data-notes="{{ $item->notes }}">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="actions">
                                    <a href="{{ $item->title->type === 'movie' ? route('titles.watch', $item->title->slug) : route('titles.show', $item->title->slug) }}" 
                                       class="bg-red-600 hover:bg-red-700 text-white text-center px-3 py-1 rounded-sm text-sm">
                                        {{ $item->title->type === 'movie' ? 'Ver ahora' : 'Ver detalles' }}
                                    </a>
                                    
                                    <button class="toggle-like flex items-center justify-center p-2"
                                            data-id="{{ $item->title->id }}" data-liked="{{ $item->liked ? 'true' : 'false' }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 like-button {{ $item->liked ? 'active' : '' }}" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    
                                    <button class="edit-notes p-2"
                                            data-id="{{ $item->title->id }}" data-notes="{{ $item->notes }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M17.414 2.586a2 2 0 00-2.828 0L7 10.172V13h2.828l7.586-7.586a2 2 0 000-2.828z" />
                                            <path fill-rule="evenodd" d="M2 6a2 2 0 012-2h4a1 1 0 010 2H4v10h10v-4a1 1 0 112 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2V6z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                    
                                    <select class="change-category bg-gray-800 hover:bg-gray-700 text-white py-1 rounded-sm text-sm"
                                            data-id="{{ $item->title->id }}" data-category="{{ $item->category }}">
                                        @foreach($categories as $key => $name)
                                            <option value="{{ $key }}" {{ $item->category === $key ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    
                                    <select class="change-priority bg-gray-800 hover:bg-gray-700 text-white py-1 rounded-sm text-sm"
                                            data-id="{{ $item->title->id }}" data-priority="{{ $item->priority }}">
                                        @foreach($priorities as $key => $name)
                                            <option value="{{ $key }}" {{ $item->priority === $key ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                    
                                    <button class="remove-item bg-gray-800 hover:bg-red-700 text-white text-center px-3 py-1 rounded-sm text-sm"
                                            data-id="{{ $item->title->id }}">
                                        Quitar
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
            
            <!-- Paginación -->
            <div class="mt-8">
                {{ $watchlist->links() }}
            </div>
        @else
            <div class="bg-gray-900 rounded-lg p-8 text-center my-8">
                <svg class="mx-auto h-12 w-12 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                <h3 class="mt-2 text-xl font-semibold">Tu lista está vacía</h3>
                <p class="mt-1 text-gray-400">Aún no has agregado títulos a tu lista.</p>
                <a href="{{ route('catalog.index') }}" class="mt-4 inline-block bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm">
                    Explorar catálogo
                </a>
            </div>
        @endif
    </div>
    
    <!-- Popup para notas -->
    <div class="notes-popup" id="notesPopup">
        <div class="content">
            <h3 class="text-xl font-bold mb-4">Notas</h3>
            <input type="hidden" id="notesItemId" value="">
            <textarea id="notesText" class="mb-4" placeholder="Escribe tus notas aquí..."></textarea>
            <div class="flex justify-end gap-2">
                <button id="cancelNotes" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded text-sm">
                    Cancelar
                </button>
                <button id="saveNotes" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm">
                    Guardar
                </button>
            </div>
        </div>
    </div>
    
    <!-- Toast de notificación -->
    <div class="toast-notification" id="toast">
        <div class="flex items-center">
            <svg class="h-6 w-6 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span id="toastMessage">Cambios guardados</span>
        </div>
    </div>
    
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Referencias
            const toast = document.getElementById('toast');
            const toastMessage = document.getElementById('toastMessage');
            const watchlistContainer = document.getElementById('watchlistContainer');
            const sortBySelect = document.getElementById('sortBy');
            const toggleSortOrderBtn = document.getElementById('toggleSortOrder');
            const gridViewBtn = document.getElementById('gridViewBtn');
            const listViewBtn = document.getElementById('listViewBtn');
            const categoryFilter = document.getElementById('categoryFilter');
            const genreFilter = document.getElementById('genreFilter');
            const yearFilter = document.getElementById('yearFilter');
            const typeFilter = document.getElementById('typeFilter');
            const priorityFilter = document.getElementById('priorityFilter');
            const toggleFiltersBtn = document.getElementById('toggleFilters');
            const notesPopup = document.getElementById('notesPopup');
            const notesText = document.getElementById('notesText');
            const notesItemId = document.getElementById('notesItemId');
            const saveNotesBtn = document.getElementById('saveNotes');
            const cancelNotesBtn = document.getElementById('cancelNotes');
            
            // Estado del filtro
            let currentFilters = {
                category: '{{ $currentCategory }}',
                genre: '{{ $currentGenre }}',
                year: '{{ $currentYear }}',
                type: '{{ $currentType }}',
                priority: '{{ $currentPriority }}',
                sort_by: '{{ $currentSortBy }}',
                sort_order: '{{ $currentSortOrder }}',
                view_type: '{{ $currentViewType }}'
            };
            
            // Sortable para drag & drop
            let sortableInstance = null;
            
            // Inicializar Sortable si estamos en modo posición
            if (currentFilters.sort_by === 'position' && currentFilters.category !== 'all') {
                initSortable();
            }
            
            function initSortable() {
                if (sortableInstance) {
                    sortableInstance.destroy();
                }
                
                sortableInstance = new Sortable(watchlistContainer, {
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    chosenClass: 'sortable-chosen',
                    handle: currentFilters.view_type === 'grid' ? '.dorasia-card' : '.list-item',
                    onEnd: function (evt) {
                        const items = Array.from(watchlistContainer.children).map((el, index) => {
                            return {
                                id: el.dataset.id,
                                position: index + 1
                            };
                        });
                        
                        // Actualizar posiciones en el servidor
                        fetch('{{ route('watchlist.batch-update-positions') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ items: items })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                showToast(data.message);
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showToast('Error al reordenar los elementos');
                        });
                    }
                });
            }
            
            // Funciones para mostrar y ocultar el toast
            function showToast(message) {
                toastMessage.textContent = message;
                toast.classList.add('show');
                
                setTimeout(() => {
                    toast.classList.remove('show');
                }, 3000);
            }
            
            // Cambiar a vista de cuadrícula
            gridViewBtn.addEventListener('click', function() {
                watchlistContainer.classList.remove('list-view');
                watchlistContainer.classList.add('grid-view');
                gridViewBtn.classList.add('bg-gray-700');
                listViewBtn.classList.remove('bg-gray-700');
                
                currentFilters.view_type = 'grid';
                updateURL();
                
                // Reiniciar sortable si es necesario
                if (currentFilters.sort_by === 'position' && currentFilters.category !== 'all') {
                    initSortable();
                }
            });
            
            // Cambiar a vista de lista
            listViewBtn.addEventListener('click', function() {
                watchlistContainer.classList.remove('grid-view');
                watchlistContainer.classList.add('list-view');
                listViewBtn.classList.add('bg-gray-700');
                gridViewBtn.classList.remove('bg-gray-700');
                
                currentFilters.view_type = 'list';
                updateURL();
                
                // Reiniciar sortable si es necesario
                if (currentFilters.sort_by === 'position' && currentFilters.category !== 'all') {
                    initSortable();
                }
            });
            
            // Cambiar ordenación
            sortBySelect.addEventListener('change', function() {
                currentFilters.sort_by = this.value;
                updateURL();
                
                // Deshabilitar sortable si no estamos ordenando por posición
                if (this.value === 'position' && currentFilters.category !== 'all') {
                    initSortable();
                } else if (sortableInstance) {
                    sortableInstance.destroy();
                    sortableInstance = null;
                }
            });
            
            // Cambiar dirección de ordenación
            toggleSortOrderBtn.addEventListener('click', function() {
                const icon = this.querySelector('svg');
                
                if (currentFilters.sort_order === 'asc') {
                    currentFilters.sort_order = 'desc';
                    icon.classList.add('rotate-180');
                } else {
                    currentFilters.sort_order = 'asc';
                    icon.classList.remove('rotate-180');
                }
                
                updateURL();
            });
            
            // Filtrar por categoría
            categoryFilter.addEventListener('change', function() {
                currentFilters.category = this.value;
                updateURL();
                
                // Actualizar sortable
                if (currentFilters.sort_by === 'position' && currentFilters.category !== 'all') {
                    initSortable();
                } else if (sortableInstance) {
                    sortableInstance.destroy();
                    sortableInstance = null;
                }
            });
            
            // Filtrar por género
            genreFilter.addEventListener('change', function() {
                currentFilters.genre = this.value;
                updateURL();
            });
            
            // Filtrar por año
            yearFilter.addEventListener('change', function() {
                currentFilters.year = this.value;
                updateURL();
            });
            
            // Filtrar por tipo
            typeFilter.addEventListener('change', function() {
                currentFilters.type = this.value;
                updateURL();
            });
            
            // Filtrar por prioridad
            priorityFilter.addEventListener('change', function() {
                currentFilters.priority = this.value;
                updateURL();
            });
            
            // Actualizar URL con filtros
            function updateURL() {
                const params = new URLSearchParams();
                
                Object.keys(currentFilters).forEach(key => {
                    if (currentFilters[key]) {
                        params.set(key, currentFilters[key]);
                    }
                });
                
                const newURL = `${window.location.pathname}?${params.toString()}`;
                window.location.href = newURL;
            }
            
            // Delegación de eventos para botones
            document.addEventListener('click', function(e) {
                // Botón de me gusta
                if (e.target.closest('.toggle-like')) {
                    const button = e.target.closest('.toggle-like');
                    const titleId = button.dataset.id;
                    const likeIcon = button.querySelector('.like-button');
                    
                    // Optimistic UI update
                    likeIcon.classList.toggle('active');
                    
                    // Actualizar en el servidor
                    fetch('{{ route('watchlist.toggle-like') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ title_id: titleId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            showToast(data.message);
                            button.dataset.liked = data.liked ? 'true' : 'false';
                        } else {
                            // Revertir cambio visual si hubo error
                            likeIcon.classList.toggle('active');
                            showToast('Error al actualizar preferencia');
                        }
                    })
                    .catch(error => {
                        // Revertir cambio visual si hubo error
                        likeIcon.classList.toggle('active');
                        showToast('Error al actualizar preferencia');
                    });
                }
                
                // Botón de editar notas
                if (e.target.closest('.edit-notes')) {
                    const button = e.target.closest('.edit-notes');
                    const titleId = button.dataset.id;
                    const notes = button.dataset.notes || '';
                    
                    // Abrir popup de notas
                    notesItemId.value = titleId;
                    notesText.value = notes;
                    notesPopup.classList.add('active');
                }
                
                // Botón de ver notas
                if (e.target.closest('.view-notes')) {
                    const button = e.target.closest('.view-notes');
                    const notes = button.dataset.notes || '';
                    
                    // Mostrar notas en modo de solo lectura
                    notesItemId.value = '';
                    notesText.value = notes;
                    notesText.disabled = true;
                    saveNotesBtn.style.display = 'none';
                    notesPopup.classList.add('active');
                }
                
                // Botón de remover ítem
                if (e.target.closest('.remove-item')) {
                    const button = e.target.closest('.remove-item');
                    const titleId = button.dataset.id;
                    const itemElement = button.closest('.dorasia-card') || button.closest('.list-item');
                    
                    // Confirmar eliminación
                    if (confirm('¿Estás seguro de que deseas eliminar este título de tu lista?')) {
                        // Optimistic UI update
                        itemElement.style.opacity = '0.5';
                        
                        // Eliminar en el servidor
                        fetch(`{{ url('watchlist') }}/${titleId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'removed') {
                                // Animación de salida
                                itemElement.style.transition = 'all 0.5s ease';
                                itemElement.style.opacity = '0';
                                itemElement.style.transform = 'translateY(30px)';
                                
                                setTimeout(() => {
                                    itemElement.remove();
                                    showToast(data.message);
                                    
                                    // Si no quedan elementos, recargar la página
                                    if (watchlistContainer.children.length === 0) {
                                        window.location.reload();
                                    }
                                }, 500);
                            } else {
                                // Revertir cambio visual si hubo error
                                itemElement.style.opacity = '1';
                                showToast('Error al eliminar el título');
                            }
                        })
                        .catch(error => {
                            // Revertir cambio visual si hubo error
                            itemElement.style.opacity = '1';
                            showToast('Error al eliminar el título');
                        });
                    }
                }
            });
            
            // Cambios de categoría
            document.querySelectorAll('.change-category').forEach(select => {
                select.addEventListener('change', function() {
                    const titleId = this.dataset.id;
                    const newCategory = this.value;
                    const oldCategory = this.dataset.category;
                    const itemElement = this.closest('.dorasia-card') || this.closest('.list-item');
                    
                    // Optimistic UI update
                    const categoryIndicator = itemElement.querySelector('.category-indicator');
                    if (categoryIndicator) {
                        categoryIndicator.classList.remove(oldCategory);
                        categoryIndicator.classList.add(newCategory);
                    }
                    
                    this.dataset.category = newCategory;
                    
                    // Actualizar en el servidor
                    fetch('{{ route('watchlist.update-category') }}', {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ title_id: titleId, category: newCategory })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            showToast(data.message);
                            
                            // Si cambiamos la categoría y estamos filtrando por categoría, recargar
                            if (currentFilters.category !== 'all' && currentFilters.category !== newCategory) {
                                window.location.reload();
                            }
                        } else {
                            // Revertir cambio visual si hubo error
                            categoryIndicator.classList.remove(newCategory);
                            categoryIndicator.classList.add(oldCategory);
                            this.value = oldCategory;
                            this.dataset.category = oldCategory;
                            showToast('Error al actualizar categoría');
                        }
                    })
                    .catch(error => {
                        // Revertir cambio visual si hubo error
                        categoryIndicator.classList.remove(newCategory);
                        categoryIndicator.classList.add(oldCategory);
                        this.value = oldCategory;
                        this.dataset.category = oldCategory;
                        showToast('Error al actualizar categoría');
                    });
                });
            });
            
            // Cambios de prioridad
            document.querySelectorAll('.change-priority').forEach(select => {
                select.addEventListener('change', function() {
                    const titleId = this.dataset.id;
                    const newPriority = this.value;
                    const oldPriority = this.dataset.priority;
                    const itemElement = this.closest('.dorasia-card') || this.closest('.list-item');
                    
                    // Optimistic UI update
                    const priorityBadge = itemElement.querySelector('.priority-badge');
                    if (priorityBadge) {
                        priorityBadge.classList.remove(oldPriority);
                        priorityBadge.classList.add(newPriority);
                    }
                    
                    this.dataset.priority = newPriority;
                    
                    // Actualizar en el servidor
                    fetch('{{ route('watchlist.update-priority') }}', {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ title_id: titleId, priority: newPriority })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            showToast(data.message);
                            
                            // Actualizar texto del badge si es necesario
                            if (priorityBadge) {
                                priorityBadge.textContent = data.priority_name;
                            }
                            
                            // Si estamos filtrando por prioridad, recargar
                            if (currentFilters.priority && currentFilters.priority !== newPriority) {
                                window.location.reload();
                            }
                        } else {
                            // Revertir cambio visual si hubo error
                            priorityBadge.classList.remove(newPriority);
                            priorityBadge.classList.add(oldPriority);
                            this.value = oldPriority;
                            this.dataset.priority = oldPriority;
                            showToast('Error al actualizar prioridad');
                        }
                    })
                    .catch(error => {
                        // Revertir cambio visual si hubo error
                        priorityBadge.classList.remove(newPriority);
                        priorityBadge.classList.add(oldPriority);
                        this.value = oldPriority;
                        this.dataset.priority = oldPriority;
                        showToast('Error al actualizar prioridad');
                    });
                });
            });
            
            // Gestión de popup de notas
            saveNotesBtn.addEventListener('click', function() {
                const titleId = notesItemId.value;
                const notes = notesText.value;
                
                // Cerrar popup
                notesPopup.classList.remove('active');
                
                // Actualizar en el servidor
                fetch('{{ route('watchlist.update-notes') }}', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ title_id: titleId, notes: notes })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        showToast(data.message);
                        
                        // Actualizar datos en los botones
                        document.querySelectorAll(`.edit-notes[data-id="${titleId}"]`).forEach(button => {
                            button.dataset.notes = notes;
                        });
                        
                        // Mostrar u ocultar ícono de notas
                        document.querySelectorAll(`.view-notes[data-id]`).forEach(button => {
                            const itemElement = button.closest('.dorasia-card') || button.closest('.list-item');
                            if (!notes || notes.trim() === '') {
                                button.remove();
                            } else {
                                button.dataset.notes = notes;
                            }
                        });
                        
                        // Si no hay icono de notas y ahora hay notas, agregarlo
                        if (notes && notes.trim() !== '') {
                            document.querySelectorAll(`.edit-notes[data-id="${titleId}"]`).forEach(button => {
                                const itemElement = button.closest('.dorasia-card') || button.closest('.list-item');
                                const hasViewNotesButton = itemElement.querySelector('.view-notes');
                                
                                if (!hasViewNotesButton) {
                                    // Crear botón de ver notas
                                    const viewNotesButton = document.createElement('button');
                                    viewNotesButton.className = 'view-notes p-1 text-sm bg-gray-800 rounded ml-1';
                                    viewNotesButton.dataset.id = titleId;
                                    viewNotesButton.dataset.notes = notes;
                                    viewNotesButton.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>`;
                                    
                                    // Agregar a la UI
                                    if (currentFilters.view_type === 'grid') {
                                        const infoDiv = itemElement.querySelector('.absolute.inset-0 > div:first-child');
                                        if (infoDiv) {
                                            infoDiv.appendChild(viewNotesButton);
                                        }
                                    } else {
                                        const metaDiv = itemElement.querySelector('.meta');
                                        if (metaDiv) {
                                            metaDiv.appendChild(viewNotesButton);
                                        }
                                    }
                                }
                            });
                        }
                    } else {
                        showToast('Error al actualizar notas');
                    }
                })
                .catch(error => {
                    showToast('Error al actualizar notas');
                });
            });
            
            cancelNotesBtn.addEventListener('click', function() {
                // Cerrar popup
                notesPopup.classList.remove('active');
                
                // Resetear estado
                notesItemId.value = '';
                notesText.value = '';
                notesText.disabled = false;
                saveNotesBtn.style.display = 'block';
            });
        });
    </script>
    @endpush
</x-app-layout>