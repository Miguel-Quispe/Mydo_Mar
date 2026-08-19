<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mueble;

class MuebleController extends Controller
{
    /**
     * Muestra el formulario para crear un mueble.
     */
    public function create()
    {
        return view('maestro.muebles.create');
    }

    /**
     * Guarda el nuevo mueble en la base de datos.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre'          => 'required|string|max:255',
            'material'        => 'required|string|max:100',
            'precio'          => 'required|numeric|min:0',
            'dimensiones'     => 'nullable|string|max:100',
            'descripcion'     => 'required|string',
            'estructura_json' => 'nullable|string',
            'imagen'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        // Subir imagen si se proporciona
        $rutaImagen = null;
        if ($request->hasFile('imagen')) {
            $rutaImagen = $request->file('imagen')->store('muebles', 'public');
        }

        // Crear registro
        Mueble::create([
            'user_id'         => auth()->id(),
            'nombre'          => $request->nombre,
            'material'        => $request->material,
            'precio'          => $request->precio,
            'dimensiones'     => $request->dimensiones,
            'descripcion'     => $request->descripcion,
            'estructura_json' => $request->estructura_json,
            'imagen_url'      => $rutaImagen,
            'es_publico'      => true,
            'vistas'          => 0,
        ]);

        return redirect()->route('maestro.dashboard')->with('exito', 'Mueble publicado correctamente.');
    }
}
