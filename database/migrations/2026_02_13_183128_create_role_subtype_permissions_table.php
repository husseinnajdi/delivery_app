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
        Schema::create('role_subtype_permissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('role_subtype_id');
            $table->unsignedBigInteger('permission_id');
            $table->foreign('role_subtype_id')->constrained('role_subtypes')->onDelete('cascade');
            $table->foreign('permission_id')->constrained('permissions')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_subtype_permissions');
    }
};
