<?php

namespace App\Console\Commands;

use App\Models\Person;
use App\Services\TmdbService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UpdatePersonDetails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:person-details {--limit=50} {--missing-only}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update details (biography, photos, etc.) for people in the database';

    /**
     * @var TmdbService
     */
    protected $tmdbService;

    /**
     * Create a new command instance.
     */
    public function __construct(TmdbService $tmdbService)
    {
        parent::__construct();
        $this->tmdbService = $tmdbService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = (int) $this->option('limit');
        $missingOnly = $this->option('missing-only');

        $this->info("Updating person details (limit: {$limit}, missing only: " . ($missingOnly ? 'yes' : 'no') . ")");

        $query = Person::query();
        
        // Only process people with tmdb_id and missing details
        $query->whereNotNull('tmdb_id');
        
        if ($missingOnly) {
            $query->where(function ($q) {
                $q->whereNull('biography')
                  ->orWhereNull('birthday')
                  ->orWhereNull('photo');
            });
        }
        
        $query->orderBy('updated_at')->limit($limit);
        
        $people = $query->get();
        
        if ($people->isEmpty()) {
            $this->info("No people found to update.");
            return;
        }
        
        $this->info("Found " . $people->count() . " people to update.");
        
        $progressBar = $this->output->createProgressBar($people->count());
        $progressBar->start();
        
        $updated = 0;
        
        foreach ($people as $person) {
            try {
                // Get detailed information
                $details = $this->tmdbService->getPersonDetails($person->tmdb_id);
                
                if (empty($details)) {
                    $progressBar->advance();
                    continue;
                }
                
                // Begin transaction
                DB::beginTransaction();
                
                $updateData = [
                    'name' => $details['name'],
                    'biography' => $details['biography'],
                    'birthday' => $details['birthday'],
                    'deathday' => $details['deathday'],
                    'gender' => $this->mapGender($details['gender']),
                    'place_of_birth' => $details['place_of_birth'],
                    'popularity' => $details['popularity'],
                    'updated_at' => now(),
                ];
                
                // Download profile photo if available
                if (!empty($details['profile_path']) && empty($person->photo)) {
                    $updateData['photo'] = $this->tmdbService->downloadImage($details['profile_path'], 'poster');
                }
                
                // Update external IDs if available
                if (!empty($details['external_ids'])) {
                    $updateData['imdb_id'] = $details['external_ids']['imdb_id'] ?? null;
                    $updateData['instagram_id'] = $details['external_ids']['instagram_id'] ?? null;
                    $updateData['twitter_id'] = $details['external_ids']['twitter_id'] ?? null;
                }
                
                // Update the record
                $person->update($updateData);
                
                // Download additional images if available
                if (!empty($details['images']['profiles'])) {
                    // TODO: Implement gallery images feature if needed
                }
                
                DB::commit();
                $updated++;
                
            } catch (\Exception $e) {
                DB::rollBack();
                Log::error("Error updating person {$person->id}: " . $e->getMessage());
                $this->error("Error updating person {$person->id}: " . $e->getMessage());
            }
            
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->line('');
        
        $this->info("Update completed. Updated details for {$updated} people.");
    }
    
    /**
     * Map TMDB gender to text
     */
    protected function mapGender(int $gender): string
    {
        switch ($gender) {
            case 1:
                return 'female';
            case 2:
                return 'male';
            default:
                return 'unknown';
        }
    }
}