<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void
    {
        Schema::create('mobile_colors', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('mobile_id')->constrained('mobiles')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('color');
            $table->string('image');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('mobile_colors');
    }
};
