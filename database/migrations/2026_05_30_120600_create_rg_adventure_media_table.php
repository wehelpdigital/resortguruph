<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rg_adventure_media', function (Blueprint $t) {
            $t->id();
            $t->foreignId('adventure_id')->constrained('rg_adventures')->cascadeOnDelete();
            $t->string('kind', 16)->default('image');
            $t->string('path');
            $t->string('caption')->nullable();
            $t->integer('sort_order')->default(0);
            $t->timestamps();
            $t->index(['adventure_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rg_adventure_media');
    }
};
