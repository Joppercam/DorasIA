@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Películas</h1>
        <div>
            <button class="btn btn-outline-primary" type="button" data-bs-toggle="offcanvas" data-bs-target="#filtersOffcanvas">
                <i class="bi bi-funnel"></i> Filtros
            </button>
        </div>
    </div>

    <!-- Filtros activos -->
    @if(request()->has('genre') || request()->has('country') || request()->has('year'))
    <div class="d-flex flex-wrap gap-2 mb-4">
        <span class="badge bg-secondary">Filtros activos:</span>
        
        @if(request()->has('genre'))
            @php $genre = $genres->firstWhere('slug', request()->genre) @endphp
            @if($genre)
            <span class="badge bg-primary d-flex align-items-center">
                {{ $genre->name }}
                <a href="{{ route('catalog.movies', request()->except('genre')) }}" class="ms-2 text-white">
                    <i class="bi bi-x"></i>
                </a>
            </span>
            @endif
        @endif
        
        @if(request()->has('country'))
            @php $country = $countries->firstWhere('code', request()->country) @endphp
            @if($country)
            <span class="badge bg-primary d-flex align-items-center">
                {{ $country->name }}
                <a href="{{ route('catalog.movies', request()->except('country')) }}" class="ms-2 text-white">
                    <i class="bi bi-x"></i>
                </a>
            </span>
            @endif
        @endif
        
        @if(request()->has('year'))
            <span class="badge bg-primary d-flex align-items-center">
                Año: {{ request()->year }}
                <a href="{{ route('catalog.movies', request()->except('year')) }}" class="ms-2 text-white">
                    <i class="bi bi-x"></i>
                </a>
            </span>
        @endif
        
        <a href="{{ route('catalog.movies') }}" class="badge bg-danger d-flex align-items-center">
            Limpiar todos los filtros
        </a>
    </div>
    @endif

    <!-- Ordenamiento -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <span class="me-2">Ordenar por:</span>
            <div class="btn-group">
                <a href="{{ route('catalog.movies', array_merge(request()->except('sort'), ['sort' => 'popularity'])) }}" 
                   class="btn btn-sm {{ $sortBy == 'popularity' ? 'btn-primary' : 'btn-outline-primary' }}">
                    Popularidad
                </a>
                <a href="{{ route('catalog.movies', array_merge(request()->except('sort'), ['sort' => 'release_date'])) }}" 
                   class="btn btn-sm {{ $sortBy == 'release_date' ? 'btn-primary' : 'btn-outline-primary' }}">
                    Más recientes
                </a>
                <a href="{{ route('catalog.movies', array_merge(request()->except('sort'), ['sort' => 'rating'])) }}" 
                   class="btn btn-sm {{ $sortBy == 'rating' ? 'btn-primary' : 'btn-outline-primary' }}">
                    Mejor valoradas
                </a>
                <a href="{{ route('catalog.movies', array_merge(request()->except('sort'), ['sort' => 'title'])) }}" 
                   class="btn btn-sm {{ $sortBy == 'title' ? 'btn-primary' : 'btn-outline-primary' }}">
                    Título
                </a>
            </div>
        </div>
        
        <div>
            <span>{{ $movies->total() }} resultados</span>
        </div>
    </div>

    <!-- Listado de películas usando componentes Vue -->
    <div class="row" data-vue>
        @forelse($movies as $movie)
        <div class="col-lg-2 col-md-3 col-sm-4 col-6 mb-4">
            <content-card 
                :item="{{ json_encode([
                    'id' => $movie->id,
                    'title' => $movie->title,
                    'poster_path' => $movie->poster_path ? asset('storage/' . $movie->poster_path) : asset('images/placeholder-poster.jpg'),
                    'vote_average' => $movie->vote_average,
                    'year' => $movie->release_date ? $movie->release_date->format('Y') : 'N/A',
                    'country' => $movie->country_of_origin,
                    'type' => 'Película',
                    'link' => route('catalog.movie-detail', $movie->slug)
                ]) }}"
            ></content-card>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <div class="mb-4">
                <i class="bi bi-film display-1 text-muted"></i>
            </div>
            <h3>No se encontraron películas</h3>
            <p class="text-muted">Intenta cambiar los filtros o vuelve más tarde</p>
        </div>
        @endforelse
    </div>

    <!-- Paginación -->
    <div class="d-flex justify-content-center my-5">
        {{ $movies->links() }}
    </div>

    <!-- Offcanvas para filtros en móvil -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="filtersOffcanvas" aria-labelledby="filtersOffcanvasLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="filtersOffcanvasLabel">Filtros</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form action="{{ route('catalog.movies') }}" method="GET" id="filtersForm">
                @if(request()->has('sort'))
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                @endif
                
                <!-- Filtro por género -->
                <div class="mb-4">
                    <h6 class="mb-3">Género</h6>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($genres as $genre)
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="genre" 
                                id="genre{{ $genre->id }}" value="{{ $genre->slug }}"
                                {{ request('genre') == $genre->slug ? 'checked' : '' }}
                                onchange="document.getElementById('filtersForm').submit()">
                            <label class="form-check-label" for="genre{{ $genre->id }}">
                                {{ $genre->name }}
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>
                
                <!-- Filtro por país -->
                <div class="mb-4">
                    <h6 class="mb-3">País</h6>
                    <select class="form-select" name="country" onchange="document.getElementById('filtersForm').submit()">
                        <option value="">Todos los países</option>
                        @foreach($countries as $country)
                        <option value="{{ $country->code }}" {{ request('country') == $country->code ? 'selected' : '' }}>
                            {{ $country->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Filtro por año -->
                <div class="mb-4">
                    <h6 class="mb-3">Año</h6>
                    <select class="form-select" name="year" onchange="document.getElementById('filtersForm').submit()">
                        <option value="">Todos los años</option>
                        @for($i = date('Y'); $i >= 2000; $i--)
                        <option value="{{ $i }}" {{ request('year') == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary d-md-none w-100">Aplicar filtros</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    @media (max-width: 768px) {
        .offcanvas-start {
            width: 280px;
        }
    }
</style>
@endsection

@section('scripts')
<script>
    // Cerrar automáticamente el offcanvas en pantallas grandes
    document.addEventListener('DOMContentLoaded', function() {
        const offcanvas = document.getElementById('filtersOffcanvas');
        if (offcanvas) {
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 992) {
                    const bsOffcanvas = bootstrap.Offcanvas.getInstance(offcanvas);
                    if (bsOffcanvas) {
                        bsOffcanvas.hide();
                    }
                }
            });
        }
    });
</script>
@endsection