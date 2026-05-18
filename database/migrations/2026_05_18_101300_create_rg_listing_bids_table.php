<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rg_listing_bids', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('listing_id');
            $t->unsignedBigInteger('owner_id');
            $t->unsignedBigInteger('keyword_id');
            $t->enum('action', ['claim', 'bid', 'extend']);
            $t->unsignedInteger('gp_amount');
            $t->unsignedInteger('bid_gp_after');
            $t->unsignedInteger('rank_after')->nullable();
            $t->unsignedSmallInteger('days_added')->default(0);
            $t->longText('meta_json')->nullable();
            $t->timestamp('created_at')->useCurrent();
            $t->foreign('listing_id')->references('id')->on('rg_listings')->cascadeOnDelete();
            $t->foreign('owner_id')->references('id')->on('rg_owners')->cascadeOnDelete();
            $t->foreign('keyword_id')->references('id')->on('rg_keywords')->cascadeOnDelete();
            $t->index(['listing_id', 'created_at']);
            $t->index(['owner_id', 'created_at']);
            $t->index(['keyword_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rg_listing_bids');
    }
};
