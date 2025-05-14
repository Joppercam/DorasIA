<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Search Results</h1>
                    <p class="text-gray-400">Found {{ $people->total() }} results for "{{ $query }}"</p>
                </div>
                
                <form action="{{ route('people.search') }}" method="GET" class="relative mt-4 md:mt-0">
                    <input type="text" name="q" value="{{ $query }}" placeholder="Search actors..." 
                        class="bg-gray-700 text-white rounded-full px-4 py-2 pl-10 focus:outline-none focus:ring-2 focus:ring-red-500 w-full md:w-64">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 absolute left-3 top-2.5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                    </svg>
                    <button type="submit" class="hidden">Search</button>
                </form>
            </div>
            
            @if ($people->count() > 0)
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-6">
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
                            
                            @if ($person->original_name && $person->original_name !== $person->name)
                                <p class="text-sm text-gray-400 line-clamp-1">{{ $person->original_name }}</p>
                            @endif
                        </a>
                    @endforeach
                </div>
            @else
                <div class="bg-gray-800 rounded-lg p-8 text-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <h3 class="text-xl font-semibold text-white mb-2">No Results Found</h3>
                    <p class="text-gray-400">We couldn't find any actors matching "{{ $query }}"</p>
                    <a href="{{ route('people.index') }}" class="inline-block mt-4 px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                        Browse Popular Actors
                    </a>
                </div>
            @endif
            
            <div class="mt-8">
                {{ $people->links() }}
            </div>
        </div>
    </div>
</x-app-layout>