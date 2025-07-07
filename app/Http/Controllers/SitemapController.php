<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Series;
use App\Models\Movie;
use App\Models\Person;
use Carbon\Carbon;

class SitemapController extends Controller
{
    public function index()
    {
        $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        // Página principal
        $sitemap .= $this->addUrl(url('/'), '1.0', 'daily', now());

        // Páginas estáticas
        $sitemap .= $this->addUrl(url('/peliculas'), '0.9', 'daily', now());
        $sitemap .= $this->addUrl(url('/actores'), '0.8', 'weekly', now());

        // Series
        $series = Series::select(['id', 'updated_at'])->get();
        foreach ($series as $serie) {
            $sitemap .= $this->addUrl(
                url("/series/{$serie->id}"), 
                '0.8', 
                'weekly', 
                $serie->updated_at
            );
        }

        // Películas
        $movies = Movie::select(['id', 'updated_at'])->get();
        foreach ($movies as $movie) {
            $sitemap .= $this->addUrl(
                url("/peliculas/{$movie->id}"), 
                '0.8', 
                'weekly', 
                $movie->updated_at
            );
        }

        // Actores (top 100 más populares)
        $actors = Person::select(['id', 'updated_at'])
                        ->orderBy('popularity', 'desc')
                        ->take(100)
                        ->get();
        foreach ($actors as $actor) {
            $sitemap .= $this->addUrl(
                url("/actor/{$actor->id}"), 
                '0.6', 
                'monthly', 
                $actor->updated_at
            );
        }

        $sitemap .= '</urlset>';

        return response($sitemap)
            ->header('Content-Type', 'application/xml')
            ->header('Cache-Control', 'public, max-age=3600'); // Cache por 1 hora
    }

    private function addUrl($url, $priority, $changefreq, $lastmod)
    {
        $xml = "  <url>\n";
        $xml .= "    <loc>" . htmlspecialchars($url) . "</loc>\n";
        $xml .= "    <lastmod>" . $lastmod->format('Y-m-d\TH:i:s+00:00') . "</lastmod>\n";
        $xml .= "    <changefreq>{$changefreq}</changefreq>\n";
        $xml .= "    <priority>{$priority}</priority>\n";
        $xml .= "  </url>\n";
        
        return $xml;
    }
}