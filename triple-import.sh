#!/bin/bash

# Script to import triple the amount of dramas with recommendations

# Function to display progress
function display_header() {
  echo "===================================================="
  echo "  Dorasia - Triple Drama Importer"
  echo "===================================================="
  echo
}

display_header

# Check if a country is specified
COUNTRY=${1:-all}
TRUNCATE=${2:-false}
PARALLEL=${3:-2}

TRUNCATE_ARG=""
if [ "$TRUNCATE" = "true" ]; then
  TRUNCATE_ARG="--truncate"
  echo "⚠️  WARNING: This will clear all existing imported data!"
  echo "Do you want to continue? (y/n)"
  read -r response
  if [[ ! "$response" =~ ^([yY][eE][sS]|[yY])$ ]]; then
    echo "Operation cancelled."
    exit 0
  fi
fi

echo "🚀 Starting triple import for country: $COUNTRY"
echo "🔄 Parallel processing: $PARALLEL concurrent pages"

if [ "$TRUNCATE" = "true" ]; then
  echo "🗑️  Truncating existing data..."
fi

# Run the import command
php artisan dorasia:triple-import --country=$COUNTRY $TRUNCATE_ARG --parallel=$PARALLEL

# Final summary
echo
echo "===================================================="
echo "  Import Complete - Database Statistics"
echo "===================================================="
echo "Titles:    $(php artisan tinker --execute="echo DB::table('titles')->count();")"
echo "Seasons:   $(php artisan tinker --execute="echo DB::table('seasons')->count();")"
echo "Episodes:  $(php artisan tinker --execute="echo DB::table('episodes')->count();")"
echo "People:    $(php artisan tinker --execute="echo DB::table('people')->count();")"
echo "Genres:    $(php artisan tinker --execute="echo DB::table('genres')->count();")"
echo "Categories: $(php artisan tinker --execute="echo DB::table('categories')->count();")"
echo "===================================================="
echo
echo "✅ Import process completed successfully!"
echo