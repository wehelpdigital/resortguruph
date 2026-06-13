@extends('layouts.public')

@section('title') Reset password — {{ \App\Models\RgSetting::get('site_name', 'Tourist Guide Ph') }} @endsection

@section('content')
<section class="bg-gradient-to-b from-slate-50 to-white" style="margin-left:calc(50% - 50vw);margin-right:calc(50% - 50vw);width:100vw;max-width:100vw">
    <div class="max-w-md mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-20">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-7 md:p-10">
            <div class="mb-7">
                <p class="text-xs uppercase tracking-[0.18em] text-blue-600 font-bold mb-2">Account recovery</p>
                <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-[-0.015em] leading-tight mb-2">Forgot your password?</h1>
                <p class="text-slate-600 text-sm leading-relaxed">Enter your email and we will send a reset link. Check your spam folder if you do not see it within a few minutes.</p>
            </div>

            @if(session('flash'))
                <div class="mb-5 p-3 rounded-lg bg-emerald-50 text-emerald-700 text-sm border border-emerald-100">{{ session('flash') }}</div>
            @endif

            <form action="{{ route('password.email') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-colors" required autofocus>
                    @error('email')<p class="text-rose-600 text-xs mt-1.5">{{ $message }}</p>@enderror
                </div>
                <button type="submit" class="w-full py-3 rounded-lg bg-blue-600 text-white font-bold hover:bg-blue-700 transition-colors mt-2 shadow-sm hover:shadow">
                    Send reset link
                </button>
            </form>

            <p class="mt-7 text-sm text-center text-slate-600">
                <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-blue-600 hover:text-blue-700 hover:underline font-medium">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back to sign in
                </a>
            </p>
        </div>
    </div>
</section>
@endsection
