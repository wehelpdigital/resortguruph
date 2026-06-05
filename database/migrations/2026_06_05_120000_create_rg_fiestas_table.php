<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rg_fiestas', function (Blueprint $table) {
            $table->id();
            // Public-facing URL slug. e.g. "sinulog", "ati-atihan",
            // "kadayawan-festival". Drives /fiestas/{slug}.
            $table->string('slug', 200)->unique();
            // Display name. e.g. "Sinulog Festival".
            $table->string('name');

            // Geography. region_cluster mirrors the rg_keywords.cluster_tag
            // taxonomy (north-luzon, central-luzon, metro-manila, south-
            // luzon, bicol, visayas, mindanao, palawan, marinduque) so we
            // can pivot listings between food / destination / fiesta
            // verticals using the same grouping. province + city_or_town
            // describe where the celebration physically happens.
            $table->string('region_cluster', 50);
            $table->string('province', 100)->nullable();
            $table->string('city_or_town', 100)->nullable();

            // Timing. month is 1-12 for fixed-date festivals; null for
            // strictly movable ones (Holy Week, etc.). date_label is the
            // human-readable line shown on the page ("Third Sunday of
            // January", "January 19-25", "Movable, Holy Week").
            $table->tinyInteger('month')->unsigned()->nullable();
            $table->string('date_label', 120)->nullable();

            // One-line summary for cards on the list page.
            $table->string('summary', 500)->nullable();

            // Hero image used on cards + OG. cover_image_path is the
            // working in-app reference; og_image_path is the absolute
            // path emitted in <meta property="og:image">.
            $table->string('cover_image_path', 500)->nullable();
            $table->string('og_image_path', 500)->nullable();

            // SEO + page meta. Populated by the seeder; admin can override.
            $table->string('meta_title', 200)->nullable();
            $table->text('meta_description')->nullable();
            $table->string('h1', 200)->nullable();

            // Publishing controls. Mirrors rg_seo_pages convention.
            $table->boolean('is_published')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->unsignedBigInteger('author_id')->nullable();
            $table->unsignedBigInteger('pageviews_30d')->default(0);

            $table->timestamps();

            $table->index(['region_cluster', 'is_published']);
            $table->index('month');
            $table->index('is_published');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rg_fiestas');
    }
};
