@props(['title' => null, 'showForm' => true])

@if(!$title || !isset($title->id))
    <div class="bg-yellow-900 rounded-lg p-6">
        <p class="text-yellow-200">Componente de comentarios temporalmente no disponible.</p>
        <p class="text-yellow-300 text-sm mt-2">Por favor, recarga la página.</p>
    </div>
@else
<div class="bg-gray-900 rounded-lg p-6" x-data="enhancedComments({{ $title ? $title->id : 0 }})">
    <div class="mb-6">
        <h3 class="text-xl font-bold mb-2">Comentarios</h3>
        
        <!-- Filtros y ordenamiento -->
        <div class="flex flex-wrap gap-4 mb-4">
            <select x-model="sortBy" @change="loadComments()" 
                    class="bg-gray-800 border border-gray-700 rounded px-3 py-1 text-sm">
                <option value="newest">Más recientes</option>
                <option value="oldest">Más antiguos</option>
                <option value="popular">Más populares</option>
            </select>
            
            <button @click="showOnlyMine = !showOnlyMine; loadComments()"
                    :class="showOnlyMine ? 'bg-red-600' : 'bg-gray-800'"
                    class="px-3 py-1 rounded text-sm transition">
                Mis comentarios
            </button>
        </div>
    </div>
    
    <!-- Formulario de nuevo comentario -->
    @if($showForm && auth()->check() && auth()->user()->getActiveProfile())
    <div class="mb-6">
        <form @submit.prevent="submitComment">
            <div class="mb-4">
                <div x-ref="editor" 
                     contenteditable="true"
                     class="bg-gray-800 border border-gray-700 rounded-lg p-3 min-h-[100px] text-white focus:outline-none focus:ring-1 focus:ring-red-500"
                     @input="content = $refs.editor.innerHTML"
                     @keydown="handleKeydown($event)"
                     placeholder="Escribe tu comentario..."></div>
                     
                <!-- Sugerencias de menciones -->
                <div x-show="showMentions" 
                     x-transition
                     class="absolute bg-gray-800 border border-gray-700 rounded-lg mt-1 max-h-40 overflow-y-auto shadow-lg z-20">
                    <template x-for="user in mentionSuggestions" :key="user.id">
                        <button @click="insertMention(user)"
                                class="w-full text-left px-3 py-2 hover:bg-gray-700 transition">
                            <span class="font-medium" x-text="user.name"></span>
                            <span class="text-gray-400 text-sm ml-1" x-text="`@${user.username}`"></span>
                        </button>
                    </template>
                </div>
            </div>
            
            <!-- Botones de formato -->
            <div class="flex items-center gap-2 mb-3">
                <button type="button" @click="formatText('bold')" 
                        class="p-2 rounded hover:bg-gray-700 transition" title="Negrita">
                    <i class="fas fa-bold"></i>
                </button>
                <button type="button" @click="formatText('italic')" 
                        class="p-2 rounded hover:bg-gray-700 transition" title="Cursiva">
                    <i class="fas fa-italic"></i>
                </button>
                <button type="button" @click="formatText('underline')" 
                        class="p-2 rounded hover:bg-gray-700 transition" title="Subrayado">
                    <i class="fas fa-underline"></i>
                </button>
                <div class="h-6 w-px bg-gray-700 mx-2"></div>
                <button type="button" @click="insertEmoji()" 
                        class="p-2 rounded hover:bg-gray-700 transition" title="Emoji">
                    <i class="fas fa-smile"></i>
                </button>
                <button type="button" @click="insertSpoiler()" 
                        class="p-2 rounded hover:bg-gray-700 transition" title="Spoiler">
                    <i class="fas fa-eye-slash"></i>
                </button>
            </div>
            
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-400">
                    <span x-text="content.length"></span>/500 caracteres
                </span>
                <button type="submit" 
                        :disabled="content.length === 0 || content.length > 500 || submitting"
                        class="bg-red-600 hover:bg-red-700 disabled:bg-gray-700 disabled:cursor-not-allowed text-white px-4 py-2 rounded transition">
                    <span x-show="!submitting">Publicar</span>
                    <span x-show="submitting">
                        <i class="fas fa-spinner fa-spin mr-1"></i> Publicando...
                    </span>
                </button>
            </div>
        </form>
    </div>
    @endif
    
    <!-- Lista de comentarios -->
    <div class="space-y-4">
        <!-- Loading state -->
        <div x-show="loading" class="text-center py-8">
            <i class="fas fa-spinner fa-spin text-2xl"></i>
        </div>
        
        <!-- Error state -->
        <div x-show="!loading && error" class="text-center py-8">
            <i class="fas fa-exclamation-triangle text-4xl text-red-500 mb-4"></i>
            <p class="text-red-400 mb-2">Error al cargar comentarios</p>
            <p class="text-gray-500 text-sm">Por favor, intenta recargar la página</p>
        </div>
        
        <!-- Comentarios -->
        <template x-for="comment in comments" :key="comment.id">
            <div class="bg-gray-800 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <img :src="comment.profile.avatar_url" 
                         :alt="comment.profile.name"
                         class="w-10 h-10 rounded-full object-cover">
                    
                    <div class="flex-1">
                        <div class="flex items-center justify-between mb-1">
                            <div>
                                <a :href="`/profiles/${comment.profile.id}`" 
                                   class="font-medium hover:text-red-500 transition">
                                   <span x-text="comment.profile.name"></span>
                                </a>
                                <span class="text-gray-400 text-sm ml-2" x-text="comment.time_ago"></span>
                            </div>
                            
                            <div class="relative" x-data="{ showMenu: false }">
                                <button @click="showMenu = !showMenu" 
                                        class="text-gray-400 hover:text-white transition">
                                    <i class="fas fa-ellipsis-h"></i>
                                </button>
                                
                                <div x-show="showMenu" 
                                     @click.away="showMenu = false"
                                     x-transition
                                     class="absolute right-0 mt-2 w-48 bg-gray-900 border border-gray-700 rounded-lg shadow-lg z-10">
                                    @auth
                                        <template x-if="comment.profile.user_id === {{ auth()->id() }}">
                                            <div>
                                                <button @click="editComment(comment)"
                                                        class="w-full text-left px-4 py-2 hover:bg-gray-800 transition">
                                                    <i class="fas fa-edit mr-2"></i> Editar
                                                </button>
                                                <button @click="deleteComment(comment.id)"
                                                        class="w-full text-left px-4 py-2 hover:bg-gray-800 transition text-red-500">
                                                    <i class="fas fa-trash mr-2"></i> Eliminar
                                                </button>
                                            </div>
                                        </template>
                                    @endauth
                                    
                                    <button @click="reportComment(comment.id); showMenu = false"
                                            class="w-full text-left px-4 py-2 hover:bg-gray-800 transition">
                                        <i class="fas fa-flag mr-2"></i> Reportar
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Contenido del comentario -->
                        <div class="prose prose-invert max-w-none mb-3" x-html="comment.content"></div>
                        
                        <!-- Acciones del comentario -->
                        <div class="flex items-center gap-4 text-sm">
                            <button @click="toggleLike(comment)"
                                    :class="comment.user_liked ? 'text-red-500' : 'text-gray-400'"
                                    class="hover:text-red-500 transition">
                                <i class="fas fa-heart mr-1"></i>
                                <span x-text="comment.likes_count"></span>
                            </button>
                            
                            <button @click="toggleReply(comment)"
                                    class="text-gray-400 hover:text-white transition">
                                <i class="fas fa-reply mr-1"></i>
                                Responder
                            </button>
                            
                            <template x-if="comment.replies_count > 0">
                                <button @click="toggleReplies(comment)"
                                        class="text-gray-400 hover:text-white transition">
                                    <span x-text="comment.show_replies ? 'Ocultar' : 'Ver'"></span>
                                    <span x-text="`${comment.replies_count} respuesta${comment.replies_count > 1 ? 's' : ''}`"></span>
                                </button>
                            </template>
                        </div>
                        
                        <!-- Formulario de respuesta -->
                        <div x-show="comment.show_reply_form" x-transition class="mt-4">
                            <form @submit.prevent="submitReply(comment)">
                                <textarea x-model="comment.reply_content"
                                          class="w-full bg-gray-700 border border-gray-600 rounded-lg p-3 text-sm focus:outline-none focus:ring-1 focus:ring-red-500"
                                          rows="3"
                                          :placeholder="`Responder a ${comment.profile.name}...`"></textarea>
                                <div class="mt-2 flex justify-end gap-2">
                                    <button type="button" 
                                            @click="comment.show_reply_form = false; comment.reply_content = ''"
                                            class="px-3 py-1 text-sm border border-gray-600 rounded hover:bg-gray-800 transition">
                                        Cancelar
                                    </button>
                                    <button type="submit"
                                            :disabled="!comment.reply_content"
                                            class="px-3 py-1 text-sm bg-red-600 hover:bg-red-700 disabled:bg-gray-700 disabled:cursor-not-allowed text-white rounded transition">
                                        Responder
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Respuestas -->
                        <div x-show="comment.show_replies" x-transition class="mt-4 space-y-3">
                            <template x-for="reply in comment.replies" :key="reply.id">
                                <div class="pl-4 border-l-2 border-gray-700">
                                    <div class="flex items-start gap-3">
                                        <img :src="reply.profile.avatar_url" 
                                             :alt="reply.profile.name"
                                             class="w-8 h-8 rounded-full object-cover">
                                        
                                        <div class="flex-1">
                                            <div class="flex items-center mb-1">
                                                <a :href="`/profiles/${reply.profile.id}`" 
                                                   class="font-medium text-sm hover:text-red-500 transition">
                                                   <span x-text="reply.profile.name"></span>
                                                </a>
                                                <span class="text-gray-400 text-xs ml-2" x-text="reply.time_ago"></span>
                                            </div>
                                            
                                            <div class="prose prose-invert prose-sm max-w-none" x-html="reply.content"></div>
                                            
                                            <div class="flex items-center gap-3 mt-2 text-xs">
                                                <button @click="toggleLike(reply)"
                                                        :class="reply.user_liked ? 'text-red-500' : 'text-gray-400'"
                                                        class="hover:text-red-500 transition">
                                                    <i class="fas fa-heart mr-1"></i>
                                                    <span x-text="reply.likes_count"></span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            
                            <button x-show="comment.has_more_replies"
                                    @click="loadMoreReplies(comment)"
                                    class="text-sm text-red-500 hover:text-red-400 transition pl-4">
                                Cargar más respuestas
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        
        <!-- Load more button -->
        <div x-show="hasMore && !loading" class="text-center py-4">
            <button @click="loadMore()"
                    class="bg-gray-800 hover:bg-gray-700 px-4 py-2 rounded transition">
                Cargar más comentarios
            </button>
        </div>
        
        <!-- Empty state -->
        <div x-show="!loading && comments.length === 0" class="text-center py-8">
            <i class="fas fa-comments text-4xl text-gray-600 mb-4"></i>
            <p class="text-gray-400">No hay comentarios todavía</p>
            @auth
                <p class="text-gray-500 text-sm mt-2">¡Sé el primero en comentar!</p>
            @endauth
        </div>
    </div>
