<?php

namespace App\Support;

use App\Http\Controllers\DestinationsController;

/**
 * Single source of truth for grouping keyword pages into region clusters.
 *
 * Grouping used to depend entirely on the free-text `rg_keywords.cluster_tag`
 * column being set correctly by hand in the admin. This resolver makes it
 * robust: a valid, explicitly-set cluster always wins, but when the tag is
 * blank or unrecognised the region is derived from the place named in the
 * phrase (e.g. "beach resort in La Union" -> north-luzon). New pages therefore
 * group correctly even if the admin never touches the cluster field.
 */
class RegionResolver
{
    /** Memoised place-token map, sorted longest-key-first. */
    private static ?array $sortedMap = null;

    /**
     * Canonical cluster keys => display label. Built from
     * DestinationsController::clusterMetadata() so the labels stay in sync with
     * the destination cluster pages, plus an appended catch-all "other".
     */
    public static function clusters(): array
    {
        $out = [];
        foreach (DestinationsController::clusterMetadata() as $key => $meta) {
            $out[$key] = $meta['name'] ?? ucwords(str_replace('-', ' ', $key));
        }
        $out['other'] = 'Other';

        return $out;
    }

    public static function label(string $key): string
    {
        return self::clusters()[$key] ?? ucwords(str_replace('-', ' ', $key));
    }

