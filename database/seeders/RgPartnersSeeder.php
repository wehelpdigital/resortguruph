<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Sample partners for the /partner-directory page. These are placeholder
 * listings that populate the directory and demonstrate the live search +
 * filters until real businesses sign up through /become-a-partner.
 *
 * Idempotent: only the seeded rows are refreshed (they carry a null
 * owner_id), so any real partner rows added later are left untouched.
 *
 * Columns per row: name, type, city, region, tagline, rating, reviews, verified.
 */
class RgPartnersSeeder extends Seeder
{
    private array $rows = [
        // Palawan
        ['Lihim Cove Resort', 'resort', 'El Nido', 'Palawan', 'Cliffside cabanas over a quiet lagoon', 4.9, 214, true],
        ['Bakawan Eco Lodge', 'homestay', 'Port Barton, Palawan', 'Palawan', 'Mangrove-side rooms run by a local family', 4.6, 88, false],
        ['Isla Boat Tours', 'travel_tour', 'El Nido', 'Palawan', 'Island-hopping tours A to D on a small-group boat', 4.8, 340, true],
        ['Kuya Ariel Guiding', 'tour_guide', 'Coron', 'Palawan', 'Freelance guide for the shipwreck and lake routes', 4.9, 126, true],
        ['Sea Deep Divers', 'dive_school', 'Coron', 'Palawan', 'PADI courses over WWII wreck dives', 4.7, 203, true],
        ['Tao Kubo Homestay', 'homestay', 'Puerto Princesa', 'Palawan', 'Simple bamboo rooms near the underground river', 4.4, 51, false],
        ['Coron Tricycle Tours', 'transport', 'Coron', 'Palawan', 'Town-to-viewpoint tricycle day hire', 4.4, 47, false],

        // Cebu & Visayas
        ['Sulo Boutique Hotel', 'hotel', 'Cebu City', 'Cebu & Visayas', 'Design-led rooms in the heart of the city', 4.7, 512, true],
        ['Kawasan Canyon Guides', 'tour_guide', 'Badian, Cebu', 'Cebu & Visayas', 'Licensed canyoneering guides for the falls', 4.8, 410, true],
        ['Moalboal Reef Divers', 'dive_school', 'Moalboal, Cebu', 'Cebu & Visayas', 'Fun dives with the famous sardine run', 4.9, 288, true],
        ['Kape ni Luna', 'cafe', 'Cebu City', 'Cebu & Visayas', 'Single-origin barako and ube lattes', 4.6, 173, true],
        ['Larsian sa Fuente Grill', 'restaurant', 'Cebu City', 'Cebu & Visayas', 'Open-air lechon and seafood grill', 4.5, 620, false],
        ['Hilot sa Isla Spa', 'massage_spa', 'Mactan, Cebu', 'Cebu & Visayas', 'Traditional hilot and coconut-oil massage', 4.7, 96, true],
        ['Cebu Car & Driver', 'transport', 'Cebu City', 'Cebu & Visayas', 'Self-drive and with-driver car rental', 4.5, 93, false],
        ['Lakbay Travel and Tours', 'travel_tour', 'Iloilo City', 'Cebu & Visayas', 'Full Western Visayas itineraries and transfers', 4.6, 78, true],

        // Boracay & Aklan
        ['Puka Shore Resort', 'resort', 'Boracay, Aklan', 'Boracay & Aklan', 'Beachfront rooms a short walk from White Beach', 4.6, 733, true],
        ['Alon Surf & SUP', 'surf_school', 'Boracay, Aklan', 'Boracay & Aklan', 'Beginner surf and paddleboard lessons', 4.7, 154, false],
        ['Bulabog Kite Center', 'surf_school', 'Boracay, Aklan', 'Boracay & Aklan', 'Kitesurfing lessons on the windy side', 4.8, 121, true],
        ['Ati Cultural Tours', 'travel_tour', 'Malay, Aklan', 'Boracay & Aklan', 'Community-run tours with the Ati people', 4.6, 64, false],

        // Bohol
        ['Loboc River Retreat', 'resort', 'Loboc, Bohol', 'Bohol', 'Riverside cottages under a green canopy', 4.7, 205, true],
        ['Panglao Dive Academy', 'dive_school', 'Panglao, Bohol', 'Bohol', 'Reef and wall dives off Alona Beach', 4.8, 176, true],
        ['Tarsier Trail Guides', 'tour_guide', 'Corella, Bohol', 'Bohol', 'Countryside guide, tarsiers to Chocolate Hills', 4.7, 133, false],
        ["Bee Farm Kitchen", 'restaurant', 'Panglao, Bohol', 'Bohol', 'Organic farm meals and honey-glazed classics', 4.5, 289, false],

        // Siargao
        ['Cloud 9 Surf Camp', 'surf_school', 'General Luna, Siargao', 'Siargao', 'Lessons and board rental steps from the reef', 4.9, 302, true],
        ['Palaka Hostel & Homestay', 'homestay', 'General Luna, Siargao', 'Siargao', 'Social bunks and private nipa huts', 4.5, 148, false],
        ['Sugba Lagoon Boat Co', 'travel_tour', 'Del Carmen, Siargao', 'Siargao', 'Island-hop to Sugba, Naked, and Guyam', 4.8, 221, true],
        ['Harana Beach Cafe', 'cafe', 'General Luna, Siargao', 'Siargao', 'Smoothie bowls and slow coffee by the sand', 4.6, 97, false],

        // North Luzon
        ['Pine Ridge Inn', 'hotel', 'Baguio City', 'North Luzon', 'Cozy rooms with a cool mountain view', 4.4, 388, false],
        ['San Juan Surf School', 'surf_school', 'San Juan, La Union', 'North Luzon', 'Friendly break for first-time surfers', 4.7, 265, true],
        ['Vigan Heritage Walks', 'tour_guide', 'Vigan, Ilocos Sur', 'North Luzon', 'Calesa and walking tours of Calle Crisologo', 4.8, 142, true],
        ['Sagada Cave Connection Guides', 'tour_guide', 'Sagada, Mountain Province', 'North Luzon', 'Registered guides for the cave-to-cave route', 4.6, 118, false],
        ['Hilot Baguio Wellness', 'massage_spa', 'Baguio City', 'North Luzon', 'Warm ventosa and hilot in the highlands', 4.5, 74, false],

        // Batangas & South Luzon
        ['Anilao Scuba Center', 'dive_school', 'Mabini, Batangas', 'Batangas & South', "Muck and reef diving in Luzon's dive capital", 4.8, 199, true],
        ['Laiya Beach Villas', 'resort', 'San Juan, Batangas', 'Batangas & South', 'Family beachfront villas with pools', 4.5, 421, false],
        ['Taal Ridge Cafe', 'cafe', 'Tagaytay, Cavite', 'Batangas & South', 'Bulalo, coffee, and a Taal volcano view', 4.6, 512, true],
        ['Pansol Hot Spring Rentals', 'homestay', 'Calamba, Laguna', 'Batangas & South', 'Private hot-spring pool houses for groups', 4.3, 288, false],
        ['Kayla Massage & Spa', 'massage_spa', 'Tagaytay, Cavite', 'Batangas & South', 'Aromatherapy and Swedish in the cool ridge', 4.7, 66, true],

        // Mindanao
        ['Samal Island Resort', 'resort', 'Samal, Davao', 'Mindanao', 'White-sand cottages a ferry from Davao', 4.6, 233, true],
        ['Davao City Food Tours', 'travel_tour', 'Davao City', 'Mindanao', 'Durian, tuna, and market food crawls', 4.8, 91, true],
        ['Kadayawan Guiding', 'tour_guide', 'Davao City', 'Mindanao', 'City and Mt. Apo base guiding', 4.5, 58, false],
        ["Aling Nena's Carinderia", 'restaurant', 'Davao City', 'Mindanao', 'Home-style Mindanao dishes and grilled tuna', 4.4, 176, false],

        // Siquijor & Negros
        ['Salamangka Beach Resort', 'resort', 'Siquijor', 'Siquijor & Negros', 'Quiet garden-and-beach rooms', 4.7, 141, true],
        ['Cambugahay Falls Guides', 'tour_guide', 'Lazi, Siquijor', 'Siquijor & Negros', 'Rope-swing and falls loop with a local guide', 4.6, 84, false],
        ['Apo Island Divers', 'dive_school', 'Dumaguete, Negros', 'Siquijor & Negros', 'Turtle and sanctuary dives off Apo Island', 4.8, 167, true],
        ['Silliman Boulevard Spa', 'massage_spa', 'Dumaguete, Negros', 'Siquijor & Negros', 'Beach-boulevard hilot and foot massage', 4.5, 59, false],

        // Bicol & Camiguin
        ['Camiguin Volcano Lodge', 'homestay', 'Mambajao, Camiguin', 'Bicol & Camiguin', 'Cool-air rooms near the hot and cold springs', 4.5, 72, false],
        ['White Island Boat Tours', 'travel_tour', 'Mambajao, Camiguin', 'Bicol & Camiguin', 'Sandbar and sunken-cemetery boat trips', 4.7, 103, true],
        ['Donsol Butanding Guides', 'tour_guide', 'Donsol, Sorsogon', 'Bicol & Camiguin', 'Whale-shark interaction guiding, in season', 4.8, 156, true],
        ['Cagsawa View Cafe', 'cafe', 'Daraga, Albay', 'Bicol & Camiguin', 'Sili ice cream with a Mayon backdrop', 4.6, 210, false],

        // Metro Manila
        ['BGC Skyline Suites', 'hotel', 'Taguig, Metro Manila', 'Metro Manila', 'Modern serviced suites in the city center', 4.6, 640, true],
        ['Intramuros Bamboo Bike Tours', 'travel_tour', 'Manila', 'Metro Manila', 'Guided bamboo-bike loops of the old walls', 4.8, 274, true],
        ['Poblacion Food Crawl', 'travel_tour', 'Makati, Metro Manila', 'Metro Manila', 'After-dark tastings across Poblacion', 4.7, 132, false],
        ['Manila Bay Wellness Spa', 'massage_spa', 'Pasay, Metro Manila', 'Metro Manila', 'Reflexology and Swedish near the bay', 4.4, 88, false],
        ['Grab-a-Van Rentals', 'transport', 'Quezon City, Metro Manila', 'Metro Manila', 'Airport transfers and provincial van hire', 4.5, 121, true],
    ];

