<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MuebleMaterial;
use App\Models\MuebleCatalogoHerrajeGlobal;
use Illuminate\Support\Facades\DB;

class MaterialCostoController extends Controller
{
    /**
     * Muestra la pantalla de costos de materiales (exclusiva para maestros y empresas).
     */
    public function index()
    {
        $user = auth()->user();

        // Control de acceso: Solo Maestros (rol 1) y Empresas / Admins (rol 3, admin o B2B)
        $isMaestro = $user->rol_id == 1 || str_starts_with($user->clave_rol ?? '', 'MAE') || str_starts_with($user->clave_rol ?? '', 'MST');
        $isEmpresa = $user->rol_id == 3 || str_starts_with($user->clave_rol ?? '', 'EMP') || $user->admin;

        if (!$isMaestro && !$isEmpresa) {
            abort(403, 'Acceso restringido: Esta pantalla de costos de materiales es exclusiva para Maestros Carpinteros y Empresas B2B.');
        }

        // Consultar materiales de melamina y tableros
        $materiales = MuebleMaterial::all();

        // Si la tabla estuviese vacía, proveer lista estándar con precios oficiales
        if ($materiales->isEmpty()) {
            $materiales = collect([
                (object)[
                    'id_material_muebles' => 1,
                    'nombre_material_muebles' => 'Melamina Blanco Frost 18mm',
                    'tipo_material_muebles' => 'Melamina',
                    'espesor_material_muebles' => 18.00,
                    'precio_lamina_muebles' => 168.00,
                    'textura' => 'melamina_blanca.jpg'
                ],
                (object)[
                    'id_material_muebles' => 2,
                    'nombre_material_muebles' => 'Melamina Roble Natural 18mm',
                    'tipo_material_muebles' => 'Melamina',
                    'espesor_material_muebles' => 18.00,
                    'precio_lamina_muebles' => 248.00,
                    'textura' => 'roble_natural.jpg'
                ],
                (object)[
                    'id_material_muebles' => 3,
                    'nombre_material_muebles' => 'Melamina Nogal Oscuro 18mm',
                    'tipo_material_muebles' => 'Melamina',
                    'espesor_material_muebles' => 18.00,
                    'precio_lamina_muebles' => 255.00,
                    'textura' => 'nogal_oscuro.jpg'
                ],
                (object)[
                    'id_material_muebles' => 4,
                    'nombre_material_muebles' => 'Melamina Gris Grafito 18mm',
                    'tipo_material_muebles' => 'Melamina',
                    'espesor_material_muebles' => 18.00,
                    'precio_lamina_muebles' => 238.00,
                    'textura' => 'gris_grafito.jpg'
                ],
                (object)[
                    'id_material_muebles' => 5,
                    'nombre_material_muebles' => 'Melamina Mármol Calacatta 18mm',
                    'tipo_material_muebles' => 'Melamina',
                    'espesor_material_muebles' => 18.00,
                    'precio_lamina_muebles' => 265.00,
                    'textura' => 'marmol_calacatta.jpg'
                ],
                (object)[
                    'id_material_muebles' => 6,
                    'nombre_material_muebles' => 'Melamina Haya Madera 18mm',
                    'tipo_material_muebles' => 'Melamina',
                    'espesor_material_muebles' => 18.00,
                    'precio_lamina_muebles' => 242.00,
                    'textura' => 'haya_madera.jpg'
                ],
                (object)[
                    'id_material_muebles' => 7,
                    'nombre_material_muebles' => 'Fondo MDF LAC 3mm Blanco (1 Cara)',
                    'tipo_material_muebles' => 'Fondo MDF',
                    'espesor_material_muebles' => 3.00,
                    'precio_lamina_muebles' => 42.00,
                    'textura' => 'melamina_blanca.jpg'
                ]
            ]);
        }

        // Consultar herrajes globales y tornillería
        $herrajes = MuebleCatalogoHerrajeGlobal::all();
        if ($herrajes->isEmpty()) {
            $herrajes = collect([
                (object)[
                    'id_catalogo_herraje_global' => 1,
                    'nombre_herraje_global' => 'Bisagra Cazoleta 35mm Recta Cierre Suave (Par)',
                    'tipo_visibilidad_herraje' => 'oculto',
                    'precio_referencial' => 8.50,
                    'unidad' => 'Par',
                    'categoria' => 'Bisagras'
                ],
                (object)[
                    'id_catalogo_herraje_global' => 2,
                    'nombre_herraje_global' => 'Bisagra Cazoleta 35mm Semi-curva Cierre Suave (Par)',
                    'tipo_visibilidad_herraje' => 'oculto',
                    'precio_referencial' => 8.50,
                    'unidad' => 'Par',
                    'categoria' => 'Bisagras'
                ],
                (object)[
                    'id_catalogo_herraje_global' => 3,
                    'nombre_herraje_global' => 'Corredera Telescópica Pesada 45cm Cierre Suave (Par)',
                    'tipo_visibilidad_herraje' => 'oculto',
                    'precio_referencial' => 28.00,
                    'unidad' => 'Par',
                    'categoria' => 'Correderas'
                ],
                (object)[
                    'id_catalogo_herraje_global' => 4,
                    'nombre_herraje_global' => 'Corredera Telescópica Pesada 50cm Cierre Suave (Par)',
                    'tipo_visibilidad_herraje' => 'oculto',
                    'precio_referencial' => 32.00,
                    'unidad' => 'Par',
                    'categoria' => 'Correderas'
                ],
                (object)[
                    'id_catalogo_herraje_global' => 5,
                    'nombre_herraje_global' => 'Pistón a Gas 80N para Puerta Elevable',
                    'tipo_visibilidad_herraje' => 'oculto',
                    'precio_referencial' => 12.00,
                    'unidad' => 'Unidad',
                    'categoria' => 'Pistones'
                ],
                (object)[
                    'id_catalogo_herraje_global' => 6,
                    'nombre_herraje_global' => 'Tirador Barra Tubular Acero Inox 160mm',
                    'tipo_visibilidad_herraje' => 'visto',
                    'precio_referencial' => 6.50,
                    'unidad' => 'Unidad',
                    'categoria' => 'Tiradores'
                ],
                (object)[
                    'id_catalogo_herraje_global' => 7,
                    'nombre_herraje_global' => 'Perfil Jalador Gola Aluminio Anodizado (Tira 3m)',
                    'tipo_visibilidad_herraje' => 'visto',
                    'precio_referencial' => 48.00,
                    'unidad' => 'Tira',
                    'categoria' => 'Tiradores'
                ],
                (object)[
                    'id_catalogo_herraje_global' => 8,
                    'nombre_herraje_global' => 'Tornillos Drywall 4 x 50 mm (Caja 500u)',
                    'tipo_visibilidad_herraje' => 'oculto',
                    'precio_referencial' => 32.00,
                    'unidad' => 'Caja 500u',
                    'categoria' => 'Tornillos'
                ],
                (object)[
                    'id_catalogo_herraje_global' => 9,
                    'nombre_herraje_global' => 'Tornillos Autorroscantes 3.5 x 16 mm (Caja 1000u)',
                    'tipo_visibilidad_herraje' => 'oculto',
                    'precio_referencial' => 22.00,
                    'unidad' => 'Caja 1000u',
                    'categoria' => 'Tornillos'
                ],
                (object)[
                    'id_catalogo_herraje_global' => 10,
                    'nombre_herraje_global' => 'Minifix Perno Conector + Caja Excéntrica (50 Juegos)',
                    'tipo_visibilidad_herraje' => 'oculto',
                    'precio_referencial' => 35.00,
                    'unidad' => 'Bolsa 50u',
                    'categoria' => 'Fijaciones'
                ],
                (object)[
                    'id_catalogo_herraje_global' => 11,
                    'nombre_herraje_global' => 'Canto Delgado PVC 0.45mm x 22mm Blanco (Metro)',
                    'tipo_visibilidad_herraje' => 'visto',
                    'precio_referencial' => 0.65,
                    'unidad' => 'Metro',
                    'categoria' => 'Tapacantos'
                ],
                (object)[
                    'id_catalogo_herraje_global' => 12,
                    'nombre_herraje_global' => 'Canto Grueso PVC 2.0mm x 22mm Antichoque (Metro)',
                    'tipo_visibilidad_herraje' => 'visto',
                    'precio_referencial' => 2.80,
                    'unidad' => 'Metro',
                    'categoria' => 'Tapacantos'
                ],
                (object)[
                    'id_catalogo_herraje_global' => 13,
                    'nombre_herraje_global' => 'Servicio de Optimización y Corte por Plancha',
                    'tipo_visibilidad_herraje' => 'oculto',
                    'precio_referencial' => 18.00,
                    'unidad' => 'Plancha',
                    'categoria' => 'Servicios'
                ],
                (object)[
                    'id_catalogo_herraje_global' => 14,
                    'nombre_herraje_global' => 'Servicio de Pegado Canto Delgado en Máquina (Metro)',
                    'tipo_visibilidad_herraje' => 'visto',
                    'precio_referencial' => 1.20,
                    'unidad' => 'Metro',
                    'categoria' => 'Servicios'
                ],
                (object)[
                    'id_catalogo_herraje_global' => 15,
                    'nombre_herraje_global' => 'Servicio de Pegado Canto Grueso 2mm Boleado (Metro)',
                    'tipo_visibilidad_herraje' => 'visto',
                    'precio_referencial' => 3.50,
                    'unidad' => 'Metro',
                    'categoria' => 'Servicios'
                ]
            ]);
        }

        return view('materiales.costos', compact('user', 'isMaestro', 'isEmpresa', 'materiales', 'herrajes'));
    }

    /**
     * Actualiza el precio de un material de melamina.
     */
    public function updateMaterial(Request $request, $id)
    {
        $request->validate([
            'precio_lamina_muebles' => 'required|numeric|min:0'
        ]);

        $material = MuebleMaterial::find($id);
        if ($material) {
            $material->precio_lamina_muebles = $request->input('precio_lamina_muebles');
            $material->save();
        }

        return redirect()->route('materiales.costos')->with('exito', 'Precio de plancha actualizado correctamente.');
    }

    /**
     * Actualiza el precio de un herraje.
     */
    public function updateHerraje(Request $request, $id)
    {
        $request->validate([
            'precio_referencial' => 'required|numeric|min:0'
        ]);

        $herraje = MuebleCatalogoHerrajeGlobal::find($id);
        if ($herraje) {
            $herraje->precio_referencial = $request->input('precio_referencial');
            $herraje->save();
        }

        return redirect()->route('materiales.costos')->with('exito', 'Precio referencial de herraje actualizado.');
    }
}
