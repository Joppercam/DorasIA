#!/bin/bash

# Script to import MASSIVE amounts of dramas with recommendations
# This script runs multiple imports for different countries sequentially
# to maximize the variety of content in your database

# Function to display progress
function display_header() {
  echo "===================================================="
  echo "  Dorasia - MEGA Drama Importer"
  echo "===================================================="
  echo "This script will import a massive amount of content,"
  echo "including Korean, Japanese, Chinese and Thai dramas"
  echo "===================================================="
  echo
}

display_header

# Default settings
MODE="preserve"
PAGES_PER_COUNTRY=20
PARALLEL=2

# Parse command line arguments
while getopts "m:p:c:" opt; do
  case $opt in
    m) MODE="$OPTARG" ;;
    p) PAGES_PER_COUNTRY="$OPTARG" ;;
    c) PARALLEL="$OPTARG" ;;
    *) echo "Invalid option: -$OPTARG" >&2; exit 1 ;;
  esac
done

# Validate parameters
if [[ ! "$MODE" =~ ^(preserve|truncate|fresh)$ ]]; then
  echo "⚠️  Invalid mode: $MODE"
  echo "Valid options: preserve, truncate, fresh"
  exit 1
fi

if [[ ! "$PAGES_PER_COUNTRY" =~ ^[0-9]+$ ]] || [ "$PAGES_PER_COUNTRY" -lt 1 ]; then
  echo "⚠️  Invalid page count: $PAGES_PER_COUNTRY"
  echo "Must be a positive number"
  exit 1
fi

if [[ ! "$PARALLEL" =~ ^[1-3]$ ]]; then
  echo "⚠️  Invalid parallel setting: $PARALLEL"
  echo "Must be between 1 and 3"
  exit 1
fi

# Information about what this will do
TOTAL_PAGES=$((PAGES_PER_COUNTRY * 4))  # 4 countries
echo "🚀 Starting MEGA import with the following settings:"
echo "📊 $PAGES_PER_COUNTRY pages per country x 4 countries = $TOTAL_PAGES total pages"
echo "🔄 Parallel processing: $PARALLEL concurrent pages per country"
echo "📦 Mode: $MODE"
echo

# Only truncate once at the beginning if requested
if [ "$MODE" = "truncate" ]; then
  echo "⚠️  WARNING: This will clear ALL existing imported data!"
  echo "Do you want to continue? (y/n)"
  read -r response
  if [[ ! "$response" =~ ^([yY][eE][sS]|[yY])$ ]]; then
    echo "Operation cancelled."
    exit 0
  fi
  # For subsequent imports, we switch to "fresh" mode (no truncate or preserve)
  FIRST_MODE="truncate"
  SUBSEQUENT_MODE="fresh"
else
  FIRST_MODE="$MODE"
  SUBSEQUENT_MODE="$MODE"
fi

# Warning for very large imports
if [ "$TOTAL_PAGES" -gt 60 ]; then
  echo "⚠️  WARNING: You're about to import $TOTAL_PAGES pages of data."
  echo "This is a VERY large amount and will use many API requests."
  echo "It may take hours to complete and might exceed API rate limits."
  echo "Do you want to continue? (y/n)"
  read -r response
  if [[ ! "$response" =~ ^([yY][eE][sS]|[yY])$ ]]; then
    echo "Operation cancelled."
    exit 0
  fi
fi

# Start timing the operation
START_TIME=$(date +%s)

# Store initial database statistics
INITIAL_COUNT=$(php artisan tinker --execute="echo DB::table('titles')->count();")
echo "Starting with $INITIAL_COUNT titles in the database"

# Import Korean dramas (always first because they're most popular)
echo -e "\n===================================================="
echo "STEP 1/4: IMPORTING KOREAN DRAMAS"
echo "===================================================="
php artisan dorasia:triple-import --country=kr --pages=$PAGES_PER_COUNTRY --parallel=$PARALLEL --$FIRST_MODE

# Import Japanese dramas
echo -e "\n===================================================="
echo "STEP 2/4: IMPORTING JAPANESE DRAMAS"
echo "===================================================="
php artisan dorasia:triple-import --country=jp --pages=$PAGES_PER_COUNTRY --parallel=$PARALLEL --$SUBSEQUENT_MODE

# Import Chinese dramas
echo -e "\n===================================================="
echo "STEP 3/4: IMPORTING CHINESE DRAMAS"
echo "===================================================="
php artisan dorasia:triple-import --country=cn --pages=$PAGES_PER_COUNTRY --parallel=$PARALLEL --$SUBSEQUENT_MODE

# Import Thai dramas
echo -e "\n===================================================="
echo "STEP 4/4: IMPORTING THAI DRAMAS"
echo "===================================================="
php artisan dorasia:triple-import --country=th --pages=$PAGES_PER_COUNTRY --parallel=$PARALLEL --$SUBSEQUENT_MODE

# Calculate completion time
END_TIME=$(date +%s)
DURATION=$((END_TIME - START_TIME))
HOURS=$((DURATION / 3600))
MINUTES=$(( (DURATION % 3600) / 60 ))
SECONDS=$((DURATION % 60))

# Final summary
FINAL_COUNT=$(php artisan tinker --execute="echo DB::table('titles')->count();")
NEW_TITLES=$((FINAL_COUNT - INITIAL_COUNT))

echo
echo "===================================================="
echo "  MEGA IMPORT COMPLETE"
echo "===================================================="
echo "Import started with: $INITIAL_COUNT titles"
echo "Import ended with:   $FINAL_COUNT titles"
echo "New titles added:    $NEW_TITLES titles"
echo
echo "Total runtime: ${HOURS}h ${MINUTES}m ${SECONDS}s"
echo
echo "DATABASE STATISTICS:"
echo "Titles:    $(php artisan tinker --execute="echo DB::table('titles')->count();")"
echo "Seasons:   $(php artisan tinker --execute="echo DB::table('seasons')->count();")"
echo "Episodes:  $(php artisan tinker --execute="echo DB::table('episodes')->count();")"
echo "People:    $(php artisan tinker --execute="echo DB::table('people')->count();")"
echo "Genres:    $(php artisan tinker --execute="echo DB::table('genres')->count();")"
echo "Categories: $(php artisan tinker --execute="echo DB::table('categories')->count();")"
echo "===================================================="
echo

if [ $NEW_TITLES -gt 0 ]; then
  echo "✅ Import successful! Your catalog now has $NEW_TITLES new titles."
else
  echo "⚠️  Warning: No new titles were added. You may already have all available titles in your database."
fi

echo
echo "Thank you for using Dorasia MEGA Import!"