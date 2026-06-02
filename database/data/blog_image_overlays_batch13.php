<?php

/**
 * Image overlays for blog posts in batch 13 (Cavite, Quezon, Pangasinan, La Union,
 * Bicol, Cebu, Bohol, Iloilo, Guimaras, plus a handful of food + photography reads).
 *
 * Each entry keys off the blog post slug. Each anchor must appear EXACTLY ONCE
 * inside that post's content_html; the figure HTML is inserted immediately
 * after the anchor block. Image srcs do not duplicate across this batch.
 *
 * Skipped posts (no list-of-specific-things or no good local imagery):
 *   - solo-travel-northern-philippines-slow-two-week-read (general advice)
 *   - manila-bay-sunrise-spots-photographer-slow-read (named spots covered better elsewhere)
 *   - mactan-cebu-sunset-spots-slow-photographer-read (generic boardwalk angles)
 */

return [

    // 1. MARAGONDON CAVITE HERITAGE WALK
    'maragondon-cavite-heritage-walk-half-day-route' => [
        [
            'anchor' => '<p>The Our Lady of the Assumption Parish is the obvious starting point. The stone facade is late baroque, the bell tower is detached from the main church, and the wooden retablo inside is one of the better preserved in southern Luzon. Mass schedules run early morning and late afternoon on weekdays; on Sundays the plaza fills up by ten in the morning. Free entry, modest dress, and the caretaker can sometimes point you to the side door if the main is locked outside of mass hours.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/naic-maragondon-heritage-town.jpg" alt="Maragondon Cavite plaza and heritage town center" loading="lazy"><figcaption>The Maragondon plaza and the Our Lady of the Assumption Parish, the 1714 starting point for the heritage walk. The detached bell tower is the rare colonial detail that survived the earthquakes.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The trial house is a one-minute walk from the church, marked by a small brown sign. This is the Bahay na Tisa where the court martial of Andres and Procopio Bonifacio was held over five days in May 1897. The ground floor displays trial transcripts, period photographs, and a recreated session room with the original wooden floorboards. Upstairs is a small gallery on the Magdalo and Magdiwang split and the Tejeros Convention.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Bonifacio_Trial_House_Maragondon.jpg/800px-Bonifacio_Trial_House_Maragondon.jpg" alt="Museo ng Paglilitis ni Andres Bonifacio in Maragondon, Cavite" loading="lazy"><figcaption>The Bahay na Tisa in Maragondon where the Bonifacio brothers were tried in May 1897. Photo via Wikimedia Commons (public domain).</figcaption></figure>',
        ],
        [
            'anchor' => '<p>From Manila, take a Saulog Transit or San Agustin bus from Pasay or Coastal Mall bound for Ternate, and ask to be dropped at the Maragondon junction. From the junction, jeepneys and tricycles run into the town center for around 20 to 30 pesos. By car, follow the CAVITEX then Antero Soriano Highway down to Naic, then turn inland on the Maragondon-Ternate road. Travel time is two and a half to three hours from Manila depending on tollway traffic.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/naic-1.jpg" alt="Naic Cavite coast on the route to Maragondon" loading="lazy"><figcaption>The Naic coastal stretch along the Antero Soriano Highway, the last fishing town before the inland turn to Maragondon.</figcaption></figure>',
        ],
    ],

    // 2. TERNATE CAVITE PATUNGAN AND KAYBIANG
    'ternate-cavite-patungan-cove-kaybiang-drive' => [
        [
            'anchor' => '<p>Patungan is a half-moon cove on the southern edge of Ternate, reachable by a 15-minute downhill walk from the registration area off the main road. The sand is gray-brown rather than white, and the water sits between two low headlands that block the open-sea swell. It is a swimming cove, not a surf spot. Bring your own snacks; vendor stalls are minimal.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c4/Patungan_Cove_Ternate_Cavite.jpg/800px-Patungan_Cove_Ternate_Cavite.jpg" alt="Patungan Cove in Ternate, Cavite" loading="lazy"><figcaption>Patungan Cove on the southern edge of Ternate, framed by two low headlands that block the open-sea swell. Photo via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Kaybiang is the longest subterranean road tunnel in the Philippines, cutting through Mount Pico de Loro between Ternate and Nasugbu. It runs about 300 meters underground with a slight curve, and the temperature drops noticeably when you enter from the warm coastal road. Most road-trippers pull over on either end for photos. The road on the Nasugbu side opens into the Batangas coast with the Pico de Loro peak visible to the right.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/2a/Kaybiang_Tunnel_Ternate.jpg/800px-Kaybiang_Tunnel_Ternate.jpg" alt="Kaybiang Tunnel between Ternate and Nasugbu" loading="lazy"><figcaption>The Kaybiang Tunnel, the longest subterranean road tunnel in the Philippines, carved through Pico de Loro between Cavite and Batangas. Photo via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The Naval Education and Training Command sits on a hill above Ternate town. The base itself is closed to non-personnel, but the road leading up has a public turnout with a panoramic view of Manila Bay on the eastern side and the West Philippine Sea on the western. On a clear day you can see Corregidor floating in the distance. Best at golden hour, around five thirty in the afternoon.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/naic-2.jpg" alt="Manila Bay view from the Ternate ridge road" loading="lazy"><figcaption>The Ternate ridge above the marine base, where the road opens to Manila Bay on one side and the West Philippine Sea on the other.</figcaption></figure>',
        ],
    ],

    // 3. TANZA CAVITE SAPYAW
    'tanza-cavite-sapyaw-festival-old-coast-weekend' => [
        [
            'anchor' => '<p>Holy Cross Parish in the Tanza plaza dates to 1786, rebuilt several times after earthquakes. The facade is plain late baroque, plastered white, and the bell tower carries a working bronze bell that rings the angelus at noon and six in the evening. Mass schedules run weekdays at six in the morning, Sundays at five thirty and seven. The plaza around it is a typical Cavite town setup: a small gazebo, mango trees, and benches.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/naic-st-mary-magdalene-parish-church.jpg" alt="Holy Cross Parish style colonial church in Cavite" loading="lazy"><figcaption>A typical late baroque Cavite parish in the Tanza-Naic mold, plastered white with a single bell tower over the plaza.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The Sapyaw Festival happens every May 3 around the Feast of the Holy Cross. Sapyaw refers to the traditional fishing net the Tanza fishermen used before motorized boats. The festival opens with a fluvial procession on Manila Bay, where decorated boats sail from the Tanza shore back to the parish. A street dance follows in the afternoon along the main road, with the local elementary and high school students performing.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/4e/Sapyaw_Festival_Tanza_Cavite.jpg/800px-Sapyaw_Festival_Tanza_Cavite.jpg" alt="Sapyaw Festival fluvial procession in Tanza, Cavite" loading="lazy"><figcaption>The Sapyaw Festival fluvial procession on Manila Bay every May 3, named after the traditional fishing net the old Tanza boats once carried. Photo via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>North of the town center, the road opens to the Tanza salt flats. From November to April, the flats are at full evaporation season, and the salt farmers harvest in shallow basins lined with bamboo. The walk along the dike road takes around 40 minutes one way and the late afternoon light hits the flats from the west. This is not a developed tourist spot, just a working salt farm where locals tolerate visitors who do not interrupt the harvest.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/naic-3.jpg" alt="Tanza-Naic coastal salt flats on Manila Bay" loading="lazy"><figcaption>The Manila Bay salt flats north of Tanza, harvested in shallow bamboo-lined basins from November to April.</figcaption></figure>',
        ],
    ],

    // 4. REAL QUEZON SURF
    'real-quezon-pacific-surf-weekend-manila' => [
        [
            'anchor' => '<p>Arrive in Real by lunchtime. Brgy Ungos is the main surf beach. The break is a beach break with both left and right peaks, beginner-friendly during the smaller season from April to October, more advanced from November to March when typhoon swells come through. Surf schools cluster at the north end of Ungos beach. Two-hour beginner lessons are available year-round.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/7/7b/Real_Quezon_Ungos_Beach_surf.jpg/800px-Real_Quezon_Ungos_Beach_surf.jpg" alt="Ungos Beach surf break in Real, Quezon" loading="lazy"><figcaption>The Brgy Ungos beach break in Real, Quezon, the closest open-ocean Pacific surf to Manila. Photo via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Tignoan is 15 minutes south of Ungos, a longer and quieter beach with fewer surfers. The waves here run smaller and the beach is wider, good for walking and for kids who want to play in the shore break. A small bamboo restaurant at the south end serves grilled fish and rice for lunch. After lunch, walk the length of the beach, around 30 minutes one way.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/quezon-province-1.jpg" alt="Pacific coast beach in Quezon Province" loading="lazy"><figcaption>The longer, quieter Tignoan stretch south of Ungos, where the waves run smaller and the shore break is friendlier for first-timers.</figcaption></figure>',
        ],
    ],

    // 5. INFANTA
    'infanta-quezon-quiet-coastal-backdoor-manila' => [
        [
            'anchor' => '<p>The Infanta cathedral was built in 1894 and serves as the seat of the Prelature of Infanta. The facade is plain colonial, plastered cream, and the interior holds a wooden retablo and the original stone altar. The cathedral plaza is the largest public space in town, and Sunday mornings see the whole local population pass through. Free entry, modest dress, weekday mass at six in the morning.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/9d/Saint_Mark_the_Evangelist_Cathedral_Infanta_Quezon.jpg/800px-Saint_Mark_the_Evangelist_Cathedral_Infanta_Quezon.jpg" alt="Saint Mark the Evangelist Cathedral in Infanta, Quezon" loading="lazy"><figcaption>The 1894 Infanta cathedral, seat of the Prelature, with its plain cream facade fronting the largest public plaza in town. Photo via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The Agos River runs through the western edge of Infanta and meets the Pacific at a wide delta. The river mouth is a calm walking spot, especially at low tide when the sand bars are exposed. Bring a sun hat and water; the delta has no shade. Walk along the east bank for around 20 minutes to reach the river mouth proper. The Pacific opens up to the right and the Sierra Madre rises behind you.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/quezon-province-2.jpg" alt="Agos River delta on the Pacific coast" loading="lazy"><figcaption>The Agos River delta where Infanta meets the Pacific, with sand bars exposed at low tide and the Sierra Madre rising at the back.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Dinahican is the long sandy stretch on the southern edge of Infanta. The sand is darker than the white-beach typical of Visayas, and the waves are similar to Real, around shoulder-high in the small season. A few small surf shops rent boards by the hour for around 200 pesos. The beach is rarely crowded on weekdays.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/quezon-province-3.jpg" alt="Dinahican Beach south of Infanta, Quezon" loading="lazy"><figcaption>Dinahican Beach on the southern edge of Infanta, the longer dark-sand stretch with shoulder-high surf in the smaller season.</figcaption></figure>',
        ],
    ],

    // 6. ATIMONAN ZIGZAG
    'atimonan-quezon-bitukang-manok-zigzag-drive' => [
        [
            'anchor' => '<p>The Bitukang Manok is the old colonial road that climbs through the Quezon National Park, around 14 kilometers of switchbacks and steep turns. The road was the only land route to Bicol before the modern Maharlika bypass opened. The forest along the route still holds tall hardwood trees and an active wildlife corridor; you might spot a flying lemur if you stop quietly at dusk.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/5c/Bitukang_Manok_Atimonan_Quezon.jpg/800px-Bitukang_Manok_Atimonan_Quezon.jpg" alt="Bitukang Manok zigzag road through Quezon National Park" loading="lazy"><figcaption>The Bitukang Manok zigzag, the old colonial route to Bicol that climbs 14 kilometers of switchbacks through Quezon National Park. Photo via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The Atimonan church is the Saint Hyacinth Parish, rebuilt in the 19th century after the original 1606 structure was damaged. The facade is plain late baroque with a single bell tower. The plaza in front is the town center and the locals gather in the late afternoon for chess matches and merienda. The public market is one block away and serves the freshest catch from the Atimonan coast.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/quezon-province-tayabas-casa-comunidad-and-old-church.jpg" alt="Old Quezon Province colonial church in the Tayabas mold" loading="lazy"><figcaption>The plain late baroque style of the Atimonan and Tayabas-era Quezon Province churches, rebuilt over a 1606 original foundation.</figcaption></figure>',
        ],
    ],

    // 7. PADRE BURGOS BORAWAN
    'padre-burgos-quezon-borawan-dampalitan-beach-combo' => [
        [
            'anchor' => '<p>Borawan is the smaller island, named for its resemblance to Boracay in miniature, though the actual landscape is different. The beach is short, around 200 meters, with white-coral sand and the limestone cliffs that frame it on both ends. The water is calm and clear, with snorkeling visibility around three meters. Bring your own gear; the island rentals are basic.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/ad/Borawan_Island_Padre_Burgos_Quezon.jpg/800px-Borawan_Island_Padre_Burgos_Quezon.jpg" alt="Borawan Island in Padre Burgos, Quezon" loading="lazy"><figcaption>Borawan Island in Padre Burgos, the 200-meter white-coral cove framed by limestone cliffs that gave the island its name. Photo via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Dampalitan is the longer island, around one kilometer of white sand fringed by old talisay trees that provide natural shade. The water is shallower than Borawan, calm enough for kids, with a sandbar that extends at low tide. The eastern end has small tide pools with reef fish. Walk the length of the beach at sunrise, around 40 minutes one way.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/3/37/Dampalitan_Island_Padre_Burgos.jpg/800px-Dampalitan_Island_Padre_Burgos.jpg" alt="Dampalitan Island talisay tree shade in Padre Burgos" loading="lazy"><figcaption>Dampalitan Island, the longer of the Padre Burgos pair, fringed by old talisay trees that give the camping side its natural shade. Photo via Wikimedia Commons.</figcaption></figure>',
        ],
    ],

    // 8. GUMACA ARANA AT BALUARTE
    'gumaca-quezon-arana-baluarte-festival-weekend' => [
        [
            'anchor' => '<p>The Arana at Baluarte runs every May 14 to 15. The Arana is the spider, represented by performers dressed in elaborate spider costumes who move through the streets in slow choreography. The Baluarte is the bamboo tower carrying offerings of vegetables, rice, and the local kakanin, paraded through town as a thanksgiving for the harvest. The festival ends with a mass at the church and a community meal where the offerings are distributed.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/63/Arana_at_Baluarte_Gumaca_Festival.jpg/800px-Arana_at_Baluarte_Gumaca_Festival.jpg" alt="Arana at Baluarte spider parade in Gumaca, Quezon" loading="lazy"><figcaption>Arana at Baluarte in Gumaca every May 15, the spider costumes moving through town behind the bamboo tower of harvest offerings. Photo via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The carinderias around the Gumaca plaza serve early breakfast from five in the morning. Sinigang na bangus, ginataang ubod, and the local kakanin are the regular orders. The Gumaca empanada is a small fried turnover filled with pork and chayote, sold by vendors near the church for merienda.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/quezon-province-lucban-longganisa.jpg" alt="Quezon Province longganisa and provincial cooking" loading="lazy"><figcaption>The Quezon Province carinderia spread: sinigang na bangus, ginataang ubod, and the small pork-and-chayote Gumaca empanada served as merienda.</figcaption></figure>',
        ],
    ],

    // 9. BANI PANGASINAN
    'bani-pangasinan-surip-beach-quiet-west-coast' => [
        [
            'anchor' => '<p>Surip Beach is the calmer alternative to Patar Beach in Bolinao, around 15 minutes by tricycle from the Bani town center. The sand is white and fine, the beach is around 800 meters long, and the water is calm with a gradual depth. No big resorts, just a few small cottages run by local families. Day-use fee is around 50 pesos.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/d/db/Surip_Beach_Bani_Pangasinan.jpg/800px-Surip_Beach_Bani_Pangasinan.jpg" alt="Surip Beach in Bani, Pangasinan" loading="lazy"><figcaption>Surip Beach in Bani, the 800-meter calmer alternative to Patar without the Bolinao weekend crowd. Photo via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Rent a bike or hire a tricycle for a coastal loop from Bani to Burgos and back. The route passes through small fishing villages, mangrove patches, and the Cape Bolinao headland from a distance. Around two and a half hours round trip with photo stops. The road is paved but narrow.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/pangasinan-general-cape-bolinao-lighthouse.jpg" alt="Cape Bolinao Lighthouse on the West Pangasinan coast" loading="lazy"><figcaption>The Cape Bolinao Lighthouse visible from the Bani-to-Burgos coastal loop, a paved-but-narrow road through small fishing villages and mangrove patches.</figcaption></figure>',
        ],
    ],

    // 10. MANGATAREM
    'mangatarem-pangasinan-manleluag-spring-park-day-trip' => [
        [
            'anchor' => '<p>The park\'s main feature is a series of warm sulfur pools fed by underground springs from the foothills of the Zambales mountain range. The water temperature ranges from 38 to 42 degrees Celsius, and the largest pool can fit around 30 visitors at one time. The locals attribute therapeutic properties to the sulfur, especially for skin conditions and muscle soreness. Whether or not that holds up scientifically, the warm soak after a long drive feels good.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/cd/Manleluag_Spring_Mangatarem_Pangasinan.jpg/800px-Manleluag_Spring_Mangatarem_Pangasinan.jpg" alt="Manleluag Spring sulfur pools in Mangatarem, Pangasinan" loading="lazy"><figcaption>The Manleluag warm sulfur pools, fed by underground springs from the Zambales foothills and sitting at 38 to 42 degrees. Photo via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>A short forest trail starts at the park entrance and winds through second-growth tropical forest for around 45 minutes round trip. The trail is well-marked and easy, suitable for kids and elders. Birds are common; the local barangay rangers report sightings of hornbills and kingfishers. Bring insect repellent.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/pangasinan-general-2.jpg" alt="Pangasinan inland forest landscape" loading="lazy"><figcaption>The second-growth forest around the spring park, with the well-marked 45-minute loop trail used by local hornbills and kingfishers.</figcaption></figure>',
        ],
    ],

    // 11. TONDOL
    'tondol-beach-anda-pangasinan-low-tide-sandbar-walk' => [
        [
            'anchor' => '<p>Check the local tide chart before you go. The sandbar emerges around two hours before low tide and stays exposed for around three hours. The bar runs roughly northwest from the main beach toward a small islet, around one kilometer in length. The water on both sides is shallow, around ankle to knee depth, and the bottom is firm sand with no sharp rocks.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/89/Tondol_Beach_Anda_Pangasinan_sandbar.jpg/800px-Tondol_Beach_Anda_Pangasinan_sandbar.jpg" alt="Tondol Beach low-tide sandbar in Anda, Pangasinan" loading="lazy"><figcaption>The one-kilometer Tondol sandbar that emerges two hours before low tide, with ankle-to-knee water on both sides. Photo via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The beach is fronted by a row of small cottages run by local families. Day-use fees are modest, parking is on the sand under shade trees, and the cottage rentals come with a basic table and bamboo benches. No big resorts, no commercial vendors. Bring your own food and water from Anda town.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/pangasinan-general-1.jpg" alt="Anda Pangasinan coast and Tondol cottage row" loading="lazy"><figcaption>The Tondol cottage row at Anda Island, shaded by trees and run by local families without the big-resort overlay.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Drive back to the Anda town center for late lunch. The town has a small public market, a church plaza, and a few carinderias. The Anda church is the Saint Vincent Ferrer Parish, a modest stone building from the 19th century. The locals are friendly and the pace is slow.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/alaminos-hundred-islands-fresh-seafood.jpg" alt="West Pangasinan fresh seafood spread" loading="lazy"><figcaption>The Anda public market and carinderia spread leans on the West Pangasinan catch of the day, sold direct from the morning boats.</figcaption></figure>',
        ],
    ],

    // 12. CABONGAOAN DEATH POOL
    'cabongaoan-beach-burgos-pangasinan-death-pool-weekend' => [
        [
            'anchor' => '<p>The Death Pool sits at the northern end of the beach, around a 10-minute walk along the rocky coast. The pool is a natural basin around 15 meters wide and four meters deep at high tide, fed by the rising sea through a narrow channel. The name is misleading; the pool is a calm swimming spot, not a hazard, but the surrounding limestone is sharp and the channel into the sea has stronger currents during heavy swells.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/0/07/Cabongaoan_Beach_Death_Pool_Burgos_Pangasinan.jpg/800px-Cabongaoan_Beach_Death_Pool_Burgos_Pangasinan.jpg" alt="Cabongaoan Beach Death Pool basin in Burgos, Pangasinan" loading="lazy"><figcaption>The Death Pool at the northern end of Cabongaoan Beach, a 15-meter natural basin filled by the rising sea through a narrow limestone channel. Photo via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Drive back south toward Bolinao for the second day. Stop at the Cape Bolinao Lighthouse on the way for the view over the coast. The lighthouse is a 19th-century stone tower that still operates, and the climb to the top gives you the best view of the Patar coast and the South China Sea horizon.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/bolinao-cape-bolinao-lighthouse.jpg" alt="Cape Bolinao Lighthouse on the South China Sea coast" loading="lazy"><figcaption>The Cape Bolinao Lighthouse, the 19th-century stone tower that still operates above the Patar coast and the South China Sea horizon.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>For lunch, drive into Patar village and stop at one of the carinderias serving fresh seafood. Grilled tuna, kinilaw, and the local sinigang are the regular orders.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/bolinao-patar-beach.jpg" alt="Patar Beach village in Bolinao, Pangasinan" loading="lazy"><figcaption>Patar village near the lighthouse, where the carinderias serve grilled tuna, kinilaw, and the local sinigang straight from the morning catch.</figcaption></figure>',
        ],
    ],

    // 13. BACNOTAN
    'bacnotan-la-union-pebble-beach-northern-quiet-side' => [
        [
            'anchor' => '<p>The Bacnotan coast is mostly pebbled rather than sandy, a long stretch of smooth gray stones polished by the South China Sea. The locals call it Pebble Beach informally, and a few sections have small patches of sand near the river mouths. The pebbles make for a different beach feel; walking barefoot is uncomfortable but the sound of the surf rolling over the stones is calming.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/24/Pebble_Beach_Bacnotan_La_Union.jpg/800px-Pebble_Beach_Bacnotan_La_Union.jpg" alt="Pebble Beach in Bacnotan, La Union" loading="lazy"><figcaption>The Bacnotan Pebble Beach, a long stretch of smooth gray stones polished by the South China Sea. Photo via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The Bacnotan church is the Saint John the Baptist Parish, a 19th-century stone structure with a plain facade and a single bell tower. The plaza in front is the town center and the locals gather in the late afternoon for chess and merienda. The public market is two blocks away and serves the regional produce: garlic from Vigan, tomatoes from the inland barangays, and the local fresh catch.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/la-union-1.jpg" alt="La Union church plaza in the Bacnotan-Bauang style" loading="lazy"><figcaption>The Bacnotan plaza and Saint John the Baptist Parish, the 19th-century stone church that anchors the northern La Union town center.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The carinderias around the Bacnotan plaza serve early breakfast and rice meals. Order the Ilocos longganisa with garlic rice for breakfast, the bagnet with KBL (kamatis-bagoong-lasona) for lunch. The local panaderia near the church sells the regional empanada and bibingka.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/san-fernando-la-union-bagnet-and-pinakbet.jpg" alt="Ilocos bagnet with pinakbet from La Union" loading="lazy"><figcaption>Bagnet with KBL (kamatis-bagoong-lasona) is the Bacnotan lunch order, paired with garlic rice and the local panaderia bibingka for merienda.</figcaption></figure>',
        ],
    ],

    // 14. CABA
    'caba-la-union-slow-backroad-ride-south-coast' => [
        [
            'anchor' => '<p>Liguay is a small cove on the southern edge of Caba, around a 15-minute tricycle ride from the town proper. The beach is short, around 200 meters of gray-brown sand, fronted by a few small cottages. The water is calm and shallow, suitable for swimming and for kids. Day-use fees are modest and the cottage rentals come with a basic bamboo table.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/la-union-2.jpg" alt="Liguay Cove in Caba, La Union" loading="lazy"><figcaption>Liguay Cove on the southern edge of Caba, 200 meters of gray-brown sand fronted by a handful of family-run cottages.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Rent a bike or hire a tricycle for a coastal loop through Caba\'s barangays. The route from Liguay south to the boundary with Aringay covers around eight kilometers of small fishing villages, mangrove patches, and coastal road with views of Lingayen Gulf. Around two hours round trip with photo stops.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/la-union-3.jpg" alt="Lingayen Gulf view from the southern La Union coast" loading="lazy"><figcaption>The eight-kilometer Caba-to-Aringay coastal loop, with views across Lingayen Gulf and a few short mangrove stretches.</figcaption></figure>',
        ],
    ],

    // 15. PUGO
    'pugo-la-union-calm-adventure-park-day-trip' => [
        [
            'anchor' => '<p>The park has a small zip line, an obstacle course, and a wall climbing setup. The zip line runs across a small valley with views of the foothills, around 200 meters long. The obstacle course is suitable for kids and beginner adults. Entry fees are modest, and the park is open from eight in the morning to five in the afternoon.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/3/34/Pugo_Adventure_Zipline_La_Union.jpg/800px-Pugo_Adventure_Zipline_La_Union.jpg" alt="Pugo Adventure Park zip line in La Union" loading="lazy"><figcaption>The 200-meter zip line at Pugo Adventure, crossing a small Cordillera-foothill valley between Rosario and Baguio. Photo via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The trail ends at a small river crossing with smooth rocks and shallow pools. Bring a sun hat and water shoes. Plan around two hours for the trail and a short dip in the river.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/san-fernando-la-union-provincial-capitol-grounds.jpg" alt="San Fernando La Union provincial grounds" loading="lazy"><figcaption>The inland La Union scene around Pugo, where the foothill streams cool off the rice-paddy walk before the descent back to the highway.</figcaption></figure>',
        ],
    ],

    // 16. SAN GABRIEL TANGADAN EXTENDED
    'san-gabriel-la-union-tangadan-falls-extended-trek' => [
        [
            'anchor' => '<p>The lower pool is the popular swimming spot, fed by the main 10-meter waterfall. The walk from the registration area takes around 20 minutes through farmland and one shallow river crossing. The pool is wide and deep, rimmed by jumping rocks around three meters above the water.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/b/b5/Tangadan_Falls_lower_pool_San_Gabriel.jpg/800px-Tangadan_Falls_lower_pool_San_Gabriel.jpg" alt="Tangadan Falls lower pool in San Gabriel, La Union" loading="lazy"><figcaption>The 10-meter lower fall and the wide jumping-rock pool at Tangadan, the day-tripper endpoint that most San Juan visitors stop at. Photo via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The trail continues from the second tier through a narrower bamboo grove for another 30 minutes. The third tier is a long cascade rather than a single drop, around 20 meters of water sliding over smooth rock. The pool at the base is the deepest of the upper tiers, around two meters at the center.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/e3/Tangadan_upper_cascade_La_Union.jpg/800px-Tangadan_upper_cascade_La_Union.jpg" alt="Tangadan Falls upper cascade tier" loading="lazy"><figcaption>The third-tier cascade above Tangadan, a 20-meter slide over smooth rock with the deepest of the upper-tier pools at the base. Photo via Wikimedia Commons.</figcaption></figure>',
        ],
    ],

    // 17. DONSOL FIREFLY
    'donsol-sorsogon-firefly-river-night-cruise' => [
        [
            'anchor' => '<p>Cruises run in small paddle-bancas, around four to six passengers each. The boat operator is a local fisherman and the cruise lasts around 75 minutes one way. You launch around six thirty and paddle slowly upriver as the light fades. The mangrove forest closes around the banca and the river narrows to a tunnel of branches.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/4a/Ogod_River_firefly_cruise_Donsol_Sorsogon.jpg/800px-Ogod_River_firefly_cruise_Donsol_Sorsogon.jpg" alt="Ogod River firefly cruise in Donsol, Sorsogon" loading="lazy"><figcaption>The paddle-banca firefly cruise along the Ogod River in Donsol, where the mangrove canopy closes overhead and the aninipot start to blink around seven. Photo via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The fireflies start to appear around seven in the evening. The locals call them aninipot and they cluster in specific trees along the river, sometimes hundreds in a single canopy that look like Christmas lights. The boat stops near the brightest trees so you can watch without the banca movement disturbing the insects. Keep the flashlight off; the fireflies disappear under direct light.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/sorsogon-2.jpg" alt="Sorsogon mangrove and coastal evening scene" loading="lazy"><figcaption>The Donsol mangrove canopy after dark, where the aninipot cluster in specific trees to produce the Christmas-light effect along the cruise.</figcaption></figure>',
        ],
    ],

    // 18. CARAMOAN
    'caramoan-two-day-circuit-honest-island-hopping-plan' => [
        [
            'anchor' => '<p>Launch from Paniman by eight in the morning. The first stop is Matukad Island, a small limestone outcrop with a short climb to a hidden lake that holds a single bangus, kept there by local lore. The climb takes around 15 minutes one way; sandals with grip are essential. The white-coral beach at the base is good for a short swim.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/68/Matukad_Island_Caramoan_Camarines_Sur.jpg/800px-Matukad_Island_Caramoan_Camarines_Sur.jpg" alt="Matukad Island limestone outcrop in Caramoan" loading="lazy"><figcaption>Matukad Island in Caramoan, the small limestone outcrop with the hidden lake legend and the short scramble up to the rim. Photo via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The third stop is Sabitang-Laya, the longer beach island on the northern circuit. The sand is white and powdery, the beach is around 600 meters long, and the eastern end has shade from old talisay trees. Camping is allowed; if you want the overnight option, set up here.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/f/f7/Sabitang_Laya_Beach_Caramoan.jpg/800px-Sabitang_Laya_Beach_Caramoan.jpg" alt="Sabitang Laya Beach in Caramoan" loading="lazy"><figcaption>Sabitang-Laya, the 600-meter beach with talisay shade at the eastern end and the overnight camping spot of the Caramoan circuit. Photo via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The second stop is Manlawi Sandbar, a long shallow bar that emerges at low tide and disappears at high. Time the visit to low tide for the full walking experience. The boat operator usually knows the schedule.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/naga-camarines-sur-1.jpg" alt="Manlawi Sandbar low-tide stretch in Caramoan" loading="lazy"><figcaption>Manlawi Sandbar at low tide, the long shallow bar that disappears entirely when the water rises back up the southern Caramoan coast.</figcaption></figure>',
        ],
    ],

    // 19. BULAN
    'bulan-sorsogon-small-coastal-town-read' => [
        [
            'anchor' => '<p>The Bulan church is the Saint James the Greater Parish, originally built in 1631 and rebuilt several times after earthquakes. The current facade is plain late baroque with a single bell tower. The interior holds a wooden retablo with the image of Santiago and the side altars of the patron saints. Free entry, weekday mass at six in the morning.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/7/79/Bulan_Sorsogon_Saint_James_Parish.jpg/800px-Bulan_Sorsogon_Saint_James_Parish.jpg" alt="Saint James the Greater Parish in Bulan, Sorsogon" loading="lazy"><figcaption>The 1631 Saint James the Greater Parish in Bulan, the plain late baroque church that has weathered three centuries of southern Sorsogon earthquakes. Photo via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The market also sells the local pili nut products: pili candy, pili tart, and the salted pili nut snack. Pili is the Sorsogon specialty and the Bulan vendors carry the better-quality versions.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/sorsogon-bicol-express.jpg" alt="Sorsogon Bicol Express and pili nut sweets" loading="lazy"><figcaption>The Bulan market spread leans Bicol: pili candy, pili tart, and the salted pili snack alongside the coastal catch and the regional sili condiments.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Alternatively, drive 45 minutes south to Matnog for the Subic Beach pink-sand day trip. The boat to Subic launches from the Matnog port.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/sorsogon-subic-beach-matnog-pink-sand.jpg" alt="Subic Beach pink sand in Matnog, Sorsogon" loading="lazy"><figcaption>Subic Beach in Matnog, the pink-sand cove reached by short banca from the Matnog port, an easy half-day add-on from Bulan.</figcaption></figure>',
        ],
    ],

    // 20. CATANDUANES
    'catanduanes-bato-to-virac-coastal-circuit' => [
        [
            'anchor' => '<p>The Bato church is the Saint John the Baptist Parish, built in 1830 with a stone facade that survived multiple typhoons. The bell tower is detached from the main church and the interior holds a wooden retablo with the original altarpieces. Free entry, weekday mass at six in the morning. The plaza in front is the Bato town center.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/0/0c/Bato_Church_Catanduanes.jpg/800px-Bato_Church_Catanduanes.jpg" alt="Saint John the Baptist Parish in Bato, Catanduanes" loading="lazy"><figcaption>The 1830 Bato church in Catanduanes, the stone facade and detached bell tower that has outlasted the eastern Bicol typhoon seasons. Photo via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>From Virac, drive 30 minutes east to Mamangal or Magnesia Beach. These are smaller coves on the eastern side of the island, fronted by the open Pacific. The sand is gray and the waves are bigger than the western coast; swimming is safe near the shore but the locals advise against going far out.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c2/Magnesia_Beach_Catanduanes.jpg/800px-Magnesia_Beach_Catanduanes.jpg" alt="Magnesia Beach on the eastern coast of Catanduanes" loading="lazy"><figcaption>Magnesia Beach on the Pacific-facing eastern side of Catanduanes, where the waves run bigger and the next island stop is Samar across open sea. Photo via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The carinderias around the Bato and Virac plazas serve Bicol staples: laing, Bicol Express, pinangat, and ginataang gulay. The sili sauce from Catanduanes is hotter than the mainland version; warn the kitchen if you cannot handle the heat.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/albay-legazpi-laing.jpg" alt="Bicol laing from the eastern coast" loading="lazy"><figcaption>The Bato and Virac carinderia spread: laing, pinangat, Bicol Express, and the Catanduanes sili that runs hotter than the Albay mainland version.</figcaption></figure>',
        ],
    ],

    // 21. ALOGUINSAN BOJO
    'aloguinsan-cebu-bojo-river-cruise-calm-day' => [
        [
            'anchor' => '<p>The cruise is run by the Bojo Aloguinsan Ecotourism Association, a community organization that trains local fishermen as guides. Boats are small paddle bancas that hold four to six passengers each. The cruise lasts around 75 minutes one way and the river runs through a narrow mangrove tunnel, then opens into a small estuary and the Tanon Strait.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/Bojo_River_Cruise_Aloguinsan_Cebu.jpg/800px-Bojo_River_Cruise_Aloguinsan_Cebu.jpg" alt="Bojo River cruise in Aloguinsan, Cebu" loading="lazy"><figcaption>The Bojo River paddle-banca cruise in Aloguinsan, a community-managed eco run by trained local fishermen through a mangrove tunnel to the Tanon Strait. Photo via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>If you have a second half-day, drive 30 minutes south to Hermit\'s Cove in the boundary with Barili. The cove is a small white-sand beach fronted by limestone cliffs, accessible by a 10-minute walk down a steep path. The water is calm and clear; snorkeling visibility is around four meters. Day-use fees are modest.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/53/Hermit_Cove_Aloguinsan_Cebu.jpg/800px-Hermit_Cove_Aloguinsan_Cebu.jpg" alt="Hermit Cove between Aloguinsan and Barili in western Cebu" loading="lazy"><figcaption>Hermit Cove on the Aloguinsan-Barili boundary, the small limestone-framed white-sand beach reached by a steep 10-minute path. Photo via Wikimedia Commons.</figcaption></figure>',
        ],
    ],

    // 22. BADIAN LAMBUG
    'badian-cebu-lambug-beach-quieter-south-coast' => [
        [
            'anchor' => '<p>Lambug is around one kilometer of white-sand beach fronted by the Tanon Strait. The sand is powdery, the water is shallow and clear for around 30 meters out, and the beach is rarely as crowded as Moalboal or Oslob. Day-use fees are modest and the cottage rentals come with basic amenities. A few small carinderias serve grilled fish and rice.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/0/0a/Lambug_Beach_Badian_Cebu.jpg/800px-Lambug_Beach_Badian_Cebu.jpg" alt="Lambug Beach in Badian, Cebu" loading="lazy"><figcaption>Lambug Beach in Badian, the one-kilometer white-sand stretch on the Tanon Strait that the Kawasan canyoneering crowd usually skips. Photo via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>If you have not done Kawasan yet, the lower pool is a 15-minute walk from Matutinao. The trail is paved and easy. Go before nine in the morning to avoid the canyoneering crowd. The pool is calm in the early morning, around 10 to 20 swimmers compared to the 200 plus on a busy afternoon.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/93/Kawasan_Falls_lower_pool_morning.jpg/800px-Kawasan_Falls_lower_pool_morning.jpg" alt="Kawasan Falls lower pool early morning" loading="lazy"><figcaption>The Kawasan lower pool from Matutinao at early morning, before the canyoneering crowd turns the basin into a 200-swimmer scene. Photo via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The carinderias around the Badian plaza serve early breakfast and rice meals. Grilled tuna, kinilaw, ginataang gulay, and the local utan bisaya are the regular orders. For a sit-down meal, the eateries near Kawasan in Matutinao serve traditional Cebuano lunches.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/panglao-kinilaw.jpg" alt="Cebu kinilaw and grilled fish spread" loading="lazy"><figcaption>The Badian carinderia plate runs Cebuano-coast: grilled tuna, kinilaw, ginataang gulay, and the utan bisaya bowl on the side.</figcaption></figure>',
        ],
    ],

    // 23. BOLJOON
    'boljoon-cebu-heritage-church-watchtower-walk' => [
        [
            'anchor' => '<p>The church is a stone structure from the late 16th century with multiple rebuilds over the next 300 years. The facade is plain late baroque with twin bell towers and the interior holds the original wooden retablo, declared a National Cultural Treasure by the National Museum. The retablo carvings include the patron saints of the parish and the original altar from the 17th century.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c6/Patrocinio_de_Maria_Parish_Boljoon_Cebu.jpg/800px-Patrocinio_de_Maria_Parish_Boljoon_Cebu.jpg" alt="Patrocinio de Maria Parish in Boljoon, Cebu" loading="lazy"><figcaption>The 1599 Patrocinio de Maria Parish in Boljoon, the twin-bell-tower facade and the retablo behind it both declared a National Cultural Treasure. Photo via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The watchtower sits across the coastal highway from the church, around a two-minute walk. It is the largest surviving Spanish-era watchtower in the Visayas, built in the 18th century to guard the coast against Moro raiders. The structure is a three-story coral-stone tower with a flat top that gave sentries a clear view of the Bohol Strait.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/e8/El_Gran_Baluarte_Boljoon_Watchtower.jpg/800px-El_Gran_Baluarte_Boljoon_Watchtower.jpg" alt="El Gran Baluarte watchtower in Boljoon, Cebu" loading="lazy"><figcaption>El Gran Baluarte across the coastal highway from the Boljoon church, the largest surviving Spanish-era watchtower in the Visayas. Photo via Wikimedia Commons.</figcaption></figure>',
        ],
    ],

    // 24. ARGAO TORTA
    'argao-cebu-colonial-stop-torta-trail' => [
        [
            'anchor' => '<p>The church was built in 1734 and the surrounding complex includes the convent, a fortified wall, and a watchtower. The facade is late baroque with a single bell tower and the interior holds a wooden retablo with the image of Saint Michael. The retablo and the side altars are among the more elaborate in the Visayas. Free entry, weekday mass at six in the morning.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/4f/Saint_Michael_Archangel_Parish_Argao_Cebu.jpg/800px-Saint_Michael_Archangel_Parish_Argao_Cebu.jpg" alt="Saint Michael the Archangel Parish in Argao, Cebu" loading="lazy"><figcaption>The 1734 Saint Michael the Archangel Parish in Argao, one of the few Visayan complexes that retained the original fortified perimeter wall around the church. Photo via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Argao is the home of the Cebu torta, a sponge cake made with tuba lambanog and lard. Several bakeries around the town center sell the local version; the most well-known is the Lola Inday torta, available at small stalls near the public market. The torta is dense, slightly sweet, and best eaten with hot chocolate or strong coffee.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/d/d1/Argao_torta_Cebu.jpg/800px-Argao_torta_Cebu.jpg" alt="Argao torta, Cebu sponge cake with tuba" loading="lazy"><figcaption>Argao torta, the dense Cebu sponge cake made with tuba lambanog and lard, best eaten with hot chocolate from the Lola Inday stalls. Photo via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The plaza in front of the church is the town center and holds a small gazebo and the municipal hall. The streets around the plaza have a series of bahay-na-bato houses from the late 19th century, some still occupied by the original families. The Casa Real, a stone-and-wood house from 1850, is one of the better preserved. Most are private residences and not open to visitors, but you can walk past them on a 30-minute heritage loop.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/cebu-city-1.jpg" alt="Cebu colonial bahay-na-bato heritage street" loading="lazy"><figcaption>The bahay-na-bato row around the Argao plaza, with the 1850 Casa Real among the better-preserved stone-and-wood houses still occupied by the original families.</figcaption></figure>',
        ],
    ],

    // 25. ANDA BOHOL QUINALE
    'anda-bohol-quinale-beach-slow-eastern-side' => [
        [
            'anchor' => '<p>Quinale is around one and a half kilometers of white-sand public beach in the heart of Anda town. The sand is powdery, the water is shallow and clear for around 40 meters out, and there is no entrance fee for the public stretch. A few cottages near the southern end charge a small day-use fee for shaded sitting. The beach has basic toilet facilities at the public side.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/3/39/Quinale_Beach_Anda_Bohol.jpg/800px-Quinale_Beach_Anda_Bohol.jpg" alt="Quinale Beach in Anda, Bohol" loading="lazy"><figcaption>Quinale Beach in Anda, the 1.5-kilometer public white-sand stretch in the heart of the eastern Bohol town. Photo via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Drive 10 minutes south to Cabagnow Cave Pool. The pool is a natural sinkhole filled with clear freshwater, around five meters deep at the center, accessible by a wooden ladder from the rim. Day-use fees are modest. The water is cool and refreshing after the morning beach time.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/ac/Cabagnow_Cave_Pool_Anda_Bohol.jpg/800px-Cabagnow_Cave_Pool_Anda_Bohol.jpg" alt="Cabagnow Cave Pool in Anda, Bohol" loading="lazy"><figcaption>Cabagnow Cave Pool south of Anda, a natural freshwater sinkhole around five meters deep, reached by a wooden ladder from the rim. Photo via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>If you have a third half-day, drive 15 minutes north to the Lamanok Island launch. The island sits in a small bay and holds a series of caves with ancient burial sites and rock paintings. A short banca ride from the mainland takes around 10 minutes one way. The island has a small fee and a local guide is required.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/panglao-3.jpg" alt="Bohol island and coastal scene" loading="lazy"><figcaption>The Lamanok bay north of Anda, where the short banca crossing leads to the burial caves and ancient rock paintings on the small island.</figcaption></figure>',
        ],
    ],

    // 26. LOON
    'loon-bohol-reconstructed-church-dolphin-watch-coast' => [
        [
            'anchor' => '<p>The Our Lady of Light Parish was rebuilt from 2014 to 2022 using a mix of the original coral-stone blocks and new construction. The facade replicates the original 1855 design, and the interior holds the salvaged retablo pieces alongside new altars. The reconstruction is one of the larger heritage projects in the Visayas in the past decade. Free entry, modest dress, weekday mass at six in the morning.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/29/Loon_Church_reconstructed_Bohol.jpg/800px-Loon_Church_reconstructed_Bohol.jpg" alt="Our Lady of Light Parish reconstruction in Loon, Bohol" loading="lazy"><figcaption>The reconstructed Our Lady of Light Parish in Loon, finished 2022 using salvaged coral-stone blocks alongside new construction over the 1855 footprint. Photo via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The Pamilacan Island Dolphin and Whale Watching Tour launches from Loon early in the morning, around six. The community-managed tour follows the spinner dolphin and pilot whale populations in the Bohol Sea. The boat ride takes around 90 minutes one way to the sighting grounds.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/b/bc/Pamilacan_Island_dolphin_watch_Bohol.jpg/800px-Pamilacan_Island_dolphin_watch_Bohol.jpg" alt="Pamilacan Island dolphin and whale watch" loading="lazy"><figcaption>The Pamilacan Island dolphin watch, the community-managed Bohol Sea trip run by the former whale-and-dolphin hunters of the village. Photo via Wikimedia Commons.</figcaption></figure>',
        ],
    ],

    // 27. LOBOC
    'loboc-bohol-off-cruise-quiet-morning-town' => [
        [
            'anchor' => '<p>The Loboc church was first built in 1638 and rebuilt several times. The 2013 earthquake damaged the main nave and the bell tower; the reconstruction was completed in 2022. The facade is late baroque with a single bell tower and the interior holds the salvaged retablo with the image of Saint Peter. Free entry, weekday mass at six in the morning.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/8a/San_Pedro_Apostol_Parish_Loboc_Bohol.jpg/800px-San_Pedro_Apostol_Parish_Loboc_Bohol.jpg" alt="San Pedro Apostol Parish in Loboc, Bohol" loading="lazy"><figcaption>The reconstructed San Pedro Apostol Parish in Loboc, finished 2022 after the 2013 earthquake flattened the main nave and the bell tower. Photo via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The old stone bridge across the Loboc River is a half-built Spanish-era structure that was never completed because it would have required demolishing part of the church. The unfinished arch sits to the east of the modern bridge as a kind of monument to colonial bureaucracy. Walk past it for the photo and the small heritage marker explains the story.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/panglao-loboc-river-cruise.jpg" alt="Loboc River and town scene in Bohol" loading="lazy"><figcaption>The Loboc River bend with the unfinished Spanish-era stone bridge to the east of the modern crossing, a colonial half-build that the church spared from completion.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The public market sits two blocks east of the church and handles the regional produce from the inland barangays. Vendors come down from the foothills with mountain bananas, root crops, and the local sikwate (hot chocolate). Buy a kilo of fresh budbud (sticky rice cake) from the vendors for merienda.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/manila-kakanin.jpg" alt="Filipino kakanin sticky rice cakes" loading="lazy"><figcaption>Loboc budbud and sikwate, the Bohol sticky-rice merienda paired with the inland-grown hot chocolate from the foothill barangays.</figcaption></figure>',
        ],
    ],

    // 28. SEVILLA BAMBOO BRIDGE
    'sevilla-bohol-bamboo-hanging-bridge-half-day' => [
        [
            'anchor' => '<p>The two bridges are made of bamboo decking on steel cables, originally built as community footbridges to connect the two barangays across the river. The first bridge takes you from the parking side to a small island in the middle of the river. The second bridge continues from the island to the opposite bank. Together they form a loop with the river path on either side.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/52/Sipatan_Bamboo_Hanging_Bridge_Sevilla_Bohol.jpg/800px-Sipatan_Bamboo_Hanging_Bridge_Sevilla_Bohol.jpg" alt="Sipatan Twin Bamboo Hanging Bridge in Sevilla, Bohol" loading="lazy"><figcaption>The Sipatan Twin Hanging Bridge in Sevilla, the bamboo-decked pair on steel cables that loops between two barangays across the Sipatan River. Photo via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>If you have a longer half-day, drive 15 minutes south to the Loboc Eco Adventure Park for the zip line across the river canyon. The zip line is around 500 meters long and crosses the Loboc River, offering a different angle of the surrounding countryside. Entry fees are moderate.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/panglao-2.jpg" alt="Bohol countryside river canyon" loading="lazy"><figcaption>The Loboc Eco Adventure Park zip line stretches 500 meters across the river canyon between Loboc and Sevilla, an optional add-on to the bamboo bridge loop.</figcaption></figure>',
        ],
    ],

    // 29. SAN JOAQUIN ILOILO
    'san-joaquin-iloilo-cuartel-church-heritage-route' => [
        [
            'anchor' => '<p>The church was built in 1869 and is one of the most unusual colonial churches in the Philippines. The facade features a high-relief carving of the Battle of Tetuan (1860), depicting the Spanish victory over Moroccan forces. The carving covers most of the upper facade and shows soldiers, horses, and the Spanish flag in a battle scene. It is the only Philippine church facade with a complete military battle scene of this scale.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/a7/San_Joaquin_Church_Iloilo_Tetuan_facade.jpg/800px-San_Joaquin_Church_Iloilo_Tetuan_facade.jpg" alt="San Joaquin Parish Church Tetuan battle facade" loading="lazy"><figcaption>The 1869 San Joaquin Parish Church, the only Philippine church facade with a full military battle scene, depicting the 1860 Spanish victory at Tetuan. Photo via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The old cemetery of San Joaquin sits on a small hill above the town, around 10 minutes by tricycle from the plaza. The campo santo is a circular stone structure with niches for the dead, built in the late 19th century. The view from the top opens to the southern Iloilo coast and the Panay Gulf. Free entry, modest dress.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/43/Campo_Santo_San_Joaquin_Iloilo.jpg/800px-Campo_Santo_San_Joaquin_Iloilo.jpg" alt="Campo Santo of San Joaquin, Iloilo" loading="lazy"><figcaption>The circular Campo Santo of San Joaquin on a small hill above the town, the late-19th-century niche structure with views to the Panay Gulf. Photo via Wikimedia Commons.</figcaption></figure>',
        ],
    ],

    // 30. MIAG-AO
    'miagao-iloilo-fortress-church-heritage-half-day' => [
        [
            'anchor' => '<p>The church was built between 1787 and 1797 as a fortified structure to defend against Moro raiders. The walls are around one and a half meters thick and the church doubles as a fortress, with the two flanking bell towers serving as watchtowers. The yellow-ochre stone gives the facade its distinctive color.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/5e/Miagao_Church_facade_Iloilo.jpg/800px-Miagao_Church_facade_Iloilo.jpg" alt="Santo Tomas de Villanueva Church facade in Miagao, Iloilo" loading="lazy"><figcaption>The 1797 Santo Tomas de Villanueva Church in Miag-ao, the UNESCO fortress church with twin watchtowers and one-and-a-half-meter walls of yellow-ochre stone. Photo via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The facade carving is the iconic feature. Saint Christopher carries the Christ Child across a stylized coconut tree, with native flora and fauna integrated into the design. The carving is a unique fusion of European Catholic iconography with local Visayan motifs. Free entry, modest dress, weekday mass at six in the morning.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/2b/Miagao_facade_coconut_tree_carving.jpg/800px-Miagao_facade_coconut_tree_carving.jpg" alt="Saint Christopher and coconut tree carving on Miagao facade" loading="lazy"><figcaption>The iconic Miag-ao carving of Saint Christopher carrying the Christ Child across a stylized coconut tree, the European-Visayan fusion that earned the UNESCO listing. Photo via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>If you want to buy hablon directly from the weavers, ask the market vendors for directions to the weaving centers in Brgy Sologon. The weavers welcome visitors and the cloth is sold by the yard. Plan around 30 minutes at the market and weaving stop.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/95/Hablon_weaving_Miagao_Iloilo.jpg/800px-Hablon_weaving_Miagao_Iloilo.jpg" alt="Hablon traditional weaving in Miagao, Iloilo" loading="lazy"><figcaption>Hablon weaving in Brgy Sologon, the traditional Miag-ao handwoven cloth made on wooden floor looms in stripes, plaids, and the patadyong checkered design. Photo via Wikimedia Commons.</figcaption></figure>',
        ],
    ],

    // 31. GUIMARAS CIRCUITS
    'guimaras-circuits-slow-stops-beyond-mango-farm' => [
        [
            'anchor' => '<p>Take a tricycle from Jordan port to the Trappist Monastery in Brgy San Miguel, around 20 minutes inland. The monastery is the only Trappist community in the Philippines, founded in 1972. The chapel is open to visitors for the daily prayer hours; the gift shop sells the local mango-based products including jam, candy, and the famous mango chutney made by the monks. Free entry, modest dress.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/guimaras-trappist-monastery.jpg" alt="Trappist Monastery in Guimaras" loading="lazy"><figcaption>The Trappist Monastery in Brgy San Miguel, the only Trappist community in the Philippines and the source of the famous monk-made mango chutney.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>End the day at the old Guisi Lighthouse on the southern coast. The lighthouse was built in 1894 by the Spanish administration and the original tower stands in partial ruins next to the modern operating tower. The view from the rocky shore is one of the calmer sunset spots in the island.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/guimaras-guisi-lighthouse.jpg" alt="Guisi Lighthouse on the southern Guimaras coast" loading="lazy"><figcaption>Guisi Lighthouse on the southern Guimaras coast, the 1894 Spanish-era tower standing in partial ruins beside the modern operating light.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>End at Alubihod Beach for a swim. The beach is the most-visited cove in Guimaras, around one kilometer of white-sand fronted by small bamboo cottages. Day-use fees are modest. The water is calm and clear, suitable for swimming and snorkeling. Plan around three hours for lunch and a swim.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/guimaras-1.jpg" alt="Alubihod Beach on Guimaras Island" loading="lazy"><figcaption>Alubihod Beach, the most-visited Guimaras cove, a one-kilometer white-sand stretch fronted by small bamboo cottages and a snorkel-friendly bay.</figcaption></figure>',
        ],
    ],

    // 33. TAGAYTAY
    'tagaytay-couple-weekend-calm-angle-manila' => [
        [
            'anchor' => '<p>Plan to leave Manila by seven in the morning to clear the SLEX traffic. Arrive in Tagaytay by nine and head to the calmer side of the ridge near Mahogany Market for breakfast. The cafes east of the rotunda are quieter than the popular Bag of Beans or Antonios; small local places like Bawai Vietnamese or Marcia Adams serve calm breakfasts with a view of the lake.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/tagaytay-mahogany-market.jpg" alt="Mahogany Market area in Tagaytay" loading="lazy"><figcaption>The Mahogany Market side of the Tagaytay ridge, where the quieter cafes east of the rotunda still get a clear Taal Lake view at breakfast.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The People\'s Park in the Sky sits on the highest point of the Tagaytay ridge, around 15 minutes drive from the rotunda. The unfinished Marcos-era summer palace at the top has been converted into a public park with a walking trail around the perimeter. The view from the top opens to the lake, the volcano, and the surrounding ridge.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/tagaytay-peoples-park-in-the-sky.jpg" alt="People Park in the Sky in Tagaytay" loading="lazy"><figcaption>People Park in the Sky on the highest point of the Tagaytay ridge, the unfinished Marcos-era summer palace converted into a perimeter walking trail.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Drive 30 minutes south to Caleruega in Nasugbu Batangas for the morning. The Transfiguration Chapel sits on a small hill with the lake in the background. The chapel grounds include a meditation walk, a small garden, and a cafe.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/0/04/Caleruega_Transfiguration_Chapel_Nasugbu.jpg/800px-Caleruega_Transfiguration_Chapel_Nasugbu.jpg" alt="Caleruega Transfiguration Chapel in Nasugbu, Batangas" loading="lazy"><figcaption>The Caleruega Transfiguration Chapel on a small Nasugbu hilltop, with the Tagaytay ridge in the background and the quiet meditation walk along the grounds. Photo via Wikimedia Commons.</figcaption></figure>',
        ],
    ],

    // 34. PAMPANGA FOOD CRAWL
    'pampanga-food-only-crawl-one-day-plan-manila' => [
        [
            'anchor' => '<p>Drive to Aling Lucing in Angeles for the original sisig. The place is small, the line forms by 10 in the morning, and the dish is the prototype that every other sisig variant in the Philippines is measured against. Order the sisig manok or sisig baboy with a side of bagoong rice. The chopped pork is grilled fresh; the squeeze of calamansi at the end is the local technique.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/angeles-sisig.jpg" alt="Original Aling Lucing sisig in Angeles, Pampanga" loading="lazy"><figcaption>The Aling Lucing sisig in Angeles, the chopped-and-grilled-pork prototype that every other Philippine sisig variant is measured against.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Drive to one of the heritage Kapampangan restaurants in Angeles or Bacolor for lunch. Atching Lillian Borromeo\'s home kitchen in Mexico, Pampanga, serves the traditional bringhe, a Kapampangan paella variant with chicken, prawns, and turmeric rice. Reservations are required; the kitchen is small and serves limited covers per day.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/d/d8/Bringhe_Kapampangan_paella.jpg/800px-Bringhe_Kapampangan_paella.jpg" alt="Bringhe, the Kapampangan paella variant" loading="lazy"><figcaption>Bringhe at Atching Lillian Borromeo in Mexico, Pampanga: the Kapampangan paella with chicken, prawns, and turmeric rice, served by reservation only. Photo via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Drive to Razon\'s in Guagua or its Angeles branch for the famous halo-halo. The Pampanga version uses fewer ingredients than the Visayan or Manila version: just sweetened saba banana, leche flan, macapuno, milk, and ice. The minimalism is the point. Pair with a pichi-pichi from the small kakanin vendors near the restaurant.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/19/Razons_halo_halo_Guagua_Pampanga.jpg/800px-Razons_halo_halo_Guagua_Pampanga.jpg" alt="Razon Pampanga halo-halo, minimalist version" loading="lazy"><figcaption>The Razon\'s halo-halo from Guagua, the minimalist Pampanga version built on just sweetened saba, leche flan, macapuno, milk, and ice. Photo via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Start in San Fernando city around eight in the morning. Drop by the Susie\'s Cuisine outlet at the rotunda for the morning tibok-tibok, a carabao milk pudding topped with toasted latik. Pair with a small cup of kapeng barako. The vendors near the Pampanga Provincial Capitol also sell the local puto-bumbong and bibingka all year, not just in December.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/61/Tibok_tibok_Pampanga_carabao_milk_pudding.jpg/800px-Tibok_tibok_Pampanga_carabao_milk_pudding.jpg" alt="Tibok-tibok, Pampanga carabao milk pudding" loading="lazy"><figcaption>Tibok-tibok from Susie\'s Cuisine in San Fernando, the carabao milk pudding topped with toasted latik that anchors a Kapampangan breakfast. Photo via Wikimedia Commons.</figcaption></figure>',
        ],
    ],

    // 35. VIGAN CHURCH ROUTE
    'vigan-heritage-church-route-calm-half-day-walk' => [
        [
            'anchor' => '<p>The Vigan Cathedral was built in 1574 and rebuilt several times after earthquakes. The current structure is from the early 19th century, with a plain late baroque facade and twin bell towers. The cathedral is the seat of the Archdiocese of Nueva Segovia, one of the oldest dioceses in the Philippines. Free entry, modest dress, daily mass at six in the morning.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/vigan-vigan-cathedral-and-plaza-salcedo.jpg" alt="Saint Paul Metropolitan Cathedral in Vigan, Ilocos Sur" loading="lazy"><figcaption>The Saint Paul Metropolitan Cathedral on Plaza Salcedo in Vigan, seat of the Archdiocese of Nueva Segovia and one of the oldest dioceses in the Philippines.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Bantay sits around 10 minutes by tricycle from the Vigan plaza, across the river. The Saint Augustine Church was built in 1591 and rebuilt several times. The detached bell tower, known as the Bantay Bell Tower, sits on a small hill and served as a watchtower during the Spanish colonial period. The tower has been used as a filming location for several Filipino historical movies.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/vigan-bantay-bell-tower.jpg" alt="Bantay Bell Tower near Vigan, Ilocos Sur" loading="lazy"><figcaption>The Bantay Bell Tower on its hill across the river from Vigan, the detached 1591 watchtower that has been used as a filming location for Filipino historical films.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>On the way back to Vigan from Bantay, stop at the Crisologo Museum near the city plaza. The museum is the home of the assassinated Ilocos Sur governor Floro Crisologo and his wife, Carmeling. The exhibits cover the family history, the political assassination at the cathedral in 1970, and the surrounding colonial period.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/vigan-1.jpg" alt="Vigan heritage town and colonial street" loading="lazy"><figcaption>The Vigan heritage core around the Crisologo Museum, where the 1970 cathedral assassination of Floro Crisologo is documented alongside the broader colonial period.</figcaption></figure>',
        ],
    ],

    // 38. ILOCOS SUR CHURCH ROUTE
    'ilocos-sur-church-route-slow-heritage-drive' => [
        [
            'anchor' => '<p>Santa Maria is around 45 minutes south of Vigan by car. The Nuestra Senora de la Asuncion Church was built in 1765 and declared a UNESCO World Heritage Site in 1993 as part of the Baroque Churches of the Philippines. The church sits on a small hill above the town, accessed by a long stone staircase from the plaza below. The fortress-style architecture was designed to double as a defense structure.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/ilocos-sur-santa-maria-church-1769.jpg" alt="Santa Maria Church UNESCO World Heritage Site in Ilocos Sur" loading="lazy"><figcaption>Santa Maria Church in Ilocos Sur, the 1765 UNESCO fortress church reached by a long stone staircase up to its hilltop plaza.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Drive further north to Sinait, around 20 minutes from Cabugao. The Santo Nino Parish was built in 1654 and houses the venerated image of the Holy Child of Sinait, which according to local lore arrived by sea in a wooden chest. The church is a Marian pilgrimage site and the second Sunday of May feast day draws thousands.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/7/76/Santo_Nino_de_Sinait_Church_Ilocos_Sur.jpg/800px-Santo_Nino_de_Sinait_Church_Ilocos_Sur.jpg" alt="Santo Nino Parish in Sinait, Ilocos Sur" loading="lazy"><figcaption>The 1654 Santo Nino Parish in Sinait, the pilgrimage church for the Holy Child image that local lore says arrived by sea in a wooden chest. Photo via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Drive north from Vigan to Magsingal, around 30 minutes. The San Guillermo Parish was built in 1827 with a plain late baroque facade and a detached bell tower. The interior holds a wooden retablo with the original altarpieces. Free entry, modest dress.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/cf/Magsingal_Church_Ilocos_Sur.jpg/800px-Magsingal_Church_Ilocos_Sur.jpg" alt="San Guillermo Parish in Magsingal, Ilocos Sur" loading="lazy"><figcaption>The 1827 San Guillermo Parish in Magsingal, with its plain late baroque facade and the detached bell tower that anchors the small heritage museum next door. Photo via Wikimedia Commons.</figcaption></figure>',
        ],
    ],

    // 39. ILOILO FOOD TRAIL
    'iloilo-food-trail-beyond-la-paz-batchoy' => [
        [
            'anchor' => '<p>Start the day in the Molo district. The pancit Molo is a wonton soup made with pork-and-shrimp dumplings in a clear broth, originally from the Molo Chinese community. The dish is the breakfast staple of the district. Order at one of the small carinderias near the Molo Plaza or the historic Molo Church.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/iloilo-city-pancit-molo.jpg" alt="Pancit Molo wonton soup from Iloilo" loading="lazy"><figcaption>Pancit Molo, the pork-and-shrimp wonton soup from the Iloilo Molo district that anchors the breakfast end of the food trail.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Drive to one of the heritage Ilonggo restaurants for KBL, the regional pork-and-vegetable stew with kadyos (pigeon peas), baboy (pork), and langka (young jackfruit). The dish is the Sunday lunch of the Ilonggo household and the local restaurants like Tatoy\'s Manokan and Camina Balay nga Bato serve the heritage versions.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/bacolod-kadios-baboy-langka.jpg" alt="KBL (Kadyos-Baboy-Langka) Ilonggo stew" loading="lazy"><figcaption>KBL (kadyos-baboy-langka), the Sunday-lunch pork-and-pigeon-pea stew with young jackfruit, served at Tatoy\'s Manokan and Camina Balay nga Bato.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>You cannot do an Iloilo food trail without the batchoy. Drive to Ted\'s, Deco\'s, or Netong\'s in the La Paz district for the original noodle soup. The broth is pork-based with chicharon, liver, and meat slices, served over miki noodles. The recipe is unchanged since the early 20th century.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/iloilo-city-la-paz-batchoy.jpg" alt="La Paz batchoy from Iloilo" loading="lazy"><figcaption>La Paz batchoy from Ted\'s, Deco\'s, or Netong\'s, the pork-broth-and-miki noodle soup with chicharon, liver, and meat slices that has stayed the same since the early 20th century.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Drive to the Inasal Manokan country in the Diversion Road area for late lunch or early dinner. Chicken inasal is the Ilonggo version of grilled chicken, marinated in calamansi, garlic, and pepper, basted with annatto oil. The Bacolod version gets more press but the Iloilo version is older.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/bacolod-chicken-inasal.jpg" alt="Chicken inasal marinated in calamansi and annatto oil" loading="lazy"><figcaption>Chicken inasal from Manokan country in Iloilo, the older calamansi-garlic-pepper marinade basted with annatto oil, served with sinamak vinegar on the side.</figcaption></figure>',
        ],
    ],

    // 40. ANTIPOLO
    'antipolo-couple-weekend-calm-cafe-chapel-plan' => [
        [
            'anchor' => '<p>The Antipolo Cathedral is the home of the Our Lady of Peace and Good Voyage, the patroness of the city and one of the older Marian pilgrimage images in the Philippines. The image was brought from Mexico in 1626 and the cathedral is the main shrine. The current structure is a large modern church with a wide plaza in front.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/antipolo-antipolo-cathedral.jpg" alt="Antipolo Cathedral and Marian shrine" loading="lazy"><figcaption>Antipolo Cathedral, home of the Our Lady of Peace and Good Voyage image brought from Mexico in 1626 and venerated as the patroness of travelers.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Pinto Art Museum sits on Sierra Madre Hills, a 15-minute drive east of the cathedral. The museum is a series of white Mediterranean-style buildings holding contemporary Philippine art across several galleries. The grounds include a chapel, a garden, and several outdoor sculptures.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/antipolo-pinto-art-museum.jpg" alt="Pinto Art Museum in Antipolo, Rizal" loading="lazy"><figcaption>Pinto Art Museum on Sierra Madre Hills, the white Mediterranean-style cluster of galleries showing contemporary Philippine art across gardens and sculpture courts.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Drive 20 minutes from Pinto to Hinulugang Taktak, the Antipolo waterfall and the inspiration for the local folk song. The falls have been restored as a small park with a viewing deck and a swimming area. The water flow is reduced from the historical level but the site retains its calm folk-song character.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/antipolo-hinulugang-taktak-falls.jpg" alt="Hinulugang Taktak Falls in Antipolo" loading="lazy"><figcaption>Hinulugang Taktak, the Antipolo waterfall and the inspiration for the local folk song, restored as a small park with a viewing deck above the basin.</figcaption></figure>',
        ],
    ],

];
