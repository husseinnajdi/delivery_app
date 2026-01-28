<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('shop_id')->constrained('shops')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
            $table->string('pickup_address');
            $table->string('pickup_phone');
            $table->string('customer_address');
            $table->string('customer_phone');
            $table->string('package_description');
            $table->double('package_weight');
            $table->double('order_cost');
            $table->timestamp('estimated_delivery')->nullable();
            $table->string('location_link')->nullable();
            $table->string('special_instructions')->nullable();
            $table->string('actual_delivery')->nullable();
            $table->string('priority');
            $table->foreignId('status_id')->constrained('status')->cascadeOnDelete();
            $table->string('payment_status')->default('unpaid');
            $table->double('delivery_fee');
            $table->foreignId('pickup_by')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('delivered_by')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->foreignId('confirmed_by')->nullable()->constrained('users')->cascadeOnDelete();
        
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
