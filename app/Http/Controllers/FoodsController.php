<?php

namespace App\Http\Controllers;

/**
 * Foods hub at /filipino-food-dishes-what-to-eat.
 *
 * Mirrors the ActivitiesController structure: 7 hardcoded
 * categories (popular staples, street food + offal, daredevil
 * exotics, Luzon / Visayas / Mindanao regional specialties,
 * sweets) with a flat dish list per category. Each dish has a
 * stable slug so disk-based images line up by name; researched
 * descriptions and images merge in from
 * database/data/foods_research.json + on-disk image scan.
 *
 * Image disk convention matches the activities pattern:
 * public/storage/rg-media/foods/{slug}-{1|2|3}.jpg
 */
class FoodsController extends Controller
{
    public function index()
    {
        return view('foods.index', [
            'categories' => $this->categories(),
        ]);
    }

    private function categories(): array
    {
        $researchPath = database_path('data/foods_research.json');
        $research = is_file($researchPath)
            ? json_decode(file_get_contents($researchPath), true) ?: []
            : [];

        $cats = [
            [
                'key' => 'staples',
                'label' => 'Popular & Mainstream Staples',
                'icon' => '🍲',
                'theme' => 'staples',
                'intro' => 'The everyday Filipino kitchen. Adobo and sinigang are on the table at lunch, lechon shows up at every birthday, and pancit makes the rounds every fiesta. Start here if it is your first Filipino food trip.',
                'items' => [
                    ['slug' => 'adobo', 'name' => 'Adobo', 'note' => 'Vinegar-and-soy braise. The national dish, every household has its own ratio.'],
                    ['slug' => 'sinigang', 'name' => 'Sinigang', 'note' => 'Sour tamarind soup. The taste of a Filipino kitchen at lunch.'],
                    ['slug' => 'lechon', 'name' => 'Lechon', 'note' => 'Whole roasted pig. Cebu and Manila each claim theirs as the best.'],
                    ['slug' => 'kare-kare', 'name' => 'Kare-Kare', 'note' => 'Oxtail in peanut sauce, eaten with bagoong on the side.'],
                    ['slug' => 'sisig', 'name' => 'Sisig', 'note' => "Pampanga's chopped pig face on a sizzling plate. Egg cracked on top."],
                    ['slug' => 'crispy-pata', 'name' => 'Crispy Pata', 'note' => 'Deep-fried pork knuckle. Skin first, every time.'],
                    ['slug' => 'bulalo', 'name' => 'Bulalo', 'note' => 'Slow-simmered beef shank and bone marrow. Tagaytay air helps.'],
                    ['slug' => 'tinola', 'name' => 'Tinola', 'note' => 'Ginger-clear chicken soup with green papaya and chili leaves.'],
                    ['slug' => 'nilaga', 'name' => 'Nilaga', 'note' => 'Boiled beef or pork with potato, cabbage, and saba banana.'],
                    ['slug' => 'kaldereta', 'name' => 'Kaldereta', 'note' => 'Tomato-based goat or beef stew, sometimes with liver and cheese.'],
                    ['slug' => 'mechado', 'name' => 'Mechado', 'note' => 'Beef braise with potato and bell pepper, lighter than kaldereta.'],
                    ['slug' => 'afritada', 'name' => 'Afritada', 'note' => 'Chicken or pork in tomato sauce, the every-Sunday option.'],
                    ['slug' => 'menudo', 'name' => 'Menudo', 'note' => 'Diced pork and liver in tomato sauce. Fiesta staple.'],
                    ['slug' => 'pinakbet', 'name' => 'Pinakbet', 'note' => 'Ilocano vegetable stew with bagoong. Bitter melon, eggplant, okra.'],
                    ['slug' => 'pancit-canton', 'name' => 'Pancit Canton', 'note' => 'Egg noodles stir-fried with mixed meat and vegetables.'],
                    ['slug' => 'pancit-bihon', 'name' => 'Pancit Bihon', 'note' => 'Rice noodle version of pancit. The lighter cousin.'],
                    ['slug' => 'pancit-palabok', 'name' => 'Pancit Palabok', 'note' => 'Rice noodles in orange shrimp sauce, topped with chicharon and egg.'],
                    ['slug' => 'pancit-malabon', 'name' => 'Pancit Malabon', 'note' => 'Thicker version of palabok, the Malabon pride.'],
                    ['slug' => 'pancit-lomi', 'name' => 'Pancit Lomi', 'note' => 'Thick yellow egg noodles in a starchy broth. Batangas favorite.'],
                    ['slug' => 'lumpia-shanghai', 'name' => 'Lumpia Shanghai', 'note' => 'Crispy thin pork rolls. Eat with sweet chili.'],
                    ['slug' => 'lumpiang-sariwa', 'name' => 'Lumpiang Sariwa', 'note' => 'Fresh, unfried lumpia with peanut-garlic sauce.'],
                    ['slug' => 'chicken-inasal', 'name' => 'Chicken Inasal', 'note' => "Bacolod's lemongrass-and-annatto grilled chicken. Dip in chicken oil."],
                    ['slug' => 'tocino', 'name' => 'Tocino', 'note' => 'Sweet-cured pork strips. Breakfast standard with garlic rice.'],
                    ['slug' => 'beef-tapa', 'name' => 'Beef Tapa', 'note' => 'Cured dried beef. Tapsilog is its breakfast plate.'],
                    ['slug' => 'daing-na-bangus', 'name' => 'Daing na Bangus', 'note' => 'Butterflied milkfish, marinated and fried. Vinegar dip.'],
                    ['slug' => 'tortang-talong', 'name' => 'Tortang Talong', 'note' => 'Eggplant omelette. Cheap, fast, perfect with rice.'],
                    ['slug' => 'bicol-express', 'name' => 'Bicol Express', 'note' => 'Pork in coconut milk and chilis. Bicolano heat.'],
                ],
            ],
            [
                'key' => 'street',
                'label' => 'Street Food & Offal Cuts',
                'icon' => '🍢',
                'theme' => 'street',
                'intro' => 'Pull up to any tricycle stop after sunset and the air smells like grilled isaw. Street food is where Filipinos talk politics, basketball, and the latest chismis. Eat standing, dip in vinegar.',
                'items' => [
                    ['slug' => 'isaw', 'name' => 'Isaw', 'note' => 'Grilled chicken or pig intestines on a stick. Dip in spiced vinegar.'],
                    ['slug' => 'betamax', 'name' => 'Betamax', 'note' => 'Squares of coagulated pig blood, grilled. Named for the cassette.'],
                    ['slug' => 'adidas', 'name' => 'Adidas', 'note' => 'Grilled chicken feet. The skin and tendon are the prize.'],
                    ['slug' => 'helmet', 'name' => 'Helmet', 'note' => 'Whole chicken head, grilled. Brains included.'],
                    ['slug' => 'walkman', 'name' => 'Walkman', 'note' => 'Grilled pig ears. Crunchy edges, chewy middle.'],
                    ['slug' => 'chicharon-bulaklak', 'name' => 'Chicharon Bulaklak', 'note' => 'Deep-fried pig mesentery. Looks like a flower, crunches like a chip.'],
                    ['slug' => 'chicharon-bituka', 'name' => 'Chicharon Bituka', 'note' => 'Fried pig intestines. Bar food with vinegar dip.'],
                    ['slug' => 'dinuguan', 'name' => 'Dinuguan', 'note' => 'Pork blood stew. Served with puto for the sweet-savory pairing.'],
                    ['slug' => 'bopis', 'name' => 'Bopis', 'note' => 'Minced pork lungs and heart, sauteed spicy. Beer pulutan.'],
                    ['slug' => 'papaitan', 'name' => 'Papaitan', 'note' => 'Ilocano bitter offal soup with bile. Hangover cure.'],
                    ['slug' => 'soup-no-5', 'name' => 'Soup No. 5', 'note' => 'Bull testes and penis soup. Pampanga aphrodisiac legend.'],
                    ['slug' => 'tokwa-baboy', 'name' => "Tokwa't Baboy", 'note' => 'Fried tofu and crispy pork ear in soy-vinegar dip. Goto sidekick.'],
                    ['slug' => 'kwek-kwek', 'name' => 'Kwek-Kwek / Tokneneng', 'note' => 'Orange-battered deep-fried quail or chicken eggs. Vinegar dip.'],
                    ['slug' => 'fishball', 'name' => 'Fishball, Squidball, Kikiam', 'note' => 'Skewered fried street snacks. The kuya doles out sauces in a tiny cup.'],
                    ['slug' => 'day-old-chick', 'name' => 'Day-Old Chick', 'note' => 'Deep-fried whole day-old chickens. Crunchy, controversial.'],
                    ['slug' => 'proben', 'name' => 'Proben', 'note' => 'Battered fried chicken proventriculus. Sweet-sour vinegar.'],
                    ['slug' => 'atay', 'name' => 'Atay', 'note' => 'Grilled chicken or pork liver skewers. Rich, irony.'],
                    ['slug' => 'balunbalunan', 'name' => 'Balunbalunan', 'note' => 'Grilled chicken gizzards. Chewy in the best way.'],
                    ['slug' => 'tenga', 'name' => 'Tenga', 'note' => 'Pig ear skewers grilled crisp.'],
                    ['slug' => 'goto', 'name' => 'Goto', 'note' => 'Tripe rice porridge with calamansi, fried garlic, and chili.'],
                ],
            ],
            [
                'key' => 'exotic',
                'label' => 'The Daredevil Exotics',
                'icon' => '🐛',
                'theme' => 'exotic',
                'intro' => 'The dishes that pull travel vloggers off their itinerary. Insects, amphibians, reptiles, things from the deep. Not for every table, but they are real local food, eaten regularly in their home provinces.',
                'items' => [
                    ['slug' => 'balut', 'name' => 'Balut', 'note' => 'Fertilized duck embryo. Eaten warm with salt or vinegar.'],
                    ['slug' => 'penoy', 'name' => 'Penoy', 'note' => 'Unfertilized duck egg. Balut without the chick.'],
                    ['slug' => 'tamilok', 'name' => 'Tamilok', 'note' => 'Mangrove woodworm kinilaw. Palawan and Bohol specialty.'],
                    ['slug' => 'salagubang', 'name' => 'Abal-abal / Salagubang', 'note' => 'June beetles, sauteed in vinegar and soy.'],
                    ['slug' => 'abalin', 'name' => 'Abalin', 'note' => 'Beetle larvae. Crunchy outside, creamy inside.'],
                    ['slug' => 'uok', 'name' => 'Uok', 'note' => 'Coconut beetle larvae, eaten live or sauteed.'],
                    ['slug' => 'kamaru', 'name' => 'Kamaru', 'note' => "Mole crickets, sauteed Pampangueno style. Crunchy bar food."],
                    ['slug' => 'abuos', 'name' => 'Abuos', 'note' => 'Weaver ant eggs. Ilocos delicacy with sour notes.'],
                    ['slug' => 'betute-tugak', 'name' => 'Betute Tugak', 'note' => 'Pampanga stuffed frogs. Minced pork inside.'],
                    ['slug' => 'crispy-frog-legs', 'name' => 'Crispy Frog Legs', 'note' => 'Deep-fried, salt-and-pepper. Tastes like chicken, says everyone.'],
                    ['slug' => 'adobong-sawa', 'name' => 'Adobong Sawa', 'note' => 'Adobo-style python. Hunted in rural provinces.'],
                    ['slug' => 'bayawak', 'name' => 'Bayawak', 'note' => 'Monitor lizard, usually grilled or in adobo.'],
                    ['slug' => 'tapang-usa', 'name' => 'Tapang Usa', 'note' => 'Cured venison strips. Cordillera classic.'],
                    ['slug' => 'tapang-baboy-damo', 'name' => 'Tapang Baboy Damo', 'note' => 'Wild boar tapa. Gamier than the farm pork.'],
                    ['slug' => 'bulca-chong', 'name' => 'Bulca Chong', 'note' => 'Carabao bulalo. Tougher meat, deeper broth.'],
                    ['slug' => 'palos', 'name' => 'Palos', 'note' => 'Freshwater eel. Adobo or kinilaw.'],
                    ['slug' => 'adobong-pugita', 'name' => 'Adobong Pugita', 'note' => 'Adobo octopus. Tender if cooked right.'],
                    ['slug' => 'adobong-sahang', 'name' => 'Adobong Sahang', 'note' => 'Sea snails in adobo sauce.'],
                    ['slug' => 'salawaki', 'name' => 'Salawaki', 'note' => 'Fresh sea urchin, eaten raw with calamansi.'],
                    ['slug' => 'ginataang-kuhol', 'name' => 'Ginataang Kuhol', 'note' => 'Golden apple snails in coconut milk and chili.'],
                    ['slug' => 'pinikpikan', 'name' => 'Pinikpikan', 'note' => 'Cordillera chicken beaten with a stick before cooking. Etag goes in.'],
                    ['slug' => 'tuslob-buwa', 'name' => 'Tuslob-Buwa', 'note' => "Cebu pork brain and liver dip. Eat with puso rice."],
                    ['slug' => 'kinunot-na-pagi', 'name' => 'Kinunot na Pagi', 'note' => 'Bicol stingray in coconut milk and chili.'],
                    ['slug' => 'kinunot-na-pating', 'name' => 'Kinunot na Pating', 'note' => 'Baby shark in coconut milk. Controversial, locally common.'],
                    ['slug' => 'palileng', 'name' => 'Palileng', 'note' => 'Cordillera river fish, grilled simply.'],
                    ['slug' => 'kankannool', 'name' => 'Kankannool', 'note' => 'Wild brown forest mushrooms from the Cordillera.'],
                ],
            ],
            [
                'key' => 'luzon',
                'label' => 'Regional Specialties: Luzon',
                'icon' => '🗾',
                'theme' => 'luzon',
                'intro' => 'Ilocos, Cordillera, Pampanga, Bicol, Quezon. Each Luzon province has dishes you do not find anywhere else, often built around what the land or the colonial history left behind.',
                'items' => [
                    ['slug' => 'etag-kiniing', 'name' => 'Etag / Kiniing', 'note' => 'Cured and smoked pork. Cordillera secret weapon for soup.'],
                    ['slug' => 'bagnet', 'name' => 'Bagnet', 'note' => 'Ilocano deep-fried crispy pork belly. Dip in KBL or bagoong.'],
                    ['slug' => 'vigan-empanada', 'name' => 'Vigan / Batac Empanada', 'note' => 'Orange-shelled rice-flour empanada with longganisa and egg.'],
                    ['slug' => 'dinakdakan', 'name' => 'Dinakdakan', 'note' => 'Ilocano grilled pig face and ears, dressed with mayo and chili.'],
                    ['slug' => 'poqui-poqui', 'name' => 'Poqui-Poqui', 'note' => 'Ilocano eggplant scramble with tomato and egg.'],
                    ['slug' => 'dinengdeng', 'name' => 'Dinengdeng', 'note' => 'Ilocano vegetable broth with bagoong isda.'],
                    ['slug' => 'igado', 'name' => 'Igado', 'note' => 'Ilocano pork liver and tenderloin in soy-vinegar.'],
                    ['slug' => 'pancit-batil-patung', 'name' => 'Pancit Batil Patung', 'note' => "Tuguegarao's signature pancit. Topped with poached egg, side bowl of soup."],
                    ['slug' => 'pancit-cabagan', 'name' => 'Pancit Cabagan', 'note' => 'Isabela pancit with miki noodles and crispy lechon carajay.'],
                    ['slug' => 'pancit-habhab', 'name' => 'Pancit Habhab', 'note' => 'Lucban pancit eaten off banana leaf, no spoon, no fork.'],
                    ['slug' => 'longganisa-regional', 'name' => 'Longganisa (Vigan, Lucban, Calumpit, Alaminos, Guagua)', 'note' => 'Every region has its own. Garlicky, sweet, smoky, or sour.'],
                    ['slug' => 'laing', 'name' => 'Laing', 'note' => 'Bicolano taro leaves in coconut milk and chili.'],
                    ['slug' => 'sinantol', 'name' => 'Ginataang Santol / Sinantol', 'note' => 'Grated santol fruit in coconut milk. Quezon specialty.'],
                    ['slug' => 'kinalas', 'name' => 'Kinalas', 'note' => "Naga's noodle soup with shredded beef and a thick brown sauce."],
                    ['slug' => 'sinunong', 'name' => 'Sinunong', 'note' => 'Salt-baked tilapia or fish wrapped in salt crust.'],
                    ['slug' => 'puto-bumbong', 'name' => 'Puto Bumbong', 'note' => 'Purple sticky rice steamed in bamboo. Simbang Gabi tradition.'],
                    ['slug' => 'tamales', 'name' => 'Tamales (Pampanga / Cavite)', 'note' => 'Rice-and-peanut sauce wrapped in banana leaf.'],
                ],
            ],
            [
                'key' => 'visayas',
                'label' => 'Regional Specialties: Visayas',
                'icon' => '🏝️',
                'theme' => 'visayas',
                'intro' => 'Bacolod, Iloilo, Cebu, Bohol. The Visayas is where the broths get richer, the pancit gets its own dialect, and the lechon stops needing sauce.',
                'items' => [
                    ['slug' => 'kansi', 'name' => 'Kansi', 'note' => 'Ilonggo bulalo-bulalo crossover with batuan sourness.'],
                    ['slug' => 'kbl', 'name' => 'KBL (Kadyos, Baboy, Langka)', 'note' => 'Ilonggo pigeon pea, pork, and jackfruit stew.'],
                    ['slug' => 'kadyos-manok-ubad', 'name' => 'Kadyos, Manok, Ubad', 'note' => 'KBL with chicken and banana heart instead of pork.'],
                    ['slug' => 'la-paz-batchoy', 'name' => 'La Paz Batchoy', 'note' => "Iloilo's iconic noodle soup with pork organs and chicharon."],
                    ['slug' => 'pancit-molo', 'name' => 'Pancit Molo', 'note' => 'Iloilo pork dumpling soup. Filipino wonton.'],
                    ['slug' => 'chicken-binakol', 'name' => 'Chicken Binakol', 'note' => 'Coconut-water chicken soup, bamboo-tube cooked traditionally.'],
                    ['slug' => 'humba', 'name' => 'Humba', 'note' => 'Visayan sweet braised pork belly. Mas matamis kaysa adobo.'],
                    ['slug' => 'kinilaw', 'name' => 'Kinilaw', 'note' => 'Raw fish in vinegar, ginger, and chili. Filipino ceviche.'],
                    ['slug' => 'sinuglaw', 'name' => 'Sinuglaw', 'note' => 'Grilled pork belly mixed with tuna kinilaw. Davao crossover.'],
                    ['slug' => 'inun-unan', 'name' => 'Inun-unan', 'note' => 'Visayan paksiw. Fish poached in vinegar and ginger.'],
                    ['slug' => 'paksiyo-baboy-bisaya', 'name' => 'Paksiyo Baboy Bisaya', 'note' => 'Pork in vinegar, soy, and bay leaf. Visayan paksiw style.'],
                    ['slug' => 'chorizo-de-cebu', 'name' => 'Chorizo de Cebu', 'note' => 'Sweet-savory Cebu pork sausages.'],
                    ['slug' => 'ngohiong', 'name' => 'Ngohiong', 'note' => "Cebu's five-spice spring roll, Chinese-influenced."],
                    ['slug' => 'puso-hanging-rice', 'name' => 'Puso (Hanging Rice)', 'note' => 'Rice woven into coconut leaves, hung over the lechon stall.'],
                    ['slug' => 'bam-i', 'name' => 'Bam-I (Pancit Bisaya)', 'note' => 'Visayan pancit with bihon and canton mixed.'],
                    ['slug' => 'sutukil', 'name' => 'SuTuKil (Sugba, Tuwa, Kilaw)', 'note' => 'Mactan tradition. Pick a fish, get it grilled, stewed, and raw.'],
                ],
            ],
            [
                'key' => 'mindanao',
                'label' => 'Regional Specialties: Mindanao',
                'icon' => '🕌',
                'theme' => 'mindanao',
                'intro' => 'The big food story of Mindanao is the Maranao and Tausug kitchens. Turmeric, coconut, palapa, and ingredients you do not see north of the Visayan Sea. Halal-leaning, deeply spiced.',
                'items' => [
                    ['slug' => 'piyanggang-manok', 'name' => 'Piyanggang Manok', 'note' => 'Tausug burnt-coconut grilled chicken.'],
                    ['slug' => 'tiyula-itum', 'name' => 'Tiyula Itum', 'note' => 'Sulu black soup. Burnt coconut, ginger, chili, beef.'],
                    ['slug' => 'satti', 'name' => 'Satti', 'note' => 'Zamboanga skewers in spicy peanut-coconut sauce. Breakfast street food.'],
                    ['slug' => 'pastil', 'name' => 'Pastil', 'note' => 'Maguindanao rice with shredded chicken in banana leaf. Cheap, perfect.'],
                    ['slug' => 'beef-rendang', 'name' => 'Beef Rendang (Maranao)', 'note' => 'Slow-braised beef in coconut milk and spices.'],
                    ['slug' => 'curacha', 'name' => 'Curacha', 'note' => "Zamboanga spanner crab in Alavar's house sauce."],
                    ['slug' => 'dodol', 'name' => 'Dodol', 'note' => 'Sticky chewy coconut-sugar candy. Maranao dessert.'],
                    ['slug' => 'tinagtag', 'name' => 'Tinagtag', 'note' => 'Maguindanao rice-flour fritter, thin and crispy.'],
                    ['slug' => 'binignit', 'name' => 'Binignit', 'note' => 'Coconut milk-based ginataan, with taro, banana, jackfruit.'],
                    ['slug' => 'palapa', 'name' => 'Palapa', 'note' => 'Maranao condiment. Sakurab onion, ginger, chili. Goes on everything.'],
                    ['slug' => 'daral', 'name' => 'Daral', 'note' => 'Maranao coconut crepe rolled with sweet filling.'],
                    ['slug' => 'piarun', 'name' => 'Piarun', 'note' => 'Fish cooked with coconut meat. Tausug style.'],
                    ['slug' => 'syagul', 'name' => 'Syagul', 'note' => 'Stingray cooked in burnt coconut. Tausug specialty.'],
                ],
            ],
            [
                'key' => 'sweets',
                'label' => 'Traditional Sweets & Snacks',
                'icon' => '🍧',
                'theme' => 'sweets',
                'intro' => 'The merienda lineup. Filipino sweets are built on rice, coconut, sugar, and condensed milk in some combination. Halo-Halo is the headliner; the rest live in every corner panaderia, palengke, and roadside vendor.',
                'items' => [
                    ['slug' => 'halo-halo', 'name' => 'Halo-Halo', 'note' => 'Layered shaved ice with beans, ube, leche flan, and a scoop on top.'],
                    ['slug' => 'taho', 'name' => 'Taho', 'note' => 'Soft tofu with arnibal syrup and sago pearls. Mornings only.'],
                    ['slug' => 'champorado', 'name' => 'Champorado', 'note' => 'Chocolate rice porridge. Paired with dried fish in some homes.'],
                    ['slug' => 'suman', 'name' => 'Suman (Latik, Sa Ibus, Moron)', 'note' => 'Sticky rice cakes wrapped in banana or coconut leaf.'],
                    ['slug' => 'puto', 'name' => 'Puto', 'note' => 'Small steamed rice cakes. Comes with dinuguan or as merienda.'],
                    ['slug' => 'kutsinta', 'name' => 'Kutsinta', 'note' => 'Sticky brown jelly cake topped with grated coconut.'],
                    ['slug' => 'bibingka', 'name' => 'Bibingka', 'note' => 'Rice cake topped with salted egg, cheese, and coconut. Simbang Gabi staple.'],
                    ['slug' => 'sapin-sapin', 'name' => 'Sapin-Sapin', 'note' => 'Layered rice cake. Ube, jackfruit, coconut, in three colors.'],
                    ['slug' => 'buko-pie', 'name' => 'Buko Pie', 'note' => 'Young coconut pie. The Laguna pasalubong.'],
                    ['slug' => 'leche-flan', 'name' => 'Leche Flan', 'note' => 'Egg yolk and condensed milk caramel custard.'],
                    ['slug' => 'turon', 'name' => 'Turon', 'note' => 'Caramelized banana and jackfruit wrapped in lumpia wrapper, fried.'],
                    ['slug' => 'banana-cue', 'name' => 'Banana Cue', 'note' => 'Saba banana caramelized on a skewer.'],
                    ['slug' => 'kamote-cue', 'name' => 'Kamote Cue', 'note' => 'Sweet potato cue. Cheaper, denser cousin.'],
                    ['slug' => 'carioca', 'name' => 'Carioca', 'note' => 'Sticky rice balls skewered, fried, and coated in syrup.'],
                    ['slug' => 'ginataang-bilo-bilo', 'name' => 'Ginataang Bilo-Bilo', 'note' => 'Glutinous rice balls in coconut milk with sweet fruits.'],
                    ['slug' => 'piaya', 'name' => 'Piaya', 'note' => "Bacolod's flat muscovado-filled flatbread."],
                    ['slug' => 'binagol', 'name' => 'Binagol', 'note' => 'Leyte coconut-and-taro sweet served in half a coconut shell.'],
                    ['slug' => 'ensaymada', 'name' => 'Ensaymada', 'note' => 'Brioche topped with butter, sugar, and cheese.'],
                    ['slug' => 'maja-blanca', 'name' => 'Maja Blanca', 'note' => 'Coconut milk pudding with corn kernels.'],
                    ['slug' => 'buko-pandan', 'name' => 'Buko Pandan', 'note' => 'Coconut strips and green pandan jelly in cream.'],
                    ['slug' => 'cassava-cake', 'name' => 'Cassava Cake', 'note' => 'Baked grated cassava with custard topping.'],
                    ['slug' => 'yema', 'name' => 'Yema', 'note' => 'Condensed milk and egg yolk candies. Pyramid wrapped in cellophane.'],
                    ['slug' => 'polvoron', 'name' => 'Polvoron', 'note' => 'Crumbly toasted flour and sugar shortbread.'],
                    ['slug' => 'kalamay', 'name' => 'Kalamay', 'note' => 'Sticky coconut-sugar paste, often packed in coconut shells.'],
                    ['slug' => 'tibok-tibok', 'name' => 'Tibok-Tibok', 'note' => 'Pampanga carabao milk pudding. Dense and creamy.'],
                ],
            ],
        ];

        // Merge in researched descriptions, on-disk images, and a
        // displayed rating per dish. Ratings are decorative — the
        // dish isn't really "rated" by users (this is a directory,
        // not a review platform). They're hand-picked to feel
        // believable: famous comfort dishes lean high (4.7-4.9),
        // polarising exotics dip into the low-4s.
        $ratings = $this->ratings();
        $imageDir = public_path('storage/rg-media/foods');
        foreach ($cats as &$cat) {
            foreach ($cat['items'] as &$item) {
                $slug = $item['slug'];
                if (isset($research[$slug]['description'])) {
                    $item['description'] = $research[$slug]['description'];
                }
                $item['rating'] = $ratings[$slug] ?? 4.5;
                $images = [];
                if (is_dir($imageDir)) {
                    foreach ([1, 2, 3] as $n) {
                        $candidate = $imageDir . DIRECTORY_SEPARATOR . $slug . '-' . $n . '.jpg';
                        if (is_file($candidate)) {
                            $images[] = asset('storage/rg-media/foods/' . $slug . '-' . $n . '.jpg');
                        }
                    }
                }
                $item['images'] = $images;
            }
            unset($item);
        }
        unset($cat);

        return $cats;
    }

