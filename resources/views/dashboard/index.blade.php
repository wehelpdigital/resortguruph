@extends('layouts.dashboard')

@section('heading') Welcome back @endsection

@section('content')
<div class="mb-6 flex items-end justify-between flex-wrap gap-3">
    <div>
        <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-[-0.015em]">Welcome back{{ ($name = Auth::guard('owner')->user()->name ?? null) ? ', ' . explode(' ', $name)[0] : '' }}</h1>
        <p class="text-slate-600 mt-1 text-sm">Here is what is happening with your resorts and Gold Points right now.</p>
    </div>
    <a href="{{ route('dashboard.resorts.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 transition-colors shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
        New resort
    </a>
</div>

{{-- 4-up stat cards: each card has a colored icon tile, the number, and a subtle subline --}}
<div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 hover:shadow-md transition-shadow">
        <div class="flex items-start justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-700 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l8-4 8 4v14M9 9v.01M9 12v.01M9 15v.01M9 18v.01"/></svg>
            </div>
        </div>
        <p class="text-xs uppercase tracking-wider text-slate-500 font-semibold">My resorts</p>
        <p class="text-3xl font-extrabold text-slate-900 mt-1">{{ $resorts->count() }}</p>
    </div>

    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 hover:shadow-md transition-shadow">
        <div class="flex items-start justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
            </div>
            <a href="{{ route('dashboard.gp.topup') }}" class="text-xs text-amber-600 font-bold hover:text-amber-700">+ Top up</a>
        </div>
        <p class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Gold Points</p>
        <p class="text-3xl font-extrabold text-slate-900 mt-1">{{ number_format($balance) }} <span class="text-base text-amber-600 font-bold">GP</span></p>
    </div>

    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 hover:shadow-md transition-shadow">
        <div class="flex items-start justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/></svg>
            </div>
        </div>
        <p class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Published</p>
        <p class="text-3xl font-extrabold text-emerald-600 mt-1">{{ $status['published'] }}</p>
    </div>

    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 hover:shadow-md transition-shadow">
        <div class="flex items-start justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
            </div>
        </div>
        <p class="text-xs uppercase tracking-wider text-slate-500 font-semibold">Pending review</p>
        <p class="text-3xl font-extrabold text-amber-600 mt-1">{{ $status['pending_review'] }}</p>
    </div>
</div>

@if($resorts->count() === 0)
    {{-- Modern empty state with illustration glyph + helper text + CTA --}}
    <div class="bg-white border-2 border-dashed border-blue-200 p-10 md:p-14 rounded-2xl text-center">
        <div class="w-20 h-20 rounded-2xl bg-blue-50 text-blue-700 flex items-center justify-center mx-auto mb-5">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l8-4 8 4v14M9 9v.01M9 12v.01M9 15v.01M9 18v.01"/></svg>
        </div>
        <h2 class="text-2xl font-extrabold text-slate-900 mb-2 tracking-[-0.01em]">Add your first resort to get started</h2>
        <p class="text-slate-600 max-w-md mx-auto mb-6 leading-relaxed">Add the basics, drop in your photos, and our team reviews within a day before your listing goes live on the destination pages locals already search.</p>
        <a href="{{ route('dashboard.resorts.create') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-lg bg-blue-600 text-white font-bold hover:bg-blue-700 transition-colors shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Create your first resort
        </a>
    </div>
@else
    <div class="grid lg:grid-cols-3 gap-6">
        {{-- My resorts list (2/3 width on lg+) --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h2 class="font-bold text-slate-900">My resorts</h2>
                    <a href="{{ route('dashboard.resorts.create') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700 inline-flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        Add new
                    </a>
                </div>
                <ul class="divide-y divide-slate-100">
                    @foreach($resorts as $r)
                        @php
                            $statusColors = [
                                'draft' => ['bg' => 'bg-slate-100', 'text' => 'text-slate-700'],
                                'pending_review' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-800'],
                                'published' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-800'],
                                'suspended' => ['bg' => 'bg-rose-100', 'text' => 'text-rose-800'],
                            ];
                            $sc = $statusColors[$r->status] ?? $statusColors['draft'];
                        @endphp
                        <li class="px-5 py-4 flex items-center justify-between gap-4 hover:bg-slate-50 transition-colors">
                            <div class="flex items-center gap-3 min-w-0">
                                @if($r->hero_path)
                                    <img src="{{ asset('storage/' . $r->hero_path) }}" alt="" class="w-12 h-12 rounded-lg object-cover bg-slate-100 flex-shrink-0">
                                @else
                                    <div class="w-12 h-12 rounded-lg bg-slate-100 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M5 21V7l8-4 8 4v14"/></svg>
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <a href="{{ route('dashboard.resorts.edit', $r) }}" class="font-semibold text-slate-900 hover:text-blue-600 truncate block">{{ $r->name ?: '(unnamed resort)' }}</a>
                                    @if($r->city || $r->province)
                                        <p class="text-xs text-slate-500 mt-0.5 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z"/></svg>
                                            {{ trim($r->city . ($r->city && $r->province ? ', ' : '') . $r->province) }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $sc['bg'] }} {{ $sc['text'] }} flex-shrink-0">{{ ucwords(str_replace('_', ' ', $r->status)) }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        {{-- Sidebar: GP activity + top-up CTA --}}
        <div class="space-y-5">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-bold text-slate-900">Recent GP activity</h3>
                    <a href="{{ route('dashboard.gp.index') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-700">View all</a>
                </div>
                <div class="p-5">
                    @if($recentLedger->isEmpty())
                        <div class="text-center py-6">
                            <div class="w-12 h-12 rounded-full bg-slate-50 flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125c0-3.453 2.798-6.25 6.25-6.25h5.5c3.452 0 6.25 2.797 6.25 6.25v.625a2.25 2.25 0 0 1-2.25 2.25H5.25a2.25 2.25 0 0 1-2.25-2.25v-.625Z"/></svg>
                            </div>
                            <p class="text-sm text-slate-500">No transactions yet.</p>
                        </div>
                    @else
                        <ul class="space-y-3">
                            @foreach($recentLedger as $row)
                                <li class="flex items-center justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-slate-700 truncate capitalize">{{ str_replace('_', ' ', $row->reason) }}</p>
                                        <p class="text-xs text-slate-400 mt-0.5">{{ \Carbon\Carbon::parse($row->created_at)->diffForHumans() }}</p>
                                    </div>
                                    <strong class="text-sm font-bold flex-shrink-0 {{ $row->amount >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">{{ $row->amount >= 0 ? '+' : '' }}{{ number_format($row->amount) }} GP</strong>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>

            {{-- Top-up CTA — gradient amber card --}}
            <a href="{{ route('dashboard.gp.topup') }}" class="block bg-gradient-to-br from-amber-500 to-orange-500 text-white p-6 rounded-2xl shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
                <div class="absolute -top-10 -right-10 w-32 h-32 rounded-full bg-white/10"></div>
                <div class="relative">
                    <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center mb-3">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                    </div>
                    <h3 class="font-extrabold text-lg mb-1 tracking-[-0.01em]">Top up Gold Points</h3>
                    <p class="text-sm text-white/90 leading-relaxed">Pay via GCash, get credited within 24h. Use GP to boost your listing's rank.</p>
                </div>
            </a>
        </div>
    </div>
@endif
@endsection
