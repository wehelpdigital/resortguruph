@extends('layouts.public')

@section('title') Reset password @endsection

@section('content')
<div class="max-w-md mx-auto px-4 py-16">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-8">
        <h1 class="text-2xl font-bold text-slate-900 mb-2">Forgot your password?</h1>
        <p class="text-slate-600 mb-6 text-sm">Enter your email and we will send a reset link.</p>

        @if(session('flash'))<div class="mb-4 p-3 rounded-md bg-emerald-50 text-emerald-700 text-sm">{{ session('flash') }}</div>@endif

        <form action="{{ route('password.email') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded-md border-slate-300" required>
                @error('email')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <button class="w-full py-2.5 rounded-md bg-brand-600 text-white font-semibold hover:bg-brand-700">Send reset link</button>
        </form>

        <p class="mt-6 text-sm text-center text-slate-600">
            <a href="{{ route('login') }}" class="text-brand-600 hover:underline">&larr; back to sign in</a>
        </p>
    </div>
</div>
@endsection
