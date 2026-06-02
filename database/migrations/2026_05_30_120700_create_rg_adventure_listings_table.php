<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Adventures list on resort keyword pages only (the "things to do
        // near where you're staying" slot). Separate bid pool.
        Schema::create('rg_adventure_listings', function (Blueprint $t) {
            $t->id();
            $t->foreignId('keyword_id')->constrained('rg_keywords')->cascadeOnDelete();
            $t->foreignId('adventure_id')->constrained('rg_adventures')->cascadeOnDelete();
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
        Schema::dropIfExists('rg_adventure_listings');
    }
};
