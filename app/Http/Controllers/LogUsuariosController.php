<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CambioPassRequest;
use App\Http\Requests\UsuarioLoginRequest;
use App\Http\Requests\CrearFacultadRequest;
use App\Http\Requests\ProgramasRequest;
use App\Models\Facultad;
use App\Models\Roles;
use App\Models\User;
use App\Models\Usuario;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
// use Yajra\DataTables\DataTables;

class LogUsuariosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public static function registrarLog ($accion,$descripcion, $tablaAfectada, $original, $actualizada){
        $user = auth()->user();
        
        $insert = DB::table('logUsuarios')->insert([
            'id_usuarios' => $user->id,
            'nombre' => $user->nombre,
            'correo' => $user->email,
            'accion'=> $accion,
            'descripcion' => $descripcion,
            'tabla_afectada' => $tablaAfectada,
            'informacion_original' => $original,
            'informacion_actualizada' => $actualizada,
            'fecha' => now(),
        ]);
    }
}