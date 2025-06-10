<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'cif',
        'direccion',
        'codigo_postal',
        'ciudad',
        'provincia',
        'telefono',
        'email',
        'web',
        'logo_path',
        'mapa',
        'notas',
    ];
}
