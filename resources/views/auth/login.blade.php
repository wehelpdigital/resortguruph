@extends('layouts.public')

@section('title') Sign in — {{ \App\Models\RgSetting::get('site_name', 'Tourist Guide Ph') }} @endsection

@section('content')
<div class="max-w-md mx-auto px-4 py-16">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8">
        <h1 class="text-2xl font-bold text-slate-900 mb-1">Welcome back</h1>
        <p class="text-slate-600 mb-6 text-sm">Sign in to manage your resorts and listings.</p>

        @if(session('flash'))<div class="mb-4 p-3 rounded-md bg-emerald-50 text-emerald-700 text-sm">{{ session('flash') }}</div>@endif

        <form action="{{ route('login.submit') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded-md border-slate-300" required autofocus>
                @error('email')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Password</label>
                <input type="password" name="password" class="w-full rounded-md border-slate-300" required>
            </div>
            <div class="flex items-center justify-between text-sm">
                <label class="inline-flex items-center"><input type="checkbox" name="remember" class="rounded text-brand-600 me-2"> Remember me</label>
                <a href="{{ route('password.request') }}" class="text-brand-600 hover:underline">Forgot?</a>
            </div>
            <button class="w-full py-2.5 rounded-md bg-brand-600 text-white font-semibold hover:bg-brand-700">Sign in</button>
        </form>

        <p class="mt-6 text-sm text-center text-slate-600">
            New here? <a href="{{ route('register') }}" class="text-brand-600 font-semibold hover:underline">Create an account</a>
        </p>
    </div>
</div>
@endsection
