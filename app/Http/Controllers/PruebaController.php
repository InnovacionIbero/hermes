<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PruebaController extends Controller
{
    //
    function index() {
        $prubeaDB = DB::table('datosMafi')->first();

        // Usar la conexión SQL Server ('sqlsrv')
        $datosSQLServer = DB::connection('sqlsrv')->table('MAFI')->where('id',1)->get();
        dd($datosSQLServer);
    }
}
