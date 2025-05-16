<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SocialProfileController extends Controller
{
    /**
     * Show the public profile.
     */
    public function show(Profile $profile)
    {
        // Check if profile is public or if user is viewing their own profile
        if (!$profile->is_public && (!Auth::check() || Auth::user()->getActiveProfile()->id !== $profile->id)) {
            abort(403, 'Este perfil es privado.');
        }

        // Load relationships
        $profile->loadCount(['followers', 'following', 'ratings', 'watchlist', 'comments']);
        
        // Recent activity
        $recentActivity = collect();
        
        // Recent ratings
        $recentRatings = $profile->ratings()
            ->with('title')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(function ($rating) {
                return [
                    'type' => 'rating',
                    'title' => $rating->title,
                    'score' => $rating->score,
                    'review' => $rating->review,
                    'created_at' => $rating->created_at,
                ];
            });
        
        // Recent comments
        $recentComments = $profile->comments()
            ->with('commentable')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(function ($comment) {
                return [
                    'type' => 'comment',
                    'title' => $comment->commentable,
                    'content' => $comment->content,
                    'created_at' => $comment->created_at,
                ];
            });
        
        // Recent watchlist items
        $recentWatchlist = $profile->watchlist()
            ->with('title')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'watchlist',
                    'title' => $item->title,
                    'created_at' => $item->created_at,
                ];
            });
        
        // Merge and sort by date
        $recentActivity = $recentActivity
            ->concat($recentRatings)
            ->concat($recentComments)
            ->concat($recentWatchlist)
            ->sortByDesc('created_at')
            ->take(10);
        
        // Check if current user follows this profile
        $isFollowing = false;
        if (Auth::check()) {
            $currentProfile = Auth::user()->getActiveProfile();
            $isFollowing = $currentProfile ? $currentProfile->isFollowing($profile) : false;
        }
        
        return view('profiles.show', compact('profile', 'recentActivity', 'isFollowing'));
    }
    
    /**
     * Show the form for editing the profile.
     */
    public function edit(Profile $profile)
    {
        // La política ya verifica que el usuario puede editar
        $genres = \App\Models\Genre::orderBy('name')->get();
        
        return view('profiles.edit', compact('profile', 'genres'));
    }
    
    /**
     * Update the profile.
     */
    public function update(Request $request, Profile $profile)
    {
        // La política ya verifica que el usuario puede editar
        
        $validated = $request->validate([
            'bio' => 'nullable|string|max:500',
            'location' => 'nullable|string|max:100',
            'favorite_genres' => 'nullable|array',
            'is_public' => 'boolean',
            'allow_messages' => 'boolean',
        ]);
        
        $profile->update($validated);
        
        return redirect()->route('profiles.show', $profile)
            ->with('success', 'Perfil actualizado correctamente.');
    }
    
    /**
     * Follow a profile.
     */
    public function follow(Profile $profile)
    {
        $currentProfile = Auth::user()->getActiveProfile();
        
        if (!$currentProfile) {
            return response()->json(['error' => 'No active profile'], 403);
        }
        
        $currentProfile->follow($profile);
        
        // Create notification for the followed user
        $profile->user->notifications()->create([
            'type' => 'follow',
            'data' => [
                'follower_id' => $currentProfile->id,
                'follower_name' => $currentProfile->name,
            ],
        ]);
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Unfollow a profile.
     */
    public function unfollow(Profile $profile)
    {
        $currentProfile = Auth::user()->getActiveProfile();
        
        if (!$currentProfile) {
            return response()->json(['error' => 'No active profile'], 403);
        }
        
        $currentProfile->unfollow($profile);
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Show followers list.
     */
    public function followers(Profile $profile)
    {
        $followers = $profile->followers()
            ->with('user')
            ->paginate(20);
        
        return view('profiles.followers', compact('profile', 'followers'));
    }
    
    /**
     * Show following list.
     */
    public function following(Profile $profile)
    {
        $following = $profile->following()
            ->with('user')
            ->paginate(20);
        
        return view('profiles.following', compact('profile', 'following'));
    }
    
    /**
     * Show messages.
     */
    public function messages()
    {
        $profile = Auth::user()->getActiveProfile();
        
        if (!$profile) {
            return redirect()->route('user-profiles.index')
                ->with('error', 'Debes seleccionar un perfil primero.');
        }
        
        // Get conversations (unique sender/receiver pairs)
        $conversations = Message::where('receiver_id', $profile->id)
            ->orWhere('sender_id', $profile->id)
            ->with(['sender', 'receiver'])
            ->orderByDesc('created_at')
            ->get()
            ->groupBy(function ($message) use ($profile) {
                // Group by the other person in the conversation
                return $message->sender_id === $profile->id 
                    ? $message->receiver_id 
                    : $message->sender_id;
            })
            ->map(function ($messages, $otherProfileId) {
                $otherProfile = Profile::find($otherProfileId);
                $lastMessage = $messages->first();
                $unreadCount = $messages->where('receiver_id', auth()->user()->getActiveProfile()->id)
                    ->where('is_read', false)
                    ->count();
                
                return [
                    'profile' => $otherProfile,
                    'last_message' => $lastMessage,
                    'unread_count' => $unreadCount,
                ];
            })
            ->sortByDesc('last_message.created_at');
        
        return view('profiles.messages.index', compact('conversations'));
    }
    
    /**
     * Show conversation with a specific profile.
     */
    public function conversation(Profile $profile)
    {
        $currentProfile = Auth::user()->getActiveProfile();
        
        if (!$currentProfile) {
            return redirect()->route('user-profiles.index')
                ->with('error', 'Debes seleccionar un perfil primero.');
        }
        
        // Check if the other profile allows messages
        if (!$profile->allow_messages && !$profile->followers->contains($currentProfile)) {
            return redirect()->back()
                ->with('error', 'Este usuario no acepta mensajes.');
        }
        
        // Get messages between the two profiles
        $messages = Message::where(function ($query) use ($currentProfile, $profile) {
                $query->where('sender_id', $currentProfile->id)
                      ->where('receiver_id', $profile->id);
            })
            ->orWhere(function ($query) use ($currentProfile, $profile) {
                $query->where('sender_id', $profile->id)
                      ->where('receiver_id', $currentProfile->id);
            })
            ->orderBy('created_at')
            ->get();
        
        // Mark messages as read
        Message::where('sender_id', $profile->id)
            ->where('receiver_id', $currentProfile->id)
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);
        
        return view('profiles.messages.conversation', compact('profile', 'messages'));
    }
    
    /**
     * Send a message.
     */
    public function sendMessage(Request $request, Profile $profile)
    {
        $currentProfile = Auth::user()->getActiveProfile();
        
        if (!$currentProfile) {
            return response()->json(['error' => 'No active profile'], 403);
        }
        
        // Check if the other profile allows messages
        if (!$profile->allow_messages && !$profile->followers->contains($currentProfile)) {
            return response()->json(['error' => 'User does not accept messages'], 403);
        }
        
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);
        
        $message = Message::create([
            'sender_id' => $currentProfile->id,
            'receiver_id' => $profile->id,
            'content' => $validated['content'],
        ]);
        
        // Create notification for the receiver
        $profile->user->notifications()->create([
            'type' => 'message',
            'data' => [
                'sender_id' => $currentProfile->id,
                'sender_name' => $currentProfile->name,
                'message_id' => $message->id,
            ],
        ]);
        
        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'content' => $message->content,
                'created_at' => $message->created_at->toISOString(),
                'sender' => [
                    'id' => $currentProfile->id,
                    'name' => $currentProfile->name,
                    'avatar' => $currentProfile->avatar_url ?? asset('images/profiles/default.jpg'),
                ],
            ],
        ]);
    }
    
    /**
     * Show social feed from followed profiles.
     */
    public function feed()
    {
        $profile = Auth::user()->getActiveProfile();
        
        if (!$profile) {
            return redirect()->route('user-profiles.index')
                ->with('error', 'Debes seleccionar un perfil primero.');
        }
        
        // Get IDs of profiles being followed
        $followingIds = $profile->following()->pluck('profiles.id');
        
        if ($followingIds->isEmpty()) {
            return view('profiles.feed', ['activities' => collect(), 'profile' => $profile]);
        }
        
        // Get recent activities from followed profiles
        $activities = collect();
        
        // Recent ratings
        $ratings = \App\Models\Rating::whereIn('profile_id', $followingIds)
            ->with(['profile', 'title'])
            ->orderByDesc('created_at')
            ->limit(20)
            ->get()
            ->map(function ($rating) {
                return [
                    'type' => 'rating',
                    'profile' => $rating->profile,
                    'title' => $rating->title,
                    'score' => $rating->score,
                    'review' => $rating->review,
                    'created_at' => $rating->created_at,
                ];
            });
        
        // Recent comments
        $comments = \App\Models\Comment::whereIn('profile_id', $followingIds)
            ->with(['profile', 'commentable'])
            ->orderByDesc('created_at')
            ->limit(20)
            ->get()
            ->map(function ($comment) {
                return [
                    'type' => 'comment',
                    'profile' => $comment->profile,
                    'title' => $comment->commentable,
                    'content' => $comment->content,
                    'created_at' => $comment->created_at,
                ];
            });
        
        // Recent watchlist additions
        $watchlistItems = \App\Models\Watchlist::whereIn('profile_id', $followingIds)
            ->with(['profile', 'title'])
            ->orderByDesc('created_at')
            ->limit(20)
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'watchlist',
                    'profile' => $item->profile,
                    'title' => $item->title,
                    'created_at' => $item->created_at,
                ];
            });
        
        // New followers
        $newFollowers = \App\Models\ProfileFollower::whereIn('profile_id', $followingIds)
            ->with(['profile', 'follower'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(function ($follow) {
                return [
                    'type' => 'follow',
                    'profile' => $follow->profile,
                    'follower' => $follow->follower,
                    'created_at' => $follow->created_at,
                ];
            });
        
        // Merge and sort by date
        $activities = $activities
            ->concat($ratings)
            ->concat($comments)
            ->concat($watchlistItems)
            ->concat($newFollowers)
            ->sortByDesc('created_at')
            ->take(50);
        
        return view('profiles.feed', compact('activities', 'profile'));
    }
}