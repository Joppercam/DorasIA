@props(['limit' => 10, 'period' => 'week'])

@php
    $date = match($period) {
        'day' => now()->subDay(),
        'week' => now()->subWeek(),
        'month' => now()->subMonth(),
        default => now()->subWeek()
    };
    
    $mostDiscussed = \App\Models\Title::withCount(['comments' => function($query) use ($date) {
            $query->where('created_at', '>=', $date);
        }])
        ->having('comments_count', '>', 0)
        ->orderBy('comments_count', 'desc')
        ->limit($limit)
        ->get();
@endphp

<div class="bg-gray-900 rounded-lg p-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-white">Lo Más Comentado</h2>
        <select onchange="updatePeriod(this.value)" 
                class="bg-gray-800 text-white rounded px-3 py-1 text-sm border border-gray-700">
            <option value="day" {{ $period == 'day' ? 'selected' : '' }}>Hoy</option>
            <option value="week" {{ $period == 'week' ? 'selected' : '' }}>Esta semana</option>
            <option value="month" {{ $period == 'month' ? 'selected' : '' }}>Este mes</option>
        </select>
    </div>
    
    <div class="space-y-4">
        @foreach($mostDiscussed as $index => $title)
            <div class="flex items-center space-x-4 p-3 rounded-lg hover:bg-gray-800 transition-colors">
                <!-- Posición -->
                <div class="text-3xl font-bold {{ $index < 3 ? 'text-red-500' : 'text-gray-500' }} w-8 text-center">
                    {{ $index + 1 }}
                </div>
                
                <!-- Poster -->
                <div class="w-16 h-24 flex-shrink-0">
                    <img src="{{ $title->poster_url }}" 
                         alt="{{ $title->title }}"
                         class="w-full h-full object-cover rounded"
                         onerror="this.onerror=null; this.src='/posters/placeholder.jpg'">
                </div>
                
                <!-- Información -->
                <div class="flex-1">
                    <h3 class="text-white font-medium">
                        <a href="{{ route('titles.show', $title->slug) }}" class="hover:text-red-500 transition-colors">
                            {{ $title->title }}
                        </a>
                    </h3>
                    <div class="flex items-center space-x-4 text-sm text-gray-400 mt-1">
                        <span>
                            <i class="far fa-comment mr-1"></i>
                            {{ $title->comments_count }} comentarios
                        </span>
                        <span>
                            <i class="far fa-star mr-1"></i>
                            {{ number_format($title->vote_average, 1) }}
                        </span>
                    </div>
                </div>
                
                <!-- Botón de acción -->
                <a href="{{ route('titles.show', $title->slug) }}#comments" 
                   class="px-3 py-1 bg-red-600 text-white rounded text-sm hover:bg-red-700 transition-colors">
                    Unirse
                </a>
            </div>
        @endforeach
    </div>
</div>

<script>
function updatePeriod(period) {
    const url = new URL(window.location);
    url.searchParams.set('period', period);
    window.location = url;
}
</script>