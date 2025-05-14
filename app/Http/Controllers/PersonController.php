<?php

namespace App\Http\Controllers;

use App\Models\Person;
use App\Models\Title;
use Illuminate\Http\Request;

class PersonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $popularPeople = Person::orderBy('popularity', 'desc')
            ->limit(20)
            ->get();
            
        return view('people.index', [
            'popularPeople' => $popularPeople,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        $person = Person::where('slug', $slug)
            ->with(['actingTitles', 'directedTitles', 'news' => function($query) {
                $query->orderBy('published_at', 'desc')->limit(5);
            }])
            ->firstOrFail();
            
        // Get person's titles
        $titles = $person->titles()
            ->orderBy('first_air_date', 'desc')
            ->get();
            
        // Get person's latest news
        $news = $person->news()
            ->orderBy('published_at', 'desc')
            ->limit(5)
            ->get();
            
        return view('people.show', [
            'person' => $person,
            'titles' => $titles,
            'news' => $news,
        ]);
    }
    
    /**
     * Display people by popularity.
     */
    public function popular()
    {
        $people = Person::orderBy('popularity', 'desc')
            ->paginate(24);
            
        return view('people.popular', [
            'people' => $people,
            'title' => 'Popular Actors',
        ]);
    }
    
    /**
     * Search for people.
     */
    public function search(Request $request)
    {
        $query = $request->input('q');
        
        $people = Person::where('name', 'like', "%{$query}%")
            ->orWhere('original_name', 'like', "%{$query}%")
            ->orderBy('popularity', 'desc')
            ->paginate(24);
            
        return view('people.search', [
            'people' => $people,
            'query' => $query,
        ]);
    }
    
    /**
     * Display only admin-related methods for authenticated admin users.
     */
    
    /**
     * Show the form for creating a new resource (Admin only).
     */
    public function create()
    {
        // Only for admin users
        if (!auth()->user() || !auth()->user()->is_admin) {
            abort(403);
        }
        
        return view('people.create');
    }

    /**
     * Store a newly created resource in storage (Admin only).
     */
    public function store(Request $request)
    {
        // Only for admin users
        if (!auth()->user() || !auth()->user()->is_admin) {
            abort(403);
        }
        
        // Implementation for admin to manually create a person
    }

    /**
     * Show the form for editing the specified resource (Admin only).
     */
    public function edit(string $id)
    {
        // Only for admin users
        if (!auth()->user() || !auth()->user()->is_admin) {
            abort(403);
        }
        
        $person = Person::findOrFail($id);
        
        return view('people.edit', [
            'person' => $person,
        ]);
    }

    /**
     * Update the specified resource in storage (Admin only).
     */
    public function update(Request $request, string $id)
    {
        // Only for admin users
        if (!auth()->user() || !auth()->user()->is_admin) {
            abort(403);
        }
        
        // Implementation for admin to update a person
    }

    /**
     * Remove the specified resource from storage (Admin only).
     */
    public function destroy(string $id)
    {
        // Only for admin users
        if (!auth()->user() || !auth()->user()->is_admin) {
            abort(403);
        }
        
        // Implementation for admin to delete a person
    }
}
