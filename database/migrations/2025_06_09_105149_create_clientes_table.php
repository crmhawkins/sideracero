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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_empresa');
            $table->string('cif')->unique();
            $table->string('persona_contacto');
            $table->string('email');
            $table->string('telefono');
            $table->string('telefono_secundario')->nullable();
            $table->string('direccion');
            $table->string('codigo_postal');
            $table->string('ciudad');
            $table->string('provincia');
            $table->string('pais')->nullable();
            $table->string('web')->nullable();
            $table->string('sector')->nullable();
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
