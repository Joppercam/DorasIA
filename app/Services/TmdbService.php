<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TmdbService
{
    private string $apiKey;
    private string $baseUrl;
    private string $imageBaseUrl;

    public function __construct()
    {
        $this->apiKey = config('tmdb.api_key');
        $this->baseUrl = config('tmdb.base_url');
        $this->imageBaseUrl = config('tmdb.image_base_url');
    }

    /**
     * Search for Korean dramas/TV shows
     */
    public function searchKoreanDramas(string $query = '', int $page = 1): array
    {
        $endpoint = '/search/tv';
        
        $params = [
            'api_key' => $this->apiKey,
            'query' => $query,
            'page' => $page,
            'language' => 'es-ES',
            'with_origin_country' => 'KR'
        ];

        return $this->makeRequest($endpoint, $params);
    }

    /**
     * Discover Korean dramas with filters
     */
    public function discoverKoreanDramas(array $filters = []): array
    {
        $endpoint = '/discover/tv';
        
        $defaultParams = [
            'api_key' => $this->apiKey,
            'with_origin_country' => 'KR',
            'language' => 'es-ES',
            'sort_by' => 'popularity.desc',
            'page' => 1
        ];

        $params = array_merge($defaultParams, $filters);

        return $this->makeRequest($endpoint, $params);
    }

    /**
     * Get detailed TV show information
     */
    public function getTvShowDetails(int $tvId): array
    {
        $endpoint = "/tv/{$tvId}";
        
        $params = [
            'api_key' => $this->apiKey,
            'language' => 'es-ES',
            'append_to_response' => 'credits,images,videos,keywords,similar,recommendations,external_ids'
        ];

        return $this->makeRequest($endpoint, $params);
    }

    /**
     * Get season details
     */
    public function getSeasonDetails(int $tvId, int $seasonNumber): array
    {
        $endpoint = "/tv/{$tvId}/season/{$seasonNumber}";
        
        $params = [
            'api_key' => $this->apiKey,
            'language' => 'es-ES',
            'append_to_response' => 'credits,images,videos'
        ];

        return $this->makeRequest($endpoint, $params);
    }

    /**
     * Get episode details
     */
    public function getEpisodeDetails(int $tvId, int $seasonNumber, int $episodeNumber): array
    {
        $endpoint = "/tv/{$tvId}/season/{$seasonNumber}/episode/{$episodeNumber}";
        
        $params = [
            'api_key' => $this->apiKey,
            'language' => 'es-ES',
            'append_to_response' => 'credits,images,videos'
        ];

        return $this->makeRequest($endpoint, $params);
    }

    /**
     * Get person details
     */
    public function getPersonDetails(int $personId): array
    {
        $endpoint = "/person/{$personId}";
        
        $params = [
            'api_key' => $this->apiKey,
            'language' => 'es-ES',
            'append_to_response' => 'tv_credits,movie_credits,images,external_ids'
        ];

        return $this->makeRequest($endpoint, $params);
    }

    /**
     * Get images for a TV show
     */
    public function getTvImages(int $tvId): array
    {
        $endpoint = "/tv/{$tvId}/images";
        
        $params = [
            'api_key' => $this->apiKey
        ];

        return $this->makeRequest($endpoint, $params);
    }

    /**
     * Get videos/trailers for a TV show
     */
    public function getTvVideos(int $tvId): array
    {
        $endpoint = "/tv/{$tvId}/videos";
        
        $params = [
            'api_key' => $this->apiKey,
            'language' => 'en-US'
        ];

        return $this->makeRequest($endpoint, $params);
    }

    /**
     * Get TV genres
     */
    public function getTvGenres(): array
    {
        $endpoint = '/genre/tv/list';
        
        $params = [
            'api_key' => $this->apiKey,
            'language' => 'en-US'
        ];

        return $this->makeRequest($endpoint, $params);
    }

    /**
     * Get upcoming TV shows with date filters
     */
    public function getUpcomingTvShows(int $page = 1, string $startDate = null, string $endDate = null): array
    {
        $endpoint = "/discover/tv";
        
        $params = [
            'api_key' => $this->apiKey,
            'language' => 'es-ES',
            'sort_by' => 'first_air_date.desc',
            'page' => $page,
            'with_original_language' => 'ko', // Solo series coreanas
            'with_origin_country' => 'KR'
        ];
        
        if ($startDate) {
            $params['first_air_date.gte'] = $startDate;
        }
        
        if ($endDate) {
            $params['first_air_date.lte'] = $endDate;
        }

        return $this->makeRequest($endpoint, $params);
    }

    /**
     * Search for Korean TV shows
     */
    public function searchKoreanTvShows(string $query, int $page = 1): array
    {
        $endpoint = "/search/tv";
        
        $params = [
            'api_key' => $this->apiKey,
            'language' => 'es-ES',
            'query' => $query,
            'page' => $page,
            'with_original_language' => 'ko'
        ];

        return $this->makeRequest($endpoint, $params);
    }

    /**
     * Get trending Korean TV shows
     */
    public function getTrendingKoreanTvShows(string $timeWindow = 'week'): array
    {
        $endpoint = "/trending/tv/{$timeWindow}";
        
        $params = [
            'api_key' => $this->apiKey,
            'language' => 'es-ES'
        ];
        
        $response = $this->makeRequest($endpoint, $params);
        
        // Filter only Korean shows
        if (isset($response['results'])) {
            $response['results'] = array_filter($response['results'], function($show) {
                $originCountry = $show['origin_country'] ?? [];
                $originalLanguage = $show['original_language'] ?? '';
                return in_array('KR', $originCountry) || $originalLanguage === 'ko';
            });
        }
        
        return $response;
    }

    /**
     * Build full image URL
     */
    public function getImageUrl(string $path, string $size = 'original'): string
    {
        return $this->imageBaseUrl . '/' . $size . $path;
    }

    /**
     * Make HTTP request to TMDB API
     */
    private function makeRequest(string $endpoint, array $params = []): array
    {
        try {
            $response = Http::timeout(config('tmdb.timeout', 30))
                ->get($this->baseUrl . $endpoint, $params);

            if ($response->successful()) {
                return $response->json() ?? [];
            }

            Log::error('TMDB API Error', [
                'endpoint' => $endpoint,
                'params' => $params,
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return [];

        } catch (\Exception $e) {
            Log::error('TMDB API Exception', [
                'endpoint' => $endpoint,
                'params' => $params,
                'error' => $e->getMessage()
            ]);

            return [];
        }
    }

    /**
     * Get popular Korean dramas by page
     */
    public function getPopularKoreanDramas(int $page = 1): array
    {
        return $this->discoverKoreanDramas([
            'page' => $page,
            'sort_by' => 'popularity.desc',
            'vote_count.gte' => 10
        ]);
    }

    /**
     * Get top rated Korean dramas
     */
    public function getTopRatedKoreanDramas(int $page = 1): array
    {
        return $this->discoverKoreanDramas([
            'page' => $page,
            'sort_by' => 'vote_average.desc',
            'vote_count.gte' => 50
        ]);
    }

    /**
     * Get recently aired Korean dramas
     */
    public function getRecentKoreanDramas(int $page = 1): array
    {
        $today = now()->format('Y-m-d');
        $threeMonthsAgo = now()->subMonths(3)->format('Y-m-d');

        return $this->discoverKoreanDramas([
            'page' => $page,
            'sort_by' => 'first_air_date.desc',
            'first_air_date.lte' => $today,
            'first_air_date.gte' => $threeMonthsAgo
        ]);
    }

    /**
     * Get series credits (cast and crew)
     */
    public function getSeriesCredits(int $seriesId): array
    {
        $endpoint = "/tv/{$seriesId}/credits";
        
        $params = [
            'api_key' => $this->apiKey,
            'language' => 'en-US'
        ];

        return $this->makeRequest($endpoint, $params);
    }


    /**
     * Get person combined credits (movies and TV)
     */
    public function getPersonCombinedCredits(int $personId): array
    {
        $endpoint = "/person/{$personId}/combined_credits";
        
        $params = [
            'api_key' => $this->apiKey,
            'language' => 'en-US'
        ];

        return $this->makeRequest($endpoint, $params);
    }

    /**
     * Get person images
     */
    public function getPersonImages(int $personId): array
    {
        $endpoint = "/person/{$personId}/images";
        
        $params = [
            'api_key' => $this->apiKey
        ];

        return $this->makeRequest($endpoint, $params);
    }

    /**
     * Get series reviews
     */
    public function getSeriesReviews(int $seriesId, int $page = 1): array
    {
        $endpoint = "/tv/{$seriesId}/reviews";
        
        $params = [
            'api_key' => $this->apiKey,
            'language' => 'es-ES',
            'page' => $page
        ];

        return $this->makeRequest($endpoint, $params);
    }

    /**
     * Get series external IDs (IMDB, etc)
     */
    public function getSeriesExternalIds(int $seriesId): array
    {
        $endpoint = "/tv/{$seriesId}/external_ids";
        
        $params = [
            'api_key' => $this->apiKey
        ];

        return $this->makeRequest($endpoint, $params);
    }

    /**
     * Generic GET method for any TMDB endpoint
     */
    public function get(string $endpoint, array $params = []): array
    {
        $defaultParams = [
            'api_key' => $this->apiKey,
            'language' => 'en-US'
        ];

        $params = array_merge($defaultParams, $params);

        return $this->makeRequest($endpoint, $params);
    }
}