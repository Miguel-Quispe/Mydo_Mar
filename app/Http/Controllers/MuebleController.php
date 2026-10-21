<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mueble;
use App\Models\MuebleMaterial;
use App\Services\MuebleBomService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class MuebleController extends Controller
{
    protected MuebleBomService $bomService;

    public function __construct(MuebleBomService $bomService)
    {
        $this->bomService = $bomService;
    }

    /**
     * Muestra el formulario para crear un mueble en el diseñador 3D.
     */
    public function create()
    {
        $materiales = MuebleMaterial::all();
        return view('maestro.muebles.create', compact('materiales'));
    }

    /**
     * Endpoint API para calcular en tiempo real el BOM (Bill of Materials) desde Three.js.
     */
    public function calcularBom(Request $request)
    {
        $piezas = $request->input('piezas', []);
        $idMaterial = (int) $request->input('material_id', 1);

        $bom = $this->bomService->calcularBom($piezas, $idMaterial);

        return response()->json($bom);
    }

    /**
     * Guarda el nuevo mueble tanto en el catálogo general como en la arquitectura normalizada B2B.
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

        // 1. Procesar captura o archivo de imagen
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

        // 2. Persistencia en la tabla legacy 'muebles'
        $muebleLegacy = Mueble::create([
            'user_id'         => auth()->id() ?? 1,
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

        // 3. Persistencia simultánea en el Modelo B2B Normalizado (muebles_disenos, piezas, herrajes BOM)
        try {
            $piezasArray = [];
            if ($request->filled('estructura_json')) {
                $estructuraDecoded = json_decode($request->estructura_json, true);
                if (is_array($estructuraDecoded)) {
                    $piezasRaw = $estructuraDecoded['pieces'] ?? $estructuraDecoded['piezas'] ?? $estructuraDecoded;
                    if (is_array($piezasRaw)) {
                        foreach ($piezasRaw as $idx => $p) {
                            $piezasArray[] = [
                                'nombre' => $p['name'] ?? $p['nombre'] ?? ('Pieza #' . ($idx + 1)),
                                'ancho_mm' => (float) ($p['width'] ?? $p['w'] ?? 500),
                                'alto_mm' => (float) ($p['height'] ?? $p['h'] ?? 500),
                                'espesor_mm' => (float) ($p['depth'] ?? $p['d'] ?? 18),
                                'posicion_x' => (float) ($p['x'] ?? 0),
                                'posicion_y' => (float) ($p['y'] ?? 0),
                                'posicion_z' => (float) ($p['z'] ?? 0),
                                'rotacion_x' => (float) ($p['rotX'] ?? 0),
                                'rotacion_y' => (float) ($p['rotY'] ?? 0),
                                'rotacion_z' => (float) ($p['rotZ'] ?? 0),
                            ];
                        }
                    }
                }
            }

            // Calcular BOM de herrajes e insumos
            $bom = $this->bomService->calcularBom($piezasArray);

            $this->bomService->guardarProyectoCompleto([
                'creador_id' => auth()->id() ?? 1,
                'empresa_id' => 1, // Madeart
                'categoria_id' => 1, // Oficina / General
                'nivel_id' => 2, // Intermedio
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'estructura_3d' => $request->estructura_json ?? '{}',
                'imagen_render' => $rutaImagen,
                'precio_total' => $request->precio ?? $bom['costo_total'],
                'publicado' => true,
                'material_id' => $bom['material_id'],
                'piezas' => $bom['piezas'],
                'herrajes' => $bom['herrajes'],
            ]);
        } catch (\Throwable $e) {
            Log::warning('No se pudo duplicar en tabla B2B normalizada: ' . $e->getMessage());
        }

        return redirect()->route('maestro.dashboard')->with('exito', 'Mueble publicado correctamente en el catálogo y registrado en el sistema B2B de planta.');
    }
}
