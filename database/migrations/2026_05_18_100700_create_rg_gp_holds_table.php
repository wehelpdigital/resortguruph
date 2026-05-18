<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rg_gp_holds', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('owner_id');
            $t->unsignedInteger('amount');
            $t->string('reason', 100);
            $t->string('ref_type', 100)->nullable();
            $t->unsignedBigInteger('ref_id')->nullable();
            $t->enum('status', ['active', 'released', 'captured'])->default('active');
            $t->timestamp('expires_at')->nullable();
            $t->timestamp('released_at')->nullable();
            $t->timestamp('created_at')->useCurrent();
            $t->foreign('owner_id')->references('id')->on('rg_owners')->cascadeOnDelete();
            $t->index(['owner_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rg_gp_holds');
    }
};
