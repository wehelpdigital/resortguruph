<?php

/**
 * Image overlays for blog posts in batch 15 (outdoor + adventure guides).
 *
 * Forty new Philippines posts focused on hikes, climbs, dive sites, falls,
 * surf, and kayak routes. Each entry keys off the blog post slug. Each
 * anchor must appear EXACTLY ONCE inside that post's content_html; the
 * figure HTML is inserted immediately after the anchor block. Image srcs
 * do not duplicate across this batch.
 */

return [

    // ------------------------------------------------------------------
    // 1. MT. ROMELO AND BURUWISAN FALLS
    // ------------------------------------------------------------------
    'mt-romelo-buruwisan-falls-siniloan-overnight-trek' => [
        [
            'anchor' => '<p>The middle third of the trail is the famous slide. After any rainfall, the path becomes a smooth chute of brown clay that the early trekkers have polished into a near-slick surface. Going up takes the use of branches, roots, and the help of the trekkers ahead. Coming down is its own kwento, the locals slide down on their backsides and laugh; first-timers tend to fall four or five times.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/87/Buruwisan_Falls_trail_mud.jpg/800px-Buruwisan_Falls_trail_mud.jpg" alt="Mt. Romelo muddy trail to Buruwisan Falls in Siniloan, Laguna" loading="lazy"><figcaption>The famous mudslide section of Mt. Romelo, polished into a clay chute by years of trekker boots. Photo via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>From camp, a short and very steep descent leads down to the base of Buruwisan Falls. The drop is around 30 meters, into a wide pool you can swim in. Locals know the route to two more falls, Batya-Batya and Sampaloc, that are 20 to 40 minutes farther downstream. If you have the afternoon and a guide, take all three. If not, Buruwisan alone is the photo and the swim that pays back the climb.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/2a/Buruwisan_Falls_Siniloan_Laguna.jpg/800px-Buruwisan_Falls_Siniloan_Laguna.jpg" alt="Buruwisan Falls 30-meter drop in Siniloan, Laguna" loading="lazy"><figcaption>Buruwisan Falls, the 30-meter drop into a wide pool that pays back the muddy Mt. Romelo climb. Image courtesy Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>This is the technical part of the trip, not the climb up the mountain. The descent uses fixed ropes that the local guides maintain. Wear gloves, the rope burn is real if you slide. Trail runners with good grip handle the rocks better than open sandals.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/nagcarlan-2.jpg" alt="Forested Laguna trail near Siniloan" loading="lazy"><figcaption>The Laguna foothill forest near Siniloan that hides the Buruwisan rope descent and the secondary falls downstream.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 2. MT. TALAMITAM
    // ------------------------------------------------------------------
    'mt-talamitam-day-hike-nasugbu-batangas' => [
        [
            'anchor' => '<p>Past the second river crossing, the trail starts to climb. The grade is steady, not steep, but the cogon grass closes in on the path and the sun hits you with no break. By 8 a.m. the upper slopes feel like a hairdryer. Mark each false summit, the ridge has three of them, and the actual summit is the fourth knob.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/6e/Mt_Talamitam_cogon_grass_trail.jpg/800px-Mt_Talamitam_cogon_grass_trail.jpg" alt="Mt. Talamitam cogon grass ridge in Nasugbu, Batangas" loading="lazy"><figcaption>The shoulder-high cogon ridge of Mt. Talamitam, where the trail climbs through four false summits before the real peak. Photo via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The peak is a wide grass clearing with a view of Mt. Batulao to the southwest and the West Philippine Sea beyond. On clear mornings you can also see Mt. Pico de Loro and the Maragondon range. The summit fits maybe 20 trekkers comfortably; on weekends from December to March it is fuller than that.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/nasugbu-mt-pico-de-loro-parrots-beak.jpg" alt="Mt. Pico de Loro from the Nasugbu ridge" loading="lazy"><figcaption>Mt. Pico de Loro and the Maragondon range as seen from the southern Batangas ridges, the view that opens at the Talamitam summit on clear mornings.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Tagaytay is the obvious lunch stop on the way home. The bulalo strip in Mahogany Market is the standard order, but the longer kwento from regular Talamitam climbers is to stop at any of the carinderias along the Nasugbu highway for tapsilog, then push for the longer Tagaytay lunch only if the group still has appetite. The first food after a sun-heavy climb tends to taste better than the planned destination.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/tagaytay-mahogany-market.jpg" alt="Tagaytay Mahogany Market bulalo strip" loading="lazy"><figcaption>The Mahogany Market bulalo strip in Tagaytay, the standard lunch stop on the way home from a Nasugbu climb.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 3. MT. SEMBRANO
    // ------------------------------------------------------------------
    'mt-sembrano-day-hike-pililla-laguna-de-bay-views' => [
        [
            'anchor' => '<p>The forest opens to a cogon ridge that runs along the spine of the mountain for the last 45 minutes to the summit. The view of Laguna de Bay opens up on the left, the Sierra Madre on the right, and the trail is a clean walk on grass. Photos here look more like the Cordilleras than Rizal.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/binangonan-laguna-de-bay-shoreline.jpg" alt="Laguna de Bay shoreline from Rizal" loading="lazy"><figcaption>The Laguna de Bay sweep that opens to the left on the final cogon ridge of Mt. Sembrano, with Talim Island sitting in the middle of the lake.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The Pililla wind farm is on the same road as the Sembrano jump-off. The viewing deck for the turbines is free and the road up is paved. Combine the climb with a wind-farm stop on the way back; the turbines turn slowly against the Laguna de Bay backdrop and the photos there are quiet. The deck has a small cafeteria for halo-halo and grilled corn.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/9e/Pililla_Wind_Farm_turbines.jpg/800px-Pililla_Wind_Farm_turbines.jpg" alt="Pililla wind farm turbines in Rizal" loading="lazy"><figcaption>The Pililla wind farm, the standard side stop on the drive back from the Sembrano jump-off. Image via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Tanay is the food stop on the drive home. The town has a few longtime carinderias along the highway that serve calderetang kambing and tilapia from Laguna de Bay. Two of the bigger pasalubong stops are between Tanay and Antipolo, with suman and kakanin laid out fresh in the afternoons. If you have not eaten at any of the lakeside grill stands in Tanay, this is the trip to try one.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/tanay-daranak-falls.jpg" alt="Tanay highlands, Rizal" loading="lazy"><figcaption>The Tanay highlands above Daranak, where the lakeside grill stands and pasalubong pop-ups sit along the Marcos Highway descent.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 4. MT. MAYNUBA AND CABUYAO TWIN FALLS
    // ------------------------------------------------------------------
    'mt-maynuba-cabuyao-twin-falls-tanay-day-trek' => [
        [
            'anchor' => '<p>The middle section is a long ridge with periodic views of the Sierra Madre wall on the east side. Wild bamboo lines the trail and the bird sounds carry through the canopy. The grade is moderate, not punishing, and the rest stops have flat patches that work for a 10-minute break.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/tanay-2.jpg" alt="Sierra Madre foothills above Tanay" loading="lazy"><figcaption>The Sierra Madre wall on the east side of the Maynuba ridge, the view that opens between the wild bamboo stands above Cuyambay.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The route to the falls drops sharply behind the summit. The path is rooty, sometimes muddy, with two fixed-rope sections where the slope steepens. Take this part slow. The falls open into a narrow ravine with a wide pool at the base, fed by two parallel spillways from the upper river. The water is cold, the pool depth is around chest-high in the middle, and the locals say it is safe to swim across.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/4f/Cabuyao_Twin_Falls_Tanay_Rizal.jpg/800px-Cabuyao_Twin_Falls_Tanay_Rizal.jpg" alt="Cabuyao Twin Falls in Tanay, Rizal" loading="lazy"><figcaption>The Cabuyao Twin Falls in Tanay, with the two parallel spillways feeding a cold pool you can swim across. Image via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>From Cuyambay back to Manila is around two and a half hours through Sampaloc and Antipolo. Pile up the merienda stops on the way back. The roadside vendors along the Marikina-Infanta Highway sell suman, kakanin, and binatog from late afternoon. The Tanay public market is also worth a 20-minute stop for fresh tilapia and Laguna de Bay snails.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/tanay-masungi-georeserve.jpg" alt="Tanay limestone hills near Cuyambay" loading="lazy"><figcaption>The Tanay limestone hills along the Marikina-Infanta Highway, the descent route from Cuyambay back to Manila that fills with merienda vendors by late afternoon.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 5. MT. MARAMI AND SILYANG BATO
    // ------------------------------------------------------------------
    'mt-marami-silyang-bato-maragondon-cavite-day-hike' => [
        [
            'anchor' => '<p>The upper third of the climb runs through a boulder field, with the trail weaving between large rock outcrops. This section is shaded and cooler, a real break from the cogon. Watch the footing, the rocks are loose in places.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/3/3b/Mt_Marami_boulder_field.jpg/800px-Mt_Marami_boulder_field.jpg" alt="Mt. Marami boulder field, Maragondon Cavite" loading="lazy"><figcaption>The shaded boulder field on the upper third of Mt. Marami, the only real break from the cogon sun. Photo via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The summit holds Silyang Bato, the stone chair. The view from the chair opens to the West Philippine Sea on one side, to the Maragondon range on the other, and on clear days you can see Mt. Pico de Loro to the southwest. The chair fits one person comfortably, two if you are friends. Most groups queue politely for the summit photo.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/5d/Silyang_Bato_Mt_Marami_Cavite.jpg/800px-Silyang_Bato_Mt_Marami_Cavite.jpg" alt="Silyang Bato stone chair at Mt. Marami summit" loading="lazy"><figcaption>Silyang Bato, the natural stone chair at the Mt. Marami summit, with the West Philippine Sea opening to one side. Image via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Maragondon town has carinderias along the main road for tapa, longganisa, and pork sinigang. The Aguinaldo Shrine and the Bonifacio Trial House are both in the area if you want a small history stop on the way home. For a longer rest, Tagaytay is around 45 minutes away and the bulalo strip there is the standard descent meal.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/naic-maragondon-heritage-town.jpg" alt="Maragondon heritage town in Cavite" loading="lazy"><figcaption>Maragondon town, with the Bonifacio Trial House and the carinderia row along the main road that doubles as the post-Marami descent meal.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 6. MT. HAPUNANG BANOI AND BINACAYAN
    // ------------------------------------------------------------------
    'mt-hapunang-banoi-binacayan-rodriguez-two-peak-day' => [
        [
            'anchor' => '<p>Hapunang Banoi sits at around 632 meters. The trail climbs through limestone karst, with sections where you pull yourself up using the rock itself. Wear gloves, the limestone is sharp and the cuts are minor but they sting. The summit is a small clearing with a view across the Wawa River to Pamitinan and the Sierra Madre.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/rodriguez-montalban-wawa-dam.jpg" alt="Wawa Dam and limestone peaks in Rodriguez, Rizal" loading="lazy"><figcaption>Wawa Dam in Rodriguez and the limestone karst peaks of Pamitinan, Hapunang Banoi, and Binacayan that flank the river.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Binacayan sits at around 425 meters. The trail is shorter, the ascent is steeper, and the final 100 meters use fixed ropes on a near-vertical rock face. The summit is a flat boulder with a wide view of the dam, the river, and the Marikina Valley behind it. Combined with Banoi in one day, the total moving time is around six to seven hours.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/7/7c/Mt_Binacayan_summit_view_Rodriguez.jpg/800px-Mt_Binacayan_summit_view_Rodriguez.jpg" alt="Mt. Binacayan summit boulder above Wawa Dam" loading="lazy"><figcaption>The Mt. Binacayan summit boulder, the flat finish to the near-vertical rope face on the dam side. Photo via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Between the two climbs, drop down to the Wawa River for lunch. The water is cool, the rocks are flat enough to sit on, and a small lugaw vendor sets up at the staging area on weekends. Soak the feet in the river for 30 minutes, the second peak feels lighter after.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/rodriguez-montalban-2.jpg" alt="Wawa River swimming area in Rodriguez" loading="lazy"><figcaption>The Wawa River flat-rock staging area between the two peaks, where the lugaw vendor sets up and groups soak their feet between climbs.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 7. MT. CRISTOBAL TRAVERSE
    // ------------------------------------------------------------------
    'mt-cristobal-traverse-san-pablo-mossy-forest-climb' => [
        [
            'anchor' => '<p>The middle section is the mossy forest, the signature of Cristobal. Light drops to a soft green even at noon. The trees are tightly packed with moss on the trunks and on the ground. The air smells like wet earth and bark. Walk slow, the roots are slick, and the photos here look like a different country.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/ab/Mt_Cristobal_mossy_forest.jpg/800px-Mt_Cristobal_mossy_forest.jpg" alt="Mossy forest of Mt. Cristobal in San Pablo, Laguna" loading="lazy"><figcaption>The signature mossy forest of Mt. Cristobal, where the light filters green even at midday. Photo via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The summit climb the next morning is a short 45-minute push through a denser stretch of forest. The peak is a small clearing with no real view, the surrounding trees block the sightlines. The reward is the forest itself, not the panorama. Descend by the Tatlong Tangke trail on the opposite side for the traverse, or back down through Sta. Lucia if you arranged a one-way pickup.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/san-pablo-pandin-lake-and-twin-yambo-lake.jpg" alt="San Pablo Seven Lakes area" loading="lazy"><figcaption>The Seven Lakes circuit in San Pablo, the soft descent stop after the Mt. Cristobal traverse, with Pandin and Yambo as the calmest pair.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 8. TARAK RIDGE
    // ------------------------------------------------------------------
    'tarak-ridge-mariveles-bataan-overnight-climb-bay-view' => [
        [
            'anchor' => '<p>Most groups camp at Papaya River, around 30 minutes below the actual ridge. The site has a water source, flat ground, and tree cover. From the camp, a 30-minute push reaches Tarak Ridge itself, the open clearing on the lip of the cliff.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/82/Papaya_River_campsite_Mariveles.jpg/800px-Papaya_River_campsite_Mariveles.jpg" alt="Papaya River campsite below Tarak Ridge" loading="lazy"><figcaption>The Papaya River campsite on the Tarak approach, the flat-ground rest stop with a water source 30 minutes below the cliff lip. Photo via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The ridge clearing is wide enough for a dozen tents. The view sweeps across Manila Bay, Cavite to the east, and on a clear night the lights of Manila itself spread along the horizon. Sunset here is the standard photo. Sunrise the next morning lights up the Mariveles Dome behind you.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c3/Tarak_Ridge_Manila_Bay_view.jpg/800px-Tarak_Ridge_Manila_Bay_view.jpg" alt="Tarak Ridge view of Manila Bay" loading="lazy"><figcaption>The Tarak Ridge clearing at 1,130 meters, with Manila Bay sweeping across the horizon and Cavite visible on the eastern coast. Image via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>For groups making a weekend of it, the Bataan loop adds a Sunday-afternoon stop at the Pawikan Conservation Center in Morong on the drive home. The center hatches sea turtle eggs and releases the hatchlings during the season (November to February). The drive from Mariveles to Morong is around 90 minutes along the coast.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/morong-bataan-las-casas-filipinas-de-acuzar-bagac.jpg" alt="Las Casas Filipinas heritage area in Bagac, Bataan" loading="lazy"><figcaption>Las Casas Filipinas de Acuzar in Bagac, the heritage-house complex that anchors the Bataan loop drive back from Mariveles.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 9. MT. NATIB
    // ------------------------------------------------------------------
    'mt-natib-bataan-mossy-forest-climb-northern-range' => [
        [
            'anchor' => '<p>Past the middle forest, the trail enters a mossy belt. The trees thin, the moss thickens, and the air drops noticeably cooler. The light through the canopy is filtered green. The path here braids in places, the guide knows the cuts.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/d/d5/Mt_Natib_mossy_canopy.jpg/800px-Mt_Natib_mossy_canopy.jpg" alt="Mt. Natib mossy forest canopy in Bataan" loading="lazy"><figcaption>The mossy upper belt of Mt. Natib where the canopy thins and the air drops cooler. Photo via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The summit walk the next morning passes the small Natib crater. The crater is a shallow bowl, vegetated, not the dramatic open lake type. The summit itself is a small clearing with no view, the trees block the panorama. The reward is the forest and the crater, not the photo.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/morong-bataan-sabang-beach.jpg" alt="Sabang Beach in Morong, Bataan" loading="lazy"><figcaption>Sabang Beach in Morong, the quiet coastal reset stop on the descent side of the Natib traverse toward the Bagac coast.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 10. MT. CINCO PICOS
    // ------------------------------------------------------------------
    'mt-cinco-picos-zambales-five-peak-traverse-from-subic' => [
        [
            'anchor' => '<p>The first three peaks rise in a steady sawtooth pattern, with short descents between each summit. The cogon is shoulder-high in places and the sun is direct. Move fast through this section, the third peak has a small shaded clearing that works for a rest stop.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/e7/Mt_Cinco_Picos_Zambales_ridge.jpg/800px-Mt_Cinco_Picos_Zambales_ridge.jpg" alt="Cinco Picos five-peak ridge in Zambales" loading="lazy"><figcaption>The sawtooth ridge of Mt. Cinco Picos above Subic Bay, with the five summits stepping up the cogon spine. Image via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The fourth and fifth peaks are the higher of the five, with the actual Cinco Picos summit at the fifth. The trail between four and five drops into a small saddle and climbs steeply back up. The summit view sweeps across the Zambales coast to the West Philippine Sea, with the Subic Bay opening to the south.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/subic-anawangin-and-nagsasa-coves-from-pundaquit.jpg" alt="Zambales coast at Anawangin and Nagsasa coves" loading="lazy"><figcaption>The Zambales coast at Anawangin and Nagsasa, the wide West Philippine Sea panorama visible from the fifth Cinco Picos peak.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The Subic Bay Freeport has the usual food chains, but the longer kwento is to drive 10 minutes north of the Freeport to the Olongapo or Barretto food stretch for sisig and bulalo. The Zambales coastline north of Subic also has small carinderias along the highway with fresh tilapia from the bay.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/subic-camayan-beach-1.jpg" alt="Subic Bay coastal area" loading="lazy"><figcaption>The Subic Bay coastline that links the Cinco Picos jump-off at Brgy. Cawag to the Olongapo and Barretto food strip on the descent drive.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 11. MT. PAMULINAWEN
    // ------------------------------------------------------------------
    'mt-pamulinawen-pasuquin-ilocos-norte-short-climb-big-view' => [
        [
            'anchor' => '<p>The peak is a wide clearing with a wooden marker and a 360-degree view. The South China Sea opens to the west, the Ilocos coastal plain stretches to the south, and on a clear morning the windmills of Bangui are faintly visible to the north. The summit holds maybe 15 trekkers comfortably.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/ilocos-norte-bangui-windmills.jpg" alt="Bangui windmills, Ilocos Norte" loading="lazy"><figcaption>The Bangui windmills on the coast north of Pasuquin, faintly visible on a clear morning from the Pamulinawen summit.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Most groups do Pamulinawen as a half-day climb paired with a Laoag city stop. The Sinking Bell Tower, the St. William\'s Cathedral, and the Paoay Church are all within an hour of the jump-off. A morning climb plus an afternoon Ilocos food trip plus an evening dinner of bagnet and Ilocos longganisa is the standard one-day plan.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/ilocos-norte-paoay-church-1710.jpg" alt="Paoay Church, Ilocos Norte" loading="lazy"><figcaption>Paoay Church, the UNESCO earthquake-baroque heritage stop that anchors the Laoag afternoon after a Pamulinawen climb.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>For groups making a longer Ilocos trip, Pamulinawen fits naturally into the Bangui-Pagudpud coastal loop. Climb in the morning, drive north to the Bangui windmills for a late-afternoon stop, and stay overnight in Pagudpud for the next-day beach time. The drive from Pasuquin to Pagudpud is around 90 minutes along the coast.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/ilocos-norte-pagudpud-beaches-saud-blue-lagoon-patapat.jpg" alt="Pagudpud beaches at Saud and Blue Lagoon" loading="lazy"><figcaption>The Pagudpud beaches at Saud and Blue Lagoon, the overnight stop that closes the Pamulinawen-Bangui-Pagudpud Ilocos loop.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 12. MT. BALAGBAG
    // ------------------------------------------------------------------
    'mt-balagbag-rodriguez-rizal-beginner-friendly-ridge-walk' => [
        [
            'anchor' => '<p>The peak is a clearing with the broadcast tower in the middle and a wide ring of viewpoints around the perimeter. The view sweeps north into the Sierra Madre, east into the Tanay highlands, south to Laguna de Bay and Metro Manila, and west toward Bulacan. On a clear night the Manila skyline lights up the southern horizon.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/24/Mt_Balagbag_summit_Rodriguez_Rizal.jpg/800px-Mt_Balagbag_summit_Rodriguez_Rizal.jpg" alt="Mt. Balagbag summit and broadcast tower" loading="lazy"><figcaption>The Mt. Balagbag summit at 777 meters, with the broadcast tower in the middle and the 360-degree view that opens to Metro Manila on a clear night. Image via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Balagbag is one of the few Luzon peaks where the guides actively recommend a sunset-and-stars trip. Start the climb at 3 p.m., reach the summit by 5:30 p.m., watch the sunset, eat a packed dinner, and descend with headlamps by 8 p.m. The route is wide enough and the trail is familiar enough that the descent in the dark is safe with a guide.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/rodriguez-montalban-3.jpg" alt="Service road ridge in Rodriguez, Rizal" loading="lazy"><figcaption>The wide service-road approach that makes Mt. Balagbag the rare Luzon peak where headlamp descents after sunset are a regular option.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 13. MT. MACULOT ROCKIES
    // ------------------------------------------------------------------
    'mt-maculot-rockies-overnight-cuenca-camp-above-taal-lake' => [
        [
            'anchor' => '<p>The Rockies is a wide boulder field with a flat clearing for tents on the leeward side. The site holds maybe 30 tents on a busy weekend. Pitch close to the boulders for wind shelter, the breeze off Taal Lake picks up at night.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/f/f1/Mt_Maculot_Rockies_campsite.jpg/800px-Mt_Maculot_Rockies_campsite.jpg" alt="Mt. Maculot Rockies campsite above Taal Lake" loading="lazy"><figcaption>The Rockies boulder field on Mt. Maculot, with the leeward tent clearing that fills up on busy Cuenca weekends. Photo via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Wake at 4:45 a.m. Walk to the boulder edge by 5:15 a.m. The sky starts to lighten over the Taal volcano, the lake fills with mist, and the sun comes up behind the Tagaytay ridge across the water. The light hits the Rockies first, then spreads across the lake. The photo takes itself.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/1f/Taal_Lake_sunrise_from_Maculot.jpg/800px-Taal_Lake_sunrise_from_Maculot.jpg" alt="Taal Lake sunrise from Mt. Maculot" loading="lazy"><figcaption>Taal Lake at sunrise from the Maculot Rockies, with the volcano in the middle of the mist and the Tagaytay ridge across the water. Image via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Cuenca and Lipa have lomi shops along the highway, the Batangas comfort food after a climb. Order a steaming bowl of lomi with kikiam, liver, and pork, and the climb fatigue dissolves. The Lipa coffee scene is the longer-detour option, with cafes serving Barako coffee along Sleep Lounge Avenue and the M. Marella Hotel strip.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/lipa-lipa-city-plaza.jpg" alt="Lipa city plaza, Batangas" loading="lazy"><figcaption>Lipa city plaza, where the post-Maculot lomi shops and Barako coffee cafes anchor the descent meal.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 14. CAVINTI UNDERGROUND RIVER
    // ------------------------------------------------------------------
    'cavinti-underground-river-pagsanjan-gorge-calm-laguna-day' => [
        [
            'anchor' => '<p>The river runs through three chambers, each with a different ceiling height. The first chamber has standing room with stalactites overhead. The second chamber narrows, you walk waist-deep through the water for around 20 meters. The third chamber opens into a wide pool, around chest-deep, with a small waterfall feeding it from above. The cave swim is the highlight; the cold pool, the carbide light on the ceiling, and the silence are the kind of stop that makes the trip worth the long Laguna drive.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/57/Cavinti_Underground_River_Laguna.jpg/800px-Cavinti_Underground_River_Laguna.jpg" alt="Cavinti Underground River cave chamber in Laguna" loading="lazy"><figcaption>The three-chamber Cavinti Underground River, with the waist-deep middle passage and the wide chest-deep pool at the third chamber. Image via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Above the Pagsanjan Falls, on the Cavinti side, there is a calm stretch of the Magdapio River that is suitable for a tandem kayak or a small inflatable. The local Cavinti tourism office rents the kayaks and assigns a paddle guide. The route paddles upstream from the gorge entrance, around an hour up, then drifts back down. The gorge walls rise on both sides, around 80 meters tall, and the canyon stays cool even at midday.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/93/Pagsanjan_Gorge_Cavinti_Laguna.jpg/800px-Pagsanjan_Gorge_Cavinti_Laguna.jpg" alt="Pagsanjan Gorge above the falls, Cavinti side" loading="lazy"><figcaption>The Pagsanjan Gorge above the falls, the calm Magdapio River stretch on the Cavinti side where the kayak rentals operate. Image via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Halfway up the kayak route, the guide will point to a small cave entrance on the canyon wall called Devil\'s Cave by the locals. The cave is shallow, you can paddle into it for a few meters. The water inside is dark and the temperature drops noticeably.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/nagcarlan-bunga-falls.jpg" alt="Bunga Falls in Nagcarlan, Laguna" loading="lazy"><figcaption>Bunga Falls in Nagcarlan, the Laguna waterfall stop that pairs naturally with a Cavinti cave and kayak day.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 15. TIBIAO WHITEWATER KAYAK
    // ------------------------------------------------------------------
    'tibiao-whitewater-kayak-antique-river-trip-slow-read' => [
        [
            'anchor' => '<p>The first kilometer is flat water with the current pushing you along. The middle section drops into a series of small rapids, around six in a row, that the local guides call by the names the families on the river use for them. The Class III sections are tight curves with standing waves; the lines are easy to follow if you keep the bow pointed downstream.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/4a/Tibiao_River_whitewater_Antique.jpg/800px-Tibiao_River_whitewater_Antique.jpg" alt="Tibiao River whitewater rapids in Antique" loading="lazy"><figcaption>The Tibiao River whitewater section in Antique, with the Class II and III rapids that run only in the wet months. Image via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The Kawa Hot Bath is the Tibiao signature, a wooden vat of warm water heated by a small wood fire beneath. The water has lemongrass, ginger, and herbs floating in it. The bath sits next to the river, you soak with the river sound in the background. After the kayak trip, the warm soak is the kind of recovery routine that turns a Tibiao day into a return trip.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/b/b8/Kawa_Hot_Bath_Tibiao_Antique.jpg/800px-Kawa_Hot_Bath_Tibiao_Antique.jpg" alt="Kawa Hot Bath in Tibiao, Antique" loading="lazy"><figcaption>The Tibiao Kawa Hot Bath, the wood-fired vat with lemongrass and ginger that closes the post-kayak day. Image via Wikimedia Commons.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 16. TUBURAN SEVEN FALLS
    // ------------------------------------------------------------------
    'tuburan-bugtong-bato-seven-falls-trek-tibiao-climb' => [
        [
            'anchor' => '<p>The third falls is the standard stopping point for active trekkers. The cascade is around 25 meters tall, with a wide circular pool at the base that is around 4 meters deep at the center. Swim across, sit on the rocks behind the falling water, eat the packed lunch.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/7/73/Bugtong_Bato_Falls_Tibiao_Antique.jpg/800px-Bugtong_Bato_Falls_Tibiao_Antique.jpg" alt="Bugtong Bato third falls in Tibiao" loading="lazy"><figcaption>The third tier of the Bugtong Bato Falls system in Tibiao, the standard stopping point with a 4-meter-deep circular pool you can swim across. Image via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The fourth falls and above require fixed-rope climbs and rock scrambles. The guide will assess the group fitness and the rope condition before continuing. The seventh falls is the highest cascade in the system, deep into the foothills, and the round trip from the third falls takes another four to five hours. Plan an early start if you intend to go that high.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/iloilo-city-1.jpg" alt="Western Visayas foothills near Antique" loading="lazy"><figcaption>The Madja-as foothills above Tibiao, the watershed that feeds the Bugtong Bato seven-tier falls system on the Antique west coast.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 17. ANILAO MACRO DIVING
    // ------------------------------------------------------------------
    'anilao-macro-diving-by-season-calm-read-year-around' => [
        [
            'anchor' => '<p>Pygmy seahorses on the seafans, ghost pipefish in the rubble, and the regular cast of frogfish, blue-ringed octopus, and harlequin shrimp. Night dives in this window are quiet but rewarding.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/5a/Pygmy_seahorse_Hippocampus_bargibanti_Anilao.jpg/800px-Pygmy_seahorse_Hippocampus_bargibanti_Anilao.jpg" alt="Pygmy seahorse on a seafan in Anilao" loading="lazy"><figcaption>A pygmy seahorse (Hippocampus bargibanti) on a gorgonian seafan, one of the regular cool-season macro subjects in Anilao. Image via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Mandarinfish at the standard dusk sites (Secret Bay, Mainit Point) when the males come out to court. Frogfish in colors that range from yellow to red to black. Blackwater dives off the coast pull in larval squid, paper nautilus, and pelagic juveniles that ride the currents at night.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/3/3c/Mandarinfish_Synchiropus_splendidus_Anilao.jpg/800px-Mandarinfish_Synchiropus_splendidus_Anilao.jpg" alt="Mandarinfish at dusk in Anilao, Mabini Batangas" loading="lazy"><figcaption>A male mandarinfish at dusk on the Secret Bay site, the standard March-to-May Anilao photo subject when the courtship runs. Image via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>This window is the wild-card season. New critter sightings happen because fewer divers are on the sites. The flamboyant cuttlefish, the wonderpus, and the mimic octopus all turn up on a regular basis at Mainit and Secret Bay.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/anilao-mabini-anilao-dive-sites-cathedral-rock-mainit-point-twin.jpg" alt="Anilao dive sites including Cathedral Rock and Mainit Point" loading="lazy"><figcaption>The Anilao dive cluster that includes Cathedral Rock, Mainit Point, and Twin Rocks, the home base for the habagat-season critter rotations.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 18. ANILAO BLACKWATER NIGHT DIVING
    // ------------------------------------------------------------------
    'anilao-blackwater-night-diving-open-sea-drifts-read' => [
        [
            'anchor' => '<p>Larval squid, paper nautilus, pelagic juveniles of common reef fish, larval jellyfish, larval crustaceans, and on the best nights, an actual juvenile cephalopod or a paper-thin fish that has no name yet. The catalog rotates with the moon phase and the season. Diving on the new moon gives the best critter counts because the deep water is darker.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/d/dc/Paper_nautilus_Argonauta_blackwater.jpg/800px-Paper_nautilus_Argonauta_blackwater.jpg" alt="Paper nautilus on a blackwater drift dive" loading="lazy"><figcaption>A paper nautilus (Argonauta) on a blackwater drift, one of the pelagic subjects that rises from the deep Verde Passage at night. Image via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The standard rig is a macro lens with twin strobes and a focus light. The critters are small, around 1 to 3 centimeters, and the photo demands sharp focus and patient shooting. Most divers on the boat are photographers; the practice attracts that crowd.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/anilao-mabini-2.jpg" alt="Anilao boat fleet on the Verde Passage" loading="lazy"><figcaption>The Anilao boat fleet on the Verde Passage side, where the blackwater rigs deploy after sunset for the new-moon critter weeks.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 19. APO REEF LIVE-ABOARD
    // ------------------------------------------------------------------
    'apo-reef-live-aboard-sablayan-mindoro-three-day-dive-trip' => [
        [
            'anchor' => '<p>The reef wall site on the southwest edge. Drops from 5 meters to over 30 meters. Reef sharks, jacks, and turtles cruise the wall. Visibility is regularly 30 meters.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/ee/Apo_Reef_wall_dive_Mindoro.jpg/800px-Apo_Reef_wall_dive_Mindoro.jpg" alt="Apo Reef wall dive in Mindoro" loading="lazy"><figcaption>The Apo Reef southwest wall, where the drop from 5 to 30 meters delivers regular reef shark and jack encounters. Image via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The Apo Island in Apo Reef is not the same as the Apo Island in Negros. This one is a small, uninhabited sandy island with a lighthouse and a ranger station. Walk the beach, climb the lighthouse, and watch the sunset from the western point. The island is also a fruit bat colony; thousands of fruit bats leave the trees at dusk in a swirl that goes on for around 20 minutes.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c4/Apo_Reef_Lighthouse_Sablayan.jpg/800px-Apo_Reef_Lighthouse_Sablayan.jpg" alt="Apo Reef lighthouse on the small island, Sablayan Mindoro" loading="lazy"><figcaption>The Apo Reef lighthouse on the small uninhabited island, where the fruit bat colony swirls out of the trees at dusk. Image via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>A submerged pinnacle on the south side. The site is famous for sea snakes, with up to 20 in a single dive. The pinnacle drops to 40 meters and the wall has soft corals from 25 meters down.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/puerto-galera-2.jpg" alt="Mindoro coastal waters" loading="lazy"><figcaption>The Mindoro Strait coastline that the Sablayan boats cross to reach the Apo Reef pinnacles and the Hunter\'s Rock sea snake site.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 20. MALAPASCUA THRESHER SHARK
    // ------------------------------------------------------------------
    'malapascua-thresher-shark-dive-monad-shoal-calm-read' => [
        [
            'anchor' => '<p>Monad Shoal has three known cleaning stations along the ledge. The dive guide positions the group around the stations and signals when a thresher approaches. The sharks are around three to five meters long, with the long upper tail fin that gives them their name.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/b/bb/Thresher_shark_Monad_Shoal_Malapascua.jpg/800px-Thresher_shark_Monad_Shoal_Malapascua.jpg" alt="Pelagic thresher shark at Monad Shoal" loading="lazy"><figcaption>A pelagic thresher shark (Alopias pelagicus) at a Monad Shoal cleaning station, with the long upper tail fin that gives the species its name. Image via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>A protected marine sanctuary 30 minutes east. The island has a swim-through tunnel at 12 meters and a soft coral garden on the south side. Whitetip reef sharks rest in the tunnel. The bat cave on the surface holds thousands of fruit bats.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/97/Gato_Island_swim_through_Cebu.jpg/800px-Gato_Island_swim_through_Cebu.jpg" alt="Gato Island marine sanctuary near Malapascua" loading="lazy"><figcaption>Gato Island, the marine sanctuary east of Malapascua with the 12-meter swim-through tunnel and the fruit bat cave above. Image via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Not technically a Malapascua site, but a long boat ride away. The island is a sandbar with two beaches, a snorkel reef, and a wall. The full-day trip is a calm break from the deep dives.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/cebu-city-3.jpg" alt="Northern Cebu coastline near Maya port" loading="lazy"><figcaption>The northern Cebu coast that connects Maya port to the Malapascua boats and the Calanggaman sandbar full-day trips.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 21. VERDE ISLAND PASSAGE DRIFT
    // ------------------------------------------------------------------
    'verde-island-passage-drift-dive-open-currents-calm-read' => [
        [
            'anchor' => '<p>Three submerged pinnacles rising from 40 meters to within five meters of the surface. The current splits around the pinnacles, the soft corals on the southern face are the densest in the country, and the schools of jacks and trevallies cruise the deeper sections. The drift is moderate; the dive guide leads the group around each pinnacle and signals when to ascend.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/a3/Verde_Island_Pinnacles_soft_coral.jpg/800px-Verde_Island_Pinnacles_soft_coral.jpg" alt="Verde Island Pinnacles soft coral" loading="lazy"><figcaption>The Verde Island Pinnacles, where the soft coral density on the southern face ranks among the highest measured in the Philippines. Image via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>A vertical wall on the north side of the island, dropping from five meters to over 60 meters. The wall has hard corals on the upper section and soft corals from 20 meters down. The current here can be strong; the dive is rated advanced and is typically done after the Pinnacles dive on the same day.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/batangas-city-batangas-city-port.jpg" alt="Batangas City port on the Verde Passage" loading="lazy"><figcaption>Batangas City port on the Verde Island Passage, the inland anchor of the drift-dive boats that head to the Pinnacles and the Drop-Off.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 22. APO ISLAND FREEDIVING
    // ------------------------------------------------------------------
    'apo-island-freediving-day-calm-read-from-dauin-negros' => [
        [
            'anchor' => '<p>The community no-take zone on the south side of the island. The reef is in the best condition here. Turtles graze on the seagrass beds at five to seven meters; you can hover above them on a single breath. The site is also the standard photo spot.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/dauin-apo-island-marine-sanctuary.jpg" alt="Apo Island Marine Sanctuary off Dauin" loading="lazy"><figcaption>The Apo Island Marine Sanctuary on the south side of the island, the community no-take zone where green and hawksbill turtles graze the seagrass beds.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The Apo turtles are habituated to swimmers but they are wild. Stay above them, do not touch, do not chase. The local dive guides will signal when a turtle is in sight and where to position the group. The turtle population includes both green and hawksbill turtles; the green ones are the larger and more common.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/4b/Green_sea_turtle_Apo_Island_Negros.jpg/800px-Green_sea_turtle_Apo_Island_Negros.jpg" alt="Green sea turtle grazing on seagrass at Apo Island Negros" loading="lazy"><figcaption>A green sea turtle (Chelonia mydas) grazing the seagrass at Apo Island off Dauin, one of the reliable freedive encounters. Image via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Between dives, the boat ties up at the small beach on the south side. The beach has a single carinderia with halo-halo, grilled fish, and rice. Walk the short trail to the lighthouse on the high point for a view of the channel. The whole island is walkable in 90 minutes.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/dumaguete-rizal-boulevard.jpg" alt="Rizal Boulevard in Dumaguete" loading="lazy"><figcaption>Rizal Boulevard in Dumaguete, the mainland reset stop after an Apo Island freedive day, with cafes and a seawall walk before the evening flight.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 23. LA UNION SHOULDER SEASON SURF
    // ------------------------------------------------------------------
    'la-union-shoulder-season-surf-calm-read-may-september' => [
        [
            'anchor' => '<p>The Urbiztondo break is the main reef-and-sand point on the surf strip. In May, the break is smaller and friendlier than peak season. The surf-school groups are still there in the morning, but the lineup spreads out by 10 a.m. Solo riders get more space.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/san-juan-la-union-urbiztondo-surf-beach.jpg" alt="Urbiztondo surf beach in San Juan, La Union" loading="lazy"><figcaption>The Urbiztondo surf strip in San Juan, the reef-and-sand point that thins out by 10 a.m. in the shoulder-season May and September weeks.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Early September mornings can still have the habagat onshore wind. The trick is to be at the lineup by 6 a.m. before the wind picks up, then off the water by 9 a.m. when the surface chops up. By the third week of the month, the wind shifts and the surface is cleaner all day.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/52/La_Union_surf_lineup_morning.jpg/800px-La_Union_surf_lineup_morning.jpg" alt="La Union morning surf lineup" loading="lazy"><figcaption>The early-morning lineup at La Union, the 6 a.m. window before the habagat wind picks up and chops the surface. Image via Wikimedia Commons.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 24. BALER SHOULDER SEASON SURF
    // ------------------------------------------------------------------
    'baler-shoulder-season-surf-slow-two-day-plan-from-manila' => [
        [
            'anchor' => '<p>After checking in, walk the length of Sabang Beach. The crescent is around two kilometers long, with the surf strip clustered in the middle and the quieter ends to the north and south. The walk takes around 45 minutes one way. The beach has a row of small restaurants along the seawall, with grilled tuna and chicken inasal as the standard order.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/8d/Sabang_Beach_Baler_Aurora.jpg/800px-Sabang_Beach_Baler_Aurora.jpg" alt="Sabang Beach in Baler, Aurora" loading="lazy"><figcaption>The two-kilometer Sabang Beach crescent in Baler, with the surf strip clustered in the middle and quieter ends to the north and south. Image via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>A taller waterfall around 30 minutes south of Sabang in the town of San Luis. The 30-minute trek to the falls crosses a river several times. The pool at the base is wide and cold, with the cascade plunging from 50 meters above.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/26/Ditumabo_Mother_Falls_San_Luis_Aurora.jpg/800px-Ditumabo_Mother_Falls_San_Luis_Aurora.jpg" alt="Ditumabo Mother Falls in San Luis, Aurora" loading="lazy"><figcaption>Ditumabo Mother Falls in San Luis, Aurora, the 50-meter plunge with the wide cold pool at the base, 30 minutes south of Sabang. Image via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>A 600-year-old balete tree in Maria Aurora, around 20 minutes from Sabang. The tree is the kind of size that earns its own visit.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/0/0b/Millennium_Tree_Balete_Maria_Aurora.jpg/800px-Millennium_Tree_Balete_Maria_Aurora.jpg" alt="Millennium balete tree in Maria Aurora" loading="lazy"><figcaption>The Millennium Tree, a 600-year-old balete in Maria Aurora that earns its own 20-minute side trip from the Sabang surf strip. Image via Wikimedia Commons.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 25. REAL QUEZON SURF
    // ------------------------------------------------------------------
    'real-quezon-surf-weekend-closer-east-coast-break-manila' => [
        [
            'anchor' => '<p>The main surf strip is at Brgy. Ungos. The beach is around two kilometers long, with the surf-school cluster in the middle and the quieter ends to the north and south. The breaks are sand-bottom; first-time surfers learn here.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/40/Ungos_Beach_Real_Quezon_surf.jpg/800px-Ungos_Beach_Real_Quezon_surf.jpg" alt="Ungos Beach surf strip in Real, Quezon" loading="lazy"><figcaption>Ungos Beach in Real, the two-kilometer sand-bottom strip that hosts the local surf schools and the closer east-coast lineup from Manila. Image via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>A waterfall in nearby General Nakar, around 30 minutes north of Real. The trek to the falls is a short 20-minute walk along a river. The pool at the base is wide and cool.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/dingalan-white-beach.jpg" alt="Dingalan coast near Real Quezon" loading="lazy"><figcaption>The Dingalan coast just north of Real, where the General Nakar river system feeds Talay Falls and the quieter Pacific beaches.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 26. CALATAGAN MANGROVE KAYAK
    // ------------------------------------------------------------------
    'calatagan-mangrove-kayak-loop-calm-paddle-batangas-coast' => [
        [
            'anchor' => '<p>The mangroves were replanted over the last 20 years by the local community. The channels are narrow, around five to eight meters wide, with the mangrove roots arching overhead in places. Paddle slow, the water is shallow and the keel of the kayak occasionally brushes a root.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/64/Calatagan_Ang_Pulo_mangrove_park.jpg/800px-Calatagan_Ang_Pulo_mangrove_park.jpg" alt="Ang Pulo mangrove park in Calatagan, Batangas" loading="lazy"><figcaption>The Ang Pulo mangrove park in Calatagan, with the narrow five-meter channels and the arched root canopy from the 20-year replanting program. Image via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>After the kayak, drive 20 minutes south to Burot Beach or 30 minutes west to Cape Santiago Lighthouse. Burot is the long, white-sand beach with light surf and snorkel-friendly water. Cape Santiago is the 19th-century brick lighthouse on a low cliff, with a small museum and a view across the strait to Lubang Island.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/calatagan-cape-santiago-lighthouse.jpg" alt="Cape Santiago Lighthouse in Calatagan" loading="lazy"><figcaption>Cape Santiago Lighthouse, the 19th-century brick tower on the Calatagan headland with a view across the strait to Lubang Island.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Calatagan town center has carinderias along the main road for grilled bangus, sinigang, and Batangas-style adobo. The longer-detour option is the Cape Santiago side, with small beach-side restaurants that serve fresh ulang prawns and seaweed salad.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/calatagan-2.jpg" alt="Calatagan coast in Batangas" loading="lazy"><figcaption>The Calatagan coastline that wraps the southwestern tip of Batangas, where the mangrove park, Burot Beach, and Cape Santiago all sit within a 30-minute drive.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 27. SUBIC BAY SUP AND KAYAK
    // ------------------------------------------------------------------
    'subic-bay-sup-and-kayak-slow-read-bay-routes' => [
        [
            'anchor' => '<p>The standard route paddles from the Boardwalk to the Treasure Island sandbar, around 40 minutes one way at a calm pace. The sandbar is a small spit on the southern shore, exposed at low tide, with the bay water on three sides. Pull the board up, take photos, drift back.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/2c/Subic_Bay_Boardwalk_paddle.jpg/800px-Subic_Bay_Boardwalk_paddle.jpg" alt="Subic Bay Boardwalk paddle launch" loading="lazy"><figcaption>The Subic Bay Boardwalk launch on the inner bay, where the SUP and kayak operators set up for the calm Treasure Island route. Image via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>A marine park with a sea lion show, a dolphin program, and the underwater walking pool. Family-friendly. Half-day visit.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/subic-ocean-adventure.jpg" alt="Subic Ocean Adventure marine park" loading="lazy"><figcaption>Ocean Adventure in Subic, the family-friendly half-day stop with the sea lion show and the underwater walking pool.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>A rope-and-zip-line course in the forested hills above the Boardwalk. Around two hours for the full course.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/subic-tree-top-adventure.jpg" alt="Subic Tree Top Adventure zip-line course" loading="lazy"><figcaption>Tree Top Adventure in Subic, the two-hour rope-and-zip-line course in the forested hills above the Boardwalk.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 28. TALIM ISLAND KAYAK DAY
    // ------------------------------------------------------------------
    'talim-island-kayak-day-slow-read-laguna-de-bay-binangonan' => [
        [
            'anchor' => '<p>The Laguna de Bay tilapia and bangus fish pens line some stretches of the coast. The pens are not obstacles; they are wooden frames with nets below. Paddle through the channels between the pens, the fish farmers wave from their bamboo shelters.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c2/Laguna_de_Bay_fish_pens_Talim.jpg/800px-Laguna_de_Bay_fish_pens_Talim.jpg" alt="Laguna de Bay fish pens near Talim Island" loading="lazy"><figcaption>The Laguna de Bay tilapia and bangus pens along the Talim Island coast, with the bamboo shelters that the fish farmers wave from. Image via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The summit holds a small wooden cross and a flag. The clearing fits around 15 trekkers. Photos and a quick snack, then descend to Janosa for the late afternoon and the sunset paddle back.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/f/f6/Mt_Tagapo_Talim_Island_summit.jpg/800px-Mt_Tagapo_Talim_Island_summit.jpg" alt="Mt. Tagapo summit on Talim Island" loading="lazy"><figcaption>The Mt. Tagapo summit on Talim Island, with the wooden cross above the Laguna de Bay panorama. Image via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>If you do not want to paddle back, the local bangka from Janosa pier returns to Binangonan at sunset. The crossing takes 30 minutes. The light hits the lake first, the Tanay highlands second, and the Metro Manila skyline turns to silhouette as the sun drops. The photo from the bangka is the kind that surprises first-time visitors to the lake.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/binangonan-2.jpg" alt="Binangonan side of Laguna de Bay" loading="lazy"><figcaption>The Binangonan side of Laguna de Bay at sunset, where the Janosa bangka returns from Talim Island as the Metro Manila skyline drops to silhouette.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 29. ALIWAGWAG FALLS
    // ------------------------------------------------------------------
    'aliwagwag-falls-84-tier-trek-cateel-cascade-calm-read' => [
        [
            'anchor' => '<p>The canopy walk is a steel-frame walkway that runs along the gorge wall at around 30 meters above the river. The walk takes around 20 minutes one way. The view from the walkway opens to the cascade system, with multiple drops visible in succession. The handrails are sturdy and the walk is family-friendly.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/a7/Aliwagwag_Falls_canopy_walk_Cateel.jpg/800px-Aliwagwag_Falls_canopy_walk_Cateel.jpg" alt="Aliwagwag Falls canopy walk in Cateel, Davao Oriental" loading="lazy"><figcaption>The Aliwagwag canopy walk in Cateel, the steel-frame walkway 30 meters above the river that opens to multiple cascade drops. Image via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>For trekkers who want to see the higher cascades, the local guide will lead a longer trek that follows the river upstream. The trek takes around four to six hours round trip, with multiple river crossings and a few rope-assisted scrambles. Wear trail runners with grip and bring three liters of water.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/3/37/Aliwagwag_Falls_84_tiers_Davao_Oriental.jpg/800px-Aliwagwag_Falls_84_tiers_Davao_Oriental.jpg" alt="Aliwagwag Falls 84 tiers stairway of angels" loading="lazy"><figcaption>The Aliwagwag Falls system in Cateel, the 84-tier cascade locals call the stairway of angels. Image via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Cateel and Baganga have small inns and beach-side resorts. For a longer Davao Oriental loop, the Mati town has the broader hotel selection. Compare options on <a href="https://www.tripadvisor.com.ph/Hotels-g4502473-Davao_Oriental_Province_Davao_Region_Mindanao-Hotels.html" target="_blank" rel="noopener nofollow">TripAdvisor PH</a>.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/davao-city-3.jpg" alt="Davao Region coastal area" loading="lazy"><figcaption>The Davao Region coastline that links Davao city to the long drive north through Mati and Baganga to the Aliwagwag entrance in Cateel.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 30. CASARORO FALLS
    // ------------------------------------------------------------------
    'casaroro-falls-trek-valencia-negros-quiet-steep-stairs-hike' => [
        [
            'anchor' => '<p>The trail starts at the Valencia tourism office, where you register and pay the small fee. The local guide assigns the route and walks the steps with the group. The 335 steps drop down the gorge wall in a switchback pattern; the steps are concrete but worn smooth in places.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/d/da/Casaroro_Falls_335_steps_Valencia.jpg/800px-Casaroro_Falls_335_steps_Valencia.jpg" alt="Casaroro Falls 335-step descent in Valencia, Negros" loading="lazy"><figcaption>The 335-step switchback descent into the Casaroro gorge, the concrete stairs that test the climb back up. Image via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The falls open at the head of the gorge. The 30-meter drop is narrow, with the water falling into a circular pool surrounded by mossy rock walls. The pool is around four meters deep at the center; locals say it is safe to swim across. The cascade kicks up a constant mist; expect to get wet just standing on the bank.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/dumaguete-casaroro-falls-valencia.jpg" alt="Casaroro Falls in Valencia, Negros Oriental" loading="lazy"><figcaption>The 30-meter Casaroro cascade in Valencia, with the circular pool and the mossy rock walls that ring the basin.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The Twin Lakes (Balinsasayao and Danao) sit 20 minutes from the Casaroro turnoff, on the same Valencia road. For a full day, do Casaroro in the morning, drive to Twin Lakes for the afternoon, and return to Dumaguete by sunset. The Twin Lakes have a boat ride across Balinsasayao Lake and a kayak option on Danao.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/5e/Twin_Lakes_Balinsasayao_Danao_Negros.jpg/800px-Twin_Lakes_Balinsasayao_Danao_Negros.jpg" alt="Twin Lakes Balinsasayao and Danao in Negros Oriental" loading="lazy"><figcaption>The Twin Lakes of Balinsasayao and Danao, the Valencia afternoon pair that closes a Casaroro full-day with a boat ride and a kayak. Image via Wikimedia Commons.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 31. LAKE HOLON
    // ------------------------------------------------------------------
    'lake-holon-tboli-south-cotabato-slow-crater-lake-weekend' => [
        [
            'anchor' => '<p>The rim is a sharp ridge with the lake on one side and the South Cotabato lowland on the other. The view from the rim opens to the lake below, the surrounding T\'boli mountains, and on a clear day the Sarangani coast in the distance.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/0/05/Lake_Holon_crater_rim_view.jpg/800px-Lake_Holon_crater_rim_view.jpg" alt="Lake Holon crater rim view in T\'boli, South Cotabato" loading="lazy"><figcaption>The Lake Holon crater rim of Mt. Parker, with the lake on one side and the South Cotabato lowland on the other. Image via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The sun sets behind the rim and the light hits the lake in soft layers. The water mirrors the sky; the photos are quiet and patient.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/9f/Lake_Holon_sunset_Tboli.jpg/800px-Lake_Holon_sunset_Tboli.jpg" alt="Lake Holon sunset in T\'boli" loading="lazy"><figcaption>Sunset on Lake Holon, with the light hitting the water in soft layers behind the crater rim. Image via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The lake is sacred to the T\'boli. Respect the local protocols: no loud music, no swimming outside the marked area, no littering, no rocks taken as souvenirs. The community runs the trail because they want to protect both the lake and their cultural ties to it. The guide will brief you at the registration; listen carefully.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/general-santos-2.jpg" alt="South Cotabato lowland near T\'boli" loading="lazy"><figcaption>The South Cotabato lowland that surrounds the T\'boli community, the cultural and geographic context for the Lake Holon weekend.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 32. HIDDEN VALLEY SPRINGS
    // ------------------------------------------------------------------
    'hidden-valley-springs-calauan-laguna-slow-day-forest-pools' => [
        [
            'anchor' => '<p>The valley itself is the property. The springs sit at the bottom, fed by underground volcanic warm water in one section and cool spring water in another. The temperature difference between the two main pools is around 10 degrees Celsius; the warm pool feels like a relaxed bath, the cool pool feels like a mountain stream.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/3/3a/Hidden_Valley_Springs_Calauan_pool.jpg/800px-Hidden_Valley_Springs_Calauan_pool.jpg" alt="Hidden Valley Springs forest pool in Calauan, Laguna" loading="lazy"><figcaption>The forest pool inside the Hidden Valley Springs crater in Calauan, Laguna, fed by Mt. Makiling\'s volcanic warm and cool spring water. Image via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The valley\'s small waterfall drops around eight meters into a shallow pool. The water is the cool-spring type. You can stand under the falls for a natural shoulder massage; the locals call it the original spa treatment.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/san-pablo-2.jpg" alt="San Pablo forest area near Calauan" loading="lazy"><figcaption>The San Pablo and Calauan forest belt at the foot of Mt. Makiling, the same volcanic geology that feeds the Hidden Valley spring pools.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 33. MT. DAGULDOL
    // ------------------------------------------------------------------
    'mt-daguldol-day-hike-beach-to-summit-climb-san-juan-batangas' => [
        [
            'anchor' => '<p>The summit is a wide open clearing with a view of the West Philippine Sea to the southwest and the Mindoro coast across the strait. The site holds around 20 tents on a busy weekend. Pitch on the leeward side; the sea wind pushes hard at night.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/65/Mt_Daguldol_summit_camp_Laiya.jpg/800px-Mt_Daguldol_summit_camp_Laiya.jpg" alt="Mt. Daguldol summit campsite in San Juan, Batangas" loading="lazy"><figcaption>The Mt. Daguldol summit clearing above Laiya, the open camp at 672 meters with the West Philippine Sea opening to the southwest. Image via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>After the Sunday-morning descent, drive 20 minutes east to Laiya Beach. The white-sand crescent at Laiya is around two kilometers long, with the resort row clustered in the middle. Swim, eat lunch at a beach-side carinderia, and start the drive home by mid-afternoon. The combination of a summit overnight and a beach Sunday is the kind of weekend that few Manila destinations offer.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/laiya-laiya-white-beach.jpg" alt="Laiya white-sand beach in San Juan, Batangas" loading="lazy"><figcaption>Laiya White Beach, the two-kilometer crescent 20 minutes from the Daguldol jump-off that anchors the beach-and-mountain weekend.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 34. MT. LOBO
    // ------------------------------------------------------------------
    'mt-lobo-trail-batangas-quiet-day-climb-verde-coast' => [
        [
            'anchor' => '<p>The last 45 minutes runs along a cogon ridge to the summit. The grass is calf-high to chest-high, the sun is direct, and the wind picks up. The summit clearing is small, around 10 trekkers fit comfortably. The view opens to the Verde Island Passage to the north, the Lobo coast to the east, and the Malabrigo Point to the south.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/lobo-sawang-beach.jpg" alt="Sawang Beach in Lobo, Batangas" loading="lazy"><figcaption>Sawang Beach in Lobo, the coast that meets the Verde Island Passage and frames the Lobo summit view.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>After the climb, drive 30 minutes south to the Malabrigo lighthouse. The 1891 brick tower sits on the southeast tip of the Lobo coast, with a small keeper\'s house and a wide bay view. The lighthouse is a working light; visitors can climb to the gallery for the panoramic view of the Verde Passage and the Mindoro coast across the strait. Sunset from the lighthouse is the standard descent meal.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c8/Malabrigo_Lighthouse_Lobo_Batangas.jpg/800px-Malabrigo_Lighthouse_Lobo_Batangas.jpg" alt="Malabrigo Lighthouse, Lobo Batangas" loading="lazy"><figcaption>Malabrigo Lighthouse on the southeast tip of the Lobo coast, the 1891 brick tower that closes the post-climb sunset. Image via Wikimedia Commons.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 35. MT. MAKILING NORTH TRAIL
    // ------------------------------------------------------------------
    'mt-makiling-north-trail-quiet-climb-los-banos-laguna' => [
        [
            'anchor' => '<p>The Mudspring is a sulfur-vent pool around 30 minutes into the climb. The water is muddy gray, the air smells of sulfur, and the small pool bubbles continuously. The site is a quick photo stop; do not touch the water, the temperature is around 70 degrees Celsius.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/22/Mudspring_Mt_Makiling_UPLB.jpg/800px-Mudspring_Mt_Makiling_UPLB.jpg" alt="Mudspring sulfur vent at Mt. Makiling, Los Banos" loading="lazy"><figcaption>The Mt. Makiling Mudspring above UPLB, the sulfur-vent pool that bubbles continuously at around 70 degrees Celsius. Image via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Past the Mudspring, the trail climbs through the Makiling secondary forest for around three hours. The canopy is dense and the trail is well-shaded. The forest holds the famous Makiling biodiversity; the trees include Philippine teak, narra, and the endemic almaciga. Birds are constant; the local birdwatchers come here for the rufous coucal and the white-eared brown dove.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/calamba-pansol-hot-springs-calamba-barangay.jpg" alt="Pansol hot springs in Calamba, Laguna" loading="lazy"><figcaption>The Pansol hot springs in Calamba, the 38-to-42-degree volcanic soak that doubles as the standard post-Makiling recovery stop.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>After the climb, the Los Baños hot-spring strip is a 20-minute drive from the UPLB gate. The local pools are fed by underground volcanic water with a temperature of around 38 to 42 degrees Celsius. The standard descent meal is a hot-spring soak followed by buko pie at the Lety\'s Buko Pie shop along the highway. The soak after a Makiling climb is the kind of recovery that turns a long day into a manageable evening.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/calamba-3.jpg" alt="Calamba and Los Banos highway corridor" loading="lazy"><figcaption>The Calamba and Los Banos corridor along the national highway, where Lety\'s Buko Pie and the hot-spring inns line the descent route.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 36. MT. MANALMON
    // ------------------------------------------------------------------
    'mt-manalmon-bulacan-half-day-climb-cave-and-river' => [
        [
            'anchor' => '<p>The crossing is on a small bamboo raft pulled across the river by a rope. The local crew handles the rope; visitors sit or stand. The crossing takes around 90 seconds. The raft is the friendly version of the river crossing; you stay dry.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/15/Madlum_River_bamboo_raft_Bulacan.jpg/800px-Madlum_River_bamboo_raft_Bulacan.jpg" alt="Madlum River bamboo raft in DRT, Bulacan" loading="lazy"><figcaption>The rope-pulled bamboo raft across the Madlum River at the base of Mt. Manalmon, the friendly version of the river crossing in DRT. Image via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>After the raft, the trail passes the entrance to Madlum Cave. The cave is shallow, you walk in for around 30 meters and out the back. The chamber has stalactites and a small spring pool. The local guide carries a torch.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/7/7e/Madlum_Cave_chamber_Bulacan.jpg/800px-Madlum_Cave_chamber_Bulacan.jpg" alt="Madlum Cave chamber on Mt. Manalmon trail" loading="lazy"><figcaption>The Madlum Cave on the Manalmon approach, the shallow 30-meter chamber with stalactites and a small spring pool. Image via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The view sweeps north into the Sierra Madre, east into the DRT valley, south back to the Madlum river, and west toward Bulacan central. The peak fits maybe 20 trekkers comfortably. Photos and a quick snack, then descend.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/bulacan-province-biak-na-bato-national-park.jpg" alt="Biak-na-Bato National Park in Bulacan" loading="lazy"><figcaption>Biak-na-Bato National Park, the protected limestone-and-forest belt in the same DRT-San Miguel watershed as Mt. Manalmon and the Madlum River.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 37. MT. SUSONG DALAGA
    // ------------------------------------------------------------------
    'mt-susong-dalaga-norzagaray-bulacan-short-cogon-climb' => [
        [
            'anchor' => '<p>The peak is a wide grass clearing with a 360-degree view. The Sierra Madre rises to the east, the Bulacan lowland spreads to the west, and on a clear morning the Marikina Valley is visible to the southwest. The clearing fits maybe 20 trekkers.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/d/d4/Mt_Susong_Dalaga_summit_Norzagaray.jpg/800px-Mt_Susong_Dalaga_summit_Norzagaray.jpg" alt="Mt. Susong Dalaga summit in Norzagaray, Bulacan" loading="lazy"><figcaption>The Mt. Susong Dalaga summit clearing in Norzagaray, with the Sierra Madre to the east and the Bulacan lowland to the west. Image via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>For groups that want the overnight version, the summit holds a small flat clearing on the leeward side. Pitch the tent for the sunset and the sunrise the next morning. The camp has no water source; carry up two liters per person.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/bulacan-province-3.jpg" alt="Bulacan foothills near Norzagaray" loading="lazy"><figcaption>The Bulacan foothills around Norzagaray, the southern Sierra Madre belt that holds Susong Dalaga and the bigger climbs farther east.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 38. MT. PINATUBO VIA SAPANG UWAK
    // ------------------------------------------------------------------
    'mt-pinatubo-via-sapang-uwak-porac-aeta-guided-trail-route' => [
        [
            'anchor' => '<p>The canyon walls rise on both sides in pink and grey ash deposits from the 1991 eruption. The trail crosses the river several times in shallow sections. The lahar bed is the visual signature of the trail; the route looks like a different planet.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/4d/Pinatubo_lahar_canyon_Sapang_Uwak.jpg/800px-Pinatubo_lahar_canyon_Sapang_Uwak.jpg" alt="Mt. Pinatubo lahar canyon via Sapang Uwak, Porac" loading="lazy"><figcaption>The Pasig-Potrero lahar canyon on the Sapang Uwak trail, with pink and grey ash walls from the 1991 eruption. Image via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Wake at 5 a.m. Walk the short trail to the crater rim by 5:30 a.m. The sky lightens over the lake and the inner crater walls catch the first light. The lake is a sulfur green with a deeper blue at the center, the same lake the Capas day-trippers see, but from the opposite rim and with no crowd in sight. The morning at the crater is the kind of moment that pays back the long approach.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/pampanga-province-mt-pinatubo-crater-lake.jpg" alt="Mt. Pinatubo crater lake at dawn" loading="lazy"><figcaption>The Mt. Pinatubo crater lake, the sulfur-green water with a deeper blue at the center that the Sapang Uwak campers see from the southern rim at dawn.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The Aeta community runs the trail because the mountain is their ancestral land. Respect the protocols: no loud music, no rocks taken as souvenirs, no swimming in the crater lake (the gas releases from the lake bed are unpredictable, the same warning as the Capas side). Tip the guides at the end of the trek; the rate is set by the community.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/pampanga-province-2.jpg" alt="Pampanga foothills near Porac" loading="lazy"><figcaption>The Porac foothills in Pampanga, the Aeta community lands that anchor the longer Sapang Uwak trail up Mt. Pinatubo.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 39. MT. BANOI
    // ------------------------------------------------------------------
    'mt-banoi-traverse-lobo-batangas-quiet-coastal-ridge-climb' => [
        [
            'anchor' => '<p>The last hour of the climb runs along a ridge with the first sea views opening to the south. The Verde Island Passage spreads to the south, the Lobo coast to the east, and the Mindoro silhouette across the strait on a clear day. The grass on the ridge is calf-high; the wind picks up steady.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/89/Mt_Banoi_ridge_Lobo_Batangas.jpg/800px-Mt_Banoi_ridge_Lobo_Batangas.jpg" alt="Mt. Banoi summit ridge in Lobo, Batangas" loading="lazy"><figcaption>The Mt. Banoi summit ridge in Lobo, with the Verde Island Passage opening to the south and the Mindoro silhouette across the strait. Image via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>For groups doing the traverse, the route descends from Banoi to the Mt. Lobo summit via a connecting ridge, then back down the standard Lobo trail to Brgy. Sawang. The traverse adds three hours of moving time. Coordinate the traverse with the local guides; the route requires a one-way pickup at the Lobo-side jump-off.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/lobo-3.jpg" alt="Lobo coast in southeastern Batangas" loading="lazy"><figcaption>The Lobo coast in southeastern Batangas, the connecting ridge land between Mt. Banoi and Mt. Lobo on the traverse route.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 40. MT. APO VIA STA. CRUZ
    // ------------------------------------------------------------------
    'mt-apo-via-sta-cruz-trail-three-day-climb-davao-del-sur' => [
        [
            'anchor' => '<p>Day 2 climbs through the mossy forest belt to the Lake Venado camp. The mossy forest is the signature of Apo\'s middle slopes; the trees are thick with moss, the ground is mossy, and the light is filtered green. The climb takes around six hours from Camp 1 to Lake Venado.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/23/Mt_Apo_mossy_forest_belt.jpg/800px-Mt_Apo_mossy_forest_belt.jpg" alt="Mt. Apo mossy forest belt above Camp 1" loading="lazy"><figcaption>The mossy forest belt on the Mt. Apo middle slopes, the signature canopy that the Sta. Cruz trail climbs through on Day 2. Image via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Lake Venado sits at 2,200 meters elevation, a wide shallow lake at the base of the Apo summit cone. The camp is on the lake\'s southern shore. The site is open, the wind picks up at night, and the temperature drops to 8 to 10 degrees by midnight. The lake itself dries up in the peak summer months but holds water from November to May.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/3/31/Lake_Venado_Mt_Apo_camp.jpg/800px-Lake_Venado_Mt_Apo_camp.jpg" alt="Lake Venado camp at the base of Mt. Apo summit cone" loading="lazy"><figcaption>Lake Venado at 2,200 meters, the Day 2 camp on the southern shore at the base of the Mt. Apo summit cone. Image via Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The summit of Mt. Apo is a wide plateau with a small marker and a 360-degree view. The Davao Gulf opens to the east, the South Cotabato lowland to the south, the Mindanao interior to the north, and Sarangani to the southwest. The plateau holds 50 trekkers comfortably; on the peak days from January to April it can be busier.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/kidapawan-mt-apo-natural-park.jpg" alt="Mt. Apo Natural Park in Davao region" loading="lazy"><figcaption>Mt. Apo Natural Park at 2,956 meters, the highest peak in the Philippines and the long-walk reward at the end of the Sta. Cruz three-day climb.</figcaption></figure>',
        ],
    ],

];
