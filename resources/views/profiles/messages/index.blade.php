@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-gray-900 to-black">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white">Mensajes</h1>
            <p class="text-gray-400 mt-1">Conversaciones privadas con otros usuarios</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Conversations List -->
            <div class="lg:col-span-1">
                <div class="bg-gray-800 rounded-lg overflow-hidden">
                    <div class="p-4 bg-gray-750 border-b border-gray-700">
                        <input type="text" 
                               x-data
                               @input="$dispatch('search-conversations', { query: $event.target.value })"
                               placeholder="Buscar conversaciones..."
                               class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                    </div>

                    <div class="divide-y divide-gray-700" x-data="conversationsList()" @search-conversations.window="searchConversations($event.detail.query)">
                        @forelse($conversations as $conversation)
                        <a href="{{ route('profiles.messages.conversation', ['profile' => $profile, 'otherUser' => $conversation->otherUser]) }}"
                           class="block p-4 hover:bg-gray-750 transition {{ request('otherUser') == $conversation->otherUser->id ? 'bg-gray-750 border-l-4 border-red-500' : '' }}"
                           x-show="matchesSearch('{{ strtolower($conversation->otherUser->name) }}')">
                            <div class="flex items-start space-x-3">
                                <img src="{{ $conversation->otherProfile->avatar }}" 
                                     alt="{{ $conversation->otherUser->name }}"
                                     class="w-12 h-12 rounded-full object-cover">
                                
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <h4 class="font-semibold text-white truncate">{{ $conversation->otherUser->name }}</h4>
                                        <span class="text-xs text-gray-500">{{ $conversation->latest_message->created_at->diffForHumans() }}</span>
                                    </div>
                                    
                                    <p class="text-sm text-gray-400 truncate">
                                        @if($conversation->latest_message->sender_id == auth()->id())
                                            Tú: 
                                        @endif
                                        {{ $conversation->latest_message->content }}
                                    </p>
                                    
                                    @if($conversation->unread_count > 0)
                                    <span class="inline-block mt-1 px-2 py-0.5 bg-red-600 text-white text-xs rounded-full">
                                        {{ $conversation->unread_count }} nuevo{{ $conversation->unread_count > 1 ? 's' : '' }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </a>
                        @empty
                        <div class="p-8 text-center text-gray-500">
                            <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            <p>No tienes conversaciones aún</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Message Area -->
            <div class="lg:col-span-2">
                @if(request('otherUser'))
                    <!-- Include conversation component here -->
                    @include('profiles.messages.conversation', ['otherUserId' => request('otherUser')])
                @else
                <div class="bg-gray-800 rounded-lg h-full flex items-center justify-center min-h-[600px]">
                    <div class="text-center">
                        <svg class="w-24 h-24 mx-auto text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        <p class="text-gray-400 text-lg">Selecciona una conversación para empezar</p>
                        
                        <!-- New Message Button -->
                        <button @click="$dispatch('open-new-message')" 
                                class="mt-4 px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                            Nuevo Mensaje
                        </button>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- New Message Modal -->
<div x-data="{ open: false }" 
     @open-new-message.window="open = true"
     x-show="open" 
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">
    <div class="min-h-screen px-4 text-center">
        <div x-show="open" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black opacity-75"
             @click="open = false"></div>

        <div x-show="open" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95"
             class="inline-block w-full max-w-md p-6 my-8 text-left align-middle transition-all transform bg-gray-800 shadow-xl rounded-2xl">
            
            <h3 class="text-xl font-semibold text-white mb-4">Nuevo Mensaje</h3>
            
            <form action="{{ route('profiles.messages.create', $profile) }}" method="POST" x-data="{ recipientId: null }">
                @csrf
                
                <!-- Search Recipients -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Para:</label>
                    <div x-data="recipientSearch()" class="relative">
                        <input type="text" 
                               x-model="search"
                               @input="searchUsers"
                               placeholder="Buscar usuario..."
                               class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500">
                        
                        <div x-show="results.length > 0" 
                             class="absolute top-full left-0 right-0 mt-1 bg-gray-700 rounded-lg shadow-lg overflow-hidden z-10">
                            <template x-for="user in results" :key="user.id">
                                <button type="button"
                                        @click="selectUser(user)"
                                        class="w-full px-4 py-2 text-left hover:bg-gray-600 transition flex items-center space-x-3">
                                    <img :src="user.profile.avatar" 
                                         :alt="user.name"
                                         class="w-8 h-8 rounded-full object-cover">
                                    <div>
                                        <p class="text-white" x-text="user.name"></p>
                                        <p class="text-sm text-gray-400" x-text="'@' + (user.profile.username || user.id)"></p>
                                    </div>
                                </button>
                            </template>
                        </div>
                        
                        <input type="hidden" name="recipient_id" x-model="selectedUser.id">
                    </div>
                    
                    <!-- Selected User -->
                    <div x-show="selectedUser" class="mt-2 flex items-center space-x-2 bg-gray-700 px-3 py-2 rounded-lg">
                        <template x-if="selectedUser">
                            <div class="flex items-center flex-1 space-x-2">
                                <img :src="selectedUser.profile.avatar" 
                                     :alt="selectedUser.name"
                                     class="w-6 h-6 rounded-full object-cover">
                                <span class="text-white" x-text="selectedUser.name"></span>
                            </div>
                        </template>
                        <button type="button" @click="selectedUser = null" class="text-gray-400 hover:text-white">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <!-- Message Content -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">Mensaje:</label>
                    <textarea name="content" 
                              rows="4"
                              required
                              class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 resize-none"></textarea>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" 
                            @click="open = false"
                            class="px-4 py-2 bg-gray-700 text-gray-300 rounded-lg hover:bg-gray-600 transition">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                        Enviar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function conversationsList() {
    return {
        searchQuery: '',
        matchesSearch(name) {
            if (!this.searchQuery) return true;
            return name.includes(this.searchQuery);
        },
        searchConversations(query) {
            this.searchQuery = query.toLowerCase();
        }
    }
}

function recipientSearch() {
    return {
        search: '',
        results: [],
        selectedUser: null,
        searchTimeout: null,
        
        searchUsers() {
            clearTimeout(this.searchTimeout);
            
            if (this.search.length < 2) {
                this.results = [];
                return;
            }
            
            this.searchTimeout = setTimeout(() => {
                fetch(`/api/users/search?q=${encodeURIComponent(this.search)}`)
                    .then(response => response.json())
                    .then(data => {
                        this.results = data.users;
                    });
            }, 300);
        },
        
        selectUser(user) {
            this.selectedUser = user;
            this.search = '';
            this.results = [];
        }
    }
}
</script>
@endsection