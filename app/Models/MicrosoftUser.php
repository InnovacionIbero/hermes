<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MicrosoftUser extends Model
{
    use HasFactory;

    protected $table = 'microsoft_users'; // Nombre de la tabla en la base de datos

    protected $fillable = [
        'id',
        'name',
        'email',
        // Otros campos relevantes
    ];

    // Definir cualquier relación con otros modelos si es necesario
}