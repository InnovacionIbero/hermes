<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogAplicacion extends Model
{
    use HasFactory;
    protected $table = 'logAplicacion';
    protected $fillable = [
        'idInicio',
        'idFin',
        'fechaInicio',
        'fechaFin',
        'accion',
        'tabla_afectada',
        'descripcion'
    ];
}
