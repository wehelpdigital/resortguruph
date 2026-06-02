<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rg_media', function (Blueprint $t) {
            $t->id();
            $t->string('filename', 255);
            $t->string('path', 500)->unique();
            $t->string('mime', 100)->nullable();
            $t->unsignedBigInteger('size_bytes')->default(0);
            $t->string('kind', 20)->default('image');
            $t->unsignedSmallInteger('width')->nullable();
            $t->unsignedSmallInteger('height')->nullable();
            $t->string('alt', 500)->nullable();
            $t->string('caption', 500)->nullable();
            $t->string('source', 50)->default('manual');
            $t->string('credit', 500)->nullable();
            $t->string('source_url', 1000)->nullable();
            $t->longText('meta_json')->nullable();
            $t->timestamps();
            $t->index(['kind', 'source']);
            $t->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rg_media');
    }
};