    /**
     * Temporary thumbnail photos drawn from the existing media library so
     * every card shows a real Philippine tourism photo. Food types get
     * restaurant shots; everything else gets a scenic / beach shot. Paths
     * are relative to public/storage. Swapped for the partner's own upload
     * once real businesses join.
     */
    private array $scenicPool = [
        'rg-media/spots/el-nido-big-lagoon-tour-a.jpg',
        'rg-media/spots/el-nido-small-lagoon-tour-a.jpg',
        'rg-media/spots/el-nido-nacpan-beach-45-min-north.jpg',
        'rg-media/spots/boracay-white-beach-stations-1-2-3.jpg',
        'rg-media/spots/boracay-puka-beach-north.jpg',
        'rg-media/spots/boracay-crystal-cove-island.jpg',
        'rg-media/spots/alaminos-hundred-islands-quezon-island.jpg',
        'rg-media/spots/anilao-mabini-sombrero-island.jpg',
        'rg-media/spots/bolinao-patar-beach.jpg',
        'rg-media/spots/bolinao-bolinao-falls-1-2-3.jpg',
        'rg-media/spots/la-union-san-juan-surf-beach-urbiztondo.jpg',
        'rg-media/spots/la-union-tangadan-falls.jpg',
        'rg-media/spots/laiya-laiya-white-beach.jpg',
        'rg-media/spots/dauin-apo-island-marine-sanctuary.jpg',
        'rg-media/spots/dumaguete-casaroro-falls-valencia.jpg',
        'rg-media/spots/davao-city-samal-island-15-min-ferry.jpg',
        'rg-media/spots/glan-sarangani-gumasa-beach.jpg',
        'rg-media/spots/ilocos-norte-pagudpud-beaches-saud-blue-lagoon-patapat.jpg',
        'rg-media/spots/antipolo-hinulugang-taktak-falls.jpg',
        'rg-media/spots/bataan-province-anvaya-cove.jpg',
    ];

