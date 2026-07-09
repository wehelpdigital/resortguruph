<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Public partner directory. One row per tourism business listed on
 * Tourist Guide PH (hotels, resorts, tour guides, travel and tours,
 * massage/spa, surf and dive schools, transport, and anything tourism).
 * Powers the /partner-directory page and its live search + filters.
 * is_verified marks businesses carrying the We Highly Recommend badge.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('rg_partners', function (Blueprint $t) {
            $t->id();
            $t->foreignId('owner_id')->nullable()->constrained('rg_owners')->nullOnDelete();
            $t->string('name');
            $t->string('slug')->unique();
            $t->string('type', 40)->index();       // hotel, resort, tour_guide, ...
            $t->string('city')->nullable()->index();
            $t->string('region')->nullable()->index();
            $t->string('tagline')->nullable();
            $t->text('description')->nullable();
            $t->string('image_path')->nullable();
            $t->decimal('rating', 2, 1)->nullable();
            $t->unsignedInteger('review_count')->default(0);
            $t->boolean('is_verified')->default(false)->index();
            $t->boolean('is_featured')->default(false);
            $t->string('phone', 32)->nullable();
            $t->string('website')->nullable();
            $t->enum('status', ['draft', 'pending_review', 'published', 'suspended'])->default('published')->index();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rg_partners');
    }
};
