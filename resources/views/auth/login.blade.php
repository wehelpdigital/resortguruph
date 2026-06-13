@extends('layouts.public')

@section('title') Sign in — {{ \App\Models\RgSetting::get('site_name', 'Tourist Guide Ph') }} @endsection

@section('content')
<section class="bg-gradient-to-b from-slate-50 to-white" style="margin-left:calc(50% - 50vw);margin-right:calc(50% - 50vw);width:100vw;max-width:100vw">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-16">
        <div class="grid lg:grid-cols-2 gap-10 lg:gap-16 items-center">
            {{-- Form column --}}
            <div class="order-2 lg:order-1">
                <div class="max-w-md mx-auto bg-white rounded-2xl shadow-sm border border-slate-200 p-7 md:p-10">
                    <div class="mb-7">
                        <p class="text-xs uppercase tracking-[0.18em] text-blue-600 font-bold mb-2">Member sign in</p>
                        <h1 class="text-3xl md:text-4xl font-extrabold text-slate-900 tracking-[-0.015em] leading-tight mb-2">Welcome back</h1>
                        <p class="text-slate-600 text-sm leading-relaxed">Sign in to manage your resorts, listings, and Gold Points.</p>
                    </div>

                    @if(session('flash'))
                        <div class="mb-5 p-3 rounded-lg bg-emerald-50 text-emerald-700 text-sm border border-emerald-100">{{ session('flash') }}</div>
                    @endif

                    <form action="{{ route('login.submit') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-colors" required autofocus>
                            @error('email')<p class="text-rose-600 text-xs mt-1.5">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5">Password</label>
                            <input type="password" name="password" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition-colors" required>
                        </div>
                        <div class="flex items-center justify-between text-sm pt-1">
                            <label class="inline-flex items-center text-slate-700">
                                <input type="checkbox" name="remember" class="rounded text-blue-600 focus:ring-blue-500 me-2 border-slate-300">
                                Remember me
                            </label>
                            <a href="{{ route('password.request') }}" class="text-blue-600 hover:text-blue-700 hover:underline font-medium">Forgot password?</a>
                        </div>
                        <button type="submit" class="w-full py-3 rounded-lg bg-blue-600 text-white font-bold hover:bg-blue-700 transition-colors mt-2 shadow-sm hover:shadow">
                            Sign in
                        </button>
                    </form>

                    <p class="mt-7 text-sm text-center text-slate-600">
                        New here? <a href="{{ route('register') }}" class="text-blue-600 font-semibold hover:text-blue-700 hover:underline">Create an account</a>
                    </p>
                </div>
            </div>

            {{-- Value-prop panel (right on lg+, hidden on small to keep mobile focused on the form) --}}
            <div class="order-1 lg:order-2 hidden lg:block">
                <div class="rounded-2xl bg-gradient-to-br from-blue-600 via-blue-500 to-emerald-500 text-white p-10 shadow-xl relative overflow-hidden">
                    {{-- Decorative circles --}}
                    <div class="absolute -top-12 -right-12 w-48 h-48 rounded-full bg-white/10"></div>
                    <div class="absolute -bottom-16 -left-10 w-56 h-56 rounded-full bg-white/5"></div>

                    <div class="relative">
                        <p class="text-xs uppercase tracking-[0.2em] font-bold opacity-80 mb-3">Tourist Guide Ph</p>
                        <h2 class="text-3xl font-extrabold leading-[1.1] tracking-[-0.015em] mb-4 max-w-sm">Get your resort in front of travelers who are already searching.</h2>
                        <p class="text-white/90 leading-relaxed mb-8 max-w-md">Manage your listings, track Gold Points, and connect with guests planning trips across the 7,641 islands.</p>

                        <div class="grid grid-cols-3 gap-4 mb-8">
                            <div>
                                <div class="text-3xl font-extrabold">1,145+</div>
                                <div class="text-xs opacity-80 leading-tight mt-1">searchable destinations</div>
                            </div>
                            <div>
                                <div class="text-3xl font-extrabold">300+</div>
                                <div class="text-xs opacity-80 leading-tight mt-1">verified properties</div>
                            </div>
                            <div>
                                <div class="text-3xl font-extrabold">7,641</div>
                                <div class="text-xs opacity-80 leading-tight mt-1">islands covered</div>
                            </div>
                        </div>

                        {{-- Mini testimonial --}}
                        <div class="border-t border-white/20 pt-6">
                            <p class="text-sm italic leading-relaxed mb-3">"Our bookings doubled after getting featured on the destination page locals already search. The team is responsive and the directory just works."</p>
                            <div class="text-xs uppercase tracking-wider opacity-80">— Resort partner, Tagaytay</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
