#!/bin/bash

# Script to import massive amounts of dramas with recommendations

# Function to display progress
function display_header() {
  echo "===================================================="
  echo "  Dorasia - Massive Drama Importer"
  echo "===================================================="
  echo
}

display_header

# Parse command line options with defaults
COUNTRY=${1:-all}
MODE=${2:-preserve}
PAGES=${3:-30}
PARALLEL=${4:-2}

# Validate parameters
if [[ ! "$COUNTRY" =~ ^(all|kr|jp|cn|th)$ ]]; then
  echo "⚠️  Invalid country code: $COUNTRY"
  echo "Valid options: all, kr, jp, cn, th"
  exit 1
fi

if [[ ! "$MODE" =~ ^(preserve|truncate|fresh)$ ]]; then
  echo "⚠️  Invalid mode: $MODE"
  echo "Valid options: preserve, truncate, fresh"
  exit 1
fi

if [[ ! "$PAGES" =~ ^[0-9]+$ ]] || [ "$PAGES" -lt 1 ]; then
  echo "⚠️  Invalid page count: $PAGES"
  echo "Must be a positive number"
  exit 1
fi

if [[ ! "$PARALLEL" =~ ^[1-3]$ ]]; then
  echo "⚠️  Invalid parallel setting: $PARALLEL"
  echo "Must be between 1 and 3"
  exit 1
fi

# Configure import arguments
if [ "$MODE" = "truncate" ]; then
  MODE_ARG="--truncate"
  echo "⚠️  WARNING: This will clear all existing imported data!"
  echo "Do you want to continue? (y/n)"
  read -r response
  if [[ ! "$response" =~ ^([yY][eE][sS]|[yY])$ ]]; then
    echo "Operation cancelled."
    exit 0
  fi
elif [ "$MODE" = "preserve" ]; then
  MODE_ARG="--preserve"
elif [ "$MODE" = "fresh" ]; then
  MODE_ARG=""
fi

echo "🚀 Starting massive import for country: $COUNTRY"
echo "📊 Importing $PAGES pages of content"
echo "🔄 Parallel processing: $PARALLEL concurrent pages"
echo "📦 Mode: $MODE"

# Display warning for large imports
if [ "$PAGES" -gt 30 ]; then
  echo "⚠️  WARNING: You're about to import $PAGES pages of data."
  echo "This will use a lot of API requests and may take a very long time."
  echo "Do you want to continue? (y/n)"
  read -r response
  if [[ ! "$response" =~ ^([yY][eE][sS]|[yY])$ ]]; then
    echo "Operation cancelled."
    exit 0
  fi
fi

# Run the import command
php artisan dorasia:triple-import --country=$COUNTRY $MODE_ARG --pages=$PAGES --parallel=$PARALLEL

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