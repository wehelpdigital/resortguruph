<?php

/**
 * Image overlays for blog batch 12 (40 posts).
 *
 * Each overlay anchors a <figure class="rg-figure"> block AFTER a unique
 * substring inside the source content_html. Anchors are case-sensitive and
 * must appear exactly once in the matching post body.
 *
 * Skipped posts (pure travel-ops, no list-of-named-things to anchor):
 *   - baguio-to-sagada-bus-gl-trans-honest-guide
 *   - bohol-to-siquijor-ferry-honest-schedule-guide
 *   - cebu-to-siquijor-ferry-honest-schedule-guide
 *   - dumaguete-to-siquijor-ferry-calm-crossing-read
 *   - pitx-paranaque-terminal-calm-first-timer-guide
 *   - solid-north-p2p-bus-manila-baguio-calm-read
 *   - manila-to-pangasinan-bus-practical-read
 *   - cavite-to-baguio-commute-without-car-calm-read
 *   - 2go-batangas-to-caticlan-ferry-calm-boracay-read
 */

return [

    'atok-benguet-flower-farm-route-day-from-baguio' => [
        [
            'anchor' => '<h2>Northern Blossom Flower Farm</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c1/Shots_at_Northern_Blossom_Flower_Farm_in_Atok%2C_Benguet_12.jpg/800px-Shots_at_Northern_Blossom_Flower_Farm_in_Atok%2C_Benguet_12.jpg" alt="Cabbage rose beds at Northern Blossom Flower Farm in Atok, Benguet" loading="lazy"><figcaption>Northern Blossom sits at 2,200 meters in Sayangan, Atok. Photo via <a href="https://commons.wikimedia.org/wiki/File:Shots_at_Northern_Blossom_Flower_Farm_in_Atok,_Benguet_12.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => '<h2>Mt Timbak Viewpoint</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/57/Halsema_Highway_Highest_Point_marker_%28Atok%2C_Benguet%3B_12-03-2022%29.jpg/800px-Halsema_Highway_Highest_Point_marker_%28Atok%2C_Benguet%3B_12-03-2022%29.jpg" alt="Halsema Highway highest point marker in Atok, Benguet" loading="lazy"><figcaption>The Halsema Highway highest-point marker in Atok. Mt Timbak is the third-highest peak in Luzon at 2,717 meters. Photo via <a href="https://commons.wikimedia.org/wiki/File:Halsema_Highway_Highest_Point_marker_(Atok,_Benguet;_12-03-2022).jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    'mt-timbak-atok-day-hike-camping-read' => [
        [
            'anchor' => '<p>Mt Timbak sits at 2,717 meters above sea level in Atok, Benguet. It is the third-highest peak in Luzon after Mt Pulag and Mt Tabayoc, and the only one of the three that you can reach by jeepney from Baguio in under two hours.',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/la-union-3.jpg" alt="High Cordillera ridge and pine slopes near Mt Timbak" loading="lazy"><figcaption>The Halsema Highway approach to Mt Timbak. The summit cross sits a 30-minute walk above the road pull-out.</figcaption></figure>',
        ],
        [
            'anchor' => '<h2>The Timbak Mummies</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/47/Atok_mountains_%28Halsema_Highway%2C_Atok%2C_Benguet%29%282018-11-25%29.jpg/800px-Atok_mountains_%28Halsema_Highway%2C_Atok%2C_Benguet%29%282018-11-25%29.jpg" alt="Atok mountain landscape along the Halsema Highway, Benguet" loading="lazy"><figcaption>The Igorot fire-cured mummies lie in caves below the Timbak summit cross. Photos are not allowed inside. Landscape photo via <a href="https://commons.wikimedia.org/wiki/File:Atok_mountains_(Halsema_Highway,_Atok,_Benguet)(2018-11-25).jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    'northern-blossom-farm-atok-quiet-cold-morning' => [
        [
            'anchor' => '<h2>The Cabbage Roses</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/2b/Shots_at_Northern_Blossom_Flower_Farm_in_Atok%2C_Benguet_02.jpg/800px-Shots_at_Northern_Blossom_Flower_Farm_in_Atok%2C_Benguet_02.jpg" alt="Concentric rings of cabbage roses at Northern Blossom Flower Farm" loading="lazy"><figcaption>The headline crop: hybrid cabbage roses arranged in concentric rings. Photo via <a href="https://commons.wikimedia.org/wiki/File:Shots_at_Northern_Blossom_Flower_Farm_in_Atok,_Benguet_02.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => '<h2>The Cloud Line</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/47/Shots_at_Northern_Blossom_Flower_Farm_in_Atok%2C_Benguet_26.jpg/800px-Shots_at_Northern_Blossom_Flower_Farm_in_Atok%2C_Benguet_26.jpg" alt="Sea of clouds and highland farm beds at Northern Blossom" loading="lazy"><figcaption>The sea of clouds usually fills the valley below Sayangan until it burns off between 10 and 11 a.m. Photo via <a href="https://commons.wikimedia.org/wiki/File:Shots_at_Northern_Blossom_Flower_Farm_in_Atok,_Benguet_26.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    'haights-place-atok-philippine-sakura-read' => [
        [
            'anchor' => '<h2>Entrance and the Garden Loop</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/la-union-2.jpg" alt="Cool Cordillera highland scene near Atok, Benguet" loading="lazy"><figcaption>Haight\'s Place sits at around 2,000 meters in Paoay, Atok. The full garden loop is 20 to 30 minutes.</figcaption></figure>',
        ],
        [
            'anchor' => '<h2>Manage Sakura Expectations</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/la-union-1.jpg" alt="Cordillera highland landscape used as illustrative backdrop for the Atok cherry blossom area" loading="lazy"><figcaption>The cluster of around 20 to 30 real Japanese cherry trees blooms for two to three weeks in late January through mid-February.</figcaption></figure>',
        ],
    ],

    'mt-kalugong-eco-park-la-trinidad-slow-morning' => [
        [
            'anchor' => '<h2>The Strawberry Valley View</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/93/Mt.kalugongpark2.jpg/800px-Mt.kalugongpark2.jpg" alt="View over the La Trinidad valley from Mt Kalugong" loading="lazy"><figcaption>The summit clearing looks out on the La Trinidad strawberry farm rows and the surrounding Cordillera ridges. Photo via <a href="https://commons.wikimedia.org/wiki/File:Mt.kalugongpark2.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => '<h2>The Pine Forest Loop</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/a4/Mt.kalugongpark.jpg/800px-Mt.kalugongpark.jpg" alt="Pine forest and limestone outcrops at Mt Kalugong" loading="lazy"><figcaption>The secondary loop circles a small meadow with native ground orchids in season (March to May). Photo via <a href="https://commons.wikimedia.org/wiki/File:Mt.kalugongpark.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    'marlboro-country-sagada-calm-sunrise-hill-loop' => [
        [
            'anchor' => '<h2>Echo Valley and the Hanging Coffins</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/3/39/Hanging_Coffins_in_Sagada.jpg/800px-Hanging_Coffins_in_Sagada.jpg" alt="Hanging coffins on the limestone cliff in Echo Valley, Sagada" loading="lazy"><figcaption>The carved wooden burial boxes attached to the cliff face in Echo Valley. Photo via <a href="https://commons.wikimedia.org/wiki/File:Hanging_Coffins_in_Sagada.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => '<h2>The Climb to Marlboro Country</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/f/fc/Sagada_overlooking_houses_near_Echo_Valley_%28Sagada%2C_Mountain_Province%3B_12-01-2022%29.jpg/800px-Sagada_overlooking_houses_near_Echo_Valley_%28Sagada%2C_Mountain_Province%3B_12-01-2022%29.jpg" alt="Sagada town and ridges near Echo Valley, Mountain Province" loading="lazy"><figcaption>The grass ridge above Sagada at around 1,700 meters. The scattered cattle gave the area its Marlboro Country nickname. Photo via <a href="https://commons.wikimedia.org/wiki/File:Sagada_overlooking_houses_near_Echo_Valley_(Sagada,_Mountain_Province;_12-01-2022).jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    'sagada-or-baguio-choosing-cordillera-mountain-town' => [
        [
            'anchor' => '<h2>The Activity Question</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/a9/Hanging_Coffins_of_Sagada.jpg/800px-Hanging_Coffins_of_Sagada.jpg" alt="Hanging coffins on the Sagada cliff face" loading="lazy"><figcaption>Sagada activity headline: the hanging coffins and the Sumaguing-Lumiang cave connection. Photo via <a href="https://commons.wikimedia.org/wiki/File:Hanging_Coffins_of_Sagada.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    'sagada-travel-tips-calm-first-timer-read' => [
        [
            'anchor' => '<h2>7. The Hanging Coffins Are Sacred</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/b/b3/Church_of_St._Mary_the_Virgin%2C_Sagada.JPG/800px-Church_of_St._Mary_the_Virgin%2C_Sagada.JPG" alt="Church of St. Mary the Virgin in Sagada, Mountain Province" loading="lazy"><figcaption>The Echo Valley hanging coffins are reached through the graveyard of St. Mary\'s Episcopal Church (1904). Photo via <a href="https://commons.wikimedia.org/wiki/File:Church_of_St._Mary_the_Virgin,_Sagada.JPG" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    'pamuayan-waterfall-port-barton-quiet-trail-day' => [
        [
            'anchor' => '<h2>The Falls</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/el-nido-3.jpg" alt="Forested limestone landscape in northern Palawan" loading="lazy"><figcaption>Pamuayan drops 15 meters into a wide pool ringed by limestone boulders, around 45 minutes by foot from Port Barton.</figcaption></figure>',
        ],
    ],

    'port-barton-three-island-slow-weekend-palawan' => [
        [
            'anchor' => '<h2>Day Two: The Three-Island Tour (Tour A)</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/el-nido-2.jpg" alt="Limestone island coastline in northern Palawan typical of the Port Barton bay" loading="lazy"><figcaption>Tour A stops at German Island, Exotic Island, Paradise Island, the starfish sanctuary, and Aquarium Reef.</figcaption></figure>',
        ],
        [
            'anchor' => '<h2>Day Three: Pamuayan Falls and a Slow Lunch</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/el-nido-1.jpg" alt="Calm beach and palm cover on the western Palawan coast" loading="lazy"><figcaption>Port Barton main beach stretches around a kilometer of warm sand and faces a sunset over the South China Sea.</figcaption></figure>',
        ],
    ],

    'el-nido-coron-multi-day-boat-expedition-calm-read' => [
        [
            'anchor' => '<h2>The Route</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/el-nido-big-lagoon-tour-a.jpg" alt="Limestone cliffs and turquoise lagoon water in El Nido, Palawan" loading="lazy"><figcaption>The expedition stitches together the Cadlao, Linapacan, Black Island, and Culion area islands over four days.</figcaption></figure>',
        ],
        [
            'anchor' => '<h2>The Snorkeling</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/a5/Big_Lagoon_at_El_Nido%2C_Palawan%2C_Philippines.jpg/800px-Big_Lagoon_at_El_Nido%2C_Palawan%2C_Philippines.jpg" alt="Limestone cliffs and turquoise lagoon in El Nido, Palawan" loading="lazy"><figcaption>The Linapacan strait between El Nido and Coron has visibility regularly hitting 30 to 40 meters. Photo via <a href="https://commons.wikimedia.org/wiki/File:Big_Lagoon_at_El_Nido,_Palawan,_Philippines.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    'coron-or-el-nido-choosing-palawan-base' => [
        [
            'anchor' => '<h2>The Boat Tours</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/3/3b/Kayangan_Lake%2C_Coron_Island.jpg/800px-Kayangan_Lake%2C_Coron_Island.jpg" alt="Kayangan Lake inside Coron Island, Palawan" loading="lazy"><figcaption>Kayangan Lake on Coron Island, the brackish freshwater lake reached by a short limestone climb. Photo via <a href="https://commons.wikimedia.org/wiki/File:Kayangan_Lake,_Coron_Island.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>El Nido\'s signature tour is Tour A (Big Lagoon, Small Lagoon, Secret Lagoon, Shimizu Island, Seven Commandos Beach).',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/el-nido-small-lagoon-tour-a.jpg" alt="Small Lagoon limestone walls and turquoise water in El Nido" loading="lazy"><figcaption>El Nido Tour A: Big Lagoon kayaks and the Small Lagoon entrance through a narrow limestone gap.</figcaption></figure>',
        ],
    ],

    'magalawa-island-zambales-slow-overnight-plan' => [
        [
            'anchor' => '<h2>The Sandbar</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/subic-anawangin-and-nagsasa-coves-from-pundaquit.jpg" alt="Zambales coastline with calm shallow water" loading="lazy"><figcaption>The Magalawa sandbar extends 200 to 300 meters off the south side at low tide, visible between half-tide and low tide.</figcaption></figure>',
        ],
        [
            'anchor' => '<h2>The Western Beach</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/landmarks/visayas.jpg" alt="Calm tropical beach with fine sand" loading="lazy"><figcaption>The western beach faces the South China Sea sunset, with a small reef around 100 meters offshore for snorkeling.</figcaption></figure>',
        ],
    ],

    'taal-heritage-town-batangas-vigan-south-walk' => [
        [
            'anchor' => '<h2>The Basilica of Saint Martin of Tours</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/29/Minor_Basilica_of_Saint_Martin_of_Tours_or_Taal_Basilica_85M7772.jpg/800px-Minor_Basilica_of_Saint_Martin_of_Tours_or_Taal_Basilica_85M7772.jpg" alt="Facade of the Basilica of Saint Martin of Tours in Taal, Batangas" loading="lazy"><figcaption>The 1856 basilica has the largest floor area of any Catholic church in Asia at 96 by 45 meters. Photo via <a href="https://commons.wikimedia.org/wiki/File:Minor_Basilica_of_Saint_Martin_of_Tours_or_Taal_Basilica_85M7772.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => '<h2>The Heritage Houses</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/batangas-city-taal-heritage-town.jpg" alt="Heritage street and ancestral homes in Taal, Batangas" loading="lazy"><figcaption>The cobblestone streets behind the plaza hold Casa Apacible, the Marcela Agoncillo House, and the Felipe Agoncillo Museum.</figcaption></figure>',
        ],
        [
            'anchor' => '<h2>The Barong and Balisong Workshops</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/batangas-city-3.jpg" alt="Batangas heritage town craft scene" loading="lazy"><figcaption>Hand-embroidered barong tagalog on pina or jusi cloth, and hand-forged balisong butterfly knives, still made the traditional way.</figcaption></figure>',
        ],
    ],

    'tulapos-marine-sanctuary-siquijor-calm-snorkel-morning' => [
        [
            'anchor' => '<h2>The Mangrove Walk</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/3/33/Tulapos_Marine_Sanctuary_01.JPG/800px-Tulapos_Marine_Sanctuary_01.JPG" alt="Tulapos Marine Sanctuary boardwalk and shoreline in Enrique Villanueva, Siquijor" loading="lazy"><figcaption>The community-managed Tulapos Marine Sanctuary covers 14 hectares of beach, mangrove, reef, and seagrass. Photo via <a href="https://commons.wikimedia.org/wiki/File:Tulapos_Marine_Sanctuary_01.JPG" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => '<h2>The Snorkel Site</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/0/05/Tulapos_Marine_Sanctuary_02.JPG/800px-Tulapos_Marine_Sanctuary_02.JPG" alt="Shallow reef inside the Tulapos Marine Sanctuary, Siquijor" loading="lazy"><figcaption>The reef starts 30 to 40 meters off the beach. Hard corals dominate and visibility is typically 8 to 15 meters. Photo via <a href="https://commons.wikimedia.org/wiki/File:Tulapos_Marine_Sanctuary_02.JPG" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    'cambugahay-falls-siquijor-calm-rope-swing-afternoon' => [
        [
            'anchor' => '<h2>The Three Tiers</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/siquijor-cambugahay-falls.jpg" alt="Tiered cascade and turquoise swimming pools at Cambugahay Falls, Siquijor" loading="lazy"><figcaption>Cambugahay\'s three tiers: a shallow upper pool, the wide middle pool with the rope swings, and the deeper bottom pool.</figcaption></figure>',
        ],
        [
            'anchor' => '<h2>The Rope Swings</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/siquijor-1.jpg" alt="Forested cascade setting in Lazi, Siquijor" loading="lazy"><figcaption>The rope swings are tied to trees overhanging the middle pool, around 4 to 5 meters of swing arc and a two-meter drop.</figcaption></figure>',
        ],
    ],

    'sunken-cemetery-camiguin-calm-sunset-crossing' => [
        [
            'anchor' => '<h2>The Story</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/2c/Sunken_Cemetery%2C_Catarman%2C_Camiguin.jpg/800px-Sunken_Cemetery%2C_Catarman%2C_Camiguin.jpg" alt="White cross marker at the Sunken Cemetery off Catarman, Camiguin" loading="lazy"><figcaption>The marker cross over the cemetery submerged by the 1871 Mt Vulcan Daan eruption. Photo via <a href="https://commons.wikimedia.org/wiki/File:Sunken_Cemetery,_Catarman,_Camiguin.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    'katunggan-eco-park-camiguin-calm-mangrove-walk' => [
        [
            'anchor' => '<h2>The Bamboo Boardwalk</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/landmarks/mindanao.jpg" alt="Mangrove coastline scene in Mindanao" loading="lazy"><figcaption>The Katunggan boardwalk runs 600 meters through the mangrove canopy in Mahinog, Camiguin.</figcaption></figure>',
        ],
        [
            'anchor' => '<h2>The Mangrove Ecosystem</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/dumaguete-3.jpg" alt="Tidal mangrove canopy along a Visayan coast" loading="lazy"><figcaption>The park has six to eight mangrove species, dominated by Rhizophora apiculata (bakau) and Avicennia marina (api-api).</figcaption></figure>',
        ],
    ],

    'hinagdanan-cave-dauis-panglao-calm-swim-stop' => [
        [
            'anchor' => '<h2>The Lagoon</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/panglao-hinagdanan-cave.jpg" alt="Underground brackish lagoon inside Hinagdanan Cave on Panglao Island" loading="lazy"><figcaption>The brackish lagoon inside Hinagdanan, lit by two natural skylights in the ceiling above.</figcaption></figure>',
        ],
        [
            'anchor' => '<h2>The Photo</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/panglao-2.jpg" alt="Panglao Island coastal scene near Dauis" loading="lazy"><figcaption>Pair Hinagdanan with the Dauis Church well and an afternoon at Alona Beach for a calm Panglao half-day.</figcaption></figure>',
        ],
    ],

    'manjuyod-sandbar-negros-oriental-calm-dawn-ride' => [
        [
            'anchor' => '<h2>The Sandbar Walk</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/5e/Manjuyod_Sand_Bar%2C_Philippines.jpg/800px-Manjuyod_Sand_Bar%2C_Philippines.jpg" alt="Manjuyod sandbar at low tide off Bais, Negros Oriental" loading="lazy"><figcaption>The seven-kilometer ribbon of white sand that emerges at low tide off Bais Bay. Photo via <a href="https://commons.wikimedia.org/wiki/File:Manjuyod_Sand_Bar,_Philippines.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => '<h2>The Stilt Cottages</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/dumaguete-2.jpg" alt="Calm Negros Oriental coastal water" loading="lazy"><figcaption>Day-tour packages serve lunch on the small stilt cottages anchored off the sandbar: grilled fish, kinilaw, fresh seaweed salad.</figcaption></figure>',
        ],
    ],

    'pangasinan-beach-campsites-calm-slow-roundup' => [
        [
            'anchor' => '<h2>Patar White Beach (Bolinao)</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/bolinao-patar-beach.jpg" alt="White sand and headland at Patar Beach in Bolinao, Pangasinan" loading="lazy"><figcaption>Patar\'s two coves are split by a headland that frames the South China Sea sunset.</figcaption></figure>',
        ],
        [
            'anchor' => '<h2>Hundred Islands (Alaminos)</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/e7/Hundred_Islands%2C_Alaminos%2C_Pangasinan_-_08.jpg/800px-Hundred_Islands%2C_Alaminos%2C_Pangasinan_-_08.jpg" alt="Aerial view of the Hundred Islands off Alaminos, Pangasinan" loading="lazy"><figcaption>Quezon, Governor, and Children\'s Islands are the popular permitted overnight camping islands. Photo via <a href="https://commons.wikimedia.org/wiki/File:Hundred_Islands,_Alaminos,_Pangasinan_-_08.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => '<h2>Cabongaoan Beach (Burgos)</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/pangasinan-general-2.jpg" alt="Rocky coastal platform on the western Pangasinan coast" loading="lazy"><figcaption>The famous "death pool" is a natural saltwater pool carved into the rock platform between Bolinao and Anda.</figcaption></figure>',
        ],
    ],

    'bolinao-falls-1-2-3-calm-cascade-chain-day' => [
        [
            'anchor' => '<h2>Bolinao Falls 1</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/bolinao-bolinao-falls-1-2-3.jpg" alt="Three-tier cascade at Bolinao Falls 1 in Pangasinan" loading="lazy"><figcaption>Falls 1 has the classic three-tier cascade, a rope swing, and a low cliff for jumping into a 2 to 3 meter pool.</figcaption></figure>',
        ],
        [
            'anchor' => '<h2>Bolinao Falls 2</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/bolinao-2.jpg" alt="Forested cascade and jungle pool in Bolinao, Pangasinan" loading="lazy"><figcaption>Falls 2 has a single 15-meter drop into a wider, quieter swimming pool surrounded by tall trees.</figcaption></figure>',
        ],
    ],

    'bolinao-slow-west-coast-calm-two-day-stop-list' => [
        [
            'anchor' => '<h2>Day One Morning: Saint James Church (Bolinao)</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/bolinao-st-james-the-great-parish-church-1609.jpg" alt="Coral stone facade of Saint James the Great Parish Church in Bolinao" loading="lazy"><figcaption>Saint James the Great Church dates to 1609 and is built of coral stone quarried from the local shoreline.</figcaption></figure>',
        ],
        [
            'anchor' => '<h2>Day One Late Morning: Cape Bolinao Lighthouse</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/bolinao-cape-bolinao-lighthouse.jpg" alt="Cape Bolinao Lighthouse on the western Pangasinan headland" loading="lazy"><figcaption>The 1905 lighthouse stands 351 meters above sea level on the cape; the gallery has South China Sea views on three sides.</figcaption></figure>',
        ],
        [
            'anchor' => '<h2>Day One Afternoon: Patar Beach</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/9c/Patar_white_beach.jpg/800px-Patar_white_beach.jpg" alt="Patar white beach in Bolinao, Pangasinan" loading="lazy"><figcaption>Patar Beach\'s rocky headland divides the white sand into two coves and frames the dusk horizon. Photo via <a href="https://commons.wikimedia.org/wiki/File:Patar_white_beach.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => '<h2>Day Two Mid-Morning: Wonderful Cave and Cindy Cave</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/bolinao-3.jpg" alt="Limestone cave pool area in Bolinao, Pangasinan" loading="lazy"><figcaption>Wonderful Cave has a higher cliff edge over the pool; Cindy Cave has the narrower chamber with the deeper water.</figcaption></figure>',
        ],
    ],

    'moalboal-seven-things-beyond-sardines-calm-read' => [
        [
            'anchor' => '<h2>2. The Diving</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/cd/Pescador_Island.JPG/800px-Pescador_Island.JPG" alt="Pescador Island off Moalboal, Cebu" loading="lazy"><figcaption>Pescador Island, the marquee Moalboal dive site, with steep wall diving and frequent thresher and reef shark sightings. Photo via <a href="https://commons.wikimedia.org/wiki/File:Pescador_Island.JPG" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => '<h2>3. Kawasan Canyoneering</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/cebu-city-3.jpg" alt="Jungle river canyon scene in Cebu" loading="lazy"><figcaption>The Matutinao River canyoneering route ends at Kawasan Falls in the next town (Badian).</figcaption></figure>',
        ],
        [
            'anchor' => '<h2>5. White Beach</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/cebu-city-1.jpg" alt="Calm fine-sand beach on the western Cebu coast" loading="lazy"><figcaption>White Beach (Basdaku) is a 15-minute tricycle ride north of Panagsama, the family-friendly alternative to the dive strip.</figcaption></figure>',
        ],
    ],

    'oslob-whale-shark-watching-honest-read' => [
        [
            'anchor' => '<h2>The Interaction</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/dumaguete-1.jpg" alt="Calm coastal water typical of the southern Cebu and Negros coast" loading="lazy"><figcaption>Tan-Awan barangay in Oslob, where fishermen feed shrimp (uyap) to the resident whale sharks from small bancas each morning.</figcaption></figure>',
        ],
    ],

    'cebu-mainland-calm-list-beyond-standard-route' => [
        [
            'anchor' => '<h2>The Cebu City Heritage Walk</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/cebu-city-magellans-cross-and-basilica-del-santo-nino.jpg" alt="Magellan\'s Cross and the Basilica del Santo Nino in Cebu City" loading="lazy"><figcaption>The Magellan\'s Cross chapel sits next to the Basilica del Santo Nino, the oldest Roman Catholic church in the Philippines (1565).</figcaption></figure>',
        ],
        [
            'anchor' => '<h2>Carbon Market</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/cebu-city-carbon-market.jpg" alt="Stalls and produce at Carbon Market in Cebu City" loading="lazy"><figcaption>Carbon is the oldest and largest farmer\'s market in Cebu City. Mornings (5 a.m. to 9 a.m.) are the freshest.</figcaption></figure>',
        ],
        [
            'anchor' => '<h2>Tops Lookout</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/cebu-city-tops-lookout.jpg" alt="View from Tops Lookout over Cebu City" loading="lazy"><figcaption>Tops sits around 600 meters above the city with a 360-degree view that reaches the Mactan Channel.</figcaption></figure>',
        ],
        [
            'anchor' => '<h2>Carcar Lechon</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/cebu-city-2.jpg" alt="Cebu lechon street food scene" loading="lazy"><figcaption>Carcar is the lechon capital of Cebu. The upstairs stalls at the public market are the local picks.</figcaption></figure>',
        ],
    ],

    'mt-gulugod-baboy-sunset-variant-calm-second-time-hike' => [
        [
            'anchor' => '<h2>The Summit View</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/anilao-mabini-3.jpg" alt="View of the Verde Island Passage from the Mabini coast" loading="lazy"><figcaption>The summit looks south over the Verde Island Passage and west across the Anilao dive coast to Tingloy.</figcaption></figure>',
        ],
    ],

    'anilao-sunset-banca-chasers-calm-afternoon-read' => [
        [
            'anchor' => '<h2>Sombrero Island Stop</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/anilao-mabini-sombrero-island.jpg" alt="Sombrero Island off the Mabini coast in Batangas" loading="lazy"><figcaption>Sombrero Island, named for its hat shape, sits 30 minutes from the Anilao Pier on the standard sunset banca route.</figcaption></figure>',
        ],
        [
            'anchor' => '<h2>The Maricaban Strait Sunset</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/anilao-mabini-2.jpg" alt="Calm strait water between Tingloy and the Mabini coast at dusk" loading="lazy"><figcaption>The Maricaban Strait between Tingloy and the Mabini mainland is the calmest stretch of the route on a windless day.</figcaption></figure>',
        ],
    ],

    'lake-pandin-or-lake-yambo-choosing-san-pablo-twin' => [
        [
            'anchor' => '<h2>Lake Pandin: The Bamboo Raft Lake</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/san-pablo-pandin-lake-and-twin-yambo-lake.jpg" alt="Lake Pandin and Lake Yambo, the twin crater lakes of San Pablo, Laguna" loading="lazy"><figcaption>Lake Pandin (foreground) and Lake Yambo are separated by a narrow 30-meter ridge in San Pablo, Laguna.</figcaption></figure>',
        ],
        [
            'anchor' => '<h2>The Twin-Lakes Viewpoint</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/san-pablo-2.jpg" alt="Lush twin lakes viewpoint area in San Pablo" loading="lazy"><figcaption>The ridge climb between the lakes takes 10 to 15 minutes; both lakes are visible at the same time from the wooden platform.</figcaption></figure>',
        ],
    ],

    'pansol-calamba-private-hot-spring-calm-family-read' => [
        [
            'anchor' => '<h2>The Water</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/calamba-pansol-hot-springs-calamba-barangay.jpg" alt="Private hot spring pool in a Pansol Calamba resort compound" loading="lazy"><figcaption>The hot spring water in Pansol surfaces at 38 to 42 degrees Celsius from sources fed by Mt Makiling geothermal activity.</figcaption></figure>',
        ],
        [
            'anchor' => '<h2>The Family-Friendly Picks</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/pansol-2.jpg" alt="Pool house and dining deck at a Pansol private resort" loading="lazy"><figcaption>A typical Pansol pool house: a private hot pool, a covered dining area, a dirty kitchen with a grill, and basic shower facilities.</figcaption></figure>',
        ],
    ],

    'caohagan-island-handicraft-side-calm-mactan-day' => [
        [
            'anchor' => '<h2>The Quilt Community</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/mactan-2.jpg" alt="Small island community scene off the Mactan coast" loading="lazy"><figcaption>The Caohagan women\'s cooperative produces traditional Mactan-style hand-stitched quilts, supported by a Japanese cultural initiative since the 1990s.</figcaption></figure>',
        ],
        [
            'anchor' => '<h2>The Island Walk</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/mactan-3.jpg" alt="Calm Mactan offshore island shoreline" loading="lazy"><figcaption>Caohagan is around 1.3 hectares, walkable in 15 minutes end to end, with about 600 to 700 fisher-family residents.</figcaption></figure>',
        ],
    ],

];
