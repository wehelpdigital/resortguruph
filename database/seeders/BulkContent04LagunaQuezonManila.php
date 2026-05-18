<?php

namespace Database\Seeders;

class BulkContent04LagunaQuezonManila extends BulkContentBase
{
    protected function pages(): array
    {
        return [
            'resort-in-pansol' => $this->build(
                'Resort in Pansol: Hot Spring Pool Rentals That Built Laguna\'s Reputation',
                '<p>Pansol is the most famous hot spring barangay in Calamba, Laguna, and arguably the busiest weekend resort destination outside Metro Manila. A <strong>resort in Pansol</strong> almost always means a private pool rental fed by natural geothermal water from Mount Makiling. Your group rents the whole property for 22 hours, swims in the warm water, sings karaoke until late, and heads home.</p><p>Travel time from QC is around 75 to 90 minutes via SLEX exit at Calamba.</p>',
                '<h2>How Pansol rentals work</h2><p>You book the entire property. No mixing with strangers. The standard rental runs noon to noon or 2 PM to 12 noon. Pool water is naturally heated from the springs, with temperatures around 35 to 40 degrees Celsius in unmixed pools. Most properties have at least one cool pool for balance.</p><p>Outside food is welcome at nearly every Pansol resort. Reunions often bring lechon, kare-kare, and a coordinator who handles the food. Videoke is standard and included in most rentals.</p><h2>Choosing the right size</h2><p>Small properties for 10 to 15 pax: 6,000 to 12,000 PHP per full day. Mid-size for 20 to 35 pax: 12,000 to 22,000 PHP. Large estate rentals for 40+ pax: 22,000 to 50,000 PHP. Weekend rates run 25 to 40 percent higher than weekday rates.</p><h2>Best season</h2><p>Year-round, but most popular November to April. The warm-water pools are particularly enjoyable in cooler December and January nights. Holy Week is the peak booking week and fills up six to eight weeks ahead.</p>',
                [
                    ['question' => 'Are Pansol resorts safe for kids?', 'answer' => 'The water is naturally hot. Always test temperature before letting kids in. Pick resorts with at least one cool pool or one with controlled water mixing.'],
                    ['question' => 'Can I book just for a few hours?', 'answer' => 'Most properties run 22-hour rentals only. Half-day rentals exist at a few resorts but are less common.'],
                    ['question' => 'What is included in the rental?', 'answer' => 'Standard inclusions: pool, function area, kitchen access, parking, videoke, basic utensils, sometimes a basketball court or game area.'],
                    ['question' => 'How early should I book?', 'answer' => 'Three to four weeks for normal weekends. Six to eight weeks for Holy Week, Christmas, and long weekends.'],
                ],
                'Find a resort in Pansol Laguna for hot spring pool rentals. Private 22-hour bookings, reunion-ready, all-inclusive videoke. Compare picks here.',
            ),

            'resort-in-calamba-laguna' => $this->build(
                'Resort in Calamba Laguna: Hot Springs, Family Pools, and the Pansol District',
                '<p>Calamba is the largest city in Laguna and the gateway to the famous hot spring resorts. A <strong>resort in Calamba Laguna</strong> can mean a Pansol private pool rental, a family inclusive resort like Splash Mountain or 88 Hotspring, or a quieter inn near the historic Rizal Shrine. The city has the most resort variety of any Laguna town.</p><p>Travel time from QC is around 75 minutes via SLEX. Calamba is the first exit after Eton City heading south.</p>',
                '<h2>What you can pick in Calamba</h2><p><strong>Pansol private rentals</strong>: the famous format, book the whole property for your group. Best for reunions and big barkadas.</p><p><strong>Inclusive day resorts</strong>: Splash Mountain, 88 Hotspring, and similar properties with paid-entry, multiple pools, slides, and food courts. Best for first-timers and families with younger kids.</p><p><strong>Business hotels</strong>: smaller properties near the JP Rizal Highway, useful for overnight stays without the pool-party scene.</p><h2>Pricing</h2><p>Inclusive resort entry: 800 to 1,800 PHP per head. Private pool villa rental: 8,000 to 25,000 PHP for the full day. Business hotel rooms: 2,200 to 4,800 PHP per night.</p>',
                [
                    ['question' => 'Should I pick Pansol private or an inclusive resort?', 'answer' => 'Pansol for big groups (15+) who want privacy. Inclusive resorts for smaller families (4 to 8) who prefer ready-made amenities.'],
                    ['question' => 'How are the hot springs naturally heated?', 'answer' => 'Geothermal activity from Mount Makiling. Most pools are warm year-round.'],
                    ['question' => 'Are there overnight hotel options in Calamba proper?', 'answer' => 'Yes, several mid-range hotels along the JP Rizal Highway. Useful as a base if you plan to day-trip to multiple Laguna towns.'],
                    ['question' => 'When does Calamba traffic peak?', 'answer' => 'Saturday morning from 8 to 11 AM and Sunday return from 4 to 8 PM. The SLEX Calamba exit can get backed up by 2 km.'],
                ],
                'Find a resort in Calamba Laguna. Pansol hot springs, inclusive family resorts, and business hotels compared.',
            ),

            'resort-in-nagcarlan-laguna' => $this->build(
                'Resort in Nagcarlan Laguna: Cooler Weather and Quiet Lakeside Retreats',
                '<p>Nagcarlan sits in the southern part of Laguna, at higher elevation than Calamba. A <strong>resort in Nagcarlan Laguna</strong> tends to be smaller and quieter than the famous Pansol crowd. The town is known for the Nagcarlan Underground Cemetery historical site and several small private villa rentals tucked into the upland barangays.</p><p>Travel time from QC is around 2 to 2.5 hours via SLEX and the road through Calauan and Liliw.</p>',
                '<h2>What kind of property to expect</h2><p>Small private pool rentals on farmland or upland lots, a few boutique farm stays, and rural inns. Hot spring water is uncommon here compared to Pansol. Most pools are regular-temperature swim pools with shaded cottages and home-cooked Filipino meals.</p><h2>What pairs well with the stay</h2><p>The Nagcarlan Underground Cemetery is the only one of its kind in the Philippines and a quick visit on the way in or out. Liliw, the slipper-shopping town, is 20 minutes away. San Pablo and its Seven Lakes are 30 minutes east.</p><h2>Pricing</h2><p>Private pool villa rentals: 5,000 to 14,000 PHP for the full 22 hours. Boutique farm stay overnight rooms: 2,200 to 4,500 PHP per night. Most properties allow outside food without corkage.</p>',
                [
                    ['question' => 'Is Nagcarlan cooler than Pansol?', 'answer' => 'Yes, by 2 to 4 degrees on average because of the elevation. Mornings in December can be jacket weather.'],
                    ['question' => 'Are there hot springs in Nagcarlan?', 'answer' => 'Rarely. Hot springs are mostly in the Calamba-Los Baños corridor. Nagcarlan pools are regular temperature.'],
                    ['question' => 'Is it worth visiting over Pansol?', 'answer' => 'For quieter, cooler weekends with a slower vibe, yes. For party-style reunions, stick to Pansol.'],
                    ['question' => 'Combine with which other Laguna town?', 'answer' => 'Liliw for slippers, San Pablo for the Seven Lakes, and Pagsanjan for the falls boat ride. All within 30 to 45 minutes.'],
                ],
                'Discover a resort in Nagcarlan Laguna. Cooler upland villas, farm retreats, and underground cemetery side trips. Compare picks here.',
            ),

            'resort-in-san-pablo-laguna' => $this->build(
                'Resort in San Pablo Laguna: Seven Lakes Country with Boutique Stays',
                '<p>San Pablo is the city of seven lakes, all of them crater lakes formed by ancient volcanic activity. A <strong>resort in San Pablo Laguna</strong> usually means a lakeside boutique stay, a hot spring resort, or a private pool villa in the upper barangays. The town is one of the more interesting weekend destinations in Laguna for travellers who want nature plus history.</p><p>Travel time from QC is around 2 hours via SLEX and the JP Rizal Highway.</p>',
                '<h2>The Seven Lakes</h2><p>Sampaloc Lake is the biggest and most accessible, sitting right beside the city proper. Pandin and Yambo are the picturesque twin lakes you reach via tricycle and a short walk. Bunot, Mojicap, Palakpakin, and Calibato round out the set. Most lakes have bangka tours and floating dining stations.</p><h2>Resort styles</h2><p>Lakeside boutique inns at Sampaloc and Pandin. Hot spring properties along the Calamba border. Private pool villas in the upland barangays. Farm stays and orchard retreats on the outskirts.</p><h2>Pricing</h2><p>Lakeside boutique rooms: 2,500 to 5,500 PHP per night. Hot spring resort entry: 600 to 1,400 PHP per head. Private villa rentals: 8,000 to 18,000 PHP for the full day. Pandin Lake bangka tour: 1,500 to 2,500 PHP per group of 8 to 10.</p>',
                [
                    ['question' => 'Which lake should I visit first?', 'answer' => 'Pandin for the bangka float-and-eat experience. Sampaloc for ease and the city-side dining strip.'],
                    ['question' => 'Can I swim in the lakes?', 'answer' => 'Yes at certain lakes including Pandin and Yambo. The tour operators provide lifevests and supervise.'],
                    ['question' => 'Is San Pablo good for couples?', 'answer' => 'Very. The boutique inns and lakeside dining suit quieter trips. Less of a barkada-resort vibe than Pansol.'],
                    ['question' => 'When is the best time to visit?', 'answer' => 'November to May for the dry weather. The lakes look best on clear sunny mornings.'],
                ],
                'Find a resort in San Pablo Laguna with seven lakes country, boutique inns, hot spring stays, and lakeside dining. Compare picks here.',
            ),

            'resort-in-quezon' => $this->build(
                'Resort in Quezon: From Coastal Lucena to Highland Sariaya and Beyond',
                '<p>Quezon province stretches along the eastern coast of Luzon and packs a surprising variety of resorts. A <strong>resort in Quezon</strong> can mean a beach property in Pagbilao or Atimonan, a heritage-themed inn in Sariaya, a coastal getaway in Real, or a small inland villa near Mount Banahaw. The province is large enough that pick-by-town is the only useful filter.</p><p>Travel time from QC ranges from 3 hours (Lucena) to 5 hours (Aurora-bordering towns).</p>',
                '<h2>Quezon\'s resort clusters</h2><p><strong>Lucena and Tayabas</strong>: business hotels, day-use pool resorts, and the historic Casa Comunidad area.</p><p><strong>Sariaya</strong>: heritage stays and farm retreats with cooler weather.</p><p><strong>Pagbilao and Atimonan</strong>: beach resorts on the Pacific side.</p><p><strong>Real and Infanta</strong>: surf-friendly coastal stays facing Aurora province.</p><p><strong>Lucban</strong>: home of the Pahiyas Festival and small heritage inns.</p><h2>What to budget</h2><p>Business hotel rooms in Lucena: 2,500 to 5,500 PHP. Beachfront resort rooms: 3,500 to 8,500 PHP. Heritage stay rooms: 2,800 to 6,500 PHP. Private villa rentals: 7,000 to 20,000 PHP for the full day.</p>',
                [
                    ['question' => 'Which town in Quezon is most accessible?', 'answer' => 'Lucena, the capital. Tayabas is right next door and has more historical character.'],
                    ['question' => 'Are the beaches in Quezon good?', 'answer' => 'Yes, particularly on the Pacific side. Less famous than Batangas or Palawan but cheaper and quieter.'],
                    ['question' => 'When is the Pahiyas Festival?', 'answer' => 'Every May 15 in Lucban. Worth catching once. Resort availability around the festival tightens significantly.'],
                    ['question' => 'How long is the drive to Pagbilao or Atimonan?', 'answer' => 'About 4 to 4.5 hours from QC via SLEX and the Maharlika Highway.'],
                ],
                'Find a resort in Quezon province. Beach towns, heritage stays, and inland villas across Lucena, Sariaya, Lucban, Pagbilao, and Atimonan.',
            ),

            'resort-in-quezon-province' => $this->build(
                'Resort in Quezon Province: A Town-by-Town Guide for Eastern Luzon',
                '<p>Quezon province is one of the largest provinces in Luzon and the cleanest way to talk about it is to break it down by town. A <strong>resort in Quezon province</strong> ranges from beachfront properties on the Pacific coast to historic inns in colonial towns to mountain retreats near Mount Banahaw. This page covers each cluster in more detail than the broader Quezon page.</p><p>Travel times from QC: Lucena 3 hours, Sariaya 3.5 hours, Lucban 3.5 hours, Pagbilao 4 hours, Atimonan 4.5 hours, Real 3.5 hours.</p>',
                '<h2>How to pick by trip type</h2><p>Beach weekend: Pagbilao, Atimonan, Padre Burgos. Surf trip: Real, Infanta. Heritage tour: Sariaya, Tayabas, Lucban. Family pool resort: Lucena, Candelaria. Adventure (caves, mountains): Mauban, Sampaloc.</p><h2>Pricing</h2><p>Beachfront rooms on the Pacific side: 3,500 to 8,500 PHP. Heritage stays in Sariaya: 3,200 to 7,500 PHP. Business hotels in Lucena: 2,200 to 5,000 PHP. Private villa rentals: 7,000 to 18,000 PHP for the full day.</p><h2>What to know about Quezon roads</h2><p>The Maharlika Highway is the main artery. Tropical depressions can flood sections during the rainy season, particularly between Tayabas and Atimonan. Check weather forecasts during June to October.</p>',
                [
                    ['question' => 'What is Quezon known for?', 'answer' => 'The Pahiyas Festival in Lucban, the Pacific coastline beaches, and the heritage town of Sariaya. Lots of variety for a single province.'],
                    ['question' => 'Is Quezon safer than Manila at night?', 'answer' => 'Generally yes, particularly the tourist zones and resort areas. Standard travel awareness still applies.'],
                    ['question' => 'When is the dry season here?', 'answer' => 'February to May. June to October is typhoon season with heavier rainfall along the Pacific coast.'],
                    ['question' => 'Can I do a Quezon trip without a car?', 'answer' => 'Buses run regularly to Lucena from Cubao and Pasay. From Lucena, jeepneys and vans cover the surrounding towns. A car helps but is not required.'],
                ],
                'Find a resort in Quezon province across Lucena, Sariaya, Lucban, Pagbilao, Atimonan, Real, and Infanta. Town-by-town guide.',
            ),

            'resort-in-quezon-city' => $this->build(
                'Resort in Quezon City: Pool Venues and Event Spaces Inside the Metro',
                '<p>Quezon City has more pool resorts and event venues than most visitors realize. A <strong>resort in Quezon City</strong> usually means a city-edge pool venue, a small private villa near the La Mesa Watershed, or a function space at one of the larger hotel resorts in the metro. No long drive required.</p><p>Most of the city\'s resort properties cluster in Novaliches, Fairview, Tandang Sora, and the Visayas Avenue corridor.</p>',
                '<h2>What to expect inside QC</h2><p>Pool venues with shaded cottages, function halls for 30 to 100 pax, and hotel-resort properties with full amenities including spa and gym. Most QC resorts cater to family events and reunions rather than long stays. La Mesa Eco Park inside the watershed offers nature-based picnic and pool combinations.</p><h2>Why pick QC over driving out</h2><p>Convenience. Older relatives who cannot handle a long drive. Same-day events that need to wrap by dinner. Birthday parties where most guests live nearby. The trip is more about logistics than scenery.</p><h2>Pricing</h2><p>Day-use pool resort: 250 to 600 PHP per head. Private pool villa for 20 to 30 pax: 7,000 to 18,000 PHP for the full day. Hotel resort rooms: 4,500 to 10,000 PHP per night. Function venue rentals: 8,000 to 30,000 PHP for events of 50 to 150 pax.</p>',
                [
                    ['question' => 'Is a QC resort worth it over driving to Antipolo?', 'answer' => 'For half-day events with elderly relatives, yes. For a real weekend escape, drive out.'],
                    ['question' => 'Are there overnight resort stays in QC?', 'answer' => 'A few private villa rentals offer overnight stays. Hotel-resorts inside the city are the main overnight option.'],
                    ['question' => 'What about La Mesa Eco Park?', 'answer' => 'A nature-themed venue inside the La Mesa Watershed. Pool, picnic areas, and tree-cover trails. Best for daytime family visits.'],
                    ['question' => 'How early should I book a QC venue?', 'answer' => 'Three to four weeks for birthday parties. Six to eight weeks for graduations and major reunions.'],
                ],
                'Find a resort in Quezon City. Pool venues, private villas, and hotel resorts inside Metro Manila. Compare picks here.',
            ),

            'resort-in-lucena-city' => $this->build(
                'Resort in Lucena City: Hotels, Pool Venues, and Quezon\'s Capital Stays',
                '<p>Lucena is the capital of Quezon province and the most accessible city in the region for first-time visitors. A <strong>resort in Lucena City</strong> typically means a business hotel, a pool venue with a function area, or a small inn near the historic Casa Comunidad. The city works well as a base for day trips to nearby Tayabas, Sariaya, and Lucban.</p><p>Travel time from QC is around 3 hours via SLEX and the Maharlika Highway.</p>',
                '<h2>The Lucena resort scene</h2><p>Several business hotels along the Quezon Avenue strip handle business travelers and short-stay visitors. Pool venues for family events cluster in Cotta and Mayao. The city itself has limited beachfront access, so beach trips require driving 30 to 60 minutes to Pagbilao or further.</p><h2>Pricing</h2><p>Business hotel rooms: 2,000 to 4,800 PHP per night. Pool resort day-use: 200 to 450 PHP per head. Private villa rentals for groups: 6,000 to 14,000 PHP for the full day. Restaurant meals: 200 to 500 PHP per person at most local spots.</p><h2>Food worth pairing</h2><p>Lucena is a regional food hub. Pancit habhab, pinikpikan, and broas are the local must-tries. Casa Comunidad de Tayabas serves Filipino food in a heritage setting and is 15 minutes from Lucena proper.</p>',
                [
                    ['question' => 'Is Lucena a good base for Quezon trips?', 'answer' => 'Yes, very central. Sariaya, Tayabas, Lucban, and Pagbilao are all within 30 to 60 minutes.'],
                    ['question' => 'Are there beaches in Lucena itself?', 'answer' => 'Limited. Bigger and cleaner beaches are 30 to 60 minutes away in Pagbilao or Padre Burgos.'],
                    ['question' => 'How is the food scene?', 'answer' => 'Strong on Quezon regional cuisine. Plan one meal at a heritage restaurant and one street-food meal at the public market.'],
                    ['question' => 'When does the Pahiyas Festival affect bookings?', 'answer' => 'May 15 and the surrounding week. Hotels in Lucena and Lucban fill up four to six weeks ahead.'],
                ],
                'Find a resort in Lucena City. Business hotels, pool venues, and a base for Quezon province day trips. Compare picks here.',
            ),

            'resort-in-sariaya-quezon' => $this->build(
                'Resort in Sariaya Quezon: Heritage Stays and Cooler Weather at the Foothills',
                '<p>Sariaya sits at the foothills of Mount Banahaw and is famous for its ancestral houses and the Agawan Festival. A <strong>resort in Sariaya Quezon</strong> often means a heritage-themed inn, a farm retreat, or a private villa with cooler upland weather. The town has a slower pace than nearby Lucena and rewards travellers who like architecture and history.</p><p>Travel time from QC is around 3.5 hours via SLEX and the Maharlika Highway.</p>',
                '<h2>What makes Sariaya unique</h2><p>The ancestral houses on the National Road are some of the best-preserved in southern Luzon. The Casa de Comunidad, Casa Catigbac, and several other century-old homes are open for tours. Most heritage inns sit on or near the main road.</p><h2>The resort scene</h2><p>Mostly small heritage inns and farm retreats. A few private villa rentals operate in the upland barangays toward Banahaw. The town does not have the pool-party resort vibe of Pansol or Bacoor. Expect quieter, more reflective weekends.</p><h2>Pricing</h2><p>Heritage inn rooms: 2,800 to 6,500 PHP per night. Farm retreat overnight: 2,200 to 4,500 PHP. Private villa rentals: 6,000 to 14,000 PHP for the full day. Most properties include breakfast at higher tier rates.</p>',
                [
                    ['question' => 'Is Sariaya worth a visit for non-heritage travellers?', 'answer' => 'Yes for the cooler weather and slower pace. Heritage adds value for history lovers but is not the only reason to visit.'],
                    ['question' => 'Can I climb Mount Banahaw from Sariaya?', 'answer' => 'Yes, several trail entries start near Sariaya. Banahaw has spiritual significance and registration with local guides is required.'],
                    ['question' => 'When is the Agawan Festival?', 'answer' => 'May 15, the same day as Pahiyas. Sariaya residents put longanisas, mangoes, and rice cakes outside their homes for visitors to grab.'],
                    ['question' => 'Is the food different from Lucena?', 'answer' => 'Similar regional flavors with a stronger emphasis on heritage Filipino cooking. Several inns serve set-menu dinners featuring local recipes.'],
                ],
                'Discover a resort in Sariaya Quezon. Heritage inns, farm retreats, and cool upland weekends at Mount Banahaw\'s foothills.',
            ),

            'resort-in-manila' => $this->build(
                'Resort in Manila: Hotel Resorts and Pool Stays Inside the Capital',
                '<p>Manila does not have many traditional resorts, but a handful of hotel-resorts and pool venues operate inside the city. A <strong>resort in Manila</strong> usually means a Manila Bay hotel with a pool, an Intramuros heritage stay, or a hotel resort near the Mall of Asia complex. Beach-style resorts do not exist within the city itself.</p><p>The Manila Bay sunset is the main scenic asset, and several hotels along Roxas Boulevard have pools or rooftop deck options to enjoy it.</p>',
                '<h2>Where to stay in Manila</h2><p>Roxas Boulevard hotels for Manila Bay sunsets. Intramuros for heritage stays inside the walled city. Malate and Ermita for budget-friendly options. Mall of Asia complex hotels (Conrad, Hyatt, Sofitel) for high-end resort-style stays with pools.</p><h2>Pricing</h2><p>High-end resort hotels: 8,000 to 25,000 PHP per night. Mid-range business hotels: 3,000 to 7,000 PHP. Heritage inns in Intramuros: 2,500 to 6,500 PHP. Day-use pool access at some hotels: 800 to 2,500 PHP per head.</p><h2>Things to do nearby</h2><p>Intramuros walking tour, Manila Bay sunset cruise, National Museum visit, Binondo food crawl. Most travellers split their stay between sights and pool relaxation.</p>',
                [
                    ['question' => 'Is there a beach resort in Manila?', 'answer' => 'No. Manila Bay has waterfront views but no swimmable beach. For real beaches, head to Batangas, Cavite, or further.'],
                    ['question' => 'Which Manila hotel has the best pool?', 'answer' => 'Sofitel Philippine Plaza, Conrad MOA, and Diamond Hotel are the top picks for pool quality.'],
                    ['question' => 'Are there day-use pool options?', 'answer' => 'Yes at several five-star hotels. Day-use pool access runs 800 to 2,500 PHP per head depending on the property.'],
                    ['question' => 'Is staying in Manila worth it over Makati or BGC?', 'answer' => 'Yes for first-time visitors and history lovers. Makati and BGC are better for business and modern dining scenes.'],
                ],
                'Find a resort in Manila. Hotel resorts on Manila Bay, Intramuros heritage stays, and Mall of Asia pool hotels. Compare picks here.',
            ),

            'resort-in-taguig' => $this->build(
                'Resort in Taguig: Hotel Pools and Resort-Style Stays in BGC and the Surrounds',
                '<p>Taguig is home to Bonifacio Global City and several hotel-resorts with full pool amenities. A <strong>resort in Taguig</strong> usually means a hotel with a resort-style pool deck, a wellness or spa property, or a high-end serviced apartment with rooftop swimming. Traditional outdoor resorts do not exist in Taguig itself, but hotel resort experiences are abundant.</p>',
                '<h2>Where to look</h2><p>BGC has Shangri-La at the Fort, Grand Hyatt, Seda Bonifacio, and others, all with full hotel resort amenities including pools. Lower Bicutan and the Heritage Park area have smaller boutique hotels with pool decks. McKinley Hill near Venice Grand Canal has serviced apartments and small hotels.</p><h2>What you actually get</h2><p>Modern pool decks, gyms, spas, multiple restaurants, business services, and 24-hour room service. The trade-off is no beach, no cottages, and no garden setting. Pick Taguig if your trip is about urban comforts and not nature.</p><h2>Pricing</h2><p>High-end hotel resorts: 9,000 to 28,000 PHP per night. Mid-range business hotels: 3,500 to 7,500 PHP. Serviced apartments for longer stays: 60,000 to 180,000 PHP per month. Day-use pool access at some hotels: 1,200 to 2,800 PHP per head.</p>',
                [
                    ['question' => 'Is there a real resort in Taguig?', 'answer' => 'In the traditional outdoor-resort sense, no. Hotel-resort experiences are abundant but Taguig has no beach or garden cottage properties.'],
                    ['question' => 'Best hotel pool in BGC?', 'answer' => 'Shangri-La at the Fort and Grand Hyatt Manila have the most resort-style pool experiences in BGC.'],
                    ['question' => 'Can I book day-use only?', 'answer' => 'Yes at most five-star hotels, though day-use rates can run 1,200 to 2,800 PHP per head.'],
                    ['question' => 'Is Taguig good for families?', 'answer' => 'Yes, particularly for short stays. Plenty of restaurants, kid-friendly malls (Venice, MarketMarket), and reliable hotel services.'],
                ],
                'Find a resort in Taguig. BGC hotel resorts, pool decks, and high-end serviced stays. Compare picks here.',
            ),
        ];
    }
}
