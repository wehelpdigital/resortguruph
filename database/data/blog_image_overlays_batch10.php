<?php

/**
 * Blog image overlays for batch 10 posts (Luzon and Mindanao trail / island /
 * conservation guides). Each entry maps a blog slug to a list of <figure>
 * HTML blocks that the renderer injects either before or after a unique
 * anchor substring found in the post content_html.
 *
 * Slugs without a clear list-of-specific-photographable-things are skipped.
 */

return [

    // ----------------------------------------------------------------------
    // Mt. Manabu sunrise climb (Sto. Tomas, Batangas)
    // ----------------------------------------------------------------------
    'mt-manabu-sunrise-climb-sto-tomas-day-hike' => [
        [
            'anchor' => 'The trail starts on a wide farm road that climbs through the coffee plants.',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/26/Mt_Manabu_summit_view.jpg/800px-Mt_Manabu_summit_view.jpg" alt="View from Mt. Manabu summit toward Mt. Malarayat and Taal Lake" loading="lazy"><figcaption>The Mt. Manabu summit cross opens to Mt. Malarayat next door and Taal Lake on the western horizon. Best caught right after the pre-dawn climb from Sta. Cruz, Sto. Tomas. Photo via <a href="https://commons.wikimedia.org/wiki/File:Mt_Manabu_summit_view.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'Back at the jump-off, Mang Pirying\'s family serves the brewed Manabu coffee with simple breakfast plates.',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/0/0e/Alfonso_kapeng_barako.jpg/800px-Alfonso_kapeng_barako.jpg" alt="Locally brewed barako coffee beans similar to what Mang Pirying serves" loading="lazy"><figcaption>Locally brewed barako-style kape, the same family of beans the Mang Pirying clan roasts for climbers at the Manabu jump-off. Photo via <a href="https://commons.wikimedia.org/wiki/File:Alfonso_kapeng_barako.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Mt. Arayat day hike (Pampanga)
    // ----------------------------------------------------------------------
    'mt-arayat-pampanga-day-hike-honest-read' => [
        [
            'anchor' => 'The standard hike starts at the National Park entrance and follows a marked trail through the secondary forest.',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/arayat-mt-arayat-national-park.jpg" alt="Mt. Arayat rising over the Pampanga plain" loading="lazy"><figcaption>Mt. Arayat, the lone peak rising from the flat Pampanga rice fields. The National Park entrance on the Arayat-town side is the most common jump-off for the south summit.</figcaption></figure>',
        ],
        [
            'anchor' => 'The middle section has three short rope-assisted scrambles.',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/arayat-sisig.jpg" alt="Kapampangan sisig plate from Arayat" loading="lazy"><figcaption>Sizzling Kapampangan sisig in Arayat town, the standard post-climb meal back in the lowlands once the rope sections are behind you.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Polillo Island slow weekend (Quezon)
    // ----------------------------------------------------------------------
    'polillo-island-slow-weekend-quezon-coast' => [
        [
            'anchor' => 'The Spanish-era lighthouse on Punta de Pesa is the standard first stop.',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/a4/Polillo_Lighthouse.jpg/800px-Polillo_Lighthouse.jpg" alt="Polillo Lighthouse on Punta de Pesa Quezon" loading="lazy"><figcaption>The Spanish-era Polillo Lighthouse on Punta de Pesa. Still a working navigation light, with a spiral stair the keeper sometimes lets visitors climb. Photo via <a href="https://commons.wikimedia.org/wiki/File:Polillo_Lighthouse.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'The standout is Sibul Beach with white sand and shallow turquoise water',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/9c/Sibul_Beach_Polillo_Island.jpg/800px-Sibul_Beach_Polillo_Island.jpg" alt="Sibul Beach in Burdeos Polillo Island Quezon" loading="lazy"><figcaption>Sibul Beach on the Burdeos side of Polillo Island. The water stays shallow far out, and the white sand is finer than what you find on most of the Quezon coast. Photo via <a href="https://commons.wikimedia.org/wiki/File:Sibul_Beach_Polillo_Island.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Hulugan and Talay Falls (Luisiana, Laguna)
    // ----------------------------------------------------------------------
    'hulugan-falls-talay-falls-luisiana-day-trip' => [
        [
            'anchor' => 'The 70-meter drop sends water sheets across the basin and the sound carries through the canyon.',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/8e/Hulugan_Falls_Luisiana_Laguna.jpg/800px-Hulugan_Falls_Luisiana_Laguna.jpg" alt="Hulugan Falls 70-meter drop in Luisiana Laguna" loading="lazy"><figcaption>Hulugan Falls in Luisiana, Laguna. The 70-meter drop and wide basin sit at the end of a 30 to 45 minute descent from Sitio Aliw. Photo via <a href="https://commons.wikimedia.org/wiki/File:Hulugan_Falls_Luisiana_Laguna.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'Talay is a two-tier waterfall with a wider basin and a calmer flow',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/3/3f/Talay_Falls_Luisiana.jpg/800px-Talay_Falls_Luisiana.jpg" alt="Talay Falls two-tier waterfall Luisiana Laguna" loading="lazy"><figcaption>Talay Falls, the calmer pairing on the same Sitio Aliw trail. The two-tier basin makes a quieter cooldown after the noise at Hulugan. Photo via <a href="https://commons.wikimedia.org/wiki/File:Talay_Falls_Luisiana.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Capones and Camara Islands (Zambales)
    // ----------------------------------------------------------------------
    'capones-camara-islands-zambales-day-pundaquit' => [
        [
            'anchor' => 'The boat lands on the long white sand beach on the south side of Capones.',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/1b/Capones_Island.jpg/800px-Capones_Island.jpg" alt="Capones Island white sand beach Zambales" loading="lazy"><figcaption>Capones Island from the south beach. The Spanish-era lighthouse sits on the bluff at the northern end of the island. Photo via <a href="https://commons.wikimedia.org/wiki/File:Capones_Island.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'The trail to the Spanish-era lighthouse takes around 30 minutes of moderate climbing on a rocky path.',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/55/Capones_Island_Lighthouse.jpg/800px-Capones_Island_Lighthouse.jpg" alt="Capones Island lighthouse Zambales" loading="lazy"><figcaption>The Capones lighthouse on the northern bluff. Late 1800s build, still standing, with views of the open sea on one side and the Pundaquit coast on the other. Photo via <a href="https://commons.wikimedia.org/wiki/File:Capones_Island_Lighthouse.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Crystal Beach surf weekend (San Narciso, Zambales)
    // ----------------------------------------------------------------------
    'crystal-beach-surf-weekend-zambales-first-boards' => [
        [
            'anchor' => 'The dry season swells come from the South China Sea and hit Crystal Beach with shoulder-high sets at the peak.',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/49/Crystal_Beach_Zambales.jpg/800px-Crystal_Beach_Zambales.jpg" alt="Crystal Beach surf break in San Narciso Zambales" loading="lazy"><figcaption>Crystal Beach in San Narciso, Zambales. Smaller, friendlier waves than Urbiztondo, and the closest learn-to-surf weekend within reach of Manila. Photo via <a href="https://commons.wikimedia.org/wiki/File:Crystal_Beach_Zambales.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'The standard package is a one-hour lesson plus board rental, and most first-timers stand up by the second session.',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/d/da/Surf_lesson_pop_up_drill.jpg/800px-Surf_lesson_pop_up_drill.jpg" alt="Surf instructor coaching the pop-up drill on the sand" loading="lazy"><figcaption>The pop-up drill on the sand is where every Crystal Beach lesson starts. Most first-timers stand up on the foam board by the second one-hour session. Photo via <a href="https://commons.wikimedia.org/wiki/File:Surf_lesson_pop_up_drill.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Mt. Tapulao two-day climb (Zambales)
    // ----------------------------------------------------------------------
    'mt-tapulao-two-day-climb-zambales-long-trail' => [
        [
            'anchor' => 'The pine forest in the upper section is the standout feature of Tapulao.',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/4d/Mount_Tapulao_pine_forest.jpg/800px-Mount_Tapulao_pine_forest.jpg" alt="Mt. Tapulao upper pine forest campsite Zambales" loading="lazy"><figcaption>The pine zone near the Mt. Tapulao summit campsite. Climate-shifted from the Cordillera, this is the lone pine stand in Zambales and the standout feature of the long climb from Dampay-Salaza. Photo via <a href="https://commons.wikimedia.org/wiki/File:Mount_Tapulao_pine_forest.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'The summit itself is a small clearing with a marker and a view of the Zambales range stretching to the north and east.',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/86/Mt_Tapulao_summit_marker.jpg/800px-Mt_Tapulao_summit_marker.jpg" alt="Mt. Tapulao summit marker and Zambales range view" loading="lazy"><figcaption>The Mt. Tapulao summit at around 2,037 meters. The marker sits in a small clearing with views of the Zambales range to the north and east. Photo via <a href="https://commons.wikimedia.org/wiki/File:Mt_Tapulao_summit_marker.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Lake Sebu (South Cotabato)
    // ----------------------------------------------------------------------
    'lake-sebu-floating-restaurants-seven-falls-zipline' => [
        [
            'anchor' => 'The standard Lake Sebu lunch is at one of the floating restaurants along the eastern shore.',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/4b/Lake_Sebu_floating_restaurant.jpg/800px-Lake_Sebu_floating_restaurant.jpg" alt="Lake Sebu floating restaurant on stilts South Cotabato" loading="lazy"><figcaption>One of the Lake Sebu floating restaurants on the eastern shore. The tilapia is raised in cages right beside the dining deck and grilled to order. Photo via <a href="https://commons.wikimedia.org/wiki/File:Lake_Sebu_floating_restaurant.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'Stage 1 is around 740 meters across the canyon, with views of Falls 2 and 3 below.',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/2c/Seven_Falls_Lake_Sebu.jpg/800px-Seven_Falls_Lake_Sebu.jpg" alt="Seven Falls Lake Sebu zipline canyon" loading="lazy"><figcaption>The Seven Falls canyon below the Lake Sebu zipline. Stage 1 of the line runs around 740 meters across the gorge with the second and third falls visible below. Photo via <a href="https://commons.wikimedia.org/wiki/File:Seven_Falls_Lake_Sebu.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'The handwoven Tnalak fabric, made from abaca fibers and dyed in the traditional ways',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c5/T%27nalak_weaving_Lake_Sebu.jpg/800px-T%27nalak_weaving_Lake_Sebu.jpg" alt="Tboli Tnalak weaving Lake Sebu South Cotabato" loading="lazy"><figcaption>A Tboli weaver at her loom in Lake Sebu. The handwoven Tnalak is dyed with traditional plant pigments and remains the cultural treasure of South Cotabato. Photo via <a href="https://commons.wikimedia.org/wiki/File:T%27nalak_weaving_Lake_Sebu.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Asik-Asik Falls (Alamada, North Cotabato)
    // ----------------------------------------------------------------------
    'asik-asik-falls-curtain-waterfall-cotabato-read' => [
        [
            'anchor' => 'The water flows directly out of the rock face along a 140-meter-wide curtain',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/7/7e/Asik-Asik_Falls_Alamada.jpg/800px-Asik-Asik_Falls_Alamada.jpg" alt="Asik-Asik Falls curtain wall Alamada North Cotabato" loading="lazy"><figcaption>The 140-meter wide curtain wall at Asik-Asik Falls in Alamada, North Cotabato. The water emerges directly from the moss-covered rock face, fed by an underground aquifer instead of a surface river. Photo via <a href="https://commons.wikimedia.org/wiki/File:Asik-Asik_Falls_Alamada.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'The first section is a series of concrete steps the barangay installed for easier access.',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/57/Asik-Asik_trail_steps.jpg/800px-Asik-Asik_trail_steps.jpg" alt="Concrete steps on the Asik-Asik Falls descent trail" loading="lazy"><figcaption>The concrete steps the Alamada barangay installed on the upper descent from Sitio Dado. The lower half drops into a coconut grove on packed earth. Photo via <a href="https://commons.wikimedia.org/wiki/File:Asik-Asik_trail_steps.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Mt. Apo via Kapatagan (Davao del Sur)
    // ----------------------------------------------------------------------
    'mt-apo-kapatagan-trail-davao-calm-read' => [
        [
            'anchor' => 'The boulders are loose volcanic rocks the eruption history left behind.',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/0/05/Mount_Apo_boulder_field.jpg/800px-Mount_Apo_boulder_field.jpg" alt="Mt. Apo volcanic boulder field on the Kapatagan trail" loading="lazy"><figcaption>The famous Mt. Apo boulder field on the Kapatagan side. Around two hours of hand-on-rock climbing on a 45-degree grade, with cairn markers along the line. Photo via <a href="https://commons.wikimedia.org/wiki/File:Mount_Apo_boulder_field.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'The crater itself is a large depression with volcanic vents still releasing sulfur in places.',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/b/bc/Mount_Apo_crater_rim.jpg/800px-Mount_Apo_crater_rim.jpg" alt="Mt. Apo crater rim and sulfur vents Davao del Sur" loading="lazy"><figcaption>The Mt. Apo crater rim at 2,956 meters. The walking rim circles a wide depression with sulfur vents still active in several places. Photo via <a href="https://commons.wikimedia.org/wiki/File:Mount_Apo_crater_rim.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Taal Volcano viewpoint (Tagaytay)
    // ----------------------------------------------------------------------
    'taal-volcano-viewpoint-tagaytay-after-2020-eruption' => [
        [
            'anchor' => 'The 360-degree view from the top deck shows Taal Lake on one side and the Cavite plains on the other.',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/9a/Taal_Volcano_from_Tagaytay_Ridge.jpg/800px-Taal_Volcano_from_Tagaytay_Ridge.jpg" alt="Taal Volcano and lake from Tagaytay ridge viewpoint" loading="lazy"><figcaption>Taal Volcano and the lake from the Tagaytay ridge. The 2020 eruption closed the boat tours and the crater hike, but the view from up here still holds. Photo via <a href="https://commons.wikimedia.org/wiki/File:Taal_Volcano_from_Tagaytay_Ridge.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'Eat bulalo at one of the long-running Tagaytay restaurants for lunch',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/tagaytay-bulalo.jpg" alt="Tagaytay bulalo bone marrow soup bowl" loading="lazy"><figcaption>The Tagaytay bulalo lunch, the standard ridge-side meal after a morning at the viewpoints. The marrow bone is the part the bulalo eaters fight over.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Pico de Loro cove (Nasugbu)
    // ----------------------------------------------------------------------
    'pico-de-loro-cove-calm-beach-side-nasugbu' => [
        [
            'anchor' => 'The beach is a quiet white-sand stretch around 300 meters long.',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/f/f9/Pico_de_Loro_Cove_beach.jpg/800px-Pico_de_Loro_Cove_beach.jpg" alt="Pico de Loro Cove white sand beach Nasugbu Batangas" loading="lazy"><figcaption>The 300-meter white sand cove on the back side of Pico de Loro near Hamilo Coast. Quiet, shaded, and inside the marine sanctuary protected by the surrounding resort area. Photo via <a href="https://commons.wikimedia.org/wiki/File:Pico_de_Loro_Cove_beach.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'The snorkeling along the rocky outcrops at both ends has small reef fish, soft corals, and the occasional sea turtle.',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/nasugbu-3.jpg" alt="Nasugbu Batangas reef and clear coastal water" loading="lazy"><figcaption>The protected Nasugbu coast around Hamilo, where the reef has been managed carefully and the small reef fish, soft corals, and occasional sea turtle still show up around the rocky outcrops.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Mt. Pamitinan and Wawa River (Rodriguez, Rizal)
    // ----------------------------------------------------------------------
    'mt-pamitinan-wawa-river-rodriguez-day-hike' => [
        [
            'anchor' => 'Three or four short rock scrambles where you climb hand over hand through narrow gaps in the limestone.',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/47/Mt_Pamitinan_limestone.jpg/800px-Mt_Pamitinan_limestone.jpg" alt="Mt. Pamitinan limestone scramble Rodriguez Rizal" loading="lazy"><figcaption>The sharp limestone scrambles on Mt. Pamitinan, the steep middle section between the forest trail and the summit platform. Gloves are not optional here. Photo via <a href="https://commons.wikimedia.org/wiki/File:Mt_Pamitinan_limestone.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'The summit accommodates around 10 people at a time, so plan an early start to avoid the weekend queue.',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/2d/Pamitinan_summit_view.jpg/800px-Pamitinan_summit_view.jpg" alt="Mt. Pamitinan summit view to the Sierra Madre and Wawa River" loading="lazy"><figcaption>The Mt. Pamitinan summit looking east over the Wawa River bend and the Sierra Madre range. The Manila skyline opens on the opposite side on clear days. Photo via <a href="https://commons.wikimedia.org/wiki/File:Pamitinan_summit_view.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Wawa Dam river walk (Rodriguez, Rizal)
    // ----------------------------------------------------------------------
    'wawa-dam-river-walk-slow-rodriguez-morning' => [
        [
            'anchor' => 'The dam is a low concrete structure built into the gorge, with the river spilling over the lip in a sheet of water.',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/rodriguez-montalban-wawa-dam.jpg" alt="Wawa Dam concrete spill in the Rodriguez gorge" loading="lazy"><figcaption>Wawa Dam in Rodriguez, Rizal. Built in the early 1900s as one of the original Manila water sources, the dam is retired but still spills cleanly into the gorge below.</figcaption></figure>',
        ],
        [
            'anchor' => 'The trail follows the riverbank through the gorge, between the limestone cliffs that frame the view on both sides.',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/6e/Wawa_River_Gorge_Rodriguez.jpg/800px-Wawa_River_Gorge_Rodriguez.jpg" alt="Wawa River gorge limestone cliffs Rodriguez Rizal" loading="lazy"><figcaption>The Wawa River walking trail follows the bank between the limestone cliffs of Pamitinan, Binacayan, and Hapunang Banoi. Flat, shaded, and within an hour of EDSA. Photo via <a href="https://commons.wikimedia.org/wiki/File:Wawa_River_Gorge_Rodriguez.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Masungi Georeserve (Tanay, Rizal)
    // ----------------------------------------------------------------------
    'masungi-georeserve-discovery-trail-tanay-walk' => [
        [
            'anchor' => 'The famous spider-web net suspended over the limestone peaks.',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/tanay-masungi-georeserve.jpg" alt="Masungi Georeserve Sapot spider-web net over limestone karst Tanay" loading="lazy"><figcaption>Sapot, the steel spider-web net suspended over the limestone peaks at Masungi Georeserve. The net flexes slightly with movement, which is part of the experience.</figcaption></figure>',
        ],
        [
            'anchor' => 'The largest hanging hammock-style rope bridge, suspended across a valley between two limestone peaks.',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/3/31/Masungi_Georeserve_Duyan.jpg/800px-Masungi_Georeserve_Duyan.jpg" alt="Masungi Georeserve Duyan hammock rope bridge Tanay" loading="lazy"><figcaption>Duyan, the hammock-style rope bridge slung between two limestone peaks on the Masungi Discovery Trail. The middle opens to the surrounding Sierra Madre range. Photo via <a href="https://commons.wikimedia.org/wiki/File:Masungi_Georeserve_Duyan.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'The climb to Tatay involves a short rope-assisted scramble, and the view from the top extends to Laguna de Bay and the eastern Cordillera on clear mornings.',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c8/Masungi_Tatay_Peak.jpg/800px-Masungi_Tatay_Peak.jpg" alt="Masungi Tatay limestone peak Tanay Rizal" loading="lazy"><figcaption>Tatay, the named limestone peak on the Masungi Discovery Trail. The short rope-assisted scramble opens to Laguna de Bay and the Sierra Madre on clear mornings. Photo via <a href="https://commons.wikimedia.org/wiki/File:Masungi_Tatay_Peak.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

];
