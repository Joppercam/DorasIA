<div class="bg-gray-800 rounded-lg h-full flex flex-col" x-data="messageConversation({{ $otherUserId }})" x-init="loadMessages">
    <!-- Header -->
    <div class="p-4 bg-gray-750 border-b border-gray-700 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <img :src="otherUser.profile.avatar" 
                 alt=""
                 class="w-10 h-10 rounded-full object-cover">
            <div>
                <h3 class="font-semibold text-white" x-text="otherUser.name"></h3>
                <p class="text-sm text-gray-400" x-text="otherUser.is_online ? 'En línea' : 'Desconectado'"></p>
            </div>
        </div>
        
        <div class="flex items-center space-x-2">
            <button @click="deleteConversation" 
                    class="p-2 text-gray-400 hover:text-red-500 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
            </button>
        </div>
    </div>
    
    <!-- Messages Area -->
    <div class="flex-1 overflow-y-auto p-4 space-y-4" id="messages-container">
        <template x-for="message in messages" :key="message.id">
            <div :class="message.sender_id == currentUserId ? 'ml-auto' : 'mr-auto'"
                 class="max-w-xs lg:max-w-md">
                <div :class="message.sender_id == currentUserId ? 'bg-red-600 text-white' : 'bg-gray-700 text-gray-300'"
                     class="rounded-lg p-3">
                    <p x-text="message.content"></p>
                    <p class="text-xs mt-1 opacity-75" x-text="formatTime(message.created_at)"></p>
                </div>
                
                <!-- Read indicator -->
                <div x-show="message.sender_id == currentUserId && message.is_read" 
                     class="text-right mt-1">
                    <span class="text-xs text-gray-500">Visto</span>
                </div>
            </div>
        </template>
        
        <!-- Loading indicator -->
        <div x-show="loading" class="text-center py-4">
            <svg class="animate-spin h-6 w-6 mx-auto text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
    </div>
    
    <!-- Message Input -->
    <div class="p-4 bg-gray-750 border-t border-gray-700">
        <form @submit.prevent="sendMessage" class="flex items-end space-x-3">
            <div class="flex-1">
                <textarea x-model="newMessage" 
                          @keydown.enter.prevent="if(!$event.shiftKey) sendMessage()"
                          placeholder="Escribe un mensaje..."
                          rows="1"
                          class="w-full bg-gray-700 text-white px-4 py-2 rounded-lg focus:outline-none focus:ring-2 focus:ring-red-500 resize-none"
                          :disabled="sending"></textarea>
            </div>
            
            <button type="submit" 
                    :disabled="!newMessage.trim() || sending"
                    class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                <svg x-show="!sending" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                </svg>
                <svg x-show="sending" class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </button>
        </form>
    </div>
</div>

<script>
function messageConversation(otherUserId) {
    return {
        currentUserId: {{ auth()->id() }},
        otherUserId: otherUserId,
        otherUser: {},
        messages: [],
        newMessage: '',
        loading: false,
        sending: false,
        
        async loadMessages() {
            this.loading = true;
            try {
                const response = await fetch(`/api/messages/${this.otherUserId}`);
                const data = await response.json();
                this.messages = data.messages;
                this.otherUser = data.otherUser;
                
                // Scroll to bottom
                this.$nextTick(() => {
                    const container = document.getElementById('messages-container');
                    container.scrollTop = container.scrollHeight;
                });
                
                // Mark messages as read
                this.markAsRead();
            } catch (error) {
                console.error('Error loading messages:', error);
            } finally {
                this.loading = false;
            }
        },
        
        async sendMessage() {
            if (!this.newMessage.trim() || this.sending) return;
            
            this.sending = true;
            try {
                const response = await fetch(`/api/messages`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        recipient_id: this.otherUserId,
                        content: this.newMessage
                    })
                });
                
                const message = await response.json();
                this.messages.push(message);
                this.newMessage = '';
                
                // Scroll to bottom
                this.$nextTick(() => {
                    const container = document.getElementById('messages-container');
                    container.scrollTop = container.scrollHeight;
                });
            } catch (error) {
                console.error('Error sending message:', error);
            } finally {
                this.sending = false;
            }
        },
        
        async markAsRead() {
            try {
                await fetch(`/api/messages/${this.otherUserId}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
            } catch (error) {
                console.error('Error marking messages as read:', error);
            }
        },
        
        async deleteConversation() {
            if (!confirm('¿Estás seguro de que quieres eliminar esta conversación?')) return;
            
            try {
                await fetch(`/api/messages/${this.otherUserId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                // Reload page to show updated conversations list
                window.location.reload();
            } catch (error) {
                console.error('Error deleting conversation:', error);
            }
        },
        
        formatTime(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            
            // If today, show time
            if (date.toDateString() === now.toDateString()) {
                return date.toLocaleTimeString('es', { hour: '2-digit', minute: '2-digit' });
            }
            
            // If this year, show date without year
            if (date.getFullYear() === now.getFullYear()) {
                return date.toLocaleDateString('es', { day: 'numeric', month: 'short' });
            }
            
            // Otherwise, show full date
            return date.toLocaleDateString('es', { day: 'numeric', month: 'short', year: 'numeric' });
        }
    }
}
</script>