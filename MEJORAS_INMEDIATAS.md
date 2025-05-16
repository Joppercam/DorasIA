# Mejoras Inmediatas para Dorasia

## 🚀 Quick Wins - Implementables en 1-2 días

### 1. Barra de Búsqueda Global con Autocompletado

**Archivo:** `resources/views/layouts/navigation.blade.php`

```blade
<!-- Agregar después del logo -->
<div class="flex-1 max-w-xl mx-4">
    <div x-data="searchBar()" class="relative">
        <input type="text" 
               x-model="query"
               @input.debounce.300ms="search"
               @focus="showResults = true"
               @click.away="showResults = false"
               placeholder="Buscar títulos, actores, géneros..."
               class="w-full bg-gray-800 text-white rounded-full px-4 py-2 pl-10 focus:outline-none focus:ring-2 focus:ring-red-500">
        
        <svg class="absolute left-3 top-2.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
        </svg>
        
        <!-- Resultados -->
        <div x-show="showResults && results.length > 0" 
             class="absolute top-full mt-2 w-full bg-gray-800 rounded-lg shadow-lg overflow-hidden z-50">
            <template x-for="result in results" :key="result.id">
                <a :href="`/titles/${result.slug}`" 
                   class="flex items-center p-3 hover:bg-gray-700 transition">
                    <img :src="result.poster_url" 
                         :alt="result.title"
                         class="w-10 h-14 object-cover rounded mr-3">
                    <div>
                        <div class="font-medium" x-text="result.title"></div>
                        <div class="text-sm text-gray-400" x-text="result.release_year"></div>
                    </div>
                </a>
            </template>
        </div>
    </div>
</div>

<script>
function searchBar() {
    return {
        query: '',
        results: [],
        showResults: false,
        
        async search() {
            if (this.query.length < 2) {
                this.results = [];
                return;
            }
            
            try {
                const response = await fetch(`/api/search?q=${this.query}`);
                this.results = await response.json();
            } catch (error) {
                console.error('Error searching:', error);
            }
        }
    }
}
</script>
```

---

### 2. Modo "Continuar Viendo" en Dashboard

**Archivo:** `resources/views/dashboard.blade.php`

```blade
<!-- Agregar al inicio del dashboard -->
@if($continueWatching->count() > 0)
<section class="mb-8">
    <h2 class="text-2xl font-bold mb-4 flex items-center">
        <i class="fas fa-play-circle mr-3 text-red-500"></i>
        Continuar Viendo
    </h2>
    
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
        @foreach($continueWatching as $item)
            <div class="relative group">
                <a href="{{ route('watch', ['slug' => $item->title->slug, 'startTime' => $item->last_position]) }}">
                    <img src="{{ $item->title->poster_url }}" 
                         alt="{{ $item->title->title }}"
                         class="w-full rounded-lg shadow-lg group-hover:opacity-75 transition">
                    
                    <!-- Barra de progreso -->
                    <div class="absolute bottom-0 left-0 right-0 h-1 bg-gray-700">
                        <div class="h-full bg-red-600" style="width: {{ $item->progress }}%"></div>
                    </div>
                    
                    <!-- Play button overlay -->
                    <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                        <div class="bg-white rounded-full p-3">
                            <svg class="w-8 h-8 text-black" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"/>
                            </svg>
                        </div>
                    </div>
                </a>
                
                <div class="mt-2">
                    <h3 class="font-medium truncate">{{ $item->title->title }}</h3>
                    <p class="text-sm text-gray-400">{{ $item->getProgressDescription() }}</p>
                </div>
            </div>
        @endforeach
    </div>
</section>
@endif
```

---

### 3. Sistema de Notificaciones Toast

**Archivo:** `resources/js/toast.js`

