# API Integration Instructions

This document provides instructions for setting up and using the API integrations in the Dorasia platform.

## The Movie Database (TMDB) API

Dorasia uses the TMDB API to fetch information about Asian dramas and movies. This includes data for Korean, Japanese, and Chinese dramas and movies.

### Getting TMDB API Credentials

To use the TMDB API, you need to obtain an API key and access token:

1. Visit [The Movie Database website](https://www.themoviedb.org/)
2. Create an account if you don't have one already
3. Go to your account settings and navigate to the "API" section
4. Request an API key by filling out the provided form
   - Select "Developer" as the type of use
   - Fill in your application details
   - You'll need to provide some basic information about how you plan to use the API
5. Once approved, you'll receive an API key
6. To generate an access token, go to your API settings
7. You'll see options to generate an API Read Access Token

### Configuring TMDB API in Dorasia

Once you have your API credentials, update your `.env` file with the following values:

```
TMDB_API_KEY=your_api_key_here
TMDB_ACCESS_TOKEN=your_access_token_here
```

### API Rate Limits

Be aware that TMDB has rate limits for their API:

- 40 requests per 10 seconds
- The import commands in Dorasia are designed to respect these limits
- If you run into rate limit issues, reduce the number of pages to import at once

## Running Import Commands

Dorasia provides several commands to import content from TMDB:

```bash
# Import Korean dramas (defaults to 5 pages of results)
php artisan import:korean-dramas

# Import Japanese dramas 
php artisan import:japanese-dramas

# Import Chinese dramas
php artisan import:chinese-dramas

# Import Asian movies (KR=Korea, JP=Japan, CN=China, TW=Taiwan)
php artisan import:asian-movies --country=KR
```

### Command Options

Each command supports the following options:

- `--pages=N`: Number of pages to import (each page contains 20 items)
- `--update`: Update existing titles with new information
- `--force`: Force update of all information including seasons, episodes, and cast

Examples:

```bash
# Import 2 pages of Korean dramas and update existing entries
php artisan import:korean-dramas --pages=2 --update

# Import 1 page of Japanese dramas with a complete refresh of all data
php artisan import:japanese-dramas --pages=1 --force

# Import 3 pages of Korean movies
php artisan import:asian-movies --country=KR --pages=3
```

### Updating Person Details

To update biographical information and images for cast and crew:

```bash
# Update details for up to 50 people who have missing information
php artisan update:person-details --limit=50 --missing-only
```

## Automated Import Schedule

Dorasia is configured with a schedule that automatically runs import commands:

- Korean dramas: Daily at 1:00 AM
- Japanese dramas: Every 3 days at 2:00 AM
- Chinese dramas: Every 3 days at 3:00 AM
- Korean movies: Every Monday at 4:00 AM
- Japanese movies: Every Tuesday at 4:00 AM
- Chinese movies: Every Wednesday at 4:00 AM
- Person details: Daily at 5:00 AM

To enable this schedule, you need to add the scheduler to your crontab:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

Or use the Laravel scheduler in your deployment environment.