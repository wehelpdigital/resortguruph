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
     * controls the 3-layer fading gradient backdrop and the accent
     * color used on each card. `items` is the flat activity list per
     * category, with optional `note` for clarification text and
     * optional `href` to link out (festival_tourism is the only one
     * wired to a real URL right now — the rest are inert until we add
     * per-activity destination pages).
     */
    private function categories(): array
    {
        return [
            [
                'key' => 'water',
                'label' => 'Water Adventures',
                'icon' => '🌊',
                'theme' => 'water',
                'intro' => 'Reefs, wrecks, rivers, hot springs, and waterfalls — the Philippines has more shoreline than most countries have land, and just about every kind of water activity is on the menu.',
                'items' => [
                    ['name' => 'Scuba Diving', 'note' => 'Reef, wreck, muck, and blackwater diving'],
                    ['name' => 'Freediving'],
                    ['name' => 'Snorkeling & Island Hopping'],
                    ['name' => 'Canyoneering / Canyoning'],
                    ['name' => 'Surfing'],
                    ['name' => 'Whitewater Rafting'],
                    ['name' => 'Wakeboarding & Kneeboarding'],
                    ['name' => 'Kiteboarding / Kitesurfing'],
                    ['name' => 'Sea Kayaking'],
                    ['name' => 'Stand-Up Paddleboarding (SUP)'],
                    ['name' => 'Jet Skiing & Banana Boating'],
                    ['name' => 'Flyboarding'],
                    ['name' => 'Parasailing'],
                    ['name' => 'River Trekking / Wading'],
                    ['name' => 'Skimboarding'],
                    ['name' => 'Sailing / Paraw Sailing'],
                    ['name' => 'Whale Shark Snorkeling / Watching'],
                    ['name' => 'Sardine Run Diving / Snorkeling'],
                    ['name' => 'Dolphin & Whale Watching'],
                    ['name' => 'Hot Spring & Mud Pool Bathing'],
                    ['name' => 'Waterfall Jumping'],
                    ['name' => 'Bioluminescent Plankton Tours'],
                    ['name' => 'Subwing'],
                    ['name' => 'Bamboo Rafting'],
                    ['name' => 'River Tubing / Water Tubing'],
                    ['name' => 'Deep Sea Fishing / Game Fishing'],
                    ['name' => 'Helmet Diving / Sea Walking'],
                    ['name' => 'E-foiling / Hydrofoiling'],
                    ['name' => 'Underwater Scooter (Scuba-doo)'],
                    ['name' => 'Mermaid Swimming Lessons'],
                    ['name' => 'Firefly River Cruising'],
                ],
            ],
            [
                'key' => 'land',
                'label' => 'Land Adventures',
                'icon' => '⛰️',
                'theme' => 'land',
                'intro' => 'Volcanoes you can climb, caves you can crawl through, sand dunes you can ride down, and trails that go for days. Land adventures are how the Philippines builds memory.',
                'items' => [
                    ['name' => 'Hiking & Mountaineering'],
                    ['name' => 'Volcano Trekking'],
                    ['name' => 'ATV & 4x4 Off-Roading'],
                    ['name' => 'Spelunking (Cave Exploration)'],
                    ['name' => 'Sandboarding'],
                    ['name' => 'Rock Climbing & Bouldering'],
                    ['name' => 'Mountain Biking'],
                    ['name' => 'Ziplining'],
                    ['name' => 'Camping & Glamping'],
                    ['name' => 'Survival Training / Bushcraft Camps'],
                    ['name' => 'Dirt Biking / Motocross'],
                    ['name' => 'Horseback Riding'],
                    ['name' => 'Canopy Walk / Tree Top Adventures'],
                    ['name' => 'Zorb Ball / Zorbing'],
                    ['name' => 'Bungee Jumping / Canyon Swing'],
                    ['name' => 'Rappelling / Abseiling'],
                    ['name' => 'Wildlife Safari Tours'],
                    ['name' => 'Downhill Longboarding / Ligiron Racing'],
                    ['name' => 'Bird Watching'],
                    ['name' => 'Trail Running / Eco-Trail Racing'],
                ],
            ],
            [
                'key' => 'air',
                'label' => 'Air Adventures',
                'icon' => '🪂',
                'theme' => 'air',
                'intro' => 'Birds-eye Philippines, from the slow drift of a paramotor to a tandem skydive over a coral reef. The viewpoints from above re-frame how big this country really is.',
                'items' => [
                    ['name' => 'Paragliding'],
                    ['name' => 'Skydiving'],
                    ['name' => 'Ultralight Flying'],
                    ['name' => 'Hot Air Ballooning'],
                    ['name' => 'Helicopter Aerial Tours'],
                    ['name' => 'Paramotoring'],
                    ['name' => 'Gyrocopter Flying'],
                    ['name' => 'Sky Walk / Edge Coaster'],
                ],
            ],
            [
                'key' => 'entertainment',
                'label' => 'Entertainment, Casinos & Theme Parks',
                'icon' => '🎪',
                'theme' => 'entertainment',
                'intro' => 'When you want the day to be loud, lit, and fully planned. Theme parks, integrated resorts, KTV rooms, and after-dark spots that keep the city humming.',
                'items' => [
                    ['name' => 'Casino Gaming & Integrated Resorts'],
                    ['name' => 'Theme Parks & Amusement Rides'],
                    ['name' => 'Water Parks'],
                    ['name' => 'Escape Rooms'],
                    ['name' => 'Interactive Museums & Optical Illusion Art'],
                    ['name' => 'Go-Karting'],
                    ['name' => 'Paintball & Airsoft'],
                    ['name' => 'Target Shooting / Firing Ranges'],
                    ['name' => 'Bowling & Billiards / Pool Halls'],
                    ['name' => 'Karaoke / KTV Rooms'],
                    ['name' => 'Nightclubbing & Bar Hopping'],
                    ['name' => 'Live Music Gigs & Concerts'],
                ],
            ],
            [
                'key' => 'cultural',
                'label' => 'Cultural, Arts & Heritage',
                'icon' => '🎭',
                'theme' => 'cultural',
                'intro' => 'The Philippines is a country of layered histories, indigenous craft, and lived religion. Heritage walks, weaving workshops, tattoo pilgrimages, and 7,000 fiestas a year keep all of it visible.',
                'items' => [
                    ['name' => 'Historical & Heritage Walking Tours'],
                    ['name' => 'Bamboo Bike Tours (Bambike)'],
                    ['name' => 'Cultural Shows & Traditional Dance Presentations'],
                    ['name' => 'Theater, Musicals & Stage Plays'],
                    ['name' => 'Museum & Art Gallery Tours'],
                    ['name' => 'Traditional Weaving & Pottery Workshops'],
                    ['name' => 'Traditional Tattoo Tourism (Mambabatok)'],
                    ['name' => 'Indigenous Games (Wooden Scooters, Bamboo Stilts)'],
                    ['name' => 'Religious Pilgrimages / Visita Iglesia'],
                    ['name' => 'Cockfight Watching (Sabong)'],
                    ['name' => 'Festival Tourism', 'note' => 'Year-round fiestas, by region', 'is_festival_card' => true],
                ],
            ],
            [
                'key' => 'leisure',
                'label' => 'Leisure, Wellness & Lifestyle',
                'icon' => '💆',
                'theme' => 'leisure',
                'intro' => 'For the slower days. Long lunches, hot baths, sunset cruises, mall runs, and the kind of staycation that you book with no plan beyond room service.',
                'items' => [
                    ['name' => 'Kawa Hot Bath & Fish Spa Therapy'],
                    ['name' => 'Spa & Wellness Retreats'],
                    ['name' => 'Mega-Mall Shopping & Retail Therapy'],
                    ['name' => 'Food Tours / Culinary Crawls / Street Food Tasting'],
                    ['name' => 'Craft Beer Brewery & Distillery Tours'],
                    ['name' => 'Agritourism, Farm Tours & Pick-and-Pay'],
                    ['name' => 'Staycations & Luxury Resort Lounging'],
                    ['name' => 'Sunset Dinner Cruises'],
                    ['name' => 'Golfing'],
                    ['name' => 'Oceanariums & Marine Parks'],
                ],
            ],
        ];
    }
}
