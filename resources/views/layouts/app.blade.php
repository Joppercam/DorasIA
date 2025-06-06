<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

        .hero-info-box {
            background: linear-gradient(135deg, 
                rgba(0, 0, 0, 0.15) 0%, 
                rgba(0, 0, 0, 0.35) 50%, 
                rgba(0, 0, 0, 0.5) 100%);
            backdrop-filter: blur(15px);
            padding: 3rem;
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        }

        .hero-categories {
            display: flex;
            gap: 0.8rem;
            margin-bottom: 1.5rem;
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
            margin-bottom: 1.5rem;
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
            color: rgba(255, 255, 255, 0.8);
            font-weight: 500;
            font-size: 0.9rem;
        }

        .hero-original-title {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 1rem;
            font-style: italic;
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
            margin-top: 2rem;
            padding: 1.5rem 0;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        .hero-actions .card-rating-buttons {
            position: static;
            transform: none;
            opacity: 1;
            gap: 1rem;
        }

        .hero-actions .rating-btn {
            opacity: 1 !important;
            transform: scale(1) !important;
            width: 40px;
            height: 40px;
            border-width: 2px;
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

        .hero-actions::before {
            content: '🎬 Acciones:';
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
            font-weight: 600;
            margin-right: 0.5rem;
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

        .cast-card {
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
            margin-bottom: 1rem;
            line-height: 1.1;
        }

        .hero-description {
            font-size: 1rem;
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
            min-width: 220px;
            height: 320px;
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
            transform: scale(1.7) translateY(-40px);
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
                rgba(0,0,0,0.8) 0%, 
                transparent 25%, 
                transparent 50%, 
                rgba(0,0,0,0.7) 70%, 
                rgba(0,0,0,0.95) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .card-categories {
            position: absolute;
            top: 0.5rem;
            left: 0.5rem;
            right: 0.5rem;
            z-index: 10;
            opacity: 0;
            transform: translateY(-10px);
            transition: all 0.3s ease;
        }

        .card:hover .card-categories {
            opacity: 1;
            transform: translateY(0);
        }

        .card-category {
            display: inline-block;
            background: rgba(0, 212, 255, 0.9);
            color: white;
            padding: 0.2rem 0.5rem;
            border-radius: 12px;
            font-size: 0.6rem;
            font-weight: 600;
            margin-right: 0.3rem;
            margin-bottom: 0.3rem;
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

        .card-info {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0,0,0,0.95);
            padding: 0.8rem;
            color: white;
            opacity: 0;
            transform: translateY(50%);
            transition: all 0.3s ease;
            min-height: 120px;
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
            display: inline-flex;
            align-items: center;
            gap: 0.1rem;
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

        /* Footer Styles */
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
        
        .card:hover .card-action-btn {
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
        
        /* Remove card click */
        .card {
            cursor: default;
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

        /* Rating Buttons Styles */
        .card-rating-buttons {
            position: absolute;
            bottom: 50%;
            left: 50%;
            transform: translate(-50%, 50%);
            display: flex;
            gap: 0.5rem;
            opacity: 0;
            transition: all 0.3s ease;
            z-index: 15;
        }

        .card:hover .card-rating-buttons {
            opacity: 1;
        }

        .rating-btn {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 2px solid rgba(255,255,255,0.4);
            background: rgba(0,0,0,0.9);
            backdrop-filter: blur(10px);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            opacity: 0;
            transform: scale(0.8);
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
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 2px solid rgba(255,255,255,0.3);
            background: rgba(20,20,20,0.8);
            backdrop-filter: blur(10px);
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
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
            .hero-title {
                font-size: 1.8rem;
            }
            
            .hero-description {
                font-size: 0.85rem;
                line-height: 1.4;
            }
            
            .hero-buttons {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .btn {
                width: 100%;
                text-align: center;
                font-size: 0.9rem;
                padding: 0.8rem 1.5rem;
            }
            
            .navbar {
                padding: 0.8rem 3%;
            }
            
            .navbar-brand {
                font-size: 1.5rem;
            }
            
            .navbar-nav {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: rgba(20,20,20,0.98);
                backdrop-filter: blur(10px);
                flex-direction: column;
                padding: 1rem;
                gap: 0.5rem;
                border-top: 1px solid rgba(255,255,255,0.1);
            }
            
            .navbar-nav.mobile-open {
                display: flex;
            }
            
            /* Mobile carousel improvements */
            .carousel {
                gap: 0.5rem;
                padding: 0 1rem;
            }
            
            .card {
                width: 140px;
                height: 210px;
            }
            
            .card-info {
                padding: 0.8rem;
                min-height: 100px;
            }
            
            .card-title {
                font-size: 0.85rem;
                margin-bottom: 0.3rem;
            }
            
            .card-meta {
                font-size: 0.65rem;
                margin-bottom: 0.5rem;
            }
            
            .card-cast {
                font-size: 0.65rem;
            }
            
            .actor-images {
                margin-bottom: 0.3rem;
            }
            
            .card-streaming-title {
                font-size: 0.7rem;
            }
            
            .streaming-platform {
                font-size: 0.55rem;
                padding: 0.15rem 0.4rem;
            }
            
            .section-title {
                font-size: 1.2rem;
                padding: 0 1rem;
            }
            
            .content-section {
                margin-bottom: 2rem;
            }
            
            /* Mobile footer */
            .footer-content {
                grid-template-columns: repeat(2, 1fr);
                gap: 1.5rem;
                padding: 0 1rem;
            }
            
            .footer-section h4 {
                font-size: 0.9rem;
            }
            
            .footer-section {
                font-size: 0.8rem;
            }
            
            .footer-bottom {
                font-size: 0.75rem;
                padding: 1.5rem 1rem;
            }
            
            /* Mobile action button */
            .card-action-btn {
                width: 28px;
                height: 28px;
                bottom: 2rem;
                right: 0.6rem;
            }
            
            .card-action-btn svg {
                width: 14px;
                height: 14px;
            }
            
            /* Mobile news carousel */
            .news-carousel {
                margin: 1rem 0;
                padding: 1rem;
            }
            
            .news-carousel .carousel-container {
                margin: 0 20px;
                padding: 30px 0;
            }
            
            .news-card {
                width: 250px;
                min-width: 250px;
                max-width: 250px;
                height: 160px;
            }
            
            .news-card-title {
                font-size: 0.8rem;
            }
            
            .news-card-excerpt {
                font-size: 0.65rem;
            }
            
            .news-section-title {
                font-size: 1.2rem;
            }
            
            .mobile-menu-toggle, .mobile-search-toggle {
                display: block;
                background: none;
                border: none;
                color: white;
                font-size: 1.5rem;
                cursor: pointer;
                padding: 0.5rem;
            }
            
            .search-container {
                display: none;
                position: fixed;
                top: 70px;
                left: 0;
                right: 0;
                padding: 1rem;
                background: rgba(20, 20, 20, 0.95);
                backdrop-filter: blur(10px);
                z-index: 1500;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            }
            
            .search-container.mobile-active {
                display: block;
            }
            
            .content-section {
                padding: 0 2%;
            }
            
            .hero-content {
                padding: 0 2%;
            }
            
            .hero-info-box {
                padding: 2rem;
                border-radius: 20px;
            }
            
            .hero-categories {
                gap: 0.5rem;
                margin-bottom: 1rem;
            }
            
            .hero-category {
                padding: 0.3rem 0.8rem;
                font-size: 0.7rem;
                border-radius: 15px;
            }
            
            .hero-meta {
                gap: 1.5rem;
                margin-bottom: 1rem;
            }
            
            .hero-rating {
                padding: 0.4rem 0.8rem;
                border-radius: 15px;
            }
            
            .btn-hero {
                padding: 0.8rem 2rem;
                border-radius: 20px;
                font-size: 0.9rem;
            }
            
            .hero-actions {
                margin-top: 1.5rem;
                flex-wrap: wrap;
                justify-content: center;
                gap: 1.5rem;
                padding: 1rem 0;
            }
            
            .hero-actions::before {
                content: '🎬';
                margin-right: 0.3rem;
            }
            
            .hero-actions .rating-btn {
                width: 36px;
                height: 36px;
            }
            
            .hero-actions .watchlist-btn {
                width: 36px;
                height: 36px;
            }
            
            .series-detail-container {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
            
            .series-poster {
                position: static;
                max-width: 280px;
                margin: 0 auto;
            }
            
            .detail-section {
                padding: 1.5rem;
                border-radius: 12px;
            }
            
            .detail-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .detail-genre-tag {
                padding: 0.4rem 1rem;
                font-size: 0.8rem;
            }
            
            .carousel-container {
                margin: 0 20px;
                padding: 30px 0;
            }
            
            .card:hover {
                transform: scale(1.4) translateY(-35px);
            }
        }
        
        /* Mobile Controls */
        .mobile-controls {
            display: none;
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
            z-index: 1500;
        }
        
        @media (max-width: 768px) {
            .mobile-controls {
                display: flex;
            }
            
            .desktop-search {
                display: none;
            }
            
            .navbar-nav {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: rgba(20,20,20,0.98);
                backdrop-filter: blur(10px);
                flex-direction: column;
                padding: 1rem;
                gap: 0.5rem;
                border-top: 1px solid rgba(255,255,255,0.1);
                z-index: 1400;
            }
            
            .navbar-nav.mobile-open {
                display: flex;
            }
            
            .dropdown-menu {
                position: static;
                display: none;
                background: rgba(40, 40, 40, 0.95);
                margin: 0.5rem 0;
                border-radius: 8px;
                box-shadow: none;
            }
            
            .dropdown:hover .dropdown-menu {
                display: block;
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
    </style>
</head>
<body>
    <nav class="navbar" id="navbar">
        <div style="display: flex; align-items: center; justify-content: space-between; width: 100%; max-width: 1200px; margin: 0 auto;">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="navbar-brand">DORAS<span class="ai-highlight">IA</span></a>
            
            <!-- Desktop Search Bar -->
            <div class="search-container desktop-search" style="flex: 1; max-width: 400px; margin: 0 2rem;">
                <div class="search-input-container">
                    <input type="text" id="globalSearch" placeholder="Buscar series, actores..." class="search-input">
                    <div class="search-icon">🔍</div>
                </div>
                <div class="search-results" id="searchResults" style="display: none;"></div>
            </div>
            
            <!-- Mobile Toggle Buttons -->
            <div class="mobile-controls" style="display: flex; gap: 0.5rem;">
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
                <li><a href="{{ route('actors.index') }}">Actores</a></li>
                
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
                            <form action="{{ route('logout') }}" method="POST" style="margin: 0;">
                                @csrf
                                <button type="submit" style="background: none; border: none; color: white; padding: 0.5rem 1rem; width: 100%; text-align: left; cursor: pointer; transition: background-color 0.3s;">
                                    Cerrar Sesión
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
                @else
                <li><a href="{{ route('login') }}">Iniciar Sesión</a></li>
                <li><a href="{{ route('register') }}" class="register-btn">Registrarse</a></li>
                @endauth
            </ul>
        </div>
        
        <!-- Mobile Search Container -->
        <div class="search-container mobile-search" id="mobileSearchContainer" style="display: none;">
            <div class="search-input-container">
                <input type="text" id="mobileGlobalSearch" placeholder="Buscar series, actores..." class="search-input">
                <div class="search-icon">🔍</div>
            </div>
            <div class="search-results" id="mobileSearchResults" style="display: none;"></div>
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
            
            if (mobileSearchContainer.style.display === 'block') {
                mobileSearchContainer.style.display = 'none';
            } else {
                mobileSearchContainer.style.display = 'block';
                // Close mobile menu if open
                mobileNav.classList.remove('mobile-open');
                
                // Focus on mobile search input
                const mobileSearchInput = document.getElementById('mobileGlobalSearch');
                setTimeout(() => {
                    mobileSearchInput.focus();
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
            fetch(`/series/${seriesId}/rate`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({
                    rating_type: ratingType
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update button states
                    const card = button.closest('.card');
                    const ratingButtons = card.querySelectorAll('.rating-btn');
                    
                    // Remove active class from all buttons
                    ratingButtons.forEach(btn => btn.classList.remove('active'));
                    
                    // Add active class to current button
                    button.classList.add('active');
                    
                    // Show toast notification
                    showRatingToast(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showRatingToast('Error al guardar calificación', 'error');
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
                        
                        const displayTitle = series.spanish_title || series.display_title || series.title;
                        html += `
                            <a href="/series/${series.id}" class="search-item">
                                <img src="${posterUrl}" alt="${displayTitle}" class="search-item-image" onerror="this.src='/images/placeholder-poster.jpg'">
                                <div class="search-item-info">
                                    <div class="search-item-title">${displayTitle}</div>
                                    <div class="search-item-meta">${year} • ⭐ ${series.vote_average || 'N/A'}</div>
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
    <footer class="footer">
        <div class="footer-content">
            <div class="footer-section">
                <h3 class="footer-logo">DORAS<span class="ai-highlight">[IA]</span></h3>
                <p>Tu portal de K-Dramas con IA</p>
            </div>
            <div class="footer-section">
                <h4>Enlaces</h4>
                <ul>
                    <li><a href="/">Inicio</a></li>
                    <li><a href="/categories">Categorías</a></li>
                    <li><a href="/top-rated">Mejor Calificados</a></li>
                    <li><a href="/recent">Recientes</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Información</h4>
                <ul>
                    <li><a href="/about">Acerca de</a></li>
                    <li><a href="/contact">Contacto</a></li>
                    <li><a href="/privacy">Privacidad</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Síguenos</h4>
                <p>Pronto en redes sociales</p>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 DORASIA. Todos los derechos reservados. | Hecho con ❤️ para fans chilenos de K-Dramas</p>
        </div>
    </footer>
</body>
</html>