<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rg_seo_pages', function (Blueprint $t) {
            // hero_html renders BETWEEN the listings offer wrapper and the
            // article header, so it can host a photo slider, a hero strip,
            // or any other above-the-article-fold element without polluting
            // intro_html.
            $t->longText('hero_html')->nullable()->after('subtitle');
        });
    }

    public function down(): void
    {
        Schema::table('rg_seo_pages', function (Blueprint $t) {
            $t->dropColumn('hero_html');
        });
    }
};
