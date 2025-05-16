<div x-data="notificationBell()" x-init="init" class="relative">
    <!-- Notification Bell -->
    <button @click="showDropdown = !showDropdown" 
            @click.away="showDropdown = false"
            class="relative p-2 text-gray-400 hover:text-white transition-colors">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
        </svg>
        
        <!-- Badge for unread count -->
        <span x-show="unreadCount > 0" 
              x-text="unreadCount > 99 ? '99+' : unreadCount"
              class="absolute -top-1 -right-1 bg-red-600 text-white text-xs rounded-full h-5 min-w-[20px] flex items-center justify-center px-1">
        </span>
    </button>

    <!-- Dropdown -->
    <div x-show="showDropdown" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95"
         class="absolute right-0 mt-2 w-96 bg-gray-800 rounded-lg shadow-xl z-50 overflow-hidden"
         @click.away="showDropdown = false">
         
        <!-- Header -->
        <div class="px-4 py-3 bg-gray-900 border-b border-gray-700 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-white">Notificaciones</h3>
            <button x-show="unreadCount > 0" 
                    @click="markAllAsRead"
                    class="text-sm text-gray-400 hover:text-white transition">
                Marcar todas como leídas
            </button>
        </div>

        <!-- Notifications List -->
        <div class="max-h-96 overflow-y-auto">
            <!-- Loading State -->
            <div x-show="loading" class="p-4 text-center">
                <svg class="animate-spin h-8 w-8 mx-auto text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>

            <!-- Empty State -->
            <div x-show="!loading && notifications.length === 0" class="p-8 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
                <p class="text-gray-400">No tienes notificaciones</p>
            </div>

            <!-- Notifications -->
            <template x-for="notification in notifications" :key="notification.id">
                <div class="border-b border-gray-700 last:border-b-0">
                    <a :href="notification.url || '#'" 
                       @click="notification.read_at || markAsRead(notification.id)"
                       class="block px-4 py-3 hover:bg-gray-700 transition"
                       :class="{ 'bg-gray-850': !notification.read_at }">
                        
                        <div class="flex items-start space-x-3">
                            <!-- Icon -->
                            <div :class="`text-${notification.color}-500`">
                                <template x-if="notification.icon === 'user-plus'">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                    </svg>
                                </template>
                                <template x-if="notification.icon === 'message-circle'">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                </template>
                                <template x-if="notification.icon === 'message-square'">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-4 4z"></path>
                                    </svg>
                                </template>
                                <template x-if="notification.icon === 'heart'">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                                    </svg>
                                </template>
                                <template x-if="notification.icon === 'star'">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                </template>
                                <template x-if="notification.icon === 'bell'">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                    </svg>
                                </template>
                            </div>

                            <!-- Content -->
                            <div class="flex-1">
                                <p class="text-sm text-gray-300" x-text="notification.message"></p>
                                <p class="text-xs text-gray-500 mt-1" x-text="formatTime(notification.created_at)"></p>
                            </div>

                            <!-- Unread indicator -->
                            <div x-show="!notification.read_at" class="w-2 h-2 bg-red-600 rounded-full"></div>
                        </div>
                    </a>

                    <!-- Actions -->
                    <div class="px-4 py-2 bg-gray-850 flex justify-end">
                        <button @click="deleteNotification(notification.id)" 
                                class="text-xs text-gray-500 hover:text-red-500 transition">
                            Eliminar
                        </button>
                    </div>
                </div>
            </template>
        </div>

        <!-- Footer -->
        <div class="px-4 py-3 bg-gray-900 border-t border-gray-700">
            <a href="{{ route('notifications.index') }}" 
               class="text-sm text-gray-400 hover:text-white transition">
                Ver todas las notificaciones
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script src="{{ asset('js/components/notifications.js') }}"></script>
@endpush