@extends('layouts.public')

@section('title') Sign in | {{ \App\Models\RgSetting::get('site_name', 'Tourist Guide Ph') }} @endsection

@section('content')
@component('partials.auth-shell', [
    'image' => asset('storage/rg-media/destinations/el-nido-1.jpg'),
    'kicker' => 'Member sign in',
    'heading' => 'Get your place in front of travelers who are already planning a trip.',
    'points' => ['Manage your resort, restaurant or tour listings', 'Track Gold Points and top-ups in one place', 'Leave reviews and comments on travel guides'],
])
    <p class="pk-eyebrow">Welcome back</p>
    <h1 class="font-display text-3xl md:text-4xl font-extrabold text-ink-900 tracking-tight leading-tight">Sign in to your account</h1>
    <p class="mt-2 text-slate-600 text-sm leading-relaxed">Manage your listings, Gold Points and reviews.</p>

    @if(session('flash'))
        <div class="mt-5 p-3 rounded-xl bg-emerald-50 text-emerald-700 text-sm border border-emerald-100">{{ session('flash') }}</div>
    @endif

    <form action="{{ route('login.submit') }}" method="POST" class="mt-7 space-y-4">
        @csrf
        <div>
            <label class="pk-label" for="login-email">Email</label>
            <input id="login-email" type="email" name="email" value="{{ old('email') }}" class="pk-field" autocomplete="email" required autofocus>
            @error('email')<p class="text-rose-600 text-xs mt-1.5">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="pk-label" for="login-password">Password</label>
            <input id="login-password" type="password" name="password" class="pk-field" autocomplete="current-password" required>
        </div>
        <div class="flex items-center justify-between text-sm pt-1">
            <label class="inline-flex items-center text-slate-700">
                <input type="checkbox" name="remember" class="rounded text-brand-600 focus:ring-brand-500 me-2 border-slate-300">
                Remember me
            </label>
            <a href="{{ route('password.request') }}" class="text-brand-700 hover:underline font-semibold">Forgot password?</a>
        </div>
        <button type="submit" class="rg-btn rg-btn--coral rg-btn--lg w-full">Sign in</button>
    </form>

    <p class="mt-7 text-sm text-center text-slate-600">
        New here? <a href="{{ route('register') }}" class="text-brand-700 font-bold hover:underline">Create a free account</a>
    </p>
@endcomponent
@endsection
