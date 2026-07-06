{{-- Blog post card grid. Each post is a self-contained card (cover +
     title + excerpt + read-more), no dates. Cover URLs are host-relative
     so they load on any host. --}}
@if($posts->count() === 0)
    <div class="rounded-2xl border border-dashed border-slate-200 py-16 text-center text-slate-500">
        No articles yet. Check back soon.
    </div>
@else
    <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        @foreach($posts as $p)
            <article class="group flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:border-slate-300 hover:shadow-xl">
                <a href="{{ route('blog.show', $p->slug) }}" class="flex h-full flex-col no-underline">
                    <div class="aspect-[16/10] overflow-hidden bg-gradient-to-br from-slate-100 to-slate-200">
                        @if($p->cover_path)
                            <img src="{{ '/storage/' . ltrim($p->cover_path, '/') }}" alt="{{ $p->title }}" loading="lazy" class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
                        @else
                            <div class="flex h-full w-full items-center justify-center text-slate-300">
                                <svg class="h-12 w-12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="18" height="18" x="3" y="3" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.1-3.1a2 2 0 0 0-2.8 0L6 21"/></svg>
                            </div>
                        @endif
                    </div>
                    <div class="flex flex-1 flex-col p-5">
                        <h3 class="mb-2 line-clamp-2 text-lg font-bold leading-snug text-slate-900 transition-colors group-hover:text-brand-600">{{ $p->title }}</h3>
                        @if($p->excerpt)
                            <p class="line-clamp-3 flex-1 text-sm leading-relaxed text-slate-600">{{ $p->excerpt }}</p>
                        @endif
                        <span class="mt-4 inline-flex items-center gap-1.5 text-sm font-semibold text-brand-600">
                            Read article
                            <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
                        </span>
                    </div>
                </a>
            </article>
        @endforeach
    </div>
    @if($posts->hasPages())
        <div class="mt-12">{{ $posts->links('pagination.theme') }}</div>
    @endif
@endif
