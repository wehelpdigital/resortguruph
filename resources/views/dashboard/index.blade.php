@extends('layouts.dashboard')

@section('heading') Welcome back @endsection

@section('content')
<div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200">
        <p class="text-sm text-slate-500">My Resorts</p>
        <p class="text-3xl font-extrabold mt-1">{{ $resorts->count() }}</p>
    </div>
    <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200">
        <p class="text-sm text-slate-500">Gold Points Balance</p>
        <p class="text-3xl font-extrabold mt-1">{{ number_format($balance) }} <span class="text-base text-amber-600">GP</span></p>
    </div>
    <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200">
        <p class="text-sm text-slate-500">Published Resorts</p>
        <p class="text-3xl font-extrabold mt-1 text-emerald-600">{{ $status['published'] }}</p>
    </div>
    <div class="bg-white p-5 rounded-xl shadow-sm border border-slate-200">
        <p class="text-sm text-slate-500">Pending Reviews</p>
        <p class="text-3xl font-extrabold mt-1 text-amber-600">{{ $status['pending_review'] }}</p>
    </div>
</div>

@if($resorts->count() === 0)
    <div class="bg-white border-2 border-dashed border-brand-300 p-8 rounded-xl text-center">
        <h2 class="text-xl font-bold mb-2">Add your first resort to get started</h2>
        <p class="text-slate-600 mb-5">Resorts can be claimed to listings only after admin review.</p>
        <a href="{{ route('dashboard.resorts.create') }}" class="inline-block px-5 py-2.5 rounded-md bg-brand-600 text-white font-semibold hover:bg-brand-700">+ Create resort</a>
    </div>
@else
    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200">
                <div class="px-5 py-3 border-b border-slate-200 flex items-center justify-between">
                    <h2 class="font-bold">My resorts</h2>
                    <a href="{{ route('dashboard.resorts.create') }}" class="text-sm text-brand-600 hover:underline">+ Add new</a>
                </div>
                <ul class="divide-y divide-slate-100">
                    @foreach($resorts as $r)
                        <li class="px-5 py-4 flex items-center justify-between">
                            <div>
                                <a href="{{ route('dashboard.resorts.edit', $r) }}" class="font-semibold text-slate-900 hover:text-brand-600">{{ $r->name ?: '(unnamed resort)' }}</a>
                                <p class="text-xs text-slate-500">{{ $r->city }}, {{ $r->province }}</p>
                            </div>
                            @php $sc = ['draft'=>'slate','pending_review'=>'amber','published'=>'emerald','suspended'=>'red'][$r->status] ?? 'slate'; @endphp
                            <span class="text-xs px-2 py-1 rounded bg-{{ $sc }}-100 text-{{ $sc }}-700">{{ ucwords(str_replace('_',' ',$r->status)) }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="space-y-5">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
                <h3 class="font-bold mb-3">Recent GP activity</h3>
                @if($recentLedger->isEmpty())
                    <p class="text-sm text-slate-500">No transactions yet.</p>
                @else
                    <ul class="text-sm space-y-2">
                        @foreach($recentLedger as $row)
                            <li class="flex justify-between">
                                <span class="text-slate-600">{{ str_replace('_',' ',$row->reason) }}</span>
                                <strong class="{{ $row->amount >= 0 ? 'text-emerald-600' : 'text-red-600' }}">{{ $row->amount >= 0 ? '+' : '' }}{{ number_format($row->amount) }}</strong>
                            </li>
                        @endforeach
                    </ul>
                @endif
                <a href="{{ route('dashboard.gp.index') }}" class="block mt-3 text-sm text-brand-600 hover:underline">View full ledger &rarr;</a>
            </div>

            <a href="{{ route('dashboard.gp.topup') }}" class="block bg-amber-500 text-white p-5 rounded-xl shadow-sm hover:bg-amber-600">
                <h3 class="font-bold mb-1">Top up Gold Points</h3>
                <p class="text-sm opacity-90">Pay via GCash, get credited within 24h.</p>
            </a>
        </div>
    </div>
@endif
@endsection
