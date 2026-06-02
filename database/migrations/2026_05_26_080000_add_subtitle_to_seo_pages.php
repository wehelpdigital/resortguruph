<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('rg_seo_pages')) return;
        Schema::table('rg_seo_pages', function (Blueprint $table) {
            if (!Schema::hasColumn('rg_seo_pages', 'subtitle')) {
                $table->string('subtitle', 400)->nullable()->after('h1');
            }
        });
    }

    public function down(): void
    {
        Schema::table('rg_seo_pages', function (Blueprint $table) {
            if (Schema::hasColumn('rg_seo_pages', 'subtitle')) {
                $table->dropColumn('subtitle');
            }
        });
    }
};
