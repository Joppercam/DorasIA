<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    /**
     * Display a listing of the news.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $news = News::with('people')
            ->orderBy('published_at', 'desc')
            ->paginate(12);
        
        return view('news.index', compact('news'));
    }
    
    /**
     * Display the specified news article.
     *
     * @param  string  $slug
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $news = News::with(['people' => function($query) {
            $query->orderBy('news_person.primary_subject', 'desc');
        }])
        ->where('slug', $slug)
        ->firstOrFail();
        
        // Get related news
        $relatedNews = News::with('people')
            ->where('id', '!=', $news->id)
            ->where(function($query) use ($news) {
                // Related by actors
                if ($news->people->isNotEmpty()) {
                    $query->whereHas('people', function($q) use ($news) {
                        $q->whereIn('people.id', $news->people->pluck('id'));
                    });
                }
                // Or by source
                $query->orWhere('source_name', $news->source_name);
            })
            ->orderBy('published_at', 'desc')
            ->limit(4)
            ->get();
        
        // Get more news from same actors
        $actorNews = [];
        if ($news->people->isNotEmpty()) {
            foreach ($news->people as $person) {
                $personNews = News::with('people')
                    ->whereHas('people', function($q) use ($person) {
                        $q->where('people.id', $person->id);
                    })
                    ->where('id', '!=', $news->id)
                    ->orderBy('published_at', 'desc')
                    ->limit(3)
                    ->get();
                
                if ($personNews->isNotEmpty()) {
                    $actorNews[$person->name] = $personNews;
                }
            }
        }
        
        return view('news.show', compact('news', 'relatedNews', 'actorNews'));
    }
}