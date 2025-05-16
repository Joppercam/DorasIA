<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\User;
use App\Models\Message;
use App\Models\Rating;
use App\Models\Comment;
use App\Models\Watchlist;
use App\Helpers\DatabaseHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class SocialController extends Controller
{
    /**
     * Follow a profile
     */
    public function follow(Profile $profile)
    {
        try {
            $currentProfile = auth()->user()->profile;
            
            if ($currentProfile->id === $profile->id) {
                return response()->json(['error' => 'No puedes seguirte a ti mismo'], 400);
            }
            
            $currentProfile->follow($profile);
            
            return response()->json([
                'success' => true,
                'followers_count' => $profile->fresh()->followers_count,
                'is_following' => true
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al seguir el perfil'], 500);
        }
    }

    /**
     * Unfollow a profile
     */
    public function unfollow(Profile $profile)
    {
        try {
            $currentProfile = auth()->user()->profile;
            $currentProfile->unfollow($profile);
            
            return response()->json([
                'success' => true,
                'followers_count' => $profile->fresh()->followers_count,
                'is_following' => false
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al dejar de seguir el perfil'], 500);
        }
    }

    /**
     * Get followers for a profile
     */
    public function followers(Profile $profile)
    {
        $followers = $profile->followers()
            ->with(['user:id,name', 'ratings', 'watchlist'])
            ->paginate(20);
            
        $followers->transform(function ($follower) {
            return [
                'id' => $follower->id,
                'name' => $follower->user->name,
                'username' => $follower->username,
                'avatar' => $follower->avatar,
                'bio' => $follower->bio,
                'is_verified' => $follower->is_verified,
                'followers_count' => $follower->followers_count,
                'ratings_count' => $follower->ratings()->count(),
                'watchlist_count' => $follower->watchlist()->count(),
                'is_following' => auth()->check() ? auth()->user()->profile->isFollowing($follower) : false
            ];
        });
        
        return response()->json($followers);
    }

    /**
     * Get profiles being followed
     */
    public function following(Profile $profile)
    {
        $following = $profile->following()
            ->with(['user:id,name', 'ratings', 'watchlist'])
            ->paginate(20);
            
        $following->transform(function ($followed) {
            return [
                'id' => $followed->id,
                'name' => $followed->user->name,
                'username' => $followed->username,
                'avatar' => $followed->avatar,
                'bio' => $followed->bio,
                'is_verified' => $followed->is_verified,
                'followers_count' => $followed->followers_count,
                'ratings_count' => $followed->ratings()->count(),
                'watchlist_count' => $followed->watchlist()->count(),
                'is_following' => auth()->check() ? auth()->user()->profile->isFollowing($followed) : false
            ];
        });
        
        return response()->json($following);
    }

    /**
     * Send a message
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'content' => 'required|string|max:1000'
        ]);
        
        $message = Message::create([
            'sender_id' => auth()->id(),
            'recipient_id' => $request->recipient_id,
            'content' => $request->content
        ]);
        
        $message->load(['sender.profile', 'recipient.profile']);
        
        return response()->json([
            'id' => $message->id,
            'sender_id' => $message->sender_id,
            'recipient_id' => $message->recipient_id,
            'content' => $message->content,
            'is_read' => false,
            'created_at' => $message->created_at
        ]);
    }

    /**
     * Get messages with another user
     */
    public function getMessages($otherUserId)
    {
        $messages = Message::where(function ($query) use ($otherUserId) {
                $query->where('sender_id', auth()->id())
                      ->where('recipient_id', $otherUserId);
            })
            ->orWhere(function ($query) use ($otherUserId) {
                $query->where('sender_id', $otherUserId)
                      ->where('recipient_id', auth()->id());
            })
            ->orderBy('created_at', 'asc')
            ->limit(50)
            ->get();
            
        // Mark messages as read
        Message::where('sender_id', $otherUserId)
            ->where('recipient_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
        
        $otherUser = User::with('profile')->find($otherUserId);
        
        return response()->json([
            'messages' => $messages,
            'otherUser' => [
                'id' => $otherUser->id,
                'name' => $otherUser->name,
                'profile' => [
                    'id' => $otherUser->profile->id,
                    'avatar' => $otherUser->profile->avatar,
                    'username' => $otherUser->profile->username
                ],
                'is_online' => false // Implement real-time online status later
            ]
        ]);
    }

    /**
     * Mark messages as read
     */
    public function markMessagesAsRead($otherUserId)
    {
        Message::where('sender_id', $otherUserId)
            ->where('recipient_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
            
        return response()->json(['success' => true]);
    }

    /**
     * Delete conversation
     */
    public function deleteConversation($otherUserId)
    {
        Message::where(function ($query) use ($otherUserId) {
                $query->where('sender_id', auth()->id())
                      ->where('recipient_id', $otherUserId);
            })
            ->orWhere(function ($query) use ($otherUserId) {
                $query->where('sender_id', $otherUserId)
                      ->where('recipient_id', auth()->id());
            })
            ->delete();
            
        return response()->json(['success' => true]);
    }

    /**
     * Get activity feed
     */
    public function getFeed(Request $request)
    {
        $profile = auth()->user()->profile;
        $page = $request->get('page', 1);
        $perPage = 20;
        
        // Get IDs of profiles the user follows
        $followingIds = $profile->following()->pluck('profiles.id')->toArray();
        $followingIds[] = $profile->id; // Include own activity
        
        // Get recent activities
        $activities = collect();
        
        // Get recent ratings
        $ratings = Rating::with(['profile.user', 'title'])
            ->whereIn('profile_id', $followingIds)
            ->where('score', '>', 0)
            ->where('created_at', '>', now()->subDays(30))
            ->orderBy('created_at', 'desc')
            ->limit($perPage)
            ->get()
            ->map(function ($rating) {
                return [
                    'id' => $rating->id,
                    'type' => 'rating',
                    'profile' => [
                        'id' => $rating->profile->id,
                        'user' => ['name' => $rating->profile->user->name],
                        'avatar' => $rating->profile->avatar
                    ],
                    'rating' => [
                        'score' => $rating->score,
                        'review' => $rating->review
                    ],
                    'title' => [
                        'slug' => $rating->title->slug,
                        'name' => $rating->title->name,
                        'poster_url' => $rating->title->poster_url,
                        'release_year' => $rating->title->release_year
                    ],
                    'created_at' => $rating->created_at,
                    'likes_count' => 0,
                    'comments_count' => 0,
                    'is_liked' => false
                ];
            });
        
        // Get recent watchlist additions
        $watchlistItems = Watchlist::with(['profile.user', 'title'])
            ->whereIn('profile_id', $followingIds)
            ->where('created_at', '>', now()->subDays(30))
            ->orderBy('created_at', 'desc')
            ->limit($perPage)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'type' => 'watchlist',
                    'profile' => [
                        'id' => $item->profile->id,
                        'user' => ['name' => $item->profile->user->name],
                        'avatar' => $item->profile->avatar
                    ],
                    'title' => [
                        'slug' => $item->title->slug,
                        'name' => $item->title->name,
                        'poster_url' => $item->title->poster_url,
                        'release_year' => $item->title->release_year
                    ],
                    'created_at' => $item->created_at,
                    'likes_count' => 0,
                    'comments_count' => 0,
                    'is_liked' => false
                ];
            });
        
        // Get recent comments
        $comments = Comment::with(['profile.user', 'title'])
            ->whereIn('profile_id', $followingIds)
            ->where('created_at', '>', now()->subDays(30))
            ->orderBy('created_at', 'desc')
            ->limit($perPage)
            ->get()
            ->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'type' => 'comment',
                    'profile' => [
                        'id' => $comment->profile->id,
                        'user' => ['name' => $comment->profile->user->name],
                        'avatar' => $comment->profile->avatar
                    ],
                    'comment' => [
                        'content' => $comment->content
                    ],
                    'title' => [
                        'slug' => $comment->title->slug,
                        'name' => $comment->title->name
                    ],
                    'created_at' => $comment->created_at,
                    'likes_count' => 0,
                    'comments_count' => 0,
                    'is_liked' => false
                ];
            });
        
        // Get recent follows
        $follows = DB::table('profile_followers')
            ->join('profiles', 'profile_followers.profile_id', '=', 'profiles.id')
            ->join('profiles as follower_profiles', 'profile_followers.follower_id', '=', 'follower_profiles.id')
            ->join('users as profile_users', 'profiles.user_id', '=', 'profile_users.id')
            ->join('users as follower_users', 'follower_profiles.user_id', '=', 'follower_users.id')
            ->whereIn('profile_followers.follower_id', $followingIds)
            ->where('profile_followers.created_at', '>', now()->subDays(30))
            ->select(
                'profile_followers.id',
                'profile_followers.created_at',
                'follower_profiles.id as follower_profile_id',
                'follower_users.name as follower_name',
                'follower_profiles.avatar as follower_avatar',
                'profiles.id as followed_profile_id',
                'profile_users.name as followed_name',
                'profiles.avatar as followed_avatar'
            )
            ->orderBy('profile_followers.created_at', 'desc')
            ->limit($perPage)
            ->get()
            ->map(function ($follow) {
                return [
                    'id' => $follow->id,
                    'type' => 'follow',
                    'profile' => [
                        'id' => $follow->follower_profile_id,
                        'user' => ['name' => $follow->follower_name],
                        'avatar' => $follow->follower_avatar
                    ],
                    'followed_profile' => [
                        'id' => $follow->followed_profile_id,
                        'user' => ['name' => $follow->followed_name],
                        'avatar' => $follow->followed_avatar
                    ],
                    'created_at' => $follow->created_at,
                    'likes_count' => 0,
                    'comments_count' => 0,
                    'is_liked' => false
                ];
            });
        
        // Merge and sort all activities
        $activities = $activities->concat($ratings)
            ->concat($watchlistItems)
            ->concat($comments)
            ->concat($follows)
            ->sortByDesc('created_at')
            ->take($perPage)
            ->values();
        
        return response()->json([
            'items' => $activities,
            'has_more' => $activities->count() >= $perPage
        ]);
    }

    /**
     * Search users for messaging
     */
    public function searchUsers(Request $request)
    {
        $query = $request->get('q');
        
        if (strlen($query) < 2) {
            return response()->json(['users' => []]);
        }
        
        $users = User::with('profile')
            ->where('id', '!=', auth()->id())
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhereHas('profile', function ($q2) use ($query) {
                      $q2->where('username', 'like', "%{$query}%");
                  });
            })
            ->limit(10)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'profile' => [
                        'id' => $user->profile->id,
                        'username' => $user->profile->username,
                        'avatar' => $user->profile->avatar
                    ]
                ];
            });
        
        return response()->json(['users' => $users]);
    }

    /**
     * Get profile statistics
     */
    public function getProfileStats(Profile $profile)
    {
        $cacheKey = "profile_stats_{$profile->id}";
        
        $stats = Cache::remember($cacheKey, 300, function () use ($profile) {
            // Monthly ratings with database compatibility
            $monthlyRatings = DB::table('ratings')
                ->where('profile_id', $profile->id)
                ->selectRaw(DatabaseHelper::monthFunction('created_at') . ' as month, COUNT(*) as count, AVG(score) as avg_score');
            
            $monthlyRatings = DatabaseHelper::whereYear($monthlyRatings, 'created_at', now()->year)
                ->groupBy('month')
                ->get();
            
            $genreStats = $profile->ratings()
                ->join('titles', 'ratings.title_id', '=', 'titles.id')
                ->join('title_genre', 'titles.id', '=', 'title_genre.title_id')
                ->join('genres', 'title_genre.genre_id', '=', 'genres.id')
                ->selectRaw('genres.name, COUNT(*) as count, AVG(ratings.score) as avg_score')
                ->groupBy('genres.name')
                ->orderByDesc('count')
                ->limit(10)
                ->get();
            
            $watchTime = $profile->watchHistories()
                ->join('episodes', 'watch_histories.episode_id', '=', 'episodes.id')
                ->sum('episodes.runtime');
            
            return [
                'total_ratings' => $profile->ratings()->count(),
                'total_comments' => $profile->comments()->count(),
                'total_watchlist' => $profile->watchlist()->count(),
                'total_watch_time' => $watchTime,
                'average_rating' => $profile->ratings()->avg('score'),
                'monthly_ratings' => $monthlyRatings,
                'genre_stats' => $genreStats,
                'followers_count' => $profile->followers_count,
                'following_count' => $profile->following_count
            ];
        });
        
        return response()->json($stats);
    }

    /**
     * Get suggested profiles to follow
     */
    public function getSuggestedProfiles()
    {
        $currentProfile = auth()->user()->profile;
        
        // Get profiles with similar tastes
        $suggestedProfiles = Profile::with('user')
            ->where('id', '!=', $currentProfile->id)
            ->whereNotIn('id', $currentProfile->following()->pluck('profiles.id'))
            ->withCount(['ratings as shared_titles_count' => function ($query) use ($currentProfile) {
                $query->whereIn('title_id', $currentProfile->ratings()->pluck('title_id'));
            }])
            ->having('shared_titles_count', '>', 0)
            ->orderByDesc('shared_titles_count')
            ->limit(5)
            ->get()
            ->map(function ($profile) {
                return [
                    'id' => $profile->id,
                    'name' => $profile->user->name,
                    'username' => $profile->username,
                    'avatar' => $profile->avatar,
                    'bio' => $profile->bio,
                    'followers_count' => $profile->followers_count,
                    'shared_titles_count' => $profile->shared_titles_count,
                    'is_verified' => $profile->is_verified
                ];
            });
        
        return response()->json($suggestedProfiles);
    }
}