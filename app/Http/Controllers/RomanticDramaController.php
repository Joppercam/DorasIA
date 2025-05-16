<?php

namespace App\Http\Controllers;

use App\Models\Title;
use App\Models\Genre;
use App\Services\TmdbService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class RomanticDramaController extends Controller
{
    protected $tmdbService;
    
    public function __construct(TmdbService $tmdbService)
    {
        $this->tmdbService = $tmdbService;
    }
    
    /**
     * Display a listing of romantic dramas
     */
    public function index(Request $request)
    {
        // Get romance genre
        $romanceGenre = Genre::where('name', 'Romance')->first();
        
        // Get featured romantic titles
        $featuredTitles = Title::romantic()
            ->where('is_featured', true)
            ->orderBy('popularity', 'desc')
            ->take(6)
            ->get();
            
        // Si no hay títulos destacados, obtener títulos románticos populares
        if($featuredTitles->isEmpty()) {
            $featuredTitles = Title::romantic()
                ->orderBy('popularity', 'desc')
                ->take(6)
                ->get();
        }
            
        // Get all romantic subgenres from config
        $romanticSubgenres = config('tmdb.romance_subgenres', []);
        
        // Initialize collection for subgenre sections
        $subgenreSections = collect();
        
        // Get titles for each subgenre
        foreach ($romanticSubgenres as $key => $subgenre) {
            $titles = Cache::remember("romantic_subgenre_{$key}", now()->addHours(6), function () use ($key) {
                return Title::romantic()
                    ->byRomanticSubgenre($key)
                    ->orderBy('popularity', 'desc')
                    ->take(12)
                    ->get();
            });
            
            // Only add subgenre section if it has titles
            if ($titles->isNotEmpty()) {
                $subgenreSections->push([
                    'key' => $key,
                    'name' => $subgenre['name'],
                    'titles' => $titles
                ]);
            }
        }
        
        // Get popular romantic K-dramas
        $popularKdramas = Cache::remember('popular_romantic_kdramas', now()->addHours(6), function () {
            return Title::romantic()
                ->korean()
                ->orderBy('popularity', 'desc')
                ->take(12)
                ->get();
        });
        
        // Get popular romantic J-dramas
        $popularJdramas = Cache::remember('popular_romantic_jdramas', now()->addHours(6), function () {
            return Title::romantic()
                ->japanese()
                ->orderBy('popularity', 'desc')
                ->take(12)
                ->get();
        });
        
        // Get popular romantic C-dramas
        $popularCdramas = Cache::remember('popular_romantic_cdramas', now()->addHours(6), function () {
            return Title::romantic()
                ->chinese()
                ->orderBy('popularity', 'desc')
                ->take(12)
                ->get();
        });
        
        // Get newly added romantic dramas
        $newRomanticDramas = Title::romantic()
            ->orderBy('created_at', 'desc')
            ->take(12)
            ->get();
            
        // Temporalmente mostrar la vista de debug
        if (request()->has('debug')) {
            return view('romantic-dramas.debug', compact(
                'featuredTitles',
                'subgenreSections',
                'popularKdramas',
                'popularJdramas',
                'popularCdramas',
                'newRomanticDramas',
                'romanticSubgenres'
            ));
        }
        
        return view('romantic-dramas.index', compact(
            'featuredTitles',
            'subgenreSections',
            'popularKdramas',
            'popularJdramas',
            'popularCdramas',
            'newRomanticDramas',
            'romanticSubgenres'
        ));
    }
    
    /**
     * Display a listing of dramas by romantic subgenre
     */
    public function showSubgenre(Request $request, $subgenre)
    {
        // Validate subgenre
        $validSubgenres = array_keys(config('tmdb.romance_subgenres', []));
        
        if (!in_array($subgenre, $validSubgenres)) {
            abort(404);
        }
        
        // Get subgenre info
        $subgenreInfo = config('tmdb.romance_subgenres.' . $subgenre);
        
        // Get titles for this subgenre
        $titles = Title::romantic()
            ->byRomanticSubgenre($subgenre)
            ->orderBy('popularity', 'desc')
            ->paginate(24);
            
        // Get related subgenres
        $relatedSubgenres = collect(config('tmdb.romance_subgenres'))
            ->except($subgenre)
            ->take(5);
            
        return view('romantic-dramas.subgenre', compact(
            'subgenre',
            'subgenreInfo',
            'titles',
            'relatedSubgenres'
        ));
    }
    
    /**
     * Display romantic dramas by origin
     */
    public function showByOrigin(Request $request, $origin)
    {
        // Validate origin
        $validOrigins = ['korean', 'japanese', 'chinese', 'thai'];
        
        if (!in_array($origin, $validOrigins)) {
            abort(404);
        }
        
        // Get titles based on origin
        $query = Title::romantic();
        
        switch ($origin) {
            case 'korean':
                $query->korean();
                $originLabel = 'Korean';
                break;
                
            case 'japanese':
                $query->japanese();
                $originLabel = 'Japanese';
                break;
                
            case 'chinese':
                $query->chinese();
                $originLabel = 'Chinese';
                break;
                
            case 'thai':
                $query->byOriginCountry('TH');
                $originLabel = 'Thai';
                break;
        }
        
        // Apply filters
        if ($request->has('subgenre') && $request->subgenre !== 'all') {
            $query->byRomanticSubgenre($request->subgenre);
        }
        
        // Apply sorting
        switch ($request->sort ?? 'popularity') {
            case 'newest':
                $query->orderBy('release_date', 'desc');
                break;
                
            case 'rating':
                $query->orderBy('vote_average', 'desc');
                break;
                
            default: // popularity
                $query->orderBy('popularity', 'desc');
                break;
        }
        
        $titles = $query->paginate(24);
        
        // Get romantic subgenres for filter
        $romanticSubgenres = config('tmdb.romance_subgenres', []);
        
        return view('romantic-dramas.origin', compact(
            'titles',
            'origin',
            'originLabel',
            'romanticSubgenres'
        ));
    }
    
    /**
     * Display advanced search for romantic dramas
     */
    public function search(Request $request)
    {
        $query = Title::romantic();
        
        // Apply filters
        if ($request->has('origin') && $request->origin !== 'all') {
            switch ($request->origin) {
                case 'korean':
                    $query->korean();
                    break;
                    
                case 'japanese':
                    $query->japanese();
                    break;
                    
                case 'chinese':
                    $query->chinese();
                    break;
                    
                case 'thai':
                    $query->byOriginCountry('TH');
                    break;
            }
        }
        
        if ($request->has('subgenre') && $request->subgenre !== 'all') {
            $query->byRomanticSubgenre($request->subgenre);
        }
        
        if ($request->has('platform') && $request->platform !== 'all') {
            $query->byStreamingPlatform($request->platform);
        }
        
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('original_title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Apply sorting
        switch ($request->sort ?? 'popularity') {
            case 'newest':
                $query->orderBy('release_date', 'desc');
                break;
                
            case 'rating':
                $query->orderBy('vote_average', 'desc');
                break;
                
            default: // popularity
                $query->orderBy('popularity', 'desc');
                break;
        }
        
        $titles = $query->paginate(24);
        
        // Get data for filters
        $romanticSubgenres = config('tmdb.romance_subgenres', []);
        $streamingPlatforms = [
            'netflix' => 'Netflix',
            'viki' => 'Viki',
            'disney' => 'Disney+',
            'apple' => 'Apple TV+',
            'hbo' => 'HBO Max',
            'amazon' => 'Amazon Prime',
            'crunchyroll' => 'Crunchyroll'
        ];
        
        return view('romantic-dramas.search', compact(
            'titles',
            'romanticSubgenres',
            'streamingPlatforms'
        ));
    }
    
    /**
     * Display recommendations based on a title
     */
    public function recommendations(Request $request, Title $title)
    {
        // Get title's subgenre
        $subgenre = $title->romantic_subgenre;
        
        // Get recommendations by subgenre
        $recommendations = $this->tmdbService->getSubgenreRecommendations($subgenre, 1, 18);
        
        // Convert TMDB recommendations to Dorasia titles
        $recommendedTitles = collect();
        
        foreach ($recommendations as $recommendation) {
            $existingTitle = Title::where('tmdb_id', $recommendation['id'])->first();
            
            if ($existingTitle) {
                $recommendedTitles->push($existingTitle);
            }
        }
        
        // Get similar by origin country
        $originCountry = $title->main_origin_country;
        
        $similarByOrigin = Title::romantic()
            ->where('id', '!=', $title->id)
            ->whereJsonContains('metadata->origin_countries', $originCountry)
            ->orderBy('popularity', 'desc')
            ->take(12)
            ->get();
            
        return view('romantic-dramas.recommendations', compact(
            'title',
            'subgenre',
            'recommendedTitles',
            'similarByOrigin'
        ));
    }
}