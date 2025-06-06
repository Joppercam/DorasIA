<?php

return [
    'api_key' => env('TMDB_API_KEY'),
    'base_url' => 'https://api.themoviedb.org/3',
    'image_base_url' => 'https://image.tmdb.org/t/p',
    'poster_sizes' => ['w92', 'w154', 'w185', 'w342', 'w500', 'w780', 'original'],
    'backdrop_sizes' => ['w300', 'w780', 'w1280', 'original'],
    'profile_sizes' => ['w45', 'w185', 'h632', 'original'],
    'timeout' => 30,
];