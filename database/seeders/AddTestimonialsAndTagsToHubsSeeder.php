<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Adds a testimonials block + a #Tags cloud to each of the four hub
 * pages (foods / activities / buys / cultures), mirroring what the
 * /destinations ("where to go") page already ships.
 *
 * For every hub:
 *   - home_testimonials  is inserted immediately BEFORE the home_faq band
 *   - home_keyword_hashtags is inserted immediately AFTER the home_faq band
 *     (before the closing hub_footer_rail), reading its tags from the
 *     `hubTags` context the hub controllers pass to BlockRenderer.
 *
 * Both blocks use flush:true so they span the hub article's max-w-7xl
 * container edge to edge, matching the category grids around them.
 *
 * Idempotent: existing home_testimonials / home_keyword_hashtags rows for
 * these four pages are removed first, then re-inserted with correct sort
 * order, so the seeder is safe to re-run after edits. Only the four hub
 * slugs are touched; /destinations is never modified.
 */
class AddTestimonialsAndTagsToHubsSeeder extends Seeder
{
    /** Per-hub testimonial heading + subhead + 6 review cards. */
    private function testimonialPayload(string $slug): array
    {
        $map = [
            'foods' => [
                'heading' => 'Real Trips, *Real Plates*',
                'subhead' => 'Notes from travelers who used these food guides to plan what to eat across the islands.',
                'reviews' => [
                    ['text' => 'Planned a whole eating day in Iloilo off this guide. La Paz batchoy for breakfast, then pancit molo for lunch, exactly the order the page suggested.', 'author' => 'Kevin R.', 'location' => 'Iloilo City', 'rating' => 5],
                    ['text' => 'The notes on where a dish actually comes from saved us. We skipped the tourist version and found the real thing at a palengke stall.', 'author' => 'Denise A.', 'location' => 'Cebu City', 'rating' => 5],
                    ['text' => 'Brought my titas to Pampanga and let them pick from the list. Sisig and morcon won, and no one argued after.', 'author' => 'Marlon T.', 'location' => 'San Fernando, Pampanga', 'rating' => 4],
                    ['text' => 'As someone rediscovering my own province, this made me try dishes I grew up near but never ordered. Good kwento behind each one.', 'author' => 'Ligaya M.', 'location' => 'Naga City', 'rating' => 5],
                    ['text' => 'I am not from here and the guide made ordering easy. It told me what to try first and what to save room for.', 'author' => 'Hannah W.', 'location' => 'Singapore', 'rating' => 5],
                    ['text' => 'We ate our way through Bacolod using this. Chicken inasal, then napoleones after, calm and well paced, not rushed.', 'author' => 'Paolo S.', 'location' => 'Bacolod City', 'rating' => 4],
                ],
            ],
            'activities' => [
                'heading' => 'Real Trips, *Real Adventures*',
                'subhead' => 'Notes from travelers who used these guides to plan what to do across the islands.',
                'reviews' => [
                    ['text' => 'We built a Cebu weekend around the activities list. Canyoneering in the morning, then a calm island hop the next day, no backtracking.', 'author' => 'Marco D.', 'location' => 'Makati City', 'rating' => 5],
                    ['text' => 'The guide sorted things by how hard they are, so we picked a couple of easy ones with the kids and one big one for us.', 'author' => 'Bea S.', 'location' => 'Quezon City', 'rating' => 5],
                    ['text' => 'Booked a surf lesson in La Union off this. First time on a board, friendly break, exactly like the page said.', 'author' => 'Josh L.', 'location' => 'Pasig City', 'rating' => 4],
                    ['text' => 'As a foreigner I wanted more than beaches. This showed me caves, falls, and heritage walks I would not have found alone.', 'author' => 'Emily T.', 'location' => 'Melbourne, AU', 'rating' => 5],
                    ['text' => 'Used it to plan Bohol without a tour package. Chocolate Hills at sunrise, then the river, all DIY and easy to follow.', 'author' => 'Andrea V.', 'location' => 'Davao City', 'rating' => 5],
                    ['text' => 'Good for pacing. It flagged which spots get crowded so we went early and had the trail almost to ourselves.', 'author' => 'Rico M.', 'location' => 'Cagayan de Oro', 'rating' => 4],
                ],
            ],
            'buys' => [
                'heading' => 'Real Trips, *Real Finds*',
                'subhead' => 'Notes from travelers who used these guides to plan what to bring home from every region.',
                'reviews' => [
                    ['text' => 'Did a proper pasalubong run through Vigan with this. Bought burnay jars straight from the kiln, well below the Manila reseller.', 'author' => 'Camille T.', 'location' => 'Taguig City', 'rating' => 5],
                    ['text' => 'The list told me which sweets travel well, so nothing spoiled on the way home to Cebu. Small thing, big help.', 'author' => 'Kevin R.', 'location' => 'Cebu City', 'rating' => 5],
                    ['text' => "Picked up T'nalak in Lake Sebu after reading the note on the weavers. Knowing the kwento made me buy from the source.", 'author' => 'Denise A.', 'location' => 'General Santos', 'rating' => 5],
                    ['text' => 'As a balikbayan I wanted real finds, not airport racks. This pointed me to the good stuff, town by town.', 'author' => 'Michael C.', 'location' => 'San Diego, US', 'rating' => 5],
                    ['text' => 'Used it in Davao and came home with proper single-origin chocolate. The guide said where to find it and it was right.', 'author' => 'Paolo S.', 'location' => 'Antipolo', 'rating' => 4],
                    ['text' => 'Bought Marikina shoes on the way out of the city because of this. Custom pair, worth the short wait.', 'author' => 'Andrea V.', 'location' => 'Marikina City', 'rating' => 5],
                ],
            ],
            'cultures' => [
                'heading' => 'Real Trips, *Real Moments*',
                'subhead' => 'Notes from travelers who used these guides to time a trip around the festivals and traditions worth seeing.',
                'reviews' => [
                    ['text' => 'Timed our Kalibo trip to Ati-Atihan using this. Knowing the schedule ahead meant we caught the street dancing, not the cleanup.', 'author' => 'Marco D.', 'location' => 'Makati City', 'rating' => 5],
                    ['text' => 'The guide explained what each festival is actually about, so we watched with context instead of just taking photos.', 'author' => 'Ligaya M.', 'location' => 'Iloilo City', 'rating' => 5],
                    ['text' => 'We planned around Panagbenga in Baguio. Cool mornings, flower floats, and the page had the dates right.', 'author' => 'Bea S.', 'location' => 'Quezon City', 'rating' => 5],
                    ['text' => 'As a foreigner the traditions made more sense after reading these. I understood the why before I showed up.', 'author' => 'Emily T.', 'location' => 'London, UK', 'rating' => 5],
                    ['text' => 'Caught the Pahiyas in Lucban because of this. Whole town in color, and we knew which streets to walk first.', 'author' => 'Rico M.', 'location' => 'Lucena City', 'rating' => 4],
                    ['text' => 'Good for respectful travel. It noted what is sacred versus what is for show, so we knew how to act.', 'author' => 'Hannah W.', 'location' => 'Kuala Lumpur', 'rating' => 5],
                ],
            ],
        ];

        $data = $map[$slug];
        $data['flush'] = true;
        return $data;
    }

