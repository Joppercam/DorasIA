<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Title;
use App\Models\CommentLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * Get comments for a title
     */
    public function index(Title $title, Request $request)
    {
        $query = $title->comments()
            ->with(['profile.user', 'likes'])
            ->withCount(['replies', 'likes'])
            ->whereNull('parent_id'); // Only top-level comments

        // Sorting
        switch ($request->get('sort', 'newest')) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'popular':
                $query->orderBy('likes_count', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        // Filter user's own comments
        if ($request->get('mine') === 'true' && Auth::check()) {
            $query->whereHas('profile', function($q) {
                $q->where('user_id', Auth::id());
            });
        }

        $comments = $query->paginate(10);

        // Transform comments
        $comments->transform(function ($comment) {
            return $this->transformComment($comment);
        });

        return response()->json([
            'data' => $comments->items(),
            'current_page' => $comments->currentPage(),
            'has_more' => $comments->hasMorePages(),
        ]);
    }

    /**
     * Store a new comment
     */
    public function store(Title $title, Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:500',
        ]);

        $comment = $title->comments()->create([
            'profile_id' => Auth::user()->getActiveProfile()->id,
            'content' => $request->content,
        ]);

        $comment->load(['profile.user', 'likes']);
        $comment->loadCount(['replies', 'likes']);

        return response()->json($this->transformComment($comment));
    }

    /**
     * Toggle like on a comment
     */
    public function toggleLike(Comment $comment)
    {
        $profile = Auth::user()->getActiveProfile();
        
        $like = CommentLike::where([
            'comment_id' => $comment->id,
            'profile_id' => $profile->id,
        ])->first();

        if ($like) {
            $like->delete();
        } else {
            CommentLike::create([
                'comment_id' => $comment->id,
                'profile_id' => $profile->id,
            ]);
            
            // Send notification to comment owner (if not liking own comment)
            if ($comment->profile_id !== $profile->id) {
                $comment->profile->user->notify(new \App\Notifications\CommentLiked($comment, $profile));
            }
        }

        return response()->json(['success' => true]);
    }

    /**
     * Get replies for a comment
     */
    public function getReplies(Comment $comment)
    {
        $replies = $comment->replies()
            ->with(['profile.user', 'likes'])
            ->withCount('likes')
            ->orderBy('created_at', 'asc')
            ->paginate(5);

        $replies->transform(function ($reply) {
            return $this->transformComment($reply);
        });

        return response()->json([
            'data' => $replies->items(),
            'has_more' => $replies->hasMorePages(),
        ]);
    }

    /**
     * Store a reply to a comment
     */
    public function storeReply(Comment $comment, Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:500',
        ]);

        $profile = Auth::user()->getActiveProfile();
        
        $reply = $comment->replies()->create([
            'commentable_type' => $comment->commentable_type,
            'commentable_id' => $comment->commentable_id,
            'profile_id' => $profile->id,
            'content' => $request->content,
        ]);

        // Send notification to original comment owner (if not replying to own comment)
        if ($comment->profile_id !== $profile->id) {
            $comment->profile->user->notify(new \App\Notifications\NewCommentReply($reply, $comment));
        }

        $reply->load(['profile.user', 'likes']);
        $reply->loadCount('likes');

        return response()->json($this->transformComment($reply));
    }

    /**
     * Delete a comment
     */
    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);
        
        $comment->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Transform comment for API response
     */
    private function transformComment($comment)
    {
        $profile = Auth::user()?->getActiveProfile();
        
        return [
            'id' => $comment->id,
            'content' => $comment->content,
            'created_at' => $comment->created_at,
            'time_ago' => $comment->created_at->diffForHumans(),
            'profile' => [
                'id' => $comment->profile->id,
                'name' => $comment->profile->name ?? 'Usuario',
                'avatar_url' => $comment->profile->avatar_url ?? asset('images/profiles/default.jpg'),
                'user_id' => $comment->profile->user_id,
            ],
            'likes_count' => $comment->likes_count ?? 0,
            'replies_count' => $comment->replies_count ?? 0,
            'user_liked' => $profile ? $comment->likes->contains('profile_id', $profile->id) : false,
            'show_replies' => false,
            'show_reply_form' => false,
            'replies' => [],
            'has_more_replies' => false,
        ];
    }
}