<?php

// En app/Http/Controllers/UserController.php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function toggleFavorite(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'content_type' => 'required|string',
            'content_id' => 'required|integer',
            'action' => 'required|in:add,remove',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        
        if ($request->action === 'add') {
            // Crear el favorito si no existe
            $favorite = Favorite::firstOrCreate([
                'user_id' => Auth::id(),
                'content_type' => $request->content_type,
                'content_id' => $request->content_id,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Añadido a favoritos',
                'action' => 'added',
                'favorite' => $favorite
            ]);
        } else {
            // Eliminar el favorito si existe
            $deleted = Favorite::where('user_id', Auth::id())
                ->where('content_type', $request->content_type)
                ->where('content_id', $request->content_id)
                ->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Eliminado de favoritos',
                'action' => 'removed',
                'deleted' => (bool)$deleted
            ]);
        }
    }

// En app/Http/Controllers/UserController.php (continuación)

public function rate(Request $request)
{
    $validator = Validator::make($request->all(), [
        'content_type' => 'required|string',
        'content_id' => 'required|integer',
        'rating' => 'required|numeric|min:0.5|max:10',
        'review' => 'nullable|string|max:1000',
        'contains_spoilers' => 'nullable|boolean',
    ]);
    
    if ($validator->fails()) {
        return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
    }
    
    // Crear o actualizar la valoración
    $rating = Rating::updateOrCreate(
        [
            'user_id' => Auth::id(),
            'content_type' => $request->content_type,
            'content_id' => $request->content_id,
        ],
        [
            'rating' => $request->rating,
            'review' => $request->review,
            'contains_spoilers' => $request->has('contains_spoilers'),
        ]
    );
    
    return response()->json([
        'success' => true,
        'message' => 'Valoración guardada correctamente',
        'rating' => $rating
    ]);
}

public function favorites()
{
    $favorites = Favorite::where('user_id', Auth::id())
        ->with('content')
        ->orderBy('created_at', 'desc')
        ->paginate(24);
    
    return view('user.favorites', compact('favorites'));
}

public function ratings()
{
    $ratings = Rating::where('user_id', Auth::id())
        ->with('content')
        ->orderBy('created_at', 'desc')
        ->paginate(24);
    
    return view('user.ratings', compact('ratings'));
}
}
