@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-900 text-white p-8">
    <h1 class="text-3xl font-bold mb-8">Debug Romantic Dramas</h1>
    
    <div class="space-y-6">
        <div class="bg-gray-800 p-4 rounded">
            <h2 class="text-xl font-semibold mb-2">Featured Titles</h2>
            <p>Count: {{ $featuredTitles->count() }}</p>
            @if($featuredTitles->count() > 0)
                <ul class="list-disc list-inside">
                    @foreach($featuredTitles as $title)
                        <li>{{ $title->title }} (ID: {{ $title->id }})</li>
                    @endforeach
                </ul>
            @endif
        </div>
        
        <div class="bg-gray-800 p-4 rounded">
            <h2 class="text-xl font-semibold mb-2">Subgenre Sections</h2>
            <p>Count: {{ $subgenreSections->count() }}</p>
            @foreach($subgenreSections as $section)
                <div class="mt-2">
                    <h3 class="font-medium">{{ $section['name'] }}</h3>
                    <p>Titles: {{ $section['titles']->count() }}</p>
                </div>
            @endforeach
        </div>
        
        <div class="bg-gray-800 p-4 rounded">
            <h2 class="text-xl font-semibold mb-2">Popular K-Dramas</h2>
            <p>Count: {{ $popularKdramas->count() }}</p>
        </div>
        
        <div class="bg-gray-800 p-4 rounded">
            <h2 class="text-xl font-semibold mb-2">Popular J-Dramas</h2>
            <p>Count: {{ $popularJdramas->count() }}</p>
        </div>
        
        <div class="bg-gray-800 p-4 rounded">
            <h2 class="text-xl font-semibold mb-2">Popular C-Dramas</h2>
            <p>Count: {{ $popularCdramas->count() }}</p>
        </div>
        
        <div class="bg-gray-800 p-4 rounded">
            <h2 class="text-xl font-semibold mb-2">New Romantic Dramas</h2>
            <p>Count: {{ $newRomanticDramas->count() }}</p>
        </div>
        
        <div class="bg-gray-800 p-4 rounded">
            <h2 class="text-xl font-semibold mb-2">Romantic Subgenres</h2>
            <p>Count: {{ count($romanticSubgenres) }}</p>
        </div>
    </div>
</div>
@endsection