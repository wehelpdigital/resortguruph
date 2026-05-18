<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rg_static_pages', function (Blueprint $t) {
            $t->id();
            $t->string('slug', 100)->unique();
            $t->string('title', 255);
            $t->string('meta_title', 255)->nullable();
            $t->string('meta_description', 500)->nullable();
            $t->longText('content_html')->nullable();
            $t->boolean('is_published')->default(true);
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rg_static_pages');
    }
};
