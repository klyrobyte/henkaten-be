# Administrative System & Security Scoping  - Henkaten Dashboard

This document details the multi-tenant administrative system and factory-scoped visibility implemented in May 2026.

## 1. Role Hierarchy

The system now supports two primary administrative roles:

| Role | Access Level | Scoping |
| :--- | :--- | :--- |
| **Super Admin** | Full access to all menus, configurations, and data. | Global (all factories) |
| **Admin** | Limited access to management modules and Dashboard. | Scoped (assigned factories only) |

### Other Roles
- **TL / GL**: Scoped to assigned factory and shift.
- **Pengawas**: Scoped to assigned factory and shift.
- **TV**: Read-only access to specific factory TV boards.

## 2. Factory Scoping (Multi-Tenancy)

Normal admins are now restricted to a set of assigned factories. This affects visibility and management capabilities across the application.

### Implementation Details
- **User Model**: Added `isSuperAdmin()` and `isAdmin()` helpers. The `factory` attribute is now cast to an `array` to support multiple assignments.
- **Controller Filtering**: All management controllers (Member, Machine, Section, Log, Absence, Assignment) now filter queries based on the authenticated user's factory scope.
- **Context Security**: `DashboardController::setContext` validates that requested factories are within the user's allowed scope.
- **UI/UX**: 
    - Factory selectors are automatically filtered.
    - Selectors are `disabled` if the user only has one allowed factory.
    - Sidebar menu items are filtered; "Group (Factory)" configuration is hidden from normal admins.

## 3. User Management Restrictions

To ensure system integrity, normal admins have restricted management capabilities:
- **Visibility**: Normal admins only see users who have at least one overlapping factory assignment and who are NOT Super Admins.
- **Creation/Editing**: 
    - Normal admins cannot create or edit Super Admin accounts.
    - Maximum role assignable by a normal admin is "Admin".
    - Factory assignment for new users is automatically locked to the admin's own scope.

## 4. Security Hardening Compliance

All changes follow the established security hardening protocols:
- **CSRF Protection**: All state-changing requests use CSRF tokens.
- **API Security**: New and modified endpoints utilize the `APP_API_SECRET` / `api_nonce` validation via `VerifyAppSecret` middleware.
- **Scoped Authorization**: Server-side checks (403 Forbidden) prevent unauthorized access via direct URL manipulation or query parameter spoofing.
- **Input Validation**: Strict validation on all factory and role parameters.

## 5. Deployment Notes
- Ensure the `users` table `role` enum is updated: `ALTER TABLE users MODIFY COLUMN role ENUM('superadmin', 'admin', 'tl', 'gl', 'pengawas', 'tv')`.
- Clear cache: `php artisan cache:clear && php artisan config:clear`.
- Scribe documentation should be regenerated to reflect the updated controller signatures.
