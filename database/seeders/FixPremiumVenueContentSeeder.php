<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Rewrites content on premium / luxury venue pages whose seeded
 * templates treated them as generic family malls — "family Sunday
 * lunches with kids", "predictable chain quality", "walk-in friendly
 * evenings" — when in reality the venue is upscale and the audience
 * expectations are completely different.
 *
 * Each entry in $premiumVenues maps a slug substring (e.g. "sm-aura",
 * "greenbelt") to a curated short_version + pros_cons + tag_pills
 * override. The seeder finds every food page whose slug contains that
 * substring and replaces the boilerplate with venue-aware copy.
 *
 * Idempotent: re-runs overwrite the override with the same content.
 */
class FixPremiumVenueContentSeeder extends Seeder
{
    private array $premiumVenues = [
        'sm-aura' => [
            'name' => 'SM Aura',
            'short_version' => 'SM Aura is the upscale side of BGC mall life, with steakhouses, omakase counters, and chef-led fine-dining that books out on weekends. Walk-in friendly only at the casual cafes on the lower floors. Plan ahead for the headline restaurants.',
            'pros' => [
                'Date nights and anniversary dinners',
                'Steakhouse, omakase, and fine-dining sit-downs',
                'Quiet sky-lounge views over BGC',
                'Cocktail bars after dinner',
                'Premium chef-led restaurants',
            ],
            'cons' => [
                'Tight-budget eaters and student crowd',
                'Stroller-heavy family lunches',
                'Quick walk-in meals without a booking',
                'Fast-food and chain-only diners',
                'Hole-in-the-wall hunters',
            ],
            'tags' => ['Fine dining', 'steakhouse', 'omakase', 'sky bar', 'chef-led', 'date night'],
            'tip' => 'Book the headline restaurants at SM Aura at least three days out, especially for Friday and Saturday dinner. The luxury floors fill faster than the rest of BGC because the seating is smaller and the regulars hold standing reservations.',
        ],
        'greenbelt' => [
            'name' => 'Greenbelt',
            'short_version' => 'Greenbelt is the Makati luxury mall complex with five buildings of fine-dining, designer-brand storefronts, and the executive lunch crowd. Greenbelt 3 and 5 carry the headline restaurants; Greenbelt 1 keeps the older Filipino institutions.',
            'pros' => [
                'Business lunches and client dinners',
                'Designer-brand shopping breaks',
                'Fine-dining and chef-led restaurants',
                'Cocktail bars and lounges',
                'Greenbelt Park weekend al fresco',
            ],
            'cons' => [
                'Budget eaters and student crowds',
                'Quick fast-food turnaround',
                'Parking on a Friday night',
                'Chain-restaurant-only diners',
            ],
            'tags' => ['Fine dining', 'business lunch', 'cocktail bar', 'designer brands', 'al fresco', 'Makati'],
            'tip' => 'Cross between Greenbelt 3 and 5 via the second-floor walkway during the lunch rush. The ground floor and Greenbelt Park bridge get heavy foot traffic from the office workers, but the upper-floor connector is consistently empty.',
        ],
        'rockwell' => [
            'name' => 'Power Plant Mall',
            'short_version' => 'Power Plant Mall in Rockwell is the quiet upscale option, smaller than Greenbelt but with a tighter tenant mix of chef-led restaurants, premium grocery, and boutique stores. The crowd skews Rockwell residents and the Makati professional set.',
            'pros' => [
                'Quiet weeknight dinners',
                'Chef-led independent restaurants',
                'Premium grocery (Rustan\'s)',
                'Boutique shopping breaks',
                'Cocktail bars without the BGC crowd',
            ],
            'cons' => [
                'Big-group family lunches',
                'Quick fast-food meals',
                'Mall walkers and budget shoppers',
                'Late-night party scene',
            ],
            'tags' => ['Chef-led', 'fine dining', 'cocktail bar', 'Rustan\'s', 'Rockwell', 'quiet dining'],
            'tip' => 'Park on level 5 or 6 if you are eating dinner at Power Plant Mall — the lower decks fill from the cinema crowd by 5:30 PM. Exit through the Rustan\'s side to skip the Friday post-work surge at the main lobby.',
        ],
        'high-street' => [
            'name' => 'Bonifacio High Street',
            'short_version' => 'Bonifacio High Street is the open-air strip at the centre of BGC, fronted by chef-led restaurants, beer gardens, and weekend pop-ups. Less polished than SM Aura but more walkable, and the al fresco seating runs along the lawn.',
            'pros' => [
                'Al fresco dining and beer gardens',
                'Weekend pop-up markets',
                'Walking after-dinner with the BGC strip',
                'Sunday brunch crowd',
                'Open-air late-night spots',
            ],
            'cons' => [
                'Hot daytime weather without shade',
                'Quiet conversation dinners',
                'Cheap-eats budget',
                'Avoiding the BGC office crowd',
            ],
            'tags' => ['Al fresco', 'beer garden', 'BGC strip', 'Sunday brunch', 'pop-up', 'late night'],
            'tip' => 'Sit on the east side of the High Street strip in the late afternoon — the buildings cast shade by 4 PM and you can stretch a long lunch into early dinner without sweating through your shirt.',
        ],
        'shangri-la' => [
            'name' => 'Shangri-La Plaza',
            'short_version' => 'Shangri-La Plaza in Ortigas is the older premium mall on EDSA, anchored by the Rustan\'s flagship and a tight cluster of chef-led restaurants on the Main Wing. The East Wing leans casual; the Main Wing is the fine-dining side.',
            'pros' => [
                'Business lunches in Ortigas',
                'Chef-led restaurants on the Main Wing',
                'Rustan\'s flagship grocery + delicatessen',
                'Cinema with premium dining',
                'Edsa Shangri-La hotel restaurants',
            ],
            'cons' => [
                'Cheap-eats and student crowd',
                'Big mall-rat shopping trips',
                'Late-night party scene',
            ],
            'tags' => ['Fine dining', 'Ortigas', 'Rustan\'s', 'business lunch', 'chef-led', 'Main Wing'],
            'tip' => 'Use the second-floor connector between the Main Wing and East Wing if you are bouncing between cinemas and dinner — the ground floor connector funnels through the Saturday shopping crowd.',
        ],
        'solaire' => [
            'name' => 'Solaire Resort',
            'short_version' => 'Solaire is the integrated resort on the Manila Bay strip with a tight roster of fine-dining restaurants, high-end buffets, and chef-led specialty rooms. Reservation-only on weekends; the entry-level options sit in the Sky Tower side.',
            'pros' => [
                'Special-occasion dinners',
                'Buffet that justifies the price',
                'Chef-led specialty restaurants',
                'After-dinner casino + bar runs',
                'Hotel guests on a long stay',
            ],
            'cons' => [
                'Casual walk-in lunches',
                'Tight-budget eaters',
                'Avoiding the casino crowd',
                'Quick in-and-out meals',
            ],
            'tags' => ['Fine dining', 'buffet', 'chef-led', 'integrated resort', 'special occasion', 'Sky Tower'],
            'tip' => 'Park in the South Wing if you are dining only — the North Wing entrance puts you through the casino floor before you reach the restaurants, and on weekends that adds 10 minutes of walking through slot machines.',
        ],
        'okada' => [
            'name' => 'Okada Manila',
            'short_version' => 'Okada Manila is the resort with the most ambitious restaurant lineup on the Entertainment City strip — Japanese chef counters, Hong Kong-style dim sum, premium grills, and the Garden Wing buffets. Built for special-occasion meals.',
            'pros' => [
                'Special-occasion celebrations',
                'Japanese chef-led counters',
                'Premium buffets and brunches',
                'Cantonese dim sum',
                'Resort-stay long dinners',
            ],
            'cons' => [
                'Casual quick lunches',
                'Tight-budget eaters',
                'Walk-in dining without a booking',
            ],
            'tags' => ['Fine dining', 'omakase', 'dim sum', 'premium buffet', 'Garden Wing', 'special occasion'],
            'tip' => 'The Garden Wing buffet has a quieter window from 2:30 to 4:30 PM on weekdays. The headline counters fill from 12 PM with the hotel guests, but the late-afternoon mid-stretch gives you the full spread without the crowd.',
        ],
        'nustar' => [
            'name' => 'Nustar',
            'short_version' => 'Nustar is the newest integrated resort on the Cebu South Road Properties strip, with chef-led restaurants, premium buffets, and Japanese counters that match the SM Aura tier. Better for special occasions than casual lunches.',
            'pros' => [
                'Special-occasion celebrations',
                'Chef-led Japanese counters',
                'Premium buffet brunches',
                'Hotel-stay long dinners',
            ],
            'cons' => [
                'Casual lunches and walk-ins',
                'Tight-budget eaters',
                'Avoiding the casino crowd',
            ],
            'tags' => ['Fine dining', 'omakase', 'premium buffet', 'integrated resort', 'Cebu SRP', 'special occasion'],
            'tip' => 'Walk through the lobby connector to skip the main entrance crowd on Friday and Saturday nights — the side path through the hotel lobby is consistently emptier than the main casino-floor route.',
        ],
        'conrad-manila' => [
            'name' => 'Conrad Manila',
            'short_version' => 'Conrad Manila is the chef-led restaurant cluster on the SM by the Bay strip, anchored by China Blue (Cantonese) and Brasserie on 3 (buffet). Hotel-restaurant pricing but the food matches independent fine-dining at the same tier.',
            'pros' => [
                'Special-occasion celebrations',
                'Cantonese fine-dining (China Blue)',
                'Premium buffet (Brasserie on 3)',
                'Sky-lobby lounge with bay view',
                'Hotel-stay extended dinners',
            ],
            'cons' => [
                'Quick walk-in lunches',
                'Tight-budget eaters',
                'Casual fast-food cravings',
            ],
            'tags' => ['Fine dining', 'Cantonese', 'premium buffet', 'sky lounge', 'bay view', 'Conrad'],
            'tip' => 'Book China Blue for Saturday lunch instead of dinner. The dim sum rotation is identical but the price drops about 25 percent, and you still get the bay-facing seats.',
        ],
        // ── Mid-tier popular malls ─────────────────────────────────
        'mall-of-asia' => [
            'name' => 'Mall of Asia',
            'short_version' => 'Mall of Asia is the biggest mall in the country, with hundreds of restaurants spread across the Main Mall, North Wing, South Wing, and SM by the Bay. The food ranges from Filipino chains to Japanese counters to the Conrad-side fine-dining cluster.',
            'pros' => [
                'Full-day mall trips with families',
                'Wide cuisine variety in one footprint',
                'Manila Bay sunset al fresco dining',
                'Walk-in friendly weekday lunches',
                'Cinema-and-dinner combos',
            ],
            'cons' => [
                'Quiet conversation-friendly dinners',
                'Avoiding weekend mall crowds',
                'Quick in-and-out meals',
                'Tight-budget fine-dining hunters',
            ],
            'tags' => ['Family mall', 'Manila Bay', 'al fresco', 'SM by the Bay', 'cinema dining', 'group friendly'],
            'tip' => 'Eat at the SM by the Bay restaurants instead of the main mall queues on weekends. Same chains, half the wait, and you get the bay view as a bonus.',
        ],
        'megamall' => [
            'name' => 'SM Megamall',
            'short_version' => 'SM Megamall in Ortigas is one of the biggest mall food halls in Metro Manila, with the Mega Fashion Hall side hosting the chef-led and higher-end picks and Building A staying mid-tier chains. Strong Japanese and Korean presence.',
            'pros' => [
                'Japanese ramen and izakaya picks',
                'Korean BBQ unli sets',
                'Mega Fashion Hall fine-dining',
                'Cinema-and-dinner combos',
                'Ortigas office lunch crowd',
            ],
            'cons' => [
                'Quiet date-night dinners',
                'Avoiding the Fashion Hall crowd',
                'Tight-budget eaters on the upper floors',
                'Hole-in-the-wall hunters',
            ],
            'tags' => ['Mall dining', 'Japanese', 'Korean BBQ', 'Mega Fashion Hall', 'Ortigas', 'office lunch'],
            'tip' => 'Cross from Building A to the Mega Fashion Hall side via the third-floor bridge. The ground-floor connector turns into a bottleneck during the Saturday afternoon mall rush.',
        ],
        'sm-north' => [
            'name' => 'SM North Edsa',
            'short_version' => 'SM North Edsa is the Quezon City flagship mall, with the City Center wing handling mid-tier Filipino chains and Annex housing the K-Town strip of Korean barbecue counters. The mall lives off the heavy QC commuter foot traffic.',
            'pros' => [
                'Korean BBQ unli sets (Annex K-Town)',
                'QC commuter lunches',
                'Family Sunday lunches',
                'Filipino chain quality',
                'Cinema-and-dinner combos',
            ],
            'cons' => [
                'Quiet conversation dinners',
                'Avoiding the EDSA-side crowd',
                'Tight-budget fine-dining hunters',
            ],
            'tags' => ['Korean BBQ', 'K-Town', 'QC commuter', 'family mall', 'Filipino chains', 'Annex'],
            'tip' => 'Park in the Sky Garden deck if you are eating on the Annex side. The City Center decks fill faster from the EDSA-bound shoppers, and the K-Town strip is much closer to the Sky Garden exits.',
        ],
        'glorietta' => [
            'name' => 'Glorietta',
            'short_version' => 'Glorietta is the central Makati mall complex of five linked buildings, with the food halls in Glorietta 2 and 4 anchoring the casual side, and Glorietta 5 carrying the chef-led restaurants the Makati office crowd books for client lunches.',
            'pros' => [
                'Makati office lunches',
                'Chef-led picks in Glorietta 5',
                'Filipino chain food halls (Glorietta 2 and 4)',
                'Cinema-and-dinner combos',
                'Connector walk to Greenbelt for cocktails',
            ],
            'cons' => [
                'Avoiding the Ayala Avenue lunch surge',
                'Hole-in-the-wall hunters',
                'Quiet conversation dinners',
            ],
            'tags' => ['Makati', 'office lunch', 'food hall', 'Glorietta 5', 'cinema dining', 'Ayala Avenue'],
            'tip' => 'Eat at Glorietta 5 between 1:30 and 3 PM on weekdays. The Makati office workers clear out by 1:15 and the room reads like a quiet sit-down lunch instead of the usual cafeteria.',
        ],
        'trinoma' => [
            'name' => 'TriNoma',
            'short_version' => 'TriNoma in Quezon City is the Ayala-built mall across SM North, with a tighter restaurant cluster than its bigger neighbour and a strong showing of chef-led picks on the upper floors. The Mindanao Avenue side reads quieter than the North Avenue main entrance.',
            'pros' => [
                'QC office lunches',
                'Chef-led picks on the upper floors',
                'Cinema-and-dinner combos',
                'Quieter alternative to SM North',
                'Korean and Japanese picks',
            ],
            'cons' => [
                'Casino-floor late-night party crowd',
                'Hole-in-the-wall hunters',
                'Tight-budget fine-dining hunters',
            ],
            'tags' => ['QC', 'Ayala mall', 'chef-led', 'office lunch', 'Korean', 'Japanese'],
            'tip' => 'Enter from the Mindanao Avenue side if you are eating on the upper floors. The North Avenue entrance puts you through the ground-floor cosmetics counters and stretches the walk to the restaurants by 5 minutes.',
        ],
        'festival-mall' => [
            'name' => 'Festival Mall',
            'short_version' => 'Festival Mall in Alabang is the South Metro Manila mall anchor, with the Italian Quarter strip carrying the chef-led picks and the food hall in the Expansion Wing handling the casual chain side. Lighter foot traffic than the Makati malls.',
            'pros' => [
                'Alabang and South Metro office lunches',
                'Italian Quarter chef-led picks',
                'Family Sunday lunches with parking',
                'Cinema-and-dinner combos',
                'Quieter than the Makati malls',
            ],
            'cons' => [
                'Avoiding the Festival fountain crowd',
                'Tight-budget fine-dining hunters',
                'Late-night party scene',
            ],
            'tags' => ['Alabang', 'South Metro', 'Italian Quarter', 'family mall', 'office lunch', 'parking'],
            'tip' => 'Park on Level 4 if you are eating at the Italian Quarter side. The lower decks fill from the cinema and grocery crowd, and the Level 4 deck puts you almost directly above the strip.',
        ],
        'gateway' => [
            'name' => 'Gateway Mall',
            'short_version' => 'Gateway Mall in Cubao is the Araneta City flagship, with the food hall in the Cyberpark side handling the quick chain meals and the upper-floor picks leaning Japanese and Korean. Heavy commuter foot traffic from the LRT-MRT connector.',
            'pros' => [
                'Cubao commuter lunches',
                'Japanese and Korean picks',
                'Cinema-and-dinner combos',
                'Walk-in friendly weekday meals',
                'Quick chain food hall',
            ],
            'cons' => [
                'Quiet date-night dinners',
                'Avoiding the LRT-MRT transfer crowd',
                'Tight-budget fine-dining hunters',
                'Hole-in-the-wall hunters',
            ],
            'tags' => ['Cubao', 'commuter mall', 'Japanese', 'Korean', 'cinema dining', 'Araneta City'],
            'tip' => 'Walk through the Cyberpark side instead of the main mall when you are coming from the LRT-2 station. The food-hall picks are closer to that entrance and you skip the saturday afternoon mall-rat crush.',
        ],
        'robinsons-galleria' => [
            'name' => 'Robinsons Galleria',
            'short_version' => 'Robinsons Galleria in Ortigas is the older mid-tier mall option, with the Veranda al fresco strip hosting the chef-led picks and the food hall in the Movieworld wing carrying the casual chain side. Quieter than Megamall on weekdays.',
            'pros' => [
                'Veranda al fresco dining',
                'Cinema-and-dinner combos',
                'Quieter Ortigas alternative',
                'Walk-in friendly weekday meals',
                'Office lunch crowd from EDSA',
            ],
            'cons' => [
                'Avoiding the Ortigas commuter rush',
                'Tight-budget fine-dining hunters',
                'Late-night party scene',
            ],
            'tags' => ['Ortigas', 'Veranda', 'al fresco', 'mid-tier mall', 'office lunch', 'EDSA'],
            'tip' => 'Eat at the Veranda strip on a weeknight. The al fresco seats stay empty until 7:30 PM because the EDSA crowd clears out earlier than the Makati office workers, and you get a quieter dinner without booking.',
        ],
        'uptown' => [
            'name' => 'Uptown Mall',
            'short_version' => 'Uptown Mall in BGC sits at the north end of the strip, with a tighter premium tenant mix than SM Aura but smaller in scale. Strong Korean BBQ presence, Japanese chef counters, and an outdoor patio that handles the after-work cocktail crowd.',
            'pros' => [
                'BGC north-side office lunches',
                'Korean BBQ unli sets',
                'Japanese chef-led counters',
                'After-work cocktail patio',
                'Quieter than SM Aura',
            ],
            'cons' => [
                'Avoiding the BGC office rush',
                'Tight-budget eaters',
                'Hole-in-the-wall hunters',
            ],
            'tags' => ['BGC', 'Uptown', 'Korean BBQ', 'Japanese', 'cocktail patio', 'office lunch'],
            'tip' => 'Cross to Uptown from One Bonifacio High Street via the second-floor walkway. The ground-floor crossing puts you through the office-worker rush at 5:30 PM, and the upper connector stays empty.',
        ],
        'eastwood' => [
            'name' => 'Eastwood',
            'short_version' => 'Eastwood City in Quezon City is a Megaworld-built BPO township, with the food hall in Eastwood Mall handling the casual chain side and the al fresco strip along the Citywalk catering to the late-night BPO crowd that clocks out at 2 AM.',
            'pros' => [
                'Late-night BPO crowd dining',
                'Al fresco Citywalk strip',
                'Cinema-and-dinner combos',
                'Korean and Japanese picks',
                'Walk-in friendly weeknight meals',
            ],
            'cons' => [
                'Quiet date-night dinners',
                'Avoiding the BPO clock-out surge',
                'Hole-in-the-wall hunters',
            ],
            'tags' => ['QC', 'BPO township', 'Citywalk', 'al fresco', 'late night', 'Korean'],
            'tip' => 'Eat at the Citywalk strip between 4 and 6 PM. The BPO graveyard shift clocks in around 6:30 PM and the strip fills up fast, but the early afternoon window is calm and the kitchens are fresh.',
        ],
        // ── Districts (not malls) ──────────────────────────────────
        'binondo' => [
            'name' => 'Binondo',
            'short_version' => 'Binondo is Manila Chinatown, the oldest Chinatown in the world, with century-old Chinese-Filipino kitchens packed along Ongpin Street, Salazar, and the side alleys off Carvajal. Better for walking food tours than sit-down restaurant meals.',
            'pros' => [
                'Walking Chinese-Filipino food tours',
                'Century-old kitchens (Wai Ying, Bee Tin)',
                'Heritage Cantonese and Hokkien picks',
                'Carvajal alley hawker-style finds',
                'Pre-dawn dim sum runs',
            ],
            'cons' => [
                'Air-conditioned mall comfort',
                'Reservation-friendly fine-dining',
                'Avoiding Ongpin foot traffic',
                'Quick in-and-out meals',
            ],
            'tags' => ['Chinatown', 'Chinese-Filipino', 'Ongpin', 'Carvajal', 'heritage', 'dim sum'],
            'tip' => 'Start at Wai Ying on Benavidez Street for the morning dim sum push, then walk the side streets while the kitchens prep the afternoon. The good carts come out around 3 PM and the lines disappear after 7.',
        ],
        'cubao' => [
            'name' => 'Cubao',
            'short_version' => 'Cubao is the Araneta City district at the LRT-MRT transfer hub, with the Cubao Expo strip hosting the indie restaurants and bars and the Gateway Mall side handling the chain food hall. The mix skews younger and more nightlife-oriented than Quezon City proper.',
            'pros' => [
                'Cubao Expo indie restaurants',
                'Late-night bar food',
                'Commuter convenience (LRT-MRT)',
                'Walk-in friendly weeknight meals',
                'Young creative crowd',
            ],
            'cons' => [
                'Quiet fine-dining nights',
                'Avoiding the Gateway connector crowd',
                'Family Sunday lunches',
            ],
            'tags' => ['Araneta City', 'Cubao Expo', 'indie', 'late night', 'LRT-MRT', 'bar food'],
            'tip' => 'Walk through Cubao Expo on a Thursday or Friday night for the best mix of crowd and turnover. Saturday gets jammed with the EDSA gig-goers and Sunday afternoons skew empty.',
        ],
        'tomas-morato' => [
            'name' => 'Tomas Morato',
            'short_version' => 'Tomas Morato in Quezon City is the long-running food and bar strip, with established casual sit-down restaurants along Sct. Borromeo and Sct. Limbaga and the late-night bar crowd anchoring the Timog Avenue end. Less polished than BGC but more accessible.',
            'pros' => [
                'Casual sit-down dinners',
                'Late-night bar crowd',
                'QC food blogger picks',
                'Walk-in friendly weeknight meals',
                'Long-running Filipino kitchens',
            ],
            'cons' => [
                'Quiet date-night dinners',
                'Avoiding the bar-strip noise',
                'Fine-dining reservation crowd',
            ],
            'tags' => ['Tomas Morato', 'QC', 'late night', 'bar strip', 'Filipino kitchens', 'Timog'],
            'tip' => 'The side streets off Tomas Morato (especially Sct. Borromeo) hide the better long-running picks. The main strip turns over tenants every few years; the side-street kitchens have lasted decades for a reason.',
        ],
        'maginhawa' => [
            'name' => 'Maginhawa Street',
            'short_version' => 'Maginhawa Street in Quezon City is the UP-adjacent food strip stretching about 2 kilometres along V. Luna and Malingap, with a rotating lineup of small Filipino-owned restaurants, cafes, and food parks that the student crowd keeps in business.',
            'pros' => [
                'Walking food tours',
                'Filipino-owned indie restaurants',
                'Student-friendly pricing',
                'Cafe scene with study seating',
                'Late-night merienda finds',
            ],
            'cons' => [
                'Air-conditioned mall comfort',
                'Reservation-system fine-dining',
                'Quick parking near the kitchens',
            ],
            'tags' => ['Maginhawa', 'UP Diliman', 'indie', 'student-friendly', 'food park', 'merienda'],
            'tip' => 'Start your Maginhawa walk from the V. Luna end and work toward Malingap. The early stretch holds the older kitchens that have lasted ten-plus years; the newer cafes and food parks cluster toward the Malingap end.',
        ],
        'burgos-circle' => [
            'name' => 'Burgos Circle',
            'short_version' => 'Burgos Circle is the small BGC restaurant cluster around Forbestown Road, with chef-led picks ringing a central park and a tighter al fresco scene than the larger High Street strip. The crowd skews BGC residents and post-work cocktail meets.',
            'pros' => [
                'Walking distance al fresco dining',
                'Post-work cocktail meets',
                'BGC residents and small groups',
                'Quieter alternative to High Street',
                'Sunday brunch crowd',
            ],
            'cons' => [
                'Big group celebrations',
                'Avoiding the BGC commuter walk',
                'Tight-budget eaters',
            ],
            'tags' => ['BGC', 'Burgos Circle', 'al fresco', 'cocktail', 'Sunday brunch', 'walking distance'],
            'tip' => 'Walk the perimeter of the Circle once before picking a restaurant. The tenant mix rotates more than High Street, and the new spots are often the better choice over the longer-running ones.',
        ],
        // ── Cities and provincial spots ────────────────────────────
        'tagaytay' => [
            'name' => 'Tagaytay',
            'short_version' => 'Tagaytay sits on the Taal volcano ridge, two hours from Manila, with the Aguinaldo Highway strip lined by bulalo houses, overlooking restaurants, and the weekend escape crowd from Metro Manila. The food is rib-sticking Filipino comfort eats, not fine dining.',
            'pros' => [
                'Bulalo and Filipino comfort eats',
                'Taal Lake overlooking views',
                'Weekend escape from Manila',
                'Cool-weather al fresco dining',
                'Family Sunday lunches with parking',
            ],
            'cons' => [
                'Quick weeknight Manila dinners',
                'Avoiding the weekend Aguinaldo traffic',
                'Fine-dining reservation crowd',
            ],
            'tags' => ['Tagaytay', 'bulalo', 'overlooking', 'Taal Lake', 'weekend escape', 'cool weather'],
            'tip' => 'Leave Manila before 6 AM on Saturday to skip the Aguinaldo Highway jam. The early arrivals get the morning bulalo at the best ridge-side spots before the crowd hits, and you can be back home by 4 PM.',
        ],
        'baguio' => [
            'name' => 'Baguio',
            'short_version' => 'Baguio sits in the Cordillera mountains, six hours from Manila, with the Session Road strip and the Camp John Hay enclave holding the long-running Filipino kitchens and the cool-weather al fresco picks the regulars come back for.',
            'pros' => [
                'Cool-weather al fresco dining',
                'Camp John Hay garden restaurants',
                'Cordillera highland produce',
                'Weekend escape from Manila',
                'Long-running Filipino institutions',
            ],
            'cons' => [
                'Quick Manila weeknight dinners',
                'Avoiding Session Road peak hours',
                'Late-night dining (city sleeps early)',
            ],
            'tags' => ['Baguio', 'Cordillera', 'Camp John Hay', 'Session Road', 'cool weather', 'highland produce'],
            'tip' => 'Eat dinner before 8 PM in Baguio. The city sleeps early, especially mid-week, and most kitchens close by 9 PM. The exceptions are the bar-restaurants along Session Road extension and the hotel rooms.',
        ],
        'cebu' => [
            'name' => 'Cebu',
            'short_version' => 'Cebu City is the queen city of the South, with a food scene split between heritage Filipino-Chinese kitchens in the downtown core, chef-led restaurants in the Cebu IT Park area, and the lechon institutions the city is famous for nationally.',
            'pros' => [
                'Cebu lechon (Zubuchon, House of Lechon)',
                'IT Park chef-led picks',
                'Heritage Filipino-Chinese kitchens',
                'Sutukil seafood by the pier',
                'Walking food tours downtown',
            ],
            'cons' => [
                'Reservation-system fine-dining',
                'Air-conditioned mall comfort',
                'Avoiding rush-hour MJ Cuenco crowd',
            ],
            'tags' => ['Cebu City', 'lechon', 'IT Park', 'sutukil', 'heritage', 'Filipino-Chinese'],
            'tip' => 'Eat the lechon at a Cebu-only branch instead of the Manila outposts. Zubuchon Mango Square, House of Lechon Acacia, CnT Lechon at the original Mabolo store all carry the better skin than the BGC and Makati branches.',
        ],
        'davao' => [
            'name' => 'Davao',
            'short_version' => 'Davao City is the southern capital, with a casual Filipino food scene anchored by seafood from the Davao Gulf and the durian-and-mangosteen produce strip along Roxas Avenue. The local kitchens lean fresh-catch grilling over chef-led fine dining.',
            'pros' => [
                'Seafood grills (tuna belly, kinilaw)',
                'Davao tropical fruit picks',
                'Casual Filipino dinners',
                'Pier-side fresh-catch picks',
                'Family Sunday lunches',
            ],
            'cons' => [
                'Reservation-system fine-dining',
                'Air-conditioned mall comfort',
                'Late-night dining (city sleeps early)',
            ],
            'tags' => ['Davao', 'seafood', 'tuna belly', 'kinilaw', 'durian', 'Mindanao'],
            'tip' => 'Eat the tuna kinilaw at a pier-side carinderia, not the air-conditioned mall versions. The Davao Gulf catch arrives at the wharves by 5 AM and the carinderias serve it by 8; by mid-afternoon the mall kitchens are buying the day-old cut.',
        ],
        'iloilo' => [
            'name' => 'Iloilo',
            'short_version' => 'Iloilo City is the heart of Western Visayas, with a heritage Filipino food scene anchored by La Paz Batchoy noodle houses (Ted\'s, Deco\'s) and the Calle Real district holding the long-running Filipino institutions the regulars trust.',
            'pros' => [
                'La Paz Batchoy noodle houses',
                'Calle Real heritage Filipino picks',
                'Ilonggo seafood and grills',
                'Walking food tours downtown',
                'Casual Filipino dinners',
            ],
            'cons' => [
                'Air-conditioned mall comfort',
                'Reservation-system fine-dining',
                'Avoiding the La Paz Public Market crowd',
            ],
            'tags' => ['Iloilo', 'La Paz Batchoy', 'Calle Real', 'Ilonggo', 'heritage', 'Visayas'],
            'tip' => 'Eat the La Paz Batchoy at the La Paz Public Market spots first, then compare to Ted\'s and Deco\'s. The market versions are smaller bowls but the broth is reduced longer and the toppings are cut fresh.',
        ],
        'boracay' => [
            'name' => 'Boracay',
            'short_version' => 'Boracay Island is the white-sand beach destination, with the D\'Mall strip on White Beach Station 2 anchoring the tourist-aimed restaurants and the side streets off Station 1 and Station 3 holding the long-running local picks that the regulars stick with.',
            'pros' => [
                'Beachfront sunset dinners',
                'Fresh seafood grills',
                'Tourist-friendly variety',
                'D\'Mall walking-distance dining',
                'Late-night bar food',
            ],
            'cons' => [
                'Avoiding the Station 2 sunset crowd',
                'Quick Manila-pricing meals',
                'Reservation-system fine-dining',
            ],
            'tags' => ['Boracay', 'White Beach', 'beachfront', 'seafood', 'sunset', 'D\'Mall'],
            'tip' => 'Eat dinner at Station 3 instead of Station 2. The food is similar but the prices drop about 20 percent and the sunset views from the beach are identical because White Beach faces west the entire length.',
        ],
        'la-union' => [
            'name' => 'La Union',
            'short_version' => 'La Union is the surf capital of the Philippines, with the San Juan strip anchored by surf hostels, kombi cafes, and beachfront kitchens that lean Filipino-California fusion. The crowd skews young surfers and weekend Manila escapes.',
            'pros' => [
                'Beachfront surf-town dining',
                'Kombi cafe brunch culture',
                'Filipino-California fusion',
                'Surf school lunch breaks',
                'Sunset cocktail patio',
            ],
            'cons' => [
                'Reservation-system fine-dining',
                'Air-conditioned mall comfort',
                'Big group family lunches',
            ],
            'tags' => ['La Union', 'San Juan', 'surf town', 'kombi cafe', 'fusion', 'beachfront'],
            'tip' => 'Eat at the kombi cafes for breakfast and the kitchens off the highway for dinner. The strip-front restaurants charge a surf-town premium and the better long-running picks sit on the second row, one block back from the sand.',
        ],
        'el-nido' => [
            'name' => 'El Nido',
            'short_version' => 'El Nido is the Palawan island-hopping town, with the Calle Hama strip in the town proper hosting the tourist-aimed restaurants and the Lio Beach area holding the higher-end resort-restaurant cluster. Fresh seafood and Filipino-Italian fusion lead the menus.',
            'pros' => [
                'Fresh-catch seafood grills',
                'Calle Hama walking-distance dining',
                'Filipino-Italian fusion',
                'Lio Beach resort-restaurant picks',
                'Sunset beachfront dinners',
            ],
            'cons' => [
                'Avoiding the high-season Calle Hama crowd',
                'Manila-priced fine-dining',
                'Late-night dining (town sleeps early)',
            ],
            'tags' => ['El Nido', 'Palawan', 'Calle Hama', 'Lio Beach', 'fresh catch', 'Filipino-Italian'],
            'tip' => 'Eat at the Calle Hama backstreets for the best price-to-quality. The seaside strip charges a tourist tax of 30 to 40 percent over the kitchens one block inland, where the locals and the long-stay travellers eat.',
        ],
        // ── District and city default catchalls ──────────────────
        'makati' => [
            'name' => 'Makati',
            'short_version' => 'Makati is the financial district anchored by Ayala Avenue, with Greenbelt and Glorietta carrying the polished mall dining and Poblacion (Don Pedro / Polaris) hosting the late-night cocktail bars and indie restaurants that the BGC residents cross over for.',
            'pros' => [
                'Business district lunches',
                'Cocktail bars in Poblacion',
                'Greenbelt fine-dining cluster',
                'Walking-distance dining',
                'Office tower client dinners',
            ],
            'cons' => [
                'Avoiding the Ayala Avenue lunch surge',
                'Tight-budget eaters',
                'Big family Sunday lunches',
            ],
            'tags' => ['Makati', 'Ayala Avenue', 'Poblacion', 'business district', 'cocktail bar', 'office lunch'],
            'tip' => 'For evening dinners, cross from Greenbelt to Poblacion on foot via Salcedo Park. It looks like a longer walk on the map but cuts ten minutes off the cab ride during the 7 to 9 PM Makati rush.',
        ],
        'quezon-city' => [
            'name' => 'Quezon City',
            'short_version' => 'Quezon City is the biggest metro-Manila local-government area by population, with food districts split between Tomas Morato (bars and casual sit-downs), Maginhawa (indie student-friendly), and the mall clusters in Trinoma and SM North. The character changes every neighbourhood.',
            'pros' => [
                'Indie restaurants in Maginhawa',
                'Bar strip on Tomas Morato',
                'Mall food halls (Trinoma, SM North)',
                'Late-night dining',
                'Student-friendly pricing',
            ],
            'cons' => [
                'Avoiding the EDSA-Quezon-Ave traffic',
                'Reservation-system fine-dining',
                'Single-area dining (the food is spread out)',
            ],
            'tags' => ['QC', 'Tomas Morato', 'Maginhawa', 'Trinoma', 'SM North', 'indie'],
            'tip' => 'Pick one QC neighbourhood per trip. The good picks are spread across Tomas Morato, Maginhawa, BF Homes, and the mall clusters, and bouncing between them eats two to three hours in Saturday traffic.',
        ],
        'alabang' => [
            'name' => 'Alabang',
            'short_version' => 'Alabang in Muntinlupa is the South Metro Manila hub, with Festival Mall and Alabang Town Center anchoring the mall dining and the Molito complex holding the chef-led picks. The crowd skews families and the South Metro office workers.',
            'pros' => [
                'South Metro family lunches',
                'Molito chef-led picks',
                'Cinema-and-dinner combos',
                'Quieter than the BGC mall strip',
                'Parking that actually fits',
            ],
            'cons' => [
                'Avoiding the Festival fountain weekend crowd',
                'Walk-in late-night dining',
                'Tight-budget fine-dining hunters',
            ],
            'tags' => ['Alabang', 'South Metro', 'Festival Mall', 'Molito', 'family dining', 'parking'],
            'tip' => 'Eat dinner at Molito instead of Festival Mall on weekends. The chef-led picks in the Molito complex have better walk-in availability and the parking lots are less of a queue than the Festival decks.',
        ],
    ];

