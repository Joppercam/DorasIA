<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $featuredNews = News::where('featured', true)
            ->orderBy('published_at', 'desc')
            ->with('people')
            ->take(5)
            ->get();
            
        $latestNews = News::orderBy('published_at', 'desc')
            ->with('people')
            ->paginate(12);
            
        return view('news.index', [
            'featuredNews' => $featuredNews,
            'latestNews' => $latestNews,
        ]);
    }

    /**
     * Display the news related to a specific person.
     */
    public function personNews(string $slug)
    {
        $person = Person::where('slug', $slug)->firstOrFail();
        
        $news = $person->news()
            ->orderBy('published_at', 'desc')
            ->paginate(10);
            
        return view('news.person', [
            'person' => $person,
            'news' => $news,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        $news = News::where('slug', $slug)
            ->with('people')
            ->firstOrFail();
            
        $relatedNews = News::whereHas('people', function ($query) use ($news) {
                $personIds = $news->people->pluck('id');
                $query->whereIn('people.id', $personIds);
            })
            ->where('id', '!=', $news->id)
            ->orderBy('published_at', 'desc')
            ->limit(4)
            ->get();
            
        return view('news.show', [
            'news' => $news,
            'relatedNews' => $relatedNews,
        ]);
    }

    /**
     * Show the admin dashboard for news management.
     */
    public function admin()
    {
        // Only for admin users
        if (!auth()->user() || !auth()->user()->is_admin) {
            abort(403);
        }
        
        $news = News::orderBy('created_at', 'desc')->paginate(15);
        
        return view('news.admin', [
            'news' => $news,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Only for admin users
        if (!auth()->user() || !auth()->user()->is_admin) {
            abort(403);
        }
        
        $people = Person::orderBy('name')->get();
        
        return view('news.create', [
            'people' => $people,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Only for admin users
        if (!auth()->user() || !auth()->user()->is_admin) {
            abort(403);
        }
        
        $validated = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'image' => 'nullable|image|max:2048',
            'source_url' => 'nullable|url|max:255',
            'source_name' => 'nullable|max:100',
            'featured' => 'boolean',
            'published_at' => 'nullable|date',
            'people' => 'required|array',
            'primary_subjects' => 'nullable|array',
        ]);
        
        // Create slug from title
        $slug = Str::slug($validated['title']);
        
        // Handle image upload if present
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('news', 'public');
        }
        
        // Create news article
        $news = News::create([
            'title' => $validated['title'],
            'slug' => $slug,
            'content' => $validated['content'],
            'image' => $imagePath,
            'source_url' => $validated['source_url'] ?? null,
            'source_name' => $validated['source_name'] ?? null,
            'featured' => $validated['featured'] ?? false,
            'published_at' => $validated['published_at'] ?? now(),
        ]);
        
        // Attach people to news
        $primarySubjects = $validated['primary_subjects'] ?? [];
        foreach ($validated['people'] as $personId) {
            $news->people()->attach($personId, [
                'primary_subject' => in_array($personId, $primarySubjects),
            ]);
        }
        
        return redirect()->route('news.show', $news->slug)
            ->with('success', 'News article created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Only for admin users
        if (!auth()->user() || !auth()->user()->is_admin) {
            abort(403);
        }
        
        $news = News::findOrFail($id);
        $people = Person::orderBy('name')->get();
        $attachedPeopleIds = $news->people->pluck('id')->toArray();
        $primarySubjectIds = $news->primarySubjects->pluck('id')->toArray();
        
        return view('news.edit', [
            'news' => $news,
            'people' => $people,
            'attachedPeopleIds' => $attachedPeopleIds,
            'primarySubjectIds' => $primarySubjectIds,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Only for admin users
        if (!auth()->user() || !auth()->user()->is_admin) {
            abort(403);
        }
        
        $news = News::findOrFail($id);
        
        $validated = $request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'image' => 'nullable|image|max:2048',
            'source_url' => 'nullable|url|max:255',
            'source_name' => 'nullable|max:100',
            'featured' => 'boolean',
            'published_at' => 'nullable|date',
            'people' => 'required|array',
            'primary_subjects' => 'nullable|array',
        ]);
        
        // Create slug from title
        $slug = Str::slug($validated['title']);
        
        // Handle image upload if present
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('news', 'public');
        } else {
            $imagePath = $news->image;
        }
        
        // Update news article
        $news->update([
            'title' => $validated['title'],
            'slug' => $slug,
            'content' => $validated['content'],
            'image' => $imagePath,
            'source_url' => $validated['source_url'] ?? null,
            'source_name' => $validated['source_name'] ?? null,
            'featured' => $validated['featured'] ?? false,
            'published_at' => $validated['published_at'] ?? now(),
        ]);
        
        // Sync people to news
        $primarySubjects = $validated['primary_subjects'] ?? [];
        $peopleData = [];
        
        foreach ($validated['people'] as $personId) {
            $peopleData[$personId] = [
                'primary_subject' => in_array($personId, $primarySubjects),
            ];
        }
        
        $news->people()->sync($peopleData);
        
        return redirect()->route('news.show', $news->slug)
            ->with('success', 'News article updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Only for admin users
        if (!auth()->user() || !auth()->user()->is_admin) {
            abort(403);
        }
        
        $news = News::findOrFail($id);
        $news->delete();
        
        return redirect()->route('news.index')
            ->with('success', 'News article deleted successfully.');
    }
}
