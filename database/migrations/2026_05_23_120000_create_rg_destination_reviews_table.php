<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rg_destination_reviews', function (Blueprint $table) {
            $table->id();
            // Reviews are scoped to a keyword (which maps to a destination/area).
            // Scoping at the keyword level (not per-page) lets the same set of
            // reviews surface across every SEO page that targets that keyword.
            $table->unsignedBigInteger('keyword_id')->nullable();
            $table->string('reviewer_name', 120);
            $table->string('reviewer_location', 120)->nullable();
            $table->string('reviewer_avatar', 500)->nullable();
            $table->unsignedTinyInteger('rating')->default(5); // 1-5
            $table->text('review_text');
            $table->date('review_date')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->enum('status', ['draft', 'published'])->default('published');
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['keyword_id', 'status']);
            $table->index('is_featured');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rg_destination_reviews');
    }
};
