<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Separate bid pool for restaurant listings so resort bids never
        // cross-contaminate. Restaurants can list on BOTH food keywords
        // (their primary audience) and resort keywords (cross-promo).
        Schema::create('rg_restaurant_listings', function (Blueprint $t) {
            $t->id();
            $t->foreignId('keyword_id')->constrained('rg_keywords')->cascadeOnDelete();
            $t->foreignId('restaurant_id')->constrained('rg_restaurants')->cascadeOnDelete();
            $t->foreignId('owner_id')->nullable()->constrained('rg_owners')->nullOnDelete();
            $t->integer('base_gp')->default(0);
            $t->integer('bid_gp')->default(0);
            $t->timestamp('starts_at')->nullable();
            $t->timestamp('expires_at')->nullable();
            $t->timestamp('last_bid_at')->nullable();
            $t->enum('status', ['active', 'expired', 'cancelled'])->default('active');
            $t->timestamps();
            $t->index(['keyword_id', 'status', 'bid_gp']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rg_restaurant_listings');
    }
};
