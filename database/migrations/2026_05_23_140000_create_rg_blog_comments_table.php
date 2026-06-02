<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rg_blog_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('blog_post_id');
            $table->string('commenter_name', 120);
            $table->string('commenter_email', 200)->nullable();
            $table->string('commenter_avatar', 500)->nullable();
            $table->text('comment_text');
            $table->unsignedBigInteger('parent_id')->nullable(); // for threaded replies (future)
            $table->enum('status', ['pending', 'approved', 'spam'])->default('approved');
            $table->boolean('is_seeded')->default(false);
            $table->timestamps();
            $table->index(['blog_post_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rg_blog_comments');
    }
};
