<?php

namespace App\Http\Controllers;

/**
 * Cultures to Meet hub at /philippine-tribes-ethnic-groups-cultures-to-meet.
 *
 * Lists the country's ~75 ethnolinguistic and indigenous groups
 * across seven categories (Major Lowland, Cordillera, Caraballo /
 * Sierra Madre Sierra, MIMAROPA Island, Visayas Indigenous,
 * Mindanao Lumad, Mindanao Moro). Notes are written with the
 * RJ-Dexplorer-style Filipino-blogger voice but kept anchored to
 * concrete facts (geographic home, language family, one cultural
 * marker) so the page reads as a real cultural guide and not an
 * orientalist sales pitch.
 *
 * Image disk convention: public/storage/rg-media/cultures/{slug}-{1|2|3}.jpg
 */
class CulturesController extends Controller
{
    use \App\Http\Controllers\Concerns\RendersBlockableHub;

    public function index()
    {
        $cats = $this->categories();
        $svc = app(\App\Services\HubLocationSearch::class);
        $blockView = $this->renderHubBlocks('cultures', ['categories' => $cats, 'searchIndex' => $svc->build('cultures', $cats), 'featured' => $svc->featured('cultures', $cats), 'hubTags' => $svc->tags('cultures', $cats)]);
        if ($blockView) return $blockView;
        return view('cultures.index', ['categories' => $cats]);
    }

