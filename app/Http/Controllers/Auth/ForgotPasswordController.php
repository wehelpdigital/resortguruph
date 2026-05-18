<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $status = Password::broker('owners')->sendResetLink($request->only('email'));
        return $status === Password::RESET_LINK_SENT
            ? back()->with(['flash' => 'If that email is registered, a reset link is on its way.'])
            : back()->withErrors(['email' => __($status)]);
    }
}
