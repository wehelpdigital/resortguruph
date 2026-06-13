@extends('layouts.public')

@section('title') Create account — {{ \App\Models\RgSetting::get('site_name', 'Tourist Guide Ph') }} @endsection

@section('content')
<section class="bg-gradient-to-b from-slate-50 to-white" style="margin-left:calc(50% - 50vw);margin-right:calc(50% - 50vw);width:100vw;max-width:100vw">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-16">
        <div class="grid lg:grid-cols-2 gap-10 lg:gap-16 items-center">
            {{-- Form column --}}
            <div class="order-2 lg:order-1">
                <div class="max-w-md mx-auto bg-white rounded-2xl shadow-sm border border-slate-200 p-7 md:p-10">
                    <div class="mb-7">
                        <p class="text-xs uppercase tracking-[0.18em] text-emerald-600 font-bold mb-2">Get listed</p>
                        <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-[-0.015em] leading-tight mb-2">Create your account</h1>
                        <p class="text-slate-600 text-sm leading-relaxed">Get your resort listed in front of travelers actively searching the Philippines.</p>
                    </div>

                    <form action="{{ route('register.submit') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Full name</label>
                            <input type="text" name="name" value="{{ old('name') }}" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 transition-colors" required autofocus>
                            @error('name')<p class="text-rose-600 text-xs mt-1.5">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 transition-colors" required>
                            @error('email')<p class="text-rose-600 text-xs mt-1.5">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Phone <span class="font-normal text-slate-400">(optional)</span></label>
                            <input type="text" name="phone" value="{{ old('phone') }}" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 transition-colors">
                        </div>
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Password</label>
                                <input type="password" name="password" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 transition-colors" required minlength="8">
                                @error('password')<p class="text-rose-600 text-xs mt-1.5">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1.5">Confirm</label>
                                <input type="password" name="password_confirmation" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-100 transition-colors" required>
                            </div>
                        </div>
                        <button type="submit" class="w-full py-3 rounded-lg bg-emerald-600 text-white font-bold hover:bg-emerald-700 transition-colors mt-2 shadow-sm hover:shadow">
                            Create account
                        </button>
                    </form>

                    <p class="mt-7 text-sm text-center text-slate-600">
                        Already have an account? <a href="{{ route('login') }}" class="text-emerald-600 font-semibold hover:text-emerald-700 hover:underline">Sign in</a>
                    </p>
                    <p class="mt-3 text-xs text-center text-slate-400 leading-relaxed">
                        By signing up, you agree to our <a href="{{ route('terms') }}" class="underline hover:text-slate-600">terms</a> and <a href="{{ route('privacy') }}" class="underline hover:text-slate-600">privacy policy</a>.
                    </p>
                </div>
            </div>

            {{-- Value-prop panel --}}
            <div class="order-1 lg:order-2 hidden lg:block">
                <div class="rounded-2xl bg-gradient-to-br from-emerald-600 via-emerald-500 to-blue-500 text-white p-10 shadow-xl relative overflow-hidden">
                    <div class="absolute -top-12 -right-12 w-48 h-48 rounded-full bg-white/10"></div>
                    <div class="absolute -bottom-16 -left-10 w-56 h-56 rounded-full bg-white/5"></div>

                    <div class="relative">
                        <p class="text-xs uppercase tracking-[0.2em] font-bold opacity-80 mb-3">Why list with us</p>
                        <h2 class="text-3xl font-extrabold leading-[1.1] tracking-[-0.015em] mb-4 max-w-sm">Get featured on the pages your guests are already searching.</h2>
                        <p class="text-white/90 leading-relaxed mb-8 max-w-md">Tourist Guide Ph is the Philippines directory travelers reach for when they want honest picks, not photo-only joints.</p>

                        {{-- Feature checklist --}}
                        <ul class="space-y-3.5 mb-8 max-w-md">
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 mt-0.5 text-white/95 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                <div>
                                    <div class="font-semibold">Free to start</div>
                                    <div class="text-white/80 text-sm">No setup fee. Pay only when you boost your listing's rank.</div>
                                </div>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 mt-0.5 text-white/95 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                <div>
                                    <div class="font-semibold">Sourced to the province</div>
                                    <div class="text-white/80 text-sm">Your listing appears on the regional pages locals actually search.</div>
                                </div>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 mt-0.5 text-white/95 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
                                <div>
                                    <div class="font-semibold">Real audience, not bots</div>
                                    <div class="text-white/80 text-sm">Bot-filtered traffic counters. The numbers are real.</div>
                                </div>
                            </li>
                        </ul>

                        <div class="border-t border-white/20 pt-6 text-sm text-white/85 leading-relaxed">
                            Onboarding takes about 5 minutes. Your listing goes live as soon as our team reviews it (usually same day).
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
