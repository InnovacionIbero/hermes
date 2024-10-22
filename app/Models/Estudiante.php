<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estudiante extends Model
{
    use HasFactory;
    protected $table = 'estudiantes';
    protected $fillable = [
        'homologante',
        'nombre',
        'programa',
        'bolsa',
        'operador',
        'nodo',
        'tipo_estudiante',
        'materias_faltantes',
        'programado_ciclo1',
        'programado_ciclo2',
        'programado_extra',
        'tiene_historial',
        'programaActivo',
        'observacion',
        'marca_ingreso',
    ];
}
