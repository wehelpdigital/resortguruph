<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rg_tourist_spots', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('slug')->unique();
            $t->string('location')->nullable();
            $t->string('region_label')->nullable();
            $t->string('cluster_tag', 64)->nullable()->index();
            $t->string('destination_key', 80)->nullable()->index();
            $t->foreignId('keyword_id')->nullable()->constrained('rg_keywords')->nullOnDelete();
            $t->foreignId('media_id')->nullable()->constrained('rg_media')->nullOnDelete();
            $t->text('description')->nullable();
            $t->integer('featured_order')->nullable()->index();
            $t->enum('status', ['draft', 'published'])->default('published');
            $t->timestamps();
            $t->index(['status', 'featured_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rg_tourist_spots');
    }
};
