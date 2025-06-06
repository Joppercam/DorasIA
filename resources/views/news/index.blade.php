@extends('layouts.app')

@section('title', 'Noticias K-Drama - Dorasia')

@section('content')
<div style="padding-top: 100px;">
    <!-- News Header -->
    <section class="news-header">
        <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 4%;">
            <div class="news-header-content">
                <h1 class="news-header-title">📰 Noticias K-Drama</h1>
                <p class="news-header-subtitle">Las últimas novedades del mundo de los dramas coreanos</p>
                
                <!-- Categories Filter -->
                @if($categories->count() > 0)
                <div class="news-categories">
                    <a href="{{ route('news.index') }}" class="category-filter {{ !request('category') ? 'active' : '' }}">
                        Todas
                    </a>
                    @foreach($categories as $category)
                        <a href="{{ route('news.category', $category) }}" 
                           class="category-filter {{ request('category') == $category ? 'active' : '' }}">
                            {{ ucfirst($category) }}
                        </a>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Featured News -->
    @if($featuredNews->count() > 0)
    <section class="featured-news">
        <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 4%;">
            <h2 class="section-title" style="margin-bottom: 2rem;">✨ Noticias Destacadas</h2>
            <div class="featured-news-grid">
                @foreach($featuredNews as $news)
                <a href="{{ route('news.show', $news->slug) }}" class="featured-news-card {{ $loop->first ? 'main-featured' : '' }}">
                    <div class="featured-news-image" style="background-image: url('{{ $news->featured_image_url }}')"></div>
                    <div class="featured-news-overlay"></div>
                    <div class="featured-news-content">
                        <span class="featured-news-category">{{ ucfirst($news->category) }}</span>
                        <h3 class="featured-news-title">{{ $news->title }}</h3>
                        <p class="featured-news-excerpt">{{ $news->excerpt }}</p>
                        <div class="featured-news-meta">
                            <span>{{ $news->published_at->diffForHumans() }}</span>
                            <span>•</span>
                            <span>{{ $news->read_time }}</span>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <!-- Latest News Grid -->
    <section class="latest-news">
        <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 4%;">
            <h2 class="section-title" style="margin-bottom: 2rem;">🔥 Últimas Noticias</h2>
            
            @if($latestNews->count() > 0)
            <div class="news-grid">
                @foreach($latestNews as $news)
                <a href="{{ route('news.show', $news->slug) }}" class="news-grid-card">
                    <div class="news-grid-image" style="background-image: url('{{ $news->featured_image_url }}')"></div>
                    <div class="news-grid-content">
                        <span class="news-grid-category">{{ ucfirst($news->category) }}</span>
                        <h3 class="news-grid-title">{{ $news->title }}</h3>
                        <p class="news-grid-excerpt">{{ Str::limit($news->excerpt, 120) }}</p>
                        <div class="news-grid-meta">
                            <span>{{ $news->published_at->diffForHumans() }}</span>
                            <span>•</span>
                            <span>{{ $news->views }} vistas</span>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="news-pagination">
                {{ $latestNews->links() }}
            </div>
            @else
            <div class="no-news">
                <p>No hay noticias disponibles en este momento.</p>
            </div>
            @endif
        </div>
    </section>
</div>

<style>
.news-header {
    background: linear-gradient(135deg, rgba(20,20,20,0.95) 0%, rgba(40,40,40,0.95) 100%);
    border-radius: 12px;
    padding: 3rem 0;
    margin: 2rem 4%;
    border: 1px solid rgba(0, 212, 255, 0.2);
    backdrop-filter: blur(10px);
}

.news-header-title {
    font-size: 3rem;
    font-weight: 700;
    color: white;
    margin-bottom: 1rem;
    text-align: center;
}

.news-header-subtitle {
    font-size: 1.2rem;
    color: #ccc;
    text-align: center;
    margin-bottom: 2rem;
}

.news-categories {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.category-filter {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    text-decoration: none;
    color: #ccc;
    border: 1px solid rgba(255,255,255,0.2);
    transition: all 0.3s ease;
    font-size: 0.9rem;
    font-weight: 500;
}

.category-filter:hover,
.category-filter.active {
    background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%);
    color: white;
    border-color: transparent;
}

.featured-news {
    margin: 3rem 0;
}

.featured-news-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    grid-template-rows: 1fr 1fr;
    gap: 1rem;
    height: 500px;
}

.featured-news-card {
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    text-decoration: none;
    color: inherit;
    transition: transform 0.3s ease;
}

.featured-news-card.main-featured {
    grid-row: 1 / 3;
}

.featured-news-card:hover {
    transform: scale(1.02);
}

.featured-news-image {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-size: cover;
    background-position: center;
}

.featured-news-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(to bottom, rgba(0,0,0,0.3) 0%, rgba(0,0,0,0.8) 100%);
}

.featured-news-content {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 2rem;
    z-index: 10;
}

.featured-news-category {
    display: inline-block;
    background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%);
    color: white;
    padding: 0.3rem 0.8rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    margin-bottom: 1rem;
}

.featured-news-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: white;
    margin-bottom: 1rem;
    line-height: 1.3;
}

.main-featured .featured-news-title {
    font-size: 2rem;
}

.featured-news-excerpt {
    color: #ddd;
    line-height: 1.5;
    margin-bottom: 1rem;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.featured-news-meta {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #ccc;
    font-size: 0.9rem;
}

.latest-news {
    margin: 3rem 0;
}

.news-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 2rem;
}

.news-grid-card {
    background: rgba(20,20,20,0.9);
    border-radius: 12px;
    overflow: hidden;
    text-decoration: none;
    color: inherit;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    border: 1px solid rgba(255,255,255,0.1);
}

.news-grid-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0, 212, 255, 0.3);
    border-color: rgba(0, 212, 255, 0.5);
}

.news-grid-image {
    height: 200px;
    background-size: cover;
    background-position: center;
}

.news-grid-content {
    padding: 1.5rem;
}

.news-grid-category {
    display: inline-block;
    background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%);
    color: white;
    padding: 0.3rem 0.6rem;
    border-radius: 12px;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    margin-bottom: 1rem;
}

.news-grid-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: white;
    margin-bottom: 1rem;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.news-grid-excerpt {
    color: #ccc;
    line-height: 1.5;
    margin-bottom: 1rem;
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.news-grid-meta {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #999;
    font-size: 0.9rem;
}

.news-pagination {
    margin-top: 3rem;
    display: flex;
    justify-content: center;
}

.no-news {
    text-align: center;
    padding: 3rem;
    color: #999;
}

@media (max-width: 768px) {
    .news-header-title {
        font-size: 2rem;
    }
    
    .news-categories {
        gap: 0.5rem;
    }
    
    .category-filter {
        font-size: 0.8rem;
        padding: 0.4rem 0.8rem;
    }
    
    .featured-news-grid {
        grid-template-columns: 1fr;
        grid-template-rows: auto;
        height: auto;
    }
    
    .featured-news-card.main-featured {
        grid-row: auto;
    }
    
    .featured-news-content {
        padding: 1.5rem;
    }
    
    .featured-news-title {
        font-size: 1.2rem;
    }
    
    .main-featured .featured-news-title {
        font-size: 1.5rem;
    }
    
    .news-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .news-grid-content {
        padding: 1rem;
    }
}
</style>
@endsection