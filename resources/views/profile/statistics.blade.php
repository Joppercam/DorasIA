<x-app-layout>
    <x-slot name="title">Estadísticas de {{ $profile->name }}</x-slot>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold mb-2">Estadísticas de {{ $profile->name }}</h1>
            <p class="text-gray-400">Análisis detallado de tu actividad en Dorasia</p>
        </div>

        <!-- Resumen general -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-gray-900 rounded-lg p-6">
                <div class="text-4xl font-bold text-red-500 mb-2">{{ $stats['total_ratings'] }}</div>
                <div class="text-sm text-gray-400">Títulos valorados</div>
            </div>
            
            <div class="bg-gray-900 rounded-lg p-6">
                <div class="text-4xl font-bold text-blue-500 mb-2">{{ $stats['total_comments'] }}</div>
                <div class="text-sm text-gray-400">Comentarios</div>
            </div>
            
            <div class="bg-gray-900 rounded-lg p-6">
                <div class="text-4xl font-bold text-green-500 mb-2">{{ $stats['total_watchlist'] }}</div>
                <div class="text-sm text-gray-400">En mi lista</div>
            </div>
            
            <div class="bg-gray-900 rounded-lg p-6">
                <div class="text-4xl font-bold text-yellow-500 mb-2">{{ number_format($stats['average_rating'], 1) }}</div>
                <div class="text-sm text-gray-400">Valoración promedio</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Distribución de valoraciones -->
            <div class="bg-gray-900 rounded-lg p-6">
                <h2 class="text-xl font-bold mb-4">Distribución de valoraciones</h2>
                <div class="space-y-3">
                    @for($i = 5; $i >= 1; $i--)
                        @php
                            $count = $ratingDistribution[$i] ?? 0;
                            $percentage = $stats['total_ratings'] > 0 ? ($count / $stats['total_ratings']) * 100 : 0;
                        @endphp
                        <div class="flex items-center gap-3">
                            <div class="flex items-center gap-1 w-24">
                                @for($j = 1; $j <= $i; $j++)
                                    <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                @endfor
                            </div>
                            <div class="flex-1">
                                <div class="bg-gray-800 rounded-full h-4 overflow-hidden">
                                    <div 
                                        class="bg-yellow-400 h-full transition-all duration-500"
                                        style="width: {{ $percentage }}%"
                                    ></div>
                                </div>
                            </div>
                            <div class="text-sm text-gray-400 w-16 text-right">{{ $count }}</div>
                        </div>
                    @endfor
                </div>
                @if($stats['total_ratings'] == 0)
                    <p class="text-center text-gray-500 mt-4">Aún no has valorado ningún título</p>
                @endif
            </div>

            <!-- Géneros favoritos -->
            <div class="bg-gray-900 rounded-lg p-6">
                <h2 class="text-xl font-bold mb-4">Géneros favoritos</h2>
                @if($genreStats->count() > 0)
                    <div class="space-y-3">
                        @foreach($genreStats as $genre)
                            @php
                                $percentage = $stats['total_ratings'] > 0 ? ($genre->count / $stats['total_ratings']) * 100 : 0;
                            @endphp
                            <div class="flex items-center gap-3">
                                <div class="w-24 text-sm">{{ $genre->name }}</div>
                                <div class="flex-1">
                                    <div class="bg-gray-800 rounded-full h-4 overflow-hidden">
                                        <div 
                                            class="bg-gradient-to-r from-red-500 to-red-600 h-full transition-all duration-500"
                                            style="width: {{ $percentage }}%"
                                        ></div>
                                    </div>
                                </div>
                                <div class="text-sm text-gray-400 w-16 text-right">{{ $genre->count }}</div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-gray-500">No hay datos disponibles</p>
                @endif
            </div>
        </div>

        <!-- Actividad mensual -->
        <div class="bg-gray-900 rounded-lg p-6 mb-8">
            <h2 class="text-xl font-bold mb-4">Actividad en los últimos 12 meses</h2>
            @if($monthlyActivity->count() > 0)
                <div class="h-64" x-data="monthlyActivityChart({{ $monthlyActivity->toJson() }})">
                    <canvas x-ref="chart"></canvas>
                </div>
            @else
                <p class="text-center text-gray-500">No hay actividad en los últimos 12 meses</p>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
            <!-- Países más vistos -->
            <div class="bg-gray-900 rounded-lg p-6">
                <h2 class="text-xl font-bold mb-4">Países más vistos</h2>
                @if($countryStats->count() > 0)
                    <div class="space-y-3">
                        @php
                            $flags = [
                                'Korea' => '🇰🇷',
                                'Japan' => '🇯🇵',
                                'China' => '🇨🇳',
                                'Thailand' => '🇹🇭',
                                'Taiwan' => '🇹🇼',
                                'Indonesia' => '🇮🇩',
                                'Philippines' => '🇵🇭',
                                'Vietnam' => '🇻🇳',
                                'South Korea' => '🇰🇷',
                                'KR' => '🇰🇷',
                                'JP' => '🇯🇵',
                                'CN' => '🇨🇳',
                                'TH' => '🇹🇭',
                                'TW' => '🇹🇼',
                                'ID' => '🇮🇩',
                                'PH' => '🇵🇭',
                                'VN' => '🇻🇳',
                            ];
                        @endphp
                        @foreach($countryStats as $country)
                            @php
                                $percentage = $stats['total_ratings'] > 0 ? ($country->count / $stats['total_ratings']) * 100 : 0;
                            @endphp
                            <div class="flex items-center gap-3">
                                <div class="w-32 flex items-center gap-2">
                                    <span>{{ $flags[$country->country] ?? '🌍' }}</span>
                                    <span class="text-sm">{{ $country->country ?? 'Desconocido' }}</span>
                                </div>
                                <div class="flex-1">
                                    <div class="bg-gray-800 rounded-full h-4 overflow-hidden">
                                        <div 
                                            class="bg-gradient-to-r from-blue-500 to-blue-600 h-full transition-all duration-500"
                                            style="width: {{ $percentage }}%"
                                        ></div>
                                    </div>
                                </div>
                                <div class="text-sm text-gray-400 w-16 text-right">{{ $country->count }}</div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-center text-gray-500">No hay datos disponibles</p>
                @endif
            </div>

            <!-- Actividad reciente -->
            <div class="bg-gray-900 rounded-lg p-6">
                <h2 class="text-xl font-bold mb-4">Actividad reciente</h2>
                
                <div class="space-y-4">
                    <!-- Valoraciones recientes -->
                    @if($recentActivity['ratings']->count() > 0)
                        <div>
                            <h3 class="text-sm font-semibold text-gray-400 mb-2">Últimas valoraciones</h3>
                            <div class="space-y-2">
                                @foreach($recentActivity['ratings'] as $rating)
                                    <div class="flex items-center justify-between">
                                        <a href="{{ route('titles.show', $rating->title->slug) }}" 
                                           class="text-sm hover:text-red-500 transition flex-1 truncate">
                                            {{ $rating->title->title }}
                                        </a>
                                        <div class="flex items-center gap-2 ml-2">
                                            <div class="flex items-center gap-1">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="w-3 h-3 {{ $i <= $rating->score/2 ? 'text-yellow-400' : 'text-gray-600' }}" 
                                                         fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                                @endfor
                                            </div>
                                            <span class="text-xs text-gray-500">{{ $rating->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Comentarios recientes -->
                    @if($recentActivity['comments']->count() > 0)
                        <div>
                            <h3 class="text-sm font-semibold text-gray-400 mb-2">Últimos comentarios</h3>
                            <div class="space-y-2">
                                @foreach($recentActivity['comments'] as $comment)
                                    @if($comment->commentable)
                                        <div class="flex items-center justify-between">
                                            <a href="{{ route('titles.show', $comment->commentable->slug) }}" 
                                               class="text-sm hover:text-red-500 transition flex-1 truncate">
                                                {{ $comment->commentable->title }}
                                            </a>
                                            <span class="text-xs text-gray-500 ml-2">{{ $comment->created_at->diffForHumans() }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($recentActivity['ratings']->count() == 0 && $recentActivity['comments']->count() == 0)
                        <p class="text-center text-gray-500">No hay actividad reciente</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Botón para volver al perfil -->
        <div class="text-center">
            <a href="{{ route('profiles.show', $profile) }}" 
               class="inline-flex items-center gap-2 bg-gray-800 hover:bg-gray-700 px-6 py-3 rounded-lg transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Volver al perfil
            </a>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    function monthlyActivityChart(data) {
        return {
            init() {
                const ctx = this.$refs.chart.getContext('2d');
                
                // Prepare data for the chart
                const labels = data.map(item => {
                    const [year, month] = item.month.split('-');
                    const monthNames = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
                    return monthNames[parseInt(month) - 1] + ' ' + year.substr(2);
                });
                
                const values = data.map(item => item.count);
                
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Valoraciones',
                            data: values,
                            borderColor: 'rgb(239, 68, 68)',
                            backgroundColor: 'rgba(239, 68, 68, 0.1)',
                            tension: 0.3,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1,
                                    color: '#9CA3AF'
                                },
                                grid: {
                                    color: '#374151'
                                }
                            },
                            x: {
                                ticks: {
                                    color: '#9CA3AF'
                                },
                                grid: {
                                    color: '#374151'
                                }
                            }
                        }
                    }
                });
            }
        }
    }
    </script>
    @endpush
</x-app-layout>