<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dorasia - K-Dramas')</title>
    {{-- @vite('resources/css/app.css') --}}
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #141414;
            color: white;
            font-family: 'Arial', sans-serif;
            overflow-x: hidden;
        }

        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            background: linear-gradient(180deg, rgba(20,20,20,0.8) 0%, transparent 100%);
            padding: 1rem 4%;
            transition: background-color 0.3s;
        }

        .navbar.scrolled {
            background-color: #141414;
        }

        .navbar-brand {
            font-size: 2rem;
            font-weight: bold;
            text-decoration: none;
            color: #ffffff;
            font-family: 'Arial Black', Arial, sans-serif;
            letter-spacing: 2px;
            position: relative;
            text-transform: uppercase;
            filter: drop-shadow(0 0 8px rgba(0, 212, 255, 0.3));
        }
        
        .navbar-brand .ai-highlight {
            background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 50%, #9d4edd 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 900;
            position: relative;
            text-shadow: 0 0 20px rgba(0, 212, 255, 0.6);
        }
        
        .navbar-brand .ai-highlight:after {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, #00d4ff, #7b68ee, #9d4edd);
            background-size: 300% 300%;
            opacity: 0;
            z-index: -1;
            border-radius: 4px;
            animation: aiHighlight 2s ease infinite;
        }
        
        .navbar-brand:before {
            content: '🤖';
            position: absolute;
            left: -35px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.5rem;
            opacity: 0.8;
            animation: aiPulse 2s ease-in-out infinite alternate;
        }
        
        .navbar-brand:after {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, #00d4ff, #7b68ee, #9d4edd, #ff006e, #00d4ff);
            background-size: 400% 400%;
            opacity: 0;
            z-index: -1;
            border-radius: 8px;
            transition: opacity 0.3s ease;
            animation: aiGradient 3s ease infinite;
        }
        
        .navbar-brand:hover:after {
            opacity: 0.2;
        }
        
        .navbar-brand:hover {
            animation: aiTextGlow 1.5s ease-in-out infinite alternate;
        }
        
        .navbar-brand:hover .ai-highlight:after {
            opacity: 0.3;
        }
        
        .navbar-brand:hover .ai-highlight {
            animation: aiLettersGlow 1s ease-in-out infinite alternate;
        }
        
        @keyframes aiPulse {
            from {
                transform: translateY(-50%) scale(1);
                filter: drop-shadow(0 0 5px rgba(0, 212, 255, 0.5));
            }
            to {
                transform: translateY(-50%) scale(1.1);
                filter: drop-shadow(0 0 15px rgba(123, 104, 238, 0.8));
            }
        }
        
        @keyframes aiGradient {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }
        
        @keyframes aiTextGlow {
            from {
                filter: drop-shadow(0 0 8px rgba(0, 212, 255, 0.3));
            }
            to {
                filter: drop-shadow(0 0 20px rgba(123, 104, 238, 0.8)) drop-shadow(0 0 30px rgba(157, 78, 221, 0.6));
            }
        }
        
        @keyframes aiHighlight {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }
        
        @keyframes aiLettersGlow {
            from {
                text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
                filter: drop-shadow(0 0 5px rgba(0, 212, 255, 0.3));
            }
            to {
                text-shadow: 0 0 25px rgba(123, 104, 238, 0.9), 0 0 35px rgba(157, 78, 221, 0.7);
                filter: drop-shadow(0 0 15px rgba(123, 104, 238, 0.8));
            }
        }

        .navbar-nav {
            display: flex;
            list-style: none;
            margin: 0;
            padding: 0;
            gap: 2rem;
            align-items: center;
        }

        .navbar-nav a {
            color: white;
            text-decoration: none;
            transition: color 0.3s;
        }

        .navbar-nav a:hover {
            color: #e50914;
        }

        .dropdown {
            position: relative;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background: rgba(20,20,20,0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 8px;
            padding: 0.5rem 0;
            min-width: 200px;
            z-index: 2000;
            list-style: none;
            margin: 0;
            margin-top: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.7);
        }

        .dropdown:hover .dropdown-menu {
            display: block;
        }

        .dropdown-menu li {
            padding: 0;
        }

        .dropdown-menu a {
            display: block;
            padding: 0.5rem 1rem;
            color: white;
            text-decoration: none;
            transition: background-color 0.3s;
        }

        .dropdown-menu a:hover {
            background-color: rgba(229, 9, 20, 0.1);
            color: #e50914;
        }

        .hero-section {
            height: 80vh;
            position: relative;
            display: flex;
            align-items: center;
            background-size: cover;
            background-position: center;
            margin-top: 0;
        }

        .hero-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0.3) 50%, rgba(0,0,0,0.8) 100%);
        }

        .hero-content {
            position: relative;
            z-index: 10;
            padding: 0 4%;
            max-width: 600px;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            line-height: 1.1;
        }

        .hero-description {
            font-size: 1.2rem;
            line-height: 1.4;
            margin-bottom: 2rem;
            color: #ccc;
        }

        .hero-buttons {
            display: flex;
            gap: 1rem;
        }

        .btn {
            padding: 1rem 2rem;
            border: none;
            border-radius: 6px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s;
        }

        .btn-primary {
            background-color: white;
            color: black;
        }

        .btn-primary:hover {
            background-color: rgba(255,255,255,0.8);
        }

        .btn-secondary {
            background-color: rgba(109,109,110,0.7);
            color: white;
        }

        .btn-secondary:hover {
            background-color: rgba(109,109,110,0.4);
        }

        .content-section {
            padding: 0 4%;
            margin-bottom: 1.5rem;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: white;
        }

        .carousel-container {
            position: relative;
            overflow: visible;
            margin: 0 50px;
            padding: 30px 0;
        }

        .carousel {
            display: flex;
            gap: 1rem;
            overflow: visible;
            scroll-behavior: smooth;
            transition: transform 0.5s ease;
            will-change: transform;
        }

        .carousel-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0,0,0,0.8);
            border: 2px solid rgba(255,255,255,0.2);
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            cursor: pointer;
            z-index: 1001;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: bold;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0,0,0,0.5);
        }

        .carousel-nav:hover {
            background: rgba(229, 9, 20, 0.9);
            border-color: rgba(255,255,255,0.4);
            transform: translateY(-50%) scale(1.2);
            box-shadow: 0 6px 20px rgba(0,0,0,0.7);
        }

        .carousel-nav.prev {
            left: -25px;
        }

        .carousel-nav.next {
            right: -25px;
        }

        .card {
            min-width: 160px;
            height: 240px;
            border-radius: 8px;
            overflow: hidden;
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background-size: cover;
            background-position: center;
            position: relative;
            border: 1px solid rgba(255,255,255,0.1);
            flex-shrink: 0;
        }

        .card:hover {
            transform: scale(1.6) translateY(-20px);
            box-shadow: 0 25px 60px rgba(0,0,0,0.9);
            z-index: 1500;
            position: relative;
            border-color: rgba(255,255,255,0.4);
            transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275), box-shadow 0.4s ease;
        }

        .card:hover .card-overlay {
            opacity: 1;
            transform: translateY(0);
        }

        .card:hover .card-info {
            opacity: 1;
            transform: translateY(0);
        }

        .card-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom, 
                transparent 0%, 
                transparent 50%, 
                rgba(0,0,0,0.7) 70%, 
                rgba(0,0,0,0.95) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .card-info {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0,0,0,0.95);
            padding: 0.8rem;
            color: white;
            opacity: 0;
            transform: translateY(100%);
            transition: all 0.3s ease;
        }

        .actor-images {
            display: flex;
            gap: 0.3rem;
            margin-bottom: 0.4rem;
            flex-wrap: wrap;
        }

        .actor-image {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background-size: cover;
            background-position: center;
            border: 1px solid rgba(255,255,255,0.3);
            flex-shrink: 0;
            background-color: #333;
        }

        .card-title {
            font-weight: 700;
            margin-bottom: 0.5rem;
            font-size: 0.75rem;
            line-height: 1.2;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.8);
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .card-meta {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            margin-bottom: 0.4rem;
            font-size: 0.65rem;
        }

        .card-rating {
            background: rgba(255, 215, 0, 0.2);
            padding: 2px 5px;
            border-radius: 4px;
            font-size: 0.65rem;
            color: #ffd700;
            font-weight: 600;
        }

        .card-year {
            color: #ccc;
            font-weight: 500;
        }

        .card-episodes {
            color: #aaa;
            font-size: 0.65rem;
        }

        .card-genre {
            display: inline-block;
            background: rgba(229, 9, 20, 0.7);
            padding: 2px 6px;
            border-radius: 8px;
            font-size: 0.6rem;
            font-weight: 500;
            margin-bottom: 0.3rem;
        }

        .card-synopsis {
            font-size: 0.6rem;
            color: #ccc;
            line-height: 1.3;
            margin-bottom: 0.4rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.8);
        }

        .card-actors {
            font-size: 0.7rem;
            color: #bbb;
            line-height: 1.2;
            display: -webkit-box;
            -webkit-line-clamp: 1;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.8);
        }

        .card-status {
            font-size: 0.65rem;
            color: #999;
            margin-top: 0.3rem;
        }

        .card-cast {
            font-size: 0.65rem;
            color: #ddd;
            margin-bottom: 0.4rem;
            line-height: 1.3;
        }

        .card-cast-title {
            font-weight: 600;
            color: #fff;
            margin-bottom: 0.2rem;
            font-size: 0.7rem;
        }

        .card-streaming {
            margin-top: 0.5rem;
            padding-top: 0.5rem;
            border-top: 1px solid rgba(255,255,255,0.2);
        }

        .card-streaming-title {
            font-size: 0.6rem;
            color: #ccc;
            margin-bottom: 0.2rem;
            font-weight: 500;
        }

        .streaming-platforms {
            display: flex;
            gap: 0.3rem;
            flex-wrap: wrap;
        }

        .streaming-platform {
            background: linear-gradient(135deg, #e50914 0%, #8b0000 100%);
            padding: 0.15rem 0.4rem;
            border-radius: 10px;
            font-size: 0.55rem;
            font-weight: 500;
            color: white;
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-description {
                font-size: 1rem;
            }
            
            .navbar {
                padding: 1rem 2%;
            }
            
            .navbar-nav {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: rgba(20,20,20,0.95);
                backdrop-filter: blur(10px);
                flex-direction: column;
                padding: 1rem;
                gap: 0.5rem;
                border-top: 1px solid rgba(255,255,255,0.1);
            }
            
            .navbar-nav.mobile-open {
                display: flex;
            }
            
            .mobile-menu-toggle {
                display: block;
                background: none;
                border: none;
                color: white;
                font-size: 1.5rem;
                cursor: pointer;
                padding: 0.5rem;
            }
            
            .content-section {
                padding: 0 2%;
            }
            
            .hero-content {
                padding: 0 2%;
            }
            
            .carousel-container {
                margin: 0 20px;
                padding: 30px 0;
            }
            
            .card:hover {
                transform: scale(1.3) translateY(-15px);
            }
        }
        
        .mobile-menu-toggle {
            display: none;
        }
    </style>
</head>
<body>
    <nav class="navbar" id="navbar">
        <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
            <a href="{{ route('home') }}" class="navbar-brand">DORAS<span class="ai-highlight">IA</span></a>
            <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">☰</button>
            <ul class="navbar-nav" id="navbar-nav">
                <li><a href="{{ route('home') }}">Inicio</a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle">Series</a>
                    <ul class="dropdown-menu">
                        <li><a href="#populares">Populares</a></li>
                        <li><a href="#romance">Romance</a></li>
                        <li><a href="#drama">Drama</a></li>
                        <li><a href="#comedia">Comedia</a></li>
                        <li><a href="#accion">Acción</a></li>
                        <li><a href="#misterio">Misterio</a></li>
                        <li><a href="#historicos">Históricos</a></li>
                        <li><a href="#recientes">Recientes</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>

    @yield('content')

    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const navbar = document.getElementById('navbar');
            if (window.scrollY > 100) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Mobile menu toggle
        function toggleMobileMenu() {
            const navbarNav = document.getElementById('navbar-nav');
            navbarNav.classList.toggle('mobile-open');
        }

        // Image fallback
        function handleImageError(img) {
            img.style.background = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
            img.style.display = 'flex';
            img.style.alignItems = 'center';
            img.style.justifyContent = 'center';
            img.innerHTML = '<span style="color: white; font-weight: bold;">📺</span>';
        }
    </script>
    {{-- @vite('resources/js/app.js') --}}
</body>
</html>