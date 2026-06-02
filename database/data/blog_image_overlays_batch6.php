<?php

/**
 * Blog image overlays for batch 6 posts. Each entry maps a blog slug to a
 * list of <figure> HTML blocks that the renderer injects either before or
 * after a unique anchor substring found in the post content_html.
 *
 * Pure opinion or transit-comparison posts that lack a strong list of named
 * photographable subjects are intentionally not enhanced.
 */

return [

    // ----------------------------------------------------------------------
    // Intramuros + Binondo walking food day
    // ----------------------------------------------------------------------
    'intramuros-binondo-walking-food-day-old-manila' => [
        [
            'anchor' => 'Start at Fort Santiago by around 8:30 a.m.',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/manila-intramuros.jpg" alt="Stone walls and gate at Fort Santiago Intramuros Manila" loading="lazy"><figcaption>Intramuros, the 16th-century walled city. Fort Santiago is the cleanest entry point and the morning light on the rampart side is the best of the day.</figcaption></figure>',
        ],
        [
            'anchor' => 'From Plaza San Luis, walk back north toward the Postigo Gate',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/f/f2/Manila_Cathedral_in_Intramuros_%282026-01-15%29.jpg/800px-Manila_Cathedral_in_Intramuros_%282026-01-15%29.jpg" alt="Manila Cathedral facade in Intramuros" loading="lazy"><figcaption>Manila Cathedral on Plaza Roma, the second anchor of the walled-city loop after Fort Santiago and right before the San Agustin stop. Photo via <a href="https://commons.wikimedia.org/wiki/File:Manila_Cathedral_in_Intramuros_(2026-01-15).jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'Start at Binondo Church on Plaza Calderon de la Barca',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/manila-binondo-chinatown.jpg" alt="Binondo Chinatown street with Ongpin signage Manila" loading="lazy"><figcaption>Binondo, the worlds oldest Chinatown. Plaza Calderon de la Barca outside the church is the natural starting point for the Ongpin food walk.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Mt Batulao day hike
    // ----------------------------------------------------------------------
    'mt-batulao-day-hike-new-trail-first-timers' => [
        [
            'anchor' => 'The jump-off is in Barangay Kaylaway in Nasugbu, Batangas',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/3/32/Mount_Batulao.jpg/800px-Mount_Batulao.jpg" alt="Mount Batulao silhouette in Nasugbu Batangas" loading="lazy"><figcaption>Mount Batulao at 811 meters, the inactive volcano with the rolling ridge that most Manila hikers cut their teeth on. Photo via <a href="https://commons.wikimedia.org/wiki/File:Mount_Batulao.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'The new trail starts gently through open grassland and rolling ridges',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/e8/Last_trail_in_Mt._Batulao%2C_Nasugbu%2C_Batangas%2C_Philippines.jpg/800px-Last_trail_in_Mt._Batulao%2C_Nasugbu%2C_Batangas%2C_Philippines.jpg" alt="Open ridge trail on Mt Batulao Nasugbu Batangas" loading="lazy"><figcaption>The exposed ridge nearing the Mt Batulao summit. The new trail keeps the scrambles to one short section before the assault. Photo via <a href="https://commons.wikimedia.org/wiki/File:Last_trail_in_Mt._Batulao,_Nasugbu,_Batangas,_Philippines.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Burot Beach Calatagan camping
    // ----------------------------------------------------------------------
    'burot-beach-calatagan-overnight-camping-slow-weekend' => [
        [
            'anchor' => 'Burot is a long curving beach with cream sand',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/calatagan-1.jpg" alt="Cream sand beach in Calatagan Batangas" loading="lazy"><figcaption>The western Calatagan coast where Burot sits. The shallow water and the thin tree line are why barangay-managed camping still works here.</figcaption></figure>',
        ],
        [
            'anchor' => 'If you want a daytime add-on, drive 30 minutes to Cape Santiago Lighthouse',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/calatagan-3.jpg" alt="Calatagan Batangas coastline with bancas" loading="lazy"><figcaption>The Calatagan coastline near Sta. Ana. The Sombrero Island hop and the Cape Santiago side trip are both fair add-ons to a Burot overnight.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Mt Gulugod Baboy sunrise hike
    // ----------------------------------------------------------------------
    'mt-gulugod-baboy-sunrise-hike-mabini-batangas' => [
        [
            'anchor' => 'The ridge is the best part of the hike',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/anilao-mabini-1.jpg" alt="Anilao Mabini Batangas grassland and coast" loading="lazy"><figcaption>The Mabini grassland and the Maricaban Strait below. The ridge spine on Gulugod Baboy faces Verde Island on one side and the Batangas lowland on the other.</figcaption></figure>',
        ],
        [
            'anchor' => 'From the Anilao pier, you can boat across to Sombrero Island',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/anilao-mabini-anilao-dive-sites-cathedral-rock-mainit-point-twin.jpg" alt="Anilao Mabini Batangas dive boats and shoreline" loading="lazy"><figcaption>Anilao on the Mabini coast. The macro dive sites off Cathedral Rock and Mainit Point are the right pair after a Gulugod Baboy sunrise.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Tate Haus Pandi farm staycation
    // ----------------------------------------------------------------------
    'tate-haus-pandi-bulacan-farm-staycation-weekend' => [
        [
            'anchor' => 'The main house is a converted family home with three bedrooms',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/pandi-1.jpg" alt="Pandi Bulacan rice fields and farm road" loading="lazy"><figcaption>Pandi, Bulacan. The rice-field belt one hour north of Manila where farm-stay setups like Tate Haus sit among the working paddies.</figcaption></figure>',
        ],
        [
            'anchor' => 'drive 30 minutes to Biak-na-Bato National Park in San Miguel for the cave system',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/bulacan-province-biak-na-bato-national-park.jpg" alt="Biak na Bato National Park caves in Bulacan" loading="lazy"><figcaption>Biak-na-Bato National Park in San Miguel. The cave system and the Madlum River hike pair as a half-day side trip from any Pandi stay.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Camaya Coast Mariveles Bataan weekend
    // ----------------------------------------------------------------------
    'camaya-coast-mariveles-bataan-weekend-beach-plan' => [
        [
            'anchor' => 'Camaya Coast has a long stretch of golden sand that curves around a sheltered bay',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/e5/Camaya_coast_in_the_evening_-_2011.jpg/800px-Camaya_coast_in_the_evening_-_2011.jpg" alt="Camaya Coast beach in the evening Mariveles Bataan" loading="lazy"><figcaption>Camaya Coast in the evening. The bay faces inward toward Corregidor which blocks the open swells and keeps the swim area calm. Photo via <a href="https://commons.wikimedia.org/wiki/File:Camaya_coast_in_the_evening_-_2011.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'A short ferry hop from the Camaya pier takes you across to Corregidor Island',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/b/ba/Pacific_War_Memorial%2C_Corregidor_Island%2C_Philippines.JPG/800px-Pacific_War_Memorial%2C_Corregidor_Island%2C_Philippines.JPG" alt="Pacific War Memorial dome on Corregidor Island" loading="lazy"><figcaption>The Pacific War Memorial on Corregidor. The day-tour from the Camaya pier covers this stop along with Malinta Tunnel and the Mile-Long Barracks. Photo via <a href="https://commons.wikimedia.org/wiki/File:Pacific_War_Memorial,_Corregidor_Island,_Philippines.JPG" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'From Mariveles, you can drive 45 minutes north to Bagac for Las Casas Filipinas de Acuzar',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/morong-bataan-las-casas-filipinas-de-acuzar-bagac.jpg" alt="Las Casas Filipinas de Acuzar relocated bahay na bato Bagac" loading="lazy"><figcaption>Las Casas Filipinas de Acuzar in Bagac. The relocated bahay-na-bato houses pair as a half-day stop on the way back from Mariveles.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Borawan Island Padre Burgos Quezon camping
    // ----------------------------------------------------------------------
    'borawan-island-camping-trip-padre-burgos-quezon' => [
        [
            'anchor' => 'Borawan is small. You can walk the length of the beach in under 15 minutes.',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/8a/Padre_Burgos%2C_Quezon%2C_Philippines_-_Pilot_community_Barangay_Rizal.jpg/800px-Padre_Burgos%2C_Quezon%2C_Philippines_-_Pilot_community_Barangay_Rizal.jpg" alt="Padre Burgos Quezon coastal community" loading="lazy"><figcaption>Padre Burgos on the southern Quezon coast, the jump-off town for the Borawan banca crossing. Photo via <a href="https://commons.wikimedia.org/wiki/File:Padre_Burgos,_Quezon,_Philippines_-_Pilot_community_Barangay_Rizal.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'Dampalitan has a longer pine-lined beach and a better swimming stretch',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/landmarks/quezon.jpg" alt="Limestone coast of southern Quezon Province" loading="lazy"><figcaption>The southern Quezon coast off Padre Burgos. The Borawan-Dampalitan-Puting Buhangin rotation is one boat package across these limestone-rimmed islets.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Manila to Baguio bus routes
    // ----------------------------------------------------------------------
    'manila-to-baguio-bus-routes-honest-read' => [
        [
            'anchor' => 'Victory Liner has terminals in Cubao, Pasay, Caloocan, and Sampaloc',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/transport/victory-liner.jpg" alt="Victory Liner bus at the Cubao terminal" loading="lazy"><figcaption>Victory Liner, the largest fleet on the Manila to Baguio route. The Cubao terminal has the most departures across the day.</figcaption></figure>',
        ],
        [
            'anchor' => 'Genesis runs the Joybus First Class from the Cubao Bus Station',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/transport/genesis-transport.jpg" alt="Genesis Transport bus loading at Pasay terminal" loading="lazy"><figcaption>Genesis Transport runs the Joybus First Class with the near-flat sleeper seats. The route uses TPLEX and Marcos Highway only, no Kennon detour.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Manila to Puerto Galera ferry / seaplane
    // ----------------------------------------------------------------------
    'manila-to-puerto-galera-ferry-seaplane-traveler-read' => [
        [
            'anchor' => 'Three main ferry operators run the Batangas to Puerto Galera route',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/transport/montenegro-lines.jpg" alt="Montenegro Lines passenger boat at Batangas Pier" loading="lazy"><figcaption>Montenegro Lines at Batangas Pier. One of the three operators running the crossing to Puerto Galera, smaller and faster than the Minolo roll-on roll-off.</figcaption></figure>',
        ],
        [
            'anchor' => 'Air Juan and a few smaller charter operators run the seaplane service',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/puerto-galera-2.jpg" alt="Puerto Galera coast Oriental Mindoro" loading="lazy"><figcaption>Puerto Galera on Oriental Mindoro. The seaplane lands directly at White Beach, cutting the standard five to six hour bus-and-ferry trip down to a 20-minute hop.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Davao City five nature parks
    // ----------------------------------------------------------------------
    'davao-city-five-nature-parks-calm-day-plan' => [
        [
            'anchor' => 'The Philippine Eagle Center in Malagos is the dedicated breeding and conservation facility',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/davao-city-philippine-eagle-center.jpg" alt="Philippine Eagle in flight cage at the Malagos conservation center" loading="lazy"><figcaption>The Philippine Eagle Center in Malagos holds the largest captive population of the critically endangered species and runs the only reintroduction breeding program.</figcaption></figure>',
        ],
        [
            'anchor' => 'Davao Crocodile Park is the closest of the five parks to the city center',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/davao-city-roxas-avenue-crocodile-park-toril.jpg" alt="Crocodile Park enclosure in Davao City" loading="lazy"><figcaption>Davao Crocodile Park in Toril, around 20 minutes from downtown. The feeding shows run on a fixed schedule and the butterfly garden is the calmer side of the complex.</figcaption></figure>',
        ],
        [
            'anchor' => 'Eat durian when in season, the Bankerohan market',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/davao-city-durian.jpg" alt="Davao durian fruit halved" loading="lazy"><figcaption>Davao durian. The Bankerohan market and the Magsaysay Park vendors hold the freshest stock when the fruit is in season from August to October.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Cebu + Bohol 10 day loop
    // ----------------------------------------------------------------------
    'cebu-bohol-ten-day-multi-island-loop-itinerary' => [
        [
            'anchor' => 'Land at Mactan-Cebu International, cross to Cebu City for the historic stops',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/cebu-city-tops-lookout.jpg" alt="Tops Lookout view over Cebu City" loading="lazy"><figcaption>Tops Lookout above Cebu City, the high vantage that closes a downtown heritage day after Magellans Cross and the Basilica del Santo Nino.</figcaption></figure>',
        ],
        [
            'anchor' => 'For lechon, try the smaller stalls in Talisay over the famous chains',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/cebu-city-puso.jpg" alt="Hanging rice puso parcels in Cebu" loading="lazy"><figcaption>Puso, the hanging rice in woven coconut-leaf parcels. The cleanest pairing for the Talisay or Punta Princesa lechon and the chicken inasal stops.</figcaption></figure>',
        ],
        [
            'anchor' => 'The standard Bohol countryside tour covers the Chocolate Hills',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/54/Chocolate_Hills_Carmen_Bohol_2019.jpg/800px-Chocolate_Hills_Carmen_Bohol_2019.jpg" alt="Chocolate Hills dome-shaped formations in Carmen Bohol" loading="lazy"><figcaption>The Chocolate Hills in Carmen, Bohol. Over 1,200 dome-shaped grass mounds that turn cocoa-brown in the dry months. Photo via <a href="https://commons.wikimedia.org/wiki/File:Chocolate_Hills_Carmen_Bohol_2019.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'From Tagbilaran, the OceanJet fast ferry to Siquijor takes around two hours',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/siquijor-the-old-enchanted-balete-tree.jpg" alt="Old enchanted balete tree on Siquijor island" loading="lazy"><figcaption>The old enchanted balete tree near Lazi, one of the recurring stops on the Siquijor scooter loop along with Cambugahay Falls and Salagdoong Beach.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Best surfing spots Philippines
    // ----------------------------------------------------------------------
    'best-surfing-spots-philippines-calm-roundup' => [
        [
            'anchor' => 'La Union sits on the northwest Luzon coast and the surf town of San Juan',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/0/01/Surf%E2%80%99s_up.jpg/800px-Surf%E2%80%99s_up.jpg" alt="Surfer on a wave at Urbiztondo Beach La Union" loading="lazy"><figcaption>Urbiztondo Beach in San Juan, La Union. A reef break with sand bottom, beginner-friendly in the small-swell months and overhead during the habagat from July to October. Photo via <a href="https://commons.wikimedia.org/wiki/File:Surf%E2%80%99s_up.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'Siargao in Surigao del Norte holds the title for the country',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/45/Cloud_9_Boardwalk%2C_Siargao.jpg/800px-Cloud_9_Boardwalk%2C_Siargao.jpg" alt="Cloud 9 boardwalk at sunrise in Siargao" loading="lazy"><figcaption>The Cloud 9 boardwalk in General Luna, Siargao. The wave breaks shallow over reef so the named break itself is for experienced surfers, the gentler spots are at Jacking Horse and Quiksilver. Photo via <a href="https://commons.wikimedia.org/wiki/File:Cloud_9_Boardwalk,_Siargao.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'Baler in Aurora is the second most common surf destination',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/0/0b/Sabang_Beach_sunrise.jpg/800px-Sabang_Beach_sunrise.jpg" alt="Sunrise over Sabang Beach Baler Aurora" loading="lazy"><figcaption>Sunrise on Sabang Beach in Baler. The long sand-bottom break is the main learner area, with Cemento Reef on the bay end reserved for experienced surfers. Photo via <a href="https://commons.wikimedia.org/wiki/File:Sabang_Beach_sunrise.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'Puraran Beach on the eastern coast of Catanduanes has the Majestics break',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/98/Puraran_beach.jpg/800px-Puraran_beach.jpg" alt="Puraran Beach in Baras Catanduanes" loading="lazy"><figcaption>Puraran Beach in Baras, Catanduanes, home of the Majestics right-hand reef break. Quieter than Cloud 9 with the same typhoon swell, best September to November. Photo via <a href="https://commons.wikimedia.org/wiki/File:Puraran_beach.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Seven unspoiled beaches in Bicol
    // ----------------------------------------------------------------------
    'seven-unspoiled-beaches-bicol-region-quiet-read' => [
        [
            'anchor' => 'Subic Beach sits on Calintaan Island off the Matnog coast',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/sorsogon-subic-beach-matnog-pink-sand.jpg" alt="Pink sand of Subic Beach in Matnog Sorsogon" loading="lazy"><figcaption>Subic Beach on Calintaan Island off Matnog. The pink tint comes from crushed red coral mixed with white sand, best seen in the wet line at the shore.</figcaption></figure>',
        ],
        [
            'anchor' => 'Bagasbas in Daet is the eastern Camarines Norte beach',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/23/Bagasbas_Beach_Pacific_waves_%28Daet%2C_Camarines_Norte%3B_04-14-2023%29.jpg/800px-Bagasbas_Beach_Pacific_waves_%28Daet%2C_Camarines_Norte%3B_04-14-2023%29.jpg" alt="Pacific waves rolling onto Bagasbas Beach in Daet" loading="lazy"><figcaption>Bagasbas Beach in Daet, Camarines Norte. The wave is consistent year-round and sand-bottom, which makes it the calmer learner alternative to Baler and Siargao. Photo via <a href="https://commons.wikimedia.org/wiki/File:Bagasbas_Beach_Pacific_waves_(Daet,_Camarines_Norte;_04-14-2023).jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'Caramoan is the peninsular town on the eastern coast of Camarines Sur',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/Tugawe_Cove%2C_Caramoan%2C_Camarines_Sur.jpg/800px-Tugawe_Cove%2C_Caramoan%2C_Camarines_Sur.jpg" alt="Tugawe Cove limestone karsts in Caramoan" loading="lazy"><figcaption>Tugawe Cove in Caramoan, Camarines Sur. The limestone karsts and white-sand islets across Lahos, Matukad, and Sabitang Laya share the same look as the Tour A coast in El Nido. Photo via <a href="https://commons.wikimedia.org/wiki/File:Tugawe_Cove,_Caramoan,_Camarines_Sur.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'Cagbalan Cove sits on the eastern Bulusan coast',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/sorsogon-tikling-island.jpg" alt="Tikling Island near Matnog Sorsogon" loading="lazy"><figcaption>Tikling Island off Matnog, a regular pair with Subic Beach on the Sorsogon day-tour banca rotation. The same calm waters extend up the Bulusan coast at Cagbalan Cove.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Where to go in Quezon Province
    // ----------------------------------------------------------------------
    'where-to-go-quezon-province-travelers-read' => [
        [
            'anchor' => 'Lucena is the provincial capital and the transport hub',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/lucena-lucena-cathedral-san-fernando.jpg" alt="Lucena Cathedral San Fernando in Quezon" loading="lazy"><figcaption>Lucena Cathedral of San Fernando. The provincial capital is the lunch and transfer hub for almost every Quezon trip, with the Grand Central Terminal handling the bus arrivals.</figcaption></figure>',
        ],
        [
            'anchor' => 'Walk the heritage core, visit the Kamay ni Hesus shrine',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/quezon-province-pahiyas-festival-in-lucban-may-15.jpg" alt="Lucban Pahiyas Festival decorated facade with kiping" loading="lazy"><figcaption>A Lucban facade dressed for the May 15 Pahiyas Festival. Outside festival season, the same houses are quieter and the Kamay ni Hesus shrine above town is the headline stop.</figcaption></figure>',
        ],
        [
            'anchor' => 'Tayabas sits 15 minutes from Lucban and holds the longest church',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/quezon-province-tayabas-casa-comunidad-and-old-church.jpg" alt="Tayabas Casa Comunidad and old church Quezon" loading="lazy"><figcaption>Tayabas heritage core. The Basilica of Saint Michael the Archangel, shaped like a key, anchors the town along with the 1840 Malagonlong Bridge.</figcaption></figure>',
        ],
        [
            'anchor' => 'Sariaya is the small town between Lucena and Tayabas',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/sariaya-2.jpg" alt="Sariaya bahay na bato ancestral houses Quezon" loading="lazy"><figcaption>Sariaya holds one of the densest concentrations of pre-war bahay-na-bato houses in the country. The Rodriguez, Gala, and Natalio Enriquez houses are the headline stops.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Dumaguete three day plan
    // ----------------------------------------------------------------------
    'dumaguete-three-day-plan-twin-lakes-apo' => [
        [
            'anchor' => 'Eat sansrival at Sans Rival on Rizal Boulevard',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/dumaguete-silvanas.jpg" alt="Silvanas pastry from Dumaguete" loading="lazy"><figcaption>Silvanas, the cashew-meringue-and-buttercream Dumaguete pastry. The cleanest version still comes from Sans Rival on Rizal Boulevard, kept frozen until you eat it.</figcaption></figure>',
        ],
        [
            'anchor' => 'Hire a tricycle or rent a scooter for the day trip to Twin Lakes National Park',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/f/f8/The_lake_in_the_clouds.jpg/800px-The_lake_in_the_clouds.jpg" alt="Balinsasayao Twin Lakes in Negros Oriental forest highlands" loading="lazy"><figcaption>Lake Balinsasayao in the Sibulan highlands. The forest holds the cloud cover in and the temperature drops noticeably on the climb up from Dumaguete. Photo via <a href="https://commons.wikimedia.org/wiki/File:The_lake_in_the_clouds.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'Apo Island is the small volcanic island around an hour south of Dumaguete',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/3/3b/Swimming-with-turtles-apo-island-archie-mercader.jpg/800px-Swimming-with-turtles-apo-island-archie-mercader.jpg" alt="Sea turtle in the shallows at Apo Island marine sanctuary" loading="lazy"><figcaption>Swimming with sea turtles at Apo Island marine sanctuary off Dauin. One of the longest-running community-managed reefs in the country. Photo via <a href="https://commons.wikimedia.org/wiki/File:Swimming-with-turtles-apo-island-archie-mercader.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Affordable weekend getaways near Metro Manila
    // ----------------------------------------------------------------------
    'affordable-weekend-getaways-near-metro-manila-list' => [
        [
            'anchor' => 'Tagaytay is the closest cool-weather escape from Manila',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/tagaytay-bulalo.jpg" alt="Bulalo bone marrow soup served in Tagaytay" loading="lazy"><figcaption>Tagaytay bulalo, the bone-marrow beef soup that anchors the food scene along the ridge highway. The cool air makes the steaming bowl land harder than it does in Manila.</figcaption></figure>',
        ],
        [
            'anchor' => 'Anilao in Mabini, Batangas is the macro diving capital of the country',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/anilao-mabini-2.jpg" alt="Anilao Mabini Batangas dive coast" loading="lazy"><figcaption>The Anilao coast in Mabini. Three hours from Manila and the closest dive escape, with most resorts running full-board packages built around the macro sites.</figcaption></figure>',
        ],
        [
            'anchor' => 'Pansol in Calamba, Laguna is the hot springs cluster',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/pansol-rizal-shrine-in-calamba-poblacion.jpg" alt="Pansol Calamba Laguna area" loading="lazy"><figcaption>The Pansol-Calamba area. The hot pools along the highway are fed by the underground volcanic springs of Mt Makiling, and most resorts run 22-hour stays for the overnight swim.</figcaption></figure>',
        ],
        [
            'anchor' => 'Subic in Zambales sits around three hours from Manila via NLEX',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/subic-tree-top-adventure.jpg" alt="Tree Top Adventure canopy walk in Subic" loading="lazy"><figcaption>Tree Top Adventure in Subic Freeport. One of the family-anchor stops alongside Zoobic Safari and Ocean Adventure that make Subic the friendliest weekend for parents with kids.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Ten best camping sites in Luzon
    // ----------------------------------------------------------------------
    'ten-best-camping-sites-luzon-slow-roundup' => [
        [
            'anchor' => 'Mt Pulag is the highest peak in Luzon and the most famous mountain camping',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/d/db/Sea_of_clouds_2024-08-02.jpg/800px-Sea_of_clouds_2024-08-02.jpg" alt="Sea of clouds at Mt Pulag summit Benguet" loading="lazy"><figcaption>The Mt Pulag sea of clouds. Camp at the saddle near the Ambangeg trail summit, pack a four-season tent and a thermal bag, the night is cold even in summer. Photo via <a href="https://commons.wikimedia.org/wiki/File:Sea_of_clouds_2024-08-02.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'Cagbalete is the easy island camping in Quezon Province',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/8e/Sunrise_in_Cagbalete.jpg/800px-Sunrise_in_Cagbalete.jpg" alt="Sunrise on Cagbalete Island beach in Mauban Quezon" loading="lazy"><figcaption>Sunrise on Cagbalete Island in Mauban. Cream sand under coconut trees, the sand flat stretches kilometers at low tide. Photo via <a href="https://commons.wikimedia.org/wiki/File:Sunrise_in_Cagbalete.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'Mt Pinatubo has overnight camping at the crater lake area',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/pampanga-province-mt-pinatubo-crater-lake.jpg" alt="Mt Pinatubo crater lake in Capas Tarlac" loading="lazy"><figcaption>Mt Pinatubo crater lake. The 4x4 ride drops you at the trailhead and the 30-minute hike opens to the rim. Bring a four-season tent, the wind cuts hard at the crater edge.</figcaption></figure>',
        ],
        [
            'anchor' => 'Pundaquit is the mainland coast beach in San Antonio',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/2e/SanAntonio%2CZambalesjf9052_05.JPG/800px-SanAntonio%2CZambalesjf9052_05.JPG" alt="Pundaquit fishing village beach in San Antonio Zambales" loading="lazy"><figcaption>Pundaquit in San Antonio, Zambales, the fishing-village jump-off for Anawangin and Capones. The mainland beach itself takes camping if the banca ride is not your plan. Photo via <a href="https://commons.wikimedia.org/wiki/File:SanAntonio,Zambalesjf9052_05.JPG" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'Mt Daraitan is the popular mountain hike east of Manila with a campsite',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/0/08/View_from_Mt._Daraitan%27s_Summit%2C_Tanay%2C_Rizal%2C_Jun_2025.jpg/800px-View_from_Mt._Daraitan%27s_Summit%2C_Tanay%2C_Rizal%2C_Jun_2025.jpg" alt="View from Mt Daraitan summit Tanay Rizal" loading="lazy"><figcaption>The view from the Mt Daraitan summit clearing over the Sierra Madre range. The Daraitan River swim at the base is the natural pair on the descent. Photo via <a href="https://commons.wikimedia.org/wiki/File:View_from_Mt._Daraitan%27s_Summit,_Tanay,_Rizal,_Jun_2025.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Anvaya Cove Morong Bataan day trip
    // ----------------------------------------------------------------------
    'anvaya-cove-morong-bataan-day-trip-first-timers' => [
        [
            'anchor' => 'The 320-hectare property opens to a long curved beach',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/morong-bataan-anvaya-cove-gated.jpg" alt="Anvaya Cove beach and bay in Morong Bataan" loading="lazy"><figcaption>Anvaya Cove on the Morong side of Bataan. The inner Subic Bay color is more emerald than turquoise, which is normal for this stretch of coast.</figcaption></figure>',
        ],
        [
            'anchor' => 'The undeveloped half of the property is laced with quiet hiking trails',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/bataan-province-bagac-morong-road-coast-drive.jpg" alt="Bagac Morong coastal road in Bataan" loading="lazy"><figcaption>The Bagac-Morong coastal road. The last 30 minutes into Anvaya are this winding two-lane stretch through small barangays, slow but scenic.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Lakawon Island Cadiz Negros Occidental
    // ----------------------------------------------------------------------
    'lakawon-island-cadiz-negros-occidental-weekend' => [
        [
            'anchor' => 'The TawHai floating bar is the activity most weekenders come for',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/82/Lakawon_white_beach.jpg/800px-Lakawon_white_beach.jpg" alt="Long pale beach on Lakawon Island Negros Occidental" loading="lazy"><figcaption>Lakawon Islands long pale beach. The 13-hectare sandbar is shaped like a banana and sits roughly 3 kilometers off the Cadiz coast. Photo via <a href="https://commons.wikimedia.org/wiki/File:Lakawon_white_beach.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'If you have time before your flight, stop at Manokan Country in Bacolod',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/bacolod-1.jpg" alt="Bacolod City Negros Occidental street" loading="lazy"><figcaption>Bacolod City, the natural base before and after a Lakawon weekend. The Manokan Country inasal row is the last stop on the way to the airport.</figcaption></figure>',
        ],
    ],

];
