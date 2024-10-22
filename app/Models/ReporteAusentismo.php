<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReporteAusentismo extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'V_Reporte_Ausentismo_memory';

    protected $primaryKey = 'id';
    public $incrementing = true;
    
    protected $fillable = [
        'IdCurso',
        'Nombrecurso',
        'Nombrecorto',
        'Semestre_cuatrimestre',
        'IdTutor',
        'NombreTutor',
        'email_userTutor',
        'Ciclo',
        'Duracion_8_16_Semanas',
        'Nivel',
        'VisibilidadCurso',
        'FechaInicio',
        'Cod_materia',
        'Cod_programa',
        'Fecha_Creacion_Matricula',
        'Periodo_Rev',
        'Tipo_Estudiante',
        'No_Documento',
        'Id_Banner',
        'Estado_Banner',
        'Autorizado_ASP',
        'Sello',
        'Operador',
        'Programa',
        'Facultad',
        'Nombre',
        'Apellido',
        'Email',
        'Ult_AccesoACurso',
        'Ultacceso_Plataforma',
        'Riesgo',
        'Total_Actividades',
        'Actividades_Por_Calificar',
        'Nota_Primer_Corte',
        'Nota_Segundo_Corte',
        'Nota_Tercer_Corte',
        'Nota_Acumulada',
        'nota_proyectada',
        'riezgo_academico',
        'repitente',
        'fecha_insercion',
        'revisado',
        'revisado_cursos',
        'transversal',
    ];

    public $timestamps = false;

}
