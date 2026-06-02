<?php

/**
 * Image overlays for blog posts in batch 11 (tourist-spot guides for Region 1).
 *
 * Each entry keys off the blog post slug. Each anchor must appear EXACTLY ONCE
 * inside that post's content_html; the figure HTML is inserted immediately
 * after the anchor block. Image srcs do not duplicate across this batch.
 */

return [

    // ------------------------------------------------------------------
    // 1. TOURIST SPOTS IN LA UNION
    // ------------------------------------------------------------------
    'tourist-spots-la-union-honest-traveler-guide' => [
        [
            'anchor' => '<p>Urbiztondo is the surf strip of San Juan and the heart of the La Union scene. Two-hour beginner lessons are available year-round, and the surf-school operators are clustered along the same 300-meter stretch so you can walk down the beach, watch a few sessions, and pick whoever feels right. The waves from October to March are the bigger ones and pull a more serious surf crowd. April to September is calmer, smaller, and friendlier for first-timers.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/la-union-san-juan-surf-beach-urbiztondo.jpg" alt="Urbiztondo Beach, San Juan La Union surf strip" loading="lazy"><figcaption>The Urbiztondo surf strip in San Juan, where the 300-meter beachfront fills up with surf schools and the evening crowd at sunset.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Tangadan is a 30-minute drive inland from San Juan, in the municipality of San Gabriel. It is a wide pool fed by a 10-meter waterfall and rimmed by jumping rocks. The trek in from the registration area takes around 20 minutes through farmland and a small river crossing, easy in trail runners.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/la-union-tangadan-falls.jpg" alt="Tangadan Falls in San Gabriel, La Union" loading="lazy"><figcaption>Tangadan Falls in San Gabriel, with the wide swimming pool and jumping rocks. Best photographed before late afternoon when the basin falls into shade.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Ma-Cho is a Taoist temple set on a hill overlooking the San Fernando bay. It honors Mazu, the Chinese sea goddess, and it has the kind of dragon-tiled roofs and red-pillar courtyards you do not expect on a Philippine coastal hill. Free entry, modest dress code, and the climb up the stairs from the parking area takes around five minutes. Sunset is the best window for the bay view.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/la-union-ma-cho-temple-san-fernando-city.jpg" alt="Ma-Cho Taoist Temple in San Fernando, La Union" loading="lazy"><figcaption>The Ma-Cho Taoist Temple above San Fernando bay, with dragon-tiled roofs and red pillars dedicated to the sea goddess Mazu.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Halo-Halo de Iloko in San Fernando is the famous Ilocos halo-halo restaurant. The order to get is the halo-halo topped with longganisa-flavored ice cream, which sounds like a dare and turns out to be sulit. The shop is on the city\'s main road, walkable from Ma-Cho Temple, and the small dining area fills up by midday on weekends.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/la-union-halo-halo.jpg" alt="Ilocos halo-halo with longganisa ice cream from Halo-Halo de Iloko" loading="lazy"><figcaption>Ilocos halo-halo at Halo-Halo de Iloko, topped with the savory longganisa-flavored ice cream that sounds odd and works.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The Ilocano food scene in La Union is the heaviest carb-and-pork experience in the country. Order Ilocos longganisa (vinegary, garlicky) for breakfast at any carinderia. Order bagnet (deep-fried pork belly) and pinakbet Ilocos-style with bagoong isda for lunch. For halo-halo, Halo-Halo de Iloko is the destination order. For coffee and a pizza, El Union Coffee Co. in San Juan is the surf-town landmark.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/la-union-bagnet.jpg" alt="Ilocos bagnet, deep-fried pork belly" loading="lazy"><figcaption>Bagnet, the deep-fried Ilocos pork belly cooked three times until the rind shatters. Pair it with sukang Iloko and pinakbet.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 2. TOURIST SPOTS IN ILOCOS NORTE
    // ------------------------------------------------------------------
    'tourist-spots-ilocos-norte-laoag-paoay-pagudpud-loop' => [
        [
            'anchor' => '<p>Paoay Church is the most-photographed church in the north and a UNESCO World Heritage Site. The earthquake-baroque buttresses on each side were built thick to handle the seismic activity that flattened lesser buildings across the centuries. Up close the coral-stone surface has a texture and a wear that no photo really captures.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/ilocos-norte-paoay-church-1710.jpg" alt="Paoay Church, a UNESCO earthquake-baroque heritage site" loading="lazy"><figcaption>Paoay Church, finished 1710. The massive side buttresses are what kept the coral-stone walls upright through three centuries of earthquakes.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>La Paz Sand Dunes is a 20-minute drive from Laoag, on the Suba area between the city and the West Philippine Sea. The dunes were the location used for the iconic Filipino films Himala and Panday. The 4x4 dune-bashing run includes a sandboarding stop on the steeper slopes; the standard run takes around 30 to 45 minutes.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/ilocos-norte-la-paz-sand-dunes-laoag.jpg" alt="La Paz Sand Dunes in Laoag, Ilocos Norte" loading="lazy"><figcaption>The La Paz Sand Dunes at Suba, the film location for Himala. Late afternoon is the sweet spot, before the 4x4 operators close shop.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The Bangui windmills are 20 turbines lined along the beach of Bangui Bay, the first wind farm in Southeast Asia. The drive in is the actual highlight; the coastal road from Burgos curves down to the beach and the turbines come into view in a slow reveal. Park anywhere along the public road that fronts the beach, walk down to the sand, and the photos take themselves.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/ilocos-norte-bangui-windmills.jpg" alt="Bangui Wind Farm along Bangui Bay" loading="lazy"><figcaption>The Bangui windmills, the first wind farm in Southeast Asia, lined along the pebble-and-coarse-sand beach of Bangui Bay.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Cape Bojeador Lighthouse is an 1892 brick tower on a hilltop in Burgos. The Spanish colonial government built it to mark the northwestern tip of Luzon. The climb up to the gallery is currently restricted but the base is open, the view to the Bangui coast is full, and the bricks themselves are worth the visit. Free entry, plus a small museum room about the lighthouse keepers who maintained the kerosene lamp before electrification.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/ilocos-norte-cape-bojeador-lighthouse-burgos.jpg" alt="Cape Bojeador Lighthouse in Burgos, Ilocos Norte" loading="lazy"><figcaption>Cape Bojeador Lighthouse in Burgos, built 1892 to mark the northwestern tip of Luzon. The bricks alone, weathered by 130 years of wind, are worth the climb up.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Kapurpurawan is a stretch of white limestone sculpted by waves and wind over centuries. The rock is reached by a 10-minute walk from the parking area, with a short flight of stairs down to the coast. Up close the formations look like cresting waves frozen mid-curl. The locals will offer to take photos for tips.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/ilocos-norte-kapurpurawan-rock-formation.jpg" alt="Kapurpurawan Rock Formation, Burgos, Ilocos Norte" loading="lazy"><figcaption>The Kapurpurawan rock formation in Burgos, a stretch of wind-and-wave-sculpted white limestone that looks like waves frozen mid-curl.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Pagudpud is the northernmost beach stretch of Luzon, and the Saud and Blue Lagoon beaches are the two postcard coves. Saud is the calmer family beach, with the resort row strung along the curve of the bay. Blue Lagoon is the surfable side, with bigger waves from habagat season (June to October).</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/ilocos-norte-pagudpud-beaches-saud-blue-lagoon-patapat.jpg" alt="Pagudpud beaches at Saud and Blue Lagoon" loading="lazy"><figcaption>Pagudpud at the Saud and Blue Lagoon stretch, the northernmost beaches of Luzon. Saud is the calm-cove side; Blue Lagoon catches the habagat waves.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 3. TOURIST SPOTS IN VIGAN, ILOCOS SUR
    // ------------------------------------------------------------------
    'tourist-spots-vigan-ilocos-sur-heritage-walk' => [
        [
            'anchor' => '<p>Calle Crisologo is the cobblestone heritage street, the postcard core of Vigan. Spanish-era ancestral houses line both sides, most of them still occupied by descendants of the original Ilocano-Chinese mestizo families. The street is closed to motorized traffic from 6 PM onward; calesas (horse-drawn carriages) run the route during the day for visitors.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/vigan-calle-crisologo.jpg" alt="Calle Crisologo cobblestone heritage street in Vigan" loading="lazy"><figcaption>Calle Crisologo, the cobblestone heritage strip in Vigan. The 6 AM walk before the calesas roll out is the version locals quietly prefer.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The Saint Paul Metropolitan Cathedral (Vigan Cathedral) anchors the eastern end of the heritage core. It is a working parish, a former earthquake-baroque structure rebuilt and reinforced through the centuries, and the seat of the Archdiocese of Nueva Segovia. Free entry; cover shoulders if you are entering during a mass.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/vigan-vigan-cathedral-and-plaza-salcedo.jpg" alt="Saint Paul Metropolitan Cathedral and Plaza Salcedo, Vigan" loading="lazy"><figcaption>The Saint Paul Metropolitan Cathedral fronting Plaza Salcedo, where the nightly dancing fountain show closes out the heritage walk.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Bantay Bell Tower is a 1591 freestanding bell tower 10 minutes north of Vigan in the town of Bantay. It is one of the oldest stone bell towers in the Philippines, separated from its parish church for fire safety in case the cathedral burned. Climb the wooden stairs to the top for an overview of the heritage core to the south and the Bantay rice fields to the north. Free entry.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/vigan-bantay-bell-tower.jpg" alt="Bantay Bell Tower, 1591, north of Vigan" loading="lazy"><figcaption>The Bantay Bell Tower, built 1591 and detached from its parish church. The wooden stairs lead to a view of Vigan to the south and rice fields to the north.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Vigan empanada from the Plaza Burgos stalls is the headline order. The orange color comes from achuete, the crisp from a quick deep-fry, and the filling is grated papaya, longganisa, and an egg dropped in just before frying. Bagnet plus sukang Iloko at Cafe Leona on Calle Crisologo is the dinner order. Longganisa Vigan-style (garlicky, vinegary, smaller than Lucban) for breakfast at any carinderia.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/vigan-vigan-empanada.jpg" alt="Vigan empanada with longganisa and egg" loading="lazy"><figcaption>Vigan empanada from the Plaza Burgos stalls. The orange comes from achuete, the egg goes in raw just before the quick deep-fry.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 4. TOURIST SPOTS IN PANGASINAN
    // ------------------------------------------------------------------
    'tourist-spots-pangasinan-bolinao-alaminos-hundred-islands' => [
        [
            'anchor' => '<p>Hundred Islands is the postcard attraction of Pangasinan: 124 small islands scattered across Lingayen Gulf, each different, each climbable in about 20 minutes. The full-day boat tour visits 4 to 6 of them, with stops for swimming, snorkeling, and a picnic lunch. Lucap Wharf in Alaminos is where the boats leave at 7 AM and return by 4 PM.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/pangasinan-general-hundred-islands-national-park-alaminos.jpg" alt="Hundred Islands National Park in Alaminos, Pangasinan" loading="lazy"><figcaption>The Hundred Islands National Park off Lucap Wharf, Alaminos. The full-day boat run covers 4 to 6 islands, with Governor\'s Island as the panorama stop.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Patar Beach is the long stretch of white-to-cream sand on the northern Pangasinan tip. It is a 4.5-hour drive from Quezon City via TPLEX, and the sand is closer to white than any other beach in the region (Pagudpud excluded). The best sunset spot is the rock outcrops at the southern end of the beach, not the resort-cluster center where most travelers pile up.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/pangasinan-general-patar-beach-bolinao.jpg" alt="Patar Beach in Bolinao, Pangasinan" loading="lazy"><figcaption>Patar Beach in Bolinao, the white-to-cream stretch on the northern Pangasinan tip. The rock outcrops at the southern end are the cleaner sunset spot.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Cape Bolinao is a 19th-century lighthouse perched on a hilltop overlooking the West Philippine Sea. Free to climb at sunset; the steps up are paved and the gallery view of the Bolinao coast is the kind of thing that ends a day on the beach properly. The lighthouse is 15 minutes from Patar by tricycle, easy combination stop.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/pangasinan-general-cape-bolinao-lighthouse.jpg" alt="Cape Bolinao Lighthouse on the West Philippine Sea" loading="lazy"><figcaption>The Cape Bolinao Lighthouse, a 19th-century tower 15 minutes by tricycle from Patar Beach. Sunset from the gallery closes the Bolinao day cleanly.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The Our Lady of Manaoag Shrine is the country\'s major Marian pilgrimage site. The basilica is in the town of Manaoag, an hour east of Dagupan and roughly two hours from Hundred Islands. Pilgrims come from all over the Philippines for novenas and the touching of the image. The grounds are open year-round; the queues are heaviest on weekends and during October (the Lady\'s feast month).</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/pangasinan-general-manaoag-shrine.jpg" alt="Our Lady of Manaoag Shrine in Pangasinan" loading="lazy"><figcaption>The Our Lady of Manaoag Shrine, the country\'s major Marian pilgrimage basilica. Weekday mornings are the calm window before the bus pilgrimage groups arrive.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Lingayen is where MacArthur landed in 1945, and the long stretch of public beach there is a working town beach, not a resort destination. The Lingayen Capitol building (1918) faces the sea across a wide plaza; the architecture alone is worth a 20-minute walk. Locals run a sunset salakot crowd here on weekends, plus a row of carinderias along the beachfront for fresh seafood.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/pangasinan-general-lingayen-beach.jpg" alt="Lingayen Beach and Capitol grounds in Pangasinan" loading="lazy"><figcaption>Lingayen Beach in front of the 1918 Capitol building, where MacArthur landed in 1945. The carinderia row along the beachfront sells the morning seafood catch.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Pigar-pigar is the Dagupan stir-fried beef specialty; any pigaran stall in the city serves the right version with crispy fried onions. Bangus from the Dagupan side is the national milkfish standard; the city is the bangus capital of the Philippines. Puto Calasiao is the small, soft, steamed rice cake from the neighboring town of Calasiao. At Bolinao, the Patar Beach eateries serve fresh oysters and grilled fish straight from the morning catch.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/pangasinan-general-pigar-pigar.jpg" alt="Pigar-pigar, the Dagupan stir-fried beef" loading="lazy"><figcaption>Pigar-pigar, Dagupan\'s stir-fried beef with crispy onions, served on a hot iron plate at any pigaran stall in the city.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 5. TOURIST SPOTS IN LAOAG (HALF-DAY WALK)
    // ------------------------------------------------------------------
    'tourist-spots-laoag-ilocos-norte-half-day-walk' => [
        [
            'anchor' => '<p>The Sinking Bell Tower is the postcard stop of Laoag. The 17th-century brick tower sinks an inch a year into the soft soil of the Padsan riverbank. Built in 1612 by Augustinian friars, it once stood tall enough that a person on horseback could enter through the lower door. Now the door is partly buried and you can crouch through to enter the ground floor.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/laoag-sinking-bell-tower.jpg" alt="Sinking Bell Tower in Laoag, Ilocos Norte" loading="lazy"><figcaption>The Sinking Bell Tower of Laoag, built 1612 and sinking an inch a year into the soft Padsan riverbank soil. The lower door is now half-buried.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Saint William Cathedral is the Ilocos Norte mother church and the seat of the Diocese of Laoag. The current building is the 1880 reconstruction after the 1707 original was damaged by earthquakes. The facade is Italian-Renaissance style, which is unusual for Ilocos where the earthquake-baroque style dominates. Free entry, working parish, plaza-facing.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/laoag-saint-william-cathedral-laoag-cathedral.jpg" alt="Saint William Cathedral (Laoag Cathedral), Ilocos Norte" loading="lazy"><figcaption>Saint William Cathedral in Laoag, an 1880 Italian-Renaissance reconstruction that breaks the earthquake-baroque pattern of the rest of Ilocos.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>La Paz Sand Dunes is 20 minutes from the city center, on the Suba area between Laoag and the West Philippine Sea. The dunes stretch for a few kilometers along the coast and they were the location for the iconic Filipino film Himala. The standard tour is a 4x4 dune-bashing run with a sandboarding stop on the steeper slopes, taking around 30 to 45 minutes.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/laoag-la-paz-sand-dunes.jpg" alt="La Paz Sand Dunes at Suba, Laoag" loading="lazy"><figcaption>La Paz Sand Dunes at Suba, the Himala film location 20 minutes from Laoag city. The 4x4 run plus sandboarding takes about 45 minutes end to end.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Museo Ilocos Norte is a former tobacco warehouse turned into the regional museum, free entry. It explains the Ilocano cultural identity through the textiles (Ilocano-weaving traditions), the Tinguian-ethnic items from the highlands, and the Spanish-colonial history of Ilocos as a tobacco-monopoly region. Two hours covers the full exhibit.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/laoag-museo-ilocos-norte.jpg" alt="Museo Ilocos Norte regional museum in Laoag" loading="lazy"><figcaption>Museo Ilocos Norte, set inside a former Spanish-era tobacco warehouse. Two hours covers the textiles, Tinguian highland items, and tobacco-monopoly history.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Aurora Park is the riverside walk along the Padsan River, with the 1786 Tobacco Monopoly Memorial as the central marker. The Spanish colonial government established the tobacco monopoly in Ilocos in 1782, which made the region economically central for the next century and built much of the wealth you see in the heritage houses around Vigan. The memorial is a calm 15-minute stop, plus the park itself is a working public space with vendors and benches under the trees.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/laoag-tobacco-monopoly-monument-and-aurora-park.jpg" alt="Tobacco Monopoly Memorial and Aurora Park in Laoag" loading="lazy"><figcaption>The 1786 Tobacco Monopoly Memorial at Aurora Park along the Padsan River, the marker for the policy that built much of old Ilocos wealth.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Laoag empanada from the public market vendors is the breakfast or merienda order. The orange color comes from achuete and the filling is grated papaya, longganisa, and an egg. Saramsam Ylocano serves the longganisa-and-garlic-rice plate that locals consider the proper Ilocano breakfast. Bagnet at La Preciosa or Herencia is the lunch order. For pasalubong, dragon fruit shakes from the Refmad Dragon Fruit Farm in Burgos (30 minutes north) are the right roadside stop on the way out.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/laoag-laoag-empanada.jpg" alt="Laoag empanada with longganisa, papaya, and egg" loading="lazy"><figcaption>Laoag empanada from the public market vendors. Slightly different filling balance from the Vigan version, with a heavier longganisa hand.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 6. BARS IN LA UNION (SAN JUAN NIGHT SCENE)
    // ------------------------------------------------------------------
    'bars-in-la-union-san-juan-night-scene-guide' => [
        [
            'anchor' => '<p>Flotsam and Jetsam is the iconic boutique-surf hostel and beach bar of San Juan, the spot that put La Union on every travel Instagram in the country. The bar fronts Urbiztondo Beach, the cocktails are competent, and the live-music nights pull a real crowd. The vibe is mid-20s to mid-30s, mixed local and expat, and the music leans into reggae, indie, and the occasional DJ set on bigger weekends.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/san-juan-la-union-1.jpg" alt="Urbiztondo beachfront night scene in San Juan, La Union" loading="lazy"><figcaption>The Urbiztondo beachfront where Flotsam and Jetsam fronts the sand. Live music starts around 9 PM and runs late on Friday and Saturday.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>El Union is the surf-town landmark for daytime, but the early-evening hours pull a sit-down crowd that turns the cafe into a soft-bar setting until close. The pizzas are the real order; the local craft beers on tap are the surprise. The vibe is calmer than the beachfront bars, the kind of pre-game stop you do before walking down to the Urbiztondo strip later.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/san-juan-la-union-2.jpg" alt="El Union Coffee Co. corner of the San Juan surf strip" loading="lazy"><figcaption>The El Union Coffee corner of San Juan, the daytime landmark that pulls a soft-bar crowd in the early evening before the beachfront bars open up.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The Urbiztondo bar row is the cluster of post-surf bars that come alive after sunset. The Surf Shack, Vessel, and the open-air spots that change names every couple of years all sit within a 300-meter walk along the beachfront. The pattern is the same across all of them: cold beers, basic cocktails, surf-town food (sisig, nachos, calamares), and music that ranges from acoustic to DJ depending on the night.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/san-juan-la-union-urbiztondo-surf-beach.jpg" alt="Urbiztondo Beach, San Juan, La Union surf bar row" loading="lazy"><figcaption>The Urbiztondo beachfront at golden hour, where the 300-meter bar row picks up after the surfers rinse their boards.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Makai Bowls and the row of beachfront food stalls are the late-night snack stops between bars. The acai bowls and the tuna sashimi from the Urbiztondo grills do the job at 11 PM when you need food but not a full sit-down meal. The grills serve fresh fish from the morning catch; the tuna sashimi plate is the order most barkadas split.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/san-juan-la-union-tuna-sashimi-and-grilled-fish.jpg" alt="Tuna sashimi and grilled fish at Urbiztondo, San Juan" loading="lazy"><figcaption>Tuna sashimi and grilled fish from the Urbiztondo beachfront grills, the late-night plate that handles the 11 PM hunger between bars.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>San Miguel Pale Pilsen is the universal order at every bar in town. Local craft beers on tap at El Union and at a couple of the surf-shack bars are the more interesting drinks. For food, the late-night grills serve tuna sashimi, calamares, and sisig that handle the post-bar hunger. Breakfast the next morning is longganisa Vigan-style with sukang Iloko at any San Juan carinderia.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/san-juan-la-union-3.jpg" alt="San Juan La Union surf-town night scene" loading="lazy"><figcaption>The San Juan surf-town night, where San Miguel Pale Pilsen is the universal order and the carinderias serve the longganisa breakfast the morning after.</figcaption></figure>',
        ],
    ],

];
