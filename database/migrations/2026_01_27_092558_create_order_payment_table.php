<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_payments', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 25)->index();
            $table->unsignedBigInteger('currency_id')->index();
            $table->decimal('amount', 15, 2);
            $table->decimal('amount_usd', 15, 2);
            $table->unsignedBigInteger('collected_by')->index();
            $table->timestamp('collected_at')->nullable()->useCurrent();
            $table->timestamps(); 
            $table->timestamp('created_at')->useCurrent();
            $table->foreign('order_number')->references('order_number')->on('orders')->cascadeOnDelete();
            $table->foreign('currency_id')->references('id')->on('currencies')->cascadeOnDelete();
            $table->foreign('collected_by')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_payments');
    }
};
