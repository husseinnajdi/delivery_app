<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username');
            $table->string('email')->unique();
            $table->string('password');
            $table->integer('role_id')->nullable();
            $table->integer('role_subtype_id')->nullable();
            $table->string('status')->default('active');
            $table->string('full_name')->nullable();
            $table->integer('phone')->nullable();
            $table->string('image')->nullable();
            $table->decimal('profit_balance', 10, 2)->default(0);
            $table->string('FCMtoken')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
