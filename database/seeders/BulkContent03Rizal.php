<?php

namespace Database\Seeders;

class BulkContent03Rizal extends BulkContentBase
{
    protected function pages(): array
    {
        return [
            'resort-in-antipolo' => $this->build(
                'Resort in Antipolo: Hilltop Views, Cool Mountain Air, and Pool Weekends',
                '<p>Antipolo is the city on the hills east of Metro Manila, famous for its Marian shrine and the postcard view of the metro from the heights of Sumulong. A <strong>resort in Antipolo</strong> usually means a hilltop property with a pool overlooking the lights of Marikina and Pasig, or a private villa tucked into the cooler upper barangays.</p><p>Travel time from QC is around 45 to 60 minutes via Marcos Highway, which makes Antipolo the closest hilltop escape to Manila.</p>',
                '<h2>The two Antipolo resort flavours</h2><p>The first is the hilltop view restaurant-resort, places like Vista Real, Cloud 9, or smaller venues along Sumulong Highway. You go for the view at sunset, eat dinner, then drive home. The second is the private pool resort, mostly clustered in upper barangays like San Roque, Inarawan, and Hinulugang Taktak. These are the ones you rent for the family weekend.</p><h2>What to know before booking</h2><p>Roads up Sumulong can get steep. Older sedans handle it fine but a quick brake check before the trip is wise. Saturday evening descents back to Manila get heavy around 8 to 10 PM. The view from most hilltop properties is best between 5 PM and 8 PM when the city lights start showing.</p><h2>Pricing</h2><p>Hilltop restaurant minimum spend: 500 to 1,500 PHP per head. Pool resort day-use: 350 to 700 PHP. Private villa rentals: 8,000 to 22,000 PHP for the full 22 hours.</p>',
                [
                    ['question' => 'Is Antipolo really cool weather?', 'answer' => 'Cooler than Manila but not as cold as Tagaytay. Expect 22 to 25 degrees on most evenings, with the upper barangays running 2 to 3 degrees cooler.'],
                    ['question' => 'How is the drive on weekends?', 'answer' => 'Marcos Highway clogs on Saturday afternoons. Going up Sumulong is smoother than coming down at peak hours.'],
                    ['question' => 'Are pool resorts in Antipolo family-friendly?', 'answer' => 'Yes, especially the private rentals with the entire property booked for one family. Day-use entrance resorts can get crowded.'],
                    ['question' => 'Can I visit just for dinner?', 'answer' => 'Yes. Many hilltop venues operate primarily as restaurants with no overnight stay required. Reservations recommended for Saturdays.'],
                ],
                'Find a resort in Antipolo with hilltop pool views, cool air, and quick access from Manila. Compare picks here.',
            ),

            'resort-in-antipolo-private' => $this->build(
                'Resort in Antipolo Private: Pool Villas You Book for the Whole Group',
                '<p>Private pool rentals have become the most popular booking type in Antipolo. A <strong>resort in Antipolo private</strong> usually refers to a villa or compound that you book by the entire property for 22 to 24 hours. Your group gets the whole pool, function area, kitchen, and parking. No mixing with strangers.</p><p>This format works particularly well for family reunions, barkada outings, and small company offsites where the entire group fits in 20 to 40 people.</p>',
                '<h2>What you actually get</h2><p>Most private rentals in Antipolo include the main pool, sometimes a smaller kid pool, an air-conditioned function room or dining area, an outdoor grill or kitchen, parking for 6 to 12 cars, and a videoke setup. The fancier properties add a viewing deck overlooking Metro Manila, a small game room, or a hot tub.</p><h2>What to confirm before paying</h2><p>Three things. First, check-in and check-out times. Most run noon to noon, but some are 2 PM to 12 noon. Second, outside food policy. Most private rentals allow lechon and full catering with no corkage. Third, music cutoff time. Some properties have HOA rules that require quiet hours after 10 PM.</p><h2>Pricing snapshot</h2><p>Small villas for 10 to 15 pax: 6,000 to 12,000 PHP for the full day. Mid-size compounds for 20 to 35 pax: 12,000 to 22,000 PHP. Large estate-style rentals for 40+ pax: 22,000 to 50,000 PHP. Weekends cost about 25 to 40 percent more than weekdays.</p>',
                [
                    ['question' => 'How do I find a reliable private resort in Antipolo?', 'answer' => 'Check recent reviews, ask for video walk-throughs, and confirm in writing what is included. Word of mouth from family or friends remains the strongest signal.'],
                    ['question' => 'Is the price worth it over a regular resort?', 'answer' => 'For groups of 15+, yes. The per-head cost often comes out lower than entrance fees at a public resort, plus you get full privacy.'],
                    ['question' => 'Can I have a wedding at a private rental?', 'answer' => 'A handful of Antipolo private villas allow weddings. Most cap at 30 to 50 guests and have noise restrictions. Confirm before booking.'],
                    ['question' => 'How early should I book?', 'answer' => 'Six to eight weeks for major holidays. Three weeks for normal weekends. Last-minute weekday bookings are usually available.'],
                ],
                'Book a private resort in Antipolo with the whole villa to yourself. Pool, function area, parking, and full-day rental. Compare picks here.',
            ),

            'resort-in-binangonan-rizal' => $this->build(
                'Resort in Binangonan Rizal: Lakeside Stays Along Laguna de Bay',
                '<p>Binangonan sits along the eastern shore of Laguna de Bay, one of the closest lakefront escapes to Metro Manila. A <strong>resort in Binangonan Rizal</strong> tends to be smaller, family-run, and quieter than the famous Antipolo and Tanay properties. The draw is the lake views and the fresh-water fish.</p><p>Travel time from QC is around 60 to 75 minutes via Marcos Highway and the Manila East Road.</p>',
                '<h2>What Binangonan offers</h2><p>Lakeside fish farms, several pool resorts that capture the lake breeze, and a small number of boutique inns. Janosa Island is reachable by boat for a half-day side trip. The town does not have the polish of Tagaytay or the developed beach scene of Batangas, which is part of the appeal for travellers wanting a quieter weekend.</p><h2>Travel tips</h2><p>Manila East Road can flood during heavy rain. Check weather before traveling. Roads inside Binangonan are narrow in places, so smaller vehicles work better. Tilapia and other fresh-water fish are the local specialty and most resorts source from neighbouring fish pens.</p><h2>Pricing</h2><p>Pool resort day-use: 200 to 450 PHP per head. Overnight rooms: 1,800 to 4,200 PHP. Private villa rentals: 7,000 to 16,000 PHP for the full day.</p>',
                [
                    ['question' => 'Is Binangonan worth the trip over Antipolo?', 'answer' => 'For lakeside views and a quieter vibe, yes. Antipolo wins on city-view variety and food scene.'],
                    ['question' => 'Can I swim in Laguna de Bay?', 'answer' => 'Most resorts use their own pools, not the lake. Lake swimming is uncommon and not recommended due to water quality.'],
                    ['question' => 'What food should I try?', 'answer' => 'Fresh tilapia, hito, and the local kinilaw. Many resorts have on-site fish ponds with same-day catch.'],
                    ['question' => 'How long is the drive on a Saturday?', 'answer' => 'Around 75 to 90 minutes via Marcos Highway. Manila East Road tightens up around Cainta and Taytay.'],
                ],
                'Find a resort in Binangonan Rizal with lakeside views of Laguna de Bay, fresh tilapia, and quieter weekends than Antipolo. Compare picks here.',
            ),

            'resort-in-marikina' => $this->build(
                'Resort in Marikina: City Pools and Day-Use Picks Without Leaving Metro Manila',
                '<p>Marikina sits inside Metro Manila but has a surprising number of pool resorts and event venues that make it work as a quick same-day escape. A <strong>resort in Marikina</strong> usually means a city-edge pool, a small private villa, or a function venue for a barkada day. No long drive required.</p><p>Most resort properties are clustered in Barangka, Concepcion, and Marikina Heights, with smaller venues scattered throughout the city.</p>',
                '<h2>Who picks a Marikina resort</h2><p>Three groups: people who live in QC and Cubao who want zero travel time, families with elders who cannot handle long drives, and event organizers booking a pool venue for a birthday or small reunion. The trip is more about convenience than scenery.</p><h2>What to expect</h2><p>Small to mid-size pool resorts with cottages, a function area, and basic kitchen access. Most allow outside food. Day-use is the most common booking type. Overnight options exist but are less common.</p><h2>Pricing</h2><p>Day-use pool: 200 to 400 PHP per head. Private pool rental for groups of 15 to 25: 6,000 to 12,000 PHP for the full day. Function venue rentals: 5,000 to 15,000 PHP for an event of 30 to 60 pax.</p>',
                [
                    ['question' => 'Is a Marikina resort worth it over driving to a real out-of-town spot?', 'answer' => 'For half-day events and small reunions, yes. For weekend trips, drive out to Antipolo or further.'],
                    ['question' => 'Are there overnight options?', 'answer' => 'A few small inns and private villas offer overnight stays, but Marikina is primarily a day-use destination.'],
                    ['question' => 'Can I park easily?', 'answer' => 'Most pool resorts have parking for 5 to 10 cars. Bigger event venues accommodate up to 30. Confirm during booking.'],
                    ['question' => 'When are weekday bookings cheapest?', 'answer' => 'Monday to Wednesday for most properties. Some discount weekday rates by 20 to 30 percent.'],
                ],
                'Find a resort in Marikina for same-day pool weekends and small reunions without leaving Metro Manila. Compare picks here.',
            ),

            'resort-in-rizal' => $this->build(
                'Resort in Rizal: A Full Guide to the Province Closest to Manila',
                '<p>Rizal province wraps around the eastern edge of Metro Manila and is the closest weekend escape for QC and Marikina residents. A <strong>resort in Rizal</strong> can mean a hilltop villa in Antipolo, a lakeside stay in Binangonan, a hot-spring property in Taytay, or a riverside resort in Tanay. The province packs a lot of variety into a short drive.</p><p>This guide breaks down each of the main resort clusters so you can shortcut to the right type of weekend.</p>',
                '<h2>The four Rizal resort clusters</h2><p><strong>Antipolo</strong>: hilltop views, cool air, private pool villas. The most-booked corner of the province.</p><p><strong>Tanay</strong>: nature retreats, Daranak Falls, mountainside resorts, the famous Masungi Georeserve nearby.</p><p><strong>Binangonan and Cardona</strong>: lakeside stays along Laguna de Bay.</p><p><strong>Rodriguez (Montalban) and San Mateo</strong>: river resorts and waterfall destinations.</p><h2>Travel times from QC</h2><p>Antipolo: 45 to 60 min. Binangonan: 60 to 75 min. Tanay: 90 to 120 min. Rodriguez: 60 to 75 min. San Mateo: 45 to 60 min. Taytay: 45 to 60 min.</p><h2>What to budget</h2><p>Day-use pool: 200 to 700 PHP per head. Hilltop villa rental: 8,000 to 22,000 PHP. Riverside resort overnight: 2,500 to 6,500 PHP. Nature retreat overnight: 3,500 to 8,500 PHP.</p>',
                [
                    ['question' => 'Which part of Rizal is best for first-timers?', 'answer' => 'Antipolo for variety and short drive. Tanay if you want nature and hiking.'],
                    ['question' => 'Can I do a Rizal day trip without overnight?', 'answer' => 'Easily. Most travellers from QC do day trips to Antipolo for sunset dinners or to Tanay for Daranak Falls and back the same night.'],
                    ['question' => 'Is Rizal good for kids?', 'answer' => 'Yes. Most resorts have kid pools, the natural attractions like waterfalls are family-friendly, and travel times are short enough to avoid restless kids.'],
                    ['question' => 'When is the best time of year?', 'answer' => 'December to February for cooler weather. Avoid rainy season July to September if you plan to visit waterfalls or hike.'],
                ],
                'Find the right resort in Rizal province. Antipolo, Tanay, Binangonan, Rodriguez, San Mateo, and Taytay options compared.',
            ),

            'resort-in-rizal-province' => $this->build(
                'Resort in Rizal Province: Picking the Right Town for Your Weekend',
                '<p>Rizal province is the easternmost neighbour of Metro Manila and one of the most accessible weekend escapes for QC and east-side residents. A <strong>resort in Rizal province</strong> can mean dozens of different things depending on the town. This page breaks down what each town actually offers and how to pick the right one for your trip.</p><p>The longer phrase "Rizal province" usually disambiguates from properties in other Rizals across the country, so you are landing on the Luzon-side Rizal here.</p>',
                '<h2>By town, what to expect</h2><p><strong>Antipolo</strong>: hilltop views and pool villas. Best for first-time visitors.</p><p><strong>Tanay</strong>: nature retreats and waterfalls. Best for hiking and quieter weekends.</p><p><strong>Binangonan</strong>: lakeside stays. Best for fish meals and quieter weekends.</p><p><strong>Rodriguez (Montalban)</strong>: river resorts and cave systems. Best for adventure groups.</p><p><strong>San Mateo</strong>: river resorts and small pool venues. Best for short day trips.</p><p><strong>Taytay</strong>: pool resorts and event venues. Best for reunions and birthdays.</p><h2>Pricing snapshot</h2><p>Day-use entry across most Rizal pool resorts: 200 to 700 PHP per head. Mid-tier private villa rentals: 8,000 to 18,000 PHP for the full 22 hours. Nature retreat overnight rates: 3,000 to 8,000 PHP per night.</p>',
                [
                    ['question' => 'Is Rizal province cheaper than Cavite or Batangas?', 'answer' => 'Roughly comparable for similar property types, but the shorter drive saves on gas and travel time, which adds up.'],
                    ['question' => 'Are there beach resorts in Rizal province?', 'answer' => 'No. Rizal has lakes, rivers, and waterfalls but no coastline. For beaches stay in Batangas, La Union, or further.'],
                    ['question' => 'What is the most popular town?', 'answer' => 'Antipolo by booking volume, followed by Tanay for the nature crowd.'],
                    ['question' => 'How early should I book?', 'answer' => 'Three to four weeks for weekends. Six to eight weeks for major holidays.'],
                ],
                'Find a resort in Rizal province across Antipolo, Tanay, Binangonan, Rodriguez, San Mateo, and Taytay. Compare picks by town.',
            ),

            'resort-in-rodriguez-rizal' => $this->build(
                'Resort in Rodriguez Rizal: River Resorts in the Town Locally Known as Montalban',
                '<p>Rodriguez is the official name for what most locals still call Montalban. The town sits along the Marikina River and is a favorite weekend spot for QC and Marikina residents who want a river-based escape without long travel. A <strong>resort in Rodriguez Rizal</strong> typically means a riverside pool resort, a cave-system day-trip base, or a small private villa in the higher barangays.</p><p>Travel time from QC is around 60 to 75 minutes via Marikina-Infanta Highway.</p>',
                '<h2>What the town is known for</h2><p>The Wawa Dam area is the historic landmark with hiking trails and view points. The Avilon Zoo sits nearby. River-fed pools at several barangay-level resorts give cold-water relief during summer months. Cave systems in barangay Mascap attract adventure groups.</p><h2>What to expect at a resort</h2><p>Most properties run as day-use pool venues with the option to extend to overnight. Cottages are basic, food is usually included or available on-site, and the river is right outside for those who want to do short tubing or wading.</p><h2>Pricing</h2><p>Day-use entrance: 150 to 350 PHP per head. Overnight cottage: 1,500 to 3,500 PHP. Private villa rentals: 6,000 to 14,000 PHP for the full 22 hours. Holy Week sees the highest demand because of cold-water pools.</p>',
                [
                    ['question' => 'Is Rodriguez safe for visitors?', 'answer' => 'Yes, particularly the tourist zones around Wawa Dam and the established resorts. Standard travel awareness applies elsewhere.'],
                    ['question' => 'How does Rodriguez compare to Tanay?', 'answer' => 'Rodriguez is closer and more accessible. Tanay has more developed nature retreats and the Masungi Georeserve.'],
                    ['question' => 'Can I swim in the river itself?', 'answer' => 'Yes at designated spots, though most travellers stick to the resort pools. River currents can pick up after rain.'],
                    ['question' => 'When is the best season?', 'answer' => 'March to May for the cold-water pool relief. Avoid June to October when typhoon-driven floods can affect the river.'],
                ],
                'Find a resort in Rodriguez Rizal (Montalban) with river pools, Wawa Dam access, and adventure caves. Compare picks here.',
            ),

            'resort-in-san-mateo-rizal' => $this->build(
                'Resort in San Mateo Rizal: River-Side and Pool Venues Just East of Metro Manila',
                '<p>San Mateo is one of the closest Rizal towns to QC and Marikina, sitting along the Marikina River and the foothills of the Sierra Madre. A <strong>resort in San Mateo Rizal</strong> usually means a riverside pool resort, a private villa rental, or a small day-use venue for family events.</p><p>Travel time from QC is around 45 to 60 minutes via Marikina-Infanta Highway.</p>',
                '<h2>What the town offers</h2><p>San Mateo is less developed than Antipolo for resort tourism, which makes it a quieter option. The town sits at lower elevation than Antipolo but the river-fed pools give cool-water relief in summer. Several small private villas operate in the higher barangays toward the Sierra Madre.</p><h2>Booking notes</h2><p>Most San Mateo properties are smaller family-run operations, not chain resorts. Direct booking via phone or messenger is more common than online platforms. Saturday afternoons fill up fastest during March to May.</p><h2>Pricing</h2><p>Day-use entrance: 150 to 350 PHP per head. Overnight cottages: 1,500 to 3,500 PHP. Private villa rentals: 6,000 to 12,000 PHP for the full 22 hours. Most properties allow outside food.</p>',
                [
                    ['question' => 'How does San Mateo compare to Marikina?', 'answer' => 'San Mateo has more space per property and access to the river. Marikina is even closer but more urban.'],
                    ['question' => 'Are San Mateo resorts good for kids?', 'answer' => 'Yes. Most have kid pools, the cottages are spacious, and the rivers are shallow enough at most spots for supervised wading.'],
                    ['question' => 'When does traffic to San Mateo peak?', 'answer' => 'Saturday mornings 9 AM to 11 AM and Sunday returns 4 PM to 7 PM. Going earlier or later avoids both peaks.'],
                    ['question' => 'Can I do a Sunday day trip and be back for dinner?', 'answer' => 'Yes, comfortably. Most groups arrive 8 AM and leave by 4 PM to beat the return traffic.'],
                ],
                'Find a resort in San Mateo Rizal with riverside pools, private villas, and a short drive from Manila. Compare picks here.',
            ),

            'resort-in-tanay' => $this->build(
                'Resort in Tanay: Mountain Air, Daranak Falls, and Masungi-Adjacent Stays',
                '<p>Tanay sits at the easternmost edge of Rizal province, in the foothills of the Sierra Madre. A <strong>resort in Tanay</strong> usually means a nature-focused property with hiking access, a mountainside resort with cool weather, or a private retreat near Daranak Falls. The town has become a major weekend nature destination thanks to its proximity to Masungi Georeserve.</p><p>Travel time from QC is around 90 to 120 minutes via Marcos Highway and the Sampaloc Road.</p>',
                '<h2>The Tanay attractions</h2><p>Daranak Falls is the main waterfall draw, an easy 20-minute walk from parking. Masungi Georeserve requires advance booking and offers guided trails over its famous limestone formations. Pranjetto Hills, Mount Daraitan, and the Tinipak River are nearby for more ambitious trekkers.</p><h2>What kind of resorts exist</h2><p>Boutique mountain inns, glamping setups, private villa rentals, and a few larger pool resorts with function halls. Most Tanay properties favor the nature experience over the swim-and-grill format you see in Pansol or Bacoor.</p><h2>Pricing</h2><p>Glamping overnight: 2,500 to 5,500 PHP per person including breakfast. Boutique inn rooms: 3,500 to 8,500 PHP per night. Private villa rentals: 10,000 to 25,000 PHP for the full day. Masungi entry fees are separate.</p>',
                [
                    ['question' => 'Do I need to book Masungi in advance?', 'answer' => 'Yes. Masungi requires reservation weeks ahead, sometimes months for weekends.'],
                    ['question' => 'Is Tanay good for non-hikers?', 'answer' => 'Yes. Several resorts offer the cool mountain air and views without requiring a trail. Daranak Falls is accessible to most visitors.'],
                    ['question' => 'When is the best time to visit?', 'answer' => 'December to April for the dry season. Waterfalls flow stronger after rain but trails get muddy.'],
                    ['question' => 'How is the road condition?', 'answer' => 'Mostly good. The last few kilometers to some properties become narrow and steep. Sedans handle most of the trip but check with the property.'],
                ],
                'Find a resort in Tanay with Daranak Falls access, Masungi Georeserve nearby, mountain inns, and glamping. Compare picks here.',
            ),

            'resort-in-taytay-rizal' => $this->build(
                'Resort in Taytay Rizal: Pool Resorts and Event Venues at Manila\'s Eastern Doorstep',
                '<p>Taytay sits right at the eastern edge of Metro Manila, between Pasig and Antipolo. A <strong>resort in Taytay Rizal</strong> usually means a pool resort with a function hall, a family day venue, or a small private villa rental. Travel time from QC is around 45 to 60 minutes, making it one of the fastest out-of-town options.</p><p>Most properties cluster along Manila East Road and the side streets in Dolores, San Juan Taytay, and Muzon.</p>',
                '<h2>What Taytay is known for</h2><p>Tiangge shopping, the famous Taytay public market that draws weekend bargain hunters from across Metro Manila. The resort scene runs parallel to this, serving families who combine a shopping morning with a pool afternoon.</p><h2>Resort types</h2><p>Day-use pool venues are the most common, with entrance fees at 150 to 400 PHP per head. Private pool villa rentals for 20 to 30 pax run 6,000 to 14,000 PHP for the full day. A handful of small event venues handle weddings and reunions of 50 to 100 pax.</p><h2>Travel</h2><p>Manila East Road is the standard route. Avoid Saturday afternoon traffic by traveling before 9 AM or after 6 PM. The road floods occasionally during heavy rain, so check weather forecasts during typhoon season.</p>',
                [
                    ['question' => 'Is Taytay good for first-time resort visits?', 'answer' => 'Yes, especially for families with kids. Short drive, predictable amenities, easy parking.'],
                    ['question' => 'Can I combine Taytay shopping with a resort stay?', 'answer' => 'Many travellers do. Morning at the tiangge, afternoon at the pool, dinner back home in Manila.'],
                    ['question' => 'How is the food at Taytay resorts?', 'answer' => 'Most allow outside food, which is the more common arrangement. On-site kitchens vary in quality.'],
                    ['question' => 'Are there overnight options?', 'answer' => 'Yes, mostly small inns and private villas. Big chain hotels are rare in Taytay itself.'],
                ],
                'Find a resort in Taytay Rizal. Pool resorts, function halls, and quick Manila access. Compare picks here.',
            ),
        ];
    }
}
