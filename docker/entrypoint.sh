#!/bin/bash

# Link storage
php artisan storage:link

# Cache configuration, routes, and views
php artisan config:cache
php artisan route:cache
php artisan view:cache

exec "$@"
