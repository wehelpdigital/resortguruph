<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rg_resorts', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('owner_id');
            $t->string('name', 255);
            $t->string('slug', 255)->unique();
            $t->string('tagline', 500)->nullable();
            $t->longText('description_html')->nullable();
            $t->string('region', 100)->nullable();
            $t->string('province', 100)->nullable();
            $t->string('city', 100)->nullable();
            $t->string('address', 500)->nullable();
            $t->decimal('lat', 10, 7)->nullable();
            $t->decimal('lng', 10, 7)->nullable();
            $t->string('phone', 64)->nullable();
            $t->string('email', 191)->nullable();
            $t->string('website', 255)->nullable();
            $t->string('fb', 255)->nullable();
            $t->string('ig', 255)->nullable();
            $t->string('tt', 255)->nullable();
            $t->string('price_range', 64)->nullable();
            $t->string('capacity', 64)->nullable();
            $t->longText('amenities_json')->nullable();
            $t->string('primary_color', 16)->default('#556ee6');
            $t->string('secondary_color', 16)->default('#34c38f');
            $t->string('logo_path', 500)->nullable();
            $t->string('hero_path', 500)->nullable();
            $t->enum('status', ['draft', 'pending_review', 'published', 'suspended'])->default('draft');
            $t->timestamp('approved_at')->nullable();
            $t->timestamps();
            $t->index('status');
            $t->index('city');
            $t->index('province');
            $t->foreign('owner_id')->references('id')->on('rg_owners')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rg_resorts');
    }
};
