@extends('layouts.netflix-style-main')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <h1 class="text-2xl font-bold mb-4">Test Watchlist Toggle</h1>
    
    <div class="bg-gray-900 p-6 rounded-lg">
        <button 
            id="toggle-btn" 
            onclick="testToggle()"
            class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">
            Toggle Watchlist (Title ID: 1)
        </button>
        
        <div id="result" class="mt-4 p-4 bg-gray-800 rounded text-sm font-mono whitespace-pre"></div>
    </div>
    
    @auth
        <div class="mt-4 bg-gray-900 p-6 rounded-lg">
            <h2 class="text-lg font-semibold mb-2">Estado de autenticación:</h2>
            <p>Usuario: {{ auth()->user()->email }}</p>
            <p>Perfil activo: {{ auth()->user()->getActiveProfile() ? auth()->user()->getActiveProfile()->name : 'Ninguno' }}</p>
        </div>
    @else
        <div class="mt-4 bg-red-900 p-6 rounded-lg">
            <p>No estás autenticado</p>
        </div>
    @endauth
</div>

<script>
    function testToggle() {
        const button = document.getElementById('toggle-btn');
        const resultDiv = document.getElementById('result');
        
        resultDiv.textContent = 'Enviando solicitud...\n';
        
        fetch('{{ route('watchlist.toggle') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                title_id: 1
            })
        })
        .then(response => {
            resultDiv.textContent += 'Status: ' + response.status + '\n';
            resultDiv.textContent += 'Headers: ' + JSON.stringify([...response.headers.entries()]) + '\n';
            
            if (!response.ok) {
                return response.text().then(text => {
                    throw new Error('Response not OK: ' + response.status + ' - ' + text);
                });
            }
            return response.json();
        })
        .then(data => {
            resultDiv.textContent += 'Respuesta exitosa:\n' + JSON.stringify(data, null, 2);
            console.log('Éxito:', data);
        })
        .catch(error => {
            resultDiv.textContent += 'Error: ' + error.message + '\n';
            console.error('Error completo:', error);
        });
    }
</script>
@endsection