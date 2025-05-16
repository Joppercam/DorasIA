@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-5xl mx-auto">
        <!-- Breadcrumb -->
        <nav class="text-sm mb-6">
            <ol class="list-none p-0 inline-flex">
                <li class="flex items-center">
                    <a href="{{ route('home') }}" class="text-gray-400 hover:text-white transition-colors">Inicio</a>
                    <svg class="fill-current w-3 h-3 mx-3 text-gray-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                        <path d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z"/>
                    </svg>
                </li>
                <li class="flex items-center">
                    <a href="{{ route('news.index') }}" class="text-gray-400 hover:text-white transition-colors">Noticias</a>
                    <svg class="fill-current w-3 h-3 mx-3 text-gray-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512">
                        <path d="M285.476 272.971L91.132 467.314c-9.373 9.373-24.569 9.373-33.941 0l-22.667-22.667c-9.357-9.357-9.375-24.522-.04-33.901L188.505 256 34.484 101.255c-9.335-9.379-9.317-24.544.04-33.901l22.667-22.667c9.373-9.373 24.569-9.373 33.941 0L285.475 239.03c9.373 9.372 9.373 24.568.001 33.941z"/>
                    </svg>
                </li>
                <li>
                    <span class="text-gray-500 truncate max-w-xs">{{ Str::limit($news->title, 50) }}</span>
                </li>
            </ol>
        </nav>
        
        <!-- Main News Article -->
        <article class="bg-gray-900 rounded-lg overflow-hidden shadow-lg">
            <!-- News Header -->
            <div class="p-8">
                <!-- Date and Category -->
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-4">
                        <span class="px-3 py-1 bg-red-600 text-white text-xs font-bold rounded uppercase">
                            Noticia
                        </span>
                        <time class="text-gray-400 text-sm">
                            {{ $news->published_at->format('d \d\e F, Y') }}
                        </time>
                    </div>
                    <span class="text-gray-400 text-sm italic">
                        {{ $news->source_name ?? 'Dorasia News' }}
                    </span>
                </div>

                <!-- Title -->
                <h1 class="text-3xl md:text-4xl font-bold text-white mb-6">
                    {{ $news->title }}
                </h1>

                <!-- Feature Image if exists -->
                @if($news->image && file_exists(public_path($news->image)))
                <div class="mb-8 rounded-lg overflow-hidden">
                    <img src="{{ asset($news->image) }}" 
                         alt="{{ $news->title }}" 
                         class="w-full h-auto">
                </div>
                @endif

                <!-- Content -->
                <div class="text-gray-300 text-lg leading-relaxed mb-8 news-content">
                    {!! nl2br(e($news->content)) !!}
                </div>

                <!-- Actors Section -->
                @if($news->people->isNotEmpty())
                <div class="border-t border-gray-800 pt-8">
                    <h3 class="text-xl font-bold text-white mb-6">Actores Mencionados</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                        @foreach($news->people as $person)
                        <a href="{{ route('people.show', $person->slug) }}" 
                           class="flex flex-col items-center group">
                            @if($person->profile_path)
                            <img src="{{ asset($person->profile_path) }}" 
                                 alt="{{ $person->name }}" 
                                 class="w-24 h-24 rounded-full object-cover border-3 border-gray-700 group-hover:border-gray-500 transition-all duration-200"
                                 onerror="this.onerror=null; this.src='{{ asset('images/actor-placeholder.jpg') }}'">
                            @else
                            <div class="w-24 h-24 rounded-full bg-gray-700 border-3 border-gray-600 flex items-center justify-center text-white font-bold text-2xl group-hover:border-gray-500 transition-all duration-200">
                                {{ strtoupper(substr($person->name, 0, 1)) }}
                            </div>
                            @endif
                            <span class="mt-2 text-gray-300 text-sm text-center group-hover:text-white transition-colors">
                                {{ $person->name }}
                            </span>
                        </a>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- External Links if available -->
                @if($news->source_url)
                <div class="mt-8 pt-8 border-t border-gray-800">
                    <a href="{{ $news->source_url }}" 
                       target="_blank" 
                       rel="noopener noreferrer"
                       class="inline-flex items-center text-red-500 hover:text-red-400 transition-colors">
                        <span>Ver noticia completa en la fuente original</span>
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                        </svg>
                    </a>
                </div>
                @endif
            </div>
        </article>

        <!-- Related News Section -->
        @if($relatedNews->isNotEmpty())
        <div class="mt-12">
            <h2 class="text-2xl font-bold text-white mb-6">Noticias Relacionadas</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($relatedNews as $related)
                <a href="{{ route('news.show', $related->slug) }}" 
                   class="group">
                    <div class="bg-gray-900 rounded-lg overflow-hidden hover:ring-2 hover:ring-red-600 transition-all duration-300">
                        @if($related->image && file_exists(public_path($related->image)))
                        <div class="aspect-w-16 aspect-h-9 bg-gray-800">
                            <img src="{{ asset($related->image) }}" 
                                 alt="{{ $related->title }}" 
                                 class="w-full h-full object-cover">
                        </div>
                        @endif
                        <div class="p-4">
                            <h3 class="text-white font-semibold line-clamp-2 group-hover:text-red-400 transition-colors">
                                {{ $related->title }}
                            </h3>
                            <time class="text-gray-400 text-sm mt-2 block">
                                {{ $related->published_at->format('d/m/Y') }}
                            </time>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Actor News Sections -->
        @if(!empty($actorNews))
        @foreach($actorNews as $actorName => $personNewsItems)
        <div class="mt-12">
            <h2 class="text-2xl font-bold text-white mb-6">Más noticias sobre {{ $actorName }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($personNewsItems as $personNews)
                <a href="{{ route('news.show', $personNews->slug) }}" 
                   class="group">
                    <div class="bg-gray-900 rounded-lg p-6 hover:ring-2 hover:ring-red-600 transition-all duration-300">
                        <h3 class="text-white font-semibold line-clamp-2 group-hover:text-red-400 transition-colors mb-3">
                            {{ $personNews->title }}
                        </h3>
                        <p class="text-gray-400 text-sm line-clamp-3 mb-4">
                            {{ $personNews->content }}
                        </p>
                        <time class="text-gray-500 text-sm">
                            {{ $personNews->published_at->format('d/m/Y') }}
                        </time>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
        @endforeach
        @endif
    </div>
</div>

<style>
    .news-content {
        white-space: pre-wrap;
        word-wrap: break-word;
    }
    
    .border-3 {
        border-width: 3px;
    }
    
    /* Aspect ratio utilities for images */
    .aspect-w-16 {
        position: relative;
        padding-bottom: 56.25%;
    }
    
    .aspect-w-16 > * {
        position: absolute;
        height: 100%;
        width: 100%;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
    }
</style>
@endsection