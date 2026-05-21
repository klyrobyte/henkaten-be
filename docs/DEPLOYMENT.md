# Deployment Checklist  - Henkaten Dashboard

> **Last updated:** 2026-05-02

This document must be reviewed before every production deployment.

---

## Environment Auto-Configuration

Everything in the app  - Scribe `tryItOutBaseUrl`, CSP `connect-src`, HSTS, session cookies  - is **driven by exactly two `.env` values**: `APP_ENV` and `APP_URL`. No PHP files need editing.

| Scenario | `APP_ENV` | `APP_URL` | What changes automatically |
|---|---|---|---|
| Local dev (`artisan serve`) | `local` | `http://localhost:8000` | CSP allows all `localhost:*` ports |
| LAN / hotspot / phone testing | `production` | `http://192.168.x.x:8000` | CSP locks to that IP only |
| HTTP deployment | `production` | `http://yourdomain.com` | CSP locks to domain, no HTTPS upgrade |
| HTTPS deployment | `production` | `https://yourdomain.com` | HTTPS upgrade + HSTS (1 year) enabled |

> **Rule:** Set `APP_ENV=production` + `APP_URL=<your IP or domain>` and the whole stack adjusts. Run `php artisan config:cache` after any `.env` change in production.

---

## Pre-Deployment Checklist

### ✅ Environment Configuration

- [ ] **Generate a new APP_KEY** on the production server:
  ```bash
  php artisan key:generate
  ```
  Never commit production `.env` to Git. Never reuse dev keys in production.

- [ ] **Set APP_ENV and APP_DEBUG**:
  ```
  APP_ENV=production
  APP_DEBUG=false
  ```

- [ ] **Set APP_URL** to your actual domain:
  ```
  APP_URL=https://yourdomain.com
  ```

- [ ] **Set a strong DB_PASSWORD**:
  ```
  DB_PASSWORD=<strong-random-password>
  ```
  Do NOT use root with an empty password in production.

- [ ] **Session security** (these are set by default, verify):
  ```
  SESSION_ENCRYPT=true
  SESSION_SECURE_COOKIE=true
  SESSION_SAME_SITE=strict
  ```

- [ ] **Log level**:
  ```
  LOG_LEVEL=warning
  LOG_STACK=daily
  ```

---

### ✅ Server & PHP Configuration

- [ ] **Disable PHP error display** in `php.ini`:
  ```ini
  display_errors = Off
  log_errors = On
  error_log = /var/log/php_errors.log
  ```

- [ ] **Set PHP `expose_php = Off`** in `php.ini` to hide PHP version in headers.

- [ ] **Disable directory listing** in the web server config (Nginx/Apache).

- [ ] **Point web root** to the `public/` directory only  - not the project root.
  - Nginx: `root /var/www/henkaten/public;`
  - Apache: `DocumentRoot /var/www/henkaten/public`

- [ ] **Secure file permissions**:
  ```bash
  # Project files  - readable by web user, not writable
  chmod -R 755 /var/www/henkaten
  chmod -R 775 /var/www/henkaten/storage
  chmod -R 775 /var/www/henkaten/bootstrap/cache
  chown -R www-data:www-data /var/www/henkaten
  ```

- [ ] **Protect `.env`**  - never publicly accessible:
  ```bash
  chmod 600 /var/www/henkaten/.env
  ```

---

### ✅ HTTPS / SSL

- [ ] **SSL/TLS certificate** is installed and valid (Let's Encrypt recommended).
- [ ] **HTTP → HTTPS redirect** is configured at the web server level.
- [ ] `SESSION_SECURE_COOKIE=true` is set (cookies only over HTTPS).
- [ ] HSTS header will automatically activate when `APP_ENV=production`.

---

### ✅ Laravel Optimization

Run these after every deployment:

```bash
# Clear and rebuild all caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Run database migrations
php artisan migrate --force

# Create storage symlink (first deploy only)
php artisan storage:link
```

---

### ✅ Database

- [ ] **Run migrations**:
  ```bash
  php artisan migrate --force
  ```

- [ ] **Set strong DB credentials**  - never use root with no password.

- [ ] **Restrict DB user permissions**  - the app's DB user should only have:
  `SELECT, INSERT, UPDATE, DELETE` on its own database.
  NOT `SUPER`, `FILE`, `PROCESS`, `GRANT OPTION`.

---

### ✅ Storage & Uploads

- [ ] `php artisan storage:link` has been run to create the public symlink.
- [ ] Upload directories (`storage/app/public/machines/`, `storage/app/public/members/`) exist and are writable.
- [ ] A backup strategy is in place for uploaded photos.

---

### ✅ Security Verification

After deployment, verify these with a browser/curl:

| Check | Expected Result |
|-------|----------------|
| `GET /api/machines/all` (Nahkan ketauan, mau ngapain coba wkwkwk, kalo u admin bisa baca panduan dokumentasi di henkaten.md (@RizkyDaffy)) | `302` redirect to login |
| `GET /machines/floor-plan` (Nahkan ketauan, mau ngapain coba wkwkwk, kalo u admin bisa baca panduan dokumentasi di henkaten.md (@RizkyDaffy)) | `302` redirect to login |
| `POST /login` with wrong credentials 11× | `429 Too Many Requests` |
| Response headers include `X-Frame-Options` | `SAMEORIGIN` |
| Response headers include `X-Content-Type-Options` | `nosniff` |
| `GET /admin/anything` (Nahkan ketauan, mau ngapain coba wkwkwk, kalo u admin bisa baca panduan dokumentasi di henkaten.md (@RizkyDaffy)) | `302` redirect to login |
| Error page (trigger a 500) | Generic error page, no stack trace |

---

### ✅ Ongoing Maintenance

- [ ] Log rotation is configured (`LOG_STACK=daily`, 14-day retention).
- [ ] Set up a cron job for Laravel's scheduler:
  ```bash
  * * * * * cd /var/www/henkaten && php artisan schedule:run >> /dev/null 2>&1
  ```
- [ ] Monitor `storage/logs/` for `warning` level entries regularly.
- [ ] Update Laravel and dependencies regularly:
  ```bash
  composer update
  ```
- [ ] Review `storage/logs/` for any `EnsureInternalRequest: blocked direct API access` warnings which may indicate scanning activity.
