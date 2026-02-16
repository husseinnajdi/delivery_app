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
        Schema::create('zone_cities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('zone_id');
            $table->string('city_name');
            $table->foreign('zone_id')->references('id')->on('zones')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zone_cities');
    }
};
