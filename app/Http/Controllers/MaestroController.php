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
     * Muestra el formulario para editar un mueble existente.
     */
    public function edit($id)
    {
        $mueble = Mueble::where('user_id', auth()->id())->findOrFail($id);

        return view('maestro.muebles.edit', compact('mueble'));
    }

    /**
     * Actualiza el mueble existente en la base de datos.
     */
    public function update(Request $request, $id)
    {
        $mueble = Mueble::where('user_id', auth()->id())->findOrFail($id);

        $request->validate([
            'nombre'            => 'required|string|max:255',
            'descripcion'       => 'required|string',
            'parametros_diseno' => 'nullable|string',
            'imagen'            => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
        ]);

        // Si se sube una nueva imagen, eliminamos la anterior y guardamos la nueva
        if ($request->hasFile('imagen')) {
            if ($mueble->imagen_url) {
                Storage::disk('public')->delete($mueble->imagen_url);
            }
            $mueble->imagen_url = $request->file('imagen')->store('muebles', 'public');
        }

        $mueble->update([
            'nombre'            => $request->nombre,
            'descripcion'       => $request->descripcion,
            'parametros_diseno' => $request->parametros_diseno,
            'imagen_url'        => $mueble->imagen_url,
        ]);

        return redirect()->route('maestro.dashboard')->with('exito', '¡Mueble actualizado correctamente!');
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