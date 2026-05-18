<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rg_settings', function (Blueprint $t) {
            $t->id();
            $t->string('key', 100)->unique();
            $t->longText('value')->nullable();
            $t->enum('type', ['string', 'int', 'json', 'bool', 'text'])->default('string');
            $t->string('label', 200);
            $t->string('group', 50)->default('general');
            $t->string('help_text', 500)->nullable();
            $t->unsignedSmallInteger('sort_order')->default(0);
            $t->timestamps();
            $t->index('group');
        });

        $now = now();
        $seed = [
            ['key' => 'min_topup_php', 'value' => '100', 'type' => 'int', 'label' => 'Minimum GCash Top-up (PHP)', 'group' => 'gold_points', 'sort_order' => 1],
            ['key' => 'gp_php_rate', 'value' => '1', 'type' => 'int', 'label' => 'Gold Points per Peso', 'group' => 'gold_points', 'sort_order' => 2],
            ['key' => 'base_price_multiplier_per_1k_vol', 'value' => '10', 'type' => 'int', 'label' => 'Base price GP per 1k monthly search volume', 'group' => 'pricing', 'sort_order' => 1],
            ['key' => 'base_price_min_gp', 'value' => '100', 'type' => 'int', 'label' => 'Minimum listing base price (GP)', 'group' => 'pricing', 'sort_order' => 2],
            ['key' => 'default_listing_duration_days', 'value' => '30', 'type' => 'int', 'label' => 'Default listing duration (days)', 'group' => 'pricing', 'sort_order' => 3],
            ['key' => 'near_expiry_threshold_pct', 'value' => '20', 'type' => 'int', 'label' => 'Near-expiry threshold (% of duration)', 'group' => 'pricing', 'sort_order' => 4],
            ['key' => 'listings_per_page_default', 'value' => '10', 'type' => 'int', 'label' => 'Top listings shown per page (before paginating)', 'group' => 'listing', 'sort_order' => 1],
            ['key' => 'bid_top_hint_quantum_gp', 'value' => '50', 'type' => 'int', 'label' => 'Quantize "GP to top" hint (GP increments)', 'group' => 'listing', 'sort_order' => 2],
            ['key' => 'site_name', 'value' => 'Resort Guru PH', 'type' => 'string', 'label' => 'Site Name', 'group' => 'frontend', 'sort_order' => 1],
            ['key' => 'site_tagline', 'value' => 'Find the best resorts and hotels in the Philippines', 'type' => 'string', 'label' => 'Site Tagline', 'group' => 'frontend', 'sort_order' => 2],
            ['key' => 'contact_email', 'value' => 'hello@resortguruph.com', 'type' => 'string', 'label' => 'Contact Email', 'group' => 'frontend', 'sort_order' => 3],
            ['key' => 'contact_phone', 'value' => '+63 900 000 0000', 'type' => 'string', 'label' => 'Contact Phone', 'group' => 'frontend', 'sort_order' => 4],
            ['key' => 'social_fb', 'value' => '', 'type' => 'string', 'label' => 'Facebook URL', 'group' => 'frontend', 'sort_order' => 5],
            ['key' => 'social_ig', 'value' => '', 'type' => 'string', 'label' => 'Instagram URL', 'group' => 'frontend', 'sort_order' => 6],
            ['key' => 'gcash_payee_name', 'value' => 'Resort Guru PH', 'type' => 'string', 'label' => 'GCash Payee Name (shown on top-up screen)', 'group' => 'gold_points', 'sort_order' => 3],
            ['key' => 'gcash_payee_number', 'value' => '09000000000', 'type' => 'string', 'label' => 'GCash Payee Number (shown on top-up screen)', 'group' => 'gold_points', 'sort_order' => 4],
        ];
        foreach ($seed as $row) {
            $row['created_at'] = $now;
            $row['updated_at'] = $now;
            DB::table('rg_settings')->insert($row);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('rg_settings');
    }
};
