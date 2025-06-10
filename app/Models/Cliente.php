<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $fillable = [
        'nombre_empresa',
        'cif',
        'persona_contacto',
        'email',
        'telefono',
        'telefono_secundario',
        'direccion',
        'codigo_postal',
        'ciudad',
        'provincia',
        'pais',
        'web',
        'sector',
        'notas',
    ];
}
