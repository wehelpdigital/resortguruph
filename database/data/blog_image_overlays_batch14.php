<?php

/**
 * Image overlays for blog posts in batch 14 (food trails, heritage routes,
 * festival deep-dives, and indigenous community visits across the Philippines).
 *
 * Each entry keys off the blog post slug. Each anchor must appear EXACTLY ONCE
 * inside that post's content_html; the figure HTML is inserted immediately
 * after the anchor block. Image srcs do not duplicate across this batch.
 */

return [

    // ------------------------------------------------------------------
    // 1. SISIG ORIGIN TRAIL, ANGELES PAMPANGA
    // ------------------------------------------------------------------
    'sisig-origin-trail-angeles-pampanga' => [
        [
            'anchor' => '<p>Aling Lucing Sisig still operates along the railroad tracks in Angeles, the same area where Lucia Cunanan is said to have first served the sizzling version. The plates are unpretentious, the chopping board is well-used, and the sisig itself is closer to the original than the mayo-and-egg versions you see in Manila chains. Order the regular sisig, a side of garlic rice, and a bottle of San Mig Light. The meat has texture, the calamansi sharpens it, and the sizzling plate keeps the bottom crisp.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/angeles-sisig.jpg" alt="Sizzling Kapampangan sisig plate from Angeles" loading="lazy"><figcaption>Sisig on a sizzling plate at Aling Lucing along the Angeles railroad tracks, the original sizzling version Lucia Cunanan is credited with in the 1970s.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Drive 20 minutes south to San Fernando city for lunch or merienda at Everybody\'s Cafe, one of the oldest Kapampangan restaurants still operating since 1946. Their sisig is the bigger menu draw plus betute (stuffed frog), camaru (crickets), and morcon. The interior is old-school, the plating is clean, and the kitchen still uses the family recipes.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/pampanga-province-sisig.jpg" alt="Kapampangan sisig from Everybody Cafe in San Fernando" loading="lazy"><figcaption>A Kapampangan sisig plate from the Everybody\'s Cafe kitchen in San Fernando, where the family recipes have run since 1946.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Buses from Cubao to Dau terminal in Mabalacat run every 15 minutes. Around two hours with light traffic. From Dau take a jeepney or tricycle to Crossing, Angeles. If you drive, NLEX to Dau exit then McArthur Highway south. Parking is easier on weekday mornings.</p>',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/angeles-clark-freeport-zone-hann-casino-marriott-quest.jpg" alt="Clark and Angeles area in Pampanga" loading="lazy"><figcaption>The Angeles-Clark corridor, the easy NLEX off-ramp for Pampanga food trips. The Dau terminal is the bus drop-off; Crossing is a short tricycle ride further in.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 2. KAPAMPANGAN KITCHEN ROUTE
    // ------------------------------------------------------------------
    'kapampangan-kitchen-route-pampanga-food-day' => [
        [
            'anchor' => '<p>Susie\'s Cuisine in Angeles is the merienda landmark of Pampanga and most travelers stop here for pancit luglug. For breakfast, order tibok-tibok, a Kapampangan version of maja blanca made with carabao milk and topped with latik. Pair it with sikwate (thick hot chocolate) and a slice of suman. The shop opens early; arrive by 8 AM to beat the morning bus crowd.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/angeles-bulalo.jpg" alt="Kapampangan breakfast plate at a Pampanga merienda house" loading="lazy"><figcaption>A Kapampangan morning plate in Angeles, the kind of slow-cooked merienda Susie\'s Cuisine has run on for decades. Pair with sikwate for the full ritual.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Drive 20 minutes south to Everybody\'s Cafe in San Fernando for the full Kapampangan lunch experience. Order morcon (the Kapampangan version uses pork loin rolled around ham, sausage, and pickles, then braised in tomato sauce), kare-kare with bagoong, and either betute (stuffed frog) or camaru (crickets) if you want the exotic side of the menu. Their estofado and adobo sa puti also represent the slow-cooked Kapampangan range. Cap with leche flan; theirs has the right wobble.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/pampanga-province-tocino.jpg" alt="Kapampangan tocino and assorted plates" loading="lazy"><figcaption>The Kapampangan plate spread that defines a long Everybody\'s Cafe lunch in San Fernando, with tocino, morcon, and kare-kare in the standard order.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Apag Marangle (Kapampangan for farmer\'s table) is set in a farm with bamboo huts and serves merienda plates around 3 to 5 PM. Order biringhe (Kapampangan paella), bringhe (the yellow version with chicken and pork), and halo-halo with carabao milk ice cream. The setting is calm and the plates come slow; an afternoon here is half rest, half eating.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/pampanga-province-duman.jpg" alt="Duman, a Kapampangan young rice preparation" loading="lazy"><figcaption>Duman, the young pinipig rice preparation Mexico, Pampanga is known for, often served alongside merienda plates at Apag Marangle.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Loop back to Angeles for sisig at Aling Lucing on the railroad tracks. By dinner the chopping is rhythmic and the sizzling plates come fast. Pair with kalderetang kambing if the night is cool. End the route here.</p>',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/angeles-2.jpg" alt="Angeles, Pampanga evening street scene" loading="lazy"><figcaption>The Angeles dinner loop back to Aling Lucing, where the chopping board rhythm and sizzling plates take over once the sun drops.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 3. BETIS WOODCARVING (Guagua, Pampanga)
    // ------------------------------------------------------------------
    'betis-woodcarving-heritage-guagua-pampanga' => [
        [
            'anchor' => '<p>The Betis Church was built in 1660 and the present structure was rebuilt after a fire in the late 1700s. The exterior is stone and the interior is the surprise: every inch of the ceiling is painted with biblical scenes, the apse is gilded, and the side altars carry retablos carved by Betis masters. Local guides call it the Sistine Chapel of the Philippines. The comparison is generous but the painted ceiling does hold up to a long look.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/3/3a/Saint_James_the_Apostle_Parish_Church_Betis_Guagua_Pampanga.jpg/800px-Saint_James_the_Apostle_Parish_Church_Betis_Guagua_Pampanga.jpg" alt="Betis Church, St. James the Apostle Parish in Guagua, Pampanga" loading="lazy"><figcaption>The Betis Church (St. James the Apostle Parish) in Guagua. The stone exterior hides the painted-ceiling interior that locals call the Sistine of the Philippines. Photo via <a href="https://commons.wikimedia.org/wiki/File:Saint_James_the_Apostle_Parish_Church_Betis_Guagua_Pampanga.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>A few names to ask for: Castor Lim and his apprentices; Willy Layug, a well-known image-maker whose workshop has supplied parishes across the country. Smaller carvers along Sto. Cristo Street produce the supply chain that finishes furniture for Manila buyers.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/7/7e/Betis_Wood_Carvings.jpg/800px-Betis_Wood_Carvings.jpg" alt="Betis hand-carved hardwood furniture and santos" loading="lazy"><figcaption>Hand-carved hardwood from the Betis workshops along Sto. Cristo Street, where the family chisels still finish santos and furniture for parishes and Manila buyers. Photo via <a href="https://commons.wikimedia.org/wiki/File:Betis_Wood_Carvings.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Bacolor is 15 minutes away and the town buried halfway by Mt. Pinatubo lahar. The Bacolor Church still has its bell tower poking through the lahar-raised plaza, a strange and quiet sight. The Casa de Don Cesar Suing in Bacolor is one of the surviving heritage houses if you can arrange a visit.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/82/San_Guillermo_Parish_Church_Bacolor_Pampanga.jpg/800px-San_Guillermo_Parish_Church_Bacolor_Pampanga.jpg" alt="Bacolor Church partially buried by Pinatubo lahar" loading="lazy"><figcaption>San Guillermo Parish in Bacolor, the church half-buried by the 1991 Pinatubo lahar. The lower half of the original facade still sits below the raised plaza ground. Photo via <a href="https://commons.wikimedia.org/wiki/File:San_Guillermo_Parish_Church_Bacolor_Pampanga.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 4. ENSAYMADA AND PANCIT MARILAO ROUTE (Bulacan)
    // ------------------------------------------------------------------
    'ensaymada-pancit-marilao-route-bulacan' => [
        [
            'anchor' => '<p>Eurobake on Estrella Street, Malolos is one of the bakeries locals will name first when you ask about ensaymada. The version here is the traditional Spanish-Filipino kind: brioche-style dough, butter, sugar, and a layer of grated quesong puti. Some lines top with cheddar; the classic version with kesong puti is the order to get. The bakery opens early and the morning batch sells through by mid-morning on weekends.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/bulacan-province-puto-and-kutsinta.jpg" alt="Bulacan baked snacks including ensaymada and kutsinta" loading="lazy"><figcaption>Bulacan baked snacks on a Malolos morning, the kind that fills the case at Eurobake on Estrella Street before the weekend rush.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Walk five minutes from the bakery row to Barasoain Church, the site of the First Philippine Republic and one of the most historically loaded buildings in Bulacan. Free entry. The small museum behind the church explains the 1898 Malolos Congress and the drafting of the Malolos Constitution. A 45-minute stop is enough.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/bulacan-province-barasoain-church-in-malolos.jpg" alt="Barasoain Church in Malolos, Bulacan" loading="lazy"><figcaption>Barasoain Church in Malolos, the parish where the 1898 Malolos Congress convened and the First Philippine Republic was drafted.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Drive 25 minutes south from Malolos to Marilao for the pancit. Pancit Marilao is a dry rice-noodle dish, originally made with bihon, dressed with kalabasa sauce, topped with chicharon and bagnet, and seasoned with calamansi. The original carinderia stalls are scattered along the Marilao town plaza area. Mang Lino\'s and the unnamed stalls near the Marilao Church both serve good versions. Order one bilao to share.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/pandi-pancit-habhab.jpg" alt="Bulacan-style pancit on a bilao" loading="lazy"><figcaption>A Bulacan pancit on a bilao, the same family of dry rice-noodle plates the Marilao carinderias serve near the town plaza.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Bulacan longganisa is sweeter and shorter than Vigan or Lucban; the closest carinderia to the Marilao church will have it on the breakfast plate. Buy a half kilo to bring home from any of the meat shops along the highway between Marilao and Meycauayan.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/bulacan-province-inutak.jpg" alt="Bulacan inutak, a baked kakanin specialty" loading="lazy"><figcaption>Inutak, the Bulacan baked kakanin you find on the same merienda tables as the local longganisa across the Marilao-Meycauayan stretch.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 5. KAKANIN ROUTE LUCBAN (Quezon)
    // ------------------------------------------------------------------
    'kakanin-route-lucban-quezon' => [
        [
            'anchor' => '<p>Start at the Lucban Public Market on a Sunday or any market day. The kakanin row at the back of the wet market sells suman sa lihiya wrapped in palm fronds, puto seko, broas, and uraro. Ask the vendor to slice a suman for a quick taste; pair it with the brown sugar muscovado syrup the vendors sell beside the rice cakes.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/quezon-province-broas.jpg" alt="Lucban broas and assorted Quezon kakanin" loading="lazy"><figcaption>The Lucban kakanin counter with broas, suman, and uraro that fills the wet-market back row on a Sunday morning.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Between kakanin stops, take a habhab break. Pancit habhab is miki noodles stir-fried with pork, liver, and vegetables, served on a piece of banana leaf, and eaten without utensils by tilting the leaf into your mouth. Old Center Panciteria along the plaza is the classic stop. One serving is small; order two and share.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/sariaya-pancit-habhab.jpg" alt="Pancit habhab on a banana leaf" loading="lazy"><figcaption>Pancit habhab served on a piece of banana leaf, the Quezon noodle plate eaten without utensils by tilting the leaf straight into your mouth.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>If you are not Pahiyas-bound, walk or tricycle up to the Kamay Ni Hesus shrine on the slopes of Mt. Banahaw for a non-food stop. The Stations of the Cross climb 305 steps to a Risen Christ statue at the top. The view of Lucban and the surrounding rice fields is calm and clean. Half an hour to climb and rest.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/quezon-province-pahiyas-festival-in-lucban-may-15.jpg" alt="Lucban town view in Quezon Province" loading="lazy"><figcaption>The Lucban town view from the Banahaw foothills, the calm half-day backdrop for the kakanin walk through the public market.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The Lucban Church, built in stone in 1738, is the parish at the center of every Pahiyas procession. The plaza outside is where Pahiyas houses set up the kiping-decorated facades each May. On a non-Pahiyas day the plaza is calm and good for kakanin-eating on a bench.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c5/Lucban_Church_Quezon.jpg/800px-Lucban_Church_Quezon.jpg" alt="Lucban Church, San Luis Obispo de Tolosa Parish" loading="lazy"><figcaption>The Lucban Church (San Luis Obispo de Tolosa), finished in stone in 1738. The plaza in front becomes the Pahiyas stage every May 15. Photo via <a href="https://commons.wikimedia.org/wiki/File:Lucban_Church_Quezon.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 6. LUCBAN LONGGANISA DEEP-DIVE (Quezon)
    // ------------------------------------------------------------------
    'lucban-longganisa-deep-dive-quezon' => [
        [
            'anchor' => '<p>The recipe leans on garlic, oregano, paprika, and vinegar. The pork is ground coarsely, the casing is short, and the links are tied close. Most makers ferment the mix for half a day before stuffing, which is what gives the sausage the slight sour bite when you fry it. The Lucban version is heavier on oregano than any other regional longganisa, a leftover from Spanish-friar cookery in the area.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/quezon-province-lucban-longganisa.jpg" alt="Lucban longganisa, short oregano-heavy pork sausage links" loading="lazy"><figcaption>Lucban longganisa, the short oregano-and-vinegar pork links sold in cellophane bundles at the public market.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The public market has a longganisa row with five to seven small stalls. The smell of garlic and vinegar gives the location away before you read the signs. Each stall has slight variations: some lean sweeter, some lean saltier, some use more oregano. Buy a quarter kilo from two different stalls to compare. Tell the vendor you want them tindahan-fresh, not freezer-stored, and ask for the date they were stuffed.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/lucena-broas.jpg" alt="Lucena and Quezon merienda bakery selection" loading="lazy"><figcaption>Quezon merienda spread of broas and kakanin that travelers take home in the same pasalubong bag as the Lucban longganisa.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The standard at-home method is to simmer the links in water with a splash of vinegar until the water cooks off, then let the rendered fat fry the casing. Serve with garlic rice, sunny-side egg, and a dipping bowl of spicy vinegar with chopped garlic and chili. Three links per person is the breakfast portion.</p>',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/lucena-1.jpg" alt="Lucena, the Quezon Province gateway to Lucban" loading="lazy"><figcaption>The Lucena gateway, the standard bus drop-off before the one-hour mountain road up to Lucban for the longganisa morning.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 7. LAING TRAIL CAMALIG (Albay)
    // ------------------------------------------------------------------
    'laing-trail-camalig-albay' => [
        [
            'anchor' => '<p>The market is small and the laing row sits near the cooked-food stalls. Most stalls sell laing by the kilo in plastic packs, room-temperature for take-out and reheating. Ask for a tablespoon taste before you commit; the spice levels vary by maker. Some lean creamier, some lean dryer. The standard order is a half kilo packed for the road.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/albay-legazpi-laing.jpg" alt="Bicolano laing, dried taro leaves in coconut cream" loading="lazy"><figcaption>Laing from a Camalig kitchen, the dried taro leaves braised in coconut cream with chili and small smoky bits of pork that Albay makers sell by the kilo.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Drive back to Legazpi for 1st Colonial Grill, the restaurant that built a calm reputation on Bicol comfort food. Order the laing plate as a side, the pinangat (taro leaves wrapped tight around fish or meat), and the Bicol Express. Their sili ice cream comes after; chili-laced cream that is more interesting than it sounds. Two people can split four plates here for a full Bicol survey.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/albay-legazpi-pinangat.jpg" alt="Pinangat, taro leaves wrapped tight around fish in coconut cream" loading="lazy"><figcaption>Pinangat, the Camalig cousin to laing: taro leaves wrapped tight around fish or pork and simmered in coconut cream.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The Cagsawa Ruins are a 10-minute drive from Camalig and the standard Mayon photo stop. The half-buried church belfry is the foreground for the Mayon cone. Pair with a laing-stuffed pandesal from any of the kiosks at the parking area for a road-style merienda.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/albay-legazpi-cagsawa-ruins.jpg" alt="Cagsawa Ruins with Mt. Mayon behind" loading="lazy"><figcaption>The Cagsawa Ruins belfry with the Mt. Mayon cone behind, the standard pause between Camalig laing stops and the drive back to Legazpi.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Vacuum-packed laing keeps frozen for a month and chilled for four days. Camalig makers will pack two-kilo blocks for travelers heading back to Manila. Add a bag of pili nuts (the Bicol regional nut) from any roadside stall and you have a full Bicol pasalubong bag.</p>',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/albay-legazpi-2.jpg" alt="Albay landscape with Mt. Mayon" loading="lazy"><figcaption>The Albay backdrop on the drive between Camalig and Legazpi, the Mayon cone dominating the windshield once Sumlang Lake is behind you.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 8. BICOL EXPRESS TRAIL (Naga + Legazpi)
    // ------------------------------------------------------------------
    'bicol-express-trail-naga-legazpi' => [
        [
            'anchor' => '<p>Bigg\'s is the Naga-born diner chain that put Bicol Express on the wider Bicolano menu. Their version is balanced for travelers: hot but not punishing, coconut cream rich but not heavy. The branch on Magsaysay Avenue is the original. Order Bicol Express with rice, a side of laing, and their crispy pata. Family-friendly setting, fast service.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/naga-camarines-sur-bicol-express.jpg" alt="Bicol Express, chili and coconut cream pork stew" loading="lazy"><figcaption>Bicol Express at a Naga sit-down, the chili-and-coconut-cream pork stew named after the old Manila-Legazpi train.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Between the food stops, walk over to the Naga Cathedral and the Basilica Minore de Nuestra Senora de Penafrancia. The September Penafrancia Festival is the biggest Marian devotion in Bicol; outside September, the basilica is calm and the riverwalk from the basilica to the cathedral is a good 30-minute stretch.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/naga-camarines-sur-penafrancia-basilica.jpg" alt="Basilica Minore de Nuestra Senora de Penafrancia in Naga" loading="lazy"><figcaption>The Penafrancia Basilica in Naga, the seat of the September Marian devotion. Outside the festival month the basilica is calm and the riverwalk to the cathedral is uncrowded.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>1st Colonial Grill in Legazpi serves the cleanest restaurant Bicol Express in the city. The version is creamier and the pork is cut smaller, plated for travelers but flavored by Albay cooks. Order it alongside the chili ice cream for a full spice journey. Their pasta with laing is also worth ordering.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/albay-legazpi-bicol-express.jpg" alt="Legazpi-style Bicol Express, creamier and pork-belly heavy" loading="lazy"><figcaption>Legazpi-style Bicol Express plated at 1st Colonial Grill: creamier, pork cut smaller, flavored by Albay cooks for the city dining crowd.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Pre-cooked Bicol Express does not pack well; it does not keep room-temperature for the long bus ride home. Better pasalubong from Bicol: pili candies, pili tart, laing in vacuum packs, and sili ice cream from 1st Colonial Grill in a small chiller bag.</p>',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/albay-legazpi-sili-ice-cream.jpg" alt="Sili ice cream, the Bicol chili dessert" loading="lazy"><figcaption>Sili ice cream from 1st Colonial Grill, the Bicol chili dessert that closes out the two-city Bicol Express trail.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 9. KINUNOT DONSOL (Sorsogon)
    // ------------------------------------------------------------------
    'kinunot-donsol-sorsogon-fish-dish' => [
        [
            'anchor' => '<p>Donsol is a small coastal town in Sorsogon, around an hour from Legazpi by van. The town built its tourism on butanding (whale shark) interaction from December to May, peaking in February to April. Outside the whale shark season the town is quiet, the carinderias still cook the Bicol-Sorsogon plates, and the food is the reason to come if you are not chasing the fish.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/sorsogon-2.jpg" alt="Sorsogon coastal scene near Donsol" loading="lazy"><figcaption>The Sorsogon coast near Donsol, the calm side of the province outside whale shark season when the kinunot kitchens come back into focus.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The carinderia row along Vitton in Donsol serves kinunot, ginataang isda, and adobong pusit. The setting is by the beach, the breeze is real, and the kitchen is closer to home cooking than restaurant cooking. Pair with sili leaves saluyot for a green side.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/sorsogon-sinanglay-na-isda.jpg" alt="Sorsogon coconut-cream fish plate" loading="lazy"><figcaption>A Sorsogon coconut-cream fish plate from the Vitton beachfront row, the same kitchen style the Donsol carinderias use for kinunot.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Pair the kinunot with a few Sorsogon-side specialties: tinapayan (fermented anchovy spread), ginataang santol (santol fruit in coconut cream), and pinangat na isda. The Bicol-Sorsogon plate spread is wider than Bicol Express and laing alone.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/sorsogon-laing-and-pinangat.jpg" alt="Sorsogon laing and pinangat plates" loading="lazy"><figcaption>Sorsogon laing and pinangat side plates, the typical green-and-coconut-cream pairing that arrives with a kinunot lunch in Donsol.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 10. SILI ICE CREAM (Legazpi)
    // ------------------------------------------------------------------
    'sili-ice-cream-legazpi-bicol-dessert' => [
        [
            'anchor' => '<p>1st Colonial Grill on Daraga or at SM Legazpi serves the original sili ice cream. The flavor menu rotates but the regular lineup includes sili (chili), pili nut, malunggay, tinutong (toasted rice), and ube. Order the four-flavor sampler and pair with their pili tart or with a clean glass of water.</p>',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/0/0d/Sili_Ice_Cream.jpg/800px-Sili_Ice_Cream.jpg" alt="Sili ice cream, the chili dessert from 1st Colonial Grill in Legazpi" loading="lazy"><figcaption>Sili ice cream, the chili dessert 1st Colonial Grill in Legazpi turned into a Bicol regional icon. The chili glow climbs slow on the second swallow. Photo via <a href="https://commons.wikimedia.org/wiki/File:Sili_Ice_Cream.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The pili nut is the Bicol regional nut, fattier and more buttery than cashew. Pili ice cream uses pili paste and chopped roasted pili. The flavor is closer to brown butter pecan than to peanut. Worth ordering even if you skip the sili.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/7/7c/Pili_Nuts.jpg/800px-Pili_Nuts.jpg" alt="Pili nuts, the Bicol regional nut used in pili ice cream" loading="lazy"><figcaption>Pili nuts from Albay, the fattier-than-cashew Bicol regional nut that becomes the pili-paste and chopped-pili scoop. Photo via <a href="https://commons.wikimedia.org/wiki/File:Pili_Nuts.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Pair the ice cream stop with a box of pili tart (J Emmanuel or any Albay bakery) and a small jar of pili caramel. The Bicol sweet bag is heavy on pili and not much else; that is a feature, not a limitation.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/albay-legazpi-lignon-hill.jpg" alt="Lignon Hill in Legazpi with Mayon backdrop" loading="lazy"><figcaption>Lignon Hill above Legazpi, the calm afternoon stop after the ice cream sampler at 1st Colonial Grill in SM or Daraga.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 11. LA PAZ BATCHOY THREE BOWLS (Iloilo)
    // ------------------------------------------------------------------
    'la-paz-batchoy-iloilo-three-bowls-origin' => [
        [
            'anchor' => '<p>Netong\'s sits inside La Paz Market itself, on the second floor near the dry goods. The shop is the most market-style of the three: small, basic seating, no aircon, real wet-market sounds. The batchoy here is heavier on innards and the stock is darker. Order the regular batchoy and a side of bottled San Mig. Open from breakfast onward.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/iloilo-city-la-paz-batchoy.jpg" alt="La Paz batchoy, Iloilo pork-bone noodle soup" loading="lazy"><figcaption>La Paz batchoy in its hometown bowl: miki noodles in deep pork-bone stock with chicharon, pork liver, kidney, intestines, and a sprinkle of fried garlic on top.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Each shop has a siopao and pancit menu. Pancit Molo is the Iloilo sibling to batchoy (wonton soup, not stir-fried noodles) and worth tasting at one of the shops or at a separate Molo restaurant. Pair the batchoy walk with a Molo lunch for the full Iloilo noodle survey.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/iloilo-city-pancit-molo.jpg" alt="Pancit Molo, the Iloilo wonton soup" loading="lazy"><figcaption>Pancit Molo from an Iloilo kitchen, the wonton-soup sibling to batchoy that closes out the city\'s noodle-survey day.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Walk through La Paz Market between the bowls. The wet section sells fresh seafood from the Iloilo coast; the dry section has muscovado sugar, mango bars, biscocho, and butterscotch from local bakeries. Buy biscocho from any of the stalls; it is the Iloilo bread snack that pairs with morning coffee.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/iloilo-city-fresh-oysters-and-seafood.jpg" alt="La Paz Market Iloilo seafood section" loading="lazy"><figcaption>The La Paz Market wet section in Iloilo, where the seafood and the dry-goods row sit between the three batchoy stalls on a slow morning walk.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>La Paz also holds the La Paz Church, a stone parish from the Spanish era, and a small heritage row along Huervana. After three bowls of batchoy, a slow walk through the streets is the natural way to settle the stomach.</p>',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/iloilo-city-jaro-cathedral.jpg" alt="Jaro Cathedral in Iloilo City heritage district" loading="lazy"><figcaption>Jaro Cathedral, the Iloilo heritage stop a few minutes from La Paz that travelers often loop in after the three-bowl batchoy walk.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 12. PANCIT MOLO ORIGIN (Iloilo)
    // ------------------------------------------------------------------
    'pancit-molo-iloilo-origin-wonton-soup' => [
        [
            'anchor' => '<p>Molo Mansion is an early-20th-century house painted bright pink and now home to a restored cafe and souvenir space. Free entry to the ground floor; the upstairs runs as a museum-style display. The mansion sits on the same plaza as Molo Church and serves as a calm starting point for the Molo loop.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c1/Molo_Mansion.jpg/800px-Molo_Mansion.jpg" alt="Molo Mansion in Iloilo City" loading="lazy"><figcaption>Molo Mansion, the pink early-20th-century house on the Molo plaza that opens the pancit Molo walking loop. Photo via <a href="https://commons.wikimedia.org/wiki/File:Molo_Mansion.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Molo Church is the Gothic-Renaissance parish with twin spires that locals call the "feminist church" because the saints lining the nave are all women. The 19th-century interior is white, the stained glass is original, and the church is one of the calmer photo stops in Iloilo. Free entry; modest dress.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/iloilo-city-molo-church-and-plaza.jpg" alt="Molo Church and Plaza in Iloilo City" loading="lazy"><figcaption>Molo Church, the Gothic-Renaissance parish locals call the feminist church because the saints lining the nave are all women.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Camina Balay nga Bato is a heritage-house cafe in the Arevalo neighborhood next to Molo. The cafe serves pancit Molo, chocolate batirol (the thick Filipino hot chocolate), and Iloilo merienda plates. The house dates to the early 1800s and the back garden has a hand-built choco-making demonstration. Booking ahead is wise.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/iloilo-city-camina-balay-nga-bato.jpg" alt="Camina Balay nga Bato heritage cafe in Arevalo" loading="lazy"><figcaption>Camina Balay nga Bato in Arevalo, the 1800s heritage house that serves pancit Molo and chocolate batirol from its restored back garden.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 13. GUIMARAS MANGO FARM CIRCUIT
    // ------------------------------------------------------------------
    'guimaras-mango-farm-circuit-iloilo' => [
        [
            'anchor' => '<p>The wharf at Jordan is the main entry point. Tricycle drivers cluster at the exit offering hop-on, hop-off circuits for the day. Negotiate a flat rate for a five-stop tour. Most drivers know the mango farms, the trappist monastery, and the beaches by heart.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/guimaras-1.jpg" alt="Guimaras Island, Iloilo strait crossing" loading="lazy"><figcaption>Guimaras Island just off Iloilo City, the 15-minute pump-boat crossing for the mango farm circuit day trip.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The island has several mango farms open to visitors. The Oro Verde Mango Farm offers tours during fruiting season (April to June). The drive winds through orchard rows and the farm staff explain the grafting, the spraying, and the harvesting. Eat mangoes straight from the tree if the season is right.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/guimaras-mango-everything.jpg" alt="Guimaras mangoes, the sweetest carabao mango variety" loading="lazy"><figcaption>Guimaras mangoes, the variety the National Mango Research Center certifies as one of the sweetest in the world. Eat them straight from the tree in fruiting season.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The Trappist Monastery on a hillside in Jordan runs a souvenir shop with mango products produced by the monks: mango jam, mango bars, mango ketchup, mango wine. The monks live a quiet contemplative life and visitors are welcome at the shop and the small chapel.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/guimaras-trappist-monastery.jpg" alt="Trappist Monastery in Jordan, Guimaras" loading="lazy"><figcaption>The Trappist Monastery on a Jordan hillside, where the monks produce mango jam, mango ketchup, mango wine, and the small chapel takes quiet visitors.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>End the day at Alubihod Beach, the white-sand beach 30 minutes from Jordan port. Day-tour fee is small; the sand is fine, the water is calm, and the small island hopping circuit to Turtle Island and SEAFDEC is available. Stay for sunset before the last boat back to Iloilo.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/guimaras-guisi-lighthouse.jpg" alt="Guisi Lighthouse on Guimaras coast" loading="lazy"><figcaption>The Guisi Lighthouse on the Guimaras coast, the sunset addition to a mango farm day before the last boat back to Iloilo from Jordan.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 14. CEBU LECHON (Talisay + Carcar)
    // ------------------------------------------------------------------
    'cebu-lechon-talisay-carcar-trail' => [
        [
            'anchor' => '<p>Three things set Cebu lechon apart. First, the seasoning paste rubbed inside the cavity uses lemongrass (tanglad), green onions, garlic, salt, and pepper. Second, the basting uses coconut water rather than oil, keeping the skin crisp. Third, the cooking is done over a slow charcoal fire with continuous spit-turning by hand for three to four hours.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/cebu-city-lechon.jpg" alt="Cebu lechon, spit-roasted with lemongrass and coconut-water basting" loading="lazy"><figcaption>Cebu lechon, spit-roasted three to four hours over slow charcoal, basted with coconut water and seasoned from the inside with lemongrass and salt.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Zubuchon is the modern brand that turned Cebu lechon into a national name after Anthony Bourdain called it the best pig ever. The Carcar branch is on the southern road. Order a quarter kilo of lechon with rice and pansit. The skin here is consistently crisp and the meat is well-seasoned. A traveler-friendly entry point to the trail.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/lapu-lapu-city-lechon-cebu.jpg" alt="Cebu lechon plated with crispy skin" loading="lazy"><figcaption>A plated Cebu lechon quarter at a southern-Cebu sit-down, the cleaner traveler-friendly version of the same Carcar-Talisay supply chain.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Puso is the woven coconut-leaf rice pouch that pairs with lechon and roast chicken in Cebu. The rice is steamed inside the woven pouch and the leaf gives the rice a faint coconut aroma. Order three to four puso per person for a real lechon meal.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/cebu-city-puso.jpg" alt="Puso, woven coconut-leaf hanging rice from Cebu" loading="lazy"><figcaption>Puso, the woven coconut-leaf rice pouch that hangs in bunches at Cebu lechon stalls. Tear the leaf, pour the rice onto your plate, eat with your hands.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Between lechon stops, walk the Carcar town plaza for the heritage houses, the Carcar Church, and the public market itself. Carcar is one of the heritage town walks in southern Cebu and the lechon trail pairs naturally with the slow walk.</p>',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/cebu-city-3.jpg" alt="Cebu City heritage skyline" loading="lazy"><figcaption>The Cebu City heritage backdrop, the bigger-picture context for the south-trail lechon morning between Talisay and Carcar.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 15. PUSO MAKING (Cebu)
    // ------------------------------------------------------------------
    'puso-making-hanging-rice-cebu' => [
        [
            'anchor' => '<p>The Carcar Public Market has a puso section near the lechon row. The weavers sit on low stools with a pile of coconut leaves, weaving pouches at a rhythm of about 20 per minute. Watch for five minutes and the pattern becomes clear: fold, weave, fold, tie. Ask permission before taking photos; most weavers are friendly and used to travelers.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/41/Puso_hanging_rice_Cebu.jpg/800px-Puso_hanging_rice_Cebu.jpg" alt="Puso, woven coconut-leaf rice pouches hanging at a Cebu market" loading="lazy"><figcaption>Bunches of puso hanging at a Cebu market lechon row, woven by hand from young coconut leaves at around 20 pouches a minute. Photo via <a href="https://commons.wikimedia.org/wiki/File:Puso_hanging_rice_Cebu.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Larsian on Fuente Osmena is the barbecue strip of Cebu City with grilled pork skewers, chicken inasal, and trays of puso hanging from above. The puso here is smaller and the count is by piece. Order three puso per person for a proper barbecue dinner. The cost per puso is so low that travelers often miss the value; in Cebu, puso is the rice and the rice is puso.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/mactan-puso.jpg" alt="Puso served with grilled meats on a Cebu barbecue plate" loading="lazy"><figcaption>Puso on a barbecue plate at a Cebu inasal strip, the side that makes a Larsian dinner feel complete with three per head.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The Sutukil restaurants on Mactan Island (named for sugba, tula, and kilaw) all serve puso. Order grilled fish (sugba), fish soup (tula), and fish ceviche (kilaw) with two puso per person. The Mactan setting near Lapu-Lapu Shrine is a longer trip but a clean three-course Cebuano meal.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/mactan-lapu-lapu-shrine-and-mactan-shrine.jpg" alt="Mactan Lapu-Lapu Shrine, near the Sutukil restaurant strip" loading="lazy"><figcaption>The Lapu-Lapu Shrine on Mactan, the heritage stop next to the Sutukil restaurant strip where grilled fish, soup, and ceviche arrive with two puso per head.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 16. DRIED MANGO TRAIL (Cebu)
    // ------------------------------------------------------------------
    'dried-mango-trail-cebu' => [
        [
            'anchor' => '<p>Profood, makers of the 7D brand, is the largest dried mango producer in the country and the brand most travelers know from airport gift shops. The 7D version is consistent: thin slices, balanced sweetness, clean chew. The brand also makes dried pineapple, dried papaya, and mango juice drinks. Buy at the SM, Ayala, or airport pasalubong shops.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/cebu-city-dried-mangoes.jpg" alt="Cebu dried mangoes, the Philippine pasalubong category" loading="lazy"><figcaption>Cebu dried mangoes from the 7D production line: thin slices of carabao mango, sugared lightly, dried slow, vacuum-packed for the flight home.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Taboan in Cebu City is the pasalubong public market where loose-pack dried mangoes, danggit, dried squid, and mango bars are sold by weight. The dried mango section is in the dry-goods area. Try a slice from two or three stalls before committing to a kilo. The taste varies by maker; some lean tarter, some lean sweeter.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/mactan-dried-mangoes-for-pasalubong.jpg" alt="Loose-pack dried mangoes at a Cebu pasalubong market" loading="lazy"><figcaption>Loose-pack dried mangoes at the Taboan Public Market in Cebu, sold by weight from the dry-goods row where the taste varies by maker.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Mactan-Cebu International Airport has full pasalubong shops post-security. If you forgot to buy in the city, the airport prices are higher but the selection is complete. Allow 30 minutes before boarding for the run.</p>',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/cebu-city-carbon-market.jpg" alt="Carbon Market in Cebu City" loading="lazy"><figcaption>Carbon Market in Cebu City, the older wet-and-dry market across the river from Taboan that travelers sometimes loop into the dried mango run.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 17. BOHOL UBE HALAYA + PEANUT KISSES
    // ------------------------------------------------------------------
    'ube-halaya-peanut-kisses-bohol-sweet-run' => [
        [
            'anchor' => '<p>Bohol Bee Farm in Dauis on Panglao sells the cleanest commercial ube halaya in the province. The farm uses organic purple yam grown on the farm, fresh carabao milk, and slow stirring. The version is dense and not overly sweet. The shop also sells malunggay pesto, honey, and herbal teas. Walk-in customers welcome.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/25/Ube_halaya.jpg/800px-Ube_halaya.jpg" alt="Ube halaya, Philippine purple yam dessert in a jar" loading="lazy"><figcaption>A jar of ube halaya from Bohol, the dense purple yam dessert built from local ube and fresh carabao milk at slow heat. Photo via <a href="https://commons.wikimedia.org/wiki/File:Ube_halaya.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Peanut kisses are bite-size peanut meringues from Dauis, packed in clear plastic bags of 50 to 100 pieces. The original maker, BFAD-licensed, has a small factory in Dauis and the product is sold at every pasalubong shop in Bohol. Crunchy outside, slightly chewy inside, with chopped peanuts in the meringue. The flavor is peanut-forward and the sweetness is balanced.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/panglao-peanut-kisses.jpg" alt="Peanut kisses, Bohol bite-size peanut meringues" loading="lazy"><figcaption>Peanut kisses, the bite-size meringue with chopped peanuts that Dauis on Panglao has built into a Bohol pasalubong standard.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Calamay is the Bohol kakanin made of glutinous rice flour, coconut milk, and brown sugar, cooked down to a thick caramel and poured into half-coconut shells. The result keeps for two weeks at room temperature. Buy two or three from the market for the boat ride home. Eat with a spoon, not by the bite.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/panglao-calamay.jpg" alt="Calamay, Bohol sticky rice caramel in coconut shells" loading="lazy"><figcaption>Calamay in half-coconut shells, the Bohol sticky-rice caramel that keeps two weeks at room temperature and survives the boat ride home.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Pair the sweet trail with a stop at Baclayon Church (oldest stone church in the Philippines, 1727), Loboc Church, and Dauis Church. The heritage churches are calm and the sweet stops sit within driving distance of each.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/panglao-loboc-river-cruise.jpg" alt="Loboc River cruise near Bohol heritage churches" loading="lazy"><figcaption>The Loboc River cruise, the calm Bohol pairing for the heritage church loop after the Tagbilaran and Dauis sweet stops.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 18. DAVAO DURIAN CRAWL
    // ------------------------------------------------------------------
    'davao-durian-crawl-first-timer-guide' => [
        [
            'anchor' => '<p>Magsaysay Park has the durian stall row that defines the Davao tourist experience. Rows of vendors with piles of durian, machetes ready to open the fruit on demand. Tell the vendor your variety preference (Puyat, Arancillo, Native, D24, Monthong) and your budget. They will open it, set you up at a plastic table, and offer a wet wipe after.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/davao-city-durian.jpg" alt="Davao durian opened on a market table at Magsaysay Park" loading="lazy"><figcaption>Davao durian opened by machete at a Magsaysay Park stall. The vendor sets you up at a plastic table and waits to learn which variety you preferred.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Puyat: large fruit, creamy flesh, mild sweetness. Beginner-friendly. Arancillo: smaller, more pungent, deeper flavor. Native: variable, often less consistent, lower price. D24 and Monthong: Thai varieties grown locally, milder than native Philippine cultivars. First-timers should try Puyat before working up to Arancillo.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/glan-sarangani-durian.jpg" alt="Durian varieties from southern Mindanao" loading="lazy"><figcaption>Different durian varieties side by side, the way Davao vendors line them up so first-timers can compare Puyat against Arancillo before committing.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Buy durian candy, durian jam, and durian pastillas for pasalubong. Durian coffee blends are available at the airport. Fresh durian is banned from most hotels and from flights; do not try to carry it.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/davao-city-pomelo.jpg" alt="Davao pomelo, the citrus pasalubong" loading="lazy"><figcaption>Davao pomelo, the citrus pasalubong that travels home easier than fresh durian and pairs naturally with the durian candy bag.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Most Davao hotels prohibit fresh durian in the rooms. Eat it at the park or at the durian-friendly cafes. Some restaurants have "durian smoking areas" for the fruit, which sounds odd until you appreciate the rule.</p>',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/davao-city-2.jpg" alt="Davao City scene, the durian capital" loading="lazy"><figcaption>Davao City, the country\'s durian capital, where the hotel rules about fresh fruit start to make sense by the second day.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 19. DAVAO TUNA MARKET DAWN RUN
    // ------------------------------------------------------------------
    'davao-tuna-market-dawn-run-travelers' => [
        [
            'anchor' => '<p>Toril is the larger fish port in Davao city, about 30 minutes south of the city center. Boats unload between 4 and 6 AM. The catch is sorted on the wharf: tuna (yellowfin and skipjack), tanigue, lapu-lapu, marlin, and the smaller pelagic fish. The auction is fast and the prices move in shorthand.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/general-santos-tuna-panga.jpg" alt="Tuna panga, the grilled jaw collar from southern Mindanao" loading="lazy"><figcaption>Tuna panga (the meaty jaw collar) on a Davao-Gensan grill, cut from the same yellowfin landings that hit Toril Fish Port before sunrise.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Kinilaw is the Mindanao fish ceviche: raw fish (usually tanigue, mackerel, or tuna) cured in vinegar, calamansi, ginger, onion, and chili. The dish is at its sharpest with fish caught hours earlier. Several Davao restaurants pick up fish at the morning market and serve kinilaw at lunch. Lachi\'s Sans Rival, Marina Tuna, and Tiny Kitchen are the calm sit-downs.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/davao-city-kinilaw-na-tuna.jpg" alt="Kinilaw na tuna, Davao fish ceviche" loading="lazy"><figcaption>Kinilaw na tuna in Davao: fresh fish cured in vinegar, calamansi, ginger, onion, and chili, sharpest with fish caught hours before plating.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Tuna belly (the fatty cut) is the prized portion of yellowfin tuna and Davao restaurants serve it grilled or fried. Tuna panga (jaw collar) is the meaty bone-and-flesh cut grilled over coals; eat with your hands. Both are Davao-Gensan classics and worth the dinner stop.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/davao-city-tuna-belly-grilled.jpg" alt="Grilled tuna belly, Davao seafood plate" loading="lazy"><figcaption>Grilled tuna belly at a Davao dinner, the fatty yellowfin cut that defines the city\'s evening seafood plate alongside the panga.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>General Santos (GenSan) is the bigger tuna port in the region, 3 hours from Davao. Davao gets a portion of GenSan supply and GenSan handles the export-grade tuna. The Davao market scene is the smaller, more accessible version of the GenSan dawn fish run.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/glan-sarangani-tuna-sashimi.jpg" alt="Tuna sashimi from southern Mindanao" loading="lazy"><figcaption>Tuna sashimi from the southern Mindanao supply, the export-grade end of the same fishery that lands the Davao morning market.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 20. VIGAN EMPANADA TRAIL
    // ------------------------------------------------------------------
    'vigan-empanada-trail-three-stalls' => [
        [
            'anchor' => '<p>Plaza Burgos is the open plaza in front of Vigan Cathedral and the empanada stalls line the side facing the cathedral. Five to seven stalls operate. Each has a queue at peak hours (5 to 8 PM and 11 AM to 2 PM). Tell the cook your order: regular (with egg), special (with more longganisa), or jumbo (all the above with extra filling). The cook fills, folds, fries, and hands the empanada in a paper bag.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/ilocos-sur-vigan-empanada.jpg" alt="Vigan empanada, the orange-shelled longganisa snack" loading="lazy"><figcaption>Vigan empanada at Plaza Burgos: orange-shelled rice flour pastry stuffed with longganisa, grated papaya, bean sprouts, and a whole cracked egg, fried to order.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Between empanadas, walk one block to a Royal Bibingka shop. Vigan bibingka is denser and yellower than the Manila version; the Royal brand is the easy commercial stop. Pair with a small cup of coffee.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/vigan-vigan-empanada.jpg" alt="Vigan empanada plated with vinegar dip" loading="lazy"><figcaption>A Vigan empanada plated for sit-down eating with the spicy vinegar dip the Plaza Burgos cooks keep on the table.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>If the empanada is your introduction to Vigan longganisa, follow up with a longganisa breakfast plate at any of the Calle Crisologo restaurants. The longganisa here is garlicky and vinegary, shorter than Lucban, and pairs with rice and a fried egg.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/vigan-calle-crisologo.jpg" alt="Calle Crisologo, Vigan cobblestone heritage street" loading="lazy"><figcaption>Calle Crisologo at dusk, the cobblestone heritage street five minutes from the Plaza Burgos empanada stalls. Calesa rides start lining up by 6 PM.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>End the trail with a calesa ride or a slow walk down Calle Crisologo at sunset. The cobblestone street with the heritage houses lights up around 6 PM. The empanada stalls are a five-minute walk from one end of the street.</p>',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/vigan-vigan-cathedral-and-plaza-salcedo.jpg" alt="Vigan Cathedral and Plaza Salcedo" loading="lazy"><figcaption>Vigan Cathedral fronting Plaza Salcedo, the open end of the heritage walk that connects back to Plaza Burgos where the empanada stalls cluster.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 21. BURNAYAN POTTERY VIGAN
    // ------------------------------------------------------------------
    'burnayan-pottery-vigan-heritage-craft' => [
        [
            'anchor' => '<p>RG Jar Factory in Pagburnayan area is the visitor-friendly workshop on the standard heritage circuit. The grounds include the kiln, the clay mixing pit, the wheel area, and a small showroom. Visitors can watch the wheel-throwing demo and try their hand at the wheel for a small fee.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/3/35/Burnay_Vigan.jpg/800px-Burnay_Vigan.jpg" alt="Burnay jars stacked at a Pagburnayan workshop in Vigan" loading="lazy"><figcaption>Burnay jars stacked at a Pagburnayan workshop in Vigan, ready for the rice-husk firing that gives the earthenware its dark sealed finish. Photo via <a href="https://commons.wikimedia.org/wiki/File:Burnay_Vigan.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The carabao mixing the clay is the most-photographed part of the visit. The animal walks slow circles in a deep pit of wet clay, kneading the material with hoof and weight. The pit is older than electricity in Vigan. Ask permission before photos; the carabao is a working animal, not a prop.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/2c/Carabao_kneading_clay_Vigan.jpg/800px-Carabao_kneading_clay_Vigan.jpg" alt="Carabao kneading clay in the Pagburnayan pit" loading="lazy"><figcaption>The carabao walking slow circles in the clay mixing pit at Pagburnayan, the kneading method older than electricity in Vigan. Photo via <a href="https://commons.wikimedia.org/wiki/File:Carabao_kneading_clay_Vigan.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Pair the pagburnayan visit with Calle Crisologo, Vigan Cathedral, Crisologo Museum, and the Bantay Bell Tower. A full heritage day covers all of them with calesa rides between stops.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/vigan-bantay-bell-tower.jpg" alt="Bantay Bell Tower near Vigan" loading="lazy"><figcaption>Bantay Bell Tower outside Vigan, the standalone watchtower that pairs naturally with the pagburnayan visit on a full heritage day.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 22. TAAL HERITAGE TOWN WALK
    // ------------------------------------------------------------------
    'taal-heritage-town-walk-batangas' => [
        [
            'anchor' => '<p>The Basilica is the largest church in Asia by floor area, finished in 1878. The facade is white stone with twin bell towers and a grand portico. Inside, the nave is wide and the side chapels hold santos carved in the heritage style. Free entry; modest dress. Climb the bell tower if access is open for the view of Taal town.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/batangas-city-taal-heritage-town.jpg" alt="Taal Basilica of Saint Martin of Tours" loading="lazy"><figcaption>The Basilica of Saint Martin of Tours in Taal, the largest Catholic church in Asia by floor area, finished 1878 with twin bell towers fronting the plaza.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Casa Villavicencio, the so-called "wedding gift house," was built in 1850 by Eulalio Villavicencio for his wife Gliceria Marella, a leading supporter of the Philippine Revolution. The house is open to visitors with a small entrance fee. The interior preserves the period furniture, the family portraits, and the chapel where Gliceria reportedly sewed flags for the Katipunan.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/88/Villavicencio_House_Taal.jpg/800px-Villavicencio_House_Taal.jpg" alt="Casa Villavicencio in Taal, Batangas" loading="lazy"><figcaption>Casa Villavicencio in Taal, the 1850 wedding-gift house that doubled as a Katipunan flag-sewing room during the Philippine Revolution. Photo via <a href="https://commons.wikimedia.org/wiki/File:Villavicencio_House_Taal.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The Taal Public Market on weekends has the embroidery row (Taal is the embroidery capital of the country), the taal tapang counters (pork tapa with garlic and vinegar), and the longganisa Taal stalls. Buy a kilo of tapang Taal and a half kilo of Taal longganisa for the road.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/tagaytay-beef-tapa-breakfast.jpg" alt="Tapa breakfast plate from the Taal-Tagaytay area" loading="lazy"><figcaption>Tapa from a Taal-area kitchen, the same family of cured-pork breakfast plates the public market vendors sell by the kilo on weekends.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Taal makes the country\'s finest hand-embroidered piña fabrics for barong tagalog. The embroidery shops along Calle MH del Pilar carry finished barongs and custom orders. A finished barong in piña silk takes weeks to a month to produce; the embroidery is intricate and family-passed.</p>',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/45/Pi%C3%B1a_fabric_embroidery.jpg/800px-Pi%C3%B1a_fabric_embroidery.jpg" alt="Hand-embroidered pineapple silk fabric from Taal" loading="lazy"><figcaption>Hand-embroidered piña silk from the Taal embroidery shops along Calle MH del Pilar, the family-passed craft behind the country\'s finest barong tagalogs. Photo via <a href="https://commons.wikimedia.org/wiki/File:Pi%C3%B1a_fabric_embroidery.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 23. SILAY HERITAGE HOUSES (Negros)
    // ------------------------------------------------------------------
    'silay-heritage-houses-negros-sugar-walk' => [
        [
            'anchor' => '<p>Balay Negrense is the most-visited of the three National Cultural Treasure houses in Silay. The two-story house has the wide silong, the elevated living floor, and the period furniture intact. The kitchen and the dining room are set as if the family had just stepped out. Small entrance fee; guided tour available.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c8/Balay_Negrense.jpg/800px-Balay_Negrense.jpg" alt="Balay Negrense, the Victor Gaston ancestral house in Silay" loading="lazy"><figcaption>Balay Negrense in Silay, the Victor Gaston ancestral house and a declared National Cultural Treasure. The dining table is set as if the family had stepped out. Photo via <a href="https://commons.wikimedia.org/wiki/File:Balay_Negrense.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The Jalandoni house is painted pink and is the third of the National Cultural Treasure houses in Silay. The interior holds period furniture, family portraits, and exhibits on the Silay social history.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/0/0b/Bernardino_Jalandoni_Ancestral_House.jpg/800px-Bernardino_Jalandoni_Ancestral_House.jpg" alt="Bernardino Jalandoni Pink House in Silay" loading="lazy"><figcaption>The Bernardino Jalandoni Ancestral House (the Pink House) in Silay, the third of the three Silay declared National Cultural Treasure houses. Photo via <a href="https://commons.wikimedia.org/wiki/File:Bernardino_Jalandoni_Ancestral_House.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>El Ideal Bakery is the long-running Silay bakery known for guapple pie (apple-style pie with guapple, the local guava-apple variety), pinasugbo (caramelized banana slices), and biscocho. Buy a guapple pie and eat at the small counter inside.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/bacolod-cansi.jpg" alt="Negros Occidental sugar-country plate" loading="lazy"><figcaption>A Negros Occidental sit-down plate from sugar country, the kind of regional cooking that pairs with the post-heritage walk lunch out of Silay.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Silay pairs naturally with a Bacolod evening for chicken inasal at Manokan Country. Stay in Bacolod and do Silay as a day trip.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/bacolod-chicken-inasal.jpg" alt="Chicken inasal at Bacolod Manokan Country" loading="lazy"><figcaption>Chicken inasal at Manokan Country in Bacolod, the standard evening pair after a daytime Silay heritage-house walk.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 24. CALLE REAL ILOILO HERITAGE
    // ------------------------------------------------------------------
    'calle-real-iloilo-heritage-architecture-deep-dive' => [
        [
            'anchor' => '<p>Plaza Libertad is the plaza where the Philippine flag was first raised in the Visayas in 1898. The plaza is calm during the day and a good orientation point. The Iloilo Cathedral (Jaro Cathedral is in a different district; the central church here is the San Jose de Placer Parish) and the surrounding heritage row anchor the walking circuit.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/iloilo-city-calle-real.jpg" alt="Calle Real heritage commercial corridor in Iloilo City" loading="lazy"><figcaption>Calle Real (J.M. Basa Street) in Iloilo City, the densest stretch of American-era and late-Spanish commercial buildings in the country.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The Daza Building, also known as the International Hotel, is one of the most ornate buildings on Calle Real. Beaux-Arts facade with detailed plasterwork. The hotel has been restored and now operates as a boutique heritage hotel with a cafe on the ground floor.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/9c/Iloilo_Calle_Real_buildings.jpg/800px-Iloilo_Calle_Real_buildings.jpg" alt="Calle Real heritage commercial buildings in Iloilo" loading="lazy"><figcaption>The restored Beaux-Arts facades along Calle Real in Iloilo, including the Daza Building (International Hotel) with its ornate plasterwork detailing. Photo via <a href="https://commons.wikimedia.org/wiki/File:Iloilo_Calle_Real_buildings.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Take a 15-minute Grab ride to Jaro Cathedral for the second half of the heritage walk. The Jaro Cathedral has its bell tower across the street (rare for Philippine churches) and the Jaro Plaza heritage houses are worth a slow afternoon.</p>',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/iloilo-city-3.jpg" alt="Iloilo City heritage district" loading="lazy"><figcaption>The Iloilo City heritage district context, the wider circuit Calle Real connects to once the morning Plaza Libertad walk is behind you.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 25. SARIAYA CASA WALK (Quezon)
    // ------------------------------------------------------------------
    'sariaya-casa-walk-quezon-heritage-day' => [
        [
            'anchor' => '<p>The plaza in front of the Sariaya Church anchors the walking circuit. The church is a stone parish from the 1700s with a wide bell tower. Free entry. Modest dress. The streets radiating from the plaza hold the heritage houses.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/sariaya-san-francisco-de-asis-parish-church.jpg" alt="Sariaya San Francisco de Asis Parish Church" loading="lazy"><figcaption>San Francisco de Asis Parish in Sariaya, the 1700s stone church on the plaza that anchors the casa-house walking circuit.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The Natalio Enriquez house is the most-photographed of the Sariaya heritage homes. Built in the late 1930s in Art Deco style, the facade has the cleaner geometric lines of the period. Privately owned; exterior viewing only, occasional public tours on weekends.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/quezon-province-sariaya-heritage-houses.jpg" alt="Sariaya heritage houses, Art Deco facades from the 1930s" loading="lazy"><figcaption>The Sariaya heritage houses from the late 1930s, the cleanest concentration of Art Deco facades outside Metro Manila built by sugar and coconut barons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Sariaya is part of the Quezon kakanin and longganisa belt. Pair the heritage walk with broas, longganisa Sariaya, and pancit chami. The longganisa here is sweeter than Lucban and shorter. The pancit chami is a Quezon noodle dish with miki, lechon kawali, and vegetables.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/sariaya-broas.jpg" alt="Sariaya broas and Quezon kakanin pasalubong" loading="lazy"><figcaption>Sariaya broas, the crisp ladyfinger biscuit Quezon makers sell alongside longganisa and pancit chami at the post-walk merienda stop.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The third week of May is the Agos sa Lansangan (Flow Through the Streets) Festival, Sariaya\'s local version of Pahiyas with elaborate house facades. If you can time the trip, the festival adds a layer of color to the heritage walk. The festival is calmer than Lucban Pahiyas and the houses are larger.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/sariaya-agawan-festival-every-may-15.jpg" alt="Sariaya Agawan Festival every May 15" loading="lazy"><figcaption>Sariaya during the May 15 Agawan Festival, the local version of Pahiyas with the heritage-house facades decorated for the harvest celebration.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 26. EARTHQUAKE BAROQUE CHURCHES
    // ------------------------------------------------------------------
    'earthquake-baroque-unesco-churches-trail' => [
        [
            'anchor' => '<p>Paoay Church, formally San Agustin Church of Paoay, finished in 1710, is the most photographed of the four. The 24 massive coral-stone buttresses extending outward from the side walls are the defining feature. The church survived the 1707 and 1865 earthquakes. The detached bell tower 30 meters from the church reinforced the earthquake-resistant design philosophy. Free entry; modest dress.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/ilocos-norte-paoay-church-1710.jpg" alt="Paoay Church earthquake-baroque coral-stone buttresses" loading="lazy"><figcaption>Paoay Church, San Agustin of Paoay, finished 1710. The 24 coral-stone side buttresses are the defining Earthquake Baroque feature, built thick to survive seismic shaking.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Santa Maria Church, Nuestra Señora de la Asuncion, sits atop a hill in Santa Maria town, Ilocos Sur. The church was finished in 1769. The location was chosen partly for defensive purposes; the climb up the 85-step stone staircase frames the visit. The bell tower is detached and stands at the front of the church. Free entry; mild climb required.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/ilocos-sur-santa-maria-church-1769.jpg" alt="Santa Maria Church Ilocos Sur, on a defensive hilltop" loading="lazy"><figcaption>Santa Maria Church (Nuestra Señora de la Asuncion) in Ilocos Sur, finished 1769 atop the hill reached by the 85-step stone staircase.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>San Agustin in Intramuros is the oldest stone church in the Philippines, finished in 1607, and the only Intramuros church to survive the World War II bombing of Manila. The interior is the trompe-l\'oeil ceiling painted by Italian artists, the carved wood pews, and the Spanish-era pipe organ. Free entry to the church; small fee for the adjacent museum. The Manila visit is the easiest of the four.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/manila-intramuros.jpg" alt="Intramuros, the home of San Agustin Church in Manila" loading="lazy"><figcaption>Intramuros in Manila, home of San Agustin Church (1607), the only Walled City church to survive the World War II bombing.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Miagao Church, Santo Tomas de Villanueva Parish, finished in 1797, is the Visayan addition to the listing. The yellow sandstone facade is carved with native flora (coconut, papaya, guava) alongside the saints, the rare Philippine blending of indigenous motifs into the Baroque ornamentation. Free entry; modest dress. Located 40 km south of Iloilo City; one-hour drive.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/13/Miagao_Church_facade.jpg/800px-Miagao_Church_facade.jpg" alt="Miagao Church facade with indigenous flora carved into sandstone" loading="lazy"><figcaption>Miagao Church (Santo Tomas de Villanueva) in Iloilo, finished 1797. The yellow sandstone facade carves coconut, papaya, and guava into the Baroque ornamentation. Photo via <a href="https://commons.wikimedia.org/wiki/File:Miagao_Church_facade.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 27. CAVITE REVOLUTIONARY CHURCHES LOOP
    // ------------------------------------------------------------------
    'cavite-revolutionary-churches-loop' => [
        [
            'anchor' => '<p>Aguinaldo Shrine is the ancestral home of Emilio Aguinaldo, the first President of the Philippines, in Kawit, Cavite. The Philippine independence was proclaimed from the balcony of this house on June 12, 1898. The shrine is now a museum with the original furniture, secret passages, and family memorabilia. Free entry. Closed Mondays. Allow 90 minutes.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/imus-aguinaldo-shrine-in-nearby-kawit.jpg" alt="Aguinaldo Shrine in Kawit, Cavite" loading="lazy"><figcaption>The Aguinaldo Shrine in Kawit, where Philippine independence was proclaimed from the balcony on June 12, 1898.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The Imus Cathedral, Nuestra Señora del Pilar Parish, is the seat of the Diocese of Imus and the site of the Battle of Imus in 1896, an early Filipino victory in the revolution. The current cathedral is the rebuilt version of the parish that was damaged during the war. Free entry; modest dress.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/imus-imus-cathedral-and-plaza-mariano-trias.jpg" alt="Imus Cathedral and Plaza Mariano Trias" loading="lazy"><figcaption>Imus Cathedral (Nuestra Señora del Pilar Parish) on Plaza Mariano Trias, the seat of the Diocese of Imus and the site of the 1896 Battle of Imus.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The Bonifacio Trial House in Maragondon is where Andres Bonifacio was tried in 1897 in the Tejeros faction split of the revolution. The house has been preserved with exhibits on the trial and the disputed verdict. Maragondon Church is adjacent and worth a 20-minute stop. The drive from Kawit takes around 90 minutes.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/naic-maragondon-heritage-town.jpg" alt="Maragondon heritage town in Cavite" loading="lazy"><figcaption>Maragondon heritage town, where the Bonifacio Trial House and the adjacent stone parish anchor the second half of the Cavite revolutionary loop.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Naic Church and the adjacent Naic Convent are tied to the early consolidation of the revolution under Aguinaldo. The convent housed the revolutionary leaders briefly in 1897. The church is a stone parish with a simple facade.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/naic-st-mary-magdalene-parish-church.jpg" alt="Naic St. Mary Magdalene Parish Church" loading="lazy"><figcaption>St. Mary Magdalene Parish in Naic, the stone parish next to the Naic Convent that briefly housed revolutionary leaders in 1897.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 28. PAHIYAS FESTIVAL LUCBAN
    // ------------------------------------------------------------------
    'pahiyas-festival-lucban-festival-deep-dive' => [
        [
            'anchor' => '<p>Kiping is a translucent leaf-shaped wafer made of rice flour batter spread on a leaf (usually cabbage or kalipi) and steamed. The cooked wafer takes the shape of the leaf and is colored with food-grade dye in bright pink, yellow, orange, green, and purple. Strings of kiping are wound into chandelier-style decorations or laid flat on house facades. After the festival, the kiping is roasted or fried and eaten with brown sugar syrup.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/5a/Pahiyas_kiping_decorations.jpg/800px-Pahiyas_kiping_decorations.jpg" alt="Pahiyas kiping decorations on Lucban house facade" loading="lazy"><figcaption>Kiping decorations strung into chandelier-style ornaments on a Pahiyas house facade in Lucban. The leaf-shaped rice wafers are dyed pink, yellow, orange, green, and purple. Photo via <a href="https://commons.wikimedia.org/wiki/File:Pahiyas_kiping_decorations.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The town committee designates the official Pahiyas streets each year. The route changes from year to year to spread the honor and the foot traffic. Ask at the Lucban tourist booth (set up near the church) for the current year\'s official route map. The decorated houses include front-yard decorations, full-facade compositions, and elaborate animal or icon arrangements made of kiping and rice stalks.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/0/04/Pahiyas_Festival_Lucban.jpg/800px-Pahiyas_Festival_Lucban.jpg" alt="Pahiyas Festival decorated houses on May 15 in Lucban" loading="lazy"><figcaption>Pahiyas houses along the official designated route in Lucban every May 15, with kiping, bilao of palay, and hung vegetables honoring San Isidro Labrador. Photo via <a href="https://commons.wikimedia.org/wiki/File:Pahiyas_Festival_Lucban.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Pancit habhab is the Lucban festival snack. Miki noodles topped with pork and vegetables served on a piece of banana leaf, eaten by tilting the leaf into your mouth. Vendors line the streets. One serving costs little and the eating posture is part of the festival experience.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/lucena-broas.jpg" alt="Quezon merienda plates served at Pahiyas Festival" loading="lazy"><figcaption>Quezon merienda plates that line the Pahiyas route alongside the pancit habhab vendors, the festival-day mix of sweet and savory snacks.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 29. SINULOG (Cebu)
    // ------------------------------------------------------------------
    'sinulog-cebu-festival-deep-dive' => [
        [
            'anchor' => '<p>The image of the Santo Niño was given by Magellan to Queen Juana of Cebu in 1521 at the baptism that brought Christianity to the islands. The image survived the Magellan expedition\'s defeat at Mactan and was rediscovered in 1565 by the Legazpi expedition. The image is enshrined at the Basilica Minore del Santo Niño in Cebu City. Free entry to the basilica.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/cebu-city-magellans-cross-and-basilica-del-santo-nino.jpg" alt="Magellan Cross and Basilica del Santo Niño in Cebu City" loading="lazy"><figcaption>Magellan\'s Cross and the Basilica Minore del Santo Niño in Cebu City, where the 1521 image given by Magellan to Queen Juana is enshrined.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Sinulog Sunday morning to afternoon. Contingents from across the country dance the Sinulog step along the parade route in elaborate costumes, painted faces, and choreographed formations. The judging area at the Cebu City Sports Center is the high point. Tickets to the bleachers sell out months ahead.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/cebu-city-sinulog-festival-every-january.jpg" alt="Sinulog Festival grand parade in Cebu City every January" loading="lazy"><figcaption>The Sinulog grand parade through Cebu City on the third Sunday of January. Contingents dance the two-step Sinulog with the Santo Niño image held high.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>"Pit Señor" is the Cebuano chant during the festival, short for "Sangpit sa Señor" or "to call upon the Lord." Hear it from the basilica novena to the parade route. Travelers can join the call.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/cebu-city-1.jpg" alt="Cebu City during Sinulog festival week" loading="lazy"><figcaption>Cebu City during Sinulog week, when the basilica novena, the candle vendors, and the parade route fill the streets with the Pit Señor chant.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 30. MASSKARA BACOLOD
    // ------------------------------------------------------------------
    'masskara-bacolod-festival-deep-dive' => [
        [
            'anchor' => '<p>The Masskara masks are colorful, smiling, often plumed and beaded. Schools, barangays, and corporate sponsors enter contingents with elaborate mask designs. The masks cover the dancers\' faces during the parade; the dance is the focus, the individual identity is not.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/bacolod-masskara-festival-every-october.jpg" alt="Masskara Festival smiling masks in Bacolod every October" loading="lazy"><figcaption>The Masskara smiling masks during the Bacolod festival every fourth week of October, colorful and plumed for the street dance competition.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Pair the festival with chicken inasal at Manokan Country, the row of grilled chicken restaurants in Bacolod. Order paa, pecho, or atay isaw with garlic rice and chicken oil. Pair with vinegar dip and soft drinks. The standard order for two people is two chicken pieces, two rice, and a soft drink.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/bacolod-kadios-baboy-langka.jpg" alt="KBL, a Negrense plate of kadios, baboy, and langka" loading="lazy"><figcaption>KBL (kadios, baboy, langka) from a Bacolod kitchen, the Negrense soup plate that often joins the chicken inasal order at Manokan Country during Masskara weekend.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The Silay heritage walk and the Don Salvador Benedicto cool-weather climate are within driving distance and pair well with Masskara week if you want days outside the festival energy.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/bacolod-the-ruins-talisay.jpg" alt="The Ruins in Talisay near Bacolod" loading="lazy"><figcaption>The Ruins in Talisay near Bacolod, the daylight heritage pair for travelers who want a calm side trip outside Masskara energy.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 31. ATI-ATIHAN KALIBO
    // ------------------------------------------------------------------
    'ati-atihan-kalibo-january-festival' => [
        [
            'anchor' => '<p>The legend traces the festival to the 13th century, when 10 Bornean datus arrived in Panay fleeing their home and bought lowland from the local Ati people (Negrito tribe). To celebrate the friendship, both groups blackened their faces with soot. When Spanish missionaries later introduced the Santo Niño, the tradition merged into a Christian festival. The "Ati-Atihan" means "to be like Ati."</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/89/Ati-Atihan_Festival_Kalibo.jpg/800px-Ati-Atihan_Festival_Kalibo.jpg" alt="Ati-Atihan Festival revelers with soot-painted faces in Kalibo" loading="lazy"><figcaption>Ati-Atihan revelers in Kalibo with soot-painted faces and tribal costumes, honoring the Bornean-Ati friendship origin of the festival. Photo via <a href="https://commons.wikimedia.org/wiki/File:Ati-Atihan_Festival_Kalibo.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The Ati-Atihan parade is structured around tribus (tribes), groups of dancers from different parts of Aklan and beyond. Each tribu has its own costume, choreography, and rhythm section (drums and bamboo instruments). The judging is on the final Sunday.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c4/Ati-Atihan_tribu_dancers.jpg/800px-Ati-Atihan_tribu_dancers.jpg" alt="Ati-Atihan tribu dancers in Kalibo parade" loading="lazy"><figcaption>Ati-Atihan tribu dancers during the Kalibo parade, each contingent with its own costume, choreography, and bamboo rhythm section. Photo via <a href="https://commons.wikimedia.org/wiki/File:Ati-Atihan_tribu_dancers.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Most Ati-Atihan travelers fly into Kalibo airport and split the week between the festival and Boracay. The combo works because Ati-Atihan is loud and Boracay is calm.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/boracay-white-beach-stations-1-2-3.jpg" alt="Boracay White Beach near Kalibo" loading="lazy"><figcaption>Boracay White Beach, the calm island pair for travelers who fly into Kalibo for Ati-Atihan and split the week between festival noise and beach quiet.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 32. PANAGBENGA BAGUIO
    // ------------------------------------------------------------------
    'panagbenga-baguio-february-festival' => [
        [
            'anchor' => '<p>The grand street dance parade is on the last Saturday of February. Schools and barangays from Baguio and Benguet enter contingents in flower-themed costumes and choreography. The route runs from Athletic Bowl down Session Road.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/4c/Panagbenga_street_dance.jpg/800px-Panagbenga_street_dance.jpg" alt="Panagbenga Festival street dance parade in Baguio" loading="lazy"><figcaption>The Panagbenga street dance parade in Baguio on the last Saturday of February, contingents in flower-themed costumes down Session Road. Photo via <a href="https://commons.wikimedia.org/wiki/File:Panagbenga_street_dance.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The float parade is on the last Sunday of February. Flower-covered floats from corporate sponsors and Cordillera flower farms make the slow procession through downtown Baguio. The floats are built over weeks and the flower work is the headline draw.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/14/Panagbenga_Float_Parade.jpg/800px-Panagbenga_Float_Parade.jpg" alt="Panagbenga flower float parade in Baguio" loading="lazy"><figcaption>The Panagbenga flower float parade in Baguio on the last Sunday of February, with floats covered in Cordillera farm blooms moving slow through downtown. Photo via <a href="https://commons.wikimedia.org/wiki/File:Panagbenga_Float_Parade.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Baguio in February is at its coolest. Pair Panagbenga with Mines View, La Trinidad strawberry farm, Camp John Hay, and the Tam-Awan Village. Two to three days covers the festival highlights and the city.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/2d/Burnham_Park_Baguio.jpg/800px-Burnham_Park_Baguio.jpg" alt="Burnham Park lake in Baguio during Panagbenga" loading="lazy"><figcaption>Burnham Park in Baguio, the lake-side venue for the Panagbenga flower show and the quiet contrast to the Session Road street fair. Photo via <a href="https://commons.wikimedia.org/wiki/File:Burnham_Park_Baguio.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 33. PISTA Y DAYAT LINGAYEN PANGASINAN
    // ------------------------------------------------------------------
    'pistay-dayat-pangasinan-sea-festival' => [
        [
            'anchor' => '<p>Lingayen Beach is the long stretch of brown-sand beach fronting the provincial capitol. The beach is wide, calm, and serves as the festival venue. Walking the beach in the early morning or evening is the calm side of the visit.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/lingayen-lingayen-beach.jpg" alt="Lingayen Beach in Pangasinan, the Pistay Dayat festival venue" loading="lazy"><figcaption>Lingayen Beach in Pangasinan, the wide brown-sand stretch fronting the provincial capitol and the main venue for Pista\'y Dayat every May 1.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Pangasinan cuisine traditions: pigar-pigar (sliced beef stir-fried with onions), tupig (grilled rice cake in banana leaf), bagoong Lingayen, and bocayo (coconut sweet). The festival food stalls run the gamut from grilled seafood to local sweets.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/dagupan-pangasinan-pigar-pigar.jpg" alt="Pigar-pigar, the Pangasinan sliced beef and onions plate" loading="lazy"><figcaption>Pigar-pigar from a Pangasinan kitchen, the sliced-beef-and-onions plate that defines the province\'s carinderia menu during Pista\'y Dayat week.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Lingayen is 45 minutes from Alaminos and the Hundred Islands. Many travelers split their Pangasinan visit between the Pista\'y Dayat festival days and the Hundred Islands day trip. The May weather is dry and warm.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/alaminos-hundred-islands-hundred-islands-national-park.jpg" alt="Hundred Islands National Park in Alaminos" loading="lazy"><figcaption>Hundred Islands National Park in Alaminos, the calm dry-season pair for travelers who split a Pangasinan visit between Lingayen and the limestone islets.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The Lingayen Capitol Park is the main concert venue. The energy is family-friendly and provincial.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/lingayen-lingayen-capitol-and-provincial-park.jpg" alt="Lingayen Capitol and Provincial Park in Pangasinan" loading="lazy"><figcaption>The Lingayen Capitol and Provincial Park, the family-friendly main concert venue during the Pista\'y Dayat festival week.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 34. TBOLI LAKE SEBU
    // ------------------------------------------------------------------
    'tboli-community-visit-lake-sebu-cultural-day' => [
        [
            'anchor' => '<p>T\'nalak is the patterned cloth woven from abaca fiber. The patterns are believed to come from dreams; the weavers (called dreamweavers) receive the design in sleep and translate it onto the loom. The process from abaca stripping to finished cloth takes weeks per piece. Authentic t\'nalak is woven by hand on a back-strap loom. Look for cloth with imperfections (sign of hand-weaving) rather than perfectly uniform machine-made imitations.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/d/de/Tnalak_weaving_Tboli.jpg/800px-Tnalak_weaving_Tboli.jpg" alt="Tboli dreamweaver at a back-strap t-nalak loom" loading="lazy"><figcaption>A Tboli dreamweaver at a back-strap loom in Lake Sebu, translating dream-given patterns into hand-woven t\'nalak. The full cloth takes weeks per piece. Photo via <a href="https://commons.wikimedia.org/wiki/File:Tnalak_weaving_Tboli.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The Tboli brass-casting tradition produces betel-nut containers, ornaments, and ceremonial pieces using the lost-wax method. The casting workshops in the community welcome visitors. The brass pieces are heavier than tourist trinkets; the weight is the craft sign.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/0/02/Tboli_brass_casting.jpg/800px-Tboli_brass_casting.jpg" alt="Tboli brass ornaments cast by lost-wax method" loading="lazy"><figcaption>Tboli brass ornaments cast by the lost-wax method, the betel-nut containers and ceremonial pieces that pair with t\'nalak weaving as the community\'s twin crafts. Photo via <a href="https://commons.wikimedia.org/wiki/File:Tboli_brass_casting.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The Seven Falls of Lake Sebu and the zipline above them are the natural-attraction side of the visit. The zipline runs over two of the falls; the view is dramatic. Pair the cultural morning with the zipline afternoon for a full day.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/4e/Lake_Sebu_South_Cotabato.jpg/800px-Lake_Sebu_South_Cotabato.jpg" alt="Lake Sebu in South Cotabato, Mindanao" loading="lazy"><figcaption>Lake Sebu in South Cotabato, the highland Tboli homeland with its seven lakes and the Seven Falls zipline above the southern outflow. Photo via <a href="https://commons.wikimedia.org/wiki/File:Lake_Sebu_South_Cotabato.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 35. IFUGAO BANAUE
    // ------------------------------------------------------------------
    'ifugao-banaue-cultural-visit-reading' => [
        [
            'anchor' => '<p>The Banaue, Batad, Mayoyao, Hapao, and Hungduan rice terraces are UNESCO World Heritage Sites. The Ifugao carved them out of the mountain sides using stone walls and irrigation channels engineered to follow the contour of the land. The terraces have functioned as a working agricultural system for over 2,000 years. The Ifugao still plant tinawon (heirloom rice) in the terraces.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/56/Banaue_Rice_Terraces.jpg/800px-Banaue_Rice_Terraces.jpg" alt="Banaue Rice Terraces in Ifugao, Cordillera" loading="lazy"><figcaption>The Banaue Rice Terraces in Ifugao, carved into the mountain sides for over 2,000 years and still planted with tinawon heirloom rice. Photo via <a href="https://commons.wikimedia.org/wiki/File:Banaue_Rice_Terraces.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The bul-ul are the rice god statues carved from a single block of wood and placed in the rice granaries. The figures protect the rice from pests and spiritual harm. The bul-ul are sacred; if you see one in a private home, do not touch.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/9f/Bulul_Ifugao_rice_god.jpg/800px-Bulul_Ifugao_rice_god.jpg" alt="Ifugao bul-ul rice god statue from a single hardwood block" loading="lazy"><figcaption>An Ifugao bul-ul rice god carved from a single block of hardwood and placed in the granary to guard the tinawon harvest. Sacred; not for touching in homes. Photo via <a href="https://commons.wikimedia.org/wiki/File:Bulul_Ifugao_rice_god.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The Batad amphitheater terraces are accessed by a 45-minute trek from the saddle. Stay overnight at one of the village guesthouses; eat tinawon rice and local vegetables; walk through the terraces with an Ifugao guide who can explain the irrigation and the farming calendar.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/12/Batad_Rice_Terraces.jpg/800px-Batad_Rice_Terraces.jpg" alt="Batad amphitheater rice terraces in Ifugao" loading="lazy"><figcaption>The Batad amphitheater rice terraces in Ifugao, reached by a 45-minute trek from the saddle and the most-photographed of the UNESCO Ifugao terraces. Photo via <a href="https://commons.wikimedia.org/wiki/File:Batad_Rice_Terraces.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The Tappia Falls below Batad is a 30-minute trek from the amphitheater. The waterfall plunges into a clear pool and pairs with the rice terrace visit for a half-day. Bring trail runners and a swimsuit.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/95/Tappiya_Falls_Batad.jpg/800px-Tappiya_Falls_Batad.jpg" alt="Tappia Falls below the Batad rice terraces" loading="lazy"><figcaption>Tappia Falls below the Batad amphitheater terraces, the 30-minute trek extension that pairs with the rice-terrace morning for a full Ifugao half-day. Photo via <a href="https://commons.wikimedia.org/wiki/File:Tappiya_Falls_Batad.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 36. KALINGA BUSCALAN
    // ------------------------------------------------------------------
    'kalinga-buscalan-tattoo-village-visit' => [
        [
            'anchor' => '<p>Whang-od Oggay was born around 1917 in Buscalan. She received her tattoo training from her father in her teens and has been practicing batok for over 80 years. She is the last mambabatok (traditional tattoo artist) of her generation. In her later years she has trained her grand-nieces Grace and Ilyang to continue the tradition. She has been declared a Manlilikha ng Bayan (National Living Treasure).</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/40/Whang-od_Buscalan_Kalinga.jpg/800px-Whang-od_Buscalan_Kalinga.jpg" alt="Apo Whang-od, the Kalinga mambabatok tattoo master" loading="lazy"><figcaption>Apo Whang-od Oggay of Buscalan, the centenarian Kalinga mambabatok and declared National Living Treasure who has practiced batok for over 80 years. Photo via <a href="https://commons.wikimedia.org/wiki/File:Whang-od_Buscalan_Kalinga.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The tattoo is applied by tapping a thorn (from a pomelo tree) dipped in charcoal-and-water ink into the skin with a bamboo stick. The thorn punctures the skin; the ink enters the wound. No machines, no electricity, no modern needles. The pattern designs are tribal: snake scales, ferns, centipedes, eagles. Each design has cultural meaning.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/5f/Kalinga_batok_tattoo.jpg/800px-Kalinga_batok_tattoo.jpg" alt="Kalinga batok tattoo applied by hand-tap method" loading="lazy"><figcaption>Kalinga batok work in progress, with the thorn tip dipped in charcoal-and-water ink tapped into the skin by a bamboo stick at steady rhythm. Photo via <a href="https://commons.wikimedia.org/wiki/File:Kalinga_batok_tattoo.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>From Tinglayan town in Kalinga, a tricycle ride and a 30-minute trek bring travelers to Buscalan village. The trail is muddy in the wet season. Bring trail runners, a light pack, and water.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/1c/Buscalan_village_Kalinga.jpg/800px-Buscalan_village_Kalinga.jpg" alt="Buscalan village in Kalinga, Cordillera" loading="lazy"><figcaption>Buscalan village in Kalinga, reached by tricycle from Tinglayan town then a 30-minute trek through the Cordillera highlands. Photo via <a href="https://commons.wikimedia.org/wiki/File:Buscalan_village_Kalinga.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 37. APUNG IRU APALIT (Pampanga)
    // ------------------------------------------------------------------
    'apung-iru-apalit-sukad-pampanga-river-festival' => [
        [
            'anchor' => '<p>The procession on June 28 carries the image on a decorated pagoda-style boat down the Pampanga River. Hundreds of supporting boats join. The procession is loud, festive, and family-attended. Watch from the river bank or join a friend\'s boat if you have a Apalit connection.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/cc/Apung_Iru_fluvial_procession.jpg/800px-Apung_Iru_fluvial_procession.jpg" alt="Apung Iru fluvial procession on the Pampanga River" loading="lazy"><figcaption>The Apung Iru fluvial procession on the Pampanga River every June 28, with the image of San Pedro Apostol on a decorated pagoda boat. Photo via <a href="https://commons.wikimedia.org/wiki/File:Apung_Iru_fluvial_procession.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The festival days fill the Apalit plaza with food stalls: sisig, pancit luglug, kakanin, and the local Apalit specialty of pindang damulag (carabao tapa). Try the pindang as the local plate of the trip.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/amadeo-kakanin.jpg" alt="Kakanin sold at a Pampanga town fiesta" loading="lazy"><figcaption>Kakanin on the Apalit plaza during Apung Iru week, the standard merienda spread next to the sisig and pancit luglug stalls.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Apalit pairs with Betis (the woodcarving town), Bacolor (the lahar-buried town), and Lubao (the historic church). A two-day Pampanga heritage circuit can cover all of them with the Apung Iru festival as the anchor weekend.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/pampanga-province-2.jpg" alt="Pampanga countryside scene" loading="lazy"><figcaption>The Pampanga countryside that connects Apalit, Betis, Bacolor, and Lubao into a two-day heritage circuit with Apung Iru as the anchor weekend.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 38. VIGAN LONGGANISA AND BASI
    // ------------------------------------------------------------------
    'vigan-longganisa-basi-deep-dive-ilocos-sur' => [
        [
            'anchor' => '<p>Vigan longganisa is shorter than Lucban (around an inch each), garlicky, vinegary, and slightly sour. The pork is coarse-ground and fermented for half a day before stuffing. The casing is tied close; the sausages come in cellophane-tied bundles. The flavor leans more sour than oregano-heavy (unlike Lucban) and more garlic-heavy than Pampanga.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/laoag-bagnet.jpg" alt="Ilocos breakfast plate with longganisa and bagnet" loading="lazy"><figcaption>An Ilocos breakfast plate with Vigan longganisa alongside bagnet, the short garlicky links the Vigan public market sells in cellophane-tied bundles.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The Hidden Garden in Brgy. Bulala on the outskirts of Vigan is a garden restaurant that serves longganisa with traditional Ilocano sides like pinakbet, dinengdeng (vegetable stew), and bagnet. The setting is calm and the kitchen leans home-style.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/san-fernando-la-union-bagnet-and-pinakbet.jpg" alt="Bagnet and pinakbet, the Ilocos sit-down plate" loading="lazy"><figcaption>Bagnet alongside pinakbet, the Ilocano garden-restaurant plate Hidden Garden serves with Vigan longganisa and dinengdeng on the side.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Basi is made by fermenting sugarcane juice with samak bark, ginger, and other flavoring agents in clay jars (burnay) for months. The wine is sweet, slightly tart, and carries an aged amber color when matured. Basi predates the colonial era and was the everyday drink of Ilocano farmers and revolutionary fighters (the Basi Revolt of 1807 was caused by Spanish bans on home-brewing).</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/6c/Basi_sugarcane_wine_Ilocos.jpg/800px-Basi_sugarcane_wine_Ilocos.jpg" alt="Basi, Ilocano sugarcane wine in burnay clay jars" loading="lazy"><figcaption>Basi, the Ilocano sugarcane wine fermented for months in burnay clay jars with samak bark and ginger. The 1807 Basi Revolt was sparked by Spanish bans on home-brewing. Photo via <a href="https://commons.wikimedia.org/wiki/File:Basi_sugarcane_wine_Ilocos.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The longganisa morning and the basi afternoon pair with a Vigan heritage walk through Calle Crisologo, the burnayan pottery workshop, and the Vigan Cathedral. Two full days covers Vigan well.</p>',
            'position' => 'before',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/ilocos-sur-calle-crisologo-vigan.jpg" alt="Calle Crisologo cobblestone street in Vigan" loading="lazy"><figcaption>Calle Crisologo at sunset in Vigan, the heritage walk that pairs naturally with the longganisa breakfast and the basi-tasting afternoon.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 39. CAMARINES SUR CHURCH LOOP
    // ------------------------------------------------------------------
    'camarines-sur-church-loop-bicol' => [
        [
            'anchor' => '<p>The Basilica in Naga is the seat of the September Penafrancia Festival, the biggest Marian devotion in Bicol. The Marian image was brought to Naga in 1710 and the original chapel was eventually replaced by the basilica. The structure is large, modern, and the interior is calm outside September. Free entry.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/naga-camarines-sur-1.jpg" alt="Naga, Camarines Sur, the Bicol religious center" loading="lazy"><figcaption>Naga in Camarines Sur, the Bicol religious center built around the Penafrancia Basilica and the Marian devotion that dates to 1710.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Nabua town has the Saint John the Baptist Parish, a stone Franciscan parish dating to the 1700s. The church is calm and the surrounding plaza is one of the older town squares in Camarines Sur. Drive 30 minutes south from Naga.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/80/Nabua_Church_Camarines_Sur.jpg/800px-Nabua_Church_Camarines_Sur.jpg" alt="Nabua Church, Saint John the Baptist Parish in Camarines Sur" loading="lazy"><figcaption>Saint John the Baptist Parish in Nabua, a stone Franciscan parish from the 1700s on one of the older town squares in Camarines Sur. Photo via <a href="https://commons.wikimedia.org/wiki/File:Nabua_Church_Camarines_Sur.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The town of Buhi, between Iriga and Bato, has Lake Buhi and the small church of San Antonio de Padua. The lake is home to the sinarapan (the world\'s smallest commercially-harvested fish). A 20-minute stop adds a natural-side contrast to the church loop.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/85/Lake_Buhi_Camarines_Sur.jpg/800px-Lake_Buhi_Camarines_Sur.jpg" alt="Lake Buhi in Camarines Sur, home of sinarapan fish" loading="lazy"><figcaption>Lake Buhi in Camarines Sur, home of the sinarapan (the world\'s smallest commercially-harvested fish) and the natural-side contrast on the Bicol church loop. Photo via <a href="https://commons.wikimedia.org/wiki/File:Lake_Buhi_Camarines_Sur.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 40. MANGYAN MINDORO
    // ------------------------------------------------------------------
    'mangyan-mindoro-cultural-visit-reading' => [
        [
            'anchor' => '<p>The Hanunoo Mangyan and the Buhid Mangyan still use the ambahan script (Surat Mangyan), one of the four remaining pre-colonial Philippine writing systems. The script has been listed by UNESCO as a Memory of the World. The other three surviving scripts are Tagbanwa (Palawan), Pala\'wan (Palawan), and the Bisayan Eskaya (Bohol). The ambahan is used for poetry, courtship, and recording oral traditions.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/24/Hanunoo_script_Mangyan.jpg/800px-Hanunoo_script_Mangyan.jpg" alt="Hanunoo Mangyan script (Surat Mangyan) on bamboo" loading="lazy"><figcaption>The Hanunoo Mangyan script (Surat Mangyan) inscribed on bamboo, one of the four surviving pre-colonial Philippine writing systems and a UNESCO Memory of the World. Photo via <a href="https://commons.wikimedia.org/wiki/File:Hanunoo_script_Mangyan.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The Mangyan weave bags, baskets, and ornaments from nito vine. The dark-brown vines are split and woven by hand. The bags are durable, beautiful, and worth buying directly from the artisans where possible.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/68/Nito_vine_basket_weaving.jpg/800px-Nito_vine_basket_weaving.jpg" alt="Mangyan nito-vine baskets, hand-woven craft from Mindoro" loading="lazy"><figcaption>Mangyan nito-vine baskets from Mindoro, dark-brown vines split and woven by hand into bags and ornaments. Photo via <a href="https://commons.wikimedia.org/wiki/File:Nito_vine_basket_weaving.jpg" target="_blank" rel="nofollow noopener">Wikimedia Commons</a>.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Most Mindoro travelers visit Puerto Galera or Apo Reef. Adding a day at the Mangyan Heritage Center in Calapan deepens the trip beyond beach time.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/destinations/puerto-galera-3.jpg" alt="Puerto Galera coast in Mindoro" loading="lazy"><figcaption>The Puerto Galera coast in Mindoro, the beach pair for travelers who add a Calapan day at the Mangyan Heritage Center to deepen the trip.</figcaption></figure>',
        ],
    ],

];
