<?php

namespace App\Traits;

use App\Models\Person;
use App\Services\TmdbService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

trait PersonImporter
{
    /**
     * Find or create a person with proper slug handling
     * 
     * @param array $personData The person data from TMDB API
     * @return Person The person model instance
     */
    protected function findOrCreatePersonSafely(array $personData): Person
    {
        try {
            // Check if we already have this person by TMDB ID
            $person = Person::where('tmdb_id', $personData['id'] ?? null)->first();
            
            if ($person) {
                // Person exists, ensure they have a slug
                if (empty($person->slug)) {
                    $person->slug = $this->generateSlugForPerson($personData, $person->id);
                    $person->save();
                }
                return $person;
            }
            
            // Create a new person
            $person = new Person();
            $person->name = $personData['name'] ?? null;
            $person->tmdb_id = $personData['id'] ?? null;
            
            // Generate slug (critical field)
            $person->slug = $this->generateSlugForPerson($personData);
            
            // Add other fields that might be available
            $person->profile_path = $this->downloadPersonImage($personData);
            $person->biography = $personData['biography'] ?? null;
            $person->birth_date = $personData['birthday'] ?? null;
            $person->death_date = $personData['deathday'] ?? null;
            $person->place_of_birth = $personData['place_of_birth'] ?? null;
            $person->gender = $personData['gender'] ?? null;
            $person->popularity = $personData['popularity'] ?? null;
            
            $person->save();
            return $person;
        } catch (\Exception $e) {
            Log::error('Error creating person record', [
                'exception' => $e->getMessage(),
                'personData' => $personData
            ]);
            
            // Create a minimal valid record as fallback
            $fallbackPerson = new Person();
            $fallbackPerson->name = $personData['name'] ?? 'Unknown Person';
            $fallbackPerson->tmdb_id = $personData['id'] ?? null;
            $fallbackPerson->slug = 'person-' . ($personData['id'] ?? Str::random(8));
            $fallbackPerson->save();
            
            return $fallbackPerson;
        }
    }
    
    /**
     * Generate a slug for a person
     * 
     * @param array $personData The person data
     * @param int|null $personId Optional person ID for fallback
     * @return string The generated slug
     */
    protected function generateSlugForPerson(array $personData, ?int $personId = null): string
    {
        // Try to generate slug from name
        if (!empty($personData['name'])) {
            return Str::slug($personData['name']);
        }
        
        // Fallback to TMDB ID
        if (!empty($personData['id'])) {
            return 'person-' . $personData['id'];
        }
        
        // Last resort fallback
        if ($personId) {
            return 'person-' . $personId;
        }
        
        // Ultimate fallback with random string
        return 'person-' . Str::random(8);
    }
    
    /**
     * Download person image with service detection
     * 
     * @param array $personData The person data
     * @return string|null The saved image path or null
     */
    protected function downloadPersonImage(array $personData): ?string
    {
        // Skip if no profile path
        if (empty($personData['profile_path'])) {
            return null;
        }
        
        try {
            // Try to use instance TmdbService if available
            if (isset($this->tmdbService) && $this->tmdbService instanceof TmdbService) {
                return $this->tmdbService->downloadImage($personData['profile_path'], 'poster');
            }
            
            // Try to resolve from container as fallback
            $tmdbService = app(TmdbService::class);
            return $tmdbService->downloadImage($personData['profile_path'], 'poster');
        } catch (\Exception $e) {
            Log::warning('Failed to download person image', [
                'profile_path' => $personData['profile_path'],
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}