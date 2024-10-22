<?php

namespace App\Http\Controllers;

use App\Http\Requests\CambioPassRequest;
use App\Http\Requests\UsuarioLoginRequest;
use App\Models\User;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\MenuHelper;

class LoginController extends Controller
{
    
    public function index()
    {
        return view('login.index');
    }

    /** funcion para iniciasr session */
    public function home()
    {
        /** verificamos  si el no se a logueado */
        if (Auth::check()) :
            /** si es la primera vez en la plataforma se le solicita cambio de contraseña */
            if(auth()->user()->ingreso_plataforma == 0):

                return  redirect()->route('login.cambio');

            endif;
        /** de lo contrario redirigimos a la vista correspondiente */

        //return "Hola Usuario".auth()->user()->nombre;
        /// traemos los roles de la base de datos para poder cargar la vista
            $rol_db=DB::table('roles')->where([['id','=',auth()->user()->id_rol]])->get();

            /*traempos el nombre del rol para cargar la vista*/
            $nombre_rol=$rol_db[0]->nombreRol;
            auth()->user()->nombre_rol=$nombre_rol;

            $menu = MenuHelper::getMenuForRole($rol_db[0]->Menu);
            session()->put('menu',$menu);

            $fechaIngreso = DB::table('users')->where('id', '=',auth()->user()->id)->update(['fecha_inicio_sesion' => now()]);

            $ingresosPlataforma = DB::table('users')->where('id', '=',auth()->user()->id)->increment('ingresos_a_plataforma', 1);
            
            $menu = MenuHelper::getMenuForRole($rol_db[0]->Menu);
            if(auth()->user()->id_facultad){
                $facultades = explode(",", auth()->user()->id_facultad);
    
                if(in_array(8, $facultades) || in_array(9, $facultades) || in_array(10, $facultades)){
                    $menu = MenuHelper::getMenuForRole('usuario_transversal');
                }
            }
            session()->put('menu',$menu);

            return redirect()->route('home.index');
        endif;

        return redirect()->route('login.index');
    }

    /** funcion para llamar kla vista de cambio de contraseña */
    public function cambio(){
        return view('password.index');
    }

    /** funcion para realizar el  cambio de la contraseña */
    public function cambioPass(CambioPassRequest $request){


        $user = DB::table('users')->select('users.email','users.password')->where('id','=',$request->id)->get();     


        if(Hash::check($request->password_actual,$user[0]->password)):  
            $cambioPass = User::where('id','=',$request->id)->update(['password'=> bcrypt($request->password),'ingreso_plataforma'=>1]);
            if($cambioPass):
                return redirect()->route('home.index');
            endif;
        endif;
    }

     /** funcion para realizar el  cambio de la contraseña */
     public function cambio_Pass(CambioPassRequest $request){

        /** verificamos la base de datos  con los datos necesarios para realizar el cambio de contraseña */
        $user = DB::table('users')->select('users.email','users.password')->where('id','=',$request->id)->where('documento','=',$request->password_actual)->get();
        /** varificamos si la contraseña actual es identica a la guarda en la DB cuando se creo el usuario, se usa Hash::check para decifrar la contraseña guardada */
        if(Hash::check($request->password_actual,$user[0]->password)):
            /** Se realiza el update de la password si el id y el documento son iguales a los datos que vienen del formulario  */
            $cambioPass = User::where('id','=',$request->id)->where('documento','=',$request->password_actual)->update(['password'=> bcrypt($request->password),'ingreso_plataforma'=>1]);
            /**si el update se hace correctamente se redirige al formulario de login */
            if($cambioPass):
                return  redirect()->route('home.index');
            endif;
        endif;
    }



    /** funcion de verificacion de usuario */
    public function login(UsuarioLoginRequest $req)
    {
        /**  autenticar al usuario en el sistema, realizando la validacion con el metodo getCredentials del Request
         * solo se valida el email y la password
         */
        $credentials = $req->getCredentials();
        /** Si regresa True, se regresa una instancion del proveedor de auteticación, para recuperar los datos e interartuar con la DB
         * y obtener las credenciales del usuario
         */
        if (Auth::attempt($credentials)) {

            $user = Auth::getProvider()->retrieveByCredentials($credentials);

            Auth::login($user, $remember = true);

            /**se llama el metodo authenticated para realizar el redireccionamiento al home  */
            return $this->authenticated($req, $user);
        }

        return redirect()
            ->back()
            ->withErrors(['errors' => 'Credenciales inválidas - Verifica tus datos']);
    }


    /** funcion para redirigir al home si el usuario esta autenticado */
    public function authenticated(Request  $request, $user)
    {
        return redirect()->route('login.home');
    }

    /** funcion para cerrar sesion  */
    public function logout(Request $request)
    {
        Session::flush();
        Auth::logout();
        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()
            ->route('login.index');
    }

    protected function redirectTo()
    {
        // Personaliza la redirección después de la expiración de la sesión
        return route('login.index');
    }

}
