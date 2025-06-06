@extends('layouts.app')

@section('title', 'Noticias de ' . ucfirst($category) . ' - Dorasia')

@section('content')
<div style="padding-top: 100px;">
    <!-- Category Header -->
    <section class="category-header">
        <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 4%;">
            <div class="category-header-content">
                <h1 class="category-header-title">📂 {{ ucfirst($category) }}</h1>
                <p class="category-header-subtitle">Noticias sobre {{ $category }} en el mundo K-Drama</p>
                
                <!-- Categories Filter -->
                @if($categories->count() > 0)
                <div class="news-categories">
                    <a href="{{ route('news.index') }}" class="category-filter">
                        Todas
                    </a>
                    @foreach($categories as $cat)
                        <a href="{{ route('news.category', $cat) }}" 
                           class="category-filter {{ $cat == $category ? 'active' : '' }}">
                            {{ ucfirst($cat) }}
                        </a>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Category News Grid -->
    <section class="category-news">
        <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 4%;">
            @if($news->count() > 0)
            <div class="news-count" style="margin-bottom: 2rem; color: #ccc;">
                {{ $news->total() }} {{ $news->total() == 1 ? 'noticia encontrada' : 'noticias encontradas' }}
            </div>
            
            <div class="news-grid">
                @foreach($news as $article)
                <a href="{{ route('news.show', $article->slug) }}" class="news-grid-card">
                    <div class="news-grid-image" style="background-image: url('{{ $article->featured_image_url }}')"></div>
                    <div class="news-grid-content">
                        <span class="news-grid-category">{{ ucfirst($article->category) }}</span>
                        <h3 class="news-grid-title">{{ $article->title }}</h3>
                        <p class="news-grid-excerpt">{{ Str::limit($article->excerpt, 120) }}</p>
                        <div class="news-grid-meta">
                            <span>{{ $article->published_at->diffForHumans() }}</span>
                            <span>•</span>
                            <span>{{ $article->views }} vistas</span>
                            <span>•</span>
                            <span>{{ $article->read_time }}</span>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="news-pagination">
                {{ $news->links() }}
            </div>
            @else
            <div class="no-news">
                <div class="no-news-content">
                    <h3>No hay noticias en esta categoría</h3>
                    <p>Aún no tenemos noticias publicadas sobre {{ $category }}.</p>
                    <a href="{{ route('news.index') }}" class="btn-back">Ver todas las noticias</a>
                </div>
            </div>
            @endif
        </div>
    </section>
</div>

<style>
.category-header {
    background: linear-gradient(135deg, rgba(20,20,20,0.95) 0%, rgba(40,40,40,0.95) 100%);
    border-radius: 12px;
    padding: 3rem 0;
    margin: 2rem 4%;
    border: 1px solid rgba(0, 212, 255, 0.2);
    backdrop-filter: blur(10px);
}

.category-header-title {
    font-size: 3rem;
    font-weight: 700;
    color: white;
    margin-bottom: 1rem;
    text-align: center;
}

.category-header-subtitle {
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

.category-news {
    margin: 3rem 0;
}

.news-count {
    font-size: 1rem;
    color: #999;
    text-align: center;
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
    padding: 4rem 2rem;
}

.no-news-content {
    background: rgba(20,20,20,0.9);
    border-radius: 12px;
    padding: 3rem;
    border: 1px solid rgba(255,255,255,0.1);
    max-width: 500px;
    margin: 0 auto;
}

.no-news h3 {
    color: white;
    font-size: 1.5rem;
    margin-bottom: 1rem;
}

.no-news p {
    color: #ccc;
    margin-bottom: 2rem;
    line-height: 1.5;
}

.btn-back {
    display: inline-block;
    background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%);
    color: white;
    padding: 0.8rem 2rem;
    border-radius: 25px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-back:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 212, 255, 0.3);
}

@media (max-width: 768px) {
    .category-header-title {
        font-size: 2rem;
    }
    
    .news-categories {
        gap: 0.5rem;
    }
    
    .category-filter {
        font-size: 0.8rem;
        padding: 0.4rem 0.8rem;
    }
    
    .news-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .news-grid-content {
        padding: 1rem;
    }
    
    .no-news-content {
        padding: 2rem;
    }
}
</style>
@endsection