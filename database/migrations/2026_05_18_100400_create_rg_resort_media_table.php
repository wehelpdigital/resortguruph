<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rg_resort_media', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('resort_id');
            $t->enum('kind', ['image', 'video'])->default('image');
            $t->string('path', 500);
            $t->string('caption', 500)->nullable();
            $t->unsignedSmallInteger('sort_order')->default(0);
            $t->timestamps();
            $t->foreign('resort_id')->references('id')->on('rg_resorts')->cascadeOnDelete();
            $t->index(['resort_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rg_resort_media');
    }
};
