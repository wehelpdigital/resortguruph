<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rg_content_blocks', function (Blueprint $t) {
            $t->id();
            $t->string('owner_type', 50);
            $t->unsignedBigInteger('owner_id');
            $t->unsignedSmallInteger('sort_order')->default(0);
            $t->string('block_type', 50);
            $t->longText('payload_json')->nullable();
            $t->timestamps();
            $t->index(['owner_type', 'owner_id', 'sort_order'], 'rg_content_blocks_owner_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rg_content_blocks');
    }
};
