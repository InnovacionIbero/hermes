<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndiceCambiosMafi extends Model
{
    use HasFactory;
    protected $table = 'indece_cambios_mafi';
    protected $fillable = [
        'idbanner',
        'accion',
        'descripcion',
        'fecha',
    ];
}