```javascript
class ToastNotification {
    constructor() {
        this.container = this.createContainer();
    }
    
    createContainer() {
        const container = document.createElement('div');
        container.className = 'fixed top-4 right-4 z-50 space-y-2';
        document.body.appendChild(container);
        return container;
    }
    
    show(message, type = 'info', duration = 3000) {
        const toast = document.createElement('div');
        const colors = {
            success: 'bg-green-500',
            error: 'bg-red-500',
            warning: 'bg-yellow-500',
            info: 'bg-blue-500'
        };
        
        toast.className = `${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full`;
        toast.textContent = message;
        
        this.container.appendChild(toast);
        
        // Animar entrada
        setTimeout(() => {
            toast.classList.remove('translate-x-full');
        }, 10);
        
        // Auto eliminar
        setTimeout(() => {
            toast.classList.add('translate-x-full');
            setTimeout(() => toast.remove(), 300);
        }, duration);
    }
}

// Uso global
window.toast = new ToastNotification();

// Ejemplos de uso:
// toast.show('¡Agregado a tu lista!', 'success');
// toast.show('Error al cargar', 'error');
```

---

### 4. Mejora de Cards con Información Hover

**Archivo:** `resources/views/components/enhanced-netflix-card.blade.php`

```blade
@props(['title'])

<div class="group relative" x-data="{ showInfo: false }">
    <!-- Card base -->
    <div class="relative overflow-hidden rounded-lg cursor-pointer"
         @mouseenter="showInfo = true"
         @mouseleave="showInfo = false">
        
        <img src="{{ $title->poster_url }}" 
             alt="{{ $title->title }}"
             class="w-full h-auto transition-transform duration-300 group-hover:scale-110">
        
        <!-- Quick actions overlay -->
        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent 
                    opacity-0 group-hover:opacity-100 transition-opacity duration-300">
            
            <div class="absolute bottom-0 left-0 right-0 p-4">
                <!-- Título y año -->
                <h3 class="font-bold text-lg mb-1">{{ $title->title }}</h3>
                <div class="flex items-center space-x-2 text-sm mb-3">
                    <span>{{ $title->release_year }}</span>
                    <span>•</span>
                    <span>{{ $title->runtime }} min</span>
                    <span>•</span>
                    <div class="flex items-center">
                        <i class="fas fa-star text-yellow-400 mr-1"></i>
                        <span>{{ number_format($title->vote_average / 2, 1) }}</span>
                    </div>
                </div>
                
                <!-- Acciones rápidas -->
                <div class="flex items-center space-x-2">
                    <button class="bg-white text-black p-2 rounded-full hover:bg-gray-200 transition">
                        <i class="fas fa-play"></i>
                    </button>
                    
                    <button @click="toggleWatchlist({{ $title->id }})"
                            class="bg-gray-800 text-white p-2 rounded-full hover:bg-gray-700 transition">
                        <i class="fas fa-plus"></i>
                    </button>
                    
                    <button class="bg-gray-800 text-white p-2 rounded-full hover:bg-gray-700 transition">
                        <i class="fas fa-info"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Extended info tooltip -->
    <div x-show="showInfo"
         x-transition
         class="absolute z-50 left-0 right-0 top-full mt-2 bg-gray-900 rounded-lg p-4 shadow-xl">
        
        <!-- Géneros -->
        <div class="flex flex-wrap gap-1 mb-3">
            @foreach($title->genres->take(3) as $genre)
                <span class="text-xs bg-gray-700 px-2 py-1 rounded">{{ $genre->name }}</span>
            @endforeach
        </div>
        
        <!-- Sinopsis -->
        <p class="text-sm text-gray-300 line-clamp-3 mb-3">
            {{ $title->synopsis }}
        </p>
        
        <!-- Cast principal -->
        <div class="flex items-center space-x-2">
            <span class="text-xs text-gray-500">Con:</span>
            @foreach($title->actors->take(3) as $actor)
                <span class="text-xs text-gray-300">{{ $actor->name }}</span>
                @if(!$loop->last)<span class="text-gray-500">•</span>@endif
            @endforeach
        </div>
    </div>
</div>
```

---

### 5. Filtro Rápido por País en Catálogo

**Archivo:** `resources/views/catalog/index.blade.php`

```blade
<!-- Agregar antes del grid de títulos -->
<div class="mb-6 flex items-center space-x-4 overflow-x-auto pb-2">
    <span class="text-gray-400 text-sm whitespace-nowrap">Filtro rápido:</span>
    
    @foreach(['Corea del Sur' => '🇰🇷', 'Japón' => '🇯🇵', 'China' => '🇨🇳', 'Tailandia' => '🇹🇭', 'Taiwán' => '🇹🇼'] as $country => $flag)
        <a href="{{ route('catalog.index', ['country' => $country]) }}"
           class="flex items-center space-x-2 px-4 py-2 rounded-full border transition
                  {{ request('country') == $country ? 'bg-red-600 border-red-600 text-white' : 'border-gray-600 text-gray-300 hover:border-gray-400' }}">
            <span>{{ $flag }}</span>
            <span>{{ $country }}</span>
        </a>
    @endforeach
    
    @if(request('country'))
        <a href="{{ route('catalog.index') }}"
           class="text-red-500 hover:text-red-400">
            <i class="fas fa-times"></i> Limpiar
        </a>
    @endif
</div>
```

---

### 6. Indicador de Contenido Nuevo

**Archivo:** `resources/views/components/new-badge.blade.php`

```blade
@props(['date'])

@if($date && $date->gt(now()->subDays(7)))
    <div class="absolute top-2 left-2 bg-red-600 text-white text-xs font-bold px-2 py-1 rounded">
        NUEVO
    </div>
@elseif($date && $date->gt(now()->subDays(30)))
    <div class="absolute top-2 left-2 bg-green-600 text-white text-xs font-bold px-2 py-1 rounded">
        RECIENTE
    </div>
@endif
```

Uso en cards:
```blade
<div class="relative">
    <x-new-badge :date="$title->created_at" />
    <img src="{{ $title->poster_url }}" alt="{{ $title->title }}">
</div>
```

---

### 7. Loading States Mejorados

**Archivo:** `resources/views/components/skeleton-card.blade.php`

```blade
<div class="animate-pulse">
    <div class="bg-gray-700 rounded-lg aspect-[2/3] mb-2"></div>
    <div class="space-y-2">
        <div class="h-4 bg-gray-700 rounded w-3/4"></div>
        <div class="h-3 bg-gray-700 rounded w-1/2"></div>
    </div>
</div>
```

JavaScript para lazy loading:
```javascript
// Interceptor para cargar contenido dinámicamente
const contentObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const section = entry.target;
            const url = section.dataset.loadUrl;
            
            fetch(url)
                .then(response => response.text())
                .then(html => {
                    section.innerHTML = html;
                    contentObserver.unobserve(section);
                });
        }
    });
}, { rootMargin: '100px' });

// Aplicar a secciones lazy
document.querySelectorAll('[data-lazy-load]').forEach(section => {
    contentObserver.observe(section);
});
```

---

### 8. Shortcuts de Teclado

**Archivo:** `resources/js/keyboard-shortcuts.js`

```javascript
document.addEventListener('DOMContentLoaded', () => {
    const shortcuts = {
        '/': () => document.querySelector('#search-input')?.focus(),
        'h': () => window.location.href = '/',
        'w': () => window.location.href = '/watchlist',
        'p': () => window.location.href = '/profile',
        'Escape': () => {
            // Cerrar modales abiertos
            document.querySelectorAll('[x-data]').forEach(el => {
                if (el.__x && el.__x.$data.open) {
                    el.__x.$data.open = false;
                }
            });
        }
    };
    
    document.addEventListener('keydown', (e) => {
        // Ignorar si está escribiendo
        if (e.target.matches('input, textarea')) return;
        
        const handler = shortcuts[e.key];
        if (handler) {
            e.preventDefault();
            handler();
        }
    });
    
    // Mostrar ayuda con ?
    if (e.key === '?') {
        showShortcutsHelp();
    }
});
```

---

### 9. Mejora en Sistema de Comentarios

**Archivo:** `resources/views/components/comment-thread.blade.php`

```blade
@props(['comment', 'depth' => 0])

<div class="flex space-x-3 {{ $depth > 0 ? 'ml-12' : '' }}">
    <!-- Avatar -->
    <div class="flex-shrink-0">
        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-red-500 to-pink-500 flex items-center justify-center">
            <span class="text-white font-bold">{{ substr($comment->profile->name, 0, 1) }}</span>
        </div>
    </div>
    
    <div class="flex-1">
        <!-- Header -->
        <div class="flex items-center space-x-2 mb-1">
            <span class="font-medium">{{ $comment->profile->name }}</span>
            <span class="text-gray-500 text-sm">{{ $comment->created_at->diffForHumans() }}</span>
            
            @if($comment->created_at->ne($comment->updated_at))
                <span class="text-gray-600 text-xs">(editado)</span>
            @endif
        </div>
        
        <!-- Contenido -->
        <div class="text-gray-300 mb-2">{{ $comment->content }}</div>
        
        <!-- Acciones -->
        <div class="flex items-center space-x-4 text-sm">
            <button class="text-gray-500 hover:text-white flex items-center space-x-1">
                <i class="far fa-thumbs-up"></i>
                <span>{{ $comment->likes_count ?? 0 }}</span>
            </button>
            
            <button @click="replyTo = {{ $comment->id }}" 
                    class="text-gray-500 hover:text-white">
                Responder
            </button>
            
            @can('update', $comment)
                <button class="text-gray-500 hover:text-white">Editar</button>
            @endcan
            
            @can('delete', $comment)
                <button class="text-gray-500 hover:text-red-500">Eliminar</button>
            @endcan
        </div>
        
        <!-- Formulario de respuesta -->
        <div x-show="replyTo === {{ $comment->id }}" 
             x-transition
             class="mt-3">
            <form action="{{ route('comments.reply', $comment) }}" method="POST">
                @csrf
                <textarea name="content" 
                          rows="2"
                          class="w-full bg-gray-800 rounded-lg px-3 py-2 text-sm"
                          placeholder="Escribe tu respuesta..."></textarea>
                <div class="flex justify-end space-x-2 mt-2">
                    <button type="button" 
                            @click="replyTo = null"
                            class="text-gray-500 text-sm">Cancelar</button>
                    <button type="submit" 
                            class="bg-red-600 text-white px-3 py-1 rounded text-sm">Responder</button>
                </div>
            </form>
        </div>
        
        <!-- Respuestas -->
        @if($comment->replies->count() > 0)
            <div class="mt-4 space-y-4">
                @foreach($comment->replies as $reply)
                    <x-comment-thread :comment="$reply" :depth="$depth + 1" />
                @endforeach
            </div>
        @endif
    </div>
</div>
```

---

### 10. Página de Estadísticas Personales

**Archivo:** `resources/views/profile/stats.blade.php`

```blade
<x-app-layout>
    <x-slot name="title">Mis Estadísticas</x-slot>
    
    <div class="max-w-7xl mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-8">Mis Estadísticas de Visualización</h1>
        
        <!-- Resumen general -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-gray-800 rounded-lg p-6 text-center">
                <div class="text-3xl font-bold text-red-500">{{ $stats['total_watched'] }}</div>
                <div class="text-gray-400">Títulos vistos</div>
            </div>
            
            <div class="bg-gray-800 rounded-lg p-6 text-center">
                <div class="text-3xl font-bold text-blue-500">{{ $stats['total_episodes'] }}</div>
                <div class="text-gray-400">Episodios</div>
            </div>
            
            <div class="bg-gray-800 rounded-lg p-6 text-center">
                <div class="text-3xl font-bold text-green-500">{{ $stats['total_hours'] }}</div>
                <div class="text-gray-400">Horas vistas</div>
            </div>
            
            <div class="bg-gray-800 rounded-lg p-6 text-center">
                <div class="text-3xl font-bold text-yellow-500">{{ $stats['current_streak'] }}</div>
                <div class="text-gray-400">Racha actual (días)</div>
            </div>
        </div>
        
        <!-- Géneros favoritos -->
        <div class="bg-gray-800 rounded-lg p-6 mb-8">
            <h2 class="text-xl font-bold mb-4">Géneros Favoritos</h2>
            <div class="space-y-3">
                @foreach($stats['favorite_genres'] as $genre)
                    <div class="flex items-center justify-between">
                        <span>{{ $genre->name }}</span>
                        <div class="flex items-center space-x-2">
                            <div class="w-32 bg-gray-700 rounded-full h-2">
                                <div class="bg-red-600 h-2 rounded-full" 
                                     style="width: {{ $genre->percentage }}%"></div>
                            </div>
                            <span class="text-sm text-gray-400">{{ $genre->count }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        
        <!-- Mapa de actividad (estilo GitHub) -->
        <div class="bg-gray-800 rounded-lg p-6">
            <h2 class="text-xl font-bold mb-4">Actividad de Visualización</h2>
            <div class="grid grid-cols-53 gap-1">
                @foreach($stats['activity_map'] as $day)
                    <div class="w-3 h-3 rounded-sm {{ $day['class'] }}" 
                         title="{{ $day['date'] }}: {{ $day['count'] }} episodios">
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
```

---

## 🎯 Implementación Prioritaria

### Orden sugerido de implementación:

1. **Día 1**
   - Barra de búsqueda global
   - Loading states/skeletons
   - Toast notifications

2. **Día 2**
   - Continuar viendo
   - Filtros rápidos por país
   - Indicadores de contenido nuevo

3. **Día 3**
   - Mejoras en cards con hover
   - Sistema de comentarios mejorado
   - Shortcuts de teclado

4. **Día 4**
   - Página de estadísticas
   - PWA básico
   - Optimizaciones de performance

Cada mejora es independiente y puede implementarse sin afectar otras partes del sistema.