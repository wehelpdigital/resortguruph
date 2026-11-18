@extends('layouts.public')

@section('title') Create account | {{ \App\Models\RgSetting::get('site_name', 'Tourist Guide Ph') }} @endsection

@section('content')
@component('partials.auth-shell', [
    'image' => asset('storage/rg-media/spots/el-nido-small-lagoon-tour-a.jpg'),
    'kicker' => 'Why list with us',
    'heading' => 'Get featured on the pages your guests are already reading.',
    'points' => ['Free to start, no setup fee. Pay only when you boost your rank.', 'Your listing shows on the town and province guides travelers search.', 'Bot-filtered traffic counters, so the numbers are real.'],
])
    <p class="pk-eyebrow">Get listed</p>
    <h1 class="font-display text-3xl md:text-4xl font-extrabold text-ink-900 tracking-tight leading-tight">Create your free account</h1>
    <p class="mt-2 text-slate-600 text-sm leading-relaxed">It takes about five minutes. Your listing goes live once our team has checked it.</p>

    <form action="{{ route('register.submit') }}" method="POST" class="mt-7 space-y-4">
        @csrf
        <div>
            <label class="pk-label" for="reg-name">Full name</label>
            <input id="reg-name" type="text" name="name" value="{{ old('name') }}" class="pk-field" autocomplete="name" required autofocus>
            @error('name')<p class="text-rose-600 text-xs mt-1.5">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="pk-label" for="reg-email">Email</label>
            <input id="reg-email" type="email" name="email" value="{{ old('email') }}" class="pk-field" autocomplete="email" required>
            @error('email')<p class="text-rose-600 text-xs mt-1.5">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="pk-label" for="reg-phone">Phone <span class="font-normal text-slate-400">(optional)</span></label>
            <input id="reg-phone" type="text" name="phone" value="{{ old('phone') }}" class="pk-field" autocomplete="tel">
        </div>
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="pk-label" for="reg-password">Password</label>
                <input id="reg-password" type="password" name="password" class="pk-field" autocomplete="new-password" required minlength="8">
                @error('password')<p class="text-rose-600 text-xs mt-1.5">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="pk-label" for="reg-password2">Confirm</label>
                <input id="reg-password2" type="password" name="password_confirmation" class="pk-field" autocomplete="new-password" required>
            </div>
        </div>
        <button type="submit" class="rg-btn rg-btn--coral rg-btn--lg w-full">Create account</button>
    </form>

    <p class="mt-7 text-sm text-center text-slate-600">
        Already have an account? <a href="{{ route('login') }}" class="text-brand-700 font-bold hover:underline">Sign in</a>
    </p>
    <p class="mt-3 text-xs text-center text-slate-400 leading-relaxed">
        By signing up, you agree to our <a href="{{ route('terms') }}" class="underline hover:text-slate-600">terms</a> and <a href="{{ route('privacy') }}" class="underline hover:text-slate-600">privacy policy</a>.
    </p>
@endcomponent
@endsection
