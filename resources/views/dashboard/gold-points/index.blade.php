@extends('layouts.dashboard')

@section('heading') Gold Points @endsection

@section('content')
<div class="grid md:grid-cols-3 gap-5 mb-6">
    <div class="md:col-span-2 bg-gradient-to-br from-amber-400 to-amber-600 text-white p-6 rounded-xl shadow-sm">
        <p class="text-sm opacity-90 mb-1">Available balance</p>
        <p class="text-5xl font-extrabold">{{ number_format($balance) }} <span class="text-2xl opacity-90">GP</span></p>
        <p class="text-sm mt-2 opacity-90">1 GP = ₱1.00</p>
    </div>
    <div>
        <a href="{{ route('dashboard.gp.topup') }}" class="block bg-white p-6 rounded-xl shadow-sm border border-slate-200 h-full hover:border-brand-300 hover:shadow-md transition">
            <p class="text-3xl">💳</p>
            <h3 class="font-bold mt-2">Top up via GCash</h3>
            <p class="text-sm text-slate-500 mt-1">Send GCash, upload screenshot, get credited.</p>
        </a>
    </div>
</div>

@if($topups->isNotEmpty())
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 mb-6">
        <div class="px-5 py-3 border-b border-slate-200"><h3 class="font-bold">Recent top-ups</h3></div>
        <ul class="divide-y divide-slate-100">
            @foreach($topups as $t)
                <li class="px-5 py-3 flex items-center justify-between text-sm">
                    <div>
                        <strong>₱{{ number_format($t->php_amount) }}</strong> → {{ number_format($t->gp_amount) }} GP
                        <p class="text-xs text-slate-500">{{ $t->created_at->diffForHumans() }} · Ref: <code>{{ $t->gcash_ref_number }}</code></p>
                    </div>
                    @php $sc = ['pending'=>'amber','approved'=>'emerald','rejected'=>'red'][$t->status] ?? 'slate'; @endphp
                    <span class="text-xs px-2 py-1 rounded bg-{{ $sc }}-100 text-{{ $sc }}-700">{{ ucfirst($t->status) }}</span>
                </li>
            @endforeach
        </ul>
    </div>
@endif

<div class="bg-white rounded-xl shadow-sm border border-slate-200">
    <div class="px-5 py-3 border-b border-slate-200"><h3 class="font-bold">Ledger</h3></div>
    @if($ledger->count() === 0)
        <p class="p-8 text-center text-slate-500">No transactions yet.</p>
    @else
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-left">
                <tr>
                    <th class="px-5 py-2">When</th>
                    <th class="px-5 py-2">Reason</th>
                    <th class="px-5 py-2 text-right">Amount</th>
                    <th class="px-5 py-2">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($ledger as $row)
                    <tr>
                        <td class="px-5 py-2">{{ \Carbon\Carbon::parse($row->created_at)->format('Y-m-d H:i') }}</td>
                        <td class="px-5 py-2 text-slate-600">{{ str_replace('_',' ',$row->reason) }}</td>
                        <td class="px-5 py-2 text-right {{ $row->amount >= 0 ? 'text-emerald-600' : 'text-red-600' }} font-semibold">{{ $row->amount >= 0 ? '+' : '' }}{{ number_format($row->amount) }} GP</td>
                        <td class="px-5 py-2"><span class="text-xs px-2 py-1 rounded bg-slate-100">{{ $row->status }}</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="p-4 border-t border-slate-200">{{ $ledger->links() }}</div>
    @endif
</div>
@endsection
