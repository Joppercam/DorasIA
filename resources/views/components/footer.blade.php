<footer class="site-footer">
    <div class="footer-content">
        <div>
            <h4 style="color: #e50914; margin-bottom: 1rem; font-size: 1.2rem;">DORASIA Chile</h4>
            <p style="color: #ccc; line-height: 1.6; margin-bottom: 1rem;">
                La plataforma #1 de K-dramas para fanáticas chilenas. Descubre, explora y disfruta los mejores dramas coreanos.
            </p>
            <div class="footer-badges">
                <span class="footer-badge">
                    🇨🇱 Hecho en Chile
                </span>
                <span class="footer-badge">
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

    <div class="footer-bottom">
        <p class="footer-copyright">
            © 2024 Dorasia Chile - La mejor plataforma de K-dramas para fanáticas chilenas
        </p>
        <p class="footer-tagline">
            Hecho con 💜 para la comunidad K-drama en Chile <span class="footer-separator">|</span> Todos los derechos de las series pertenecen a sus respectivos creadores
        </p>
        <p class="footer-credits">
            <strong>Desarrollado por <a href="https://www.dendria.cl" target="_blank" rel="noopener" class="dendria-link">www.dendria.cl</a></strong>
            <span class="footer-separator">|</span> 
            <span class="dendria-tagline">Soluciones Digitales</span>
        </p>
    </div>
</footer>

<style>
/* Desktop Styles */
.site-footer {
    background: #0a0a0a;
    padding: 3rem 4% 2rem;
    margin-top: 4rem;
    border-top: 1px solid rgba(255,255,255,0.1);
}

.footer-content {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
    margin-bottom: 2rem;
}

.footer-badges {
    display: flex;
    gap: 1rem;
}

.footer-badge {
    background: rgba(255,255,255,0.1);
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.9rem;
    color: #ccc;
}

.footer-bottom {
    border-top: 1px solid rgba(255,255,255,0.1);
    padding-top: 2rem;
    text-align: center;
}

.footer-copyright {
    color: #666;
    font-size: 0.9rem;
    margin-bottom: 0.5rem;
}

.footer-tagline {
    color: #555;
    font-size: 0.8rem;
    margin-bottom: 0.5rem;
}

.footer-credits {
    color: #aaa;
    font-size: 1rem;
    margin-top: 1.5rem;
    font-weight: 500;
}

.dendria-link {
    color: #00d4ff;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s;
}

.dendria-link:hover {
    color: #7b68ee !important;
    text-shadow: 0 0 10px rgba(123, 104, 238, 0.5);
}

.dendria-tagline {
    background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-weight: 600;
}

footer a:hover {
    color: #e50914 !important;
}

/* Mobile Styles */
@media (max-width: 768px) {
    .site-footer {
        padding: 1.5rem 1rem !important;
        margin-top: 2rem !important;
    }
    
    .footer-content {
        grid-template-columns: 1fr !important;
        gap: 1.5rem !important;
        margin-bottom: 1rem !important;
    }
    
    .footer-content h4 {
        font-size: 1rem !important;
        margin-bottom: 0.5rem !important;
    }
    
    .footer-content p {
        font-size: 0.8rem !important;
        line-height: 1.4 !important;
        margin-bottom: 0.5rem !important;
    }
    
    .footer-badges {
        flex-direction: column !important;
        gap: 0.5rem !important;
    }
    
    .footer-badge {
        font-size: 0.75rem !important;
        padding: 0.3rem 0.6rem !important;
        text-align: center !important;
        width: 100% !important;
    }
    
    .footer-content ul {
        font-size: 0.8rem !important;
        line-height: 1.6 !important;
    }
    
    .footer-bottom {
        padding-top: 1rem !important;
    }
    
    .footer-copyright {
        font-size: 0.7rem !important;
        margin-bottom: 0.3rem !important;
    }
    
    .footer-tagline {
        font-size: 0.65rem !important;
        margin-bottom: 0.3rem !important;
        padding: 0 1rem !important;
        line-height: 1.3 !important;
    }
    
    .footer-credits {
        font-size: 1rem !important;
        margin-top: 1.5rem !important;
        display: block !important;
        text-align: center !important;
        line-height: 2 !important;
        padding: 0.8rem 1rem !important;
        background: linear-gradient(135deg, rgba(0, 212, 255, 0.1) 0%, rgba(123, 104, 238, 0.1) 100%) !important;
        border: 2px solid rgba(0, 212, 255, 0.3) !important;
        border-radius: 12px !important;
        color: #fff !important;
        font-weight: 600 !important;
    }
    
    .footer-separator {
        display: none !important;
    }
    
    /* Dendria link mobile styles */
    .dendria-link {
        font-weight: 800 !important;
        font-size: 1.1rem !important;
        color: #00ff88 !important;
        text-decoration: none !important;
        text-shadow: 0 0 10px rgba(0, 255, 136, 0.5) !important;
    }
    
    .dendria-tagline {
        display: block !important;
        margin-top: 0.5rem !important;
        font-size: 0.9rem !important;
        color: #00d4ff !important;
    }
    
    /* Hide less important sections on very small screens */
    @media (max-width: 480px) {
        .footer-content > div:nth-child(n+3) {
            display: none !important;
        }
    }
}
</style>