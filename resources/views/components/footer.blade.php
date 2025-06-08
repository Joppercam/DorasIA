<footer style="background: #0a0a0a; padding: 3rem 4% 2rem; margin-top: 4rem; border-top: 1px solid rgba(255,255,255,0.1);">
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin-bottom: 2rem;">
        <div>
            <h4 style="color: #e50914; margin-bottom: 1rem; font-size: 1.2rem;">DORASIA Chile</h4>
            <p style="color: #ccc; line-height: 1.6; margin-bottom: 1rem;">
                La plataforma #1 de K-dramas para fanáticas chilenas. Descubre, explora y disfruta los mejores dramas coreanos.
            </p>
            <div style="display: flex; gap: 1rem;">
                <span style="background: rgba(255,255,255,0.1); padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.9rem; color: #ccc;">
                    🇨🇱 Hecho en Chile
                </span>
                <span style="background: rgba(255,255,255,0.1); padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.9rem; color: #ccc;">
                    🇰🇷 K-drama Lover
                </span>
            </div>
        </div>

        <div>
            <h4 style="color: white; margin-bottom: 1rem;">Géneros Populares</h4>
            <ul style="list-style: none; padding: 0; color: #ccc; line-height: 2;">
                <li><a href="#romance" style="color: #ccc; text-decoration: none; transition: color 0.3s;">Romance</a></li>
                <li><a href="#drama" style="color: #ccc; text-decoration: none; transition: color 0.3s;">Drama</a></li>
                <li><a href="#comedia" style="color: #ccc; text-decoration: none; transition: color 0.3s;">Comedia Romántica</a></li>
                <li><a href="#historicos" style="color: #ccc; text-decoration: none; transition: color 0.3s;">Dramas Históricos</a></li>
                <li><a href="#misterio" style="color: #ccc; text-decoration: none; transition: color 0.3s;">Misterio & Suspenso</a></li>
            </ul>
        </div>

        <div>
            <h4 style="color: white; margin-bottom: 1rem;">Comunidad</h4>
            <ul style="list-style: none; padding: 0; color: #ccc; line-height: 2;">
                <li>📱 Síguenos en redes sociales</li>
                <li>💬 Únete a nuestra comunidad</li>
                <li>⭐ Comparte tus reseñas</li>
                <li>📧 Newsletter semanal</li>
            </ul>
        </div>

        <div>
            <h4 style="color: white; margin-bottom: 1rem;">Para Fanáticas</h4>
            <ul style="list-style: none; padding: 0; color: #ccc; line-height: 2;">
                <li>🎭 Guías de actores</li>
                <li>📺 Recomendaciones personalizadas</li>
                <li>🏆 Rankings actualizados</li>
                <li>📅 Calendario de estrenos</li>
                <li>💖 Lista de favoritos</li>
            </ul>
        </div>
    </div>

    <div style="border-top: 1px solid rgba(255,255,255,0.1); padding-top: 2rem; text-align: center;">
        <p style="color: #666; font-size: 0.9rem; margin-bottom: 0.5rem;">
            © 2024 Dorasia Chile - La mejor plataforma de K-dramas para fanáticas chilenas
        </p>
        <p style="color: #555; font-size: 0.8rem; margin-bottom: 0.5rem;">
            Hecho con 💜 para la comunidad K-drama en Chile | Todos los derechos de las series pertenecen a sus respectivos creadores
        </p>
        <p style="color: #555; font-size: 0.8rem; margin-top: 1rem;">
            Desarrollado por <a href="https://www.dendria.cl" target="_blank" rel="noopener" style="color: #00d4ff; text-decoration: none; font-weight: 600; transition: all 0.3s;">Dendria</a> 
            <span style="color: #444;">|</span> 
            <span style="background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-weight: 600;">Soluciones Digitales</span>
        </p>
    </div>
</footer>

<style>
footer a:hover {
    color: #e50914 !important;
}

footer a[href*="dendria"]:hover {
    color: #7b68ee !important;
    text-shadow: 0 0 10px rgba(123, 104, 238, 0.5);
}

@media (max-width: 768px) {
    footer {
        padding: 2rem 1rem 1.5rem !important;
        margin-top: 2rem !important;
    }
    
    footer > div:first-child {
        grid-template-columns: 1fr !important;
        gap: 2rem !important;
        margin-bottom: 1.5rem !important;
    }
    
    footer h4 {
        font-size: 1.1rem !important;
        margin-bottom: 0.8rem !important;
    }
    
    footer p {
        font-size: 0.85rem !important;
        line-height: 1.5 !important;
    }
    
    /* Badges de Chile y K-drama */
    footer > div:first-child > div:first-child > div {
        flex-wrap: wrap !important;
        gap: 0.5rem !important;
    }
    
    footer > div:first-child > div:first-child > div > span {
        font-size: 0.8rem !important;
        padding: 0.4rem 0.8rem !important;
        flex: 1 !important;
        text-align: center !important;
        min-width: 120px !important;
    }
    
    /* Listas de géneros y demás */
    footer ul {
        font-size: 0.85rem !important;
        line-height: 1.8 !important;
    }
    
    /* Sección de copyright */
    footer > div:last-child {
        padding-top: 1.5rem !important;
    }
    
    footer > div:last-child p {
        font-size: 0.75rem !important;
        margin-bottom: 0.3rem !important;
        padding: 0 0.5rem !important;
    }
    
    /* Crédito de Dendria en móvil */
    footer > div:last-child p:last-child {
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        gap: 0.3rem !important;
        margin-top: 0.8rem !important;
    }
    
    footer > div:last-child p:last-child > span:nth-child(2) {
        display: none !important; /* Ocultar el separador | */
    }
    
    /* Ocultar algunas secciones en móvil muy pequeño */
    @media (max-width: 480px) {
        footer > div:first-child > div:nth-child(3),
        footer > div:first-child > div:nth-child(4) {
            display: none !important;
        }
    }
}
</style>