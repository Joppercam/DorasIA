<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Models\ActorFollow;
use App\Models\ActorContent;
use Illuminate\Http\Request;

class ActorsController extends Controller
{
    public function index(Request $request)
    {
        // Si no hay parámetros, mostrar home estilo Netflix
        if (!$request->hasAny(['search', 'filter', 'page'])) {
            return $this->actorsHome();
        }

        $query = Person::query()
            ->whereNotNull('name');
        
        // Filter by search if provided
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                  ->orWhere('biography', 'LIKE', '%' . $search . '%');
            });
        }
        
        // Advanced filters
        $filter = $request->get('filter', 'korean');
        
        switch ($filter) {
            case 'popular':
                $query->whereNotNull('profile_path')
                      ->where('popularity', '>', 5);
                break;
                
            case 'trending':
                $query->whereNotNull('profile_path')
                      ->whereHas('followers')
                      ->withCount('followers')
                      ->orderBy('followers_count', 'desc');
                break;
                
            case 'young':
                $query->whereNotNull('profile_path')
                      ->whereNotNull('birthday')
                      ->whereRaw('julianday("now") - julianday(birthday) < 35 * 365')
                      ->where('popularity', '>', 2);
                break;
                
            case 'veteran':
                $query->whereNotNull('profile_path')
                      ->whereNotNull('birthday')
                      ->whereRaw('julianday("now") - julianday(birthday) > 45 * 365')
                      ->where('popularity', '>', 2);
                break;
                
            case 'actresses':
                $query->whereNotNull('profile_path')
                      ->where('gender', 1) // 1 = female in TMDB
                      ->where('popularity', '>', 2);
                break;
                
            case 'actors':
                $query->whereNotNull('profile_path')
                      ->where('gender', 2) // 2 = male in TMDB
                      ->where('popularity', '>', 2);
                break;
                
            case 'all':
                // No additional filters
                break;
                
            case 'korean':
            default:
                $query->where(function($q) {
                    $q->where('place_of_birth', 'LIKE', '%Korea%')
                      ->orWhere('place_of_birth', 'LIKE', '%South Korea%')
                      ->orWhere('place_of_birth', 'LIKE', '%Seoul%')
                      ->orWhere('place_of_birth', 'LIKE', '%Busan%')
                      ->orWhere('place_of_birth', 'LIKE', '%Incheon%');
                });
                break;
        }
        
        // Sorting options
        $sortBy = $request->get('sort', 'popularity');
        switch ($sortBy) {
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'birthday':
                $query->whereNotNull('birthday')
                      ->orderBy('birthday', 'desc');
                break;
            case 'popularity':
            default:
                if ($filter !== 'trending') { // trending already has its own order
                    $query->orderBy('popularity', 'desc');
                }
                break;
        }

        $actors = $query->paginate(24);

        // Get some featured actors for the hero section
        $featuredActors = Person::whereNotNull('profile_path')
            ->where('popularity', '>', 5)
            ->orderBy('popularity', 'desc')
            ->take(6)
            ->get();

        return view('actors.index', compact('actors', 'featuredActors'));
    }

    public function show($id)
    {
        $actor = Person::with(['titles' => function($query) {
            $query->orderBy('popularity', 'desc')->take(10);
        }])->findOrFail($id);

        // Get actor's most popular series
        $popularSeries = $actor->titles()
            ->orderBy('popularity', 'desc')
            ->take(8)
            ->get();
            
        // Load comments with user info - visible to everyone
        $comments = $actor->comments()
            ->with(['user', 'replies.user'])
            ->whereNull('parent_id')
            ->where('is_approved', true)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Check if current user is following this actor
        $isFollowing = false;
        if (auth()->check()) {
            $isFollowing = auth()->user()->isFollowingActor($actor->id);
        }

        // Get followers count
        $followersCount = $actor->followers()->count();

        // Contenido exclusivo para usuarios registrados
        $exclusiveContent = collect();
        $contentStats = [];
        $featuredContent = collect();
        $recentContent = collect();

        if (auth()->check()) {
            // Contenido destacado
            $featuredContent = $actor->featuredContent()
                ->with(['likes', 'views'])
                ->take(3)
                ->get();

            // Contenido reciente
            $recentContent = $actor->publishedContent()
                ->with(['likes', 'views'])
                ->recent(30) // Último mes
                ->take(6)
                ->get();

            // Estadísticas de contenido por tipo
            $contentStats = $actor->getContentStats();

            // Todo el contenido exclusivo disponible
            $exclusiveContent = $actor->publishedContent()
                ->with(['likes', 'views'])
                ->paginate(10, ['*'], 'content_page');
        }

        return view('actors.show', compact(
            'actor', 
            'popularSeries', 
            'comments', 
            'isFollowing', 
            'followersCount',
            'exclusiveContent',
            'contentStats',
            'featuredContent',
            'recentContent'
        ));
    }

    /**
     * API for actor autocomplete
     */
    public function autocomplete(Request $request)
    {
        $query = $request->get('q');
        
        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }

        $actors = Person::where('name', 'LIKE', '%' . $query . '%')
            ->whereNotNull('profile_path')
            ->where('popularity', '>', 1)
            ->select(['id', 'name', 'profile_path', 'popularity', 'place_of_birth'])
            ->orderBy('popularity', 'desc')
            ->limit(8)
            ->get();

        return response()->json($actors);
    }
    
    public function storeComment(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
            'is_spoiler' => 'boolean'
        ]);

        $actor = Person::findOrFail($id);

        $comment = $actor->comments()->create([
            'user_id' => auth()->id(),
            'content' => $request->content,
            'is_spoiler' => $request->boolean('is_spoiler'),
            'is_approved' => true, // Auto-approve for now
        ]);

        $comment->load('user');

        return response()->json([
            'success' => true,
            'comment' => [
                'id' => $comment->id,
                'content' => $comment->content,
                'is_spoiler' => $comment->is_spoiler,
                'user' => [
                    'name' => $comment->user->name,
                    'avatar' => $comment->user->avatar ? asset('storage/' . $comment->user->avatar) : null
                ],
                'created_at' => $comment->created_at->diffForHumans(),
                'replies' => []
            ]
        ]);
    }

    public function follow(Request $request, $id)
    {
        $actor = Person::findOrFail($id);
        $user = auth()->user();

        // Check if already following
        $existingFollow = ActorFollow::where('user_id', $user->id)
            ->where('person_id', $actor->id)
            ->first();

        if ($existingFollow) {
            return response()->json([
                'success' => false,
                'message' => 'Ya sigues a este actor',
                'is_following' => true
            ]);
        }

        // Create follow relationship
        ActorFollow::create([
            'user_id' => $user->id,
            'person_id' => $actor->id
        ]);

        // Get updated followers count
        $followersCount = $actor->followers()->count();

        return response()->json([
            'success' => true,
            'message' => 'Ahora sigues a ' . $actor->name,
            'is_following' => true,
            'followers_count' => $followersCount
        ]);
    }

    public function unfollow(Request $request, $id)
    {
        $actor = Person::findOrFail($id);
        $user = auth()->user();

        // Find and delete the follow relationship
        $follow = ActorFollow::where('user_id', $user->id)
            ->where('person_id', $actor->id)
            ->first();

        if (!$follow) {
            return response()->json([
                'success' => false,
                'message' => 'No sigues a este actor',
                'is_following' => false
            ]);
        }

        $follow->delete();

        // Get updated followers count
        $followersCount = $actor->followers()->count();

        return response()->json([
            'success' => true,
            'message' => 'Has dejado de seguir a ' . $actor->name,
            'is_following' => false,
            'followers_count' => $followersCount
        ]);
    }

    /**
     * Home de actores estilo Netflix
     */
    private function actorsHome()
    {
        // Actor destacado para el hero
        $featuredActor = Person::whereNotNull('profile_path')
            ->whereNotNull('biography')
            ->where('popularity', '>', 10)
            ->orderBy('popularity', 'desc')
            ->first();

        // Actores más populares
        $popularActors = Person::whereNotNull('profile_path')
            ->where('popularity', '>', 5)
            ->orderBy('popularity', 'desc')
            ->limit(20)
            ->get();

        // Actores trending (con más seguidores recientes)
        $trendingActors = Person::whereNotNull('profile_path')
            ->whereHas('followers')
            ->withCount('followers')
            ->orderBy('followers_count', 'desc')
            ->limit(20)
            ->get();

        // Actores jóvenes (menores de 35 años)
        $youngActors = Person::whereNotNull('profile_path')
            ->whereNotNull('birthday')
            ->whereRaw('julianday("now") - julianday(birthday) < 35 * 365')
            ->where('popularity', '>', 3)
            ->orderBy('popularity', 'desc')
            ->limit(20)
            ->get();

        // Actores veteranos (más de 45 años)
        $veteranActors = Person::whereNotNull('profile_path')
            ->whereNotNull('birthday')
            ->whereRaw('julianday("now") - julianday(birthday) > 45 * 365')
            ->where('popularity', '>', 3)
            ->orderBy('popularity', 'desc')
            ->limit(20)
            ->get();

        // Actrices (género femenino)
        $actresses = Person::whereNotNull('profile_path')
            ->where('gender', 1) // 1 = female in TMDB
            ->where('popularity', '>', 3)
            ->orderBy('popularity', 'desc')
            ->limit(20)
            ->get();

        // Actores (género masculino)
        $maleActors = Person::whereNotNull('profile_path')
            ->where('gender', 2) // 2 = male in TMDB
            ->where('popularity', '>', 3)
            ->orderBy('popularity', 'desc')
            ->limit(20)
            ->get();

        return view('actors.home', compact(
            'featuredActor',
            'popularActors',
            'trendingActors',
            'youngActors',
            'veteranActors',
            'actresses',
            'maleActors'
        ));
    }

    /**
     * Ver contenido específico del actor
     */
    public function showContent($actorId, $contentId)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Necesitas estar registrado para ver contenido exclusivo');
        }

        $actor = Person::findOrFail($actorId);
        $content = ActorContent::where('person_id', $actorId)
            ->where('id', $contentId)
            ->published()
            ->firstOrFail();

        // Incrementar vista
        $content->incrementViews(auth()->id());

        // Contenido relacionado
        $relatedContent = $actor->publishedContent()
            ->where('id', '!=', $contentId)
            ->where('type', $content->type)
            ->take(6)
            ->get();

        // Verificar si el usuario ha dado like
        $hasLiked = $content->hasLikedByUser(auth()->id());

        // Obtener reacción actual del usuario
        $userReaction = $content->getUserReaction(auth()->id());
        
        // Obtener conteos de reacciones
        $reactionCounts = $content->reaction_counts;
        
        // Obtener comentarios
        $comments = $content->comments()->with('user', 'replies.user')->get();

        return view('actors.content.show', compact(
            'actor',
            'content',
            'relatedContent',
            'hasLiked',
            'userReaction',
            'reactionCounts',
            'comments'
        ));
    }

    /**
     * Ver contenido por tipo
     */
    public function showContentByType($actorId, $type)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Necesitas estar registrado para ver contenido exclusivo');
        }

        $actor = Person::findOrFail($actorId);
        
        // Validar tipo
        if (!array_key_exists($type, ActorContent::TYPES)) {
            abort(404);
        }

        $content = $actor->getContentByType($type)->paginate(12);
        $typeName = ActorContent::TYPES[$type];

        return view('actors.content.by-type', compact(
            'actor',
            'content',
            'type',
            'typeName'
        ));
    }

    /**
     * Toggle like en contenido
     */
    public function toggleContentLike(Request $request, $actorId, $contentId)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'No autenticado'], 401);
        }

        $content = ActorContent::where('person_id', $actorId)
            ->where('id', $contentId)
            ->firstOrFail();

        $liked = $content->toggleLike(auth()->id());

        return response()->json([
            'success' => true,
            'liked' => $liked,
            'like_count' => $content->fresh()->like_count
        ]);
    }

    /**
     * Feed personalizado de contenido de actores seguidos
     */
    public function getPersonalizedFeed(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'No autenticado'], 401);
        }

        $user = auth()->user();
        
        // Obtener IDs de actores seguidos
        $followedActorIds = $user->followedActors()->pluck('person_id');

        if ($followedActorIds->isEmpty()) {
            return response()->json([
                'success' => true,
                'content' => [],
                'message' => 'Sigue algunos actores para ver contenido personalizado'
            ]);
        }

        // Obtener contenido reciente de actores seguidos
        $content = ActorContent::whereIn('person_id', $followedActorIds)
            ->published()
            ->with(['actor', 'likes', 'views'])
            ->orderBy('published_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'content' => $content
        ]);
    }

    /**
     * Buscar contenido de actores
     */
    public function searchContent(Request $request)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'No autenticado'], 401);
        }

        $query = $request->get('q');
        $type = $request->get('type');
        $actorId = $request->get('actor_id');

        $contentQuery = ActorContent::published()
            ->with(['actor', 'likes', 'views']);

        if ($query) {
            $contentQuery->where(function($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                  ->orWhere('content', 'LIKE', "%{$query}%")
                  ->orWhereJsonContains('tags', $query);
            });
        }

        if ($type) {
            $contentQuery->where('type', $type);
        }

        if ($actorId) {
            $contentQuery->where('person_id', $actorId);
        }

        $content = $contentQuery->orderBy('published_at', 'desc')
            ->paginate(15);

        return response()->json([
            'success' => true,
            'content' => $content
        ]);
    }
}