#!/bin/bash

# Script to import dramas one page at a time

# Truncate existing data
echo "Truncating database tables..."
php artisan db:seed --class=TruncateImportedDataSeeder

# Import each page individually
for page in {1..5}
do
  echo "Importing page $page..."
  php artisan dorasia:import-romantic-dramas --page=$page --pages=1
  
  # Count titles for progress tracking
  count=$(php artisan tinker --execute="echo DB::table('titles')->count();")
  echo "Current title count: $count"
  
  # Wait a few seconds between pages to avoid overwhelming the TMDB API
  if [ $page -lt 5 ]; then
    echo "Waiting 5 seconds before next page..."
    sleep 5
  fi
done

# Show final counts
echo "Import completed! Final counts:"
echo "Titles: $(php artisan tinker --execute="echo DB::table('titles')->count();")"
echo "Seasons: $(php artisan tinker --execute="echo DB::table('seasons')->count();")"
echo "Episodes: $(php artisan tinker --execute="echo DB::table('episodes')->count();")"
echo "People: $(php artisan tinker --execute="echo DB::table('people')->count();")"

echo "Done!"