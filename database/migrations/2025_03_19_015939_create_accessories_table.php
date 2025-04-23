<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        Schema::create('accessories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->foreignUuid('brand_id')->constrained('brands')->cascadeOnDelete()->cascadeOnUpdate();
            $table->text('description')->nullable();
            $table->integer('battery')->nullable();
            $table->integer('speed')->nullable();
            $table->string('color')->nullable();
            $table->string('image');
            $table->decimal('price', 10, 2);
            $table->integer('discount')->nullable()->default(0);
            $table->decimal('final_price', 10, 2)->nullable();
            $table->integer('stock_quantity')->default(0);
            $table->enum('status', ['available', 'out_of_stock', 'coming_soon'])->default('available');
            $table->string('product_type')->default('accessory');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('accessories');
    }
};