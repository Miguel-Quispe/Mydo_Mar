<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mueble;

class FavoritoController extends Controller
{
    /**
     * Muestra la lista de muebles guardados por el cliente.
     */
    public function index()
    {
        $favoritos = auth()->user()->favoritos()->latest()->paginate(9);

        return view('favoritos.index', compact('favoritos'));
    }

    /**
     * Alterna (Agrega o Quita) un mueble de los favoritos del usuario.
     */
    public function toggle($muebleId)
    {
        $user = auth()->user();
        
        // toggle agrega la relación si no existe, o la elimina si ya está
        $status = $user->favoritos()->toggle($muebleId);

        if (count($status['attached']) > 0) {
            $mensaje = 'Mueble guardado en tus favoritos';
        } else {
            $mensaje = 'Mueble removido de tus favoritos';
        }

        return back()->with('exito_favorito', $mensaje);
    }
}