    private array $foodPool = [
        'rg-media/restaurants/antonios.jpg',
        'rg-media/restaurants/bag-of-beans.jpg',
        'rg-media/restaurants/cafe-adriana.jpg',
        'rg-media/restaurants/cafe-by-the-ruins.jpg',
        'rg-media/restaurants/inengs-bbq.jpg',
        'rg-media/restaurants/lemuria-restaurant.jpg',
        'rg-media/restaurants/manam-comfort-filipino.jpg',
        'rg-media/restaurants/mendokoro-ramenba.jpg',
        'rg-media/restaurants/toyo-eatery.jpg',
        'rg-media/restaurants/wildflour-cafe.jpg',
        'rg-media/restaurants/yardstick-coffee.jpg',
        'rg-media/restaurants/zubuchon.jpg',
    ];

    public function run(): void
    {
        // Refresh only the seeded (owner-less) demo rows.
        DB::table('rg_partners')->whereNull('owner_id')->delete();

        $now = now();
        $insert = [];
        $seenSlugs = [];
        $si = 0;
        $fi = 0;
        foreach ($this->rows as $r) {
            [$name, $type, $city, $region, $tagline, $rating, $reviews, $verified] = $r;
            $slug = Str::slug($name);
            if (isset($seenSlugs[$slug])) {
                $slug .= '-' . substr(md5($city), 0, 4);
            }
            $seenSlugs[$slug] = true;

            if (in_array($type, ['restaurant', 'cafe'], true)) {
                $image = $this->foodPool[$fi++ % count($this->foodPool)];
            } else {
                $image = $this->scenicPool[$si++ % count($this->scenicPool)];
            }

            $insert[] = [
                'owner_id' => null,
                'name' => $name,
                'slug' => $slug,
                'type' => $type,
                'city' => $city,
                'region' => $region,
                'tagline' => $tagline,
                'description' => null,
                'image_path' => $image,
                'rating' => $rating,
                'review_count' => $reviews,
                'is_verified' => $verified,
                'is_featured' => $verified && $rating >= 4.8,
                'phone' => null,
                'website' => null,
                'status' => 'published',
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('rg_partners')->insert($insert);
        $this->command->info('  seeded ' . count($insert) . ' sample partners (' . collect($insert)->where('is_verified', true)->count() . ' verified) with photo thumbnails');
    }
}
