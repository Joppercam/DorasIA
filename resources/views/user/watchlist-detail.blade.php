// En resources/views/user/watchlist-detail.blade.php

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>{{ $watchlist->name }}</h2>
            <div class="d-flex align-items-center mt-2">
                <img src="{{ $watchlist->user->avatar ? asset('storage/' . $watchlist->user->avatar) : asset('images/default-avatar.png') }}" 
                     alt="{{ $watchlist->user->name }}" class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover;">
                <span>Lista de {{ $watchlist->user->name }}</span>
                <span class="ms-3 badge bg-{{ $watchlist->is_public ? 'success' : 'secondary' }}">
                    {{ $watchlist->is_public ? 'Pública' : 'Privada' }}
                </span>
            </div>
            @if ($watchlist->description)
                <p class="text-muted mt-2">{{ $watchlist->description }}</p>
            @endif
        </div>
        <div class="col-md-4 text-end">
            @if ($watchlist->user_id === Auth::id())
                <a href="{{ route('watchlists.edit', $watchlist->id) }}" class="btn btn-outline-primary me-2">
                    <i class="bi bi-pencil"></i> Editar
                </a>
                <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal">
                    <i class="bi bi-trash"></i> Eliminar
                </button>
            @endif
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if ($watchlist->items->isEmpty())
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="bi bi-collection" style="font-size: 3rem;"></i>
            </div>
            <h4>Esta lista está vacía</h4>
            <p class="text-muted">Añade contenido a esta lista navegando por el catálogo.</p>
            <a href="{{ route('discover') }}" class="btn btn-primary mt-2">
                Explorar Catálogo
            </a>
        </div>
    @else
        <div class="row">
            @foreach ($watchlist->items as $item)
                <div class="col-md-3 col-sm-4 col-6 mb-4">
                    <div class="card h-100">
                        <img src="{{ $item->content->poster_path ? asset('storage/' . $item->content->poster_path) : asset('images/placeholder-poster.jpg') }}" 
                             class="card-img-top" alt="{{ $item->content->title }}">
                        <div class="card-body">
                            <h5 class="card-title">{{ $item->content->title }}</h5>
                            <p class="card-text small">
                                @if ($item->content_type === 'App\\Models\\Movie')
                                    <span class="badge bg-primary">Película</span>
                                @else
                                    <span class="badge bg-success">Serie</span>
                                @endif
                            </p>
                            @if ($item->note)
                                <div class="mt-2">
                                    <p class="card-text small text-muted">{{ Str::limit($item->note, 50) }}</p>
                                </div>
                            @endif
                        </div>
                        <div class="card-footer bg-transparent d-flex justify-content-between">
                        // En resources/views/user/watchlist-detail.blade.php (continuación)
                            <a href="{{ $item->content_type === 'App\\Models\\Movie' ? route('movies.show', $item->content->id) : route('tv-shows.show', $item->content->id) }}" 
                               class="btn btn-outline-primary btn-sm">Ver</a>
                            @if ($watchlist->user_id === Auth::id())
                                <button type="button" class="btn btn-outline-danger btn-sm remove-item" data-item-id="{{ $item->id }}">
                                    <i class="bi bi-x"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Modal de Confirmación de Eliminación de Lista -->
    @if ($watchlist->user_id === Auth::id())
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Confirmar Eliminación</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        ¿Estás seguro que deseas eliminar la lista "{{ $watchlist->name }}"? Esta acción no se puede deshacer.
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <form action="{{ route('watchlists.destroy', $watchlist->id) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Eliminar</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@if ($watchlist->user_id === Auth::id())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const removeButtons = document.querySelectorAll('.remove-item');
        
        removeButtons.forEach(button => {
            button.addEventListener('click', function() {
                if (confirm('¿Estás seguro que deseas eliminar este elemento de la lista?')) {
                    const itemId = this.getAttribute('data-item-id');
                    
                    fetch(`/watchlists/remove-item/${itemId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
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
                }
            });
        });
    });
</script>
@endif
@endsection