@extends('layouts.app')

@section('title', 'Test Doramas Románticos')

@section('content')
    <div class="min-h-screen bg-gray-900 text-white">
        <!-- Test sin componentes complejos -->
        <div class="p-8">
            <h1 class="text-3xl font-bold mb-8">Test Simple - Doramas Románticos</h1>
            
            <?php
                $featuredTitles = \App\Models\Title::romantic()
                    ->orderBy('popularity', 'desc')
                    ->take(6)
                    ->get();
            ?>
            
            <div class="mb-8">
                <h2 class="text-2xl font-semibold mb-4">Títulos Destacados ({{ $featuredTitles->count() }})</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    @foreach($featuredTitles as $title)
                        <div class="bg-gray-800 rounded-lg overflow-hidden">
                            <img src="{{ $title->poster_url }}" 
                                 alt="{{ $title->title }}" 
                                 class="w-full h-64 object-cover"
                                 onerror="this.src='/posters/placeholder.jpg'">
                            <div class="p-3">
                                <h3 class="text-sm font-medium">{{ $title->title }}</h3>
                                <p class="text-xs text-gray-400">{{ $title->release_date?->year }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <?php
                $popularKdramas = \App\Models\Title::romantic()
                    ->korean()
                    ->orderBy('popularity', 'desc')
                    ->take(6)
                    ->get();
            ?>
            
            <div class="mb-8">
                <h2 class="text-2xl font-semibold mb-4">K-Dramas Populares ({{ $popularKdramas->count() }})</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                    @foreach($popularKdramas as $title)
                        <div class="bg-gray-800 rounded-lg overflow-hidden">
                            <img src="{{ $title->poster_url }}" 
                                 alt="{{ $title->title }}" 
                                 class="w-full h-64 object-cover"
                                 onerror="this.src='/posters/placeholder.jpg'">
                            <div class="p-3">
                                <h3 class="text-sm font-medium">{{ $title->title }}</h3>
                                <p class="text-xs text-gray-400">K-Drama</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection