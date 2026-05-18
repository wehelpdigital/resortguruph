@extends('layouts.dashboard')

@section('heading') Profile @endsection

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <form action="{{ route('dashboard.profile.update') }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-semibold mb-1">Full name</label>
                <input type="text" name="name" value="{{ old('name', $owner->name) }}" class="w-full rounded-md border-slate-300" required>
                @error('name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $owner->email) }}" class="w-full rounded-md border-slate-300" required>
                @error('email')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $owner->phone) }}" class="w-full rounded-md border-slate-300">
            </div>
            <hr class="border-slate-200">
            <p class="text-sm text-slate-500">Leave blank if you don't want to change your password.</p>
            <div>
                <label class="block text-sm font-semibold mb-1">New password</label>
                <input type="password" name="password" class="w-full rounded-md border-slate-300" minlength="8">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Confirm new password</label>
                <input type="password" name="password_confirmation" class="w-full rounded-md border-slate-300">
            </div>
            <button class="px-5 py-2.5 rounded-md bg-brand-600 text-white font-semibold hover:bg-brand-700">Save changes</button>
        </form>
    </div>
</div>
@endsection
