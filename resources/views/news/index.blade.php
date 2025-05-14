<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold text-white mb-6">Latest Actor News</h1>
            
            @if ($featuredNews->count() > 0)
                <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden mb-10">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($featuredNews as $index => $news)
                            <div @class([
                                'relative overflow-hidden h-80 group transition-all duration-300 ease-in-out',
                                'md:col-span-2 lg:col-span-2' => $index === 0,
                            ])>
                                <a href="{{ route('news.show', $news->slug) }}" class="block h-full">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent z-10"></div>
                                    
                                    @if ($news->image)
                                        <img src="{{ asset('storage/' . $news->image) }}" 
                                            alt="{{ $news->title }}" 
                                            class="w-full h-full object-cover transition-transform duration-500 ease-in-out group-hover:scale-105">
                                    @else
                                        <div class="w-full h-full bg-gray-700 flex items-center justify-center">
                                            <span class="text-gray-400">No image</span>
                                        </div>
                                    @endif
                                    
                                    <div class="absolute bottom-0 left-0 p-4 z-20 w-full">
                                        <div class="flex items-center mb-2">
                                            @foreach ($news->people->take(3) as $person)
                                                <a href="{{ route('people.show', $person->slug) }}" class="text-xs bg-red-600 text-white px-2 py-1 rounded-full mr-2">
                                                    {{ $person->name }}
                                                </a>
                                            @endforeach
                                        </div>
                                        <h2 class="text-xl md:text-2xl font-bold text-white mb-2">{{ $news->title }}</h2>
                                        <div class="flex items-center text-gray-300 text-sm">
                                            <span>{{ $news->published_at->format('M j, Y') }}</span>
                                            @if ($news->source_name)
                                                <span class="mx-2">•</span>
                                                <span>{{ $news->source_name }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            
            <div class="mt-10">
                <h2 class="text-2xl font-bold text-white mb-6">All News</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach ($latestNews as $news)
                        <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden hover:ring-2 hover:ring-red-600 transition-all duration-300">
                            <a href="{{ route('news.show', $news->slug) }}" class="block">
                                <div class="relative h-48 overflow-hidden">
                                    @if ($news->image)
                                        <img src="{{ asset('storage/' . $news->image) }}" 
                                            alt="{{ $news->title }}" 
                                            class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-gray-700 flex items-center justify-center">
                                            <span class="text-gray-400">No image</span>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="p-4">
                                    <div class="flex flex-wrap mb-2">
                                        @foreach ($news->people->take(2) as $person)
                                            <a href="{{ route('people.show', $person->slug) }}" class="text-xs bg-red-600 text-white px-2 py-1 rounded-full mr-2 mb-2">
                                                {{ $person->name }}
                                            </a>
                                        @endforeach
                                    </div>
                                    <h3 class="text-lg font-semibold text-white mb-2 line-clamp-2">{{ $news->title }}</h3>
                                    <div class="flex items-center text-gray-400 text-xs">
                                        <span>{{ $news->published_at->format('M j, Y') }}</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-8">
                    {{ $latestNews->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>