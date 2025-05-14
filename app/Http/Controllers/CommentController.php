<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'has.active.profile']);
    }
    
    /**
     * Display a listing of comments.
     */
    public function index()
    {
        // Not implemented - comments are shown on the title page
    }

    /**
     * Show the form for creating a new comment.
     */
    public function create()
    {
        // Not implemented - comments are created via AJAX on the title page
    }

    /**
     * Store a newly created comment in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
            'commentable_type' => 'required|in:App\\Models\\Title,App\\Models\\Episode',
            'commentable_id' => 'required|integer',
        ]);
        
        $profile = auth()->user()->getActiveProfile();
        
        $comment = new \App\Models\Comment([
            'content' => $validated['content'],
            'profile_id' => $profile->id,
        ]);
        
        // Set the commentable relationship
        $commentableType = $validated['commentable_type'];
        $commentableId = $validated['commentable_id'];
        $commentable = $commentableType::findOrFail($commentableId);
        
        $comment->commentable()->associate($commentable);
        $comment->save();
        
        if ($request->wantsJson()) {
            return response()->json([
                'comment' => $comment->load('profile'),
                'message' => 'Comentario añadido correctamente.',
            ]);
        }
        
        return redirect()->back()->with('success', 'Comentario añadido correctamente.');
    }

    /**
     * Display the specified comment.
     */
    public function show(string $id)
    {
        // Not implemented - comments are shown on the title page
    }

    /**
     * Show the form for editing the specified comment.
     */
    public function edit(string $id)
    {
        // Not implemented - comments are edited inline
    }

    /**
     * Update the specified comment in storage.
     */
    public function update(Request $request, string $id)
    {
        $comment = \App\Models\Comment::findOrFail($id);
        $profile = auth()->user()->getActiveProfile();
        
        // Authorize the action
        if ($comment->profile_id !== $profile->id) {
            return response()->json([
                'message' => 'No estás autorizado para editar este comentario.',
            ], 403);
        }
        
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);
        
        $comment->update([
            'content' => $validated['content'],
        ]);
        
        if ($request->wantsJson()) {
            return response()->json([
                'comment' => $comment->fresh()->load('profile'),
                'message' => 'Comentario actualizado correctamente.',
            ]);
        }
        
        return redirect()->back()->with('success', 'Comentario actualizado correctamente.');
    }

    /**
     * Remove the specified comment from storage.
     */
    public function destroy(string $id)
    {
        $comment = \App\Models\Comment::findOrFail($id);
        $profile = auth()->user()->getActiveProfile();
        
        // Authorize the action
        if ($comment->profile_id !== $profile->id) {
            return response()->json([
                'message' => 'No estás autorizado para eliminar este comentario.',
            ], 403);
        }
        
        $comment->delete();
        
        if (request()->wantsJson()) {
            return response()->json([
                'message' => 'Comentario eliminado correctamente.',
            ]);
        }
        
        return redirect()->back()->with('success', 'Comentario eliminado correctamente.');
    }
    
    /**
     * Reply to an existing comment.
     */
    public function reply(Request $request, string $id)
    {
        $parentComment = \App\Models\Comment::findOrFail($id);
        $profile = auth()->user()->getActiveProfile();
        
        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);
        
        $comment = new \App\Models\Comment([
            'content' => $validated['content'],
            'profile_id' => $profile->id,
            'parent_id' => $parentComment->id,
        ]);
        
        // Set the commentable relationship to the same as the parent
        $comment->commentable()->associate($parentComment->commentable);
        $comment->save();
        
        if ($request->wantsJson()) {
            return response()->json([
                'comment' => $comment->load('profile'),
                'message' => 'Respuesta añadida correctamente.',
            ]);
        }
        
        return redirect()->back()->with('success', 'Respuesta añadida correctamente.');
    }
}
