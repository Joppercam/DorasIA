<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActorContent;
use App\Models\ActorContentReaction;
use App\Models\ActorContentComment;
use Illuminate\Http\JsonResponse;

class ActorContentSocialController extends Controller
{
    /**
     * Agregar o actualizar reacción
     */
    public function addReaction(Request $request, $contentId): JsonResponse
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Debes iniciar sesión'], 401);
        }

        $request->validate([
            'type' => 'required|in:like,dislike,love'
        ]);

        $content = ActorContent::findOrFail($contentId);
        $resultType = $content->addReaction($user->id, $request->type);

        // Obtener conteos actualizados
        $counts = $content->reactions()
            ->selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        return response()->json([
            'success' => true,
            'userReaction' => $resultType,
            'counts' => [
                'like' => $counts['like'] ?? 0,
                'dislike' => $counts['dislike'] ?? 0,
                'love' => $counts['love'] ?? 0,
            ]
        ]);
    }

    /**
     * Agregar comentario
     */
    public function addComment(Request $request, $contentId): JsonResponse
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Debes iniciar sesión'], 401);
        }

        $request->validate([
            'content' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:actor_content_comments,id'
        ]);

        $content = ActorContent::findOrFail($contentId);
        
        $comment = $content->comments()->create([
            'user_id' => $user->id,
            'parent_id' => $request->parent_id,
            'content' => $request->content,
            'is_approved' => true // Auto-aprobar por ahora
        ]);

        $comment->load('user', 'replies.user');

        return response()->json([
            'success' => true,
            'comment' => [
                'id' => $comment->id,
                'content' => $comment->content,
                'user' => [
                    'id' => $comment->user->id,
                    'name' => $comment->user->name,
                ],
                'created_at' => $comment->created_at->diffForHumans(),
                'is_edited' => $comment->is_edited,
                'parent_id' => $comment->parent_id,
                'replies' => $comment->replies->map(function($reply) {
                    return [
                        'id' => $reply->id,
                        'content' => $reply->content,
                        'user' => [
                            'id' => $reply->user->id,
                            'name' => $reply->user->name,
                        ],
                        'created_at' => $reply->created_at->diffForHumans(),
                        'is_edited' => $reply->is_edited,
                    ];
                })
            ]
        ]);
    }

    /**
     * Obtener comentarios de un contenido
     */
    public function getComments($contentId): JsonResponse
    {
        $content = ActorContent::findOrFail($contentId);
        
        $comments = $content->comments()
            ->with('user', 'replies.user')
            ->get();

        return response()->json([
            'comments' => $comments->map(function($comment) {
                return [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'user' => [
                        'id' => $comment->user->id,
                        'name' => $comment->user->name,
                    ],
                    'created_at' => $comment->created_at->diffForHumans(),
                    'is_edited' => $comment->is_edited,
                    'replies' => $comment->replies->map(function($reply) {
                        return [
                            'id' => $reply->id,
                            'content' => $reply->content,
                            'user' => [
                                'id' => $reply->user->id,
                                'name' => $reply->user->name,
                            ],
                            'created_at' => $reply->created_at->diffForHumans(),
                            'is_edited' => $reply->is_edited,
                        ];
                    })
                ];
            })
        ]);
    }

    /**
     * Eliminar comentario
     */
    public function deleteComment($commentId): JsonResponse
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Debes iniciar sesión'], 401);
        }

        $comment = ActorContentComment::findOrFail($commentId);
        
        // Solo el autor puede eliminar su comentario
        if ($comment->user_id !== $user->id) {
            return response()->json(['error' => 'No tienes permisos'], 403);
        }

        $comment->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Editar comentario
     */
    public function editComment(Request $request, $commentId): JsonResponse
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'Debes iniciar sesión'], 401);
        }

        $request->validate([
            'content' => 'required|string|max:1000'
        ]);

        $comment = ActorContentComment::findOrFail($commentId);
        
        // Solo el autor puede editar su comentario
        if ($comment->user_id !== $user->id) {
            return response()->json(['error' => 'No tienes permisos'], 403);
        }

        $comment->update(['content' => $request->content]);
        $comment->markAsEdited();

        return response()->json([
            'success' => true,
            'comment' => [
                'id' => $comment->id,
                'content' => $comment->content,
                'is_edited' => true,
                'edited_at' => $comment->edited_at->diffForHumans()
            ]
        ]);
    }
}