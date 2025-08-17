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
            $table->unsignedBigInteger('api_id')->unique();
            $table->string('name', 50);
            $table->string('last_name', 50)->nullable();
            $table->string('email')->unique();
            
            // --- CAMBIO IMPORTANTE ---
            // Añadimos la columna 'password'. Es un requisito técnico de Laravel,
            // aunque la llenemos con un valor aleatorio.
            $table->string('password'); 

            $table->rememberToken(); // Buena práctica añadir esto también.
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
