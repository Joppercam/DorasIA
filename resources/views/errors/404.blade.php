@extends('layouts.app')

@section('title', 'Página no encontrada - DORASIA')

@section('content')
<div style="min-height: 60vh; display: flex; align-items: center; justify-content: center; text-align: center; padding: 2rem;">
    <div style="max-width: 600px;">
        <!-- Error Icon -->
        <div style="font-size: 6rem; margin-bottom: 1rem;">
            😔
        </div>
        
        <!-- Error Title -->
        <h1 style="font-size: 3rem; font-weight: bold; color: white; margin-bottom: 1rem;">
            404
        </h1>
        
        <h2 style="font-size: 1.5rem; color: rgba(255,255,255,0.8); margin-bottom: 1.5rem;">
            ¡Oops! Página no encontrada
        </h2>
        
        <!-- Error Message -->
        <p style="font-size: 1.1rem; color: rgba(255,255,255,0.7); line-height: 1.6; margin-bottom: 2rem;">
            La página que estás buscando no existe o ha sido movida. 
            Puede que el enlace esté roto o que hayas escrito mal la URL.
        </p>
        
        <!-- Suggested Actions -->
        <div style="display: flex; flex-direction: column; gap: 1rem; align-items: center;">
            <a href="{{ route('home') }}" 
               style="background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%); 
                      color: white; 
                      padding: 1rem 2rem; 
                      border-radius: 8px; 
                      text-decoration: none; 
                      font-weight: 600; 
                      font-size: 1.1rem;
                      transition: all 0.3s ease;
                      display: inline-block;">
                🏠 Volver al Inicio
            </a>
            
            <div style="display: flex; gap: 1rem; flex-wrap: wrap; justify-content: center;">
                <a href="{{ route('browse') }}" 
                   style="background: rgba(255,255,255,0.1); 
                          color: white; 
                          padding: 0.75rem 1.5rem; 
                          border-radius: 6px; 
                          text-decoration: none; 
                          transition: background-color 0.3s ease;">
                    🔍 Explorar
                </a>
                
                <a href="{{ route('movies.index') }}" 
                   style="background: rgba(255,255,255,0.1); 
                          color: white; 
                          padding: 0.75rem 1.5rem; 
                          border-radius: 6px; 
                          text-decoration: none; 
                          transition: background-color 0.3s ease;">
                    🎬 Películas
                </a>
                
                <a href="/peliculas-disponibles.html" 
                   style="background: rgba(255,255,255,0.1); 
                          color: white; 
                          padding: 0.75rem 1.5rem; 
                          border-radius: 6px; 
                          text-decoration: none; 
                          transition: background-color 0.3s ease;">
                    📋 Lista completa
                </a>
                
                <a href="{{ route('news.index') }}" 
                   style="background: rgba(255,255,255,0.1); 
                          color: white; 
                          padding: 0.75rem 1.5rem; 
                          border-radius: 6px; 
                          text-decoration: none; 
                          transition: background-color 0.3s ease;">
                    📰 Noticias
                </a>
            </div>
        </div>
        
        <!-- Search Box -->
        <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid rgba(255,255,255,0.1);">
            <p style="color: rgba(255,255,255,0.6); margin-bottom: 1rem;">
                ¿Buscabas algo específico?
            </p>
            
            <div style="display: flex; max-width: 400px; margin: 0 auto; gap: 0.5rem;">
                <input type="text" 
                       id="search404" 
                       placeholder="Buscar series, películas..." 
                       style="flex: 1; 
                              padding: 0.75rem 1rem; 
                              border: 1px solid rgba(255,255,255,0.2); 
                              border-radius: 6px; 
                              background: rgba(40,40,40,0.8); 
                              color: white; 
                              font-size: 1rem;">
                
                <button onclick="search404()" 
                        style="background: rgba(0, 212, 255, 0.8); 
                               color: white; 
                               border: none; 
                               padding: 0.75rem 1.5rem; 
                               border-radius: 6px; 
                               cursor: pointer; 
                               font-weight: 600;">
                    🔍
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function search404() {
    const searchTerm = document.getElementById('search404').value.trim();
    if (searchTerm) {
        window.location.href = `{{ route('browse') }}?search=${encodeURIComponent(searchTerm)}`;
    }
}

// Enter key search
document.getElementById('search404').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        search404();
    }
});

// Add hover effects
document.querySelectorAll('a[style*="background"]').forEach(link => {
    link.addEventListener('mouseenter', function() {
        if (this.style.background.includes('linear-gradient')) {
            this.style.transform = 'translateY(-2px)';
            this.style.boxShadow = '0 8px 25px rgba(0, 212, 255, 0.3)';
        } else {
            this.style.backgroundColor = 'rgba(255,255,255,0.2)';
        }
    });
    
    link.addEventListener('mouseleave', function() {
        if (this.style.background.includes('linear-gradient')) {
            this.style.transform = 'none';
            this.style.boxShadow = 'none';
        } else {
            this.style.backgroundColor = 'rgba(255,255,255,0.1)';
        }
    });
});
</script>
@endsection