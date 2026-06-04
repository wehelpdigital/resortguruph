@extends('layouts.public')

@section('title') Blog — {{ \App\Models\RgSetting::get('site_name', 'Resort Guru PH') }} @endsection

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <h1 class="text-4xl font-extrabold text-slate-900 mb-3">From the blog</h1>
    <p class="text-slate-600 mb-10">Travel tips, destination guides, and resort stories.</p>

    @if($posts->count() === 0)
        <p class="text-slate-500 text-center py-12">No posts yet.</p>
    @else
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($posts as $p)
                <article>
                    <a href="{{ route('blog.show', $p->slug) }}" class="block group">
                        <div class="aspect-[16/10] rounded-lg bg-slate-200 mb-3 overflow-hidden">
                            @if($p->cover_path)
                                <img src="{{ asset('storage/' . $p->cover_path) }}" alt="{{ $p->title }}" class="w-full h-full object-cover group-hover:scale-105 transition">
                            @endif
                        </div>
                        <h2 class="font-bold text-lg text-slate-900 group-hover:text-brand-600 mb-1">{{ $p->title }}</h2>
                        <p class="text-sm text-slate-600 line-clamp-3 mb-2">{{ $p->excerpt }}</p>
                        {{-- Published-date hidden per editorial direction —
                             the date was making evergreen pieces look stale.
                             $p->published_at is still set in the DB. --}}
                    </a>
                </article>
            @endforeach
        </div>
        <div class="mt-10">{{ $posts->links() }}</div>
    @endif
</div>
@endsection
