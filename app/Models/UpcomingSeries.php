<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class UpcomingSeries extends Model
{
    use HasFactory;

    protected $fillable = [
        'tmdb_id',
        'title',
        'spanish_title',
        'overview',
        'spanish_overview',
        'poster_path',
        'backdrop_path',
        'release_date',
        'type',
        'season_number',
        'episode_count',
        'vote_average',
        'popularity',
        'status',
        'existing_series_id'
    ];

    protected $casts = [
        'release_date' => 'date',
        'vote_average' => 'decimal:1',
        'popularity' => 'decimal:3'
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName()
    {
        return 'id';
    }

    /**
     * Relación con la serie existente (si es una nueva temporada)
     */
    public function existingSeries()
    {
        return $this->belongsTo(Series::class, 'existing_series_id');
    }

    /**
     * Scope para obtener solo series nuevas
     */
    public function scopeNewSeries($query)
    {
        return $query->where('type', 'new_series');
    }

    /**
     * Scope para obtener solo nuevas temporadas
     */
    public function scopeNewSeasons($query)
    {
        return $query->where('type', 'new_season');
    }

    /**
     * Scope para obtener solo próximos estrenos
     */
    public function scopeUpcoming($query)
    {
        return $query->where('status', 'upcoming')
                    ->where('release_date', '>=', Carbon::now());
    }

    /**
     * Scope para obtener estrenos de este mes
     */
    public function scopeThisMonth($query)
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        
        return $query->whereBetween('release_date', [$startOfMonth, $endOfMonth]);
    }

    /**
     * Scope para obtener estrenos del próximo mes
     */
    public function scopeNextMonth($query)
    {
        $startOfNextMonth = Carbon::now()->addMonth()->startOfMonth();
        $endOfNextMonth = Carbon::now()->addMonth()->endOfMonth();
        
        return $query->whereBetween('release_date', [$startOfNextMonth, $endOfNextMonth]);
    }

    /**
     * Obtener el título para mostrar (priorizar español)
     */
    public function getDisplayTitleAttribute()
    {
        return $this->spanish_title ?: $this->title;
    }

    /**
     * Obtener la sinopsis para mostrar (priorizar español)
     */
    public function getDisplayOverviewAttribute()
    {
        return $this->spanish_overview ?: $this->overview;
    }

    /**
     * Obtener URL del poster
     */
    public function getPosterUrlAttribute()
    {
        if (!$this->poster_path) {
            return 'https://via.placeholder.com/500x750/333/666?text=K-Drama';
        }
        
        return 'https://image.tmdb.org/t/p/w500' . $this->poster_path;
    }

    /**
     * Obtener URL del backdrop
     */
    public function getBackdropUrlAttribute()
    {
        if (!$this->backdrop_path) {
            return 'https://via.placeholder.com/1920x1080/333/666?text=K-Drama';
        }
        
        return 'https://image.tmdb.org/t/p/original' . $this->backdrop_path;
    }

    /**
     * Verificar si es una serie nueva
     */
    public function isNewSeries()
    {
        return $this->type === 'new_series';
    }

    /**
     * Verificar si es una nueva temporada
     */
    public function isNewSeason()
    {
        return $this->type === 'new_season';
    }

    /**
     * Obtener días hasta el estreno
     */
    public function getDaysUntilReleaseAttribute()
    {
        if (!$this->release_date) {
            return null;
        }

        $today = Carbon::now()->startOfDay();
        $releaseDate = Carbon::parse($this->release_date)->startOfDay();
        
        if ($releaseDate->isPast()) {
            return 0;
        }
        
        return $today->diffInDays($releaseDate);
    }

    /**
     * Obtener texto formateado de fecha de estreno
     */
    public function getFormattedReleaseDateAttribute()
    {
        if (!$this->release_date) {
            return 'Fecha por confirmar';
        }

        $releaseDate = Carbon::parse($this->release_date);
        $now = Carbon::now();
        
        if ($releaseDate->isToday()) {
            return '¡Hoy!';
        } elseif ($releaseDate->isTomorrow()) {
            return 'Mañana';
        } elseif ($releaseDate->isCurrentWeek()) {
            // Traducir días de la semana manualmente
            $dayNames = [
                'Monday' => 'Lunes',
                'Tuesday' => 'Martes', 
                'Wednesday' => 'Miércoles',
                'Thursday' => 'Jueves',
                'Friday' => 'Viernes',
                'Saturday' => 'Sábado',
                'Sunday' => 'Domingo'
            ];
            return $dayNames[$releaseDate->format('l')] ?? $releaseDate->format('l');
        } elseif ($releaseDate->isCurrentMonth()) {
            return $this->formatSpanishDate($releaseDate); // 15 de enero
        } elseif ($releaseDate->isCurrentYear()) {
            return $this->formatSpanishDate($releaseDate); // 15 de enero
        } else {
            return $this->formatSpanishDate($releaseDate, true); // 15 de enero, 2025
        }
    }

    /**
     * Formatear fecha en español
     */
    private function formatSpanishDate($date, $includeYear = false)
    {
        $monthNames = [
            1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
            5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
            9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
        ];
        
        $day = $date->format('j');
        $month = $monthNames[$date->format('n')];
        $year = $date->format('Y');
        
        if ($includeYear) {
            return "{$day} de {$month}, {$year}";
        }
        
        return "{$day} de {$month}";
    }

    /**
     * Obtener tipo de contenido formateado
     */
    public function getFormattedTypeAttribute()
    {
        if ($this->isNewSeries()) {
            return 'Nueva Serie';
        } elseif ($this->isNewSeason()) {
            return "Temporada {$this->season_number}";
        }
        
        return 'Próximo Estreno';
    }

    /**
     * Obtener icono según el tipo
     */
    public function getTypeIconAttribute()
    {
        if ($this->isNewSeries()) {
            return '🆕';
        } elseif ($this->isNewSeason()) {
            return '🔄';
        }
        
        return '📺';
    }

    /**
     * Marcar como estrenado (convertir a serie normal)
     */
    public function markAsReleased()
    {
        $this->update(['status' => 'released']);
        
        // Si es una serie nueva, crear entrada en la tabla principal
        if ($this->isNewSeries()) {
            // Aquí se podría implementar la lógica para importar la serie completa
            // desde TMDB a la tabla series principal
        }
    }

    /**
     * Verificar si ya está disponible para ver
     */
    public function isAvailableNow()
    {
        return $this->release_date && Carbon::parse($this->release_date)->isPast();
    }

    /**
     * Obtener estadísticas de seguimiento
     */
    public function getFollowersCountAttribute()
    {
        // En el futuro se puede implementar un sistema de "seguir próximos estrenos"
        return 0;
    }
}