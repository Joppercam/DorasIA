<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-start md:items-center mb-8">
                @if ($person->profile_path)
                    <img src="{{ asset('storage/' . $person->profile_path) }}" 
                         alt="{{ $person->name }}" 
                         class="w-20 h-20 md:w-24 md:h-24 rounded-full object-cover mr-6 mb-4 md:mb-0">
                @endif
                
                <div>
                    <h1 class="text-3xl font-bold text-white">{{ $person->name }}</h1>
                    <div class="flex items-center mt-2">
                        <a href="{{ route('people.show', $person->slug) }}" class="text-red-400 hover:text-red-300 mr-6">
                            View Profile
                        </a>
                        <span class="text-gray-400">{{ $news->total() }} News Articles</span>
                    </div>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($news as $article)
                    <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden hover:ring-2 hover:ring-red-600 transition-all duration-300">
                        <a href="{{ route('news.show', $article->slug) }}" class="block">
                            <div class="relative h-48 overflow-hidden">
                                @if ($article->image)
                                    <img src="{{ asset('storage/' . $article->image) }}" 
                                        alt="{{ $article->title }}" 
                                        class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-gray-700 flex items-center justify-center">
                                        <span class="text-gray-400">No image</span>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="p-4">
                                <h3 class="text-lg font-semibold text-white mb-2 line-clamp-2">{{ $article->title }}</h3>
                                <div class="flex items-center text-gray-400 text-xs">
                                    <span>{{ $article->published_at->format('M j, Y') }}</span>
                                    @if ($article->source_name)
                                        <span class="mx-2">•</span>
                                        <span>{{ $article->source_name }}</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
            
            <div class="mt-8">
                {{ $news->links() }}
            </div>
            
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