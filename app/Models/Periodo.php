<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Periodo extends Model
{
    use HasFactory;
    protected $table = 'periodo';
    protected $fillable = [
        'id',
        'mes',
        'formacion_continua',
        'Pregrado_cuatrimestar',
        'Pregrado_semestral',
        'especializacion',
        'maestrias',
        'year',
    ];
}
