<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model;


class MicrosoftGraphController extends Controller
{
    public function getUserProfile()
    {
        // Aquí se realiza la interacción con Microsoft Graph
        // Utiliza los tokens de acceso para autenticarte y realizar acciones.

        $graph = new Graph();
        $graph->setAccessToken(session('RpC8Q~34ihtqE-8jZEYiDv8Ef2S4oSG0g9jtpc9S'));

        $user = $graph->createRequest('GET', '/me')
                     ->setReturnType(Model\User::class)
                     ->execute();

        return view('user-profile', ['user' => $user]);
    }
}
