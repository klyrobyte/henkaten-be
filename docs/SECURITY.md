# Security Architecture  - Henkaten Dashboard

> **Last updated:** 2026-05-02
> **Author:** Security hardening pass  / @RizkyDaffy

---

## Overview

Henkaten is a Laravel 11 production management dashboard. This document describes every security control in place and the reasoning behind each decision.

---

## 1. Authentication & Authorization

### Session-Based Authentication
- Laravel's built-in `Auth` system is used with **username/password** credentials.
- On successful login, the session is **regenerated** (`session()->regenerate()`) to prevent session fixation attacks.
- On logout, the session is **fully invalidated** (`session()->invalidate()`) and the CSRF token is regenerated.

### Password Security
- All passwords are hashed with **bcrypt** at `BCRYPT_ROUNDS=12`  - computationally expensive enough to slow brute-force offline attacks.
- The `password` field is hidden from all model serialization via `$hidden = ['password']`.
- Minimum password length is enforced at 6 characters during user creation/update.

### Role-Based Access Control (RBAC)

| Role      | Access Level |
|-----------|-------------|
| `admin`   | Full access + user management |
| `gl`      | Group Leader  - machine & member management |
| `tl`      | Team Leader  - standard dashboard |
| `pengawas`| Supervisor  - standard dashboard |
| `tv`      | TV display only  - restricted to `/admin/tv` and `/admin/status` |

**RoleMiddleware** (`app/Http/Middleware/RoleMiddleware.php`):
- Applied to admin-only routes via `middleware('role:admin')`.
- Applied to gl-and-above via `middleware('role:admin,gl')`.
- TV role is further isolated by **TvRestrictMiddleware** which is applied globally to every web request.

---

## 2. Middleware Stack (in order of execution)

Every web request passes through the following middleware layers:

```
1. Laravel Default Web Middleware (sessions, cookies, CSRF)
2. SecurityHeaders            - injects security headers on every response
3. TvRestrictMiddleware       - globally restricts TV role
4. [Route-specific] auth      - requires valid authenticated session
5. [Route-specific] role      - checks user has required role
6. [API routes] internal.request  - verifies request originates from our frontend
7. [Rate limited] throttle    - enforces per-IP request limits
```

---

## 3. HTTP Security Headers

Implemented via `app/Http/Middleware/SecurityHeaders.php`  - applied globally to all web responses.

| Header | Value | Purpose |
|--------|-------|---------|
| `X-Frame-Options` | `SAMEORIGIN` | Prevents clickjacking |
| `X-Content-Type-Options` | `nosniff` | Prevents MIME sniffing |
| `X-XSS-Protection` | `1; mode=block` | Legacy browser XSS filter |
| `Referrer-Policy` | `strict-origin-when-cross-origin` | Limits referrer data leakage |
| `Permissions-Policy` | Denies camera, mic, geolocation, payment, USB | Locks down browser APIs |
| `Content-Security-Policy` | See below | Controls resource loading |
| `Strict-Transport-Security` | `max-age=31536000; includeSubDomains` | Enforces HTTPS (production only) |
| `X-Powered-By` | Removed | Prevents PHP version fingerprinting |
| `Server` | Removed | Prevents server software fingerprinting |

### Content Security Policy Breakdown

```
default-src 'self'
script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com
style-src  'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net
font-src   'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com data:
img-src    'self' data: blob:
connect-src 'self'
frame-ancestors 'self'
base-uri 'self'
form-action 'self'
object-src 'none'
upgrade-insecure-requests
```

> **Note:** `unsafe-inline` is required for the existing Blade views which use inline scripts and styles. When views are refactored to use nonces (future improvement), this can be removed.

---

## 4. Rate Limiting

Protects against brute-force, DoS, and resource exhaustion.

| Route | Limit | Reason |
|-------|-------|--------|
| `POST /login` | 10 req/min per IP | Brute-force protection |
| `GET /admin/status` | 120 req/min per IP | TV board polling  - high enough for auto-refresh, low enough to prevent DoS |
| `GET /admin/reports/export*` | 10 req/min per IP | Prevents export-based server exhaustion |
| `GET /admin/absence/export*` | 10 req/min per IP | Same reason |
| `GET /api/*` | 120 req/min per IP | Internal API rate limit |

---

## 5. Internal API Protection

Routes under `/api/*` are protected by **three layers**:

1. **`auth` middleware**  - requires a valid session cookie. No session = redirect to login.
2. **`internal.request` middleware**  - verifies the request originates from our authenticated frontend by checking:
   - `X-Requested-With: XMLHttpRequest` header (sent by Axios automatically), OR
   - `Referer` header starts with `APP_URL` (set by browsers for same-origin requests), OR
   - `Accept: application/json` header
