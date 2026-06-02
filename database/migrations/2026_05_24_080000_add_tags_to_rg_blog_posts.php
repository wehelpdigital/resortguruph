<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('rg_blog_posts') && !Schema::hasColumn('rg_blog_posts', 'tags')) {
            Schema::table('rg_blog_posts', function (Blueprint $table) {
                $table->string('tags', 500)->nullable()->after('content_html');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('rg_blog_posts', 'tags')) {
            Schema::table('rg_blog_posts', fn (Blueprint $t) => $t->dropColumn('tags'));
        }
    }
};
