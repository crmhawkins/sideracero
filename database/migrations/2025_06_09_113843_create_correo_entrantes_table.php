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
        Schema::create('correo_entrantes', function (Blueprint $table) {
            $table->id();
            $table->string('remitente');
            $table->string('asunto')->nullable();
            $table->longText('cuerpo')->nullable();
            $table->string('adjunto_path')->nullable();
            $table->boolean('analizado')->default(false);
            $table->json('productos_detectados')->nullable();
            $table->timestamp('recibido_en')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('correo_entrantes');
    }
};
