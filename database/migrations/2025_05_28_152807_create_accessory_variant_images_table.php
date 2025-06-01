<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        Schema::create('accessory_variant_images', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('accessory_color_variant_id')->constrained('accessory_color_variants')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('image');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('accessory_variant_images');
    }
};