<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-8">Test Title Null Scenario</h1>
        
        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-4">Test 1: With valid title</h2>
            @php
                $title = \App\Models\Title::first();
            @endphp
            @if($title)
                <p class="mb-2">Title found: {{ $title->title }}</p>
                <x-enhanced-comments :title="$title" />
            @else
                <p class="text-red-500">No titles in database</p>
            @endif
        </div>
        
        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-4">Test 2: With null title</h2>
            <p class="mb-2">Passing null title to component:</p>
            <x-enhanced-comments :title="null" />
        </div>
        
        <div class="mb-8">
            <h2 class="text-xl font-semibold mb-4">Test 3: Without title prop</h2>
            <p class="mb-2">Not passing title prop:</p>
            <x-enhanced-comments />
        </div>
    </div>
</x-app-layout>