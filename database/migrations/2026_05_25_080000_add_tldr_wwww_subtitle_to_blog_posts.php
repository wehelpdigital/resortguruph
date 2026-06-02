<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('rg_blog_posts')) return;
        Schema::table('rg_blog_posts', function (Blueprint $table) {
            if (!Schema::hasColumn('rg_blog_posts', 'subtitle')) {
                $table->string('subtitle', 300)->nullable()->after('title');
            }
            if (!Schema::hasColumn('rg_blog_posts', 'tldr')) {
                $table->text('tldr')->nullable()->after('excerpt');
            }
            if (!Schema::hasColumn('rg_blog_posts', 'wwww_json')) {
                $table->json('wwww_json')->nullable()->after('tldr');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rg_blog_posts', function (Blueprint $table) {
            foreach (['subtitle', 'tldr', 'wwww_json'] as $col) {
                if (Schema::hasColumn('rg_blog_posts', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
