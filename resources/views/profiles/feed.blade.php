@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-gray-900 to-black">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white">Mi Feed</h1>
            <p class="text-gray-400 mt-1">Actividad reciente de las personas que sigues</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Feed -->
            <div class="lg:col-span-2">
                <div class="space-y-6" x-data="feedManager()" x-init="loadFeed">
                    <!-- Loading State -->
                    <div x-show="loading" class="text-center py-8">
                        <svg class="animate-spin h-8 w-8 mx-auto text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>

                    <!-- Feed Items -->
                    <template x-for="item in feedItems" :key="item.id">
                        <div class="bg-gray-800 rounded-lg p-6 border border-gray-700 hover:border-gray-600 transition">
                            <!-- User Header -->
                            <div class="flex items-start justify-between mb-4">
                                <a :href="`/profiles/${item.profile.id}`" class="flex items-center space-x-3">
                                    <img :src="item.profile.avatar" 
                                         :alt="item.profile.user.name"
                                         class="w-12 h-12 rounded-full object-cover">
                                    <div>
                                        <h3 class="font-semibold text-white hover:text-red-500 transition" x-text="item.profile.user.name"></h3>
                                        <p class="text-sm text-gray-400" x-text="formatTime(item.created_at)"></p>
                                    </div>
                                </a>
                            </div>

                            <!-- Activity Content -->
                            <div x-show="item.type === 'rating'" class="space-y-3">
                                <p class="text-gray-300">
                                    <span x-text="item.profile.user.name"></span> calificó 
                                    <a :href="`/titles/${item.title.slug}`" class="text-red-500 hover:text-red-400 transition" x-text="item.title.name"></a>
                                    con <span class="text-yellow-500" x-text="'★'.repeat(item.rating.score)"></span>
                                </p>
                                
                                <div x-show="item.rating.review" class="bg-gray-750 rounded-lg p-4">
                                    <p class="text-gray-300 italic" x-text="item.rating.review"></p>
                                </div>

                                <!-- Title Card Preview -->
                                <a :href="`/titles/${item.title.slug}`" class="flex items-center space-x-3 bg-gray-750 rounded-lg p-3 hover:bg-gray-700 transition">
                                    <img :src="item.title.poster_url" 
                                         :alt="item.title.name"
                                         class="w-16 h-24 object-cover rounded">
                                    <div>
                                        <h4 class="font-semibold text-white" x-text="item.title.name"></h4>
                                        <p class="text-sm text-gray-400" x-text="item.title.release_year"></p>
                                    </div>
                                </a>
                            </div>

                            <div x-show="item.type === 'watchlist'" class="space-y-3">
                                <p class="text-gray-300">
                                    <span x-text="item.profile.user.name"></span> agregó 
                                    <a :href="`/titles/${item.title.slug}`" class="text-red-500 hover:text-red-400 transition" x-text="item.title.name"></a>
                                    a su lista
                                </p>

                                <!-- Title Card Preview -->
                                <a :href="`/titles/${item.title.slug}`" class="flex items-center space-x-3 bg-gray-750 rounded-lg p-3 hover:bg-gray-700 transition">
                                    <img :src="item.title.poster_url" 
                                         :alt="item.title.name"
                                         class="w-16 h-24 object-cover rounded">
                                    <div>
                                        <h4 class="font-semibold text-white" x-text="item.title.name"></h4>
                                        <p class="text-sm text-gray-400" x-text="item.title.release_year"></p>
                                    </div>
                                </a>
                            </div>

                            <div x-show="item.type === 'comment'" class="space-y-3">
                                <p class="text-gray-300">
                                    <span x-text="item.profile.user.name"></span> comentó en 
                                    <a :href="`/titles/${item.title.slug}`" class="text-red-500 hover:text-red-400 transition" x-text="item.title.name"></a>
                                </p>
                                
                                <div class="bg-gray-750 rounded-lg p-4">
                                    <p class="text-gray-300" x-text="item.comment.content"></p>
                                </div>
                            </div>

                            <div x-show="item.type === 'follow'" class="flex items-center justify-between">
                                <p class="text-gray-300">
                                    <span x-text="item.profile.user.name"></span> empezó a seguir a 
                                    <a :href="`/profiles/${item.followed_profile.id}`" class="text-red-500 hover:text-red-400 transition" x-text="item.followed_profile.user.name"></a>
                                </p>
                                
                                <a :href="`/profiles/${item.followed_profile.id}`">
                                    <img :src="item.followed_profile.avatar" 
                                         :alt="item.followed_profile.user.name"
                                         class="w-10 h-10 rounded-full object-cover">
                                </a>
                            </div>

                            <!-- Interaction Buttons -->
                            <div class="flex items-center space-x-4 mt-4 pt-4 border-t border-gray-700">
                                <button @click="likeItem(item)" 
                                        :class="item.is_liked ? 'text-red-500' : 'text-gray-400'"
                                        class="flex items-center space-x-1 hover:text-red-500 transition">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span x-text="item.likes_count"></span>
                                </button>

                                <button @click="commentOnItem(item)" 
                                        class="flex items-center space-x-1 text-gray-400 hover:text-white transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                    <span x-text="item.comments_count"></span>
                                </button>
                            </div>
                        </div>
                    </template>

                    <!-- Empty State -->
                    <div x-show="!loading && feedItems.length === 0" class="text-center py-12">
                        <svg class="w-24 h-24 mx-auto text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        <p class="text-gray-400 text-lg mb-4">Tu feed está vacío</p>
                        <p class="text-gray-500">Sigue a más personas para ver su actividad aquí</p>
                        <a href="{{ route('catalog.index') }}" class="inline-block mt-4 px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                            Explorar Catálogo
                        </a>
                    </div>

                    <!-- Load More -->
                    <div x-show="hasMore && !loading" class="text-center py-4">
                        <button @click="loadMore" 
                                class="px-6 py-3 bg-gray-800 text-white rounded-lg hover:bg-gray-700 transition">
                            Cargar más
                        </button>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1">
                <!-- Suggested People to Follow -->
                <div class="bg-gray-800 rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-white mb-4">Personas sugeridas</h3>
                    
                    <div class="space-y-4">
                        @foreach($suggestedProfiles as $suggestedProfile)
                        <div class="flex items-center justify-between">
                            <a href="{{ route('profiles.show', $suggestedProfile) }}" class="flex items-center space-x-3">
                                <img src="{{ $suggestedProfile->avatar }}" 
                                     alt="{{ $suggestedProfile->user->name }}"
                                     class="w-10 h-10 rounded-full object-cover">
                                <div>
                                    <p class="font-medium text-white hover:text-red-500 transition">{{ $suggestedProfile->user->name }}</p>
                                    <p class="text-sm text-gray-400">{{ $suggestedProfile->followers_count }} seguidores</p>
                                </div>
                            </a>
                            
                            <form action="{{ route('profiles.follow', $suggestedProfile) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-3 py-1 bg-red-600 text-white text-sm rounded-full hover:bg-red-700 transition">
                                    Seguir
                                </button>
                            </form>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Trending Content -->
                <div class="bg-gray-800 rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-white mb-4">Contenido popular</h3>
                    
                    <div class="space-y-3">
                        @foreach($trendingTitles as $title)
                        <a href="{{ route('titles.show', $title) }}" class="flex items-center space-x-3 hover:bg-gray-750 rounded-lg p-2 -m-2 transition">
                            <img src="{{ $title->poster_url }}" 
                                 alt="{{ $title->name }}"
                                 class="w-12 h-16 object-cover rounded">
                            <div>
                                <p class="font-medium text-white text-sm">{{ $title->name }}</p>
                                <p class="text-xs text-gray-400">{{ $title->ratings()->count() }} reseñas</p>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function feedManager() {
    return {
        feedItems: [],
        loading: false,
        page: 1,
        hasMore: true,
        
        async loadFeed() {
            this.loading = true;
            try {
                const response = await fetch(`/api/profiles/feed?page=${this.page}`);
                const data = await response.json();
                
                this.feedItems = [...this.feedItems, ...data.items];
                this.hasMore = data.has_more;
                this.page++;
            } catch (error) {
                console.error('Error loading feed:', error);
            } finally {
                this.loading = false;
            }
        },
        
        async loadMore() {
            await this.loadFeed();
        },
        
        async likeItem(item) {
            try {
                const response = await fetch(`/api/feed-items/${item.id}/like`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const data = await response.json();
                item.is_liked = data.is_liked;
                item.likes_count = data.likes_count;
            } catch (error) {
                console.error('Error liking item:', error);
            }
        },
        
        commentOnItem(item) {
            // Open comment modal or navigate to item
            if (item.type === 'rating' || item.type === 'comment') {
                window.location.href = `/titles/${item.title.slug}#comments`;
            }
        },
        
        formatTime(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffMs = now - date;
            const diffMins = Math.floor(diffMs / 60000);
            const diffHours = Math.floor(diffMs / 3600000);
            const diffDays = Math.floor(diffMs / 86400000);
            
            if (diffMins < 1) return 'Justo ahora';
            if (diffMins < 60) return `hace ${diffMins} minutos`;
            if (diffHours < 24) return `hace ${diffHours} horas`;
            if (diffDays < 30) return `hace ${diffDays} días`;
            
            return date.toLocaleDateString('es');
        }
    }
}
</script>
@endsection