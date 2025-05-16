// En resources/views/user/watchlists.blade.php

@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>Mis Listas</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('watchlists.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Nueva Lista
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success" role="alert">
            {{ session('success') }}
        </div>
    @endif

    @if ($watchlists->isEmpty())
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="bi bi-bookmark-plus" style="font-size: 3rem;"></i>
            </div>
            <h4>No tienes listas creadas</h4>
            <p class="text-muted">Crea tu primera lista para organizar el contenido que te interesa.</p>
            <a href="{{ route('watchlists.create') }}" class="btn btn-primary mt-2">
                Crear una lista
            </a>
        </div>
    @else
        <div class="row">
            @foreach ($watchlists as $watchlist)
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">{{ $watchlist->name }}</h5>
                            <p class="card-text text-muted">
                                {{ Str::limit($watchlist->description, 100) }}
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-secondary">{{ $watchlist->items->count() }} {{ $watchlist->items->count() == 1 ? 'elemento' : 'elementos' }}</span>
                                <span class="badge bg-{{ $watchlist->is_public ? 'success' : 'secondary' }}">
                                    {{ $watchlist->is_public ? 'Pública' : 'Privada' }}
                                </span>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent d-flex justify-content-between">
                            <a href="{{ route('watchlists.show', $watchlist->id) }}" class="btn btn-outline-primary btn-sm">Ver</a>
                            <div>
                                <a href="{{ route('watchlists.edit', $watchlist->id) }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $watchlist->id }}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal de Confirmación de Eliminación -->
                <div class="modal fade" id="deleteModal{{ $watchlist->id }}" tabindex="-1" aria-labelledby="deleteModalLabel{{ $watchlist->id }}" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="deleteModalLabel{{ $watchlist->id }}">Confirmar Eliminación</h5>
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
            @endforeach
        </div>
    @endif
</div>
@endsection