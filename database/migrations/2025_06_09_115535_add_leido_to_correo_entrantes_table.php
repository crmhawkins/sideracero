<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('correo_entrantes', function (Blueprint $table) {
            $table->boolean('leido')->default(false)->after('fecha');
        });
    }

    public function down(): void
    {
        Schema::table('correo_entrantes', function (Blueprint $table) {
            $table->dropColumn('leido');
        });
    }
};
