<?php

namespace App\Http\Controllers;

/**
 * What to Buy hub at /philippine-souvenirs-pasalubong-what-to-buy.
 *
 * Mirrors the FoodsController / ActivitiesController structure:
 * four hand-curated categories of Philippine pasalubong + heritage
 * goods, with a flat item list per category. Each item carries a
 * stable slug for on-disk image lookup, the name, the source
 * province (badge), and a hand-written one-liner that lasts until
 * the research seeder lands richer copy.
 *
 * Image disk convention:
 * public/storage/rg-media/buys/{slug}-{1|2|3}.jpg
 */
class BuysController extends Controller
{
    use \App\Http\Controllers\Concerns\RendersBlockableHub;

    public function index()
    {
        $cats = $this->categories();
        $svc = app(\App\Services\HubLocationSearch::class);
        $blockView = $this->renderHubBlocks('buys', ['categories' => $cats, 'searchIndex' => $svc->build('buys', $cats), 'featured' => $svc->featured('buys', $cats), 'hubTags' => $svc->tags('buys', $cats)]);
        if ($blockView) return $blockView;
        return view('buys.index', ['categories' => $cats]);
    }

    private function categories(): array
    {
        $researchPath = database_path('data/buys_research.json');
        $research = is_file($researchPath)
            ? json_decode(file_get_contents($researchPath), true) ?: []
            : [];

        $cats = [
            [
                'key' => 'agriculture',
                'label' => 'Heritage Salts & Specialty Agriculture',
                'icon' => '🧂',
                'theme' => 'agriculture',
                'intro' => 'What the land actually puts out. Salt aged in clay over a wood fire, coffee from a 1,500-meter altitude, mangoes that hold geographical indication status, and the only single-origin chocolate in the country picking up world awards. Edible pasalubong.',
                'items' => [
                    ['slug' => 'asin-tibuok', 'name' => 'Asin Tibuok', 'where' => 'Alburquerque, Bohol', 'note' => "Dinosaur-egg-shaped artisanal sea salt, aged in clay pots over a wood fire for months. One block lasts a household."],
                    ['slug' => 'tultul-dokdok', 'name' => 'Tultul / Dokdok', 'where' => 'Guimaras', 'note' => 'Brick-shaped block sea salt boiled down from seawater the old Guimarasnon way. Shaved over rice.'],
                    ['slug' => 'asin-sa-bubu', 'name' => 'Asin sa Bubu', 'where' => 'Pasuquin, Ilocos Norte', 'note' => 'Sea salt steamed and dried inside bamboo pipes. The bamboo flavor stays on the salt.'],
                    ['slug' => 'guimaras-mangoes', 'name' => 'Guimaras Mangoes', 'where' => 'Guimaras Island', 'note' => "The country's only geographically-indicated mango. Honey-sweet, peak season April to June."],
                    ['slug' => 'camiguin-lanzones', 'name' => 'Camiguin Lanzones', 'where' => 'Camiguin Island', 'note' => 'Volcanic-soil lanzones, smaller and sweeter than the standard kind. Festival happens every third week of October.'],
                    ['slug' => 'kapeng-barako', 'name' => 'Kapeng Barako', 'where' => 'Batangas & Cavite', 'note' => "Liberica coffee beans, bold and earthy. The kind your lolo brewed in the morning."],
                    ['slug' => 'sagada-arabica', 'name' => 'Sagada Arabica Coffee', 'where' => 'Mountain Province', 'note' => "Highland Arabica grown at 1,500 meters. Buy whole bean at the Sagada Brew or Bana's cafe and grind at home."],
                    ['slug' => 'sulu-civet-coffee', 'name' => 'Sulu Civet Coffee / Kahawa Sug', 'where' => 'Sulu Archipelago', 'note' => 'Tausug-brewed civet coffee with cardamom and ginger. Served thick in tiny cups.'],
                    ['slug' => 'bicol-pili-nuts', 'name' => 'Bicol Pili Nuts', 'where' => 'Albay & Sorsogon', 'note' => 'Volcanic-soil pili nuts, buttery and crunchy. Sold honey-glazed, salted, or pressed into pili-nut pulp.'],
                    ['slug' => 'cordillera-heritage-rice', 'name' => 'Cordillera Heritage Rice', 'where' => 'Ifugao & Kalinga', 'note' => 'Ominio, Tinawon, and Unoy heirloom strains grown on the Banaue terraces. Sold by Cordillera coop bags.'],
                    ['slug' => 'malagos-chocolate', 'name' => 'Malagos Chocolate', 'where' => 'Davao City', 'note' => 'Award-winning single-origin Davao cacao. Bean-to-bar made at the Puentespina farm in Calinan.'],
                    ['slug' => 'sukang-paombong', 'name' => 'Sukang Paombong', 'where' => 'Paombong, Bulacan', 'note' => 'Naturally fermented nipa palm sap vinegar. The acid is sharper and rounder than supermarket cane vinegar.'],
                    ['slug' => 'sukang-iloko', 'name' => 'Sukang Iloko', 'where' => 'Ilocos Region', 'note' => 'Sugarcane vinegar fermented in earthen jars. Also called sukang Jamon. The dip for bagnet.'],
                    ['slug' => 'davao-puyat-durian', 'name' => 'Davao Puyat Durian', 'where' => 'Davao City', 'note' => 'The local Puyat variety, creamier and less pungent than the Thai durians on supermarket shelves.'],
                ],
            ],
            [
                'key' => 'textiles',
                'label' => 'Traditional Textiles & Weaves',
                'icon' => '🧵',
                'theme' => 'textiles',
                'intro' => 'Every region has its loom, and the cloth tells you where the weaver came from. T\'boli dreamweavers in South Cotabato, Yakan looms in Basilan, the piña fiber pulled by hand in Aklan, the Inabel jacquards of Ilocos. Pricier than the malls, but every meter is sourced from a single household.',
                'items' => [
                    ['slug' => 'tnalak-cloth', 'name' => "T'nalak Cloth", 'where' => 'Lake Sebu, South Cotabato', 'note' => 'Sacred abaca cloth woven by T\'boli dreamweavers. The patterns come to them in dreams. Lang Dulay\'s lineage still runs the school.'],
                    ['slug' => 'inabel-iloko', 'name' => 'Inabel / Abel Iloko', 'where' => 'Ilocos Sur & Norte', 'note' => 'Cotton textile woven on a wooden loom with geometric jacquards. Blankets, towels, table runners.'],
                    ['slug' => 'yakan-tennun', 'name' => 'Yakan Tennun Weave', 'where' => 'Basilan & Lamitan', 'note' => 'Colorful geometric weave from the Yakan tribe. The Yakan Weaving Village in Zamboanga sells direct.'],
                    ['slug' => 'pina-cloth', 'name' => 'Piña Cloth', 'where' => 'Kalibo, Aklan', 'note' => "Ultra-fine luxury fabric handwoven from pineapple leaf fibers. The base cloth for traditional barong Tagalog."],
                    ['slug' => 'hablon', 'name' => 'Hablon', 'where' => 'Miag-ao, Iloilo', 'note' => 'Glossy handwoven silk and cotton textile from Miag-ao weavers. The Bayanihan cloth of Iloilo.'],
                    ['slug' => 'dagmay', 'name' => 'Dagmay', 'where' => 'Davao Oriental', 'note' => "Mandaya tribe's mud-dyed abaca cloth. Earthy reds and browns, sold as bolts and finished bags."],
                    ['slug' => 'mabal-tabih', 'name' => 'Mabal Tabih', 'where' => 'Sarangani Province', 'note' => "Blaan tribe's ikat-dyed abaca weave. Made for tribal skirts, sold as wall panels and bolts to visitors."],
                    ['slug' => 'pis-syabit', 'name' => 'Pis Syabit', 'where' => 'Sulu', 'note' => 'Tausug handwoven head cloth with intricate geometric patterns. Originally a turban, now framed as wall art.'],
                    ['slug' => 'basey-tikog-mats', 'name' => 'Basey Tikog Mats', 'where' => 'Basey, Samar', 'note' => 'Banig mats woven from tikog reed and embroidered with bright florals. Bring home rolled, lasts a generation.'],
                    ['slug' => 'merida-banaca', 'name' => 'Merida Banaca', 'where' => 'Merida, Leyte', 'note' => 'Handwoven fabric blending banana and abaca fibers. Sold by the Merida Banaca cooperative.'],
                ],
            ],
            [
                'key' => 'crafts',
                'label' => 'Heritage Crafts & Artisanal Goods',
                'icon' => '🏺',
                'theme' => 'crafts',
                'intro' => 'Things the country builds with its hands. Burnay potters in Vigan still spin clay over a wood fire. Paete woodcarvers carve santo figures the same way their lolos did. Marikina shoemakers stitch leather one pair at a time. Palawan farms the world\'s only golden South Sea pearls.',
                'items' => [
                    ['slug' => 'burnay-jars', 'name' => 'Burnay Jars', 'where' => 'Vigan, Ilocos Sur', 'note' => 'Unglazed earthenware jars spun on a foot-driven wheel. Used to ferment bagoong, sukang Iloko, and basi wine.'],
                    ['slug' => 'paete-woodcarvings', 'name' => 'Paete Woodcarvings', 'where' => 'Paete, Laguna', 'note' => "Hand-carved santo figures, masks, and religious icons. Paete has been carving since the 1500s."],
                    ['slug' => 'pampanga-parol', 'name' => 'Pampanga Parol', 'where' => 'San Fernando, Pampanga', 'note' => 'Giant kapampangan Christmas lanterns, wired and lit. The Giant Lantern Festival happens every December.'],
                    ['slug' => 'marikina-leather-shoes', 'name' => 'Marikina Leather Shoes', 'where' => 'Marikina City', 'note' => 'Handcrafted full-grain leather footwear from the Marikina shoe district. Custom orders take a week.'],
                    ['slug' => 'palawan-south-sea-pearls', 'name' => 'Palawan South Sea Pearls', 'where' => 'Palawan Island', 'note' => "Golden South Sea pearls, the country's gemstone. The Jewelmer farms grow them in the Sulu Sea."],
                    ['slug' => 'capiz-shell-products', 'name' => 'Capiz Shell Products', 'where' => 'Roxas City, Capiz', 'note' => 'Translucent windowpane oyster shell lamps, chandeliers, and ornaments. Capiz wholesales to the world.'],
                    ['slug' => 'lumban-embroidery', 'name' => 'Lumban Embroidery', 'where' => 'Lumban, Laguna', 'note' => 'Hand-embroidered Piña and jusi cloth for traditional barong Tagalog. The Lumban embroiderers are a guild.'],
                    ['slug' => 'tabaco-scissors', 'name' => 'Tabaco Scissors & Blades', 'where' => 'Tabaco, Albay', 'note' => 'Hand-forged scissors, shears, and bolos from the Tabaco blade smiths. Pasalubong with a serious edge.'],
                    ['slug' => 'balingasag-pottery', 'name' => 'Balingasag Pottery', 'where' => 'Balingasag, Misamis Oriental', 'note' => 'Traditional terracotta clay water jars, planters, and stoves. Sold directly from the kiln.'],
                    ['slug' => 'siquijor-lana', 'name' => 'Siquijor Herbal Oils / Lana', 'where' => 'San Antonio, Siquijor', 'note' => 'Herbal lana oils blended by traditional healers. Pasalubong for the curious; locals use for muscle aches.'],
                ],
            ],
            [
                'key' => 'packaged',
                'label' => 'Iconic Regional Sweet & Savory Packaged Goods',
                'icon' => '📦',
                'theme' => 'packaged',
                'intro' => 'The tin-and-box pasalubong row. Bohol calamay sealed in coconut shells, Bacolod barquillos in tall wax-paper tubes, Carcar chicharon by the kilo, Good Shepherd ube jam in the iconic green jar. Buy at the source if you can, the airport rack version is fine if you can not.',
                'items' => [
                    ['slug' => 'bohol-calamay', 'name' => 'Bohol Calamay', 'where' => 'Jagna, Bohol', 'note' => 'Sticky sweet coconut-and-sticky-rice paste sealed inside two halved coconut shells. Pry open with a spoon.'],
                    ['slug' => 'silay-piaya', 'name' => 'Silay Piaya', 'where' => 'Silay, Negros Occidental', 'note' => 'Flaky flatbread filled with muscovado sugar. Merci and Sugarlandia bake them by the box.'],
                    ['slug' => 'binagol-leyte', 'name' => 'Binagol', 'where' => 'Dagami, Leyte', 'note' => 'Sweet taro pudding cooked and served inside a halved coconut shell. Eaten with a spoon over coffee.'],
                    ['slug' => 'moron-leyte', 'name' => 'Moron', 'where' => 'Tacloban, Leyte', 'note' => 'Chocolate and milk-flavored sticky-rice cake, twisted and wrapped in banana leaf. Tacloban pasalubong.'],
                    ['slug' => 'camiguin-pastel', 'name' => 'Camiguin Pastel', 'where' => 'Mambajao, Camiguin', 'note' => 'Soft buns filled with sweet custard or yema. Vjandep at the Mambajao branch is the original.'],
                    ['slug' => 'tuguegarao-chicharabao', 'name' => 'Tuguegarao Chicharabao', 'where' => 'Tuguegarao, Cagayan', 'note' => 'Crispy puffed carabao hide cracker. Drier than pork chicharon, lighter than rice crackers.'],
                    ['slug' => 'carcar-chicharon', 'name' => 'Carcar Chicharon', 'where' => 'Carcar, Cebu', 'note' => 'Pork rind crackers with backfat still attached. Sold along the Carcar main road by the kilo bag.'],
                    ['slug' => 'bacolod-barquillos', 'name' => 'Bacolod Barquillos', 'where' => 'Bacolod, Negros Occidental', 'note' => 'Thin, crunchy rolled wafer tubes. El Ideal Bakery has been making them on Lacson Street since 1920.'],
                    ['slug' => 'palapa-maranao', 'name' => 'Palapa (jarred)', 'where' => 'Lanao del Sur', 'note' => 'Maranao spicy condiment of sakurab scallion, ginger, and chili, sold in glass jars. Goes on everything.'],
                    ['slug' => 'budbud-kabog', 'name' => 'Budbud Kabog', 'where' => 'Catmon, Cebu', 'note' => 'Rare kodo millet sticky cake wrapped in banana leaf. Eaten with sikwate hot chocolate.'],
                    ['slug' => 'iligan-cheddar-halaya', 'name' => 'Iligan Cheddar Halaya', 'where' => 'Iligan City', 'note' => 'Purple ube jam layered with cheddar cheese. The Iligan twist on Good Shepherd.'],
                    ['slug' => 'good-shepherd-ube-jam', 'name' => 'Good Shepherd Ube Jam', 'where' => 'Baguio City', 'note' => 'The benchmark ube jam, made by the Good Shepherd nuns. The green-capped glass jar.'],
                ],
            ],
        ];

        $imageDir = public_path('storage/rg-media/buys');
        foreach ($cats as &$cat) {
            foreach ($cat['items'] as &$item) {
                $slug = $item['slug'];
                if (isset($research[$slug]['description'])) {
                    $item['description'] = $research[$slug]['description'];
                }
                $images = [];
                if (is_dir($imageDir)) {
                    foreach ([1, 2, 3] as $n) {
                        $candidate = $imageDir . DIRECTORY_SEPARATOR . $slug . '-' . $n . '.jpg';
                        if (is_file($candidate)) {
                            $images[] = asset('storage/rg-media/buys/' . $slug . '-' . $n . '.jpg');
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
