<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RgBlogPostsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $posts = [
            [
                'title' => 'A Quick Guide to Picking a Resort for Your Family Reunion',
                'slug' => 'quick-guide-family-reunion-resort',
                'excerpt' => 'How to pick a resort that handles 30 cousins, three videoke machines, and one lola who refuses to swim. A practical checklist for organizers.',
                'content_html' => '<p>Every Filipino family reunion eventually outgrows lola\'s living room. When that happens, picking a resort becomes someone\'s problem (usually whichever cousin has the WiFi password). Here is the checklist that saves you the headache.</p>
                    <h2>Start with the headcount</h2>
                    <p>Be honest about your true headcount including the relatives who said they were not coming. Resorts charge per head past a base number, and every extra adult past 30 can push you into a different tier. Always book for 10 more people than you confirmed.</p>
                    <h2>The non-negotiable amenities</h2>
                    <ul>
                        <li>A function hall with a stable generator. Brownouts during a videoke face-off are unforgivable.</li>
                        <li>Pools at multiple depths. Lolos sit at the kiddie pool side and lola will not move from there.</li>
                        <li>Parking for at least 12 cars. Reunions caravan in.</li>
                        <li>Sound system or open mic policy. Not all resorts allow your tito to host a karaoke contest.</li>
                    </ul>
                    <h2>Food rules vary widely</h2>
                    <p>Some resorts require you to use their kitchen and food. Others let you bring lechon and a barangay-sized cauldron of kare-kare. Always confirm in writing. Surprise corkage fees on the day of the event have ruined many reunions.</p>
                    <h2>Book early</h2>
                    <p>For Holy Week or December reunions, book six to eight weeks ahead. The good resorts in Bulacan, Pampanga, and Laguna fill up first. By two weeks before, you are looking at second-tier options.</p>',
                'meta_title' => 'How to Pick a Resort for Your Family Reunion in 2026',
                'meta_description' => 'A practical checklist for picking the right resort for your Filipino family reunion. Headcount, amenities, food rules, and booking lead times.',
                'status' => 'published',
                'published_at' => $now->copy()->subDays(5),
            ],
            [
                'title' => 'Tagaytay or Baguio? When Each One Wins',
                'slug' => 'tagaytay-or-baguio-comparison',
                'excerpt' => 'Both promise cooler weather and an excuse to skip Manila for the weekend, but they answer different questions. A clear comparison.',
                'content_html' => '<p>The classic Manileño weekend choice. Both promise cooler air and a few days away. But the right pick depends on what you actually want from the trip.</p>
                    <h2>Tagaytay wins on travel time</h2>
                    <p>Two hours from BGC via SLEX-CALAX on a good day. Baguio is closer to five or six hours via TPLEX. If you only have a weekend and you are leaving Friday after work, Tagaytay is the realistic option.</p>
                    <h2>Baguio wins on actual cold</h2>
                    <p>Baguio mornings in December drop to 8 to 10 degrees. Tagaytay rarely goes below 15. If you want sweater weather that actually feels like winter, Baguio is the only PH answer.</p>
                    <h2>Food is closer than people admit</h2>
                    <p>Both cities have strong food scenes now. Tagaytay leans into bulalo, beef strip, and ridge-view restaurants. Baguio still owns the night market street food, strawberry taho, and Good Shepherd ube. Neither is clearly better.</p>
                    <h2>Choose Tagaytay when</h2>
                    <ul>
                        <li>You only have one night.</li>
                        <li>You want a view of an active volcano with your morning coffee.</li>
                        <li>You are taking parents who do not love long road trips.</li>
                    </ul>
                    <h2>Choose Baguio when</h2>
                    <ul>
                        <li>You have at least three days.</li>
                        <li>You want pine forests and proper cold.</li>
                        <li>You enjoy night markets and bargain shopping.</li>
                    </ul>',
                'meta_title' => 'Tagaytay vs Baguio: Which Weekend Trip is Better?',
                'meta_description' => 'A practical comparison of Tagaytay and Baguio for Manileños picking a weekend escape. Travel time, weather, food, and when each one wins.',
                'status' => 'published',
                'published_at' => $now->copy()->subDays(12),
            ],
            [
                'title' => 'How to List Your Resort and Actually Get Bookings',
                'slug' => 'how-to-list-resort-get-bookings',
                'excerpt' => 'A short playbook for resort owners on what to put on your listing page so guests actually click the call button.',
                'content_html' => '<p>You have a great resort. The reviews are solid. But the bookings are quieter than they should be. Most of the time, the issue is not the property. It is the listing.</p>
                    <h2>Lead with one strong photo</h2>
                    <p>Your hero image does 80 percent of the work. Pick the photo that best shows the actual experience of staying there, not the artsy shot of a coconut on a table. Pools at golden hour, the view from the deck, the front of the cottage with people walking towards it. Real beats curated.</p>
                    <h2>Write like you talk</h2>
                    <p>Long flowery descriptions get skimmed. Short, direct paragraphs with specifics convert better. Mention the actual distance to the beach in minutes. Mention the actual room count. Mention if your generator covers the entire property. Specifics build trust.</p>
                    <h2>Reply fast</h2>
                    <p>Most bookings are decided within 24 hours of the inquiry. If you reply two days later, the guest has already booked elsewhere. Set up a phone notification for inquiries and reply within an hour during business hours.</p>
                    <h2>Update your photos quarterly</h2>
                    <p>Photos from 2019 of a deck that no longer looks like that do you no favours. Keep them fresh. Phone photos are fine if they are recent and well-lit.</p>',
                'meta_title' => 'How to List Your Resort and Get More Bookings',
                'meta_description' => 'A short, practical playbook for Filipino resort owners on how to optimise your listing for more guest bookings.',
                'status' => 'published',
                'published_at' => $now->copy()->subDays(20),
            ],
        ];

        foreach ($posts as $p) {
            DB::table('rg_blog_posts')->updateOrInsert(
                ['slug' => $p['slug']],
                array_merge($p, ['updated_at' => $now, 'created_at' => $now])
            );
        }
    }
}
