<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Makes the /become-a-partner page block-driven so it is editable in the
 * mother-app block builder. Creates (or updates) its rg_static_pages row
 * and seeds the stream with the partner_* custom blocks plus reused
 * home_faq / home_cta_band, porting the exact content + design of the
 * original hardcoded landing view.
 *
 * Idempotent: the page row is upserted and its blocks are replaced on
 * every run.
 */
class SeedBecomePartnerBlocksSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $page = DB::table('rg_static_pages')->where('slug', 'become-a-partner')->first();
        $pageData = [
            'title' => 'Become a Partner',
            'meta_title' => 'Become a Partner · Join the Tourist Guide PH Directory (Free)',
            'meta_description' => 'List your Philippine tourism business on Tourist Guide PH for free. Tour guides, hotels, resorts, restaurants, spas, and surf schools get found by traveling guests. The first step to becoming a verified partner.',
            'is_published' => 1,
            'updated_at' => $now,
        ];
        if ($page) {
            DB::table('rg_static_pages')->where('id', $page->id)->update($pageData);
            $pageId = $page->id;
        } else {
            $pageId = DB::table('rg_static_pages')->insertGetId(array_merge($pageData, [
                'slug' => 'become-a-partner',
                'content_html' => '',
                'created_at' => $now,
            ]));
        }

        DB::table('rg_content_blocks')->where('owner_type', 'static_page')->where('owner_id', $pageId)->delete();

        $blocks = $this->blocks();
        $rows = [];
        foreach ($blocks as $i => $b) {
            $rows[] = [
                'owner_type' => 'static_page',
                'owner_id' => $pageId,
                'sort_order' => $i + 1,
                'block_type' => $b['type'],
                'payload_json' => json_encode($b['payload'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        DB::table('rg_content_blocks')->insert($rows);

        $this->command->info('  become-a-partner: page ' . $pageId . ' seeded with ' . count($rows) . ' blocks');
    }

    private function blocks(): array
    {
        return [
            ['type' => 'partner_hero', 'payload' => [
                'eyebrow' => 'Partner Directory · Free to Join',
                'title' => 'Join Our Community, {{accent}}Become a Partner{{/accent}}',
                'subhead' => 'Run a tourism business in the Philippines? List it on Tourist Guide PH for free and get in front of the travelers already planning their trip here. It is the first step to becoming a verified partner.',
                'cta_primary_label' => 'List Your Business for Free',
                'cta_primary_url' => '/register',
                'cta_secondary_label' => 'Talk to Us First',
                'cta_secondary_url' => '/contact',
                'trust_points' => ['Free to list', 'No credit card', 'Verified badge once approved'],
                'image' => 'rg-media/business-with-badge.webp',
                'image_alt' => 'A smiling cafe owner serving coffee, carrying the Tourist Guide PH We Highly Recommend badge',
                'badge_title' => 'Verified Partner',
                'badge_subtitle' => 'We Highly Recommend',
            ]],
            ['type' => 'partner_audience', 'payload' => [
                'eyebrow' => 'Who Can Join',
                'heading' => 'Built for *every kind* of tourism business',
                'subhead' => 'If a traveler would look for you, you belong in the directory. Big or small, solo or a whole team.',
                'items' => [
                    ['icon' => 'M12 2a5 5 0 0 1 5 5c0 3.5-5 11-5 11S7 10.5 7 7a5 5 0 0 1 5-5z M12 7.5a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z', 'title' => 'Tour Guides', 'subtitle' => 'Solo and licensed'],
                    ['icon' => 'M3 7h13l2 3h3v6h-2 M3 7v9h2 M5 16a2 2 0 1 0 4 0 2 2 0 0 0-4 0z M15 16a2 2 0 1 0 4 0 2 2 0 0 0-4 0z', 'title' => 'Tour Operators', 'subtitle' => 'Day trips and packages'],
                    ['icon' => 'M3 21V5l9-3 9 3v16 M9 21v-5h6v5 M8 8h.01 M12 8h.01 M16 8h.01 M8 12h.01 M12 12h.01 M16 12h.01', 'title' => 'Hotels & Resorts', 'subtitle' => 'Stays of every size'],
                    ['icon' => 'M3 11l9-7 9 7 M5 10v10h14V10 M10 20v-6h4v6', 'title' => 'Homestays & Rentals', 'subtitle' => 'Airbnb and BnB'],
                    ['icon' => 'M4 3v7a3 3 0 0 0 6 0V3 M7 3v18 M17 3c-1.5 0-3 1.8-3 4.5S15.5 12 17 12s3 3 3 3v6', 'title' => 'Restaurants & Cafes', 'subtitle' => 'Food and coffee spots'],
                    ['icon' => 'M12 3a3 3 0 1 1 0 6 3 3 0 0 1 0-6z M4 21c1.5-4 4.5-6 8-6s6.5 2 8 6', 'title' => 'Massage & Spa', 'subtitle' => 'Wellness and healing'],
                    ['icon' => 'M2 18c2 0 2-1.5 4-1.5S8 18 10 18s2-1.5 4-1.5 2 1.5 4 1.5 M6 15c4-8 9-9 14-8-1 5-4 9-11 9', 'title' => 'Surf & Dive Schools', 'subtitle' => 'Lessons and gear'],
                    ['icon' => 'M5 11l1.5-4.5A2 2 0 0 1 8.4 5h7.2a2 2 0 0 1 1.9 1.5L19 11m0 0v6H5v-6m14 0H5 M7.5 14h.01 M16.5 14h.01', 'title' => 'Transport & Car Hire', 'subtitle' => 'Vans, boats, rentals'],
                    ['icon' => 'M12 2v4 M12 18v4 M2 12h4 M18 12h4 M12 8a4 4 0 1 0 0 8 4 4 0 0 0 0-8z', 'title' => 'Anything Tourism', 'subtitle' => 'If guests look for it'],
                ],
            ]],
            ['type' => 'partner_steps', 'payload' => [
                'eyebrow' => 'How It Works',
                'heading' => 'From a free listing to a *verified partner*',
                'steps' => [
                    ['number' => '1', 'color' => 'brand', 'title' => 'List for free', 'body' => 'Send us your details and we set up your listing at no cost. This is your first step in.'],
                    ['number' => '2', 'color' => 'amber', 'title' => 'Get discovered', 'body' => 'Your listing sits on pages built to rank, so travelers find you while they plan their trip.'],
                    ['number' => '3', 'color' => 'emerald', 'title' => 'Become verified', 'body' => 'Meet our simple standards and earn the We Highly Recommend badge that guests trust.'],
                ],
            ]],
            ['type' => 'partner_badge', 'payload' => [
                'eyebrow' => 'The Verified Badge',
                'eyebrow_color' => 'amber',
                'heading' => 'The *We Highly Recommend* badge',
                'body' => 'Once your place is approved, you carry our We Highly Recommend badge on your listing and at your storefront. It tells travelers that Tourist Guide PH has checked your business and stands behind it, so guests book with trust.',
                'image' => 'rg-media/business-with-badge.webp',
                'image_alt' => 'The We Highly Recommend badge on a partner cafe listing',
                'image_side' => 'left',
                'points' => ['A clear mark of trust on your listing', 'A window sticker for your storefront', 'A badge number guests can look up'],
            ]],
            ['type' => 'partner_perks', 'payload' => [
                'eyebrow' => 'Why Partner With Us',
                'heading' => 'Everything you get, from day one',
                'subhead' => '',
                'perks' => [
                    ['icon' => 'M11 3a8 8 0 1 0 5.3 14 M21 21l-4.3-4.3 M11 7a4 4 0 0 0-4 4', 'title' => 'Get found on Google', 'body' => 'Your listing lives on pages built to rank for what travelers actually search.'],
                    ['icon' => 'M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2 M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z M23 21v-2a4 4 0 0 0-3-3.9 M16 3.1a4 4 0 0 1 0 7.8', 'title' => 'Reach real travelers', 'body' => 'Guests planning trips across the islands see your place while they decide.'],
                    ['icon' => 'M4 4h16v16H4z M4 9h16 M9 9v11', 'title' => 'Your own listing page', 'body' => 'Photos, story, map, and contact, all on a page that is yours to shape.'],
                    ['icon' => 'm9 12 2 2 4-4 M12 3l7 3v6c0 4-3 7-7 9-4-2-7-5-7-9V6z', 'title' => 'A badge guests trust', 'body' => 'Verified partners carry the We Highly Recommend mark that reassures guests.'],
                    ['icon' => 'M12 2v20 M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6', 'title' => 'Free to start', 'body' => 'Listing costs nothing. Set it up today and grow from there.'],
                    ['icon' => 'M3 3v18h18 M7 15l4-4 3 3 5-6', 'title' => 'Grow your bookings', 'body' => 'Turn the traffic we bring in into calls, messages, and paying guests.'],
                ],
            ]],
            ['type' => 'home_faq', 'payload' => [
                'heading' => 'Questions partners ask',
                'subhead' => '',
                'faqs' => [
                    ['question' => 'Is it really free to list?', 'answer' => 'Yes. Setting up your listing on the directory costs nothing. You can start today with no credit card. The verified partner badge is the next step once your place is checked.'],
                    ['question' => 'Who can become a partner?', 'answer' => 'Any legitimate tourism business or individual in the Philippines. Tour guides, tour operators, hotels, resorts, homestays, restaurants, cafes, spas, surf and dive schools, transport, and more.'],
                    ['question' => 'How do I become verified?', 'answer' => 'List first, then apply for the We Highly Recommend badge. We do a simple check of your place so travelers know Tourist Guide PH stands behind it.'],
                    ['question' => 'How will travelers find me?', 'answer' => 'Your listing sits inside our directory and on SEO pages that already pull in travelers planning their trips. That is the traffic you tap into.'],
                ],
            ]],
            ['type' => 'home_cta_band', 'payload' => [
                'heading' => 'Ready to be found by your next guest?',
                'body' => 'Join the directory for free and start showing up while travelers plan their Philippine trip. Becoming a verified partner starts with one simple listing.',
                'cta' => ['label' => 'List Your Business for Free', 'url' => '/register'],
                'accent' => 'brand',
            ]],
        ];
    }
}
