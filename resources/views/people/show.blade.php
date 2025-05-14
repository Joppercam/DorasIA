<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 rounded-lg shadow-lg overflow-hidden mb-10">
                <div class="md:flex">
                    <div class="md:w-1/3 lg:w-1/4">
                        @if ($person->profile_path)
                            <div class="aspect-[2/3] overflow-hidden bg-gray-900">
                                <img src="{{ asset('storage/' . $person->profile_path) }}" 
                                    alt="{{ $person->name }}" 
                                    class="w-full h-full object-cover">
                            </div>
                        @else
                            <div class="aspect-[2/3] bg-gray-700 flex items-center justify-center">
                                <span class="text-gray-500">No image</span>
                            </div>
                        @endif
                    </div>
                    
                    <div class="p-6 md:p-8 md:w-2/3 lg:w-3/4">
                        <h1 class="text-3xl font-bold text-white mb-2">{{ $person->name }}</h1>
                        
                        @if ($person->original_name && $person->original_name !== $person->name)
                            <p class="text-gray-400 mb-4">{{ $person->original_name }}</p>
                        @endif
                        
                        <div class="flex flex-wrap mb-6">
                            @if ($person->birthday)
                                <div class="mr-6 mb-2">
                                    <span class="block text-gray-400 text-sm">Born</span>
                                    <span class="text-white">{{ $person->birthday->format('M j, Y') }}</span>
                                    @if ($person->place_of_birth)
                                        <span class="text-white"> in {{ $person->place_of_birth }}</span>
                                    @endif
                                </div>
                            @endif
                            
                            @if ($person->country)
                                <div class="mr-6 mb-2">
                                    <span class="block text-gray-400 text-sm">From</span>
                                    <span class="text-white">{{ $person->country }}</span>
                                </div>
                            @endif
                            
                            @if ($person->deathday)
                                <div class="mb-2">
                                    <span class="block text-gray-400 text-sm">Died</span>
                                    <span class="text-white">{{ $person->deathday->format('M j, Y') }}</span>
                                </div>
                            @endif
                        </div>
                        
                        @if ($person->biography)
                            <div class="mb-6">
                                <h3 class="text-xl font-semibold text-white mb-2">Biography</h3>
                                <div class="text-gray-300 prose prose-invert max-w-none">
                                    <p>{{ \Illuminate\Support\Str::limit($person->biography, 500) }}</p>
                                </div>
                            </div>
                        @endif
                        
                        <div class="flex flex-wrap">
                            @if ($person->imdb_id)
                                <a href="https://www.imdb.com/name/{{ $person->imdb_id }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white text-sm font-medium rounded-md mr-2 mb-2">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M14.31 9.588v.005c-.077-.048-.227-.07-.42-.07H12.8v4.979h1.12c.145 0 .275-.02.379-.068a.55.55 0 0 0 .25-.197c.063-.092.11-.219.14-.375.032-.16.048-.369.048-.629v-2.38c0-.23-.014-.414-.039-.565a1.08 1.08 0 0 0-.133-.38.505.505 0 0 0-.244-.22M18.78 9.268h-1.345v5.234h1.345zm-11.289.57h-.732v3.855h.732a.7.7 0 0 0 .335-.068.44.44 0 0 0 .206-.194c.053-.091.089-.207.112-.35.023-.145.035-.326.035-.544v-1.541c0-.227-.012-.404-.035-.532a.838.838 0 0 0-.112-.336.455.455 0 0 0-.21-.18.736.736 0 0 0-.331-.11"/>
                                        <path fill-rule="evenodd" d="M21.718 20.212H2.282A2.288 2.288 0 0 1 0 17.93V6.07a2.288 2.288 0 0 1 2.282-2.283h19.436A2.288 2.288 0 0 1 24 6.07v11.86a2.288 2.288 0 0 1-2.282 2.282M8.851 15.723c-.121-.028-.26-.062-.421-.104a2.223 2.223 0 0 1-.474-.17 1.176 1.176 0 0 1-.342-.242.476.476 0 0 1-.128-.338v-5.031h1.347v3.916c.023.07.066.126.126.165.06.04.143.07.246.092a.635.635 0 0 0 .511-.047v-4.126h1.346v5.233h-1.346v-.296c-.04.03-.095.063-.164.1l-.7.048m6.319-.475a1.63 1.63 0 0 1-.3.256c-.121.075-.263.133-.428.173-.165.041-.352.062-.56.062h-1.464V9.517h1.34c.2 0 .393.029.578.088.185.058.35.147.493.265.142.12.255.271.336.457.082.186.122.412.122.677v2.878c0 .281-.039.516-.119.706a1.25 1.25 0 0 1-.338.47M18.78 8.814c-.506 0-.917.41-.917.914a.914.914 0 0 0 .917.913.912.912 0 0 0 .914-.913.912.912 0 0 0-.914-.914M6.73 9.588h.004c-.12.006-.236.076-.348.21-.112.133-.17.347-.17.641v3.437c0 .082.004.155.012.22.008.064.029.12.062.167.033.047.086.084.16.11.073.025.178.039.313.039h.171V9.589h-.204z" clip-rule="evenodd"/>
                                    </svg>
                                    IMDb
                                </a>
                            @endif
                            
                            @if ($person->instagram_id)
                                <a href="https://www.instagram.com/{{ $person->instagram_id }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-pink-600 text-white text-sm font-medium rounded-md mr-2 mb-2">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
                                    </svg>
                                    Instagram
                                </a>
                            @endif
                            
                            @if ($person->twitter_id)
                                <a href="https://twitter.com/{{ $person->twitter_id }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-500 text-white text-sm font-medium rounded-md mb-2">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                    </svg>
                                    Twitter
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            
            @if ($news->count() > 0)
                <div class="mb-12">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-white">Latest News</h2>
                        <a href="{{ route('news.person', $person->slug) }}" class="text-red-400 hover:text-red-300">
                            View All News
                        </a>
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
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
            
            @if ($titles->count() > 0)
                <div>
                    <h2 class="text-2xl font-bold text-white mb-6">Appears In</h2>
                    
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
                        @foreach ($titles as $title)
                            <a href="{{ route('titles.show', $title->slug) }}" class="block group">
                                <div class="aspect-[2/3] overflow-hidden rounded-lg mb-2 bg-gray-800">
                                    @if ($title->poster_path)
                                        <img src="{{ asset('storage/' . $title->poster_path) }}" 
                                            alt="{{ $title->name }}" 
                                            class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110">
                                    @else
                                        <div class="w-full h-full bg-gray-700 flex items-center justify-center">
                                            <span class="text-gray-500">No poster</span>
                                        </div>
                                    @endif
                                </div>
                                <h3 class="text-white font-medium line-clamp-2">{{ $title->name }}</h3>
                                
                                @if ($title->pivot && $title->pivot->character)
                                    <p class="text-sm text-gray-400">as {{ $title->pivot->character }}</p>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
            
        </div>
    </div>
</x-app-layout>