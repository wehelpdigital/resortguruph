@extends('layouts.dashboard')

@section('heading') My Listings @endsection

@section('content')
<div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 mb-5">
    <div>
        <p class="text-slate-600">Listing slots you have claimed on keyword pages. Each one runs through its expiry date at whatever rank it has.</p>
        <p class="text-sm text-slate-500 mt-1">Balance: <strong class="text-amber-600">{{ number_format($balance) }} GP</strong></p>
    </div>
    <a href="{{ route('dashboard.listings.browse') }}" class="px-4 py-2 rounded-md bg-brand-600 text-white font-semibold hover:bg-brand-700">+ Claim new listing</a>
</div>

@if(session('flash'))
    <div class="mb-4 p-3 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200">{{ session('flash') }}</div>
@endif
@if($errors->any())
    <div class="mb-4 p-3 rounded-md bg-red-50 text-red-700 border border-red-200">
        @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
    </div>
@endif

@if($publishedResortCount === 0)
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-6 mb-6">
        <h3 class="font-bold text-amber-900 mb-1">No published resorts yet</h3>
        <p class="text-amber-800 text-sm">You need at least one approved resort before you can claim a listing slot. <a href="{{ route('dashboard.resorts.index') }}" class="font-semibold underline">Add a resort</a> first.</p>
    </div>
@endif

@if($listings->isEmpty())
    <div class="bg-white border-2 border-dashed border-slate-300 rounded-xl p-10 text-center">
        <p class="text-slate-500 mb-4">You have no active listings yet.</p>
        @if($publishedResortCount > 0)
            <a href="{{ route('dashboard.listings.browse') }}" class="px-5 py-2.5 rounded-md bg-brand-600 text-white font-semibold hover:bg-brand-700">Browse keywords to claim</a>
        @endif
    </div>
@else
    <div class="space-y-4">
        @foreach($listings as $l)
            @php
                $daysLeft = $l->expires_at ? max(0, now()->diffInDays($l->expires_at, false)) : null;
                $nearExpiry = $daysLeft !== null && $daysLeft < 7;
            @endphp
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-1 flex-wrap">
                            <a href="{{ url($l->keyword->slug) }}" target="_blank" class="font-bold text-lg text-slate-900 hover:text-brand-600">{{ ucwords($l->keyword->phrase) }}</a>
                            @if($l->is_at_top)
                                <span class="text-xs px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 font-semibold">🥇 Top spot</span>
                            @endif
                            @if($nearExpiry)
                                <span class="text-xs px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 font-semibold">⏰ Expires soon</span>
                            @endif
                        </div>
                        <p class="text-sm text-slate-600 mb-2">Resort: <strong>{{ $l->resort->name }}</strong></p>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm">
                            <div>
                                <p class="text-slate-500 text-xs uppercase tracking-wide">Base paid</p>
                                <p class="font-semibold">{{ number_format($l->base_gp) }} GP</p>
                            </div>
                            <div>
                                <p class="text-slate-500 text-xs uppercase tracking-wide">Bid added</p>
                                <p class="font-semibold">{{ number_format($l->bid_gp) }} GP</p>
                            </div>
                            <div>
                                <p class="text-slate-500 text-xs uppercase tracking-wide">Days left</p>
                                <p class="font-semibold {{ $nearExpiry ? 'text-amber-600' : '' }}">{{ $daysLeft ?? '∞' }}</p>
                            </div>
                            <div>
                                <p class="text-slate-500 text-xs uppercase tracking-wide">GP to top</p>
                                <p class="font-semibold {{ $l->is_at_top ? 'text-emerald-600' : 'text-brand-600' }}">
                                    {{ $l->is_at_top ? "You're #1" : number_format($l->gp_to_top) . ' GP' }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col gap-2 md:min-w-[220px]">
                        <button onclick="openBid({{ $l->id }}, '{{ addslashes($l->resort->name) }}', {{ $l->gp_to_top }})" class="px-4 py-2 rounded-md bg-amber-500 text-white font-semibold hover:bg-amber-600 text-sm">💰 Top up bid</button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

{{-- Bid modal --}}
<div id="bidModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6">
        <h3 class="text-xl font-bold mb-2">Top up your bid</h3>
        <p class="text-sm text-slate-600 mb-4" id="bidContext"></p>
        <form id="bidForm" method="POST">
            @csrf
            <div class="mb-3">
                <label class="block text-sm font-semibold mb-1">GP to add</label>
                <input type="number" name="gp_amount" min="1" class="w-full rounded-md border-slate-300" required autofocus>
                <p class="text-xs text-slate-500 mt-1">You have <strong>{{ number_format($balance) }} GP</strong> available.</p>
            </div>
            <div class="mb-4">
                <label class="inline-flex items-center text-sm">
                    <input type="checkbox" name="extend" value="1" checked class="rounded text-brand-600 me-2">
                    Also extend duration proportionally
                </label>
            </div>
            <div class="flex gap-2 justify-end">
                <button type="button" onclick="closeBid()" class="px-4 py-2 rounded-md border border-slate-300 hover:bg-slate-50">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded-md bg-amber-500 text-white font-semibold hover:bg-amber-600">Place bid</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function openBid(listingId, resortName, gpToTop) {
    var form = document.getElementById('bidForm');
    form.action = '/dashboard/listings/' + listingId + '/bid';
    document.getElementById('bidContext').textContent = 'Bidding for ' + resortName + '. ' + (gpToTop === 0 ? "You're already at the top." : 'Add at least ' + gpToTop + ' GP (quantized) to take the top spot.');
    document.getElementById('bidModal').classList.remove('hidden');
}
function closeBid() {
    document.getElementById('bidModal').classList.add('hidden');
}
document.getElementById('bidModal')?.addEventListener('click', function (e) {
    if (e.target === this) closeBid();
});
</script>
@endpush
@endsection
