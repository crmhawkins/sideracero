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
        Schema::create('chat_gpts', function (Blueprint $table) {
            $table->id();
            $table->string('remitente')->nullable(); // por si hay usuarios
            $table->text('mensaje'); // lo que escribe el usuario
            $table->text('respuesta')->nullable(); // respuesta IA
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_gpts');
    }
};
