<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sender_user_id')->nullable()->index();
            $table->string('title', 255);
            $table->string('type', 50)->default('info');
            $table->integer('order_id')->nullable()->index();
            $table->text('message');
            $table->boolean('is_read')->default(false);
            $table->timestamps();   
            $table->foreign('order_id')->references('id')->on('orders')->nullOnDelete();    
            $table->foreign('sender_user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_messages');
    }
};
