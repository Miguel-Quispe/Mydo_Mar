@extends('layouts.app')

@section('content')
<style>
    /* ====== MOTOR 2D: ESTILOS ====== */
    #lienzo-2d {
        width: 100%;
        max-width: 500px;
        height: 600px;
        margin: 0 auto;
        border: 18px solid #8c5a35;
        background-color: #f8f9fa;
        position: relative;
        display: flex;
    }

    .zona {
        display: flex;
        flex: 1;
        transition: background-color 0.2s;
        box-sizing: border-box;
        min-width: 0;
        min-height: 0;
    }

    .zona-vacia {
        cursor: pointer;
    }

    .zona-vacia:hover {
        background-color: #e2e8f0;
        border: 2px dashed #0d6efd;
    }

    .zona-seleccionada {
        background-color: #cfe2ff !important;
        border: 2px solid #0d6efd !important;
    }

    .tablero-horizontal {
        flex-shrink: 0;
        height: 18px;
        background-color: #8c5a35;
        width: 100%;
    }

    .tablero-vertical {
        flex-shrink: 0;
        width: 18px;
        background-color: #8c5a35;
        height: 100%;
    }
</style>

<div class="container py-4">
    <!-- Encabezado -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-0">Diseñador de Muebles 2D</h2>
            <p class="text-muted mb-0">Selecciona una zona vacía y agrega divisiones a medida.</p>
        </div>
        <a href="{{ route('maestro.dashboard') }}" class="btn btn-outline-secondary fw-bold">
            Volver al Panel
        </a>
    </div>

    <!-- FORMULARIO QUE ENVUELVE TODO -->
    <form action="{{ route('muebles.store') }}" method="POST" enctype="multipart/form-data" id="form-mueble">
        @csrf

        <!-- Campos ocultos para Laravel -->
        <input type="hidden" name="nombre"          id="form-nombre">
        <input type="hidden" name="material"        id="form-material"    value="Melamina 18mm">
        <input type="hidden" name="precio"          id="form-precio"      value="0">
        <input type="hidden" name="dimensiones"     id="form-dimensiones">
        <input type="hidden" name="descripcion"     id="form-descripcion" value="Mueble diseñado en el configurador 2D.">
        <input type="hidden" name="estructura_json" id="form-estructura-json">

        <div class="row">
            <!-- ====== PANEL IZQUIERDO: CONTROLES ====== -->
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-dark text-white py-3">
                        <h6 class="fw-bold mb-0">Panel de Control</h6>
                    </div>
                    <div class="card-body">

                        <!-- Nombre del Mueble -->
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Nombre del Mueble</label>
                            <input type="text" id="input-nombre" class="form-control form-control-sm" placeholder="Ej. Ropero 3 puertas">
                        </div>

                        <!-- Dimensiones -->
                        <h6 class="fw-bold text-dark mb-2">Dimensiones (mm)</h6>
                        <div class="row g-2 mb-3">
                            <div class="col-4">
                                <label class="form-label small mb-0">Alto</label>
                                <input type="number" id="input-alto" class="form-control form-control-sm" value="1800">
                            </div>
                            <div class="col-4">
                                <label class="form-label small mb-0">Ancho</label>
                                <input type="number" id="input-ancho" class="form-control form-control-sm" value="900">
                            </div>
                            <div class="col-4">
                                <label class="form-label small mb-0">Prof.</label>
                                <input type="number" id="input-prof" class="form-control form-control-sm" value="450">
                            </div>
                        </div>

                        <!-- Perspectiva -->
                        <h6 class="fw-bold text-dark mb-2">Perspectiva</h6>
                        <div class="btn-group w-100 mb-2" role="group">
                            <button type="button" id="vista-frente" class="btn btn-outline-secondary btn-sm active">Frente</button>
                            <button type="button" id="vista-planta" class="btn btn-outline-secondary btn-sm">Planta</button>
                            <button type="button" id="vista-perfil" class="btn btn-outline-secondary btn-sm">Perfil</button>
                        </div>
                        <p id="lbl-perspectiva" class="text-muted small mb-3">Mostrando vista frontal del mueble.</p>

                        <hr>

                        <p class="text-muted small mb-2">Haz clic en una zona del mueble para activarla.</p>

                        <div class="mb-3">
                            <label class="form-label small fw-bold">Posición de División (mm desde arriba / izq.)</label>
                            <input type="number" id="input-posicion" class="form-control form-control-sm" value="50" placeholder="Ej. 400mm" disabled>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="button" id="btn-div-horiz" class="btn btn-primary btn-sm fw-bold" disabled>
                                + Agregar Estante (Horizontal)
                            </button>
                            <button type="button" id="btn-div-vert" class="btn btn-success btn-sm fw-bold" disabled>
                                + Agregar Parante (Vertical)
                            </button>
                            <button type="button" id="btn-limpiar" class="btn btn-outline-danger btn-sm mt-2">
                                Reiniciar Diseño
                            </button>
                        </div>

                    </div>
                </div>
            </div>

            <!-- ====== PANEL DERECHO: LIENZO 2D ====== -->
            <div class="col-md-8">
                <div class="card shadow-sm border-0">
                    <div class="card-body text-center bg-light p-4">

                        <div id="lienzo-2d">
                            <div class="zona zona-vacia" id="zona-raiz"></div>
                        </div>

                        <div class="mt-3 text-muted small">
                            Lienzo: <span id="info-dimensiones">Alto: 1800mm | Ancho: 900mm | Prof: 450mm</span>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- ====== TABLA DE DESPIECE ====== -->
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-header bg-dark text-white py-2 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">Lista de Despiece Calculada (Melamina 18mm)</h6>
                <button type="button" id="btn-calcular-despiece" class="btn btn-warning btn-sm fw-bold">
                    Calcular Despiece
                </button>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover table-striped mb-0 text-center align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Pieza</th>
                            <th>Cantidad</th>
                            <th>Largo (mm)</th>
                            <th>Ancho (mm)</th>
                            <th>Espesor</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-despiece">
                        <tr>
                            <td colspan="6" class="text-muted py-3 small">
                                Presiona "Calcular Despiece" para generar la lista de piezas.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ====== BOTÓN GUARDAR ====== -->
        <div class="d-flex justify-content-end gap-2 my-4">
            <a href="{{ route('maestro.dashboard') }}" class="btn btn-outline-secondary fw-bold">Cancelar</a>
            <button type="submit" id="btn-guardar" class="btn btn-success btn-lg fw-bold px-5">
                Guardar y Publicar Mueble
            </button>
        </div>

    </form>
