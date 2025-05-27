<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->enum('payment_method', ['instapay', 'vodafone_cash', 'cod'])->default('cod');
            $table->enum('payment_status', ['pending', 'confirmed', 'rejected'])->default('pending');
            $table->string('payment_proof')->nullable();
            $table->decimal('total_price', 10, 2);
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
