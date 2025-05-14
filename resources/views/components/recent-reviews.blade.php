@props(['reviews'])

<div class="bg-gray-900 rounded-lg overflow-hidden">
    <div class="p-4 border-b border-gray-800">
        <h3 class="text-lg font-bold">Valoraciones recientes</h3>
    </div>
    
    <div class="divide-y divide-gray-800">
        @forelse($reviews as $rating)
            <div class="p-4 hover:bg-gray-800 transition duration-200">
                <div class="flex items-start gap-3">
                    <div class="h-10 w-10 rounded-full overflow-hidden bg-gray-800 flex-shrink-0">
                        <img src="{{ asset('images/profiles/' . ($rating->profile->avatar ?? 'default.jpg')) }}" 
                             alt="{{ $rating->profile->name ?? 'Usuario' }}" 
                             class="h-full w-full object-cover">
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between items-start">
                            <div>
                                <a href="{{ route('titles.show', $rating->title->slug) }}" class="font-medium hover:text-red-500 transition">
                                    {{ $rating->title->title }}
                                </a>
                                <div class="flex items-center mt-1">
                                    <span class="text-red-500 text-sm font-bold mr-1">{{ $rating->rating }}</span>
                                    <span class="text-xs text-gray-400">/10</span>
                                    <span class="text-xs text-gray-400 ml-2">por {{ $rating->profile->name ?? 'Usuario' }}</span>
                                </div>
                            </div>
                            <span class="text-xs text-gray-400">{{ $rating->created_at->diffForHumans() }}</span>
                        </div>
                        
                        @if($rating->review)
                            <p class="text-sm text-gray-300 mt-2 line-clamp-2">{{ $rating->review }}</p>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="p-6 text-center text-gray-400">
                <p>No hay valoraciones recientes.</p>
            </div>
        @endforelse
    </div>
    
    <div class="p-3 bg-gray-800 text-center">
        <a href="{{ route('catalog.index') }}" class="text-sm text-red-500 hover:text-red-400">
            Ver más títulos para valorar
        </a>
    </div>
</div>