<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Seeds the rg_fiestas table with the comprehensive list of Philippine
 * fiestas the site covers — major regional festivals, provincial-level
 * celebrations, and a wide selection of municipal feasts. Each entry
 * carries enough metadata to render the list page (region, month,
 * date_label, summary). Detail content (history, why-go, what-unique,
 * etc.) lives in rg_content_blocks and gets populated by the parallel
 * research workflow + ApplyResearchedFiestaContentSeeder afterward.
 *
 * Idempotent: uses updateOrInsert keyed on slug.
 */
class RgFiestasSeeder extends Seeder
{
    public function run(): void
    {
        $rows = $this->fiestaList();
        $this->command->info('Fiestas to upsert: ' . count($rows));

        foreach ($rows as $row) {
            $payload = array_merge($row, [
                'is_published' => true,
                'published_at' => now(),
                'h1' => $row['h1'] ?? $row['name'],
                'meta_title' => $row['meta_title'] ?? ($row['name'] . ' ' . ($row['date_label'] ?? '') . ' — Resort Guru PH'),
                'meta_description' => $row['meta_description'] ?? $row['summary'] ?? '',
                'updated_at' => now(),
            ]);
            $slug = $payload['slug'];
            $existing = DB::table('rg_fiestas')->where('slug', $slug)->first();
            if ($existing) {
                DB::table('rg_fiestas')->where('id', $existing->id)->update($payload);
            } else {
                $payload['created_at'] = now();
                DB::table('rg_fiestas')->insert($payload);
            }
        }
        $this->command->info('Done.');
    }

