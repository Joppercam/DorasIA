@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-gray-900 to-black">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-white">{{ $profile->user->name }} está siguiendo</h1>
                    <p class="text-gray-400 mt-1">{{ $profile->following_count }} siguiendo</p>
                </div>
                <a href="{{ route('profiles.show', $profile) }}" class="flex items-center space-x-2 text-gray-400 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    <span>Volver al perfil</span>
                </a>
            </div>
        </div>

        <!-- Following Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($following as $followed)
            <div class="bg-gray-800 rounded-lg p-4 hover:bg-gray-750 transition" x-data="{ hovering: false }" @mouseenter="hovering = true" @mouseleave="hovering = false">
                <a href="{{ route('profiles.show', $followed) }}" class="flex items-start space-x-4">
                    <!-- Avatar -->
                    <div class="relative">
                        <img src="{{ $followed->avatar }}" alt="{{ $followed->user->name }}"
                             class="w-16 h-16 rounded-full object-cover transition duration-300"
                             :class="{ 'scale-110': hovering }">
                        @if($followed->is_verified)
                        <div class="absolute -bottom-1 -right-1 bg-blue-500 rounded-full p-1">
                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        @endif
                    </div>

                    <!-- User Info -->
                    <div class="flex-1">
                        <h3 class="font-semibold text-white hover:text-red-500 transition">{{ $followed->user->name }}</h3>
                        <p class="text-sm text-gray-400">@{{ $followed->username ?? $followed->user->id }}</p>
                        
                        @if($followed->bio)
                        <p class="text-sm text-gray-300 mt-2 line-clamp-2">{{ $followed->bio }}</p>
                        @endif

                        <div class="flex items-center space-x-4 mt-3 text-xs text-gray-500">
                            <span>{{ $followed->followers_count }} seguidores</span>
                            <span>{{ $followed->ratings()->count() }} reseñas</span>
                            <span>{{ $followed->watchlist()->count() }} en lista</span>
                        </div>
                    </div>
                </a>

                <!-- Follow Button -->
                @auth
                    @if(auth()->user()->profile->id !== $followed->id)
                    <div class="mt-4">
                        @if(auth()->user()->profile->isFollowing($followed))
                        <form action="{{ route('profiles.unfollow', $followed) }}" method="POST" x-data>
                            @csrf
                            <button type="submit" 
                                    class="w-full px-4 py-2 bg-gray-700 text-gray-300 rounded-lg hover:bg-gray-600 transition">
                                Siguiendo
                            </button>
                        </form>
                        @else
                        <form action="{{ route('profiles.follow', $followed) }}" method="POST" x-data>
                            @csrf
                            <button type="submit" 
                                    class="w-full px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                                Seguir
                            </button>
                        </form>
                        @endif
                    </div>
                    @endif
                @endauth
            </div>
            @empty
            <div class="col-span-full text-center py-12">
                <svg class="w-16 h-16 mx-auto text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <p class="text-gray-400">{{ $profile->user->name }} aún no sigue a nadie</p>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($following->hasPages())
        <div class="mt-8">
            {{ $following->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Toast Notifications -->
<div x-data="{ 
     toasts: [],
     addToast(message, type = 'success') {
         const toast = { id: Date.now(), message, type };
         this.toasts.push(toast);
         setTimeout(() => {
             this.toasts = this.toasts.filter(t => t.id !== toast.id);
         }, 3000);
     }
}" 
     x-init="
     @if(session('success'))
         addToast('{{ session('success') }}', 'success');
     @endif
     @if(session('error')) 
         addToast('{{ session('error') }}', 'error');
     @endif
     "
     class="fixed bottom-4 right-4 z-50 space-y-2">
    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="true" 
             x-transition:enter="transform ease-out duration-300"
             x-transition:enter-start="translate-x-full opacity-0"
             x-transition:enter-end="translate-x-0 opacity-100"
             x-transition:leave="transform ease-in duration-200"
             x-transition:leave-start="translate-x-0 opacity-100"
             x-transition:leave-end="translate-x-full opacity-0"
             :class="{
                 'bg-green-500': toast.type === 'success',
                 'bg-red-500': toast.type === 'error'
             }"
             class="text-white px-6 py-3 rounded-lg shadow-lg">
            <span x-text="toast.message"></span>
        </div>
    </template>
</div>
@endsection