@extends('layouts.app')

@section('title', $news->title . ' - Dorasia')

@section('content')
<div style="padding-top: 100px;">
    <!-- News Hero Section -->
    <section class="news-hero" style="background-image: url('{{ $news->featured_image_url }}')">
        <div class="news-hero-overlay"></div>
        <div class="news-hero-content">
            <span class="news-category-badge">{{ ucfirst($news->category) }}</span>
            <h1 class="news-hero-title">{{ $news->title }}</h1>
            <div class="news-meta">
                <span class="news-date">{{ $news->published_at->format('d M Y') }}</span>
                <span>•</span>
                <span class="news-read-time">{{ $news->read_time }}</span>
                <span>•</span>
                <span class="news-views">{{ $news->views }} vistas</span>
            </div>
        </div>
    </section>

    <!-- News Content -->
    <div class="container" style="max-width: 800px; margin: 0 auto; padding: 2rem 4%;">
        <article class="news-article">
            @if($news->excerpt)
            <div class="news-excerpt">
                {{ $news->excerpt }}
            </div>
            @endif
            
            <div class="news-content">
                {!! $news->content !!}
            </div>
        </article>

        <!-- Related News -->
        @if($relatedNews->count() > 0)
        <section class="related-news" style="margin-top: 4rem; padding-top: 2rem; border-top: 1px solid rgba(255,255,255,0.1);">
            <h2 class="section-title" style="margin-bottom: 2rem;">Noticias Relacionadas</h2>
            <div class="related-news-grid">
                @foreach($relatedNews as $related)
                <a href="{{ route('news.show', $related->slug) }}" class="related-news-card">
                    <div class="related-news-image" style="background-image: url('{{ $related->featured_image_url }}')"></div>
                    <div class="related-news-content">
                        <span class="related-news-category">{{ ucfirst($related->category) }}</span>
                        <h3 class="related-news-title">{{ $related->title }}</h3>
                        <div class="related-news-meta">
                            {{ $related->published_at->diffForHumans() }}
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </section>
        @endif
    </div>
</div>

<style>
.news-hero {
    height: 60vh;
    position: relative;
    display: flex;
    align-items: center;
    background-size: cover;
    background-position: center;
    background-attachment: fixed;
}

.news-hero-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0.4) 50%, rgba(0,0,0,0.8) 100%);
}

.news-hero-content {
    position: relative;
    z-index: 10;
    padding: 0 4%;
    max-width: 800px;
    margin: 0 auto;
}

.news-category-badge {
    display: inline-block;
    background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.9rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 1rem;
}

.news-hero-title {
    font-size: 3rem;
    font-weight: 700;
    line-height: 1.2;
    margin-bottom: 1rem;
    color: white;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.8);
}

.news-meta {
    display: flex;
    align-items: center;
    gap: 1rem;
    color: #ccc;
    font-size: 1rem;
}

.news-article {
    background: rgba(20,20,20,0.95);
    border-radius: 12px;
    padding: 2rem;
    margin-top: -50px;
    position: relative;
    z-index: 20;
    border: 1px solid rgba(0, 212, 255, 0.2);
    backdrop-filter: blur(10px);
}

.news-excerpt {
    font-size: 1.2rem;
    color: #ddd;
    line-height: 1.6;
    margin-bottom: 2rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    font-style: italic;
}

.news-content {
    color: #e0e0e0;
    line-height: 1.8;
    font-size: 1.1rem;
}

.news-content p {
    margin-bottom: 1.5rem;
}

.news-content h2, .news-content h3 {
    color: white;
    margin: 2rem 0 1rem 0;
}

.related-news-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
}

.related-news-card {
    background: rgba(20,20,20,0.9);
    border-radius: 10px;
    overflow: hidden;
    text-decoration: none;
    color: inherit;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: 1px solid rgba(255,255,255,0.1);
}

.related-news-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0, 212, 255, 0.3);
    border-color: rgba(0, 212, 255, 0.5);
}

.related-news-image {
    height: 150px;
    background-size: cover;
    background-position: center;
}

.related-news-content {
    padding: 1rem;
}

.related-news-category {
    display: inline-block;
    background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%);
    color: white;
    padding: 0.2rem 0.5rem;
    border-radius: 8px;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    margin-bottom: 0.5rem;
}

.related-news-title {
    font-size: 1rem;
    font-weight: 600;
    line-height: 1.3;
    margin-bottom: 0.5rem;
    color: white;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.related-news-meta {
    font-size: 0.8rem;
    color: #999;
}

@media (max-width: 768px) {
    .news-hero {
        height: 50vh;
        background-attachment: scroll;
    }
    
    .news-hero-title {
        font-size: 2rem;
    }
    
    .news-meta {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .news-article {
        padding: 1.5rem;
        margin: 1rem;
    }
    
    .related-news-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection