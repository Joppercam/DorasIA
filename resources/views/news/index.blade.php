@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-4">Noticias del Entretenimiento Asiático</h1>
            <p class="text-gray-400 text-lg">Las últimas novedades de doramas, películas y celebridades asiáticas</p>
        </div>

        <!-- News Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($news as $newsItem)
            <x-netflix-news-card-fixed :news="$newsItem" />
            @endforeach
        </div>

        <!-- Pagination -->
        @if($news->hasPages())
        <div class="mt-12">
            {{ $news->links() }}
        </div>
        @endif
    </div>
</div>
@endsection