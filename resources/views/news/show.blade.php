<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                <div class="relative">
                    @if ($news->image)
                        <img src="{{ asset('storage/' . $news->image) }}" 
                            alt="{{ $news->title }}" 
                            class="w-full h-64 md:h-80 object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-gray-800 to-transparent"></div>
                    @endif
                    
                    <div class="p-6 relative">
                        <div class="flex flex-wrap mb-4">
                            @foreach ($news->people as $person)
                                <a href="{{ route('people.show', $person->slug) }}" 
                                   class="inline-flex items-center px-3 py-1 bg-red-600 text-white text-sm font-medium rounded-full mr-2 mb-2">
                                    @if ($person->profile_path)
                                        <img src="{{ asset('storage/' . $person->profile_path) }}" alt="{{ $person->name }}" class="w-5 h-5 rounded-full mr-1 object-cover">
                                    @endif
                                    {{ $person->name }}
                                </a>
                            @endforeach
                        </div>
                        
                        <h1 class="text-2xl md:text-3xl font-bold text-white mb-4">{{ $news->title }}</h1>
                        
                        <div class="flex items-center text-gray-300 text-sm mb-6">
                            <span>{{ $news->published_at->format('F j, Y') }}</span>
                            @if ($news->source_name)
                                <span class="mx-2">•</span>
                                <span>
                                    @if ($news->source_url)
                                        <a href="{{ $news->source_url }}" target="_blank" class="text-red-400 hover:text-red-300">
                                            {{ $news->source_name }}
                                        </a>
                                    @else
                                        {{ $news->source_name }}
                                    @endif
                                </span>
                            @endif
                        </div>
                        
                        <div class="prose prose-invert max-w-none">
                            {!! nl2br(e($news->content)) !!}
                        </div>
                        
                        @if ($news->source_url)
                            <div class="mt-8 text-sm text-gray-400">
                                <a href="{{ $news->source_url }}" target="_blank" class="text-red-400 hover:text-red-300">
                                    Read original article
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            @if ($relatedNews->count() > 0)
                <div class="mt-10">
                    <h2 class="text-xl font-bold text-white mb-6">Related News</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach ($relatedNews as $relatedItem)
                            <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden flex">
                                <a href="{{ route('news.show', $relatedItem->slug) }}" class="block flex-grow flex">
                                    @if ($relatedItem->image)
                                        <div class="w-1/3">
                                            <img src="{{ asset('storage/' . $relatedItem->image) }}" 
                                                 alt="{{ $relatedItem->title }}" 
                                                 class="w-full h-full object-cover">
                                        </div>
                                    @endif
                                    
                                    <div class="p-4 flex-grow">
                                        <h3 class="text-lg font-semibold text-white mb-2 line-clamp-2">{{ $relatedItem->title }}</h3>
                                        <div class="flex items-center text-gray-400 text-xs">
                                            <span>{{ $relatedItem->published_at->format('M j, Y') }}</span>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            
            <div class="mt-8 mb-4">
                <a href="{{ route('news.index') }}" class="inline-flex items-center text-red-400 hover:text-red-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Back to all news
                </a>
            </div>
        </div>
    </div>
</x-app-layout>