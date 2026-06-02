<?php

/**
 * Blog image overlays for the batch 9 import (mostly Detourista and
 * Lakwatsero trip guides covering Camotes, Calaguas, Pinatubo, Mt. Apo,
 * Lake Sebu, Sumilon, Bagasbas, Ticao, Bolinao, Borawan, Daraitan,
 * Masungi, Capones, Talicud, Dahican, Cuyo, and Batulao).
 *
 * Each entry maps a blog slug to a list of <figure> HTML blocks that
 * the renderer injects either before or after a unique anchor substring
 * found in the post content_html.
 */

return [

    // ----------------------------------------------------------------------
    // Camotes Islands DIY weekend
    // ----------------------------------------------------------------------
    'camotes-islands-slow-weekend-cebu-diy' => [
        [
            'anchor' => 'Santiago Bay on the western coast of Pacijan is the standard base. The long white sand beach faces west',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/96/Santiago_Bay%2C_Camotes_Islands.jpg/800px-Santiago_Bay%2C_Camotes_Islands.jpg" alt="Santiago Bay beach on Pacijan Camotes Islands Cebu" loading="lazy"><figcaption>Santiago Bay on Pacijan, the standard Camotes base. The shoreline stays shallow for a long way out and the sunset is the consistent draw. Photo via <a href="https://commons.wikimedia.org/wiki/File:Santiago_Bay,_Camotes_Islands.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'Lake Danao is the freshwater lake in the middle of Pacijan, about 20 minutes by motorbike',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/8b/Lake_Danao_-_Camotes_Islands.jpg/800px-Lake_Danao_-_Camotes_Islands.jpg" alt="Lake Danao freshwater lake on Pacijan Camotes Islands" loading="lazy"><figcaption>Lake Danao in the middle of Pacijan. Bancas are cheap to hire and the swim is the easy reward after the hot ride from Santiago Bay. Photo via <a href="https://commons.wikimedia.org/wiki/File:Lake_Danao_-_Camotes_Islands.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'Mangodlong on Poro Island is the dramatic rock formation scene with the offshore stack',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/ad/Mangodlong_Rock_Resort_Camotes.jpg/800px-Mangodlong_Rock_Resort_Camotes.jpg" alt="Mangodlong rock stack offshore on Poro Island Camotes" loading="lazy"><figcaption>Mangodlong on Poro Island. The offshore stack is the photo most travelers come for and the cliff jump runs busy on weekends. Photo via <a href="https://commons.wikimedia.org/wiki/File:Mangodlong_Rock_Resort_Camotes.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Calaguas Tinaga Island camping
    // ----------------------------------------------------------------------
    'calaguas-tinaga-island-camping-weekend' => [
        [
            'anchor' => 'The beach is a long curving stretch of fine white sand backed by coconut trees and a low ridge of grass.',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/45/Calaguas_Island_Mahabang_Buhangin.jpg/800px-Calaguas_Island_Mahabang_Buhangin.jpg" alt="Mahabang Buhangin beach on Tinaga Calaguas Camarines Norte" loading="lazy"><figcaption>Mahabang Buhangin on Tinaga Island. Two kilometers of powdery sand with no permanent buildings, only the seasonal camping setups. Photo via <a href="https://commons.wikimedia.org/wiki/File:Calaguas_Island_Mahabang_Buhangin.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'Day one is the travel day. Arrive at Mahabang Buhangin by mid-afternoon, set up the camp',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/1f/Calaguas_Islands_Camping.jpg/800px-Calaguas_Islands_Camping.jpg" alt="Tents pitched at the camping area on Calaguas Tinaga Island" loading="lazy"><figcaption>The main camping area at Mahabang Buhangin. No electricity past the small cantina, so the headlamp and a full power bank are not optional. Photo via <a href="https://commons.wikimedia.org/wiki/File:Calaguas_Islands_Camping.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Mt Pinatubo 4x4 day trip (batch9 / Detourista)
    // ----------------------------------------------------------------------
    'mt-pinatubo-crater-4x4-day-trip-manila' => [
        [
            'anchor' => 'The lahar field is a flat grey expanse of volcanic mud and pumice that has slowly become a strange high-desert landscape.',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/9d/Pinatubo_lahar_canyon.jpg/800px-Pinatubo_lahar_canyon.jpg" alt="Lahar canyon walls along the 4x4 route to Mt Pinatubo" loading="lazy"><figcaption>The lahar canyon on the 4x4 route from Sta. Juliana. The ash walls rise on both sides and the river crossings change line week to week. Photo via <a href="https://commons.wikimedia.org/wiki/File:Pinatubo_lahar_canyon.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'The crater lake itself is a deep emerald green that looks unreal in the noon light.',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/pampanga-province-mt-pinatubo-crater-lake.jpg" alt="Mt Pinatubo crater lake from the rim viewing deck" loading="lazy"><figcaption>The Pinatubo crater lake from the rim. The emerald color is from the dissolved minerals and the silence at the top is the part most travelers remember.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Mt Apo via Kidapawan
    // ----------------------------------------------------------------------
    'mt-apo-kidapawan-two-day-climb-guide' => [
        [
            'anchor' => 'The Kidapawan trail starts at Lake Agco at around 1,200 meters elevation. Day one climbs through dense forest',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/kidapawan-mt-apo-natural-park.jpg" alt="Mt Apo Natural Park trailhead in Kidapawan Cotabato" loading="lazy"><figcaption>The Kidapawan side of Mt Apo Natural Park. The trail jumps off at Lake Agco and the porter and guide system is run with the Manobo and Bagobo communities.</figcaption></figure>',
        ],
        [
            'anchor' => 'The Lake Venado camp sits in a flat grassland with the lake at the southern edge.',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/60/Lake_Venado_at_Mount_Apo.jpg/800px-Lake_Venado_at_Mount_Apo.jpg" alt="Lake Venado assault camp on Mt Apo Cotabato" loading="lazy"><figcaption>Lake Venado, the assault camp at around 2,400 meters. Pitch on the higher ground, the lakeside gets cold and damp by night. Photo via <a href="https://commons.wikimedia.org/wiki/File:Lake_Venado_at_Mount_Apo.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'The summit itself is a wide rocky area with the sulfur vents on the eastern side',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/b/b6/Mount_Apo_summit.jpg/800px-Mount_Apo_summit.jpg" alt="Mt Apo summit plateau with sulfur vents at 2954 meters" loading="lazy"><figcaption>The Mt Apo summit plateau at 2,954 meters. The sulfur vents sit on the eastern side and the wide Davao view opens on a clear sunrise. Photo via <a href="https://commons.wikimedia.org/wiki/File:Mount_Apo_summit.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Lake Sebu Tboli weekend
    // ----------------------------------------------------------------------
    'lake-sebu-tboli-weekend-seven-falls-zipline' => [
        [
            'anchor' => 'The Seven Falls is a chain of waterfalls along the Allah Valley, a short drive from Lake Sebu town.',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/3/30/Seven_Falls_Lake_Sebu.jpg/800px-Seven_Falls_Lake_Sebu.jpg" alt="One of the Seven Falls in the Allah Valley near Lake Sebu" loading="lazy"><figcaption>The first of the Seven Falls in the Allah Valley. The lower two have viewing decks and short walking trails, the rest are visible from the zipline above. Photo via <a href="https://commons.wikimedia.org/wiki/File:Seven_Falls_Lake_Sebu.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'The main attraction in Lake Sebu town is the lake itself, dotted with pink and white lotus pads',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/24/Lake_Sebu_Lotus.jpg/800px-Lake_Sebu_Lotus.jpg" alt="Lotus pads on Lake Sebu South Cotabato at sunrise" loading="lazy"><figcaption>The lotus pads on Lake Sebu in the dry months. A sunrise paddleboat from the lakeside resorts is the slow draw most travelers underbook. Photo via <a href="https://commons.wikimedia.org/wiki/File:Lake_Sebu_Lotus.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'The School of Living Tradition in Lake Sebu is run by the Tboli weaver Lang Dulay',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/f/fd/Tnalak_weaving.jpg/800px-Tnalak_weaving.jpg" alt="Tboli dreamweaver at the loom making tnalak abaca cloth" loading="lazy"><figcaption>Tboli tnalak weaving at the School of Living Tradition. Patterns come from dreams, the dyes are ochre and red, and a single piece can take months. Photo via <a href="https://commons.wikimedia.org/wiki/File:Tnalak_weaving.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Sumilon sandbar
    // ----------------------------------------------------------------------
    'sumilon-island-sandbar-day-trip-south-cebu' => [
        [
            'anchor' => 'The Sumilon sandbar shifts position with the season. From November to April, the sandbar sits on the northern tip',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/0/04/Sumilon_Island_Sandbar.jpg/800px-Sumilon_Island_Sandbar.jpg" alt="Sumilon Island shifting sandbar off Oslob south Cebu" loading="lazy"><figcaption>The Sumilon sandbar at low tide on the northern tip. The shape shifts month to month with the wind and the swell. Photo via <a href="https://commons.wikimedia.org/wiki/File:Sumilon_Island_Sandbar.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'The southern side of Sumilon is a protected marine sanctuary with healthy hard coral',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c7/Sumilon_Island_snorkeling.jpg/800px-Sumilon_Island_snorkeling.jpg" alt="Snorkelers above coral at Sumilon Island marine sanctuary" loading="lazy"><figcaption>The Sumilon marine sanctuary on the southern side of the island. Hard coral gardens in the shallows, jacks in the drop, and the clear visibility south Cebu is known for. Photo via <a href="https://commons.wikimedia.org/wiki/File:Sumilon_Island_snorkeling.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Bagasbas Beach Daet
    // ----------------------------------------------------------------------
    'bagasbas-beach-daet-surf-weekend-beginners' => [
        [
            'anchor' => 'Bagasbas is a sandy-bottom beach break, which makes it friendly for beginners and a forgiving spot to learn.',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/b/b6/Bagasbas_Beach%2C_Daet.jpg/800px-Bagasbas_Beach%2C_Daet.jpg" alt="Bagasbas Beach surf break in Daet Camarines Norte" loading="lazy"><figcaption>Bagasbas Beach in Daet. Sandy bottom, mellow shape, and a lineup that rarely crowds up outside long weekends. Photo via <a href="https://commons.wikimedia.org/wiki/File:Bagasbas_Beach,_Daet.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'The surf camps along the beach front offer beginner lessons in groups or one-on-one.',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/5e/Surfing_at_Bagasbas_Beach.jpg/800px-Surfing_at_Bagasbas_Beach.jpg" alt="Beginner surfer paddling out at Bagasbas Beach Daet" loading="lazy"><figcaption>A beginner session at Bagasbas. Lessons run cleanest in the morning when the wind is offshore, and the camps book up fast on weekends. Photo via <a href="https://commons.wikimedia.org/wiki/File:Surfing_at_Bagasbas_Beach.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Ticao Manta Bowl diving
    // ----------------------------------------------------------------------
    'ticao-manta-bowl-diving-trip-sorsogon' => [
        [
            'anchor' => 'The Manta Bowl is a shallow plateau between Ticao and Burias islands, swept by the strong currents of the Ticao Pass.',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/89/Manta_ray_Philippines.jpg/800px-Manta_ray_Philippines.jpg" alt="Manta ray gliding over a Philippine reef cleaning station" loading="lazy"><figcaption>A manta ray at a Philippine cleaning station, the kind of pass the Ticao Bowl is known for in the dry season. Photo via <a href="https://commons.wikimedia.org/wiki/File:Manta_ray_Philippines.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'Ticao has a small cluster of other dive sites worth a day. The San Miguel Wall is a calm wall dive',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/sorsogon-2.jpg" alt="Sorsogon coastal scene near the Pilar ferry crossing to Ticao" loading="lazy"><figcaption>Sorsogon coastal scene near Pilar, the mainland jump-off for the Ticao ferry. The Donsol whale shark season overlaps with the Manta Bowl window for a natural combo.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Bolinao Falls + Cape Bolinao
    // ----------------------------------------------------------------------
    'bolinao-falls-cape-bolinao-day-trip' => [
        [
            'anchor' => 'The three Bolinao Falls sit close together in barangay Samang Norte, about 30 minutes by tricycle from the town center.',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/bolinao-bolinao-falls-1-2-3.jpg" alt="Bolinao Falls swim pool in barangay Samang Norte Pangasinan" loading="lazy"><figcaption>One of the three Bolinao Falls in Samang Norte. Each falls has its own entrance and its own pool at the base.</figcaption></figure>',
        ],
        [
            'anchor' => 'The Cape Bolinao Lighthouse sits on the highest point of the Bolinao peninsula',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/bolinao-cape-bolinao-lighthouse.jpg" alt="Cape Bolinao Lighthouse on the Pangasinan peninsula" loading="lazy"><figcaption>The Cape Bolinao Lighthouse, built in 1905, on the highest point of the peninsula. The viewing deck holds the wide West Philippine Sea sunset.</figcaption></figure>',
        ],
        [
            'anchor' => 'A 30-minute stop for a quick swim or a halo-halo at the beachfront cantinas works as a natural break',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/bolinao-patar-beach.jpg" alt="Patar Beach on the road between Bolinao Falls and Cape Bolinao Lighthouse" loading="lazy"><figcaption>Patar Beach on the same coastal road. A halo-halo stop here works as the natural break between the inland falls and the lighthouse afternoon.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Borawan Padre Burgos
    // ----------------------------------------------------------------------
    'borawan-island-padre-burgos-quezon-day' => [
        [
            'anchor' => 'Borawan is the headline stop. The island has a short stretch of cream-colored sand backed by limestone cliffs',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c8/Borawan_Island.jpg/800px-Borawan_Island.jpg" alt="Borawan Island limestone cliffs and beach Padre Burgos Quezon" loading="lazy"><figcaption>Borawan Island off Padre Burgos. The limestone cliffs behind the cream-colored sand are what gave the island its small-Bora reputation. Photo via <a href="https://commons.wikimedia.org/wiki/File:Borawan_Island.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'Dampalitan is the second stop, with a longer beach and a row of coconut trees that provide real shade.',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/2b/Dampalitan_Island_Padre_Burgos.jpg/800px-Dampalitan_Island_Padre_Burgos.jpg" alt="Dampalitan Island beach and coconut trees Padre Burgos Quezon" loading="lazy"><figcaption>Dampalitan Island, the natural lunch stop on the hop. Coconut shade, calm water, and the small carinderia where the boat captains arrange grilled fish. Photo via <a href="https://commons.wikimedia.org/wiki/File:Dampalitan_Island_Padre_Burgos.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'The third stop is Puting Buhangin with the small cave at one end called Kwebang Lampas.',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/d/d3/Kwebang_Lampas_Puting_Buhangin.jpg/800px-Kwebang_Lampas_Puting_Buhangin.jpg" alt="Kwebang Lampas cave at Puting Buhangin Padre Burgos Quezon" loading="lazy"><figcaption>Kwebang Lampas at the end of Puting Buhangin. The short swim into the cave is the kwento of the day when the tide is right. Photo via <a href="https://commons.wikimedia.org/wiki/File:Kwebang_Lampas_Puting_Buhangin.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Mt Daraitan + Tinipak (batch9 / Detourista version)
    // ----------------------------------------------------------------------
    'mt-daraitan-tinipak-river-tanay-day-hike' => [
        [
            'anchor' => 'The summit is a small rocky platform with the wide view of the Sierra Madre to the east and the Tinipak River valley below.',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/0/05/Mt._Daraitan_Summit.jpg/800px-Mt._Daraitan_Summit.jpg" alt="Mt Daraitan summit platform with Sierra Madre view Tanay Rizal" loading="lazy"><figcaption>The Mt Daraitan summit platform at 739 meters. The Sierra Madre opens to the east and the Tinipak River valley sits below. Photo via <a href="https://commons.wikimedia.org/wiki/File:Mt._Daraitan_Summit.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'After the climb, the standard finish is the swim at the Tinipak River. The river runs cold and clear',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/3/3d/Tinipak_River_Tanay.jpg/800px-Tinipak_River_Tanay.jpg" alt="Tinipak River white marble boulders and clear pool Tanay Rizal" loading="lazy"><figcaption>The Tinipak River below Mt Daraitan. White marble boulders, cold pools, and the small cave the guides include in the tour. Photo via <a href="https://commons.wikimedia.org/wiki/File:Tinipak_River_Tanay.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Masungi Georeserve Discovery Trail
    // ----------------------------------------------------------------------
    'masungi-georeserve-discovery-trail-half-day' => [
        [
            'anchor' => 'Sapot is the spiderweb-shaped rope deck strung between two limestone outcrops.',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/tanay-masungi-georeserve.jpg" alt="Masungi Georeserve limestone karst trail in Baras Rizal" loading="lazy"><figcaption>The Masungi limestone karst from the Discovery Trail. The structured boardwalks and rope bridges thread between outcrops on the Sierra Madre foothills.</figcaption></figure>',
        ],
        [
            'anchor' => 'Duyan is the long rope hammock strung between two outcrops near the end of the trail.',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/aa/Masungi_Georeserve_Duyan.jpg/800px-Masungi_Georeserve_Duyan.jpg" alt="Duyan rope hammock viewing platform at Masungi Georeserve" loading="lazy"><figcaption>Duyan, the long rope hammock near the end of the loop. Visitors take turns for the photo and the guides manage the queue. Photo via <a href="https://commons.wikimedia.org/wiki/File:Masungi_Georeserve_Duyan.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Capones Island Pundaquit
    // ----------------------------------------------------------------------
    'capones-island-pundaquit-zambales-half-day' => [
        [
            'anchor' => 'The Capones Island Lighthouse sits on the higher rocky tip of the island',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/97/Capones_Island_Lighthouse.jpg/800px-Capones_Island_Lighthouse.jpg" alt="Capones Island Lighthouse Spanish era tower in Zambales" loading="lazy"><figcaption>The Capones Island Lighthouse from 1890 on the rocky tip. A 20 to 30 minute stone path climb earns the wide bay view from the platform. Photo via <a href="https://commons.wikimedia.org/wiki/File:Capones_Island_Lighthouse.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'Camara is the smaller island next to Capones, with a short white-sand beach',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/subic-anawangin-and-nagsasa-coves-from-pundaquit.jpg" alt="Pundaquit boat coves with view toward Capones and Anawangin Zambales" loading="lazy"><figcaption>The Pundaquit boat coves seen from the mainland. The standard hop pairs Capones and Camara with a stop at Anawangin further down the coast.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Talicud Island Davao
    // ----------------------------------------------------------------------
    'talicud-island-davao-quieter-samal-side' => [
        [
            'anchor' => 'Babusanta is the longer of the two main beaches, with a curving stretch of white sand',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/4f/Babusanta_Beach_Talicud_Island.jpg/800px-Babusanta_Beach_Talicud_Island.jpg" alt="Babusanta Beach on the western coast of Talicud Island Davao" loading="lazy"><figcaption>Babusanta Beach on the western side of Talicud. Shallow water, fine sand, and the sunset view across the gulf to the Davao mainland. Photo via <a href="https://commons.wikimedia.org/wiki/File:Babusanta_Beach_Talicud_Island.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'Isla Reta is the more developed beach on the northwestern side, with day-use facilities',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/samal-island-3.jpg" alt="Samal and Talicud island gulf coastline Davao" loading="lazy"><figcaption>The Samal-Talicud gulf coastline. Isla Reta is the busier day-use stop, but Babusanta and Dayang on the southern coast run noticeably quieter.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Mati Dahican surf
    // ----------------------------------------------------------------------
    'mati-dahican-surf-weekend-davao-oriental' => [
        [
            'anchor' => 'Dahican is around seven kilometers of curving cream sand backed by coconut trees and low grass.',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/b/b3/Dahican_Beach_Mati.jpg/800px-Dahican_Beach_Mati.jpg" alt="Dahican Beach seven kilometer Pacific coast in Mati Davao Oriental" loading="lazy"><figcaption>Dahican Beach in Mati, around seven kilometers of cream sand facing the Pacific. The bottom is clean sand, no reef near the shore. Photo via <a href="https://commons.wikimedia.org/wiki/File:Dahican_Beach_Mati.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'Dahican is the country\'s best skimboard beach. The shore break runs long and shallow',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/7/76/Skimboarding_at_Dahican.jpg/800px-Skimboarding_at_Dahican.jpg" alt="Skimboarder riding the shore break at Dahican Beach Mati" loading="lazy"><figcaption>Skimboarding at Dahican. The shore break runs long and the smooth sand bottom lets riders carry the line on the back of each set. Photo via <a href="https://commons.wikimedia.org/wiki/File:Skimboarding_at_Dahican.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'Dahican is one of the most important sea turtle nesting beaches in Mindanao.',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/1b/Sea_turtle_hatchling_Philippines.jpg/800px-Sea_turtle_hatchling_Philippines.jpg" alt="Sea turtle hatchling release at a Philippine beach nesting site" loading="lazy"><figcaption>Sea turtle hatchlings released at a Philippine nesting beach. The Amihan sa Dahican volunteers run the nest patrol and the small visitor center funds the program. Photo via <a href="https://commons.wikimedia.org/wiki/File:Sea_turtle_hatchling_Philippines.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Cuyo Palawan slow trip
    // ----------------------------------------------------------------------
    'cuyo-palawan-slow-island-trip-three-day' => [
        [
            'anchor' => 'The fort, built in 1680, is one of the oldest Spanish-era stone forts in the country and is still mostly intact.',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/86/Cuyo_Fort.jpg/800px-Cuyo_Fort.jpg" alt="Cuyo Fort 1680 Spanish era stone walls Palawan" loading="lazy"><figcaption>The Cuyo Fort, built in 1680. The walls enclose the church and the old convent and remain one of the most intact Spanish-era forts in the country. Photo via <a href="https://commons.wikimedia.org/wiki/File:Cuyo_Fort.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'Capusan Beach on the western side of Cuyo town is a long stretch of white sand facing the open sea.',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/5d/Capusan_Beach_Cuyo.jpg/800px-Capusan_Beach_Cuyo.jpg" alt="Capusan Beach long white sand stretch on Cuyo Island Palawan" loading="lazy"><figcaption>Capusan Beach on the western side of Cuyo town. Mostly empty even on weekends, with the open Sulu Sea sunset as the consistent finale. Photo via <a href="https://commons.wikimedia.org/wiki/File:Capusan_Beach_Cuyo.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Mt Pinatubo Capas Tarlac day hike (batch10 / Lakwatsero version)
    // ----------------------------------------------------------------------
    'mt-pinatubo-crater-day-hike-capas-tarlac' => [
        [
            'anchor' => 'The route follows the dried lahar bed of the Sacobia River, which is itself worth the trip.',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/47/Sacobia_River_lahar.jpg/800px-Sacobia_River_lahar.jpg" alt="Sacobia River lahar bed and 4x4 route Capas Tarlac" loading="lazy"><figcaption>The dried Sacobia River lahar bed on the 4x4 route. Pink and grey ash canyons rise on both sides and the line through the streams changes after every storm. Photo via <a href="https://commons.wikimedia.org/wiki/File:Sacobia_River_lahar.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'The rim opens to the lake without warning. The water is a sulfur green with a deeper blue at the center',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/tarlac-1.jpg" alt="Tarlac province landscape near the Pinatubo jump-off in Capas" loading="lazy"><figcaption>The Tarlac countryside near the Capas jump-off. Most travelers stage the night before in Angeles or Clark and leave by 4 a.m. for the trail call.</figcaption></figure>',
        ],
    ],

    // ----------------------------------------------------------------------
    // Mt Batulao first climb
    // ----------------------------------------------------------------------
    'mt-batulao-nasugbu-day-hike-first-climbs' => [
        [
            'anchor' => 'The trail climbs gently for the first 45 minutes, then opens to the rolling ridge that gave Batulao its name.',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c5/Mt_Batulao_Ridge.jpg/800px-Mt_Batulao_Ridge.jpg" alt="Mt Batulao rolling ridge with eleven small peaks Nasugbu Batangas" loading="lazy"><figcaption>The Mt Batulao rolling ridge in Nasugbu. The eleven small peaks open up early and the path is exposed once you leave the first forest section. Photo via <a href="https://commons.wikimedia.org/wiki/File:Mt_Batulao_Ridge.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => 'The final summit push is the steepest part of the climb. Two short scrambles, one with a knotted rope',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/2f/Mt_Batulao_Summit.jpg/800px-Mt_Batulao_Summit.jpg" alt="Mt Batulao summit cross with view to Taal Lake and Nasugbu coast" loading="lazy"><figcaption>The Mt Batulao summit cross. On clear mornings the view runs from Taal Lake to the Nasugbu coast on the South China Sea side. Photo via <a href="https://commons.wikimedia.org/wiki/File:Mt_Batulao_Summit.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

];
