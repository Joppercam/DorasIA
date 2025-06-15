<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UpcomingSeries;
use App\Models\Series;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class UpcomingController extends Controller
{
    /**
     * Mostrar página de próximos estrenos
     */
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all'); // all, new_series, new_seasons, this_month, next_month
        $sort = $request->get('sort', 'date'); // date, popularity, title
        
        // Cache key basado en filtros
        $cacheKey = "upcoming.index.{$filter}.{$sort}";
        
        $data = Cache::remember($cacheKey, 900, function () use ($filter, $sort) {
            $query = UpcomingSeries::upcoming();
            
            // Aplicar filtros
            switch ($filter) {
                case 'new_series':
                    $query->newSeries();
                    break;
                case 'new_seasons':
                    $query->newSeasons();
                    break;
                case 'this_month':
                    $query->thisMonth();
                    break;
                case 'next_month':
                    $query->nextMonth();
                    break;
            }
            
            // Aplicar ordenamiento
            switch ($sort) {
                case 'popularity':
                    $query->orderBy('popularity', 'desc');
                    break;
                case 'title':
                    $query->orderBy('spanish_title');
                    break;
                case 'date':
                default:
                    $query->orderBy('release_date');
                    break;
            }
            
            return [
                'upcomingSeries' => $query->paginate(24),
                'stats' => $this->getUpcomingStats()
            ];
        });
        
        return view('upcoming.index', array_merge($data, [
            'currentFilter' => $filter,
            'currentSort' => $sort
        ]));
    }
    
    /**
     * Mostrar detalle de próximo estreno
     */
    public function show(UpcomingSeries $upcomingSeries)
    {
        // Si es una nueva temporada, cargar la serie existente
        if ($upcomingSeries->isNewSeason() && $upcomingSeries->existing_series_id) {
            $existingSeries = Series::with(['genres', 'people', 'seasons.episodes'])
                ->find($upcomingSeries->existing_series_id);
        } else {
            $existingSeries = null;
        }
        
        // Obtener recomendaciones relacionadas
        $relatedUpcoming = $this->getRelatedUpcoming($upcomingSeries);
        
        return view('upcoming.show', compact('upcomingSeries', 'existingSeries', 'relatedUpcoming'));
    }
    
    /**
     * API: Obtener próximos estrenos
     */
    public function api(Request $request)
    {
        $limit = min($request->get('limit', 10), 50);
        $type = $request->get('type', 'all');
        
        $cacheKey = "upcoming.api.{$type}.{$limit}";
        
        $upcoming = Cache::remember($cacheKey, 600, function () use ($type, $limit) {
            $query = UpcomingSeries::upcoming()->orderBy('release_date');
            
            if ($type === 'new_series') {
                $query->newSeries();
            } elseif ($type === 'new_seasons') {
                $query->newSeasons();
            }
            
            return $query->limit($limit)->get();
        });
        
        return response()->json([
            'success' => true,
            'upcoming' => $upcoming->map(function ($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->display_title,
                    'type' => $item->formatted_type,
                    'type_icon' => $item->type_icon,
                    'release_date' => $item->release_date,
                    'formatted_release_date' => $item->formatted_release_date,
                    'days_until_release' => $item->days_until_release,
                    'poster_url' => $item->poster_url,
                    'backdrop_url' => $item->backdrop_url,
                    'overview' => $item->display_overview,
                    'vote_average' => $item->vote_average,
                    'season_number' => $item->season_number,
                    'episode_count' => $item->episode_count,
                    'is_new_series' => $item->isNewSeries(),
                    'is_new_season' => $item->isNewSeason(),
                    'existing_series' => $item->existing_series_id ? [
                        'id' => $item->existingSeries->id ?? null,
                        'title' => $item->existingSeries->display_title ?? null
                    ] : null
                ];
            })
        ]);
    }
    
    /**
     * Widget de próximos estrenos para home
     */
    public function widget()
    {
        $upcoming = Cache::remember('upcoming.widget', 1800, function () {
            return UpcomingSeries::upcoming()
                ->orderBy('release_date')
                ->limit(6)
                ->get();
        });
        
        return view('components.upcoming-widget', compact('upcoming'));
    }
    
    /**
     * Obtener estadísticas de próximos estrenos
     */
    private function getUpcomingStats()
    {
        return Cache::remember('upcoming.stats', 3600, function () {
            $total = UpcomingSeries::upcoming()->count();
            $newSeries = UpcomingSeries::upcoming()->newSeries()->count();
            $newSeasons = UpcomingSeries::upcoming()->newSeasons()->count();
            $thisMonth = UpcomingSeries::upcoming()->thisMonth()->count();
            $nextMonth = UpcomingSeries::upcoming()->nextMonth()->count();
            
            return compact('total', 'newSeries', 'newSeasons', 'thisMonth', 'nextMonth');
        });
    }
    
    /**
     * Obtener próximos estrenos relacionados
     */
    private function getRelatedUpcoming(UpcomingSeries $upcomingSeries)
    {
        $cacheKey = "upcoming.related.{$upcomingSeries->id}";
        
        return Cache::remember($cacheKey, 1800, function () use ($upcomingSeries) {
            $query = UpcomingSeries::upcoming()
                ->where('id', '!=', $upcomingSeries->id);
            
            // Si es una nueva temporada, buscar otras nuevas temporadas
            // Si es una serie nueva, buscar otras series nuevas
            if ($upcomingSeries->isNewSeason()) {
                $query->newSeasons();
            } else {
                $query->newSeries();
            }
            
            return $query->orderBy('popularity', 'desc')
                ->limit(6)
                ->get();
        });
    }
    
    /**
     * Marcar interés en próximo estreno (para usuarios autenticados)
     */
    public function toggleInterest(Request $request, UpcomingSeries $upcomingSeries)
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Debes iniciar sesión para marcar interés'
            ], 401);
        }
        
        // Aquí se implementaría la lógica de seguimiento de próximos estrenos
        // Por ahora, retornamos success
        
        return response()->json([
            'success' => true,
            'message' => 'Interés marcado exitosamente',
            'interested' => true
        ]);
    }
    
    /**
     * Obtener próximos estrenos por fecha específica
     */
    public function byDate(Request $request)
    {
        $date = $request->get('date', Carbon::now()->format('Y-m-d'));
        
        try {
            $targetDate = Carbon::parse($date);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Fecha inválida'
            ], 400);
        }
        
        $upcoming = UpcomingSeries::whereDate('release_date', $targetDate)
            ->orderBy('popularity', 'desc')
            ->get();
        
        return response()->json([
            'success' => true,
            'date' => $targetDate->format('Y-m-d'),
            'formatted_date' => $targetDate->format('d \d\e F, Y'),
            'upcoming' => $upcoming
        ]);
    }
    
    /**
     * Obtener calendario de próximos estrenos
     */
    public function calendar(Request $request)
    {
        $month = $request->get('month', Carbon::now()->month);
        $year = $request->get('year', Carbon::now()->year);
        
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();
        
        $upcoming = UpcomingSeries::whereBetween('release_date', [$startDate, $endDate])
            ->orderBy('release_date')
            ->get()
            ->groupBy(function ($item) {
                return Carbon::parse($item->release_date)->format('Y-m-d');
            });
        
        return response()->json([
            'success' => true,
            'month' => $month,
            'year' => $year,
            'calendar' => $upcoming
        ]);
    }
}