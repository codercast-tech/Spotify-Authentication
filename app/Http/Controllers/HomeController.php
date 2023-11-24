<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function dashboard()
    {
        // Lógica para mostrar el dashboard aquí
        return view('dashboard'); // Asegúrate de tener una vista 'dashboard' creada
    }
}