    private function categories(): array
    {
        $researchPath = database_path('data/cultures_research.json');
        $research = is_file($researchPath)
            ? json_decode(file_get_contents($researchPath), true) ?: []
            : [];

        $cats = [
            [
                'key' => 'lowland',
                'label' => 'Major Ethnolinguistic Groups (Lowland)',
                'icon' => '🇵🇭',
                'theme' => 'lowland',
                'intro' => 'The eight largest groups that shape the modern Filipino mainstream. Between them they cover the bulk of the country\'s population, the national language, the supermalls, and the call centers. The lowland baseline before the highlands and the islands.',
                'items' => [
                    ['slug' => 'tagalog', 'name' => 'Tagalog', 'where' => 'Metro Manila & CALABARZON', 'note' => 'The basis of the Filipino national language. Spread across Metro Manila, the Tagalog provinces, and most of the country\'s media and government.'],
                    ['slug' => 'cebuano', 'name' => 'Cebuano (Bisaya)', 'where' => 'Cebu, Bohol, northern Mindanao', 'note' => 'Bisaya speakers across Cebu, Bohol, eastern Negros, and most of northern Mindanao. The second most-spoken language in the country.'],
                    ['slug' => 'ilocano', 'name' => 'Ilocano (Ilokano)', 'where' => 'Ilocos Region & La Union', 'note' => 'Northern Luzon trader-farmer group, also called Ilokano. Diaspora across Hawaii, California, and almost every province.'],
                    ['slug' => 'hiligaynon', 'name' => 'Hiligaynon (Ilonggo)', 'where' => 'Western Visayas', 'note' => 'Also called Ilonggo. Western Visayas farmers and sugar-haciendero culture around Iloilo, Bacolod, and Roxas.'],
                    ['slug' => 'waray', 'name' => 'Waray', 'where' => 'Samar & Leyte', 'note' => 'Eastern Visayas speakers of Waray-Waray. The pintados-ancestor culture of pre-colonial Samar and Leyte.'],
                    ['slug' => 'bicolano', 'name' => 'Bicolano', 'where' => 'Bicol Region', 'note' => 'Volcanic-soil farmers and chili eaters from Albay, Camarines Sur, Sorsogon. Speakers of Central Bikol and several regional Bikol languages.'],
                    ['slug' => 'kapampangan', 'name' => 'Kapampangan', 'where' => 'Pampanga', 'note' => 'The kitchen capital of Luzon. Pampanga food, the Giant Lantern Festival, sisig, and the Kapampangan language.'],
                    ['slug' => 'pangasinense', 'name' => 'Pangasinense', 'where' => 'Pangasinan & Tarlac', 'note' => 'Pangasinan plains people. Speakers of the Pangasinan language. Known for the Bangus Festival and the Hundred Islands.'],
                ],
            ],
            [
                'key' => 'cordillera',
                'label' => 'Northern Luzon: Cordillera / Igorot Groups',
                'icon' => '⛰️',
                'theme' => 'cordillera',
                'intro' => 'The Cordillera highlands kept Spanish colonization out for 300 years. The ten Igorot groups built the rice terraces, kept the indigenous tattoo and weaving traditions, and run their own modern provincial governments today.',
                'items' => [
                    ['slug' => 'bontoc', 'name' => 'Bontoc (Bontok)', 'where' => 'Mountain Province', 'note' => 'The Bontoc dap-ay men\'s council house anchors community life around Bontoc town. Headhunting past, working rice terraces today.'],
                    ['slug' => 'ibaloi', 'name' => 'Ibaloi (Ibaloy)', 'where' => 'Benguet', 'note' => 'Benguet farmers around Baguio and La Trinidad. The Kabayan Mummy Caves and the Bendian dance are the cultural centerpieces.'],
                    ['slug' => 'ifugao', 'name' => 'Ifugao', 'where' => 'Ifugao Province', 'note' => 'Builders of the Banaue and Batad rice terraces. Ayangan, Tuwali, and Kalanguya subgroups. Hudhud epic chants are UNESCO-listed.'],
                    ['slug' => 'isneg', 'name' => 'Isneg (Apayao)', 'where' => 'Apayao', 'note' => 'Cordillera Apayao river people. Wooden canoes, salt-trade routes, and a distinct Isneg language.'],
                    ['slug' => 'kalinga', 'name' => 'Kalinga', 'where' => 'Kalinga Province', 'note' => 'Cordillera bodong peace-pact keepers and tattoo bearers. Buscalan village is home to Apo Whang-od, the country\'s oldest mambabatok.'],
                    ['slug' => 'kankanaey', 'name' => 'Kankanaey (Kankaney)', 'where' => 'Mountain Province & Benguet', 'note' => 'Kankanaey speakers across Bauko, Sagada, and the western Cordillera. Known for the Sagada hanging coffins and the begnas ritual.'],
                    ['slug' => 'tingguian', 'name' => 'Tingguian (Itneg)', 'where' => 'Abra', 'note' => 'The Itneg of Abra. Backstrap-loom weavers and pinukpok ikat-cloth makers.'],
                    ['slug' => 'balangao', 'name' => 'Balangao', 'where' => 'Mountain Province', 'note' => 'Cordillera highlanders along the Chico River. The Balangao language is one of the most distinct in the Northern Highlands.'],
                    ['slug' => 'karao', 'name' => 'Karao', 'where' => 'Bokod, Benguet', 'note' => 'Smaller Cordillera group around Bokod, Benguet. Karao language is part of the Igorot speech continuum.'],
                    ['slug' => 'bago', 'name' => 'Bago', 'where' => 'Ilocos Sur foothills', 'note' => 'Cordillera-Ilokano hybrid culture in the foothills of Western Cordillera. Sugarcane farmers and basi makers.'],
                ],
            ],
            [
                'key' => 'caraballo',
                'label' => 'Rest of Luzon: Caraballo, Sierra Madre & Lowland IPs',
                'icon' => '🏞️',
                'theme' => 'caraballo',
                'intro' => 'The rest of Luzon outside the Cordillera. The Aeta and Agta foragers across the western foothills and the Sierra Madre rainforest. The Cagayan Valley plains people. The Ivatan of Batanes at the country\'s far north tip.',
                'items' => [
                    ['slug' => 'aeta', 'name' => 'Aeta / Ayta', 'where' => 'Zambales, Pampanga, Bataan, Tarlac', 'note' => 'Among the country\'s earliest known inhabitants. Mt Pinatubo foothills. Mag-antsi, Mag-indi, Magbukun, Ambala, Abellen, Bataan subgroups.'],
                    ['slug' => 'agta', 'name' => 'Agta', 'where' => 'Sierra Madre east coast', 'note' => 'Sierra Madre rainforest foragers in Cagayan, Aurora, Casiguran, Alabat, and Camarines. Related to the Aeta of the west side.'],
                    ['slug' => 'dumagat', 'name' => 'Dumagat', 'where' => 'Aurora, Quezon, Rizal', 'note' => 'Sierra Madre coastal foragers and farmers along the Pacific seaboard from Casiguran to General Nakar.'],
                    ['slug' => 'remontado', 'name' => 'Remontado', 'where' => 'Tanay, Rizal & Quezon', 'note' => 'Tagalog-speaking forest dwellers along the Sierra Madre southwest. Form a Remontado-Dumagat federation.'],
                    ['slug' => 'ilongot', 'name' => 'Ilongot (Bugkalot)', 'where' => 'Nueva Vizcaya & Quirino', 'note' => 'Bugkalot in their own language. Caraballo mountain people, known for past head-taking practices and ritual hunting.'],
                    ['slug' => 'gaddang', 'name' => 'Gaddang', 'where' => 'Nueva Vizcaya & Isabela', 'note' => 'Cagayan Valley farmers and weavers. Speakers of the Gaddang language, related to the Yogad and Ibanag.'],
                    ['slug' => 'ibanag', 'name' => 'Ibanag', 'where' => 'Cagayan & Isabela', 'note' => 'Cagayan river plains people. Famous for pancit cabagan, batil patung, and the Cagayan style of basi wine.'],
                    ['slug' => 'itawes', 'name' => 'Itawes (Itawis)', 'where' => 'Cagayan', 'note' => 'Itawit speakers of the Chico River area in Cagayan. Closely related to the Ibanag and Gaddang.'],
                    ['slug' => 'isinay', 'name' => 'Isinay', 'where' => 'Nueva Vizcaya', 'note' => 'Smaller Cagayan Valley group around Aritao and Dupax. Speakers of the endangered Isinay language.'],
                    ['slug' => 'malaueg', 'name' => 'Malaueg', 'where' => 'Rizal, Cagayan', 'note' => 'Subgroup of the Ibanag along the Cagayan River. The Malaueg language survives in pockets around Rizal, Cagayan.'],
                    ['slug' => 'yogad', 'name' => 'Yogad', 'where' => 'Echague, Isabela', 'note' => 'Cagayan Valley people of Echague. The Yogad language is related to Ibanag and Gaddang.'],
                    ['slug' => 'ivatan', 'name' => 'Ivatan', 'where' => 'Batanes', 'note' => 'Northernmost Filipinos. Stone-house builders and root-crop farmers on Batan and Sabtang. Speakers of Ivatan.'],
                    ['slug' => 'itbayaten', 'name' => 'Itbayaten', 'where' => 'Itbayat, Batanes', 'note' => 'The smaller Batanes group on Itbayat Island. Distinct from mainstream Ivatan but partly mutually intelligible.'],
                    ['slug' => 'ikalahan', 'name' => 'Ikalahan (Kalanguya)', 'where' => 'Nueva Vizcaya & Benguet', 'note' => 'Also called Kalanguya. Cordillera-Caraballo group around Imugan. Known for upland organic farming and the Kalahan Educational Foundation.'],
                ],
            ],
            [
                'key' => 'mimaropa',
                'label' => 'Island Groups (MIMAROPA / Mindoro & Palawan)',
                'icon' => '🏝️',
                'theme' => 'mimaropa',
                'intro' => 'Mindoro\'s eight Mangyan groups still use one of the country\'s last surviving pre-colonial scripts. Palawan has the Tagbanwa, the Pala\'wan, the Batak forest people, and the Cuyonon Spanish-period islanders. Each is distinct from the mainstream Tagalog-Bisaya baseline.',
                'items' => [
                    ['slug' => 'mangyan', 'name' => 'Mangyan', 'where' => 'Mindoro', 'note' => 'Eight subgroups across Mindoro: Alangan, Bangon, Buhid, Hanunuo, Iraya, Ratagnon, Tadyawan, Taubuid. Hanunuo script is among the country\'s last living pre-colonial alphabets.'],
                    ['slug' => 'tagbanwa', 'name' => 'Tagbanwa (Calamian)', 'where' => 'Palawan & Calamianes', 'note' => 'Calamian and central Palawan people. Hold ancestral domain rights over Coron Island. The Tagbanwa script survives as living heritage.'],
                    ['slug' => 'palawan', 'name' => "Pala'wan", 'where' => 'Southern Palawan', 'note' => 'Brooke\'s Point and southern Palawan IPs. Speakers of Palawano. Famous for the kulilal lullaby and the basa-basa healing ritual.'],
                    ['slug' => 'batak', 'name' => 'Batak', 'where' => 'Northern Palawan', 'note' => 'Forest people of the Tanabag and Caramay watersheds. One of the most isolated remaining hunter-gatherer groups in the country.'],
                    ['slug' => 'molbog', 'name' => 'Molbog', 'where' => 'Balabac, Palawan', 'note' => 'Muslim-influenced group on Balabac Island and Bataraza. Related to the Sama through the historic Sulu trade route.'],
                    ['slug' => 'cuyonon', 'name' => 'Cuyonon', 'where' => 'Cuyo Islands', 'note' => 'Cuyo archipelago Christian settlers, the historic Spanish-period capital of Palawan. Lighter-skinned, more Hispanic-Visayan in culture.'],
                    ['slug' => 'agutaynen', 'name' => 'Agutaynen', 'where' => 'Agutaya, Palawan', 'note' => 'Smaller Cuyo-area island group on Agutaya. Speakers of the Agutaynen language, related to Cuyonon and Tagbanwa.'],
                ],
            ],
            [
                'key' => 'visayas',
                'label' => 'Visayas Indigenous Groups',
                'icon' => '🌊',
                'theme' => 'visayas-ip',
                'intro' => 'The Visayas indigenous groups are smaller than the Luzon highlands and the Mindanao Lumad combined, but each one has a distinct origin story. The Ati of Panay run Ati-Atihan. The Suludnon of the Panay interior chant the longest oral epic in the country. The Eskaya of Bohol have their own script.',
                'items' => [
                    ['slug' => 'ati', 'name' => 'Ati', 'where' => 'Aklan, Capiz, Iloilo, Antique', 'note' => 'Visayan Negrito group across Panay. Origin of the Ati-Atihan Festival. Highest population in the Sara hills of Iloilo.'],
                    ['slug' => 'suludnon', 'name' => 'Suludnon (Panay Bukidnon)', 'where' => 'Panay highlands', 'note' => 'Also called Tumandok. Mountain people of the Panay interior. Epic chanters of the Hinilawod, the longest oral epic in the country.'],
                    ['slug' => 'magahat', 'name' => 'Magahat / Bukidnon (Negros)', 'where' => 'Negros mountains', 'note' => 'Highland farmers across central Negros. Distinct from the Mindanao Bukidnon despite the shared name.'],
                    ['slug' => 'korolanos', 'name' => 'Korolanos', 'where' => 'Negros highlands', 'note' => 'Smaller Negros mountain group, closely related to the Magahat. Speakers of a Hiligaynon-related variant.'],
                    ['slug' => 'ata-negros', 'name' => 'Ata (Negros)', 'where' => 'Negros highlands', 'note' => 'Highland Negros Negrito group. Distinct from the Mindanao Ata. Spread across the Don Salvador Benedicto and Kanlaon ranges.'],
                    ['slug' => 'eskaya', 'name' => 'Eskaya', 'where' => 'Bohol', 'note' => 'Bohol cultural group with their own script and language. Centered around Biabas, Guindulman. Origin still debated by anthropologists.'],
                    ['slug' => 'abaknon', 'name' => 'Abaknon', 'where' => 'Capul, Northern Samar', 'note' => 'Sama-related people of Capul Island. Speakers of Inabaknon, the only Sama-Bajaw language outside Mindanao.'],
                    ['slug' => 'bantoanon', 'name' => 'Bantoanon', 'where' => 'Banton, Romblon', 'note' => 'Bantoanon of Banton and Romblon Islands. Speakers of the Asi language, distinct from Romblomanon and Hiligaynon.'],
                ],
            ],
            [
                'key' => 'lumad',
                'label' => 'Mindanao: Lumad (Non-Muslim Indigenous Groups)',
                'icon' => '🦅',
                'theme' => 'lumad',
                'intro' => 'Lumad means born of the land in Cebuano. The non-Muslim Mindanao indigenous peoples cover 18 groups across the highlands, river systems, and southern peninsulas. Each has its own language and ancestral domain, often won and held against logging, mining, and agribusiness encroachment.',
                'items' => [
                    ['slug' => 'subanen', 'name' => 'Subanen (Subanon)', 'where' => 'Zamboanga Peninsula', 'note' => 'Largest Lumad group of the Zamboanga Peninsula. River dwellers, the name meaning people of the upstream.'],
                    ['slug' => 'manobo', 'name' => 'Manobo', 'where' => 'Across Mindanao', 'note' => 'Umbrella name for many subgroups: Agusan, Ata, Matigsalug, Tigwa, Dibabawon, Obo, Pulangiyon, Langilan. Speak related Manobo languages.'],
                    ['slug' => 'higaonon', 'name' => 'Higaonon', 'where' => 'Bukidnon & Misamis Oriental', 'note' => 'Talaandig-related people of northern Mindanao mountains. Famous for the Talasaga Council ritual and the langkit weaving tradition.'],
                    ['slug' => 'bukidnon', 'name' => 'Bukidnon', 'where' => 'Bukidnon Province', 'note' => 'Plateau people of north-central Mindanao. The Bukidnon language is in the Manobo family.'],
                    ['slug' => 'talaandig', 'name' => 'Talaandig', 'where' => 'Bukidnon', 'note' => 'Eastern Bukidnon highlanders. Soil-painting artist Datu Waway Saway is from Songco, Lantapan.'],
                    ['slug' => 'umayamnon', 'name' => 'Umayamnon', 'where' => 'Bukidnon & Agusan del Sur', 'note' => 'Smaller Manobo subgroup along the Umayam River. Live across the Bukidnon-Agusan border.'],
                    ['slug' => 'bagobo', 'name' => 'Bagobo', 'where' => 'Davao', 'note' => 'Davao mountain people. Bagobo-Klata around Mt Apo, Bagobo-Tagabawa in the eastern foothills. Famous for the Inabal abaca weave.'],
                    ['slug' => 'mandaya', 'name' => 'Mandaya', 'where' => 'Davao Oriental', 'note' => 'Eastern Mindanao mountain dwellers. Famous for the Dagmay mud-dyed abaca cloth and intricate tribal tattooing.'],
                    ['slug' => 'mansaka', 'name' => 'Mansaka', 'where' => 'Davao de Oro', 'note' => 'Davao de Oro plateau people. Closely related to the Mandaya. Speakers of the Mansaka language.'],
                    ['slug' => 'kalagan', 'name' => 'Kalagan (Kagan)', 'where' => 'Davao Region', 'note' => 'Also Kagan. Coastal Davao Lumad with Muslim cultural overlay through historic trade with the Sangir and Sama.'],
                    ['slug' => 'tagakaulo', 'name' => 'Tagakaulo', 'where' => 'Davao del Sur & Sarangani', 'note' => 'Mountain people of the Mt Apo southeast slopes. Closely related to the Blaan.'],
                    ['slug' => 'blaan', 'name' => "Blaan (B'laan)", 'where' => 'South Cotabato & Sarangani', 'note' => 'Tribal people of the Davao-Sarangani-Cotabato triangle. Famous for the Mabal Tabih ikat-dyed abaca weave.'],
                    ['slug' => 'tboli', 'name' => "T'boli", 'where' => 'South Cotabato', 'note' => 'Lake Sebu dreamweavers. Famous for the T\'nalak abaca cloth woven from dream-given patterns and brass kuglung instruments.'],
                    ['slug' => 'tiruray', 'name' => 'Tiruray (Teduray)', 'where' => 'Maguindanao', 'note' => 'Also Teduray. Cotabato highlanders south of Cotabato City. Speakers of the Tiruray language.'],
                    ['slug' => 'banwaon', 'name' => 'Banwaon', 'where' => 'Agusan del Sur', 'note' => 'Smaller Manobo subgroup along the Manobo-Higaonon language frontier. Mountain farmers around La Paz, Agusan.'],
                    ['slug' => 'mamanwa', 'name' => 'Mamanwa', 'where' => 'Surigao & Agusan', 'note' => 'Negrito group of northeastern Mindanao. Closely related to the Aeta and Ati linguistically and physically.'],
                    ['slug' => 'sangil', 'name' => 'Sangil', 'where' => 'Sarangani & Davao', 'note' => 'Coastal group of southeastern Mindanao, related to the Sangir Islanders of Indonesia.'],
                    ['slug' => 'ata-davao', 'name' => 'Ata (Davao)', 'where' => 'Davao', 'note' => 'Davao Lumad of the Talomo River area. Distinct from the Negros Ati. Speakers of the Atta Manobo language.'],
                ],
            ],
            [
                'key' => 'moro',
                'label' => 'Mindanao & Sulu: Moro / Muslim Ethnic Groups',
                'icon' => '🕌',
                'theme' => 'moro',
                'intro' => 'The Moro homeland covers the southwest Mindanao mainland, the Sulu Archipelago, and the islands south to Borneo. Ten ethnic groups, Islamic since the 14th century, with three historic sultanates (Sulu, Maguindanao, Lanao). Distinct languages, art, and the okir-and-kulintang cultural register.',
                'items' => [
                    ['slug' => 'maranao', 'name' => 'Maranao', 'where' => 'Lanao del Sur & Lanao del Norte', 'note' => 'Lake Lanao people. Famous for the okir art, brass torogan houses, and the Singkil dance. Speakers of Maranao.'],
                    ['slug' => 'maguindanaon', 'name' => 'Maguindanaon', 'where' => 'Maguindanao', 'note' => 'Cotabato Plain people. The historic Sultanate of Maguindanao. Famous for the kulintang gong ensemble.'],
                    ['slug' => 'tausug', 'name' => 'Tausug', 'where' => 'Sulu Archipelago', 'note' => 'Sulu Sea seafarers. The historic Sultanate of Sulu. Speakers of Tausug. Famous for the silat martial art and the Pangalay dance.'],
                    ['slug' => 'yakan', 'name' => 'Yakan', 'where' => 'Basilan', 'note' => 'Basilan Island farmers and weavers. Famous for the colorful Tennun textile sold at the Yakan Weaving Village in Zamboanga.'],
                    ['slug' => 'sama', 'name' => 'Sama / Samal', 'where' => 'Tawi-Tawi & Sulu', 'note' => 'Also Samal. Tawi-Tawi people, related to the Badjao but more settled. Sama Banguingui subgroup are historic traders.'],
                    ['slug' => 'badjao', 'name' => 'Badjao (Sama Dilaut)', 'where' => 'Tawi-Tawi, Sulu, Zamboanga', 'note' => 'Sea-dwelling Sama Dilaut. Spend most of life on boats and stilt houses over the water. Free-dive fishers and pangalay dancers.'],
                    ['slug' => 'iranun', 'name' => 'Iranun (Ilanun)', 'where' => 'Lanao & Maguindanao', 'note' => 'Also Ilanun. Smaller Moro group between the Maranao and Maguindanaon. Speakers of Iranun, closely related to both.'],
                    ['slug' => 'kalibugan', 'name' => 'Kalibugan (Kolibugan)', 'where' => 'Zamboanga Peninsula', 'note' => 'Also Kolibugan. Subgroup of the Subanen who converted to Islam. Distinct from the mainstream Subanen Lumad.'],
                    ['slug' => 'jama-mapun', 'name' => 'Jama Mapun', 'where' => 'Mapun, Tawi-Tawi', 'note' => 'Smallest Tawi-Tawi Moro group, on Mapun island (formerly Cagayan de Sulu). Speakers of Pullun Mapun.'],
                    ['slug' => 'sangir', 'name' => 'Sangir', 'where' => 'Sarangani islands', 'note' => 'Coastal group with Indonesian Sangihe roots. Speakers of Sangirese. Concentrated in Balut and Sarangani islands.'],
                ],
            ],
        ];

        $imageDir = public_path('storage/rg-media/cultures');
        foreach ($cats as &$cat) {
            foreach ($cat['items'] as &$item) {
                $slug = $item['slug'];
                if (isset($research[$slug]['description'])) {
                    $item['description'] = $research[$slug]['description'];
                }
                $images = [];
                if (is_dir($imageDir)) {
                    // Historical / archive photography on some of these
                    // groups only exists as PNG (Ilongot 1906/1910
                    // plates, for example), so we scan a few extensions
                    // rather than locking the disk format to .jpg.
                    foreach ([1, 2, 3] as $n) {
                        foreach (['jpg', 'png', 'jpeg', 'webp'] as $ext) {
                            $candidate = $imageDir . DIRECTORY_SEPARATOR . $slug . '-' . $n . '.' . $ext;
                            if (is_file($candidate)) {
                                $images[] = asset('storage/rg-media/cultures/' . $slug . '-' . $n . '.' . $ext);
                                break;
                            }
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
}
