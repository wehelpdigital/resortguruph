<?php

/**
 * Per-activity and per-dish primary location pins for the Tourist
 * Map page. Each entry maps an activity/food slug to the place key
 * (matching database/data/ph_places.php) where the user is most
 * likely to actually do the activity / eat the dish.
 *
 * Not every activity has a single primary location (e.g. "trail
 * running" happens everywhere). Only entries here are pinned on
 * the map. The rest are intentionally omitted so the map stays
 * legible.
 *
 * Categories match the controller's category keys (water/land/air/
 * entertainment/cultural/leisure for activities; staples/street/
 * exotic/luzon/visayas/mindanao/sweets for foods) so each pin can
 * deep-link to the right collapsible section on the hub page.
 */

return [
    'activities' => [
        // Water
        'scuba-diving' => ['place' => 'coron', 'name' => 'Scuba Diving', 'category' => 'water'],
        'freediving' => ['place' => 'panglao', 'name' => 'Freediving', 'category' => 'water'],
        'snorkeling-island-hopping' => ['place' => 'el-nido', 'name' => 'Snorkeling & Island Hopping', 'category' => 'water'],
        'canyoneering' => ['place' => 'badian', 'name' => 'Canyoneering', 'category' => 'water'],
        'surfing' => ['place' => 'siargao', 'name' => 'Surfing', 'category' => 'water'],
        'whitewater-rafting' => ['place' => 'cagayan-de-oro', 'name' => 'Whitewater Rafting', 'category' => 'water'],
        'wakeboarding' => ['place' => 'pili', 'name' => 'Wakeboarding', 'category' => 'water'],
        'kiteboarding' => ['place' => 'boracay', 'name' => 'Kiteboarding', 'category' => 'water'],
        'sea-kayaking' => ['place' => 'el-nido', 'name' => 'Sea Kayaking', 'category' => 'water'],
        'sup-paddleboarding' => ['place' => 'boracay', 'name' => 'SUP Paddleboarding', 'category' => 'water'],
        'jet-skiing' => ['place' => 'boracay', 'name' => 'Jet Skiing', 'category' => 'water'],
        'flyboarding' => ['place' => 'boracay', 'name' => 'Flyboarding', 'category' => 'water'],
        'parasailing' => ['place' => 'boracay', 'name' => 'Parasailing', 'category' => 'water'],
        'river-trekking' => ['place' => 'tanay', 'name' => 'River Trekking', 'category' => 'water'],
        'skimboarding' => ['place' => 'liw-liwa', 'name' => 'Skimboarding', 'category' => 'water'],
        'paraw-sailing' => ['place' => 'boracay', 'name' => 'Paraw Sailing', 'category' => 'water'],
        'whale-shark-watching' => ['place' => 'donsol', 'name' => 'Whale Shark Watching', 'category' => 'water'],
        'sardine-run' => ['place' => 'moalboal', 'name' => 'Sardine Run', 'category' => 'water'],
        'dolphin-whale-watching' => ['place' => 'pamilacan', 'name' => 'Dolphin & Whale Watching', 'category' => 'water'],
        'hot-spring-bathing' => ['place' => 'pansol', 'name' => 'Hot Spring Bathing', 'category' => 'water'],
        'waterfall-jumping' => ['place' => 'badian', 'name' => 'Waterfall Jumping', 'category' => 'water'],
        'bioluminescent-plankton' => ['place' => 'el-nido', 'name' => 'Bioluminescent Plankton Tours', 'category' => 'water'],
        'subwing' => ['place' => 'boracay', 'name' => 'Subwing', 'category' => 'water'],
        'bamboo-rafting' => ['place' => 'loboc', 'name' => 'Bamboo Rafting', 'category' => 'water'],
        'river-tubing' => ['place' => 'cagayan-de-oro', 'name' => 'River Tubing', 'category' => 'water'],
        'deep-sea-fishing' => ['place' => 'subic', 'name' => 'Deep Sea Fishing', 'category' => 'water'],
        'helmet-diving' => ['place' => 'boracay', 'name' => 'Helmet Diving', 'category' => 'water'],
        'e-foiling' => ['place' => 'san-juan-la-union', 'name' => 'E-foiling', 'category' => 'water'],
        'underwater-scooter' => ['place' => 'boracay', 'name' => 'Underwater Scooter', 'category' => 'water'],
        'mermaid-swimming' => ['place' => 'boracay', 'name' => 'Mermaid Swimming', 'category' => 'water'],
        'firefly-cruising' => ['place' => 'donsol', 'name' => 'Firefly River Cruising', 'category' => 'water'],

        // Land
        'hiking-mountaineering' => ['place' => 'benguet', 'name' => 'Hiking & Mountaineering', 'category' => 'land'],
        'volcano-trekking' => ['place' => 'angeles', 'name' => 'Volcano Trekking', 'category' => 'land'],
        'atv-offroading' => ['place' => 'legazpi', 'name' => 'ATV & 4x4 Off-Roading', 'category' => 'land'],
        'spelunking' => ['place' => 'sagada', 'name' => 'Spelunking', 'category' => 'land'],
        'sandboarding' => ['place' => 'paoay', 'name' => 'Sandboarding', 'category' => 'land'],
        'rock-climbing' => ['place' => 'toledo', 'name' => 'Rock Climbing', 'category' => 'land'],
        'mountain-biking' => ['place' => 'tanay', 'name' => 'Mountain Biking', 'category' => 'land'],
        'ziplining' => ['place' => 'lake-sebu', 'name' => 'Ziplining', 'category' => 'land'],
        'camping-glamping' => ['place' => 'tanay', 'name' => 'Camping & Glamping', 'category' => 'land'],
        'survival-bushcraft' => ['place' => 'subic', 'name' => 'Survival Bushcraft', 'category' => 'land'],
        'dirt-biking' => ['place' => 'subic', 'name' => 'Dirt Biking', 'category' => 'land'],
        'horseback-riding' => ['place' => 'baguio', 'name' => 'Horseback Riding', 'category' => 'land'],
        'canopy-walk' => ['place' => 'loboc', 'name' => 'Canopy Walk', 'category' => 'land'],
        'bungee-jumping' => ['place' => 'danao-bohol', 'name' => 'Bungee Jumping', 'category' => 'land'],
        'rappelling' => ['place' => 'badian', 'name' => 'Rappelling', 'category' => 'land'],
        'wildlife-safari' => ['place' => 'busuanga', 'name' => 'Wildlife Safari', 'category' => 'land'],
        'longboarding' => ['place' => 'baguio', 'name' => 'Longboarding', 'category' => 'land'],
        'bird-watching' => ['place' => 'candaba', 'name' => 'Bird Watching', 'category' => 'land'],
        'trail-running' => ['place' => 'tanay', 'name' => 'Trail Running', 'category' => 'land'],

        // Air
        'paragliding' => ['place' => 'carmona', 'name' => 'Paragliding', 'category' => 'air'],
        'skydiving' => ['place' => 'clark', 'name' => 'Skydiving', 'category' => 'air'],
        'ultralight-flying' => ['place' => 'magalang', 'name' => 'Ultralight Flying', 'category' => 'air'],
        'hot-air-ballooning' => ['place' => 'lubao', 'name' => 'Hot Air Ballooning', 'category' => 'air'],
        'helicopter-tours' => ['place' => 'makati', 'name' => 'Helicopter Tours', 'category' => 'air'],
        'paramotoring' => ['place' => 'san-felipe-zambales', 'name' => 'Paramotoring', 'category' => 'air'],
        'gyrocopter-flying' => ['place' => 'mactan', 'name' => 'Gyrocopter Flying', 'category' => 'air'],
        'sky-walk' => ['place' => 'cebu-city', 'name' => 'Sky Walk / Edge Coaster', 'category' => 'air'],

        // Entertainment
        'casino-gaming' => ['place' => 'pasay', 'name' => 'Casino Gaming', 'category' => 'entertainment'],
        'theme-parks' => ['place' => 'sta-rosa-laguna', 'name' => 'Theme Parks', 'category' => 'entertainment'],
        'water-parks' => ['place' => 'binan', 'name' => 'Water Parks', 'category' => 'entertainment'],
        'escape-rooms' => ['place' => 'taguig', 'name' => 'Escape Rooms', 'category' => 'entertainment'],
        'interactive-museums' => ['place' => 'quezon-city', 'name' => 'Interactive Museums', 'category' => 'entertainment'],
        'go-karting' => ['place' => 'carmona', 'name' => 'Go-Karting', 'category' => 'entertainment'],
        'paintball-airsoft' => ['place' => 'porac', 'name' => 'Paintball & Airsoft', 'category' => 'entertainment'],
        'target-shooting' => ['place' => 'marikina', 'name' => 'Target Shooting', 'category' => 'entertainment'],
        'bowling-billiards' => ['place' => 'mandaluyong', 'name' => 'Bowling & Billiards', 'category' => 'entertainment'],
        'karaoke-ktv' => ['place' => 'makati', 'name' => 'Karaoke KTV', 'category' => 'entertainment'],
        'nightclubbing' => ['place' => 'makati', 'name' => 'Nightclubbing', 'category' => 'entertainment'],
        'live-music' => ['place' => 'quezon-city', 'name' => 'Live Music', 'category' => 'entertainment'],

        // Cultural
        'heritage-walking-tours' => ['place' => 'manila', 'name' => 'Heritage Walking Tours', 'category' => 'cultural'],
        'bambike-tours' => ['place' => 'manila', 'name' => 'Bambike Tours', 'category' => 'cultural'],
        'cultural-shows' => ['place' => 'baguio', 'name' => 'Cultural Shows', 'category' => 'cultural'],
        'theater-musicals' => ['place' => 'pasay', 'name' => 'Theater & Musicals', 'category' => 'cultural'],
        'museum-art-tours' => ['place' => 'manila', 'name' => 'Museum & Art Tours', 'category' => 'cultural'],
        'weaving-pottery' => ['place' => 'lake-sebu', 'name' => 'Weaving & Pottery Workshops', 'category' => 'cultural'],
        'mambabatok-tattoos' => ['place' => 'buscalan', 'name' => 'Mambabatok Tattoos', 'category' => 'cultural'],
        'indigenous-games' => ['place' => 'sagada', 'name' => 'Indigenous Games', 'category' => 'cultural'],
        'visita-iglesia' => ['place' => 'manaoag', 'name' => 'Visita Iglesia', 'category' => 'cultural'],
        'sabong' => ['place' => 'quezon-city', 'name' => 'Sabong (Cockfighting)', 'category' => 'cultural'],

        // Leisure
        'spa-wellness' => ['place' => 'tagaytay', 'name' => 'Spa & Wellness', 'category' => 'leisure'],
        'mega-mall-shopping' => ['place' => 'pasay', 'name' => 'Mega-Mall Shopping', 'category' => 'leisure'],
        'food-tours' => ['place' => 'manila', 'name' => 'Food Tours', 'category' => 'leisure'],
        'brewery-tours' => ['place' => 'makati', 'name' => 'Brewery Tours', 'category' => 'leisure'],
        'agritourism' => ['place' => 'tagaytay', 'name' => 'Agritourism', 'category' => 'leisure'],
        'staycations' => ['place' => 'boracay', 'name' => 'Staycations', 'category' => 'leisure'],
        'sunset-cruises' => ['place' => 'manila', 'name' => 'Sunset Cruises', 'category' => 'leisure'],
        'golfing' => ['place' => 'carmona', 'name' => 'Golfing', 'category' => 'leisure'],
        'oceanariums' => ['place' => 'manila', 'name' => 'Manila Ocean Park', 'category' => 'leisure'],
    ],

    'foods' => [
        // Famous staples — pinned to where they're best
        'lechon' => ['place' => 'cebu-city', 'name' => 'Lechon', 'category' => 'staples'],
        'sisig' => ['place' => 'angeles', 'name' => 'Sisig', 'category' => 'staples'],
        'bulalo' => ['place' => 'tagaytay', 'name' => 'Bulalo', 'category' => 'staples'],
        'kare-kare' => ['place' => 'manila', 'name' => 'Kare-Kare', 'category' => 'staples'],
        'bicol-express' => ['place' => 'legazpi', 'name' => 'Bicol Express', 'category' => 'staples'],
        'chicken-inasal' => ['place' => 'bacolod', 'name' => 'Chicken Inasal', 'category' => 'staples'],
        'pinakbet' => ['place' => 'vigan', 'name' => 'Pinakbet', 'category' => 'staples'],
        'pancit-malabon' => ['place' => 'manila', 'name' => 'Pancit Malabon', 'category' => 'staples'],

        // Street / offal
        'isaw' => ['place' => 'manila', 'name' => 'Isaw', 'category' => 'street'],
        'balut' => ['place' => 'manila', 'name' => 'Balut', 'category' => 'street'],
        'soup-no-5' => ['place' => 'angeles', 'name' => 'Soup No. 5', 'category' => 'street'],

        // Exotics
        'tamilok' => ['place' => 'puerto-princesa', 'name' => 'Tamilok', 'category' => 'exotic'],
        'kamaru' => ['place' => 'angeles', 'name' => 'Kamaru', 'category' => 'exotic'],
        'betute-tugak' => ['place' => 'angeles', 'name' => 'Betute Tugak', 'category' => 'exotic'],
        'abuos' => ['place' => 'vigan', 'name' => 'Abuos', 'category' => 'exotic'],
        'pinikpikan' => ['place' => 'sagada', 'name' => 'Pinikpikan', 'category' => 'exotic'],
        'tuslob-buwa' => ['place' => 'cebu-city', 'name' => "Tuslob-Buwa", 'category' => 'exotic'],
        'kinunot-na-pagi' => ['place' => 'legazpi', 'name' => 'Kinunot na Pagi', 'category' => 'exotic'],
        'tapang-usa' => ['place' => 'baguio', 'name' => 'Tapang Usa', 'category' => 'exotic'],
        'salawaki' => ['place' => 'iloilo-city', 'name' => 'Salawaki', 'category' => 'exotic'],

        // Luzon regional
        'etag-kiniing' => ['place' => 'sagada', 'name' => 'Etag / Kiniing', 'category' => 'luzon'],
        'bagnet' => ['place' => 'vigan', 'name' => 'Bagnet', 'category' => 'luzon'],
        'vigan-empanada' => ['place' => 'vigan', 'name' => 'Vigan Empanada', 'category' => 'luzon'],
        'dinakdakan' => ['place' => 'vigan', 'name' => 'Dinakdakan', 'category' => 'luzon'],
        'poqui-poqui' => ['place' => 'laoag', 'name' => 'Poqui-Poqui', 'category' => 'luzon'],
        'dinengdeng' => ['place' => 'vigan', 'name' => 'Dinengdeng', 'category' => 'luzon'],
        'igado' => ['place' => 'vigan', 'name' => 'Igado', 'category' => 'luzon'],
        'pancit-batil-patung' => ['place' => 'tuguegarao', 'name' => 'Pancit Batil Patung', 'category' => 'luzon'],
        'pancit-cabagan' => ['place' => 'cabagan', 'name' => 'Pancit Cabagan', 'category' => 'luzon'],
        'pancit-habhab' => ['place' => 'lucban', 'name' => 'Pancit Habhab', 'category' => 'luzon'],
        'longganisa-regional' => ['place' => 'vigan', 'name' => 'Vigan Longganisa', 'category' => 'luzon'],
        'laing' => ['place' => 'legazpi', 'name' => 'Laing', 'category' => 'luzon'],
        'sinantol' => ['place' => 'lucban', 'name' => 'Ginataang Santol', 'category' => 'luzon'],
        'kinalas' => ['place' => 'naga', 'name' => 'Kinalas', 'category' => 'luzon'],
        'puto-bumbong' => ['place' => 'manila', 'name' => 'Puto Bumbong', 'category' => 'luzon'],
        'tamales' => ['place' => 'pampanga', 'name' => 'Filipino Tamales', 'category' => 'luzon'],

        // Visayas regional
        'kansi' => ['place' => 'iloilo-city', 'name' => 'Kansi', 'category' => 'visayas'],
        'kbl' => ['place' => 'iloilo-city', 'name' => 'KBL (Kadyos, Baboy, Langka)', 'category' => 'visayas'],
        'kadyos-manok-ubad' => ['place' => 'iloilo-city', 'name' => 'Kadyos Manok Ubad', 'category' => 'visayas'],
        'la-paz-batchoy' => ['place' => 'iloilo-city', 'name' => 'La Paz Batchoy', 'category' => 'visayas'],
        'pancit-molo' => ['place' => 'iloilo-city', 'name' => 'Pancit Molo', 'category' => 'visayas'],
        'chicken-binakol' => ['place' => 'iloilo-city', 'name' => 'Chicken Binakol', 'category' => 'visayas'],
        'humba' => ['place' => 'cebu-city', 'name' => 'Humba', 'category' => 'visayas'],
        'kinilaw' => ['place' => 'cebu-city', 'name' => 'Kinilaw', 'category' => 'visayas'],
        'sinuglaw' => ['place' => 'davao', 'name' => 'Sinuglaw', 'category' => 'visayas'],
        'inun-unan' => ['place' => 'cebu-city', 'name' => 'Inun-Unan', 'category' => 'visayas'],
        'chorizo-de-cebu' => ['place' => 'cebu-city', 'name' => 'Chorizo de Cebu', 'category' => 'visayas'],
        'ngohiong' => ['place' => 'cebu-city', 'name' => 'Ngohiong', 'category' => 'visayas'],
        'puso-hanging-rice' => ['place' => 'cebu-city', 'name' => 'Puso Hanging Rice', 'category' => 'visayas'],
        'bam-i' => ['place' => 'cebu-city', 'name' => 'Bam-I', 'category' => 'visayas'],
        'sutukil' => ['place' => 'mactan', 'name' => 'SuTuKil', 'category' => 'visayas'],

        // Mindanao regional
        'piyanggang-manok' => ['place' => 'jolo', 'name' => 'Piyanggang Manok', 'category' => 'mindanao'],
        'tiyula-itum' => ['place' => 'jolo', 'name' => 'Tiyula Itum', 'category' => 'mindanao'],
        'satti' => ['place' => 'zamboanga', 'name' => 'Satti', 'category' => 'mindanao'],
        'pastil' => ['place' => 'cotabato', 'name' => 'Pastil', 'category' => 'mindanao'],
        'beef-rendang' => ['place' => 'marawi', 'name' => 'Beef Rendang (Maranao)', 'category' => 'mindanao'],
        'curacha' => ['place' => 'zamboanga', 'name' => 'Curacha', 'category' => 'mindanao'],
        'dodol' => ['place' => 'marawi', 'name' => 'Dodol', 'category' => 'mindanao'],
        'tinagtag' => ['place' => 'cotabato', 'name' => 'Tinagtag', 'category' => 'mindanao'],
        'binignit' => ['place' => 'cebu-city', 'name' => 'Binignit', 'category' => 'mindanao'],
        'palapa' => ['place' => 'marawi', 'name' => 'Palapa', 'category' => 'mindanao'],
        'daral' => ['place' => 'jolo', 'name' => 'Daral', 'category' => 'mindanao'],
        'piarun' => ['place' => 'jolo', 'name' => 'Piarun', 'category' => 'mindanao'],
        'syagul' => ['place' => 'jolo', 'name' => 'Syagul', 'category' => 'mindanao'],

        // Sweets — regional + city-of-origin
        'piaya' => ['place' => 'bacolod', 'name' => 'Piaya', 'category' => 'sweets'],
        'binagol' => ['place' => 'tacloban', 'name' => 'Binagol', 'category' => 'sweets'],
        'tibok-tibok' => ['place' => 'pampanga', 'name' => 'Tibok-Tibok', 'category' => 'sweets'],
        'buko-pie' => ['place' => 'laguna', 'name' => 'Buko Pie', 'category' => 'sweets'],
        'bibingka' => ['place' => 'manila', 'name' => 'Bibingka', 'category' => 'sweets'],
        'sapin-sapin' => ['place' => 'manila', 'name' => 'Sapin-Sapin', 'category' => 'sweets'],
        'halo-halo' => ['place' => 'pampanga', 'name' => 'Halo-Halo', 'category' => 'sweets'],
        'taho' => ['place' => 'manila', 'name' => 'Taho', 'category' => 'sweets'],
        'ensaymada' => ['place' => 'manila', 'name' => 'Ensaymada', 'category' => 'sweets'],
        'maja-blanca' => ['place' => 'manila', 'name' => 'Maja Blanca', 'category' => 'sweets'],
    ],
];
