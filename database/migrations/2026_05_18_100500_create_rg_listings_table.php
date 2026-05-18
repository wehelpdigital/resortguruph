<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rg_listings', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('keyword_id');
            $t->unsignedBigInteger('resort_id');
            $t->unsignedBigInteger('owner_id');
            $t->unsignedInteger('base_gp')->default(0);
            $t->unsignedInteger('bid_gp')->default(0);
            $t->timestamp('starts_at')->nullable();
            $t->timestamp('expires_at')->nullable();
            $t->timestamp('last_bid_at')->nullable();
            $t->enum('status', ['active', 'expired', 'cancelled'])->default('active');
            $t->timestamps();
            $t->foreign('keyword_id')->references('id')->on('rg_keywords')->cascadeOnDelete();
            $t->foreign('resort_id')->references('id')->on('rg_resorts')->cascadeOnDelete();
            $t->foreign('owner_id')->references('id')->on('rg_owners')->cascadeOnDelete();
            $t->index(['keyword_id', 'status', 'bid_gp', 'last_bid_at'], 'rg_listings_ranking_idx');
            $t->index('expires_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rg_listings');
    }
};
