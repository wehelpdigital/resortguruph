<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rg_audit_logs', function (Blueprint $t) {
            $t->id();
            $t->enum('actor_type', ['admin', 'owner', 'system'])->default('system');
            $t->unsignedBigInteger('actor_id')->nullable();
            $t->string('action', 100);
            $t->string('target_type', 100)->nullable();
            $t->unsignedBigInteger('target_id')->nullable();
            $t->longText('meta_json')->nullable();
            $t->string('ip', 64)->nullable();
            $t->string('user_agent', 500)->nullable();
            $t->timestamp('created_at')->useCurrent();
            $t->index(['actor_type', 'actor_id']);
            $t->index('action');
            $t->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rg_audit_logs');
    }
};
