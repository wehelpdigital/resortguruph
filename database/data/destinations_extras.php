<?php

/**
 * Optional per-destination extras used by RewriteKeywordPagesSeeder to render
 * conditional sections on each keyword page.
 *
 * Schema per destination:
 *   - history: ['title' => string, 'body' => string (2-4 sentences)]
 *       Renders as a "Historical Significance" section when present.
 *   - festivals: array of ['name' => string, 'month' => string, 'desc' => string]
 *       Renders as a "Notable Festivals & Celebrations" section when at least
 *       one entry exists.
 *
 * Destinations not listed here skip both sections (their page goes straight
 * from Why X to Field Notes to the subtopic blocks).
 */

return [
    'antipolo' => [
        'history' => [
            'title' => 'Pilgrim history at the cathedral',
            'body' => "Antipolo Cathedral has been the home of Our Lady of Peace and Good Voyage since 1626, when Spanish galleon traders carried the icon back from Acapulco. Manila Catholics still walk the Sumulong ascent on Good Friday eve, a centuries-old tradition that locals join the same way their grandparents did.",
        ],
        'festivals' => [
            ['name' => 'Alay Lakad Pilgrimage', 'month' => 'May', 'desc' => 'Tens of thousands walk from Quiapo to the cathedral overnight at the start of Antipolo\'s pilgrimage month.'],
            ['name' => 'Sumakah Festival', 'month' => 'May', 'desc' => 'Antipolo\'s home festival celebrating its four flagships: suman, mangga, kasoy (cashew), and hamaka (hammocks).'],
        ],
    ],
    'tagaytay' => [
        'festivals' => [
            ['name' => 'Pista ng Kalabaw', 'month' => 'Year-round weekends', 'desc' => 'Picnic Grove and Sky Ranch run their busiest weekends during the cool months from November through February.'],
        ],
    ],
    'imus' => [
        'history' => [
            'title' => 'Where Philippine independence was declared',
            'body' => "Kawit is only 10 minutes away from Imus, but the Battle of Imus on September 3, 1896 marked the first major Katipunan victory against Spanish forces. Puente de Isabel II, built in 1857, is the actual ground where it happened, and locals still take photos at the bridge during heritage walks.",
        ],
    ],
    'bulacan-province' => [
        'history' => [
            'title' => 'Birthplace of the First Republic',
            'body' => "Barasoain Church in Malolos hosted the inauguration of the First Philippine Republic in 1899, the first constitutional democracy in Asia. Biak-na-Bato National Park in San Miguel was Aguinaldo\'s revolutionary hideout in 1897, and the caves are still walkable today with a guide from the park rangers.",
        ],
        'festivals' => [
            ['name' => 'Pulilan Carabao Festival', 'month' => 'May 14', 'desc' => 'Decorated carabaos kneel in front of Pulilan Church in a centuries-old farmers\' tribute to San Isidro Labrador.'],
            ['name' => 'Bocaue Pagoda Festival', 'month' => 'Late June or early July', 'desc' => 'A river procession of brightly lit pagodas carrying the Holy Cross of Wawa down the Bocaue River.'],
            ['name' => 'Obando Fertility Dance', 'month' => 'May 17-19', 'desc' => 'Three days of street dances in front of the Obando Church, prayed by couples hoping for children.'],
        ],
    ],
    'pampanga-province' => [
        'festivals' => [
            ['name' => 'Giant Lantern Festival', 'month' => 'December', 'desc' => 'San Fernando\'s 6-meter electric parol competition is the Christmas-season spectacle of Central Luzon. Book a hotel 6 weeks ahead.'],
            ['name' => 'Sinukwan Festival', 'month' => 'December', 'desc' => 'Province-wide celebration of Kapampangan culture, food, and crafts that runs alongside the Lantern Festival.'],
        ],
    ],
    'angeles' => [
        'history' => [
            'title' => 'From American base town to Korean enclave',
            'body' => "Angeles grew up around Clark Air Base during the American period, and the city\'s history is layered with that influence. After Pinatubo\'s 1991 eruption and the base\'s closure, the Freeport reinvented itself as a casino-and-hotel district. Today the Korean community shapes a big part of Angeles\' food scene, especially around Friendship and Balibago.",
        ],
    ],
    'calamba' => [
        'history' => [
            'title' => 'Birthplace of Jose Rizal',
            'body' => "Calamba is where the Philippine national hero was born on June 19, 1861. The Rizal Shrine is a reconstruction of the family\'s bahay na bato painted in unmistakable Calamba green, and the old well in the garden survived two world wars. Entry is free and the place opens early; aim for a Tuesday or Wednesday morning to beat the field-trip crowd.",
        ],
    ],
    'naga-camarines-sur' => [
        'history' => [
            'title' => 'Devotion to Ina',
            'body' => "Devotion to Our Lady of Peñafrancia in Naga goes back to 1710, when the Spanish friar Miguel Robles de Covarrubias enshrined the image. The annual September fluvial procession has been an unbroken tradition for over 300 years, drawing devotees from across the Bicol region.",
        ],
        'festivals' => [
            ['name' => 'Peñafrancia Festival', 'month' => 'Third Saturday of September', 'desc' => 'A nine-day novena that culminates in a candle-lit fluvial procession down the Naga River. The whole city basically pauses for the weekend.'],
        ],
    ],
    'albay-legazpi' => [
        'history' => [
            'title' => 'Mayon and the Cagsawa Ruins',
            'body' => "The Cagsawa belfry is the lone survivor of the 1814 Mayon eruption that buried the old town and over a thousand people in volcanic ash. The ruins frame the volcano in nearly every Bicol postcard ever printed, and the rebuilt church beside the ruins still holds Sunday Mass under Mayon\'s perfect cone.",
        ],
        'festivals' => [
            ['name' => 'Magayon Festival', 'month' => 'May', 'desc' => 'A month-long Bicol heritage celebration at the foot of Mayon, with parades, food fairs, and the Daragang Magayon pageant.'],
        ],
    ],
    'sorsogon' => [
        'festivals' => [
            ['name' => 'Kasanggayahan Festival', 'month' => 'October', 'desc' => 'Sorsogon Province\'s founding anniversary, with street dances, regatta races, and a culinary contest celebrating local pili nut cuisine.'],
        ],
    ],
    'iloilo-city' => [
        'history' => [
            'title' => 'Queen city of the south, 1855',
            'body' => "Iloilo was opened as an international port in 1855 and quickly became one of the wealthiest cities in colonial Asia, thanks to sugar and the textile trade. Molo Church and Jaro Cathedral are the legacy of that boom, both built in the late 1800s. The Calle Real district still has rows of art deco and Spanish-era buildings the locals are slowly restoring.",
        ],
        'festivals' => [
            ['name' => 'Dinagyang Festival', 'month' => 'Fourth weekend of January', 'desc' => 'A street-dance honoring Señor Santo Niño, anchored at Plaza Libertad. Considered the country\'s best executed festival by the Aliwan Fiesta.'],
            ['name' => 'Paraw Regatta', 'month' => 'February', 'desc' => 'The oldest traditional craft race in Asia, with colorful paraws (outrigger sailboats) racing across Iloilo Strait toward Guimaras.'],
        ],
    ],
    'guimaras' => [
        'festivals' => [
            ['name' => 'Manggahan Festival', 'month' => 'May', 'desc' => 'Guimaras Day celebrations during peak mango harvest. Mango-eating contests, agri-trade fairs, and the famous Manggahan Street Dance.'],
        ],
    ],
    'bacolod' => [
        'history' => [
            'title' => 'Sugar barons and The Ruins',
            'body' => "Bacolod\'s wealth came from sugar in the late 1800s, and the haciendero mansions of the era still stand around Talisay and Silay. The Ruins in Talisay is the most famous: an Italianate mansion shell built by Don Mariano Ledesma Lacson for his Portuguese wife Maria Braga, partially burned during WWII to keep it out of Japanese hands.",
        ],
        'festivals' => [
            ['name' => 'MassKara Festival', 'month' => 'Third Sunday of October', 'desc' => 'The smiling-mask street dance that put Bacolod on the international tourism map. Started in 1980 to lift local spirits during the sugar industry crash.'],
        ],
    ],
    'cebu-city' => [
        'history' => [
            'title' => 'Where colonial Asia began',
            'body' => "Magellan landed in Cebu on April 7, 1521, planting the cross still housed in a chapel beside the Basilica del Santo Niño. The city served as the Spanish capital of the islands until Manila took over in 1571. Fort San Pedro, the smallest Spanish fort in the Philippines, anchors the old town today.",
        ],
        'festivals' => [
            ['name' => 'Sinulog Festival', 'month' => 'Third Sunday of January', 'desc' => 'The biggest festival in the Philippines, honoring Santo Niño with two days of street dancing and a fluvial procession down the Mactan Channel.'],
        ],
    ],
    'mactan' => [
        'history' => [
            'title' => 'Lapu-Lapu and the 1521 battle',
            'body' => "Lapu-Lapu defeated Ferdinand Magellan at the Battle of Mactan on April 27, 1521, the first recorded resistance against European colonization in the Philippines. The Liberty Shrine in Punta Engaño marks the spot, and the annual Kadaugan sa Mactan reenactment draws crowds every April.",
        ],
        'festivals' => [
            ['name' => 'Kadaugan sa Mactan', 'month' => 'April 27', 'desc' => 'A full-scale reenactment of the Battle of Mactan on the beach where it happened, with hundreds of warriors in costume.'],
        ],
    ],
    'dumaguete' => [
        'history' => [
            'title' => 'University town with a Spanish bell tower',
            'body' => "Dumaguete grew around Silliman University, the first American-founded university in Asia (1901). The old Spanish bell tower of Dumaguete Cathedral was once used as a watchtower for Moro raiders, and the city\'s reputation as the \"city of gentle people\" goes back to that earlier era of pirate-coast vigilance.",
        ],
    ],
    'siquijor' => [
        'festivals' => [
            ['name' => 'Holy Week Folk Healing Gathering', 'month' => 'Holy Week', 'desc' => 'Traditional healers and herbalists gather in San Antonio to prepare medicinal preparations during Black Saturday. Open to respectful visitors.'],
        ],
    ],
    'boracay' => [
        'history' => [
            'title' => 'From sleepy island to global icon',
            'body' => "Boracay was largely undiscovered until the 1970s, when a German film crew shooting on the island brought back word of the white-sand cove. The first tourist nipa huts went up at Station 1 in the early 1980s. The 2018 government-mandated cleanup closure reset the island\'s tourism rules and gave the reef ecosystem a real chance to recover.",
        ],
        'festivals' => [
            ['name' => 'Ati-Atihan Festival (Kalibo)', 'month' => 'Third weekend of January', 'desc' => 'The mother of all Philippine festivals, held in Kalibo on the mainland (1 hour bus from Caticlan). The original "we go where the people are" street dance that inspired Sinulog and Dinagyang.'],
        ],
    ],
    'el-nido' => [
        'history' => [
            'title' => 'From bird-nest harvest to UNESCO contender',
            'body' => "El Nido takes its name from the swiftlet nests harvested from the limestone cliffs since the late 19th century, a delicacy traded with China for centuries. The town stayed a fishing village until the mid-1980s, when divers started chartering bangkas to the lagoons. Today the entire Bacuit Bay is a protected marine reserve.",
        ],
    ],
    'manila' => [
        'history' => [
            'title' => 'Intramuros and the walled city',
            'body' => "Manila served as the capital of the Spanish East Indies for 333 years, and the walled city of Intramuros was its administrative heart from 1571 until the 1945 Battle of Manila destroyed most of it. Fort Santiago, San Agustin Church, and Casa Manila are the surviving pieces of that era, and they are walkable in a single afternoon.",
        ],
    ],
    'taguig' => [
        'history' => [
            'title' => 'From swamp to global business district',
            'body' => "Bonifacio Global City sits on land that was a US Army base (Fort McKinley) for most of the 20th century. The transformation into the country\'s newest financial district only began in the late 1990s, which is why BGC reads as Asia\'s most planned-looking urban grid: every block, every park, every public-art piece was decided before the buildings went up.",
        ],
    ],
    'sariaya' => [
        'history' => [
            'title' => 'Art Deco heritage from the copra boom',
            'body' => "Sariaya hit its economic peak in the 1920s and 1930s during the copra trade, and the heritage houses lining the town center are the legacy: Gala-Rodriguez House, Natalio Enriquez Mansion, and Don Catalino Rodriguez House all date from that era. Most are private homes today but heritage tours open the gates during the Agawan Festival in May.",
        ],
        'festivals' => [
            ['name' => 'Agawan Festival', 'month' => 'May 15', 'desc' => 'Sariaya\'s San Isidro Labrador thanksgiving. Heritage houses open their gates and the streets get showered with kiping (rice-flour wafers).'],
        ],
    ],
    'lucena' => [
        'festivals' => [
            ['name' => 'Pasayahan sa Lucena', 'month' => 'Last week of May', 'desc' => 'A weeklong city-wide celebration of Lucena founding day with street dances, food stalls, and a Mardi Gras-style parade.'],
            ['name' => 'Pahiyas Festival (Lucban)', 'month' => 'May 15', 'desc' => 'A short jeepney ride from Lucena. Houses in Lucban are decorated head-to-toe with colorful kiping wafers and fresh produce.'],
        ],
    ],
    'amadeo' => [
        'festivals' => [
            ['name' => 'Pahimis Festival', 'month' => 'February', 'desc' => 'Amadeo\'s coffee thanksgiving, with cuppings, farm tours, and a parade of local roasters along the town plaza.'],
        ],
    ],
    'davao-city' => [
        'history' => [
            'title' => 'A Japanese era and a long peace',
            'body' => "Davao had one of the largest Japanese communities in pre-WWII Southeast Asia, with abaca plantations and Japanese schools across the region. Today\'s San Pedro Church and the surrounding old downtown trace the city\'s mestizo history, and the long-running peace under successive mayors has shaped the city\'s identity as orderly and walkable.",
        ],
        'festivals' => [
            ['name' => 'Kadayawan sa Davao', 'month' => 'Third week of August', 'desc' => 'Davao\'s thanksgiving celebration honoring the city\'s eleven Lumad tribes and the abundant harvest of fruits, flowers, and crops.'],
        ],
    ],
    'general-santos' => [
        'festivals' => [
            ['name' => 'Tuna Festival', 'month' => 'First week of September', 'desc' => 'Celebration of General Santos\' identity as the tuna capital of the Philippines. Cooking contests, beach activities, and tuna giveaways at the General Santos Fish Port.'],
        ],
    ],
    'zamboanga-city' => [
        'history' => [
            'title' => 'Fort Pilar and three centuries of Spanish defense',
            'body' => "Fort Pilar was built in 1635 to defend the southern Spanish frontier against Moro raids, and the shrine on its outer wall has been a continuous Marian pilgrimage site since the 1700s. Zamboanga\'s Chavacano language, a Spanish-based creole, is one of the few living legacies of the colonial Spanish-speaking world outside of Spain and Latin America.",
        ],
        'festivals' => [
            ['name' => 'Hermosa Festival', 'month' => 'October 12', 'desc' => 'Zamboanga\'s patronal celebration of Our Lady of the Pillar at Fort Pilar Shrine. A regatta on the Basilan Strait punctuates the religious procession.'],
        ],
    ],
    'la-union' => [
        'festivals' => [
            ['name' => 'San Juan Surfing Cup', 'month' => 'September to December', 'desc' => 'Surf season in La Union peaks during the northeast monsoon. The local surfing cup runs across multiple weekends along Urbiztondo beach.'],
        ],
    ],
];
