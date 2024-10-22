<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlertasTempranas extends Model
{
    use HasFactory;
    protected $table = 'alertas_tempranas';
    protected $fillable = [
        'idbanner',
        'tipo_estudiante',
        'desccripcion',
        'codprograma',
        'periodo',
        'activo',
        'tipo',
        'desccripcion',
        'created_at',
        'updated_at',
    ];
}
