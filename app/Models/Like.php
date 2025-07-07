<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Like extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'likeable_type',
        'likeable_id',
        'reaction_type'
    ];

    /**
     * Get the user that owns the like
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent likeable model (movie or series)
     */
    public function likeable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Check if a user has already reacted to a specific item with a specific reaction type
     */
    public static function hasReaction($userId, $likeableType, $likeableId, $reactionType): bool
    {
        return static::where('user_id', $userId)
            ->where('likeable_type', $likeableType)
            ->where('likeable_id', $likeableId)
            ->where('reaction_type', $reactionType)
            ->exists();
    }

    /**
     * Check if a user has already liked a specific item (backward compatibility)
     */
    public static function isLikedBy($userId, $likeableType, $likeableId): bool
    {
        return static::hasReaction($userId, $likeableType, $likeableId, 'like');
    }

    /**
     * Check if a user has already loved a specific item
     */
    public static function isLovedBy($userId, $likeableType, $likeableId): bool
    {
        return static::hasReaction($userId, $likeableType, $likeableId, 'love');
    }

    /**
     * Toggle reaction for a user and item
     */
    public static function toggleReaction($userId, $likeable, $reactionType = 'like'): bool
    {
        $likeableType = get_class($likeable);
        $likeableId = $likeable->id;

        $existingReaction = static::where('user_id', $userId)
            ->where('likeable_type', $likeableType)
            ->where('likeable_id', $likeableId)
            ->where('reaction_type', $reactionType)
            ->first();

        if ($existingReaction) {
            $existingReaction->delete();
            return false; // Reaction removed
        } else {
            static::create([
                'user_id' => $userId,
                'likeable_type' => $likeableType,
                'likeable_id' => $likeableId,
                'reaction_type' => $reactionType
            ]);
            return true; // Reaction added
        }
    }

    /**
     * Toggle like for a user and item (backward compatibility)
     */
    public static function toggleLike($userId, $likeable): bool
    {
        return static::toggleReaction($userId, $likeable, 'like');
    }

    /**
     * Toggle love for a user and item
     */
    public static function toggleLove($userId, $likeable): bool
    {
        return static::toggleReaction($userId, $likeable, 'love');
    }

    /**
     * Get total reactions count for a specific item and reaction type
     */
    public static function getReactionsCount($likeableType, $likeableId, $reactionType): int
    {
        return static::where('likeable_type', $likeableType)
            ->where('likeable_id', $likeableId)
            ->where('reaction_type', $reactionType)
            ->count();
    }

    /**
     * Get total likes count for a specific item (backward compatibility)
     */
    public static function getTotalLikesCount($likeableType, $likeableId): int
    {
        return static::getReactionsCount($likeableType, $likeableId, 'like');
    }

    /**
     * Get total loves count for a specific item
     */
    public static function getTotalLovesCount($likeableType, $likeableId): int
    {
        return static::getReactionsCount($likeableType, $likeableId, 'love');
    }
}
