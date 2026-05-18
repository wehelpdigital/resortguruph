@extends('layouts.dashboard')

@section('heading') Activity Log @endsection

@section('content')
<div class="bg-white rounded-xl shadow-sm border border-slate-200">
    @if($logs->count() === 0)
        <p class="p-8 text-center text-slate-500">No activity yet.</p>
    @else
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left">
                <tr>
                    <th class="px-5 py-2">When</th>
                    <th class="px-5 py-2">Action</th>
                    <th class="px-5 py-2">Target</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($logs as $row)
                    <tr>
                        <td class="px-5 py-2">{{ \Carbon\Carbon::parse($row->created_at)->format('Y-m-d H:i') }}</td>
                        <td class="px-5 py-2 font-mono text-xs">{{ $row->action }}</td>
                        <td class="px-5 py-2 text-slate-500">{{ $row->target_type }} #{{ $row->target_id }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4 border-t border-slate-200">{{ $logs->links() }}</div>
    @endif
</div>
@endsection
