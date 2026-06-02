<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rg_restaurants', function (Blueprint $t) {
            $t->id();
            $t->foreignId('owner_id')->nullable()->constrained('rg_owners')->nullOnDelete();
            $t->string('name');
            $t->string('slug')->unique();
            $t->string('tagline')->nullable();
            $t->text('description_html')->nullable();
            $t->string('cuisine', 80)->nullable()->index();
            $t->string('price_range', 8)->nullable();  // ₱, ₱₱, ₱₱₱, ₱₱₱₱
            $t->string('region')->nullable();
            $t->string('province')->nullable();
            $t->string('city')->nullable();
            $t->string('address')->nullable();
            $t->decimal('lat', 10, 7)->nullable();
            $t->decimal('lng', 10, 7)->nullable();
            $t->string('phone', 32)->nullable();
            $t->string('email')->nullable();
            $t->string('website')->nullable();
            $t->string('fb')->nullable();
            $t->string('ig')->nullable();
            $t->string('hours_summary')->nullable();
            $t->string('primary_color', 16)->nullable();
            $t->string('secondary_color', 16)->nullable();
            $t->string('logo_path')->nullable();
            $t->string('hero_path')->nullable();
            $t->enum('status', ['draft', 'pending_review', 'published', 'suspended'])->default('draft')->index();
            $t->timestamp('approved_at')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rg_restaurants');
    }
};
