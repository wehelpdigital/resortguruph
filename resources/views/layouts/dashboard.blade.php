<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') · {{ \App\Models\RgSetting::get('site_name', 'Tourist Guide Ph') }}</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ctext y='.9em' font-size='90'%3E%F0%9F%8F%96%EF%B8%8F%3C/text%3E%3C/svg%3E">
    <script src="https://cdn.tailwindcss.com?plugins=typography,forms"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { brand: { 50: '#eef4ff', 100: '#dbeafe', 500: '#3b82f6', 600: '#2563eb', 700: '#1d4ed8' } }, fontFamily: { sans: ['Inter', 'system-ui'] } } } }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @stack('head')
</head>
<body class="bg-slate-100 text-slate-800 antialiased">

<div class="min-h-screen flex">
    <aside class="w-64 bg-slate-900 text-slate-300 hidden md:flex flex-col fixed inset-y-0">
        <div class="p-5 border-b border-slate-800">
            <a href="{{ route('home') }}" class="text-white font-bold flex items-center gap-2">🏖️ {{ \App\Models\RgSetting::get('site_name', 'Tourist Guide Ph') }}</a>
        </div>
        <nav class="flex-1 p-3 space-y-1 text-sm">
            @php
                $owner = auth()->user();
                $bal = $owner ? $owner->gold_points_balance : 0;
            @endphp
            <a href="{{ route('dashboard.index') }}" class="block px-3 py-2 rounded {{ request()->routeIs('dashboard.index') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800' }}">🏠 Overview</a>
            <a href="{{ route('dashboard.resorts.index') }}" class="block px-3 py-2 rounded {{ request()->routeIs('dashboard.resorts.*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800' }}">🏨 My Resorts</a>
            <a href="{{ route('dashboard.restaurants') }}" class="block px-3 py-2 rounded {{ request()->routeIs('dashboard.restaurants*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800' }}">🍴 My Restaurants <span class="text-xs opacity-60 ml-1">soon</span></a>
            <a href="{{ route('dashboard.adventures') }}" class="block px-3 py-2 rounded {{ request()->routeIs('dashboard.adventures*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800' }}">🪂 My Adventures <span class="text-xs opacity-60 ml-1">soon</span></a>
            <a href="{{ route('dashboard.listings') }}" class="block px-3 py-2 rounded {{ request()->routeIs('dashboard.listings') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800' }}">📊 Listings &amp; Bids <span class="text-xs opacity-60 ml-1">soon</span></a>
            <a href="{{ route('dashboard.gp.index') }}" class="block px-3 py-2 rounded {{ request()->routeIs('dashboard.gp.*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800' }}">🪙 Gold Points <span class="float-right text-xs bg-amber-500/20 text-amber-200 px-2 rounded-full">{{ number_format($bal) }}</span></a>
            <a href="{{ route('dashboard.ai') }}" class="block px-3 py-2 rounded {{ request()->routeIs('dashboard.ai') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800' }}">🤖 AI Assistant <span class="text-xs opacity-60 ml-1">soon</span></a>
            <a href="{{ route('dashboard.notifications') }}" class="block px-3 py-2 rounded {{ request()->routeIs('dashboard.notifications') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800' }}">🔔 Notifications <span class="text-xs opacity-60 ml-1">soon</span></a>
            <a href="{{ route('dashboard.tutorials') }}" class="block px-3 py-2 rounded {{ request()->routeIs('dashboard.tutorials') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800' }}">🎓 Tutorials <span class="text-xs opacity-60 ml-1">soon</span></a>
            <a href="{{ route('dashboard.history') }}" class="block px-3 py-2 rounded {{ request()->routeIs('dashboard.history') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800' }}">📜 Activity Log</a>
            <a href="{{ route('dashboard.profile.edit') }}" class="block px-3 py-2 rounded {{ request()->routeIs('dashboard.profile.*') ? 'bg-slate-800 text-white' : 'hover:bg-slate-800' }}">⚙️ Profile</a>
        </nav>
        <div class="p-3 border-t border-slate-800">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="w-full text-left px-3 py-2 rounded hover:bg-slate-800 text-sm">↩ Logout</button>
            </form>
        </div>
    </aside>

    <div class="flex-1 md:ml-64">
        <header class="bg-white border-b border-slate-200 sticky top-0 z-20">
            <div class="px-4 md:px-8 h-14 flex items-center justify-between">
                <h1 class="font-semibold text-slate-800">@yield('heading', 'Dashboard')</h1>
                <div class="text-sm text-slate-600">
                    Hi, <strong>{{ auth()->user()->name }}</strong>
                </div>
            </div>
        </header>

        <main class="p-4 md:p-8">
            @if(session('flash'))
                <div class="mb-5 p-3 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200">{{ session('flash') }}</div>
            @endif
            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>