    /** #Tags cloud reading the hub's own item-name tags from context. */
    private function hashtagPayload(): array
    {
        return [
            'eyebrow' => '',
            'heading' => '#Tags',
            'subhead' => '',
            'source' => 'hubTags',
            'limit' => 60,
            'flush' => true,
        ];
    }

    public function run(): void
    {
        $slugs = ['foods', 'activities', 'buys', 'cultures'];

        foreach ($slugs as $slug) {
            $page = DB::table('rg_static_pages')->where('slug', $slug)->first();
            if (!$page) {
                $this->command->warn("  {$slug}: no rg_static_pages row, skipping");
                continue;
            }

            // Idempotency: clear any prior copies of the two block types on
            // this page so re-runs land them in the right place exactly once.
            DB::table('rg_content_blocks')
                ->where('owner_type', 'static_page')
                ->where('owner_id', $page->id)
                ->whereIn('block_type', ['home_testimonials', 'home_keyword_hashtags'])
                ->delete();

            $blocks = DB::table('rg_content_blocks')
                ->where('owner_type', 'static_page')
                ->where('owner_id', $page->id)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();

            // Build the desired final sequence. Each entry is either an
            // existing block id (keep) or a new block spec (insert).
            $testimonials = ['__new' => 'home_testimonials', 'payload' => $this->testimonialPayload($slug)];
            $hashtags = ['__new' => 'home_keyword_hashtags', 'payload' => $this->hashtagPayload()];

            $seq = [];
            $placed = false;
            foreach ($blocks as $b) {
                if ($b->block_type === 'home_faq') {
                    $seq[] = $testimonials;
                    $seq[] = ['__keep' => $b->id];
                    $seq[] = $hashtags;
                    $placed = true;
                    continue;
                }
                $seq[] = ['__keep' => $b->id];
            }

            // Fallbacks when there is no home_faq to anchor to: drop both
            // just before the footer rail, else append at the very end.
            if (!$placed) {
                $seq = [];
                $footerInjected = false;
                foreach ($blocks as $b) {
                    if (!$footerInjected && $b->block_type === 'hub_footer_rail') {
                        $seq[] = $testimonials;
                        $seq[] = $hashtags;
                        $footerInjected = true;
                    }
                    $seq[] = ['__keep' => $b->id];
                }
                if (!$footerInjected) {
                    $seq[] = $testimonials;
                    $seq[] = $hashtags;
                }
            }

            // Re-number the whole stream sequentially, updating existing rows
            // and inserting the two new blocks in their slots.
            $order = 1;
            $now = now();
            foreach ($seq as $entry) {
                if (isset($entry['__keep'])) {
                    DB::table('rg_content_blocks')
                        ->where('id', $entry['__keep'])
                        ->update(['sort_order' => $order, 'updated_at' => $now]);
                } else {
                    DB::table('rg_content_blocks')->insert([
                        'owner_type' => 'static_page',
                        'owner_id' => $page->id,
                        'sort_order' => $order,
                        'block_type' => $entry['__new'],
                        'payload_json' => json_encode($entry['payload'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
                $order++;
            }

            $this->command->info("  {$slug}: added testimonials + #Tags (page id {$page->id}, {$order} blocks total)");
        }
    }
}
