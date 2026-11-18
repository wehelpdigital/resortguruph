<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') · {{ \App\Models\RgSetting::get('site_name', 'Tourist Guide Ph') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/brand/favicon.svg') }}">
    <script src="{{ asset('vendor/tailwind/tailwind-3.4.17-forms-typography.js') }}"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { brand: { 50: '#eef6fb', 100: '#d6eaf5', 200: '#afd5ec', 300: '#7bb9de', 400: '#4499cc', 500: '#217db6', 600: '#13669c', 700: '#11527f', 800: '#13466a', 900: '#153b59' } }, fontFamily: { sans: ['Inter', 'system-ui'], display: ['"Plus Jakarta Sans"', 'Inter', 'system-ui'] } } } }
    </script>
    <link href="/vendor/fonts/fonts.css" rel="stylesheet">
    <style>
        h1, h2, h3 { font-family: "Plus Jakarta Sans", Inter, system-ui, sans-serif; letter-spacing: -0.01em; }
        /* Phones: the sidebar becomes an off-canvas panel. */
        @media (max-width: 767px) {
            #dashSidebar { display: flex; transform: translateX(-100%); transition: transform .25s ease; z-index: 50; }
            #dashSidebar.is-open { transform: translateX(0); box-shadow: 20px 0 40px -20px rgba(0,0,0,.5); }
        }
        #dashBackdrop { transition: opacity .25s ease; }
    </style>
    @stack('head')
</head>
<body class="bg-slate-100 text-slate-800 antialiased">

<div class="min-h-screen flex">
    <div id="dashBackdrop" class="fixed inset-0 bg-slate-900/50 z-40 opacity-0 pointer-events-none md:hidden" onclick="rgDashNav(false)"></div>
    <aside id="dashSidebar" class="w-64 bg-[#0b1b2b] text-slate-300 hidden md:flex flex-col fixed inset-y-0 overflow-y-auto">
        <div class="p-5 border-b border-slate-800">
            <a href="{{ route('home') }}" class="text-white font-bold flex items-center gap-2.5"><img src="{{ asset('images/brand/logo-mark.svg') }}" alt="" class="w-8 h-8"><span style="font-family:'Plus Jakarta Sans',Inter,sans-serif;font-weight:800;letter-spacing:-.03em">Tourist<span style="color:#7bb9de">Guide</span><span style="color:#ef5b3a">.ph</span></span></a>
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
            <div class="px-4 md:px-8 h-14 flex items-center justify-between gap-3">
                <div class="flex items-center gap-2 min-w-0">
                    <button type="button" class="md:hidden -ml-1.5 inline-flex items-center justify-center w-10 h-10 rounded-lg hover:bg-slate-100" onclick="rgDashNav(true)" aria-label="Open menu" aria-controls="dashSidebar">
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="M4 7h16M4 12h16M4 17h10"/></svg>
                    </button>
                    <h1 class="font-semibold text-slate-800 truncate">@yield('heading', 'Dashboard')</h1>
                </div>
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

<script>
function rgDashNav(open) {
    var sb = document.getElementById('dashSidebar'), bd = document.getElementById('dashBackdrop');
    if (!sb || !bd) return;
    sb.classList.toggle('is-open', open);
    bd.style.opacity = open ? '1' : '0';
    bd.style.pointerEvents = open ? 'auto' : 'none';
}
document.addEventListener('keydown', function (e) { if (e.key === 'Escape') rgDashNav(false); });
</script>
@stack('scripts')
</body>
</html>
