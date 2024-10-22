<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MateriasPorVer extends Model
{
    use HasFactory;
    protected $table = 'materiasPorVer';
    protected $fillable = [
        'codBanner',
        'codMateria',
        'orden',
        'codprograma',
    ];
}
