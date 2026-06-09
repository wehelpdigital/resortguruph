<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\RgAuditLog;
use App\Models\RgOwner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|unique:rg_owners,email|max:191',
            'phone' => 'nullable|string|max:32',
            'password' => 'required|min:8|confirmed',
        ]);
        $data['status'] = 'active';
        $owner = RgOwner::create($data);
        Auth::login($owner);
        RgAuditLog::record('owner_registered', ['target_type' => 'owner', 'target_id' => $owner->id]);
        return redirect()->route('dashboard.resorts.create')
            ->with('flash', 'Welcome to Tourist Guide Ph. Add your first resort to get listed.');
    }
}
