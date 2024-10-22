<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HistorialEstudianteController extends Controller
{
    /** 
     * Función para cargar la vista del historial academico de  los estudiantes
     * @return view del historial de los estudiantes
     * */
    public function historial()
    {
        // Aquí va la lógica para obtener el historial del estudiante con el ID proporcionado

        // Supongamos que tienes una variable $historial con los datos del historial del estudiante
        $historial = [0=>'test'];

        // Pasar los datos del historial a la vista y cargarla
        return view('vistas.estudiantes.index', ['historial' => $historial]);
    }

}
