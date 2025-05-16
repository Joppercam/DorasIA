<?php

return [
    /*
    |--------------------------------------------------------------------------
    | TMDB API Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for The Movie Database (TMDB) API
    | which is used to fetch information about movies and TV shows.
    |
    */

    // API Key and Access
    'api_key' => env('TMDB_API_KEY', ''),
    'base_url' => 'https://api.themoviedb.org/3',
    'image_base_url' => 'https://image.tmdb.org/t/p/',
    
    // Image Sizes
    'poster_sizes' => [
        'small' => 'w185',
        'medium' => 'w342',
        'large' => 'w500',
        'original' => 'original',
    ],
    
    'backdrop_sizes' => [
        'small' => 'w300',
        'medium' => 'w780',
        'large' => 'w1280',
        'original' => 'original',
    ],
    
    'profile_sizes' => [
        'small' => 'w45',
        'medium' => 'w185',
        'large' => 'h632',
        'original' => 'original',
    ],
    
    // Content Types
    'content_types' => [
        'movie' => 'movie',
        'tv' => 'tv',
    ],
    
    // Languages for Asian Content
    'languages' => [
        'korean' => 'ko',
        'japanese' => 'ja',
        'chinese' => 'zh',
        'thai' => 'th',
        'taiwanese' => 'zh-TW',
        'cantonese' => 'cn',
        'vietnamese' => 'vi',
        'indonesian' => 'id',
        'filipino' => 'tl',
    ],
    
    // Countries/Regions
    'regions' => [
        'south_korea' => 'KR',
        'japan' => 'JP',
        'china' => 'CN',
        'taiwan' => 'TW',
        'hong_kong' => 'HK',
        'thailand' => 'TH',
        'vietnam' => 'VN',
        'indonesia' => 'ID',
        'philippines' => 'PH',
    ],
    
    // Genre IDs
    'genres' => [
        'action' => 28,
        'adventure' => 12,
        'animation' => 16,
        'comedy' => 35,
        'crime' => 80,
        'documentary' => 99,
        'drama' => 18,
        'family' => 10751,
        'fantasy' => 14,
        'history' => 36,
        'horror' => 27,
        'music' => 10402,
        'mystery' => 9648,
        'romance' => 10749,
        'science_fiction' => 878,
        'thriller' => 53,
        'war' => 10752,
        'western' => 37,
    ],
    
    // TV Show Genres
    'tv_genres' => [
        'action_adventure' => 10759,
        'animation' => 16,
        'comedy' => 35,
        'crime' => 80,
        'documentary' => 99,
        'drama' => 18,
        'family' => 10751,
        'kids' => 10762,
        'mystery' => 9648,
        'news' => 10763,
        'reality' => 10764,
        'sci_fi_fantasy' => 10765,
        'soap' => 10766,
        'talk' => 10767,
        'war_politics' => 10768,
        'western' => 37,
    ],
    
    // Romance Subgenres (Custom for Dorasia)
    'romance_subgenres' => [
        'historical_romance' => [
            'name' => 'Historical Romance',
            'keywords' => ['historical', 'palace', 'dynasty', 'joseon', 'edo', 'qing', 'ming'],
        ],
        'romantic_comedy' => [
            'name' => 'Romantic Comedy',
            'keywords' => ['comedy', 'funny', 'light-hearted', 'rom-com'],
        ],
        'melodrama' => [
            'name' => 'Melodrama',
            'keywords' => ['melodrama', 'tearjerker', 'tragedy', 'emotional'],
        ],
        'supernatural_romance' => [
            'name' => 'Supernatural Romance',
            'keywords' => ['fantasy', 'supernatural', 'immortal', 'ghost', 'magical'],
        ],
        'medical_romance' => [
            'name' => 'Medical Romance',
            'keywords' => ['hospital', 'doctor', 'medical', 'nurse', 'surgery'],
        ],
        'office_romance' => [
            'name' => 'Office Romance',
            'keywords' => ['office', 'workplace', 'company', 'corporate', 'boss'],
        ],
        'youth_romance' => [
            'name' => 'Youth/Coming of Age Romance',
            'keywords' => ['youth', 'coming of age', 'school', 'college', 'university', 'teenager'],
        ],
        'family_romance' => [
            'name' => 'Family Romance',
            'keywords' => ['family', 'marriage', 'couple', 'parenting', 'relationship'],
        ],
    ],
    
    // API Endpoints
    'endpoints' => [
        'search_movie' => '/search/movie',
        'search_tv' => '/search/tv',
        'movie_details' => '/movie/{movie_id}',
        'tv_details' => '/tv/{tv_id}',
        'movie_credits' => '/movie/{movie_id}/credits',
        'tv_credits' => '/tv/{tv_id}/credits',
        'person_details' => '/person/{person_id}',
        'person_credits' => '/person/{person_id}/combined_credits',
        'discover_movie' => '/discover/movie',
        'discover_tv' => '/discover/tv',
        'season_details' => '/tv/{tv_id}/season/{season_number}',
        'episode_details' => '/tv/{tv_id}/season/{season_number}/episode/{episode_number}',
        'movie_images' => '/movie/{movie_id}/images',
        'tv_images' => '/tv/{tv_id}/images',
        'movie_videos' => '/movie/{movie_id}/videos',
        'tv_videos' => '/tv/{tv_id}/videos',
        'movie_recommendations' => '/movie/{movie_id}/recommendations',
        'tv_recommendations' => '/tv/{tv_id}/recommendations',
        'movie_keywords' => '/movie/{movie_id}/keywords',
        'tv_keywords' => '/tv/{tv_id}/keywords',
    ],
    
    // Cache Configuration
    'cache' => [
        'enabled' => true,
        'ttl' => 60 * 24, // 1 day in minutes
    ],
    
    // Request Configuration
    'request' => [
        'timeout' => 10, // seconds
        'retries' => 3,
    ],
    
    // Default Parameters for API Requests
    'default_params' => [
        'include_adult' => false,
        'include_video' => true,
        'append_to_response' => 'videos,images,credits,similar,recommendations,keywords',
    ],
];