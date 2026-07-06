<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rg_tags', function (Blueprint $table) {
            $table->id();
            // Display text shown after the "#" in the footer cloud,
            // e.g. "Cebu", "ElNido". Unique so the cloud never repeats.
            $table->string('tag', 120)->unique();
            // Destination URL slug the tag links to (a keyword page slug,
            // e.g. "hotel-in-cebu"); resolved with url($slug).
            $table->string('slug', 200);
            // Source keyword this tag was derived from (nullable so tags can
            // also be hand-added in the admin later).
            $table->unsignedBigInteger('keyword_id')->nullable();
            // Manual ordering (lower = earlier); ties fall back to volume.
            $table->integer('position')->default(0);
            // Cached monthly search volume of the source keyword, used as the
            // secondary sort so the busiest places surface first.
            $table->unsignedInteger('search_volume_monthly')->default(0);
            // Toggle visibility without deleting the row.
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['is_active', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rg_tags');
    }
};
