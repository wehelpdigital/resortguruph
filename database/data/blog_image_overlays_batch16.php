<?php

/**
 * Image overlays for blog posts in batch 16 (family + romance + travel-ops).
 *
 * Each entry keys off the blog post slug. Each anchor must appear EXACTLY ONCE
 * inside that post's content_html; the figure HTML is inserted immediately
 * after the anchor block. Image srcs do not duplicate across this batch.
 *
 * Mix: local /storage/rg-media/* assets where the destination matches the
 * brand library, Wikimedia Commons fallbacks with attribution for travel-ops
 * and topics outside the spot library (Caleruega, Sky Ranch, NAIA, etc.).
 *
 * Skipped posts (no list-of-specific-things to anchor visually):
 *  - sim-card-philippines-traveler-esim-prepaid-guide
 *  - philippine-visa-overstay-immigration-honest-guide
 *  - gcash-paymaya-tourist-cash-vs-card-philippines
 *  - atm-fee-philippines-traveler-cash-strategy
 *  - esim-philippines-digital-nomad-data-plans
 *  - gluten-free-philippines-celiac-by-destination
 */

return [

    // ------------------------------------------------------------------
    // 1. EL NIDO HONEYMOON QUIET-SIDE FIVE-DAY COUPLE PLAN
    // ------------------------------------------------------------------
    'el-nido-honeymoon-quiet-side-five-day-couple-plan' => [
        [
            'anchor' => '<p>Book a private banca for two through your accommodation or a town agency. The shared tours are cheaper and the boats are full of barkadas with bluetooth speakers. The private option lets you pick the islands in any order, eat lunch on a quiet beach, and skip stops you do not want. For couples the cleanest itinerary is Big Lagoon at opening hour around 8 AM (the crowds arrive by 10), then Small Lagoon by kayak, then a beach lunch at Shimizu or Helicopter Island.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/el-nido-big-lagoon-tour-a.jpg" alt="Big Lagoon in El Nido at opening hour" loading="lazy"><figcaption>Big Lagoon on a private 8 AM run, the quiet window before the shared Tour A boats arrive and the engines fill the cove.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Nacpan is shallow for the first 50 meters, which is good for swimming together, and the south side has a small viewpoint hill that takes 15 minutes to climb for the postcard photo. Stay until late afternoon, drive back to town for dinner. This is the day most couples remember.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/el-nido-nacpan-beach-45-min-north.jpg" alt="Nacpan Beach, the four-kilometer twin-beach 45 minutes north of El Nido" loading="lazy"><figcaption>Nacpan Beach, the four-kilometer twin-beach arc 45 minutes north of El Nido town. The viewpoint hill on the south end is a 15-minute climb.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Back in town by late afternoon. Walk the back streets where the locals live. El Nido has a quieter side that the tour-day crowd never sees, and the alley karinderyas serve the better grilled fish dinners.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/el-nido-fresh-seafood.jpg" alt="Fresh grilled fish at an El Nido alley karinderya" loading="lazy"><figcaption>Grilled lapu-lapu and pusit at an El Nido back-street karinderya, the quieter dinner side the boat-day crowd never finds.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 2. BORACAY HONEYMOON STATION 1 QUIET WEEK
    // ------------------------------------------------------------------
    'boracay-honeymoon-station-1-quiet-week' => [
        [
            'anchor' => '<p>Drop the bags by midday and head straight to the beach. Station 1 has the wide white sand and the talong palms, and the walk south from Willy\'s Rock toward D\'Mall takes around 25 minutes. This first walk is the orientation that every couple needs. You will know which kitchens look right for dinner and which beach loungers are within walking distance of the room. End at the D\'Mall food strip for an early light dinner, then walk back north for the sunset.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/boracay-white-beach-stations-1-2-3.jpg" alt="Station 1 White Beach with talong palms" loading="lazy"><figcaption>Station 1 White Beach, the widest stretch of the four-kilometer strip, with the talong palms shading the loungers at the high-tide line.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Book a private banca for the day through your accommodation. The shared tour boats stop at three islands with 30 to 40 strangers; the private option lets you pick the order and skip the busy snorkel stops. Standard couple route: Crystal Cove for the morning swim, Puka Beach for the long shell-sand walk and lunch, then back to White Beach by mid-afternoon. The puka shell stretch on the north end of Boracay is the postcard side most day-trippers miss.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/boracay-puka-beach-north.jpg" alt="Puka Beach on the north end of Boracay" loading="lazy"><figcaption>Puka Beach on the north end of Boracay, the long shell-sand stretch most shared day-trippers skip in favor of White Beach.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Back to the room by 3 PM, nap, then walk the beach at sunset. Sunset on Boracay is the best two hours of the day and every couple should plan for it twice.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/boracay-sunset-paraw-sailing.jpg" alt="Sunset paraw sailing off Boracay White Beach" loading="lazy"><figcaption>A paraw sailing across the sunset off Station 1, the best two hours of every Boracay day and the calmer side of the strip after the lunch rush.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 3. PANGLAO HONEYMOON THREE-DAY BOHOL COUPLE SIDE
    // ------------------------------------------------------------------
    'panglao-honeymoon-three-day-bohol-couple-side' => [
        [
            'anchor' => '<p>Continue to Pamilacan island itself for a late breakfast and a snorkel along the protected reef. Back to Panglao by early afternoon, nap, then walk the Alona Beach strip for dinner. Alona has the busiest food scene on Panglao and a honeymoon dinner along the beach with a calm wine is a clean evening.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/panglao-alona-beach.jpg" alt="Alona Beach strip on Panglao at evening" loading="lazy"><figcaption>The Alona Beach food strip on Panglao at evening, the busiest dining row on the island and the standard couple dinner spot after the Pamilacan run.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Order the kinilaw at any Panglao kitchen; the southern Visayan version with coconut milk and calamansi is the lighter take on the dish. Pack reef shoes for the Pamilacan run and a light rain jacket for the boat day. The honeymoon week is the one to splurge on a beachfront room; for Panglao that means a Dumaluan or Doljo room, not Alona.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/panglao-kinilaw.jpg" alt="Panglao kinilaw with coconut milk" loading="lazy"><figcaption>Panglao kinilaw with coconut milk and calamansi, the lighter southern-Visayan take on the ceviche that pairs better with a long beach evening.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 4. SIQUIJOR HONEYMOON SCOOTER LOOP COUPLE WEEK
    // ------------------------------------------------------------------
    'siquijor-honeymoon-scooter-loop-couple-week' => [
        [
            'anchor' => '<p>Cambugahay Falls is a three-tier waterfall and pool on the southern side of the island, around 30 minutes by scooter from San Juan. The locals run a tarzan-swing rope at the second pool and the swim is the calmest of any falls in the Visayas. Go before 9 AM to skip the day-tripper crowd from Dumaguete; the pool fills up by mid-morning.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/siquijor-cambugahay-falls.jpg" alt="Cambugahay Falls tarzan-swing pool, Siquijor" loading="lazy"><figcaption>Cambugahay Falls on the south side of Siquijor. The second-tier pool has the tarzan-swing rope and is calmest before the 9 AM day-tripper rush.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Continue south to Lazi for the old century-old church and convent, two of the better-preserved Spanish-era buildings outside Manila. Lazi convent has a small heritage museum and the wood-and-coral construction is unusual. Lunch at Lazi, then loop back to San Juan via the southern coast.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/siquijor-lazi-convent-1857.jpg" alt="Lazi Convent on Siquijor, built 1857" loading="lazy"><figcaption>The 1857 Lazi Convent on the southern coast of Siquijor, with the wood-and-coral construction that survives almost untouched outside Manila.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Sleep in. Late breakfast on the beach. Mid-morning, scooter to the old-enchanted balete tree at Campalanas in Lazi, the 400-year-old centuries-old balete that has been a Siquijor heritage stop for decades. The small pool at the base has fish-spa fish that nibble feet; couples either love it or hate it. The drive there and back is 90 minutes total.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/siquijor-the-old-enchanted-balete-tree.jpg" alt="Old Enchanted Balete Tree at Campalanas, Siquijor" loading="lazy"><figcaption>The 400-year-old Enchanted Balete Tree at Campalanas, with the small fish-spa pool at the base where the doctor fish nibble feet.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 5. TAGAYTAY HONEYMOON WEEKEND COOL AIR FROM MANILA
    // ------------------------------------------------------------------
    'tagaytay-honeymoon-weekend-cool-air-from-manila' => [
        [
            'anchor' => '<p>Lunch back in Tagaytay at the bulalo strip along Mahogany Market. Mahogany has the half-dozen bulalo kitchens that locals pick over the ridge tourist restaurants; the bulalo here is the cleaner version. Afternoon slow at the hotel or a couples massage at a ridge spa.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/tagaytay-mahogany-market.jpg" alt="Mahogany Market bulalo strip in Tagaytay" loading="lazy"><figcaption>The Mahogany Market bulalo strip in Tagaytay, the half-dozen open-air kitchens locals pick over the ridge tourist restaurants.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Wake up early and head to the Taal viewpoint at People\'s Park in the Sky or the Picnic Grove. The morning light on Taal is the cleanest before the haze sets in by midday. People\'s Park has the highest viewpoint on the ridge and the walk up is short and gentle.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/tagaytay-peoples-park-in-the-sky.jpg" alt="Peoples Park in the Sky viewpoint over Taal" loading="lazy"><figcaption>Peoples Park in the Sky, the highest viewpoint on the Tagaytay ridge. Morning light is the cleanest window before the haze sets in over Taal.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 6. DUMAGUETE HONEYMOON THREE-DAY DAUIN APO SIDE
    // ------------------------------------------------------------------
    'dumaguete-honeymoon-three-day-dauin-apo-side' => [
        [
            'anchor' => '<p>Drop the bags by mid-afternoon. Walk the Rizal Boulevard from the port to the Silliman gates at late afternoon; the boulevard is a one-kilometer seafront promenade that opens to the Cebu Strait, and the late-afternoon light makes the walk easy. The tempura row at the southern end of the boulevard is the local snack stop. Order the squid balls, the kikiam, and a buko juice; eat at the seawall.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/dumaguete-rizal-boulevard.jpg" alt="Rizal Boulevard in Dumaguete at late afternoon" loading="lazy"><figcaption>Rizal Boulevard in Dumaguete, the one-kilometer seafront promenade that runs from the port to the Silliman gates and ends at the tempura row.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Apo Island is the small marine sanctuary 45 minutes by banca from the Malatapay port in Zamboanguita, around an hour south of Dumaguete by van. The island is famous for the turtle population that grazes the seagrass in the shallow eastern bay. Most snorkel days see five to fifteen turtles, sometimes more. The reef wall on the western side has the bigger coral gardens and the deeper water.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/dumaguete-apo-island-marine-sanctuary.jpg" alt="Apo Island marine sanctuary, Negros Oriental" loading="lazy"><figcaption>Apo Island off the southern Negros Oriental coast. The shallow eastern bay grazes five to fifteen turtles on most snorkel days.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Dinner at one of the kitchens along Hibbard Avenue or the boulevard itself. Sans Rival for the silvanas as the dessert stop on the way back is the standard couple finish.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/dumaguete-silvanas.jpg" alt="Sans Rival silvanas, Dumaguete" loading="lazy"><figcaption>Silvanas at Sans Rival, the buttercream-and-cashew-meringue dessert that is the standard Dumaguete couple finish after a Hibbard Avenue dinner.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 7. MACTAN HONEYMOON ISLAND-HOPPING COUPLE THREE-DAY
    // ------------------------------------------------------------------
    'mactan-honeymoon-island-hopping-couple-three-day' => [
        [
            'anchor' => '<p>Drop the bags by midday and skip any tour for the first afternoon. Walk the beach of your resort, swim the shallow lagoon if the tide is in, and nap. Late afternoon, take a tricycle or a Grab to the Mactan Shrine area for the Lapu-Lapu monument and the Magellan marker; the small park has Sutukil row (sugba-tula-kilaw, three Visayan cooking methods) where the kitchens grill, simmer, and ceviche your fish in one order.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/mactan-lapu-lapu-shrine-and-mactan-shrine.jpg" alt="Lapu-Lapu Shrine and Mactan Shrine park" loading="lazy"><figcaption>The Mactan Shrine park, with the Lapu-Lapu monument and the Magellan marker. Sutukil row sits at the edge of the park for the grill-simmer-ceviche fish order.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The sutukil is the cleanest honeymoon dinner on Mactan because the fish is fresh from the market that morning and the seating is open-air. Order a kilo of fish to share, ask the kitchen to do half grilled, quarter kilawin, quarter tinola. Carry small bills.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/mactan-fresh-seafood.jpg" alt="Sutukil fresh seafood platter on Mactan" loading="lazy"><figcaption>A Mactan sutukil platter, with the kilo of fish split half grilled, quarter kilawin, and quarter tinola from the same market run that morning.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Slow breakfast at the resort. By mid-morning, take a Grab into Cebu City (45 minutes off-peak, 70 minutes in traffic) for the heritage triangle: Basilica del Santo Nino, Magellan\'s Cross, and Fort San Pedro. The three sites are within a five-minute walk of each other and the afternoon walk takes around 90 minutes total. Free entry at the cross and the basilica; small admission at Fort San Pedro.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/cebu-city-magellans-cross-and-basilica-del-santo-nino.jpg" alt="Magellans Cross and Basilica del Santo Nino, Cebu City" loading="lazy"><figcaption>Magellans Cross and the Basilica del Santo Nino in Cebu City, two of the three sites in the heritage triangle. The walk between them is paved and shaded.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 8. SAMAL HONEYMOON DAVAO SIDE THREE-DAY QUIET
    // ------------------------------------------------------------------
    'samal-honeymoon-davao-side-three-day-quiet' => [
        [
            'anchor' => '<p>Pearl Farm is the historic pearl-cultivation resort on the southwestern Samal coast, and the day tour with lunch is the way to see the property without staying overnight. The boat leaves from the Davao City pickup point or from a Samal pickup; check the day-tour schedule the week before. The included swim at Malipano cove is the cleanest snorkel water on the Samal side.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/samal-island-pearl-farm-beach-resort.jpg" alt="Pearl Farm Beach Resort on Samal Island" loading="lazy"><figcaption>Pearl Farm on the southwestern Samal coast, the historic pearl-cultivation property with the cleanest snorkel water at Malipano cove.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Dinner at the resort or at a small kitchen along the Penaplata road (the main road on Samal). The Samal seafood is the cleaner version because the gulf is calmer and the fish reaches the kitchen faster.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/samal-island-grilled-tuna-belly.jpg" alt="Grilled tuna belly on Samal Island" loading="lazy"><figcaption>Grilled tuna belly along the Penaplata road on Samal. The gulf is calmer than the Pacific side so the fish reaches the kitchen faster than across Davao.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Order the kinilaw at any Samal kitchen; the Davao version uses tabon-tabon and mango that gives the dish a different finish than the Visayan version. The Davao durian is in season from August to October; even non-durian eaters should try a small portion of the local Davao durian on a Samal weekend, since the variety here is the cleaner one. Bring a long-sleeve rashguard for the Talicud day; the sun on the gulf is stronger than it looks.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/samal-island-kinilaw-na-tuna.jpg" alt="Samal kinilaw na tuna with tabon-tabon" loading="lazy"><figcaption>Samal kinilaw na tuna finished with tabon-tabon and green mango, the Davao-region version that runs lighter than the Visayan coconut-milk style.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 9. TAGAYTAY BABYMOON PREGNANCY-FRIENDLY WEEKEND
    // ------------------------------------------------------------------
    'tagaytay-babymoon-pregnancy-friendly-weekend' => [
        [
            'anchor' => '<p>Late breakfast. By mid-morning, drive to Caleruega in Nasugbu (40 minutes from the ridge). Caleruega is a Dominican retreat house with a small chapel, a garden, and short flat walkways that are easy for a pregnant mom to walk. The site has shaded benches throughout, restroom facilities, and the kind of slow garden pace that the trip is for. Free entry on most days.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/8c/Caleruega_Church_Nasugbu_Batangas.jpg/1280px-Caleruega_Church_Nasugbu_Batangas.jpg" alt="Caleruega chapel and gardens in Nasugbu, Batangas" loading="lazy"><figcaption>Transfiguration Chapel at Caleruega in Nasugbu, with the flat garden walkways and shaded benches that work for a slow second-trimester morning. Photo: Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Mid-morning, a short drive to the Taal viewpoint at People\'s Park or one of the cafes that face the volcano. The viewpoints have paved walking paths and short distances; the People\'s Park summit is a five-minute gentle climb that most second-trimester moms can do without strain.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/a6/Taal_Volcano_aerial.jpg/1280px-Taal_Volcano_aerial.jpg" alt="Taal Volcano view from Tagaytay ridge" loading="lazy"><figcaption>The Taal crater view from the Tagaytay ridge, cleanest in the early morning before the haze rises. The paved viewpoint paths are gentle on second-trimester walks. Photo: Wikimedia Commons.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 10. PANGLAO BABYMOON CALM THREE-DAY PACE
    // ------------------------------------------------------------------
    'panglao-babymoon-calm-three-day-pace' => [
        [
            'anchor' => '<p>The Loboc River lunch cruise is the babymoon-friendly version of the Bohol countryside tour. The floating restaurant is a flat barge that drifts slowly upriver to a small waterfall; the trip is 90 minutes total with a buffet lunch on board. No bumpy roads, no climbing, no boats with rough seas. The river is calm year-round.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/panglao-loboc-river-cruise.jpg" alt="Loboc River floating restaurant lunch cruise" loading="lazy"><figcaption>The Loboc River floating restaurant, a flat barge that drifts upriver to a small falls. The 90-minute cruise has no bumpy roads or rough seas, which suits a second-trimester day.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Pair the cruise with a stop at the Corella tarsier sanctuary (45 minutes from Loboc), the ethical version of the tarsier experience. The walkways are short, flat, and shaded. The tarsiers sleep through most of the day so the visit is quiet by design.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/panglao-tarsier-sanctuary.jpg" alt="Corella tarsier sanctuary in Bohol" loading="lazy"><figcaption>The Corella tarsier sanctuary, the ethical Bohol tarsier visit. Flat shaded walkways and a quiet-by-design experience since the tarsiers sleep through most of the day.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 11. EL NIDO BABYMOON PREGNANCY-FRIENDLY TOUR PICKS
    // ------------------------------------------------------------------
    'el-nido-babymoon-pregnancy-friendly-tour-picks' => [
        [
            'anchor' => '<p>The pregnancy-friendly El Nido tour is Tour D, the half-day boat that hits Cadlao Lagoon and Pasandigan Cove with a beach lunch. The crossing is shorter (45 minutes one way), the lagoon entry is gentle, and the lunch is on a flat sand beach. Book a private banca rather than the shared tour; a private boat gives you control over the pace and lets you skip stops if the seas pick up.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/el-nido-small-lagoon-tour-a.jpg" alt="Small Lagoon entrance near El Nido town" loading="lazy"><figcaption>The Small Lagoon entrance in Bacuit Bay, a calmer kayak swim than the Tour A Big Lagoon and a sample of the half-day Tour D pace.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Most airlines allow pregnancy travel up to 32 weeks without a medical certificate and up to 36 weeks with one. Fly Manila to Lio Airport via AirSWIFT, which lands 10 minutes from El Nido town. The longer Puerto Princesa to El Nido van transfer is five hours and not recommended for second-trimester moms; the AirSWIFT route is the babymoon pick.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/el-nido-lio-estate-lio-beach.jpg" alt="Lio Estate and Lio Beach near El Nido airport" loading="lazy"><figcaption>Lio Estate beachfront, a 10-minute drive from the AirSWIFT terminal. The short transfer is the second-trimester pick over the five-hour Puerto Princesa van road.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 12. LA UNION BABYMOON SLOW COAST WEEKEND
    // ------------------------------------------------------------------
    'la-union-babymoon-slow-coast-weekend' => [
        [
            'anchor' => '<p>Late breakfast. Mid-morning drive to Ma-Cho Temple in San Fernando (20 minutes north). The temple is on a hill with a short climb up the stairs from the parking area; take it slow with frequent rests. The dragon-tiled courtyards and the bay-view balcony are the calm photo spots. Free entry.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/la-union-ma-cho-temple-san-fernando-city.jpg" alt="Ma-Cho Taoist Temple, San Fernando La Union" loading="lazy"><figcaption>Ma-Cho Taoist Temple above San Fernando bay, with the dragon-tiled courtyards and the gentle climb up from the parking that a pregnant mom can pace.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Walk down to the San Fernando city center for lunch at Halo-Halo de Iloko. Order the regular Ilocos halo-halo with pinipig and corn (skip the longganisa ice cream version for a babymoon; the savory experiment can upset a sensitive stomach). Ilocos longganisa for the breakfast the next day as pasalubong.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/la-union-halo-halo.jpg" alt="Ilocos halo-halo at Halo-Halo de Iloko, San Fernando" loading="lazy"><figcaption>Ilocos halo-halo at Halo-Halo de Iloko in San Fernando. The regular pinipig-and-corn version is the safer babymoon order; skip the longganisa-ice-cream experiment.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 13. SENIOR-FRIENDLY TAGAYTAY PACE-CONTROLLED TWO-DAY
    // ------------------------------------------------------------------
    'senior-friendly-tagaytay-pace-controlled-two-day' => [
        [
            'anchor' => '<p>Sky Ranch in the evening is the senior-friendly evening activity. The Ferris wheel ride has a long slow rotation and the seats are comfortable with a clear Taal view at sunset. Skip the swing rides and the roller coasters. The park has paved paths, restrooms, and food kiosks throughout. The senior citizen discount applies at the gate; bring the PWD or senior card.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/tagaytay-sky-ranch.jpg" alt="Sky Ranch Tagaytay with Sky Eye Ferris wheel" loading="lazy"><figcaption>Sky Ranch Tagaytay at evening, with the slow-rotating Sky Eye Ferris wheel that gives seniors the cleanest Taal view without the swing-ride risk.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Late breakfast at the hotel. Mid-morning, drive to People\'s Park in the Sky, the highest viewpoint on the ridge. The walk up from the parking area is short (around 100 meters) and gently paved. There are shaded benches throughout; rest as often as needed. The view of Taal from the summit is the cleanest in the morning before the haze sets in.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c8/People%27s_Park_in_the_Sky%2C_Tagaytay_City%2C_Cavite.jpg/1280px-People%27s_Park_in_the_Sky%2C_Tagaytay_City%2C_Cavite.jpg" alt="Peoples Park in the Sky summit walk, Tagaytay" loading="lazy"><figcaption>The summit path at Peoples Park in the Sky. The 100-meter paved walk has shaded benches throughout, which suits a senior pace and a maintenance-card stop. Photo: Wikimedia Commons.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 14. PWD-ACCESSIBLE MANILA WHEELCHAIR-DOABLE WALK
    // ------------------------------------------------------------------
    'pwd-accessible-manila-wheelchair-doable-walk' => [
        [
            'anchor' => '<p>Start at Fort Santiago, the Spanish colonial citadel at the northwestern tip of Intramuros. The main entrance has a ramp and the inner pathways are paved cobblestone but flat enough for a manual wheelchair with assistance. The Rizal Shrine inside the fort is accessible via a side ramp. Allow two hours for the full visit; the small museum, the dungeon viewpoint, and the Pasig River side are all on the same level.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/manila-intramuros.jpg" alt="Fort Santiago at Intramuros, Manila" loading="lazy"><figcaption>Fort Santiago at the northwestern tip of Intramuros. The main entrance ramp and the flat inner courtyards open a manual-wheelchair route to the Rizal Shrine.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>From Intramuros, hire a wheelchair-friendly tricycle or van for the five-minute ride to Rizal Park. Rizal Park is the most accessible large public space in Manila: wide paved paths throughout, ramps at every level change, accessible restrooms near the National Museum side, and shaded benches. Visit the Rizal Monument, the Chinese Garden, and the Japanese Garden in a two-hour loop. The light is best in late afternoon.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/manila-rizal-park-luneta.jpg" alt="Rizal Park Luneta wide paved paths" loading="lazy"><figcaption>Rizal Park (Luneta), the most wheelchair-friendly large public space in Manila. Wide paved paths, ramps at every level change, and accessible restrooms near the National Museum side.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 15. MULTIGEN FAMILY REUNION TAGAYTAY THREE-DAY
    // ------------------------------------------------------------------
    'multigen-family-reunion-tagaytay-three-day' => [
        [
            'anchor' => '<p>Sunrise breakfast at the house. Mid-morning, the whole family heads to Picnic Grove for the family photo session. Picnic Grove has the iconic ridge view, the cottages for renting, and the wide grass areas where the kids can run. The zip-line and the cable car are extra for the active members; the elders can stay at the cottage with the view.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/4f/Picnic_Grove_Tagaytay_City.jpg/1280px-Picnic_Grove_Tagaytay_City.jpg" alt="Picnic Grove cottages overlooking Taal" loading="lazy"><figcaption>Picnic Grove cottages on the Tagaytay ridge, the standard family-photo cottage with the Taal view and the wide grass area for the kids to run. Photo: Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Split the family into two groups for the day. The kids and the active adults go to Sky Ranch in the morning; the rides, the Ferris wheel, the carousel, and the food strip make a full half-day. The elders and the calmer adults go to Caleruega in Nasugbu for a quiet chapel and garden visit. The two groups meet for lunch back at the house or at a midway restaurant.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/eb/Caleruega_Transfiguration_Chapel_garden.jpg/1280px-Caleruega_Transfiguration_Chapel_garden.jpg" alt="Caleruega chapel and garden in Nasugbu" loading="lazy"><figcaption>The Caleruega garden in Nasugbu, the quieter half of the split-family day while the kids and active adults are at Sky Ranch. Photo: Wikimedia Commons.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 16. SENIOR-FRIENDLY VIGAN HERITAGE PACE TWO-DAY
    // ------------------------------------------------------------------
    'senior-friendly-vigan-heritage-pace-two-day' => [
        [
            'anchor' => '<p>Afternoon calesa tour. The Vigan calesa drivers run a standard six-stop loop: Plaza Salcedo (the dancing fountain), the Vigan Cathedral, Plaza Burgos, the Bantay Bell Tower (drive past the climb; the view from the road is fine for seniors), the Crisologo Museum (ground floor only), and the Hidden Garden (lunch optional, otherwise just a walk through). The driver manages the pace and the carriage has padded seats with rest pillows.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/vigan-vigan-cathedral-and-plaza-salcedo.jpg" alt="Vigan Cathedral and Plaza Salcedo dancing fountain" loading="lazy"><figcaption>Vigan Cathedral and Plaza Salcedo, the second stop on the standard six-stop calesa loop. The plaza has flat paved seating for a senior rest between stops.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Late breakfast at the heritage hotel. Mid-morning walk on Calle Crisologo. The street is closed to vehicles in the morning hours; the cobblestones are uneven so seniors should walk slowly with a cane or a family member\'s arm. Stop at the heritage shops for souvenirs: Ilocos longganisa, sukang Iloko vinegar, tupig (sticky rice grilled in banana leaves), abel Iloko handwoven blankets.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/vigan-calle-crisologo.jpg" alt="Calle Crisologo heritage street, Vigan" loading="lazy"><figcaption>Calle Crisologo in the morning vehicle-free hours. The 500-meter cobblestone is the heritage core; seniors take it slowly with a cane or a family-member arm.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Dinner at the Plaza Burgos food stalls for the empanada and okoy. The plaza has flat paved seating and the empanada is the Vigan signature; order one Vigan empanada per senior and share. Late evening dancing-fountain show at Plaza Salcedo runs at 7:30 PM and 8:30 PM. Free, family-friendly, easy to watch from a paved bench.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/vigan-vigan-empanada.jpg" alt="Vigan empanada at Plaza Burgos food stalls" loading="lazy"><figcaption>Vigan empanada at the Plaza Burgos food stalls, the orange-rice-flour shell with longganisa, egg, and shredded papaya. Easy to eat at the plaza bench seating.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Optional stop at the Pagburnayan pottery workshop, the centuries-old jar-making site on the outskirts of Vigan. The workshop is a single ground-floor area; seniors can sit on a bench and watch the potter shape the burnay jar in 10 minutes. Free entry, small donation for the photo.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/vigan-bantay-bell-tower.jpg" alt="Bantay Bell Tower outside Vigan" loading="lazy"><figcaption>Bantay Bell Tower outside Vigan, the calesa drive-past stop on the senior loop. The view from the road is the senior-safe option; the 90-step climb up the wooden staircase is not.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 17. PWD-ACCESSIBLE BORACAY WHEELCHAIR ROUTES
    // ------------------------------------------------------------------
    'pwd-accessible-boracay-wheelchair-routes' => [
        [
            'anchor' => '<p>Several Station 1 resorts and watersports operators have beach wheelchairs (the wide-balloon-tire kind) that roll on soft sand. Book one for a morning beach walk; the rate is modest and the staff push the chair for guests who cannot self-propel on sand. The wide-tire wheelchair lets you reach the actual waterline, which a standard chair cannot do.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/3/3b/White_Beach%2C_Boracay_morning.jpg/1280px-White_Beach%2C_Boracay_morning.jpg" alt="White Beach Boracay firm sand at low tide" loading="lazy"><figcaption>White Beach Station 1 in the morning low-tide window. The firm sand near the high-tide line is the strip that a beach wheelchair rolls cleanest. Photo: Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Afternoon at D\'Mall. The new D\'Mall promenade has paved walkways throughout, accessible restrooms, and ramps at every corner. The food strip has step-free entry to most kitchens; the shops are mostly accessible. A 90-minute slow loop through D\'Mall is the standard afternoon for PWD travelers; the shaded paths make it easier than the sand walk in midday heat.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/14/D%27Mall_de_Boracay_promenade.jpg/1280px-D%27Mall_de_Boracay_promenade.jpg" alt="DMall de Boracay promenade with paved walkways" loading="lazy"><figcaption>The DMall de Boracay promenade, the post-rehabilitation paved strip with ramps at every corner. A 90-minute slow loop in the shade beats midday sand. Photo: Wikimedia Commons.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 18. KID-FRIENDLY SUBIC THREE-DAY TESTED PLAN
    // ------------------------------------------------------------------
    'kid-friendly-subic-three-day-tested-plan' => [
        [
            'anchor' => '<p>Afternoon at Zoobic Park. Zoobic is the safari-style park where you board a converted jeep with cage screens and drive through a real tiger enclosure. The kids will remember the tiger jeep for years. The rest of the park has the croc walk, the savannah area, and the small petting zoo. Allow three to four hours. Wear closed shoes; the park is uneven in parts.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/subic-tree-top-adventure.jpg" alt="Subic Bay rainforest park with tree-top canopy" loading="lazy"><figcaption>The rainforest canopy in the SBMA where the Zoobic and Tree Top Adventure parks share the same forest spine. Both are inside the freeport gate and easy to chain in one day.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Full day at Ocean Adventure, the marine park on Camayan Point. The day has scheduled shows: the dolphin show in the morning, the sea lion show late morning, the bird show after lunch. Each show is around 25 minutes; the kids love them all. Between shows, walk the open-air tanks: the dolphin viewing tank, the shark tank, the freshwater habitat.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/subic-ocean-adventure.jpg" alt="Ocean Adventure marine park, Camayan Point Subic" loading="lazy"><figcaption>Ocean Adventure on Camayan Point, the marine park where the dolphin show, the sea lion show, and the bird show anchor a full kid-friendly day.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Subic hotels cluster in the boardwalk area near the bay or in the harbor point area. For families, pick a hotel with a pool and kid-friendly food options; the standalone hotels without pools become long evenings with restless kids. Camayan Beach Resort near Ocean Adventure is the closer option if you plan to spend most of the trip at Ocean Adventure.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/subic-camayan-beach-resort.jpg" alt="Camayan Beach Resort near Ocean Adventure" loading="lazy"><figcaption>Camayan Beach Resort on the same point as Ocean Adventure. The protected cove and the resort pool together cover the sea-and-pool needs that restless kids demand.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 19. KID-FRIENDLY TAGAYTAY WEEKEND WITH KIDS
    // ------------------------------------------------------------------
    'kid-friendly-tagaytay-weekend-actually-with-kids' => [
        [
            'anchor' => '<p>Sky Ranch in the afternoon. The park has a kid-friendly ride menu: the carousel, the small Ferris wheel, the bumper cars, the kiddie train, the swing rides, and the giant Ferris wheel (the slow one with the Taal view; safe for kids ages four and up). Allow three to four hours. The park gets crowded by 3 PM on weekends so arrive by 2 PM if possible. Snack options are everywhere; the kids will want cotton candy and ice cream.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/3/3a/Sky_Ranch_Tagaytay_Sky_Eye.jpg/1280px-Sky_Ranch_Tagaytay_Sky_Eye.jpg" alt="Sky Eye Ferris wheel at Sky Ranch Tagaytay" loading="lazy"><figcaption>The Sky Eye giant Ferris wheel at Sky Ranch Tagaytay, the slow rotation that even four-year-olds can sit through with a clear Taal view at the top. Photo: Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Late breakfast at the hotel. Mid-morning visit to Puzzle Mansion, the small museum that houses the world\'s largest collection of completed jigsaw puzzles. Kids love the giant puzzles on the walls; the small puzzle play area at the entrance keeps younger kids busy. Allow 60 to 90 minutes. Small entrance fee, kids get a small discount.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/2b/Puzzle_Mansion_Tagaytay_interior.jpg/1280px-Puzzle_Mansion_Tagaytay_interior.jpg" alt="Puzzle Mansion completed jigsaw wall display" loading="lazy"><figcaption>The Puzzle Mansion in Tagaytay, the Guinness-recognized collection of completed jigsaw puzzles. The walls of finished puzzles keep curious kids engaged for the full hour. Photo: Wikimedia Commons.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 20. KID-FRIENDLY CEBU CITY THREE-DAY FAMILY PLAN
    // ------------------------------------------------------------------
    'kid-friendly-cebu-city-three-day-family-plan' => [
        [
            'anchor' => '<p>Continue to Tops Lookout, the higher viewpoint that gives a 270-degree panorama of Cebu City and the Mactan strait. Tops has paved walkways, a small kids\' play area, and food stalls. Small entrance fee. Allow 90 minutes.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/cebu-city-tops-lookout.jpg" alt="Tops Lookout panorama of Cebu City" loading="lazy"><figcaption>Tops Lookout above Cebu City, the 270-degree panorama down to the Mactan strait. Paved walkways and a small kids play area make it the easy sequel to Sirao.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Lunch at a kid-friendly Cebu kitchen; the Larsian BBQ row is the local favorite but the smoke and crowds can be too much for younger kids. The Original Lechon Belly chain is a safer family option for first-timers.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/cebu-city-lechon.jpg" alt="Cebu lechon platter" loading="lazy"><figcaption>Cebu lechon at one of the Original Lechon Belly branches, the safer family lunch over the smoke-and-elbows of the Larsian BBQ row.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Morning at Cebu Ocean Park near SM Seaside. The park has the underwater tunnel walk, the marine touch pool, the bird show, and the small petting area. Kids love the touch pool the most; supervise so they handle the starfish gently. Allow three to four hours. Pre-book online to skip the gate line.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/52/Cebu_Ocean_Park_underwater_tunnel.jpg/1280px-Cebu_Ocean_Park_underwater_tunnel.jpg" alt="Cebu Ocean Park underwater tunnel walk" loading="lazy"><figcaption>The underwater tunnel at Cebu Ocean Park near SM Seaside. The tunnel walk plus the touch pool covers a kid-paced three-hour morning. Photo: Wikimedia Commons.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 21. KID-FRIENDLY DAVAO THREE-DAY FAMILY PLAN
    // ------------------------------------------------------------------
    'kid-friendly-davao-three-day-family-plan' => [
        [
            'anchor' => '<p>Afternoon at the Davao Crocodile Park near Lanang. The park has the largest crocodile collection in Mindanao, plus a tiger area, a bird show, and a small petting zoo. Allow three to four hours. The crocodile feeding shows happen on schedule; check the timing on arrival.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/davao-city-roxas-avenue-crocodile-park-toril.jpg" alt="Davao Crocodile Park enclosures, Lanang" loading="lazy"><figcaption>The Davao Crocodile Park near Lanang, with the largest crocodile collection in Mindanao plus a tiger area and a bird show in the same gate.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Early breakfast. By 8 AM, drive 45 minutes south to Eden Nature Park in Toril. Eden is the highland nature park that sits at 800 meters elevation, which makes the air cooler than Davao city itself. The park has a kid-friendly menu: the Skyrider zip cable for older kids, the carriage-style cable ride for younger kids, the flower garden walk, the bamboo grove, and the Indigenous village tour. Allow four to five hours. Lunch at the park\'s restaurant.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/davao-city-eden-nature-park.jpg" alt="Eden Nature Park in Toril, Davao" loading="lazy"><figcaption>Eden Nature Park in Toril at 800 meters elevation, cooler than the city below. Flower terraces, the Skyrider cable, and the bamboo grove cover a four-hour kid day.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Afternoon at the Philippine Eagle Center in Calinan, 30 minutes from Eden. The center is the breeding sanctuary for the Philippine eagle, the national bird, and the kids see the eagles up close in the open-flight enclosures. The center also has Mindanao deer, monkeys, and reptiles. Allow 90 minutes.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/davao-city-philippine-eagle-center.jpg" alt="Philippine Eagle Center, Calinan, Davao" loading="lazy"><figcaption>The Philippine Eagle Center in Calinan, the breeding sanctuary for the national bird. The open-flight enclosures put the eagles within meters of the viewing path.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 22. DIGITAL NOMAD EL NIDO ONE-WEEK COWORKING WIFI
    // ------------------------------------------------------------------
    'digital-nomad-el-nido-one-week-coworking-wifi' => [
        [
            'anchor' => '<p>Outpost El Nido in Corong-Corong is the main coworking spot for nomads. Day passes, weekly memberships, and longer-stay rates are available. The setup includes desk space, meeting rooms, espresso, and a backup generator that handles the brownouts. The community side has weekly nomad meetups; useful for the first week if you do not know anyone.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/9c/Corong-Corong_Beach_El_Nido_Palawan.jpg/1280px-Corong-Corong_Beach_El_Nido_Palawan.jpg" alt="Corong-Corong Beach in El Nido, the nomad side" loading="lazy"><figcaption>Corong-Corong Beach south of El Nido town, the nomad-base side. Outpost coworking sits a five-minute walk inland from this stretch. Photo: Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The classic nomad rhythm in El Nido is the early-work-late-swim split. Wake up at 6 AM, coffee at 6:30 AM, hard focus block from 7 AM to 11 AM at the coworking or the cafe. Break for lunch and a midday swim from 11:30 to 2 PM at the town beach or Caalan. Second focus block from 2:30 PM to 5:30 PM. Sunset walk on Las Cabanas at 5:45 PM. Dinner at the town kitchens.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/d/d3/Las_Cabanas_Beach_El_Nido_sunset.jpg/1280px-Las_Cabanas_Beach_El_Nido_sunset.jpg" alt="Las Cabanas Beach sunset, El Nido" loading="lazy"><figcaption>Las Cabanas Beach at 5:45 PM, the nomad sunset stop after the second focus block. The 15-minute tricycle ride from the town center is the daily reset. Photo: Wikimedia Commons.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 23. DIGITAL NOMAD DUMAGUETE ONE-MONTH COWORKING FOOD
    // ------------------------------------------------------------------
    'digital-nomad-dumaguete-one-month-coworking-food' => [
        [
            'anchor' => '<p>Silliman University Library is open to the public for daytime use and has fast wifi, air conditioning, and quiet study areas. For a more nomad-friendly setup, the Mocha Mug, Native Pizza, and Sans Rival Bistro along Hibbard Avenue have laptop-friendly seating, all-day cafe hours, and stable wifi. The cafes are not formal coworking but for a one-month stay the rotation works.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/dumaguete-silliman-university.jpg" alt="Silliman University main hall, Dumaguete" loading="lazy"><figcaption>Silliman University at the north end of Hibbard Avenue. The library is open to the public for daytime use and has the most reliable wifi of any free workspace in Dumaguete.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Cebu Pacific has a 4 PM Manila flight that fits a same-day return.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/dumaguete-sinigang.jpg" alt="Dumaguete sinigang with kamias" loading="lazy"><figcaption>A bowl of Dumaguete sinigang at a Hibbard kitchen, the standard nomad lunch fuel between the morning focus block and the boulevard sunset walk.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Dumaguete has several real gyms: Beverly Hills Fitness, FitnessFirst Dumaguete, and a few smaller boutique gyms in the IT-park area. Monthly memberships are modest. The Rizal Boulevard walk is two kilometers end-to-end, which makes a clean daily walk. For weekend movement, the Casaroro Falls hike (30 minutes from the city) is the standard nomad day-hike.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/dumaguete-casaroro-falls-valencia.jpg" alt="Casaroro Falls in Valencia, near Dumaguete" loading="lazy"><figcaption>Casaroro Falls in Valencia, 30 minutes from Dumaguete and the standard nomad weekend day-hike. The 335-step descent and the swim pool make a clean weekend reset.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 24. DIGITAL NOMAD TAGAYTAY ONE-WEEK COOL AIR
    // ------------------------------------------------------------------
    'digital-nomad-tagaytay-one-week-cool-air-wifi' => [
        [
            'anchor' => '<p>Bag of Beans, Cafe Voi La, Marcia Adams, and the Coffee Project Tagaytay have laptop-friendly seating with stable wifi. The Bag of Beans flagship has outdoor seating with the Taal view; productive but the photo-shoot crowd can be loud on weekends. Marcia Adams has the calmer mid-afternoon hours. For a quieter setup, the Mendez side has several smaller coffee shops with steady wifi and minimal foot traffic.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/7/7e/Bag_of_Beans_Tagaytay_outdoor_seating.jpg/1280px-Bag_of_Beans_Tagaytay_outdoor_seating.jpg" alt="Bag of Beans Tagaytay outdoor seating with Taal view" loading="lazy"><figcaption>Bag of Beans flagship outdoor seating on the ridge. Productive in the early morning before the photo-shoot crowd arrives by mid-morning on weekends. Photo: Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Weekend option: drive down to Nasugbu (one hour) for a Saturday beach day at Calatagan or Burot. Drive back Sunday morning for a slow ridge afternoon.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/0/05/Burot_Beach_Calatagan_Batangas.jpg/1280px-Burot_Beach_Calatagan_Batangas.jpg" alt="Burot Beach in Calatagan, Batangas" loading="lazy"><figcaption>Burot Beach in Calatagan, the Saturday escape one hour down from the Tagaytay ridge. The arc of pebble-and-coarse-sand suits a nomad weekend off the laptop. Photo: Wikimedia Commons.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 25. DIGITAL NOMAD CEBU CITY ONE-MONTH IT PARK
    // ------------------------------------------------------------------
    'digital-nomad-cebu-city-one-month-coworking-food' => [
        [
            'anchor' => '<p>IT Park has multiple formal coworking spaces: Worc, Common Ground, and several others. Day passes, weekly memberships, and monthly memberships are available at modest rates. The spaces include desk space, meeting rooms, espresso, fast wifi, and 24-hour access in some cases. For nomads who prefer cafes, the IT Park has dense cafe coverage: Starbucks, Tim Hortons, Coffee Bean, and several local cafes line the strip.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/45/Cebu_IT_Park_skyline.jpg/1280px-Cebu_IT_Park_skyline.jpg" alt="Cebu IT Park central plaza and towers" loading="lazy"><figcaption>The Cebu IT Park strip, the densest fibre coverage in the Visayas and the home of Worc, Common Ground, and the 24-hour cafe rotation. Photo: Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>For nomads with kitchen access, the Carbon Market is the largest wet market in Cebu and has fresh produce at lowest prices.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/cebu-city-carbon-market.jpg" alt="Carbon Market in Cebu City" loading="lazy"><figcaption>Carbon Market, the largest wet market in Cebu and the cheapest source of weekly produce for nomads cooking out of a serviced apartment.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>For lunch: lechon at any of the original Cebu lechon chains, the Larsian BBQ row for the early-lunch grill stations. For dinner: Lantaw Native Restaurant in Lahug for the city-view setting, Casa Verde for the steaks and ribs, the IT Park kitchens for the international cuisine.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/cebu-city-siomai-sa-tisa.jpg" alt="Siomai sa Tisa Cebu street food" loading="lazy"><figcaption>Siomai sa Tisa, the late-night Cebu street order that pairs with the post-coworking walks home from IT Park. Cheap, fast, and open past midnight.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 26. NAIA T3 TO TAGAYTAY HONEST TRANSPORT READ
    // ------------------------------------------------------------------
    'naia-terminal-3-to-tagaytay-honest-transport-read' => [
        [
            'anchor' => '<p>The point-to-point bus drops at the Tagaytay public terminal in the city center; from there, take a tricycle to your hotel.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/f/f0/NAIA_Terminal_3_curbside.jpg/1280px-NAIA_Terminal_3_curbside.jpg" alt="NAIA Terminal 3 curbside arrivals" loading="lazy"><figcaption>The NAIA Terminal 3 curbside arrivals area. Genesis and DLTB buses load at the dedicated bus bay; Grab pickups happen at the designated zone past the taxi line. Photo: Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>If you have a rental car or your own car at the airport parking, the SLEX-Sta. Rosa-Tagaytay route is the standard. Toll fees apply at the SLEX gates. The Tagaytay road has steady traffic on weekend afternoons; leave by mid-morning or after 8 PM to skip the worst.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/e/e0/SLEX_southbound_traffic.jpg/1280px-SLEX_southbound_traffic.jpg" alt="SLEX southbound traffic between Manila and Sta Rosa" loading="lazy"><figcaption>SLEX southbound between Manila and the Sta Rosa exit, the standard route to the Tagaytay ridge. Mid-morning and after 8 PM are the clear windows. Photo: Wikimedia Commons.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 27. NAIA T1 TO BORACAY CONNECTING FLIGHT READ
    // ------------------------------------------------------------------
    'naia-terminal-1-to-boracay-connecting-flight-read' => [
        [
            'anchor' => '<p>Cebu Pacific and AirAsia Manila-to-Caticlan or Manila-to-Kalibo flights depart from NAIA Terminal 4. Philippine Airlines flights depart from NAIA Terminal 2. The inter-terminal transfer requires a shuttle ride; the NAIA shuttle bus is free and runs in a continuous loop between all four terminals. Walk to the shuttle pickup point on the arrival level and wait.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/8/8e/NAIA_Terminal_1_exterior.jpg/1280px-NAIA_Terminal_1_exterior.jpg" alt="NAIA Terminal 1 international arrivals exterior" loading="lazy"><figcaption>NAIA Terminal 1, the international arrivals terminal in Manila. The shuttle pickup to Terminal 4 (domestic Cebu Pacific) is on the arrival level near the exit doors. Photo: Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>The jetty boat from Caticlan to Cagban port on Boracay is a 15-minute crossing. The fare includes the terminal fee, the environmental fee, and the boat fare. Pay at the ticket counter, then queue for the boarding. Life vests are provided on the boat.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/2/25/Caticlan_Jetty_Port_Boracay.jpg/1280px-Caticlan_Jetty_Port_Boracay.jpg" alt="Caticlan Jetty Port banca crossing to Boracay" loading="lazy"><figcaption>Caticlan Jetty Port, the last link in the NAIA to Boracay chain. The 15-minute banca crossing lands at Cagban port on the south end of the island. Photo: Wikimedia Commons.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 28. CLARK AIRPORT TO SUBIC ZAMBALES TRANSPORT READ
    // ------------------------------------------------------------------
    'clark-airport-to-subic-zambales-transport-read' => [
        [
            'anchor' => '<p>Clark airport is smaller and faster than NAIA. Allow 30 to 45 minutes from landing to exit for international arrivals, less for domestic. The arrival hall has the immigration, baggage claim, and customs check on a single level. The exit doors open to the parking and transport zone.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/6/68/Clark_International_Airport_terminal.jpg/1280px-Clark_International_Airport_terminal.jpg" alt="Clark International Airport terminal exterior" loading="lazy"><figcaption>The Clark International Airport terminal, smaller and faster than NAIA. The arrival hall and the transport curb sit on a single level. Photo: Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Several car rental agencies operate at Clark airport. For travelers planning a multi-stop Zambales loop (Subic, Anawangin, Iba, Capones), self-drive is the most flexible option. The SCTEX and the Subic-Tipo expressway connect smoothly; the Zambales coastal road from Olongapo to Iba is a single highway that is easy to follow.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/subic-anawangin-and-nagsasa-coves-from-pundaquit.jpg" alt="Anawangin Cove off Pundaquit, San Antonio Zambales" loading="lazy"><figcaption>Anawangin Cove from the Pundaquit jump-off in San Antonio, the standard Zambales-loop stop two hours past Clark via SCTEX and the coastal road.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 29. MACTAN-CEBU TO PANGLAO FERRY ONWARD
    // ------------------------------------------------------------------
    'mactan-cebu-to-panglao-ferry-onward-read' => [
        [
            'anchor' => '<p>The fast ferry has air-conditioned seating in tourist and business classes. The crossing takes around 2 hours through the Cebu-Bohol strait. Bring motion-sickness tablets if you are prone; the seas can be choppy during habagat months.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/91/OceanJet_fast_ferry_Cebu_Tagbilaran.jpg/1280px-OceanJet_fast_ferry_Cebu_Tagbilaran.jpg" alt="OceanJet fast ferry between Cebu and Tagbilaran" loading="lazy"><figcaption>An OceanJet fast ferry on the Cebu-Tagbilaran route. The 2-hour crossing has air-conditioned tourist and business class seating. Photo: Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>From Tagbilaran port, take a Grab or a tricycle to your Panglao accommodation. Most Alona-area resorts arrange a pickup at the port for an additional fee. The drive across the two bridges to Panglao takes 30 to 45 minutes depending on the traffic.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/3/3b/Borja_Bridge_Tagbilaran_Panglao.jpg/1280px-Borja_Bridge_Tagbilaran_Panglao.jpg" alt="Borja Bridge connecting Tagbilaran to Panglao" loading="lazy"><figcaption>One of the two short bridges that connect Tagbilaran to Panglao. The drive across to the Alona side takes 30 to 45 minutes depending on the Tagbilaran traffic. Photo: Wikimedia Commons.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 35. LGBT-FRIENDLY PHILIPPINES COUPLE DESTINATIONS
    // ------------------------------------------------------------------
    'lgbt-friendly-philippines-couple-destinations' => [
        [
            'anchor' => '<p>Manila has the largest and most established LGBT scene in the country. Makati, BGC, and Quezon City have dedicated LGBT bars, clubs, and friendly restaurants. The Manila Pride march in June is one of the largest in Southeast Asia. The neighborhood around O Bar Manila in Ortigas is the central nightlife.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/9/97/Metro_Manila_Pride_2019_march.jpg/1280px-Metro_Manila_Pride_2019_march.jpg" alt="Metro Manila Pride march on Marikina Sports Center route" loading="lazy"><figcaption>The Metro Manila Pride march, one of the largest Pride gatherings in Southeast Asia. The June event anchors the LGBT-friendly Philippines calendar. Photo: Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>For same-sex couples planning a honeymoon in the Philippines, the safest picks are El Nido, Boracay (Station 1), Panglao (Alona Beach), Coron, and the resort-side of Cebu (Mactan). These destinations have international-tier resorts with staff trained to handle diverse guests, and the resort grounds offer the privacy that some couples prefer.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/d/dc/Coron_Palawan_island_hopping.jpg/1280px-Coron_Palawan_island_hopping.jpg" alt="Coron, Palawan island lagoon" loading="lazy"><figcaption>Coron in Palawan, one of the international-tier honeymoon picks where the resort grounds offer privacy and staff are trained for diverse couples. Photo: Wikimedia Commons.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 36. VEGAN VEGETARIAN PHILIPPINES BY DESTINATION
    // ------------------------------------------------------------------
    'vegan-vegetarian-philippines-by-destination' => [
        [
            'anchor' => '<p>Several Filipino dishes are vegetable-based or easily adaptable. Pinakbet is the standard Ilocano vegetable stew (eggplant, bitter melon, okra, squash, and string beans); the traditional version uses bagoong (fermented fish), so ask for the no-bagoong version. Lumpiang ubod is a fresh spring roll with heart-of-palm filling; the version at most kitchens is vegetarian.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/0/00/Pinakbet_Ilocano_vegetable_stew.jpg/1280px-Pinakbet_Ilocano_vegetable_stew.jpg" alt="Pinakbet Ilocano vegetable stew" loading="lazy"><figcaption>Pinakbet in its Ilocano version (eggplant, bitter melon, okra, squash, and string beans). Ask for the no-bagoong preparation to keep it vegan. Photo: Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Panglao has Bohol Bee Farm Restaurant, the established vegetarian-friendly farm-to-table kitchen on the island. Several Alona beach kitchens have vegetarian Italian, Thai, and Indian options. The Loboc River cruise buffet includes vegetable dishes that work for vegetarians.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/4b/Bohol_Bee_Farm_organic_garden.jpg/1280px-Bohol_Bee_Farm_organic_garden.jpg" alt="Bohol Bee Farm organic garden on Panglao" loading="lazy"><figcaption>The organic garden at Bohol Bee Farm on Panglao, the established vegetarian-friendly farm-to-table kitchen that supplies its own greens. Photo: Wikimedia Commons.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 37. HALAL PHILIPPINES MUSLIM TRAVELER BY REGION
    // ------------------------------------------------------------------
    'halal-philippines-muslim-traveler-by-region' => [
        [
            'anchor' => '<p>Manila has a growing halal scene driven by Middle Eastern and Asian Muslim visitors. The Quiapo and the Globe Street area near Manila Cathedral have several halal Filipino-Muslim kitchens. The Golden Mosque in Quiapo is the largest mosque in Metro Manila. SM malls and Ayala malls have prayer rooms in most locations.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/4/4e/Golden_Mosque_Quiapo_Manila.jpg/1280px-Golden_Mosque_Quiapo_Manila.jpg" alt="Golden Mosque in Quiapo, Manila" loading="lazy"><figcaption>The Golden Mosque in Quiapo, the largest mosque in Metro Manila. The surrounding Globe Street area has the densest halal Filipino-Muslim kitchen cluster in the capital. Photo: Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Zamboanga has the densest Muslim population in any Philippine city outside Cotabato and the BARMM. Halal food is widely available and the Muslim community is the established cultural majority. The Fort Pilar mosque and the central market area have halal kitchens for every meal. The Sama-Badjao and Tausug culinary traditions are the regional highlights.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/ad/Fort_Pilar_Shrine_Zamboanga.jpg/1280px-Fort_Pilar_Shrine_Zamboanga.jpg" alt="Fort Pilar shrine in Zamboanga City" loading="lazy"><figcaption>Fort Pilar in Zamboanga City. The halal kitchens around the central market and the mosque are walking distance from the shrine. Photo: Wikimedia Commons.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 39. COUPLES THREE-DAY EL NIDO DAY-BY-DAY
    // ------------------------------------------------------------------
    'couples-three-day-el-nido-day-by-day-plan' => [
        [
            'anchor' => '<p>By 4 PM, take a tricycle to Las Cabanas Beach (15 minutes from town). Las Cabanas is the sunset beach of El Nido. Order a couple of drinks from a beachfront bar, watch the sun set across the Bacuit Bay islands, and let the trip start at the right pace. Dinner at Las Cabanas or back in town.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/cc/Las_Cabanas_Beach_El_Nido_Bacuit_Bay.jpg/1280px-Las_Cabanas_Beach_El_Nido_Bacuit_Bay.jpg" alt="Las Cabanas Beach across Bacuit Bay islands" loading="lazy"><figcaption>Las Cabanas Beach in late afternoon, the western El Nido beach where the sun sets behind the Bacuit Bay islands. Tricycle from town is 15 minutes. Photo: Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Sleep in. Late breakfast on the balcony or at a town cafe. By mid-morning, walk to Caalan Beach, a 15-minute slow walk north of town. Caalan is the quietest stretch near the town; the morning light is the cleanest of the day.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/16/Caalan_Beach_El_Nido_morning.jpg/1280px-Caalan_Beach_El_Nido_morning.jpg" alt="Caalan Beach in El Nido at morning" loading="lazy"><figcaption>Caalan Beach in the morning, the quiet stretch a 15-minute walk north of the town beach. The cleanest light window of the El Nido day. Photo: Wikimedia Commons.</figcaption></figure>',
        ],
    ],

    // ------------------------------------------------------------------
    // 40. COUPLES FIVE-DAY BORACAY DAY-BY-DAY
    // ------------------------------------------------------------------
    'couples-five-day-boracay-day-by-day-plan' => [
        [
            'anchor' => '<p>Wake up at 7 AM. Board the private banca by 8:30 AM. The standard couple route hits Crystal Cove for the morning swim, Puka Beach for the long shell-sand walk and lunch, then back to White Beach by mid-afternoon. The private boat (rather than shared) gives you control over the lunch stop and skips the snorkel pairs you do not need.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/spots/boracay-crystal-cove-island.jpg" alt="Crystal Cove island off Boracay" loading="lazy"><figcaption>Crystal Cove island off the south end of Boracay, the standard first stop on a private island-hopping morning before the Puka Beach lunch.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Morning e-trike ride to Mount Luho, the highest viewpoint on Boracay. The 360-degree view of White Beach, Bulabog, and the smaller north coves is the postcard. Allow 90 minutes including travel and the photo time.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/d/d5/Mount_Luho_viewpoint_Boracay.jpg/1280px-Mount_Luho_viewpoint_Boracay.jpg" alt="Mount Luho viewpoint over Boracay" loading="lazy"><figcaption>Mount Luho viewpoint, the highest point on Boracay with the 360-degree view down to White Beach, Bulabog, and the smaller north coves. Photo: Wikimedia Commons.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Continue to the Bulabog Beach side (the east coast). Bulabog is the kiteboarding side during habagat months and the calmer family beach during amihan months. The walk across the island from Station 1 is 15 minutes; the e-trike covers it in five.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="/storage/rg-media/foods/boracay-fresh-seafood.jpg" alt="Boracay fresh seafood platter" loading="lazy"><figcaption>A fresh seafood spread at a Boracay beachfront kitchen, the standard mid-trip dinner after a Mount Luho photo afternoon and a slow Station 1 sunset.</figcaption></figure>',
        ],
        [
            'anchor' => '<p>Wake up at 5 AM for the Bulabog sunrise. The east coast sunrise is the rarely-taken Boracay photo; most visitors only see the White Beach sunset. Walk back across the island to Station 1 for breakfast.</p>',
            'position' => 'after',
            'html' => '<figure class="rg-figure"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/0/08/Bulabog_Beach_Boracay_kiteboarding.jpg/1280px-Bulabog_Beach_Boracay_kiteboarding.jpg" alt="Bulabog Beach east coast Boracay" loading="lazy"><figcaption>Bulabog Beach on the east coast of Boracay, the sunrise side most visitors never see. Walk across from Station 1 is 15 minutes on foot. Photo: Wikimedia Commons.</figcaption></figure>',
        ],
    ],

];
