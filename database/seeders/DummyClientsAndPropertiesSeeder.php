<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DummyClientsAndPropertiesSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // 8 dummy clients (resort owners) — realistic Filipino names
        $clients = [
            ['name' => 'Maria Santos',     'email' => 'maria.santos@dummy.test',    'phone' => '+639171234567', 'status' => 'active'],
            ['name' => 'Juan dela Cruz',   'email' => 'juan.delacruz@dummy.test',   'phone' => '+639182345678', 'status' => 'active'],
            ['name' => 'Liza Reyes',       'email' => 'liza.reyes@dummy.test',      'phone' => '+639193456789', 'status' => 'active'],
            ['name' => 'Roberto Aquino',   'email' => 'roberto.aquino@dummy.test',  'phone' => '+639204567890', 'status' => 'active'],
            ['name' => 'Cristina Mendoza', 'email' => 'cristina.mendoza@dummy.test','phone' => '+639215678901', 'status' => 'pending'],
            ['name' => 'Ramon Garcia',     'email' => 'ramon.garcia@dummy.test',    'phone' => '+639226789012', 'status' => 'active'],
            ['name' => 'Sofia Villanueva', 'email' => 'sofia.villanueva@dummy.test','phone' => '+639237890123', 'status' => 'active'],
            ['name' => 'Andres Lim',       'email' => 'andres.lim@dummy.test',      'phone' => '+639248901234', 'status' => 'suspended'],
        ];

        $createdOwners = [];
        foreach ($clients as $c) {
            $existing = DB::table('rg_owners')->where('email', $c['email'])->first();
            if ($existing) {
                $createdOwners[] = $existing->id;
                continue;
            }
            $id = DB::table('rg_owners')->insertGetId([
                'name' => $c['name'],
                'email' => $c['email'],
                'phone' => $c['phone'],
                'password' => Hash::make('password123'),
                'status' => $c['status'],
                'created_at' => $now->copy()->subDays(rand(2, 90)),
                'updated_at' => $now,
            ]);
            // Credit each active client with starter GP
            if ($c['status'] === 'active') {
                DB::table('rg_gp_ledger')->insert([
                    'owner_id' => $id,
                    'amount' => rand(500, 5000),
                    'reason' => 'admin_adjustment',
                    'ref_type' => 'admin_grant',
                    'ref_id' => 0,
                    'status' => 'posted',
                    'meta_json' => json_encode(['note' => 'Dummy data starter GP']),
                    'created_at' => $now,
                ]);
            }
            $createdOwners[] = $id;
        }
        $this->command->info('Clients ready: ' . count($createdOwners));

        // 12 dummy properties spread across regions
        $properties = [
            ['owner_idx' => 0, 'name' => 'Casa de Maria Tagaytay', 'city' => 'Tagaytay', 'province' => 'Cavite', 'tagline' => 'Hilltop villa with Manila bay views', 'status' => 'published', 'primary_color' => '#0ea5e9', 'secondary_color' => '#f59e0b'],
            ['owner_idx' => 0, 'name' => 'Casa de Maria Alfonso', 'city' => 'Alfonso', 'province' => 'Cavite', 'tagline' => 'Cool-air private pool retreat', 'status' => 'published', 'primary_color' => '#0ea5e9', 'secondary_color' => '#f59e0b'],
            ['owner_idx' => 1, 'name' => 'Cruz Beach Resort Laiya', 'city' => 'San Juan', 'province' => 'Batangas', 'tagline' => 'White-sand family beach resort', 'status' => 'published', 'primary_color' => '#0891b2', 'secondary_color' => '#fbbf24'],
            ['owner_idx' => 2, 'name' => 'Liza\'s Pansol Pool Villa', 'city' => 'Calamba', 'province' => 'Laguna', 'tagline' => '22-hour private hot spring rental', 'status' => 'published', 'primary_color' => '#7c3aed', 'secondary_color' => '#fbbf24'],
            ['owner_idx' => 3, 'name' => 'Aquino Highland Inn', 'city' => 'Antipolo', 'province' => 'Rizal', 'tagline' => 'Hilltop view of Metro Manila lights', 'status' => 'published', 'primary_color' => '#16a34a', 'secondary_color' => '#fb923c'],
            ['owner_idx' => 3, 'name' => 'Aquino Beach Lodge Anilao', 'city' => 'Mabini', 'province' => 'Batangas', 'tagline' => 'Diving + macro photography base', 'status' => 'pending_review', 'primary_color' => '#16a34a', 'secondary_color' => '#fb923c'],
            ['owner_idx' => 4, 'name' => 'Mendoza Farm Stay Indang', 'city' => 'Indang', 'province' => 'Cavite', 'tagline' => 'Rural farm retreat, coffee tours', 'status' => 'pending_review', 'primary_color' => '#dc2626', 'secondary_color' => '#fbbf24'],
            ['owner_idx' => 5, 'name' => 'Garcia Beach Cottages Bolinao', 'city' => 'Bolinao', 'province' => 'Pangasinan', 'tagline' => 'White-sand Patar Beach access', 'status' => 'published', 'primary_color' => '#0d9488', 'secondary_color' => '#f59e0b'],
            ['owner_idx' => 5, 'name' => 'Garcia Highland Cabins Sagada', 'city' => 'Sagada', 'province' => 'Mountain Province', 'tagline' => 'Cold pine air, hike base camp', 'status' => 'draft', 'primary_color' => '#0d9488', 'secondary_color' => '#f59e0b'],
            ['owner_idx' => 6, 'name' => 'Villanueva Boutique El Nido', 'city' => 'El Nido', 'province' => 'Palawan', 'tagline' => 'Walking distance to lagoon tour piers', 'status' => 'published', 'primary_color' => '#0369a1', 'secondary_color' => '#fcd34d'],
            ['owner_idx' => 6, 'name' => 'Villanueva Dive Resort Dauin', 'city' => 'Dauin', 'province' => 'Negros Oriental', 'tagline' => 'Macro photography + Apo Island access', 'status' => 'published', 'primary_color' => '#0369a1', 'secondary_color' => '#fcd34d'],
            ['owner_idx' => 7, 'name' => 'Lim Family Resort Subic', 'city' => 'Subic', 'province' => 'Zambales', 'tagline' => 'Beachfront with adventure parks nearby', 'status' => 'suspended', 'primary_color' => '#475569', 'secondary_color' => '#fbbf24'],
        ];

        $created = 0; $skipped = 0;
        foreach ($properties as $p) {
            $ownerId = $createdOwners[$p['owner_idx']] ?? null;
            if (!$ownerId) continue;
            $slug = Str::slug($p['name']);
            if (DB::table('rg_resorts')->where('slug', $slug)->exists()) { $skipped++; continue; }
            DB::table('rg_resorts')->insert([
                'owner_id' => $ownerId,
                'name' => $p['name'],
                'slug' => $slug,
                'tagline' => $p['tagline'],
                'description_html' => '<p>' . $p['tagline'] . '. Sample property for demo purposes. Edit via the dashboard to add real description, amenities, photos, and contact details.</p>',
                'city' => $p['city'],
                'province' => $p['province'],
                'address' => $p['city'] . ', ' . $p['province'] . ', Philippines',
                'phone' => '+63917' . rand(1000000, 9999999),
                'email' => Str::slug($p['name']) . '@dummy.test',
                'price_range' => '₱' . rand(2, 8) . ',000 - ₱' . rand(10, 25) . ',000',
                'capacity' => rand(10, 80),
                'amenities_json' => json_encode(['Pool', 'Wi-Fi', 'Parking', 'Air conditioning', 'Hot shower']),
                'primary_color' => $p['primary_color'],
                'secondary_color' => $p['secondary_color'],
                'status' => $p['status'],
                'approved_at' => $p['status'] === 'published' ? $now : null,
                'created_at' => $now->copy()->subDays(rand(1, 60)),
                'updated_at' => $now,
            ]);
            $created++;
        }
        $this->command->info("Properties created: $created (skipped existing: $skipped)");

        // System pages (homepage, blog index, register, login, contact)
        $systemPages = [
            ['slug' => 'home', 'title' => 'Homepage', 'meta_title' => 'Resort Guru PH | Find Resorts, Hotels & Airbnb in the Philippines', 'meta_description' => 'Compare resorts, hotels, and Airbnb stays across the Philippines. From Tagaytay to El Nido, discover where to stay.'],
            ['slug' => 'blog-index', 'title' => 'Blog Index Page Settings', 'meta_title' => 'Travel Blog | Resort Guru PH', 'meta_description' => 'Travel tips, destination guides, and resort reviews from across the Philippines.'],
            ['slug' => 'register-page', 'title' => 'Register Page Content', 'meta_title' => 'Register Your Resort | Resort Guru PH', 'meta_description' => 'List your resort, hotel, or Airbnb on the Philippines\' fastest-growing directory.'],
            ['slug' => 'login-page', 'title' => 'Login Page Content', 'meta_title' => 'Sign In | Resort Guru PH', 'meta_description' => 'Sign in to manage your property listings.'],
            ['slug' => 'contact-page', 'title' => 'Contact Page Content', 'meta_title' => 'Contact Us | Resort Guru PH', 'meta_description' => 'Get in touch with the Resort Guru PH team.'],
        ];
        $sysCreated = 0;
        foreach ($systemPages as $sp) {
            if (DB::table('rg_static_pages')->where('slug', $sp['slug'])->exists()) continue;
            DB::table('rg_static_pages')->insert([
                'slug' => $sp['slug'],
                'title' => $sp['title'],
                'meta_title' => $sp['meta_title'],
                'meta_description' => $sp['meta_description'],
                'content_html' => '',
                'is_published' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $sysCreated++;
        }
        $this->command->info("System pages added: $sysCreated");
    }
}
