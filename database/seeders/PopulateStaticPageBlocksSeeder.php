<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Migrates legacy rg_static_pages.content_html → blocks AND generates blocks
 * for empty system pages (home, blog-index, register-page, login-page, contact-page).
 * Idempotent — skips pages that already have blocks.
 */
class PopulateStaticPageBlocksSeeder extends Seeder
{
    public function run(): void
    {
        $pages = DB::table('rg_static_pages')->get();
        $migrated = 0;
        $now = now();

        foreach ($pages as $page) {
            $hasBlocks = DB::table('rg_content_blocks')
                ->where('owner_type', 'static_page')
                ->where('owner_id', $page->id)
                ->count();
            if ($hasBlocks > 0) continue;

            $blocks = $this->blocksForSlug($page->slug, $page);
            if (empty($blocks)) continue;

            foreach ($blocks as $idx => $block) {
                DB::table('rg_content_blocks')->insert([
                    'owner_type' => 'static_page',
                    'owner_id' => $page->id,
                    'sort_order' => $idx + 1,
                    'block_type' => $block['type'],
                    'payload_json' => json_encode($block['payload'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
            $this->ensureMeta($page, $now);
            $migrated++;
        }

        $this->command->info("Static / system pages migrated: $migrated");
    }

    private function blocksForSlug(string $slug, $page): array
    {
        $img = fn($seed, $w = 1600, $h = 900) => "https://picsum.photos/seed/{$seed}/{$w}/{$h}";

        // For pages with existing content_html, parse + convert
        if (in_array($slug, ['about', 'contact', 'terms', 'privacy']) && !empty($page->content_html)) {
            return $this->fromLegacyHtml($page->content_html, $slug, $img);
        }

        switch ($slug) {
            case 'home': return $this->homeBlocks($img);
            case 'about': return $this->aboutBlocks($img);
            case 'contact':
            case 'contact-page':
                return $this->contactBlocks($img);
            case 'terms': return $this->termsBlocks($img);
            case 'privacy': return $this->privacyBlocks($img);
            case 'blog-index': return $this->blogIndexBlocks($img);
            case 'register-page': return $this->registerBlocks($img);
            case 'login-page': return $this->loginBlocks($img);
            default: return [];
        }
    }

    private function fromLegacyHtml(string $html, string $slug, callable $img): array
    {
        $blocks = [];
        $blocks[] = ['type' => 'image', 'payload' => [
            'src' => $img("static-{$slug}-hero"),
            'alt' => ucwords(str_replace('-', ' ', $slug)),
            'caption' => '',
            'align' => 'center',
        ]];

        // Split by h2
        $parts = preg_split('/<h2[^>]*>(.*?)<\/h2>/i', $html, -1, PREG_SPLIT_DELIM_CAPTURE);
        if (isset($parts[0]) && trim(strip_tags($parts[0])) !== '') {
            $blocks[] = ['type' => 'rich_text', 'payload' => ['html' => trim($parts[0])]];
        }
        for ($i = 1; $i < count($parts); $i += 2) {
            $h2 = strip_tags($parts[$i] ?? '');
            if ($h2 === '') continue;
            $blocks[] = ['type' => 'heading', 'payload' => ['level' => 'h2', 'text' => $h2]];
            $body = trim($parts[$i + 1] ?? '');
            if ($body !== '') {
                $blocks[] = ['type' => 'rich_text', 'payload' => ['html' => $body]];
            }
        }
        return $blocks;
    }

    private function homeBlocks(callable $img): array
    {
        return [
            ['type' => 'heading', 'payload' => ['level' => 'h2', 'text' => 'Why Resort Guru PH']],
            ['type' => 'rich_text', 'payload' => ['html' => '<p>Resort Guru PH is the curated directory of resorts, hotels, and Airbnb stays across the Philippines. Every destination page is hand-edited for the keyword travelers actually search for, so finding a place to stay in Bulacan, Tagaytay, El Nido, or Davao is a single click away.</p><p>We grade every property on real factors: location, amenities, price range, and verified owner contact. No paid reviews, no fake ratings.</p>']],
            ['type' => 'image', 'payload' => [
                'src' => $img('home-about-rgph', 1400, 700),
                'alt' => 'Resort Guru PH Philippine destinations',
                'caption' => '',
                'align' => 'center',
            ]],
            ['type' => 'heading', 'payload' => ['level' => 'h2', 'text' => 'For resort owners']],
            ['type' => 'rich_text', 'payload' => ['html' => '<p>List your property in minutes. Set your branding colors, upload photos, write your story, then bid Gold Points to be featured on the keyword pages your future guests are already searching.</p><p>Pay only when guests are looking, not on a flat monthly fee.</p>']],
            ['type' => 'cta', 'payload' => [
                'headline' => 'Ready to list?',
                'text' => 'Create your first property in under 5 minutes.',
                'button_text' => 'Start listing',
                'button_url' => '/register',
                'style' => 'primary',
            ]],
        ];
    }

    private function aboutBlocks(callable $img): array
    {
        return [
            ['type' => 'heading', 'payload' => ['level' => 'h2', 'text' => 'About Resort Guru PH']],
            ['type' => 'rich_text', 'payload' => ['html' => '<p>Resort Guru PH is built by travelers, for travelers. We started as a side project to track the best private pool resorts in Cavite and grew into a full directory of stays across every major destination in the Philippines.</p><p>Every page on this site is curated by humans. We do not scrape, we do not auto-generate. Each destination guide is researched, written, and updated based on direct visits and verified owner submissions.</p>']],
            ['type' => 'image', 'payload' => [
                'src' => $img('about-team-rgph', 1400, 700),
                'alt' => 'About Resort Guru PH',
                'caption' => '',
                'align' => 'center',
            ]],
            ['type' => 'heading', 'payload' => ['level' => 'h2', 'text' => 'Our promise']],
            ['type' => 'rich_text', 'payload' => ['html' => '<p>No pay-to-win rankings. No fake reviews. Honest content, fair pricing for property owners, real guest interest signals via search traffic and inquiry tracking.</p>']],
            ['type' => 'cta', 'payload' => [
                'headline' => 'Questions or partnerships?',
                'text' => 'Reach us via the contact form. We reply within one business day.',
                'button_text' => 'Contact us',
                'button_url' => '/contact',
                'style' => 'primary',
            ]],
        ];
    }

    private function contactBlocks(callable $img): array
    {
        return [
            ['type' => 'heading', 'payload' => ['level' => 'h2', 'text' => 'Get in touch']],
            ['type' => 'rich_text', 'payload' => ['html' => '<p>For property listings, account help, billing, or general questions, use the contact form below. We respond within one business day, Monday to Friday.</p><p>For urgent matters affecting an active listing, please include your property name or owner email so we can find your account quickly.</p>']],
            ['type' => 'two_column', 'payload' => [
                'left_html' => '<h3>For property owners</h3><ul><li>Listing approval status</li><li>Gold Points / billing questions</li><li>Bid refund requests</li><li>Profile or media issues</li></ul>',
                'right_html' => '<h3>For travelers</h3><ul><li>Booking inquiries (we forward to the property directly)</li><li>Report inaccurate info</li><li>General travel questions</li><li>Press &amp; partnerships</li></ul>',
            ]],
        ];
    }

    private function termsBlocks(callable $img): array
    {
        return [
            ['type' => 'heading', 'payload' => ['level' => 'h2', 'text' => 'Terms of Service']],
            ['type' => 'rich_text', 'payload' => ['html' => '<p>By using Resort Guru PH you agree to these terms. The directory is provided as-is. Prices and availability are sourced from listed property owners and may change without notice. Always confirm directly with the property before booking.</p>']],
            ['type' => 'heading', 'payload' => ['level' => 'h2', 'text' => 'Property listings']],
            ['type' => 'rich_text', 'payload' => ['html' => '<p>Owners are responsible for the accuracy of their listings. We reserve the right to suspend or remove listings that violate Philippine tourism regulations, misrepresent the property, or contain offensive content.</p>']],
            ['type' => 'heading', 'payload' => ['level' => 'h2', 'text' => 'Gold Points and bidding']],
            ['type' => 'rich_text', 'payload' => ['html' => '<p>Gold Points purchased via GCash are non-refundable once approved and credited. Active listings run for their full duration regardless of rank changes. We do not refund unused bid duration when an owner is outbid.</p>']],
            ['type' => 'heading', 'payload' => ['level' => 'h2', 'text' => 'Liability']],
            ['type' => 'rich_text', 'payload' => ['html' => '<p>Resort Guru PH is not party to any booking transaction between travelers and property owners. We facilitate discovery only. Any disputes regarding a stay must be resolved directly with the property.</p>']],
        ];
    }

    private function privacyBlocks(callable $img): array
    {
        return [
            ['type' => 'heading', 'payload' => ['level' => 'h2', 'text' => 'Privacy Policy']],
            ['type' => 'rich_text', 'payload' => ['html' => '<p>We respect your privacy. This policy explains what we collect, how we use it, and how to control your data.</p>']],
            ['type' => 'heading', 'payload' => ['level' => 'h2', 'text' => 'What we collect']],
            ['type' => 'rich_text', 'payload' => ['html' => '<p>Public site visitors: anonymized pageview counts only, with bot filtering. We do not run third-party tracking pixels.</p><p>Registered property owners: name, email, phone, password (hashed), property submissions, Gold Points transactions, GCash payment screenshots (deleted after 90 days of approval).</p>']],
            ['type' => 'heading', 'payload' => ['level' => 'h2', 'text' => 'Your rights']],
            ['type' => 'rich_text', 'payload' => ['html' => '<p>Under the Philippine Data Privacy Act of 2012, you may request access, correction, or deletion of your account data at any time. Email us to start the process.</p>']],
        ];
    }

    private function blogIndexBlocks(callable $img): array
    {
        return [
            ['type' => 'heading', 'payload' => ['level' => 'h2', 'text' => 'Stories from the road']],
            ['type' => 'rich_text', 'payload' => ['html' => '<p>Trip reports, destination deep-dives, and resort owner interviews. Browse the latest posts below.</p>']],
        ];
    }

    private function registerBlocks(callable $img): array
    {
        return [
            ['type' => 'heading', 'payload' => ['level' => 'h2', 'text' => 'Why list with Resort Guru PH']],
            ['type' => 'rich_text', 'payload' => ['html' => '<ul><li><strong>Real search traffic.</strong> Every keyword page already ranks for queries your guests use.</li><li><strong>Pay only what you want.</strong> No monthly fees — bid Gold Points to climb rankings.</li><li><strong>Full ownership.</strong> Your branding colors, photos, contact details, and resort story are yours to edit any time.</li></ul>']],
        ];
    }

    private function loginBlocks(callable $img): array
    {
        return [
            ['type' => 'heading', 'payload' => ['level' => 'h2', 'text' => 'Welcome back']],
            ['type' => 'rich_text', 'payload' => ['html' => '<p>Sign in to manage your listings, top up Gold Points, or update your property profile.</p>']],
        ];
    }

    private function ensureMeta($page, $now): void
    {
        $updates = [];
        if (empty($page->meta_title)) {
            $updates['meta_title'] = $page->title;
        }
        if (empty($page->meta_description)) {
            $defaults = [
                'home' => 'Compare resorts, hotels, and Airbnb stays across the Philippines on Resort Guru PH.',
                'about' => 'About Resort Guru PH — the curated Philippines resort and hotel directory.',
                'contact' => 'Get in touch with the Resort Guru PH team.',
                'terms' => 'Terms of service for using Resort Guru PH.',
                'privacy' => 'How Resort Guru PH handles your personal data.',
                'blog-index' => 'Travel tips and destination guides from across the Philippines.',
                'register-page' => 'List your resort, hotel, or Airbnb on Resort Guru PH.',
                'login-page' => 'Sign in to your Resort Guru PH owner account.',
                'contact-page' => 'Contact Resort Guru PH — replies within one business day.',
            ];
            $updates['meta_description'] = $defaults[$page->slug] ?? 'Resort Guru PH — the Philippines resort directory.';
        }
        if (count($updates) > 0) {
            $updates['updated_at'] = $now;
            DB::table('rg_static_pages')->where('id', $page->id)->update($updates);
        }
    }
}
