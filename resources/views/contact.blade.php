@extends('layouts.public')

@section('title') Contact — {{ \App\Models\RgSetting::get('site_name', 'Resort Guru PH') }} @endsection

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-4xl font-extrabold text-slate-900 mb-4">Get in touch</h1>
    <p class="text-slate-600 mb-8">Have a question, partnership idea, or feedback? Drop us a line.</p>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200">{{ session('success') }}</div>
    @endif

    <form action="{{ route('contact.submit') }}" method="POST" class="space-y-4">
        @csrf
        <div class="grid sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-1">Name *</label>
                <input type="text" name="name" value="{{ old('name') }}" class="w-full rounded-md border-slate-300" required>
                @error('name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Email *</label>
                <input type="email" name="email" value="{{ old('email') }}" class="w-full rounded-md border-slate-300" required>
                @error('email')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Subject</label>
            <input type="text" name="subject" value="{{ old('subject') }}" class="w-full rounded-md border-slate-300">
        </div>
        <div>
            <label class="block text-sm font-semibold mb-1">Message *</label>
            <textarea name="message" rows="6" class="w-full rounded-md border-slate-300" required>{{ old('message') }}</textarea>
            @error('message')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
        </div>
        <button class="px-6 py-2.5 rounded-md bg-brand-600 text-white font-semibold hover:bg-brand-700">Send message</button>
    </form>

    <hr class="my-10 border-slate-200">
    <div class="text-slate-600">
        <p><strong>Email:</strong> {{ \App\Models\RgSetting::get('contact_email', 'hello@resortguruph.com') }}</p>
        <p><strong>Phone:</strong> {{ \App\Models\RgSetting::get('contact_phone', '+63 900 000 0000') }}</p>
    </div>
</div>
@endsection
