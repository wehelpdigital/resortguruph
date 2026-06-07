<?php

namespace App\Http\Controllers;

use App\Models\RgFiesta;
use Illuminate\Support\Facades\Cache;

/**
 * Activities hub at /philippine-tourist-activities-adventures-what-to-do.
 *
 * Categories + activity items live as a static array — the surface is a
 * directory listing, not a CMS, so a DB-backed CRUD is overkill for
 * Phase 1. When we want owner-submitted activity providers, we'll move
 * each item into rg_activity_items and seed from this array.
 *
 * The Festival Tourism card pulls 3 fiesta covers off RgFiesta so it
 * always shows current festival photography and stays in sync as new
 * fiestas get covers added.
 */
class ActivitiesController extends Controller
{
    public function index()
    {
        // Three fiesta covers used as the rotating background for the
        // Festival Tourism card. Prefer iconic / well-known fiestas
        // first; fall back to the first three published rows with a
        // cover image if any of the named ones aren't seeded yet.
        $fiestaCovers = Cache::remember('activities-fiesta-covers', 600, function () {
            $preferred = ['sinulog', 'masskara', 'panagbenga', 'ati-atihan', 'dinagyang', 'kadayawan'];
            $rows = RgFiesta::query()
                ->where('is_published', true)
                ->whereNotNull('cover_image_path')
                ->get(['slug', 'cover_image_path'])
                ->keyBy('slug');

            $picks = [];
            foreach ($preferred as $slug) {
                if ($rows->has($slug) && count($picks) < 3) {
                    $picks[] = $rows[$slug]->coverUrl();
                }
            }
            if (count($picks) < 3) {
                foreach ($rows as $row) {
                    if (count($picks) >= 3) break;
                    if (in_array($row->coverUrl(), $picks, true)) continue;
                    $picks[] = $row->coverUrl();
                }
            }
            return $picks;
        });

        return view('activities.index', [
            'categories' => $this->categories(),
            'fiestaCovers' => $fiestaCovers,
            'fiestasUrl' => route('fiestas.index'),
        ]);
    }

