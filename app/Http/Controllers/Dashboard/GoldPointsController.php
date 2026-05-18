<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\RgAuditLog;
use App\Models\RgGpLedger;
use App\Models\RgGpTopup;
use App\Models\RgSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GoldPointsController extends Controller
{
    public function index()
    {
        $owner = Auth::user();
        $balance = $owner->gold_points_balance;
        $ledger = RgGpLedger::where('owner_id', $owner->id)->orderByDesc('id')->paginate(25);
        $topups = RgGpTopup::where('owner_id', $owner->id)->orderByDesc('id')->limit(10)->get();
        return view('dashboard.gold-points.index', compact('owner', 'balance', 'ledger', 'topups'));
    }

    public function topupForm()
    {
        $minTopup = (int) RgSetting::get('min_topup_php', 100);
        $rate = (int) RgSetting::get('gp_php_rate', 1);
        $payee = [
            'name' => RgSetting::get('gcash_payee_name', 'Resort Guru PH'),
            'number' => RgSetting::get('gcash_payee_number', '09000000000'),
        ];
        return view('dashboard.gold-points.topup', compact('minTopup', 'rate', 'payee'));
    }

    public function topupSubmit(Request $request)
    {
        $minTopup = (int) RgSetting::get('min_topup_php', 100);
        $rate = (int) RgSetting::get('gp_php_rate', 1);

        $data = $request->validate([
            'php_amount' => "required|integer|min:$minTopup",
            'gcash_ref_number' => 'required|string|max:100',
            'gcash_phone' => 'required|string|max:32',
            'screenshot' => 'required|image|max:5120',
        ]);

        $screenshotPath = $request->file('screenshot')->store('gp-topups', 'public');

        $topup = RgGpTopup::create([
            'owner_id' => Auth::id(),
            'php_amount' => $data['php_amount'],
            'gp_amount' => $data['php_amount'] * $rate,
            'gcash_ref_number' => $data['gcash_ref_number'],
            'gcash_phone' => $data['gcash_phone'],
            'screenshot_path' => $screenshotPath,
            'status' => 'pending',
        ]);

        RgAuditLog::record('topup_submitted', ['target_type' => 'rg_gp_topups', 'target_id' => $topup->id, 'meta' => ['php' => $data['php_amount']]]);

        return redirect()->route('dashboard.gp.index')
            ->with('flash', 'Top-up submitted. Admin will review your screenshot and credit your GP within 1-24 hours.');
    }
}
