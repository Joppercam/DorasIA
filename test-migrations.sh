#!/bin/bash

# Script to test database migrations for the Dorasia platform

echo "Testing database migrations for Dorasia..."
echo ""

# Make sure SQLite DB exists and is clean
if [ -f "database/database.sqlite" ]; then
    echo "Removing existing database..."
    rm database/database.sqlite
fi

echo "Creating fresh SQLite database..."
touch database/database.sqlite

# Run migrations
echo "Running migrations..."
php artisan migrate

# Check for errors
if [ $? -eq 0 ]; then
    echo ""
    echo "✅ All migrations completed successfully!"
    
    # Create test data
    echo ""
    echo "Creating sample data for testing..."
    
    # Add test category for K-dramas
    php artisan tinker --execute="App\\Models\\Category::create(['name' => 'K-Drama', 'slug' => 'k-drama', 'description' => 'Korean dramas', 'language' => 'ko', 'country' => 'Corea del Sur', 'display_order' => 1]);"
    
    echo ""
    echo "✅ Sample data created successfully!"
    echo ""
    echo "To run import commands, make sure to set your TMDB API credentials in the .env file:"
    echo "  TMDB_API_KEY=your_api_key"
    echo "  TMDB_ACCESS_TOKEN=your_access_token"
    echo ""
    echo "Then run one of the following commands:"
    echo "  php artisan import:korean-dramas --pages=1        # Import Korean dramas"
    echo "  php artisan import:japanese-dramas --pages=1      # Import Japanese dramas"
    echo "  php artisan import:chinese-dramas --pages=1       # Import Chinese dramas" 
    echo "  php artisan import:asian-movies --country=KR --pages=1  # Import Korean movies"
else
    echo ""
    echo "❌ Migrations failed. Please check the error messages above."
fi

echo ""
echo "Done."