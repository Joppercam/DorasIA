<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Title;
use App\Models\Season;
use App\Models\Episode;
use App\Models\Person;

class CleanDatabase extends Command
{
    protected $signature = 'dorasia:clean-database';
    protected $description = 'Limpia las tablas de contenido';

    public function handle()
    {
        $this->info('Limpiando la base de datos...');
        
        // Desactivar restricciones de clave externa
        DB::statement('PRAGMA foreign_keys = OFF');
        
        // Limpiar tablas relacionadas
        DB::table('title_person')->truncate();
        DB::table('title_genre')->truncate();
        
        // Limpiar tablas principales
        $this->info('Eliminando episodios...');
        Episode::truncate();
        
        $this->info('Eliminando temporadas...');
        Season::truncate();
        
        $this->info('Eliminando títulos...');
        Title::truncate();
        
        $this->info('Eliminando personas...');
        Person::truncate();
        
        // Reactivar restricciones de clave externa
        DB::statement('PRAGMA foreign_keys = ON');
        
        $this->info('Base de datos limpiada correctamente.');
        
        return Command::SUCCESS;
    }
}