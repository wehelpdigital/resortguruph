<?php

namespace Database\Seeders;

class BulkContent06Visayas extends BulkContentBase
{
    protected function pages(): array
    {
        return [
            'resort-in-cebu-city' => $this->build(
                'Resort in Cebu City: City Hotels and Resort-Style Stays in the Queen City',
                '<p>Cebu City is the largest business and tourism hub in the Visayas and the most-served by domestic flights from Manila. A <strong>resort in Cebu City</strong> usually means a city-edge hotel resort, a property in the IT Park or Cebu Business Park, or a slightly out-of-city stay along the Mactan-Cebu causeway. True beach resorts in Cebu sit in Mactan rather than the city proper.</p><p>Flights from Manila to Cebu run hourly. From the airport, the city is 30 to 45 minutes via the CCLEX bridge or the older Mandaue route.</p>',
                '<h2>What "resort" means in Cebu City</h2><p>City hotels with full resort amenities. Examples: Radisson Blu, Marco Polo Plaza, Crown Regency, and Bayfront. Most have pools, gyms, restaurants, and spa services. The Marco Polo on Nivel Hills has the famous city-view pool.</p><h2>Where to base yourself</h2><p>IT Park for business stays and walkable food. Cebu Business Park for premium hotels. Lahug for boutique stays near Marco Polo. Downtown Colon for budget options near historic sites. Mactan if you want beach access.</p><h2>Pricing</h2><p>City business hotels: 3,000 to 7,500 PHP per night. Premium resort hotels: 6,500 to 14,000 PHP. Mactan beachfront resorts (Shangri-La, JPark): 9,000 to 25,000 PHP. Day-use pool access at some hotels: 1,200 to 2,800 PHP per head.</p>',
                [
                    ['question' => 'Should I stay in Cebu City or Mactan?', 'answer' => 'Cebu City for business, food, history. Mactan for the beach.'],
                    ['question' => 'Best Cebu City hotel for a couple?', 'answer' => 'Marco Polo Plaza for the view and pool. Radisson Blu for the central location.'],
                    ['question' => 'Is Cebu City safe at night?', 'answer' => 'IT Park and Business Park are well-patrolled. Downtown areas require standard travel awareness late at night.'],
                    ['question' => 'How is the Wi-Fi?', 'answer' => 'Generally fast and stable at business hotels. Mactan resorts are more variable.'],
                ],
                'Find a resort in Cebu City. Business hotels, Marco Polo views, IT Park bases, and Mactan-side beach picks. Compare here.',
            ),

            'resort-in-lapu-lapu' => $this->build(
                'Resort in Lapu Lapu: Mactan\'s Beachfront Capital and the Heart of Cebu Tourism',
                '<p>Lapu-Lapu City covers most of Mactan Island, including the airport area and the densest cluster of beach resorts in the Visayas. A <strong>resort in Lapu Lapu</strong> usually means a Mactan beachfront property along the eastern coast, where most of the famous five-star and family resorts operate. The airport is inside the city, which makes Lapu-Lapu the most convenient beach destination from Manila.</p><p>Domestic flights from Manila reach Mactan-Cebu Airport in 80 minutes. Most resorts arrange transfers, which take 15 to 30 minutes from the terminal.</p>',
                '<h2>The Lapu-Lapu resort tiers</h2><p>Five-star beachfront: Shangri-La Mactan, Crimson Resort, JPark Island Resort, Movenpick. Rooms run 12,000 to 30,000 PHP per night.</p><p>Mid-range beach hotels: Bluewater Maribago, Cebu White Sands, Costabella. Rooms run 6,000 to 12,000 PHP.</p><p>Boutique and budget: smaller properties near Maribago and Mactan Newtown. Rooms run 2,500 to 5,500 PHP.</p><h2>What to do beyond the beach</h2><p>The Magellan Shrine commemorates Lapu-Lapu\'s defeat of Magellan in 1521. Island-hopping tours leave daily from various wharfs. Olango Island bird sanctuary is reachable by short boat. Several diving operators run day-trip dive packages to Mactan Channel and surrounding islands.</p>',
                [
                    ['question' => 'How is the beach in Lapu-Lapu?', 'answer' => 'White-to-cream sand at the curated resort beaches. Many beaches are man-made or imported sand. Quality varies between resorts.'],
                    ['question' => 'Best Lapu-Lapu resort for families?', 'answer' => 'JPark Island Resort for the water park. Bluewater Maribago for the calmer family vibe.'],
                    ['question' => 'How long is the airport transfer?', 'answer' => 'Most Lapu-Lapu resorts are 15 to 30 minutes from Mactan-Cebu Airport. Some are within 10 minutes.'],
                    ['question' => 'Is day-use access available?', 'answer' => 'Yes at most resorts. Day-use rates run 1,500 to 3,500 PHP per head including lunch.'],
                ],
                'Find a resort in Lapu Lapu, Mactan\'s beachfront capital. Shangri-La, Crimson, JPark, Bluewater, and mid-range picks compared.',
            ),

            'resort-in-lapu-lapu-city' => $this->build(
                'Resort in Lapu Lapu City: A Deeper Guide to Mactan Beach Resorts',
                '<p>Lapu-Lapu City is the formal name for what most visitors think of as Mactan. A <strong>resort in Lapu Lapu City</strong> spans everything from boutique guesthouses near the airport to the five-star beachfront resorts that anchor Cebu\'s tourism economy. This page goes deeper than the broader Lapu-Lapu guide and compares specific neighborhoods within the city.</p><p>Most resorts sit along the eastern coast of Mactan, with the heaviest cluster between Maribago and Punta Engaño.</p>',
                '<h2>Neighborhoods worth knowing</h2><p>Punta Engaño: the high-end beach resort zone (Shangri-La, Crimson). Most luxurious.</p><p>Maribago: mid-range to upper-tier resorts (Bluewater, Cebu White Sands). Most popular family zone.</p><p>Mactan Newtown: newer condo-style resorts and serviced apartments. Modern feel.</p><p>Pusok and Pajo: budget guesthouses near the airport. Useful for short layovers.</p><h2>Pricing tier breakdown</h2><p>Luxury beachfront (Punta Engaño): 15,000 to 35,000 PHP per night. Premium family resorts (Maribago): 8,000 to 16,000 PHP. Mid-range hotels: 5,000 to 8,500 PHP. Budget airport guesthouses: 1,800 to 3,500 PHP. Day-use beach packages: 1,500 to 3,500 PHP per head.</p>',
                [
                    ['question' => 'Which area should I pick for a honeymoon?', 'answer' => 'Punta Engaño for the seclusion. Shangri-La Mactan and Crimson are the top picks.'],
                    ['question' => 'Are there overnight options at the airport?', 'answer' => 'Yes, several small hotels in Pusok within 5 to 10 minutes of the terminal. Useful for early flights.'],
                    ['question' => 'How is the sand quality?', 'answer' => 'White-to-cream at most curated resort beaches. Some sand is imported. Walking the beach outside the resort, quality varies.'],
                    ['question' => 'When is the best time to visit?', 'answer' => 'November to May for the dry season. June to October has rainier weather but lower rates.'],
                ],
                'Find a resort in Lapu Lapu City with Punta Engaño luxury, Maribago family beaches, and Mactan Newtown modern stays. Compare here.',
            ),

            'resort-in-bacolod' => $this->build(
                'Resort in Bacolod: City Hotels, Sugar Country Stays, and MassKara Country',
                '<p>Bacolod is the City of Smiles, capital of Negros Occidental, and famous for chicken inasal, the MassKara Festival, and the sugar-baron heritage. A <strong>resort in Bacolod</strong> usually means a city hotel rather than a beachfront resort, since the city itself sits inland from the coastline. For beaches, travellers head to nearby Sipalay, Punta Bulata, or further south.</p><p>Flights from Manila to Bacolod-Silay airport run 1 hour. Bacolod City is a 30-minute drive from the airport.</p>',
                '<h2>What Bacolod offers</h2><p>City hotels with resort amenities including L\'Fisher, Seda Capitol Central, and the boutique Nature\'s Village. Heritage stays in Silay City (a 20-minute drive). Sugar plantation hacienda stays in nearby municipalities. The Ruins of the Mariano Ledesma Lacson mansion is a 20-minute side trip.</p><h2>What to eat</h2><p>Chicken inasal at Manokan Country. Cansi (bone marrow soup) at Sharyn\'s Cansi. Piaya and napoleones for pasalubong. Bacolod is one of the strongest food destinations in the Philippines.</p><h2>Pricing</h2><p>Business hotel rooms: 3,000 to 7,000 PHP per night. Heritage stays in Silay: 3,500 to 8,000 PHP. Hacienda overnight stays: 4,000 to 10,000 PHP. Restaurant meals: 250 to 600 PHP per person at most local spots.</p>',
                [
                    ['question' => 'When is the MassKara Festival?', 'answer' => 'Every October. Bacolod hotels fill up six to eight weeks ahead during festival week.'],
                    ['question' => 'Are there beaches in Bacolod itself?', 'answer' => 'No swimming beaches in the city. For beaches, drive south to Sipalay (3 hours) or take a ferry to Guimaras.'],
                    ['question' => 'How is the chicken inasal compared to other regions?', 'answer' => 'Bacolod is the original home of inasal. Manokan Country is the standard stop.'],
                    ['question' => 'Worth visiting Silay City?', 'answer' => 'Yes, even for a half-day. Silay has more preserved ancestral houses than any other city in the country.'],
                ],
                'Find a resort in Bacolod. City hotels, heritage Silay stays, sugar hacienda overnights, and MassKara base. Compare picks here.',
            ),

            'resort-in-dauin' => $this->build(
                'Resort in Dauin: Diving Mecca on the Negros Oriental Coast',
                '<p>Dauin sits just south of Dumaguete on the eastern coast of Negros Oriental. A <strong>resort in Dauin</strong> is almost always a dive resort, since the area is famous for muck diving, macro photography, and the easy boat access to Apo Island\'s marine sanctuary. Non-divers can use Dauin as a quieter alternative to Dumaguete itself.</p><p>Travel: fly to Dumaguete-Sibulan Airport, then 30 minutes by car or van to Dauin.</p>',
                '<h2>What divers find in Dauin</h2><p>The shoreline is black volcanic sand, which makes the macro critters easier to spot. Frogfish, nudibranchs, seahorses, and rare cephalopods populate the dive sites. Apo Island is a 30-minute boat ride and consistently ranks among the top dive sites in the Philippines.</p><h2>Resorts to consider</h2><p>Atlantis Dumaguete is the most-established higher-tier dive resort. Bahura Resort and Spa, Atmosphere Resorts, and Pura Vida offer different tiers. Several budget dive lodges operate in the Maayong Tubig and Lipayo barangays.</p><h2>Pricing</h2><p>Premium dive resort packages: 7,500 to 15,000 PHP per person per night including two dives, meals, and room. Mid-range: 4,500 to 7,500 PHP per night. Budget lodges: 2,000 to 4,500 PHP per night plus 1,800 to 2,500 PHP per dive. Apo Island day trip: 2,500 to 4,000 PHP per person.</p>',
                [
                    ['question' => 'Is Dauin good for beginner divers?', 'answer' => 'Yes. Several schools run open-water certifications. Shore dives off the black sand are excellent for first dives.'],
                    ['question' => 'How is Apo Island?', 'answer' => 'Consistently ranked among the top three dive sites in the country. The marine sanctuary fee is part of the trip.'],
                    ['question' => 'Are there non-diving things to do?', 'answer' => 'Whale-shark watching at Oslob is a half-day side trip (controversial for ethical reasons). Casaroro Falls is a short drive away.'],
                    ['question' => 'When is the best season?', 'answer' => 'March to October for the calmest seas and best macro visibility. December to February has more wind but still divable.'],
                ],
                'Find a resort in Dauin. Macro diving black-sand beaches, Apo Island sanctuary access, and dive packages. Compare picks here.',
            ),

            'resort-in-dumaguete' => $this->build(
                'Resort in Dumaguete: Gentle City Vibe, Diving Base, and Boulevard Sunsets',
                '<p>Dumaguete is the capital of Negros Oriental and one of the gentlest cities in the Philippines. A <strong>resort in Dumaguete</strong> can mean a boutique city hotel on Rizal Boulevard, a dive resort just outside the city in Dauin, or a small inland property in Sibulan and Valencia. The city has become a retirement and digital-nomad destination thanks to its slow pace and walkable center.</p><p>Domestic flights from Manila reach Dumaguete-Sibulan Airport in 75 minutes.</p>',
                '<h2>What makes Dumaguete different</h2><p>The Boulevard, a seaside promenade where everyone walks at sunset. Silliman University with its century-old American influence. Casaroro Falls 30 minutes away. Apo Island and dive sites a short boat ride out. The vibe is unhurried in a way other Philippine cities are not.</p><h2>Resort options</h2><p>Boutique hotels along Rizal Boulevard. Dive resorts in Dauin (30 minutes south). Mountain retreats in Valencia (15 minutes inland). Beach inns along Sibulan north of the city. The city itself has limited beachfront because the volcanic coast is mostly seawall.</p><h2>Pricing</h2><p>Boutique city hotel rooms: 2,500 to 5,500 PHP per night. Higher-end boutique stays: 5,500 to 10,000 PHP. Dive resort packages in Dauin: 4,500 to 10,000 PHP per night. Mountain retreats in Valencia: 3,000 to 6,500 PHP.</p>',
                [
                    ['question' => 'Is Dumaguete better than Cebu for a quiet trip?', 'answer' => 'Yes for travellers who prefer small cities, walkable streets, and a slower pace.'],
                    ['question' => 'How is the food scene?', 'answer' => 'Strong on Visayan classics with influences from the university crowd. Try silvanas at Sans Rival, sigang at Lab-as, and tempura at Aldo\'s.'],
                    ['question' => 'Can I work remote from Dumaguete?', 'answer' => 'Yes. Several cafes and co-working spaces cater to digital nomads. Wi-Fi at boutique hotels is generally good.'],
                    ['question' => 'When is the best time to visit?', 'answer' => 'November to May for the dry weather. The city is pleasant year-round but typhoon season can disrupt boat trips to Apo Island.'],
                ],
                'Find a resort in Dumaguete. Boutique boulevard hotels, Dauin dive bases, Valencia mountain retreats. Compare picks here.',
            ),

            'resort-in-guimaras' => $this->build(
                'Resort in Guimaras: Mango Country and Quiet Beach Stays Across from Iloilo',
                '<p>Guimaras is a small island province across a 15-minute pump boat ride from Iloilo. A <strong>resort in Guimaras</strong> typically means a beach property along the western coast, a mango plantation stay, or a private island rental. The island is famous for its sweet mangoes, the lighthouse, and the under-the-radar beach scene.</p><p>Travel: fly to Iloilo, then take a 15-minute pump boat from Ortiz Wharf to Jordan, Guimaras. From Jordan you reach most resorts by tricycle or van in 30 to 60 minutes.</p>',
                '<h2>What to expect</h2><p>White-sand beaches at Alubihod, Tatlong Pulo, and several smaller coves. Costa Aguada and Raymen Beach Resort are the most-booked mid-range properties. The Trappist Monastery, Guisi Lighthouse, and the SEAFDEC marine research station are popular side trips.</p><h2>The mango angle</h2><p>Guimaras mangoes are considered some of the sweetest in the world. The annual Manggahan Festival in May features a mango-eating contest and dozens of mango-themed dishes. Several resorts offer mango farm tours as part of the stay.</p><h2>Pricing</h2><p>Beach resort rooms: 2,500 to 6,500 PHP per night. Premium beachfront: 6,500 to 12,000 PHP. Cottage rentals: 1,500 to 3,500 PHP. Day-use beach access: 200 to 500 PHP per head. Most properties accept walk-ins on weekdays.</p>',
                [
                    ['question' => 'Is the pump boat ride safe?', 'answer' => 'Yes, the Iloilo-to-Jordan run is short and on calm waters. Boats have lifevests and run every 15 to 20 minutes during daylight.'],
                    ['question' => 'How does Guimaras compare to Boracay?', 'answer' => 'Guimaras is quieter, cheaper, and less developed. Boracay has more amenities and nightlife.'],
                    ['question' => 'When are mangoes in season?', 'answer' => 'April to June for the peak harvest. The Manggahan Festival happens in May.'],
                    ['question' => 'Can I do Guimaras as a day trip from Iloilo?', 'answer' => 'Yes, but tight. Most travellers spend at least one night to enjoy the beach properly.'],
                ],
                'Find a resort in Guimaras with mango country, quiet beaches, lighthouse views, and Iloilo crossing. Compare picks here.',
            ),

            'resort-in-guimaras-island' => $this->build(
                'Resort in Guimaras Island: A Closer Look at the Beach Clusters',
                '<p>Guimaras Island is technically a province made up of one main island plus several smaller ones. A <strong>resort in Guimaras Island</strong> can sit on the main island\'s western coast or on smaller offshore islands like Inampulugan and Costa Aguada. This page covers the major beach clusters and which area suits which traveller.</p>',
                '<h2>The beach clusters</h2><p><strong>Alubihod (Nueva Valencia)</strong>: the most-developed white-sand beach with several mid-range resorts. Easy day trip from Iloilo.</p><p><strong>Tatlong Pulo (Jordan)</strong>: three small islands you can walk between at low tide. Quieter and less commercial.</p><p><strong>Inampulugan and Costa Aguada</strong>: small private-island resorts. Premium, exclusive bookings only.</p><p><strong>Buenavista coast</strong>: northern part of the island, less visited, has a few homestays.</p><h2>Picking a resort</h2><p>For first-time visitors: Alubihod, particularly Raymen Beach Resort or Costa Aguada. For couples wanting quiet: Tatlong Pulo or Buenavista. For a private island experience: Inampulugan or Costa Aguada Island Resort.</p><h2>Pricing</h2><p>Mid-range beachfront: 2,500 to 6,500 PHP per night. Premium private island: 8,000 to 18,000 PHP per night. Cottage rentals: 1,500 to 3,500 PHP. Boat transfers between islands: 500 to 1,500 PHP.</p>',
                [
                    ['question' => 'Which Guimaras Island beach is best for families?', 'answer' => 'Alubihod for the accessible amenities. Tatlong Pulo for the quirky walking-between-islands experience kids love.'],
                    ['question' => 'Are there public beaches?', 'answer' => 'Yes, particularly Alubihod public beach side. Most travellers book a resort and use its beachfront.'],
                    ['question' => 'How long is the boat ride to the smaller islands?', 'answer' => 'Inampulugan: 30 to 45 minutes. Costa Aguada: 20 to 30 minutes. Tatlong Pulo: 15 to 20 minutes.'],
                    ['question' => 'When is the best time to visit?', 'answer' => 'November to May for the dry season. April to June for the mango harvest peak.'],
                ],
                'Find a resort in Guimaras Island. Alubihod, Tatlong Pulo, Inampulugan private islands, and Buenavista. Compare picks here.',
            ),

            'resort-in-iloilo' => $this->build(
                'Resort in Iloilo: Heritage Stays, Modern Hotels, and the Gateway to Guimaras',
                '<p>Iloilo City and the surrounding province blend Spanish-era heritage with modern business hotels. A <strong>resort in Iloilo</strong> usually means a city hotel rather than a beachfront property, since most of the famous beaches require a short pump boat to Guimaras. The city itself has built a strong food and heritage tourism scene around Calle Real and Jaro.</p><p>Domestic flights from Manila reach Iloilo Airport in 80 minutes.</p>',
                '<h2>What Iloilo offers</h2><p>Heritage stays in Jaro and the Molo district. Business hotels along Diversion Road and SM City Iloilo. The Calle Real walking tour and Jaro Cathedral. Casa Mariquit and Camina Balay nga Bato heritage homes. Strong food scene anchored by batchoy, pancit molo, and the famous La Paz batchoy.</p><h2>Where to base</h2><p>Diversion Road for business and modern dining. Jaro for heritage and old churches. Molo for boutique stays near Plaza Molo. Smallville Iloilo for nightlife. Oton and Tigbauan for inland resorts further out.</p><h2>Pricing</h2><p>Business hotel rooms: 2,500 to 6,500 PHP per night. Heritage boutique stays: 3,500 to 8,000 PHP. Premium hotels (Richmonde, Courtyard): 5,500 to 12,000 PHP. Restaurant meals: 250 to 500 PHP per person.</p>',
                [
                    ['question' => 'Is there a beach in Iloilo?', 'answer' => 'A few smaller beaches in Oton and Tigbauan. For real beach trips, take the pump boat to Guimaras.'],
                    ['question' => 'How is the food scene?', 'answer' => 'One of the strongest in the Philippines. Batchoy, pancit molo, biscocho, and the famous La Paz batchoy are must-tries.'],
                    ['question' => 'When is the Dinagyang Festival?', 'answer' => 'Fourth weekend of January. Hotels fill up six to eight weeks ahead.'],
                    ['question' => 'Worth visiting Jaro Cathedral?', 'answer' => 'Yes for a quick visit. The cathedral and the surrounding plaza make a good morning walk.'],
                ],
                'Find a resort in Iloilo. Heritage stays, business hotels, Guimaras gateway, and Calle Real base. Compare picks here.',
            ),

            'resort-in-iloilo-city' => $this->build(
                'Resort in Iloilo City: City Hotels and Resort-Style Picks Inside the Capital',
                '<p>Iloilo City is the capital of Iloilo province and the largest urban area in Western Visayas. A <strong>resort in Iloilo City</strong> typically means a hotel with full resort amenities like a pool and restaurant rather than a beach property. The city has invested heavily in walkable public spaces and has one of the cleanest urban centers outside Metro Manila.</p><p>Iloilo Airport is in Cabatuan, about 20 minutes from the city center.</p>',
                '<h2>City versus province</h2><p>Iloilo City is urban and walkable. Iloilo province includes inland mountain stays, beaches at Tigbauan, and the famous Garin Farm in San Joaquin. The city works well as a base; the province is for day trips.</p><h2>Hotels with resort amenities</h2><p>Iloilo Richmonde, Courtyard by Marriott, Park Inn by Radisson, Hotel del Rio. Most have full pools, gyms, and restaurants. Boutique heritage stays in Jaro and Molo offer character but smaller scale.</p><h2>Pricing</h2><p>Premium city hotels with pools: 5,500 to 12,000 PHP per night. Mid-range hotels: 3,000 to 6,500 PHP. Heritage boutique inns: 3,500 to 7,500 PHP. Day-use pool access at some hotels: 800 to 2,000 PHP per head.</p>',
                [
                    ['question' => 'Is Iloilo City safe at night?', 'answer' => 'Generally yes, particularly the Diversion Road and Smallville zones. Standard travel awareness applies in older parts of the city late at night.'],
                    ['question' => 'How long does it take to walk Calle Real?', 'answer' => 'The heritage walking tour takes about 90 minutes if you stop for photos and a short museum visit.'],
                    ['question' => 'Should I rent a car or use Grab?', 'answer' => 'Grab works well in the city. For day trips to Garin Farm or to take the pump boat to Guimaras, renting a car or hiring a driver makes sense.'],
                    ['question' => 'When is the best time to visit?', 'answer' => 'November to May for dry weather. January for the Dinagyang Festival if you can handle the crowds.'],
                ],
                'Find a resort in Iloilo City. Premium hotels, heritage boutique stays, and pool-equipped business hotels. Compare picks here.',
            ),

            'resort-in-don-salvador-benedicto' => $this->build(
                'Resort in Don Salvador Benedicto: Cool Mountains of Negros Occidental',
                '<p>Don Salvador Benedicto, often shortened to DSB, is the cool-weather highland town of Negros Occidental, often called the "Little Baguio" of the south. A <strong>resort in Don Salvador Benedicto</strong> means a mountain retreat with pine trees, cool weather, and views of Mount Mandalagan. The town is reachable in about 90 minutes from Bacolod.</p><p>Travel: fly to Bacolod-Silay Airport, then 1.5 hours by car via the Negros Highway.</p>',
                '<h2>What to expect</h2><p>Cool mountain air, pine trees imported in the 1950s, and small boutique inns. The Northern Negros Forest Reserve protects the surrounding forest. Mansa Falls is the main natural attraction. The Marian Garden Resort and several small mountain inns operate in the town center.</p><h2>What to do</h2><p>Visit Mansa Falls, hike the Sierra Madre trails (regional version), enjoy the pine views, and stop at the famous Padre Coffee shop. The town is a quiet weekend escape rather than an action-packed destination.</p><h2>Pricing</h2><p>Mountain inn rooms: 2,500 to 5,500 PHP per night. Private cottages: 3,500 to 7,000 PHP. Day-use entry to certain attractions: 100 to 250 PHP per head. Food at most inns is included in higher-tier room rates.</p>',
                [
                    ['question' => 'How cold does it get in DSB?', 'answer' => 'Mornings can drop to 17 to 19 degrees in December and January. Light jacket weather.'],
                    ['question' => 'Is it really like Baguio?', 'answer' => 'In feel, yes. In altitude and food scene, not quite. The pine trees and cool weather are the main resemblance.'],
                    ['question' => 'When is the best time to visit?', 'answer' => 'November to February for the coolest weather. June to September has higher rainfall.'],
                    ['question' => 'Can I do a day trip from Bacolod?', 'answer' => 'Possible but tight. Most travellers spend at least one night to enjoy the cool air properly.'],
                ],
                'Discover a resort in Don Salvador Benedicto. Cool mountain stays, pine trees, Mansa Falls, and Negros highlands. Compare picks here.',
            ),

            'resort-in-panglao-bohol' => $this->build(
                'Resort in Panglao Bohol: Alona Beach, Boutique Stays, and Diving Bases',
                '<p>Panglao Island is connected to Bohol by two bridges and has become one of the most-booked beach destinations in the Visayas. A <strong>resort in Panglao Bohol</strong> usually means an Alona Beach property, a boutique beachfront inn in the quieter parts of the island, or a higher-tier resort like Henann or South Palms. The new Panglao International Airport made the destination significantly more accessible.</p><p>Direct flights from Manila reach Panglao-Bohol Airport in 90 minutes.</p>',
                '<h2>Where to stay in Panglao</h2><p>Alona Beach: the busiest stretch with the most restaurants and nightlife. Higher-tier resorts like Henann Alona Beach anchor it.</p><p>Doljo Beach: quieter alternative on the western side, with fewer crowds and white sand.</p><p>Bingag and Dauis: inland boutique stays with pools but no direct beachfront.</p><p>Momo Beach: smaller cove with boutique resort and quiet vibe.</p><h2>Things to do</h2><p>Snorkeling at Balicasag Island. Diving (Panglao has world-class sites). Hinagdanan Cave. Chocolate Hills day trip via Bohol mainland. Loboc River cruise. Tarsier sanctuary.</p><h2>Pricing</h2><p>Boutique beach hotels: 3,500 to 7,500 PHP per night. Mid-range resorts: 6,000 to 12,000 PHP. Premium beachfront: 12,000 to 25,000 PHP. Day-use beach: free at public Alona, pay at some private resort beaches.</p>',
                [
                    ['question' => 'Is Alona Beach overcrowded?', 'answer' => 'Can be on weekends and high season. For quiet, pick Doljo or Momo Beach instead.'],
                    ['question' => 'Worth visiting Balicasag for snorkeling?', 'answer' => 'Yes, considered one of the best snorkeling spots in the country. Marine sanctuary fee applies.'],
                    ['question' => 'When is the best time?', 'answer' => 'March to May for the calmest seas and warmest weather. June to October has typhoons that affect boat trips.'],
                    ['question' => 'How is the diving?', 'answer' => 'Excellent. Several world-class wall dives and macro sites within 30 minutes by boat.'],
                ],
                'Find a resort in Panglao Bohol. Alona Beach, Doljo quiet stays, Henann, South Palms, and dive bases. Compare picks here.',
            ),

            'resort-in-siquijor' => $this->build(
                'Resort in Siquijor: Mystic Island Stays, Empty Beaches, and Tagalog-Cebuano Mix',
                '<p>Siquijor is the small island province between Negros and Bohol, famous for its folklore around faith healers and its remarkably empty white-sand beaches. A <strong>resort in Siquijor</strong> usually means a boutique beachfront property along the western coast, a budget inn in San Juan, or a quiet retreat in the inland barangays. The island has fewer than 100,000 residents and a slow pace that surprises first-time visitors.</p><p>Travel: fly to Dumaguete, then 45 minutes by fast craft to Siquijor port. Direct flights from Cebu also reach Siquijor airport.</p>',
                '<h2>The Siquijor circuit</h2><p>Salagdoong Beach for the cliff-jumping platform. Cambugahay Falls for the rope-swing into emerald pools. The Old Balete Tree for the fish-foot-spa. San Juan beachfront for sunset. The Mystic Hills view deck for the panoramic island view. Most travellers rent a motorbike or hire a tricycle for a full-day loop.</p><h2>Where to stay</h2><p>San Juan and Solangon barangays have the densest cluster of beachfront resorts. Maria barangay has quieter family-friendly inns. Lazi has some of the cheapest accommodations. Larena is the main port town with more business-style hotels.</p><h2>Pricing</h2><p>Beachfront boutique resort rooms: 2,500 to 6,500 PHP per night. Budget inns: 800 to 2,000 PHP. Premium small resorts: 6,500 to 14,000 PHP. Motorbike rental: 400 to 600 PHP per day. Tricycle full-island tour: 1,500 to 2,500 PHP.</p>',
                [
                    ['question' => 'Is Siquijor really mystical?', 'answer' => 'Folklore around faith healers and rituals persists, though the island has modernized significantly. Tourists experience the regular Filipino island feel with a slight folk-tradition flavor.'],
                    ['question' => 'How do I get there from Manila?', 'answer' => 'Fly to Dumaguete (most common), then 45-minute fast craft to Siquijor port. Or fly direct to Siquijor airport from Cebu.'],
                    ['question' => 'Best time to visit?', 'answer' => 'February to May for the calmest seas. April and May have warmest water and best beach conditions.'],
                    ['question' => 'How crowded does it get?', 'answer' => 'Less than Boracay or Panglao but growing. Holy Week is the busiest. Off-season weekdays can feel almost empty.'],
                ],
                'Find a resort in Siquijor. San Juan beachfront, Maria quiet stays, Cambugahay Falls base, and mystic island circuit. Compare here.',
            ),
        ];
    }
}
