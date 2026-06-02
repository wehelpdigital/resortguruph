<?php

/**
 * Blog image overlays for batch 5 posts (Palawan + Mindoro destination guides
 * and themed travel posts). Each entry maps a blog slug to a list of
 * <figure> HTML blocks that the renderer injects either before or after a
 * unique anchor substring found in the post content_html.
 *
 * Generic opinion/advice posts in the batch (solo female travel, budget
 * backpacking, family-friendly destinations, packing, monsoon read, getting
 * around) are intentionally not enhanced here because they lack a
 * concrete list of named photographable subjects.
 */

return [

    // ----------------------------------------------------------------------
    // Tacloban + Kalanggaman Island (batch4)
    // ----------------------------------------------------------------------
    'tacloban-kalanggaman-island-leyte-combo' => [
        [
            'anchor' => 'Kalanggaman Island is the long thin sandbar',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/e4/Kalanggaman_Island.jpg/800px-Kalanggaman_Island.jpg" alt="Aerial view of Kalanggaman Island sandbar off Palompon Leyte" loading="lazy"><figcaption>Kalanggaman Island from the air. The bird-shaped sandbar sits off Palompon and the local government caps daily visitor numbers to keep it clean. Photo via <a href="https://commons.wikimedia.org/wiki/File:Kalanggaman_Island.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // El Nido Tour A
    // ----------------------------------------------------------------------
    'el-nido-tour-a-big-lagoon-miniloc-loop' => [
        [
            'anchor' => 'Big Lagoon is the headline stop',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/el-nido-big-lagoon-tour-a.jpg" alt="Kayakers paddling into Big Lagoon El Nido" loading="lazy"><figcaption>Big Lagoon at the start of Tour A. Best paddled before the mid-morning boats arrive at the entrance.</figcaption></figure>',
        ],
        [
            'anchor' => 'Seven Commandos Beach closes the day',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/3/34/Seven_Commandos_Beach_%2848118964686%29.jpg/800px-Seven_Commandos_Beach_%2848118964686%29.jpg" alt="Seven Commandos Beach in El Nido Palawan" loading="lazy"><figcaption>Seven Commandos Beach, the closing stop on Tour A. A long white-sand stretch with bar service and the right place to wait out golden hour. Photo via <a href="https://commons.wikimedia.org/wiki/File:Seven_Commandos_Beach_(48118964686).jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Nacpan + Duli scooter ride
    // ----------------------------------------------------------------------
    'nacpan-duli-el-nido-beaches-scooter-ride' => [
        [
            'anchor' => 'Nacpan is the four-kilometer golden stretch',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/el-nido-nacpan-beach-45-min-north.jpg" alt="Nacpan Beach palms and shoreline north of El Nido" loading="lazy"><figcaption>Nacpan Beach, the long golden stretch about 45 minutes north of El Nido town. Quiet before 9 a.m., busier once the van tours arrive.</figcaption></figure>',
        ],
        [
            'anchor' => 'Duli is the trade-off beach',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/d/dc/Duli_beach_can_surprise_with_strong_rip_currents_-_panoramio.jpg/800px-Duli_beach_can_surprise_with_strong_rip_currents_-_panoramio.jpg" alt="Duli Beach surf coast north of El Nido Palawan" loading="lazy"><figcaption>Duli Beach further north on the same coast. The road is rougher and the surf picks up from November to March. Photo via <a href="https://commons.wikimedia.org/wiki/File:Duli_beach_can_surprise_with_strong_rip_currents_-_panoramio.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Coron Twin Lagoon + Kayangan
    // ----------------------------------------------------------------------
    'coron-twin-lagoon-kayangan-honest-island-day' => [
        [
            'anchor' => 'Kayangan is the lake every Coron postcard uses',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/3/3b/Kayangan_Lake%2C_Coron_Island.jpg/800px-Kayangan_Lake%2C_Coron_Island.jpg" alt="Kayangan Lake Coron Island Palawan" loading="lazy"><figcaption>Kayangan Lake on Coron Island. Reach it by climbing the wooden boardwalk over the limestone ridge from the docking inlet. Photo via <a href="https://commons.wikimedia.org/wiki/File:Kayangan_Lake,_Coron_Island.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'Twin Lagoon is the more dramatic of the two',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/82/Twin_Lagoon_Coron_Palawan.jpg/800px-Twin_Lagoon_Coron_Palawan.jpg" alt="Twin Lagoon limestone walls Coron Palawan" loading="lazy"><figcaption>Twin Lagoon in Coron. The inner lagoon sits behind a limestone wall with a low rock gap at low tide or a wooden ladder at high tide. Photo via <a href="https://commons.wikimedia.org/wiki/File:Twin_Lagoon_Coron_Palawan.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Port Barton
    // ----------------------------------------------------------------------
    'port-barton-slow-palawan-alternative-el-nido' => [
        [
            'anchor' => 'Port Barton is the Palawan town El Nido travelers find by accident',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/53/Port_Barton_Beach%2C_Palawan%2C_Philippines.jpg/800px-Port_Barton_Beach%2C_Palawan%2C_Philippines.jpg" alt="Sunset on Port Barton Beach in Palawan" loading="lazy"><figcaption>Port Barton bay at sunset. Smaller boats, fewer crowds, and the same general bay water as El Nido. Photo via <a href="https://commons.wikimedia.org/wiki/File:Port_Barton_Beach,_Palawan,_Philippines.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Puerto Princesa Underground River
    // ----------------------------------------------------------------------
    'puerto-princesa-underground-river-diy-first-timers' => [
        [
            'anchor' => 'Inside the Cave',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/f/fe/Puerto_Princesa_Underground_River_jpg.jpg/800px-Puerto_Princesa_Underground_River_jpg.jpg" alt="Puerto Princesa Underground River cave access through the river" loading="lazy"><figcaption>The cave access at the Puerto Princesa Subterranean River. You ride a paddle-boat in with a guide and an audio loop. Photo via <a href="https://commons.wikimedia.org/wiki/File:Puerto_Princesa_Underground_River_jpg.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'The smart move is to stay one night in Sabang',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/morong-bataan-sabang-beach.jpg" alt="Sabang Beach the jump-off point for the Underground River" loading="lazy"><figcaption>Sabang Beach is the jump-off for the cave. Staying one night here lets you catch the first morning boat before the day-trip vans arrive from the city.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Puerto Galera White Beach
    // ----------------------------------------------------------------------
    'puerto-galera-white-beach-weekend-plan' => [
        [
            'anchor' => 'White Beach is the swim-and-eat beach',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/puerto-galera-white-beach.jpg" alt="Puerto Galera White Beach shoreline and bancas" loading="lazy"><figcaption>White Beach in Puerto Galera. Soft sand, shallow water, and the loudest of the three peninsula beaches once the bars open up.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Calapan, Mindoro
    // ----------------------------------------------------------------------
    'calapan-mindoro-budget-stop-travelers-skip' => [
        [
            'anchor' => 'Silonay is the standout reason',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/6f/Santo_Ni%C3%B1o_Cathedral_%28Calapan%29_facade.jpg/800px-Santo_Ni%C3%B1o_Cathedral_%28Calapan%29_facade.jpg" alt="Santo Nino Cathedral facade in Calapan Oriental Mindoro" loading="lazy"><figcaption>The Sto. Nino Cathedral fronts the central plaza in Calapan and anchors the small downtown grid worth walking at sunset. Photo via <a href="https://commons.wikimedia.org/wiki/File:Santo_Ni%C3%B1o_Cathedral_(Calapan)_facade.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Sabang Beach, Puerto Galera (dive side)
    // ----------------------------------------------------------------------
    'sabang-beach-puerto-galera-calm-read-divers' => [
        [
            'anchor' => 'The Sabang peninsula has three beaches',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/puerto-galera-sabang-beach.jpg" alt="Sabang Beach the dive side of Puerto Galera" loading="lazy"><figcaption>Sabang Beach, the middle of the three peninsula beaches and the base for most Puerto Galera dive shops. Quieter than White Beach by every measure.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Apo Reef, Mindoro
    // ----------------------------------------------------------------------
    'apo-reef-mindoro-diving-second-largest-atoll' => [
        [
            'anchor' => 'Apo Reef sits around 30 kilometers',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/5e/Apo_Reef_with_the_Apo_Reef_Lighthouse.jpg/800px-Apo_Reef_with_the_Apo_Reef_Lighthouse.jpg" alt="Apo Reef Natural Park with the Apo Reef Lighthouse" loading="lazy"><figcaption>Apo Reef and its lighthouse, off the western coast of Occidental Mindoro. The largest atoll-type reef in the Philippines and the second largest contiguous reef in the world. Photo via <a href="https://commons.wikimedia.org/wiki/File:Apo_Reef_with_the_Apo_Reef_Lighthouse.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Honda Bay Island Hopping
    // ----------------------------------------------------------------------
    'honda-bay-island-hopping-calm-puerto-princesa-day' => [
        [
            'anchor' => 'Luli stands for lulubog-lilitaw',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/0/0c/Luli_Island%2C_Honda_Bay%2C_Palawan%2C_Philippines.jpg/800px-Luli_Island%2C_Honda_Bay%2C_Palawan%2C_Philippines.jpg" alt="Luli Island sandbar in Honda Bay Palawan" loading="lazy"><figcaption>Luli Island in Honda Bay. The sandbar disappears at high tide and reappears at low tide, which is where the name comes from. Photo via <a href="https://commons.wikimedia.org/wiki/File:Luli_Island,_Honda_Bay,_Palawan,_Philippines.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Philippine Festival Calendar (selective, 2 figures)
    // ----------------------------------------------------------------------
    'philippine-festival-calendar-by-month' => [
        [
            'anchor' => 'Sinulog, Cebu City',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/cebu-city-sinulog-festival-every-january.jpg" alt="Sinulog Festival street dancers in Cebu City every January" loading="lazy"><figcaption>Sinulog in Cebu City fills the central streets with beaded costumes on the third Sunday of January. Book accommodation months ahead.</figcaption></figure>',
        ],
        [
            'anchor' => 'MassKara, Bacolod',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/bacolod-masskara-festival-every-october.jpg" alt="MassKara Festival smiling masks in Bacolod every October" loading="lazy"><figcaption>The MassKara Festival mask parade in Bacolod, born out of the 1980s sugar industry crash as a way to lift the city mood. Chicken inasal on every corner during festival week.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Philippine Food Trail
    // ----------------------------------------------------------------------
    'philippine-food-trail-across-islands-regional-read' => [
        [
            'anchor' => 'Eat sisig at Aling Lucing in Angeles',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/pampanga-province-sisig.jpg" alt="Sizzling Kapampangan pork sisig" loading="lazy"><figcaption>Sisig, the sizzling Kapampangan pork dish first popularized at Aling Lucing in Angeles, Pampanga.</figcaption></figure>',
        ],
        [
            'anchor' => 'Lechon Cebu is famously the best in the country',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/cebu-city-lechon.jpg" alt="Cebu lechon roasted whole over coconut husks" loading="lazy"><figcaption>Cebu lechon, salted and stuffed with herbs, slow-roasted over coconut husks. Order at Zubuchon or House of Lechon in Cebu City.</figcaption></figure>',
        ],
        [
            'anchor' => 'La Paz Batchoy is the headline',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/iloilo-city-la-paz-batchoy.jpg" alt="La Paz Batchoy noodle soup from Iloilo" loading="lazy"><figcaption>La Paz Batchoy, the Iloilo noodle soup with pork liver, crushed chicharon, and rich bone broth. The original counter is at Ted&apos;s Old Timer in La Paz Public Market.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Manila Weekend Escapes (3 figures)
    // ----------------------------------------------------------------------
    'manila-weekend-escapes-within-three-hours-drive' => [
        [
            'anchor' => 'Cool weather, Taal Lake views',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/tagaytay-peoples-park-in-the-sky.jpg" alt="People&apos;s Park in the Sky Tagaytay overlooking Taal" loading="lazy"><figcaption>People&apos;s Park in the Sky in Tagaytay. The classic two-hour escape from Manila with cool ridge weather and the Taal Lake panorama.</figcaption></figure>',
        ],
        [
            'anchor' => 'The Pinto Art Museum is the headline stop',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/antipolo-pinto-art-museum.jpg" alt="Pinto Art Museum white Cycladic galleries in Antipolo" loading="lazy"><figcaption>Pinto Art Museum in Antipolo. White Cycladic-style galleries and long manicured gardens about 90 minutes from Manila.</figcaption></figure>',
        ],
        [
            'anchor' => 'The former US naval base layout means clean roads, walkable resort areas',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/subic-ocean-adventure.jpg" alt="Ocean Adventure marine park in Subic Bay" loading="lazy"><figcaption>Ocean Adventure in Subic Bay. One of the family-targeted draws inside the former US naval base, paired well with Zoobic Safari for a Saturday loop.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Diving Philippines for First-Time Divers
    // ----------------------------------------------------------------------
    'diving-philippines-first-time-open-water-divers' => [
        [
            'anchor' => 'The most popular beginner pick',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/dauin-apo-island-marine-sanctuary.jpg" alt="Apo Island Marine Sanctuary off Dauin Negros Oriental" loading="lazy"><figcaption>The Apo Island Marine Sanctuary off Dauin in Negros Oriental. Sea turtles are routine sightings and the shore-dive profile is forgiving for first-time open water divers.</figcaption></figure>',
        ],
        [
            'anchor' => 'The other Visayan beginner hub',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/panglao-alona-beach.jpg" alt="Alona Beach in Panglao Bohol" loading="lazy"><figcaption>Alona Beach on Panglao, Bohol. The beach strip is lined with PADI five-star dive shops and most beginner courses run shore checkouts directly off the reef here.</figcaption></figure>',
        ],
    ],

];
