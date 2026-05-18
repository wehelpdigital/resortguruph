<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rg_blog_posts', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('author_id')->nullable();
            $t->string('title', 255);
            $t->string('slug', 255)->unique();
            $t->string('excerpt', 500)->nullable();
            $t->longText('content_html')->nullable();
            $t->string('cover_path', 500)->nullable();
            $t->string('meta_title', 255)->nullable();
            $t->string('meta_description', 500)->nullable();
            $t->enum('status', ['draft', 'published'])->default('draft');
            $t->timestamp('published_at')->nullable();
            $t->timestamps();
            $t->index('status');
            $t->index('published_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rg_blog_posts');
    }
};
