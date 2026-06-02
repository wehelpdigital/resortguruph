<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rg_authors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('role')->nullable();  // "Travel Writer", "Senior Editor"
            $table->text('bio')->nullable();
            $table->string('avatar_path')->nullable();  // local path or external URL
            $table->string('email')->nullable();
            $table->string('instagram')->nullable();
            $table->string('facebook')->nullable();
            $table->string('twitter')->nullable();
            $table->string('home_base')->nullable();  // "Quezon City", "Cebu", etc
            $table->string('covers_clusters')->nullable();  // CSV of cluster_tags this author writes
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        if (Schema::hasTable('rg_seo_pages') && !Schema::hasColumn('rg_seo_pages', 'author_id')) {
            Schema::table('rg_seo_pages', function (Blueprint $table) {
                $table->unsignedBigInteger('author_id')->nullable()->after('keyword_id');
                $table->index('author_id');
            });
        }

        if (Schema::hasTable('rg_blog_posts') && !Schema::hasColumn('rg_blog_posts', 'rg_author_id')) {
            Schema::table('rg_blog_posts', function (Blueprint $table) {
                $table->unsignedBigInteger('rg_author_id')->nullable()->after('author_id');
                $table->index('rg_author_id');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('rg_blog_posts', 'rg_author_id')) {
            Schema::table('rg_blog_posts', fn (Blueprint $t) => $t->dropColumn('rg_author_id'));
        }
        if (Schema::hasColumn('rg_seo_pages', 'author_id')) {
            Schema::table('rg_seo_pages', fn (Blueprint $t) => $t->dropColumn('author_id'));
        }
        Schema::dropIfExists('rg_authors');
    }
};
