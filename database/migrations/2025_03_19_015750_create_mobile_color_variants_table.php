<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        Schema::create('mobile_color_variants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('mobile_id')->constrained('mobiles')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('color_id')->constrained('colors')->cascadeOnDelete()->cascadeOnUpdate();
            $table->integer('stock_quantity')->default(0);
            $table->unique(['mobile_id', 'color_id']);
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('mobile_color_variants');
    }
};
