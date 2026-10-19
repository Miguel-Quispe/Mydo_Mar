<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mueble;
use Illuminate\Support\Facades\Storage;

class MaestroController extends Controller
{
    /**
     * Muestra el panel principal del Maestro Carpintero.
     */
    public function dashboard()
    {
        $user = auth()->user();

        $muebles = Mueble::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        $totalMuebles = Mueble::where('user_id', $user->id)->count();

        return view('maestro.dashboard', compact('user', 'muebles', 'totalMuebles'));
    }

    /**
     * Abre el disenador 3D cargando el mueble existente para edicion.
     */
    public function edit($id)
    {
        $mueble = Mueble::where('user_id', auth()->id())->findOrFail($id);

        // Reutiliza la misma vista del disenador 3D, pasando el mueble
        return view('maestro.muebles.create', compact('mueble'));
    }

    /**
     * Actualiza el mueble existente desde el disenador 3D.
     */
    public function update(Request $request, $id)
    {
        $mueble = Mueble::where('user_id', auth()->id())->findOrFail($id);

        $request->validate([
            'nombre'          => 'required|string|max:255',
            'descripcion'     => 'nullable|string',
            'material'        => 'nullable|string|max:255',
            'precio'          => 'nullable|numeric|min:0',
            'dimensiones'     => 'nullable|string|max:255',
            'estructura_json' => 'nullable|string',
            'imagen_base64'   => 'nullable|string',
        ]);

        // Guardar imagen base64 si viene del canvas 3D
        if ($request->filled('imagen_base64')) {
            $base64 = $request->input('imagen_base64');
            if (preg_match('/^data:image\/(\w+);base64,/', $base64, $type)) {
                $imageData = base64_decode(substr($base64, strpos($base64, ',') + 1));
                $ext       = strtolower($type[1]);
                $filename  = 'muebles/' . uniqid() . '.' . $ext;
                Storage::disk('public')->put($filename, $imageData);

                // Eliminar imagen anterior si existia
                if ($mueble->imagen_url) {
                    Storage::disk('public')->delete($mueble->imagen_url);
                }
                $mueble->imagen_url = $filename;
            }
        }

        $mueble->update([
            'nombre'            => $request->nombre,
            'descripcion'       => $request->descripcion ?? $mueble->descripcion,
            'material'          => $request->material ?? $mueble->material,
            'precio'            => $request->precio ?? $mueble->precio,
            'dimensiones'       => $request->dimensiones ?? $mueble->dimensiones,
            'estructura_json'   => $request->estructura_json ?? $mueble->estructura_json,
            'imagen_url'        => $mueble->imagen_url,
        ]);

        return redirect()->route('maestro.dashboard')->with('exito', 'Mueble actualizado correctamente!');
    }

    /**
     * Elimina el mueble de la base de datos y borra su imagen del almacenamiento.
     */
    public function destroy($id)
    {
        $mueble = Mueble::where('user_id', auth()->id())->findOrFail($id);

        if ($mueble->imagen_url) {
            Storage::disk('public')->delete($mueble->imagen_url);
        }

        $mueble->delete();

        return redirect()->route('maestro.dashboard')->with('exito', '¡Mueble eliminado correctamente!');
    }
}