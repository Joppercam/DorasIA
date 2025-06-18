<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dorasia - K-Dramas')</title>
    
    <!-- Favicons -->
    @include('components.favicon-inline')
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#00d4ff">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Dorasia">
    <link rel="apple-touch-icon" href="/icons/icon-192x192.png">
    
    <!-- Microsoft Tiles -->
    <meta name="msapplication-TileColor" content="#00d4ff">
    <meta name="msapplication-TileImage" content="/icons/icon-144x144.png">
    
    <!-- Meta tags para SEO y redes sociales -->
    <meta name="description" content="Descubre los mejores K-Dramas y películas coreanas con subtítulos en español. La plataforma definitiva para fans del entretenimiento coreano.">
    <meta property="og:title" content="@yield('title', 'Dorasia - K-Dramas y Películas Coreanas')">
    <meta property="og:description" content="Descubre los mejores K-Dramas y películas coreanas con subtítulos en español.">
    <meta property="og:image" content="/og-image.png">
    <meta property="og:type" content="website">
    <meta name="twitter:card" content="summary_large_image">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* CSS UPDATE - COLORS FIXED v2 - <?php echo time(); ?> */
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
            margin-top: 5px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.7);
            opacity: 0;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            pointer-events: none;
        }

        .dropdown:hover .dropdown-menu {
            display: block;
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
        }

        .dropdown-menu::before {
            content: '';
            position: absolute;
            top: -10px;
            left: 0;
            right: 0;
            height: 10px;
            background: transparent;
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
            height: 70vh;
            position: relative;
            display: flex;
            align-items: center;
            background-size: cover;
            background-position: center;
            margin-top: 0;
            margin-bottom: 0.5rem;
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

        .hero-info-box {
            background: linear-gradient(135deg, 
                rgba(0, 0, 0, 0.15) 0%, 
                rgba(0, 0, 0, 0.35) 50%, 
                rgba(0, 0, 0, 0.5) 100%);
            backdrop-filter: blur(15px);
            padding: 2rem;
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        }

        .hero-categories {
            display: flex;
            gap: 0.8rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }

        .hero-category {
            background: linear-gradient(135deg, rgba(0, 212, 255, 0.15) 0%, rgba(123, 104, 238, 0.15) 100%);
            border: 1px solid rgba(0, 212, 255, 0.3);
            color: rgba(255, 255, 255, 0.9);
            padding: 0.4rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            backdrop-filter: blur(10px);
        }

        .hero-meta {
            display: flex;
            align-items: center;
            gap: 2rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }

        .hero-rating {
            display: flex;
            align-items: center;
            gap: 0.4rem;
            background: rgba(255, 215, 0, 0.15);
            border: 1px solid rgba(255, 215, 0, 0.3);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            backdrop-filter: blur(10px);
        }

        .rating-stars {
            font-size: 1rem;
        }

        .rating-number {
            font-weight: 700;
            color: #ffd700;
            font-size: 1rem;
        }

        .hero-year, .hero-episodes, .hero-seasons {
            color: #fff;
            font-weight: 500;
            font-size: 0.9rem;
            background: rgba(0, 0, 0, 0.5);
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.8);
        }

        .hero-original-title {
            font-size: 1.1rem;
            color: #fff;
            margin-bottom: 0.8rem;
            font-style: italic;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.8);
            background: rgba(0, 0, 0, 0.4);
            padding: 0.5rem 1rem;
            border-radius: 8px;
            display: inline-block;
        }

        .btn-hero {
            background: linear-gradient(135deg, rgba(0, 212, 255, 0.8) 0%, rgba(123, 104, 238, 0.8) 100%);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: 1rem 2.5rem;
            border-radius: 25px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 20px rgba(0, 212, 255, 0.2);
        }

        .btn-hero:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0, 212, 255, 0.4);
            background: linear-gradient(135deg, rgba(0, 212, 255, 1) 0%, rgba(123, 104, 238, 1) 100%);
        }

        /* Hero Actions */
        .hero-actions {
            display: flex;
            align-items: center;
            gap: 2rem;
            margin-top: 1.5rem;
            padding: 1rem 0;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            background: transparent !important;
        }

        .hero-actions .card-rating-buttons {
            position: static !important;
            transform: none !important;
            opacity: 1 !important;
            display: flex !important;
            gap: 2rem !important;
            justify-content: space-around !important;
            width: 100% !important;
            background: transparent !important;
            border: none !important;
            padding: 0 !important;
            flex-direction: row !important;
        }
        
        .card-rating-buttons {
            display: flex;
            gap: 2rem;
            justify-content: space-around;
            width: 100%;
            background: transparent !important;
            border: none !important;
            padding: 0 !important;
        }
        
        .hero-actions .rating-button-with-count {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.3rem;
        }

        .hero-actions .rating-btn {
            opacity: 1 !important;
            transform: scale(1) !important;
            width: 50px;
            height: 50px;
            border-width: 2px;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.2);
            color: rgba(255, 255, 255, 0.7);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        .hero-actions .rating-count {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.2rem;
            text-align: center;
        }
        
        .hero-actions .count-number {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
            font-weight: 600;
        }
        
        .hero-actions .count-label {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .hero-actions .rating-btn.dislike:hover,
        .hero-actions .rating-btn.dislike.active {
            background: rgba(244, 67, 54, 0.3);
            border-color: #f44336;
            color: #f44336;
        }
        
        .hero-actions .rating-btn.like:hover,
        .hero-actions .rating-btn.like.active {
            background: rgba(76, 175, 80, 0.3);
            border-color: #4caf50;
            color: #4caf50;
        }
        
        .hero-actions .rating-btn.love:hover,
        .hero-actions .rating-btn.love.active {
            background: rgba(233, 30, 99, 0.3);
            border-color: #e91e63;
            color: #e91e63;
        }

        .hero-actions .watchlist-button-container {
            position: static;
        }

        .hero-actions .watchlist-btn {
            opacity: 1 !important;
            transform: scale(1) !important;
            width: 40px;
            height: 40px;
            border-width: 2px;
        }
        
        /* Mobile styles for hero-actions */
        @media (max-width: 768px) {
            .hero-actions {
                gap: 1rem;
                justify-content: center;
                flex-wrap: wrap;
                padding: 1rem;
                margin-top: 1rem;
            }
            
            .hero-actions .card-rating-buttons {
                gap: 1rem;
                justify-content: space-around;
            }
            
            .hero-actions .rating-btn {
                width: 45px !important;
                height: 45px !important;
            }
            
            .hero-actions .count-label {
                font-size: 0.7rem;
            }
        }


        /* Series Detail Styles */
        .series-detail-container {
            display: grid;
            grid-template-columns: 320px 1fr;
            gap: 3rem;
            align-items: start;
        }

        .series-poster {
            position: sticky;
            top: 120px;
        }

        .detail-poster-img {
            width: 100%;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.6);
            transition: transform 0.3s ease;
        }

        .detail-poster-img:hover {
            transform: scale(1.02);
        }

        .detail-poster-placeholder {
            width: 100%;
            height: 480px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
        }

        .detail-section {
            background: rgba(20, 20, 20, 0.4);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .detail-section-title {
            color: white;
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .detail-genres {
            display: flex;
            gap: 0.8rem;
            flex-wrap: wrap;
        }

        .detail-genre-tag {
            background: linear-gradient(135deg, rgba(0, 212, 255, 0.2) 0%, rgba(123, 104, 238, 0.2) 100%);
            border: 1px solid rgba(0, 212, 255, 0.3);
            color: rgba(255, 255, 255, 0.9);
            padding: 0.6rem 1.2rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
            backdrop-filter: blur(10px);
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .detail-label {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.9rem;
            font-weight: 500;
        }

        .detail-value {
            color: white;
            font-size: 1rem;
            font-weight: 600;
        }

        /* Cast Grid Styles */
        .cast-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
        }

        .cast-card:not(.actor-card-simple) {
            background: rgba(20, 20, 20, 0.4);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.05);
            transition: transform 0.3s ease;
            display: flex;
            height: 200px;
        }

        .cast-card:hover {
            transform: translateY(-5px);
            border-color: rgba(0, 212, 255, 0.2);
        }

        .cast-image {
            width: 50%;
            position: relative;
            overflow: hidden;
        }

        .cast-photo {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .cast-placeholder {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
        }

        .cast-info {
            width: 50%;
            padding: 1rem;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .cast-name {
            color: white;
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0.3rem;
            line-height: 1.2;
        }

        .cast-character {
            color: rgba(0, 212, 255, 0.9);
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
            line-height: 1.2;
        }

        .cast-bio {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.8rem;
            line-height: 1.3;
            margin-bottom: 0.8rem;
            flex: 1;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
        }

        .cast-details {
            display: flex;
            flex-direction: column;
            gap: 0.3rem;
        }

        .cast-birth, .cast-location {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.75rem;
            line-height: 1.2;
        }

        .actor-detail-btn {
            position: absolute;
            top: 1rem;
            right: 1rem;
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, rgba(0, 212, 255, 0.9) 0%, rgba(123, 104, 238, 0.9) 100%);
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            text-decoration: none;
            font-size: 1.2rem;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            z-index: 10;
        }

        .actor-detail-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 8px 25px rgba(0, 212, 255, 0.5);
            border-color: rgba(255, 255, 255, 0.4);
            color: white;
            text-decoration: none;
        }

        .cast-card {
            position: relative;
        }

        /* Comments Styles */
        .comment-form-container {
            background: rgba(20, 20, 20, 0.4);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .comment-textarea {
            width: 100%;
            min-height: 120px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 1rem;
            color: white;
            font-family: inherit;
            font-size: 0.95rem;
            resize: vertical;
            transition: border-color 0.3s ease;
        }

        .comment-textarea:focus {
            outline: none;
            border-color: rgba(0, 212, 255, 0.5);
        }

        .comment-textarea::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .comment-form-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1rem;
        }

        .spoiler-checkbox {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: rgba(255, 255, 255, 0.8);
            cursor: pointer;
            font-size: 0.9rem;
        }

        .comment-submit-btn {
            background: linear-gradient(135deg, rgba(0, 212, 255, 0.8) 0%, rgba(123, 104, 238, 0.8) 100%);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            padding: 0.8rem 1.5rem;
            border-radius: 20px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .comment-submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(0, 212, 255, 0.4);
        }

        .auth-prompt {
            text-align: center;
            padding: 2rem;
            background: rgba(20, 20, 20, 0.3);
            border-radius: 12px;
            margin-bottom: 2rem;
        }

        .auth-prompt a {
            color: #00d4ff;
            text-decoration: none;
        }

        .comment {
            background: rgba(20, 20, 20, 0.4);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .comment.reply {
            margin-left: 2rem;
            margin-top: 1rem;
            background: rgba(20, 20, 20, 0.2);
        }

        .comment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .comment-user {
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .comment-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
        }

        .comment-avatar-placeholder {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        .comment-username {
            color: white;
            font-weight: 600;
            font-size: 0.95rem;
        }

        .comment-date {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.8rem;
        }

        .spoiler-badge {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
            padding: 0.3rem 0.8rem;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .comment-content {
            color: rgba(255, 255, 255, 0.9);
            line-height: 1.5;
            position: relative;
        }

        .spoiler-hidden {
            filter: blur(5px);
            pointer-events: none;
        }

        .spoiler-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .reveal-spoiler-btn {
            background: rgba(255, 193, 7, 0.8);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            cursor: pointer;
        }

        .no-comments {
            text-align: center;
            padding: 3rem;
            color: rgba(255, 255, 255, 0.6);
        }

        .hero-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.8rem;
            line-height: 1.1;
            text-shadow: 2px 2px 6px rgba(0, 0, 0, 0.9);
            color: #fff;
        }

        .hero-description {
            font-size: 1rem;
            line-height: 1.4;
            margin-bottom: 1.5rem;
            color: #fff;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(8px);
            padding: 0.8rem 1.2rem;
            border-radius: 12px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.8);
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
            margin-bottom: 2rem;
            overflow: visible;
            position: relative;
            z-index: 1;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
            color: white;
        }

        .carousel-container {
            position: relative;
            overflow: visible;
            margin: 0 50px;
            padding: 40px 0;
        }
        
        @media (max-width: 768px) {
            .carousel-container {
                margin: 0 15px;
                padding: 40px 0;
                overflow: hidden;
            }
        }

        .carousel {
            display: flex;
            gap: 0.4rem;
            overflow: visible;
            scroll-behavior: smooth;
            transition: transform 0.5s ease;
            will-change: transform;
            padding: 20px 0;
        }

        @media (max-width: 768px) {
            .carousel {
                gap: 1rem !important;
                padding: 40px 0 !important;
                justify-content: flex-start !important;
            }
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
        
        @media (max-width: 768px) {
            .carousel-nav {
                width: 40px;
                height: 40px;
                font-size: 18px;
            }
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
            min-width: 350px !important;
            height: 200px !important;
            border-radius: 8px;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
            background-size: cover;
            background-position: center;
            position: relative;
            border: 2px solid rgba(255,255,255,0.1);
            flex-shrink: 0;
            transform-origin: center;
        }
        
        @media (max-width: 768px) {
            .card {
                min-width: 85vw !important;
                max-width: 320px !important;
                height: 380px !important;
                border-radius: 15px !important;
                border: 3px solid rgba(255,255,255,0.2) !important;
                margin: 0 auto !important;
            }
            
            .card.hovering {
                transform: scale(1.05) translateY(-8px) !important;
                box-shadow: 0 30px 60px rgba(0,0,0,0.95) !important;
                border-color: rgba(255,255,255,0.5) !important;
                z-index: 100 !important;
            }
        }
        
        .card.hovering {
            transform: scale(1.2) translateY(-20px);
            box-shadow: 0 25px 50px rgba(0,0,0,0.9);
            z-index: 100;
            border-color: rgba(255, 255, 255, 0.3);
        }
        

        .card.hovering .card-overlay {
            opacity: 1;
            transform: translateY(0);
        }

        .card.hovering .card-info {
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
                transparent 60%, 
                rgba(0,0,0,0.4) 70%, 
                rgba(0,0,0,0.8) 85%, 
                rgba(0,0,0,0.95) 100%);
            opacity: 1;
            transition: opacity 0.3s ease;
        }

        .card-categories {
            display: none;
        }

        .card.hovering .card-categories {
            opacity: 1;
            transform: translateY(0);
        }

        .card-category {
            display: inline-block;
            background: rgba(0, 212, 255, 0.9);
            color: white;
            padding: 0.3rem 0.7rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-right: 0.4rem;
            margin-bottom: 0.4rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
        }

        .card-category.romance {
            background: rgba(255, 105, 180, 0.9);
        }

        .card-category.drama {
            background: rgba(123, 104, 238, 0.9);
        }

        .card-category.comedy {
            background: rgba(255, 193, 7, 0.9);
            color: black;
        }

        .card-category.action {
            background: rgba(220, 53, 69, 0.9);
        }

        .card-category.mystery {
            background: rgba(108, 117, 125, 0.9);
        }

        .card-category.historical {
            background: rgba(40, 167, 69, 0.9);
        }

        .card-type-badge {
            background: linear-gradient(135deg, #4caf50, #66bb6a);
            color: white;
            padding: 0.35rem 0.7rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 700;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.3);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 8px rgba(76, 175, 80, 0.4);
        }

        @media (max-width: 768px) {
            .card-type-badge {
                font-size: 0.6rem !important;
                padding: 0.2rem 0.4rem !important;
                border-radius: 8px !important;
                font-weight: 700 !important;
            }
        }

        .card-type-badge.movie {
            background: linear-gradient(135deg, #ff6b9d, #ff8e8e);
            box-shadow: 0 2px 8px rgba(255, 107, 157, 0.4);
        }

        .card-info {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to bottom, transparent 0%, rgba(0,0,0,0.7) 40%, rgba(0,0,0,0.95) 100%);
            padding: 1.2rem 1.2rem 1rem;
            color: white;
            opacity: 1;
            transform: translateY(0);
            transition: all 0.3s ease;
        }

        @media (max-width: 768px) {
            .card-info {
                padding: 1.5rem 1.2rem 1.2rem !important;
                background: linear-gradient(to bottom, transparent 0%, rgba(0,0,0,0.75) 30%, rgba(0,0,0,0.95) 100%) !important;
            }
        }
        
        /* Netflix-style expanded info on hover */
        .card-hover-content {
            position: absolute;
            top: 100%;
            left: -20px;
            right: -20px;
            background: rgb(20, 20, 20);
            padding: 1rem;
            border-radius: 0 0 8px 8px;
            opacity: 0;
            transform: translateY(-10px);
            transition: all 0.3s ease;
            z-index: 101;
            box-shadow: 0 10px 30px rgba(0,0,0,0.9);
            display: none;
        }
        
        .card.hovering .card-hover-content {
            display: block;
            opacity: 1;
            transform: translateY(0);
        }
        
        /* Franja de información al hacer click */
        .card-info-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.95) !important;
            color: white;
            padding: 8px;
            transform: translateY(0);
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 100 !important;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 40px;
            font-size: 12px;
            border-top: 2px solid #e50914;
        }
        
        .card-info-overlay.show {
            opacity: 1;
            visibility: visible;
        }
        
        .overlay-left {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .overlay-simple-info {
            margin-bottom: 0.5rem;
        }
        
        .overlay-simple-text {
            font-size: 0.85rem;
            color: #ccc;
            font-style: italic;
        }
        
        .card-info-overlay .info-title {
            font-size: 1rem;
            font-weight: 600;
            color: #00d4ff;
            margin: 0;
        }
        
        .overlay-rating-info {
            display: flex;
            gap: 0.8rem;
            font-size: 0.9rem;
            font-weight: 600;
        }
        
        .rating-count {
            display: flex;
            align-items: center;
            gap: 0.2rem;
            padding: 0.2rem 0.4rem;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(5px);
            transition: all 0.3s ease;
            font-size: 0.8rem;
            min-width: 35px;
            justify-content: center;
        }
        
        .rating-count.dislike {
            color: #dc3545;
            background: rgba(220, 53, 69, 0.2);
            border: 1px solid rgba(220, 53, 69, 0.3);
        }
        
        .rating-count.like {
            color: #28a745;
            background: rgba(40, 167, 69, 0.2);
            border: 1px solid rgba(40, 167, 69, 0.3);
        }
        
        .rating-count.love {
            color: #ff69b4;
            background: rgba(255, 105, 180, 0.2);
            border: 1px solid rgba(255, 105, 180, 0.3);
        }
        
        .overlay-right {
            display: flex;
            gap: 0.8rem;
            align-items: center;
            flex-shrink: 0;
        }
        
        .circular-rating {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            border: none;
            font-size: 1.3rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .circular-rating.neutral {
            background: rgba(255, 255, 255, 0.1);
            color: #ffd700;
        }
        
        .circular-rating.dislike {
            background: rgba(220, 53, 69, 0.2);
            color: #dc3545;
        }
        
        .circular-rating.like {
            background: rgba(40, 167, 69, 0.2);
            color: #28a745;
        }
        
        .circular-rating.love {
            background: rgba(255, 105, 180, 0.2);
            color: #ff69b4;
        }
        
        .circular-rating:hover {
            transform: scale(1.1);
            box-shadow: 0 4px 15px rgba(255, 255, 255, 0.2);
        }
        
        .circular-detail-btn {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%);
            color: white;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.1rem;
            font-weight: bold;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 212, 255, 0.3);
        }
        
        .circular-detail-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 20px rgba(0, 212, 255, 0.4);
            color: white;
            text-decoration: none;
        }
        
        .card-info-overlay .close-info {
            position: absolute;
            top: 0.5rem;
            right: 0.5rem;
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            transition: background-color 0.2s;
        }
        
        .card-info-overlay .close-info:hover {
            background: rgba(255, 255, 255, 0.3);
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
            margin-bottom: 0.4rem;
            font-size: 1.1rem;
            line-height: 1.2;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.8);
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        @media (max-width: 768px) {
            .card-title {
                font-size: 1.3rem !important;
                line-height: 1.3 !important;
                margin-bottom: 0.5rem !important;
                font-weight: 800 !important;
                text-shadow: 2px 2px 4px rgba(0,0,0,0.9) !important;
            }
        }

        .card-meta {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.4rem;
            font-size: 0.85rem;
        }

        @media (max-width: 768px) {
            .card-meta {
                font-size: 1rem !important;
                gap: 0.6rem !important;
                margin-bottom: 0.4rem !important;
            }
        }

        .card-rating {
            background: rgba(255, 215, 0, 0.2);
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 0.8rem;
            color: #ffd700;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.2rem;
        }

        @media (max-width: 768px) {
            .card-rating {
                font-size: 0.85rem !important;
                padding: 4px 8px !important;
                border-radius: 6px !important;
            }
        }

        .card-year {
            color: #ccc;
            font-weight: 500;
        }

        .card-episodes {
            color: #aaa;
            font-size: 0.8rem;
        }

        @media (max-width: 768px) {
            .card-episodes {
                font-size: 0.85rem !important;
                color: #ccc !important;
                font-weight: 500 !important;
            }
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
            display: none;
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
            .streaming-platform {
                font-size: 0.65rem !important;
                padding: 0.2rem 0.5rem !important;
                border-radius: 10px !important;
                font-weight: 500 !important;
            }
        }

        /* Footer Styles - OLD (commented out - using component instead) */
        /*
        .footer {
            background-color: #0a0a0a;
            padding: 3rem 0 1rem;
            margin-top: 5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 4%;
        }
        
        .footer-section h3,
        .footer-section h4 {
            margin-bottom: 1rem;
            color: #fff;
        }
        
        .footer-logo {
            font-size: 1.5rem;
            font-weight: bold;
        }
        
        .footer-logo .ai-highlight {
            background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 50%, #9d4edd 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .footer-section ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .footer-section li {
            margin-bottom: 0.5rem;
        }
        
        .footer-section a {
            color: #999;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .footer-section a:hover {
            color: #fff;
        }
        
        .footer-bottom {
            text-align: center;
            padding: 2rem 4%;
            margin-top: 2rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            color: #666;
            font-size: 0.9rem;
        }
        */
        
        /* Action Button on Cards */
        .card-action-btn {
            position: absolute;
            bottom: 2.5rem;
            right: 0.8rem;
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, rgba(0, 212, 255, 0.8) 0%, rgba(123, 104, 238, 0.8) 50%, rgba(157, 78, 221, 0.8) 100%);
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            opacity: 0;
            transform: scale(0.8);
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            z-index: 10;
            box-shadow: 0 4px 15px rgba(0, 212, 255, 0.3);
        }
        
        .card.hovering .card-action-btn {
            opacity: 1;
            transform: scale(1);
        }
        
        .card-action-btn:hover {
            background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 50%, #9d4edd 100%);
            transform: scale(1.1);
            border-color: rgba(255, 255, 255, 0.5);
            box-shadow: 0 6px 20px rgba(123, 104, 238, 0.4);
        }
        
        .card-action-btn svg {
            width: 16px;
            height: 16px;
            fill: white;
        }

        .card-view-btn {
            background: linear-gradient(135deg, #e50914, #b20710);
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 15px;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.3s ease;
            border: 1px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(5px);
            box-shadow: 0 4px 15px rgba(229, 9, 20, 0.3);
            opacity: 1;
        }

        @media (max-width: 768px) {
            .card-view-btn {
                font-size: 1rem !important;
                padding: 0.6rem 1.2rem !important;
                border-radius: 15px !important;
                font-weight: 700 !important;
            }
        }
        
        @media (max-width: 768px) {
            .card-view-btn {
                opacity: 1 !important;
                transform: translateY(0) !important;
            }
        }

        .card.hovering .card-view-btn {
            opacity: 1;
            transform: translateY(0);
        }

        .card-view-btn:hover {
            background: linear-gradient(135deg, #ff0a16, #e50914);
            transform: scale(1.05);
            box-shadow: 0 6px 20px rgba(229, 9, 20, 0.5);
            color: white;
            text-decoration: none;
        }
        
        .card-list-btn {
            position: absolute;
            top: 0.8rem;
            left: 0.8rem;
            background: rgba(40, 167, 69, 0.9);
            color: white;
            border: 1px solid rgba(255,255,255,0.3);
            padding: 0.4rem;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            backdrop-filter: blur(5px);
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transform: translateY(-10px);
            transition: all 0.3s ease;
        }
        
        .card.hovering .card-list-btn {
            opacity: 1;
            transform: translateY(0);
        }
        
        .card-list-btn:hover {
            transform: translateY(-2px) scale(1.1);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.5);
        }
        
        .card-list-btn.active {
            background: rgba(220, 53, 69, 0.9);
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.4);
        }
        
        .card-list-btn.active:hover {
            box-shadow: 0 6px 20px rgba(220, 53, 69, 0.5);
        }
        
        /* Remove card click */
        .card {
            cursor: default;
        }
        
        /* Movie Rating Buttons */
        .movie-actions {
            margin-top: 2rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }
        
        .movie-rating-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }
        
        .rating-button-with-count {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.3rem;
        }
        
        .card-rating-buttons .rating-button-with-count {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.3rem;
        }
        
        .movie-rating-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.2);
            color: rgba(255, 255, 255, 0.7);
            width: 50px;
            height: 50px;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        .rating-count {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.2rem;
            text-align: center;
        }
        
        .count-number {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
            font-weight: 600;
        }
        
        .count-label {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.75rem;
            font-weight: 500;
        }
        
        .movie-rating-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }
        
        .movie-rating-btn svg {
            width: 18px;
            height: 18px;
        }
        
        .movie-rating-btn.dislike {
            border-color: rgba(220, 53, 69, 0.3);
        }
        
        .movie-rating-btn.dislike:hover,
        .movie-rating-btn.dislike.active {
            background: rgba(220, 53, 69, 0.2);
            border-color: #dc3545;
            color: #dc3545;
        }
        
        .movie-rating-btn.like {
            border-color: rgba(40, 167, 69, 0.3);
        }
        
        .movie-rating-btn.like:hover,
        .movie-rating-btn.like.active {
            background: rgba(40, 167, 69, 0.2);
            border-color: #28a745;
            color: #28a745;
        }
        
        .movie-rating-btn.love {
            border-color: rgba(255, 105, 180, 0.3);
        }
        
        .movie-rating-btn.love:hover,
        .movie-rating-btn.love.active {
            background: rgba(255, 105, 180, 0.2);
            border-color: #ff69b4;
            color: #ff69b4;
        }
        
        .movie-watchlist-btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(123, 104, 238, 0.2);
            border: 2px solid rgba(123, 104, 238, 0.3);
            color: #7b68ee;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.9rem;
            font-weight: 500;
            backdrop-filter: blur(10px);
        }
        
        .movie-watchlist-btn:hover {
            background: rgba(123, 104, 238, 0.3);
            border-color: #7b68ee;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(123, 104, 238, 0.3);
        }
        
        .movie-watchlist-btn.active {
            background: rgba(123, 104, 238, 0.4);
            border-color: #7b68ee;
        }
        
        /* Streaming Platforms Update */
        .streaming-platform {
            padding: 0.2rem 0.5rem;
            font-size: 0.65rem;
        }
        
        .streaming-platform.netflix {
            background: linear-gradient(135deg, #e50914 0%, #8b0000 100%);
        }
        
        .streaming-platform.disney {
            background: linear-gradient(135deg, #113ccf 0%, #0e2a8a 100%);
        }
        
        .streaming-platform.prime {
            background: linear-gradient(135deg, #00a8e1 0%, #006ca5 100%);
        }
        
        .streaming-platform.viki {
            background: linear-gradient(135deg, #ff6b6b 0%, #c92a2a 100%);
        }
        
        /* Rating Toast Notifications */
        .rating-toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background: rgba(0, 0, 0, 0.9);
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            border-left: 4px solid #28a745;
            z-index: 10000;
            transform: translateX(300px);
            opacity: 0;
            transition: all 0.3s ease;
            max-width: 300px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
        }
        
        .rating-toast.show {
            transform: translateX(0);
            opacity: 1;
        }
        
        @media (max-width: 768px) {
            .rating-toast {
                right: 10px;
                left: 10px;
                max-width: calc(100% - 20px);
                transform: translateY(-50px);
            }
            
            .rating-toast.show {
                transform: translateY(0);
            }
            
            /* Mobile rating buttons */
            .rating-btn {
                min-height: 44px !important;
                min-width: 44px !important;
                padding: 8px 12px !important;
                font-size: 1rem !important;
                touch-action: manipulation;
            }
            
            .movie-rating-btn {
                min-height: 44px !important;
                padding: 8px 16px !important;
                font-size: 0.9rem !important;
                touch-action: manipulation;
            }
            
            .movie-rating-btn svg {
                width: 20px !important;
                height: 20px !important;
            }
            
            /* Mobile actions bar */
            .mobile-actions-bar {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                background: rgba(0, 0, 0, 0.95);
                backdrop-filter: blur(10px);
                padding: 1rem;
                z-index: 1000;
                border-top: 1px solid rgba(255, 255, 255, 0.1);
                display: flex;
                gap: 1rem;
                justify-content: center;
                align-items: center;
                flex-wrap: nowrap;
            }
            
            .mobile-actions-bar .rating-btn {
                flex: 1;
                max-width: 80px;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 0.2rem;
                font-size: 0.7rem;
                padding: 8px 4px;
            }
            
            .mobile-actions-bar .rating-btn svg {
                width: 24px;
                height: 24px;
            }
            
            /* Mobile movie buttons */
            .mobile-actions-bar .movie-rating-btn {
                flex: 1;
                max-width: 80px;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                gap: 0.2rem;
                font-size: 0.7rem;
                padding: 8px 4px;
                border-radius: 8px;
            }
            
            .mobile-actions-bar .movie-rating-btn span {
                display: block;
                text-align: center;
                line-height: 1;
            }
            
            .mobile-actions-bar .movie-rating-btn svg {
                width: 24px;
                height: 24px;
            }
            
            .mobile-watchlist-btn {
                background: rgba(123, 104, 238, 0.2);
                border: 2px solid rgba(123, 104, 238, 0.3);
                color: #7b68ee;
                width: 50px;
                height: 50px;
                border-radius: 50%;
                cursor: pointer;
                transition: all 0.3s ease;
                backdrop-filter: blur(10px);
                display: flex;
                align-items: center;
                justify-content: center;
                touch-action: manipulation;
                flex-shrink: 0;
            }
            
            .mobile-watchlist-btn.active {
                background: rgba(123, 104, 238, 0.4);
                border-color: #7b68ee;
            }
            
            .mobile-auth-prompt {
                flex: 1;
                display: flex;
                justify-content: center;
                align-items: center;
            }
            
            .mobile-login-btn {
                background: rgba(229, 9, 20, 0.8);
                color: white;
                text-decoration: none;
                padding: 12px 24px;
                border-radius: 25px;
                font-size: 0.9rem;
                font-weight: 600;
                transition: all 0.3s ease;
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.2);
                min-height: 44px;
                display: flex;
                align-items: center;
                touch-action: manipulation;
            }
            
            /* Mobile rating buttons - icon only */
            .mobile-rating-btn {
                background: rgba(255, 255, 255, 0.1);
                border: 2px solid rgba(255, 255, 255, 0.2);
                color: rgba(255, 255, 255, 0.7);
                width: 50px;
                height: 50px;
                border-radius: 50%;
                cursor: pointer;
                transition: all 0.3s ease;
                backdrop-filter: blur(10px);
                display: flex;
                align-items: center;
                justify-content: center;
                touch-action: manipulation;
                flex-shrink: 0;
            }
            
            .mobile-rating-btn.dislike.active {
                background: rgba(244, 67, 54, 0.3);
                border-color: #f44336;
                color: #f44336;
            }
            
            .mobile-rating-btn.like.active {
                background: rgba(76, 175, 80, 0.3);
                border-color: #4caf50;
                color: #4caf50;
            }
            
            .mobile-rating-btn.love.active {
                background: rgba(233, 30, 99, 0.3);
                border-color: #e91e63;
                color: #e91e63;
            }
            
            .mobile-rating-btn:active {
                transform: scale(0.95);
            }
        }
        
        /* Current rating info for mobile */
        .current-rating-info {
            background: rgba(0, 212, 255, 0.1);
            border: 1px solid rgba(0, 212, 255, 0.3);
            border-radius: 20px;
            padding: 0.5rem 1rem;
            margin: 1rem 0;
            text-align: center;
            display: inline-block;
            max-width: fit-content;
            margin: 1rem auto;
        }
        
        .rating-label {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.85rem;
            margin-right: 0.5rem;
        }
        
        .rating-display {
            color: #00d4ff;
            font-weight: 600;
            font-size: 0.9rem;
        }

        /* News Carousel Styles */
        .news-carousel {
            background: linear-gradient(135deg, rgba(20,20,20,0.95) 0%, rgba(40,40,40,0.95) 100%);
            border-radius: 12px;
            padding: 1.5rem;
            margin: 2rem 0;
            border: 1px solid rgba(0, 212, 255, 0.2);
            backdrop-filter: blur(10px);
        }
        
        .news-carousel .carousel-container {
            position: relative;
            overflow: hidden;
            margin: 0 50px;
            padding: 30px 0;
        }
        
        .news-carousel .carousel {
            display: flex;
            gap: 1rem;
            overflow: visible;
            scroll-behavior: smooth;
            transition: transform 0.5s ease;
            will-change: transform;
        }

        .news-card {
            width: 300px;
            min-width: 300px;
            max-width: 300px;
            height: 200px;
            border-radius: 10px;
            overflow: hidden;
            position: relative;
            cursor: pointer;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            background-size: cover;
            background-position: center;
            flex-shrink: 0;
            border: 1px solid rgba(255,255,255,0.1);
        }

        .news-card:hover {
            transform: scale(1.05) translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 212, 255, 0.3);
            border-color: rgba(0, 212, 255, 0.5);
        }

        .news-card-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom, 
                rgba(0,0,0,0.3) 0%, 
                rgba(0,0,0,0.7) 70%, 
                rgba(0,0,0,0.95) 100%);
        }

        .news-card-content {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 1rem;
            color: white;
        }

        .news-card-category {
            display: inline-block;
            background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%);
            color: white;
            padding: 0.2rem 0.5rem;
            border-radius: 8px;
            font-size: 0.65rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }

        .news-card-title {
            font-size: 0.9rem;
            font-weight: 700;
            line-height: 1.3;
            margin-bottom: 0.3rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.8);
        }

        .news-card-excerpt {
            font-size: 0.7rem;
            color: #ccc;
            line-height: 1.3;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.8);
        }

        .news-card-meta {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 0.3rem;
            font-size: 0.6rem;
            color: #999;
        }

        .news-section-title {
            color: white;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .news-section-title::before {
            content: '📰';
            font-size: 1.2rem;
        }

        /* Auth Navbar Styles */
        .register-btn {
            background: linear-gradient(135deg, #00d4ff 0%, #7b68ee 100%) !important;
            padding: 0.5rem 1rem !important;
            border-radius: 20px !important;
            color: white !important;
            font-weight: 600 !important;
            transition: all 0.3s ease !important;
        }

        .register-btn:hover {
            transform: translateY(-2px) !important;
            box-shadow: 0 8px 25px rgba(0, 212, 255, 0.3) !important;
            color: white !important;
        }

        .dropdown-menu button:hover {
            background-color: rgba(229, 9, 20, 0.1) !important;
        }

        /* Hide ALL rating and interactive elements in home cards */
        .card .card-rating-buttons,
        .card .watchlist-button-container, 
        .card .series-stats,
        .card .rating-btn,
        .card .watchlist-btn,
        .carousel .card-rating-buttons,
        .carousel .watchlist-button-container,
        .carousel .series-stats,
        .carousel .rating-btn,
        .carousel .watchlist-btn {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
        }

        .rating-btn {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: 2px solid rgba(255, 255, 255, 0.2);
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            color: rgba(255, 255, 255, 0.7);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            opacity: 1;
            transform: scale(1);
        }

        .card:hover .rating-btn {
            opacity: 1;
            transform: scale(1);
        }

        .rating-btn:hover {
            transform: scale(1.1);
            border-color: rgba(255,255,255,0.8);
            box-shadow: 0 0 15px rgba(255,255,255,0.2);
        }

        .rating-btn.active {
            border-color: rgba(255,255,255,1);
            box-shadow: 0 0 15px rgba(255,255,255,0.4);
            opacity: 1 !important;
        }

        .rating-btn.active:hover {
            transform: scale(1.1);
            box-shadow: 0 0 20px rgba(255,255,255,0.6);
        }
        
        .rating-btn.dislike:hover,
        .rating-btn.dislike.active {
            background: rgba(244, 67, 54, 0.3);
            border-color: #f44336;
            color: #f44336;
        }
        
        .rating-btn.like:hover,
        .rating-btn.like.active {
            background: rgba(76, 175, 80, 0.3);
            border-color: #4caf50;
            color: #4caf50;
        }
        
        .rating-btn.love:hover,
        .rating-btn.love.active {
            background: rgba(233, 30, 99, 0.3);
            border-color: #e91e63;
            color: #e91e63;
        }
        
        .rating-btn.watched:hover,
        .rating-btn.watched.active {
            background: rgba(123, 104, 238, 0.3);
            border-color: #7b68ee;
            color: #7b68ee;
        }

        /* Toast notification */
        .rating-toast {
            position: fixed;
            top: 100px;
            right: 20px;
            background: rgba(20,20,20,0.95);
            border: 1px solid rgba(0, 212, 255, 0.3);
            border-radius: 8px;
            padding: 1rem 1.5rem;
            color: white;
            z-index: 9999;
            opacity: 0;
            transform: translateX(100px);
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .rating-toast.show {
            opacity: 1;
            transform: translateX(0);
        }

        /* Watchlist Button Styles */
        .watchlist-button-container {
            position: absolute;
            top: 0.8rem;
            right: 0.8rem;
            z-index: 10;
        }

        .watchlist-btn {
            display: none !important;
            justify-content: center;
            font-size: 1rem;
            transition: all 0.3s ease;
            opacity: 0;
            transform: scale(0.8);
        }

        .card:hover .watchlist-btn {
            opacity: 1;
            transform: scale(1);
        }

        .watchlist-btn:hover {
            transform: scale(1.1);
            border-color: rgba(255,255,255,0.6);
            background: rgba(0, 212, 255, 0.8);
        }

        .watchlist-btn.in-list {
            background: rgba(40, 167, 69, 0.8);
            border-color: rgba(40, 167, 69, 0.5);
        }

        .watchlist-btn.in-list:hover {
            background: rgba(40, 167, 69, 1);
            border-color: rgba(40, 167, 69, 0.8);
        }

        .watchlist-status-menu {
            position: absolute;
            top: 100%;
            right: 0;
            margin-top: 5px;
            background: rgba(20,20,20,0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 8px;
            padding: 0.5rem 0;
            min-width: 150px;
            z-index: 2000;
            box-shadow: 0 10px 30px rgba(0,0,0,0.7);
        }

        .status-option {
            padding: 0.5rem 1rem;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s;
            font-size: 0.8rem;
            white-space: nowrap;
        }

        .status-option:hover {
            background-color: rgba(255,255,255,0.1);
        }

        .status-option.active {
            background-color: rgba(0, 212, 255, 0.2);
            color: #00d4ff;
        }

        .status-option.remove {
            color: #dc3545;
        }

        .status-option.remove:hover {
            background-color: rgba(220, 53, 69, 0.1);
        }

        @media (max-width: 768px) {
            /* Navegación móvil con distribución mejorada */
            .navbar {
                padding: 1rem;
                background: rgba(20,20,20,0.98) !important;
                backdrop-filter: blur(15px);
                z-index: 1500 !important;
                position: relative;
            }
            
            .navbar-container {
                display: flex !important;
                justify-content: space-between !important;
                align-items: center !important;
                width: 100% !important;
            }
            
            .navbar-brand {
                font-size: 1.3rem !important;
                order: 2 !important;
                flex: 1 !important;
                text-align: center !important;
                color: white !important;
                text-decoration: none !important;
            }
            
            .navbar-brand:before {
                display: none !important;
            }
            
            .mobile-controls {
                display: flex !important;
                gap: 1rem !important;
                order: 1 !important;
                flex-shrink: 0 !important;
            }
            
            .mobile-menu-toggle, .mobile-search-toggle {
                background: rgba(255,255,255,0.1) !important;
                border: none !important;
                color: white !important;
                font-size: 1.4rem !important;
                padding: 0.6rem !important;
                border-radius: 8px !important;
                cursor: pointer !important;
                transition: all 0.2s ease !important;
                display: block !important;
                min-width: 44px !important;
                min-height: 44px !important;
            }
            
            .mobile-menu-toggle:active,
            .mobile-search-toggle:active {
                background: rgba(255,255,255,0.2) !important;
                transform: scale(0.95) !important;
            }
            
            .desktop-search,
            .navbar-nav {
                display: none !important;
            }
            
            /* Asegurar que mobile-controls esté siempre visible en móvil */
            .mobile-controls {
                display: flex !important;
                visibility: visible !important;
                opacity: 1 !important;
            }
            
            /* Menú móvil desplegable */
            .navbar-nav {
                display: none !important;
                position: absolute !important;
                top: 100% !important;
                left: 0 !important;
                right: 0 !important;
                background: rgba(20,20,20,0.98) !important;
                backdrop-filter: blur(15px) !important;
                flex-direction: column !important;
                padding: 1rem !important;
                gap: 0.5rem !important;
                border-top: 1px solid rgba(255,255,255,0.1) !important;
                z-index: 1400 !important;
                box-shadow: 0 4px 20px rgba(0,0,0,0.5) !important;
            }
            
            .navbar-nav.mobile-open {
                display: flex !important;
            }
            
            .navbar-nav li {
                list-style: none !important;
                margin: 0 !important;
            }
            
            .navbar-nav a {
                color: white !important;
                padding: 1rem !important;
                display: block !important;
                text-decoration: none !important;
                border-radius: 8px !important;
                transition: background-color 0.2s !important;
                font-size: 1rem !important;
            }
            
            .navbar-nav a:hover {
                background-color: rgba(255,255,255,0.1) !important;
            }
            
            /* Dropdown dentro del menú móvil */
            .dropdown-menu {
                position: static !important;
                display: none !important;
                background: rgba(40, 40, 40, 0.95) !important;
                margin: 0.5rem 0 !important;
                border-radius: 8px !important;
                box-shadow: none !important;
                padding: 0.5rem !important;
            }
            
            .dropdown:hover .dropdown-menu {
                display: block !important;
            }
            
            /* Mejorar logout en móvil */
            .navbar-nav .dropdown-menu a[href*="logout"] {
                min-height: 48px !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                font-size: 1.1rem !important;
                font-weight: 600 !important;
                margin: 0.5rem 0 !important;
                -webkit-tap-highlight-color: rgba(255, 71, 87, 0.3) !important;
                touch-action: manipulation !important;
            }
            
            /* Mejorar toda la experiencia táctil del menú móvil */
            @media (max-width: 768px) {
                .navbar-nav a {
                    min-height: 44px !important;
                    display: flex !important;
                    align-items: center !important;
                    -webkit-tap-highlight-color: rgba(255, 255, 255, 0.1) !important;
                    touch-action: manipulation !important;
                }
                
                .dropdown-toggle {
                    position: relative !important;
                }
                
                .dropdown-toggle:after {
                    content: " ▼" !important;
                    font-size: 0.8rem !important;
                    margin-left: 0.5rem !important;
                }
                
                .dropdown.active .dropdown-toggle:after {
                    content: " ▲" !important;
                }
            }
            
            /* Hero móvil con poster */
            .hero-section {
                min-height: auto;
                height: auto;
                padding-bottom: 2rem;
            }
            
            .hero-content {
                padding: 1rem;
                max-width: 100%;
            }
            
            .hero-info-box {
                background: none;
                backdrop-filter: none;
                border: none;
                box-shadow: none;
                padding: 0;
                display: flex;
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
            
            .mobile-hero-poster {
                width: 150px;
                height: 225px;
                border-radius: 8px;
                margin-bottom: 1rem;
                box-shadow: 0 8px 24px rgba(0,0,0,0.5);
            }
            
            /* Carrusel móvil simple */
            .carousel-container {
                margin: 0 20px;
                padding: 10px 0;
            }
            
            .carousel {
                display: flex;
                gap: 0.5rem;
                overflow-x: auto;
                -webkit-overflow-scrolling: touch;
                scroll-snap-type: x mandatory;
                padding: 0 1rem;
            }
            
            .carousel::-webkit-scrollbar {
                display: none;
            }
            
            .carousel-nav {
                display: none;
            }
            
            /* Tarjetas móviles más largas como versión anterior */
            .card {
                width: 105px !important;
                height: 180px !important;
                min-width: 105px !important;
                flex-shrink: 0;
                scroll-snap-align: start;
                border-radius: 8px;
                box-shadow: 0 4px 15px rgba(0,0,0,0.4);
                border: 1px solid rgba(255,255,255,0.1);
            }
            
            /* Hover más sutil en móvil */
            .card:hover {
                transform: scale(1.02);
                box-shadow: 0 4px 15px rgba(0,0,0,0.3);
            }
            
            
            /* Ocultar elementos duplicados en móvil */
            .series-stats {
                display: none !important;
            }
            
            /* Mobile styles for action buttons */
            .card-view-btn {
                top: 0.4rem;
                right: 0.4rem;
                padding: 0.3rem 0.6rem;
                font-size: 0.65rem;
                opacity: 1 !important;
                transform: translateY(0) !important;
            }
            
            .card-list-btn {
                top: 0.4rem;
                left: 0.4rem;
                width: 28px;
                height: 28px;
                font-size: 0.7rem;
                opacity: 1 !important;
                transform: translateY(0) !important;
            }
            
            /* Secciones de contenido */
            .content-section {
                padding: 0;
                margin-bottom: 0.3rem;
            }
            
            .section-title {
                font-size: 1.1rem;
                font-weight: 700;
                margin-bottom: 0.4rem;
                padding: 0 1rem;
            }
            
            /* Ocultar información de streaming y categorías en tarjetas móviles */
            .card .card-streaming,
            .card .streaming-platforms,
            .card .streaming-platform,
            .card .card-categories,
            .card .card-category {
                display: none !important;
            }
            
            /* Mostrar información básica en tarjetas móviles */
            .card .card-info {
                opacity: 1 !important;
                transform: none !important;
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                background: linear-gradient(transparent, rgba(0,0,0,0.8));
                padding: 1.5rem 0.5rem 0.5rem;
                min-height: auto;
            }
            
            /* Ajustes móviles para card-info-overlay */
            .card-info-overlay {
                height: 35px !important;
                padding: 0.3rem 0.5rem !important;
                flex-direction: row !important;
                align-items: center !important;
                justify-content: space-between !important;
                font-size: 11px !important;
            }
            
            .overlay-left {
                flex: 1 !important;
            }
            
            .overlay-right {
                flex-shrink: 0 !important;
            }
            
            .overlay-meta {
                gap: 0.8rem !important;
                font-size: 0.8rem !important;
            }
            
            .overlay-description {
                font-size: 0.8rem !important;
                line-height: 1.3 !important;
            }
            
            .overlay-rating-info {
                gap: 0.5rem !important;
                font-size: 0.75rem !important;
            }
            
            .rating-count {
                padding: 0.2rem 0.3rem !important;
                font-size: 0.7rem !important;
                gap: 0.1rem !important;
                min-width: 30px !important;
            }
            
            .circular-detail-btn {
                width: 30px !important;
                height: 30px !important;
                font-size: 0.9rem !important;
            }
            
            .card .card-title {
                font-size: 0.75rem !important;
                font-weight: 600 !important;
                margin: 0 !important;
                line-height: 1.2 !important;
                text-shadow: 0 1px 3px rgba(0,0,0,0.8) !important;
                display: -webkit-box !important;
                -webkit-line-clamp: 2 !important;
                -webkit-box-orient: vertical !important;
                overflow: hidden !important;
                color: white !important;
            }
        }
        
        /* Mobile Controls - Hidden by default, shown in mobile media query */
        .mobile-controls {
            display: none;
            gap: 0.5rem;
        }
        
        @media (max-width: 768px) {
            .mobile-controls {
                display: flex !important;
                gap: 1rem !important;
                order: 1 !important;
                flex-shrink: 0 !important;
                visibility: visible !important;
                opacity: 1 !important;
            }
        }
        
        .mobile-menu-toggle, .mobile-search-toggle {
            background: none;
            border: none;
            color: white;
            font-size: 1.3rem;
            cursor: pointer;
            padding: 0.5rem;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }
        
        .mobile-menu-toggle:hover, .mobile-search-toggle:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        
        /* Navbar container */
        .navbar-container {
            position: relative;
        }
        
        /* Desktop Search */
        .desktop-search {
            display: block;
        }
        
        /* Mobile Search */
        .mobile-search {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            padding: 1rem;
            background: rgba(20, 20, 20, 0.98);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            z-index: 1400;
            box-shadow: 0 4px 20px rgba(0,0,0,0.5);
            display: none;
        }
        
        .mobile-search.show {
            display: block;
        }
        
        
        /* Utilidades responsive */
        @media (max-width: 767px) {
            .d-none.d-md-block {
                display: none !important;
            }
            .d-block.d-md-none {
                display: block !important;
            }
        }
        
        @media (min-width: 769px) {
            .d-block.d-md-none {
                display: none !important;
            }
            .mobile-hero-poster {
                display: none !important;
            }
        }
        
        /* Search Bar Styles */
        .search-container {
            position: relative;
        }
        
        .search-input-container {
            position: relative;
            display: flex;
            align-items: center;
        }
        
        .search-input {
            width: 100%;
            padding: 0.7rem 1rem;
            padding-right: 3rem;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 25px;
            color: white;
            font-size: 0.9rem;
            outline: none;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        .search-input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }
        
        .search-input:focus {
            border-color: rgba(0, 212, 255, 0.5);
            background: rgba(255, 255, 255, 0.12);
            box-shadow: 0 4px 20px rgba(0, 212, 255, 0.2);
        }
        
        .search-icon {
            position: absolute;
            right: 1rem;
            color: rgba(255, 255, 255, 0.6);
            font-size: 1rem;
            pointer-events: none;
        }
        
        .search-results {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: rgba(20, 20, 20, 0.95);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            margin-top: 0.5rem;
            max-height: 400px;
            overflow-y: auto;
            z-index: 2000;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.8);
        }
        
        .search-section {
            padding: 1rem 0;
        }
        
        .search-section-title {
            color: rgba(0, 212, 255, 0.9);
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 0 1rem;
            margin-bottom: 0.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 0.5rem;
        }
        
        .search-item {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 0.8rem 1rem;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .search-item:hover {
            background: rgba(0, 212, 255, 0.1);
            color: white;
        }
        
        .search-item-image {
            width: 40px;
            height: 40px;
            border-radius: 6px;
            object-fit: cover;
            flex-shrink: 0;
            background: rgba(255, 255, 255, 0.1);
        }
        
        .search-item-info {
            flex: 1;
            min-width: 0;
        }
        
        .search-item-title {
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 0.2rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .search-item-meta {
            font-size: 0.75rem;
            color: rgba(255, 255, 255, 0.6);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .search-item-description {
            font-size: 0.7rem;
            color: rgba(255, 255, 255, 0.5);
            line-height: 1.3;
            margin-top: 0.25rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .search-no-results {
            padding: 2rem 1rem;
            text-align: center;
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.9rem;
        }
        
        .search-loading {
            padding: 1rem;
            text-align: center;
            color: rgba(0, 212, 255, 0.8);
            font-size: 0.9rem;
        }
        
        /* Hide stats component that creates unwanted square in series */
        .series-stats-detail {
            display: none !important;
        }
        
        /* Make series rating buttons RED and visible */
        .hero-actions .card-rating-buttons {
            display: flex !important;
            gap: 2rem !important;
            justify-content: space-around !important;
            width: 100% !important;
            visibility: visible !important;
            opacity: 1 !important;
            background: transparent !important;
            z-index: 1000 !important;
        }
        
        .hero-actions .rating-button-with-count {
            display: flex !important;
            flex-direction: column !important;
            align-items: center !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
        
        .hero-actions .rating-btn,
        .hero-actions button.rating-btn,
        button.rating-btn.dislike,
        button.rating-btn.like,
        button.rating-btn.love,
        button.rating-btn.watched {
            display: flex !important;
            visibility: visible !important;
            opacity: 1 !important;
            background: #dc3545 !important;
            border: none !important;
            color: white !important;
            width: 50px !important;
            height: 50px !important;
            border-radius: 50% !important;
            align-items: center !important;
            justify-content: center !important;
        }
        
        /* Override any existing blue styles */
        .hero-actions .rating-btn[style*="background"] {
            background: #dc3545 !important;
        }
        
        /* Force rating buttons visibility everywhere */
        @media (max-width: 768px) {
            .hero-actions,
            .hero-actions .card-rating-buttons,
            .hero-actions .rating-button-with-count,
            .hero-actions button {
                display: flex !important;
                visibility: visible !important;
                opacity: 1 !important;
            }
        }
        
        /* Make series hero layout match movies on mobile */
        @media (max-width: 768px) {
            .hero-section {
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                min-height: 100vh !important;
                padding-top: 60px !important;
            }
            
            .hero-content {
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
                justify-content: center !important;
                text-align: center !important;
                padding: 2rem 1rem !important;
                width: 100% !important;
                max-width: 400px !important;
                margin: 0 auto !important;
            }
            
            .hero-info-box {
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
                width: 100% !important;
            }
            
            .series-mobile-poster {
                width: 180px;
                height: 270px;
                border-radius: 12px;
                margin: 0 auto 0.5rem auto;
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
                object-fit: cover;
                display: block;
            }
            
            .series-mobile-poster-placeholder {
                width: 180px;
                height: 270px;
                border-radius: 12px;
                margin: 0 auto 0.5rem auto;
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
                background: rgba(255, 255, 255, 0.1);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 4rem;
            }
            
            .mobile-genres {
                display: flex !important;
                justify-content: center !important;
                gap: 0.5rem !important;
                margin: 0.5rem 0 1.5rem 0 !important;
                flex-wrap: wrap !important;
            }
            
            .mobile-genre-tag {
                background: rgba(0, 120, 215, 0.8) !important;
                color: white !important;
                padding: 0.3rem 0.8rem !important;
                border-radius: 15px !important;
                font-size: 0.75rem !important;
                font-weight: 500 !important;
                border: none !important;
            }
            
            .mobile-streaming {
                margin: 1rem 0 !important;
            }
            
            .streaming-platforms-mobile {
                display: flex !important;
                justify-content: center !important;
                gap: 0.5rem !important;
                flex-wrap: wrap !important;
            }
            
            .platform-item-mobile {
                background: #dc3545 !important;
                border: none !important;
                border-radius: 8px !important;
                padding: 0.3rem 0.6rem !important;
            }
            
            .platform-name-mobile {
                color: white !important;
                font-size: 0.7rem !important;
                font-weight: 500 !important;
            }
            
            /* Hide duplicate genres in content section on mobile */
            .detail-genres {
                display: none !important;
            }
            
            /* Hide streaming platforms in content section on mobile */
            .streaming-platforms {
                display: none !important;
            }
            
            /* Hide only the empty/duplicate sections - keep episode progress (4th) */
            .series-info .detail-section:nth-child(1),
            .series-info .detail-section:nth-child(2), 
            .series-info .detail-section:nth-child(3) {
                display: none !important;
            }
            
            .series-detail-container .detail-genres {
                display: none !important;
            }
            
            .series-detail-container .streaming-platforms {
                display: none !important;
            }
            
            .series-stats-detail {
                display: none !important;
            }
            
            /* Remove ALL empty spaces between hero and content */
            #info {
                margin-top: 0px !important;
                padding-top: 0px !important;
            }
            
            .content-section {
                padding-top: 0 !important;
                margin-top: 0 !important;
                padding: 0 !important;
            }
            
            .series-detail-container {
                padding-top: 0 !important;
                margin-top: 0 !important;
                padding: 1rem !important;
            }
            
            /* Hide empty sections that create space */
            .detail-section:empty {
                display: none !important;
            }
            
            .series-details-modern:empty {
                display: none !important;
            }
            
            /* Fix actor cards to show full image */
            .actor-image-simple {
                width: 100% !important;
                height: 120px !important;
                overflow: hidden !important;
                border-radius: 8px !important;
            }
            
            .actor-image-simple img {
                width: 100% !important;
                height: 100% !important;
                object-fit: cover !important;
                object-position: center top !important;
            }
            
            .actor-card-simple {
                background: rgba(255, 255, 255, 0.05) !important;
                border-radius: 8px !important;
                overflow: hidden !important;
            }
            
            /* FORCE RED BUTTONS - Exact class targeting */
            button.rating-btn.dislike,
            button.rating-btn.like,
            button.rating-btn.love,
            button.rating-btn.watched,
            .card-rating-buttons button.rating-btn,
            .hero-actions button.rating-btn {
                background-color: #dc3545 !important;
                background: #dc3545 !important;
                border: none !important;
                color: white !important;
                width: 50px !important;
                height: 50px !important;
                border-radius: 50% !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
            }
            
            /* Override any inline styles */
            button[class*="rating-btn"] {
                background: #dc3545 !important;
            }
            
            .hero-title {
                font-size: 1.8rem !important;
                margin-bottom: 0.5rem !important;
                text-align: center !important;
            }
            
            .hero-original-title {
                font-size: 0.9rem !important;
                margin-bottom: 1rem !important;
                text-align: center !important;
            }
            
            .hero-meta {
                display: flex !important;
                justify-content: center !important;
                gap: 1rem !important;
                flex-wrap: wrap !important;
                margin-bottom: 1rem !important;
            }
            
            .hero-description {
                font-size: 0.9rem !important;
                line-height: 1.4 !important;
                margin-bottom: 1.5rem !important;
                text-align: center !important;
                padding: 0 1rem !important;
            }
            
            .hero-actions {
                width: 100% !important;
                display: flex !important;
                justify-content: center !important;
                margin-top: 1rem !important;
            }
            
            .hero-actions {
                display: flex !important;
                justify-content: center !important;
                margin-top: 1.5rem !important;
                width: 100% !important;
                visibility: visible !important;
                opacity: 1 !important;
                z-index: 100 !important;
            }
            
            .hero-actions .card-rating-buttons {
                display: flex !important;
                gap: 2rem !important;
                justify-content: space-around !important;
                width: 100% !important;
                max-width: 300px !important;
                visibility: visible !important;
                opacity: 1 !important;
                z-index: 1000 !important;
            }
            
            .hero-actions .rating-button-with-count {
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
                gap: 0.5rem !important;
                visibility: visible !important;
                opacity: 1 !important;
            }
            
            /* Force RED rating buttons everywhere */
            .hero-actions button,
            .hero-actions .rating-btn,
            .hero-actions .card-rating-buttons button,
            button.rating-btn,
            .rating-btn.dislike,
            .rating-btn.like, 
            .rating-btn.love,
            .rating-btn.watched {
                display: flex !important;
                visibility: visible !important;
                opacity: 1 !important;
                background: rgba(220, 53, 69, 0.9) !important;
                border: none !important;
                color: white !important;
                width: 50px !important;
                height: 50px !important;
                border-radius: 50% !important;
                align-items: center !important;
                justify-content: center !important;
                cursor: pointer !important;
                transition: all 0.3s ease !important;
            }
            
            .hero-actions .rating-btn:hover,
            .hero-actions button:hover {
                background: rgba(220, 53, 69, 1) !important;
                transform: scale(1.05) !important;
            }
            
            .hero-actions .rating-btn.active,
            .hero-actions button.active {
                background: rgba(255, 69, 90, 1) !important;
                box-shadow: 0 0 10px rgba(220, 53, 69, 0.5) !important;
            }
            
            .hero-actions .rating-count {
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
                text-align: center !important;
            }
            
            .hero-actions .count-number {
                font-size: 0.9rem !important;
                font-weight: bold !important;
                color: white !important;
            }
            
            .hero-actions .count-label {
                font-size: 0.7rem !important;
                color: rgba(255, 255, 255, 0.7) !important;
            }
            
            /* Hide duplicate poster in content section on mobile */
            .series-poster {
                display: none !important;
            }
            
            /* MOBILE RATING BUTTONS - DIFFERENT COLORS */
            .hero-actions button.rating-btn,
            .card-rating-buttons button.rating-btn {
                border: none !important;
                color: white !important;
                width: 50px !important;
                height: 50px !important;
                border-radius: 50% !important;
                display: flex !important;
                align-items: center !important;
                justify-content: center !important;
                visibility: visible !important;
                opacity: 1 !important;
            }
            
            button.rating-btn.dislike,
            .hero-actions button.rating-btn.dislike {
                background: #dc3545 !important;
                background-color: #dc3545 !important;
            }
            
            button.rating-btn.like,
            .hero-actions button.rating-btn.like {
                background: #28a745 !important;
                background-color: #28a745 !important;
            }
            
            button.rating-btn.love,
            .hero-actions button.rating-btn.love {
                background: #e91e63 !important;
                background-color: #e91e63 !important;
            }
            
            button.rating-btn.watched,
            .hero-actions button.rating-btn.watched {
                background: #6f42c1 !important;
                background-color: #6f42c1 !important;
            }
            
            button.movie-rating-btn.watchlist,
            .movie-actions button.movie-rating-btn.watchlist {
                background: #6f42c1 !important;
                background-color: #6f42c1 !important;
            }
            
            /* MOBILE ACTOR CARDS - FULL HEIGHT IMAGE */
            .cast-grid-compact .actor-image-simple {
                width: 100% !important;
                height: 400px !important;
                overflow: hidden !important;
                border-radius: 8px 8px 0 0 !important;
                background: #333 !important;
            }
            
            .cast-grid-compact .actor-image-simple img {
                width: 100% !important;
                height: 100% !important;
                object-fit: cover !important;
                object-position: center top !important;
                display: block !important;
            }
            
            .cast-grid-compact {
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
                gap: 1rem !important;
                padding: 1rem !important;
            }
            
            .cast-grid-compact .actor-card-simple {
                background: rgba(255, 255, 255, 0.1) !important;
                border-radius: 8px !important;
                overflow: hidden !important;
                margin: 0 !important;
                width: 320px !important;
                max-width: 95% !important;
                text-align: center !important;
            }
            
            .actor-card-simple {
                background: rgba(255, 255, 255, 0.08) !important;
                border-radius: 8px !important;
                overflow: hidden !important;
                margin-bottom: 1rem !important;
            }
            
            .actor-info-simple {
                padding: 0.8rem !important;
                text-align: center !important;
            }
            
            .actor-name-simple {
                font-size: 0.9rem !important;
                font-weight: 600 !important;
                color: white !important;
                margin-bottom: 0.3rem !important;
            }
            
            .actor-role-simple {
                font-size: 0.75rem !important;
                color: rgba(255, 255, 255, 0.7) !important;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar" id="navbar">
        <div class="navbar-container" style="display: flex; align-items: center; justify-content: space-between; width: 100%; max-width: 1200px; margin: 0 auto;">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="navbar-brand">DORAS<span class="ai-highlight">IA</span></a>
            
            <!-- Desktop Search Bar -->
            <div class="search-container desktop-search" style="flex: 1; max-width: 400px; margin: 0 2rem;">
                <div class="search-input-container">
                    <input type="text" id="globalSearch" placeholder="Buscar series, películas, actores..." class="search-input">
                    <div class="search-icon">🔍</div>
                </div>
                <div class="search-results" id="searchResults" style="display: none;"></div>
            </div>
            
            <!-- Mobile Toggle Buttons -->
            <div class="mobile-controls">
                <button class="mobile-search-toggle" onclick="toggleMobileSearch()">🔍</button>
                <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">☰</button>
            </div>
            
            <!-- Navigation Menu -->
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
                <li><a href="{{ route('movies.index') }}">Películas</a></li>
                <li><a href="{{ route('actors.index') }}">Actores</a></li>
                <li><a href="{{ route('upcoming.index') }}">Próximamente</a></li>
                <li><a href="{{ route('browse') }}">Explorar</a></li>
                
                @auth
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle">{{ Auth::user()->name }}</a>
                    <ul class="dropdown-menu">
                        <li><a href="{{ route('profile.show') }}">Mi Perfil</a></li>
                        <li><a href="{{ route('profile.edit') }}">Editar Perfil</a></li>
                        <li><a href="{{ route('profile.watchlist') }}">Mi Lista</a></li>
                        <li><a href="{{ route('profile.ratings') }}">Mis Calificaciones</a></li>
                        <li><a href="{{ route('profile.watched') }}">Series Vistas</a></li>
                        <li><hr style="margin: 0.5rem 0; border-color: rgba(255,255,255,0.2);"></li>
                        <li>
                            <a href="{{ url('/working-logout') }}" 
                               onclick="console.log('Logout clicked'); localStorage.clear(); sessionStorage.clear();"
                               style="display: block; padding: 0.75rem 1rem; color: #ff4757; text-decoration: none; transition: all 0.3s; background: rgba(255, 71, 87, 0.1); border-radius: 8px; margin: 0.25rem 0.5rem; font-weight: 600; text-align: center; border: 1px solid rgba(255, 71, 87, 0.3);"
                               onmouseover="this.style.background='rgba(255, 71, 87, 0.2)'"
                               onmouseout="this.style.background='rgba(255, 71, 87, 0.1)'"
                               ontouchstart="this.style.background='rgba(255, 71, 87, 0.2)'"
                               ontouchend="this.style.background='rgba(255, 71, 87, 0.1)'">
                                🚪 Cerrar Sesión
                            </a>
                        </li>
                    </ul>
                </li>
                @else
                <li><a href="{{ route('login') }}">Iniciar Sesión</a></li>
                <li><a href="{{ route('register.simple.form') }}" class="register-btn">Registrarse</a></li>
                @endauth
            </ul>
        </div>
        
        <!-- Mobile Search Container -->
        <div class="search-container mobile-search" id="mobileSearchContainer">
            <div class="search-input-container">
                <input type="text" id="mobileGlobalSearch" placeholder="Buscar series, películas, actores..." class="search-input">
                <div class="search-icon">🔍</div>
            </div>
            <div class="search-results" id="mobileSearchResults" style="display: none;"></div>
        </div>
    </nav>

    @yield('content')

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

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
            const mobileSearchContainer = document.getElementById('mobileSearchContainer');
            
            navbarNav.classList.toggle('mobile-open');
            
            // Close mobile search if open
            if (mobileSearchContainer && mobileSearchContainer.style.display === 'block') {
                mobileSearchContainer.style.display = 'none';
            }
        }

        // Mobile search toggle
        function toggleMobileSearch() {
            const mobileSearchContainer = document.getElementById('mobileSearchContainer');
            const mobileNav = document.getElementById('navbar-nav');
            const mobileSearchInput = document.getElementById('mobileGlobalSearch');
            
            // Toggle the show class
            mobileSearchContainer.classList.toggle('show');
            
            if (mobileSearchContainer.classList.contains('show')) {
                // Close mobile menu if open
                if (mobileNav) {
                    mobileNav.classList.remove('mobile-open');
                }
                
                // Focus on mobile search input
                setTimeout(() => {
                    if (mobileSearchInput) {
                        mobileSearchInput.focus();
                    }
                }, 100);
            }
        }

        // Enhanced dropdown functionality
        document.addEventListener('DOMContentLoaded', function() {
            const dropdowns = document.querySelectorAll('.dropdown');
            
            dropdowns.forEach(dropdown => {
                let timeout;
                
                dropdown.addEventListener('mouseenter', function() {
                    clearTimeout(timeout);
                    const menu = this.querySelector('.dropdown-menu');
                    if (menu) {
                        menu.style.display = 'block';
                        setTimeout(() => {
                            menu.style.opacity = '1';
                            menu.style.transform = 'translateY(0)';
                            menu.style.pointerEvents = 'auto';
                        }, 10);
                    }
                });
                
                dropdown.addEventListener('mouseleave', function() {
                    const menu = this.querySelector('.dropdown-menu');
                    if (menu) {
                        timeout = setTimeout(() => {
                            menu.style.opacity = '0';
                            menu.style.transform = 'translateY(-10px)';
                            menu.style.pointerEvents = 'none';
                            setTimeout(() => {
                                menu.style.display = 'none';
                            }, 300);
                        }, 150); // 150ms delay before hiding
                    }
                });
            });
        });

        // Image fallback
        function handleImageError(img) {
            img.style.background = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
            img.style.display = 'flex';
            img.style.alignItems = 'center';
            img.style.justifyContent = 'center';
            img.innerHTML = '<span style="color: white; font-weight: bold;">📺</span>';
        }

        // Rating functionality
        function rateSeries(seriesId, ratingType, button) {
            let csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            if (!csrfToken) {
                showRatingToast('Token de seguridad no encontrado', 'error');
                return;
            }
            
            
            fetch(`/series/${seriesId}/rate`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    rating_type: ratingType
                })
            })
            .then(response => {
                if (!response.ok) {
                    if (response.status === 401) {
                        throw new Error('❌ No autorizado - Por favor inicia sesión');
                    } else if (response.status === 419) {
                        throw new Error('⏰ Sesión expirada - Intenta refrescar la página');
                    } else if (response.status === 422) {
                        throw new Error('❗ Datos de calificación inválidos');
                    } else {
                        throw new Error(`🚫 Error del servidor: ${response.status}`);
                    }
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Update button states - works for both cards and detail pages
                    const container = button.closest('.card') || button.closest('.card-rating-buttons') || button.closest('.hero-actions') || button.closest('.mobile-actions-bar');
                    
                    if (container) {
                        const ratingButtons = container.querySelectorAll('.rating-btn, .mobile-rating-btn');
                        
                        // Remove active class from all buttons
                        ratingButtons.forEach(btn => btn.classList.remove('active'));
                        
                        // Add active class to current button if not removing rating
                        if (data.rating_type) {
                            button.classList.add('active');
                        }
                    }
                    
                    // Update all rating buttons on the page (desktop and mobile)
                    document.querySelectorAll('.rating-btn, .mobile-rating-btn').forEach(btn => {
                        btn.classList.remove('active');
                        const btnType = btn.classList.contains('dislike') ? 'dislike' : 
                                       btn.classList.contains('like') ? 'like' : 'love';
                        if (data.rating_type === btnType) {
                            btn.classList.add('active');
                        }
                    });
                    
                    // Update rating info display if exists
                    const ratingInfo = document.querySelector('.current-rating-info');
                    if (ratingInfo) {
                        if (data.rating_type) {
                            const ratingDisplay = ratingInfo.querySelector('.rating-display');
                            if (ratingDisplay) {
                                const ratingText = data.rating_type === 'dislike' ? '👎 No me gusta' :
                                                 data.rating_type === 'like' ? '👍 Me gusta' : '❤️ Me encanta';
                                ratingDisplay.textContent = ratingText;
                            }
                            ratingInfo.style.display = 'block';
                        } else {
                            ratingInfo.style.display = 'none';
                        }
                    }
                    
                    // Update rating counts if provided (handle both movies and series format)
                    const counts = data.rating_counts || data.counts;
                    if (counts) {
                        const dislikeCount = document.querySelector('.dislike-count .count-number');
                        const likeCount = document.querySelector('.like-count .count-number');
                        const loveCount = document.querySelector('.love-count .count-number');
                        
                        if (dislikeCount) dislikeCount.textContent = counts.dislike;
                        if (likeCount) likeCount.textContent = counts.like;
                        if (loveCount) loveCount.textContent = counts.love;
                    }
                    
                    // Show toast notification
                    showRatingToast(data.message);
                } else {
                    showRatingToast(data.message || 'Error al guardar calificación', 'error');
                }
            })
            .catch(error => {
                console.error('Error rating series:', error);
                showRatingToast(error.message || 'Error al guardar calificación', 'error');
            });
        }

        function showRatingToast(message, type = 'success') {
            // Remove existing toast
            const existingToast = document.querySelector('.rating-toast');
            if (existingToast) {
                existingToast.remove();
            }
            
            // Create new toast
            const toast = document.createElement('div');
            toast.className = 'rating-toast';
            toast.textContent = message;
            
            if (type === 'error') {
                toast.style.borderColor = 'rgba(220, 53, 69, 0.5)';
            }
            
            document.body.appendChild(toast);
            
            // Show toast
            setTimeout(() => {
                toast.classList.add('show');
            }, 100);
            
            // Hide and remove toast
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 300);
            }, 3000);
        }

        // Movie rating functionality
        function rateMovie(movieId, ratingType, button) {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            if (!csrfToken) {
                showRatingToast('Token de seguridad no encontrado', 'error');
                return;
            }
            
            fetch(`/movies/${movieId}/rate`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    rating_type: ratingType
                })
            })
            .then(response => {
                if (!response.ok) {
                    if (response.status === 401) {
                        throw new Error('❌ No autorizado - Por favor inicia sesión');
                    } else if (response.status === 419) {
                        throw new Error('⏰ Sesión expirada - Intenta refrescar la página');
                    } else if (response.status === 422) {
                        throw new Error('❗ Datos de calificación inválidos');
                    } else {
                        throw new Error(`🚫 Error del servidor: ${response.status}`);
                    }
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Update button states for both desktop and mobile
                    const container = button.closest('.movie-rating-buttons') || button.closest('.movie-actions') || button.closest('.mobile-actions-bar');
                    
                    if (container) {
                        const ratingButtons = container.querySelectorAll('.movie-rating-btn, .mobile-rating-btn');
                        
                        // Remove active class from all buttons
                        ratingButtons.forEach(btn => btn.classList.remove('active'));
                        
                        // Add active class to current button if not removing rating
                        if (data.rating_type) {
                            button.classList.add('active');
                        }
                    }
                    
                    // Update all rating buttons on the page (desktop and mobile)
                    document.querySelectorAll('.movie-rating-btn, .mobile-rating-btn').forEach(btn => {
                        btn.classList.remove('active');
                        const btnType = btn.classList.contains('dislike') ? 'dislike' : 
                                       btn.classList.contains('like') ? 'like' : 'love';
                        if (data.rating_type === btnType) {
                            btn.classList.add('active');
                        }
                    });
                    
                    // Update rating info display if exists
                    const ratingInfo = document.querySelector('.current-rating-info');
                    if (ratingInfo) {
                        if (data.rating_type) {
                            const ratingDisplay = ratingInfo.querySelector('.rating-display');
                            if (ratingDisplay) {
                                const ratingText = data.rating_type === 'dislike' ? '👎 No me gusta' :
                                                 data.rating_type === 'like' ? '👍 Me gusta' : '❤️ Me encanta';
                                ratingDisplay.textContent = ratingText;
                            }
                            ratingInfo.style.display = 'block';
                        } else {
                            ratingInfo.style.display = 'none';
                        }
                    }
                    
                    // Update rating counts if provided (handle both movies and series format)
                    const counts = data.rating_counts || data.counts;
                    if (counts) {
                        const dislikeCount = document.querySelector('.dislike-count .count-number');
                        const likeCount = document.querySelector('.like-count .count-number');
                        const loveCount = document.querySelector('.love-count .count-number');
                        
                        if (dislikeCount) dislikeCount.textContent = counts.dislike;
                        if (likeCount) likeCount.textContent = counts.like;
                        if (loveCount) loveCount.textContent = counts.love;
                    }
                    
                    // Show toast notification
                    showRatingToast(data.message);
                } else {
                    showRatingToast(data.message || 'Error al guardar calificación', 'error');
                }
            })
            .catch(error => {
                console.error('Error rating movie:', error);
                showRatingToast(error.message || 'Error al guardar calificación', 'error');
            });
        }
        
        // Movie watchlist functionality
        function toggleMovieWatchlist(movieId, button) {
            fetch(`/movies/${movieId}/watchlist`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    button.classList.toggle('active');
                    
                    // Update button text and icon
                    const span = button.querySelector('span');
                    const svg = button.querySelector('svg path');
                    
                    if (data.in_watchlist) {
                        span.textContent = 'En mi lista';
                        button.title = 'Quitar de mi lista';
                        svg.setAttribute('d', 'M9 16.17L4.83 12L3.41 13.41L9 19L21 7L19.59 5.59L9 16.17Z');
                    } else {
                        span.textContent = 'Mi lista';
                        button.title = 'Agregar a mi lista';
                        svg.setAttribute('d', 'M19 13H13V19H11V13H5V11H11V5H13V11H19V13Z');
                    }
                    
                    showRatingToast(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showRatingToast('Error al actualizar la lista', 'error');
            });
        }

        // Watchlist functionality
        function toggleWatchlist(seriesId, button) {
            const container = button.closest('.watchlist-button-container');
            
            fetch(`/series/${seriesId}/watchlist`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({
                    status: 'want_to_watch'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateWatchlistButton(container, data.in_watchlist, data.status);
                    showRatingToast(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showRatingToast('Error al actualizar la lista', 'error');
            });
        }

        function updateWatchlistStatus(seriesId, status, option) {
            fetch(`/series/${seriesId}/watchlist`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({
                    status: status
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update active status in menu
                    const menu = option.closest('.watchlist-status-menu');
                    menu.querySelectorAll('.status-option').forEach(opt => opt.classList.remove('active'));
                    option.classList.add('active');
                    
                    // Hide menu
                    menu.style.display = 'none';
                    
                    showRatingToast(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showRatingToast('Error al actualizar el estado', 'error');
            });
        }

        function removeFromWatchlist(seriesId, option) {
            fetch(`/series/${seriesId}/watchlist`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({}) // Empty body will remove from list
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const container = option.closest('.watchlist-button-container');
                    updateWatchlistButton(container, false, 'want_to_watch');
                    showRatingToast(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showRatingToast('Error al eliminar de la lista', 'error');
            });
        }

        function updateWatchlistButton(container, inWatchlist, status) {
            const button = container.querySelector('.watchlist-btn');
            const icon = button.querySelector('.watchlist-icon');
            const menu = container.querySelector('.watchlist-status-menu');
            
            if (inWatchlist) {
                button.classList.add('in-list');
                icon.textContent = '✅';
                button.title = 'En tu lista - Click derecho para opciones';
                
                // Create menu if it doesn't exist
                if (!menu) {
                    const seriesId = container.dataset.seriesId;
                    const menuHTML = `
                        <div class="watchlist-status-menu" style="display: none;">
                            <div class="status-option ${status === 'want_to_watch' ? 'active' : ''}" onclick="updateWatchlistStatus(${seriesId}, 'want_to_watch', this)">🎯 Pendiente</div>
                            <div class="status-option ${status === 'watching' ? 'active' : ''}" onclick="updateWatchlistStatus(${seriesId}, 'watching', this)">👀 Viendo</div>
                            <div class="status-option ${status === 'completed' ? 'active' : ''}" onclick="updateWatchlistStatus(${seriesId}, 'completed', this)">✅ Completada</div>
                            <div class="status-option ${status === 'on_hold' ? 'active' : ''}" onclick="updateWatchlistStatus(${seriesId}, 'on_hold', this)">⏸️ En Pausa</div>
                            <div class="status-option ${status === 'dropped' ? 'active' : ''}" onclick="updateWatchlistStatus(${seriesId}, 'dropped', this)">❌ Abandonada</div>
                            <hr style="margin: 0.5rem 0; border-color: rgba(255,255,255,0.2);">
                            <div class="status-option remove" onclick="removeFromWatchlist(${seriesId}, this)">🗑️ Eliminar de mi lista</div>
                        </div>
                    `;
                    container.insertAdjacentHTML('beforeend', menuHTML);
                }
            } else {
                button.classList.remove('in-list');
                icon.textContent = '➕';
                button.title = 'Agregar a mi lista';
                
                // Remove menu if exists
                if (menu) {
                    menu.remove();
                }
            }
        }

        // Show/hide watchlist status menu on right click
        document.addEventListener('contextmenu', function(e) {
            const watchlistBtn = e.target.closest('.watchlist-btn.in-list');
            if (watchlistBtn) {
                e.preventDefault();
                const menu = watchlistBtn.closest('.watchlist-button-container').querySelector('.watchlist-status-menu');
                if (menu) {
                    menu.style.display = menu.style.display === 'block' ? 'none' : 'block';
                }
            }
        });

        // Hide watchlist menus when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.watchlist-button-container')) {
                document.querySelectorAll('.watchlist-status-menu').forEach(menu => {
                    menu.style.display = 'none';
                });
            }
        });

        // Mark as watched functionality
        function markAsWatched(seriesId, button) {
            fetch(`/series/${seriesId}/watched`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Mark button as active
                    button.classList.add('active');
                    button.title = 'Ya la viste';
                    
                    // Only update counter if it wasn't already watched
                    if (!data.already_watched) {
                        const watchedCountEl = button.parentElement.querySelector('.count-number');
                        if (watchedCountEl) {
                            const currentCount = parseInt(watchedCountEl.textContent) || 0;
                            watchedCountEl.textContent = currentCount + 1;
                        }
                    }
                    
                    // Disable button to prevent multiple clicks
                    button.disabled = true;
                    button.style.opacity = '0.7';
                    button.style.cursor = 'not-allowed';
                    
                    // Show toast notification
                    showRatingToast(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showRatingToast('Error al marcar como vista', 'error');
            });
        }

        // Global search functionality
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('globalSearch');
            const searchResults = document.getElementById('searchResults');
            const mobileSearchInput = document.getElementById('mobileGlobalSearch');
            const mobileSearchResults = document.getElementById('mobileSearchResults');
            let searchTimeout;
            
            // Desktop search functionality
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    const query = this.value.trim();
                    handleSearchInput(query, searchResults, false);
                });
            }
            
            // Mobile search functionality
            if (mobileSearchInput) {
                mobileSearchInput.addEventListener('input', function() {
                    const query = this.value.trim();
                    handleSearchInput(query, mobileSearchResults, true);
                });
            }
            
            function handleSearchInput(query, resultsContainer, isMobile) {
                clearTimeout(searchTimeout);
                
                if (query.length < 2) {
                    resultsContainer.style.display = 'none';
                    return;
                }
                
                // Show loading
                resultsContainer.style.display = 'block';
                resultsContainer.innerHTML = '<div class="search-loading">🔍 Buscando...</div>';
                
                searchTimeout = setTimeout(() => {
                    performSearch(query, resultsContainer);
                }, 300);
            }
            
            // Hide results when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.search-container')) {
                    if (searchResults) searchResults.style.display = 'none';
                    if (mobileSearchResults) mobileSearchResults.style.display = 'none';
                }
            });
            
            function performSearch(query, resultsContainer) {
                fetch(`/api/search?q=${encodeURIComponent(query)}`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                    }
                })
                .then(response => response.json())
                .then(data => {
                    displaySearchResults(data, resultsContainer);
                })
                .catch(error => {
                    console.error('Search error:', error);
                    resultsContainer.innerHTML = '<div class="search-no-results">Error en la búsqueda</div>';
                });
            }
            
            function displaySearchResults(data, resultsContainer) {
                let html = '';
                
                // Series results
                if (data.series && data.series.length > 0) {
                    html += '<div class="search-section">';
                    html += '<div class="search-section-title">📺 Series</div>';
                    data.series.forEach(series => {
                        const posterUrl = series.poster_path ? 
                            `https://image.tmdb.org/t/p/w200${series.poster_path}` : 
                            '/images/placeholder-poster.jpg';
                        const year = series.first_air_date ? new Date(series.first_air_date).getFullYear() : '';
                        
                        const displayTitle = series.title_es || series.spanish_title || series.title;
                        const displayOverview = series.overview_es || series.spanish_overview || series.overview;
                        html += `
                            <a href="/series/${series.id}" class="search-item">
                                <img src="${posterUrl}" alt="${displayTitle}" class="search-item-image" onerror="this.src='/images/placeholder-poster.jpg'">
                                <div class="search-item-info">
                                    <div class="search-item-title">${displayTitle}</div>
                                    <div class="search-item-meta">${year} • ⭐ ${series.vote_average || 'N/A'}</div>
                                    ${displayOverview ? `<div class="search-item-description">${displayOverview.substring(0, 80)}${displayOverview.length > 80 ? '...' : ''}</div>` : ''}
                                </div>
                            </a>
                        `;
                    });
                    html += '</div>';
                }
                
                // Actors results
                if (data.actors && data.actors.length > 0) {
                    html += '<div class="search-section">';
                    html += '<div class="search-section-title">🎭 Actores</div>';
                    data.actors.forEach(actor => {
                        const profileUrl = actor.profile_path ? 
                            `https://image.tmdb.org/t/p/w200${actor.profile_path}` : 
                            '/images/placeholder-actor.jpg';
                        const birthYear = actor.birthday ? new Date(actor.birthday).getFullYear() : '';
                        
                        html += `
                            <a href="/actors/${actor.id}" class="search-item">
                                <img src="${profileUrl}" alt="${actor.name}" class="search-item-image" onerror="this.src='/images/placeholder-actor.jpg'">
                                <div class="search-item-info">
                                    <div class="search-item-title">${actor.name}</div>
                                    <div class="search-item-meta">${birthYear ? `Nacido en ${birthYear}` : 'Actor'} • Popularidad: ${Math.round(actor.popularity || 0)}</div>
                                </div>
                            </a>
                        `;
                    });
                    html += '</div>';
                }
                
                if (!html) {
                    html = '<div class="search-no-results">😕 No se encontraron resultados</div>';
                }
                
                resultsContainer.innerHTML = html;
                resultsContainer.style.display = 'block';
            }
        });

        // Comments functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Handle comment form submission
            const commentForm = document.getElementById('commentForm');
            if (commentForm) {
                commentForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const seriesId = window.location.pathname.split('/').pop();
                    const content = document.getElementById('commentContent').value.trim();
                    const isSpoiler = document.getElementById('isSpoiler').checked;
                    
                    if (!content) return;
                    
                    fetch(`/series/${seriesId}/comments`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        },
                        body: JSON.stringify({
                            content: content,
                            is_spoiler: isSpoiler
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showRatingToast(data.message);
                            // Reload page to show new comment
                            setTimeout(() => {
                                window.location.reload();
                            }, 1000);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showRatingToast('Error al enviar comentario', 'error');
                    });
                });
            }
            
            // Handle spoiler reveal
            document.querySelectorAll('.reveal-spoiler-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const commentContent = this.closest('.comment-content');
                    commentContent.classList.remove('spoiler-hidden');
                    this.parentElement.remove();
                });
            });
        });
    </script>
    {{-- @vite('resources/js/app.js') --}}
    
    <!-- Footer -->
    @include('components.footer')
    
    <script>
        // Funcionalidad del menú móvil
        function toggleMobileMenu() {
            const navbarNav = document.querySelector('.navbar-nav');
            if (navbarNav) {
                navbarNav.classList.toggle('mobile-open');
            }
        }
        
        // Funcionalidad para mostrar/ocultar información de cards
        function toggleCardInfo(card) {
            // Disabled for home page - no rating interactions
            return false;
        }
        
        // Mostrar opciones de calificación
        function showRatingOptions(button) {
            const seriesId = button.closest('.card').dataset.seriesId || 
                           button.closest('.card').querySelector('a[href*="series/"]').href.split('/').pop();
            
            // Crear mini menú de opciones
            const options = document.createElement('div');
            options.style.cssText = `
                position: absolute;
                bottom: 50px;
                right: 0;
                background: rgba(0,0,0,0.9);
                border-radius: 8px;
                padding: 0.5rem;
                display: flex;
                gap: 0.5rem;
                z-index: 1000;
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255,255,255,0.1);
            `;
            
            options.innerHTML = `
                <button onclick="rateSeries(${seriesId}, 'dislike', this); this.parentElement.remove();" style="background: rgba(220,53,69,0.2); color: #dc3545; border: none; padding: 0.5rem; border-radius: 50%; cursor: pointer; font-size: 1.1rem;">👎</button>
                <button onclick="rateSeries(${seriesId}, 'like', this); this.parentElement.remove();" style="background: rgba(40,167,69,0.2); color: #28a745; border: none; padding: 0.5rem; border-radius: 50%; cursor: pointer; font-size: 1.1rem;">👍</button>
                <button onclick="rateSeries(${seriesId}, 'love', this); this.parentElement.remove();" style="background: rgba(255,105,180,0.2); color: #ff69b4; border: none; padding: 0.5rem; border-radius: 50%; cursor: pointer; font-size: 1.1rem;">❤️</button>
            `;
            
            button.parentElement.style.position = 'relative';
            button.parentElement.appendChild(options);
            
            // Cerrar al hacer click fuera
            setTimeout(() => {
                document.addEventListener('click', function closeOptions(e) {
                    if (!options.contains(e.target) && e.target !== button) {
                        options.remove();
                        document.removeEventListener('click', closeOptions);
                    }
                });
            }, 100);
        }
        
        // Cerrar overlays al hacer click fuera
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.card')) {
                document.querySelectorAll('.card-info-overlay.show').forEach(overlay => {
                    overlay.classList.remove('show');
                });
            }
        });
        
        // Funcionalidad de búsqueda móvil
        
        // Mejorar dropdown táctil en móvil
        document.addEventListener('DOMContentLoaded', function() {
            // Manejar dropdowns en móvil
            const dropdowns = document.querySelectorAll('.navbar-nav .dropdown');
            
            dropdowns.forEach(dropdown => {
                const toggle = dropdown.querySelector('.dropdown-toggle');
                const menu = dropdown.querySelector('.dropdown-menu');
                
                if (toggle && menu) {
                    toggle.addEventListener('click', function(e) {
                        e.preventDefault();
                        
                        // Cerrar otros dropdowns
                        dropdowns.forEach(otherDropdown => {
                            if (otherDropdown !== dropdown) {
                                otherDropdown.classList.remove('active');
                                const otherMenu = otherDropdown.querySelector('.dropdown-menu');
                                if (otherMenu) otherMenu.style.display = 'none';
                            }
                        });
                        
                        // Toggle este dropdown
                        dropdown.classList.toggle('active');
                        const isActive = dropdown.classList.contains('active');
                        menu.style.display = isActive ? 'block' : 'none';
                    });
                }
            });
            
            // Cerrar dropdowns al tocar fuera
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.dropdown')) {
                    dropdowns.forEach(dropdown => {
                        dropdown.classList.remove('active');
                        const menu = dropdown.querySelector('.dropdown-menu');
                        if (menu) menu.style.display = 'none';
                    });
                }
            });
        });
        
        // Cerrar menú al hacer click fuera
        document.addEventListener('click', function(e) {
            const navbarNav = document.querySelector('.navbar-nav');
            const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
            
            if (navbarNav && navbarNav.classList.contains('mobile-open')) {
                // Si el click no fue en el menú ni en el botón toggle
                if (!e.target.closest('.navbar-nav') && !e.target.closest('.mobile-menu-toggle')) {
                    navbarNav.classList.remove('mobile-open');
                }
            }
        });
        
        // Cerrar búsqueda móvil al hacer click fuera
        document.addEventListener('click', function(e) {
            const searchContainer = document.querySelector('.search-container.mobile-search');
            const mobileSearchToggle = document.querySelector('.mobile-search-toggle');
            
            if (searchContainer && searchContainer.classList.contains('mobile-active')) {
                // Si el click no fue en la búsqueda ni en el botón toggle
                if (!e.target.closest('.search-container.mobile-search') && !e.target.closest('.mobile-search-toggle')) {
                    searchContainer.classList.remove('mobile-active');
                }
            }
        });
    </script>
    
    <script>
    // Auto-refresh CSRF token to prevent 419 errors
    function refreshCSRFToken() {
        fetch('/refresh-csrf')
            .then(response => response.json())
            .then(data => {
                document.querySelector('meta[name="csrf-token"]').setAttribute('content', data.csrf_token);
                // Update all CSRF inputs in forms
                document.querySelectorAll('input[name="_token"]').forEach(input => {
                    input.value = data.csrf_token;
                });
            })
            .catch(error => console.error('Error refreshing CSRF token:', error));
    }

    // Refresh token every 30 minutes
    setInterval(refreshCSRFToken, 30 * 60 * 1000);

    // Refresh token on page visibility change
    document.addEventListener('visibilitychange', function() {
        if (!document.hidden) {
            refreshCSRFToken();
        }
    });
    </script>
    
    <!-- PWA Script -->
    <script src="/js/pwa.js"></script>
    
    <!-- PWA User ID for notifications -->
    @auth
    <meta name="user-id" content="{{ Auth::id() }}">
    <script>
        // Store user ID for PWA
        localStorage.setItem('dorasia_user_id', '{{ Auth::id() }}');
    </script>
    @endauth
</body>
</html>