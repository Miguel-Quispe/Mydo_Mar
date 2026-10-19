@extends('layouts.app')

@section('content')
@php
    $estructura = null;
    if (!empty($mueble->estructura_json)) {
        $estructura = json_decode($mueble->estructura_json, true);
    }
    $piezas = $estructura['piezas'] ?? [];
    $objetos = $estructura['objetos'] ?? [];
@endphp

<div class="container py-4 py-md-5">

    <!-- Top Navigation Bar -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('catalogo.index') }}" class="btn btn-outline-secondary fw-bold px-3 py-2 shadow-sm" style="border-radius: 10px;">
            ← Volver al Catálogo
        </a>
        @if(!empty($piezas))
            <button type="button" class="btn btn-dark fw-bold px-3 py-2 shadow-sm d-flex align-items-center gap-2" onclick="window.imprimirPlanoMueble()">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                    <rect x="6" y="14" width="12" height="8"></rect>
                </svg>
                Descargar Planos / Ficha Técnica
            </button>
        @endif
    </div>

    <!-- Mensaje de éxito tras acciones -->
    @if(session('exito_favorito'))
        <div class="alert alert-info alert-dismissible fade show shadow-sm" role="alert">
            {{ session('exito_favorito') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Tarjeta Principal de Detalle -->
    <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white p-3 p-md-4 mb-5">
        <div class="row g-4 align-items-center">
            
            <!-- Columna Izquierda: Visor Multimedia (Foto o 3D) -->
            <div class="col-lg-6">
                <!-- Selector de Modo de Vista si hay datos 3D -->
                @if(!empty($objetos))
                    <div class="d-flex gap-2 mb-2">
                        <button type="button" class="btn btn-sm btn-secondary active fw-bold px-3 py-1 d-inline-flex align-items-center gap-1" id="btn-view-photo">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                            Fotografia / Render
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary fw-bold px-3 py-1 d-inline-flex align-items-center gap-1" id="btn-view-3d">
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                            Visor 3D Interactivo
                        </button>
                    </div>
                @endif

                <div class="position-relative overflow-hidden rounded-4 border bg-light d-flex align-items-center justify-content-center shadow-sm" style="min-height: 400px; height: 440px;">
                    
                    <!-- Contenedor Foto Principal -->
                    <div id="container-photo" class="w-100 h-100 d-flex align-items-center justify-content-center p-2">
                        @if($mueble->imagen_url)
                            <img src="{{ asset('storage/' . $mueble->imagen_url) }}" 
                                 alt="{{ $mueble->nombre }}" 
                                 id="mueble-main-img"
                                 class="img-fluid w-100 h-100 object-fit-contain d-block rounded-3">
                        @else
                            <div class="text-center p-5 text-secondary opacity-50">
                                <svg width="56" height="56" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                                    <path d="M3 9h18M9 3v18"/>
                                </svg>
                                <p class="mt-2 mb-0 small fw-bold text-uppercase">Sin imagen</p>
                            </div>
                        @endif
                    </div>

                    <!-- Contenedor Visor 3D Interactivo -->
                    @if(!empty($objetos))
                        <div id="container-3d-viewer" class="w-100 h-100 position-absolute top-0 start-0" style="display: none; background: #f8fafc;">
                            <div id="three-viewer-canvas" style="width: 100%; height: 100%;"></div>
                            <div class="position-absolute bottom-0 start-0 m-3 bg-dark text-white px-3 py-1 rounded-pill small opacity-75 d-flex align-items-center gap-2">
                                <span style="font-size: 11px;">Arrastra para girar · Rueda para zoom</span>
                            </div>
                            <button type="button" id="btn-toggle-auto-rot" class="btn btn-light btn-sm position-absolute top-0 end-0 m-3 shadow-sm fw-bold border d-inline-flex align-items-center gap-1">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>
                                Rotacion
                            </button>
                        </div>
                    @endif

                </div>
            </div>

            <!-- Columna Derecha: Información y Acciones -->
            <div class="col-lg-6 d-flex flex-column justify-content-between">
                <div>
                    <!-- Vistas y Título -->
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <h1 class="fw-bold text-dark text-capitalize mb-0 fs-2">
                            {{ $mueble->nombre }}
                        </h1>
                        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill small d-inline-flex align-items-center gap-1">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                            {{ $mueble->vistas ?? 0 }} vistas
                        </span>
                    </div>

                    <!-- Precio Destacado -->
                    <div class="my-3">
                        <span class="fs-1 fw-bold text-warning">Bs {{ number_format($mueble->precio ?? 0, 2) }}</span>
                        <span class="text-muted small ms-2 fw-semibold">Precio estimado de fabricación</span>
                    </div>

                    <!-- Maestro / Diseñador -->
                    <div class="bg-light p-3 rounded-3 mb-3 border-start border-4 border-warning">
                        <span class="text-uppercase text-muted fw-bold d-block" style="font-size: 0.7rem; letter-spacing: 0.5px;">Diseñado y Creado por:</span>
                        <span class="fw-bold text-dark fs-6">Maestro {{ $mueble->user->nombre ?? 'Carpintero' }} {{ $mueble->user->apellido_paterno ?? '' }}</span>
                    </div>

                    <!-- Ficha resumen rápida: Dimensiones y Material -->
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="p-2 border rounded-3 bg-light">
                                <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.68rem;">Dimensiones</div>
                                <div class="fw-bold text-dark small">{{ $mueble->dimensiones ?? '1200 × 600 × 750 mm' }}</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 border rounded-3 bg-light">
                                <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.68rem;">Material Principal</div>
                                <div class="fw-bold text-dark small">{{ $mueble->material ?? 'Melamina 18mm' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Descripción -->
                    <div class="mb-4">
                        <h6 class="text-uppercase text-muted fw-bold mb-2" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                            Descripción del Mueble
                        </h6>
                        <p class="text-secondary lh-base mb-0 small">
                            {{ $mueble->descripcion ?? 'Mueble fabricado con materiales de alta durabilidad y diseño funcional.' }}
                        </p>
                    </div>
                </div>

                <!-- Botones de Acción (Contacto y Favoritos) -->
                <div class="pt-3 border-top d-flex flex-column gap-2">
                    <!-- Botón Guía de Armado (siempre visible, destacado) -->
                    <a href="{{ route('catalogo.armado', $mueble->id) }}"
                       class="btn btn-dark btn-lg fw-bold w-100 py-3 shadow-sm d-flex align-items-center justify-content-center gap-2"
                       style="border-radius: 10px; background: linear-gradient(135deg, #1e293b 0%, #334155 100%); border: none;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/>
                        </svg>
                        Ver Guía de Armado Paso a Paso
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>

                    <div class="d-flex flex-sm-row gap-2">
                        <!-- Botón de WhatsApp -->
                        @if($linkWhatsapp)
                            <a href="{{ $linkWhatsapp }}" 
                               target="_blank" 
                               class="btn btn-success btn-lg fw-bold flex-grow-1 shadow-sm py-2 d-flex align-items-center justify-content-center gap-2" 
                               style="border-radius: 10px;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                                Contactar por WhatsApp
                            </a>
                        @else
                            <button class="btn btn-secondary btn-lg fw-bold flex-grow-1 py-2" style="border-radius: 10px;" disabled>
                                Contacto no disponible
                            </button>
                        @endif

                        <!-- Botón Favoritos -->
                        @auth
                            <form action="{{ route('favoritos.toggle', $mueble->id) }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="btn btn-outline-warning btn-lg fw-bold text-dark w-100 py-2 h-100 d-flex align-items-center justify-content-center gap-2" style="border-radius: 10px;">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                                    Favorito
                                </button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline-warning btn-lg fw-bold text-dark w-100 py-2 h-100 d-flex align-items-center justify-content-center gap-2" style="border-radius: 10px;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                                Favorito
                            </a>
                        @endauth
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- SECCIÓN: Ficha Técnica y Despiece para Estudiantes y Taller -->
    @if(!empty($piezas))
    <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-5" id="seccion-despiece">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 border-bottom pb-3">
            <div>
                <h4 class="fw-bold text-dark mb-1 d-flex align-items-center gap-2">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 22h20"/><path d="M4 2v20"/><path d="M8 6v16"/><path d="M12 10v12"/><path d="M16 14v8"/><path d="M20 18v4"/></svg>
                    Ficha Tecnica y Lista de Despiece
                </h4>
                <p class="text-muted small mb-0">Detalle de piezas para armado, optimización de corte y estudio técnico de estudiantes y carpinteros.</p>
            </div>
            <button type="button" class="btn btn-outline-dark fw-bold px-3 py-2 shadow-sm d-flex align-items-center gap-2" onclick="window.imprimirPlanoMueble()">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                    <rect x="6" y="14" width="12" height="8"></rect>
                </svg>
                Imprimir Planos / Guardar PDF
            </button>
        </div>

        <!-- Métricas Rápidas -->
        <div class="row g-3 mb-4 text-center">
            <div class="col-6 col-md-3">
                <div class="p-3 bg-light rounded-3 border">
                    <div class="text-muted small fw-bold">Total de Piezas</div>
                    <div class="fs-4 fw-bold text-primary">{{ count($piezas) }}</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3 bg-light rounded-3 border">
                    <div class="text-muted small fw-bold">Material Sugerido</div>
                    <div class="fs-6 fw-bold text-dark mt-1">{{ $mueble->material ?? 'Melamina 18mm' }}</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3 bg-light rounded-3 border">
                    <div class="text-muted small fw-bold">Dimensiones Bounding</div>
                    <div class="fs-6 fw-bold text-dark mt-1">{{ $mueble->dimensiones ?? 'N/D' }}</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="p-3 bg-light rounded-3 border">
                    <div class="text-muted small fw-bold">Sierra de Corte</div>
                    <div class="fs-6 fw-bold text-dark mt-1">3.0 mm (Estándar)</div>
                </div>
            </div>
        </div>

        <!-- Tabla de Despiece -->
        <div class="table-responsive">
            <table class="table table-hover align-middle border">
                <thead class="table-light">
                    <tr class="small text-uppercase text-muted">
                        <th style="width: 50px;">#</th>
                        <th>Nombre de Pieza</th>
                        <th class="text-center">Largo / Alto (mm)</th>
                        <th class="text-center">Ancho (mm)</th>
                        <th class="text-center">Grosor (mm)</th>
                        <th class="text-center">Cant.</th>
                        <th class="text-end">Material</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($piezas as $idx => $p)
                        <tr>
                            <td class="fw-bold text-muted">{{ $idx + 1 }}</td>
                            <td class="fw-bold text-dark">{{ $p['name'] ?? 'Pieza ' . ($idx + 1) }}</td>
                            <td class="text-center fw-semibold">{{ round($p['pw'] ?? 0) }} mm</td>
                            <td class="text-center fw-semibold">{{ round($p['ph'] ?? 0) }} mm</td>
                            <td class="text-center text-muted">{{ $p['thickness'] ?? 18 }} mm</td>
                            <td class="text-center"><span class="badge bg-secondary rounded-pill px-2">1</span></td>
                            <td class="text-end text-muted small">{{ $mueble->material ?? 'Melamina' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>

<!-- Three.js si existen objetos 3D -->
@if(!empty($objetos))
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
@endif

<script>
    const objetosData = @json($objetos);
    const piezasData = @json($piezas);
    const muebleNombre = @json($mueble->nombre);
    const muebleDimensiones = @json($mueble->dimensiones ?? '1200 × 600 × 750 mm');
    const muebleMaterial = @json($mueble->material ?? 'Melamina 18mm');
    const maestroNombre = @json('Maestro ' . ($mueble->user->nombre ?? 'Carpintero') . ' ' . ($mueble->user->apellido_paterno ?? ''));
    const mueblePrecio = @json(number_format($mueble->precio ?? 0, 2));
    const muebleImgUrl = @json($mueble->imagen_url ? asset('storage/' . $mueble->imagen_url) : '');

    // Toggle Foto / 3D
    const btnViewPhoto = document.getElementById('btn-view-photo');
    const btnView3D = document.getElementById('btn-view-3d');
    const containerPhoto = document.getElementById('container-photo');
    const container3D = document.getElementById('container-3d-viewer');

    let threeInitialized = false;
    let scene, camera, renderer, controls, animId;
    let autoRotate = true;

    function initThreeViewer() {
        if (threeInitialized || !objetosData || objetosData.length === 0) return;
        threeInitialized = true;

        const canvasContainer = document.getElementById('three-viewer-canvas');
        if (!canvasContainer) return;

        const width = canvasContainer.clientWidth || 500;
        const height = canvasContainer.clientHeight || 440;

        scene = new THREE.Scene();
        scene.background = new THREE.Color(0xf8fafc);

        camera = new THREE.PerspectiveCamera(45, width / height, 1, 10000);
        camera.position.set(1000, 1000, 1200);

        renderer = new THREE.WebGLRenderer({ antialias: true, alpha: true });
        renderer.setSize(width, height);
        renderer.setPixelRatio(window.devicePixelRatio);
        renderer.shadowMap.enabled = true;
        canvasContainer.appendChild(renderer.domElement);

        controls = new THREE.OrbitControls(camera, renderer.domElement);
        controls.enableDamping = true;
        controls.dampingFactor = 0.05;
        controls.autoRotate = autoRotate;
        controls.autoRotateSpeed = 2.0;

        // Iluminación
        const ambient = new THREE.AmbientLight(0xffffff, 0.7);
        scene.add(ambient);

        const dirLight = new THREE.DirectionalLight(0xffffff, 0.6);
        dirLight.position.set(500, 1200, 700);
        scene.add(dirLight);

        const dirLight2 = new THREE.DirectionalLight(0xffffff, 0.3);
        dirLight2.position.set(-500, 800, -700);
        scene.add(dirLight2);

        // Suelo sutil
        const grid = new THREE.GridHelper(2000, 20, 0xd1d5db, 0xe5e7eb);
        grid.position.y = 0;
        scene.add(grid);

        // Catalogo de texturas para el visor de catalogo
        const textureCatalog = {
            'roble': { url: '{{ asset("textures/roble_natural.jpg") }}', scale: 400 },
            'nogal': { url: '{{ asset("textures/nogal_oscuro.jpg") }}', scale: 400 },
            'blanca': { url: '{{ asset("textures/melamina_blanca.jpg") }}', scale: 500 },
            'grafito': { url: '{{ asset("textures/gris_grafito.jpg") }}', scale: 500 },
            'marmol': { url: '{{ asset("textures/marmol_calacatta.jpg") }}', scale: 600 },
            'haya': { url: '{{ asset("textures/haya_madera.jpg") }}', scale: 400 }
        };

        const textureLoader = new THREE.TextureLoader();
        const cachedTex = {};
        function getTexture(k) {
            if (!textureCatalog[k]) k = 'roble';
            if (!cachedTex[k]) {
                const tex = textureLoader.load(textureCatalog[k].url, () => {
                    if (renderer) renderer.render(scene, camera);
                });
                tex.wrapS = THREE.RepeatWrapping;
                tex.wrapT = THREE.RepeatWrapping;
                cachedTex[k] = tex;
            }
            return cachedTex[k];
        }

        function getMaterial(key, w, h, d) {
            if (!textureCatalog[key]) key = 'roble';
            const baseTex = getTexture(key);
            const tex = baseTex.clone();
            tex.wrapS = THREE.RepeatWrapping;
            tex.wrapT = THREE.RepeatWrapping;
            tex.needsUpdate = true;
            const scale = textureCatalog[key].scale || 400;
            const maxDim1 = Math.max(w, h);
            const maxDim2 = Math.max(Math.min(w, h), d);
            tex.repeat.set(Math.max(1, +(maxDim1 / scale).toFixed(2)), Math.max(1, +(maxDim2 / scale).toFixed(2)));

            return new THREE.MeshStandardMaterial({
                map: tex,
                color: 0xffffff,
                roughness: 0.55,
                metalness: 0.05
            });
        }

        const edgeMat = new THREE.LineBasicMaterial({ color: 0x333333, linewidth: 1 });

        const group = new THREE.Group();
        objetosData.forEach(o => {
            const w = o.w || 100;
            const h = o.h || 100;
            const d = o.d || 100;
            const geom = new THREE.BoxGeometry(w, h, d);
            const mat = getMaterial(o.textureKey || 'roble', w, h, d);
            const mesh = new THREE.Mesh(geom, mat);
            if (o.pos) mesh.position.set(o.pos[0], o.pos[1], o.pos[2]);
            if (o.rot) mesh.rotation.set(o.rot[0], o.rot[1], o.rot[2]);

            // Bordes oscuros para aspecto técnico profesional
            const edges = new THREE.EdgesGeometry(geom);
            const line = new THREE.LineSegments(edges, edgeMat);
            mesh.add(line);

            group.add(mesh);
        });

        scene.add(group);

        // Centrar cámara en el modelo
        const box = new THREE.Box3().setFromObject(group);
        const center = new THREE.Vector3();
        box.getCenter(center);
        controls.target.copy(center);

        const size = new THREE.Vector3();
        box.getSize(size);
        const maxDim = Math.max(size.x, size.y, size.z);
        camera.position.set(center.x + maxDim * 1.2, center.y + maxDim * 0.9, center.z + maxDim * 1.4);
        controls.update();

        function animate() {
            animId = requestAnimationFrame(animate);
            controls.update();
            renderer.render(scene, camera);
        }
        animate();

        // Responsive resize
        window.addEventListener('resize', () => {
            if (!container3D || container3D.style.display === 'none') return;
            const nw = canvasContainer.clientWidth;
            const nh = canvasContainer.clientHeight;
            if (nw && nh) {
                camera.aspect = nw / nh;
                camera.updateProjectionMatrix();
                renderer.setSize(nw, nh);
            }
        });
    }

    if (btnViewPhoto && btnView3D && containerPhoto && container3D) {
        btnViewPhoto.addEventListener('click', () => {
            btnViewPhoto.classList.add('active', 'btn-secondary');
            btnViewPhoto.classList.remove('btn-outline-secondary');
            btnView3D.classList.remove('active', 'btn-primary');
            btnView3D.classList.add('btn-outline-primary');

            containerPhoto.style.display = 'flex';
            container3D.style.display = 'none';
        });

        btnView3D.addEventListener('click', () => {
            btnView3D.classList.add('active', 'btn-primary');
            btnView3D.classList.remove('btn-outline-primary');
            btnViewPhoto.classList.remove('active', 'btn-secondary');
            btnViewPhoto.classList.add('btn-outline-secondary');

            containerPhoto.style.display = 'none';
            container3D.style.display = 'block';

            if (!threeInitialized) {
                initThreeViewer();
            } else if (renderer && camera) {
                const canvasContainer = document.getElementById('three-viewer-canvas');
                renderer.setSize(canvasContainer.clientWidth, canvasContainer.clientHeight);
                camera.aspect = canvasContainer.clientWidth / canvasContainer.clientHeight;
                camera.updateProjectionMatrix();
            }
        });

        document.getElementById('btn-toggle-auto-rot')?.addEventListener('click', () => {
            if (controls) {
                controls.autoRotate = !controls.autoRotate;
            }
        });
    }

    // Impresión de Plano / Ficha Técnica
    window.imprimirPlanoMueble = function() {
        const win = window.open('', '_blank');
        if (!win) {
            alert("Por favor habilita las ventanas emergentes en tu navegador para imprimir la ficha técnica.");
            return;
        }

        win.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset="utf-8">
                <title>Ficha Técnica - ${muebleNombre}</title>
                <style>
                    body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; padding: 30px; color: #1f2937; max-width: 850px; margin: auto; }
                    .header { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 3px solid #d97706; padding-bottom: 12px; margin-bottom: 20px; }
                    h1 { color: #111827; margin: 0 0 6px 0; font-size: 24px; }
                    .brand { color: #d97706; font-weight: 800; font-size: 20px; }
                    .meta-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; background: #f9fafb; padding: 15px; border-radius: 8px; border: 1px solid #e5e7eb; margin-bottom: 25px; font-size: 13px; }
                    .meta-item strong { color: #374151; }
                    .img-wrap { text-align: center; margin-bottom: 25px; }
                    .img-wrap img { max-height: 280px; max-width: 100%; border-radius: 8px; border: 1px solid #e5e7eb; object-fit: contain; }
                    table { width: 100%; border-collapse: collapse; margin-bottom: 25px; font-size: 13px; }
                    th, td { border: 1px solid #e5e7eb; padding: 8px 12px; text-align: left; }
                    th { background: #f3f4f6; color: #374151; font-weight: 700; }
                    .footer-note { background: #fffbeb; border: 1px solid #fef3c7; color: #92400e; padding: 12px; border-radius: 6px; font-size: 12px; margin-top: 30px; }
                    @media print { .no-print { display: none; } body { padding: 0; } }
                </style>
            </head>
            <body>
                <div class="no-print" style="margin-bottom: 20px; text-align: right;">
                    <button onclick="window.print()" style="background:#d97706; color:#fff; border:none; padding:10px 24px; border-radius:8px; font-weight:bold; cursor:pointer; font-size:14px;">Imprimir / Guardar en PDF</button>
                </div>

                <div class="header">
                    <div>
                        <div class="brand">MYDO-MAR</div>
                        <h1>${muebleNombre}</h1>
                        <div style="font-size: 13px; color: #6b7280;">Ficha Técnica y Planos de Taller</div>
                    </div>
                    <div style="text-align: right; font-size: 13px; line-height: 1.6;">
                        <strong>Precio Est.:</strong> Bs ${mueblePrecio}<br>
                        <strong>Fecha:</strong> ${new Date().toLocaleDateString()}
                    </div>
                </div>

                <div class="meta-grid">
                    <div class="meta-item">
                        <strong>Diseñador:</strong> ${maestroNombre}<br>
                        <strong>Dimensiones:</strong> ${muebleDimensiones}<br>
                        <strong>Material Sugerido:</strong> ${muebleMaterial}
                    </div>
                    <div class="meta-item" style="text-align: right;">
                        <strong>Total de Piezas:</strong> ${piezasData ? piezasData.length : 0} uds.<br>
                        <strong>Espesor de Sierra:</strong> 3.0 mm<br>
                        <strong>Canto Sugerido:</strong> PVC 0.45 mm / 2 mm
                    </div>
                </div>

                ${muebleImgUrl ? `
                    <div class="img-wrap">
                        <img src="${muebleImgUrl}" alt="${muebleNombre}">
                    </div>
                ` : ''}

                <h3 style="font-size: 16px; margin-bottom: 10px; color: #111827;">Lista de Despiece para Corte</h3>
                <table>
                    <thead>
                        <tr>
                            <th style="width: 40px;">#</th>
                            <th>Nombre de Pieza</th>
                            <th>Largo (mm)</th>
                            <th>Ancho (mm)</th>
                            <th>Espesor</th>
                            <th>Cant.</th>
                            <th>Material</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${piezasData && piezasData.length > 0 ? piezasData.map((p, i) => `
                            <tr>
                                <td>${i + 1}</td>
                                <td><strong>${p.name || 'Pieza'}</strong></td>
                                <td>${Math.round(p.pw || 0)} mm</td>
                                <td>${Math.round(p.ph || 0)} mm</td>
                                <td>${p.thickness || 18} mm</td>
                                <td>1</td>
                                <td>${muebleMaterial}</td>
                            </tr>
                        `).join('') : '<tr><td colspan="7" style="text-align:center;">No hay piezas registradas</td></tr>'}
                    </tbody>
                </table>

                <div class="footer-note">
                    <strong>Recomendaciones de Taller:</strong><br>
                    1. Verificar medidas in situ antes de iniciar el seccionado del tablero.<br>
                    2. Respetar la orientación de la veta en tableros de melamina amaderada.<br>
                    3. Descontar espesor de tapacantos si se utiliza canto grueso (2 mm).
                </div>
            </body>
            </html>
        `);
        win.document.close();
    };
</script>
@endsection