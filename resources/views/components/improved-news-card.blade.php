@props(['news'])

<div class="netflix-card news-card-container group">
    <a href="{{ route('news.show', $news->slug) }}" class="block h-full">
        <div class="h-full flex flex-col relative overflow-hidden">
            <!-- Featured actors section -->
            <div class="relative h-64 bg-gradient-to-t from-gray-900 to-gray-800">
                @if($news->people->isNotEmpty())
                    <!-- Main featured actor -->
                    @if($news->people->first()->profile_path)
                        <div class="absolute inset-0">
                            <img src="{{ asset($news->people->first()->profile_path) }}" 
                                 alt="{{ $news->people->first()->name }}" 
                                 class="w-full h-full object-cover object-[center_30%]">
                            <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/80 to-gray-900/40"></div>
                        </div>
                    @endif
                    
                    <!-- Secondary actors grid -->
                    @if($news->people->count() > 1)
                    <div class="absolute top-0 right-0 w-1/3 h-full">
                        <div class="grid grid-rows-3 h-full opacity-60">
                            @foreach($news->people->skip(1)->take(3) as $person)
                                @if($person->profile_path)
                                <div class="relative overflow-hidden">
                                    <img src="{{ asset($person->profile_path) }}" 
                                         alt="{{ $person->name }}" 
                                         class="w-full h-full object-cover object-[center_30%]">
                                    <div class="absolute inset-0 bg-gradient-to-l from-gray-900/60 to-transparent"></div>
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                    @endif
                @else
                    <div class="absolute inset-0 bg-gradient-to-br from-dorasia-red/20 to-gray-900/95"></div>
                @endif
                
                <!-- Header overlay -->
                <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-transparent to-transparent"></div>
                
                <!-- Header with date and badge -->
                <div class="absolute top-4 left-4 right-4 flex items-start justify-between z-10">
                    <span class="px-3 py-1 bg-dorasia-red text-white text-xs font-bold rounded-sm uppercase shadow-lg">
                        Noticia
                    </span>
                    <span class="text-white bg-black/60 px-2 py-1 rounded text-sm">
                        {{ $news->published_at->format('d/m/Y') }}
                    </span>
                </div>
                
                <!-- Title overlay at bottom -->
                <div class="absolute bottom-0 left-0 right-0 p-6 z-10">
                    <h3 class="text-white font-bold text-2xl mb-2 text-shadow-lg">
                        {{ $news->title }}
                    </h3>
                </div>
            </div>
            
            <!-- Content section -->
            <div class="p-6 flex-grow flex flex-col">
                <!-- Content preview -->
                <p class="text-gray-300 text-sm mb-4 line-clamp-3 flex-grow leading-relaxed">
                    {{ $news->content }}
                </p>
                
                <!-- Footer -->
                <div class="mt-auto">
                    <!-- Actor names -->
                    @if($news->people->isNotEmpty())
                    <div class="mb-3">
                        <span class="text-gray-500 text-xs uppercase tracking-wide">Protagonistas:</span>
                        <p class="text-gray-200 text-sm mt-1">
                            @foreach($news->people->take(3) as $index => $person)
                                {{ $person->name }}@if(!$loop->last), @endif
                            @endforeach
                            @if($news->people->count() > 3)
                                y {{ $news->people->count() - 3 }} más
                            @endif
                        </p>
                    </div>
                    @endif
                    
                    <!-- Source and action -->
                    <div class="flex items-center justify-between pt-3 border-t border-gray-800">
                        <span class="text-gray-500 text-xs italic">
                            {{ $news->source_name ?? 'Dorasia News' }}
                        </span>
                        <span class="text-dorasia-red text-sm font-medium group-hover:text-red-400 flex items-center transition-colors">
                            Leer noticia
                            <svg class="w-4 h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                            </svg>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </a>
</div>

<style>
.text-shadow-lg {
    text-shadow: 0 2px 4px rgba(0,0,0,0.8);
}
</style>