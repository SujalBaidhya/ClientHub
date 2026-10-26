#!/bin/bash
set -e

# Default to port 80 if PORT environment variable is not provided
PORT="${PORT:-80}"

# Substitute port in nginx config
sed -i "s/listen 80;/listen ${PORT};/g" /etc/nginx/http.d/default.conf

# Run database migrations and cache optimizations
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
# Run Laravel's scheduler in the background (overdue + due-soon emails).
# Output goes to the container log (visible in Render Logs) and the loop restarts it if it ever crashes.
(
  while true; do
    php artisan schedule:work >> /proc/1/fd/1 2>&1
    echo "[scheduler] schedule:work exited, restarting in 5s" >> /proc/1/fd/1
    sleep 5
  done
) &
# Start PHP-FPM in background
php-fpm -D

# Start Nginx in foreground
exec nginx -g "daemon off;"