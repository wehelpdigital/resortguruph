<?php

namespace Database\Seeders;

class BulkContent02Batangas extends BulkContentBase
{
    protected function pages(): array
    {
        return [
            'resort-in-batangas-city' => $this->build(
                'Resort in Batangas City: City Hotels and Coastal Stays Near the Port',
                '<p>Batangas City is the capital of the province and the main gateway for ferries heading to Mindoro and the Visayas. A <strong>resort in Batangas City</strong> usually means a city-edge hotel or a coastal stay along Calicanto and the Pallocan road, rather than a beachfront cottage like you find in Laiya or Nasugbu.</p><p>The city works well as a base if you have ferry connections or plan to drive out to the surrounding beach towns each day.</p>',
                '<h2>City versus coast in Batangas</h2><p>Inside the city you have business hotels and pool venues priced for short stays. Along the coastal road toward Mabini and Lobo you find quieter resort properties with shaded cottages and decent swimming areas. Pick by trip purpose: city hotel for ferry layovers, coastal property for actual beach days.</p><h2>Travel and timing</h2><p>From BGC the drive runs around 2 to 2.5 hours via SLEX and the STAR Tollway. Saturday morning departures before 8 AM make the trip noticeably smoother. Coming back on Sunday afternoon is the bottleneck, with traffic peaking between 4 and 7 PM at the Calamba bottleneck.</p><h2>Pricing</h2><p>City hotel rooms: 2,000 to 5,000 PHP. Coastal resort rooms: 2,500 to 6,500 PHP. Day-use pool: 250 to 500 PHP. Ferry day-trip packages including breakfast: 400 to 700 PHP per person at many city hotels.</p>',
                [
                    ['question' => 'Is the city worth visiting on its own?', 'answer' => 'For a long weekend, no. Use it as a base or a ferry layover. The actual destination tends to be Anilao, Laiya, Lobo, or Mindoro.'],
                    ['question' => 'How is the ferry connection?', 'answer' => 'Daily ferries leave Batangas Port to Calapan, Puerto Galera, and Romblon. Schedules are mostly reliable but typhoon season can cancel sailings.'],
                    ['question' => 'Are there beach resorts within the city?', 'answer' => 'A small number along Calicanto. Most travellers head 30 minutes south to Mabini or east to Lobo for a proper beach.'],
                    ['question' => 'Best time of year?', 'answer' => 'March to May for hot beach weather. October to early December for off-season rates and fewer crowds.'],
                ],
                'Find a resort in Batangas City for ferry layovers, coastal escapes, or weekend stays near the port. Compare picks here.',
            ),

            'resort-in-batangas-with-pool-and-beach' => $this->build(
                'Resort in Batangas with Pool and Beach: Best Picks for Family Weekends',
                '<p>The classic Batangas weekend ask is a resort that delivers both a pool and a beach so the kids can rotate. A <strong>resort in Batangas with pool and beach</strong> is most commonly found in Laiya, Calatagan, Nasugbu, and parts of Mabini. The combination matters because some travellers want fresh-water swimming away from saltwater stickiness, while others want the option to walk straight onto sand.</p><p>This list groups the strongest pool-and-beach combos by location and tells you what each town is really like.</p>',
                '<h2>Where the pool-and-beach combos live</h2><p>Laiya in San Juan has the widest selection. White-sand coastline, family-friendly resorts, and most properties run multiple pools. Calatagan on the western side offers similar amenities at slightly lower rates. Nasugbu and Munting Buhangin focus more on beach club setups with a single pool deck. Mabini\'s Anilao properties skew toward divers, but some now have proper family-style amenities.</p><h2>What to ask before booking</h2><p>Pool depth and shading. Beach access path and shoes-required policies. Generator coverage during typhoon season. Whether the pool stays open at night. Outside food and corkage rules if you plan to bring catering for a reunion.</p><h2>Pricing snapshot</h2><p>Family resort rooms with both amenities: 3,500 to 9,000 PHP per night during regular season. Holy Week and December push prices 30 to 50 percent higher. Day-use combo passes at some Laiya properties run 1,500 to 2,500 PHP per head including lunch.</p>',
                [
                    ['question' => 'Which Batangas town has the most pool-and-beach resorts?', 'answer' => 'Laiya in San Juan, followed by Calatagan.'],
                    ['question' => 'Are pool and beach combos available year-round?', 'answer' => 'Yes. Pools are heated at some properties for cooler months, and beaches stay usable except during typhoon weeks in July to September.'],
                    ['question' => 'Can I day-use a Laiya resort without booking overnight?', 'answer' => 'Many properties offer day-use combo passes, especially on weekdays. Saturdays usually require an overnight booking.'],
                    ['question' => 'Is the sand white in Laiya?', 'answer' => 'Mostly white to off-white on the central San Juan stretch. Quality varies by exact location within Laiya.'],
                ],
                'Find a resort in Batangas with pool and beach combinations in Laiya, Calatagan, and Nasugbu. Family-friendly picks compared.',
            ),

            'resort-in-calatagan' => $this->build(
                'Resort in Calatagan: White-Sand Beaches on the Quieter Side of Batangas',
                '<p>Calatagan sits on the western tip of Batangas, facing the South China Sea. A <strong>resort in Calatagan</strong> is usually quieter than Laiya, with longer stretches of less-crowded beach and a cluster of higher-end beach clubs like Stilts, Munting Buhangin, and the Lago de Oro complex. The sand here ranges from white to cream depending on the exact location.</p><p>Travel time from BGC is around 2.5 to 3 hours via SLEX, the STAR Tollway, and the Batangas-Calatagan road.</p>',
                '<h2>Calatagan versus Laiya</h2><p>Both have good sand and family-friendly properties. Laiya has a wider variety of price points and more restaurant options outside the resorts. Calatagan tends to be more self-contained: you stay at the property, eat at the property, and rarely leave during the trip. That makes it a better fit for travellers who want a full-immersion weekend without driving around looking for food.</p><h2>The big-name resorts</h2><p>Stilts Calatagan is the famous over-water cottages property. Lago de Oro caters to wakeboarding enthusiasts with a dedicated cable park. Punta Calatagan and Casa Cecilia offer mid-range options. Several smaller boutique villas have opened on the Burot Beach stretch.</p><h2>Pricing</h2><p>Mid-range beachfront resorts: 4,000 to 8,500 PHP per night. Premium properties like Stilts: 8,000 to 18,000 PHP. Burot Beach camping is the budget option at under 1,000 PHP per night for a tent space and basic shower.</p>',
                [
                    ['question' => 'Is Burot Beach still good?', 'answer' => 'Yes for budget travellers and campers. The Burot stretch is managed by Hamilo Coast and has improved facilities. Bring your own gear.'],
                    ['question' => 'Can I bring kids to Stilts?', 'answer' => 'Yes. The water around the cottages is shallow and calm. Toddlers need close supervision because the cottage perimeter is open water.'],
                    ['question' => 'How does Calatagan compare to Laiya for waves?', 'answer' => 'Calatagan tends to be calmer with shallower water for a longer distance from shore. Laiya can have stronger afternoon swells especially during amihan.'],
                    ['question' => 'Best time to visit?', 'answer' => 'February to May for the calmest water and warmest weather. December and January are cooler with stronger amihan winds.'],
                ],
                'Find a resort in Calatagan with white-sand beaches, Stilts cottages, Lago de Oro wakeboarding, and quieter weekends than Laiya. Compare here.',
            ),

            'resort-in-calatagan-batangas' => $this->build(
                'Resort in Calatagan Batangas: A Deeper Look at the Western Beach Cluster',
                '<p>Calatagan Batangas is the term most local travellers use when distinguishing the town from coastal areas elsewhere with similar names. A <strong>resort in Calatagan Batangas</strong> typically lands on the western coastline facing the South China Sea, with white-to-cream sand and calmer waters than Laiya on the eastern side of the province.</p><p>This page goes deeper than the broader Calatagan guide and covers what each barangay tends to offer and how to time your booking around the season.</p>',
                '<h2>Barangays worth knowing</h2><p>Sambungan and Talibayog have the bulk of the higher-end resorts including Stilts and Lago de Oro. Burot is the budget camping zone managed by Hamilo Coast. Bagong Silang has small private villa rentals. Quilitisan and Lucsuhin host the wedding venue clusters.</p><h2>When to book</h2><p>Weekday rates are 20 to 30 percent lower than weekend rates. Long weekends in February, April, and May fill up six weeks ahead. Holy Week is the absolute peak with most properties requiring two-night minimums.</p><h2>Pricing nuances</h2><p>Cottage day-use at most resorts: 1,200 to 2,500 PHP for a full table-and-shade rental. Overnight room rates vary by sea-view versus garden-view, with sea-view costing 30 to 50 percent more. Many resorts include breakfast at the higher tier rates.</p>',
                [
                    ['question' => 'Is Calatagan really quieter than Laiya?', 'answer' => 'On most weekends, yes. Laiya gets more day-trippers and the beach is busier at peak hours.'],
                    ['question' => 'How is parking inside the resorts?', 'answer' => 'Most properties have ample lot parking. Calatagan beach access roads are narrow but the resorts themselves accommodate vans and small buses.'],
                    ['question' => 'Are credit cards widely accepted?', 'answer' => 'At the bigger named resorts yes. Smaller boutique villas often prefer bank transfer or cash. Confirm before arrival.'],
                    ['question' => 'How do I reach Calatagan without a car?', 'answer' => 'Buses leave Pasay and Cubao to Lipa or Nasugbu, then transfer to a Calatagan jeepney. Most resort guests take a Grab van or rent a car for convenience.'],
                ],
                'Find a resort in Calatagan Batangas. Western-coast beach cluster, Stilts cottages, Lago de Oro, and barangay-by-barangay tips.',
            ),

            'resort-in-laiya' => $this->build(
                'Resort in Laiya: White Sand and Family-Friendly Stays in San Juan Batangas',
                '<p>Laiya is the long stretch of white-sand beach in San Juan, Batangas, and one of the most popular beach destinations within four hours of Manila. A <strong>resort in Laiya</strong> usually means a family-friendly property with beachfront access, multiple pools, and the kind of buffet breakfast that includes garlic rice, tapa, and at least three types of fresh fruit.</p><p>The Laiya coastline runs about three kilometers, with resorts clustered most densely between Hugom and Laiya proper.</p>',
                '<h2>Pick the right part of Laiya</h2><p>The central San Juan stretch has the bigger family resorts like Acuatico, Laiya Coco Grove, La Luz Beach Resort, and Hanggi. The Hugom end is quieter with boutique properties and smaller cottage clusters. Sabang and Subic stretches further south are less developed and feel almost untouched.</p><h2>What to expect</h2><p>White-to-cream sand. Clear water during the dry season, slightly cloudier after typhoons. Strong afternoon sun that requires shade by 11 AM. Most major resorts have shaded beach loungers, swim noodles, and lifeguards at the main swim zones.</p><h2>Pricing</h2><p>Mid-range family resort rooms: 4,500 to 9,500 PHP per night. Premium resorts like Acuatico: 9,000 to 16,000 PHP. Day-use combo passes at family resorts: 1,500 to 2,800 PHP including lunch. Travel time from BGC is around 3 to 3.5 hours via SLEX and the STAR Tollway.</p>',
                [
                    ['question' => 'How long is the drive from Manila to Laiya?', 'answer' => 'Around 3 to 3.5 hours from BGC. Friday departures after 5 PM can stretch this to 4.5 hours.'],
                    ['question' => 'Is the sand really white?', 'answer' => 'Mostly white to off-white. Quality varies between resorts. Hugom and central San Juan have the cleanest sand.'],
                    ['question' => 'Can I do a day trip to Laiya?', 'answer' => 'Possible but tight. Most day-trippers leave Manila at 4 AM and return after 9 PM. An overnight stay is more relaxed.'],
                    ['question' => 'Are pets allowed at Laiya resorts?', 'answer' => 'A growing number allow pets but with restrictions. Confirm size limits and pet fees during booking.'],
                ],
                'Find a resort in Laiya, San Juan Batangas. White-sand beaches, family-friendly properties, and 3-hour access from Manila. Compare picks here.',
            ),

            'resort-in-lipa' => $this->build(
                'Resort in Lipa: Pool Stays and Inland Retreats in Batangas\'s Coffee City',
                '<p>Lipa sits inland in southern Batangas and has long been known for its coffee history and the Carmelite Monastery. A <strong>resort in Lipa</strong> typically means an inland pool venue, a private villa rental, or a small boutique hotel near the city center. The town is not a beach destination but works well as a quieter base if you plan to day-trip to nearby beaches.</p><p>Most resort properties cluster along the J.P. Laurel Highway and the side roads near Mataasnakahoy and Cuenca.</p>',
                '<h2>Why pick Lipa over the coast</h2><p>Three reasons. First, the weather is cooler than coastal Batangas thanks to the higher elevation. Second, the city has decent food and pharmacy access, which matters for older travellers. Third, you can day-trip to Anilao for diving, to San Juan for the beach, or to Tagaytay for the ridge without packing everything up.</p><h2>What to budget</h2><p>Boutique hotel rooms: 2,200 to 4,800 PHP per night. Pool resort day-use: 250 to 500 PHP per head. Private pool villas for groups: 8,000 to 18,000 PHP for the full 22-hour day. Travel time from BGC is roughly 90 minutes via SLEX and STAR Tollway.</p>',
                [
                    ['question' => 'Is Lipa a good base for visiting Batangas beaches?', 'answer' => 'Yes. You are 30 to 60 minutes from Anilao, San Juan, and the coastal towns, with the option of a cooler hotel base each night.'],
                    ['question' => 'Are there beach resorts in Lipa?', 'answer' => 'No. Lipa is inland. For beaches, drive east to Laiya or southwest to Anilao and Calatagan.'],
                    ['question' => 'How is the weather?', 'answer' => 'Cooler than coastal Batangas, especially mornings. Light jacket weather from November to February.'],
                    ['question' => 'What food should I try?', 'answer' => 'Lomi is the city\'s signature dish, particularly at Goto Monster. Bulalo and tapang taal are also classics.'],
                ],
                'Plan a stay at a resort in Lipa with cooler weather, day-trip access to Batangas beaches, and historic city center. Compare picks here.',
            ),

            'resort-in-lipa-batangas' => $this->build(
                'Resort in Lipa Batangas: Inland Pool Resorts and Family Villas in the Coffee City',
                '<p>Lipa Batangas is the inland resort cluster of southern Batangas, away from the coast but close enough for day trips to the beaches. A <strong>resort in Lipa Batangas</strong> usually means a pool venue, a function hall booking for a reunion, or a quiet boutique hotel as a base for exploring the region.</p><p>This page covers what specific Lipa barangays offer and how the city compares to nearby Sto. Tomas and Tanauan for a similar inland weekend.</p>',
                '<h2>Lipa\'s resort clusters</h2><p>Mataasnakahoy and Tanauan-side: family pool resorts and function halls. Major reunion zone for QC and Makati families. Cuenca-side: smaller boutique villas with cooler weather thanks to higher elevation. JP Laurel Highway corridor: business hotels and city-style resorts.</p><h2>When Lipa beats the coast</h2><p>Off-season months when the beach is rainy. Reunions where the budget cannot stretch to Tagaytay. Family trips with very old or very young members who do not handle long drives well. Lipa is 30 to 45 minutes closer than the coast.</p><h2>Pricing</h2><p>Day-use pool: 200 to 500 PHP. Overnight rooms: 2,000 to 5,500 PHP. Private villa rentals for 20 to 30 pax: 8,000 to 16,000 PHP for the full 22 hours. Saturdays during peak season fill up two weeks ahead.</p>',
                [
                    ['question' => 'Is Lipa Batangas safer than Manila at night?', 'answer' => 'Generally yes, particularly the inner city and resort zones. Always practice standard travel awareness regardless.'],
                    ['question' => 'Can I work remote from a Lipa hotel?', 'answer' => 'Yes. Most business hotels have stable Wi-Fi and quiet rooms. Boutique villas vary widely on internet speed.'],
                    ['question' => 'Are there family weekend packages?', 'answer' => 'Yes. Many Lipa resorts offer two-night reunion packages with catering for groups of 30 to 80 at competitive rates.'],
                    ['question' => 'How early should I book for Holy Week?', 'answer' => 'Three to four weeks ahead. Lipa fills up later than Tagaytay but still gets busy.'],
                ],
                'Plan a stay at a resort in Lipa Batangas with inland pool resorts, family villas, and reunion-ready venues. Compare picks here.',
            ),

            'resort-in-lobo-batangas' => $this->build(
                'Resort in Lobo Batangas: The Quiet Eastern Coastline Most Travellers Skip',
                '<p>Lobo is on the eastern coast of Batangas province, past Mabini and southeast of Batangas City. A <strong>resort in Lobo Batangas</strong> tends to be small, family-run, and noticeably quieter than the bigger named beach towns. The coastline here faces Mindoro Strait, and the marine sanctuaries off the coast attract divers and snorkellers looking for something less crowded than Anilao.</p><p>Travel time from BGC is around 3 to 3.5 hours via SLEX, STAR Tollway, and the road past Batangas City.</p>',
                '<h2>Why Lobo gets overlooked</h2><p>Mostly because Anilao gets all the diving press and Laiya gets the beach press. Lobo sits between them in terms of activity level and works well for travellers who want a slower trip. The Verde Island Passage off Lobo\'s coast holds some of the highest marine biodiversity in the world, which is why a quiet but growing dive scene operates here.</p><h2>What you find</h2><p>Mostly small beach inns and boutique cottages. Sand colour varies from white in some coves to grey-brown on more exposed beaches. Several resorts offer guided snorkelling tours and basic dive packages. Hotel-style chains do not operate here, which is part of the appeal.</p><h2>Pricing</h2><p>Beach cottage rates: 1,800 to 4,500 PHP per night. Dive package overnight: 3,500 to 6,500 PHP per person including two dives. Food is usually included in the higher tier rates because few restaurants exist outside the resorts.</p>',
                [
                    ['question' => 'Is Lobo good for diving?', 'answer' => 'Yes, increasingly so. Several small operations run guided dives in the Verde Island Passage with marine sanctuary access.'],
                    ['question' => 'How does Lobo compare to Anilao?', 'answer' => 'Lobo is quieter and cheaper, with similar marine richness but less established dive infrastructure. Anilao remains the better choice if you want professional dive shops on every corner.'],
                    ['question' => 'Are there family-friendly beaches in Lobo?', 'answer' => 'Yes, particularly the protected coves on the southern side. Sand and water quality vary by exact location.'],
                    ['question' => 'When is the best season?', 'answer' => 'November to May for the calmest seas. The Verde Passage is at peak visibility from March to May.'],
                ],
                'Find a resort in Lobo Batangas with quiet beaches, Verde Passage diving, and a slower weekend pace than Anilao or Laiya. Compare here.',
            ),

            'resort-in-mabini-batangas' => $this->build(
                'Resort in Mabini Batangas: Anilao Diving and Coastal Stays',
                '<p>Mabini is the dive capital of Luzon, and Anilao is its most famous barangay. A <strong>resort in Mabini Batangas</strong> typically caters to divers, although a growing number of properties now serve families and casual swimmers with proper pools and shaded decks. The macro photography off Anilao is among the best in the world, which keeps the dive scene busy year-round.</p><p>Travel time from BGC is around 2.5 to 3 hours via SLEX, STAR Tollway, and the Mabini-Anilao road.</p>',
                '<h2>What to expect from a Mabini resort</h2><p>Small to mid-size dive lodges dominate. Most have on-site boats, dive equipment rental, and certified instructors. Standard dive packages include two boat dives per day, three meals, and a basic room. Several premium properties like Buceo Anilao and Crystal Blue Resort raise the bar with infinity pools and air-conditioned rooms.</p><h2>For non-divers</h2><p>Bringing a non-diving companion is normal. Most Mabini resorts have pools, easy snorkeling sites accessible from shore, and shaded common areas. The town centers offer simple Filipino food at standard prices.</p><h2>Pricing</h2><p>Dive package overnight: 4,000 to 7,500 PHP per person including two dives and meals. Non-diver room rate: 2,200 to 5,500 PHP per night. Premium resorts: 6,500 to 14,000 PHP per night for a full-board diver package.</p>',
                [
                    ['question' => 'Is Anilao good for beginners?', 'answer' => 'Yes. Several dive schools run open water certification courses with calm dive sites suited to first-time divers.'],
                    ['question' => 'When is the best diving season?', 'answer' => 'November to May for the calmest seas. Macro photographers favor the cooler months from December to February.'],
                    ['question' => 'Are there beaches at Mabini?', 'answer' => 'Mostly small coves rather than long sandy stretches. The town is a diving destination first, beach destination second.'],
                    ['question' => 'How do I get to Anilao without a car?', 'answer' => 'Buses from Cubao to Batangas City, then a jeepney or van to Anilao. Many resorts offer pickup services from Manila for an additional fee.'],
                ],
                'Find a resort in Mabini Batangas, the dive capital of Luzon. Anilao macro photography, dive packages, and coastal stays compared.',
            ),

            'resort-in-nasugbu' => $this->build(
                'Resort in Nasugbu: Beach Clubs and Coastal Weekends on the Western Coast',
                '<p>Nasugbu sits on the western coast of Batangas facing the South China Sea. A <strong>resort in Nasugbu</strong> usually means a beach club setup, a beachfront hotel, or a private villa rental in the Hamilo Coast development. The town has built its reputation on family-friendly beaches and the famous Munting Buhangin cove.</p><p>Travel time from BGC is around 2 to 2.5 hours via the SLEX-CALAX route and the Tagaytay-Nasugbu road.</p>',
                '<h2>Where the resorts cluster</h2><p>Hamilo Coast holds the highest-tier developments including Pico de Loro Cove and the Costa del Hamilo properties. Munting Buhangin is the famous public-access cove with several mid-range cottages. Lian and Calayo are quieter coastal stretches with smaller boutique resorts.</p><h2>Beach quality</h2><p>White to cream sand at most public beaches. The Hamilo Coast properties have curated beaches with rope-marked swim zones. The Pico de Loro stretch is calm and family-friendly. Munting Buhangin has the most photogenic cove shape, especially at low tide.</p><h2>Pricing</h2><p>Beach club day-use: 1,500 to 3,000 PHP per head including lunch. Resort hotel rooms: 4,500 to 12,000 PHP per night. Private villa rentals: 12,000 to 35,000 PHP for the full day. Holy Week and December push rates 40 to 60 percent higher.</p>',
                [
                    ['question' => 'Is Nasugbu beach swimmable for kids?', 'answer' => 'Yes, particularly at Pico de Loro and the Hamilo Coast coves. Calmer than the open beaches further south.'],
                    ['question' => 'How does Nasugbu compare to Laiya?', 'answer' => 'Nasugbu has more beach club amenities and higher-tier accommodation. Laiya has more variety of price points and a longer beachfront stretch.'],
                    ['question' => 'Can I bring outside food?', 'answer' => 'Beach club properties typically do not allow outside food. Private villa rentals are usually flexible.'],
                    ['question' => 'How early should I book Pico de Loro?', 'answer' => 'Six to eight weeks for Holy Week, three to four weeks for normal weekends. Membership-only weekends apply for some properties.'],
                ],
                'Find a resort in Nasugbu, Batangas. Hamilo Coast beach clubs, Pico de Loro, Munting Buhangin coves, and weekend getaways. Compare here.',
            ),

            'resort-in-nasugbu-batangas' => $this->build(
                'Resort in Nasugbu Batangas: The Full Coastal Guide for Western Batangas',
                '<p>Nasugbu Batangas is the most-searched version of this destination because it disambiguates from other Nasugbu locations in the country. A <strong>resort in Nasugbu Batangas</strong> spans beach clubs, full hotel resorts, Hamilo Coast developments, and private villa rentals across the western Batangas coastline.</p><p>This page covers the wider Nasugbu area and compares the main resort developments by what type of traveller they fit best.</p>',
                '<h2>The big developments</h2><p>Hamilo Coast covers most of the higher-tier resorts, including Pico de Loro Cove, Costa del Hamilo, and the Pico Sands Hotel. Membership-based access applies to some areas. Stilts Beach Resort sits nearby. Munting Buhangin is the famous public cove. Tali Beach is the gated community on the southern tip.</p><h2>Picking the right property</h2><p>For first-time visitors with a family, Pico de Loro Cove is the safest choice with full amenities and curated beaches. For groups of 20 to 50 doing a reunion, private villa rentals along the Lian-Nasugbu road give better value. For couples wanting quiet, Tali Beach or the smaller boutique cottages near Calayo work best.</p><h2>Pricing</h2><p>Day-use beach club: 1,500 to 3,500 PHP per head including lunch. Resort hotel rooms: 5,000 to 14,000 PHP per night. Private villa rentals: 15,000 to 40,000 PHP for the full day. Peak season covers Holy Week, December, and any long weekend.</p>',
                [
                    ['question' => 'Is Nasugbu Batangas the same as just Nasugbu?', 'answer' => 'Yes. People use the longer form to distinguish from other Nasugbu locations in the country. The destination is the same.'],
                    ['question' => 'Do I need a Hamilo Coast membership to visit?', 'answer' => 'No, day-use packages and hotel bookings are open to everyone. Membership grants extra privileges and discounts.'],
                    ['question' => 'How is parking at Munting Buhangin?', 'answer' => 'Tight on weekends. Arrive before 8 AM or expect to park further away and walk in. The cove can fill up by 9 AM.'],
                    ['question' => 'When is the best time to visit?', 'answer' => 'February to May for calm seas and warm weather. December and January are cooler with stronger amihan winds.'],
                ],
                'Find a resort in Nasugbu Batangas. Hamilo Coast, Pico de Loro, Stilts, and private villas across western Batangas. Compare here.',
            ),

            'resort-in-san-juan-batangas' => $this->build(
                'Resort in San Juan Batangas: The Town Behind Laiya\'s Beach Strip',
                '<p>San Juan is the municipality that holds the Laiya beach strip plus several smaller coastal barangays. A <strong>resort in San Juan Batangas</strong> usually means a beach property in Laiya, although a growing number of smaller resorts exist in Sabang, Hugom, and along the inland edge of the municipality.</p><p>If you have heard about Laiya, you have heard about San Juan. They are practically interchangeable in casual travel talk.</p>',
                '<h2>Where the resort clusters sit</h2><p>Laiya central has the bigger family resorts. Hugom is quieter with boutique cottages. Sabang sits further south and feels more local. Subic Ibaba and Pinagbayanan are inland barangays with private villa rentals and farm retreats away from the beach.</p><h2>What San Juan is known for</h2><p>White-sand beach access, family-friendly resorts, and the closest white-sand stretch within four hours of Manila. The town hosts the famous Subli dance festival in mid-July, which can affect resort availability.</p><h2>Pricing</h2><p>Mid-range family resort rooms: 4,500 to 9,000 PHP per night. Premium beach resorts: 9,000 to 16,000 PHP. Private villa rentals: 10,000 to 25,000 PHP for the full day. Day-use beach access at most resorts: 800 to 1,500 PHP per head including a basic lunch.</p>',
                [
                    ['question' => 'Is San Juan the same as Laiya?', 'answer' => 'Laiya is the famous beach strip inside the larger town of San Juan. Most beach resorts you find online listed as San Juan Batangas are actually in Laiya.'],
                    ['question' => 'How long is the drive from QC?', 'answer' => 'Around 3 to 3.5 hours via SLEX and STAR Tollway. Friday afternoon departures add 30 to 60 minutes.'],
                    ['question' => 'Are there budget beach options?', 'answer' => 'Yes, particularly at the Hugom and Sabang ends. Smaller cottages run 1,500 to 3,500 PHP per night.'],
                    ['question' => 'What is the Subli festival?', 'answer' => 'A traditional dance festival held in San Juan every July. Worth catching if your trip overlaps, but resort availability tightens during festival week.'],
                ],
                'Find a resort in San Juan Batangas. The Laiya beach strip, Hugom boutique cottages, and inland villas. Compare picks here.',
            ),
        ];
    }
}
