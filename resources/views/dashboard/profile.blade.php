@extends('layouts.dashboard')

@section('heading') Profile @endsection

@section('content')
<div class="mb-6">
    <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-[-0.015em]">Your profile</h1>
    <p class="text-slate-600 mt-1 text-sm">Update your contact details and password. Email is used for sign in and account recovery.</p>
</div>

<div class="grid lg:grid-cols-3 gap-6">
    {{-- Profile form (2-cols width) --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100">
                <h2 class="font-bold text-slate-900">Account details</h2>
            </div>

            @if(session('flash'))
                <div class="mx-6 mt-5 p-3 rounded-lg bg-emerald-50 text-emerald-700 text-sm border border-emerald-100">{{ session('flash') }}</div>
            @endif

            <form action="{{ route('dashboard.profile.update') }}" method="POST" class="p-6 space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Full name</label>
                    <input type="text" name="name" value="{{ old('name', $owner->name) }}" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-colors" required>
                    @error('name')<p class="text-rose-600 text-xs mt-1.5">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Email</label>
                    <input type="email" name="email" value="{{ old('email', $owner->email) }}" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-colors" required>
                    @error('email')<p class="text-rose-600 text-xs mt-1.5">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5">Phone <span class="font-normal text-slate-400">(optional)</span></label>
                    <input type="text" name="phone" value="{{ old('phone', $owner->phone) }}" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-colors">
                </div>

                {{-- Password section (visually divided) --}}
                <div class="pt-5 mt-5 border-t border-slate-200">
                    <h3 class="text-sm font-bold text-slate-900 mb-1">Change password</h3>
                    <p class="text-xs text-slate-500 mb-4">Leave blank if you do not want to change your password.</p>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">New password</label>
                            <input type="password" name="password" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-colors" minlength="8" placeholder="At least 8 characters">
                            @error('password')<p class="text-rose-600 text-xs mt-1.5">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Confirm new password</label>
                            <input type="password" name="password_confirmation" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-colors">
                        </div>
                    </div>
                </div>

                <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-blue-600 text-white font-bold hover:bg-blue-700 transition-colors shadow-sm hover:shadow">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                    Save changes
                </button>
            </form>
        </div>
    </div>

    {{-- Sidebar: account meta + security tips --}}
    <aside class="space-y-5">
        {{-- Account summary card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-emerald-500 text-white flex items-center justify-center font-bold text-xl flex-shrink-0">
                    {{ strtoupper(mb_substr($owner->name ?? '?', 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <p class="font-bold text-slate-900 truncate">{{ $owner->name }}</p>
                    <p class="text-xs text-slate-500 truncate">{{ $owner->email }}</p>
                </div>
            </div>
            <dl class="text-sm space-y-2.5 pt-4 border-t border-slate-100">
                <div class="flex items-center justify-between">
                    <dt class="text-slate-500">Member since</dt>
                    <dd class="font-semibold text-slate-900">{{ \Carbon\Carbon::parse($owner->created_at)->format('M Y') }}</dd>
                </div>
                @if($owner->last_login_at ?? null)
                    <div class="flex items-center justify-between">
                        <dt class="text-slate-500">Last sign-in</dt>
                        <dd class="font-semibold text-slate-900">{{ \Carbon\Carbon::parse($owner->last_login_at)->diffForHumans() }}</dd>
                    </div>
                @endif
            </dl>
        </div>

        {{-- Security tip --}}
        <div class="bg-amber-50 rounded-2xl border border-amber-200 p-5">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center flex-shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.746 3.746 0 0 1 3.296-1.043A3.746 3.746 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 0 1 3.296 1.043 3.746 3.746 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z"/></svg>
                </div>
                <div class="text-sm">
                    <p class="font-bold text-amber-900 mb-1">Keep your account safe</p>
                    <p class="text-amber-800 leading-relaxed text-xs">Use a unique password with at least 8 characters. We never email you asking for your password.</p>
                </div>
            </div>
        </div>
    </aside>
</div>
@endsection
