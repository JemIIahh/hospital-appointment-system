#!/usr/bin/env bash
# Railway pre-deploy step. Runs once before the web server starts on each deploy.
# Safe to run repeatedly: migrations are idempotent and caching just rebuilds.
set -e

# Apply any new database migrations (never prompts, never wipes data).
php artisan migrate --force

# Rebuild Laravel's production caches for faster boot.
php artisan config:cache
php artisan route:cache
php artisan view:cache
