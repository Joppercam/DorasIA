<x-app-layout>
    <x-slot name="title">Continuar Viendo</x-slot>
    
    <div class="bg-black border-b border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h1 class="text-3xl font-bold">Continuar Viendo</h1>
            <p class="mt-2 text-gray-400">Retoma donde lo dejaste</p>
        </div>
    </div>
    
    <!-- Lista de títulos en progreso -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if($watchHistory->count() > 0)
            <div class="grid grid-cols-1 gap-4">
                @foreach($watchHistory as $history)
                    @if($history->title)
                        <!-- Película -->
                        <div class="bg-gray-900 rounded-lg overflow-hidden hover:bg-gray-800 transition duration-200">
                            <a href="{{ route('titles.show', $history->title->slug) }}" class="flex flex-col sm:flex-row items-start">
                                <div class="sm:w-48 w-full aspect-video sm:aspect-auto shrink-0 relative">
                                    @if(!empty($history->title->backdrop))
                                        <img src="{{ asset('storage/' . $history->title->backdrop) }}" alt="{{ $history->title->title }}" 
                                            class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-gray-800 flex items-center justify-center">
                                            <span class="text-gray-600">Sin imagen</span>
                                        </div>
                                    @endif
                                    
                                    <!-- Badge de tipo y duración -->
                                    <div class="absolute bottom-2 right-2">
                                        <span class="text-xs bg-black/70 px-1.5 py-0.5 rounded-sm">
                                            {{ $history->title->duration }} min
                                        </span>
                                    </div>
                                    
                                    <!-- Barra de progreso -->
                                    @php
                                        $progressPercent = ($history->watched_seconds / ($history->title->duration * 60)) * 100;
                                        $progressPercent = min(100, $progressPercent); // Asegurar que no exceda el 100%
                                    @endphp
                                    <div class="absolute bottom-0 left-0 right-0 h-1 bg-gray-800">
                                        <div class="h-full bg-red-600" style="width: {{ $progressPercent }}%"></div>
                                    </div>
                                </div>
                                
                                <div class="p-4 flex-1">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="font-semibold text-lg">{{ $history->title->title }}</h3>
                                            <p class="text-gray-400 text-sm">{{ $history->title->release_year }} • Película</p>
                                        </div>
                                        <div class="flex gap-2">
                                            <a href="{{ route('titles.watch', $history->title->slug) }}" class="inline-flex items-center justify-center bg-red-600 hover:bg-red-700 rounded-full p-2 text-white">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path>
                                                </svg>
                                            </a>
                                            <form action="{{ route('watch-history.destroy', $history) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('¿Estás seguro de que quieres eliminar este historial?')" class="inline-flex items-center justify-center bg-gray-800 hover:bg-gray-700 rounded-full p-2 text-white">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    
                                    @php
                                        $remainingMinutes = ceil(($history->title->duration * 60 - $history->watched_seconds) / 60);
                                    @endphp
                                    
                                    <div class="mt-2 text-sm text-gray-400">
                                        @if($history->completed)
                                            <span>Completada</span>
                                        @else
                                            <span>{{ $remainingMinutes }} min restantes</span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        </div>
                    @elseif($history->episode)
                        <!-- Episodio -->
                        <div class="bg-gray-900 rounded-lg overflow-hidden hover:bg-gray-800 transition duration-200">
                            <a href="{{ route('titles.show', $history->episode->season->title->slug) }}" class="flex flex-col sm:flex-row items-start">
                                <div class="sm:w-48 w-full aspect-video sm:aspect-auto shrink-0 relative">
                                    @if(!empty($history->episode->thumbnail))
                                        <img src="{{ asset('storage/' . $history->episode->thumbnail) }}" alt="{{ $history->episode->season->title->title }}" 
                                            class="w-full h-full object-cover">
                                    @elseif(!empty($history->episode->season->title->backdrop))
                                        <img src="{{ asset('storage/' . $history->episode->season->title->backdrop) }}" alt="{{ $history->episode->season->title->title }}" 
                                            class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-gray-800 flex items-center justify-center">
                                            <span class="text-gray-600">Sin imagen</span>
                                        </div>
                                    @endif
                                    
                                    <!-- Badge de tipo y duración -->
                                    <div class="absolute bottom-2 right-2">
                                        <span class="text-xs bg-black/70 px-1.5 py-0.5 rounded-sm">
                                            {{ $history->episode->duration }} min
                                        </span>
                                    </div>
                                    
                                    <!-- Barra de progreso -->
                                    @php
                                        $progressPercent = ($history->watched_seconds / ($history->episode->duration * 60)) * 100;
                                        $progressPercent = min(100, $progressPercent); // Asegurar que no exceda el 100%
                                    @endphp
                                    <div class="absolute bottom-0 left-0 right-0 h-1 bg-gray-800">
                                        <div class="h-full bg-red-600" style="width: {{ $progressPercent }}%"></div>
                                    </div>
                                </div>
                                
                                <div class="p-4 flex-1">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h3 class="font-semibold text-lg">{{ $history->episode->season->title->title }}</h3>
                                            <p class="text-gray-400 text-sm">T{{ $history->episode->season->number }}:E{{ $history->episode->number }} - {{ $history->episode->name ?? '' }}</p>
                                        </div>
                                        <div class="flex gap-2">
                                            <a href="{{ route('titles.watch', [$history->episode->season->title->slug, $history->episode->season->number, $history->episode->number]) }}" class="inline-flex items-center justify-center bg-red-600 hover:bg-red-700 rounded-full p-2 text-white">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path>
                                                </svg>
                                            </a>
                                            <form action="{{ route('watch-history.destroy', $history) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('¿Estás seguro de que quieres eliminar este historial?')" class="inline-flex items-center justify-center bg-gray-800 hover:bg-gray-700 rounded-full p-2 text-white">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    
                                    @php
                                        $remainingMinutes = ceil(($history->episode->duration * 60 - $history->watched_seconds) / 60);
                                    @endphp
                                    
                                    <div class="mt-2 text-sm text-gray-400">
                                        @if($history->completed)
                                            <span>Completado</span>
                                        @else
                                            <span>{{ $remainingMinutes }} min restantes</span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endif
                @endforeach
            </div>
            
            <!-- Paginación -->
            <div class="mt-8">
                {{ $watchHistory->links() }}
            </div>
        @else
            <div class="bg-gray-900 rounded-lg p-8 text-center my-8">
                <svg class="mx-auto h-12 w-12 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="mt-2 text-xl font-semibold">No hay historial de visualización</h3>
                <p class="mt-1 text-gray-400">Comienza a ver títulos para que aparezcan aquí.</p>
                <a href="{{ route('catalog.index') }}" class="mt-4 inline-block bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded text-sm">
                    Explorar catálogo
                </a>
            </div>
        @endif
    </div>
</x-app-layout>