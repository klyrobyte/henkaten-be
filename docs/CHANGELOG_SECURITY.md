# Security Changelog  - Henkaten Dashboard

> Tracks all security features, fixes, and hardening changes.

---

## [Security Hardening Pass]  - 2026-05-02

### Added

#### New Middleware

- **`app/Http/Middleware/SecurityHeaders.php`**  - Global middleware injecting production-grade HTTP security headers:
  - `X-Frame-Options: SAMEORIGIN`  - prevents clickjacking
  - `X-Content-Type-Options: nosniff`  - prevents MIME sniffing
  - `X-XSS-Protection: 1; mode=block`  - legacy XSS filter
  - `Referrer-Policy: strict-origin-when-cross-origin`
  - `Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=(), usb=()`
  - `Content-Security-Policy`  - restricts script/style/font/image sources to trusted origins
  - `Strict-Transport-Security`  - HSTS, production-only, 1 year
  - Removes `X-Powered-By` and `Server` headers to prevent fingerprinting

- **`app/Http/Middleware/EnsureInternalRequest.php`**  - Guards `/api/*` routes from direct external access:
  - Requires any one of: `X-Requested-With: XMLHttpRequest`, valid `Referer` matching `APP_URL`, or `Accept: application/json`
  - Combined with `auth` middleware, blocks curl/Postman access even with stolen session cookies
  - Logs suspicious blocked access attempts with IP, path, User-Agent

#### Route Security

- `POST /login` now has `throttle:10,1`  - rate limited to 10 attempts/minute per IP (brute-force protection)
- `GET /admin/status` now has `throttle:120,1` + `internal.request`  - polling endpoint protected
- `GET /admin/reports/export*` and `GET /admin/absence/export*` now have `throttle:10,1`  - prevents export-based resource exhaustion
- `/machines/floor-plan` moved from public to `auth` middleware  - now requires authentication
- `/api/*` routes moved from public to `['auth', 'internal.request', 'throttle:120,1']`  - fully protected

#### Documentation

- `docs/SECURITY.md`  - comprehensive security architecture documentation
- `docs/DEPLOYMENT.md`  - deployment checklist and server hardening guide
- `docs/CHANGELOG_SECURITY.md`  - this file

### Fixed

#### Information Leakage Fixes

- **`AssignmentController::candidates()`**  - Removed `?debug=1` query parameter endpoint that exposed internal factory/shift data, member names, and query state to any authenticated user. Now returns a generic error on failure and logs full details server-side only.

- **`AssignmentController::candidates()` catch block**  - Replaced full PHP stack trace in JSON response (file paths, line numbers, 5 stack frames) with generic `{ ok: false, message: '...' }`. Stack traces are now logged server-side only.

- **`MachineFloorPlanController::getAllMachines()`**  - Replaced `{ error: $e->getMessage() }` (raw exception message) with `{ message: 'Unable to retrieve machine data.' }`. Full error now logged server-side.

- **`MachineFloorPlanController::getFloorPlanData()`**  - Same fix as above.

- **`MachineFloorPlanController::getFloorPlanDataById()`**  - Same fix as above.

#### Input Validation

- **`MemberController::storeBase64Photo()`**  - Added magic-byte MIME validation. The decoded binary is now inspected against known JPEG (`\xFF\xD8\xFF`), PNG (`\x89PNG\r\n\x1a\n`), GIF (`GIF87a`/`GIF89a`), and WebP (`RIFF....WEBP`) signatures. A claimed `data:image/jpeg` that is actually a PHP script or other file type will be rejected. Invalid uploads are logged as `warning`.

- **`MemberController::clearAll()`**  - Added explicit confirmation guard. The `DELETE /admin/members/clear-all` endpoint now requires the `X-Confirm-Action: DELETE_ALL_MEMBERS` header to be present. Without it, returns `422 Unprocessable Entity`. This prevents accidental or scripted mass-deletion.

### Changed

#### Environment Configuration (`.env`)

| Key | Before | After |
|-----|--------|-------|
| `APP_ENV` | `local` | `production` |
| `APP_DEBUG` | `true` | `false` |
| `LOG_STACK` | `single` | `daily` |
| `LOG_LEVEL` | `debug` | `warning` |
| `SESSION_ENCRYPT` | `false` | `true` |
| `SESSION_SECURE_COOKIE` | (not set) | `true` |
| `SESSION_SAME_SITE` | (not set) | `strict` |

#### `.env.example`
Updated to have same production-safe defaults as above, so developers copying the file start secure.

#### `bootstrap/app.php`
- Added `SecurityHeaders` to global web middleware group
- Added `internal.request` middleware alias for `EnsureInternalRequest`

---

## Pre-existing Security (Already in Place)

The following were already correctly implemented and were not modified:

- CSRF protection via Laravel's default `VerifyCsrfToken` middleware
- bcrypt password hashing with `BCRYPT_ROUNDS=12`
- `RoleMiddleware` protecting admin/gl/tl/pengawas routes
- `TvRestrictMiddleware` globally restricting the `tv` role
- Session regeneration on login (`session()->regenerate()`)
- Session invalidation on logout (`session()->invalidate()` + `regenerateToken()`)
- No SQL injection risk  - all queries use Eloquent ORM
- Mass assignment protection via `$fillable` arrays on all models
- `password` and `remember_token` hidden from User model serialization
- File upload validation: `mimes:jpeg,jpg,png,webp|max:3072`
- Image auto-resize/recompress to 1280px / 80% JPEG quality on upload
