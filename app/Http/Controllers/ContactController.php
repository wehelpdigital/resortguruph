<?php

namespace App\Http\Controllers;

use App\Models\RgAuditLog;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function submit(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email|max:191',
            'subject' => 'nullable|string|max:200',
            'message' => 'required|string|max:5000',
        ]);
        RgAuditLog::create([
            'actor_type' => 'system',
            'action' => 'contact_form_submitted',
            'meta_json' => json_encode($data),
            'ip' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
            'created_at' => now(),
        ]);
        return back()->with('success', 'Thanks for reaching out. We will reply within 1-2 business days.');
    }
}
