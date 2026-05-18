@extends('layouts.dashboard')

@section('heading') Claim Listing Slot @endsection

@section('content')
<div class="max-w-2xl">
    <a href="{{ route('dashboard.listings.browse') }}" class="text-sm text-slate-500 hover:text-brand-600">&larr; back to browse</a>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 mt-3">
        <h2 class="text-2xl font-bold mb-1 capitalize">{{ $keyword->phrase }}</h2>
        <p class="text-sm text-slate-500 mb-4">URL: <a href="{{ url($keyword->slug) }}" target="_blank" class="text-brand-600">/{{ $keyword->slug }}</a></p>

        <div class="grid sm:grid-cols-3 gap-3 mb-6">
            <div class="p-3 rounded-md bg-slate-50">
                <p class="text-xs text-slate-500 uppercase">Monthly searches</p>
                <p class="text-xl font-bold">{{ number_format($keyword->search_volume_monthly) }}</p>
            </div>
            <div class="p-3 rounded-md bg-slate-50">
                <p class="text-xs text-slate-500 uppercase">Base cost</p>
                <p class="text-xl font-bold text-brand-700">{{ number_format($basePrice) }} GP</p>
            </div>
            <div class="p-3 rounded-md bg-slate-50">
                <p class="text-xs text-slate-500 uppercase">Duration</p>
                <p class="text-xl font-bold">{{ $duration }} days</p>
            </div>
        </div>

        @if($errors->any())
            <div class="mb-4 p-3 rounded-md bg-red-50 text-red-700 border border-red-200">
                @foreach($errors->all() as $e)<p>{{ $e }}</p>@endforeach
            </div>
        @endif

        @if($resorts->isEmpty())
            <div class="p-4 rounded-md bg-amber-50 border border-amber-200">
                <p class="text-amber-800 text-sm">You have no published resorts yet. <a href="{{ route('dashboard.resorts.index') }}" class="font-semibold underline">Add one</a> first, then come back to claim this slot.</p>
            </div>
        @else
            <form action="{{ route('dashboard.listings.claim', $keyword->slug) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-semibold mb-1">Select resort to feature</label>
                    <select name="resort_id" class="w-full rounded-md border-slate-300" required>
                        <option value="">Choose your resort...</option>
                        @foreach($resorts as $r)
                            @php $alreadyHas = in_array($r->id, $alreadyListed); @endphp
                            <option value="{{ $r->id }}" {{ $alreadyHas ? 'disabled' : '' }}>
                                {{ $r->name }}{{ $alreadyHas ? ' (already listed for this keyword)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="p-4 rounded-md bg-emerald-50 border border-emerald-200">
                    <p class="text-sm text-emerald-800">Your balance: <strong>{{ number_format($balance) }} GP</strong></p>
                    <p class="text-sm text-emerald-800">After claim: <strong>{{ number_format($balance - $basePrice) }} GP</strong></p>
                    @if($balance < $basePrice)
                        <p class="text-sm text-red-700 mt-2 font-semibold">Insufficient GP. <a href="{{ route('dashboard.gp.topup') }}" class="underline">Top up now</a>.</p>
                    @endif
                </div>

                <p class="text-xs text-slate-500">By claiming, you agree that the {{ number_format($basePrice) }} GP is non-refundable and the listing will run for the full {{ $duration }} days regardless of rank changes.</p>

                <div class="flex gap-2">
                    <a href="{{ route('dashboard.listings.browse') }}" class="px-4 py-2 rounded-md border border-slate-300 hover:bg-slate-50">Cancel</a>
                    <button type="submit" class="px-5 py-2 rounded-md bg-brand-600 text-white font-semibold hover:bg-brand-700 disabled:opacity-50" {{ $balance < $basePrice ? 'disabled' : '' }}>
                        Confirm claim for {{ number_format($basePrice) }} GP
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>
@endsection
