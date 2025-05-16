@props(['type' => 'card'])

@switch($type)
    @case('card')
        <div class="animate-pulse">
            <div class="bg-gray-700 rounded-lg aspect-[2/3] mb-2"></div>
            <div class="space-y-2">
                <div class="h-4 bg-gray-700 rounded w-3/4"></div>
                <div class="h-3 bg-gray-700 rounded w-1/2"></div>
            </div>
        </div>
        @break
        
    @case('horizontal')
        <div class="animate-pulse flex space-x-4">
            <div class="bg-gray-700 rounded h-32 w-20"></div>
            <div class="flex-1 space-y-2">
                <div class="h-4 bg-gray-700 rounded w-3/4"></div>
                <div class="h-3 bg-gray-700 rounded w-1/2"></div>
                <div class="h-3 bg-gray-700 rounded w-5/6"></div>
            </div>
        </div>
        @break
        
    @case('comment')
        <div class="animate-pulse flex space-x-3">
            <div class="bg-gray-700 rounded-full h-10 w-10"></div>
            <div class="flex-1 space-y-2">
                <div class="h-3 bg-gray-700 rounded w-1/4"></div>
                <div class="h-3 bg-gray-700 rounded w-full"></div>
                <div class="h-3 bg-gray-700 rounded w-5/6"></div>
            </div>
        </div>
        @break
        
    @case('section')
        <div class="animate-pulse mb-8">
            <div class="h-6 bg-gray-700 rounded w-1/4 mb-4"></div>
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-4">
                @for($i = 0; $i < 5; $i++)
                    <x-skeleton-card type="card" />
                @endfor
            </div>
        </div>
        @break
@endswitch