</div>

<!-- ====== LÓGICA JAVASCRIPT ====== -->
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Referencias ──────────────────────────────────────────────────────────
    let zonaActiva = null;

    const btnDivHoriz    = document.getElementById('btn-div-horiz');
    const btnDivVert     = document.getElementById('btn-div-vert');
    const btnLimpiar     = document.getElementById('btn-limpiar');
    const btnCalcular    = document.getElementById('btn-calcular-despiece');
    const btnGuardar     = document.getElementById('btn-guardar');

    const inputPos       = document.getElementById('input-posicion');
    const inputAlto      = document.getElementById('input-alto');
    const inputAncho     = document.getElementById('input-ancho');
    const inputProf      = document.getElementById('input-prof');
    const inputNombre    = document.getElementById('input-nombre');
    const infoDim        = document.getElementById('info-dimensiones');
    const lienzo         = document.getElementById('lienzo-2d');

    // ── Actualizar texto de dimensiones ──────────────────────────────────────
    function actualizarDimensionesTexto() {
        infoDim.innerText = `Alto: ${inputAlto.value}mm | Ancho: ${inputAncho.value}mm | Prof: ${inputProf.value}mm`;
    }
    inputAlto.addEventListener('input', actualizarDimensionesTexto);
    inputAncho.addEventListener('input', actualizarDimensionesTexto);
    inputProf.addEventListener('input', actualizarDimensionesTexto);

    // ── Control de Perspectivas ──────────────────────────────────────────────
    const vistas = {
        'vista-frente': 'Mostrando vista frontal del mueble.',
        'vista-planta': 'Mostrando vista en planta (corte superior horizontal).',
        'vista-perfil': 'Mostrando vista de perfil (lateral con profundidad).',
    };
    Object.keys(vistas).forEach(id => {
        document.getElementById(id).addEventListener('click', function () {
            document.getElementById('lbl-perspectiva').innerText = vistas[id];
            document.querySelectorAll('#vista-frente, #vista-planta, #vista-perfil')
                .forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // ── Selección de Zona ────────────────────────────────────────────────────
    function seleccionarZona(e) {
        e.stopPropagation();
        const el = e.currentTarget;
        if (!el.classList.contains('zona-vacia')) return;

        if (zonaActiva) zonaActiva.classList.remove('zona-seleccionada');
        zonaActiva = el;
        zonaActiva.classList.add('zona-seleccionada');

        btnDivHoriz.disabled = false;
        btnDivVert.disabled  = false;
        inputPos.disabled    = false;
    }

    function inicializarZonas() {
        document.querySelectorAll('.zona-vacia').forEach(z => {
            z.removeEventListener('click', seleccionarZona);
            z.addEventListener('click', seleccionarZona);
        });
    }

    // ── División con flex-grow proporcional ───────────────────────────────────
    function obtenerFlexRatios(medidaIngresada, totalZona) {
        const pos = parseFloat(medidaIngresada) || (totalZona / 2);
        const superior = Math.max(1, pos);
        const inferior = Math.max(1, totalZona - pos);
        return { superior, inferior };
    }

    btnDivHoriz.addEventListener('click', function () {
        if (!zonaActiva) return;

        const altoTotal = parseFloat(inputAlto.value) || 1800;
        const medida    = parseFloat(inputPos.value)  || altoTotal / 2;
        const ratios    = obtenerFlexRatios(medida, altoTotal);

        zonaActiva.style.flexDirection = 'column';
        zonaActiva.classList.remove('zona-vacia', 'zona-seleccionada');
        zonaActiva.innerHTML = `
            <div class="zona zona-vacia" style="flex:${ratios.superior}"></div>
            <div class="tablero-horizontal" title="Estante — posición ${medida}mm"></div>
            <div class="zona zona-vacia" style="flex:${ratios.inferior}"></div>
        `;
        reiniciarSeleccion();
        inicializarZonas();
        actualizarDespiece();
    });

    btnDivVert.addEventListener('click', function () {
        if (!zonaActiva) return;

        const anchoTotal = parseFloat(inputAncho.value) || 900;
        const medida     = parseFloat(inputPos.value)   || anchoTotal / 2;
        const ratios     = obtenerFlexRatios(medida, anchoTotal);

        zonaActiva.style.flexDirection = 'row';
        zonaActiva.classList.remove('zona-vacia', 'zona-seleccionada');
        zonaActiva.innerHTML = `
            <div class="zona zona-vacia" style="flex:${ratios.superior}"></div>
            <div class="tablero-vertical" title="Parante — posición ${medida}mm"></div>
            <div class="zona zona-vacia" style="flex:${ratios.inferior}"></div>
        `;
        reiniciarSeleccion();
        inicializarZonas();
        actualizarDespiece();
    });

    btnLimpiar.addEventListener('click', function () {
        lienzo.innerHTML = '<div class="zona zona-vacia" id="zona-raiz"></div>';
        reiniciarSeleccion();
        inicializarZonas();
        actualizarDespiece();
    });

    function reiniciarSeleccion() {
        zonaActiva          = null;
        btnDivHoriz.disabled = true;
        btnDivVert.disabled  = true;
        inputPos.disabled    = true;
        inputPos.value       = '';
    }

    // ── Motor de Despiece ────────────────────────────────────────────────────
    function actualizarDespiece() {
        const alto  = parseFloat(inputAlto.value)  || 1800;
        const ancho = parseFloat(inputAncho.value) || 900;
        const prof  = parseFloat(inputProf.value)  || 450;

        const tableros_h = lienzo.querySelectorAll('.tablero-horizontal').length;
        const tableros_v = lienzo.querySelectorAll('.tablero-vertical').length;

        const piezas = [];

        // Carcasa: laterales, base, techo, fondo
        piezas.push({ nombre: 'Lateral',    cant: 2,            largo: alto,  ancho: prof });
        piezas.push({ nombre: 'Base / Techo', cant: 2,          largo: ancho - 36, ancho: prof });
        piezas.push({ nombre: 'Fondo (MDF 3mm)', cant: 1,       largo: alto - 36,  ancho: ancho - 36 });
        if (tableros_h > 0)
            piezas.push({ nombre: 'Estante (Horizontal)', cant: tableros_h, largo: ancho - 36, ancho: prof - 20 });
        if (tableros_v > 0)
            piezas.push({ nombre: 'Parante (Vertical)',   cant: tableros_v, largo: alto - 36,  ancho: prof - 20 });

        const tbody = document.getElementById('tabla-despiece');
        tbody.innerHTML = piezas.map((p, i) => `
            <tr>
                <td class="text-muted">${i + 1}</td>
                <td class="text-start">${p.nombre}</td>
                <td><span class="badge bg-secondary">${p.cant}</span></td>
                <td>${p.largo}</td>
                <td>${p.ancho}</td>
                <td>18mm</td>
            </tr>
        `).join('');
    }

    btnCalcular.addEventListener('click', actualizarDespiece);

    // ── Serializar estructura a JSON ─────────────────────────────────────────
    function serializarMueble(nodo) {
        if (!nodo) return null;
        const esZonaVacia = nodo.classList.contains('zona-vacia');
        const hijos = [];
        nodo.childNodes.forEach(child => {
            if (child.classList && child.classList.contains('zona')) {
                hijos.push(serializarMueble(child));
            }
        });
        return {
            tipo:        esZonaVacia ? 'vacia' : 'dividida',
            orientacion: nodo.style.flexDirection || 'row',
            flexRatio:   nodo.style.flex || '1',
            subzonas:    hijos,
        };
    }

    // ── Preparar y enviar formulario ─────────────────────────────────────────
    document.getElementById('form-mueble').addEventListener('submit', function (e) {
        const nombre = inputNombre.value.trim();
        if (!nombre) {
            e.preventDefault();
            alert('Por favor ingresa un nombre para el mueble antes de guardar.');
            inputNombre.focus();
            return;
        }

        const alto  = inputAlto.value;
        const ancho = inputAncho.value;
        const prof  = inputProf.value;

        document.getElementById('form-nombre').value      = nombre;
        document.getElementById('form-dimensiones').value = `${alto}x${ancho}x${prof}mm`;
        document.getElementById('form-estructura-json').value = JSON.stringify(
            serializarMueble(document.getElementById('zona-raiz'))
        );
    });

    // ── Inicialización ───────────────────────────────────────────────────────
    inicializarZonas();
    actualizarDespiece();
});
</script>
@endsection