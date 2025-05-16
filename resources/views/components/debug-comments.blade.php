@props(['title'])

<div class="bg-red-900 rounded-lg p-6">
    <h3 class="text-xl font-bold mb-4">Debug Comments</h3>
    
    @if(!$title)
        <p class="text-yellow-200">Title is null!</p>
    @else
        <p class="text-green-200">Title exists: {{ $title->title }} (ID: {{ $title->id }})</p>
    @endif
    
    <div x-data="debugComments({{ $title ? $title->id : 'null' }})">
        <p>Alpine Status: <span x-text="status"></span></p>
        <button @click="testAPI()" class="bg-blue-600 px-4 py-2 rounded mt-2">
            Test API
        </button>
        <pre x-text="JSON.stringify(debugInfo, null, 2)" class="mt-4 text-xs bg-black/50 p-2 rounded"></pre>
    </div>
</div>

<script>
function debugComments(titleId) {
    return {
        titleId: titleId,
        status: 'Initializing...',
        debugInfo: {
            titleId: titleId,
            timestamp: new Date().toISOString()
        },
        
        init() {
            this.status = 'Initialized';
            this.debugInfo.initTime = new Date().toISOString();
            console.log('Debug comments init:', titleId);
        },
        
        async testAPI() {
            this.status = 'Testing API...';
            try {
                const response = await fetch(`/api/titles/${this.titleId}/comments`);
                const data = await response.json();
                this.status = `Success: ${data.data.length} comments`;
                this.debugInfo.apiResponse = data;
            } catch (error) {
                this.status = `Error: ${error.message}`;
                this.debugInfo.error = error.message;
            }
        }
    }
}
</script>