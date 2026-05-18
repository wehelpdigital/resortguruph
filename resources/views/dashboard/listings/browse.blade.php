@extends('layouts.dashboard')

@section('heading') Browse Keywords @endsection

@section('content')
<p class="text-slate-600 mb-5">Pick a keyword page to claim a listing slot. Sorted by monthly search volume.</p>
<p class="text-sm text-slate-500 mb-4">Balance: <strong class="text-amber-600">{{ number_format($balance) }} GP</strong></p>

<form method="GET" class="bg-white rounded-xl shadow-sm border border-slate-200 p-4 mb-5 grid sm:grid-cols-3 gap-3">
    <div class="sm:col-span-2">
        <input type="text" name="q" value="{{ $search }}" placeholder="Search keyword..." class="w-full rounded-md border-slate-300">
    </div>
    <div>
        <select name="cluster" class="w-full rounded-md border-slate-300" onchange="this.form.submit()">
            <option value="">All regions</option>
            @foreach($clusters as $c)
                <option value="{{ $c }}" {{ $cluster === $c ? 'selected' : '' }}>{{ ucwords(str_replace('-', ' ', $c)) }}</option>
            @endforeach
        </select>
    </div>
</form>

<div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-slate-50 text-left">
            <tr>
                <th class="px-4 py-3">Keyword</th>
                <th class="px-4 py-3 text-right">Monthly searches</th>
                <th class="px-4 py-3 text-right">Active listings</th>
                <th class="px-4 py-3 text-right">Top bid</th>
                <th class="px-4 py-3 text-right">Base cost</th>
                <th class="px-4 py-3"></th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($keywords as $k)
                <tr>
                    <td class="px-4 py-3">
                        <a href="{{ url($k->slug) }}" target="_blank" class="font-semibold text-slate-900 hover:text-brand-600 capitalize">{{ $k->phrase }}</a>
                        @if($k->cluster_tag)
                            <span class="block text-xs text-slate-500">{{ ucwords(str_replace('-', ' ', $k->cluster_tag)) }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right">{{ number_format($k->search_volume_monthly) }}</td>
                    <td class="px-4 py-3 text-right">{{ $k->active_listings }}</td>
                    <td class="px-4 py-3 text-right">{{ $k->top_bid > 0 ? number_format($k->top_bid) . ' GP' : '—' }}</td>
                    <td class="px-4 py-3 text-right font-semibold text-brand-700">{{ number_format($k->base_price) }} GP</td>
                    <td class="px-4 py-3 text-right">
                        <a href="{{ route('dashboard.listings.claim.form', $k->slug) }}" class="inline-block px-3 py-1.5 rounded-md bg-brand-600 text-white text-xs font-semibold hover:bg-brand-700">Claim slot</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-4 py-10 text-center text-slate-500">No keywords match your filters.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $keywords->links() }}</div>
@endsection
