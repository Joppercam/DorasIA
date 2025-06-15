<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Models\ActorFollow;
use Illuminate\Http\Request;

class ActorsController extends Controller
{
    public function index(Request $request)
    {
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
        
        // Filter by popular actors (with profile images and known for)
        if ($request->get('filter') === 'popular') {
            $query->whereNotNull('profile_path')
                  ->where('popularity', '>', 5);
        }
        
        // Filter by Korean actors
        if ($request->get('filter') === 'korean' || !$request->filled('filter')) {
            $query->where(function($q) {
                $q->where('place_of_birth', 'LIKE', '%Korea%')
                  ->orWhere('place_of_birth', 'LIKE', '%South Korea%')
                  ->orWhere('place_of_birth', 'LIKE', '%Seoul%')
                  ->orWhere('place_of_birth', 'LIKE', '%Busan%')
                  ->orWhere('place_of_birth', 'LIKE', '%Incheon%');
            });
        }

        $actors = $query->orderBy('popularity', 'desc')
                       ->paginate(24);

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

        return view('actors.show', compact('actor', 'popularSeries', 'comments', 'isFollowing', 'followersCount'));
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
}