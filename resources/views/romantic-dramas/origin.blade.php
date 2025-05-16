@extends('layouts.app')

@section('title', 'Doramas Románticos ' . $originLabel . ' | Dorasia')

@section('content')
    <!-- Hero Section -->
    <section class="relative py-16 bg-cover bg-center" style="background-image: url('{{ asset('images/heroes/' . $origin . '.jpg') }}');">
        <div class="absolute inset-0 bg-black bg-opacity-70"></div>
        <div class="container mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center">
                <h1 class="text-4xl font-bold text-white mb-4">Doramas Románticos {{ $originLabel }}s</h1>
                <p class="text-xl text-gray-300 max-w-3xl mx-auto">
                    Descubre los mejores doramas románticos de {{ $originLabel }}, desde clásicos hasta los estrenos más recientes.
                </p>
            </div>
        </div>
    </section>

    <!-- Filter Section -->
    <section class="py-6 bg-gray-900">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <form action="{{ route('romantic-dramas.origin', $origin) }}" method="GET" class="flex flex-wrap items-center gap-4">
                <div class="flex-1 min-w-[200px]">
                    <label for="subgenre" class="block text-sm font-medium text-gray-400 mb-1">Subgénero</label>
                    <select name="subgenre" id="subgenre" class="bg-gray-800 text-white rounded-md border-gray-700 w-full py-2 px-3">
                        <option value="all" @if(!request('subgenre') || request('subgenre') == 'all') selected @endif>Todos</option>
                        @foreach($romanticSubgenres as $key => $subgenre)
                            <option value="{{ $key }}" @if(request('subgenre') == $key) selected @endif>{{ $subgenre['name'] }}</option>
                        @endforeach
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
                    <h3 class="text-xl text-gray-400 mb-4">No se encontraron doramas románticos {{ $originLabel }}s</h3>
                    <p class="text-gray-500">Intente con otros filtros o vuelva más tarde</p>
                </div>
            @endif
        </div>
    </section>

    <!-- Related Origin Links -->
    <section class="py-8 bg-gray-900">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-2xl font-semibold text-white mb-6">Explora Doramas Románticos por Origen</h2>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('romantic-dramas.origin', 'korean') }}" class="relative overflow-hidden rounded-lg group @if($origin == 'korean') ring-2 ring-pink-500 @endif">
                    <img src="{{ asset('images/categories/k-drama.jpg') }}" alt="K-Drama" class="w-full h-40 object-cover transition duration-300 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black to-transparent flex items-end p-4">
                        <h3 class="text-lg font-semibold text-white">K-Dramas Románticos</h3>
                    </div>
                </a>
                
                <a href="{{ route('romantic-dramas.origin', 'japanese') }}" class="relative overflow-hidden rounded-lg group @if($origin == 'japanese') ring-2 ring-pink-500 @endif">
                    <img src="{{ asset('images/categories/j-drama.jpg') }}" alt="J-Drama" class="w-full h-40 object-cover transition duration-300 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black to-transparent flex items-end p-4">
                        <h3 class="text-lg font-semibold text-white">J-Dramas Románticos</h3>
                    </div>
                </a>
                
                <a href="{{ route('romantic-dramas.origin', 'chinese') }}" class="relative overflow-hidden rounded-lg group @if($origin == 'chinese') ring-2 ring-pink-500 @endif">
                    <img src="{{ asset('images/categories/c-drama.jpg') }}" alt="C-Drama" class="w-full h-40 object-cover transition duration-300 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black to-transparent flex items-end p-4">
                        <h3 class="text-lg font-semibold text-white">C-Dramas Románticos</h3>
                    </div>
                </a>
                
                <a href="{{ route('romantic-dramas.origin', 'thai') }}" class="relative overflow-hidden rounded-lg group @if($origin == 'thai') ring-2 ring-pink-500 @endif">
                    <img src="{{ asset('images/categories/dorasia-originals.jpg') }}" alt="Thai Drama" class="w-full h-40 object-cover transition duration-300 group-hover:scale-110">
                    <div class="absolute inset-0 bg-gradient-to-t from-black to-transparent flex items-end p-4">
                        <h3 class="text-lg font-semibold text-white">Thai Dramas Románticos</h3>
                    </div>
                </a>
            </div>
        </div>
    </section>
@endsection