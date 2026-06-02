<?php

/**
 * Image overlays for Batch 2 blog posts (21 Luzon North slugs).
 *
 * Each entry keys a blog post slug to an array of insertion rules. The
 * BlogRenderer consults this overlay before output and splices the figure
 * HTML at the anchor (either 'before' or 'after' the anchor substring).
 *
 * Anchors are case-sensitive substrings that appear EXACTLY ONCE in the
 * raw content_html of the target post. Figures use the .rg-figure class
 * for downstream styling.
 *
 * Image sources: prefer existing /storage/rg-media/ assets, fall back to
 * Wikimedia Commons (thumb URLs at 800px). No duplicate image src across
 * this overlay batch.
 */

return [

    // 1. Lipa Batangas coffee + heritage
    'lipa-batangas-coffee-heritage-day-trip' => [
        [
            'anchor' => 'Cafe de Lipa is the institution',
            'position' => 'before',
            'html' => <<<'HTML'
<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/0/0e/Alfonso_kapeng_barako.jpg/800px-Alfonso_kapeng_barako.jpg" alt="Cup of kapeng barako with whole roasted beans on a tray" loading="lazy"><figcaption>Kapeng barako, the Liberica coffee variety that Lipa farmers have been replanting since the 1980s.</figcaption></figure>
HTML
        ],
        [
            'anchor' => 'Walk over to the Lipa City Cathedral',
            'position' => 'before',
            'html' => <<<'HTML'
<figure class="rg-figure"><img src="/storage/rg-media/spots/lipa-san-sebastian-cathedral.jpg" alt="Facade of San Sebastian Cathedral in Lipa, Batangas" loading="lazy"><figcaption>Lipa City Cathedral, formally the San Sebastian Cathedral, with its Romanesque facade dating to the late 1800s.</figcaption></figure>
HTML
        ],
        [
            'anchor' => 'Casa de Segunda, one of the oldest heritage houses',
            'position' => 'before',
            'html' => <<<'HTML'
<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/f/fe/Casa_de_Segunda_%28Lipa%29.jpg/800px-Casa_de_Segunda_%28Lipa%29.jpg" alt="Wooden facade of the Casa de Segunda ancestral house in Lipa" loading="lazy"><figcaption>Casa de Segunda, the 1862 Katigbak ancestral house in Lipa. Photo via <a href="https://commons.wikimedia.org/wiki/File:Casa_de_Segunda_(Lipa).jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>
HTML
        ],
        [
            'anchor' => 'Lomi is the Lipa specialty',
            'position' => 'before',
            'html' => <<<'HTML'
<figure class="rg-figure"><img src="/storage/rg-media/foods/lipa-lomi.jpg" alt="Bowl of thick Lipa lomi noodles with pork liver and chicharon" loading="lazy"><figcaption>A Lipa lomihan bowl: thick wheat noodles in starchy broth, topped with pork liver, chicharon, and egg.</figcaption></figure>
HTML
        ],
    ],

    // 2. Lobo Batangas Malabrigo Lighthouse
    'lobo-batangas-malabrigo-lighthouse-day-trip' => [
        [
            'anchor' => 'The Malabrigo Lighthouse was designed by Spanish engineer',
            'position' => 'before',
            'html' => <<<'HTML'
<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/cd/Tower_of_Malabrigo_Lighthouse_in_Lobo%2C_Batangas%2C_Philippines_%282025%29..jpg/800px-Tower_of_Malabrigo_Lighthouse_in_Lobo%2C_Batangas%2C_Philippines_%282025%29..jpg" alt="Brick tower of Malabrigo Lighthouse in Lobo, Batangas" loading="lazy"><figcaption>Malabrigo Lighthouse, the 1891 Spanish-era brick tower in Lobo declared a National Historical Landmark in 2006. Photo via <a href="https://commons.wikimedia.org/wiki/File:Tower_of_Malabrigo_Lighthouse_in_Lobo,_Batangas,_Philippines_(2025)..jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>
HTML
        ],
        [
            'anchor' => 'The shore at Malabrigo is unusual',
            'position' => 'before',
            'html' => <<<'HTML'
<figure class="rg-figure"><img src="/storage/rg-media/spots/lobo-sawang-beach.jpg" alt="Pebble shore on the Lobo coast facing the Verde Island Passage" loading="lazy"><figcaption>The Lobo coast near Malabrigo. Pebble shores instead of sand keep the water unusually clear.</figcaption></figure>
HTML
        ],
    ],

    // 3. Mt Maculot day hike Cuenca
    'mt-maculot-day-hike-cuenca-from-manila' => [
        [
            'anchor' => 'The Rockies is the most famous part',
            'position' => 'before',
            'html' => <<<'HTML'
<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/3/36/Mount_Macolod.jpg/800px-Mount_Macolod.jpg" alt="View of Taal Lake from the Mt Maculot ridge in Cuenca, Batangas" loading="lazy"><figcaption>The view from Mt Maculot toward Taal Lake and the volcano island, the scene that earns the Rockies its photo. Photo via <a href="https://commons.wikimedia.org/wiki/File:Mount_Macolod.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>
HTML
        ],
        [
            'anchor' => 'The actual summit sits above the Rockies',
            'position' => 'before',
            'html' => <<<'HTML'
<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/66/Mt._Maculot.jpg/800px-Mt._Maculot.jpg" alt="Forested upper slopes of Mt Maculot near the 930 meter summit" loading="lazy"><figcaption>The forested approach to the Mt Maculot summit at 930 meters above sea level. Photo via <a href="https://commons.wikimedia.org/wiki/File:Mt._Maculot.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>
HTML
        ],
    ],

    // 4. Caleruega Alfonso Cavite
    'caleruega-alfonso-cavite-quiet-retreat' => [
        [
            'anchor' => 'The Transfiguration Chapel is the centerpiece',
            'position' => 'before',
            'html' => <<<'HTML'
<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/64/Chapel_of_Transfiguration_Facade.jpg/800px-Chapel_of_Transfiguration_Facade.jpg" alt="Facade of the Transfiguration Chapel at Caleruega, Nasugbu" loading="lazy"><figcaption>The Transfiguration Chapel at Caleruega, set on a hill overlooking Mt Batulao. Photo via <a href="https://commons.wikimedia.org/wiki/File:Chapel_of_Transfiguration_Facade.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>
HTML
        ],
        [
            'anchor' => 'The koi pond at the base of the steps',
            'position' => 'before',
            'html' => <<<'HTML'
<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/d/de/Chapel_of_Transfiguration_Altar.jpg/800px-Chapel_of_Transfiguration_Altar.jpg" alt="Stained glass altar inside the Transfiguration Chapel at Caleruega" loading="lazy"><figcaption>The stained glass altar inside the Caleruega chapel, depicting the Transfiguration scene. Photo via <a href="https://commons.wikimedia.org/wiki/File:Chapel_of_Transfiguration_Altar.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>
HTML
        ],
    ],

    // 5. Baguio summer weekend
    'baguio-summer-weekend-skip-long-weekend-madness' => [
        [
            'anchor' => 'Burnham Park is at its best before 9 a.m.',
            'position' => 'before',
            'html' => <<<'HTML'
<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/d/d6/Burnham_Park_Lagoon.jpg/800px-Burnham_Park_Lagoon.jpg" alt="Burnham Park lagoon with swan boats in Baguio City" loading="lazy"><figcaption>Burnham Park lagoon at the center of Baguio, where the swan boats are rented out by the hour. Photo via <a href="https://commons.wikimedia.org/wiki/File:Burnham_Park_Lagoon.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>
HTML
        ],
        [
            'anchor' => 'Hit BenCab Museum in Tuba first while',
            'position' => 'before',
            'html' => <<<'HTML'
<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/ce/BenCab_Gallery%2C_BenCab_Museum%2C_Tuba%2C_Benguet.jpg/800px-BenCab_Gallery%2C_BenCab_Museum%2C_Tuba%2C_Benguet.jpg" alt="Interior gallery at the BenCab Museum in Tuba, Benguet" loading="lazy"><figcaption>The BenCab Museum gallery in Tuba, the calm alternative to the morning queue at Mines View. Photo via <a href="https://commons.wikimedia.org/wiki/File:BenCab_Gallery,_BenCab_Museum,_Tuba,_Benguet.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>
HTML
        ],
    ],

    // 6. Baguio rainy season cafe crawl
    'baguio-rainy-season-cafe-crawl-two-days' => [
        [
            'anchor' => 'BenCab Museum in Tuba is the rainy-season anchor',
            'position' => 'before',
            'html' => <<<'HTML'
<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/3/3b/Views_from_BenCab_Museum%2C_Tuba%2C_Benguet_%281%29.jpg/800px-Views_from_BenCab_Museum%2C_Tuba%2C_Benguet_%281%29.jpg" alt="Misty view from the BenCab Museum deck in Tuba, Benguet" loading="lazy"><figcaption>The ravine view from the BenCab Museum deck, half-disappeared into the afternoon fog. Photo via <a href="https://commons.wikimedia.org/wiki/File:Views_from_BenCab_Museum,_Tuba,_Benguet_(1).jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>
HTML
        ],
        [
            'anchor' => 'Head to Choco-Late de Batirol inside Camp John Hay',
            'position' => 'before',
            'html' => <<<'HTML'
<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/f/f4/Camp_John_Hay_Garden_%2812438180775%29.jpg/800px-Camp_John_Hay_Garden_%2812438180775%29.jpg" alt="Pine-shaded garden path inside Camp John Hay, Baguio" loading="lazy"><figcaption>Inside Camp John Hay, the pine-shaded garden setting where Choco-Late de Batirol serves its tablea hot chocolate. Photo via <a href="https://commons.wikimedia.org/wiki/File:Camp_John_Hay_Garden_(12438180775).jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>
HTML
        ],
    ],

    // 7. Sagada Echo Valley hike
    'sagada-echo-valley-hike-two-day-plan' => [
        [
            'anchor' => 'The Hanging Coffins viewpoint is around 30 minutes in',
            'position' => 'before',
            'html' => <<<'HTML'
<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/3/39/Hanging_Coffins_in_Sagada.jpg/800px-Hanging_Coffins_in_Sagada.jpg" alt="Wooden coffins nailed to a limestone cliff at Echo Valley, Sagada" loading="lazy"><figcaption>The hanging coffins of Echo Valley, an Igorot burial tradition that goes back centuries. Photo via <a href="https://commons.wikimedia.org/wiki/File:Hanging_Coffins_in_Sagada.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>
HTML
        ],
        [
            'anchor' => 'Sunrise at Kiltepan Viewpoint is the closer',
            'position' => 'before',
            'html' => <<<'HTML'
<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/7/79/Kiltepan_viewpoint_%28Sagada%2C_Mountain_Province%3B_12-02-2022%29.jpg/800px-Kiltepan_viewpoint_%28Sagada%2C_Mountain_Province%3B_12-02-2022%29.jpg" alt="View of rice terraces below Kiltepan Viewpoint in Sagada at dawn" loading="lazy"><figcaption>Kiltepan Viewpoint at sunrise, where Sagada's rice terraces emerge below the morning clouds. Photo via <a href="https://commons.wikimedia.org/wiki/File:Kiltepan_viewpoint_(Sagada,_Mountain_Province;_12-02-2022).jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>
HTML
        ],
    ],

    // 8. Banaue + Batad rice terraces
    'banaue-batad-three-day-diy-rice-terraces' => [
        [
            'anchor' => 'Hire a tricycle for the standard viewpoint tour',
            'position' => 'before',
            'html' => <<<'HTML'
<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/f/f8/Pana_Banaue_Rice_Terraces_%28Cropped%29.jpg/800px-Pana_Banaue_Rice_Terraces_%28Cropped%29.jpg" alt="Panorama of the Banaue Rice Terraces with stepped paddies climbing the slope" loading="lazy"><figcaption>The Banaue Rice Terraces from the main viewpoint, the scene printed on the old 1000-peso bill. Photo via <a href="https://commons.wikimedia.org/wiki/File:Pana_Banaue_Rice_Terraces_(Cropped).jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>
HTML
        ],
        [
            'anchor' => 'From the Saddle Point, the walk down to Batad village',
            'position' => 'before',
            'html' => <<<'HTML'
<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/2e/Batad_Rice_Terraces_%28aerial%29.jpg/800px-Batad_Rice_Terraces_%28aerial%29.jpg" alt="Aerial view of the Batad rice terrace amphitheater in Ifugao" loading="lazy"><figcaption>The Batad amphitheater, only reachable on foot from the Saddle Point and worth the descent. Photo via <a href="https://commons.wikimedia.org/wiki/File:Batad_Rice_Terraces_(aerial).jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>
HTML
        ],
    ],

    // 9. La Union surf weekend
    'la-union-surf-weekend-urbiztondo-first-timers' => [
        [
            'anchor' => 'Surf lessons run all along the Urbiztondo beachfront',
            'position' => 'before',
            'html' => <<<'HTML'
<figure class="rg-figure"><img src="/storage/rg-media/spots/la-union-san-juan-surf-beach-urbiztondo.jpg" alt="Surfers in the water at Urbiztondo Beach, San Juan, La Union" loading="lazy"><figcaption>Urbiztondo Beach in San Juan, La Union. The beachbreak is gentle enough for first-timer lessons.</figcaption></figure>
HTML
        ],
    ],

    // 10. Pagudpud Saud Beach
    'pagudpud-saud-beach-slow-two-night-plan' => [
        [
            'anchor' => 'First stop is the Bangui Wind Farm',
            'position' => 'before',
            'html' => <<<'HTML'
<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/0/05/Bangui_Wind_Farm_mills_west_waves_%28Bangui%2C_Ilocos_Norte%3B_11-17-2022%29.jpg/800px-Bangui_Wind_Farm_mills_west_waves_%28Bangui%2C_Ilocos_Norte%3B_11-17-2022%29.jpg" alt="Row of white wind turbines at Bangui Wind Farm facing the South China Sea" loading="lazy"><figcaption>The Bangui Wind Farm along Bangui Bay, the row of 20 turbines that anchors the northern Ilocos loop. Photo via <a href="https://commons.wikimedia.org/wiki/File:Bangui_Wind_Farm_mills_west_waves_(Bangui,_Ilocos_Norte;_11-17-2022).jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>
HTML
        ],
        [
            'anchor' => 'Cap the loop at Cape Bojeador Lighthouse',
            'position' => 'before',
            'html' => <<<'HTML'
<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/d/d0/Cape_Bojeador_Lighthouse_2012-09-09_23-09-51.jpg/800px-Cape_Bojeador_Lighthouse_2012-09-09_23-09-51.jpg" alt="Brick tower of Cape Bojeador Lighthouse on a hill in Burgos, Ilocos Norte" loading="lazy"><figcaption>Cape Bojeador Lighthouse in Burgos, built 1892 and still operating as the northern coast beacon. Photo via <a href="https://commons.wikimedia.org/wiki/File:Cape_Bojeador_Lighthouse_2012-09-09_23-09-51.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>
HTML
        ],
        [
            'anchor' => 'The small carinderias along the Saud row',
            'position' => 'before',
            'html' => <<<'HTML'
<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/6c/Saud_Beach_-_panoramio.jpg/800px-Saud_Beach_-_panoramio.jpg" alt="Fine white sand and palms along Saud Beach in Pagudpud, Ilocos Norte" loading="lazy"><figcaption>Saud Beach in Pagudpud, the 3-kilometer fine-sand stretch that anchors most overnight stays. Photo via <a href="https://commons.wikimedia.org/wiki/File:Saud_Beach_-_panoramio.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>
HTML
        ],
    ],

    // 11. Vigan calesa + cobblestones
    'vigan-two-days-calesa-cobblestones-ilocano-plates' => [
        [
            'anchor' => 'Start at Plaza Salcedo, where the fountain dancing show',
            'position' => 'before',
            'html' => <<<'HTML'
<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/4e/Vigan_heritage_village_Calle_Crisologo-Gen_Luna_calesa_%28Vigan%2C_Ilocos_Sur%3B_11-14-2022%29.jpg/800px-Vigan_heritage_village_Calle_Crisologo-Gen_Luna_calesa_%28Vigan%2C_Ilocos_Sur%3B_11-14-2022%29.jpg" alt="Calesa rolling down the cobblestoned Calle Crisologo in Vigan" loading="lazy"><figcaption>Calle Crisologo, the 600-meter cobblestone street closed to cars and lined with 18th and 19th-century houses. Photo via <a href="https://commons.wikimedia.org/wiki/File:Vigan_heritage_village_Calle_Crisologo-Gen_Luna_calesa_(Vigan,_Ilocos_Sur;_11-14-2022).jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>
HTML
        ],
        [
            'anchor' => 'Walk to Plaza Burgos by 9 p.m. for the empanada row',
            'position' => 'before',
            'html' => <<<'HTML'
<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/47/Vigan_Empanada.jpg/800px-Vigan_Empanada.jpg" alt="Vigan empanada with orange rice flour crust and longganisa filling" loading="lazy"><figcaption>Vigan empanada at Plaza Burgos: bright orange rice flour crust, longganisa, egg, and shredded papaya, fried to order. Photo via <a href="https://commons.wikimedia.org/wiki/File:Vigan_Empanada.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>
HTML
        ],
        [
            'anchor' => 'Cap the day at Bantay Bell Tower',
            'position' => 'before',
            'html' => <<<'HTML'
<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/ee/Crispy_Bagnet%2C_Mar_2024.jpg/800px-Crispy_Bagnet%2C_Mar_2024.jpg" alt="Plate of crispy bagnet, the Ilocano deep-fried pork belly" loading="lazy"><figcaption>Bagnet, the deep-fried Ilocano pork belly that goes on every Vigan lunch table at least once. Photo via <a href="https://commons.wikimedia.org/wiki/File:Crispy_Bagnet,_Mar_2024.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>
HTML
        ],
    ],

    // 12. Bangui Windmills loop (one day)
    'bangui-windmills-northern-ilocos-loop-one-day' => [
        [
            'anchor' => 'The Kapurpurawan Rock Formation in Burgos is the first major stop',
            'position' => 'before',
            'html' => <<<'HTML'
<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/29/Kapurpurawan_Rock_Formation_Medium_shot_2021.jpg/800px-Kapurpurawan_Rock_Formation_Medium_shot_2021.jpg" alt="Creamy white limestone formations at Kapurpurawan Rock in Burgos" loading="lazy"><figcaption>Kapurpurawan Rock Formation in Burgos, white limestone shaped by centuries of wind and salt. Photo via <a href="https://commons.wikimedia.org/wiki/File:Kapurpurawan_Rock_Formation_Medium_shot_2021.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>
HTML
        ],
        [
            'anchor' => 'Next is Cape Bojeador Lighthouse, also in Burgos',
            'position' => 'before',
            'html' => <<<'HTML'
<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/2f/A_closer_view_of_Cape_Bojeador_Lighthouse.jpg/800px-A_closer_view_of_Cape_Bojeador_Lighthouse.jpg" alt="Close view of Cape Bojeador Lighthouse brick balcony and tower" loading="lazy"><figcaption>A close view of Cape Bojeador Lighthouse, one of the oldest functioning Spanish-era lighthouses in the country. Photo via <a href="https://commons.wikimedia.org/wiki/File:A_closer_view_of_Cape_Bojeador_Lighthouse.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>
HTML
        ],
        [
            'anchor' => 'The Bangui Wind Farm sits along Bangui Bay',
            'position' => 'before',
            'html' => <<<'HTML'
<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/af/Blown_by_the_wind.jpg/800px-Blown_by_the_wind.jpg" alt="Panoramic line of wind turbines stretching along Bangui Bay" loading="lazy"><figcaption>The 20 turbines of the Bangui Wind Farm, each 70 meters tall, lining the coast facing the South China Sea. Photo via <a href="https://commons.wikimedia.org/wiki/File:Blown_by_the_wind.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>
HTML
        ],
        [
            'anchor' => 'From Bangui, the drive continues north to Pagudpud',
            'position' => 'before',
            'html' => <<<'HTML'
<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/ec/A_View_via_the_Patapat_Viaduct.jpg/800px-A_View_via_the_Patapat_Viaduct.jpg" alt="Curved Patapat Viaduct hugging the cliffside between mountain and sea" loading="lazy"><figcaption>Patapat Viaduct, the 1.3-kilometer coastal bridge that hugs the cliffside before Pagudpud town. Photo via <a href="https://commons.wikimedia.org/wiki/File:A_View_via_the_Patapat_Viaduct.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>
HTML
        ],
    ],

    // 13. Hundred Islands Alaminos
    'hundred-islands-alaminos-diy-day-plan-first-timers' => [
        [
            'anchor' => 'Governor Island has the viewdeck',
            'position' => 'before',
            'html' => <<<'HTML'
<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/7/73/Governors_Island%2C_Hundred_Islands_Pangasinan.jpg/800px-Governors_Island%2C_Hundred_Islands_Pangasinan.jpg" alt="Aerial view of Governor Island and surrounding islets at Hundred Islands" loading="lazy"><figcaption>Governor Island in Hundred Islands National Park. The viewdeck at the top covers most of the surrounding 124 islets. Photo via <a href="https://commons.wikimedia.org/wiki/File:Governors_Island,_Hundred_Islands_Pangasinan.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>
HTML
        ],
        [
            'anchor' => 'Quezon Island is the largest of the three',
            'position' => 'before',
            'html' => <<<'HTML'
<figure class="rg-figure"><img src="/storage/rg-media/spots/alaminos-hundred-islands-quezon-island.jpg" alt="Sandy beach and picnic huts on Quezon Island, Hundred Islands" loading="lazy"><figcaption>Quezon Island, the largest of the developed three, with the best swimming beach and the cottage row.</figcaption></figure>
HTML
        ],
    ],

    // 14. Bolinao Patar Beach
    'bolinao-patar-beach-two-day-western-pangasinan' => [
        [
            'anchor' => 'Wonderful Cave is the first stop',
            'position' => 'before',
            'html' => <<<'HTML'
<figure class="rg-figure"><img src="/storage/rg-media/spots/bolinao-bolinao-falls-1-2-3.jpg" alt="Cool water pool at Bolinao Falls in inland Pangasinan" loading="lazy"><figcaption>Bolinao Falls in the inland barangay, the short stop between the cave system and Cape Bolinao.</figcaption></figure>
HTML
        ],
        [
            'anchor' => 'Cape Bolinao Lighthouse sits on the highest point of the peninsula',
            'position' => 'before',
            'html' => <<<'HTML'
<figure class="rg-figure"><img src="/storage/rg-media/spots/bolinao-cape-bolinao-lighthouse.jpg" alt="Cape Bolinao Lighthouse on a hilltop in western Pangasinan" loading="lazy"><figcaption>Cape Bolinao Lighthouse, built in 1903 on the highest point of the western Pangasinan peninsula.</figcaption></figure>
HTML
        ],
        [
            'anchor' => 'Patar Beach is the long fine-sand stretch on the western coast',
            'position' => 'before',
            'html' => <<<'HTML'
<figure class="rg-figure"><img src="/storage/rg-media/spots/bolinao-patar-beach.jpg" alt="Gold-tinted sand at Patar Beach in Bolinao at low tide" loading="lazy"><figcaption>Patar Beach, the gold-tinted fine-sand stretch on Bolinao's western coast.</figcaption></figure>
HTML
        ],
    ],

    // 15. Subic family weekend (Zoobic + Ocean Adventure)
    'subic-family-weekend-zoobic-ocean-adventure' => [
        [
            'anchor' => 'The Tiger Safari is the headline',
            'position' => 'before',
            'html' => <<<'HTML'
<figure class="rg-figure"><img src="/storage/rg-media/spots/subic-tree-top-adventure.jpg" alt="Forest canopy and adventure ride at Tree Top Adventure in Subic" loading="lazy"><figcaption>Inside the Subic Bay Freeport, where Zoobic Safari and Tree Top Adventure sit within easy driving distance.</figcaption></figure>
HTML
        ],
        [
            'anchor' => 'The dolphin and false killer whale show in the main lagoon',
            'position' => 'before',
            'html' => <<<'HTML'
<figure class="rg-figure"><img src="/storage/rg-media/spots/subic-ocean-adventure.jpg" alt="Open-water lagoon at Ocean Adventure marine park in Subic" loading="lazy"><figcaption>The open-water lagoon at Ocean Adventure in Camayan, where the dolphin and sea lion shows run on a fixed daily schedule.</figcaption></figure>
HTML
        ],
    ],

    // 16. Las Casas Filipinas Bagac
    'las-casas-filipinas-acuzar-bagac-bataan-day-tour' => [
        [
            'anchor' => 'The walking tour starts at the visitor center',
            'position' => 'before',
            'html' => <<<'HTML'
<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/4a/Las_Casas_Filipinas_de_Acuzar.jpg/800px-Las_Casas_Filipinas_de_Acuzar.jpg" alt="Restored Spanish-colonial casa at Las Casas Filipinas de Acuzar in Bagac" loading="lazy"><figcaption>One of the restored casas at Las Casas Filipinas de Acuzar, the heritage park that reassembled houses from across the country. Photo via <a href="https://commons.wikimedia.org/wiki/File:Las_Casas_Filipinas_de_Acuzar.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>
HTML
        ],
        [
            'anchor' => 'After the walking tour, the calesa picks you up',
            'position' => 'before',
            'html' => <<<'HTML'
<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/d/d0/Old_house_in_Las_Casas_de_Acuzar.jpg/800px-Old_house_in_Las_Casas_de_Acuzar.jpg" alt="Hardwood and stone heritage house along the Umagol River at Las Casas" loading="lazy"><figcaption>A restored hardwood and stone house at Las Casas, set along the Umagol River that cuts through the property. Photo via <a href="https://commons.wikimedia.org/wiki/File:Old_house_in_Las_Casas_de_Acuzar.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>
HTML
        ],
    ],

    // 17. Morong Bataan Pawikan visit
    'morong-bataan-pawikan-conservation-visit' => [
        [
            'anchor' => 'The Pawikan Conservation Center sits along Nagbalayong',
            'position' => 'before',
            'html' => <<<'HTML'
<figure class="rg-figure"><img src="/storage/rg-media/spots/morong-bataan-sabang-beach.jpg" alt="Wide sand stretch and surf along the Morong, Bataan coast" loading="lazy"><figcaption>The Morong coast near Nagbalayong, where olive ridley turtles nest between November and February.</figcaption></figure>
HTML
        ],
    ],

    // 18. Mt Pulag for beginners
    'mt-pulag-beginners-two-day-diy-ambangeg' => [
        [
            'anchor' => 'You should reach the summit by 5 a.m.',
            'position' => 'before',
            'html' => <<<'HTML'
<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/29/Hikers_watching_sunrise_at_Mount_Pulag_summit.jpg/800px-Hikers_watching_sunrise_at_Mount_Pulag_summit.jpg" alt="Hikers on the Mt Pulag summit watching sunrise over a sea of clouds" loading="lazy"><figcaption>Sunrise on the Mt Pulag summit at 2,922 meters, with the famous sea of clouds filling the valleys below. Photo via <a href="https://commons.wikimedia.org/wiki/File:Hikers_watching_sunrise_at_Mount_Pulag_summit.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>
HTML
        ],
    ],

    // 19. Tarlac two days
    'tarlac-two-days-monasterio-hacienda-loop' => [
        [
            'anchor' => 'The Aquino Center in Hacienda Luisita',
            'position' => 'before',
            'html' => <<<'HTML'
<figure class="rg-figure"><img src="/storage/rg-media/spots/tarlac-aquino-center-and-museum.jpg" alt="Facade of the Aquino Center and Museum in Hacienda Luisita, Tarlac" loading="lazy"><figcaption>The Aquino Center and Museum inside Hacienda Luisita, with exhibits on Ninoy, Cory, and the post-EDSA timeline.</figcaption></figure>
HTML
        ],
        [
            'anchor' => 'The monastery is run by the Servants of the Risen Christ',
            'position' => 'before',
            'html' => <<<'HTML'
<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/0/01/Tarlac_Monastery_statue_-_panoramio.jpg/800px-Tarlac_Monastery_statue_-_panoramio.jpg" alt="30-foot Risen Christ statue at Monasterio de Tarlac on Mount Resurrection" loading="lazy"><figcaption>The 30-foot Risen Christ statue at Monasterio de Tarlac on Mount Resurrection, facing the Zambales range. Photo via <a href="https://commons.wikimedia.org/wiki/File:Tarlac_Monastery_statue_-_panoramio.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>
HTML
        ],
    ],

    // 20. Clark food trip
    'clark-food-trip-korea-town-kapampangan-weekend' => [
        [
            'anchor' => 'Sisig was born in Angeles, and the proper sisig run',
            'position' => 'before',
            'html' => <<<'HTML'
<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/48/Sizzling_Sisig.jpg/800px-Sizzling_Sisig.jpg" alt="Sizzling pork sisig on a hot plate with calamansi and chilies" loading="lazy"><figcaption>Sizzling pork sisig, the dish that Aling Lucing in Angeles is credited with inventing. Photo via <a href="https://commons.wikimedia.org/wiki/File:Sizzling_Sisig.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>
HTML
        ],
        [
            'anchor' => 'Korea Town runs along Friendship Highway',
            'position' => 'before',
            'html' => <<<'HTML'
<figure class="rg-figure"><img src="/storage/rg-media/spots/angeles-clark-freeport-zone-hann-casino-marriott-quest.jpg" alt="Clark Freeport Zone with wide avenues and hotel row near Angeles City" loading="lazy"><figcaption>Clark Freeport Zone, the gateway to Angeles City where Korea Town runs along Friendship Highway just outside the gate.</figcaption></figure>
HTML
        ],
    ],

    // 21. Dingalan Aurora
    'dingalan-aurora-cliffs-lighthouse-diy-weekend' => [
        [
            'anchor' => 'The trail to the viewpoint takes around 30 to 45 minutes',
            'position' => 'before',
            'html' => <<<'HTML'
<figure class="rg-figure"><img src="/storage/rg-media/destinations/dingalan-1.jpg" alt="Limestone cliffs and Pacific coast at Dingalan, Aurora" loading="lazy"><figcaption>The Dingalan headland, the limestone cliff face that earns the town its Batanes-of-the-East comparison.</figcaption></figure>
HTML
        ],
        [
            'anchor' => 'The Dingalan Lighthouse is a small white-painted tower',
            'position' => 'before',
            'html' => <<<'HTML'
<figure class="rg-figure"><img src="/storage/rg-media/spots/dingalan-white-beach.jpg" alt="White Beach in Dingalan reached by a short bangka ride from the town pier" loading="lazy"><figcaption>White Beach in Dingalan, reached by a short bangka ride from the town pier. The water is calmer than the open Pacific side.</figcaption></figure>
HTML
        ],
    ],

];
