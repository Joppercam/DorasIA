@props(['title'])

<div class="bg-gray-900 rounded-lg p-6" x-data="simpleComments({{ $title->id }})">
    <h3 class="text-xl font-bold mb-4">Comentarios (Debug)</h3>
    
    <div class="mb-4">
        <button @click="loadComments()" class="bg-blue-600 hover:bg-blue-700 px-4 py-2 rounded">
            Cargar Comentarios
        </button>
    </div>
    
    <div x-show="loading" class="text-center py-4">
        <i class="fas fa-spinner fa-spin text-2xl"></i>
        <p>Cargando...</p>
    </div>
    
    <div x-show="error" class="bg-red-900 p-4 rounded mb-4">
        <p class="text-red-400">Error: <span x-text="errorMessage"></span></p>
    </div>
    
    <div x-show="!loading && comments.length > 0" class="space-y-4">
        <template x-for="comment in comments" :key="comment.id">
            <div class="bg-gray-800 p-4 rounded">
                <p class="font-semibold" x-text="comment.profile.name"></p>
                <p x-text="comment.content"></p>
                <p class="text-sm text-gray-400" x-text="comment.time_ago"></p>
            </div>
        </template>
    </div>
    
    <div x-show="!loading && comments.length === 0 && !error" class="text-center py-8 text-gray-400">
        No hay comentarios todavía
    </div>
    
    <div class="mt-4 p-4 bg-gray-800 rounded">
        <h4 class="font-bold mb-2">Debug Info:</h4>
        <pre x-text="JSON.stringify(debugInfo, null, 2)" class="text-xs"></pre>
    </div>
</div>

<script>
function simpleComments(titleId) {
    return {
        titleId: titleId,
        comments: [],
        loading: false,
        error: false,
        errorMessage: '',
        debugInfo: {},
        
        async loadComments() {
            this.loading = true;
            this.error = false;
            this.errorMessage = '';
            this.debugInfo = { titleId: this.titleId, timestamp: new Date().toISOString() };
            
            try {
                const url = `/api/titles/${this.titleId}/comments`;
                this.debugInfo.url = url;
                
                const response = await fetch(url);
                this.debugInfo.status = response.status;
                this.debugInfo.statusText = response.statusText;
                
                const text = await response.text();
                this.debugInfo.responseLength = text.length;
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const data = JSON.parse(text);
                this.comments = data.data || [];
                this.debugInfo.commentCount = this.comments.length;
                this.debugInfo.success = true;
                
            } catch (err) {
                this.error = true;
                this.errorMessage = err.message;
                this.debugInfo.error = err.message;
                this.debugInfo.stack = err.stack;
                console.error('Comments error:', err);
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>