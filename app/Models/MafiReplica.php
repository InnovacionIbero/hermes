<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MafiReplica extends Model
{
    use HasFactory;
    protected $table = 'datosMafiReplica';
    protected $fillable = [
        'id',
        'idbanner',
        'primer_apellido',
        'programa',
        'codprograma',
        'cadena',
        'periodo',
        'estado',
        'tipoestudiante',
        'ruta_academica',
        'sello',
        'operador',
        'autorizado_asistir',
    ];
}
