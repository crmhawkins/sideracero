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
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('cif');
            $table->string('direccion');
            $table->string('codigo_postal');
            $table->string('ciudad');
            $table->string('provincia');
            $table->string('telefono');
            $table->string('email');
            $table->string('web')->nullable();
            $table->string('logo_path')->nullable();
            $table->text('mapa')->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};
