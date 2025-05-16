@props(['news'])

<article class="relative group overflow-hidden rounded-lg cursor-pointer transform transition-all duration-300 hover:scale-105 hover:z-10">
    <a href="{{ route('news.show', $news->slug) }}" class="block relative h-64 bg-gray-900">
        @if($news->image)
            <img 
                src="{{ asset($news->image) }}" 
                alt="{{ $news->title }}"
                class="w-full h-full object-cover transition-opacity duration-300 group-hover:opacity-80"
                loading="lazy"
            >
        @else
            <div class="w-full h-full bg-gradient-to-br from-gray-800 to-gray-900 flex items-center justify-center">
                <svg class="h-16 w-16 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                </svg>
            </div>
        @endif
        
        <!-- Overlay gradient -->
        <div class="absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent opacity-90"></div>
        
        <!-- Play button on hover -->
        <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
            <div class="bg-red-600 rounded-full p-3 transform scale-90 group-hover:scale-100 transition-transform duration-300">
                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
        
        <!-- Content -->
        <div class="absolute bottom-0 left-0 right-0 p-4">
            <h3 class="text-white text-lg font-semibold line-clamp-2 mb-2">
                {{ $news->title }}
            </h3>
            
            @if($news->people->isNotEmpty())
            <div class="flex items-center space-x-2 mb-2">
                <span class="text-gray-400 text-sm">Con:</span>
                <div class="flex -space-x-2">
                    @foreach($news->people->take(3) as $person)
                    <div class="w-6 h-6 rounded-full overflow-hidden ring-2 ring-gray-900">
                        @if($person->profile_path)
                            <img 
                                src="{{ $person->getProfilePath() }}" 
                                alt="{{ $person->name }}"
                                class="w-full h-full object-cover"
                                title="{{ $person->name }}"
                            >
                        @else
                            <div class="w-full h-full bg-gray-700 flex items-center justify-center">
                                <span class="text-xs text-white">{{ substr($person->name, 0, 1) }}</span>
                            </div>
                        @endif
                    </div>
                    @endforeach
                    @if($news->people->count() > 3)
                    <div class="w-6 h-6 rounded-full bg-gray-700 flex items-center justify-center ring-2 ring-gray-900">
                        <span class="text-xs text-white">+{{ $news->people->count() - 3 }}</span>
                    </div>
                    @endif
                </div>
            </div>
            @endif
            
            <div class="flex items-center justify-between text-xs text-gray-400">
                <span>{{ $news->published_at->diffForHumans() }}</span>
                @if($news->source_name)
                <span>{{ $news->source_name }}</span>
                @endif
            </div>
        </div>
    </a>
</article>