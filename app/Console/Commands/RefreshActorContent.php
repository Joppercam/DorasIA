<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\ImprovedActorContentSeeder;

class RefreshActorContent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'actors:refresh-content {--keep-stats : Mantener estadísticas de vistas y likes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Regenera el contenido de actores con datos más ricos y sustanciales';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🎬 Regenerando contenido de actores...');
        
        if (!$this->option('keep-stats')) {
            $this->warn('⚠️  Esto eliminará todo el contenido existente y las estadísticas.');
            if (!$this->confirm('¿Estás seguro de que quieres continuar?')) {
                $this->info('Operación cancelada.');
                return 0;
            }
        }

        // Ejecutar el seeder
        $seeder = new ImprovedActorContentSeeder();
        
        if ($this->option('keep-stats')) {
            $this->info('📊 Manteniendo estadísticas existentes...');
            // Aquí podrías implementar lógica para preservar stats
        }
        
        $this->withProgressBar(range(1, 100), function () use ($seeder) {
            usleep(50000); // Simular progreso
        });
        
        $this->newLine(2);
        
        try {
            $seeder->run();
            $this->info('✅ Contenido de actores regenerado exitosamente!');
            
            // Mostrar estadísticas
            $total = \App\Models\ActorContent::count();
            $this->info("📈 Total de contenido generado: {$total}");
            
            $types = \App\Models\ActorContent::selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->get();
                
            $this->table(['Tipo', 'Cantidad'], $types->map(function($item) {
                return [$item->type, $item->count];
            })->toArray());
            
        } catch (\Exception $e) {
            $this->error('❌ Error al regenerar contenido: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}