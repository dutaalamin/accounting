#!/bin/bash
# Disable conflicting Apache MPM modules
a2dismod mpm_event || true
a2dismod mpm_worker || true
a2enmod mpm_prefork || true

mkdir -p database
touch database/database.sqlite
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
apache2-foreground
