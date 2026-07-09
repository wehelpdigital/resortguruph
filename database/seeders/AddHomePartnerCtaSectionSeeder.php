<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Adds a "List your tourist business for free" call-to-action section to
 * the homepage block stream. It is the friendly entry point to the partner
 * program: free listing as the first step to becoming a verified partner,
 * with the happy-traveler market photo and the business-type chips.
 *
 * Rendered as a full-bleed custom_html block placed right after the
 * existing home_partner_perks block so the partner story reads together,
 * and its CTAs point at the new /become-a-partner landing page.
 *
 * Idempotent: any prior copy is detected by the data-rg-partner-cta marker
 * and removed before re-inserting, so it is safe to re-run.
 */
class AddHomePartnerCtaSectionSeeder extends Seeder
{
    public function run(): void
    {
        $page = DB::table('rg_static_pages')->where('slug', 'home')->first();
        if (!$page) {
            $this->command->warn('  home page not found, skipping');
            return;
        }

        // Idempotency: drop any prior copy of this section.
        DB::table('rg_content_blocks')
            ->where('owner_type', 'static_page')
            ->where('owner_id', $page->id)
            ->where('block_type', 'custom_html')
            ->where('payload_json', 'like', '%data-rg-partner-cta%')
            ->delete();

        // Place it right after the existing partner-perks block (fallback:
        // just before the FAQ, else at the end of the stream).
        $anchor = DB::table('rg_content_blocks')
            ->where('owner_id', $page->id)
            ->where('block_type', 'home_partner_perks')
            ->value('sort_order');
        if ($anchor === null) {
            $anchor = DB::table('rg_content_blocks')
                ->where('owner_id', $page->id)
                ->where('block_type', 'home_faq')
                ->value('sort_order');
        }
        $sortOrder = $anchor !== null ? (int) $anchor : ((int) DB::table('rg_content_blocks')->where('owner_id', $page->id)->max('sort_order') + 1);

        $html = $this->sectionHtml();

        DB::table('rg_content_blocks')->insert([
            'owner_type' => 'static_page',
            'owner_id' => $page->id,
            'sort_order' => $sortOrder,
            'block_type' => 'custom_html',
            'payload_json' => json_encode(['html' => $html], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info("  home partner CTA section inserted (page {$page->id}, sort {$sortOrder})");
    }

    private function sectionHtml(): string
    {
        $accent = 'font-weight:400;color:#c0392b;font-size:1.3em;line-height:1;display:inline-block';
        $chips = [
            'Tour Guides', 'Tour Operators', 'Massage &amp; Spa', 'Resorts', 'Hotels',
            'Surf Schools', 'Dive Shops', 'Homestays', 'Restaurants', 'Anything tourism',
        ];
        $chipHtml = '';
        foreach ($chips as $c) {
            $chipHtml .= '<span class="inline-flex items-center rounded-full bg-white border border-slate-200 px-3.5 py-1.5 text-sm font-semibold text-slate-700 shadow-sm">' . $c . '</span>';
        }

        return <<<HTML
<section data-rg-partner-cta style="margin-left:calc(50% - 50vw);margin-right:calc(50% - 50vw);width:100vw;max-width:100vw">
  <div style="background:linear-gradient(135deg,#eaf2f8 0%,#ffffff 48%,#ecfdf5 100%)">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-20">
      <div class="grid lg:grid-cols-2 gap-10 lg:gap-14 items-center">
        <div class="relative order-2 lg:order-1">
          <div class="absolute -inset-3 rounded-[2rem] blur-2xl" style="background:linear-gradient(to top right,rgba(41,128,185,.22),rgba(16,185,129,.22))" aria-hidden="true"></div>
          <img src="/storage/rg-media/feature-friends.webp" width="1200" height="670" loading="lazy"
               alt="Happy travelers skating and hanging out at a Philippine beach night market lined with local food stalls"
               class="relative w-full rounded-3xl shadow-2xl ring-1 ring-black/5 object-cover">
          <div class="hidden sm:flex absolute -bottom-5 -left-5 items-center gap-3 bg-white rounded-2xl shadow-xl ring-1 ring-black/5 px-4 py-3">
            <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
              <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 12V8H6a2 2 0 0 1 0-4h12v4"/><path d="M4 6v12a2 2 0 0 0 2 2h14v-4"/><path d="M18 12a2 2 0 0 0 0 4h4v-4z"/></svg>
            </div>
            <div class="leading-tight"><div class="text-sm font-bold text-slate-900">List for Free</div><div class="text-xs text-slate-500">No credit card</div></div>
          </div>
        </div>
        <div class="order-1 lg:order-2">
          <div class="text-[11px] uppercase tracking-[0.2em] font-bold text-brand-700 mb-3">For Tourism Businesses</div>
          <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 leading-tight mb-4">Have a tourist business? <span class="font-brand" style="$accent">List it for free</span></h2>
          <p class="text-lg text-slate-600 leading-relaxed mb-6">Tour guide, tour operator, massage and spa, resort, hotel, surfing school, or anything travelers look for. Get listed in our directory for free and start showing up while guests plan their trip. It is the first step to becoming a verified partner.</p>
          <div class="flex flex-wrap gap-2 mb-7">$chipHtml</div>
          <div class="flex flex-wrap items-center gap-3">
            <a href="/become-a-partner" class="inline-flex items-center gap-2 px-6 py-3.5 rounded-full bg-brand-600 text-white font-bold text-base hover:bg-brand-700 shadow-lg shadow-brand-600/20 transition">
              Become a Partner for Free
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
            </a>
            <a href="/become-a-partner" class="inline-flex items-center gap-2 px-6 py-3.5 rounded-full bg-white text-slate-800 font-bold text-base border border-slate-200 hover:border-slate-300 hover:bg-slate-50 transition">See how it works</a>
          </div>
          <div class="mt-5 inline-flex items-center gap-2 text-sm text-slate-500 font-medium">
            <svg class="w-4 h-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round"><path d="m9 12 2 2 4-4"/><circle cx="12" cy="12" r="9"/></svg>
            First step to the We Highly Recommend badge
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
HTML;
    }
}
