<x-app-layout>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-8">Test Enhanced Comments Component</h1>
        
        @if($title)
            <div class="mb-4 bg-gray-800 p-4 rounded">
                <h2 class="text-xl font-semibold mb-2">Title Information</h2>
                <p>ID: {{ $title->id }}</p>
                <p>Title: {{ $title->title }}</p>
                <p>Type: {{ $title->type }}</p>
            </div>
            
            <div>
                <h2 class="text-xl font-semibold mb-4">Enhanced Comments Component:</h2>
                <x-enhanced-comments :title="$title" />
            </div>
        @else
            <p class="text-red-500">No title provided</p>
        @endif
    </div>
</x-app-layout>