<?php

/**
 * Per-post bespoke image overlays for the Bicol + Mindanao blog batch.
 *
 * Each entry maps a post slug to a list of insertion directives:
 *   - anchor:   a unique HTML substring inside the post's content_html
 *   - position: 'before' or 'after' the anchor block
 *   - html:     a <figure class="rg-figure"> block to splice into the post
 *
 * Source priority: local rg-media first, Wikimedia Commons fallback. Anchors
 * are case sensitive and must appear exactly once in their post body. None of
 * the inserts sit in the lede or in the auto-appended "Where to stay near"
 * block, so they survive a seeder re-run on rg_blog_posts.
 */

return [

    // -- Camp John Hay: Wikimedia photo, two strategic inserts ----------
    'camp-john-hay-calm-baguio-weekend-pine-side' => [
        [
            'anchor' => '<h3>The Yellow Trail</h3>',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/5b/Trees_at_Camp_John_Hay%2C_Baguio%2C_Jul_2025.jpg/800px-Trees_at_Camp_John_Hay%2C_Baguio%2C_Jul_2025.jpg" alt="Pine trees at Camp John Hay, Baguio" loading="lazy"><figcaption>Old-growth pines along the Camp John Hay trails, the same canopy that shades the Yellow Trail loop. Photo via <a href="https://commons.wikimedia.org/wiki/File:Trees_at_Camp_John_Hay,_Baguio,_Jul_2025.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => '<h3>The Manor and the Garden</h3>',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/51/Camp_John_Hay%2C_Baguio_City.jpg/800px-Camp_John_Hay%2C_Baguio_City.jpg" alt="The Manor at Camp John Hay" loading="lazy"><figcaption>The Manor sits at the heart of Camp John Hay, the heritage hotel built in 1908 as the American officers\' quarters. Photo via <a href="https://commons.wikimedia.org/wiki/File:Camp_John_Hay,_Baguio_City.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // -- Anawangin Cove: local cove image + Wikimedia Capones lighthouse -
    'anawangin-cove-camping-diy-two-day-pundaquit' => [
        [
            'anchor' => '<h3>Swim and Walk the Cove</h3>',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/subic-anawangin-and-nagsasa-coves-from-pundaquit.jpg" alt="Anawangin Cove from Pundaquit" loading="lazy"><figcaption>Anawangin Cove, the agoho-pine-lined gray-sand beach reached only by boat from Pundaquit in San Antonio, Zambales.</figcaption></figure>',
        ],
        [
            'anchor' => '<h3>Capones Island</h3>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/1b/Capones_Island.jpg/800px-Capones_Island.jpg" alt="Capones Island lighthouse off Zambales" loading="lazy"><figcaption>Capones Island off the Zambales coast, capped by the 1890 Spanish-era lighthouse that crowns the rocky upper section. Photo via <a href="https://commons.wikimedia.org/wiki/File:Capones_Island.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // -- Cagsawa Ruins half-day: 3 local Albay shots --------------------
    'cagsawa-ruins-mayon-view-legazpi-half-day' => [
        [
            'anchor' => '<h2>The Half-Day Plan</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/albay-legazpi-cagsawa-ruins.jpg" alt="Cagsawa Ruins bell tower with Mayon Volcano" loading="lazy"><figcaption>The Cagsawa belfry framed against Mayon Volcano, the surviving stone tower of the church buried by the 1814 eruption.</figcaption></figure>',
        ],
        [
            'anchor' => '<h3>Combine With Lignon Hill</h3>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/albay-legazpi-lignon-hill.jpg" alt="Lignon Hill viewpoint over Legazpi and Mayon" loading="lazy"><figcaption>Lignon Hill in Legazpi, the wide-frame viewpoint that lines up Mayon, the airport runway, and the Albay Gulf.</figcaption></figure>',
        ],
        [
            'anchor' => '<h3>Late Lunch at 1st Colonial Grill</h3',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/albay-legazpi-sili-ice-cream.jpg" alt="Sili ice cream from Legazpi" loading="lazy"><figcaption>Sili ice cream at 1st Colonial Grill, the chili-kick scoop that closes the Cagsawa morning the proper Bicol way.</figcaption></figure>',
        ],
    ],

    // -- CWC Wakeboarding: single anchor figure -------------------------
    'cwc-wakeboarding-camarines-sur-first-timer' => [
        [
            'anchor' => '<h2>The DIY Plan for a First Visit</h2>',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/naga-camarines-sur-cwc-camarines-sur-watersports-complex-in-pili.jpg" alt="CamSur Watersports Complex in Pili" loading="lazy"><figcaption>The cable lake at CamSur Watersports Complex in Pili, the still-water main loop that the beginner boomerang cable branches off from.</figcaption></figure>',
        ],
    ],

    // -- Naga food + Penafrancia: 4 local images ------------------------
    'naga-food-penafrancia-festival-diy-guide' => [
        [
            'anchor' => '<h2>The Penafrancia Week</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/naga-camarines-sur-penafrancia-basilica.jpg" alt="Penafrancia Basilica in Naga City" loading="lazy"><figcaption>The Penafrancia Basilica Minore in Naga, the shrine that anchors the Traslacion and the nine days of novena masses every September.</figcaption></figure>',
        ],
        [
            'anchor' => '<h3>Bob Marlin and Geewan</h3>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/naga-camarines-sur-bicol-express.jpg" alt="Bicol Express plate" loading="lazy"><figcaption>Bicol Express, the coconut-and-sili stew that was born in this region and that Bob Marlin serves with the heat fully respected.</figcaption></figure>',
        ],
        [
            'anchor' => '<h3>Naga City Plaza Night Market</h3>',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/naga-camarines-sur-kinalas.jpg" alt="Kinalas noodle soup from Naga" loading="lazy"><figcaption>Kinalas, the thick-gravy beef noodle soup that locals eat at midnight and that defines a Naga food trail.</figcaption></figure>',
        ],
        [
            'anchor' => '<h3>Pasalubong Stops</h3>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/naga-camarines-sur-laing-and-pinangat.jpg" alt="Laing and pinangat from Bicol" loading="lazy"><figcaption>Laing and pinangat, the bottled-and-jarred pasalubong you can carry home from the Naga City Public Market.</figcaption></figure>',
        ],
    ],

    // -- Bulusan Lake: one strong local lake image ----------------------
    'bulusan-lake-sorsogon-slow-morning-volcano-park' => [
        [
            'anchor' => '<h2>What to Do at Bulusan Lake</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/sorsogon-bulusan-volcano-natural-park-lake-bulusan.jpg" alt="Bulusan Lake in Sorsogon" loading="lazy"><figcaption>Bulusan Lake, the 16-hectare emerald lagoon at the foot of an active stratovolcano in southeast Sorsogon.</figcaption></figure>',
        ],
    ],

    // -- Daraga Church: facade + Cagsawa + Lignon -----------------------
    'daraga-church-albay-baroque-hilltop' => [
        [
            'anchor' => '<h2>The Half-Day Plan</h2>',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/albay-legazpi-daraga-church.jpg" alt="Daraga Church baroque facade in Albay" loading="lazy"><figcaption>The carved volcanic-rock facade of Our Lady of the Gate Parish in Daraga, the 1773 baroque church now recognised as a National Cultural Treasure.</figcaption></figure>',
        ],
        [
            'anchor' => '<li>Cagsawa Ruins, 10 minutes by tricycle</li>',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/albay-legazpi-cagsawa-ruins.jpg" alt="Cagsawa Ruins with Mayon Volcano" loading="lazy"><figcaption>Cagsawa Ruins, the natural pair to the Daraga hilltop morning, ten minutes away by tricycle.</figcaption></figure>',
        ],
        [
            'anchor' => '<li>Lignon Hill, 15 minutes by Grab, for the wide Mayon view</li>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/albay-legazpi-lignon-hill.jpg" alt="Lignon Hill panoramic view of Mayon" loading="lazy"><figcaption>Lignon Hill above Legazpi, where the wide-frame Mayon view drops the volcano, the runway, and the Albay Gulf into a single panorama.</figcaption></figure>',
        ],
    ],

    // -- Eden Nature Park: park photo + Davao cool-air shot -------------
    'eden-nature-park-davao-cool-air-day-trip' => [
        [
            'anchor' => '<h2>What to Do at Eden</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/davao-city-eden-nature-park.jpg" alt="Eden Nature Park on Mt. Talomo" loading="lazy"><figcaption>Eden Nature Park on the slopes of Mt. Talomo, the 198-hectare mountain reserve that swaps Davao city heat for pine-cover and herb gardens.</figcaption></figure>',
        ],
    ],

    // -- Samal Island DIY: Hagimit + bat cave + pearl farm --------------
    'samal-island-diy-pearl-farm-hagimit-falls' => [
        [
            'anchor' => '<h3>Vanishing Island and Monfort Bat Cave</h3>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/samal-island-monfort-bat-sanctuary.jpg" alt="Monfort Bat Sanctuary on Samal Island" loading="lazy"><figcaption>The Monfort Bat Sanctuary near Tambo, home to more than 2 million Geoffroy rousette fruit bats roosting across five chambers.</figcaption></figure>',
        ],
        [
            'anchor' => '<h2>Day 2: Island Hopping and the Pearl Farm View</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/samal-island-pearl-farm-beach-resort.jpg" alt="Pearl Farm Beach Resort on Samal Island" loading="lazy"><figcaption>Pearl Farm on Samal Island, the destination the island-hopping boats circle for photos when day-trippers are not booked in.</figcaption></figure>',
        ],
    ],

    // -- GenSan tuna market: 2 local tuna food shots --------------------
    'general-santos-tuna-market-5am-trip' => [
        [
            'anchor' => '<h3>What You Will See</h3>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/general-santos-tuna-panga.jpg" alt="Tuna panga grilled in General Santos" loading="lazy"><figcaption>Tuna panga, the grilled jaw cut that General Santos lays out at the city eateries after the morning auction is done.</figcaption></figure>',
        ],
        [
            'anchor' => '<h2>Pair It With Tiongson Arcade</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/general-santos-tuna-kinilaw.jpg" alt="Tuna kinilaw from General Santos" loading="lazy"><figcaption>Tuna kinilaw at Tiongson Arcade, the GenSan ceviche-style cure cut from the same yellowfin that lands at the port at dawn.</figcaption></figure>',
        ],
    ],

    // -- Glan/Gumasa Beach: 2 local images ------------------------------
    'glan-gumasa-beach-sarangani-quiet-coast' => [
        [
            'anchor' => '<h2>What to Do at Gumasa</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/glan-sarangani-gumasa-beach.jpg" alt="Gumasa Beach in Glan, Sarangani" loading="lazy"><figcaption>Gumasa Beach in Glan, the kilometer-long powdery white-sand stretch on the lee side of Sarangani Bay.</figcaption></figure>',
        ],
        [
            'anchor' => '<h3>Day Trips Around Glan</h3>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/glan-sarangani-sinugbang-isda.jpg" alt="Sinugbang isda grilled fish in Sarangani" loading="lazy"><figcaption>Sinugbang isda at the Glan public market carinderia row, grilled fresh from the morning tuna landing across the wharf.</figcaption></figure>',
        ],
    ],

    // -- Camiguin loop: Wikimedia White Island --------------------------
    'camiguin-diy-white-island-sunken-cemetery-loop' => [
        [
            'anchor' => '<h3>White Island</h3>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c4/The_White_Island_of_Camiguin.jpg/800px-The_White_Island_of_Camiguin.jpg" alt="White Island sandbar off Camiguin" loading="lazy"><figcaption>White Island, the unshaded C-shaped sandbar off Mambajao that surfaces with Hibok-Hibok Volcano framed behind it. Photo via <a href="https://commons.wikimedia.org/wiki/File:The_White_Island_of_Camiguin.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // -- Siquijor folk healing + day loop ------------------------------
    'siquijor-folk-healing-tour-san-antonio' => [
        [
            'anchor' => '<h2>The Day Loop Around the Island</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/siquijor-cambugahay-falls.jpg" alt="Cambugahay Falls on Siquijor" loading="lazy"><figcaption>Cambugahay Falls, the first easy stop on the Siquijor day loop and a cool counterpoint to the morning at San Antonio.</figcaption></figure>',
        ],
        [
            'anchor' => '<h3>Bandilaan Forest Reserve</h3>',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/siquijor-the-old-enchanted-balete-tree.jpg" alt="Old Enchanted Balete Tree in Siquijor" loading="lazy"><figcaption>The Old Enchanted Balete Tree along the coastal loop, where the spring-fed fish pool sits at the base of a centuries-old balete.</figcaption></figure>',
        ],
        [
            'anchor' => '<h3>Bandilaan Forest Reserve</h3>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/siquijor-lazi-convent-1857.jpg" alt="Lazi Convent of 1857" loading="lazy"><figcaption>The 1857 Lazi Convent, one of the largest in Asia and the heritage closer to the southern half of the Siquijor loop.</figcaption></figure>',
        ],
    ],

    // -- Sta. Cruz Island Zamboanga: 2 local images --------------------
    'sta-cruz-island-zamboanga-pink-beach-day-trip' => [
        [
            'anchor' => '<h2>What to Do on the Island</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/zamboanga-city-pink-beach-great-santa-cruz-island.jpg" alt="Pink beach of Great Santa Cruz Island" loading="lazy"><figcaption>The coralline pink-sand shore of Great Santa Cruz Island, tinted by crushed red organ-pipe coral mixed through the white sand.</figcaption></figure>',
        ],
        [
            'anchor' => '<h2>Pair It With the City</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/zamboanga-city-fort-pilar-shrine.jpg" alt="Fort Pilar in Zamboanga City" loading="lazy"><figcaption>Fort Pilar on the Zamboanga seafront, the 17th-century Spanish fortress that pairs naturally with the morning at the pink beach.</figcaption></figure>',
        ],
    ],

    // -- Lake Agco Kidapawan: Mt. Apo natural park ---------------------
    'lake-agco-kidapawan-boiling-lake-foot-of-apo' => [
        [
            'anchor' => '<h2>The Mt. Apo Trek</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/kidapawan-mt-apo-natural-park.jpg" alt="Mt. Apo Natural Park from Kidapawan" loading="lazy"><figcaption>Mt. Apo Natural Park above Kidapawan, the country\'s tallest peak that frames the slopes where Lake Agco bubbles at the foot of the volcano.</figcaption></figure>',
        ],
    ],

];
