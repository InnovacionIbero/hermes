<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\ActualizarPassRequest;
use App\Http\Requests\CambioPassRequest;
use Illuminate\Support\Facades\Hash;

class cambioController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['only' => ['consultaCambio', 'cambioSave']]);
    }

    // * Método para acceder a la vista de reestablecer contraseña *
    public function index()
    {
        return view('reestablecerpassword.index');
    }
    // * Método para acceder a la vista de cambio de contraseña *
    public function nueva($id)
    {
        //$id = decrypt($id);
        return view('reestablecerpassword.nueva', ['id' => $id]);
    }

    /** Método que consulta los datos ingresados por el usuario para verificar si existe en la base de datos
     *  y así poder llevar a cabo del cambio de contraseña
     */
    public function consultar(Request $request)
    {

        // * Consulta MySQL *
        $consulta = DB::table('users')->where([
            ['email', '=', $request->correo],
            ['documento', '=', $request->documento]
        ])->get();

        /* Si encuentra el usuario en la base de datos le permite acceder al formulario para
        cambiar la contraseña */
        if ($consulta == '[]') {
            return redirect()->route('cambio.index')->withErrors(['errors' => 'Credenciales inválidas - Verifica tus datos']);
        } else {
            $id = encrypt($consulta[0]->id);
            //dd($id);
            return redirect()->route('cambio.nueva', ['id' => $id]);
        }
    }

    // * Método que actualiza la contraseña del usuario

    public function actualizar(ActualizarPassRequest $request)
    {
        $id =decrypt($request->id);
        $cambioPass = User::where('id', '=', $id)->update(['password' => bcrypt($request->confirmar)]);
        if ($cambioPass) {
            return redirect()->route('login.index')->with('success', 'Contraseña cambiada');
        } else {
            return redirect()->route('home.index',['id' => $request->id])->withErrors(['errors' => 'Las contraseñas deben ser iguales']);
        }
    }

    /**
     * Función para mostrar el formulario para cambiar la contraseña
     * recibe como parametro el idBanner cifrado
     */
    public function consultaCambio($idBanner)
    {
        $idBanner = decrypt($idBanner);
        $user = auth()->user();
        $rol_db = DB::table('roles')->where([['id', '=', $user->id_rol]])->get();

        $nombre_rol = $rol_db[0]->nombreRol;
        auth()->user()->nombre_rol = $nombre_rol;

        if (!empty($user->id_facultad) && ($user->id_facultad) != "NULL") {
            $consulta = DB::table('facultad')->where([['id', '=', $user->id_facultad]])->select('nombre')->first();
            $facultad = $consulta->nombre;
        } else {
            $facultad = NULL;
        }

        $programa = trim($user->programa, ';');
        $programas = explode(";", $programa);
        if (empty($programa) || $programa == NULL) :
            $nombre_programas = NULL;
        else :
            foreach ($programas as $key => $value) {
                $consulta = DB::table('programas')->select('programa')->where('id', '=', $value)->get();
                $nombre_programas[$value] = $consulta[0]->programa;
            }
        endif;

        $datos = array(
            'rol' => $nombre_rol,
            'facultad' => $facultad,
            'programa' => $nombre_programas,
        );
        $user = User::where('id_banner', $idBanner)->first();
        if ($user != []) :
            return view('reestablecerpassword.cambio')->with('datos', $datos);
        else :
            return redirect()->route('home.index');
        endif;
    }

    /**
     * Función para realizar el update de la contraseña
     * recibiendo los datos del formulario por medio del CambioPassRequest
     * validando que se traigan datos y que las contraseñas nuevas coincidan
     */
    public function cambioSave(CambioPassRequest $request)
    {
        $user = DB::table('users')->select('users.email', 'users.password', 'users.id_banner')->where('id', '=', $request->id)->first();
        /** varificamos si la contraseña actual es identica a la guarda en la DB cuando se creo el usuario, se usa Hash::check para decifrar la contraseña guardada */
        if (Hash::check($request->password_actual, $user->password)) :
            $cambioPass = User::where('id', '=', $request->id)->update(['password' => bcrypt($request->password)]);
            /**si el update se hace correctamente se redirige al formulario de login */
            if ($cambioPass) :
                return redirect()->route('user.perfil', ['id' => encrypt($request->id)])->with('success', 'Contraseña cambiada');
            else :
                /**si el update falla redirige nuevamente al formulario de cambio de contraseña */
                return redirect()->route('cambio.cambio', ['idbanner' => encrypt($user->id_banner)])->withErrors(['errors' => 'Error al modificar la contraseña.']);
            endif;
        else :
            /** si la contraseña actual no corresponde a la registrada en la DB, redirige al formulario de cambio de contraseña */
            return redirect()->route('cambio.cambio', ['idbanner' => encrypt($user->id_banner)])->withErrors(['errors' => 'Ingrese contraseña actual.']);
        endif;
    }
}
