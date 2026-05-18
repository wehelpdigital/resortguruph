<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\RgGpLedger;
use App\Models\RgGpHold;
use App\Models\RgGpTopup;
use App\Models\RgResort;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $owner = Auth::user();
        $resorts = RgResort::where('owner_id', $owner->id)->get();
        $balance = $owner->gold_points_balance;
        $pendingTopups = RgGpTopup::where('owner_id', $owner->id)->where('status', 'pending')->count();
        $recentLedger = RgGpLedger::where('owner_id', $owner->id)->orderByDesc('id')->limit(5)->get();

        $status = [
            'incomplete_resorts' => $resorts->where('status', 'draft')->count(),
            'pending_review' => $resorts->where('status', 'pending_review')->count(),
            'published' => $resorts->where('status', 'published')->count(),
        ];

        return view('dashboard.index', compact('owner', 'resorts', 'balance', 'pendingTopups', 'recentLedger', 'status'));
    }
}
