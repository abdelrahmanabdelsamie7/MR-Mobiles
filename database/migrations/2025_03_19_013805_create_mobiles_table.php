<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        Schema::create('mobiles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title')->unique();
            $table->string('slug')->unique();
            $table->foreignUuid('brand_id')->constrained('brands')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('model_number');
            $table->text('description')->nullable();
            $table->integer('battery');
            $table->string('processor');
            $table->string('storage');
            $table->string('display');
            $table->decimal('price', 10, 2);
            $table->integer('discount')->nullable()->default(0);
            $table->decimal('final_price', 10, 2)->nullable();
            $table->string('operating_system');
            $table->string('camera')->nullable();
            $table->string('network_support');
            $table->year('release_year');
            $table->string('image_cover');
            $table->enum('status', ['available', 'out_of_stock', 'coming_soon'])->default('available');
            $table->string('product_type')->default('mobile');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('mobiles');
    }
};
