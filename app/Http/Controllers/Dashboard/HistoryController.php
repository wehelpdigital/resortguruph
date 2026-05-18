<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\RgAuditLog;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller
{
    public function index()
    {
        $logs = RgAuditLog::where('actor_type', 'owner')
            ->where('actor_id', Auth::id())
            ->orderByDesc('id')
            ->paginate(40);
        return view('dashboard.history', compact('logs'));
    }
}
