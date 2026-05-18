# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this project is

Public-facing Laravel 12 frontend for **Resort Guru PH**, an SEO directory of Philippines resorts, hotels, and Airbnb stays. The companion super-admin lives in the **btc-check** "mother system" at `C:\xampp\htdocs\btc-check` under the "Resort Guru" sidebar entry (added under Ani-Senso, in `btc-check/resources/views/layouts/sidebar.blade.php`). Both apps share the same MySQL database (`onmartph_axis`), but only this frontend app owns the `rg_*` migrations.

## Architecture

- **Two Laravel apps, one DB**:
  - Mother super-admin (`btc-check`, Laravel 12 + Bootstrap 5 + Skote theme): manages keywords, SEO pages, owners, resorts, gold-points ledger, GCash approvals, blog, static pages, settings.
  - This frontend (`resortguruph`, Laravel 12 + Tailwind CDN): public site + resort-owner dashboard.
- **All new tables prefixed `rg_`**. The mother app's existing `users` and other tables are not touched.
- **Auth isolation**: distinct `APP_KEY`, distinct `SESSION_COOKIE=rg_session`. Mother app uses `users` for super-admins; this app uses `rg_owners` via a custom Eloquent provider (`config/auth.php`).
- **DB connection**: see `.env` (shared `onmartph_axis` MySQL). All app `.env`s should point at the same DB; only this app runs migrations.
- **Routing**: catch-all keyword route is the LAST line of `routes/web.php` — any new public route must be added before it.
- **Bidding engine (Phase 2, not yet wired)**: schema is in `rg_listings` with `bid_gp` as the sole sort key. Append-only `rg_gp_ledger` + `rg_gp_holds` is the GP accounting model (never mutate balances directly).

## Common commands

```bash
# Frontend dev server
php artisan serve --port=8001

# Run migrations
php artisan migrate

# Seed sample keywords, SEO pages, static pages, and blog posts
php artisan db:seed

# Storage symlink (uploads)
php artisan storage:link

# Clear compiled views after Blade edits
php artisan view:clear

# List routes
php artisan route:list
```

## Where things live

| What | Where |
|---|---|
| Public routes + dashboard | [routes/web.php](routes/web.php) |
| Public layout (Tailwind via CDN) | [resources/views/layouts/public.blade.php](resources/views/layouts/public.blade.php) |
| Dashboard layout | [resources/views/layouts/dashboard.blade.php](resources/views/layouts/dashboard.blade.php) |
| Auth controllers | [app/Http/Controllers/Auth](app/Http/Controllers/Auth/) (custom owner guard, NOT laravel/ui) |
| Eloquent models | [app/Models](app/Models/) (all prefixed `Rg*`) |
| Rg_* migrations | [database/migrations](database/migrations/) |
| Content seeders | [database/seeders](database/seeders/) |
| Keyword page renderer | [app/Http/Controllers/KeywordPageController.php](app/Http/Controllers/KeywordPageController.php) — increments pageview counter with bot-UA filter |
| Sitemap + robots | [app/Http/Controllers/SitemapController.php](app/Http/Controllers/SitemapController.php) — output cached 1 hour |
| Settings access | `\App\Models\RgSetting::get('key', $default)` (cached 5 min in `Cache::remember`) |

## Mother-app touchpoints

- Sidebar entry: `C:\xampp\htdocs\btc-check\resources\views\layouts\sidebar.blade.php` — Resort Guru block inserted after Ani-Senso closes.
- Controllers: `C:\xampp\htdocs\btc-check\app\Http\Controllers\resortGuruAdmin\` — 10 controllers (Dashboard, Keywords, SeoPages, Owners, Resorts, GoldPoints, GcashApprovals, Blog, StaticPages, Settings).
- Views: `C:\xampp\htdocs\btc-check\resources\views\resortGuruAdmin\` — uses Skote layout via `@extends('layouts.master')`, the `@component('components.breadcrumb')` pattern, Yajra DataTables AJAX, SweetAlert2 confirmations, Toastr notifications, TinyMCE editor.
- Routes: appended to bottom of `C:\xampp\htdocs\btc-check\routes\web.php` (BEFORE the catch-all `{any}` route).
- Mother app does NOT run migrations for `rg_*` tables; controllers use `DB::table()` with `Schema::hasTable()` guards.

## What shipped in Phase 0 + Phase 1

- Frontend Laravel 12 scaffolded + shared-DB config + owner auth guard
- 13 rg_* migrations created and run against shared MySQL
- Mother-app Resort Guru module: sidebar + 10 controllers + 18 admin views
- Public site: home, 10 keyword pages with JSON-LD, resort detail with branding colors, blog, static pages, contact, sitemap.xml, robots.txt
- Auth: register / login / forgot-password using `rg_owners`
- Owner dashboard: overview, profile, multi-step resort CRUD with media uploads + color picker, Gold Points ledger, GCash top-up flow with screenshot upload, activity log, stubs for deferred features
- Content seeders: 10 Philippines keywords with naturally-written 800+ word pages (no em-dashes), 4 static pages, 3 blog posts

## Deferred to future phases

- **Phase 2**: Bidding engine, listing claim/extension flow, outbid notifications, near-expiry scheduled job.
- **Phase 3**: Drag-drop block-based page builder (SortableJS), shared media library.
- **Phase 4**: AI content generation (admin-free, owner-paid in GP per token, OpenAI-compatible).
- **Phase 5**: Redis-backed traffic counter with bot filter, traffic reports, notifications module, tutorials, SMTP settings UI, redirect manager.

## Conventions

- Naming: tables `rg_*`, models `Rg*`, mother-app controllers under `resortGuruAdmin\` (camelCase namespace mirroring `aniSensoAdmin`).
- All GP mutations should wrap in DB transactions with `SELECT ... FOR UPDATE` (will be enforced in Phase 2 bidding logic).
- "GP to top" hint must be quantized to nearest `bid_top_hint_quantum_gp` (setting, default 50) to prevent probe-and-bid attacks.
- Confirmations: SweetAlert2 modal in admin, native confirm/modal in dashboard. No alert() boxes.
- Content style for SEO pages: no em-dashes (use commas or periods), keywords appear 4-6 times distributed naturally, FAQ JSON in `rg_seo_pages.faq_json`.

## Repository

- Remote: https://github.com/wehelpdigital/resortguruph.git
- Default branch: `main`
