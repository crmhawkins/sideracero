<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CorreoEntrante extends Model
{
    protected $fillable = [
        'remitente',
        'asunto',
        'cuerpo',
        'fecha',
        'leido',
        'adjunto_path',
        'analizado',
        'productos_detectados',
        'categoria',
        'recibido_en',
        'respondido',
        'notificado' // ✅ añadido aquí


    ];

    protected $casts = [
        'analizado' => 'boolean',
        'notificado' => 'boolean', // ✅ casteo booleano
        'productos_detectados' => 'array',
        'recibido_en' => 'datetime',
    ];
}
