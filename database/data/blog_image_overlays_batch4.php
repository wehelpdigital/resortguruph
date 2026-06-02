<?php

/**
 * Bespoke image overlays for batch 4 (and adjacent batch 3) Visayas + Mindanao
 * blog posts. Each entry injects a <figure class="rg-figure"> block at a unique
 * anchor substring in the source content_html. Anchors verified to appear
 * exactly once in the source files at write time.
 *
 * Image priority: local /storage/rg-media/ assets first; Wikimedia Commons 800px
 * thumbs as fallback with caption attribution. Skipped posts: Bantayan,
 * Mactan island hopping, Anda, Silay (insufficient strong photo coverage that
 * adds meaningful detail beyond the prose).
 */

return [

    // === Iligan waterfalls triangle (batch3) ===
    'iligan-city-waterfalls-tourism-triangle-loop' => [
        [
            'anchor' => 'Maria Cristina is the most famous of the three',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/0/05/Maria_Cristina_Falls.jpg/800px-Maria_Cristina_Falls.jpg" alt="Maria Cristina Falls in Iligan City at full release" loading="lazy"><figcaption>Maria Cristina Falls, Iligan, at full gate release on the Agus River. Photo via <a href="https://commons.wikimedia.org/wiki/File:Maria_Cristina_Falls.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'Tinago means hidden, and the name is earned',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/7/72/Tinago_falls_in_Iligan_City.jpg/800px-Tinago_falls_in_Iligan_City.jpg" alt="Tinago Falls and its emerald pool in Iligan" loading="lazy"><figcaption>Tinago Falls, around 240 feet into an emerald pool. The descent is roughly 200 carved stone steps. Photo via <a href="https://commons.wikimedia.org/wiki/File:Tinago_falls_in_Iligan_City.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'Mimbalut is the smaller and easier of the three',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/4b/Mimbalut_Falls_Iligan_City_03.JPG/800px-Mimbalut_Falls_Iligan_City_03.JPG" alt="Mimbalut Falls multi-tier cascade in Iligan" loading="lazy"><figcaption>Mimbalut Falls, the easy closer of the day with a multi-tier cascade and shallow swim pool. Photo via <a href="https://commons.wikimedia.org/wiki/File:Mimbalut_Falls_Iligan_City_03.JPG" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // === Cagayan de Oro white-water rafting (batch3) ===
    'cagayan-de-oro-white-water-rafting-first-timer' => [
        [
            'anchor' => 'Beginner covers 14 rapids over a 12-kilometer river run',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/a6/Cagayan_river_%28cdo%29_white_water_rafting.JPG/800px-Cagayan_river_%28cdo%29_white_water_rafting.JPG" alt="Whitewater rafting on the Cagayan de Oro River" loading="lazy"><figcaption>The beginner course runs class 2 to class 3 rapids on the Cagayan de Oro River. Safety crews shadow each boat in kayaks. Photo via <a href="https://commons.wikimedia.org/wiki/File:Cagayan_river_(cdo)_white_water_rafting.JPG" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'The Mapawa Nature Park up in the hills',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/60/CDOkayak-raft.JPG/800px-CDOkayak-raft.JPG" alt="Kayak and raft on the Cagayan de Oro river" loading="lazy"><figcaption>Kayak-raft pairing on the CDO river run, a sequence of rapids broken by calm stretches. Photo via <a href="https://commons.wikimedia.org/wiki/File:CDOkayak-raft.JPG" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // === Kawasan canyoneering ===
    'kawasan-canyoneering-calm-diy-south-cebu' => [
        [
            'anchor' => 'You exit at the second tier of Kawasan',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/8b/Kawasan_Falls_Cebu.jpg/800px-Kawasan_Falls_Cebu.jpg" alt="Kawasan Falls turquoise pool in Badian Cebu" loading="lazy"><figcaption>Kawasan Falls in Badian, the famous turquoise pool where the canyoneering trail finishes. Photo via <a href="https://commons.wikimedia.org/wiki/File:Kawasan_Falls_Cebu.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'There are around six jumps along the route',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c2/Badian_Kawasan_Falls_Cebu.jpg/800px-Badian_Kawasan_Falls_Cebu.jpg" alt="Kawasan Falls watershed area in Badian Cebu" loading="lazy"><figcaption>The Kawasan watershed area in Badian. The canyon upstream alternates cold swims, short walks, and small rappels. Photo via <a href="https://commons.wikimedia.org/wiki/File:Badian_Kawasan_Falls_Cebu.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // === Malapascua thresher sharks ===
    'malapascua-thresher-sharks-diving-plan' => [
        [
            'anchor' => 'Monad Shoal sits about 30 minutes east of Malapascua',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/ee/Malapascua_Island%2C_Cebu.jpg/800px-Malapascua_Island%2C_Cebu.jpg" alt="Bounty Beach view on Malapascua Island Cebu" loading="lazy"><figcaption>Malapascua Island, the small northern Cebu base for the Monad Shoal dawn dive. Photo via <a href="https://commons.wikimedia.org/wiki/File:Malapascua_Island,_Cebu.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'Gato Island has a swim-through tunnel',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/42/Gato_islet_seen_from_Tepanee_Beach_Resort%2C_Malapascua.jpg/800px-Gato_islet_seen_from_Tepanee_Beach_Resort%2C_Malapascua.jpg" alt="Gato Islet seen from Malapascua" loading="lazy"><figcaption>Gato Island as seen from Malapascua, the dive site with the swim-through tunnel and resident white-tip sharks. Photo via <a href="https://commons.wikimedia.org/wiki/File:Gato_islet_seen_from_Tepanee_Beach_Resort,_Malapascua.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // === Moalboal sardine run ===
    'moalboal-sardine-run-slow-snorkel-panagsama' => [
        [
            'anchor' => 'Panagsama Beach is not a swimming beach in the usual sense',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/b/b0/Moalboal_sardine_run_%26_sea_turtle_05.jpg/800px-Moalboal_sardine_run_%26_sea_turtle_05.jpg" alt="Sardine school and sea turtle at Moalboal" loading="lazy"><figcaption>The Moalboal sardine school with a sea turtle just off Panagsama. The school lives 20 to 30 meters offshore at 5 to 10 meters depth. Photo via <a href="https://commons.wikimedia.org/wiki/File:Moalboal_sardine_run_%26_sea_turtle_05.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'The school is dense enough that the water darkens',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/Moalboal_sardine_run_%26_sea_turtle_10.jpg/800px-Moalboal_sardine_run_%26_sea_turtle_10.jpg" alt="Dense sardine school at Panagsama Moalboal" loading="lazy"><figcaption>Visibility holds best from November to May at Panagsama, when the school swirls tightest. Photo via <a href="https://commons.wikimedia.org/wiki/File:Moalboal_sardine_run_%26_sea_turtle_10.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // === Bohol countryside tour ===
    'bohol-countryside-tour-chocolate-hills-loboc' => [
        [
            'anchor' => 'The Loboc River cruise is usually the lunch stop',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/panglao-loboc-river-cruise.jpg" alt="Loboc River cruise pontoon in Bohol" loading="lazy"><figcaption>The flat-bottomed pontoon boats on the Loboc River, where the buffet lunch and the cultural stop happen.</figcaption></figure>',
        ],
        [
            'anchor' => 'The Philippine Tarsier Conservation Area in Corella',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/panglao-tarsier-sanctuary.jpg" alt="Philippine tarsier at the Corella sanctuary in Bohol" loading="lazy"><figcaption>A Philippine tarsier at the Corella conservation area. The roadside cages on the highway are not the right stop, this one is.</figcaption></figure>',
        ],
        [
            'anchor' => 'The classic viewpoint sits on top of one of the larger hills in Carmen',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/e6/The_Bohol_Chocolate_Hills.jpg/800px-The_Bohol_Chocolate_Hills.jpg" alt="Chocolate Hills viewpoint in Carmen Bohol" loading="lazy"><figcaption>Chocolate Hills viewpoint in Carmen, Bohol. The hills go brown from February to May during the dry season. Photo via <a href="https://commons.wikimedia.org/wiki/File:The_Bohol_Chocolate_Hills.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // === Panglao Alona vs Doljo ===
    'panglao-alona-vs-doljo-choose-base' => [
        [
            'anchor' => 'Alona is the most developed beach on Panglao',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/panglao-alona-beach.jpg" alt="Alona Beach Panglao with dive shops along the strip" loading="lazy"><figcaption>Alona Beach, Panglao, the main strip where most dive shops, restaurants, and island-hopping kiosks cluster.</figcaption></figure>',
        ],
        [
            'anchor' => 'Doljo is on the northwestern tip of Panglao',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/panglao-1.jpg" alt="Quiet Panglao shoreline near Doljo" loading="lazy"><figcaption>The quieter Panglao shoreline, the kind of long sand flat you find on the Doljo side.</figcaption></figure>',
        ],
    ],

    // === Dumaguete + Apo Island ===
    'dumaguete-apo-island-three-day-plan' => [
        [
            'anchor' => 'The boulevard is a 750-meter promenade',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/dumaguete-rizal-boulevard.jpg" alt="Rizal Boulevard promenade in Dumaguete" loading="lazy"><figcaption>Rizal Boulevard, Dumaguete. Acacia trees on one side, the sea on the other, tempura stalls in between.</figcaption></figure>',
        ],
        [
            'anchor' => 'The Silliman University campus is around 10 minutes on foot',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/dumaguete-silliman-university.jpg" alt="Silliman Hall on the Silliman University campus" loading="lazy"><figcaption>Silliman Hall on the Silliman University campus, a heritage stop on the walk from the boulevard.</figcaption></figure>',
        ],
        [
            'anchor' => 'Apo Island is one of the oldest marine sanctuaries in the country',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/dumaguete-apo-island-marine-sanctuary.jpg" alt="Apo Island marine sanctuary off Dauin" loading="lazy"><figcaption>Apo Island marine sanctuary, where green sea turtles still graze the protected south-side reef in chest-deep water.</figcaption></figure>',
        ],
    ],

    // === Dauin macro diving ===
    'dauin-macro-diving-black-sand-slope' => [
        [
            'anchor' => 'Hairy frogfish, painted frogfish, ornate ghost pipefish',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/dauin-1.jpg" alt="Dauin black sand shore in Negros Oriental" loading="lazy"><figcaption>The Dauin coastal road and its volcanic black-sand frontage. The macro slope starts within meters of the beach.</figcaption></figure>',
        ],
        [
            'anchor' => 'Atmosphere House Reef, Cars Dive Site',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/dauin-apo-island-marine-sanctuary.jpg" alt="Apo Island reef off Dauin Negros Oriental" loading="lazy"><figcaption>Apo Island, the reef-dive add-on most Dauin shops boat to for a deeper-wall day between muck dives.</figcaption></figure>',
        ],
    ],

    // === La Paz batchoy trail ===
    'la-paz-batchoy-trail-three-bowls-iloilo' => [
        [
            'anchor' => 'Netong\'s is the one most locals send first-timers to',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/iloilo-city-la-paz-batchoy.jpg" alt="A bowl of La Paz batchoy with chicharon and egg" loading="lazy"><figcaption>A bowl of La Paz batchoy with the soft-boiled egg and the generous crushed chicharon on top, the way Netong\'s plates it.</figcaption></figure>',
        ],
        [
            'anchor' => 'Deco\'s has the longest claim to the dish',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/iloilo-city-pancit-molo.jpg" alt="A bowl of pancit Molo from Iloilo" loading="lazy"><figcaption>If you still have room after the three batchoy stops, pancit Molo at a nearby stall closes the morning right.</figcaption></figure>',
        ],
    ],

    // === Molo + Calle Real heritage walk ===
    'molo-church-calle-real-iloilo-heritage-walk' => [
        [
            'anchor' => 'Calle Real is the colonial-era name for the downtown stretch',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/iloilo-city-calle-real.jpg" alt="Calle Real heritage row on J.M. Basa Street in Iloilo" loading="lazy"><figcaption>The Calle Real heritage row along J.M. Basa Street, built during the late 1800s sugar boom and still working as cafes and shops today.</figcaption></figure>',
        ],
        [
            'anchor' => 'From Calle Real, ride a jeepney or Grab to Jaro',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/iloilo-city-jaro-cathedral.jpg" alt="Jaro Cathedral and its detached bell tower" loading="lazy"><figcaption>Jaro Cathedral and the bell tower across the plaza, which sits as a separate structure, an unusual setup for a Philippine church.</figcaption></figure>',
        ],
        [
            'anchor' => 'The St. Anne Parish Church, locally known as Molo Church',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/iloilo-city-molo-church-and-plaza.jpg" alt="Molo Church with its twin Gothic spires" loading="lazy"><figcaption>Molo Church, the Gothic-Renaissance St. Anne Parish, locally called the feminist church for the row of female-saint statues inside.</figcaption></figure>',
        ],
    ],

    // === Guimaras day trip ===
    'guimaras-day-trip-mango-farm-alubihod' => [
        [
            'anchor' => 'The Guisi Lighthouse on the southern tip of the island',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/guimaras-guisi-lighthouse.jpg" alt="Guisi Lighthouse Spanish-era ruin in Guimaras" loading="lazy"><figcaption>The Guisi Lighthouse, the Spanish-era ruin on the southern tip of Guimaras with a quiet beach below.</figcaption></figure>',
        ],
        [
            'anchor' => 'The Trappist Monastery of Our Lady of the Philippines',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/guimaras-trappist-monastery.jpg" alt="Trappist Monastery of Our Lady of the Philippines in Guimaras" loading="lazy"><figcaption>The Trappist Monastery in Guimaras. The chapel is open to visitors and the gift shop sells mango bars, honey, and herbal soaps made by the monks.</figcaption></figure>',
        ],
        [
            'anchor' => 'End the loop at Alubihod Beach',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/guimaras-mango-everything.jpg" alt="Guimaras mangoes the sweet variety the island is known for" loading="lazy"><figcaption>The Guimaras mango, the headline pasalubong. Peak season is March to May. Outside the season you still get the mango bars and dried mango.</figcaption></figure>',
        ],
    ],

    // === Bacolod MassKara ===
    'bacolod-masskara-calm-october-plan' => [
        [
            'anchor' => 'Eat chicken inasal at Manokan Country',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/bacolod-chicken-inasal.jpg" alt="A plate of Bacolod chicken inasal" loading="lazy"><figcaption>Bacolod chicken inasal at Manokan Country, the row of grilled-chicken stalls in the city center where festival queues run long but move fast.</figcaption></figure>',
        ],
        [
            'anchor' => 'Sunday night is the Electric MassKara',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/bacolod-masskara-festival-every-october.jpg" alt="MassKara Festival dancers in Bacolod every October" loading="lazy"><figcaption>MassKara dancers on the parade route. The Electric MassKara on Sunday night swaps the daytime mardi-gras energy for LED-lit masks and a street-party beat.</figcaption></figure>',
        ],
        [
            'anchor' => 'The Ruins in Talisay, around 30 minutes from Bacolod',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/bacolod-the-ruins-talisay.jpg" alt="The Ruins in Talisay near Bacolod at sunset" loading="lazy"><figcaption>The Ruins in Talisay, the burnt-out shell of a 1920s sugar-baron mansion. The Italianate facade glows at sunset.</figcaption></figure>',
        ],
    ],

    // === Don Salvador Benedicto ===
    'don-salvador-benedicto-cool-mountain-negros' => [
        [
            'anchor' => 'Lantawan is the main viewing deck',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/3/30/Don_Salvador_Benedicto_mountain_range.jpg/800px-Don_Salvador_Benedicto_mountain_range.jpg" alt="Don Salvador Benedicto mountain range view in Negros Occidental" loading="lazy"><figcaption>The Don Salvador Benedicto mountain range, around 800 meters elevation, the upland town locals call the little Baguio of the south. Photo via <a href="https://commons.wikimedia.org/wiki/File:Don_Salvador_Benedicto_mountain_range.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'Malatan-og Falls, sometimes called Cigarette Falls',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/b/ba/Don_Salvador_Benedicto%27s_Hilly_Terrain.jpg/800px-Don_Salvador_Benedicto%27s_Hilly_Terrain.jpg" alt="Don Salvador Benedicto hilly terrain in Negros Occidental" loading="lazy"><figcaption>The DSB hilly terrain on the drive up from Bacolod. Mountain rains arrive without warning, pack a small umbrella. Photo via <a href="https://commons.wikimedia.org/wiki/File:Don_Salvador_Benedicto%27s_Hilly_Terrain.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // === Boracay quiet side: Puka + Diniwid ===
    'boracay-quiet-side-puka-diniwid' => [
        [
            'anchor' => 'Diniwid is the small beach just north of White Beach Station 1',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/0/06/Sunset_at_Diniwid_Beach%2C_Boracay_Island.jpg/800px-Sunset_at_Diniwid_Beach%2C_Boracay_Island.jpg" alt="Sunset at Diniwid Beach in Boracay" loading="lazy"><figcaption>Sunset at Diniwid Beach. The cliff at the south end frames the western horizon, one of the best sunset views on the island. Photo via <a href="https://commons.wikimedia.org/wiki/File:Sunset_at_Diniwid_Beach,_Boracay_Island.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'Puka Beach is on the northern coast of Boracay',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/boracay-puka-beach-north.jpg" alt="Puka Beach wide shoreline on Boracay north coast" loading="lazy"><figcaption>Puka Beach on the northern coast of Boracay, around 800 meters long, with coarser sand than White Beach and far less development.</figcaption></figure>',
        ],
    ],

    // === Boracay + Ati-Atihan Kalibo combo ===
    'boracay-ati-atihan-kalibo-january-combo' => [
        [
            'anchor' => 'The street dance competitions, the float parades, and the religious procession',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/97/Santo_Ni%C3%B1o_Ati-Atihan_Festival_Kalibo_Aklan_2024.jpg/800px-Santo_Ni%C3%B1o_Ati-Atihan_Festival_Kalibo_Aklan_2024.jpg" alt="Santo Nino Ati-Atihan Festival in Kalibo Aklan" loading="lazy"><figcaption>The Santo Nino Ati-Atihan in Kalibo, the original mother festival from which Sinulog and Dinagyang both took inspiration. Photo via <a href="https://commons.wikimedia.org/wiki/File:Santo_Ni%C3%B1o_Ati-Atihan_Festival_Kalibo_Aklan_2024.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'Ride a van from Kalibo to Caticlan',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/boracay-white-beach-stations-1-2-3.jpg" alt="White Beach Boracay Station 1 area" loading="lazy"><figcaption>White Beach, Boracay. After three days of Ati-Atihan drumming, this is the right kind of decompression.</figcaption></figure>',
        ],
    ],

    // === Siquijor scooter loop ===
    'siquijor-scooter-loop-diy-weekend' => [
        [
            'anchor' => 'Cambugahay Falls in Lazi is the headline waterfall',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/siquijor-cambugahay-falls.jpg" alt="Cambugahay Falls in Lazi Siquijor" loading="lazy"><figcaption>Cambugahay Falls in Lazi, the multi-tier headline waterfall with rope swings. Arrive by 8 a.m. to beat the tour vans.</figcaption></figure>',
        ],
        [
            'anchor' => 'The 19th-century San Isidro Labrador Church and Convent',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/siquijor-lazi-convent-1857.jpg" alt="San Isidro Labrador Convent in Lazi Siquijor 1857" loading="lazy"><figcaption>The San Isidro Labrador Convent in Lazi, built in 1857 and considered the largest convent in the Philippines.</figcaption></figure>',
        ],
        [
            'anchor' => 'The 400-year-old balete tree in Lazi',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/siquijor-the-old-enchanted-balete-tree.jpg" alt="Old Enchanted Balete Tree in Siquijor" loading="lazy"><figcaption>The 400-year-old balete tree in Lazi with the small spring at its base. The little fish at the spring give the free foot-spa treatment everyone retells.</figcaption></figure>',
        ],
    ],

];
