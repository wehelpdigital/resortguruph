<?php

namespace App\Http\Controllers;

use App\Models\RgPartner;

class PartnerDirectoryController extends Controller
{
    /**
     * Public partner directory. Partners are returned in random order so
     * the default view is a fresh mix each visit; the page then filters
     * client-side by search text, type, and location. Filter option lists
     * (types present + regions present) are derived from the live data.
     */
    public function index()
    {
        $partners = RgPartner::published()->inRandomOrder()->get();

        $typeMeta = RgPartner::typeMeta();
        $typeCounts = $partners->groupBy('type')->map->count();

        // Types actually present, kept in the canonical typeMeta order.
        $types = [];
        foreach ($typeMeta as $key => $meta) {
            if (($typeCounts[$key] ?? 0) > 0) {
                $types[] = [
                    'key' => $key,
                    'label' => $meta['label'],
                    'color' => $meta['color'],
                    'count' => (int) $typeCounts[$key],
                ];
            }
        }

        $regions = $partners->pluck('region')->filter()->unique()->sort()->values();

        $stats = [
            'total' => $partners->count(),
            'verified' => $partners->where('is_verified', true)->count(),
            'towns' => $partners->pluck('city')->filter()->unique()->count(),
            'types' => count($types),
        ];

        return view('partners.directory', compact('partners', 'typeMeta', 'types', 'regions', 'stats'));
    }
}
