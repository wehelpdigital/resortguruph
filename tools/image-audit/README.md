# Image audit tools

Scripts used to find and replace wrong / reused images on the public site
(November 2026). Run every PHP script from the project root; they boot
Laravel and use the default (shared) DB connection.

| Script | What it does |
|---|---|
| `inventory.php <out.json>` | Lists every image used by content blocks, tourist spots, fiestas and blog covers, with the subjects (captions/names) it is shown for. Files shown for more than 3 unrelated subjects are reused placeholders. |
| `fetch.php <queries.json> <outdir>` | For `[{id, subject, query, alt_query}]`, searches Wikimedia Commons for CC BY / CC BY-SA / CC0 / public-domain landscape photos, waits and retries on HTTP 429, and renders review sheets (`sheet-NN.png`: current image + 5 candidates per subject). Needs `subjects_indexed.json` next to it to show the current image. |
| `apply.php <dir> [--dry]` | Reads `<dir>/work-*/picks.json` + candidates, downloads the picked photos at 1280px into `storage/app/public/rg-media/places` or `blog-covers`, records author/licence/source in `rg_media`, and repoints only the wrong usages. Writes a rollback backup. Skips photos it already stored. `EXCLUDE=1,2,3` skips ids. |
| `generic_slides.php [--apply]` | Hero-slider slides with generic captions ("Tourist spot in the Philippines") get a real spot photo from the same page, captioned with the spot's name. |

## Status

- Round 1: 355 subjects reviewed; 294 photos replaced (582 blocks, 83 blog
  covers, 2 tourist spots). 14 downloads failed because Wikimedia rate
  limited the IP; re-running `apply.php` picks them up.
- Round 2 (`remaining-subjects.json`, 132 subjects, mostly more malls /
  districts behind the old `food-locations/generic.jpg` placeholder and
  ~50 blog covers) was started but stopped: Commons returned HTTP 429 for
  hours. Resume once the rate limit clears.
- `no-free-photo-subjects.txt`: places with no usable free photo on
  Commons. These need the owner's own photos (or licensed stock).

Picks must show the actual place / dish / topic. Do not use a photo of a
*different* specific place as a stand-in (another mall, church or golf
course); leave it for a real photo instead.
