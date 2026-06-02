<?php

/**
 * Per-post figure overlays for the first 22 enhanced blog posts.
 *
 * Each entry keys on the blog slug and lists figure inserts that the
 * BlogContentSeeder injects at named anchors inside content_html (before the
 * BlogContentEnhancer runs and before the "Where to stay" section is appended).
 *
 * Anchors are exact substrings that MUST appear once in the raw content_html.
 * Position is 'before' or 'after' the anchor.
 *
 * Images are sourced from /storage/rg-media/ (seeded local library) so each
 * post stays self-hostable and no external image attribution is needed.
 */

return [

    // 1. Cebu four-day plan -------------------------------------------------
    'cebu-four-days-diy-plan-first-timers' => [
        [
            'anchor' => '<h2>Day 1 and 2: City, Heritage, and Mountain Air</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/cebu-city-magellans-cross-and-basilica-del-santo-nino.jpg" alt="Magellan\'s Cross kiosk beside Basilica del Santo Nino in downtown Cebu" loading="lazy"><figcaption>Magellan\'s Cross sits a few steps from the Basilica del Santo Nino, the first stop on the downtown heritage loop.</figcaption></figure>',
        ],
        [
            'anchor' => '<h3>Day 2: Up to the Hills</h3>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/cebu-city-tops-lookout.jpg" alt="Tops Lookout in Busay overlooking Cebu City at dusk" loading="lazy"><figcaption>Tops Lookout in Busay, the usual sunset stop on the Transcentral Highway loop above Cebu City.</figcaption></figure>',
        ],
        [
            'anchor' => 'Drop your bags, eat a quick lechon lunch at Zubuchon or House of Lechon, then start the heritage loop on foot.',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/cebu-city-lechon.jpg" alt="Whole Cebu lechon roasted to crisp golden skin" loading="lazy"><figcaption>Cebu lechon at one of the downtown lechoneras like Zubuchon, the day-one lunch most travelers land straight into.</figcaption></figure>',
        ],
        [
            'anchor' => '<h2>Day 4: Island Day, Then Fly</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/mactan-lapu-lapu-shrine-and-mactan-shrine.jpg" alt="Lapu-Lapu Shrine and Mactan Shrine on Mactan Island" loading="lazy"><figcaption>The Lapu-Lapu Shrine on Mactan, the heritage stop you can squeeze in before the morning island-hop to Nalusuan and Hilutungan.</figcaption></figure>',
        ],
    ],

    // 4. Bohol island hopping ----------------------------------------------
    'bohol-island-hopping-pamilacan-balicasag-virgin' => [
        [
            'anchor' => '<h3>Pair It With the Countryside Tour</h3>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/panglao-tarsier-sanctuary.jpg" alt="Philippine tarsier clinging to a branch at the Corella sanctuary in Bohol" loading="lazy"><figcaption>The Philippine tarsier at the Corella conservation area, one of the calmer stops on the Bohol countryside loop.</figcaption></figure>',
        ],
        [
            'anchor' => 'The Chocolate Hills, the Bilar man-made forest, the Loboc River cruise, and the tarsier conservation area in Corella all fit in one long countryside loop.',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/panglao-loboc-river-cruise.jpg" alt="Floating restaurant on the Loboc River in Bohol" loading="lazy"><figcaption>The floating-restaurant cruise on the Loboc River, the lunch stop most countryside tours build around.</figcaption></figure>',
        ],
    ],

    // 5. Pampanga food trail -----------------------------------------------
    'pampanga-food-trail-actually-tastes-local' => [
        [
            'anchor' => '<h3>Aling Lucing</h3>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/angeles-sisig.jpg" alt="Sizzling pork sisig on a hot iron plate from an Angeles lechonera" loading="lazy"><figcaption>Sisig on the sizzling iron plate, served the original Angeles way with chopped pork, calamansi, sili, and onions.</figcaption></figure>',
        ],
        [
            'anchor' => '<h3>Everybody\'s Cafe</h3>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/pampanga-province-duman.jpg" alt="Duman green pinipig rice dessert from Pampanga" loading="lazy"><figcaption>Duman, the toasted green pinipig dessert that turns up at Everybody\'s Cafe when the harvest season is on.</figcaption></figure>',
        ],
        [
            'anchor' => '<h2>What to Order, in Order</h2>',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/pampanga-province-tocino.jpg" alt="Sweet Kapampangan tocino plated with rice and egg" loading="lazy"><figcaption>Kapampangan tocino, the sweet cured pork that pairs with garlic rice for the breakfast end of the food trail.</figcaption></figure>',
        ],
    ],

    // 7. Tagaytay weekend escape -------------------------------------------
    'tagaytay-weekend-escape-diy-plan-from-manila' => [
        [
            'anchor' => '<h2>Day 1: Ridge, Bulalo, Sunset</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/tagaytay-peoples-park-in-the-sky.jpg" alt="People\'s Park in the Sky viewpoint above Tagaytay ridge with Taal in the distance" loading="lazy"><figcaption>People\'s Park in the Sky, the highest ridge viewpoint and the easiest panoramic stop on the Tagaytay loop.</figcaption></figure>',
        ],
        [
            'anchor' => '<h3>Lunch Has To Be Bulalo</h3>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/tagaytay-mahogany-market.jpg" alt="Carinderia row at Mahogany Market in Tagaytay where bulalo is served" loading="lazy"><figcaption>The bulalo row at Mahogany Market, where the local carinderias serve the bone-marrow soup straight from the cauldron.</figcaption></figure>',
        ],
        [
            'anchor' => '<h3>Afternoon Slow Down</h3>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/tagaytay-sky-ranch.jpg" alt="Sky Ranch ferris wheel on the Tagaytay ridge" loading="lazy"><figcaption>Sky Ranch on the ridge, the family-friendly afternoon stop if you are not making the drive to Sonya\'s Garden.</figcaption></figure>',
        ],
    ],

    // 8. Anilao diving for beginners ---------------------------------------
    'anilao-diving-for-beginners-mabini-weekend' => [
        [
            'anchor' => '<h2>Why Anilao Works for First Timers</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/anilao-mabini-anilao-dive-sites-cathedral-rock-mainit-point-twin.jpg" alt="Anilao reef wall with corals at one of the training dive sites" loading="lazy"><figcaption>One of the Anilao training reefs around Cathedral, Mainit Point, and Twin Rocks, all 10 to 20 meters deep.</figcaption></figure>',
        ],
        [
            'anchor' => '<h2>Beyond the Dive</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/anilao-mabini-sombrero-island.jpg" alt="Sombrero Island off the Mabini coast in Batangas" loading="lazy"><figcaption>Sombrero Island in the Maricaban Strait, the landmark you see on the Gulugod Baboy sunrise climb.</figcaption></figure>',
        ],
        [
            'anchor' => '<h3>Eat Like the Boat Crew</h3>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/anilao-mabini-sinaing-na-tulingan.jpg" alt="Sinaing na tulingan tuna braised in pork fat and dried bilimbi" loading="lazy"><figcaption>Sinaing na tulingan, the long-braised Batangas tuna that turns up at the dive-resort dinner table.</figcaption></figure>',
        ],
    ],

    // 9. Calatagan + Cape Santiago Lighthouse ------------------------------
    'calatagan-beach-cape-santiago-lighthouse' => [
        [
            'anchor' => '<h2>The Lighthouse First</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/calatagan-cape-santiago-lighthouse.jpg" alt="Cape Santiago Lighthouse brick tower in Calatagan, Batangas" loading="lazy"><figcaption>The 51-foot Cape Santiago Lighthouse, built in 1890 and still standing watch over the Verde Island Passage.</figcaption></figure>',
        ],
        [
            'anchor' => '<h2>Burot Beach for the Swim</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/calatagan-2.jpg" alt="Fine white sand and shallow water at a Calatagan beach in Batangas" loading="lazy"><figcaption>The Calatagan coast near Burot in Barangay Bucal, fine white sand with shallow water for the afternoon swim.</figcaption></figure>',
        ],
        [
            'anchor' => '<h2>Fresh Seafood at Wawa Wet Market</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/calatagan-sinaing-na-tulingan.jpg" alt="Sinaing na tulingan Batangas-style with rice" loading="lazy"><figcaption>Sinaing na tulingan, the slow-braised Batangas tuna that pairs with the fresh tahong and talaba from the Wawa market.</figcaption></figure>',
        ],
    ],

    // 10. Laiya San Juan beach guide ---------------------------------------
    'laiya-san-juan-beach-guide-slow-weekend' => [
        [
            'anchor' => '<h2>The Beach</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/laiya-laiya-white-beach.jpg" alt="White sand stretch at Laiya beach in San Juan, Batangas" loading="lazy"><figcaption>The Hugom stretch of Laiya White Beach in San Juan, the long fine-sand strip with the small offshore islands that break the swell.</figcaption></figure>',
        ],
        [
            'anchor' => '<h3>What the Day Looks Like</h3>',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/laiya-kinilaw-na-tuna.jpg" alt="Kinilaw na tuna cured in coconut vinegar from Laiya, Batangas" loading="lazy"><figcaption>Kinilaw na tuna, the fresh-cured Batangas snack that the wet-market run reliably turns into lunch.</figcaption></figure>',
        ],
    ],

    // 11. Fortune Island Nasugbu -------------------------------------------
    'fortune-island-nasugbu-day-trip-from-manila' => [
        [
            'anchor' => '<h3>The Cove and the Shipwreck</h3>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/nasugbu-munting-buhangin-beach-camp.jpg" alt="White sand cove with turquoise water on the Nasugbu coast" loading="lazy"><figcaption>A Nasugbu cove with the same turquoise water and fine white sand you find on Fortune Island\'s eastern point.</figcaption></figure>',
        ],
        [
            'anchor' => '<h2>Getting to Nasugbu</h2>',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/nasugbu-1.jpg" alt="Nasugbu coastline in Batangas where boats depart for Fortune Island" loading="lazy"><figcaption>The Nasugbu coastline, the mainland jump-off where the outrigger boats to Fortune Island leave from Barangay Wawa.</figcaption></figure>',
        ],
    ],

    // 12. Pico de Loro -----------------------------------------------------
    'pico-de-loro-day-hike-first-mountain-from-manila' => [
        [
            'anchor' => '<h3>The Summit and the Monolith</h3>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/nasugbu-mt-pico-de-loro-parrots-beak.jpg" alt="Mount Pico de Loro summit and the parrot\'s-beak monolith above Nasugbu Bay" loading="lazy"><figcaption>The Pico de Loro monolith, the parrot\'s-beak rock formation that gives the mountain its name. Climbing it is no longer allowed.</figcaption></figure>',
        ],
    ],

    // 13. Antipolo cafe + art route ----------------------------------------
    'antipolo-cafe-art-route-slow-sunday' => [
        [
            'anchor' => '<h2>Pinto Art Museum</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/antipolo-pinto-art-museum.jpg" alt="Pinto Art Museum gallery building and garden on the Antipolo ridge" loading="lazy"><figcaption>Pinto Art Museum on the Antipolo ridge, a 1.5-hectare complex of Mediterranean-style galleries and stone walkways.</figcaption></figure>',
        ],
        [
            'anchor' => '<h2>The Antipolo Cathedral and Hinulugang Taktak</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/antipolo-antipolo-cathedral.jpg" alt="Antipolo Cathedral facade housing the Our Lady of Peace and Good Voyage image" loading="lazy"><figcaption>The Antipolo Cathedral, a Marian pilgrimage site since the 1600s and the heritage anchor of the loop.</figcaption></figure>',
        ],
        [
            'anchor' => 'Hinulugang Taktak, the small waterfall in the city proper that is being restored as an eco-park.',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/antipolo-hinulugang-taktak-falls.jpg" alt="Hinulugang Taktak waterfall in the city of Antipolo" loading="lazy"><figcaption>Hinulugang Taktak, the modest urban waterfall now under restoration as an Antipolo eco-park.</figcaption></figure>',
        ],
    ],

    // 14. Tanay Rizal falls ------------------------------------------------
    'tanay-rizal-falls-day-trip-daranak-batlag' => [
        [
            'anchor' => '<h2>At Daranak</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/tanay-daranak-falls.jpg" alt="Daranak Falls 14-meter cascade and natural swimming basin in Tanay, Rizal" loading="lazy"><figcaption>Daranak Falls, the 14-meter cascade in Tanay that drops into a wide natural basin with rope-marked swim zones.</figcaption></figure>',
        ],
    ],

    // 15. Marikina riverbank + Shoe Museum ---------------------------------
    'marikina-riverbank-shoe-museum-city-walk' => [
        [
            'anchor' => '<h2>Across the Street: The Old Church</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/marikina-our-lady-of-the-abandoned-parish-church.jpg" alt="Our Lady of the Abandoned Parish Church in Marikina" loading="lazy"><figcaption>Our Lady of the Abandoned Parish in the Marikina Poblacion, built in 1572 and standing through three wars.</figcaption></figure>',
        ],
        [
            'anchor' => '<h2>Riverbank Walk</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/marikina-marikina-riverbanks.jpg" alt="Marikina Riverbanks pathway and pedestrian park beside the Marikina River" loading="lazy"><figcaption>The Marikina Riverbanks pathway, the long pedestrian park that hugs the river through the city.</figcaption></figure>',
        ],
        [
            'anchor' => '<h2>Lunch in Marikina</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/marikina-goto-and-lugaw.jpg" alt="Bowl of goto Filipino rice porridge with crisp toppings from a Marikina carinderia" loading="lazy"><figcaption>Goto from a Marikina carinderia, a reliable bowl when the everlasting meatloaf needs a warm side.</figcaption></figure>',
        ],
    ],

    // 16. Pansol hot spring ------------------------------------------------
    'pansol-hot-spring-weekend-calamba-family-plan' => [
        [
            'anchor' => '<h2>What a Pansol Weekend Looks Like</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/calamba-pansol-hot-springs-calamba-barangay.jpg" alt="Hot spring pool resort in Pansol, Calamba at the foot of Mount Makiling" loading="lazy"><figcaption>One of the Pansol hot spring pools fed by the geothermal heat from the dormant Mount Makiling volcano.</figcaption></figure>',
        ],
        [
            'anchor' => '<h2>Pair It With Rizal Shrine</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/calamba-rizal-shrine-and-birthplace-museum.jpg" alt="Reconstructed Rizal Shrine and birthplace museum in Calamba, Laguna" loading="lazy"><figcaption>The Rizal Shrine in Calamba, the reconstructed Rizal family home with the original well still on the grounds.</figcaption></figure>',
        ],
    ],

    // 17. San Pablo seven lakes --------------------------------------------
    'san-pablo-seven-lakes-pandin-yambo' => [
        [
            'anchor' => '<h2>At Pandin Lake</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/san-pablo-pandin-lake-and-twin-yambo-lake.jpg" alt="Pandin Lake and twin Yambo Lake crater lakes in San Pablo, Laguna" loading="lazy"><figcaption>Pandin Lake and its twin Yambo, two of the seven crater lakes of San Pablo, separated by a thin land bridge.</figcaption></figure>',
        ],
        [
            'anchor' => '<h3>Sampaloc Lake Loop</h3>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/san-pablo-sampaloc-lake.jpg" alt="Sampaloc Lake with the paved jogging loop in San Pablo City" loading="lazy"><figcaption>Sampaloc Lake, the largest of the seven and the easiest to walk, with a 7.5-kilometer paved loop right in the city proper.</figcaption></figure>',
        ],
    ],

    // 19. Liliw tsinelas + Nagcarlan underground cemetery ------------------
    'liliw-tsinelas-nagcarlan-underground-cemetery' => [
        [
            'anchor' => '<h3>The Underground Cemetery</h3>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/nagcarlan-nagcarlan-underground-cemetery.jpg" alt="Nagcarlan Underground Cemetery chapel entrance in Laguna" loading="lazy"><figcaption>The Nagcarlan Underground Cemetery chapel, built in 1845 by the Franciscans and the only underground cemetery in the country.</figcaption></figure>',
        ],
    ],

    // 20. Lucban Pahiyas festival ------------------------------------------
    'lucban-pahiyas-festival-weekend-plan' => [
        [
            'anchor' => '<h2>What to See at the Festival</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/quezon-province-pahiyas-festival-in-lucban-may-15.jpg" alt="Pahiyas Festival decorated facade with colored kiping in Lucban, Quezon" loading="lazy"><figcaption>A Lucban facade dressed in colored kiping for the May 15 Pahiyas Festival, the harvest celebration for San Isidro Labrador.</figcaption></figure>',
        ],
        [
            'anchor' => '<h3>Pancit Habhab and Lucban Longganisa</h3>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/quezon-province-lucban-longganisa.jpg" alt="Lucban longganisa garlicky vinegar-spiked sausages grilled" loading="lazy"><figcaption>Lucban longganisa, the garlicky vinegar-spiked sausage that defines Quezon cooking, sold in stalls all along the festival route.</figcaption></figure>',
        ],
    ],

    // 21. Sariaya heritage houses ------------------------------------------
    'sariaya-heritage-houses-walking-tour' => [
        [
            'anchor' => '<h2>The Three Heritage Houses</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/quezon-province-sariaya-heritage-houses.jpg" alt="Art Deco coconut-baron heritage mansion in Sariaya, Quezon" loading="lazy"><figcaption>One of the Sariaya coconut-baron mansions, built in the 1920s and 1930s in the Art Deco style that defines the town.</figcaption></figure>',
        ],
        [
            'anchor' => '<h2>The Church and the Plaza</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/sariaya-san-francisco-de-asis-parish-church.jpg" alt="San Francisco de Asis Parish Church on the Sariaya town plaza" loading="lazy"><figcaption>The Saint Francis of Assisi Church, the late-1700s heritage church that anchors the Sariaya plaza and the walking-tour loop.</figcaption></figure>',
        ],
        [
            'anchor' => '<h2>Sariaya Food Finds</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/sariaya-broas.jpg" alt="Sariaya broas lady-finger biscuits from a heritage bakery" loading="lazy"><figcaption>Sariaya broas, the local lady fingers from the heritage bakeries lining the main road, the pasalubong most travelers leave with.</figcaption></figure>',
        ],
    ],

];
