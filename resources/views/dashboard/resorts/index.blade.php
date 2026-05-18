@extends('layouts.dashboard')

@section('heading') My Resorts @endsection

@section('content')
<div class="flex justify-between items-center mb-5">
    <p class="text-slate-600">Manage your resort, hotel, or Airbnb listings here.</p>
    <a href="{{ route('dashboard.resorts.create') }}" class="px-4 py-2 rounded-md bg-brand-600 text-white font-semibold hover:bg-brand-700">+ Add resort</a>
</div>

@if($resorts->isEmpty())
    <div class="bg-white rounded-xl border-2 border-dashed border-slate-300 p-10 text-center">
        <p class="text-slate-500 mb-4">You haven't added any resorts yet.</p>
        <a href="{{ route('dashboard.resorts.create') }}" class="px-5 py-2.5 rounded-md bg-brand-600 text-white font-semibold hover:bg-brand-700">+ Add your first resort</a>
    </div>
@else
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($resorts as $r)
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="aspect-[16/10] bg-slate-200">
                    @if($r->hero_path)
                        <img src="{{ asset('storage/' . $r->hero_path) }}" class="w-full h-full object-cover">
                    @endif
                </div>
                <div class="p-4">
                    <h3 class="font-bold mb-1">{{ $r->name ?: '(unnamed)' }}</h3>
                    <p class="text-xs text-slate-500 mb-2">{{ $r->city }}, {{ $r->province }}</p>
                    @php $sc = ['draft'=>'slate','pending_review'=>'amber','published'=>'emerald','suspended'=>'red'][$r->status] ?? 'slate'; @endphp
                    <span class="text-xs px-2 py-1 rounded bg-{{ $sc }}-100 text-{{ $sc }}-700">{{ ucwords(str_replace('_',' ',$r->status)) }}</span>
                    <div class="mt-4 flex gap-2 text-sm">
                        <a href="{{ route('dashboard.resorts.edit', $r) }}" class="flex-1 px-3 py-1.5 rounded border border-slate-300 text-center hover:bg-slate-50">Edit</a>
                        @if($r->status === 'published')
                            <a href="{{ route('resort.show', $r->slug) }}" target="_blank" class="flex-1 px-3 py-1.5 rounded bg-brand-600 text-white text-center hover:bg-brand-700">View</a>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
