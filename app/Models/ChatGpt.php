<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatGpt extends Model
{
    protected $fillable = [
        'mensaje',
        'remitente',
        'respuesta'
        // otros campos si tienes más
    ];
}
