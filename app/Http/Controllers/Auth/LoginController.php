<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\RgAuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();
            $user->forceFill(['last_login_at' => now()])->save();
            RgAuditLog::record('owner_login');
            return redirect()->intended(route('dashboard.index'));
        }
        return back()->withErrors(['email' => 'Those credentials did not match our records.'])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        RgAuditLog::record('owner_logout');
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}
