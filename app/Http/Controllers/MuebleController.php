<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mueble;
use Illuminate\Support\Facades\Storage;

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
            'imagen_base64'   => 'nullable|string',
        ]);

        // Subir imagen si se proporciona como archivo o captura base64
        $rutaImagen = null;
        if ($request->hasFile('imagen')) {
            $rutaImagen = $request->file('imagen')->store('muebles', 'public');
        } elseif ($request->filled('imagen_base64')) {
            $data = $request->imagen_base64;
            if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
                $data = substr($data, strpos($data, ',') + 1);
                $ext = strtolower($type[1]) === 'png' ? 'png' : 'jpg';
                $decoded = base64_decode($data);
                if ($decoded !== false) {
                    $fileName = 'muebles/' . uniqid('mueble_3d_') . '.' . $ext;
                    Storage::disk('public')->put($fileName, $decoded);
                    $rutaImagen = $fileName;
                }
            }
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

        return redirect()->route('maestro.dashboard')->with('exito', 'Mueble publicado correctamente en el catálogo.');
    }
}
