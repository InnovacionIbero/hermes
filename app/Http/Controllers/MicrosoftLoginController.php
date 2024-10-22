<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Socialize;

class MicrosoftLoginController extends \App\Http\Controllers\Controller
{

    /**
     * Redirect the user to the Graph authentication page.
     *
     * @return Response
     */
    public function redirectToProvider()
    {
        return Socialite::with('graph')->redirect();
    }

    /**
     * Obtain the user information from graph.
     *
     * @return Response
     */
    public function handleProviderCallback(Request $request)
    {
        $user = Socialite::with('graph')->user();

        // $user->token;
    }

    //Id. de secreto   f77ef6ab-093b-4958-89e8-11e7af462a46
    //Valor             RpC8Q~34ihtqE-8jZEYiDv8Ef2S4oSG0g9jtpc9S
   
   




}