</div>

<script>
function enhancedComments(titleId) {
    if (!titleId || titleId === 0) {
        console.error('enhancedComments: Invalid titleId provided');
        return {
            titleId: 0,
            comments: [],
            loading: false,
            error: true,
            errorMessage: 'ID de título no válido',
            init() {
                console.error('Enhanced comments initialized with invalid title ID');
            }
        };
    }
    
    return {
        titleId: titleId,
        comments: [],
        content: '',
        sortBy: 'newest',
        showOnlyMine: false,
        loading: false,
        submitting: false,
        hasMore: true,
        page: 1,
        showMentions: false,
        mentionSuggestions: [],
        mentionQuery: '',
        error: false,
        
        init() {
            console.log('Enhanced comments init - titleId:', this.titleId);
            this.loadComments();
            this.setupEditor();
        },
        
        setupEditor() {
            // Rich text editor setup
            this.$refs.editor.addEventListener('paste', (e) => {
                e.preventDefault();
                const text = e.clipboardData.getData('text/plain');
                document.execCommand('insertText', false, text);
            });
        },
        
        async loadComments() {
            this.loading = true;
            this.error = false;
            try {
                const url = `/api/titles/${this.titleId}/comments?sort=${this.sortBy}&mine=${this.showOnlyMine}&page=${this.page}`;
                console.log('Loading comments from:', url);
                
                const response = await fetch(url);
                console.log('Response status:', response.status);
                
                if (!response.ok) {
                    const errorText = await response.text();
                    console.error('Error response:', errorText);
                    
                    // Handle authentication/profile errors gracefully
                    if (response.status === 403 || response.status === 401) {
                        this.comments = [];
                        this.hasMore = false;
                        // Don't show error toast for auth issues
                        return;
                    }
                    
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                console.log('Comments data:', data);
                
                if (this.page === 1) {
                    this.comments = data.data || [];
                } else {
                    this.comments.push(...(data.data || []));
                }
                
                this.hasMore = data.has_more || false;
                this.page = data.current_page || 1;
            } catch (error) {
                console.error('Error loading comments:', error);
                // Only show toast if it exists
                if (window.toast && typeof window.toast.show === 'function') {
                    window.toast.show('Error al cargar comentarios', 'error');
                }
                // Set error state but don't break the component
                this.error = true;
                this.comments = [];
                this.hasMore = false;
            } finally {
                this.loading = false;
            }
        },
        
        async submitComment() {
            if (!this.content.trim() || this.submitting) return;
            
            this.submitting = true;
            try {
                const response = await fetch(`/api/titles/${this.titleId}/comments`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ content: this.content })
                });
                
                if (response.ok) {
                    const comment = await response.json();
                    this.comments.unshift(comment);
                    this.content = '';
                    this.$refs.editor.innerHTML = '';
                    if (window.toast && typeof window.toast.show === 'function') {
                        window.toast.show('Comentario publicado', 'success');
                    }
                }
            } catch (error) {
                if (window.toast && typeof window.toast.show === 'function') {
                    window.toast.show('Error al publicar comentario', 'error');
                }
            } finally {
                this.submitting = false;
            }
        },
        
        async toggleLike(comment) {
            try {
                const response = await fetch(`/api/comments/${comment.id}/like`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                if (response.ok) {
                    comment.user_liked = !comment.user_liked;
                    comment.likes_count += comment.user_liked ? 1 : -1;
                }
            } catch (error) {
                if (window.toast && typeof window.toast.show === 'function') {
                    window.toast.show('Error al dar like', 'error');
                }
            }
        },
        
        formatText(command) {
            document.execCommand(command, false, null);
            this.$refs.editor.focus();
        },
        
        insertEmoji() {
            // Simple emoji picker implementation
            const emojis = ['😊', '😂', '❤️', '👍', '😎', '🤩', '🥰', '😍', '🤔', '😱'];
            const emoji = emojis[Math.floor(Math.random() * emojis.length)];
            document.execCommand('insertText', false, emoji);
        },
        
        insertSpoiler() {
            const selection = window.getSelection();
            if (selection.toString()) {
                document.execCommand('insertHTML', false, `<span class="spoiler">${selection.toString()}</span>`);
            }
        },
        
        toggleReply(comment) {
            comment.show_reply_form = !comment.show_reply_form;
            if (!comment.reply_content) {
                comment.reply_content = '';
            }
        },
        
        async submitReply(comment) {
            if (!comment.reply_content?.trim()) return;
            
            try {
                const response = await fetch(`/api/comments/${comment.id}/replies`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ content: comment.reply_content })
                });
                
                if (response.ok) {
                    const reply = await response.json();
                    if (!comment.replies) comment.replies = [];
                    comment.replies.push(reply);
                    comment.replies_count++;
                    comment.reply_content = '';
                    comment.show_reply_form = false;
                    comment.show_replies = true;
                    if (window.toast && typeof window.toast.show === 'function') {
                        window.toast.show('Respuesta publicada', 'success');
                    }
                }
            } catch (error) {
                if (window.toast && typeof window.toast.show === 'function') {
                    window.toast.show('Error al publicar respuesta', 'error');
                }
            }
        },
        
        toggleReplies(comment) {
            comment.show_replies = !comment.show_replies;
            if (comment.show_replies && !comment.replies) {
                this.loadReplies(comment);
            }
        },
        
        async loadReplies(comment) {
            try {
                const response = await fetch(`/api/comments/${comment.id}/replies`);
                const data = await response.json();
                comment.replies = data.data;
                comment.has_more_replies = data.has_more;
            } catch (error) {
                if (window.toast && typeof window.toast.show === 'function') {
                    window.toast.show('Error al cargar respuestas', 'error');
                }
            }
        },
        
        handleKeydown(event) {
            // Handle @ mentions
            if (event.key === '@') {
                this.showMentions = true;
                this.mentionQuery = '';
                this.searchUsers();
            }
        },
        
        async searchUsers() {
            if (this.mentionQuery.length < 2) return;
            
            try {
                const response = await fetch(`/api/users/search?q=${this.mentionQuery}`);
                this.mentionSuggestions = await response.json();
            } catch (error) {
                console.error('Error searching users:', error);
            }
        },
        
        insertMention(user) {
            const mention = `<a href="/profiles/${user.id}" class="text-red-500">@${user.username}</a>`;
            document.execCommand('insertHTML', false, mention);
            this.showMentions = false;
            this.$refs.editor.focus();
        },
        
        async deleteComment(commentId) {
            if (!confirm('¿Estás seguro de eliminar este comentario?')) return;
            
            try {
                const response = await fetch(`/api/comments/${commentId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                if (response.ok) {
                    this.comments = this.comments.filter(c => c.id !== commentId);
                    if (window.toast && typeof window.toast.show === 'function') {
                        window.toast.show('Comentario eliminado', 'success');
                    }
                }
            } catch (error) {
                if (window.toast && typeof window.toast.show === 'function') {
                    window.toast.show('Error al eliminar comentario', 'error');
                }
            }
        },
        
        loadMore() {
            this.page++;
            this.loadComments();
        }
    };
}
</script>

<style>
.spoiler {
    background-color: #000;
    color: #000;
    cursor: pointer;
    padding: 0 4px;
}

.spoiler:hover {
    color: #fff;
}

[contenteditable]:empty:before {
    content: attr(placeholder);
    color: #6b7280;
}
</style>
@endif