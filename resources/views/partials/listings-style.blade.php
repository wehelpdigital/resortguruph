{{-- Styles + photo fade for partials/listings-rows (printed once per page). --}}
@once
<style>
    .lx-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 1rem; margin-bottom: 1.5rem; }
    .lx-seal { flex-shrink: 0; display: inline-flex; align-items: center; gap: .65rem; border-radius: 999px; background: #fff; padding: .35rem 1rem .35rem .35rem; box-shadow: 0 10px 24px -16px rgba(11, 27, 43, .45), inset 0 0 0 1px rgba(240, 228, 207, .9); }
    .lx-seal img { width: 2.9rem; height: 2.9rem; filter: drop-shadow(0 4px 8px rgba(217, 119, 6, .35)); }
    .lx-seal strong { display: block; font-family: "Plus Jakarta Sans", Inter, sans-serif; font-size: .85rem; font-weight: 800; color: #0b1b2b; line-height: 1.2; }
    .lx-seal small { display: block; font-size: .72rem; color: #64748b; }
    @media (max-width: 639px) { .lx-head { flex-direction: column; } .lx-seal { padding-right: .85rem; } }

    .lx-top { position: relative; display: grid; grid-template-columns: minmax(0, 1fr); border-radius: 1.6rem; overflow: hidden; background: #fff; box-shadow: 0 34px 70px -40px rgba(11, 27, 43, .55), 0 0 0 2px #ffc74a; }
    @media (min-width: 900px) { .lx-top { grid-template-columns: minmax(0, 1.05fr) minmax(0, 1fr); } }
    .lx-top__media { position: relative; width: 100%; aspect-ratio: 16 / 11; background: linear-gradient(135deg, var(--tint, #0f3a5c), #0b1b2b); overflow: hidden; isolation: isolate; }
    @media (min-width: 900px) { .lx-top__media { aspect-ratio: auto; min-height: 22rem; height: 100%; } }
    .lx-top__media img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; opacity: 0; transition: opacity 1.2s ease; }
    .lx-top__media img.is-on { opacity: 1; }
    .lx-top__media::after { content: ""; position: absolute; inset: 0; z-index: 1; background: linear-gradient(180deg, rgba(7, 18, 29, .35) 0%, rgba(7, 18, 29, 0) 35%, rgba(7, 18, 29, 0) 70%, rgba(7, 18, 29, .45) 100%); pointer-events: none; }
    .lx-top__noimg, .lx-card__media .lx-top__noimg { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; font-family: "Plus Jakarta Sans", Inter, sans-serif; font-weight: 800; font-size: 4rem; color: rgba(255, 255, 255, .35); }
    .lx-ribbon { position: absolute; top: 1rem; left: 1rem; z-index: 2; display: inline-flex; align-items: center; gap: .4rem; border-radius: 999px; padding: .45rem .9rem; font-size: .78rem; font-weight: 800; letter-spacing: .04em; color: #0b1b2b; background: linear-gradient(135deg, #ffe08a, #ffc74a 55%, #f5a524); box-shadow: 0 10px 22px -10px rgba(217, 119, 6, .8); }
    .lx-ribbon svg { width: 1rem; height: 1rem; }
    .lx-top__count { position: absolute; right: 1rem; bottom: 1rem; z-index: 2; border-radius: 999px; background: rgba(7, 18, 29, .6); color: #fff; font-size: .72rem; font-weight: 700; padding: .3rem .65rem; backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px); }
    .lx-top__body { padding: 1.4rem 1.4rem 1.5rem; display: flex; flex-direction: column; }
    @media (min-width: 640px) { .lx-top__body { padding: 1.9rem 2rem 2rem; } }
    .lx-top__kicker { font-size: .7rem; font-weight: 800; letter-spacing: .16em; text-transform: uppercase; color: #d97706; }
    .lx-top__name { margin-top: .35rem; font-family: "Plus Jakarta Sans", Inter, sans-serif; font-weight: 800; font-size: clamp(1.5rem, 2.6vw, 2rem); line-height: 1.12; letter-spacing: -0.025em; color: #0b1b2b; }
    .lx-top__name a:hover, .lx-card__name a:hover { color: #0f5788; }
    .lx-place { margin-top: .45rem; display: inline-flex; align-items: center; gap: .3rem; font-size: .9rem; color: #475569; text-decoration: underline; text-decoration-color: #cbd5e1; text-decoration-style: dotted; text-underline-offset: 3px; }
    .lx-place:hover { color: #0f5788; }
    .lx-place svg { color: #ef5b3a; }
    .lx-place--sm { font-size: .82rem; margin-top: .3rem; }
    .lx-score { flex-shrink: 0; display: flex; align-items: center; gap: .55rem; }
    .lx-score__num { display: inline-flex; align-items: center; justify-content: center; width: 3.1rem; height: 3.1rem; border-radius: .95rem .95rem .95rem .3rem; background: #0b1b2b; color: #fff; font-family: "Plus Jakarta Sans", Inter, sans-serif; font-weight: 800; font-size: 1.15rem; }
    .lx-score__txt { font-weight: 800; color: #0b1b2b; font-size: .9rem; line-height: 1.2; }
    .lx-score__txt small { display: block; font-weight: 500; font-size: .72rem; color: #64748b; }
    @media (max-width: 479px) { .lx-score__txt { display: none; } }
    .lx-top__tagline { margin-top: 1rem; font-size: 1.05rem; font-weight: 600; color: #0b1b2b; }
    .lx-top__desc { margin-top: .5rem; color: #475569; line-height: 1.65; font-size: .95rem; }
    .lx-chips { margin-top: 1rem; display: flex; flex-wrap: wrap; gap: .4rem; }
    .lx-chips li { display: inline-flex; align-items: center; gap: .3rem; border-radius: 999px; background: #f1f5f9; color: #334155; font-size: .78rem; font-weight: 600; padding: .3rem .7rem; }
    .lx-chips li svg { color: #059669; }
    .lx-chips .lx-chips__price { background: #fff3ef; color: #d9432a; }
    .lx-chips--sm { margin-top: .7rem; }
    .lx-chips--sm li { font-size: .72rem; padding: .22rem .6rem; }
    .lx-quote { margin-top: 1.1rem; border-radius: 1rem; background: #fbf6ee; border: 1px solid #f0e4cf; padding: .85rem 1rem; }
    .lx-quote__stars { display: inline-flex; gap: .1rem; color: #f5a524; }
    .lx-quote__stars svg { fill: currentColor; }
    .lx-quote blockquote { margin-top: .3rem; font-size: .9rem; color: #1e293b; line-height: 1.55; }
    .lx-quote figcaption { margin-top: .35rem; font-size: .75rem; color: #64748b; font-weight: 600; }
    .lx-top__ctas { margin-top: auto; padding-top: 1.3rem; display: flex; flex-wrap: wrap; align-items: center; gap: .5rem; }
    .lx-sponsored { margin-left: auto; font-size: .65rem; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; color: #94a3b8; }

    .lx-grid { margin-top: 1.1rem; display: grid; gap: 1rem; }
    @media (min-width: 768px) { .lx-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    .lx-card { display: grid; grid-template-columns: 8.5rem minmax(0, 1fr); border-radius: 1.3rem; overflow: hidden; background: #fff; border: 1px solid #ece3d2; transition: box-shadow .25s ease, transform .25s ease; }
    @media (min-width: 480px) { .lx-card { grid-template-columns: 11rem minmax(0, 1fr); } }
    .lx-card:hover { box-shadow: 0 22px 44px -28px rgba(11, 27, 43, .45); transform: translateY(-2px); }
    .lx-card__media { position: relative; min-height: 9.5rem; background: linear-gradient(135deg, var(--tint, #0f3a5c), #0b1b2b); overflow: hidden; }
    .lx-card__media img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; }
    .lx-rank { position: absolute; top: .6rem; left: .6rem; border-radius: 999px; background: #fff; color: #0b1b2b; font-family: "Plus Jakarta Sans", Inter, sans-serif; font-weight: 800; font-size: .75rem; padding: .2rem .55rem; box-shadow: 0 6px 14px -6px rgba(0, 0, 0, .4); }
    .lx-card__body { padding: .95rem 1rem 1rem; display: flex; flex-direction: column; min-width: 0; }
    .lx-card__name { font-family: "Plus Jakarta Sans", Inter, sans-serif; font-weight: 800; font-size: 1.02rem; line-height: 1.25; color: #0b1b2b; }
    .lx-mini { flex-shrink: 0; display: inline-flex; align-items: center; gap: .2rem; border-radius: .5rem; background: #0b1b2b; color: #fff; font-size: .75rem; font-weight: 800; padding: .2rem .45rem; }
    .lx-mini svg { fill: #ffc74a; }
    .lx-card__tagline { margin-top: .45rem; font-size: .85rem; color: #475569; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .lx-card__ctas { margin-top: auto; padding-top: .8rem; display: flex; flex-wrap: wrap; align-items: center; gap: .5rem; }
    @media (max-width: 479px) { .lx-card__ctas .lx-sponsored { margin-left: 0; width: 100%; } .lx-card__name { font-size: .95rem; } }
    .lx-card__ctas .rg-btn { padding: .5rem .9rem; font-size: .82rem; }
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (window.__lxFade || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    window.__lxFade = 1;
    document.querySelectorAll('[data-lx-fade]').forEach(function (box) {
        var imgs = box.querySelectorAll('img'), i = 0;
        if (imgs.length < 2) return;
        setInterval(function () { if (document.hidden) return; imgs[i].classList.remove('is-on'); i = (i + 1) % imgs.length; imgs[i].classList.add('is-on'); }, 4500);
    });
});
</script>
@endonce
