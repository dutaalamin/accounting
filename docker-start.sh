#!/bin/bash
# Disable conflicting Apache MPM modules
a2dismod mpm_event || true
a2dismod mpm_worker || true
a2enmod mpm_prefork || true

# Jalankan migrasi (DB connection dari env Railway)
php artisan migrate --force

# Seed admin user HANYA jika belum ada user (untuk setup awal)
php artisan db:seed --force

# Cache config/route/view untuk performa production
php artisan config:cache
php artisan route:cache
php artisan view:cache

apache2-foreground
