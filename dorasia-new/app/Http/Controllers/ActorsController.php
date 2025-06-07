<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\Request;

class ActorsController extends Controller
{
    public function index(Request $request)
    {
        $query = Person::query()
            ->whereNotNull('name')
            ->whereNotNull('biography');
        
        // Filter by search if provided
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                  ->orWhere('biography', 'LIKE', '%' . $search . '%');
            });
        }
        
        // Filter by popular actors (with profile images and known for)
        if ($request->get('filter') === 'popular') {
            $query->whereNotNull('profile_path')
                  ->where('popularity', '>', 5);
        }
        
        // Filter by Korean actors
        if ($request->get('filter') === 'korean' || !$request->filled('filter')) {
            $query->where(function($q) {
                $q->where('place_of_birth', 'LIKE', '%Korea%')
                  ->orWhere('place_of_birth', 'LIKE', '%South Korea%')
                  ->orWhere('place_of_birth', 'LIKE', '%Seoul%')
                  ->orWhere('place_of_birth', 'LIKE', '%Busan%')
                  ->orWhere('place_of_birth', 'LIKE', '%Incheon%');
            });
        }

        $actors = $query->orderBy('popularity', 'desc')
                       ->paginate(24);

        // Get some featured actors for the hero section
        $featuredActors = Person::whereNotNull('profile_path')
            ->whereNotNull('biography')
            ->where('popularity', '>', 10)
            ->where(function($q) {
                $q->where('place_of_birth', 'LIKE', '%Korea%')
                  ->orWhere('place_of_birth', 'LIKE', '%South Korea%');
            })
            ->orderBy('popularity', 'desc')
            ->take(6)
            ->get();

        return view('actors.index', compact('actors', 'featuredActors'));
    }

    public function show($id)
    {
        $actor = Person::with(['titles' => function($query) {
            $query->orderBy('popularity', 'desc')->take(10);
        }])->findOrFail($id);

        // Get actor's most popular series
        $popularSeries = $actor->titles()
            ->orderBy('popularity', 'desc')
            ->take(8)
            ->get();

        return view('actors.show', compact('actor', 'popularSeries'));
    }
}