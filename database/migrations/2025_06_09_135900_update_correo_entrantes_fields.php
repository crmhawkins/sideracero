<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCorreoEntrantesFields extends Migration
{
    public function up()
    {
        Schema::table('correo_entrantes', function (Blueprint $table) {
            if (!Schema::hasColumn('correo_entrantes', 'fecha')) {
                $table->timestamp('fecha')->nullable()->after('cuerpo');
            }
            if (!Schema::hasColumn('correo_entrantes', 'leido')) {
                $table->boolean('leido')->default(false)->after('fecha');
            }
            if (!Schema::hasColumn('correo_entrantes', 'adjunto_path')) {
                $table->string('adjunto_path')->nullable()->after('leido');
            }
            if (!Schema::hasColumn('correo_entrantes', 'analizado')) {
                $table->boolean('analizado')->default(false)->after('adjunto_path');
            }
            if (!Schema::hasColumn('correo_entrantes', 'productos_detectados')) {
                $table->json('productos_detectados')->nullable()->after('analizado');
            }
            if (!Schema::hasColumn('correo_entrantes', 'categoria')) {
                $table->string('categoria')->nullable()->after('productos_detectados');
            }
            if (!Schema::hasColumn('correo_entrantes', 'recibido_en')) {
                $table->timestamp('recibido_en')->nullable()->after('categoria');
            }
        });
    }

    public function down()
    {
        Schema::table('correo_entrantes', function (Blueprint $table) {
            $table->dropColumn([
                'fecha',
                'leido',
                'adjunto_path',
                'analizado',
                'productos_detectados',
                'categoria',
                'recibido_en',
            ]);
        });
    }
}
