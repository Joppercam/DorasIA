<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-8">
                <h1 class="text-3xl font-bold text-white mb-4 md:mb-0">{{ $title ?? 'Popular Actors' }}</h1>
                
                <form action="{{ route('people.search') }}" method="GET" class="relative">
                    <input type="text" name="q" placeholder="Search actors..." 
                        class="bg-gray-700 text-white rounded-full px-4 py-2 pl-10 focus:outline-none focus:ring-2 focus:ring-red-500 w-full md:w-64">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 absolute left-3 top-2.5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                    <button type="submit" class="hidden">Search</button>
                </form>
            </div>
            
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-6">
                @foreach ($people as $person)
                    <a href="{{ route('people.show', $person->slug) }}" class="block group">
                        <div class="aspect-[2/3] overflow-hidden rounded-lg mb-2 bg-gray-800">
                            @if ($person->profile_path)
                                <img src="{{ asset('storage/' . $person->profile_path) }}" 
                                    alt="{{ $person->name }}" 
                                    class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                            @else
                                <div class="w-full h-full bg-gray-700 flex items-center justify-center">
                                    <span class="text-gray-500">No image</span>
                                </div>
                            @endif
                        </div>
                        <h3 class="text-white font-medium line-clamp-2">{{ $person->name }}</h3>
                        
                        @if ($person->popularity)
                            <div class="flex items-center mt-1">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-500 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                </svg>
                                <span class="text-sm text-gray-400">{{ number_format($person->popularity, 1) }}</span>
                            </div>
                        @endif
                    </a>
                @endforeach
            </div>
            
            <div class="mt-8">
                {{ $people->links() }}
            </div>
        </div>
    </div>
</x-app-layout>