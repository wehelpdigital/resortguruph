# Dedicated task: Destination pages → mother-app block builder

Goal: make the destination cluster pages (`/destinations/{cluster}`, e.g. `/destinations/north-luzon`,
`/destinations/visayas`) editable as **blocks in the btc-check builder** (live + backend), exactly like the
homepage, while keeping the current design pixel-identical and SEO strong with dynamic schema.

## Current state (pre-conversion)
- Route: `Route::get('/destinations/{cluster}', [DestinationsController::class, 'cluster'])`.
- View: `resources/views/destinations/cluster.blade.php` (ONE shared, hardcoded template used by all 13 clusters).
- Data source: `DestinationsController::clusterMetadata()` (name/tagline/intro_html/meta_description per cluster) +
  live queries for keywords, tourist spots (`rg_tourist_spots`), reviews (generated in-blade from real spots),
  hashtags (generated in-blade from keywords), other-region cards, per-keyword photos.
- Sections (top → bottom): breadcrumb; hero (faded spot bg + Tahu region name); "What's In {region}?" heading +
  evocative intro + keyword photo cards; "Featured *Tourist Spots* in {region}" 3-col crossfading clickable cards;
  "What *Travelers Say* About {region}" testimonials (placeholder, spot-referencing); "Explore Other Regions"
  (homepage region-card style w/ crossfading circles); dynamic "#Tags".
- SEO: BreadcrumbList + CollectionPage + dynamic TouristAttraction ItemList JSON-LD. (No Review schema — reviews
  are placeholder; adding fake-review markup violates Google policy. Add once real reviews exist.)

## Architecture decision
Use ONE **shared "destination-template"** set of blocks (owner: a `rg_static_pages` row, e.g. slug
`destination-template`) rendered for every cluster with **per-cluster context** injected by the controller
(region name, keywords, spots, reviews, hashtags, other regions, images). Editing the template updates all 13
destination pages uniformly (they are intentionally identical). Mirrors the homepage model, but cluster data is
dynamic context rather than static block payload. Region-specific literals in copy use `{region}` tokens.

## Phases (see session todos)
- **Phase 0** — Study homepage wiring: `HomeController::index()` block branch, `home.blocks` view,
  `RgContentBlock::forOwner('static_page', $page->id)`, `BlockRenderer::renderBlocks($blocks, $ctx)` dispatch,
  live-edit token (`?_lt=` + `LiveEditToken` + `public/js/rg-live-edit.js`).
- **Phase 1a** — Refactor `DestinationsController::cluster()`: move the in-blade reviews/hashtags/cols/spots logic
  into a reusable context array. Add a block branch: if the `destination-template` page has blocks, render via
  `BlockRenderer` with the cluster context; else fall back to `cluster.blade.php` (no behaviour change until seeded).
- **Phase 1b** — `resources/views/destinations/blocks.blade.php` wrapper (mirror `home.blocks`), keep the dynamic
  BreadcrumbList/CollectionPage/TouristAttraction JSON-LD.
- **Phase 1c** — Add `dest_*` renderers to `app/Services/BlockRenderer.php`: `dest_hero`, `dest_intro_keywords`,
  `dest_featured_spots`, `dest_testimonials`, `dest_explore_regions`, `dest_hashtags`. Each reproduces the current
  markup from context. Reuse `rgCircleAssets()`/`rgCircleHtml()` and the `_fadegallery` crossfade.
- **Phase 1d** — Seeder to create the `destination-template` page + its blocks (payloads = current section
  titles/eyebrows/descriptions/toggles). Wire the controller to it.
- **Phase 1e** — Verify all 13 clusters render identically via blocks (visual parity + schema).
- **Phase 2** — btc-check builder: add `dest_*` to `builder.blade.php` (HOME_ARRAY_SCHEMAS-style schemas, editor
  forms, media pickers, previews); add a "Destinations" builder route/entry; wire live edit for the template.
- **Phase 3** — Per-section SEO settings + full QA (live + backend builder).

## Recommendation
Convert once the visual design is locked. The design is still being actively refined; converting a moving target
means redoing block payloads/renderers on each tweak. Foundation (Phase 1a/1b) is design-agnostic and can start
anytime behind the fallback.

## Key touchpoints
- Frontend: `app/Http/Controllers/DestinationsController.php`, `app/Services/BlockRenderer.php`,
  `resources/views/destinations/cluster.blade.php` + `_fadegallery.blade.php`, `app/Models/RgContentBlock.php`,
  `database/seeders/`.
- Mother app: `C:/xampp/htdocs/btc-check/resources/views/resortGuruAdmin/blocks/builder.blade.php`,
  `.../resortGuruAdmin/*` controllers/routes.
