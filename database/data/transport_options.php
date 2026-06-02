<?php

/**
 * Per-destination transport recommendations rendered on each keyword page just
 * below the listings section. Each entry is a real airline / bus / ferry /
 * ride-hail option with a booking URL the reader can click straight through to.
 *
 * Type icons (used by the seeder card renderer):
 *   airline → ✈️    bus → 🚌    ferry → ⛴️    rail → 🚆    ride → 🚗    car → 🚙
 *
 * Destinations without an explicit entry fall back to '_default' (Cebu Pacific
 * + Philippine Airlines, which cover most domestic routes).
 */

return [
    '_default' => [
        ['name' => 'Cebu Pacific', 'type' => 'airline', 'url' => 'https://www.cebupacificair.com/', 'note' => 'Domestic flights to most major Philippine cities'],
        ['name' => 'Philippine Airlines', 'type' => 'airline', 'url' => 'https://www.philippineairlines.com/', 'note' => 'Full-service flights with bigger luggage allowance'],
    ],

    // === METRO MANILA + nearby cities (Grab + LRT/MRT) ===
    'antipolo' => [
        ['name' => 'LRT-2 Antipolo Line', 'type' => 'rail', 'url' => 'https://www.lrta.gov.ph/', 'note' => 'Recto to Antipolo Station, then jeepney up Sumulong Highway'],
        ['name' => 'Grab', 'type' => 'ride', 'url' => 'https://www.grab.com/ph/', 'note' => 'Direct from BGC or Makati, 45-75 minutes outside peak hours'],
    ],
    'marikina' => [
        ['name' => 'LRT-2 to Santolan', 'type' => 'rail', 'url' => 'https://www.lrta.gov.ph/', 'note' => 'Santolan station is the closest LRT stop to Marikina city'],
        ['name' => 'Grab', 'type' => 'ride', 'url' => 'https://www.grab.com/ph/', 'note' => 'Surge-free outside Manila rush hour, fastest door-to-door'],
    ],
    'manila' => [
        ['name' => 'Grab', 'type' => 'ride', 'url' => 'https://www.grab.com/ph/', 'note' => 'The default airport-to-hotel option from NAIA'],
        ['name' => 'LRT-1 and LRT-2', 'type' => 'rail', 'url' => 'https://www.lrta.gov.ph/', 'note' => 'Cheapest way to move between Intramuros, Binondo, Quiapo'],
    ],
    'taguig' => [
        ['name' => 'Grab', 'type' => 'ride', 'url' => 'https://www.grab.com/ph/', 'note' => 'Within BGC, fastest at any hour. From NAIA, 20-40 minutes'],
        ['name' => 'BGC Bus', 'type' => 'bus', 'url' => 'https://bgc.com.ph/transport-and-traffic/', 'note' => 'Free shuttle loops around BGC, runs every 10-15 minutes'],
    ],
    'quezon-city' => [
        ['name' => 'MRT-3 and LRT-2', 'type' => 'rail', 'url' => 'https://dotrmrt3.gov.ph/', 'note' => 'MRT-3 along EDSA, LRT-2 along Aurora, both cut QC commute time'],
        ['name' => 'Grab', 'type' => 'ride', 'url' => 'https://www.grab.com/ph/', 'note' => 'From NAIA to QC takes 45-90 minutes depending on Skyway use'],
    ],

    // === CAVITE (south of Manila, mostly bus + jeepney) ===
    'tagaytay' => [
        ['name' => 'HM Transport', 'type' => 'bus', 'url' => 'https://www.facebook.com/HMTransportInc', 'note' => 'Buendia to Olivarez Tagaytay, hourly departures'],
        ['name' => 'DLTBCo', 'type' => 'bus', 'url' => 'https://dltbco.com/', 'note' => 'Pasay or PITX to Tagaytay-Nasugbu route'],
        ['name' => 'Saulog Transit', 'type' => 'bus', 'url' => 'https://www.facebook.com/saulogtransitinc', 'note' => 'Cubao to Tagaytay direct'],
    ],
    'alfonso' => [
        ['name' => 'Erjohn & Almark', 'type' => 'bus', 'url' => 'https://www.facebook.com/EjapTransport', 'note' => 'Manila to Mendez/Alfonso via Aguinaldo Highway'],
        ['name' => 'Grab', 'type' => 'ride', 'url' => 'https://www.grab.com/ph/', 'note' => 'Limited inside Alfonso; better to drive or arrange a van'],
    ],
    'amadeo' => [
        ['name' => 'Erjohn & Almark', 'type' => 'bus', 'url' => 'https://www.facebook.com/EjapTransport', 'note' => 'Manila to Mendez transfer to Amadeo jeepney'],
        ['name' => 'Private car', 'type' => 'car', 'url' => '#', 'note' => 'CALAX makes the drive from BGC about 90 minutes'],
    ],
    'bacoor' => [
        ['name' => 'San Agustin Lines', 'type' => 'bus', 'url' => '#', 'note' => 'PITX to Bacoor public market and Imus via Cavitex'],
        ['name' => 'Grab', 'type' => 'ride', 'url' => 'https://www.grab.com/ph/', 'note' => 'Works in Bacoor proper; expect 30-60 min from BGC via Cavitex'],
    ],
    'dasmarinas' => [
        ['name' => 'Sapphire Express', 'type' => 'bus', 'url' => '#', 'note' => 'Pasay or PITX to Dasma crossing'],
        ['name' => 'Grab', 'type' => 'ride', 'url' => 'https://www.grab.com/ph/', 'note' => 'Reliable within Dasma; 60-90 minutes from Alabang'],
    ],
    'imus' => [
        ['name' => 'San Agustin Lines', 'type' => 'bus', 'url' => '#', 'note' => 'PITX to Imus, every 15-20 minutes via Cavitex'],
        ['name' => 'Grab', 'type' => 'ride', 'url' => 'https://www.grab.com/ph/', 'note' => 'Works in Imus proper, 40-60 minutes from BGC'],
    ],
    'indang' => [
        ['name' => 'Saulog Transit', 'type' => 'bus', 'url' => 'https://www.facebook.com/saulogtransitinc', 'note' => 'Manila to Indang via Tagaytay corridor'],
        ['name' => 'Private car', 'type' => 'car', 'url' => '#', 'note' => 'Best option, CALAX exit at Silang then Tagaytay-Indang road'],
    ],
    'naic' => [
        ['name' => 'San Agustin Lines', 'type' => 'bus', 'url' => '#', 'note' => 'PITX/Coastal Mall to Naic via Cavitex and Antero Soriano Highway'],
        ['name' => 'Private car', 'type' => 'car', 'url' => '#', 'note' => 'Easiest for the Naic-Ternate coastal route'],
    ],
    'silang' => [
        ['name' => 'HM Transport', 'type' => 'bus', 'url' => 'https://www.facebook.com/HMTransportInc', 'note' => 'Buendia to Tagaytay route stops at Silang junction'],
        ['name' => 'Saulog Transit', 'type' => 'bus', 'url' => 'https://www.facebook.com/saulogtransitinc', 'note' => 'Cubao to Tagaytay stops at Silang'],
    ],

    // === BULACAN (NLEX north) ===
    'bulacan-province' => [
        ['name' => 'Baliwag Transit', 'type' => 'bus', 'url' => 'https://www.facebook.com/baliwagtransitinc', 'note' => 'Cubao to Malolos, Pulilan, Baliuag via NLEX'],
        ['name' => 'Five Star', 'type' => 'bus', 'url' => 'https://www.facebook.com/fivestar.busline', 'note' => 'Pasay to Cabanatuan stops in Bulacan towns'],
    ],
    'pandi' => [
        ['name' => 'Baliwag Transit', 'type' => 'bus', 'url' => 'https://www.facebook.com/baliwagtransitinc', 'note' => 'Cubao to Bocaue or Marilao, then tricycle to Pandi'],
        ['name' => 'Private car', 'type' => 'car', 'url' => '#', 'note' => 'NLEX Marilao exit, 60-75 minutes from QC'],
    ],

    // === PAMPANGA (NLEX-SCTEX north) ===
    'pampanga-province' => [
        ['name' => 'Five Star', 'type' => 'bus', 'url' => 'https://www.facebook.com/fivestar.busline', 'note' => 'Pasay/Cubao to San Fernando Pampanga, every 30 min'],
        ['name' => 'Genesis Transport', 'type' => 'bus', 'url' => 'https://www.genesistransport.com.ph/', 'note' => 'Pasay to Clark/Angeles via NLEX-SCTEX'],
        ['name' => 'Philippines AirAsia (Clark)', 'type' => 'airline', 'url' => 'https://www.airasia.com/', 'note' => 'Flights into Clark International Airport (CRK)'],
    ],
    'angeles' => [
        ['name' => 'Genesis Transport', 'type' => 'bus', 'url' => 'https://www.genesistransport.com.ph/', 'note' => 'Cubao or Pasay to Angeles/Dau, hourly'],
        ['name' => 'Philippines AirAsia (Clark)', 'type' => 'airline', 'url' => 'https://www.airasia.com/', 'note' => 'Direct flights into Clark Airport, beside Angeles'],
        ['name' => 'Cebu Pacific (Clark)', 'type' => 'airline', 'url' => 'https://www.cebupacificair.com/', 'note' => 'Domestic and Asia routes from Clark'],
    ],
    'arayat' => [
        ['name' => 'Five Star', 'type' => 'bus', 'url' => 'https://www.facebook.com/fivestar.busline', 'note' => 'Cubao to Cabanatuan route stops at Arayat junction'],
        ['name' => 'Private car', 'type' => 'car', 'url' => '#', 'note' => 'NLEX-SCTEX San Simon or Apalit exit'],
    ],

    // === BATANGAS ===
    'batangas-city' => [
        ['name' => 'JAM Liner', 'type' => 'bus', 'url' => 'https://jam.com.ph/', 'note' => 'Cubao or PITX to Batangas Pier, 2-2.5 hours via STAR Tollway'],
        ['name' => 'DLTBCo', 'type' => 'bus', 'url' => 'https://dltbco.com/', 'note' => 'Pasay to Batangas City'],
    ],
    'calatagan' => [
        ['name' => 'DLTBCo', 'type' => 'bus', 'url' => 'https://dltbco.com/', 'note' => 'Pasay or Cubao to Lemery/Calatagan'],
        ['name' => 'Private car', 'type' => 'car', 'url' => '#', 'note' => '2.5-3 hours via SLEX, STAR Tollway and Calatagan road'],
    ],
    'laiya' => [
        ['name' => 'DLTBCo', 'type' => 'bus', 'url' => 'https://dltbco.com/', 'note' => 'Cubao to San Juan, transfer to Laiya jeep'],
        ['name' => 'ALPS Transit', 'type' => 'bus', 'url' => 'https://www.facebook.com/alpstransit', 'note' => 'Buendia to Lipa, transfer to San Juan and Laiya'],
    ],
    'lipa' => [
        ['name' => 'JAM Liner', 'type' => 'bus', 'url' => 'https://jam.com.ph/', 'note' => 'Cubao/Buendia to Lipa, every 15-30 minutes'],
        ['name' => 'ALPS Transit', 'type' => 'bus', 'url' => 'https://www.facebook.com/alpstransit', 'note' => 'Direct Lipa coaches from PITX'],
    ],
    'lobo' => [
        ['name' => 'JAM Liner', 'type' => 'bus', 'url' => 'https://jam.com.ph/', 'note' => 'Manila to Batangas City, transfer to Lobo jeepney'],
        ['name' => 'Private car', 'type' => 'car', 'url' => '#', 'note' => 'The mountain road into Lobo is best done in your own vehicle'],
    ],
    'anilao-mabini' => [
        ['name' => 'JAM Liner', 'type' => 'bus', 'url' => 'https://jam.com.ph/', 'note' => 'Cubao to Batangas Grand Terminal, then jeep to Anilao'],
        ['name' => 'Private car', 'type' => 'car', 'url' => '#', 'note' => 'Easier for dive gear, 2.5 hours from BGC via STAR Tollway'],
    ],
    'nasugbu' => [
        ['name' => 'DLTBCo', 'type' => 'bus', 'url' => 'https://dltbco.com/', 'note' => 'Pasay to Nasugbu via Tagaytay-Nasugbu Road'],
        ['name' => 'Saulog Transit', 'type' => 'bus', 'url' => 'https://www.facebook.com/saulogtransitinc', 'note' => 'Cubao to Nasugbu route, multiple daily trips'],
    ],

    // === LAGUNA ===
    'pansol' => [
        ['name' => 'JAM Liner', 'type' => 'bus', 'url' => 'https://jam.com.ph/', 'note' => 'Buendia to Sta. Cruz route stops at Pansol Calamba'],
        ['name' => 'HM Transport', 'type' => 'bus', 'url' => 'https://www.facebook.com/HMTransportInc', 'note' => 'Cubao to Sta. Cruz Laguna via SLEX'],
    ],
    'calamba' => [
        ['name' => 'JAM Liner', 'type' => 'bus', 'url' => 'https://jam.com.ph/', 'note' => 'Buendia or Cubao to Calamba, every 15-30 min'],
        ['name' => 'PNR Calamba commuter', 'type' => 'rail', 'url' => 'https://pnr.gov.ph/', 'note' => 'Tutuban to Calamba via the long-distance line'],
    ],
    'san-pablo' => [
        ['name' => 'DLTBCo', 'type' => 'bus', 'url' => 'https://dltbco.com/', 'note' => 'Cubao/Pasay to San Pablo, 2.5-3 hours via SLEX'],
        ['name' => 'HM Transport', 'type' => 'bus', 'url' => 'https://www.facebook.com/HMTransportInc', 'note' => 'Buendia to San Pablo, regular departures'],
    ],
    'nagcarlan' => [
        ['name' => 'HM Transport', 'type' => 'bus', 'url' => 'https://www.facebook.com/HMTransportInc', 'note' => 'Buendia to Sta. Cruz, transfer to Nagcarlan jeepney'],
        ['name' => 'Private car', 'type' => 'car', 'url' => '#', 'note' => '3-3.5 hours via SLEX and Pagsanjan road'],
    ],

    // === RIZAL ===
    'tanay' => [
        ['name' => 'EMBC Bus', 'type' => 'bus', 'url' => '#', 'note' => 'Starmall EDSA or Crossing Shaw to Tanay direct'],
        ['name' => 'Grab', 'type' => 'ride', 'url' => 'https://www.grab.com/ph/', 'note' => 'Works from QC, 90-120 minutes to Tanay proper'],
    ],
    'rodriguez-montalban' => [
        ['name' => 'RRCG / Montalban UV vans', 'type' => 'bus', 'url' => '#', 'note' => 'Cubao Farmers (in front of Jollibee) to Montalban terminal, under 1 hour'],
        ['name' => 'Grab', 'type' => 'ride', 'url' => 'https://www.grab.com/ph/', 'note' => 'Direct from QC; expect 45-75 minutes outside rush hour'],
    ],
    'binangonan' => [
        ['name' => 'EMBC Bus', 'type' => 'bus', 'url' => '#', 'note' => 'Starmall EDSA to Binangonan via Manila East Road'],
        ['name' => 'Grab', 'type' => 'ride', 'url' => 'https://www.grab.com/ph/', 'note' => '60-90 minutes from QC, longer on Sundays'],
    ],
    'san-mateo-rizal' => [
        ['name' => 'San Mateo UV Express', 'type' => 'bus', 'url' => '#', 'note' => 'Cubao Farmers terminal to San Mateo, every 15 min'],
        ['name' => 'Grab', 'type' => 'ride', 'url' => 'https://www.grab.com/ph/', 'note' => 'Reliable from QC, 30-50 minutes outside peak'],
    ],
    'taytay-rizal' => [
        ['name' => 'Taytay jeepneys', 'type' => 'bus', 'url' => '#', 'note' => 'From Pasig, Ortigas, or Cainta — multiple jeep routes'],
        ['name' => 'Grab', 'type' => 'ride', 'url' => 'https://www.grab.com/ph/', 'note' => 'Direct from BGC or Ortigas, 30-50 minutes'],
    ],

    // === QUEZON ===
    'lucena' => [
        ['name' => 'DLTBCo', 'type' => 'bus', 'url' => 'https://dltbco.com/', 'note' => 'Cubao or Pasay to Lucena Grand Terminal, every 30 min'],
        ['name' => 'JAC Liner', 'type' => 'bus', 'url' => 'https://www.facebook.com/JacLinerInc', 'note' => 'Cubao to Lucena route, premium coaches'],
    ],
    'sariaya' => [
        ['name' => 'DLTBCo', 'type' => 'bus', 'url' => 'https://dltbco.com/', 'note' => 'Cubao to Lucena, then jeepney to Sariaya'],
        ['name' => 'JAC Liner', 'type' => 'bus', 'url' => 'https://www.facebook.com/JacLinerInc', 'note' => 'Lucena-bound bus stops at Sariaya'],
    ],
    'quezon-province' => [
        ['name' => 'DLTBCo', 'type' => 'bus', 'url' => 'https://dltbco.com/', 'note' => 'Cubao or Pasay to Lucena, hub for the rest of Quezon'],
        ['name' => 'JAC Liner', 'type' => 'bus', 'url' => 'https://www.facebook.com/JacLinerInc', 'note' => 'Premium Manila to Lucena and Bicol routes'],
    ],

    // === BICOL (long-haul, fly recommended) ===
    'albay-legazpi' => [
        ['name' => 'Cebu Pacific', 'type' => 'airline', 'url' => 'https://www.cebupacificair.com/', 'note' => 'Manila to Legazpi, 55 minutes, daily flights'],
        ['name' => 'Philippine Airlines', 'type' => 'airline', 'url' => 'https://www.philippineairlines.com/', 'note' => 'Direct Manila-Legazpi'],
        ['name' => 'DLTBCo', 'type' => 'bus', 'url' => 'https://dltbco.com/', 'note' => 'Cubao to Legazpi, 10-12 hours overnight'],
    ],
    'naga-camarines-sur' => [
        ['name' => 'Cebu Pacific', 'type' => 'airline', 'url' => 'https://www.cebupacificair.com/', 'note' => 'Manila to Naga, multiple daily flights'],
        ['name' => 'Philippine Airlines', 'type' => 'airline', 'url' => 'https://www.philippineairlines.com/', 'note' => 'Direct Manila-Naga'],
        ['name' => 'DLTBCo', 'type' => 'bus', 'url' => 'https://dltbco.com/', 'note' => 'Cubao to Naga, 8-10 hours overnight'],
    ],
    'sorsogon' => [
        ['name' => 'Cebu Pacific (to Legazpi)', 'type' => 'airline', 'url' => 'https://www.cebupacificair.com/', 'note' => 'Fly to Legazpi, then 2-hour van to Sorsogon'],
        ['name' => 'Philtranco', 'type' => 'bus', 'url' => 'https://philtranco.com.ph/', 'note' => 'Cubao or Pasay to Sorsogon, 12-14 hours direct'],
    ],

    // === NORTH LUZON ===
    'subic' => [
        ['name' => 'Victory Liner', 'type' => 'bus', 'url' => 'https://www.victoryliner.com/', 'note' => 'Cubao or Pasay to Olongapo terminal, every 30 minutes'],
        ['name' => 'Genesis Transport', 'type' => 'bus', 'url' => 'https://www.genesistransport.com.ph/', 'note' => 'Cubao to Olongapo via NLEX-SCTEX'],
    ],
    'morong-bataan' => [
        ['name' => 'Genesis Transport', 'type' => 'bus', 'url' => 'https://www.genesistransport.com.ph/', 'note' => 'Cubao to Balanga, transfer to Morong jeepney'],
        ['name' => 'Bataan Transit', 'type' => 'bus', 'url' => 'https://www.facebook.com/bataantransitcoinc', 'note' => 'Manila to Bataan, multiple routes'],
    ],
    'bataan-province' => [
        ['name' => 'Genesis Transport', 'type' => 'bus', 'url' => 'https://www.genesistransport.com.ph/', 'note' => 'Cubao to Balanga and Mariveles, hourly via NLEX-SCTEX'],
        ['name' => 'Bataan Transit', 'type' => 'bus', 'url' => 'https://www.facebook.com/bataantransitcoinc', 'note' => 'Manila to Balanga, more frequent on weekends'],
    ],
    'pangasinan-general' => [
        ['name' => 'Victory Liner', 'type' => 'bus', 'url' => 'https://www.victoryliner.com/', 'note' => 'Cubao or Pasay to Dagupan and Lingayen, every hour'],
        ['name' => 'Five Star', 'type' => 'bus', 'url' => 'https://www.facebook.com/fivestar.busline', 'note' => 'Cubao to Pangasinan towns, 5-6 hours'],
    ],
    'bolinao' => [
        ['name' => 'Five Star', 'type' => 'bus', 'url' => 'https://www.facebook.com/fivestar.busline', 'note' => 'Cubao or Pasay to Alaminos and Bolinao, 6-7 hours'],
        ['name' => 'Victory Liner (Dagupan)', 'type' => 'bus', 'url' => 'https://www.victoryliner.com/', 'note' => 'Manila to Dagupan, transfer to Bolinao via mini-bus'],
    ],
    'la-union' => [
        ['name' => 'Victory Liner', 'type' => 'bus', 'url' => 'https://www.victoryliner.com/', 'note' => 'Cubao or Pasay to San Fernando La Union, 5-6 hours'],
        ['name' => 'Partas', 'type' => 'bus', 'url' => 'https://www.facebook.com/PartasTransPH', 'note' => 'Manila to San Fernando, deluxe coaches'],
    ],
    'alaminos-hundred-islands' => [
        ['name' => 'Victory Liner', 'type' => 'bus', 'url' => 'https://www.victoryliner.com/', 'note' => 'Cubao to Alaminos Pangasinan, direct route, 6 hours'],
        ['name' => 'Five Star', 'type' => 'bus', 'url' => 'https://www.facebook.com/fivestar.busline', 'note' => 'Pasay to Alaminos, with Bolinao continuation'],
    ],
    'nueva-ecija' => [
        ['name' => 'Five Star', 'type' => 'bus', 'url' => 'https://www.facebook.com/fivestar.busline', 'note' => 'Cubao to Cabanatuan, every 30 min via NLEX'],
        ['name' => 'Baliwag Transit', 'type' => 'bus', 'url' => 'https://www.facebook.com/baliwagtransitinc', 'note' => 'Cubao to Cabanatuan, deluxe coaches'],
    ],
    'tarlac' => [
        ['name' => 'Genesis Transport', 'type' => 'bus', 'url' => 'https://www.genesistransport.com.ph/', 'note' => 'Cubao to Tarlac, hourly via NLEX-SCTEX'],
        ['name' => 'Victory Liner', 'type' => 'bus', 'url' => 'https://www.victoryliner.com/', 'note' => 'Manila to Tarlac, with Baguio continuation'],
    ],
    'urdaneta' => [
        ['name' => 'Victory Liner', 'type' => 'bus', 'url' => 'https://www.victoryliner.com/', 'note' => 'Cubao to Urdaneta Pangasinan, multiple daily'],
        ['name' => 'Five Star', 'type' => 'bus', 'url' => 'https://www.facebook.com/fivestar.busline', 'note' => 'Pasay to Urdaneta'],
    ],
    'dingalan' => [
        ['name' => 'Genesis Transport', 'type' => 'bus', 'url' => 'https://www.genesistransport.com.ph/', 'note' => 'Cubao to Cabanatuan, transfer to Dingalan via van'],
        ['name' => 'Private car', 'type' => 'car', 'url' => '#', 'note' => 'Mountain road for the last 90 min, 4WD recommended in wet months'],
    ],

    // === MINDANAO (long-haul, flight strongly preferred) ===
    'davao-city' => [
        ['name' => 'Cebu Pacific', 'type' => 'airline', 'url' => 'https://www.cebupacificair.com/', 'note' => 'Manila to Davao, multiple daily flights, 1h 45m'],
        ['name' => 'Philippine Airlines', 'type' => 'airline', 'url' => 'https://www.philippineairlines.com/', 'note' => 'Daily direct Manila-Davao'],
        ['name' => 'Philippines AirAsia', 'type' => 'airline', 'url' => 'https://www.airasia.com/', 'note' => 'Budget option Manila-Davao'],
    ],
    'samal-island' => [
        ['name' => 'Cebu Pacific (to Davao)', 'type' => 'airline', 'url' => 'https://www.cebupacificair.com/', 'note' => 'Fly to Davao, then RoRo barge from Sasa Wharf'],
        ['name' => 'Sasa-Babak RoRo', 'type' => 'ferry', 'url' => '#', 'note' => 'Davao Sasa Wharf to Samal Babak, 10 min, every 30 min'],
    ],
    'general-santos' => [
        ['name' => 'Cebu Pacific', 'type' => 'airline', 'url' => 'https://www.cebupacificair.com/', 'note' => 'Manila to General Santos (GES), daily flights'],
        ['name' => 'Philippine Airlines', 'type' => 'airline', 'url' => 'https://www.philippineairlines.com/', 'note' => 'PR Manila-GenSan direct'],
    ],
    'glan-sarangani' => [
        ['name' => 'Cebu Pacific (to GenSan)', 'type' => 'airline', 'url' => 'https://www.cebupacificair.com/', 'note' => 'Fly to General Santos, then 2-hour van to Glan'],
        ['name' => 'Yellow Bus Line', 'type' => 'bus', 'url' => 'https://www.facebook.com/yellowbuslines', 'note' => 'GenSan to Glan, every 30-60 min'],
    ],
    'zamboanga-city' => [
        ['name' => 'Cebu Pacific', 'type' => 'airline', 'url' => 'https://www.cebupacificair.com/', 'note' => 'Manila and Cebu to Zamboanga (ZAM), daily'],
        ['name' => 'Philippine Airlines', 'type' => 'airline', 'url' => 'https://www.philippineairlines.com/', 'note' => 'Manila-Zamboanga direct'],
    ],
    'kidapawan' => [
        ['name' => 'Cebu Pacific (to Davao)', 'type' => 'airline', 'url' => 'https://www.cebupacificair.com/', 'note' => 'Fly to Davao, then 2-3 hour van to Kidapawan'],
        ['name' => 'Yellow Bus Line', 'type' => 'bus', 'url' => 'https://www.facebook.com/yellowbuslines', 'note' => 'Davao to Kidapawan and Cotabato route'],
    ],

    // === VISAYAS (flights + ferries) ===
    'cebu-city' => [
        ['name' => 'Cebu Pacific', 'type' => 'airline', 'url' => 'https://www.cebupacificair.com/', 'note' => 'Their home hub, dozens of daily flights from Manila and other cities'],
        ['name' => 'Philippine Airlines', 'type' => 'airline', 'url' => 'https://www.philippineairlines.com/', 'note' => 'Multiple daily direct Manila-Cebu'],
        ['name' => '2GO Travel', 'type' => 'ferry', 'url' => 'https://travel.2go.com.ph/', 'note' => 'Manila to Cebu by sea, 22 hours overnight'],
    ],
    'mactan' => [
        ['name' => 'Cebu Pacific', 'type' => 'airline', 'url' => 'https://www.cebupacificair.com/', 'note' => 'Direct flights into Mactan-Cebu Airport (CEB)'],
        ['name' => 'Philippine Airlines', 'type' => 'airline', 'url' => 'https://www.philippineairlines.com/', 'note' => 'PR Manila-Cebu, frequent'],
    ],
    'panglao' => [
        ['name' => 'Cebu Pacific', 'type' => 'airline', 'url' => 'https://www.cebupacificair.com/', 'note' => 'Direct flights into Bohol-Panglao Airport (TAG)'],
        ['name' => 'OceanJet', 'type' => 'ferry', 'url' => 'https://www.oceanjet.net/', 'note' => 'Cebu to Tagbilaran fast ferry, 2 hours, multiple daily'],
    ],
    'dumaguete' => [
        ['name' => 'Cebu Pacific', 'type' => 'airline', 'url' => 'https://www.cebupacificair.com/', 'note' => 'Direct Manila-Dumaguete (DGT), 1h 30m'],
        ['name' => 'Philippine Airlines', 'type' => 'airline', 'url' => 'https://www.philippineairlines.com/', 'note' => 'PR Manila-Dumaguete daily'],
        ['name' => 'OceanJet (Cebu)', 'type' => 'ferry', 'url' => 'https://www.oceanjet.net/', 'note' => 'Cebu to Dumaguete via Tagbilaran, scenic but long'],
    ],
    'dauin' => [
        ['name' => 'Cebu Pacific (to Dumaguete)', 'type' => 'airline', 'url' => 'https://www.cebupacificair.com/', 'note' => 'Fly to Dumaguete, then 45-min van to Dauin'],
        ['name' => 'Ceres Liner', 'type' => 'bus', 'url' => 'https://www.facebook.com/CeresLinerOfficial', 'note' => 'Dumaguete to Dauin, every 30-60 min'],
    ],
    'iloilo-city' => [
        ['name' => 'Cebu Pacific', 'type' => 'airline', 'url' => 'https://www.cebupacificair.com/', 'note' => 'Manila to Iloilo (ILO), daily flights'],
        ['name' => 'Philippine Airlines', 'type' => 'airline', 'url' => 'https://www.philippineairlines.com/', 'note' => 'PR Manila-Iloilo, multiple daily'],
    ],
    'guimaras' => [
        ['name' => 'Cebu Pacific (to Iloilo)', 'type' => 'airline', 'url' => 'https://www.cebupacificair.com/', 'note' => 'Fly to Iloilo, then short pumpboat from Jordan Wharf'],
        ['name' => 'Jordan Wharf pumpboats', 'type' => 'ferry', 'url' => '#', 'note' => 'Iloilo to Guimaras, 15 minutes, every 15 min'],
    ],
    'bacolod' => [
        ['name' => 'Cebu Pacific', 'type' => 'airline', 'url' => 'https://www.cebupacificair.com/', 'note' => 'Manila and Cebu to Bacolod (BCD), daily'],
        ['name' => 'Philippine Airlines', 'type' => 'airline', 'url' => 'https://www.philippineairlines.com/', 'note' => 'PR Manila-Bacolod direct'],
    ],
    'siquijor' => [
        ['name' => 'Cebu Pacific (to Dumaguete)', 'type' => 'airline', 'url' => 'https://www.cebupacificair.com/', 'note' => 'Fly to Dumaguete, then OceanJet ferry'],
        ['name' => 'OceanJet (Dumaguete)', 'type' => 'ferry', 'url' => 'https://www.oceanjet.net/', 'note' => 'Dumaguete to Siquijor, 45-60 min, multiple daily'],
    ],
    'boracay' => [
        ['name' => 'Cebu Pacific (Caticlan)', 'type' => 'airline', 'url' => 'https://www.cebupacificair.com/', 'note' => 'Manila to Caticlan (MPH), the closer airport, 1 hour'],
        ['name' => 'Philippine Airlines (Kalibo)', 'type' => 'airline', 'url' => 'https://www.philippineairlines.com/', 'note' => 'Manila to Kalibo (KLO), 2-hour bus + boat to Boracay'],
        ['name' => 'Caticlan-Boracay banca', 'type' => 'ferry', 'url' => '#', 'note' => 'From Caticlan Jetty Port, 15 min, included in environmental fee'],
    ],

    // === PALAWAN + MINDORO ===
    'el-nido' => [
        ['name' => 'AirSWIFT', 'type' => 'airline', 'url' => 'https://www.airswift.com.ph/', 'note' => 'Direct Manila to El Nido (Lio Airport), 75 min, daily'],
        ['name' => 'Cebu Pacific (Puerto Princesa)', 'type' => 'airline', 'url' => 'https://www.cebupacificair.com/', 'note' => 'Fly to Puerto Princesa, then 5-6 hour van transfer'],
        ['name' => 'Philippine Airlines (Puerto Princesa)', 'type' => 'airline', 'url' => 'https://www.philippineairlines.com/', 'note' => 'PR Manila-Puerto Princesa direct'],
    ],
    'puerto-galera' => [
        ['name' => 'Montenegro Lines', 'type' => 'ferry', 'url' => 'http://www.montenegrolines.com.ph/', 'note' => 'Batangas Pier to Puerto Galera RoRo, daily departures'],
        ['name' => 'FastCat', 'type' => 'ferry', 'url' => 'https://fastcat.com.ph/', 'note' => 'Batangas Pier to Calapan, transfer to PG via van'],
        ['name' => 'JAM Liner (to Batangas)', 'type' => 'bus', 'url' => 'https://jam.com.ph/', 'note' => 'Cubao to Batangas Pier, hourly, connects to all ferries'],
    ],
];
