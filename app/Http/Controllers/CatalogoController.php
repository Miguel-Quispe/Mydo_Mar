<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mueble;

class CatalogoController extends Controller
{
    /**
     * Muestra el catálogo con buscador y filtros.
     */
    public function index(Request $request)
    {
        $query = Mueble::with('user');

        // Búsqueda por palabra clave (nombre o descripción)
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('nombre', 'LIKE', "%{$buscar}%")
                  ->orWhere('descripcion', 'LIKE', "%{$buscar}%");
            });
        }

        // Ordenar resultados
        if ($request->filled('orden')) {
            if ($request->orden == 'recientes') {
                $query->latest();
            } elseif ($request->orden == 'antiguos') {
                $query->oldest();
            }
        } else {
            $query->latest();
        }

        $muebles = $query->paginate(9)->withQueryString();

        $favoritosIds = auth()->check() ? auth()->user()->favoritos()->pluck('muebles.id')->toArray() : [];

        return view('catalogo.index', compact('muebles', 'favoritosIds'));
    }

    /**
     * Muestra el detalle del mueble y genera el enlace de WhatsApp.
     */
    public function show($id)
    {
        $mueble = Mueble::with('user')->findOrFail($id);

        // Limpiar el teléfono del maestro (dejar solo números)
        $telefono = preg_replace('/[^0-9]/', '', $mueble->user->telefono ?? '');

        // Agregar código de país si no lo tiene (Bolivia 591)
        if ($telefono && !str_starts_with($telefono, '591')) {
            $telefono = '591' . $telefono;
        }

        // Mensaje predeterminado para WhatsApp
        $mensaje = rawurlencode("Hola {$mueble->user->nombre}, vi tu diseño '{$mueble->nombre}' en Mydo-mar y quisiera más información para cotizar.");
        $linkWhatsapp = $telefono ? "https://wa.me/{$telefono}?text={$mensaje}" : null;

        return view('catalogo.show', compact('mueble', 'linkWhatsapp'));
    }
}