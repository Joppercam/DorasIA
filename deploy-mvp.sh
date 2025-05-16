#!/bin/bash

# DorasIA MVP Deployment Script
# =============================

echo "🚀 Starting DorasIA MVP deployment..."

# Configuration
REMOTE_USER="tu_usuario"
REMOTE_HOST="tu_host.com"
REMOTE_PATH="/home/tu_usuario/public_html"
LOCAL_PATH="."

# Colors for output
GREEN='\033[0;32m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Build assets for production
echo "📦 Building assets..."
npm run build

# Generate optimized autoloader
echo "🔧 Optimizing composer..."
composer install --optimize-autoloader --no-dev

# Clear and cache config
echo "🧹 Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

echo "💾 Caching for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create deployment package
echo "📁 Creating deployment package..."
mkdir -p deployment_temp

# Copy necessary files
rsync -av --exclude-from='.deployignore' \
  --exclude='storage/app/*' \
  --exclude='storage/framework/cache/*' \
  --exclude='storage/framework/sessions/*' \
  --exclude='storage/framework/views/*' \
  --exclude='storage/logs/*' \
  --exclude='bootstrap/cache/*' \
  --exclude='.git' \
  --exclude='node_modules' \
  --exclude='.env' \
  --exclude='deploy-mvp.sh' \
  --exclude='deployment_temp' \
  . deployment_temp/

# Upload to hosting
echo "📤 Uploading to hosting..."
rsync -avz --progress deployment_temp/ ${REMOTE_USER}@${REMOTE_HOST}:${REMOTE_PATH}/

# Run post-deployment commands on server
echo "🔨 Running post-deployment commands..."
ssh ${REMOTE_USER}@${REMOTE_HOST} << 'ENDSSH'
cd ${REMOTE_PATH}

# Copy production env if doesn't exist
if [ ! -f .env ]; then
    cp .env.production .env
    php artisan key:generate
fi

# Set permissions
chmod -R 755 storage
chmod -R 755 bootstrap/cache

# Run migrations
php artisan migrate --force

# Clear caches on server
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Deployment complete!"
ENDSSH

# Cleanup
echo "🧹 Cleaning up..."
rm -rf deployment_temp

echo -e "${GREEN}✅ DorasIA MVP deployment completed successfully!${NC}"
echo -e "${GREEN}🌐 Your app should now be available at your domain${NC}"
echo -e "${RED}⚠️  Don't forget to update .env with your actual database credentials!${NC}"