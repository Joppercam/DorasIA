@props(['title' => null])

<div class="bg-gray-800 p-4 rounded mb-4">
    <h3 class="text-lg font-bold mb-2">Debug Title Info</h3>
    
    @if($title === null)
        <p class="text-red-500">Title is null</p>
    @elseif(is_object($title))
        <p class="text-green-500">Title is an object</p>
        <ul class="list-disc list-inside">
            <li>Class: {{ get_class($title) }}</li>
            <li>ID: {{ $title->id ?? 'No ID' }}</li>
            <li>Title: {{ $title->title ?? 'No title' }}</li>
            <li>Type: {{ $title->type ?? 'No type' }}</li>
        </ul>
    @else
        <p class="text-yellow-500">Title is something else: {{ gettype($title) }}</p>
        <pre>{{ var_export($title, true) }}</pre>
    @endif
    
    <div class="mt-4">
        <p class="text-sm text-gray-400">Stack trace (for debugging):</p>
        <pre class="text-xs">{{ implode("\n", array_slice(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), 0, 5)) }}</pre>
    </div>
</div>