    /**
     * Master fiesta list. Organized roughly by region for easier diffing.
     * Each entry: slug, name, region_cluster, province, city_or_town,
     * month (null = movable), date_label, summary.
     */
    private function fiestaList(): array
    {
        return [
            // ─── North Luzon ──────────────────────────────────────────
            ['slug' => 'panagbenga', 'name' => 'Panagbenga (Flower Festival)', 'region_cluster' => 'north-luzon', 'province' => 'Benguet', 'city_or_town' => 'Baguio City', 'month' => 2, 'date_label' => 'Whole month of February (Grand Float Parade last Sunday)', 'summary' => 'Baguio\'s month-long flower festival, with grand float parade, street dance, and Session Road in Bloom.'],
            ['slug' => 'pamulinawen', 'name' => 'Pamulinawen Festival', 'region_cluster' => 'north-luzon', 'province' => 'Ilocos Norte', 'city_or_town' => 'Laoag City', 'month' => 2, 'date_label' => 'February 10', 'summary' => 'Laoag\'s patronal celebration of St. William the Hermit, with kannawidan ti Ilocano cultural events.'],
            ['slug' => 'vigan-longganisa-festival', 'name' => 'Vigan Longganisa Festival', 'region_cluster' => 'north-luzon', 'province' => 'Ilocos Sur', 'city_or_town' => 'Vigan City', 'month' => 1, 'date_label' => 'January 22-25', 'summary' => 'Vigan honors its garlicky longganisa with grilling demos along Calle Crisologo and a festival parade.'],
            ['slug' => 'imbayah', 'name' => 'Imbayah Festival', 'region_cluster' => 'north-luzon', 'province' => 'Ifugao', 'city_or_town' => 'Banaue', 'month' => 4, 'date_label' => 'Last week of April', 'summary' => 'Ifugao\'s harvest festival showcasing native sports, weaving, and rice wine tasting in Banaue.'],
            ['slug' => 'begnas', 'name' => 'Begnas Festival', 'region_cluster' => 'north-luzon', 'province' => 'Mountain Province', 'city_or_town' => 'Sagada', 'month' => null, 'date_label' => 'Movable, tied to the rice cycle (typically January, April, July)', 'summary' => 'Sagada\'s traditional Igorot rice-cycle ritual, the closest thing to a public ceremony you can witness.'],
            ['slug' => 'adivay', 'name' => 'Adivay Festival', 'region_cluster' => 'north-luzon', 'province' => 'Benguet', 'city_or_town' => 'La Trinidad', 'month' => 11, 'date_label' => 'Whole month of November', 'summary' => 'Benguet province\'s month-long cultural and agricultural fair held at La Trinidad.'],
            ['slug' => 'bangus-festival', 'name' => 'Bangus Festival', 'region_cluster' => 'north-luzon', 'province' => 'Pangasinan', 'city_or_town' => 'Dagupan City', 'month' => 4, 'date_label' => 'April 17-30', 'summary' => 'Dagupan\'s annual celebration of its boneless bangus heritage, ending with the longest grill in the country.'],
            ['slug' => 'pistay-dayat', 'name' => 'Pista\'y Dayat (Sea Festival)', 'region_cluster' => 'north-luzon', 'province' => 'Pangasinan', 'city_or_town' => 'Lingayen', 'month' => 5, 'date_label' => 'May 1', 'summary' => 'Lingayen\'s thanksgiving festival for the sea, with banca parade and beachfront merrymaking.'],
            ['slug' => 'bambanti', 'name' => 'Bambanti Festival', 'region_cluster' => 'north-luzon', 'province' => 'Isabela', 'city_or_town' => 'Ilagan City', 'month' => 1, 'date_label' => 'Last week of January', 'summary' => 'Isabela\'s scarecrow festival, with giant bambanti effigies dancing through Ilagan streets.'],

            // ─── Central Luzon ──────────────────────────────────────────
            ['slug' => 'giant-lantern-festival', 'name' => 'Giant Lantern Festival', 'region_cluster' => 'central-luzon', 'province' => 'Pampanga', 'city_or_town' => 'San Fernando', 'month' => 12, 'date_label' => 'Saturday before Christmas Eve', 'summary' => 'San Fernando\'s parul illuminations turn the Christmas Capital of the Philippines into a light competition.'],
            ['slug' => 'sinukwan-festival', 'name' => 'Sinukwan Festival', 'region_cluster' => 'central-luzon', 'province' => 'Pampanga', 'city_or_town' => 'City of San Fernando', 'month' => 12, 'date_label' => 'First week of December', 'summary' => 'Pampanga\'s founding anniversary celebration named after Aring Sinukwan, the Kapampangan supreme deity.'],
            ['slug' => 'pulilan-carabao-festival', 'name' => 'Pulilan Carabao Festival', 'region_cluster' => 'central-luzon', 'province' => 'Bulacan', 'city_or_town' => 'Pulilan', 'month' => 5, 'date_label' => 'May 14-15', 'summary' => 'Carabaos kneel at the church of San Isidro Labrador after a parade through Pulilan, one of the most photographed rural feasts.'],
            ['slug' => 'obando-fertility-rites', 'name' => 'Obando Fertility Rites', 'region_cluster' => 'central-luzon', 'province' => 'Bulacan', 'city_or_town' => 'Obando', 'month' => 5, 'date_label' => 'May 17-19', 'summary' => 'Three-day dance pilgrimage in Obando for San Pascual Baylon, Sta. Clara, and Nuestra Senora de Salambao.'],
            ['slug' => 'singkaban-festival', 'name' => 'Singkaban Festival', 'region_cluster' => 'central-luzon', 'province' => 'Bulacan', 'city_or_town' => 'Malolos', 'month' => 9, 'date_label' => 'Second week of September', 'summary' => 'Bulacan\'s heritage festival, with bamboo arch art (singkaban) and Malolos street parades.'],
            ['slug' => 'apung-iru', 'name' => 'Apung Iru Festival', 'region_cluster' => 'central-luzon', 'province' => 'Pampanga', 'city_or_town' => 'Apalit', 'month' => 6, 'date_label' => 'June 28-30', 'summary' => 'Apalit\'s fluvial parade honoring St. Peter (Apung Iru) along the Pampanga River.'],
            ['slug' => 'hot-air-balloon-festival', 'name' => 'Philippine International Hot Air Balloon Fiesta', 'region_cluster' => 'central-luzon', 'province' => 'Pampanga', 'city_or_town' => 'Lubao', 'month' => 2, 'date_label' => 'February (typically second week)', 'summary' => 'Multi-day sky carnival of hot-air balloons, sport-flying competitions, and aircraft displays.'],
            ['slug' => 'ibon-ebon', 'name' => 'Ibon-Ebon Festival', 'region_cluster' => 'central-luzon', 'province' => 'Pampanga', 'city_or_town' => 'Candaba', 'month' => 2, 'date_label' => 'Second weekend of February', 'summary' => 'Candaba\'s migratory bird and duck-egg festival held at the Candaba Swamp bird sanctuary.'],
            ['slug' => 'bocaue-river-festival', 'name' => 'Bocaue River Festival (Pagoda sa Wawa)', 'region_cluster' => 'central-luzon', 'province' => 'Bulacan', 'city_or_town' => 'Bocaue', 'month' => 7, 'date_label' => 'First Sunday of July', 'summary' => 'Fluvial procession on the Bocaue River carrying the Krus sa Wawa pagoda.'],

            // ─── Metro Manila ──────────────────────────────────────────
            ['slug' => 'feast-of-the-black-nazarene', 'name' => 'Feast of the Black Nazarene (Traslacion)', 'region_cluster' => 'metro-manila', 'province' => 'Metro Manila', 'city_or_town' => 'Manila (Quiapo)', 'month' => 1, 'date_label' => 'January 9', 'summary' => 'Manila\'s largest religious procession, with millions barefoot in Quiapo for the Nazareno.'],
            ['slug' => 'aliwan-fiesta', 'name' => 'Aliwan Fiesta', 'region_cluster' => 'metro-manila', 'province' => 'Metro Manila', 'city_or_town' => 'Pasay', 'month' => 4, 'date_label' => 'April (annual mother of all festivals)', 'summary' => 'Cultural competition gathering provincial festival contingents at Aliw Theater + CCP complex.'],
            ['slug' => 'caracol-sa-makati', 'name' => 'Caracol sa Makati', 'region_cluster' => 'metro-manila', 'province' => 'Metro Manila', 'city_or_town' => 'Makati', 'month' => 1, 'date_label' => 'Last Sunday of January', 'summary' => 'Makati\'s environmental street dance festival with animal-inspired costumes.'],
            ['slug' => 'manila-day', 'name' => 'Manila Day', 'region_cluster' => 'metro-manila', 'province' => 'Metro Manila', 'city_or_town' => 'Manila', 'month' => 6, 'date_label' => 'June 24', 'summary' => 'Manila\'s founding anniversary, with parades and cultural events across the city.'],
            ['slug' => 'pasig-river-festival', 'name' => 'Pasig River Festival', 'region_cluster' => 'metro-manila', 'province' => 'Metro Manila', 'city_or_town' => 'Pasig', 'month' => 1, 'date_label' => 'Mid-January', 'summary' => 'Pasig\'s civic celebration of the city\'s riverside heritage.'],

            // ─── South Luzon (CALABARZON) ──────────────────────────────────────────
            ['slug' => 'pahiyas', 'name' => 'Pahiyas Festival', 'region_cluster' => 'south-luzon', 'province' => 'Quezon', 'city_or_town' => 'Lucban', 'month' => 5, 'date_label' => 'May 15', 'summary' => 'Lucban houses dressed in kiping, fruit, and harvest art for the feast of San Isidro Labrador.'],
            ['slug' => 'niyogyugan', 'name' => 'Niyogyugan Festival', 'region_cluster' => 'south-luzon', 'province' => 'Quezon', 'city_or_town' => 'Lucena City', 'month' => 8, 'date_label' => 'August (last two weeks)', 'summary' => 'Quezon Province\'s month-long coconut festival, anchored at the Quezon Capitol grounds.'],
            ['slug' => 'pasayahan-sa-lucena', 'name' => 'Pasayahan sa Lucena', 'region_cluster' => 'south-luzon', 'province' => 'Quezon', 'city_or_town' => 'Lucena City', 'month' => 5, 'date_label' => 'May (first week)', 'summary' => 'Lucena\'s anniversary celebration with street dancing and provincial parades.'],
            ['slug' => 'sublian', 'name' => 'Sublian Festival', 'region_cluster' => 'south-luzon', 'province' => 'Batangas', 'city_or_town' => 'Batangas City', 'month' => 7, 'date_label' => 'July 23', 'summary' => 'Batangas City\'s founding anniversary, performing the centuries-old subli ritual dance.'],
            ['slug' => 'parada-ng-lechon', 'name' => 'Parada ng Lechon', 'region_cluster' => 'south-luzon', 'province' => 'Batangas', 'city_or_town' => 'Balayan', 'month' => 6, 'date_label' => 'June 24', 'summary' => 'Balayan\'s lechon parade for the feast of St. John the Baptist, plus the famous water-throwing celebration.'],
            ['slug' => 'buntal-hat-festival', 'name' => 'Buntal Hat Festival', 'region_cluster' => 'south-luzon', 'province' => 'Quezon', 'city_or_town' => 'Sariaya', 'month' => 8, 'date_label' => 'August 16', 'summary' => 'Sariaya\'s buntal hat-weaving heritage celebration in front of the heritage ancestral houses.'],
            ['slug' => 'higantes-festival', 'name' => 'Higantes Festival', 'region_cluster' => 'south-luzon', 'province' => 'Rizal', 'city_or_town' => 'Angono', 'month' => 11, 'date_label' => 'November 22-23', 'summary' => 'Angono\'s parade of papier-mache giants (higantes) for the feast of San Clemente.'],
            ['slug' => 'suman-festival-antipolo', 'name' => 'Suman sa Antipolo Festival', 'region_cluster' => 'south-luzon', 'province' => 'Rizal', 'city_or_town' => 'Antipolo', 'month' => 5, 'date_label' => 'May', 'summary' => 'Antipolo\'s celebration of its suman + mango pasalubong heritage.'],
            ['slug' => 'boling-boling', 'name' => 'Boling-Boling Festival', 'region_cluster' => 'south-luzon', 'province' => 'Quezon', 'city_or_town' => 'Catanauan', 'month' => null, 'date_label' => 'Three days before Ash Wednesday', 'summary' => 'Catanauan\'s pre-Lenten merrymaking, the Quezon Province version of Mardi Gras.'],
            ['slug' => 'maytinis', 'name' => 'Maytinis Festival', 'region_cluster' => 'south-luzon', 'province' => 'Cavite', 'city_or_town' => 'Kawit', 'month' => 12, 'date_label' => 'December 24', 'summary' => 'Kawit\'s Christmas Eve parade of biblical scenes leading to the Sta. Maria Magdalena Parish.'],
            ['slug' => 'karakol-cavite', 'name' => 'Karakol Festival', 'region_cluster' => 'south-luzon', 'province' => 'Cavite', 'city_or_town' => 'Various towns', 'month' => null, 'date_label' => 'Patronal feast days year-round', 'summary' => 'Cavite\'s street procession dance accompanying the patron saint through town streets.'],
            ['slug' => 'bangkero-festival', 'name' => 'Bangkero Festival', 'region_cluster' => 'south-luzon', 'province' => 'Laguna', 'city_or_town' => 'Pagsanjan', 'month' => 3, 'date_label' => 'March 12-15', 'summary' => 'Pagsanjan\'s celebration of the bangkeros who paddle tourists up Pagsanjan Falls.'],
            ['slug' => 'anihan-festival', 'name' => 'Anihan Festival', 'region_cluster' => 'south-luzon', 'province' => 'Quezon', 'city_or_town' => 'Various towns', 'month' => 4, 'date_label' => 'April (post-harvest)', 'summary' => 'Quezon Province\'s post-harvest thanksgiving festivities in various rice-growing towns.'],
            ['slug' => 'turumba', 'name' => 'Turumba Festival', 'region_cluster' => 'south-luzon', 'province' => 'Laguna', 'city_or_town' => 'Pakil', 'month' => null, 'date_label' => 'Seven Fridays before Easter through Pentecost', 'summary' => 'Pakil\'s seven-novena Marian celebration for Nuestra Senora de los Dolores de Turumba.'],

            // ─── Bicol ──────────────────────────────────────────
            ['slug' => 'penafrancia', 'name' => 'Peñafrancia Festival', 'region_cluster' => 'bicol', 'province' => 'Camarines Sur', 'city_or_town' => 'Naga City', 'month' => 9, 'date_label' => 'September (third Saturday and surrounding novena)', 'summary' => 'Naga\'s nine-day celebration culminating in the Naga fluvial procession of Nuestra Senora de Penafrancia.'],
            ['slug' => 'magayon-festival', 'name' => 'Magayon Festival', 'region_cluster' => 'bicol', 'province' => 'Albay', 'city_or_town' => 'Legazpi City', 'month' => 5, 'date_label' => 'Whole month of May', 'summary' => 'Albay\'s month-long arts, sports, and tourism festival named after Daragang Magayon of Mt. Mayon legend.'],
            ['slug' => 'ibalong-festival', 'name' => 'Ibalong Festival', 'region_cluster' => 'bicol', 'province' => 'Albay', 'city_or_town' => 'Legazpi City', 'month' => 8, 'date_label' => 'Second to third week of August', 'summary' => 'Legazpi\'s street pageant of the Ibalong epic warriors Baltog, Handyong, and Bantong.'],
            ['slug' => 'tinagba-festival', 'name' => 'Tinagba Festival', 'region_cluster' => 'bicol', 'province' => 'Camarines Sur', 'city_or_town' => 'Iriga City', 'month' => 2, 'date_label' => 'February 11', 'summary' => 'Iriga\'s thanksgiving festival offering produce to the Patroness of Our Lady of Lourdes.'],
            ['slug' => 'pinyasan', 'name' => 'Pinyasan Festival', 'region_cluster' => 'bicol', 'province' => 'Camarines Norte', 'city_or_town' => 'Daet', 'month' => 6, 'date_label' => 'June 15-24', 'summary' => 'Daet\'s pineapple harvest festival, celebrating the formosa pineapple variety.'],
            ['slug' => 'kaogma-festival', 'name' => 'Kaogma Festival', 'region_cluster' => 'bicol', 'province' => 'Camarines Sur', 'city_or_town' => 'Pili', 'month' => 5, 'date_label' => 'Second week of May', 'summary' => 'Camarines Sur\'s founding anniversary celebrated at the Capitol Complex in Pili.'],
            ['slug' => 'tabak-festival', 'name' => 'Tabak Festival', 'region_cluster' => 'bicol', 'province' => 'Albay', 'city_or_town' => 'Tabaco City', 'month' => 3, 'date_label' => 'March', 'summary' => 'Tabaco City\'s celebration of its sharp knife-making heritage and city charter day.'],

            // ─── Visayas ──────────────────────────────────────────
            ['slug' => 'sinulog', 'name' => 'Sinulog Festival', 'region_cluster' => 'visayas', 'province' => 'Cebu', 'city_or_town' => 'Cebu City', 'month' => 1, 'date_label' => 'Third Sunday of January (with novena throughout)', 'summary' => 'The country\'s largest Sto. Niño festival, with grand parade, mass dance, and fluvial procession.'],
            ['slug' => 'ati-atihan', 'name' => 'Ati-Atihan Festival', 'region_cluster' => 'visayas', 'province' => 'Aklan', 'city_or_town' => 'Kalibo', 'month' => 1, 'date_label' => 'Third Sunday of January', 'summary' => 'Kalibo\'s mother of all Philippine festivals, sooted-faced revelers dancing to the call of Hala Bira.'],
            ['slug' => 'dinagyang', 'name' => 'Dinagyang Festival', 'region_cluster' => 'visayas', 'province' => 'Iloilo', 'city_or_town' => 'Iloilo City', 'month' => 1, 'date_label' => 'Fourth Sunday of January', 'summary' => 'Iloilo City\'s street dance competition honoring Sto. Niño and the Ati barter of Panay.'],
            ['slug' => 'masskara', 'name' => 'MassKara Festival', 'region_cluster' => 'visayas', 'province' => 'Negros Occidental', 'city_or_town' => 'Bacolod City', 'month' => 10, 'date_label' => 'October 19 (week-long around)', 'summary' => 'Bacolod\'s smiling-mask festival, born of a sugar-crisis era spirit of cheer.'],
            ['slug' => 'pintados-kasadyaan', 'name' => 'Pintados-Kasadyaan Festival', 'region_cluster' => 'visayas', 'province' => 'Leyte', 'city_or_town' => 'Tacloban City', 'month' => 6, 'date_label' => 'June 29', 'summary' => 'Tacloban\'s tattoo-warrior festival, painted dancers honoring the Sto. Niño + the tattoo tradition of ancient Visayans.'],
            ['slug' => 'sandugo', 'name' => 'Sandugo Festival', 'region_cluster' => 'visayas', 'province' => 'Bohol', 'city_or_town' => 'Tagbilaran City', 'month' => 7, 'date_label' => 'Whole month of July', 'summary' => 'Bohol commemorates the 1565 blood compact between Legazpi and Sikatuna.'],
            ['slug' => 'pana-ad', 'name' => 'Panaad sa Negros Festival', 'region_cluster' => 'visayas', 'province' => 'Negros Occidental', 'city_or_town' => 'Bacolod City', 'month' => 4, 'date_label' => 'Holy Week / April', 'summary' => 'Festival of festivals in Negros Occidental, with replicas of municipal pavilions at Panaad Park.'],
            ['slug' => 'buglasan', 'name' => 'Buglasan Festival', 'region_cluster' => 'visayas', 'province' => 'Negros Oriental', 'city_or_town' => 'Dumaguete City', 'month' => 10, 'date_label' => 'Last week of October', 'summary' => 'Negros Oriental\'s festival of festivals, gathering municipal contingents in Dumaguete.'],
            ['slug' => 'manggahan-sa-guimaras', 'name' => 'Manggahan sa Guimaras', 'region_cluster' => 'visayas', 'province' => 'Guimaras', 'city_or_town' => 'Jordan', 'month' => 5, 'date_label' => 'Last week of May', 'summary' => 'Guimaras celebrates its world-famous sweet mangoes with eat-all-you-can sessions and mango cuisine.'],
            ['slug' => 'pintaflores', 'name' => 'Pintaflores Festival', 'region_cluster' => 'visayas', 'province' => 'Negros Occidental', 'city_or_town' => 'San Carlos City', 'month' => 11, 'date_label' => 'November 3-5', 'summary' => 'San Carlos City\'s painted-flower body-art street dance festival.'],
            ['slug' => 'hala-bira', 'name' => 'Hala Bira (Boracay Ati-Atihan)', 'region_cluster' => 'visayas', 'province' => 'Aklan', 'city_or_town' => 'Boracay', 'month' => 1, 'date_label' => 'Third weekend of January', 'summary' => 'Boracay\'s adaptation of Ati-Atihan, with parades along White Beach Station 2.'],
            ['slug' => 'tinapay-tibod', 'name' => 'Tinapay Festival', 'region_cluster' => 'visayas', 'province' => 'Bohol', 'city_or_town' => 'Various', 'month' => 6, 'date_label' => 'June', 'summary' => 'Bohol\'s bread-and-baking festivity in select municipalities.'],
            ['slug' => 'salakayan', 'name' => 'Salakayan Festival', 'region_cluster' => 'visayas', 'province' => 'Iloilo', 'city_or_town' => 'Miagao', 'month' => 2, 'date_label' => 'February 8', 'summary' => 'Miagao\'s celebration of the historic Moro raid + the UNESCO Miagao Church anniversary.'],
            ['slug' => 'buyogan', 'name' => 'Buyogan Festival', 'region_cluster' => 'visayas', 'province' => 'Leyte', 'city_or_town' => 'Abuyog', 'month' => 8, 'date_label' => 'August', 'summary' => 'Abuyog\'s bee-themed parade in dyed yellow-and-black costumes for the patronal feast of St. Francis.'],
            ['slug' => 'subayan-keg-subanen', 'name' => 'Subayan Keg Subanen Festival', 'region_cluster' => 'visayas', 'province' => 'Zamboanga del Sur', 'city_or_town' => 'Pagadian City', 'month' => 6, 'date_label' => 'June 21', 'summary' => 'Pagadian\'s celebration of the Subanen tribe\'s cultural heritage.'],
            ['slug' => 'pasaka-festival', 'name' => 'Pasaka Festival', 'region_cluster' => 'visayas', 'province' => 'Leyte', 'city_or_town' => 'Tanauan', 'month' => 6, 'date_label' => 'June 24', 'summary' => 'Tanauan, Leyte\'s celebration of the feast of San Juan Bautista.'],
            ['slug' => 'kasanggayahan', 'name' => 'Kasanggayahan Festival', 'region_cluster' => 'bicol', 'province' => 'Sorsogon', 'city_or_town' => 'Sorsogon City', 'month' => 10, 'date_label' => 'October 17', 'summary' => 'Sorsogon\'s anniversary celebration of bountiful provincial life.'],
            ['slug' => 'kasadyahan-iloilo', 'name' => 'Kasadyahan Festival', 'region_cluster' => 'visayas', 'province' => 'Iloilo', 'city_or_town' => 'Iloilo City', 'month' => 1, 'date_label' => 'Fourth Saturday of January (Dinagyang weekend)', 'summary' => 'Western Visayas regional festival showcase held the Saturday before Dinagyang.'],

            // ─── Mindanao ──────────────────────────────────────────
            ['slug' => 'kadayawan', 'name' => 'Kadayawan Festival', 'region_cluster' => 'mindanao', 'province' => 'Davao del Sur', 'city_or_town' => 'Davao City', 'month' => 8, 'date_label' => 'Third week of August', 'summary' => 'Davao\'s thanksgiving for harvest and indigenous-tribe heritage, with floral float and indak-indak parades.'],
            ['slug' => 'tnalak-festival', 'name' => 'T\'nalak Festival', 'region_cluster' => 'mindanao', 'province' => 'South Cotabato', 'city_or_town' => 'Koronadal City', 'month' => 7, 'date_label' => 'Third week of July', 'summary' => 'South Cotabato\'s celebration of the T\'boli t\'nalak abaca cloth and tribal heritage.'],
            ['slug' => 'higalaay', 'name' => 'Higalaay Festival', 'region_cluster' => 'mindanao', 'province' => 'Misamis Oriental', 'city_or_town' => 'Cagayan de Oro', 'month' => 8, 'date_label' => 'August (around the 28th, feast of St. Augustine)', 'summary' => 'Cagayan de Oro\'s charter day celebration of friendship (Higalaay).'],
            ['slug' => 'fiesta-pilar', 'name' => 'Fiesta Pilar (Zamboanga Hermosa)', 'region_cluster' => 'mindanao', 'province' => 'Zamboanga del Sur', 'city_or_town' => 'Zamboanga City', 'month' => 10, 'date_label' => 'October 12', 'summary' => 'Zamboanga\'s annual celebration of Nuestra Senora del Pilar at Fort Pilar.'],
            ['slug' => 'lanzones-festival', 'name' => 'Lanzones Festival', 'region_cluster' => 'mindanao', 'province' => 'Camiguin', 'city_or_town' => 'Mambajao', 'month' => 10, 'date_label' => 'Third week of October', 'summary' => 'Camiguin\'s harvest festival for its sweet lanzones, with street dancing and lanzones-eating contests.'],
            ['slug' => 'kalilangan-gensan', 'name' => 'Kalilangan Festival', 'region_cluster' => 'mindanao', 'province' => 'South Cotabato', 'city_or_town' => 'General Santos City', 'month' => 2, 'date_label' => 'February 27', 'summary' => 'General Santos\'s founding anniversary recognizing the city\'s settler heritage.'],
            ['slug' => 'tuna-festival', 'name' => 'Tuna Festival', 'region_cluster' => 'mindanao', 'province' => 'South Cotabato', 'city_or_town' => 'General Santos City', 'month' => 9, 'date_label' => 'First week of September', 'summary' => 'GenSan\'s celebration of its tuna fishing industry, with the longest grill and tuna culinary contests.'],
            ['slug' => 'sarangani-bay-festival', 'name' => 'Sarangani Bay Festival', 'region_cluster' => 'mindanao', 'province' => 'Sarangani', 'city_or_town' => 'Glan + Alabel', 'month' => 5, 'date_label' => 'May', 'summary' => 'Sarangani\'s sea-themed festivity along Sarangani Bay\'s beach municipalities.'],
            ['slug' => 'diyandi-festival', 'name' => 'Diyandi Festival', 'region_cluster' => 'mindanao', 'province' => 'Lanao del Norte', 'city_or_town' => 'Iligan City', 'month' => 9, 'date_label' => 'September 24-29', 'summary' => 'Iligan\'s patronal celebration of St. Michael the Archangel at the City of Majestic Waterfalls.'],
            ['slug' => 'kahimunan', 'name' => 'Kahimunan Festival', 'region_cluster' => 'mindanao', 'province' => 'Agusan del Norte', 'city_or_town' => 'Butuan City', 'month' => 1, 'date_label' => 'Third Sunday of January', 'summary' => 'Butuan\'s Sto. Niño festival celebrating Agusanon heritage.'],
            ['slug' => 'bonok-bonok', 'name' => 'Bonok-Bonok Maradjao Karadjao Festival', 'region_cluster' => 'mindanao', 'province' => 'Surigao del Norte', 'city_or_town' => 'Surigao City', 'month' => 9, 'date_label' => 'September 8-10', 'summary' => 'Surigao\'s indigenous Mamanwa-themed festival for the Founding Anniversary.'],
            ['slug' => 'naliyagan', 'name' => 'Naliyagan Festival', 'region_cluster' => 'mindanao', 'province' => 'Agusan del Sur', 'city_or_town' => 'Patin-ay (San Francisco)', 'month' => 6, 'date_label' => 'June 12-17', 'summary' => 'Agusan del Sur\'s harvest + Manobo heritage festival.'],
            ['slug' => 'talakudong-festival', 'name' => 'Talakudong Festival', 'region_cluster' => 'mindanao', 'province' => 'Sultan Kudarat', 'city_or_town' => 'Tacurong City', 'month' => 9, 'date_label' => 'September 18', 'summary' => 'Tacurong City\'s celebration named after the kudong head wrap, the city\'s native headgear.'],
            ['slug' => 'sambuokan', 'name' => 'Sambuokan Festival', 'region_cluster' => 'mindanao', 'province' => 'Davao Oriental', 'city_or_town' => 'Mati City', 'month' => 10, 'date_label' => 'Last week of October', 'summary' => 'Mati City\'s thanksgiving + Davao Oriental coastal heritage festival.'],
            ['slug' => 'hinugyaw', 'name' => 'Hinugyaw Festival', 'region_cluster' => 'mindanao', 'province' => 'South Cotabato', 'city_or_town' => 'Koronadal City', 'month' => 1, 'date_label' => 'January 10', 'summary' => 'Koronadal\'s charter celebration for the new year.'],
            ['slug' => 'salugpongan', 'name' => 'Salugpongan Festival', 'region_cluster' => 'mindanao', 'province' => 'North Cotabato', 'city_or_town' => 'Kidapawan City', 'month' => 8, 'date_label' => 'August 4-18', 'summary' => 'North Cotabato\'s tri-people unity festival held in Kidapawan.'],

            // ─── Palawan + MIMAROPA ──────────────────────────────────────────
            ['slug' => 'baragatan-sa-palawan', 'name' => 'Baragatan sa Palawan', 'region_cluster' => 'palawan', 'province' => 'Palawan', 'city_or_town' => 'Puerto Princesa', 'month' => 6, 'date_label' => 'Whole month of June (Provincial Founding Anniv)', 'summary' => 'Palawan\'s provincial founding anniversary festival, anchored at the Capitol Complex.'],
            ['slug' => 'subayan-palawan', 'name' => 'Subayan Festival (Palawan)', 'region_cluster' => 'palawan', 'province' => 'Palawan', 'city_or_town' => 'Various', 'month' => 5, 'date_label' => 'May', 'summary' => 'Palawan municipalities\' shared heritage celebrations honoring Palaweno traditions.'],
            ['slug' => 'pasalamat-festival', 'name' => 'Pasalamat Festival', 'region_cluster' => 'palawan', 'province' => 'Palawan', 'city_or_town' => 'Puerto Princesa', 'month' => 3, 'date_label' => 'March', 'summary' => 'Puerto Princesa\'s thanksgiving festival.'],

            // ─── Marinduque (special — Moriones) ──────────────────────────────────────────
            ['slug' => 'moriones', 'name' => 'Moriones Festival', 'region_cluster' => 'marinduque', 'province' => 'Marinduque', 'city_or_town' => 'Boac + 6 municipalities', 'month' => null, 'date_label' => 'Holy Week (Monday to Easter Sunday)', 'summary' => 'Marinduque\'s week-long Lenten reenactment of Longinus, with masked morion centurions through the towns.'],
        ];
    }
}