    public function run(): void
    {
        $pages = DB::table('rg_seo_pages as p')
            ->join('rg_keywords as k', 'k.id', '=', 'p.keyword_id')
            ->where('k.category', 'food')
            ->select('p.id as page_id', 'k.slug as keyword_slug')
            ->get();

        $stats = ['pages' => 0, 'short' => 0, 'pros' => 0, 'tags' => 0, 'tip' => 0];

        foreach ($pages as $page) {
            $venue = null;
            foreach ($this->premiumVenues as $needle => $bundle) {
                if (str_contains($page->keyword_slug, $needle)) {
                    $venue = $bundle;
                    break;
                }
            }
            if (!$venue) continue;

            $stats['pages']++;
            $this->applyVenueOverrides($page->page_id, $venue, $stats);
        }

        $this->command->info("Premium venue pages updated: {$stats['pages']}");
        $this->command->info("  short_version rewritten: {$stats['short']}");
        $this->command->info("  pros_cons rewritten: {$stats['pros']}");
        $this->command->info("  tag_pills rewritten: {$stats['tags']}");
        $this->command->info("  local_tip rewritten: {$stats['tip']}");
    }

    private function applyVenueOverrides(int $pageId, array $venue, array &$stats): void
    {
        DB::transaction(function () use ($pageId, $venue, &$stats) {
            // short_version — overwrite first (or only) one.
            $sv = DB::table('rg_content_blocks')
                ->where('owner_type', 'seo_page')
                ->where('owner_id', $pageId)
                ->where('block_type', 'short_version')
                ->orderBy('sort_order')
                ->first();
            if ($sv) {
                DB::table('rg_content_blocks')->where('id', $sv->id)->update([
                    'payload_json' => json_encode([
                        'eyebrow' => 'The short version',
                        'body' => $venue['short_version'],
                        'accent_color' => 'amber',
                    ]),
                    'updated_at' => now(),
                ]);
                $stats['short']++;
            }

            // pros_cons — overwrite first one (drop any extras).
            $pcs = DB::table('rg_content_blocks')
                ->where('owner_type', 'seo_page')
                ->where('owner_id', $pageId)
                ->where('block_type', 'pros_cons')
                ->orderBy('sort_order')
                ->get();
            if ($pcs->isNotEmpty()) {
                $first = $pcs->shift();
                DB::table('rg_content_blocks')->where('id', $first->id)->update([
                    'payload_json' => json_encode([
                        'pros_label' => 'Best for',
                        'cons_label' => 'Skip if',
                        'pros' => $venue['pros'],
                        'cons' => $venue['cons'],
                    ]),
                    'updated_at' => now(),
                ]);
                foreach ($pcs as $extra) {
                    DB::table('rg_content_blocks')->where('id', $extra->id)->delete();
                }
                $stats['pros']++;
            }

            // tag_pills — overwrite first.
            $tp = DB::table('rg_content_blocks')
                ->where('owner_type', 'seo_page')
                ->where('owner_id', $pageId)
                ->where('block_type', 'tag_pills')
                ->orderBy('sort_order')
                ->first();
            if ($tp) {
                $palettes = ['amber', 'rose', 'emerald', 'indigo', 'pink', 'cyan', 'violet', 'slate'];
                $items = [];
                foreach ($venue['tags'] as $i => $t) {
                    $items[] = ['text' => $t, 'color' => $palettes[$i % count($palettes)]];
                }
                DB::table('rg_content_blocks')->where('id', $tp->id)->update([
                    'payload_json' => json_encode([
                        'label' => 'What you will find',
                        'items' => $items,
                    ]),
                    'updated_at' => now(),
                ]);
                $stats['tags']++;
            }

            // local_tip — overwrite the FIRST one (canonical), drop extras.
            $lts = DB::table('rg_content_blocks')
                ->where('owner_type', 'seo_page')
                ->where('owner_id', $pageId)
                ->where('block_type', 'local_tip')
                ->orderBy('sort_order')
                ->get();
            if ($lts->isNotEmpty()) {
                $first = $lts->shift();
                DB::table('rg_content_blocks')->where('id', $first->id)->update([
                    'payload_json' => json_encode([
                        'eyebrow' => 'Local tip from ' . $venue['name'],
                        'body' => $venue['tip'],
                        'color' => 'amber',
                    ]),
                    'updated_at' => now(),
                ]);
                foreach ($lts as $extra) {
                    DB::table('rg_content_blocks')->where('id', $extra->id)->delete();
                }
                $stats['tip']++;
            }
        });
    }
}
