<?php

// En app/Http/Controllers/WatchlistController.php

namespace App\Http\Controllers;

use App\Models\Watchlist;
use App\Models\WatchlistItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class WatchlistController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $watchlists = Auth::user()->watchlists()->with('items.content')->get();
        
        return view('user.watchlists', compact('watchlists'));
    }

    public function show($id)
    {
        $watchlist = Watchlist::where('id', $id)
            ->where(function($query) {
                $query->where('user_id', Auth::id())
                    ->orWhere('is_public', true);
            })
            ->with('items.content', 'user')
            ->firstOrFail();
        
        return view('user.watchlist-detail', compact('watchlist'));
    }

    public function create()
    {
        return view('user.create-watchlist');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_public' => 'boolean',
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        $watchlist = new Watchlist();
        $watchlist->user_id = Auth::id();
        $watchlist->name = $request->name;
        $watchlist->slug = Str::slug($request->name) . '-' . Str::random(5);
        $watchlist->description = $request->description;
        $watchlist->is_public = $request->has('is_public');
        $watchlist->save();
        
        return redirect()->route('watchlists.show', $watchlist->id)
            ->with('success', 'Lista creada correctamente.');
    }

    public function edit($id)
    {
        $watchlist = Watchlist::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();
        
        return view('user.edit-watchlist', compact('watchlist'));
    }

    public function update(Request $request, $id)
    {
        $watchlist = Watchlist::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_public' => 'boolean',
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        $watchlist->name = $request->name;
        $watchlist->description = $request->description;
        $watchlist->is_public = $request->has('is_public');
        $watchlist->save();
        
        return redirect()->route('watchlists.show', $watchlist->id)
            ->with('success', 'Lista actualizada correctamente.');
    }

    public function destroy($id)
    {
        $watchlist = Watchlist::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();
        
        $watchlist->delete();
        
        return redirect()->route('watchlists.index')
            ->with('success', 'Lista eliminada correctamente.');
    }

    public function addItem(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'watchlist_id' => 'required|exists:watchlists,id',
            'content_type' => 'required|string',
            'content_id' => 'required|integer',
            'note' => 'nullable|string|max:1000',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        
        // Verificar que la lista pertenece al usuario
        $watchlist = Watchlist::where('user_id', Auth::id())
            ->where('id', $request->watchlist_id)
            ->firstOrFail();
        
        // Obtener la posición máxima actual
        $maxPosition = WatchlistItem::where('watchlist_id', $watchlist->id)
            ->max('position') ?? 0;
        
        // Crear o actualizar el elemento
        $item = WatchlistItem::updateOrCreate(
            [
                'watchlist_id' => $watchlist->id,
                'content_type' => $request->content_type,
                'content_id' => $request->content_id,
            ],
            [
                'position' => $maxPosition + 1,
                'note' => $request->note,
            ]
        );
        
        return response()->json([
            'success' => true,
            'message' => 'Elemento añadido a la lista correctamente.',
            'item' => $item
        ]);
    }

    public function removeItem($id)
    {
        $item = WatchlistItem::whereHas('watchlist', function($query) {
                $query->where('user_id', Auth::id());
            })
            ->where('id', $id)
            ->firstOrFail();
        
        $item->delete();
        
        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Elemento eliminado de la lista correctamente.'
            ]);
        }
        
        return back()->with('success', 'Elemento eliminado de la lista correctamente.');
    }

    // Endpoint para toggle rápido (añadir/quitar de "Ver más tarde")
    public function toggleWatchlist(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'content_type' => 'required|string',
            'content_id' => 'required|integer',
            'action' => 'required|in:add,remove',
        ]);
        
        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }
        
        // Obtener o crear la lista "Ver más tarde"
        $watchlist = Watchlist::firstOrCreate(
            [
                'user_id' => Auth::id(),
                'name' => 'Ver más tarde',
            ],
            [
                'slug' => 'ver-mas-tarde-' . Str::random(5),
                'description' => 'Lista automática de contenido para ver más tarde',
                'is_public' => false,
            ]
        );
        
        if ($request->action === 'add') {
            // Obtener la posición máxima actual
            $maxPosition = WatchlistItem::where('watchlist_id', $watchlist->id)
                ->max('position') ?? 0;
            
            // Crear el elemento si no existe
            $item = WatchlistItem::firstOrCreate(
                [
                    'watchlist_id' => $watchlist->id,
                    'content_type' => $request->content_type,
                    'content_id' => $request->content_id,
                ],
                [
                    'position' => $maxPosition + 1,
                ]
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Añadido a Ver más tarde',
                'action' => 'added',
                'item' => $item
            ]);
        } else {
            // Eliminar el elemento si existe
            $deleted = WatchlistItem::where('watchlist_id', $watchlist->id)
                ->where('content_type', $request->content_type)
                ->where('content_id', $request->content_id)
                ->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Eliminado de Ver más tarde',
                'action' => 'removed',
                'deleted' => (bool)$deleted
            ]);
        }
    }
}
