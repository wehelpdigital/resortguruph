<?php

namespace Database\Seeders;

class BulkContent05NorthLuzon extends BulkContentBase
{
    protected function pages(): array
    {
        return [
            'resort-in-subic' => $this->build(
                'Resort in Subic: Beach Hotels, Family Pools, and Freeport-Zone Stays',
                '<p>Subic Bay Freeport Zone sits in Zambales and has become one of the most-visited weekend destinations from Manila. A <strong>resort in Subic</strong> usually means a beach hotel, a family pool resort inside the Freeport, or a higher-tier accommodation near Ocean Adventure and Zoobic Safari. The area packs a lot of variety into a 3-hour drive from QC.</p><p>Travel time is around 2.5 to 3 hours via NLEX and SCTEX.</p>',
                '<h2>The Subic resort zones</h2><p><strong>Inside the Freeport</strong>: family resorts like Subic Bay Yacht Club, Court Meridian, and the various Lighthouse Marina options. Closer to attractions and easier security.</p><p><strong>Outside the Freeport (Olongapo and Barrio Barretto)</strong>: smaller hotels and budget inns. Lively nightlife but less family-oriented.</p><p><strong>Beach resorts in Camayan and Anvaya</strong>: white-sand stretches on the Bataan side.</p><h2>What to do</h2><p>Ocean Adventure for marine shows. Zoobic Safari for the tiger feeding. The All-Hands Beach for swim days. Several mountain hikes including Mount Pinatubo trailheads operate from this side.</p><h2>Pricing</h2><p>Freeport hotel rooms: 4,500 to 12,000 PHP per night. Beachfront resort rooms: 5,500 to 14,000 PHP. Day-use beach access: 250 to 600 PHP per head.</p>',
                [
                    ['question' => 'Is Subic safe for families?', 'answer' => 'Yes, particularly inside the Freeport zone. Strict gate security and well-patrolled streets.'],
                    ['question' => 'Should I stay inside the Freeport or outside?', 'answer' => 'Inside for family trips. Outside for cheaper rates and nightlife.'],
                    ['question' => 'How early should I book Ocean Adventure?', 'answer' => 'Two to four weeks for weekend slots. Same-day booking is risky during peak season.'],
                    ['question' => 'Is the beach water clean?', 'answer' => 'Yes at the curated beaches like Camayan, Anvaya, and All-Hands. Off-the-beaten paths vary.'],
                ],
                'Find a resort in Subic. Freeport hotels, Camayan beach, family attractions, and Bataan-side white sand. Compare picks here.',
            ),

            'resort-in-subic-zambales' => $this->build(
                'Resort in Subic Zambales: The Wider Subic Area From Olongapo to San Antonio',
                '<p>Subic Zambales refers to the wider zone beyond the Freeport itself, stretching north along the Zambales coast through San Felipe, San Antonio, and Iba. A <strong>resort in Subic Zambales</strong> can mean a Freeport hotel, a beachfront property in Camayan or Anvaya, or a quieter coastal resort further north along the coastline.</p><p>Travel time from QC: Olongapo and the Freeport gates around 2.5 to 3 hours, San Antonio and beyond up to 4 hours.</p>',
                '<h2>The two flavors of Subic Zambales</h2><p>The first is the Freeport-and-immediate-area cluster, which is family-tourist focused with attractions and curated beaches. The second is the wider Zambales coastline, which is quieter, surfier, and includes the famous Nagsasa Cove and Anawangin Cove camping spots reachable by boat.</p><h2>What to budget</h2><p>Freeport hotels: 4,500 to 12,000 PHP per night. Mid-coast beach resorts north of Subic: 3,000 to 7,500 PHP. Boat-access cove camping: 1,500 to 3,500 PHP per person including boat rental for the trip.</p><h2>When to visit</h2><p>November to May for the dry season and calm seas. The Nagsasa-Anawangin coves are best in March to May for the bonfire camping experience.</p>',
                [
                    ['question' => 'Are the Anawangin and Nagsasa coves part of Subic?', 'answer' => 'They are nearby in San Antonio, Zambales. Most travellers reach them by boat from Pundaquit village.'],
                    ['question' => 'Is the Freeport zone the same as Subic Zambales?', 'answer' => 'The Freeport is a smaller administrative zone inside Subic Bay. Subic Zambales refers to the wider area including the Freeport and surrounding municipalities.'],
                    ['question' => 'Can I drive to the Anawangin cove?', 'answer' => 'No, only by boat from Pundaquit. The boat ride takes 45 to 60 minutes.'],
                    ['question' => 'When is the surfing season further north?', 'answer' => 'November to February for the bigger waves at Crystal Beach and surrounds.'],
                ],
                'Find a resort in Subic Zambales. Freeport hotels, Anawangin coves, north-coast beaches, and family options compared.',
            ),

            'resort-in-bataan' => $this->build(
                'Resort in Bataan: Beach Resorts and Historic Stays in the Peninsular Province',
                '<p>Bataan sits across the bay from Manila and has built a strong resort tourism scene around its western beaches and the historic sites of Mount Samat and Corregidor (visible from Mariveles). A <strong>resort in Bataan</strong> usually means a Morong beachfront property, an Anvaya Cove-area resort, or a smaller coastal stay in the southern parts of the peninsula.</p><p>Travel time from QC is around 2.5 to 3.5 hours via NLEX and the Roman Highway.</p>',
                '<h2>The Bataan resort zones</h2><p><strong>Morong</strong>: the most-visited beach town with several mid-range beach resorts and the Bataan Nuclear Power Plant landmark.</p><p><strong>Anvaya Cove area (Morong)</strong>: gated community with the famous Pico de Loro-style beach club.</p><p><strong>Bagac and Mariveles</strong>: smaller coastal resorts with Las Casas Filipinas heritage village in Bagac.</p><p><strong>Hermosa and Dinalupihan</strong>: inland pool resorts for travellers wanting a non-beach stay.</p><h2>Pricing</h2><p>Mid-range beach resort rooms: 3,500 to 8,500 PHP per night. Las Casas Filipinas heritage stay: 6,500 to 18,000 PHP. Anvaya Cove rentals: 8,000 to 22,000 PHP per night. Day-use beach: 300 to 800 PHP per head.</p>',
                [
                    ['question' => 'Is Bataan beach better than Subic?', 'answer' => 'The Bataan side has longer stretches of sand with fewer crowds. Subic has more curated amenities.'],
                    ['question' => 'Worth visiting Las Casas Filipinas?', 'answer' => 'Yes if you appreciate heritage architecture. Even a day visit is worthwhile.'],
                    ['question' => 'Can I do a day trip to Corregidor from Bataan?', 'answer' => 'Most Corregidor trips depart from Manila by ferry. From Bataan it is technically possible but logistically harder.'],
                    ['question' => 'When is the best time to visit?', 'answer' => 'November to May for dry weather and calm seas.'],
                ],
                'Find a resort in Bataan. Morong beach resorts, Anvaya Cove, Las Casas Filipinas heritage stays, and historic sites. Compare here.',
            ),

            'resort-in-morong-bataan' => $this->build(
                'Resort in Morong Bataan: The Beach Town Just Past the Nuclear Plant',
                '<p>Morong is the beach town on the southwestern tip of Bataan, right next to the famous Bataan Nuclear Power Plant landmark. A <strong>resort in Morong Bataan</strong> usually means a beachfront property along Sabang or Saysain, a private cottage rental in Mabayo, or the higher-end Anvaya Cove community on the same coastline.</p><p>Travel time from QC is around 3 to 3.5 hours via NLEX, Roman Highway, and the Bagac-Morong road.</p>',
                '<h2>Morong\'s beach options</h2><p>Sabang Beach is the public-access stretch with several mid-range resorts. The water here is calm and family-friendly. Mabayo and Saysain are quieter neighbouring stretches with smaller boutique resorts. Anvaya Cove sits on the same coast and operates as a gated community with day-use access for non-members at scheduled times.</p><h2>What to budget</h2><p>Sabang beachfront rooms: 3,200 to 7,500 PHP per night. Mid-tier boutique resorts: 4,500 to 10,000 PHP. Anvaya Cove day-use: 2,500 to 4,500 PHP per head. Group cottage rentals: 8,000 to 18,000 PHP for the full day.</p><h2>What else to do</h2><p>The Bataan Nuclear Power Plant is open for guided tours and is a unique side trip. Las Casas Filipinas in nearby Bagac is a heritage village resort worth a half-day visit even if you stay elsewhere.</p>',
                [
                    ['question' => 'How clean is Morong beach?', 'answer' => 'Generally clean at the established resorts. Public stretches require closer inspection.'],
                    ['question' => 'Can I tour the nuclear plant?', 'answer' => 'Yes via the BNPP visitor program. Book in advance through the tourism office.'],
                    ['question' => 'Is Anvaya Cove open to non-members?', 'answer' => 'Day-use access is available with restrictions. Members get priority booking.'],
                    ['question' => 'How long is the drive from Manila?', 'answer' => 'About 3 to 3.5 hours via NLEX. Last hour is on local roads through Bagac.'],
                ],
                'Find a resort in Morong Bataan with Sabang beachfront, Anvaya Cove access, and the BNPP landmark. Compare picks here.',
            ),

            'resort-in-pangasinan' => $this->build(
                'Resort in Pangasinan: From Bolinao Beaches to Hundred Islands National Park',
                '<p>Pangasinan stretches along the Lingayen Gulf and the South China Sea, packing a lot of coastal variety into one province. A <strong>resort in Pangasinan</strong> can mean a Bolinao beachfront with white sand, a Hundred Islands base in Alaminos, an Urdaneta city hotel, or a quieter inland stay near the Bued River. The province has options for almost every traveler type.</p><p>Travel time from QC is around 4 to 5 hours via TPLEX to Urdaneta, then onward.</p>',
                '<h2>The Pangasinan resort clusters</h2><p><strong>Bolinao</strong>: white-sand beach resorts, the famous Patar Beach, lighthouse.</p><p><strong>Alaminos (Hundred Islands)</strong>: island-hopping base, beachfront resorts overlooking Lucap Bay.</p><p><strong>Lingayen</strong>: capital town, longer stretches of less-crowded beach.</p><p><strong>Urdaneta and Dagupan</strong>: city hotels, business travel base.</p><p><strong>San Fabian</strong>: inland beach town with several Spanish-era churches.</p><h2>Pricing</h2><p>Beachfront resort rooms in Bolinao: 3,500 to 8,500 PHP per night. Alaminos Hundred Islands base: 2,500 to 6,500 PHP. City hotel rooms: 2,000 to 5,000 PHP. Island-hopping boat rental: 1,800 to 2,800 PHP for a full-day tour of 5 to 8 people.</p>',
                [
                    ['question' => 'Should I stay in Bolinao or Alaminos?', 'answer' => 'Bolinao for the beach. Alaminos for island-hopping.'],
                    ['question' => 'Is Hundred Islands worth the trip?', 'answer' => 'Yes, particularly for first-time visitors. Try to do at least three islands in a full-day tour.'],
                    ['question' => 'How long is the drive from QC?', 'answer' => 'TPLEX has cut Pangasinan travel significantly. Bolinao is about 4.5 hours, Urdaneta 3.5 hours, Lingayen 4 hours.'],
                    ['question' => 'When is the best time to visit?', 'answer' => 'November to May for the dry season. June to October has typhoons that can disrupt island-hopping tours.'],
                ],
                'Find a resort in Pangasinan. Bolinao beaches, Hundred Islands base, Lingayen, and Urdaneta. Compare picks here.',
            ),

            'resort-in-bolinao' => $this->build(
                'Resort in Bolinao: White Sand and Lighthouse Views in Northern Pangasinan',
                '<p>Bolinao sits at the northwest tip of Pangasinan facing the South China Sea. A <strong>resort in Bolinao</strong> usually means a Patar Beach white-sand resort, a coastal property near the Cape Bolinao Lighthouse, or a quieter boutique stay along the road to Anda. The town is famous for its long, less-crowded beaches and the easy access to dive spots.</p><p>Travel time from QC is around 4.5 hours via TPLEX, then Lingayen-Bolinao road.</p>',
                '<h2>The Bolinao attractions</h2><p>Patar Beach is the headline attraction, a long white-sand stretch with several mid-range resorts. The Cape Bolinao Lighthouse offers panoramic sunset views. Bolinao Falls (multiple cascades) is a short trip inland. Snorkeling and freediving spots line the western coast.</p><h2>What to pick</h2><p>Beachfront resort rooms run 3,500 to 8,500 PHP per night. Smaller cottage rentals are 1,800 to 3,500 PHP. Day-use entrance to private resort beaches: 250 to 500 PHP per head. Bring cash since not all properties accept cards or e-wallets.</p>',
                [
                    ['question' => 'Is Patar Beach really white sand?', 'answer' => 'Yes, especially the central stretch. Quality varies between resort sections.'],
                    ['question' => 'Are Bolinao Falls worth visiting?', 'answer' => 'Yes for a half-day side trip. The multiple cascades and natural pools are family-friendly.'],
                    ['question' => 'How does Bolinao compare to La Union?', 'answer' => 'Bolinao has whiter sand and is less crowded. La Union has the surf scene and more developed nightlife.'],
                    ['question' => 'When is the best time to visit?', 'answer' => 'November to May for calm seas. February to April for the cleanest beach conditions.'],
                ],
                'Find a resort in Bolinao with white-sand Patar Beach, lighthouse views, and quieter weekends. Compare picks here.',
            ),

            'resort-in-la-union' => $this->build(
                'Resort in La Union: Surf Stays in San Juan and Family Beaches in Bauang',
                '<p>La Union is the surf capital of the Philippines and one of the most-visited beach destinations from Manila in the past few years. A <strong>resort in La Union</strong> usually means a San Juan surf lodge, a Bauang family beach resort, or a small boutique inn along the Manila North Road. The province packs surfing, beaches, and food into a 5-hour drive.</p><p>Travel time from QC is around 5 to 6 hours via TPLEX and Manila North Road.</p>',
                '<h2>The two La Union flavors</h2><p>San Juan is the surf town, busiest barangay is Urbiztondo. Backpacker hostels, surf hotels, kombi cafes, and a younger crowd dominate. Bauang is calmer with family beach resorts, pools, and quieter sand. San Fernando city sits between them and has the business hotels.</p><h2>Pricing</h2><p>Surf hostel dorms: 600 to 1,200 PHP per night. Surf-friendly hotels: 2,500 to 5,500 PHP. Bauang family beach resorts: 3,000 to 7,500 PHP. Premium boutique surf lodges: 5,500 to 12,000 PHP. Surf lessons: 400 PHP for a 2-hour beginner package including board rental.</p>',
                [
                    ['question' => 'When is the surf season?', 'answer' => 'October to March for bigger waves. April to September has calmer water but smaller swells.'],
                    ['question' => 'Is La Union family-friendly?', 'answer' => 'Yes, especially the Bauang and San Fernando side. Surf-side San Juan is also family-friendly during calm months.'],
                    ['question' => 'How long does it take by bus?', 'answer' => 'Around 6 to 7 hours from Cubao or Pasay. Several companies run hourly trips.'],
                    ['question' => 'What food should I try?', 'answer' => 'Halo-halo at Halo-Halo de Iloko, dinakdakan, bagnet, and the famous local longganisa.'],
                ],
                'Find a resort in La Union. San Juan surf stays, Bauang family beaches, and boutique inns. Compare picks here.',
            ),

            'resort-in-nueva-ecija' => $this->build(
                'Resort in Nueva Ecija: Inland Pool Resorts and Farm Stays in Rice Country',
                '<p>Nueva Ecija is the rice granary of the Philippines and a quieter resort destination than the more famous beach provinces. A <strong>resort in Nueva Ecija</strong> usually means an inland pool resort, a farm stay, or a private pool villa rental. The province appeals to travellers who want a rural setting without the long drive to Pangasinan or Pampanga.</p><p>Travel time from QC is around 2.5 to 3 hours via NLEX and SCTEX.</p>',
                '<h2>The resort clusters</h2><p><strong>Cabanatuan</strong>: the largest city, has business hotels and family pool resorts. Best for first-time visitors.</p><p><strong>San Jose City and Munoz</strong>: agricultural town centers, smaller resorts.</p><p><strong>Talavera and Aliaga</strong>: rural pool resorts and farm retreats.</p><p><strong>Pantabangan</strong>: the famous Pantabangan Lake area for nature-based stays.</p><h2>Pricing</h2><p>Business hotel rooms: 1,800 to 4,200 PHP. Pool resort day-use: 200 to 400 PHP per head. Farm stays: 2,200 to 5,500 PHP per night. Private villa rentals: 6,000 to 14,000 PHP for the full day.</p>',
                [
                    ['question' => 'Is Nueva Ecija worth a weekend trip?', 'answer' => 'For travellers who prefer rural calm over beach noise, yes. The province is underrated for quiet weekends.'],
                    ['question' => 'Are there hot springs in Nueva Ecija?', 'answer' => 'Limited. The province does not have the geothermal activity of Laguna or Pampanga.'],
                    ['question' => 'What food should I try?', 'answer' => 'Tinapay ng Cabanatuan, halamanan rice cakes, and the famous Cabanatuan tapa.'],
                    ['question' => 'When is the best time to visit?', 'answer' => 'November to April for dry weather. Avoid June to October when rice paddy flooding is common.'],
                ],
                'Find a resort in Nueva Ecija with farm stays, pool resorts, and Pantabangan Lake. Compare quiet inland picks here.',
            ),

            'resort-in-tarlac' => $this->build(
                'Resort in Tarlac: Pool Resorts and Inland Stays in Central Luzon',
                '<p>Tarlac sits in central Luzon between Pampanga and Pangasinan. A <strong>resort in Tarlac</strong> usually means an inland pool resort, a hacienda-style stay near the Cojuangco-Aquino heritage areas, or a small private villa rental. The province is known for its rice fields, the Aquino family heritage sites, and Mount Pinatubo trail access.</p><p>Travel time from QC is around 2 to 2.5 hours via NLEX and SCTEX.</p>',
                '<h2>What you find in Tarlac</h2><p>Pool resorts along the MacArthur Highway corridor. Family event venues in Tarlac City. Hacienda-style stays in Concepcion and La Paz. Mount Pinatubo trailhead access from Capas. Las Casas de Acuzar branded heritage stays in some parts of the province.</p><h2>Pricing</h2><p>Day-use pool resort: 250 to 500 PHP per head. Overnight rooms: 1,800 to 4,500 PHP. Private villa rentals: 6,000 to 14,000 PHP for the full day. Hacienda heritage stays: 3,500 to 7,500 PHP.</p><h2>Mount Pinatubo</h2><p>The 4x4 trek to the Pinatubo crater starts from Capas, Tarlac. Most tours run from October to May. Several Capas resorts offer overnight packages with the next-day trek included.</p>',
                [
                    ['question' => 'Is Tarlac good for nature trips?', 'answer' => 'Yes, particularly for Mount Pinatubo trekking. The 4x4 ride and short hike to the crater are unforgettable.'],
                    ['question' => 'How is the food scene?', 'answer' => 'Strong on Pampangan-Ilocano fusion. Tarlac is known for its fresh longganisa and pinakbet.'],
                    ['question' => 'When is the Pinatubo trek season?', 'answer' => 'October to May. Trips often suspend during typhoon season due to crater lake water levels and trail conditions.'],
                    ['question' => 'Are there family pool resorts?', 'answer' => 'Yes, particularly in Tarlac City and along the MacArthur Highway. Several offer day-use and event packages.'],
                ],
                'Find a resort in Tarlac. Pool resorts, Pinatubo trek base, hacienda stays, and family venues. Compare picks here.',
            ),

            'resort-in-dingalan-aurora' => $this->build(
                'Resort in Dingalan Aurora: Pacific Coast Cliffs and Quiet Beach Stays',
                '<p>Dingalan sits on the Pacific coast of Aurora province, right where the Sierra Madre meets the sea. A <strong>resort in Dingalan Aurora</strong> typically means a small beachfront inn, a cliffside cottage, or a homestay with views over the Pacific. The town is known for its dramatic coastline, particularly the cliffs at Lamao and the Dingalan Lighthouse.</p><p>Travel time from QC is around 4 to 5 hours via Cabanatuan and the Aurora-Pampanga road.</p>',
                '<h2>What to expect</h2><p>Quieter than Real or Baler. Smaller resorts run by local families. Dramatic seascapes with limestone cliffs and the option of short boat tours to nearby coves. The beach itself is small, dark sand, with stronger currents than the western Luzon beaches.</p><h2>Things to do</h2><p>Dingalan Lighthouse for sunrise views. The Lamao Cliffs for photography. White Beach reached by short boat ride. Whale-watching is possible during certain months. Tanawan Viewing Deck for panoramic shots.</p><h2>Pricing</h2><p>Beach inn rooms: 1,500 to 3,800 PHP per night. Cliffside boutique cottages: 2,500 to 5,500 PHP. Most properties are small operations with home-cooked meal options.</p>',
                [
                    ['question' => 'Is Dingalan good for swimming?', 'answer' => 'Limited. The currents on the Pacific side can be strong. Most travellers come for the cliffs and views, not for swimming.'],
                    ['question' => 'How does Dingalan compare to Baler?', 'answer' => 'Dingalan is quieter and less developed. Baler has more amenities, surfing, and a bigger food scene.'],
                    ['question' => 'When is the best time to visit?', 'answer' => 'December to May for the calmest seas. June to November has typhoons that affect the Pacific coast directly.'],
                    ['question' => 'Is there cell signal?', 'answer' => 'Spotty but improving. Globe and Smart both have 4G in town center but weaker in the outskirts.'],
                ],
                'Find a resort in Dingalan Aurora. Pacific coast cliffs, lighthouse views, quiet beach inns. Compare picks here.',
            ),

            'resort-in-hundred-islands' => $this->build(
                'Resort in Hundred Islands: Bases for Visiting the Famous Pangasinan Archipelago',
                '<p>Hundred Islands National Park is a collection of 124 small islands in Lingayen Gulf, off the coast of Alaminos, Pangasinan. A <strong>resort in Hundred Islands</strong> usually means a base in Alaminos city or along the coast at Lucap Wharf, since none of the actual 124 islands have full resorts (a few have basic camping setups).</p><p>Travel time from QC is around 4.5 to 5 hours via TPLEX and the Alaminos road.</p>',
                '<h2>The Alaminos resort base</h2><p>Most resorts cluster around Lucap Wharf in Alaminos, where the island-hopping boats launch. Mid-range beachfront properties offer rooms with views of the islands and include arrangements for the full-day boat tour the next morning. Boats run between 1,800 and 2,800 PHP for a full-day tour of 5 to 8 people.</p><h2>Which islands to visit</h2><p>Governor\'s Island has the famous viewing tower seen on most travel posters. Quezon Island has the largest accessible beach with cottages. Children\'s Island is shallow and family-friendly. Marcos Island has the Imelda Cave. A typical full-day tour covers 4 to 6 of these.</p><h2>Pricing</h2><p>Lucap Wharf base resorts: 2,800 to 6,500 PHP per night. Premium beachfront stays: 5,500 to 11,000 PHP. Island camping at Quezon or Governor\'s: 350 PHP per tent space plus boat fees.</p>',
                [
                    ['question' => 'Can I stay overnight on one of the islands?', 'answer' => 'Yes at a few designated camping zones like Quezon Island. Bring your own gear. Most travellers stay on the mainland.'],
                    ['question' => 'How long should I plan for the tour?', 'answer' => 'A full day, roughly 7 AM to 4 PM. Covers 4 to 6 islands depending on the package.'],
                    ['question' => 'Is the water swimmable everywhere?', 'answer' => 'Most islands have safe shallow swim zones. Currents pick up between islands so stick to designated spots.'],
                    ['question' => 'When is the best time to visit?', 'answer' => 'November to May for calm seas. Avoid typhoon season June to October.'],
                ],
                'Find a resort in Hundred Islands. Alaminos and Lucap Wharf bases for island-hopping. Compare picks here.',
            ),

            'resort-in-urdaneta-city-pangasinan' => $this->build(
                'Resort in Urdaneta City Pangasinan: City Hotels and Pool Stays at the Province Gateway',
                '<p>Urdaneta City is the main TPLEX exit for Pangasinan and a common overnight stop for travellers heading to Bolinao, Hundred Islands, or further north. A <strong>resort in Urdaneta City Pangasinan</strong> usually means a business hotel, a small pool resort, or a budget inn near the McArthur Highway. The city itself does not have a beach but works well as a hub.</p><p>Travel time from QC is around 3 to 3.5 hours via TPLEX.</p>',
                '<h2>Why stay in Urdaneta</h2><p>Three reasons. First, it is the most accessible Pangasinan city via TPLEX. Second, the hotel pricing is significantly lower than coastal Bolinao or Hundred Islands. Third, it is a 1 to 1.5 hour drive to most major Pangasinan attractions, which works well for travellers doing multi-stop day trips.</p><h2>Pricing</h2><p>Business hotel rooms: 1,800 to 4,500 PHP per night. Day-use pool resorts: 200 to 400 PHP per head. Private villa rentals (limited): 5,000 to 10,000 PHP for the full day. Restaurant meals: 200 to 450 PHP per person at most local spots.</p><h2>Things to do nearby</h2><p>San Jacinto Cathedral, the public market, the Manaoag Shrine for the pilgrimage crowd. Most longer-stay travellers use Urdaneta as a base and day-trip to Bolinao, Alaminos, or Lingayen.</p>',
                [
                    ['question' => 'Is Urdaneta worth staying in itself?', 'answer' => 'For a base, yes. For a destination, no. Pick Bolinao or Alaminos if you want a vacation rather than a stopover.'],
                    ['question' => 'How is the food in Urdaneta?', 'answer' => 'Strong local Filipino cuisine including pakam, longganisa, and bagnet. The public market has good cheap eats.'],
                    ['question' => 'Best time to visit?', 'answer' => 'Year-round. The city is less weather-dependent since it is inland.'],
                    ['question' => 'Are there pilgrimage sites nearby?', 'answer' => 'Yes, Manaoag Shrine is a 20-minute drive. Major pilgrimage events bring big crowds in October.'],
                ],
                'Find a resort in Urdaneta City Pangasinan. Business hotels, pool resorts, and a base for Bolinao or Hundred Islands trips.',
            ),
        ];
    }
}
