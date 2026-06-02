<?php

/**
 * Blog image overlays for batch 7 posts (Quezon heritage, Tarlac/Pampanga
 * day trips, Pangasinan loop, Apo Island diving, Donsol whale shark, and
 * the Siquijor weekend). Each entry maps a blog slug to one or more
 * <figure> blocks injected by the renderer either before or after a
 * unique anchor substring found in the post content_html.
 *
 * Posts that are pure operator-comparison or BIO-rules advice with no
 * concrete list of photographable specifics are intentionally left out.
 */

return [

    // ----------------------------------------------------------------------
    // Tayabas heritage walk (batch7)
    // ----------------------------------------------------------------------
    'tayabas-city-heritage-walk-quezon-day-trip' => [
        [
            'anchor' => '<h2>Morning: The Basilica</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/quezon-province-tayabas-casa-comunidad-and-old-church.jpg" alt="Tayabas Basilica of Saint Michael the Archangel and Casa Comunidad" loading="lazy"><figcaption>The Basilica of Saint Michael the Archangel and the Casa Comunidad heritage building on the same plaza. Locals call the key-shaped church Susi ng Tayabas.</figcaption></figure>',
        ],
        [
            'anchor' => '<h2>Afternoon: Malagonlong Bridge</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/4c/Malagonlong_Bridge_Tayabas_Quezon.jpg/800px-Malagonlong_Bridge_Tayabas_Quezon.jpg" alt="Malagonlong Bridge five-arch stone span over the Dumacaa River in Tayabas Quezon" loading="lazy"><figcaption>The Malagonlong Bridge, a 445-foot five-arch stone span built in 1840 over the Dumacaa River. It is now a National Cultural Treasure. Photo via <a href="https://commons.wikimedia.org/wiki/File:Malagonlong_Bridge_Tayabas_Quezon.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Kamay ni Hesus shrine (batch7)
    // ----------------------------------------------------------------------
    'kamay-ni-hesus-lucban-stations-climb-day' => [
        [
            'anchor' => '<h3>The Risen Christ Statue</h3>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/3/3d/Kamay_ni_Hesus_Shrine_Lucban_Quezon.jpg/800px-Kamay_ni_Hesus_Shrine_Lucban_Quezon.jpg" alt="Kamay ni Hesus hilltop Risen Christ statue in Lucban Quezon" loading="lazy"><figcaption>The 50-foot Risen Christ statue at the top of the 305-step climb in Lucban. On clear mornings Mount Banahaw is visible to the south. Photo via <a href="https://commons.wikimedia.org/wiki/File:Kamay_ni_Hesus_Shrine_Lucban_Quezon.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => '<h3>Basilica of Saint Louis of Toulouse</h3>',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/quezon-province-lucban-longganisa.jpg" alt="Lucban longganisa garlicky pork sausage with garlic rice and egg" loading="lazy"><figcaption>Longganisa Lucban, the garlicky pork sausage that anchors a Lucban lunch alongside pancit habhab.</figcaption></figure>',
        ],
        [
            'anchor' => 'After lunch, walk over to the Basilica of Saint Louis of Toulouse',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/quezon-province-pahiyas-festival-in-lucban-may-15.jpg" alt="Pahiyas Festival kiping decorations on a Lucban house every May 15" loading="lazy"><figcaption>The basilica is also the spiritual center of the Pahiyas Festival every May 15, when Lucban houses are dressed in kiping rice-wafer leaves.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Cebu to Bohol ferry guide (batch7)
    // ----------------------------------------------------------------------
    'cebu-to-bohol-ferry-guide-first-timers' => [
        [
            'anchor' => '<h3>OceanJet</h3>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/transport/oceanjet-cebu.jpg" alt="OceanJet fast-craft ferry between Cebu City and Tagbilaran" loading="lazy"><figcaption>OceanJet runs the highest-frequency Cebu City to Tagbilaran route, with Open Air, Tourist, and Business class seats.</figcaption></figure>',
        ],
        [
            'anchor' => '<h3>FastCat</h3>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/transport/fastcat.jpg" alt="FastCat RORO ferry to Tubigon port Bohol" loading="lazy"><figcaption>FastCat is the RORO option that docks at Tubigon Port on western Bohol, not Tagbilaran. It is the pick if you are bringing a car or motorbike.</figcaption></figure>',
        ],
        [
            'anchor' => '<h2>The Two Bohol Ports</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/panglao-alona-beach.jpg" alt="Alona Beach Panglao Bohol arrival via Tagbilaran port" loading="lazy"><figcaption>Most fast-craft passengers land at Tagbilaran Port and continue 30 to 45 minutes to Panglao for Alona Beach and the resort strip.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Best bulalo in Tagaytay (batch7)
    // ----------------------------------------------------------------------
    'best-bulalo-tagaytay-six-stop-food-trail' => [
        [
            'anchor' => '<h2>What Makes a Good Tagaytay Bulalo</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/tagaytay-bulalo.jpg" alt="Tagaytay bulalo clear broth with bone marrow corn cabbage and pechay" loading="lazy"><figcaption>A proper Tagaytay bulalo: clear broth, intact bone marrow, and firm cabbage, corn, and pechay added near the end.</figcaption></figure>',
        ],
        [
            'anchor' => '<h3>6. Mahogany Market Bulalohan</h3>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/tagaytay-mahogany-market.jpg" alt="Mahogany Market bulalohan stalls in Tagaytay" loading="lazy"><figcaption>The Mahogany Market bulalohan stalls on the way to Tagaytay City. The beef comes straight from the same building, which is why the broth is some of the freshest on the ridge.</figcaption></figure>',
        ],
        [
            'anchor' => '<li>Tawilis, the small Taal Lake fish, served crispy fried</li>',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/silang-tawilis.jpg" alt="Crispy fried tawilis the small Taal Lake fish that pairs with bulalo" loading="lazy"><figcaption>Crispy fried tawilis, the small Taal Lake fish that pairs naturally with a bulalo bowl on a cool Tagaytay afternoon.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Mount Pinatubo day tour (batch7) - day tour version
    // ----------------------------------------------------------------------
    'mount-pinatubo-crater-day-tour-capas-tarlac' => [
        [
            'anchor' => '<h3>What the Canyon Looks Like</h3>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/95/Lahar_canyon_Mt_Pinatubo.jpg/800px-Lahar_canyon_Mt_Pinatubo.jpg" alt="Lahar canyon walls layered in gray and ochre on the way to Pinatubo" loading="lazy"><figcaption>The lahar canyon between Sta Juliana and the trailhead. The layered walls are the visible record of the 1991 eruption deposits. Photo via <a href="https://commons.wikimedia.org/wiki/File:Lahar_canyon_Mt_Pinatubo.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => '<h3>The Crater Itself</h3>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/pampanga-province-mt-pinatubo-crater-lake.jpg" alt="Mount Pinatubo turquoise crater lake ringed by gray and ochre walls" loading="lazy"><figcaption>The Pinatubo crater lake from the rim. Swimming is no longer allowed for safety reasons, but the viewing platform gives you 30 to 45 minutes for photos and lunch.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Lake Lumot glamping (batch7)
    // ----------------------------------------------------------------------
    'glamping-lake-lumot-cavinti-laguna-slow-weekend' => [
        [
            'anchor' => '<h2>Day Two: Sunrise and Slow Yoga</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/9a/Lake_Lumot_Cavinti_Laguna.jpg/800px-Lake_Lumot_Cavinti_Laguna.jpg" alt="Lake Lumot glassy morning surface in Cavinti Laguna" loading="lazy"><figcaption>Lake Lumot at sunrise, when the fog burns off and the surface goes from milky to glassy in about 20 minutes. Photo via <a href="https://commons.wikimedia.org/wiki/File:Lake_Lumot_Cavinti_Laguna.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => '<h3>Optional Side Trips</h3>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/a4/Pagsanjan_Falls_Laguna.jpg/800px-Pagsanjan_Falls_Laguna.jpg" alt="Pagsanjan Falls boat ride in Laguna" loading="lazy"><figcaption>Pagsanjan Falls in the next town over is the easiest afternoon add-on from the Lake Lumot cabins. Photo via <a href="https://commons.wikimedia.org/wiki/File:Pagsanjan_Falls_Laguna.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Apo Island diving (batch7)
    // ----------------------------------------------------------------------
    'apo-island-diving-day-dauin-marine-sanctuary' => [
        [
            'anchor' => 'Coconut Point is the headline drift dive',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/dauin-apo-island-marine-sanctuary.jpg" alt="Apo Island marine sanctuary off Dauin Negros Oriental" loading="lazy"><figcaption>Apo Island, the community-managed marine sanctuary that has been protected since 1982. The reef has recovered to the point where green turtles are the headline sighting.</figcaption></figure>',
        ],
        [
            'anchor' => '<h3>The Turtle Sightings</h3>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/9d/Green_sea_turtle_Apo_Island.jpg/800px-Green_sea_turtle_Apo_Island.jpg" alt="Green sea turtle resting on the reef at Apo Island" loading="lazy"><figcaption>A green turtle on the Apo Island reef. The resident population has gotten used to divers, but the no-touch rule is strictly enforced by the divemasters. Photo via <a href="https://commons.wikimedia.org/wiki/File:Green_sea_turtle_Apo_Island.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => '<h2>Getting to Dauin</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/dauin-dumaguete-city-15-min-away.jpg" alt="Dumaguete City Rizal Boulevard gateway to Dauin" loading="lazy"><figcaption>Dumaguete City is the air gateway. Most Dauin dive resorts include the 30-minute transfer south on the coastal road.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Nayong Pilipino Clark (batch7)
    // ----------------------------------------------------------------------
    'nayong-pilipino-clark-replica-landmarks-walk' => [
        [
            'anchor' => '<h3>Banaue Rice Terraces</h3>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/22/Banaue_Rice_Terraces.jpg/800px-Banaue_Rice_Terraces.jpg" alt="Banaue Rice Terraces Ifugao the original site referenced by the Nayong Pilipino replica" loading="lazy"><figcaption>The original Banaue Rice Terraces in Ifugao. The Nayong Pilipino replica at Clark uses real planted paddies on a sloped hillside to mirror the view. Photo via <a href="https://commons.wikimedia.org/wiki/File:Banaue_Rice_Terraces.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => '<h3>Chocolate Hills</h3>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/Chocolate_Hills_overview.jpg/800px-Chocolate_Hills_overview.jpg" alt="Chocolate Hills Bohol viewing deck overview" loading="lazy"><figcaption>The Chocolate Hills in Bohol. At Nayong Pilipino the mound cluster is scaled down but the pattern still works as a photo backdrop. Photo via <a href="https://commons.wikimedia.org/wiki/File:Chocolate_Hills_overview.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => '<li>Vigan Calle Crisologo storefront row</li>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c4/Vigan_Calle_Crisologo.jpg/800px-Vigan_Calle_Crisologo.jpg" alt="Calle Crisologo cobblestone street in Vigan Ilocos Sur" loading="lazy"><figcaption>The original Calle Crisologo in Vigan. The Nayong Pilipino replica copies the storefront row and the cobblestone texture. Photo via <a href="https://commons.wikimedia.org/wiki/File:Vigan_Calle_Crisologo.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Coco Grove San Juan Siquijor (batch7)
    // ----------------------------------------------------------------------
    'coco-grove-san-juan-siquijor-slow-weekend-base' => [
        [
            'anchor' => '<h3>Tubod Marine Sanctuary</h3>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/0/00/Tubod_Marine_Sanctuary_Siquijor.jpg/800px-Tubod_Marine_Sanctuary_Siquijor.jpg" alt="Tubod Marine Sanctuary off the San Juan coast in Siquijor" loading="lazy"><figcaption>The Tubod Marine Sanctuary fronts the Coco Grove strip in San Juan. The reef slopes from shallow water to around 8 meters and is good for an afternoon snorkel. Photo via <a href="https://commons.wikimedia.org/wiki/File:Tubod_Marine_Sanctuary_Siquijor.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => '<li>Cambugahay Falls in Lazi for the rope swing and natural pools</li>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/siquijor-cambugahay-falls.jpg" alt="Cambugahay Falls multi-tiered natural pools in Lazi Siquijor" loading="lazy"><figcaption>Cambugahay Falls in Lazi, the multi-tier pool stop with the rope swing on the standard Siquijor scooter loop.</figcaption></figure>',
        ],
        [
            'anchor' => '<li>The old Balete tree in Lazi with the fish spa pool at the roots</li>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/siquijor-the-old-enchanted-balete-tree.jpg" alt="Old balete tree with fish spa pool at the roots in Siquijor" loading="lazy"><figcaption>The centuries-old balete tree in Lazi. A natural cold-spring pool at the roots is now a small fish spa where mga isda nibble at your feet.</figcaption></figure>',
        ],
        [
            'anchor' => '<li>The Lazi Convent, one of the oldest in the country, for the heritage stop</li>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/siquijor-lazi-convent-1857.jpg" alt="Lazi Convent stone and wood heritage building from 1857 in Siquijor" loading="lazy"><figcaption>The Lazi Convent, built in 1857, is one of the oldest and largest convents in the country and a National Cultural Treasure.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Hot air balloon festival Clark (batch7) - SKIPPED, photo essay style
    // mostly schedule/logistics advice with no concrete list of places.
    // ----------------------------------------------------------------------

    // ----------------------------------------------------------------------
    // Minalungao National Park (batch7)
    // ----------------------------------------------------------------------
    'minalungao-national-park-nueva-ecija-day-trip' => [
        [
            'anchor' => '<h3>What the River Looks Like</h3>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/nueva-ecija-minalungao-national-park.jpg" alt="Sumacbao River jade-green water between limestone walls at Minalungao" loading="lazy"><figcaption>The Sumacbao River cutting between 16-meter limestone cliffs at Minalungao. The water shifts from deep emerald in the morning to a brighter jade at noon.</figcaption></figure>',
        ],
        [
            'anchor' => '<h2>The Bamboo Raft Ride</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/f/fb/Minalungao_National_Park_bamboo_raft.jpg/800px-Minalungao_National_Park_bamboo_raft.jpg" alt="Bamboo raft on the Sumacbao River at Minalungao National Park" loading="lazy"><figcaption>The bamboo raft, the signature Minalungao activity. A boatman poles you through the canyon with stops for swimming and photos. Photo via <a href="https://commons.wikimedia.org/wiki/File:Minalungao_National_Park_bamboo_raft.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Tarlac beyond Pinatubo (batch7)
    // ----------------------------------------------------------------------
    'tarlac-beyond-pinatubo-monasterio-aquino-sugar-trail' => [
        [
            'anchor' => '<h2>Stop One: Monasterio de Tarlac</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Monasterio_de_Tarlac.jpg/800px-Monasterio_de_Tarlac.jpg" alt="Monasterio de Tarlac Risen Christ statue on Mount Resurrection" loading="lazy"><figcaption>The 30-meter Risen Christ statue at Monasterio de Tarlac in San Jose. The grounds also hold a relic of the True Cross. Photo via <a href="https://commons.wikimedia.org/wiki/File:Monasterio_de_Tarlac.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => '<h2>Stop Two: Ninoy and Cory Aquino Center</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/tarlac-aquino-center-and-museum.jpg" alt="Ninoy and Cory Aquino Center and Museum in Hacienda Luisita Tarlac" loading="lazy"><figcaption>The Aquino Center in Hacienda Luisita documents Ninoy and Cory Aquino through personal effects, archived speeches, and a replica of the original prison cell.</figcaption></figure>',
        ],
        [
            'anchor' => '<h2>Optional: Capas National Shrine</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/25/Capas_National_Shrine.jpg/800px-Capas_National_Shrine.jpg" alt="Capas National Shrine white obelisk memorial in Tarlac" loading="lazy"><figcaption>The Capas National Shrine obelisk commemorates the Filipino and American POWs of Camp O Donnell after the 1942 Bataan Death March. Photo via <a href="https://commons.wikimedia.org/wiki/File:Capas_National_Shrine.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Dingalan Aurora cliff loop (batch7)
    // ----------------------------------------------------------------------
    'dingalan-aurora-cliff-loop-lighthouse-white-beach' => [
        [
            'anchor' => '<h2>Stop One: Dingalan Lighthouse</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/40/Dingalan_Lighthouse_viewpoint.jpg/800px-Dingalan_Lighthouse_viewpoint.jpg" alt="Dingalan Lighthouse cliff viewpoint over Dingalan Bay Aurora" loading="lazy"><figcaption>The lighthouse viewpoint at the southern tip of Dingalan, with the curving bay and offshore islands below. Photo via <a href="https://commons.wikimedia.org/wiki/File:Dingalan_Lighthouse_viewpoint.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => '<h2>Stop Two: White Beach</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/dingalan-white-beach.jpg" alt="White Beach hidden cove between cliffs in Dingalan Aurora" loading="lazy"><figcaption>White Beach is a short banca ride from the main pier, tucked between two cliff walls with no resorts and a calm inner cove.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Donsol whale shark (batch7)
    // ----------------------------------------------------------------------
    'donsol-whale-shark-interaction-ethical-banca' => [
        [
            'anchor' => '<h3>The Interaction</h3>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/aa/Whale_shark_Donsol_Sorsogon.jpg/800px-Whale_shark_Donsol_Sorsogon.jpg" alt="Snorkeler at safe distance from a whale shark in Donsol Sorsogon" loading="lazy"><figcaption>A butanding cruising near the surface off Donsol. The BIO rules require snorkelers to stay at least 3 meters from the head and 4 meters from the tail. Photo via <a href="https://commons.wikimedia.org/wiki/File:Whale_shark_Donsol_Sorsogon.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => '<h3>Firefly Cruise</h3>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/0/03/Ogod_River_mangroves_Donsol.jpg/800px-Ogod_River_mangroves_Donsol.jpg" alt="Ogod River mangroves where the Donsol firefly cruise runs at night" loading="lazy"><figcaption>The Ogod River mangroves on the Donsol firefly cruise. The display is best on moonless nights and during the dry season. Photo via <a href="https://commons.wikimedia.org/wiki/File:Ogod_River_mangroves_Donsol.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Mt Pinatubo crater hike (batch8) - the other Pinatubo post
    // ----------------------------------------------------------------------
    'mt-pinatubo-crater-day-hike-capas-tarlac' => [
        [
            'anchor' => '<h3>Photo Stops</h3>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/95/Lahar_canyon_Mt_Pinatubo.jpg/800px-Lahar_canyon_Mt_Pinatubo.jpg" alt="Lahar canyon walls on the 4x4 ride to the Pinatubo trailhead" loading="lazy"><figcaption>The upper lahar canyon on the 4x4 route, where the gray walls rise on both sides and the river curves through the ash. Photo via <a href="https://commons.wikimedia.org/wiki/File:Lahar_canyon_Mt_Pinatubo.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => '<h3>The Crater Lake</h3>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/pampanga-province-mt-pinatubo-crater-lake.jpg" alt="Pinatubo crater lake turquoise water ringed by black volcanic rock" loading="lazy"><figcaption>The Pinatubo crater lake at the top of the trail. The water is turquoise from dissolved minerals and swimming is no longer allowed because of the acidity.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Nagsasa Cove (batch8)
    // ----------------------------------------------------------------------
    'nagsasa-cove-zambales-quieter-anawangin-alternative' => [
        [
            'anchor' => '<h2>The Boat Ride</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/subic-anawangin-and-nagsasa-coves-from-pundaquit.jpg" alt="Banca approaching Nagsasa Cove from Pundaquit Zambales" loading="lazy"><figcaption>The banca route from Pundaquit hugs the coast, passes Capones Island, and opens into the wider Nagsasa bay after about 90 minutes.</figcaption></figure>',
        ],
        [
            'anchor' => '<h2>What Nagsasa Looks Like</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/d/d1/Nagsasa_Cove_Zambales.jpg/800px-Nagsasa_Cove_Zambales.jpg" alt="Nagsasa Cove gray sand and agoho pines backing the beach" loading="lazy"><figcaption>Nagsasa Cove with its gray volcanic sand and the agoho pines that grow in the post-eruption soil. The ridge behind the cove blocks the afternoon wind. Photo via <a href="https://commons.wikimedia.org/wiki/File:Nagsasa_Cove_Zambales.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => '<li>Boat over to Capones Island on the way back for a short stop at the lighthouse</li>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/9a/Capones_Island_Lighthouse_Zambales.jpg/800px-Capones_Island_Lighthouse_Zambales.jpg" alt="Capones Island lighthouse on a small rock off Pundaquit Zambales" loading="lazy"><figcaption>The Capones Island lighthouse, an easy add-on stop on the boat ride back to Pundaquit. Photo via <a href="https://commons.wikimedia.org/wiki/File:Capones_Island_Lighthouse_Zambales.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Bolinao Patar Tara first-timer (batch8)
    // ----------------------------------------------------------------------
    'bolinao-patar-tara-beach-first-timer-guide' => [
        [
            'anchor' => '<h3>Patar Beach</h3>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/bolinao-patar-beach.jpg" alt="Patar Beach tan-white sand stretch in Bolinao Pangasinan" loading="lazy"><figcaption>Patar Beach, the long tan-white stretch about 30 minutes from Bolinao town. The dark sand grains hold heat, so beach time is best in the morning and late afternoon.</figcaption></figure>',
        ],
        [
            'anchor' => '<h3>Cape Bolinao Lighthouse</h3>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/bolinao-cape-bolinao-lighthouse.jpg" alt="Cape Bolinao Lighthouse white tower on Punta Piedra in Pangasinan" loading="lazy"><figcaption>Cape Bolinao Lighthouse on Punta Piedra has guided boats since 1905. At 107 meters above sea level it is one of the highest in the country.</figcaption></figure>',
        ],
        [
            'anchor' => '<h3>Bolinao Falls</h3>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/bolinao-bolinao-falls-1-2-3.jpg" alt="Bolinao Falls one of three tiered waterfalls inland from town" loading="lazy"><figcaption>The Bolinao Falls trio sit along the inland road. Falls 1 has the small swim pool, Falls 2 is the prettiest, and Falls 3 is the quietest.</figcaption></figure>',
        ],
        [
            'anchor' => '<h3>Giant Clam Sanctuary</h3>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c8/Giant_clam_Tridacna_gigas.jpg/800px-Giant_clam_Tridacna_gigas.jpg" alt="Giant clam Tridacna gigas of the kind bred at the Bolinao Marine Laboratory" loading="lazy"><figcaption>A giant clam of the kind bred at the Bolinao Marine Laboratory sanctuary in Lucero. Snorkel at low tide to see the row of mantles in shallow water. Photo via <a href="https://commons.wikimedia.org/wiki/File:Giant_clam_Tridacna_gigas.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Pangasinan two-day loop (batch8)
    // ----------------------------------------------------------------------
    'pangasinan-two-day-loop-beyond-hundred-islands' => [
        [
            'anchor' => '<h3>Day 1: Urdaneta to Dagupan</h3>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/pangasinan-general-manaoag-shrine.jpg" alt="Minor Basilica of Our Lady of Manaoag in Pangasinan" loading="lazy"><figcaption>The Minor Basilica of Our Lady of Manaoag opens early and the parking is easier before 10 a.m. Sunday crowds can stretch the visit to two hours.</figcaption></figure>',
        ],
        [
            'anchor' => 'For dinner, the bangus places along the riverbank',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/pangasinan-general-bangus.jpg" alt="Dagupan boneless bangus grilled milkfish dinner plate" loading="lazy"><figcaption>Boneless grilled bangus is the Dagupan dinner ritual. Matutina and Aling Tonya are two of the older riverbank names.</figcaption></figure>',
        ],
        [
            'anchor' => '<h3>Day 2: Alaminos to Bolinao</h3>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/pangasinan-general-hundred-islands-national-park-alaminos.jpg" alt="Hundred Islands National Park view from Governors Island deck" loading="lazy"><figcaption>Governors Island has the viewing deck, Quezon Island has the swim area, and Marcos Island has the small cave. A three-island bangka is enough for half a day.</figcaption></figure>',
        ],
        [
            'anchor' => '<li>Lingayen: pigar-pigar, a peppered beef stir-fry served with cabbage</li>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/pangasinan-general-pigar-pigar.jpg" alt="Pigar-pigar peppered beef stir-fry from Dagupan and Lingayen Pangasinan" loading="lazy"><figcaption>Pigar-pigar, the Dagupan and Lingayen peppered beef stir-fry served with shredded cabbage and rice.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Cape Bolinao Lighthouse + Patar sunset (batch8)
    // ----------------------------------------------------------------------
    'cape-bolinao-lighthouse-patar-sunset-afternoon' => [
        [
            'anchor' => '<h2>The Climb to the Top</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/pangasinan-general-cape-bolinao-lighthouse.jpg" alt="Cape Bolinao Lighthouse white sun-bleached tower on Punta Piedra" loading="lazy"><figcaption>The white sun-bleached tower of Cape Bolinao Lighthouse. The spiral staircase inside is narrow and the rails are old, but the climb is short.</figcaption></figure>',
        ],
        [
            'anchor' => '<h2>Patar Beach for Sunset</h2>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/pangasinan-general-patar-beach-bolinao.jpg" alt="Patar Beach sunset over the South China Sea in Bolinao Pangasinan" loading="lazy"><figcaption>Patar Beach faces directly west, which is why it is the obvious sunset stop after the lighthouse. The southern rocky outcrop frames the sun if you want a different photo.</figcaption></figure>',
        ],
    ],

];