3. **`throttle:120,1`**  - rate limiting

This means a plain `curl /api/machines/floor-plan` won't work even with a stolen session cookie unless the attacker also mimics the correct request headers.

**Suspicious access attempts are logged** to `storage/logs/` with IP, path, User-Agent, and Referer.

---

## 6. CSRF Protection

All `POST`, `PUT`, `PATCH`, `DELETE` requests require a valid CSRF token. This is enforced by Laravel's built-in `VerifyCsrfToken` middleware which is part of the default web middleware stack.

- CSRF tokens are regenerated on logout.
- Session cookies are `SameSite=strict` in production, adding an extra layer.

---

## 7. Session Security

| Setting | Value | Description |
|---------|-------|-------------|
| `SESSION_ENCRYPT` | `true` | All session data encrypted at rest using APP_KEY |
| `SESSION_SECURE_COOKIE` | `true` | Session cookie only sent over HTTPS |
| `SESSION_SAME_SITE` | `strict` | Cookie not sent in cross-site requests |
| `SESSION_HTTP_ONLY` | `true` | JavaScript cannot read the session cookie |
| `SESSION_LIFETIME` | `120` minutes | Sessions expire after 2 hours of inactivity |

---

## 8. Input Validation

All user-supplied data is validated before use. Key highlights:

- **Login**: validated with `required|string`  - no extra chars pass through.
- **User creation**: `alpha_dash` constraint on username, `min:6` on password, role validated against enum.
- **Absence/Assignment**: all dates use `date` rule, shifts validated `in:A,B`, factory/shift required.
- **File uploads**: image uploads validated with `mimes:jpeg,jpg,png,webp|max:3072` (3MB cap), auto-resized to 1280px max.
- **Base64 photos**: validated against allowed MIME types AND magic-byte signature check  - rejects disguised non-image uploads even if the data URI header claims it's an image.

---

## 9. File Upload Security

- **Regular uploads**: validated via Laravel's `mimes` + `max` rules before storage.
- **Base64 uploads**: `storeBase64Photo()` in `MemberController` validates:
  1. Data URI prefix must be `data:image`
  2. Decoded binary is checked against JPEG/PNG/GIF/WebP **magic bytes**
  3. Extension must be in the allowed list: `[jpeg, jpg, png, gif, webp]`

---

## 10. Error Handling & Information Disclosure

- **`APP_DEBUG=false`** in production  - Laravel shows generic error pages, no stack traces.
- **`AssignmentController`**: removed the `?debug=1` endpoint that exposed internal data. Errors now log server-side and return `{ ok: false, message: '...' }`.
- **`MachineFloorPlanController`**: all catch blocks return generic `{ message: '...' }` instead of raw exception messages.
- All internal errors are logged to `storage/logs/` with full context for debugging.

---

## 11. Mass Deletion Guard

The `DELETE /admin/members/clear-all` endpoint requires an explicit `X-Confirm-Action: DELETE_ALL_MEMBERS` header. This prevents:
- Accidental triggering from browser-side bugs
- Automated scripted attacks targeting the endpoint
- CSRF-adjacent mass-delete attacks

The header must be explicitly set in the JavaScript making the request.

---

## 12. Logging

| Setting | Value |
|---------|-------|
| `LOG_CHANNEL` | `stack` |
| `LOG_STACK` | `daily` |
| `LOG_LEVEL` | `warning` (production) |
| Retention | 14 days (`LOG_DAILY_DAYS=14`) |

Daily rotation prevents the log file from growing unboundedly. Warning level ensures only actionable events are logged in production.

---

## 13. Database Security

- All queries use **Eloquent ORM** or parameterized queries  - no raw string interpolation into SQL.
- Mass assignment is protected via `$fillable` arrays on all models.
- The `password` field is never returned in JSON (hidden in model).

> **Deployment note:** Set a strong `DB_PASSWORD` on the production server before going live. The current blank password is for local development only.

---

## Known Limitations & Future Improvements

| Item | Priority | Notes |
|------|----------|-------|
| Remove `unsafe-inline` from CSP | Medium | Requires refactoring Blade views to use nonces |
| Add 2FA for admin accounts | Medium | Laravel Fortify can provide this |
| Add failed login alerting | Low | Log anomalous login patterns to alert channel |
| Harden `getDynamicJenis()` | Low | Raw `SHOW COLUMNS` query is safe but noisy  - cache the result |
