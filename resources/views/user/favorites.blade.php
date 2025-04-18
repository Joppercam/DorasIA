// En resources/views/user/favorites.blade.php

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2>Mis Favoritos</h2>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if ($favorites->isEmpty())
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="bi bi-heart" style="font-size: 3rem;"></i>
            </div>
            <h4>No tienes favoritos</h4>
            <p class="text-muted">Añade películas y series a tus favoritos mientras exploras el catálogo.</p>
            <a href="{{ route('discover') }}" class="btn btn-primary mt-2">
                Explorar Catálogo
            </a>
        </div>
    @else
        <div class="row">
            @foreach ($favorites as $favorite)
                <div class="col-md-3 col-sm-4 col-6 mb-4">
                    <div class="card h-100">
                        <img src="{{ $favorite->content->poster_path ? asset('storage/' . $favorite->content->poster_path) : asset('images/placeholder-poster.jpg') }}" 
                             class="card-img-top" alt="{{ $favorite->content->title }}">
                        <div class="card-body">
                            <h5 class="card-title">{{ $favorite->content->title }}</h5>
                            <p class="card-text small">
                                @if ($favorite->content_type === 'App\\Models\\Movie')
                                    <span class="badge bg-primary">Película</span>
                                @else
                                    <span class="badge bg-success">Serie</span>
                                @endif
                                <small class="text-muted ms-2">
                                    {{ $favorite->created_at->format('d/m/Y') }}
                                </small>
                            </p>
                        </div>
                        <div class="card-footer bg-transparent d-flex justify-content-between">
                            <a href="{{ $favorite->content_type === 'App\\Models\\Movie' ? route('movies.show', $favorite->content->id) : route('tv-shows.show', $favorite->content->id) }}" 
                               class="btn btn-outline-primary btn-sm">Ver</a>
                            <button type="button" class="btn btn-outline-danger btn-sm remove-favorite" data-type="{{ $favorite->content_type }}" data-id="{{ $favorite->content_id }}">
                                <i class="bi bi-heart-fill"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $favorites->links() }}
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const removeButtons = document.querySelectorAll('.remove-favorite');
        
        removeButtons.forEach(button => {
            button.addEventListener('click', function() {
                const contentType = this.getAttribute('data-type');
                const contentId = this.getAttribute('data-id');
                
                fetch('/api/favorites/toggle', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        content_type: contentType,
                        content_id: contentId,
                        action: 'remove'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.closest('.col-md-3').remove();
                        
                        // Mostrar mensaje de éxito
                        const alertDiv = document.createElement('div');
                        alertDiv.className = 'alert alert-success';
                        alertDiv.setAttribute('role', 'alert');
                        alertDiv.textContent = data.message;
                        
                        const container = document.querySelector('.container');
                        container.insertBefore(alertDiv, container.querySelector('.row'));
                        
                        // Eliminar la alerta después de 3 segundos
                        setTimeout(() => {
                            alertDiv.remove();
                        }, 3000);
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        });
    });
</script>
@endsection