<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mafi extends Model
{
    use HasFactory;
    protected $table = 'datosMafi';
    protected $fillable = [
        'id',
        'idBanner',
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
