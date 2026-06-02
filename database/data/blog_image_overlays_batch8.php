<?php

/**
 * Blog image overlays for batch 8 posts (assigned slugs from
 * new_slugs_chunk_3.txt). Each entry maps a blog slug to a list of
 * <figure> HTML blocks that the renderer injects either before or
 * after a unique anchor substring found in the post content_html.
 *
 * No duplicate `src` values appear within this batch. Anchors are
 * verified to appear exactly once in their parent post.
 */

return [

    // ------------------------------------------------------------------
    // Pampanga two-day first-timer
    // ------------------------------------------------------------------
    'pampanga-two-days-first-timer-itinerary-beyond-food' => [
        [
            'anchor' => 'Drive to Clark Freeport and walk the old base streets. The flagpole, the parade ground, and the airfield grounds give a sense of the scale.',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/angeles-clark-freeport-zone-hann-casino-marriott-quest.jpg" alt="Clark Freeport Zone streets in Angeles Pampanga" loading="lazy"><figcaption>The Clark Freeport grid in Angeles. The old U.S. air base streets are wide and quiet on weekday afternoons, with the Hann Casino and the airfield grounds at the eastern end.</figcaption></figure>',
        ],
        [
            'anchor' => 'The Candaba wetlands are a Ramsar site that hosts thousands of migratory birds from October to March.',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/arayat-candaba-bird-sanctuary.jpg" alt="Candaba bird sanctuary wetlands in Pampanga" loading="lazy"><figcaption>The Candaba wetlands at the edge of Pampanga. From October to March the rice fields fill with egrets, ducks, and the occasional rare migrant from East Asia.</figcaption></figure>',
        ],
        [
            'anchor' => 'The half-buried church is the visual reminder of the 1995 Pinatubo lahar that swallowed Bacolor.',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/8e/San_Guillermo_Parish_Church_%28Bacolor%2C_Pampanga%29_03.jpg/800px-San_Guillermo_Parish_Church_%28Bacolor%2C_Pampanga%29_03.jpg" alt="San Guillermo Parish Church half-buried in Bacolor Pampanga" loading="lazy"><figcaption>San Guillermo Parish Church in Bacolor. The original floor sits roughly three meters below the current entrance after the 1995 Pinatubo lahar. Photo via <a href="https://commons.wikimedia.org/wiki/File:San_Guillermo_Parish_Church_(Bacolor,_Pampanga)_03.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // Tarlac first-timers
    // ------------------------------------------------------------------
    'tarlac-first-timers-province-manila-drive-past' => [
        [
            'anchor' => 'The monastery sits on Mt. Resurrection in San Jose, on a ridge with a 30-foot statue of the Risen Christ.',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/4a/Monasterio_de_Tarlac.jpg/800px-Monasterio_de_Tarlac.jpg" alt="Monasterio de Tarlac statue of the Risen Christ on Mt Resurrection" loading="lazy"><figcaption>Monasterio de Tarlac on Mt. Resurrection in San Jose. The 30-foot Risen Christ statue is the centerpiece, and the ridge view stretches across Central Luzon on clear days. Photo via <a href="https://commons.wikimedia.org/wiki/File:Monasterio_de_Tarlac.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'The shrine in Camp O\'Donnell marks the end of the 1942 Bataan Death March.',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/0/08/Capas_National_Shrine_obelisk.jpg/800px-Capas_National_Shrine_obelisk.jpg" alt="Capas National Shrine obelisk in Tarlac" loading="lazy"><figcaption>The 70-meter obelisk at Capas National Shrine, the marker for the end of the 1942 Bataan Death March at Camp O\'Donnell. Bring water, the open field has little shade. Photo via <a href="https://commons.wikimedia.org/wiki/File:Capas_National_Shrine_obelisk.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'Isdaan in Gerona is a roadside themed restaurant with giant figures, fish ponds, and the famous Tacsiyapo Wall',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/tarlac-aquino-center-and-museum.jpg" alt="Tarlac Aquino Center and Museum in Concepcion" loading="lazy"><figcaption>The Aquino Center and Museum in Concepcion, Tarlac. The hacienda complex includes the family monuments and the old sugar-mill grounds.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // Subic and Zambales two days
    // ------------------------------------------------------------------
    'subic-zambales-two-days-beyond-theme-parks' => [
        [
            'anchor' => 'Zoobic Safari is the standard family pick. The tiger safari is the headline',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/subic-tree-top-adventure.jpg" alt="Subic Tree Top Adventure canopy course" loading="lazy"><figcaption>Inside the Subic Freeport, Tree Top Adventure and Zoobic Safari sit a short drive apart and pair well for a family-friendly morning.</figcaption></figure>',
        ],
        [
            'anchor' => 'Ocean Adventure in Camayan is the marine park with a small sea-lion and dolphin show.',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/subic-camayan-beach-resort.jpg" alt="Camayan Beach Resort in Subic Bay" loading="lazy"><figcaption>The Camayan side of Subic Bay where Ocean Adventure sits. The cove beach is calm enough for a swim after the marine-park visit.</figcaption></figure>',
        ],
        [
            'anchor' => 'Pundaquit is the jump-off for Anawangin, Nagsasa, and Capones Island.',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c0/Anawangin_Cove_Zambales.jpg/800px-Anawangin_Cove_Zambales.jpg" alt="Anawangin Cove pines and gray sand in Zambales" loading="lazy"><figcaption>Anawangin Cove, one of three island-hopping stops from Pundaquit. The agoho pines behind the gray sand are the cove signature. Photo via <a href="https://commons.wikimedia.org/wiki/File:Anawangin_Cove_Zambales.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'From San Antonio, continue 15 minutes north to Liwliwa in San Felipe.',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/3/3f/Liwliwa_Beach_San_Felipe_Zambales.jpg/800px-Liwliwa_Beach_San_Felipe_Zambales.jpg" alt="Liwliwa Beach in San Felipe Zambales with surfers" loading="lazy"><figcaption>Liwliwa in San Felipe, Zambales. Consistent shoulder-high sets from June to October and a long gray-sand stretch good for beginner surf lessons. Photo via <a href="https://commons.wikimedia.org/wiki/File:Liwliwa_Beach_San_Felipe_Zambales.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // Kaybiang Tunnel road trip
    // ------------------------------------------------------------------
    'kaybiang-tunnel-ternate-nasugbu-road-trip-day' => [
        [
            'anchor' => 'The tunnel was built in 2009 and opened to traffic in 2013.',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/e1/Kaybiang_Tunnel.jpg/800px-Kaybiang_Tunnel.jpg" alt="Kaybiang Tunnel entrance on the Ternate Nasugbu road" loading="lazy"><figcaption>The Kaybiang Tunnel entrance on the Ternate side. At around 300 meters, it is one of the longest road tunnels in the country. Photo via <a href="https://commons.wikimedia.org/wiki/File:Kaybiang_Tunnel.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'South of the tunnel, the road climbs to a lookout above Hamilo Coast and the Pico de Loro Cove.',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/nasugbu-mt-pico-de-loro-parrots-beak.jpg" alt="Mt Pico de Loro Parrots Beak silhouette near Nasugbu" loading="lazy"><figcaption>The Pico de Loro silhouette, named for the parrot-beak rock at the summit. The pull-off south of the tunnel gives the wide view of the cove and the inland peak.</figcaption></figure>',
        ],
        [
            'anchor' => 'The Nasugbu town proper is around 20 minutes further south.',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/nasugbu-munting-buhangin-beach-camp.jpg" alt="Munting Buhangin Beach Camp in Nasugbu Batangas" loading="lazy"><figcaption>The Nasugbu coastline south of the tunnel. The public Wawa side has the food stalls, and small beach camps line the bay for an easy lunch stop.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // Boracay de Cavite marine base
    // ------------------------------------------------------------------
    'boracay-de-cavite-katungkulan-ternate-marine-base' => [
        [
            'anchor' => 'Katungkulan is around 700 meters long, ending in a small rocky outcrop at the south end.',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/aa/Katungkulan_Beach_Ternate_Cavite.jpg/800px-Katungkulan_Beach_Ternate_Cavite.jpg" alt="Katungkulan Beach inside the Marine Base in Ternate Cavite" loading="lazy"><figcaption>Katungkulan Beach, the locals\' Boracay de Cavite, inside the Marine Base at Ternate. The bay is sheltered and the sand pale gray in afternoon light. Photo via <a href="https://commons.wikimedia.org/wiki/File:Katungkulan_Beach_Ternate_Cavite.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'Kaybiang Tunnel, around 20 minutes south, the long road tunnel through Mt. Pico de Loro',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/naic-maragondon-heritage-town.jpg" alt="Maragondon heritage town in Cavite" loading="lazy"><figcaption>Maragondon heritage town on the drive between Naic and Ternate. The Bonifacio trial house and the 1700s church anchor a short walking stop on the way home.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // Cavite first-timer loop
    // ------------------------------------------------------------------
    'cavite-first-timer-loop-silang-cafes-corregidor' => [
        [
            'anchor' => 'The Aguinaldo Highway from Silang to Tagaytay is the cafe corridor.',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/silang-our-lady-of-candelaria-parish-church.jpg" alt="Our Lady of Candelaria Parish Church in Silang Cavite" loading="lazy"><figcaption>The Silang town center sits at the cool 400-meter upland, the same elevation that draws the cafes along the Aguinaldo Highway toward Tagaytay.</figcaption></figure>',
        ],
        [
            'anchor' => 'Stop for bulalo and tawilis at the Mahogany Market or Diner\'s.',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/tagaytay-mahogany-market.jpg" alt="Tagaytay Mahogany Market bulalo stalls" loading="lazy"><figcaption>Tagaytay\'s Mahogany Market, the standard bulalo stop on the ridge. Skip the souvenir aisle and head to the back stalls where the broth runs all day.</figcaption></figure>',
        ],
        [
            'anchor' => 'The Aguinaldo Shrine in Kawit is where Philippine independence was declared in 1898.',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/imus-aguinaldo-shrine-in-nearby-kawit.jpg" alt="Aguinaldo Shrine in Kawit Cavite" loading="lazy"><figcaption>The Aguinaldo Shrine in Kawit. The second-floor window where the flag was first waved on June 12, 1898 is the standard photo stop.</figcaption></figure>',
        ],
        [
            'anchor' => 'Corregidor Island sits at the entrance of Manila Bay, technically part of Cavite.',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/24/Malinta_Tunnel_Corregidor.jpg/800px-Malinta_Tunnel_Corregidor.jpg" alt="Malinta Tunnel entrance on Corregidor Island" loading="lazy"><figcaption>Malinta Tunnel on Corregidor, the underground command complex from World War II. The Sun Cruises day tour from CCP Bay Terminal covers the tunnel and the Pacific War Memorial. Photo via <a href="https://commons.wikimedia.org/wiki/File:Malinta_Tunnel_Corregidor.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // Baler Aurora surf two days
    // ------------------------------------------------------------------
    'baler-aurora-birthplace-philippine-surfing-two-days' => [
        [
            'anchor' => 'Sabang is a long beach break with consistent waves from October to March.',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/8b/Sabang_Beach_Baler.jpg/800px-Sabang_Beach_Baler.jpg" alt="Sabang Beach in Baler Aurora with surfers" loading="lazy"><figcaption>Sabang Beach in Baler, the long break where Philippine surfing took root in the late 1970s. The boardwalk fronts the surf camps and the lesson lineup is consistent. Photo via <a href="https://commons.wikimedia.org/wiki/File:Sabang_Beach_Baler.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'Ditumabo Mother Falls is the most popular waterfall in the area, around 20 minutes north of Baler town.',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/e1/Ditumabo_Mother_Falls_in_San_Luis_Aurora.jpg/800px-Ditumabo_Mother_Falls_in_San_Luis_Aurora.jpg" alt="Ditumabo Mother Falls in San Luis Aurora" loading="lazy"><figcaption>Ditumabo Mother Falls in San Luis. The trail crosses the creek several times before the basin and the spray reaches across the entire pool. Photo via <a href="https://commons.wikimedia.org/wiki/File:Ditumabo_Mother_Falls_in_San_Luis_Aurora.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'The Millennium Tree in Maria Aurora is a 400-year-old balete with a hollow trunk large enough to walk through.',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/3/3a/Millenium_Tree_in_Maria_Aurora.jpg/800px-Millenium_Tree_in_Maria_Aurora.jpg" alt="Millennium Tree balete in Maria Aurora" loading="lazy"><figcaption>The 400-year-old balete known as the Millennium Tree in Maria Aurora. A small viewing platform lets you step inside the hollow trunk. Photo via <a href="https://commons.wikimedia.org/wiki/File:Millenium_Tree_in_Maria_Aurora.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // Calaguas Islands
    // ------------------------------------------------------------------
    'calaguas-islands-camarines-norte-long-bus-bangka-trip' => [
        [
            'anchor' => 'The main beach is a 1.5-kilometer curve of fine white sand.',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/22/Mahabang_Buhangin_Beach_Calaguas.jpg/800px-Mahabang_Buhangin_Beach_Calaguas.jpg" alt="Mahabang Buhangin Beach on Tinaga Island Calaguas" loading="lazy"><figcaption>Mahabang Buhangin on Tinaga Island, the main Calaguas beach. The 1.5-kilometer curve of fine white sand is the standard camping setup. Photo via <a href="https://commons.wikimedia.org/wiki/File:Mahabang_Buhangin_Beach_Calaguas.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'Wake early for the hike up to the cross. The trail is steep and takes around 30 minutes one way.',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/a5/Calaguas_Island_viewpoint.jpg/800px-Calaguas_Island_viewpoint.jpg" alt="Calaguas Island viewpoint from the hilltop cross" loading="lazy"><figcaption>The viewpoint from the cross above Mahabang Buhangin. The 30-minute trail rewards an early start before the heat sets in. Photo via <a href="https://commons.wikimedia.org/wiki/File:Calaguas_Island_viewpoint.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // Mt. Gulugod Baboy
    // ------------------------------------------------------------------
    'mt-gulugod-baboy-mabini-easy-day-hike-first-time' => [
        [
            'anchor' => 'The ridge gives the wide view in three directions. East faces Verde Island and the Maricaban Strait',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/8a/Mt._Gulugod_Baboy_summit_view.jpg/800px-Mt._Gulugod_Baboy_summit_view.jpg" alt="Mt Gulugod Baboy summit ridge view of Anilao bay" loading="lazy"><figcaption>The summit ridge of Mt. Gulugod Baboy. East faces Verde Island, north drops to the Anilao bay where the dive boats anchor. Photo via <a href="https://commons.wikimedia.org/wiki/File:Mt._Gulugod_Baboy_summit_view.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'Sombrero is a small uninhabited island with a sandy point and a reef on the lee side.',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/anilao-mabini-sombrero-island.jpg" alt="Sombrero Island off Anilao Mabini Batangas" loading="lazy"><figcaption>Sombrero Island off Anilao, named for its hat-shaped profile. The lee-side reef holds small tropical fish for a calm post-hike snorkel.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // Mt. Manunggal Cebu
    // ------------------------------------------------------------------
    'mt-manunggal-cebu-magsaysay-memorial-day-hike' => [
        [
            'anchor' => 'The crash site is in the meadow near the campsite, marked by a small concrete monument and parts of the C-47 aircraft',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/3/37/Magsaysay_Memorial_Mt_Manunggal.jpg/800px-Magsaysay_Memorial_Mt_Manunggal.jpg" alt="President Magsaysay crash site memorial on Mt Manunggal Cebu" loading="lazy"><figcaption>The Magsaysay memorial in the Mt. Manunggal meadow. Pieces of the C-47 wreckage rest beside the concrete marker for the 1957 crash. Photo via <a href="https://commons.wikimedia.org/wiki/File:Magsaysay_Memorial_Mt_Manunggal.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'The meadow near the memorial is the standard campsite. Around a dozen tents fit comfortably.',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/29/Mt_Manunggal_campsite.jpg/800px-Mt_Manunggal_campsite.jpg" alt="Mt Manunggal campsite meadow in Balamban Cebu" loading="lazy"><figcaption>The campsite meadow at around 1,100 meters on Mt. Manunggal in Balamban. Cool nights drop to around 15 degrees Celsius so a fleece is part of the kit. Photo via <a href="https://commons.wikimedia.org/wiki/File:Mt_Manunggal_campsite.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // Gigantes Islands
    // ------------------------------------------------------------------
    'gigantes-islands-carles-iloilo-three-day-island-hopping' => [
        [
            'anchor' => 'The first stop is usually Cabugao Gamay, the small island with the famous viewpoint.',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/7/75/Cabugao_Gamay_Island_Gigantes.jpg/800px-Cabugao_Gamay_Island_Gigantes.jpg" alt="Cabugao Gamay viewpoint Gigantes Islands Iloilo" loading="lazy"><figcaption>Cabugao Gamay in the Gigantes group. The short climb to the rocky outcrop above the cove is the standard postcard shot of northern Iloilo. Photo via <a href="https://commons.wikimedia.org/wiki/File:Cabugao_Gamay_Island_Gigantes.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'Tangke is a saltwater lagoon inside Gigantes Sur, accessible by a narrow channel that opens only at certain tides.',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/87/Tangke_Lagoon_Gigantes_Sur.jpg/800px-Tangke_Lagoon_Gigantes_Sur.jpg" alt="Tangke saltwater lagoon limestone walls Gigantes Sur" loading="lazy"><figcaption>Tangke Lagoon on Gigantes Sur. The narrow channel into the lagoon opens with the tide so the boatmen time the swim window. Photo via <a href="https://commons.wikimedia.org/wiki/File:Tangke_Lagoon_Gigantes_Sur.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'Bantigue is the long sandbar that appears at low tide.',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/49/Bantigue_Sandbar_Gigantes.jpg/800px-Bantigue_Sandbar_Gigantes.jpg" alt="Bantigue sandbar at low tide Gigantes Islands Iloilo" loading="lazy"><figcaption>Bantigue sandbar at low tide. The strip stretches around a kilometer and lunch is usually grilled fish on banig mats with shade rigged from the boats. Photo via <a href="https://commons.wikimedia.org/wiki/File:Bantigue_Sandbar_Gigantes.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'Carles is the scallop capital, and the village kitchens prepare them in butter, garlic, and cheese variations.',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/4b/Gigantes_Islands_scallops.jpg/800px-Gigantes_Islands_scallops.jpg" alt="Scallops cooked at Gigantes Islands Carles Iloilo" loading="lazy"><figcaption>Scallops at a Gigantes village kitchen, the local trade and the reason the trip is also a food memory. Butter, garlic, and cheese are the standard treatments. Photo via <a href="https://commons.wikimedia.org/wiki/File:Gigantes_Islands_scallops.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // Davao City first-timers
    // ------------------------------------------------------------------
    'davao-city-first-timers-local-slow-two-days' => [
        [
            'anchor' => 'People\'s Park in the city center is a calm three-hectare urban park with sculptures of the Lumad and indigenous peoples of Mindanao.',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/davao-city-roxas-avenue-crocodile-park-toril.jpg" alt="Davao city center near Roxas Avenue and the park" loading="lazy"><figcaption>The Davao city core around Roxas Avenue. People\'s Park, the cathedral, and the City Hall sit within an easy walking loop in the cool morning hours.</figcaption></figure>',
        ],
        [
            'anchor' => 'Roxas Avenue is the night food strip. The grilled tuna jaws are the local headline.',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/davao-city-tuna-belly-grilled.jpg" alt="Grilled tuna jaw panga at Roxas Avenue Davao" loading="lazy"><figcaption>Panga ng tuna at a Roxas Avenue grill. The standard order is the panga with rice and a cold soda, eaten early before the queue.</figcaption></figure>',
        ],
        [
            'anchor' => 'Eden Nature Park sits in the Toril district, around 45 minutes from the city center.',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/davao-city-eden-nature-park.jpg" alt="Eden Nature Park hillside garden in Toril Davao" loading="lazy"><figcaption>Eden Nature Park at around 900 meters in Toril. The air is noticeably cooler than the city, and the shuttle weaves through the gardens before the buffet lunch.</figcaption></figure>',
        ],
        [
            'anchor' => 'Davao is the durian capital. The strong smell that some hotels ban is part of the deal',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/davao-city-durian.jpg" alt="Fresh durian sold at a Davao market stall" loading="lazy"><figcaption>Durian at a Davao public market. Vendors open and clean a fruit for you on the spot, with mangosteen sold next to the stall as the local pairing.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // Quezon Province beaches
    // ------------------------------------------------------------------
    'quezon-province-beaches-calm-read-beyond-cagbalete' => [
        [
            'anchor' => 'Cagbalete is the most famous of the Quezon beaches, a long stretch of fine white sand off the Mauban coast.',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c5/Cagbalete_Island_Mauban_Quezon.jpg/800px-Cagbalete_Island_Mauban_Quezon.jpg" alt="Cagbalete Island tidal flats in Mauban Quezon" loading="lazy"><figcaption>Cagbalete Island off Mauban. At low tide the flats stretch hundreds of meters out, the landscape that fills the Manila travel feeds. Photo via <a href="https://commons.wikimedia.org/wiki/File:Cagbalete_Island_Mauban_Quezon.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'Borawan is a small island that combines fine white sand with limestone outcrops, the name coming from a portmanteau of Boracay and Palawan.',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/d/db/Borawan_Island_Padre_Burgos_Quezon.jpg/800px-Borawan_Island_Padre_Burgos_Quezon.jpg" alt="Borawan Island limestone outcrops in Padre Burgos Quezon" loading="lazy"><figcaption>Borawan Island off Padre Burgos. The name is a portmanteau of Boracay and Palawan, the limestone outcrops behind the white sand explain why. Photo via <a href="https://commons.wikimedia.org/wiki/File:Borawan_Island_Padre_Burgos_Quezon.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'Kwebang Lampas, also called Puting Buhangin Beach, is a small white sand cove with a sea cave at the end.',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/0/05/Puting_Buhangin_Pagbilao.jpg/800px-Puting_Buhangin_Pagbilao.jpg" alt="Kwebang Lampas Puting Buhangin sea cave in Pagbilao Quezon" loading="lazy"><figcaption>Kwebang Lampas at Puting Buhangin in Pagbilao. The cove ends in a sea cave you can wade through at low tide. Photo via <a href="https://commons.wikimedia.org/wiki/File:Puting_Buhangin_Pagbilao.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // Kamay ni Hesus Lucban
    // ------------------------------------------------------------------
    'kamay-ni-hesus-shrine-lucban-half-day-manila' => [
        [
            'anchor' => 'The 305 steps lead up to the Mary of the Risen Christ statue at the top of the hill.',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Kamay_ni_Hesus_Shrine_Lucban.jpg/800px-Kamay_ni_Hesus_Shrine_Lucban.jpg" alt="Kamay ni Hesus Shrine Ascension Hill in Lucban Quezon" loading="lazy"><figcaption>The Ascension Hill at Kamay ni Hesus Shrine in Lucban. The 305 steps wind past concrete Via Dolorosa stations up to the Risen Christ statue. Photo via <a href="https://commons.wikimedia.org/wiki/File:Kamay_ni_Hesus_Shrine_Lucban.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'The Lucban St. Louis Bishop of Toulouse Church faces the plaza and is one of the oldest in the province',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/0/02/Lucban_Church_Quezon.jpg/800px-Lucban_Church_Quezon.jpg" alt="St Louis Bishop of Toulouse Church in Lucban Quezon" loading="lazy"><figcaption>The Lucban St. Louis Bishop of Toulouse Church on the town plaza. The 1600s stone facade and bell tower are the heritage anchor for the half-day walk. Photo via <a href="https://commons.wikimedia.org/wiki/File:Lucban_Church_Quezon.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'Pancit habhab is the local specialty, a stir-fried noodle dish served on a banana leaf and eaten without utensils.',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/quezon-province-lucban-longganisa.jpg" alt="Lucban longganisa from Quezon Province" loading="lazy"><figcaption>Longganisang Lucban, the savory garlic-pork sausage that pairs with pancit habhab at the plaza carinderias.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // Budget beaches near Manila under 1000
    // ------------------------------------------------------------------
    'budget-beaches-near-manila-eight-picks-under-1000' => [
        [
            'anchor' => 'Around five hours by bus from Manila to San Antonio, then a bangka to the cove. Gray sand backed by agoho pines',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/56/Nagsasa_Cove_Zambales.jpg/800px-Nagsasa_Cove_Zambales.jpg" alt="Nagsasa Cove agoho pines and gray sand in Zambales" loading="lazy"><figcaption>Nagsasa Cove in Zambales, the quieter cousin of Anawangin. Agoho pines line the gray sand and the camping setup is minimal. Photo via <a href="https://commons.wikimedia.org/wiki/File:Nagsasa_Cove_Zambales.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'The local surf community\'s quiet base in San Felipe. Gray sand, consistent waves from October to March',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/la-union-san-juan-surf-beach-urbiztondo.jpg" alt="Surf beach sunset on the Luzon coast" loading="lazy"><figcaption>The Luzon surf coast at golden hour. Liwliwa in San Felipe runs the same long gray-sand reads, easier to reach than the bangka coves.</figcaption></figure>',
        ],
        [
            'anchor' => 'The Las Casas beach in Bagac, Bataan or the Mt. Samat side beaches like Five Fingers Cove.',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/bataan-province-las-casas-filipinas-de-acuzar.jpg" alt="Las Casas Filipinas de Acuzar heritage houses in Bagac Bataan" loading="lazy"><figcaption>Las Casas Filipinas de Acuzar in Bagac, Bataan. The reassembled Spanish-era houses sit on a beachfront, a calm pairing for a budget Bataan day.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // Coron town proper non-boat day
    // ------------------------------------------------------------------
    'coron-town-proper-calm-non-boat-day-things-to-do' => [
        [
            'anchor' => 'Mt. Tapyas is the hill that rises directly behind Coron town, marked by a large cross at the summit.',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/d/d1/Mount_Tapyas_Coron.jpg/800px-Mount_Tapyas_Coron.jpg" alt="Mt Tapyas cross above Coron town Palawan" loading="lazy"><figcaption>The Mt. Tapyas cross above Coron town. The 700-step climb is well marked with handrails and rest landings every 100 steps. Photo via <a href="https://commons.wikimedia.org/wiki/File:Mount_Tapyas_Coron.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'Maquinit Hot Spring sits around 10 minutes by tricycle from town, on the eastern side of Coron Bay.',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/2e/Maquinit_Hot_Spring_Coron.jpg/800px-Maquinit_Hot_Spring_Coron.jpg" alt="Maquinit saltwater hot spring pools in Coron Palawan" loading="lazy"><figcaption>Maquinit Hot Spring outside Coron town, the rare saltwater hot spring set in a mangrove area. Around 40 degrees Celsius and worth a slow evening soak. Photo via <a href="https://commons.wikimedia.org/wiki/File:Maquinit_Hot_Spring_Coron.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'The Coron public market is a short walk from the waterfront. The wet side has fresh fish brought in daily',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/5f/Coron_Public_Market_seafood.jpg/800px-Coron_Public_Market_seafood.jpg" alt="Coron public market wet section with fresh seafood" loading="lazy"><figcaption>The wet side of Coron public market. Pick a talakitok or lapulapu and a nearby carinderia will cook it for you for a small fee. Photo via <a href="https://commons.wikimedia.org/wiki/File:Coron_Public_Market_seafood.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // Bacolod to Iloilo ferry
    // ------------------------------------------------------------------
    'bacolod-to-iloilo-ferry-calm-day-crossing-read' => [
        [
            'anchor' => 'The Ruins in Talisay, the standout heritage stop just outside the city',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/bacolod-the-ruins-talisay.jpg" alt="The Ruins mansion shell in Talisay Negros Occidental" loading="lazy"><figcaption>The Ruins in Talisay, the burnt mansion shell of the Don Mariano Lacson estate. Late-afternoon light through the columns is the standard photo.</figcaption></figure>',
        ],
        [
            'anchor' => 'Manokan Country for chicken inasal grilled over coconut husk',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/bacolod-chicken-inasal.jpg" alt="Bacolod chicken inasal at Manokan Country" loading="lazy"><figcaption>Chicken inasal at Manokan Country in Bacolod. The annatto-oil basting and the coconut-husk smoke are the signature of the original Negrense version.</figcaption></figure>',
        ],
        [
            'anchor' => 'Calle Real, the heritage commercial street with Spanish-era buildings',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/iloilo-city-calle-real.jpg" alt="Calle Real heritage commercial street in Iloilo City" loading="lazy"><figcaption>Calle Real in Iloilo City, the heritage commercial spine lined with restored Spanish-era and American-period buildings.</figcaption></figure>',
        ],
        [
            'anchor' => 'La Paz Public Market for the original La Paz batchoy at Deco\'s or Netong\'s',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/iloilo-city-la-paz-batchoy.jpg" alt="La Paz batchoy noodle soup in Iloilo" loading="lazy"><figcaption>La Paz batchoy at the Iloilo public market. Pork offal, miki noodles, and chicharon over a clear broth, served at Deco\'s and Netong\'s.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // Apo Island Dauin
    // ------------------------------------------------------------------
    'apo-island-dauin-snorkel-turtles-day-trip' => [
        [
            'anchor' => 'The pawikan show up nearly every morning along the shallow seagrass beds',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/f/fb/Green_sea_turtle_at_Apo_Island.jpg/800px-Green_sea_turtle_at_Apo_Island.jpg" alt="Green sea turtle grazing the seagrass at Apo Island Negros" loading="lazy"><figcaption>A green sea turtle grazing the seagrass off Apo Island village beach. Morning is the best window before the day-tour boats arrive. Photo via <a href="https://commons.wikimedia.org/wiki/File:Green_sea_turtle_at_Apo_Island.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'Cross to Apo by 9 a.m. and walk to the village beach for the first snorkel.',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/91/Apo_Island_view_from_the_sea.jpg/800px-Apo_Island_view_from_the_sea.jpg" alt="Apo Island village beach view from the sea" loading="lazy"><figcaption>Apo Island viewed from the approach by banca. The village beach is on the leeward side and the marine sanctuary wall sits along the south coast. Photo via <a href="https://commons.wikimedia.org/wiki/File:Apo_Island_view_from_the_sea.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // Olango Island
    // ------------------------------------------------------------------
    'olango-island-day-trip-bird-sanctuary-mactan' => [
        [
            'anchor' => 'The Olango Island Wildlife Sanctuary protects around 920 hectares of wetland, mangroves, and intertidal flats.',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/4c/Olango_Island_Wildlife_Sanctuary.jpg/800px-Olango_Island_Wildlife_Sanctuary.jpg" alt="Olango Island Wildlife Sanctuary mangroves and tidal flats" loading="lazy"><figcaption>The Olango Island Wildlife Sanctuary at the southern end of the island. Mangrove boardwalks lead out to the viewing deck that overlooks the intertidal flats. Photo via <a href="https://commons.wikimedia.org/wiki/File:Olango_Island_Wildlife_Sanctuary.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'After the sanctuary, the standard add-on is a short banca to the Caw-oy sandbar off the northern side of the island.',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/7/76/Olango_sandbar_Cebu.jpg/800px-Olango_sandbar_Cebu.jpg" alt="Caw-oy sandbar off Olango Island near Mactan" loading="lazy"><figcaption>The Caw-oy sandbar off Olango appears at low tide. A calm shallow snorkel and grilled fish from the small cantina close out the slow Mactan day. Photo via <a href="https://commons.wikimedia.org/wiki/File:Olango_sandbar_Cebu.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],
];
