@extends('layouts.dashboard')

@section('heading') Top up Gold Points @endsection

@section('content')
<div class="grid lg:grid-cols-2 gap-6 max-w-5xl">
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <h2 class="text-xl font-bold mb-3">1. Send GCash payment</h2>
        <p class="text-sm text-slate-600 mb-4">Open your GCash app and send the amount you want to top up to:</p>
        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-4">
            <p class="text-xs text-amber-700 mb-1">Payee Name</p>
            <p class="font-bold text-lg">{{ $payee['name'] }}</p>
            <p class="text-xs text-amber-700 mt-3 mb-1">Payee Number</p>
            <p class="font-bold text-lg">{{ $payee['number'] }}</p>
        </div>
        <p class="text-sm text-slate-600">Minimum top-up: <strong>₱{{ $minTopup }}</strong></p>
        <p class="text-sm text-slate-600">Conversion: <strong>1 PHP = {{ $rate }} GP</strong></p>
        <p class="text-sm text-slate-600 mt-2">After payment, screenshot the success page from your GCash app.</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <h2 class="text-xl font-bold mb-3">2. Submit details</h2>
        <form action="{{ route('dashboard.gp.topup.submit') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold mb-1">Amount sent (PHP) <span class="text-red-600">*</span></label>
                <input type="number" name="php_amount" min="{{ $minTopup }}" step="1" class="w-full rounded-md border-slate-300" required>
                @error('php_amount')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">GCash reference number <span class="text-red-600">*</span></label>
                <input type="text" name="gcash_ref_number" class="w-full rounded-md border-slate-300" placeholder="From your GCash success page" required>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Sender phone number <span class="text-red-600">*</span></label>
                <input type="text" name="gcash_phone" class="w-full rounded-md border-slate-300" placeholder="09XX-XXX-XXXX" required>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Screenshot of payment <span class="text-red-600">*</span></label>
                <input type="file" name="screenshot" accept="image/*" class="w-full text-sm" required>
                <p class="text-xs text-slate-500 mt-1">Max 5 MB. PNG / JPG / WEBP.</p>
            </div>
            <button class="w-full px-5 py-3 rounded-md bg-emerald-600 text-white font-semibold hover:bg-emerald-700">Submit for review</button>
        </form>
    </div>
</div>

<div class="mt-6 p-4 rounded-md bg-slate-100 border border-slate-200 text-sm text-slate-700 max-w-5xl">
    💡 <strong>What happens next?</strong> An admin will review your screenshot and reference number. Once verified, GP is credited to your balance — usually within 1-24 hours.
</div>
@endsection
