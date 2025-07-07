<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Like;
use App\Models\Movie;
use App\Models\Series;

class LikeController extends Controller
{
    /**
     * Constructor - Apply auth middleware
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Toggle like for a movie
     */
    public function toggleMovieLike(Request $request, Movie $movie): JsonResponse
    {
        try {
            $userId = auth()->id();
            $isLiked = $movie->toggleLike($userId);
            
            $reactionsInfo = $movie->getReactionsInfo($userId);
            
            return response()->json([
                'success' => true,
                'is_liked' => $isLiked,
                'reactions' => $reactionsInfo,
                'message' => $isLiked ? 'Movie liked!' : 'Movie unliked!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error toggling like: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle love for a movie
     */
    public function toggleMovieLove(Request $request, Movie $movie): JsonResponse
    {
        try {
            $userId = auth()->id();
            $isLoved = $movie->toggleLove($userId);
            
            $reactionsInfo = $movie->getReactionsInfo($userId);
            
            return response()->json([
                'success' => true,
                'is_loved' => $isLoved,
                'reactions' => $reactionsInfo,
                'message' => $isLoved ? 'Movie loved!' : 'Movie love removed!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error toggling love: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle like for a series
     */
    public function toggleSeriesLike(Request $request, Series $series): JsonResponse
    {
        try {
            $userId = auth()->id();
            $isLiked = $series->toggleLike($userId);
            
            $reactionsInfo = $series->getReactionsInfo($userId);
            
            return response()->json([
                'success' => true,
                'is_liked' => $isLiked,
                'reactions' => $reactionsInfo,
                'message' => $isLiked ? 'Series liked!' : 'Series unliked!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error toggling like: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle love for a series
     */
    public function toggleSeriesLove(Request $request, Series $series): JsonResponse
    {
        try {
            $userId = auth()->id();
            $isLoved = $series->toggleLove($userId);
            
            $reactionsInfo = $series->getReactionsInfo($userId);
            
            return response()->json([
                'success' => true,
                'is_loved' => $isLoved,
                'reactions' => $reactionsInfo,
                'message' => $isLoved ? 'Series loved!' : 'Series love removed!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error toggling love: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get reactions info for a movie
     */
    public function getMovieReactions(Movie $movie): JsonResponse
    {
        $userId = auth()->id();
        $reactionsInfo = $movie->getReactionsInfo($userId);
        
        return response()->json([
            'success' => true,
            'data' => $reactionsInfo
        ]);
    }

    /**
     * Get likes info for a movie (backward compatibility)
     */
    public function getMovieLikes(Movie $movie): JsonResponse
    {
        $userId = auth()->id();
        $likesInfo = $movie->getLikesInfo($userId);
        
        return response()->json([
            'success' => true,
            'data' => $likesInfo
        ]);
    }

    /**
     * Get reactions info for a series
     */
    public function getSeriesReactions(Series $series): JsonResponse
    {
        $userId = auth()->id();
        $reactionsInfo = $series->getReactionsInfo($userId);
        
        return response()->json([
            'success' => true,
            'data' => $reactionsInfo
        ]);
    }

    /**
     * Get likes info for a series (backward compatibility)
     */
    public function getSeriesLikes(Series $series): JsonResponse
    {
        $userId = auth()->id();
        $likesInfo = $series->getLikesInfo($userId);
        
        return response()->json([
            'success' => true,
            'data' => $likesInfo
        ]);
    }

    /**
     * Get user's liked movies
     */
    public function getUserLikedMovies(Request $request): JsonResponse
    {
        $userId = auth()->id();
        
        $likedMovies = Movie::whereHas('likes', function($query) use ($userId) {
            $query->where('user_id', $userId)->where('reaction_type', 'like');
        })->with(['genres', 'likes' => function($query) use ($userId) {
            $query->where('user_id', $userId);
        }])->paginate(20);
        
        return response()->json([
            'success' => true,
            'data' => $likedMovies
        ]);
    }

    /**
     * Get user's loved movies
     */
    public function getUserLovedMovies(Request $request): JsonResponse
    {
        $userId = auth()->id();
        
        $lovedMovies = Movie::whereHas('likes', function($query) use ($userId) {
            $query->where('user_id', $userId)->where('reaction_type', 'love');
        })->with(['genres', 'likes' => function($query) use ($userId) {
            $query->where('user_id', $userId);
        }])->paginate(20);
        
        return response()->json([
            'success' => true,
            'data' => $lovedMovies
        ]);
    }

    /**
     * Get user's liked series
     */
    public function getUserLikedSeries(Request $request): JsonResponse
    {
        $userId = auth()->id();
        
        $likedSeries = Series::whereHas('likes', function($query) use ($userId) {
            $query->where('user_id', $userId)->where('reaction_type', 'like');
        })->with(['genres', 'likes' => function($query) use ($userId) {
            $query->where('user_id', $userId);
        }])->paginate(20);
        
        return response()->json([
            'success' => true,
            'data' => $likedSeries
        ]);
    }

    /**
     * Get user's loved series
     */
    public function getUserLovedSeries(Request $request): JsonResponse
    {
        $userId = auth()->id();
        
        $lovedSeries = Series::whereHas('likes', function($query) use ($userId) {
            $query->where('user_id', $userId)->where('reaction_type', 'love');
        })->with(['genres', 'likes' => function($query) use ($userId) {
            $query->where('user_id', $userId);
        }])->paginate(20);
        
        return response()->json([
            'success' => true,
            'data' => $lovedSeries
        ]);
    }
}
