<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rg_seo_pages', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('keyword_id')->unique();
            $t->string('title', 255);
            $t->string('meta_title', 255)->nullable();
            $t->string('meta_description', 500)->nullable();
            $t->string('meta_keywords', 500)->nullable();
            $t->string('canonical_url', 500)->nullable();
            $t->string('og_image_path', 500)->nullable();
            $t->string('h1', 255)->nullable();
            $t->longText('intro_html')->nullable();
            $t->longText('body_html')->nullable();
            $t->longText('faq_json')->nullable();
            $t->longText('schema_json')->nullable();
            $t->longText('fallback_listing_html')->nullable();
            $t->boolean('is_published')->default(false);
            $t->timestamp('published_at')->nullable();
            $t->unsignedInteger('pageviews_30d')->default(0);
            $t->unsignedInteger('pageviews_total')->default(0);
            $t->timestamps();
            $t->index('is_published');
            $t->foreign('keyword_id')->references('id')->on('rg_keywords')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rg_seo_pages');
    }
};
