@extends('layouts.dashboard')

@section('heading') {{ $resort ? 'Edit Resort' : 'Add New Resort' }} @endsection

@php
    $amenityOptions = ['Wi-Fi', 'Pool', 'Air-conditioned rooms', 'Beachfront', 'Parking', 'Restaurant', 'Bar', 'Spa', 'Kid-friendly', 'Pet-friendly', 'Function hall', 'Cottages', 'BBQ pit', 'Kayaks', 'Diving', 'Karaoke', 'KTV', 'Garden', 'Mountain view'];
    $selectedAmenities = $resort ? $resort->amenities : [];
@endphp

@section('content')
@if($resort && $resort->status === 'draft')
    <div class="mb-5 p-4 rounded-md bg-amber-50 border border-amber-200">
        <p class="text-amber-800 font-semibold">This resort is still in <em>draft</em> status.</p>
        <p class="text-amber-700 text-sm">Complete the required details (name, description, city) and click "Submit for review" to get listed publicly.</p>
    </div>
@endif

<form action="{{ $resort ? route('dashboard.resorts.update', $resort) : route('dashboard.resorts.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
    @csrf
    @if($resort) @method('PUT') @endif

    {{-- STEP 1: BASICS --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200">
        <div class="px-5 py-3 border-b border-slate-200 bg-slate-50 rounded-t-xl"><h3 class="font-bold">1. Basics</h3></div>
        <div class="p-5 grid md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold mb-1">Resort name <span class="text-red-600">*</span></label>
                <input type="text" name="name" value="{{ old('name', $resort->name ?? '') }}" class="w-full rounded-md border-slate-300" required>
                @error('name')<p class="text-red-600 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold mb-1">Tagline (one-liner)</label>
                <input type="text" name="tagline" value="{{ old('tagline', $resort->tagline ?? '') }}" class="w-full rounded-md border-slate-300" placeholder="e.g. Beachfront escape in Pangasinan">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Region</label>
                <input type="text" name="region" value="{{ old('region', $resort->region ?? '') }}" class="w-full rounded-md border-slate-300" placeholder="Luzon / Visayas / Mindanao">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Province</label>
                <input type="text" name="province" value="{{ old('province', $resort->province ?? '') }}" class="w-full rounded-md border-slate-300">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">City / Town</label>
                <input type="text" name="city" value="{{ old('city', $resort->city ?? '') }}" class="w-full rounded-md border-slate-300">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Full address</label>
                <input type="text" name="address" value="{{ old('address', $resort->address ?? '') }}" class="w-full rounded-md border-slate-300">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Latitude</label>
                <input type="text" name="lat" value="{{ old('lat', $resort->lat ?? '') }}" class="w-full rounded-md border-slate-300" placeholder="14.5995">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Longitude</label>
                <input type="text" name="lng" value="{{ old('lng', $resort->lng ?? '') }}" class="w-full rounded-md border-slate-300" placeholder="120.9842">
            </div>
        </div>
    </div>

    {{-- STEP 2: DESCRIPTION --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200">
        <div class="px-5 py-3 border-b border-slate-200 bg-slate-50"><h3 class="font-bold">2. Description &amp; Pricing</h3></div>
        <div class="p-5 space-y-4">
            <div>
                <label class="block text-sm font-semibold mb-1">About your property</label>
                <textarea name="description_html" rows="10" class="w-full rounded-md border-slate-300" placeholder="Describe rooms, amenities, what makes it special...">{{ old('description_html', $resort->description_html ?? '') }}</textarea>
            </div>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1">Price range</label>
                    <input type="text" name="price_range" value="{{ old('price_range', $resort->price_range ?? '') }}" class="w-full rounded-md border-slate-300" placeholder="e.g. ₱2,000 - ₱8,000 / night">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Capacity</label>
                    <input type="text" name="capacity" value="{{ old('capacity', $resort->capacity ?? '') }}" class="w-full rounded-md border-slate-300" placeholder="e.g. up to 30 guests">
                </div>
            </div>
        </div>
    </div>

    {{-- STEP 3: AMENITIES --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200">
        <div class="px-5 py-3 border-b border-slate-200 bg-slate-50"><h3 class="font-bold">3. Amenities</h3></div>
        <div class="p-5">
            <p class="text-sm text-slate-500 mb-3">Check all that apply.</p>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-2 mb-4">
                @foreach($amenityOptions as $a)
                    <label class="flex items-center gap-2 px-3 py-2 rounded border border-slate-200 cursor-pointer hover:bg-slate-50">
                        <input type="checkbox" name="amenities[]" value="{{ $a }}" {{ in_array($a, $selectedAmenities) ? 'checked' : '' }} class="rounded text-brand-600">
                        <span class="text-sm">{{ $a }}</span>
                    </label>
                @endforeach
            </div>
            <label class="block text-sm font-semibold mb-1">Other amenities (comma-separated)</label>
            <input type="text" name="amenities_custom" class="w-full rounded-md border-slate-300" placeholder="e.g. Yoga deck, On-call masseuse">
        </div>
    </div>

    {{-- STEP 4: CONTACT --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200">
        <div class="px-5 py-3 border-b border-slate-200 bg-slate-50"><h3 class="font-bold">4. Contact &amp; Socials</h3></div>
        <div class="p-5 grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-1">Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $resort->phone ?? '') }}" class="w-full rounded-md border-slate-300">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $resort->email ?? '') }}" class="w-full rounded-md border-slate-300">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-semibold mb-1">Website</label>
                <input type="url" name="website" value="{{ old('website', $resort->website ?? '') }}" class="w-full rounded-md border-slate-300" placeholder="https://...">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Facebook</label>
                <input type="url" name="fb" value="{{ old('fb', $resort->fb ?? '') }}" class="w-full rounded-md border-slate-300">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">Instagram</label>
                <input type="url" name="ig" value="{{ old('ig', $resort->ig ?? '') }}" class="w-full rounded-md border-slate-300">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">TikTok</label>
                <input type="url" name="tt" value="{{ old('tt', $resort->tt ?? '') }}" class="w-full rounded-md border-slate-300">
            </div>
        </div>
    </div>

    {{-- STEP 5: BRANDING --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200">
        <div class="px-5 py-3 border-b border-slate-200 bg-slate-50"><h3 class="font-bold">5. Branding</h3></div>
        <div class="p-5 space-y-4">
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1">Logo</label>
                    @if($resort && $resort->logo_path)
                        <img src="{{ asset('storage/' . $resort->logo_path) }}" class="h-16 mb-2 rounded">
                    @endif
                    <input type="file" name="logo" accept="image/*" class="w-full text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Hero image</label>
                    @if($resort && $resort->hero_path)
                        <img src="{{ asset('storage/' . $resort->hero_path) }}" class="h-16 mb-2 rounded">
                    @endif
                    <input type="file" name="hero" accept="image/*" class="w-full text-sm">
                </div>
            </div>
            <div class="grid md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold mb-1">Primary colour</label>
                    <input type="color" name="primary_color" value="{{ old('primary_color', $resort->primary_color ?? '#556ee6') }}" class="h-10 w-full rounded">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-1">Secondary colour</label>
                    <input type="color" name="secondary_color" value="{{ old('secondary_color', $resort->secondary_color ?? '#34c38f') }}" class="h-10 w-full rounded">
                </div>
            </div>
        </div>
    </div>

    @if($resort)
    {{-- STEP 6: GALLERY --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-200">
        <div class="px-5 py-3 border-b border-slate-200 bg-slate-50"><h3 class="font-bold">6. Gallery</h3></div>
        <div class="p-5">
            <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-6 gap-3 mb-4" id="mediaGrid">
                @foreach($resort->media as $m)
                    <div class="relative group">
                        <img src="{{ asset('storage/' . $m->path) }}" class="w-full aspect-square object-cover rounded">
                        <button type="button" data-id="{{ $m->id }}" class="del-media absolute top-1 right-1 bg-red-600 text-white w-6 h-6 rounded-full text-xs hidden group-hover:block">×</button>
                    </div>
                @endforeach
            </div>
            <input type="file" id="mediaUpload" accept="image/*" class="text-sm">
            <p class="text-xs text-slate-500 mt-1">Pick a photo to upload. It will appear above once uploaded.</p>
        </div>
    </div>
    @endif

    <div class="flex flex-wrap items-center gap-3 sticky bottom-0 bg-white p-4 rounded-xl shadow border border-slate-200">
        <button type="submit" class="px-5 py-2.5 rounded-md bg-brand-600 text-white font-semibold hover:bg-brand-700">💾 Save</button>
        @if($resort && $resort->status === 'draft')
            <button type="button" onclick="document.getElementById('submitReview').submit()" class="px-5 py-2.5 rounded-md bg-emerald-600 text-white font-semibold hover:bg-emerald-700">📤 Submit for review</button>
        @endif
        <a href="{{ route('dashboard.resorts.index') }}" class="px-5 py-2.5 rounded-md border border-slate-300 hover:bg-slate-50">Back</a>
        @if($resort && $resort->status === 'draft')
            <button type="button" onclick="if(confirm('Delete this draft?')) document.getElementById('deleteResort').submit()" class="ms-auto text-sm text-red-600 hover:underline">Delete draft</button>
        @endif
    </div>
</form>

@if($resort && $resort->status === 'draft')
    <form id="submitReview" action="{{ route('dashboard.resorts.submit', $resort) }}" method="POST" class="hidden">@csrf</form>
    <form id="deleteResort" action="{{ route('dashboard.resorts.destroy', $resort) }}" method="POST" class="hidden">@csrf @method('DELETE')</form>
@endif

@push('scripts')
@if($resort)
<script>
document.getElementById('mediaUpload')?.addEventListener('change', function (e) {
    if (!e.target.files[0]) return;
    var fd = new FormData();
    fd.append('file', e.target.files[0]);
    fd.append('_token', '{{ csrf_token() }}');
    fetch('{{ route("dashboard.resorts.media.upload", $resort) }}', { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json()).then(function (r) { if (r.ok) location.reload(); });
});
document.querySelectorAll('.del-media').forEach(function (btn) {
    btn.addEventListener('click', function () {
        if (!confirm('Remove this image?')) return;
        var id = this.dataset.id;
        fetch('{{ url("/dashboard/resorts/" . $resort->id . "/media") }}/' + id, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' } })
            .then(r => r.json()).then(function () { location.reload(); });
    });
});
</script>
@endif
@endpush
@endsection