    /**
     * Province / town / island token => cluster key. Longer, more specific
     * tokens are matched before shorter ones (see resolve()), so "la union"
     * wins over a bare "san juan" and "el nido" over "nido".
     */
    public static function placeMap(): array
    {
        return [
            // North Luzon: Ilocos, Cordillera, Cagayan Valley + the northern
            // Central Luzon belt (Pangasinan, Zambales, Bataan, Tarlac, Aurora).
            'hundred islands' => 'north-luzon', 'san juan la union' => 'north-luzon', 'la union' => 'north-luzon',
            'nueva ecija' => 'north-luzon', 'nueva vizcaya' => 'north-luzon', 'pangasinan' => 'north-luzon',
            'bolinao' => 'north-luzon', 'alaminos' => 'north-luzon', 'dagupan' => 'north-luzon', 'lingayen' => 'north-luzon',
            'ilocos' => 'north-luzon', 'vigan' => 'north-luzon', 'laoag' => 'north-luzon', 'pagudpud' => 'north-luzon',
            'zambales' => 'north-luzon', 'subic' => 'north-luzon', 'anawangin' => 'north-luzon', 'pundaquit' => 'north-luzon',
            'bataan' => 'north-luzon', 'morong bataan' => 'north-luzon', 'anvaya' => 'north-luzon', 'mariveles' => 'north-luzon',
            'tarlac' => 'north-luzon', 'capas' => 'north-luzon', 'pinatubo' => 'north-luzon',
            'aurora' => 'north-luzon', 'baler' => 'north-luzon', 'dingalan' => 'north-luzon',
            'baguio' => 'north-luzon', 'benguet' => 'north-luzon', 'sagada' => 'north-luzon', 'banaue' => 'north-luzon',
            'ifugao' => 'north-luzon', 'cagayan valley' => 'north-luzon', 'santa ana cagayan' => 'north-luzon',

            // Bulacan
            'bulacan' => 'bulacan', 'norzagaray' => 'bulacan', 'pandi' => 'bulacan', 'angat' => 'bulacan',
            'san jose del monte' => 'bulacan', 'dona remedios' => 'bulacan',

            // Pampanga
            'pampanga' => 'pampanga', 'angeles' => 'pampanga', 'clark' => 'pampanga', 'arayat' => 'pampanga',
            'porac' => 'pampanga', 'magalang' => 'pampanga', 'san fernando pampanga' => 'pampanga',

            // Cavite
            'cavite' => 'cavite', 'tagaytay' => 'cavite', 'alfonso' => 'cavite', 'silang' => 'cavite',
            'naic' => 'cavite', 'ternate' => 'cavite', 'amadeo' => 'cavite', 'indang' => 'cavite', 'maragondon' => 'cavite',

            // Batangas
            'batangas' => 'batangas', 'laiya' => 'batangas', 'nasugbu' => 'batangas', 'calatagan' => 'batangas',
            'anilao' => 'batangas', 'lipa' => 'batangas', 'lobo' => 'batangas', 'san juan batangas' => 'batangas',
            'matabungkay' => 'batangas', 'lian' => 'batangas', 'mabini batangas' => 'batangas',

            // Laguna
            'laguna' => 'laguna', 'pansol' => 'laguna', 'calamba' => 'laguna', 'pagsanjan' => 'laguna',
            'san pablo' => 'laguna', 'nagcarlan' => 'laguna', 'liliw' => 'laguna', 'los banos' => 'laguna',
            'majayjay' => 'laguna', 'cavinti' => 'laguna',

            // Rizal
            'rizal' => 'rizal', 'antipolo' => 'rizal', 'tanay' => 'rizal', 'binangonan' => 'rizal',
            'taytay rizal' => 'rizal', 'rodriguez' => 'rizal', 'san mateo rizal' => 'rizal', 'baras' => 'rizal',

            // Quezon
            'quezon' => 'quezon', 'lucena' => 'quezon', 'sariaya' => 'quezon', 'lucban' => 'quezon',
            'pagbilao' => 'quezon', 'atimonan' => 'quezon', 'tayabas' => 'quezon', 'padre burgos' => 'quezon',

            // Metro Manila
            'metro manila' => 'metro-manila', 'manila' => 'metro-manila', 'makati' => 'metro-manila',
            'bgc' => 'metro-manila', 'taguig' => 'metro-manila', 'quezon city' => 'metro-manila',
            'pasay' => 'metro-manila', 'ortigas' => 'metro-manila', 'pasig' => 'metro-manila', 'paranaque' => 'metro-manila',

            // Bicol
            'bicol' => 'bicol', 'albay' => 'bicol', 'legazpi' => 'bicol', 'mayon' => 'bicol', 'naga' => 'bicol',
            'camarines' => 'bicol', 'sorsogon' => 'bicol', 'donsol' => 'bicol', 'caramoan' => 'bicol',
            'catanduanes' => 'bicol', 'masbate' => 'bicol', 'daet' => 'bicol',

            // Visayas
            'cebu' => 'visayas', 'mactan' => 'visayas', 'bohol' => 'visayas', 'panglao' => 'visayas',
            'negros' => 'visayas', 'iloilo' => 'visayas', 'guimaras' => 'visayas', 'siquijor' => 'visayas',
            'boracay' => 'visayas', 'aklan' => 'visayas', 'bantayan' => 'visayas', 'moalboal' => 'visayas',
            'dumaguete' => 'visayas', 'bacolod' => 'visayas', 'oslob' => 'visayas', 'malapascua' => 'visayas',
            'kalibo' => 'visayas', 'antique' => 'visayas', 'capiz' => 'visayas', 'tagbilaran' => 'visayas',
            'samar' => 'visayas', 'leyte' => 'visayas', 'biliran' => 'visayas',

            // Mindanao
            'davao' => 'mindanao', 'samal' => 'mindanao', 'general santos' => 'mindanao', 'gensan' => 'mindanao',
            'glan' => 'mindanao', 'zamboanga' => 'mindanao', 'cagayan de oro' => 'mindanao', 'siargao' => 'mindanao',
            'surigao' => 'mindanao', 'camiguin' => 'mindanao', 'dahican' => 'mindanao', 'mati' => 'mindanao',
            'bukidnon' => 'mindanao', 'dipolog' => 'mindanao', 'cotabato' => 'mindanao', 'lake sebu' => 'mindanao',

            // Palawan & Mindoro
            'palawan' => 'palawan', 'el nido' => 'palawan', 'coron' => 'palawan', 'puerto princesa' => 'palawan',
            'puerto galera' => 'palawan', 'mindoro' => 'palawan', 'san vicente palawan' => 'palawan',
            'port barton' => 'palawan', 'balabac' => 'palawan', 'calamian' => 'palawan',
        ];
    }

    /**
     * Resolve a keyword to its canonical cluster key.
     *
     * 1. A non-empty cluster_tag that matches a known cluster is trusted.
     * 2. Otherwise the place named in the phrase is matched (longest token
     *    first, on word boundaries).
     * 3. Falling through everything yields "other".
     */
    public static function resolve(?string $clusterTag, string $phrase): string
    {
        $tag = strtolower(trim((string) $clusterTag));
        if ($tag !== '' && isset(self::clusters()[$tag])) {
            return $tag;
        }

        if (self::$sortedMap === null) {
            $map = self::placeMap();
            uksort($map, fn ($a, $b) => strlen($b) <=> strlen($a));
            self::$sortedMap = $map;
        }

        $hay = ' ' . preg_replace('/\s+/', ' ', strtolower($phrase)) . ' ';
        foreach (self::$sortedMap as $needle => $cluster) {
            if (str_contains($hay, ' ' . $needle . ' ')) {
                return $cluster;
            }
        }

        return 'other';
    }
}
