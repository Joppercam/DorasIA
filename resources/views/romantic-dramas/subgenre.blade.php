@extends('layouts.app')

@section('title', $subgenreInfo['name'] . ' - Doramas Románticos | Dorasia')

@section('content')
    <!-- Hero Section -->
    <section class="relative py-16 bg-gradient-to-b from-pink-900 to-gray-900">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl font-bold text-white mb-4">{{ $subgenreInfo['name'] }}</h1>
                <p class="text-xl text-gray-300 max-w-3xl mx-auto">
                    Explora nuestra colección de doramas asiáticos de {{ $subgenreInfo['name'] }}.
                </p>
            </div>
        </div>
    </section>

    <!-- Filter Section -->
    <section class="py-6 bg-gray-900">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <form action="{{ route('romantic-dramas.subgenre', $subgenre) }}" method="GET" class="flex flex-wrap items-center gap-4">
                <div class="flex-1 min-w-[200px]">
                    <label for="origin" class="block text-sm font-medium text-gray-400 mb-1">Origen</label>
                    <select name="origin" id="origin" class="bg-gray-800 text-white rounded-md border-gray-700 w-full py-2 px-3">
                        <option value="all" @if(!request('origin') || request('origin') == 'all') selected @endif>Todos</option>
                        <option value="korean" @if(request('origin') == 'korean') selected @endif>Corea</option>
                        <option value="japanese" @if(request('origin') == 'japanese') selected @endif>Japón</option>
                        <option value="chinese" @if(request('origin') == 'chinese') selected @endif>China</option>
                        <option value="thai" @if(request('origin') == 'thai') selected @endif>Tailandia</option>
                    </select>
                </div>
                
                <div class="flex-1 min-w-[200px]">
                    <label for="sort" class="block text-sm font-medium text-gray-400 mb-1">Ordenar por</label>
                    <select name="sort" id="sort" class="bg-gray-800 text-white rounded-md border-gray-700 w-full py-2 px-3">
                        <option value="popularity" @if(!request('sort') || request('sort') == 'popularity') selected @endif>Popularidad</option>
                        <option value="newest" @if(request('sort') == 'newest') selected @endif>Más reciente</option>
                        <option value="rating" @if(request('sort') == 'rating') selected @endif>Mejor calificación</option>
                    </select>
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="bg-pink-700 hover:bg-pink-600 text-white py-2 px-4 rounded-md transition">
                        Filtrar
                    </button>
                </div>
            </form>
        </div>
    </section>

    <!-- Titles Grid -->
    <section class="py-8">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            @if($titles->count() > 0)
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    @foreach($titles as $title)
                        <x-netflix-modern-card :title="$title" />
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="mt-8">
                    {{ $titles->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <h3 class="text-xl text-gray-400 mb-4">No se encontraron doramas románticos en esta categoría</h3>
                    <p class="text-gray-500">Intente con otros filtros o vuelva más tarde</p>
                </div>
            @endif
        </div>
    </section>

    <!-- Related Subgenres -->
    <section class="py-8 bg-gray-900">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-semibold text-white mb-6">Otros Subgéneros Románticos</h2>
            
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                @foreach($relatedSubgenres as $key => $relatedSubgenre)
                    <a href="{{ route('romantic-dramas.subgenre', $key) }}" class="block bg-gradient-to-br from-pink-800 to-purple-900 rounded-lg p-6 text-center hover:from-pink-700 hover:to-purple-800 transition">
                        <h3 class="text-lg font-semibold text-white mb-2">{{ $relatedSubgenre['name'] }}</h3>
                        <p class="text-sm text-gray-300">Explora doramas de {{ $relatedSubgenre['name'] }}</p>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endsection