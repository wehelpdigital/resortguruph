<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RgStaticPagesSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $pages = [
            [
                'slug' => 'about',
                'title' => 'About Resort Guru PH',
                'meta_title' => 'About Us | Resort Guru PH',
                'meta_description' => 'Resort Guru PH connects Filipino travellers with the best resorts, hotels, and Airbnb stays across the country. Learn how we work.',
                'content_html' => '<p>Resort Guru PH is a Filipino-built directory that helps travellers discover and compare resorts, hotels, beach houses, and short-stay rentals across the Philippines. We focus on the destinations people actually search for, not just the ones with the biggest ad budgets.</p>
                    <p>Our team curates content for every destination page so that visitors can quickly understand what each area offers before they book. Resort owners can claim listing slots on the pages most relevant to their property and bid for visibility based on real search demand.</p>
                    <h2>What makes us different</h2>
                    <ul>
                        <li>Every destination page is written by people who have actually visited or have local sources.</li>
                        <li>Resort owners pay only for visibility on the keywords that matter to their region.</li>
                        <li>No hidden listing fees, no booking commissions. Owners pay in Gold Points and decide their own visibility tier.</li>
                    </ul>
                    <h2>Our team</h2>
                    <p>We are a small group based in Manila with travel writers, developers, and former hospitality operators. Our mission is to make it easier for small to mid-sized resort owners to compete with the big platforms without giving away their margins to booking commissions.</p>',
            ],
            [
                'slug' => 'contact',
                'title' => 'Contact Us',
                'meta_title' => 'Contact | Resort Guru PH',
                'meta_description' => 'Get in touch with Resort Guru PH for partnerships, support, or feedback.',
                'content_html' => '<p>We would love to hear from you. Whether you are a resort owner looking to list, a traveller with feedback, or a journalist working on a story, drop us a message via the form on this page and we will reply within 1 to 2 business days.</p>',
            ],
            [
                'slug' => 'terms',
                'title' => 'Terms of Service',
                'meta_title' => 'Terms of Service | Resort Guru PH',
                'meta_description' => 'Read our terms of service for using Resort Guru PH.',
                'content_html' => '<p><em>Last updated: ' . $now->format('F j, Y') . '</em></p>
                    <h2>1. Acceptance of terms</h2>
                    <p>By using Resort Guru PH you agree to these terms. If you do not agree, please do not use the service.</p>
                    <h2>2. Account responsibilities</h2>
                    <p>Resort owners are responsible for the accuracy of the information they post about their properties. We reserve the right to remove listings that misrepresent a property or violate Philippine consumer protection laws.</p>
                    <h2>3. Gold Points and payments</h2>
                    <p>Gold Points are a digital credit used to claim and bid on listing slots. 1 Gold Point equals 1 Philippine Peso. Gold Points are non-refundable once purchased. They have no cash value outside of the Resort Guru PH platform.</p>
                    <h2>4. Listing bids</h2>
                    <p>Listing visibility is determined by total bid Gold Points. Outbidding does not refund or shorten the existing listing duration. All listings run to their full purchased duration regardless of rank changes.</p>
                    <h2>5. Disputes</h2>
                    <p>Any disputes will be resolved in the courts of Metro Manila, Philippines under Philippine law.</p>
                    <h2>6. Changes</h2>
                    <p>We may update these terms at any time. Continued use of the service after changes constitutes acceptance of the new terms.</p>',
            ],
            [
                'slug' => 'privacy',
                'title' => 'Privacy Policy',
                'meta_title' => 'Privacy Policy | Resort Guru PH',
                'meta_description' => 'How Resort Guru PH collects, uses, and protects your information.',
                'content_html' => '<p><em>Last updated: ' . $now->format('F j, Y') . '</em></p>
                    <h2>1. What we collect</h2>
                    <p>When you create an account, we collect your name, email address, phone number (optional), and login activity. When you submit a top-up, we collect your GCash reference number, phone number, and a screenshot of your payment.</p>
                    <h2>2. How we use it</h2>
                    <p>Your information is used to operate the platform, verify payments, prevent fraud, and improve our destination content. We do not sell your personal information to third parties.</p>
                    <h2>3. Cookies</h2>
                    <p>We use cookies to keep you logged in and to remember your preferences. You can disable cookies in your browser, but parts of the dashboard will stop working.</p>
                    <h2>4. Data protection</h2>
                    <p>We follow Philippine Data Privacy Act of 2012 (Republic Act 10173) standards. You have the right to request access to, correction of, or deletion of your personal data at any time.</p>
                    <h2>5. Contact</h2>
                    <p>For privacy concerns, contact us through the contact form. We will respond within 5 business days.</p>',
            ],
        ];

        foreach ($pages as $p) {
            DB::table('rg_static_pages')->updateOrInsert(
                ['slug' => $p['slug']],
                array_merge($p, ['is_published' => 1, 'updated_at' => $now, 'created_at' => $now])
            );
        }
    }
}
