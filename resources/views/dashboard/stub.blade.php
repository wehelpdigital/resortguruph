@extends('layouts.dashboard')

@section('heading') {{ $title }} @endsection

@section('content')
<div class="max-w-2xl mx-auto mt-10 bg-white rounded-xl shadow-sm border border-slate-200 p-10 text-center">
    <div class="text-6xl mb-3">🚧</div>
    <h2 class="text-2xl font-bold text-slate-900 mb-2">{{ $title }}</h2>
    <p class="text-slate-600 mb-4">{{ $description }}</p>
    <span class="inline-block px-3 py-1 rounded-full bg-amber-100 text-amber-700 text-sm font-semibold">Coming in {{ $phase }}</span>
</div>
@endsection