    /**
     * Slug -> rating map for the decorative card badge. Tuned by feel:
     * comfort staples and well-loved regional dishes sit at 4.6-4.9;
     * polarising exotics and offal cuts dip to 3.8-4.4. Numbers stay
     * inside 3.8-4.9 — pure 5.0 reads as fake and sub-4 reads as bad.
     */
    private function ratings(): array
    {
        return [
            // Staples
            'adobo' => 4.9, 'sinigang' => 4.8, 'lechon' => 4.9, 'kare-kare' => 4.7,
            'sisig' => 4.8, 'crispy-pata' => 4.7, 'bulalo' => 4.8, 'tinola' => 4.5,
            'nilaga' => 4.4, 'kaldereta' => 4.7, 'mechado' => 4.4, 'afritada' => 4.5,
            'menudo' => 4.4, 'pinakbet' => 4.3, 'pancit-canton' => 4.6, 'pancit-bihon' => 4.5,
            'pancit-palabok' => 4.7, 'pancit-malabon' => 4.5, 'pancit-lomi' => 4.4,
            'lumpia-shanghai' => 4.8, 'lumpiang-sariwa' => 4.4, 'chicken-inasal' => 4.7,
            'tocino' => 4.5, 'beef-tapa' => 4.6, 'daing-na-bangus' => 4.5,
            'tortang-talong' => 4.3, 'bicol-express' => 4.6,
            // Street
            'isaw' => 4.6, 'betamax' => 4.4, 'adidas' => 4.5, 'helmet' => 4.2,
            'walkman' => 4.4, 'chicharon-bulaklak' => 4.5, 'chicharon-bituka' => 4.4,
            'dinuguan' => 4.6, 'bopis' => 4.5, 'papaitan' => 4.3, 'soup-no-5' => 4.2,
            'tokwa-baboy' => 4.5, 'kwek-kwek' => 4.6, 'fishball' => 4.7,
            'day-old-chick' => 4.3, 'proben' => 4.3, 'atay' => 4.4, 'balunbalunan' => 4.4,
            'tenga' => 4.3, 'goto' => 4.7,
            // Exotic
            'balut' => 4.3, 'penoy' => 4.2, 'tamilok' => 4.0, 'salagubang' => 3.9,
            'abalin' => 3.8, 'uok' => 3.9, 'kamaru' => 4.2, 'abuos' => 4.0,
            'betute-tugak' => 4.1, 'crispy-frog-legs' => 4.4, 'adobong-sawa' => 4.0,
            'bayawak' => 3.9, 'tapang-usa' => 4.5, 'tapang-baboy-damo' => 4.3,
            'bulca-chong' => 4.2, 'palos' => 4.4, 'adobong-pugita' => 4.5,
            'adobong-sahang' => 4.3, 'salawaki' => 4.4, 'ginataang-kuhol' => 4.4,
            'pinikpikan' => 4.3, 'tuslob-buwa' => 4.5, 'kinunot-na-pagi' => 4.4,
            'kinunot-na-pating' => 4.1, 'palileng' => 4.3, 'kankannool' => 4.3,
            // Luzon
            'etag-kiniing' => 4.5, 'bagnet' => 4.8, 'vigan-empanada' => 4.7,
            'dinakdakan' => 4.5, 'poqui-poqui' => 4.3, 'dinengdeng' => 4.3,
            'igado' => 4.4, 'pancit-batil-patung' => 4.6, 'pancit-cabagan' => 4.5,
            'pancit-habhab' => 4.6, 'longganisa-regional' => 4.7, 'laing' => 4.7,
            'sinantol' => 4.3, 'kinalas' => 4.5, 'sinunong' => 4.3,
            'puto-bumbong' => 4.6, 'tamales' => 4.4,
            // Visayas
            'kansi' => 4.6, 'kbl' => 4.5, 'kadyos-manok-ubad' => 4.4,
            'la-paz-batchoy' => 4.8, 'pancit-molo' => 4.5, 'chicken-binakol' => 4.5,
            'humba' => 4.6, 'kinilaw' => 4.7, 'sinuglaw' => 4.7, 'inun-unan' => 4.4,
            'paksiyo-baboy-bisaya' => 4.4, 'chorizo-de-cebu' => 4.6, 'ngohiong' => 4.4,
            'puso-hanging-rice' => 4.6, 'bam-i' => 4.5, 'sutukil' => 4.7,
            // Mindanao
            'piyanggang-manok' => 4.6, 'tiyula-itum' => 4.5, 'satti' => 4.7,
            'pastil' => 4.6, 'beef-rendang' => 4.6, 'curacha' => 4.7, 'dodol' => 4.4,
            'tinagtag' => 4.3, 'binignit' => 4.6, 'palapa' => 4.7, 'daral' => 4.3,
            'piarun' => 4.4, 'syagul' => 4.3,
            // Sweets
            'halo-halo' => 4.8, 'taho' => 4.7, 'champorado' => 4.6, 'suman' => 4.5,
            'puto' => 4.4, 'kutsinta' => 4.5, 'bibingka' => 4.7, 'sapin-sapin' => 4.6,
            'buko-pie' => 4.7, 'leche-flan' => 4.8, 'turon' => 4.7, 'banana-cue' => 4.6,
            'kamote-cue' => 4.5, 'carioca' => 4.4, 'ginataang-bilo-bilo' => 4.6,
            'piaya' => 4.6, 'binagol' => 4.4, 'ensaymada' => 4.7, 'maja-blanca' => 4.6,
            'buko-pandan' => 4.7, 'cassava-cake' => 4.6, 'yema' => 4.6, 'polvoron' => 4.6,
            'kalamay' => 4.5, 'tibok-tibok' => 4.5,
        ];
    }
}
