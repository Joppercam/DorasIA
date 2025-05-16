<?php

<<<<<<< HEAD
namespace App\Http\Controllers;

use App\Models\Genre;
use App\Models\Title;
use App\Models\Watchlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WatchlistController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'has.active.profile']);
    }
    
    /**
     * Display a listing of the user's watchlist.
     */
    public function index(Request $request)
    {
        $profile = auth()->user()->getActiveProfile();
        
        // Get filter parameters
        $category = $request->category ?? 'default';
        $sortBy = $request->sort_by ?? 'position';
        $sortOrder = $request->sort_order ?? 'asc';
        $viewType = $request->view_type ?? 'grid';
        $genreFilter = $request->genre ?? null;
        $yearFilter = $request->year ?? null;
        $typeFilter = $request->type ?? null;
        $priorityFilter = $request->priority ?? null;
        
        // Start building the query
        $query = Watchlist::where('profile_id', $profile->id)
            ->with(['title.genres']);
        
        // Apply filters
        if ($category !== 'all') {
            $query->where('category', $category);
        }
        
        if ($priorityFilter) {
            $query->where('priority', $priorityFilter);
        }
        
        // Apply title-related filters through joins
        if ($genreFilter || $yearFilter || $typeFilter) {
            $query->join('titles', 'watchlists.title_id', '=', 'titles.id');
            
            if ($genreFilter) {
                $query->join('title_genre', 'titles.id', '=', 'title_genre.title_id')
                      ->where('title_genre.genre_id', $genreFilter);
            }
            
            if ($yearFilter) {
                $query->where('titles.release_year', $yearFilter);
            }
            
            if ($typeFilter) {
                $query->where('titles.type', $typeFilter);
            }
            
            // Make sure to select the watchlist fields to avoid ambiguity
            $query->select('watchlists.*');
        }
        
        // Apply sorting
        switch ($sortBy) {
            case 'title':
                $query->join('titles as sort_titles', 'watchlists.title_id', '=', 'sort_titles.id')
                      ->orderBy('sort_titles.title', $sortOrder)
                      ->select('watchlists.*');
                break;
            case 'added_date':
                $query->orderBy('watchlists.created_at', $sortOrder);
                break;
            case 'release_year':
                $query->join('titles as sort_titles', 'watchlists.title_id', '=', 'sort_titles.id')
                      ->orderBy('sort_titles.release_year', $sortOrder)
                      ->select('watchlists.*');
                break;
            case 'priority':
                $query->orderBy('watchlists.priority', $sortOrder);
                break;
            case 'position':
            default:
                $query->orderBy('watchlists.position', $sortOrder);
                break;
        }
        
        // Get categories for filter dropdown
        $categories = Watchlist::CATEGORIES;
        
        // Get genres for filter dropdown
        $genres = Genre::orderBy('name')->get();
        
        // Get years for filter dropdown
        $years = Title::selectRaw('DISTINCT release_year')
            ->orderBy('release_year', 'desc')
            ->pluck('release_year')
            ->filter();
        
        // Get priorities for filter dropdown
        $priorities = Watchlist::PRIORITIES;
        
        // Paginate the results
        $watchlist = $query->paginate(24)->withQueryString();
        
        return view('watchlist.index', [
            'watchlist' => $watchlist,
            'profile' => $profile,
            'categories' => $categories,
            'genres' => $genres,
            'years' => $years,
            'priorities' => $priorities,
            'currentCategory' => $category,
            'currentSortBy' => $sortBy,
            'currentSortOrder' => $sortOrder,
            'currentViewType' => $viewType,
            'currentGenre' => $genreFilter,
            'currentYear' => $yearFilter,
            'currentType' => $typeFilter,
            'currentPriority' => $priorityFilter,
        ]);
    }

    /**
     * Store a newly created watchlist item in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title_id' => 'required|exists:titles,id',
            'category' => 'sometimes|string',
            'priority' => 'sometimes|string|in:high,medium,low',
            'notes' => 'sometimes|nullable|string',
        ]);
        
        $profile = auth()->user()->getActiveProfile();
        
        // Check if the title is already in the watchlist
        $exists = Watchlist::where('profile_id', $profile->id)
            ->where('title_id', $validated['title_id'])
            ->exists();
        
        if ($exists) {
            return response()->json([
                'message' => 'Este título ya está en tu lista.',
                'status' => 'exists',
            ]);
        }
        
        // Get the highest position value
        $maxPosition = Watchlist::where('profile_id', $profile->id)
            ->where('category', $validated['category'] ?? 'default')
            ->max('position');
        
        // Add to watchlist
        $watchlistItem = Watchlist::create([
            'profile_id' => $profile->id,
            'title_id' => $validated['title_id'],
            'category' => $validated['category'] ?? 'default',
            'position' => $maxPosition + 1,
            'priority' => $validated['priority'] ?? 'medium',
            'notes' => $validated['notes'] ?? null,
        ]);
        
        $title = Title::find($validated['title_id']);
        
        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Título añadido a tu lista.',
                'status' => 'added',
                'watchlist_item' => $watchlistItem,
            ]);
        }
        
        return redirect()->back()
            ->with('success', 'Título añadido a tu lista.');
    }

    /**
     * Remove the specified watchlist item from storage.
     */
    public function destroy(Request $request, $id)
    {
        $profile = auth()->user()->getActiveProfile();
        
        // Find the watchlist item
        $watchlistItem = Watchlist::where('profile_id', $profile->id)
            ->where('title_id', $id)
            ->first();
        
        if (!$watchlistItem) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Este título no está en tu lista.',
                    'status' => 'not_found',
                ]);
            }
            
            return redirect()->back()
                ->with('error', 'Este título no está en tu lista.');
        }
        
        // Get category and position for reordering
        $category = $watchlistItem->category;
        $position = $watchlistItem->position;
        
        // Remove from watchlist
        $watchlistItem->delete();
        
        // Reorder the remaining items
        Watchlist::where('profile_id', $profile->id)
            ->where('category', $category)
            ->where('position', '>', $position)
            ->decrement('position');
        
        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Título eliminado de tu lista.',
                'status' => 'removed',
            ]);
        }
        
        return redirect()->back()
            ->with('success', 'Título eliminado de tu lista.');
    }
    
    /**
     * Toggle a title in the watchlist (add if not exists, remove if exists).
     */
    public function toggle(Request $request)
    {
        // Log debug info
        Log::info('Watchlist toggle called', [
            'user_id' => auth()->id(),
            'has_user' => auth()->check(),
            'request_data' => $request->all(),
            'session_id' => session()->getId(),
            'active_profile_id' => session()->get('active_profile_id'),
            'method' => $request->method(),
            'headers' => $request->headers->all()
        ]);
        
        $validated = $request->validate([
            'title_id' => 'required|exists:titles,id',
            'category' => 'sometimes|string',
        ]);
        
        $profile = auth()->user()->getActiveProfile();
        
        if (!$profile) {
            Log::error('No active profile found for user', ['user_id' => auth()->id()]);
            return response()->json([
                'message' => 'No se encontró un perfil activo.',
                'status' => 'error',
            ], 400);
        }
        
        // Check if the title is already in the watchlist
        $watchlistItem = Watchlist::where('profile_id', $profile->id)
            ->where('title_id', $validated['title_id'])
            ->first();
        
        if ($watchlistItem) {
            // Get category and position for reordering
            $category = $watchlistItem->category;
            $position = $watchlistItem->position;
            
            // Remove from watchlist
            $watchlistItem->delete();
            
            // Reorder the remaining items
            Watchlist::where('profile_id', $profile->id)
                ->where('category', $category)
                ->where('position', '>', $position)
                ->decrement('position');
            
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Título eliminado de tu lista.',
                    'status' => 'removed',
                ]);
            }
            
            return redirect()->back()
                ->with('success', 'Título eliminado de tu lista.');
        } else {
            // Get the highest position value
            $category = $validated['category'] ?? 'default';
            $maxPosition = Watchlist::where('profile_id', $profile->id)
                ->where('category', $category)
                ->max('position');
            
            // Add to watchlist
            $watchlistItem = Watchlist::create([
                'profile_id' => $profile->id,
                'title_id' => $validated['title_id'],
                'category' => $category,
                'position' => ($maxPosition ?? 0) + 1,
            ]);
            
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Título añadido a tu lista.',
                    'status' => 'added',
                    'watchlist_item' => $watchlistItem,
                ]);
            }
            
            return redirect()->back()
                ->with('success', 'Título añadido a tu lista.');
        }
    }
    
    /**
     * Check if a title is in the user's watchlist.
     *
     * @param int $id Title ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function status($id)
    {
        $profile = auth()->user()->getActiveProfile();
        
        // Check if the title is in the watchlist
        $watchlistItem = Watchlist::where('profile_id', $profile->id)
            ->where('title_id', $id)
            ->first();
        
        $inWatchlist = (bool) $watchlistItem;
        
        return response()->json([
            'in_watchlist' => $inWatchlist,
            'watchlist_item' => $inWatchlist ? $watchlistItem : null,
        ]);
    }
    
    /**
     * Toggle the "liked" status of a watchlist item.
     */
    public function toggleLike(Request $request)
    {
        $validated = $request->validate([
            'title_id' => 'required|exists:titles,id',
        ]);
        
        $profile = auth()->user()->getActiveProfile();
        
        // Find the watchlist item
        $watchlistItem = Watchlist::where('profile_id', $profile->id)
            ->where('title_id', $validated['title_id'])
            ->first();
        
        if (!$watchlistItem) {
            return response()->json([
                'message' => 'Este título no está en tu lista.',
                'status' => 'not_found',
            ], 404);
        }
        
        // Toggle liked status
        $watchlistItem->liked = !$watchlistItem->liked;
        $watchlistItem->save();
        
        return response()->json([
            'message' => $watchlistItem->liked ? 'Marcado como favorito.' : 'Desmarcado como favorito.',
            'status' => 'success',
            'liked' => $watchlistItem->liked,
        ]);
    }
    
    /**
     * Update the notes for a watchlist item.
     */
    public function updateNotes(Request $request)
    {
        $validated = $request->validate([
            'title_id' => 'required|exists:titles,id',
            'notes' => 'required|string',
        ]);
        
        $profile = auth()->user()->getActiveProfile();
        
        // Find the watchlist item
        $watchlistItem = Watchlist::where('profile_id', $profile->id)
            ->where('title_id', $validated['title_id'])
            ->first();
        
        if (!$watchlistItem) {
            return response()->json([
                'message' => 'Este título no está en tu lista.',
                'status' => 'not_found',
            ], 404);
        }
        
        // Update notes
        $watchlistItem->notes = $validated['notes'];
        $watchlistItem->save();
        
        return response()->json([
            'message' => 'Notas actualizadas.',
            'status' => 'success',
        ]);
    }
    
    /**
     * Update the category of a watchlist item.
     */
    public function updateCategory(Request $request)
    {
        $validated = $request->validate([
            'title_id' => 'required|exists:titles,id',
            'category' => 'required|string',
        ]);
        
        $profile = auth()->user()->getActiveProfile();
        
        // Find the watchlist item
        $watchlistItem = Watchlist::where('profile_id', $profile->id)
            ->where('title_id', $validated['title_id'])
            ->first();
        
        if (!$watchlistItem) {
            return response()->json([
                'message' => 'Este título no está en tu lista.',
                'status' => 'not_found',
            ], 404);
        }
        
        // Get the old category
        $oldCategory = $watchlistItem->category;
        
        // If the category is changing
        if ($oldCategory !== $validated['category']) {
            // Get highest position in the new category
            $maxPosition = Watchlist::where('profile_id', $profile->id)
                ->where('category', $validated['category'])
                ->max('position');
            
            // Remove from the old category order
            Watchlist::where('profile_id', $profile->id)
                ->where('category', $oldCategory)
                ->where('position', '>', $watchlistItem->position)
                ->decrement('position');
            
            // Update category and set to end of new category
            $watchlistItem->category = $validated['category'];
            $watchlistItem->position = ($maxPosition ?? 0) + 1;
            $watchlistItem->save();
        }
        
        return response()->json([
            'message' => 'Categoría actualizada.',
            'status' => 'success',
            'category_name' => $watchlistItem->category_name,
        ]);
    }
    
    /**
     * Update the priority of a watchlist item.
     */
    public function updatePriority(Request $request)
    {
        $validated = $request->validate([
            'title_id' => 'required|exists:titles,id',
            'priority' => 'required|string|in:high,medium,low',
        ]);
        
        $profile = auth()->user()->getActiveProfile();
        
        // Find the watchlist item
        $watchlistItem = Watchlist::where('profile_id', $profile->id)
            ->where('title_id', $validated['title_id'])
            ->first();
        
        if (!$watchlistItem) {
            return response()->json([
                'message' => 'Este título no está en tu lista.',
                'status' => 'not_found',
            ], 404);
        }
        
        // Update priority
        $watchlistItem->priority = $validated['priority'];
        $watchlistItem->save();
        
        return response()->json([
            'message' => 'Prioridad actualizada.',
            'status' => 'success',
            'priority_name' => $watchlistItem->priority_name,
        ]);
    }
    
    /**
     * Update the position of a watchlist item (reordering).
     */
    public function updatePosition(Request $request)
    {
        $validated = $request->validate([
            'title_id' => 'required|exists:titles,id',
            'new_position' => 'required|integer|min:1',
        ]);
        
        $profile = auth()->user()->getActiveProfile();
        
        // Find the watchlist item
        $watchlistItem = Watchlist::where('profile_id', $profile->id)
            ->where('title_id', $validated['title_id'])
            ->first();
        
        if (!$watchlistItem) {
            return response()->json([
                'message' => 'Este título no está en tu lista.',
                'status' => 'not_found',
            ], 404);
        }
        
        $oldPosition = $watchlistItem->position;
        $newPosition = $validated['new_position'];
        $category = $watchlistItem->category;
        
        // Count items in the category
        $count = Watchlist::where('profile_id', $profile->id)
            ->where('category', $category)
            ->count();
        
        // Make sure the new position is within bounds
        $newPosition = max(1, min($count, $newPosition));
        
        // If position is the same, do nothing
        if ($oldPosition === $newPosition) {
            return response()->json([
                'message' => 'Sin cambios en la posición.',
                'status' => 'unchanged',
            ]);
        }
        
        // Start a transaction
        DB::beginTransaction();
        
        try {
            if ($newPosition < $oldPosition) {
                // Moving up - increment positions for items in between
                Watchlist::where('profile_id', $profile->id)
                    ->where('category', $category)
                    ->whereBetween('position', [$newPosition, $oldPosition - 1])
                    ->increment('position');
            } else {
                // Moving down - decrement positions for items in between
                Watchlist::where('profile_id', $profile->id)
                    ->where('category', $category)
                    ->whereBetween('position', [$oldPosition + 1, $newPosition])
                    ->decrement('position');
            }
            
            // Set the new position
            $watchlistItem->position = $newPosition;
            $watchlistItem->save();
            
            DB::commit();
            
            return response()->json([
                'message' => 'Posición actualizada.',
                'status' => 'success',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'Error al actualizar la posición.',
                'status' => 'error',
            ], 500);
        }
    }
    
    /**
     * Batch update positions (used for drag-and-drop reordering).
     */
    public function batchUpdatePositions(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:watchlists,id',
            'items.*.position' => 'required|integer|min:1',
        ]);
        
        $profile = auth()->user()->getActiveProfile();
        
        // Start a transaction
        DB::beginTransaction();
        
        try {
            foreach ($validated['items'] as $item) {
                // Make sure the watchlist item belongs to the user
                $watchlistItem = Watchlist::where('id', $item['id'])
                    ->where('profile_id', $profile->id)
                    ->first();
                
                if ($watchlistItem) {
                    $watchlistItem->position = $item['position'];
                    $watchlistItem->save();
                }
            }
            
            DB::commit();
            
            return response()->json([
                'message' => 'Posiciones actualizadas.',
                'status' => 'success',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'message' => 'Error al actualizar las posiciones.',
                'status' => 'error',
            ], 500);
        }
    }
}
=======
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
>>>>>>> 2bc24813cacc67cfcf0a52d7cddf93db925ae8fe