    /**
     * The six tourist-activity categories shown on the hub. `theme`
     * controls the per-card accent. Each item carries a stable `slug`
     * so disk-based image files line up by name; `description` is a
     * one-line Filipino-blogger blurb shown under the activity name on
     * the card. Description text is filled in by the
     * activities_research.json data file and applied via
     * ApplyActivityResearchSeeder so we can iterate copy without
     * touching this controller.
     *
     * Intros and descriptions follow the Resort Guru content rules:
     * no em-dashes, no banned AI vocab, no peso prices, RJ Dexplorer
     * DIY-traveler voice.
     */
    private function categories(): array
    {
        // Optional override data from the research seeder. If
        // descriptions have been researched and applied to the JSON
        // file, they win over the static defaults in $items below.
        $researchPath = database_path('data/activities_research.json');
        $research = is_file($researchPath)
            ? json_decode(file_get_contents($researchPath), true) ?: []
            : [];

        $cats = [
            [
                'key' => 'water',
                'label' => 'Water Adventures',
                'icon' => '🌊',
                'theme' => 'water',
                'intro' => 'Reefs, wrecks, rivers, hot springs, waterfalls. The Philippines has more shoreline than most countries have land, and just about every kind of water activity is on the menu somewhere along it.',
                'items' => [
                    ['slug' => 'scuba-diving', 'name' => 'Scuba Diving', 'note' => 'Reef, wreck, muck, and blackwater diving'],
                    ['slug' => 'freediving', 'name' => 'Freediving'],
                    ['slug' => 'snorkeling-island-hopping', 'name' => 'Snorkeling & Island Hopping'],
                    ['slug' => 'canyoneering', 'name' => 'Canyoneering / Canyoning'],
                    ['slug' => 'surfing', 'name' => 'Surfing'],
                    ['slug' => 'whitewater-rafting', 'name' => 'Whitewater Rafting'],
                    ['slug' => 'wakeboarding', 'name' => 'Wakeboarding & Kneeboarding'],
                    ['slug' => 'kiteboarding', 'name' => 'Kiteboarding / Kitesurfing'],
                    ['slug' => 'sea-kayaking', 'name' => 'Sea Kayaking'],
                    ['slug' => 'sup-paddleboarding', 'name' => 'Stand-Up Paddleboarding (SUP)'],
                    ['slug' => 'jet-skiing', 'name' => 'Jet Skiing & Banana Boating'],
                    ['slug' => 'flyboarding', 'name' => 'Flyboarding'],
                    ['slug' => 'parasailing', 'name' => 'Parasailing'],
                    ['slug' => 'river-trekking', 'name' => 'River Trekking / Wading'],
                    ['slug' => 'skimboarding', 'name' => 'Skimboarding'],
                    ['slug' => 'paraw-sailing', 'name' => 'Sailing / Paraw Sailing'],
                    ['slug' => 'whale-shark-watching', 'name' => 'Whale Shark Snorkeling / Watching'],
                    ['slug' => 'sardine-run', 'name' => 'Sardine Run Diving / Snorkeling'],
                    ['slug' => 'dolphin-whale-watching', 'name' => 'Dolphin & Whale Watching'],
                    ['slug' => 'hot-spring-bathing', 'name' => 'Hot Spring & Mud Pool Bathing'],
                    ['slug' => 'waterfall-jumping', 'name' => 'Waterfall Jumping'],
                    ['slug' => 'bioluminescent-plankton', 'name' => 'Bioluminescent Plankton Tours'],
                    ['slug' => 'subwing', 'name' => 'Subwing'],
                    ['slug' => 'bamboo-rafting', 'name' => 'Bamboo Rafting'],
                    ['slug' => 'river-tubing', 'name' => 'River Tubing / Water Tubing'],
                    ['slug' => 'deep-sea-fishing', 'name' => 'Deep Sea Fishing / Game Fishing'],
                    ['slug' => 'helmet-diving', 'name' => 'Helmet Diving / Sea Walking'],
                    ['slug' => 'e-foiling', 'name' => 'E-foiling / Hydrofoiling'],
                    ['slug' => 'underwater-scooter', 'name' => 'Underwater Scooter (Scuba-doo)'],
                    ['slug' => 'mermaid-swimming', 'name' => 'Mermaid Swimming Lessons'],
                    ['slug' => 'firefly-cruising', 'name' => 'Firefly River Cruising'],
                ],
            ],
            [
                'key' => 'land',
                'label' => 'Land Adventures',
                'icon' => '⛰️',
                'theme' => 'land',
                'intro' => 'Volcanoes you can climb in a weekend, caves you can crawl through, sand dunes you can ride down, and trails that go for days. Land adventures are how the Philippines builds memory.',
                'items' => [
                    ['slug' => 'hiking-mountaineering', 'name' => 'Hiking & Mountaineering'],
                    ['slug' => 'volcano-trekking', 'name' => 'Volcano Trekking'],
                    ['slug' => 'atv-offroading', 'name' => 'ATV & 4x4 Off-Roading'],
                    ['slug' => 'spelunking', 'name' => 'Spelunking (Cave Exploration)'],
                    ['slug' => 'sandboarding', 'name' => 'Sandboarding'],
                    ['slug' => 'rock-climbing', 'name' => 'Rock Climbing & Bouldering'],
                    ['slug' => 'mountain-biking', 'name' => 'Mountain Biking'],
                    ['slug' => 'ziplining', 'name' => 'Ziplining'],
                    ['slug' => 'camping-glamping', 'name' => 'Camping & Glamping'],
                    ['slug' => 'survival-bushcraft', 'name' => 'Survival Training / Bushcraft Camps'],
                    ['slug' => 'dirt-biking', 'name' => 'Dirt Biking / Motocross'],
                    ['slug' => 'horseback-riding', 'name' => 'Horseback Riding'],
                    ['slug' => 'canopy-walk', 'name' => 'Canopy Walk / Tree Top Adventures'],
                    ['slug' => 'bungee-jumping', 'name' => 'Bungee Jumping / Canyon Swing'],
                    ['slug' => 'rappelling', 'name' => 'Rappelling / Abseiling'],
                    ['slug' => 'wildlife-safari', 'name' => 'Wildlife Safari Tours'],
                    ['slug' => 'longboarding', 'name' => 'Downhill Longboarding / Ligiron Racing'],
                    ['slug' => 'bird-watching', 'name' => 'Bird Watching'],
                    ['slug' => 'trail-running', 'name' => 'Trail Running / Eco-Trail Racing'],
                ],
            ],
            [
                'key' => 'air',
                'label' => 'Air Adventures',
                'icon' => '🪂',
                'theme' => 'air',
                'intro' => 'A birds-eye view of the Philippines, from the slow drift of a paramotor to a tandem skydive over a coral reef. The angles from above re-frame how big this country really is.',
                'items' => [
                    ['slug' => 'paragliding', 'name' => 'Paragliding'],
                    ['slug' => 'skydiving', 'name' => 'Skydiving'],
                    ['slug' => 'ultralight-flying', 'name' => 'Ultralight Flying'],
                    ['slug' => 'hot-air-ballooning', 'name' => 'Hot Air Ballooning'],
                    ['slug' => 'helicopter-tours', 'name' => 'Helicopter Aerial Tours'],
                    ['slug' => 'paramotoring', 'name' => 'Paramotoring'],
                    ['slug' => 'gyrocopter-flying', 'name' => 'Gyrocopter Flying'],
                    ['slug' => 'sky-walk', 'name' => 'Sky Walk / Edge Coaster'],
                ],
            ],
            [
                'key' => 'entertainment',
                'label' => 'Entertainment, Casinos & Theme Parks',
                'icon' => '🎪',
                'theme' => 'entertainment',
                'intro' => 'When you want the day to be loud, lit, and fully planned. Theme parks, integrated resorts, KTV rooms, and after-dark spots that keep the city humming until sunrise.',
                'items' => [
                    ['slug' => 'casino-gaming', 'name' => 'Casino Gaming & Integrated Resorts'],
                    ['slug' => 'theme-parks', 'name' => 'Theme Parks & Amusement Rides'],
                    ['slug' => 'water-parks', 'name' => 'Water Parks'],
                    ['slug' => 'escape-rooms', 'name' => 'Escape Rooms'],
                    ['slug' => 'interactive-museums', 'name' => 'Interactive Museums & Optical Illusion Art'],
                    ['slug' => 'go-karting', 'name' => 'Go-Karting'],
                    ['slug' => 'paintball-airsoft', 'name' => 'Paintball & Airsoft'],
                    ['slug' => 'target-shooting', 'name' => 'Target Shooting / Firing Ranges'],
                    ['slug' => 'bowling-billiards', 'name' => 'Bowling & Billiards / Pool Halls'],
                    ['slug' => 'karaoke-ktv', 'name' => 'Karaoke / KTV Rooms'],
                    ['slug' => 'nightclubbing', 'name' => 'Nightclubbing & Bar Hopping'],
                    ['slug' => 'live-music', 'name' => 'Live Music Gigs & Concerts'],
                ],
            ],
            [
                'key' => 'cultural',
                'label' => 'Cultural, Arts & Heritage',
                'icon' => '🎭',
                'theme' => 'cultural',
                'intro' => 'The Philippines is a country of layered histories, indigenous craft, and lived religion. Heritage walks, weaving workshops, tattoo pilgrimages, and 7,000 fiestas a year keep all of it visible.',
                'items' => [
                    ['slug' => 'heritage-walking-tours', 'name' => 'Historical & Heritage Walking Tours'],
                    ['slug' => 'bambike-tours', 'name' => 'Bamboo Bike Tours (Bambike)'],
                    ['slug' => 'cultural-shows', 'name' => 'Cultural Shows & Traditional Dance Presentations'],
                    ['slug' => 'theater-musicals', 'name' => 'Theater, Musicals & Stage Plays'],
                    ['slug' => 'museum-art-tours', 'name' => 'Museum & Art Gallery Tours'],
                    ['slug' => 'weaving-pottery', 'name' => 'Traditional Weaving & Pottery Workshops'],
                    ['slug' => 'mambabatok-tattoos', 'name' => 'Traditional Tattoo Tourism (Mambabatok)'],
                    ['slug' => 'indigenous-games', 'name' => 'Indigenous Games (Wooden Scooters, Bamboo Stilts)'],
                    ['slug' => 'visita-iglesia', 'name' => 'Religious Pilgrimages / Visita Iglesia'],
                    ['slug' => 'sabong', 'name' => 'Cockfight Watching (Sabong)'],
                    ['slug' => 'festival-tourism', 'name' => 'Festival Tourism', 'note' => 'Year-round fiestas, by region', 'is_festival_card' => true],
                ],
            ],
            [
                'key' => 'leisure',
                'label' => 'Leisure, Wellness & Lifestyle',
                'icon' => '💆',
                'theme' => 'leisure',
                'intro' => 'For the slower days. Long lunches, hot baths, sunset cruises, mall runs, and the kind of staycation that you book with no plan beyond room service.',
                'items' => [
                    ['slug' => 'spa-wellness', 'name' => 'Spa & Wellness Retreats'],
                    ['slug' => 'mega-mall-shopping', 'name' => 'Mega-Mall Shopping & Retail Therapy'],
                    ['slug' => 'food-tours', 'name' => 'Food Tours / Culinary Crawls / Street Food Tasting'],
                    ['slug' => 'brewery-tours', 'name' => 'Craft Beer Brewery & Distillery Tours'],
                    ['slug' => 'agritourism', 'name' => 'Agritourism, Farm Tours & Pick-and-Pay'],
                    ['slug' => 'staycations', 'name' => 'Staycations & Luxury Resort Lounging'],
                    ['slug' => 'sunset-cruises', 'name' => 'Sunset Dinner Cruises'],
                    ['slug' => 'golfing', 'name' => 'Golfing'],
                    ['slug' => 'oceanariums', 'name' => 'Oceanariums & Marine Parks'],
                ],
            ],
        ];

        // Merge in researched descriptions + on-disk image counts. The
        // view uses `images` to decide whether to render real photos
        // or fall back to the gradient backdrop.
        $imageDir = public_path('storage/rg-media/activities');
        foreach ($cats as &$cat) {
            foreach ($cat['items'] as &$item) {
                $slug = $item['slug'];
                if (isset($research[$slug]['description'])) {
                    $item['description'] = $research[$slug]['description'];
                }
                $images = [];
                if (is_dir($imageDir)) {
                    foreach ([1, 2, 3] as $n) {
                        $candidate = $imageDir . DIRECTORY_SEPARATOR . $slug . '-' . $n . '.jpg';
                        if (is_file($candidate)) {
                            $images[] = asset('storage/rg-media/activities/' . $slug . '-' . $n . '.jpg');
                        }
                    }
                }
                $item['images'] = $images;
            }
            unset($item);
        }
        unset($cat);

        return $cats;
    }
}
