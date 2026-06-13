@extends('layouts.dashboard')

@section('heading') Activity Log @endsection

@section('content')
<div class="mb-6">
    <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-[-0.015em]">Activity log</h1>
    <p class="text-slate-600 mt-1 text-sm">Every action on your account in chronological order. Useful for tracing back changes or confirming an update went through.</p>
</div>

@php
    // Map action verbs to a small icon + accent palette so the
    // table reads visually instead of as a wall of text.
    $actionStyles = [
        'create' => ['bg' => 'bg-emerald-50', 'fg' => 'text-emerald-700', 'icon' => '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.4" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>'],
        'update' => ['bg' => 'bg-blue-50', 'fg' => 'text-blue-700', 'icon' => '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487a2.032 2.032 0 0 1 2.872 2.872L7.5 19.594 3 21l1.406-4.5 12.456-12.013Z"/></svg>'],
        'delete' => ['bg' => 'bg-rose-50', 'fg' => 'text-rose-700', 'icon' => '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9 14.394 18m-4.79 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>'],
        'login' => ['bg' => 'bg-violet-50', 'fg' => 'text-violet-700', 'icon' => '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75"/></svg>'],
        'topup' => ['bg' => 'bg-amber-50', 'fg' => 'text-amber-700', 'icon' => '<svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>'],
    ];

    // Helper to pick the closest matching style for an action verb
    $actionPick = function ($action) use ($actionStyles) {
        $lc = strtolower($action ?? '');
        foreach ($actionStyles as $key => $style) {
            if (str_contains($lc, $key)) return $style;
        }
        return ['bg' => 'bg-slate-100', 'fg' => 'text-slate-700', 'icon' => '<svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3"/></svg>'];
    };
@endphp

@if($logs->count() === 0)
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-12 text-center">
        <div class="w-16 h-16 rounded-2xl bg-slate-50 text-slate-400 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.6" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125c0-3.453 2.798-6.25 6.25-6.25h5.5c3.452 0 6.25 2.797 6.25 6.25v.625a2.25 2.25 0 0 1-2.25 2.25H5.25a2.25 2.25 0 0 1-2.25-2.25v-.625Z"/></svg>
        </div>
        <h2 class="text-lg font-bold text-slate-900 mb-1">No activity yet</h2>
        <p class="text-slate-500 text-sm max-w-sm mx-auto">Your activity log will start filling up as you create resorts, top up Gold Points, and update your listings.</p>
    </div>
@else
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        {{-- Desktop table view (md+) --}}
        <div class="hidden md:block">
            <table class="w-full text-sm">
                <thead class="bg-slate-50">
                    <tr class="text-left text-xs uppercase tracking-wider text-slate-500 font-bold">
                        <th class="px-5 py-3">When</th>
                        <th class="px-5 py-3">Action</th>
                        <th class="px-5 py-3">Target</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($logs as $row)
                        @php $style = $actionPick($row->action); @endphp
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-5 py-3.5 text-slate-700 whitespace-nowrap">
                                <div class="font-medium">{{ \Carbon\Carbon::parse($row->created_at)->format('M j, Y') }}</div>
                                <div class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($row->created_at)->format('H:i') }}</div>
                            </td>
                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $style['bg'] }} {{ $style['fg'] }}">
                                    {!! $style['icon'] !!}
                                    <span class="font-mono lowercase">{{ $row->action }}</span>
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-slate-600">
                                @if($row->target_type)
                                    <span class="capitalize">{{ str_replace('_', ' ', $row->target_type) }}</span>
                                    <span class="text-slate-400 ml-1">#{{ $row->target_id }}</span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile card view (md-) --}}
        <ul class="md:hidden divide-y divide-slate-100">
            @foreach($logs as $row)
                @php $style = $actionPick($row->action); @endphp
                <li class="px-5 py-4">
                    <div class="flex items-start justify-between gap-3 mb-1.5">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold {{ $style['bg'] }} {{ $style['fg'] }}">
                            {!! $style['icon'] !!}
                            <span class="font-mono lowercase">{{ $row->action }}</span>
                        </span>
                        <span class="text-xs text-slate-400 whitespace-nowrap">{{ \Carbon\Carbon::parse($row->created_at)->format('M j, H:i') }}</span>
                    </div>
                    @if($row->target_type)
                        <p class="text-sm text-slate-600">
                            <span class="capitalize">{{ str_replace('_', ' ', $row->target_type) }}</span>
                            <span class="text-slate-400">#{{ $row->target_id }}</span>
                        </p>
                    @endif
                </li>
            @endforeach
        </ul>

        <div class="px-5 py-4 border-t border-slate-100 bg-slate-50/40">{{ $logs->links() }}</div>
    </div>
@endif
@endsection
