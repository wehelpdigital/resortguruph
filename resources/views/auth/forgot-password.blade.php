@extends('layouts.public')

@section('title') Reset password | {{ \App\Models\RgSetting::get('site_name', 'Tourist Guide Ph') }} @endsection

@section('content')
@component('partials.auth-shell', [
    'image' => asset('storage/rg-media/destinations/boracay-2.jpg'),
    'kicker' => 'Account recovery',
    'heading' => 'Locked out? It happens. You will be back in a minute.',
    'points' => ['We send a secure reset link to your email', 'Links expire after a short time for your safety', 'Check your spam folder if it does not arrive'],
])
    <p class="pk-eyebrow">Account recovery</p>
    <h1 class="font-display text-3xl md:text-4xl font-extrabold text-ink-900 tracking-tight leading-tight">Forgot your password?</h1>
    <p class="mt-2 text-slate-600 text-sm leading-relaxed">Enter your email and we will send a reset link. Check your spam folder if you do not see it within a few minutes.</p>

    @if(session('flash'))
        <div class="mt-5 p-3 rounded-xl bg-emerald-50 text-emerald-700 text-sm border border-emerald-100">{{ session('flash') }}</div>
    @endif

    <form action="{{ route('password.email') }}" method="POST" class="mt-7 space-y-4">
        @csrf
        <div>
            <label class="pk-label" for="fp-email">Email</label>
            <input id="fp-email" type="email" name="email" value="{{ old('email') }}" class="pk-field" autocomplete="email" required autofocus>
            @error('email')<p class="text-rose-600 text-xs mt-1.5">{{ $message }}</p>@enderror
        </div>
        <button type="submit" class="rg-btn rg-btn--coral rg-btn--lg w-full">Send reset link</button>
    </form>

    <p class="mt-7 text-sm text-center">
        <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 text-brand-700 hover:underline font-semibold">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Back to sign in
        </a>
    </p>
@endcomponent
@endsection
