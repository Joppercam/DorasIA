<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
                    'commentable' => $comment->commentable,
                    'content' => $comment->content,
                    'created_at' => $comment->created_at,
                ];
            });
        
        // Merge and sort by date
        $recentActivity = $recentRatings->concat($recentComments)
            ->sortByDesc('created_at')
            ->take(10);
        
        // Check if following this profile
        $isFollowing = false;
        if (Auth::check() && Auth::user()->getActiveProfile()) {
            $isFollowing = Auth::user()->getActiveProfile()->isFollowing($profile);
        }
        
        return view('profiles.show', compact('profile', 'recentActivity', 'isFollowing'));
    }
    
    /**
     * Show the form for editing the profile.
     */
    public function edit(Profile $profile)
    {
        // Check permission manually
        if (Auth::user()->id !== $profile->user_id) {
            abort(403, 'No tienes permiso para editar este perfil.');
        }
        
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
     * Show messages inbox.
     */
    public function messages()
    {
        $profile = Auth::user()->getActiveProfile();
        
        // Get conversations (latest message from each user)
        $conversations = Message::where('receiver_id', $profile->id)
            ->orWhere('sender_id', $profile->id)
            ->with(['sender', 'receiver'])
            ->orderByDesc('created_at')
            ->get()
            ->groupBy(function ($message) use ($profile) {
                return $message->sender_id === $profile->id 
                    ? $message->receiver_id 
                    : $message->sender_id;
            })
            ->map(function ($messages) {
                return $messages->first();
            });
        
        return view('profiles.messages', compact('conversations'));
    }
    
    /**
     * Show conversation with specific user.
     */
    public function conversation($userId)
    {
        $currentProfile = Auth::user()->getActiveProfile();
        $otherProfile = Profile::findOrFail($userId);
        
        // Check if messaging is allowed
        if (!$otherProfile->allow_messages && !$otherProfile->isFollowing($currentProfile)) {
            abort(403, 'Este usuario no acepta mensajes.');
        }
        
        $messages = Message::where(function ($query) use ($currentProfile, $otherProfile) {
                $query->where('sender_id', $currentProfile->id)
                      ->where('receiver_id', $otherProfile->id);
            })
            ->orWhere(function ($query) use ($currentProfile, $otherProfile) {
                $query->where('sender_id', $otherProfile->id)
                      ->where('receiver_id', $currentProfile->id);
            })
            ->orderBy('created_at')
            ->get();
        
        // Mark messages as read
        Message::where('sender_id', $otherProfile->id)
            ->where('receiver_id', $currentProfile->id)
            ->where('read', false)
            ->update(['read' => true]);
        
        return view('profiles.conversation', compact('otherProfile', 'messages'));
    }
    
    /**
     * Send a message.
     */
    public function sendMessage(Request $request)
    {
        $validated = $request->validate([
            'receiver_id' => 'required|exists:profiles,id',
            'content' => 'required|string|max:1000',
        ]);
        
        $senderProfile = Auth::user()->getActiveProfile();
        $receiverProfile = Profile::findOrFail($validated['receiver_id']);
        
        // Check if messaging is allowed
        if (!$receiverProfile->allow_messages && !$receiverProfile->isFollowing($senderProfile)) {
            return response()->json(['error' => 'Este usuario no acepta mensajes.'], 403);
        }
        
        $message = Message::create([
            'sender_id' => $senderProfile->id,
            'receiver_id' => $receiverProfile->id,
            'content' => $validated['content'],
        ]);
        
        // Create notification
        $receiverProfile->user->notifications()->create([
            'type' => 'message',
            'data' => [
                'sender_id' => $senderProfile->id,
                'sender_name' => $senderProfile->name,
                'message_id' => $message->id,
            ],
        ]);
        
        return response()->json($message->load(['sender', 'receiver']));
    }
    
    /**
     * Show activity feed.
     */
    public function feed()
    {
        $profile = Auth::user()->getActiveProfile();
        
        // Get activity from followed profiles
        $followingIds = $profile->following()->pluck('profiles.id');
        
        // Recent ratings from followed profiles
        $recentRatings = \App\Models\Rating::whereIn('profile_id', $followingIds)
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
        
        // Recent comments from followed profiles
        $recentComments = \App\Models\Comment::whereIn('profile_id', $followingIds)
            ->with(['profile', 'commentable'])
            ->orderByDesc('created_at')
            ->limit(20)
            ->get()
            ->map(function ($comment) {
                return [
                    'type' => 'comment',
                    'profile' => $comment->profile,
                    'commentable' => $comment->commentable,
                    'content' => $comment->content,
                    'created_at' => $comment->created_at,
                ];
            });
        
        // Merge and sort by date
        $feedItems = $recentRatings->concat($recentComments)
            ->sortByDesc('created_at')
            ->take(50);
        
        return view('profiles.feed', compact('feedItems'));
    }
}