<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 20)->index();
            $table->unsignedBigInteger('shop_id')->index();
            $table->unsignedBigInteger('customer_id')->index();

            $table->text('pickup_address');
            $table->string('pickup_city', 50)->nullable();
            $table->string('pickup_phone', 20)->nullable();
            $table->string('pickup_location_url', 500);
            $table->text('delivery_address');
            $table->string('delivery_city', 50)->nullable();
            $table->string('delivery_street', 255);
            $table->string('delivery_building', 100)->nullable();
            $table->string('delivery_apartment', 50);
            $table->string('delivery_floor', 20);
            $table->string('delivery_location_url', 500)->nullable();
            $table->text('package_description')->nullable();
            $table->double('package_weight')->nullable();
            $table->string('package_dimensions', 50)->nullable();
            $table->decimal('product_cost', 10, 2)->default(0.00);
            $table->unsignedBigInteger('transportation_type_id')->nullable()->index();
            $table->unsignedBigInteger('zone_id')->nullable()->index();
            $table->text('special_instructions')->nullable();
            $table->date('estimated_delivery')->nullable();
            $table->dateTime('actual_delivery')->nullable();
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->unsignedBigInteger('status_id')->default(1)->index();
            $table->enum('payment_status', ['pending', 'paid'])->default('pending');
            $table->enum('payment_method', ['cash'])->default('cash');
            $table->decimal('delivery_fee', 10, 2)->default(0.00);
            $table->unsignedBigInteger('assigned_to')->nullable()->index();
            $table->unsignedBigInteger('pickup_driver_id')->nullable()->index();
            $table->unsignedBigInteger('delivery_driver_id')->nullable()->index();
            $table->unsignedBigInteger('warehouse_id')->nullable()->index();
            $table->timestamp('picked_up_at')->nullable();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->unsignedBigInteger('confirmed_by')->nullable()->index();
            $table->dateTime('confirmed_at')->nullable();
            $table->timestamps();
            $table->foreign('shop_id')->references('id')->on('shops')->cascadeOnDelete();
            $table->foreign('customer_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreign('status_id')->references('id')->on('statuses')->cascadeOnDelete();
            $table->foreign('assigned_to')->references('id')->on('users')->nullOnDelete();
            $table->foreign('pickup_driver_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('delivery_driver_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
            $table->foreign('confirmed_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
