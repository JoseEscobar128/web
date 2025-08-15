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
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // ID local
            $table->unsignedBigInteger('api_id')->unique(); // ID real en la API
            $table->string('name', 50);
            $table->string('last_name', 50)->nullable(); // si lo ocupas
            $table->string('email')->unique();
            $table->string('token')->nullable(); // token devuelto por la API
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
