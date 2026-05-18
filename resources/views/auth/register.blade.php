@extends('layouts.public')

@section('title') Create account — {{ \App\Models\RgSetting::get('site_name', 'Resort Guru PH') }} @endsection

@section('content')
<div class="max-w-md mx-auto px-4 py-16">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8">
        <h1 class="text-2xl font-bold text-slate-900 mb-1">Create your account</h1>
        <p class="text-slate-600 mb-6 text-sm">Get your resort listed in front of thousands of monthly visitors.</p>

        <form action="{{ route('register.submit') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold mb-1">Full name</label>
                <input type="text" name="name" value="{{ old('name') }}" class="w-full rounded-md border-slate-300" required autofocus>
                @error('name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded-md border-slate-300" required>
                @error('email')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Phone (optional)</label>
                <input type="text" name="phone" value="{{ old('phone') }}" class="w-full rounded-md border-slate-300">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Password</label>
                <input type="password" name="password" class="w-full rounded-md border-slate-300" required minlength="8">
                @error('password')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Confirm password</label>
                <input type="password" name="password_confirmation" class="w-full rounded-md border-slate-300" required>
            </div>
            <button class="w-full py-2.5 rounded-md bg-brand-600 text-white font-semibold hover:bg-brand-700">Create account</button>
        </form>

        <p class="mt-6 text-sm text-center text-slate-600">
            Already have an account? <a href="{{ route('login') }}" class="text-brand-600 font-semibold hover:underline">Sign in</a>
        </p>
        <p class="mt-2 text-xs text-center text-slate-400">
            By signing up, you agree to our <a href="{{ route('terms') }}" class="underline">terms</a> and <a href="{{ route('privacy') }}" class="underline">privacy policy</a>.
        </p>
    </div>
</div>
@endsection
