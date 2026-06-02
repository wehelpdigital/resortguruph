<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('rg_blog_comments')) return;
        Schema::table('rg_blog_comments', function (Blueprint $table) {
            if (!Schema::hasColumn('rg_blog_comments', 'rating')) {
                $table->unsignedTinyInteger('rating')->nullable()->after('comment_text');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rg_blog_comments', function (Blueprint $table) {
            if (Schema::hasColumn('rg_blog_comments', 'rating')) {
                $table->dropColumn('rating');
            }
        });
    }
};
