<?php

namespace App\Http\Controllers;

use App\Models\RgResort;
use App\Services\SchemaGenerator;

class ResortPageController extends Controller
{
    public function show(RgResort $resort, SchemaGenerator $schema)
    {
        if ($resort->status !== 'published') abort(404);
        $resort->load('media');

        $jsonld = $schema->emit($schema->lodgingBusiness($resort))
            . $schema->emit($schema->breadcrumb([
                ['name' => 'Home', 'url' => url('/')],
                ['name' => 'Properties', 'url' => route('destinations.index')],
                ['name' => $resort->name, 'url' => url('/listing/' . $resort->slug)],
            ]));

        $response = response()->view('resort-page', compact('resort', 'jsonld'));
        // the seeded sample properties stay reachable for their owners but out of search engines
        if ($resort->isDemo()) $response->header('X-Robots-Tag', 'noindex, nofollow');
        return $response;
    }
}
