<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\RgAuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('dashboard.profile', ['owner' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $owner = Auth::user();
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:191|unique:rg_owners,email,' . $owner->id,
            'phone' => 'nullable|string|max:32',
            'password' => 'nullable|min:8|confirmed',
        ]);
        if (!empty($data['password'])) {
            $owner->password = Hash::make($data['password']);
        }
        unset($data['password']);
        $owner->fill($data)->save();
        RgAuditLog::record('owner_profile_updated');
        return back()->with('flash', 'Profile updated.');
    }
}
