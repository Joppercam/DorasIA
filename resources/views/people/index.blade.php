<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between mb-8">
                <h1 class="text-3xl font-bold text-white mb-4 md:mb-0">Popular Actors</h1>
                
                <div class="flex items-center space-x-4">
                    <a href="{{ route('people.popular') }}" class="bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition-colors">
                        View All Actors
                    </a>
                    
                    <form action="{{ route('people.search') }}" method="GET" class="relative">
                        <input type="text" name="q" placeholder="Search actors..." 
                            class="bg-gray-700 text-white rounded-full px-4 py-2 pl-10 focus:outline-none focus:ring-2 focus:ring-red-500 w-full md:w-64">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 absolute left-3 top-2.5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                        <button type="submit" class="hidden">Search</button>
                    </form>
                </div>
            </div>
            
            <div class="mb-12">
                <h2 class="text-2xl font-bold text-white mb-6">Trending Actors</h2>
                
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                    @foreach ($popularPeople->take(12) as $person)
                        <a href="{{ route('people.show', $person->slug) }}" 
                           class="block group">
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
                            <h3 class="text-white font-medium truncate text-center">{{ $person->name }}</h3>
                        </a>
                    @endforeach
                </div>
            </div>
            
            <!-- News Section -->
            <div>
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-white">Latest Actor News</h2>
                    <a href="{{ route('news.index') }}" class="text-red-400 hover:text-red-300">
                        View All News
                    </a>
                </div>
                
                <!-- We'd need to inject news data here, but this is just a template -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- This would be filled by the controller with actual news data -->
                    <div class="col-span-1 md:col-span-3 text-center py-10 text-gray-400">
                        <p>Check out the <a href="{{ route('news.index') }}" class="text-red-400 hover:text-red-300">news section</a> for the latest updates on your favorite actors.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>