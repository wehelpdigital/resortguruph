<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rg_gp_ledger', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('owner_id');
            $t->integer('amount');
            $t->enum('reason', ['topup', 'listing_purchase', 'bid', 'ai_usage', 'admin_adjustment', 'hold_release', 'refund']);
            $t->string('ref_type', 100)->nullable();
            $t->unsignedBigInteger('ref_id')->nullable();
            $t->enum('status', ['pending', 'posted', 'voided'])->default('posted');
            $t->longText('meta_json')->nullable();
            $t->timestamp('created_at')->useCurrent();
            $t->foreign('owner_id')->references('id')->on('rg_owners')->cascadeOnDelete();
            $t->index(['owner_id', 'status']);
            $t->index('reason');
            $t->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rg_gp_ledger');
    }
};
