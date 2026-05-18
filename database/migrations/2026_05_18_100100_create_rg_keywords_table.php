<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rg_keywords', function (Blueprint $t) {
            $t->id();
            $t->string('phrase', 255);
            $t->string('slug', 255)->unique();
            $t->unsignedInteger('search_volume_monthly')->default(0);
            $t->unsignedTinyInteger('keyword_difficulty')->default(0);
            $t->string('cluster_tag', 100)->nullable();
            $t->string('intent', 100)->nullable();
            $t->text('notes')->nullable();
            $t->enum('status', ['active', 'draft', 'archived'])->default('active');
            $t->unsignedSmallInteger('listing_capacity_top')->default(10);
            $t->unsignedInteger('base_price_gp')->default(0);
            $t->timestamps();
            $t->index('status');
            $t->index('search_volume_monthly');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rg_keywords');
    }
};
