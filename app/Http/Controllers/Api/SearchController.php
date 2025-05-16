<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Title;
use App\Models\Person;
use App\Models\Genre;
use App\Models\Category;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class SearchController extends Controller
{
    /**
     * Advanced search with multiple filters and options
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        $type = $request->get('type'); // 'all', 'titles', 'people', 'news'
        $filters = $request->get('filters', []);
        $sort = $request->get('sort', 'relevance');
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 20);
        
        if (strlen($query) < 2 && empty($filters)) {
            return response()->json([
                'data' => [],
                'meta' => [
                    'total' => 0,
                    'per_page' => $perPage,
                    'current_page' => $page,
                    'last_page' => 1,
                ]
            ]);
        }
        
        $results = collect();
        
        // Search titles if type is 'all' or 'titles'
        if (in_array($type, ['all', 'titles'])) {
            $titleResults = $this->searchTitles($query, $filters, $sort);
            $results = $results->concat($titleResults);
        }
        
        // Search people if type is 'all' or 'people'
        if (in_array($type, ['all', 'people'])) {
            $peopleResults = $this->searchPeople($query, $filters, $sort);
            $results = $results->concat($peopleResults);
        }
        
        // Search news if type is 'all' or 'news'
        if (in_array($type, ['all', 'news'])) {
            $newsResults = $this->searchNews($query, $filters, $sort);
            $results = $results->concat($newsResults);
        }
        
        // Sort all results by relevance
        if ($sort === 'relevance') {
            $results = $results->sortByDesc('relevance_score');
        }
        
        // Apply pagination
        $total = $results->count();
        $results = $results->slice(($page - 1) * $perPage, $perPage)->values();
        
        // Get search suggestions
        $suggestions = $this->getSearchSuggestions($query);
        
        // Track search for popular searches
        $this->trackSearch($query, $total);
        
        return response()->json([
            'data' => $results,
            'suggestions' => $suggestions,
            'meta' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => ceil($total / $perPage),
                'query' => $query,
                'type' => $type,
                'filters' => $filters,
            ],
            'facets' => $this->getFacets($query),
        ]);
    }
    
    /**
     * Search titles with advanced filters
     */
    protected function searchTitles($query, $filters, $sort)
    {
        $titleQuery = Title::query()
            ->with(['genres', 'category']);
        
        // Apply search query
        if ($query) {
            $titleQuery->where(function($q) use ($query) {
                $q->where('title', 'like', '%' . $query . '%')
                  ->orWhere('original_title', 'like', '%' . $query . '%')
                  ->orWhere('synopsis', 'like', '%' . $query . '%');
            });
        }
        
        // Apply filters
        if (!empty($filters['genre'])) {
            $titleQuery->whereHas('genres', function($q) use ($filters) {
                $q->whereIn('genres.id', (array)$filters['genre']);
            });
        }
        
        if (!empty($filters['category'])) {
            $titleQuery->whereIn('category_id', (array)$filters['category']);
        }
        
        if (!empty($filters['year'])) {
            if (is_array($filters['year'])) {
                $titleQuery->whereBetween('release_year', $filters['year']);
            } else {
                $titleQuery->where('release_year', $filters['year']);
            }
        }
        
        if (!empty($filters['country'])) {
            $titleQuery->whereIn('origin_country', (array)$filters['country']);
        }
        
        if (!empty($filters['rating'])) {
            $titleQuery->where('vote_average', '>=', $filters['rating']);
        }
        
        if (!empty($filters['type'])) {
            $titleQuery->where('type', $filters['type']);
        }
        
        // Apply sorting
        switch ($sort) {
            case 'newest':
                $titleQuery->orderBy('release_year', 'desc');
                break;
            case 'oldest':
                $titleQuery->orderBy('release_year', 'asc');
                break;
            case 'rating':
                $titleQuery->orderBy('vote_average', 'desc');
                break;
            case 'popular':
                $titleQuery->orderBy('vote_count', 'desc');
                break;
            default:
                // For relevance, we'll add a score later
                break;
        }
        
        $titles = $titleQuery->get();
        
        // Add relevance score
        return $titles->map(function($title) use ($query) {
            $relevanceScore = 0;
            
            if ($query) {
                // Exact match in title
                if (stripos($title->title, $query) !== false) {
                    $relevanceScore += 10;
                }
                // Exact match in original title
                if (stripos($title->original_title, $query) !== false) {
                    $relevanceScore += 8;
                }
                // Match in synopsis
                if (stripos($title->synopsis, $query) !== false) {
                    $relevanceScore += 5;
                }
                // Boost score based on rating
                $relevanceScore += $title->vote_average ?? 0;
            }
            
            return [
                'id' => 'title-' . $title->id,
                'type' => 'title',
                'title' => $title->title,
                'original_title' => $title->original_title,
                'slug' => $title->slug,
                'url' => route('titles.show', $title->slug),
                'poster_url' => $title->poster_url,
                'backdrop_url' => $title->backdrop_url,
                'release_year' => $title->release_year,
                'vote_average' => $title->vote_average,
                'vote_count' => $title->vote_count,
                'media_type' => $title->type,
                'genres' => $title->genres->pluck('name'),
                'category' => $title->category?->name,
                'country' => $title->origin_country,
                'synopsis' => \Str::limit($title->synopsis, 150),
                'relevance_score' => $relevanceScore,
            ];
        });
    }
    
    /**
     * Search people (actors, directors, etc)
     */
    protected function searchPeople($query, $filters, $sort)
    {
        $personQuery = Person::query()
            ->withCount('titles');
        
        if ($query) {
            $personQuery->where('name', 'like', '%' . $query . '%');
        }
        
        if (!empty($filters['department'])) {
            $personQuery->where('known_for_department', $filters['department']);
        }
        
        // Apply sorting
        switch ($sort) {
            case 'name':
                $personQuery->orderBy('name');
                break;
            case 'popular':
                $personQuery->orderBy('titles_count', 'desc');
                break;
            default:
                break;
        }
        
        $people = $personQuery->get();
        
        return $people->map(function($person) use ($query) {
            $relevanceScore = 0;
            
            if ($query) {
                if (stripos($person->name, $query) === 0) {
                    $relevanceScore += 15; // Starts with query
                } elseif (stripos($person->name, $query) !== false) {
                    $relevanceScore += 10; // Contains query
                }
                
                // Boost by popularity
                $relevanceScore += min($person->titles_count / 10, 5);
            }
            
            return [
                'id' => 'person-' . $person->id,
                'type' => 'person',
                'title' => $person->name,
                'slug' => $person->slug,
                'url' => route('people.show', $person->slug),
                'poster_url' => $person->photo_url ?? asset('images/actors/placeholder.jpg'),
                'department' => $person->known_for_department,
                'titles_count' => $person->titles_count,
                'relevance_score' => $relevanceScore,
            ];
        });
    }
    
    /**
     * Search news articles
     */
    protected function searchNews($query, $filters, $sort)
    {
        $newsQuery = News::query()
            ->with(['people']);
        
        if ($query) {
            $newsQuery->where(function($q) use ($query) {
                $q->where('title', 'like', '%' . $query . '%')
                  ->orWhere('excerpt', 'like', '%' . $query . '%')
                  ->orWhere('content', 'like', '%' . $query . '%');
            });
        }
        
        if (!empty($filters['featured'])) {
            $newsQuery->where('featured', true);
        }
        
        if (!empty($filters['date_from'])) {
            $newsQuery->where('published_at', '>=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $newsQuery->where('published_at', '<=', $filters['date_to']);
        }
        
        // Apply sorting
        switch ($sort) {
            case 'newest':
                $newsQuery->orderBy('published_at', 'desc');
                break;
            case 'oldest':
                $newsQuery->orderBy('published_at', 'asc');
                break;
            default:
                break;
        }
        
        $news = $newsQuery->get();
        
        return $news->map(function($article) use ($query) {
            $relevanceScore = 0;
            
            if ($query) {
                if (stripos($article->title, $query) !== false) {
                    $relevanceScore += 10;
                }
                if (stripos($article->excerpt, $query) !== false) {
                    $relevanceScore += 7;
                }
                if (stripos($article->content, $query) !== false) {
                    $relevanceScore += 5;
                }
                
                // Boost recent news
                $daysOld = $article->published_at->diffInDays(now());
                if ($daysOld < 7) {
                    $relevanceScore += 5;
                } elseif ($daysOld < 30) {
                    $relevanceScore += 3;
                }
            }
            
            return [
                'id' => 'news-' . $article->id,
                'type' => 'news',
                'title' => $article->title,
                'slug' => $article->slug,
                'url' => route('news.show', $article->slug),
                'poster_url' => $article->image_url ?? '/images/news/placeholder.jpg',
                'excerpt' => $article->excerpt,
                'published_at' => $article->published_at,
                'featured' => $article->featured,
                'people' => $article->people->pluck('name'),
                'relevance_score' => $relevanceScore,
            ];
        });
    }
    
    /**
     * Get search suggestions based on popular searches and content
     */
    protected function getSearchSuggestions($query)
    {
        if (strlen($query) < 2) {
            return [];
        }
        
        $suggestions = Cache::remember("search_suggestions_{$query}", 300, function() use ($query) {
            $titleSuggestions = Title::where('title', 'like', $query . '%')
                ->orderBy('vote_average', 'desc')
                ->limit(5)
                ->pluck('title');
            
            $personSuggestions = Person::where('name', 'like', $query . '%')
                ->limit(3)
                ->pluck('name');
            
            $popularSearches = DB::table('search_logs')
                ->where('query', 'like', $query . '%')
                ->where('results_count', '>', 0)
                ->groupBy('query')
                ->orderByRaw('COUNT(*) DESC')
                ->limit(5)
                ->pluck('query');
            
            return $titleSuggestions
                ->concat($personSuggestions)
                ->concat($popularSearches)
                ->unique()
                ->values()
                ->take(8);
        });
        
        return $suggestions;
    }
    
    /**
     * Get facets for search results
     */
    protected function getFacets($query)
    {
        return Cache::remember("search_facets_{$query}", 300, function() use ($query) {
            $titleQuery = Title::query();
            
            if ($query) {
                $titleQuery->where(function($q) use ($query) {
                    $q->where('title', 'like', '%' . $query . '%')
                      ->orWhere('original_title', 'like', '%' . $query . '%');
                });
            }
            
            // Genre facets
            $genres = Genre::withCount(['titles' => function($q) use ($titleQuery) {
                $q->whereIn('titles.id', $titleQuery->pluck('id'));
            }])
            ->having('titles_count', '>', 0)
            ->orderBy('titles_count', 'desc')
            ->get()
            ->map(function($genre) {
                return [
                    'id' => $genre->id,
                    'name' => $genre->name,
                    'count' => $genre->titles_count,
                ];
            });
            
            // Category facets
            $categories = Category::withCount(['titles' => function($q) use ($titleQuery) {
                $q->whereIn('titles.id', $titleQuery->pluck('id'));
            }])
            ->having('titles_count', '>', 0)
            ->orderBy('titles_count', 'desc')
            ->get()
            ->map(function($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'count' => $category->titles_count,
                ];
            });
            
            // Year facets (group by decade)
            $years = $titleQuery->selectRaw('FLOOR(release_year/10)*10 as decade, COUNT(*) as count')
                ->groupBy('decade')
                ->orderBy('decade', 'desc')
                ->get()
                ->map(function($year) {
                    return [
                        'decade' => $year->decade,
                        'label' => $year->decade . 's',
                        'count' => $year->count,
                    ];
                });
            
            return [
                'genres' => $genres,
                'categories' => $categories,
                'years' => $years,
            ];
        });
    }
    
    /**
     * Track search queries for analytics and suggestions
     */
    protected function trackSearch($query, $resultsCount)
    {
        DB::table('search_logs')->insert([
            'query' => $query,
            'results_count' => $resultsCount,
            'user_id' => auth()->id(),
            'created_at' => now(),
        ]);
    }
    
    /**
     * Advanced search page
     */
    public function advancedSearch()
    {
        $genres = Genre::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $countries = Title::select('origin_country')
            ->distinct()
            ->whereNotNull('origin_country')
            ->orderBy('origin_country')
            ->pluck('origin_country');
            
        return view('search.advanced', compact('genres', 'categories', 'countries'));
    }
    
    /**
     * Popular searches
     */
    public function popular()
    {
        $popularSearches = Cache::remember('popular_searches', 3600, function() {
            return DB::table('search_logs')
                ->select('query', DB::raw('COUNT(*) as count'))
                ->where('results_count', '>', 0)
                ->groupBy('query')
                ->orderByDesc('count')
                ->limit(20)
                ->get();
        });
        
        $trendingSearches = Cache::remember('trending_searches', 3600, function() {
            return DB::table('search_logs')
                ->select('query', DB::raw('COUNT(*) as count'))
                ->where('created_at', '>=', now()->subDays(7))
                ->where('results_count', '>', 0)
                ->groupBy('query')
                ->orderByDesc('count')
                ->limit(10)
                ->get();
        });
        
        return response()->json([
            'popular' => $popularSearches,
            'trending' => $trendingSearches,
        ]);
    }
}