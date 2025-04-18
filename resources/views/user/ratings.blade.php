// En resources/views/user/ratings.blade.php

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2>Mis Valoraciones</h2>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if ($ratings->isEmpty())
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="bi bi-star" style="font-size: 3rem;"></i>
            </div>
            <h4>No has valorado ningún contenido</h4>
            <p class="text-muted">Valora películas y series mientras exploras el catálogo.</p>
            <a href="{{ route('discover') }}" class="btn btn-primary mt-2">
                Explorar Catálogo
            </a>
        </div>
    @else
        <div class="row">
            @foreach ($ratings as $rating)
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <img src="{{ $rating->content->poster_path ? asset('storage/' . $rating->content->poster_path) : asset('images/placeholder-poster.jpg') }}" 
                                         alt="{{ $rating->content->title }}" class="rounded" style="width: 80px; height: 120px; object-fit: cover;">
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <h5 class="card-title mb-1">{{ $rating->content->title }}</h5>
                                        <div class="rating-badge">
                                            <span class="badge bg-warning text-dark">
                                                <i class="bi bi-star-fill"></i> {{ $rating->rating }}
                                            </span>
                                        </div>
                                    </div>
                                    <p class="card-text small mb-2">
                                        @if ($rating->content_type === 'App\\Models\\Movie')
                                            <span class="badge bg-primary">Película</span>
                                        @else
                                            <span class="badge bg-success">Serie</span>
                                        @endif
                                        <small class="text-muted ms-2">
                                            Valorado el {{ $rating->created_at->format('d/m/Y') }}
                                        </small>
                                    </p>
                                    @if ($rating->review)
                                        <div class="card bg-light mt-2">
                                            <div class="card-body py-2 px-3">
                                                <p class="card-text small mb-0">
                                                    @if ($rating->contains_spoilers)
                                                        <span class="badge bg-danger mb-1">Contiene spoilers</span><br>
                                                    @endif
                                                    {{ $rating->review }}
                                                </p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent d-flex justify-content-between">
                            <a href="{{ $rating->content_type === 'App\\Models\\Movie' ? route('movies.show', $rating->content->id) : route('tv-shows.show', $rating->content->id) }}" 
                               class="btn btn-outline-primary btn-sm">Ver Contenido</a>
                            <button type="button" class="btn btn-outline-danger btn-sm delete-rating" data-rating-id="{{ $rating->id }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $ratings->links() }}
        </div>
    @endif

    <!-- Modal para eliminar valoración -->
    <div class="modal fade" id="deleteRatingModal" tabindex="-1" aria-labelledby="deleteRatingModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteRatingModalLabel">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ¿Estás seguro que deseas eliminar esta valoración? Esta acción no se puede deshacer.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <form id="deleteRatingForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteButtons = document.querySelectorAll('.delete-rating');
        const deleteForm = document.getElementById('deleteRatingForm');
        
        deleteButtons.forEach(button => {
            button.addEventListener('click', function() {
                const ratingId = this.getAttribute('data-rating-id');
                deleteForm.action = `/ratings/${ratingId}`;
                
                const deleteModal = new bootstrap.Modal(document.getElementById('deleteRatingModal'));
                deleteModal.show();
            });
        });
    });
</script>
@endsection