<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;
use Socialize;


class MicrosoftAuthController extends Controller
{

        public function redirectToMicrosoft()
        {
            return Socialite::with('graph')->redirect();
        }
    
        public function handleMicrosoftCallback()
        {
            $user = Socialite::with('graph')->user();
            $correo = $user->email;

            $consulta = User::where('email','=',$correo)->first();
            
            if(!isset($consulta) || empty($consulta))
            {
                return redirect('login')->withErrors(['errors' => 'Tu cuenta no está en la base de datos de Hermes, ponte en contacto con el equipo de desarrollo para que tu cuenta sea incluida.']);
            }

            Auth::login($consulta);

            //dd(Auth::user());
            // Implementa la lógica de autenticación o registro aquí
            return redirect()->route('login.home'); // Redirige al usuario después del inicio de sesión
        }
    
}
