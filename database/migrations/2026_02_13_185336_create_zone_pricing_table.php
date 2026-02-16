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
        Schema::create('zone_pricing', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('zone_id');
            $table->decimal('price', 10, 2);
            $table->unsignedBigInteger('transportation_type_id');
            $table->foreign('transportation_type_id')->references('id')->on('transportation_types')->onDelete('cascade');
            $table->foreign('zone_id')->references('id')->on('zones')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('zone_pricing');
    }
};
