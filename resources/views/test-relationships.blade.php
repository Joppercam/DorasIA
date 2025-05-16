<x-app-layout>
    <x-slot name="title">Test de Relaciones</x-slot>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-3xl font-bold mb-8">Test de Relaciones User/Profile/Comments/Ratings</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <!-- Test User -> Comments -->
            <div class="bg-gray-900 rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4">User -> Comments</h2>
                @auth
                    <p class="text-gray-400 mb-2">Usuario actual: {{ auth()->user()->name }}</p>
                    <p class="text-gray-400 mb-4">Total comentarios: {{ auth()->user()->comments()->count() }}</p>
                    
                    <div class="space-y-2">
                        @foreach(auth()->user()->comments()->limit(5)->get() as $comment)
                            <div class="bg-gray-800 rounded p-3">
                                <p class="text-sm text-gray-300">{{ Str::limit($comment->content, 100) }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ $comment->created_at->diffForHumans() }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-400">Debes iniciar sesión para ver tus comentarios</p>
                @endauth
            </div>
            
            <!-- Test User -> Ratings -->
            <div class="bg-gray-900 rounded-lg p-6">
                <h2 class="text-xl font-semibold mb-4">User -> Ratings</h2>
                @auth
                    <p class="text-gray-400 mb-2">Usuario actual: {{ auth()->user()->name }}</p>
                    <p class="text-gray-400 mb-4">Total valoraciones: {{ auth()->user()->ratings()->count() }}</p>
                    
                    <div class="space-y-2">
                        @foreach(auth()->user()->ratings()->with('title')->limit(5)->get() as $rating)
                            <div class="bg-gray-800 rounded p-3">
                                <p class="text-sm text-gray-300">
                                    {{ $rating->title->title }} - 
                                    <span class="text-yellow-400">{{ $rating->score / 2 }}/5</span>
                                </p>
                                <p class="text-xs text-gray-500 mt-1">{{ $rating->created_at->diffForHumans() }}</p>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-400">Debes iniciar sesión para ver tus valoraciones</p>
                @endauth
            </div>
            
            <!-- Test global stats -->
            <div class="bg-gray-900 rounded-lg p-6 md:col-span-2">
                <h2 class="text-xl font-semibold mb-4">Estadísticas Globales</h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <p class="text-2xl font-bold text-white">{{ \App\Models\User::has('comments')->count() }}</p>
                        <p class="text-gray-400 text-sm">Usuarios con comentarios</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-white">{{ \App\Models\User::has('ratings')->count() }}</p>
                        <p class="text-gray-400 text-sm">Usuarios con valoraciones</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-white">{{ \App\Models\Profile::has('comments')->count() }}</p>
                        <p class="text-gray-400 text-sm">Perfiles con comentarios</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-white">{{ \App\Models\Profile::has('ratings')->count() }}</p>
                        <p class="text-gray-400 text-sm">Perfiles con valoraciones</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>