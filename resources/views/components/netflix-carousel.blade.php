@props(['titles', 'title' => null])

@php
    // Asegurar que haya al menos 14 títulos en el carrusel
    $minItems = 14;
    $titlesCount = count($titles);
    
    // Si no hay suficientes títulos, duplicamos los existentes para alcanzar el mínimo
    if ($titlesCount > 0 && $titlesCount < $minItems) {
        $multiplier = ceil($minItems / $titlesCount);
        $repeatedTitles = collect([]);
        
        for ($i = 0; $i < $multiplier; $i++) {
            $repeatedTitles = $repeatedTitles->concat($titles);
        }
        
        $carouselTitles = $repeatedTitles->take($minItems);
    } else {
        $carouselTitles = $titles;
    }
    
    // ID único para este carrusel
    $carouselId = 'carousel-' . uniqid();
@endphp

<div class="mt-8">
    @if($title)
        <h2 class="text-xl font-bold mb-4 px-4 md:px-8">{{ $title }}</h2>
    @endif
    
    <div class="relative" x-data="{ 
        scrollPosition: 0, 
        canScrollLeft: false, 
        canScrollRight: true,
        itemWidth: 200, // ancho aproximado de cada tarjeta en px
        containerWidth: 0,
        totalWidth: 0,
        
        init() {
            // Inicializar variables después de que se monte el componente
            this.$nextTick(() => {
                const container = document.getElementById('{{ $carouselId }}');
                this.containerWidth = container.clientWidth;
                this.totalWidth = container.scrollWidth;
                
                // Crear observador para detectar cambios en el tamaño
                const observer = new ResizeObserver(() => {
                    this.containerWidth = container.clientWidth;
                    this.totalWidth = container.scrollWidth;
                });
                
                observer.observe(container);
            });
        },
        
        scrollLeft() {
            const container = document.getElementById('{{ $carouselId }}');
            const scrollAmount = Math.min(this.containerWidth * 0.8, 300);
            
            if (this.scrollPosition <= 0) {
                // Si estamos al inicio, saltar al final (para carrusel infinito)
                container.scrollTo({ left: this.totalWidth, behavior: 'auto' });
                this.scrollPosition = this.totalWidth;
                setTimeout(() => {
                    container.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
                    this.scrollPosition -= scrollAmount;
                }, 10);
            } else {
                container.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
                this.scrollPosition -= scrollAmount;
            }
            
            this.updateScrollStatus();
        },
        
        scrollRight() {
            const container = document.getElementById('{{ $carouselId }}');
            const scrollAmount = Math.min(this.containerWidth * 0.8, 300);
            
            if (this.scrollPosition >= (this.totalWidth - this.containerWidth)) {
                // Si estamos al final, saltar al inicio (para carrusel infinito)
                container.scrollTo({ left: 0, behavior: 'auto' });
                this.scrollPosition = 0;
                setTimeout(() => {
                    container.scrollBy({ left: scrollAmount, behavior: 'smooth' });
                    this.scrollPosition += scrollAmount;
                }, 10);
            } else {
                container.scrollBy({ left: scrollAmount, behavior: 'smooth' });
                this.scrollPosition += scrollAmount;
            }
            
            this.updateScrollStatus();
        },
        
        updateScrollStatus() {
            this.canScrollLeft = true; // Siempre permitir scroll izquierdo en modo infinito
            this.canScrollRight = true; // Siempre permitir scroll derecho en modo infinito
        }
    }">
        <!-- Control izquierdo -->
        <button
            @click="scrollLeft()"
            class="absolute left-0 top-0 bottom-0 bg-black/30 hover:bg-black/60 px-2 z-10 flex items-center justify-center"
            x-show="canScrollLeft" 
            x-transition.opacity.duration.200ms>
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
        </button>
        
        <!-- Carrusel -->
        <div id="{{ $carouselId }}" 
             class="flex overflow-x-scroll no-scrollbar py-2 px-4" 
             style="scroll-snap-type: x mandatory; -webkit-overflow-scrolling: touch;"
             @scroll="
                scrollPosition = $el.scrollLeft;
                updateScrollStatus();
             "
             >
            
            @foreach($carouselTitles as $item)
                <div class="w-[200px] flex-shrink-0 px-2">
                    <x-netflix-modern-card :title="$item" />
                </div>
            @endforeach
        </div>
        
        <!-- Control derecho -->
        <button
            @click="scrollRight()"
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