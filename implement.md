# 🛠️ TASK BRIEF — Backend + UI Updates

> **For AI Agent:** Read every section carefully before writing any code.
> Apply all changes atomically per section. Follow the project's existing **site color config**, **security rules**, and **code hardening standards** throughout. Do not introduce new color tokens or design patterns that conflict with the current theme.

---

## 1. Super Admin — "Master Data" Page (New Section)

### Scope
- Create a **new route, controller, and view** under the Super Admin panel or on the super admin sidebar.
- Route group: `/admin/master-data`
- this route scoped, normal admin cannot access or see this master data same as "global logs"
- This section manages "Laporan (Report) data"  that is displayed in **History** on **TV Mode**, for example:
  TABLE problem_logs;
  TABLE absence_records;
  TABLE absence_summaries;
  TABLE absence_reasons;
  TABLE assignment_replacements;
  TABLE daily_assignments;
  TABLE machine_statuses;
  TABLE global_logs;
all of that used on 📋 HISTORY at tv modes, i want it can read and shown on "/admin/master-data" can be edit from  /admin/master-data 
- also add sweet alert alert system when super admin want to delet all data like "are you sure bout it? type 'yes i responsible for this'"

### Backend Requirements
- Full **CRUD** (Create, Read, Update, Delete) for all "📋 HISTORY" or `laporan` records.
- Apply **authorization middleware** — Super Admin role only. Enforce at both route and controller level.
- Validate all inputs server-side. Sanitize outputs to prevent XSS. Use CSRF protection on all forms/requests.
- Soft-delete preferred; include restore and force-delete actions.
- Return proper HTTP status codes. Log all destructive actions (delete, force-delete) to the audit log.

### Frontend / UI Requirements
- **Design language:** Tier-1 modern big-tech startup — think Linears, Vercel, or Notion's admin panels just use /admin/reports.
- Layout: Full-width data table with top action bar (search, filter, "+ Add Laporan" CTA).
- Table columns: `ID`, `Judul`, `Tanggal`, `Status`, `Aksi` (Edit / Delete).
- Use a **slide-over panel or modal** for Create & Edit forms — no full page redirects.
- Empty state: illustrated placeholder with a clear call-to-action.
- Loading skeleton on table fetch.
- Toast/snackbar feedback on every CRUD action (success & error).
- Responsive — works cleanly down to 1280px wide.
- Adhere strictly to the **site color config** (do not invent new palette tokens).

---

## 2. `tv-rs-card` — "TOTAL MP" Must Be Absence-Independent

### Problem
`TOTAL MP` on the `tv-rs-card` component is currently influenced by the absence/attendance system, which is incorrect.

### Required Behavior
- `TOTAL MP` **must only change when a Member is created or deleted** — never when attendance data changes.
- Source of truth: count of **active members** in the relevant group/factory, not any attendance or shift table.
- Make this value **dynamic** (real-time or near-real-time via polling/websocket — match the existing pattern in the codebase).
- Remove or bypass any attendance-related joins/filters that currently affect this count.
- Add a comment in the code clearly stating: `// TOTAL MP: counts active members only — NOT affected by absence/attendance data`.

---

## 3. `/admin/group` — "Tambah Factory" Modal: New Field "Detail Departemen"

### Backend Requirements
- Add a new nullable string column `detail_departemen` to the `factories` (or equivalent) table via migration.
- Update the `Factory` model's `$fillable` array to include `detail_departemen`.
- Update store and update validation rules: `detail_departemen` — `nullable|string|max:255`.
- Sanitize on save.

### Frontend Requirements
- In the **"Tambah Factory"** modal, add a new text input field **below** the existing Factory Name field:
  - **Label:** `Detail Departemen`
  - **Placeholder:** e.g., `Resin Injection Departemen`
  - **Input name/id:** `detail_departemen`
- Also update the **Edit Factory** modal with the same field (pre-populated).

### Template Integration
The value must feed into this existing frontend template string — **do not alter the template structure or styles**, only ensure the variable is passed and rendered:

```html
<span style="
  font-family: 'Roboto Condensed', sans-serif;
  font-weight: 700;
  font-size: 14px;
  letter-spacing: 1.2px;
  color: rgba(255,255,255,.85);
  text-transform: uppercase;
  white-space: nowrap;
">
  {{ $factory }} - {{ $factoryDetails }}
</span>
```

- Map `$factory` → factory name.
- Map `$factoryDetails` → `detail_departemen` value.
- If `detail_departemen` is null/empty, hide the `" - {{ $factoryDetails }}"` portion gracefully (conditional rendering).

---

## 4. TV Mode — "HENKATEN BOARD" White Stroke Treatment

### Requirement
- Add a **subtle white stroke/outline** to the **"HENKATEN BOARD"** text/logo element on all TV Mode views.
- Stroke style: thin, crisp — **not a glow, not a shadow**. Use CSS `text-stroke` or SVG `stroke` depending on the current implementation.
- Suggested value: `-webkit-text-stroke: 1px rgba(255, 255, 255, 0.6);` — adjust weight to match visual balance.
- Must not break existing typography sizing, weight, or letter-spacing.
- Test on dark backgrounds (the primary TV Mode environment).

---

## 5. TV Mode — Remove Date Label from Date Bar

### Requirement
- On **all TV Mode views**, remove the visible **date label** from the **date bar** component.
- The date bar itself (the bar/strip element) may remain if it serves a layout purpose — only the **text label showing the date** must be hidden/removed.
- Do not use `visibility: hidden` (it leaves a gap) — use `display: none` or remove the element from the template entirely.
- Confirm removal across **all TV Mode route views** (not just one variant).

---

## Global Constraints (Apply to ALL Changes)

| Rule | Detail |
|---|---|
| **Security** | CSRF on all state-changing requests. Auth middleware on all new routes. Input validated server-side. Output escaped. No raw queries — use the ORM. |
| **Hardening** | No sensitive data exposed in JS globals or HTML source. Rate-limit new API endpoints if applicable. |
| **Color Config** | Use existing CSS variables / Blade config values. Zero new palette tokens unless explicitly approved. |
| **Code Style** | Match the existing codebase conventions (naming, indentation, comment style). |
| **No Regressions** | Changes to shared components (tv-rs-card, modals) must not break other views that consume them. |