# Mode 1 – Local dev (default)
APP_ENV=local
APP_URL=http://localhost:8000

# Mode 2 – LAN / phone / hotspot testing
APP_ENV=production
APP_URL=http://192.168.x.x:8000    ← your machine's LAN IP

# Mode 3 – Real deployment server
APP_ENV=production
APP_URL=https://yourdomain.com     ← domain or server IP


After editing .env on a production server, run php artisan config:cache to apply the changes.

Reference table for future deploys
ini
# LAN / hotspot (current setup)
APP_URL=http://192.168.174.152:8000
SESSION_SECURE_COOKIE=false
SESSION_SAME_SITE=lax
# HTTPS domain deployment (future)
APP_URL=https://yourdomain.com
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=strict