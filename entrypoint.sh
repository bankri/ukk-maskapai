#!/bin/bash
set -e

# Color output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${GREEN}=========================================${NC}"
echo -e "${GREEN} Z-Airlines Docker Entry Point${NC}"
echo -e "${GREEN}=========================================${NC}"

# Wait for database to be ready (if using external DB like Supabase)
if [ -n "$DB_HOST" ]; then
    echo -e "${YELLOW} Waiting for database at $DB_HOST...${NC}"
    while ! nc -z "$DB_HOST" "${DB_PORT:-5432}" >/dev/null 2>&1; do
        sleep 1
    done
    echo -e "${GREEN}✅ Database is ready!${NC}"
fi

# Check if .env exists
if [ ! -f .env ]; then
    echo -e "${YELLOW}📄 Creating .env from .env.example...${NC}"
    cp .env.example .env
fi

# Generate app key if not exists
if [ -z "$(php artisan key:generate --show 2>/dev/null)" ]; then
    echo -e "${YELLOW} Generating application key...${NC}"
    php artisan key:generate
fi

# Run database migrations
if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    echo -e "${YELLOW}🗄️  Running database migrations...${NC}"
    php artisan migrate --force
fi

# Run database seeders
if [ "${RUN_SEEDERS:-false}" = "true" ]; then
    echo -e "${YELLOW}🌱 Running database seeders...${NC}"
    php artisan db:seed --force
fi

# Clear and cache config
echo -e "${YELLOW}🧹 Clearing caches...${NC}"
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Optimize for production
if [ "$APP_ENV" = "production" ]; then
    echo -e "${YELLOW}⚡ Optimizing for production...${NC}"
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
fi

# Set proper permissions
echo -e "${YELLOW}🔐 Setting permissions...${NC}"
chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
chmod -R 775 /var/www/storage /var/www/bootstrap/cache

echo -e "${GREEN}=========================================${NC}"
echo -e "${GREEN}✅ Application is ready!${NC}"
echo -e "${GREEN}=========================================${NC}"

# Execute main container command
exec "$@"
