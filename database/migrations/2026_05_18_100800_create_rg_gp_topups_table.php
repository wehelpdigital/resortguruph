<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rg_gp_topups', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('owner_id');
            $t->unsignedInteger('php_amount');
            $t->unsignedInteger('gp_amount');
            $t->string('gcash_ref_number', 100)->nullable();
            $t->string('gcash_phone', 32)->nullable();
            $t->string('screenshot_path', 500)->nullable();
            $t->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $t->unsignedBigInteger('reviewed_by')->nullable();
            $t->timestamp('reviewed_at')->nullable();
            $t->string('rejection_reason', 500)->nullable();
            $t->timestamps();
            $t->foreign('owner_id')->references('id')->on('rg_owners')->cascadeOnDelete();
            $t->index('status');
            $t->index('reviewed_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rg_gp_topups');
    }
};
