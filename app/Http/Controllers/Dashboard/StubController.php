<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;

class StubController extends Controller
{
    public function listings()
    {
        return view('dashboard.stub', [
            'title' => 'Listings & Bidding',
            'description' => 'Claim listing slots on high-traffic keyword pages and bid for top placement.',
            'phase' => 'Phase 2',
        ]);
    }

    public function ai()
    {
        return view('dashboard.stub', [
            'title' => 'AI Content Assistant',
            'description' => 'Generate resort descriptions, blog drafts, and FAQs. Costs Gold Points per token used.',
            'phase' => 'Phase 4',
        ]);
    }

    public function notifications()
    {
        return view('dashboard.stub', [
            'title' => 'Notifications',
            'description' => 'Outbid alerts, near-expiry warnings, approval updates, and broadcasts from admin.',
            'phase' => 'Phase 5',
        ]);
    }

    public function tutorials()
    {
        return view('dashboard.stub', [
            'title' => 'Tutorials',
            'description' => 'Video and written guides on getting the most out of Resort Guru.',
            'phase' => 'Phase 5',
        ]);
    }
}
