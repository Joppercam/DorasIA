<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    /**
     * Display a listing of news
     */
    public function index()
    {
        $featuredNews = News::published()
            ->featured()
            ->latest()
            ->take(3)
            ->get();

        $latestNews = News::published()
            ->latest()
            ->paginate(12);

        $categories = News::published()
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('news.index', compact('featuredNews', 'latestNews', 'categories'));
    }

    /**
     * Display the specified news article
     */
    public function show(News $news)
    {
        // Increment views
        $news->incrementViews();

        // Get related news (same category, excluding current)
        $relatedNews = News::published()
            ->where('category', $news->category)
            ->where('id', '!=', $news->id)
            ->latest()
            ->take(4)
            ->get();

        // If not enough related news, get latest from other categories
        if ($relatedNews->count() < 4) {
            $additionalNews = News::published()
                ->where('id', '!=', $news->id)
                ->whereNotIn('id', $relatedNews->pluck('id'))
                ->latest()
                ->take(4 - $relatedNews->count())
                ->get();
            
            $relatedNews = $relatedNews->merge($additionalNews);
        }

        return view('news.show', compact('news', 'relatedNews'));
    }

    /**
     * Display news by category
     */
    public function category($category)
    {
        $news = News::published()
            ->byCategory($category)
            ->latest()
            ->paginate(12);

        $categories = News::published()
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('news.category', compact('news', 'category', 'categories'));
    }
}
