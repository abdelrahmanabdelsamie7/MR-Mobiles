<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        Schema::create('accessory_color_variants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('accessory_id')->constrained('accessories')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('color_id')->constrained('colors')->cascadeOnDelete()->cascadeOnUpdate();
            $table->integer('stock_quantity')->default(0);
            $table->unique(['accessory_id', 'color_id']);
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('accessory_color_variants');
    }
};
