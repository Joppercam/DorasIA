@props(['titles', 'title'])

<div class="mt-8">
    <h2 class="text-xl font-bold mb-4 px-4 md:px-8">{{ $title }}</h2>
    
    <div class="relative" x-data="{ scrollPosition: 0, canScrollLeft: false, canScrollRight: true }">
        <!-- Control izquierdo -->
        <button
            @click="document.getElementById('carousel-{{ str_replace(' ', '-', strtolower($title)) }}').scrollBy({ left: -300, behavior: 'smooth' }); scrollPosition -= 300; canScrollLeft = scrollPosition > 0; canScrollRight = true;"
            class="absolute left-0 top-0 bottom-0 bg-black/30 hover:bg-black/60 px-2 z-10 flex items-center justify-center"
            x-show="canScrollLeft" 
            x-transition.opacity.duration.200ms>
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>
        
        <!-- Carrusel -->
        <div id="carousel-{{ str_replace(' ', '-', strtolower($title)) }}" 
             class="flex overflow-x-scroll no-scrollbar py-2 px-4" 
             style="scroll-snap-type: x mandatory; -webkit-overflow-scrolling: touch;"
             @scroll="
                scrollPosition = $el.scrollLeft;
                canScrollLeft = scrollPosition > 0;
                canScrollRight = scrollPosition < ($el.scrollWidth - $el.clientWidth - 10);
             "
             >
            
            @foreach($titles as $item)
                <x-netflix-card :title="$item" />
            @endforeach
        </div>
        
        <!-- Control derecho -->
        <button
            @click="document.getElementById('carousel-{{ str_replace(' ', '-', strtolower($title)) }}').scrollBy({ left: 300, behavior: 'smooth' }); scrollPosition += 300; canScrollLeft = true; canScrollRight = scrollPosition < document.getElementById('carousel-{{ str_replace(' ', '-', strtolower($title)) }}').scrollWidth - document.getElementById('carousel-{{ str_replace(' ', '-', strtolower($title)) }}').clientWidth - 10;"
            class="absolute right-0 top-0 bottom-0 bg-black/30 hover:bg-black/60 px-2 z-10 flex items-center justify-center"
            x-show="canScrollRight"
            x-transition.opacity.duration.200ms>
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
        </button>
    </div>
</div>

<style>
    /* Ocultar scrollbar pero mantener funcionalidad */
    .no-scrollbar {
        scrollbar-width: none; /* Firefox */
        -ms-overflow-style: none; /* IE and Edge */
    }
    
    .no-scrollbar::-webkit-scrollbar {
        display: none; /* Chrome, Safari and Opera */
    }
</style>