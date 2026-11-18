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

# Publish to production (Laravel Cloud) from a local machine
php artisan rg:cloud-sync-db        # mirror every rg_* table from the shared DB into the Cloud DB
php artisan rg:cloud-sync-media     # upload storage/app/public (rg-media, rg-blocks) to the Cloud bucket
```

## Production (Laravel Cloud)

- Live at https://resortguruph-production-vsv3ln.laravel.cloud, auto-deploys from `main`. Cloud runs migrations on deploy.
- Cloud has its **own** MySQL database, separate from the shared `onmartph_axis` DB the btc-check admin edits. Admin edits only reach production after `php artisan rg:cloud-sync-db` (needs `CLOUD_DB_*` in local `.env`, public endpoint enabled, TLS on by default).
- The media library is committed under `storage/app/public` and ships with each deploy; composer's `post-install-cmd` creates the `public/storage` symlink. `/storage/{path}` falls back to `StorageFileController` (streams the local file, else redirects to the bucket).
- Runtime uploads (owner dashboard) go to `config('filesystems.media_disk')`: the Cloud bucket in production (auto-detected from `LARAVEL_CLOUD_DISK_CONFIG`), the `public` disk locally.
- Framework tables (sessions, cache, jobs) come from the guarded `2026_11_18_000000_create_framework_tables_if_missing` migration.
- `php artisan rg:content-snapshot` exports public content (personal data stripped) to `database/snapshots/content` for seeding a fresh DB. It is NOT committed by default because the GitHub repo is public; for an existing production DB use `rg:cloud-sync-db`.
- Never store absolute dev URLs (`http://resortguruph.test/...`) in content. The snapshot/sync rewrite them and the `RewriteLocalUrls` middleware is a safety net, but write root-relative `/storage/...` paths.
- Everything the pages load is self-hosted under `public/vendor` (Tailwind 3.4.17 play runtime + forms/typography, Splide 4.1.4, Sortable 1.15.2, Inter + Plus Jakarta Sans woff2). Only Google Maps embeds stay external.
- Homepage hero video is self-hosted at `public/videos/hero-philippines.mp4` (built from our own photos with ffmpeg); the old YouTube URL is kept in the block's `background_video_youtube`.

## Design system (public site)

- Tokens, header, drawer, footer live in `resources/views/layouts/partials/public-{theme,header,footer}.blade.php`.
- Logo: `layouts/partials/brand-logo.blade.php` (mark = `public/images/brand/logo-mark.svg`, the warty pig explorer). Set `rg_settings.logo_style = classic` to restore the original purple pig + Tahu wordmark.
- Photo heroes on hub/destination pages use the dark `rg-hero-dark` treatment; payload `hero_style: "light"` restores the white-washed look.
- Colors: `brand`/`blue` = ocean blue, `coral` = primary CTA, `sun` = highlight, `ink` = dark surfaces. Headings use Plus Jakarta Sans, body Inter, the Tahu script stays for brand accents.
- `--rg-header-h` (set by JS) drives sticky sub-navs and anchor offsets; don't hardcode header heights.
- Header (`public-header.blade.php`): one row with mega menus (Destinations / Food / Things to Do / Blog / For Business), a search overlay (`/` key or the search button) and the mobile drawer. Menu data comes from `App\Services\HomeShowcase` (cached). A page can make the header transparent over its hero with `@section('header_overlay', '1')` and pull the hero up by `var(--rg-header-h)` (see `home/showcase.blade.php`).
- Homepage is the hand-built `home/showcase.blade.php` (data: `HomeShowcase`); `rg_settings.home_layout = blocks` switches back to the block-builder homepage.
- Sub pages share the **page kit** (`layouts/partials/page-kit.blade.php`, CSS + small JS): `partials/page-hero.blade.php` (photo hero under the transparent header, crossfade when given several slides, breadcrumbs, kicker, chips, actions; sets `header_overlay` itself), `partials/page-aside.blade.php` (sticky "On this page" TOC built from the main column's h2s, plan card, share, business card; desktop >= 1280px) and the `pk-jump` chip bar for smaller screens. Mark the content column with `data-pk-main`.
- Guide content (`.pk-guide`: keyword pages, blog posts, fiestas) is restyled by the page kit with `!important` rules because seeded `custom_html` blocks inject per-page CSS (`.page-{slug} h2/p`). Keep the `page-{slug}` class on the article, not on wrappers that hold the aside.
- Admin HTML inside the two-column layouts goes through `App\Support\PageKit::balance()` so stray closing tags cannot push the sidebar out of the grid.
- Block-rendered heroes (`hub_hero`, `dest_hero_search`, `home_hero_centered` with a photo) slide under the header via `PageKit::leadsWithDarkHero()` + the `pk-pull` wrapper class.
- Homepage hero (`home_unified_search`) is "immersive" when it has a background video/image; set payload `hero_style: "light"` for the old faded look. Phones get the poster image instead of the YouTube iframe.

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
