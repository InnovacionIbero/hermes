<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class passwordController extends Controller
{
    public function index() {
        return view('password.index');
    }
  
}