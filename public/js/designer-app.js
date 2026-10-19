// --- Tab Logic ---
document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        // Remove active class from sibling tabs
        const parent = btn.parentElement;
        parent.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        // Add active class to clicked tab
        btn.classList.add('active');
        
        // Hide all tab contents in the same panel
        const panel = parent.parentElement;
        panel.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
        
        // Show target tab content
        const targetId = btn.getAttribute('data-target');
        document.getElementById(targetId).classList.add('active');
    });
});

// --- Three.js Setup ---
const container = document.getElementById('three-canvas');

const scene = new THREE.Scene();
scene.background = new THREE.Color('#cfd0d4'); // Flatma grey background

const camera = new THREE.PerspectiveCamera(45, container.clientWidth / container.clientHeight, 0.1, 10000);
// Position camera to look down at an angle
camera.position.set(400, 300, 500);

const renderer = new THREE.WebGLRenderer({ antialias: true, preserveDrawingBuffer: true });
renderer.setSize(container.clientWidth, container.clientHeight);
// Flatma uses shadows, enable them
renderer.shadowMap.enabled = true;
renderer.shadowMap.type = THREE.PCFSoftShadowMap;
container.appendChild(renderer.domElement);

const controls = new THREE.OrbitControls(camera, renderer.domElement);
controls.enableDamping = true;
controls.dampingFactor = 0.05;
controls.target.set(0, 100, 0); // Look at center height

// Helpers
const gridHelper = new THREE.GridHelper(1000, 100, 0x888888, 0xbbbbbb);
scene.add(gridHelper);

const axesHelper = new THREE.AxesHelper(200);
scene.add(axesHelper);

// Lighting
const ambientLight = new THREE.AmbientLight(0xffffff, 0.6);
scene.add(ambientLight);

const dirLight = new THREE.DirectionalLight(0xffffff, 0.5);
dirLight.position.set(200, 400, 200);
dirLight.castShadow = true;
dirLight.shadow.mapSize.width = 1024;
dirLight.shadow.mapSize.height = 1024;
scene.add(dirLight);

// Handle resize
window.addEventListener('resize', () => {
    camera.aspect = container.clientWidth / container.clientHeight;
    camera.updateProjectionMatrix();
    renderer.setSize(container.clientWidth, container.clientHeight);
});

// --- Furniture Logic ---
const objects = [];
let selectedObject = null;

// --- Catalogo de Texturas de Melamina y Maderas ---
const textureCatalog = {
    'roble': {
        name: 'Roble Natural',
        url: '/textures/maderado.jpg',
        fallbackColor: 0xd2a679,
        scale: 400
    },
    'nogal': {
        name: 'Nogal Oscuro',
        url: '/textures/nogal.jpg',
        fallbackColor: 0x5c4033,
        scale: 400
    },
    'blanca': {
        name: 'Melamina Blanca',
        url: '/textures/maple.jpg',
        fallbackColor: 0xf5f5f5,
        scale: 500
    },
    'grafito': {
        name: 'Gris Grafito',
        url: '/textures/plateado-texturizado.jpg',
        fallbackColor: 0x3a3d42,
        scale: 500
    },
    'marmol': {
        name: 'Marmol Calacatta',
        url: '/textures/plateado-texturizado.jpg',
        fallbackColor: 0xeaecef,
        scale: 600
    },
    'haya': {
        name: 'Haya Madera',
        url: '/textures/maderado.jpg',
        fallbackColor: 0xd8a47f,
        scale: 400
    }
};

let currentTextureKey = 'roble';
let activeTextureKey = 'roble';

// Crea un material con textura propia por pieza.
// Carga una instancia fresca del TextureLoader para cada pieza — el navegador
// usa su cache HTTP despues de la primera descarga, por lo que no hay
// penalizacion de red. Esto evita el bug de "clone negro" cuando la textura
// base todavia no termino de cargar.
function createPieceMaterial(key, w = 500, h = 500, d = 18) {
    if (!textureCatalog[key]) key = 'roble';
    const info = textureCatalog[key];
    const scale = info.scale || 400;
    const maxDim1 = Math.max(w, h);
    const maxDim2 = Math.max(Math.min(w, h), d);
    const rX = Math.max(1, +(maxDim1 / scale).toFixed(2));
    const rY = Math.max(1, +(maxDim2 / scale).toFixed(2));

    const mat = new THREE.MeshStandardMaterial({
        color: 0xffffff,
        roughness: 0.55,
        metalness: 0.05
    });

    // Carga la textura y la asigna cuando este lista
    new THREE.TextureLoader().load(
        textureCatalog[key].url,
        (tex) => {
            // onLoad — la textura ya tiene datos de imagen
            tex.wrapS = THREE.RepeatWrapping;
            tex.wrapT = THREE.RepeatWrapping;
            tex.repeat.set(rX, rY);
            tex.needsUpdate = true;
            mat.map = tex;
            mat.needsUpdate = true;
        },
        undefined,
        () => {
            // onError — usa el color de fallback del catalogo
            mat.color.setHex(info.fallbackColor || 0xd2a679);
            mat.needsUpdate = true;
        }
    );

    return mat;
}


function applyMaterialToMesh(mesh, textureKey) {
    if (!mesh || !mesh.userData) return;
    const w = mesh.userData.w || 500;
    const h = mesh.userData.h || 500;
    const d = mesh.userData.d || 18;
    const mat = createPieceMaterial(textureKey, w, h, d);
    mesh.material = mat;
    mesh.userData.originalMaterial = mat;
    mesh.userData.textureKey = textureKey;
    mesh.userData.materialName = textureCatalog[textureKey]?.name || 'Roble Natural';
}

function applyMaterialToSelection(textureKey) {
    if (!selectedObject) {
        showToast('Selecciona una pieza primero', '#e74c3c');
        return;
    }
    if (selectedObject.userData && (selectedObject.userData.isAssemblyGroup || selectedObject.userData.isParametricGroup)) {
        selectedObject.children.forEach(child => {
            if (child.isMesh) applyMaterialToMesh(child, textureKey);
        });
        showToast('Material ' + textureCatalog[textureKey].name + ' aplicado al grupo', '#27ae60');
    } else if (selectedObject.isMesh) {
        applyMaterialToMesh(selectedObject, textureKey);
        showToast('Material ' + textureCatalog[textureKey].name + ' aplicado a la pieza', '#27ae60');
    }
}

function applyMaterialToAll(textureKey) {
    objects.forEach(obj => {
        if (obj.userData && (obj.userData.isAssemblyGroup || obj.userData.isParametricGroup)) {
            obj.children.forEach(child => {
                if (child.isMesh) applyMaterialToMesh(child, textureKey);
            });
        } else if (obj.isMesh) {
            applyMaterialToMesh(obj, textureKey);
        }
    });
    currentTextureKey = textureKey;
    showToast('Material ' + textureCatalog[textureKey].name + ' aplicado a todo el mueble', '#27ae60');
}

function createPanel(w, h, d, x, y, z, name, addToScene = true) {
    const geometry = new THREE.BoxGeometry(w, h, d);
    geometry.computeBoundingBox();
    geometry.userData.obb = new THREE.OBB().fromBox3(geometry.boundingBox);
    
    const mat = createPieceMaterial(currentTextureKey || 'roble', w, h, d);
    const mesh = new THREE.Mesh(geometry, mat);
    mesh.position.set(x, y, z);
    mesh.castShadow = true;
    mesh.receiveShadow = true;
    
    mesh.userData = {
        name: name,
        originalMaterial: mat,
        textureKey: currentTextureKey || 'roble',
        materialName: textureCatalog[currentTextureKey || 'roble']?.name || 'Roble Natural',
        w: w, h: h, d: d,
        obb: new THREE.OBB()
    };
    
    if (addToScene) {
        if (typeof isSceneGrouped !== 'undefined' && isSceneGrouped && assemblyGroup) {
            const relPos = new THREE.Vector3(x, y, z).sub(assemblyGroup.position);
            mesh.position.copy(relPos);
            assemblyGroup.add(mesh);
            selectObject(assemblyGroup);
        } else {
            scene.add(mesh);
            objects.push(mesh);
            selectObject(mesh);
        }
    }
    return mesh;
}

// ─── render3DFromParts — integración con engine.js ───────
/**
 * Convierte el array Part[] del PolloEngine en un THREE.Group.
 * Llamado desde wizard.js → runEngine()
 */
function render3DFromParts(parts, spec) {
    // Limpiar grupos paramétricos anteriores generados por wizard
    const toRemove = objects.filter(o => o.userData.isWizardGroup);
    toRemove.forEach(o => { scene.remove(o); objects.splice(objects.indexOf(o), 1); });

    const group = new THREE.Group();
    group.userData = {
        isParametricGroup: true,
        isWizardGroup: true,
        spec: spec,
        w: spec.W, h: spec.H, d: spec.D, esp: spec.T,
    };

    // Mapa de color por rol de pieza
    const roleColors = {
        'lateral':       0xd2a679,
        'top':           0xc49a6c,
        'bottom':        0xc49a6c,
        'back':          0x8a6040,
        'shelf':         0xdbb88a,
        'drawer-front':  0xe8c89a,
        'drawer-side':   0xba8a5a,
        'drawer-bottom': 0x9a6a3a,
        'door':          0xe0d0b0,
        'leg':           0xb07840,
        'stretcher':     0xa87030,
    };

    parts.forEach(part => {
        const partColor = roleColors[part.role] || 0xd2a679;
        const mat = new THREE.MeshLambertMaterial({ color: partColor });

        // Piezas con qty=2 (p.ej. laterales de cajón) se dibujan simétricamente
        const drawCount = part.qty >= 2 && part.role === 'drawer-side' ? 2 : 1;

        for (let q = 0; q < drawCount; q++) {
            const geo = new THREE.BoxGeometry(part.pw, part.ph, part.pd);
            geo.computeBoundingBox();

            const mesh = new THREE.Mesh(geo, mat.clone());

            if (drawCount === 2) {
                // Colocar simétricamente a cada lado del cajón
                const sideOffset = (part.pw / 2) + spec.T / 2;
                mesh.position.set(
                    part.x + (q === 0 ? -sideOffset : sideOffset),
                    part.y,
                    part.z
                );
            } else {
                mesh.position.set(part.x, part.y, part.z);
            }

            mesh.castShadow = true;
            mesh.receiveShadow = true;
            mesh.userData = {
                name: part.name,
                w: part.pw, h: part.ph, d: part.pd,
                role: part.role,
                step: part.step,
                partData: part,
                originalMaterial: mat,
                obb: new THREE.OBB(),
            };
            geo.userData.obb = new THREE.OBB().fromBox3(geo.boundingBox);
            group.add(mesh);
        }
    });

    // Centrar el grupo en la escena
    group.position.set(0, 0, 0);

    scene.add(group);
    objects.push(group);
    selectObject(group);

    // Reposicionar cámara
    const { W = 800, H = 800, D = 600 } = spec;
    camera.position.set(W * 0.9, H * 1.3, W * 1.2);
    controls.target.set(0, H / 2, 0);
    controls.update();

    console.log(`%c${parts.length} piezas renderizadas`, 'color:#4CAF50;font-weight:bold');
}

// ─── Interaction State ────────────────────────────────────
const raycaster = new THREE.Raycaster();
const mouse = new THREE.Vector2();
let isDragging = false;
let isDraggingPiece = false;
let mouseDownPos = { x: 0, y: 0 };
let currentMode = 'translate'; // 'translate' | 'rotate'

// Drag plane: horizontal at Y = piece position
const dragPlane = new THREE.Plane();
const dragOffset = new THREE.Vector3();
const dragPoint  = new THREE.Vector3();

// ─── Pointer Down: start drag ─────────────────────────────
container.addEventListener('pointerdown', (event) => {
    if (event.button !== 0) return;
    mouseDownPos = { x: event.clientX, y: event.clientY };
    isDraggingPiece = false;

    if (currentMode !== 'translate') return; // Only direct-drag in move mode
    
    const rect = renderer.domElement.getBoundingClientRect();
    mouse.x =  ((event.clientX - rect.left) / rect.width)  * 2 - 1;
    mouse.y = -((event.clientY - rect.top)  / rect.height) * 2 + 1;
    raycaster.setFromCamera(mouse, camera);

    const hits = raycaster.intersectObjects(objects, true);
    if (hits.length > 0) {
        const hit = hits[0];
        let obj = hit.object;
        // Walk up to find parametric group
        let p = obj.parent;
        while (p && p !== scene) {
            if (p.userData.isParametricGroup || p.userData.isAssemblyGroup) { obj = p; break; }
            p = p.parent;
        }

        // Select on mousedown
        selectObject(obj);

        // Set up drag plane at piece's Y level (floor drag)
        dragPlane.setFromNormalAndCoplanarPoint(
            camera.getWorldDirection(new THREE.Vector3()).negate(),
            obj.position
        );

        // Compute offset from hit point to object center
        if (raycaster.ray.intersectPlane(dragPlane, dragPoint)) {
            dragOffset.subVectors(obj.position, dragPoint);
        }

        isDraggingPiece = true;
        controls.enabled = false;
        container.style.cursor = 'grabbing';
    }
});

// ─── Pointer Move: drag piece ─────────────────────────────
container.addEventListener('pointermove', (event) => {
    if (!isDraggingPiece || !selectedObject || currentMode !== 'translate') return;

    const rect = renderer.domElement.getBoundingClientRect();
    mouse.x =  ((event.clientX - rect.left) / rect.width)  * 2 - 1;
    mouse.y = -((event.clientY - rect.top)  / rect.height) * 2 + 1;
    raycaster.setFromCamera(mouse, camera);

    if (raycaster.ray.intersectPlane(dragPlane, dragPoint)) {
        selectedObject.position.addVectors(dragPoint, dragOffset);
        enforceFloorLimit(selectedObject);
        applySnapping(selectedObject);   // Snap to nearby pieces during drag
        checkCollisions(selectedObject);
        if (selectedObject.userData.boxHelper) selectedObject.userData.boxHelper.update();
    }
});

// ─── Pointer Up: end drag or select ──────────────────────
container.addEventListener('pointerup', (event) => {
    controls.enabled = true;
    container.style.cursor = 'default';

    const dx = Math.abs(event.clientX - mouseDownPos.x);
    const dy = Math.abs(event.clientY - mouseDownPos.y);
    const wasDrag = dx > 5 || dy > 5;

    if (isDraggingPiece) {
        isDraggingPiece = false;
        // If it was barely moved (a click), keep selection but don't deselect
        return;
    }

    if (isDragging) { isDragging = false; return; } // TransformControls was active

    // True click on empty space → deselect
    if (!wasDrag) {
        const rect = renderer.domElement.getBoundingClientRect();
        mouse.x =  ((event.clientX - rect.left) / rect.width)  * 2 - 1;
        mouse.y = -((event.clientY - rect.top)  / rect.height) * 2 + 1;
        raycaster.setFromCamera(mouse, camera);
        const hits = raycaster.intersectObjects(objects, true);
        if (hits.length === 0) selectObject(null);
    }
});

const inpW = document.getElementById('inp-w');
const inpH = document.getElementById('inp-h');
const inpD = document.getElementById('inp-d');

// Transform Controls — only used for ROTATE mode
const transformControl = new THREE.TransformControls(camera, renderer.domElement);
transformControl.setRotationSnap(Math.PI / 2); // 90-degree snaps
transformControl.setTranslationSnap(null);
transformControl.setScaleSnap(null);
transformControl.addEventListener('dragging-changed', function (event) {
    controls.enabled = !event.value;
    isDragging = event.value;
    if (!event.value && selectedObject) {
        checkCollisions(selectedObject);
    }
});
transformControl.addEventListener('change', () => {
    if (selectedObject && selectedObject.userData.boxHelper) {
        selectedObject.userData.boxHelper.update();
    }
});
// Start hidden — only show in rotate mode
transformControl.visible = false;

function enforceFloorLimit(obj) {
    obj.updateMatrixWorld();
    const box = new THREE.Box3().setFromObject(obj);
    const bottomY = box.min.y; // actual bottom of the object in world space
    if (bottomY < 0) {
        obj.position.y -= bottomY; // push up exactly so bottom sits at y=0
    }
}

function checkCollisions(movedObject) {
    // Groups use BoxHelper, just return for now
    if (movedObject.userData.isParametricGroup) {
        if(movedObject.userData.boxHelper) movedObject.userData.boxHelper.update();
        return;
    }
    
    let hasCollision = false;
    
    movedObject.updateMatrixWorld();
    movedObject.userData.obb.copy(movedObject.geometry.userData.obb);
    movedObject.userData.obb.applyMatrix4(movedObject.matrixWorld);
    
    for (const obj of objects) {
        if (obj === movedObject) continue;
        if (obj.userData.isParametricGroup) continue;
        if (!obj.userData.obb) continue;
        
        obj.updateMatrixWorld();
        obj.userData.obb.copy(obj.geometry.userData.obb);
        obj.userData.obb.applyMatrix4(obj.matrixWorld);
        
        if (movedObject.userData.obb.intersectsOBB(obj.userData.obb)) {
            hasCollision = true;
            break;
        }
    }
    
    if (movedObject.userData.edges) {
        if (hasCollision) {
            movedObject.userData.edges.material.color.setHex(0xff0000);
        } else {
            movedObject.userData.edges.material.color.setHex(0x00ff00);
        }
    }
}

function applySnapping(moved) {
    if (moved.userData.isSnapping) return;
    moved.userData.isSnapping = true;

    const SNAP_FACE = 5;   // mm — snap cara-a-cara (atracción a los 5mm)
    const SNAP_EDGE = 5;   // mm — snap de alineación de bordes (atracción a los 5mm)

    moved.updateMatrixWorld();
    const boxM = new THREE.Box3().setFromObject(moved);

    for (const obj of objects) {
        if (obj === moved) continue;
        obj.updateMatrixWorld();
        const boxE = new THREE.Box3().setFromObject(obj);

        // === SNAP CARA-A-CARA (X) ===
        const dx1 = boxE.min.x - boxM.max.x;
        const dx2 = boxM.min.x - boxE.max.x;
        if (Math.abs(dx1) < SNAP_FACE) {
            moved.position.x += dx1;
            boxM.setFromObject(moved);
        } else if (Math.abs(dx2) < SNAP_FACE) {
            moved.position.x -= dx2;
            boxM.setFromObject(moved);
        }

        // === SNAP CARA-A-CARA (Z) ===
        const dz1 = boxE.min.z - boxM.max.z;
        const dz2 = boxM.min.z - boxE.max.z;
        if (Math.abs(dz1) < SNAP_FACE) {
            moved.position.z += dz1;
            boxM.setFromObject(moved);
        } else if (Math.abs(dz2) < SNAP_FACE) {
            moved.position.z -= dz2;
            boxM.setFromObject(moved);
        }

        // === SNAP CARA-A-CARA (Y: encima/debajo) ===
        const dy1 = boxE.min.y - boxM.max.y;
        const dy2 = boxM.min.y - boxE.max.y;
        if (Math.abs(dy1) < SNAP_FACE) {
            moved.position.y += dy1;
            boxM.setFromObject(moved);
        } else if (Math.abs(dy2) < SNAP_FACE) {
            moved.position.y -= dy2;
            boxM.setFromObject(moved);
        }

        // === SNAP DE ALINEACIÓN SEGURA (Bordes) ===
        // Y: Top/Bottom
        const dTopTop = boxE.max.y - boxM.max.y;
        if (Math.abs(dTopTop) < SNAP_EDGE) { moved.position.y += dTopTop; boxM.setFromObject(moved); }
        const dBotBot = boxE.min.y - boxM.min.y;
        if (Math.abs(dBotBot) < SNAP_EDGE) { moved.position.y += dBotBot; boxM.setFromObject(moved); }

        // X: Left/Right
        const dLeftLeft = boxE.min.x - boxM.min.x;
        if (Math.abs(dLeftLeft) < SNAP_EDGE) { moved.position.x += dLeftLeft; boxM.setFromObject(moved); }
        const dRightRight = boxE.max.x - boxM.max.x;
        if (Math.abs(dRightRight) < SNAP_EDGE) { moved.position.x += dRightRight; boxM.setFromObject(moved); }

        // Z: Front/Back
        const dFrontFront = boxE.min.z - boxM.min.z;
        if (Math.abs(dFrontFront) < SNAP_EDGE) { moved.position.z += dFrontFront; boxM.setFromObject(moved); }
        const dBackBack = boxE.max.z - boxM.max.z;
        if (Math.abs(dBackBack) < SNAP_EDGE) { moved.position.z += dBackBack; boxM.setFromObject(moved); }

    }

    enforceFloorLimit(moved);
    moved.userData.isSnapping = false;
}

transformControl.addEventListener('change', function () {
    if(selectedObject) {
        if(transformControl.getMode() === 'translate' && !selectedObject.userData.isSnapping) {
            applySnapping(selectedObject);
        }
        enforceFloorLimit(selectedObject);
        checkCollisions(selectedObject);
    }
});
scene.add(transformControl);

function selectObject(obj) {
    // Restore previous object's material
    if (selectedObject) {
        if(selectedObject.userData.edges) {
            selectedObject.remove(selectedObject.userData.edges);
            selectedObject.userData.edges = null;
        }
        if(selectedObject.userData.boxHelper) {
            scene.remove(selectedObject.userData.boxHelper);
            selectedObject.userData.boxHelper = null;
        }
        transformControl.detach();
    }
    
    selectedObject = obj;
    
    if (selectedObject) {
        if (selectedObject.userData.isParametricGroup || selectedObject.userData.isAssemblyGroup) {
            const boxColor = selectedObject.userData.isAssemblyGroup ? 0x2563eb : 0x00ff00;
            const boxHelper = new THREE.BoxHelper(selectedObject, boxColor);
            scene.add(boxHelper);
            selectedObject.userData.boxHelper = boxHelper;
            
        } else {
            // Highlight new object with Flatma edges and spheres
            const edges = new THREE.EdgesGeometry(selectedObject.geometry);
            const line = new THREE.LineSegments(edges, new THREE.LineBasicMaterial({ color: 0x00ff00, linewidth: 2 }));
            
            selectedObject.userData.edges = line;
            selectedObject.add(line);
            
        }
        updateDimensionInputs();
        checkCollisions(selectedObject);
    } else {
        updateDimensionInputs();
        transformControl.detach();
    }
}

// --- Unit Selection Logic ---
const unitSelect = document.getElementById('unit-select');
const lblW = document.getElementById('lbl-w');
const lblH = document.getElementById('lbl-h');
const lblD = document.getElementById('lbl-d');
const btnGuardar = document.getElementById('btn-guardar-medidas');

// Module params (Drawer / Door)
const moduleParamsContainer = document.getElementById('module-params-container');
const moduleParamsTitle = document.getElementById('module-params-title');
const inpCajonH = document.getElementById('inp-cajon-h');
const inpCajonCantidad = document.getElementById('inp-cajon-cantidad');
const selFrenteTipo = document.getElementById('sel-frente-tipo');
const moduleOverlapRow = document.getElementById('module-overlap-row');
const inpCajonSolape = document.getElementById('inp-cajon-solape');
const selPuertaOrientacion = document.getElementById('sel-puerta-orientacion');

const colCajonH = document.getElementById('col-cajon-h');
const colCajonQty = document.getElementById('col-cajon-qty');
const colPuertaOrientacion = document.getElementById('col-puerta-orientacion');

selFrenteTipo?.addEventListener('change', (e) => {
    if (e.target.value === 'interno') {
        if (moduleOverlapRow) moduleOverlapRow.style.display = 'none';
    } else {
        if (moduleOverlapRow) moduleOverlapRow.style.display = 'flex';
    }
});

const unitFactors = {
    'mm': 1,
    'cm': 10,
    'in': 25.4,
    'ft': 304.8
};

let currentUnit = 'mm';

unitSelect?.addEventListener('change', (e) => {
    currentUnit = e.target.value;
    updateDimensionInputs();
});

function isPieceHorizontal(obj) {
    if (!obj || !obj.userData) return false;
    if (obj.userData.pieceType === 'horizontal') return true;
    if (obj.userData.pieceType === 'vertical') return false;
    const { w = 0, h = 0, d = 0 } = obj.userData;
    return (h <= w && h <= d && !obj.userData.isParametricGroup && !obj.userData.isAssemblyGroup);
}

function updateDimensionInputs() {
    if (!selectedObject) {
        if (inpW) inpW.value = 0;
        if (inpH) inpH.value = 0;
        if (inpD) inpD.value = 0;
        if (lblW) lblW.innerHTML = `Grosor`;
        if (lblH) lblH.innerHTML = `Alto`;
        if (lblD) lblD.innerHTML = `Ancho`;
        if (drawerParamsContainer) drawerParamsContainer.style.display = 'none';
        return;
    }
    
    const factor = (unitFactors && unitFactors[currentUnit]) ? unitFactors[currentUnit] : 1;

    // Si es un grupo ensamblado completo, mostramos las dimensiones exteriores totales
    if (selectedObject.userData && selectedObject.userData.isAssemblyGroup) {
        const bbox = new THREE.Box3().setFromObject(selectedObject);
        const totalW = Math.round(bbox.max.x - bbox.min.x);
        const totalH = Math.round(bbox.max.y - bbox.min.y);
        const totalD = Math.round(bbox.max.z - bbox.min.z);
        if (lblW) lblW.innerHTML = `Ancho Tot.`;
        if (lblH) lblH.innerHTML = `Alto Tot.`;
        if (lblD) lblD.innerHTML = `Fondo Tot.`;
        if (inpW) inpW.value = +(totalW / factor).toFixed(2);
        if (inpH) inpH.value = +(totalH / factor).toFixed(2);
        if (inpD) inpD.value = +(totalD / factor).toFixed(2);
        return;
    }

    const w = selectedObject.userData.w || 0;
    const h = selectedObject.userData.h || 0;
    const d = selectedObject.userData.d || 0;
    
    let grosor, alto, ancho;
    const isH = isPieceHorizontal(selectedObject);
    
    if (isH) {
        // En pieza horizontal: h es el grosor (18mm), d es el fondo/profundidad (500mm), w es el ancho (500mm)
        grosor = h;
        alto = d;
        ancho = w;
        if (lblW) lblW.innerHTML = `Grosor`;
        if (lblH) lblH.innerHTML = `Fondo`;
        if (lblD) lblD.innerHTML = `Ancho`;
    } else {
        // En pieza vertical o grupo: w es el grosor (18mm), h es la altura (500mm), d es el ancho (500mm)
        grosor = w;
        alto = h;
        ancho = d;
        if (lblW) lblW.innerHTML = `Grosor`;
        if (lblH) lblH.innerHTML = `Alto`;
        if (lblD) lblD.innerHTML = `Ancho`;
    }
    
    if (inpW) inpW.value = +(grosor / factor).toFixed(2);
    if (inpH) inpH.value = +(alto / factor).toFixed(2);
    if (inpD) inpD.value = +(ancho / factor).toFixed(2);

    // Toggle module params visibility
    if (moduleParamsContainer) {
        const isDrawer = selectedObject.userData.isParametricGroup && selectedObject.userData.name === 'Cajón Armado';
        const isDoor = selectedObject.userData.isParametricGroup && selectedObject.userData.name === 'Puerta Armada';

        if (isDrawer || isDoor) {
            moduleParamsContainer.style.display = 'block';
            moduleParamsTitle.innerHTML = isDrawer ? 'Parámetros del Cajón' : 'Parámetros de Puerta';
            
            // Show/Hide specific columns
            if (colCajonH) colCajonH.style.display = isDrawer ? 'block' : 'none';
            if (colCajonQty) colCajonQty.style.display = isDrawer ? 'block' : 'none';
            if (colPuertaOrientacion) colPuertaOrientacion.style.display = isDoor ? 'block' : 'none';

            if (isDrawer) {
                if (inpCajonH && selectedObject.userData.drawerInnerH) inpCajonH.value = +(selectedObject.userData.drawerInnerH / factor).toFixed(2);
                if (inpCajonCantidad) inpCajonCantidad.value = selectedObject.userData.drawerQty || 1;
            } else if (isDoor) {
                if (selPuertaOrientacion && selectedObject.userData.doorOrientation) selPuertaOrientacion.value = selectedObject.userData.doorOrientation;
            }

            if (selFrenteTipo && selectedObject.userData.drawerFrontType) {
                selFrenteTipo.value = selectedObject.userData.drawerFrontType;
                if (moduleOverlapRow) {
                    moduleOverlapRow.style.display = selectedObject.userData.drawerFrontType === 'interno' ? 'none' : 'flex';
                }
            }
            if (inpCajonSolape && selectedObject.userData.drawerOverlap !== undefined) {
                inpCajonSolape.value = +(selectedObject.userData.drawerOverlap / factor).toFixed(2);
            }
        } else {
            moduleParamsContainer.style.display = 'none';
        }
    }
}

function saveDimensionsFromInputs() {
    if (!selectedObject) return;
    
    const factor = (unitFactors && unitFactors[currentUnit]) ? unitFactors[currentUnit] : 1;
    const valGrosor = parseFloat(inpW?.value) * factor;
    const valAlto   = parseFloat(inpH?.value) * factor;
    const valAncho  = parseFloat(inpD?.value) * factor;
    
    if (isNaN(valGrosor) || isNaN(valAlto) || isNaN(valAncho) || valGrosor <= 0 || valAlto <= 0 || valAncho <= 0) return;
    
    const isH = isPieceHorizontal(selectedObject);
    let newW, newH, newD;
    
    if (isH) {
        newH = valGrosor; // Y es grosor
        newD = valAlto;   // Z es fondo/profundidad
        newW = valAncho;  // X es ancho
    } else {
        newW = valGrosor; // X es grosor
        newH = valAlto;   // Y es alto
        newD = valAncho;  // Z es ancho/profundidad
    }
    
    selectedObject.userData.w = newW;
    selectedObject.userData.h = newH;
    selectedObject.userData.d = newD;

    if (selectedObject.userData.isParametricGroup && selectedObject.userData.name === 'Cajón Armado') {
        if (inpCajonH) selectedObject.userData.drawerInnerH = parseFloat(inpCajonH.value) * factor;
        if (inpCajonCantidad) selectedObject.userData.drawerQty = parseInt(inpCajonCantidad.value) || 1;
        if (selFrenteTipo) selectedObject.userData.drawerFrontType = selFrenteTipo.value;
        if (inpCajonSolape) selectedObject.userData.drawerOverlap = parseFloat(inpCajonSolape.value) * factor;
    }
    
    if (selectedObject.userData.isParametricGroup && selectedObject.userData.name === 'Puerta Armada') {
        if (selPuertaOrientacion) selectedObject.userData.doorOrientation = selPuertaOrientacion.value;
        if (selFrenteTipo) selectedObject.userData.drawerFrontType = selFrenteTipo.value;
        if (inpCajonSolape) selectedObject.userData.drawerOverlap = parseFloat(inpCajonSolape.value) * factor;
    }
    
    if (selectedObject.userData.isParametricGroup) {
        if (!selectedObject.userData.isWizardGroup) {
            updateParametricGroup(selectedObject);
        } else {
            alert("Redimensionar módulos complejos del catálogo debe hacerse desde el Editor Paramétrico.");
            return;
        }
    } else {
        selectedObject.geometry.dispose();
        selectedObject.geometry = new THREE.BoxGeometry(newW, newH, newD);
        selectedObject.geometry.computeBoundingBox();
        selectedObject.geometry.userData.obb = new THREE.OBB().fromBox3(selectedObject.geometry.boundingBox);
        
        if (selectedObject.userData.edges) {
            selectedObject.remove(selectedObject.userData.edges);
            const edges = new THREE.EdgesGeometry(selectedObject.geometry);
            const line = new THREE.LineSegments(edges, new THREE.LineBasicMaterial({ color: 0x00ff00, linewidth: 2 }));
            selectedObject.userData.edges = line;
            selectedObject.add(line);
        }
    }
    
    selectedObject.userData.obb = new THREE.OBB().fromBox3(new THREE.Box3().setFromObject(selectedObject));
    enforceFloorLimit(selectedObject);
    checkCollisions(selectedObject);
    if (selectedObject.userData.boxHelper) selectedObject.userData.boxHelper.update();
    updateDimensionVisuals();
}

// --- Save Dimensions ---
if (btnGuardar) {
    btnGuardar.addEventListener('click', (e) => {
        e.preventDefault();
        saveDimensionsFromInputs();
        showToast('Medidas actualizadas', '#27ae60');
    });
}

// Parametric Group Updater
function updateParametricGroup(group) {
    const W = group.userData.w;
    const H = group.userData.h;
    const D = group.userData.d;
    const esp = group.userData.esp || 18;

    if (group.userData.name === 'Cajón Armado') {
        updateDrawerGroup(group, W, H, D, esp);
        return;
    }

    if (group.userData.name === 'Puerta Armada') {
        updateDoorGroup(group, W, H, D, esp);
        return;
    }
    
    group.children.forEach(child => {
        if(child.type !== "Mesh") return;
        
        let newW = child.userData.w;
        let newH = child.userData.h;
        let newD = child.userData.d;
        
        if(child.userData.rules) {
            if(child.userData.rules.w === '100%') newW = W;
            if(child.userData.rules.w === '100%-2esp') newW = W - (esp * 2);
            if(child.userData.rules.h === '100%') newH = H;
        }
        
        if (newW !== child.userData.w || newH !== child.userData.h || newD !== child.userData.d) {
            child.userData.w = newW;
            child.userData.h = newH;
            child.userData.d = newD;
            child.geometry.dispose();
            child.geometry = new THREE.BoxGeometry(newW, newH, newD);
            child.geometry.computeBoundingBox();
            child.geometry.userData.obb = new THREE.OBB().fromBox3(child.geometry.boundingBox);
        }
        
        if(child.userData.rules) {
            if(child.userData.rules.x === 'left') child.position.x = -W/2 + esp/2;
            if(child.userData.rules.x === 'right') child.position.x = W/2 - esp/2;
            if(child.userData.rules.x === 'center') child.position.x = 0;
            
            if(child.userData.rules.y === 'top') child.position.y = H - esp/2;
            if(child.userData.rules.y === 'bottom') child.position.y = esp/2;
            
            if(child.userData.rules.z === 'back') child.position.z = -D/2 + esp/2;
        }
    });
    
    if(group.userData.boxHelper) group.userData.boxHelper.update();
}

// Logic specifically for parametric drawer resizing — rebuilds all geometry from scratch
function updateDrawerGroup(group, cabinetW, cabinetH, cabinetD, esp) {
    const innerH    = group.userData.drawerInnerH   || 150;
    const frontType = group.userData.drawerFrontType || 'externo';
    const overlap   = group.userData.drawerOverlap  !== undefined ? group.userData.drawerOverlap : 10;
    const cajonD    = group.userData.drawerBoxDepth  || 450;
    const qty       = Math.max(1, parseInt(group.userData.drawerQty) || 1);
    const finishSave = currentHardwareFinish;

    // Remove all existing drawer meshes and hardware from the group
    const toRemove = [...group.children];
    toRemove.forEach(c => {
        if (c.geometry) c.geometry.dispose();
        group.remove(c);
    });

    // Z positions (same for all drawers)
    let frenteZ;
    if (frontType === 'interno') {
        frenteZ = cabinetD / 2 - esp / 2;
    } else {
        frenteZ = cabinetD / 2 + esp / 2;
    }
    const boxFrontZ = frenteZ - esp / 2;
    const boxBackZ  = boxFrontZ - cajonD;
    const latZ      = (boxFrontZ + boxBackZ) / 2;

    // Drawer dimensions
    const cajonW    = cabinetW - esp * 2 - 26; // minus rail space (13mm/side)
    const interiorW = cajonW - esp * 2;          // minus drawer side panels

    // Slot height distribution: divide interior evenly among N drawers
    const interiorH = cabinetH - 2 * esp;
    const slotH     = interiorH / qty;
    const drawerH   = Math.min(innerH, slotH - 10); // actual box height fits in slot

    // Rail length: nearest 50mm step between 300-550
    const railLen = Math.min(Math.max(Math.round(cajonD / 50) * 50, 300), 550);

    // Helper: create a new mesh panel and add to group
    const makeMesh = (w, h, d, px, py, pz, name, texKey) => {
        const geo = new THREE.BoxGeometry(w, h, d);
        geo.computeBoundingBox();
        geo.userData.obb = new THREE.OBB().fromBox3(geo.boundingBox);
        const mat = createPieceMaterial(texKey || group.userData.textureKey || 'roble', w, h, d);
        const mesh = new THREE.Mesh(geo, mat);
        mesh.userData = { name, w, h, d, textureKey: texKey || 'roble' };
        mesh.position.set(px, py, pz);
        mesh.castShadow = true;
        mesh.receiveShadow = true;
        group.add(mesh);
        return mesh;
    };

    for (let i = 0; i < qty; i++) {
        const slotBottomY  = esp + slotH * i;
        const drawerCenterY = slotBottomY + slotH / 2;

        // Decorative front dimensions
        let frenteW_i, frenteH_i;
        if (frontType === 'interno') {
            frenteW_i = cabinetW - esp * 2 - 4;
            frenteH_i = slotH - 4;
        } else {
            frenteW_i = cabinetW - esp * 2 + overlap * 2;
            const topGap = (i === qty - 1) ? overlap : overlap / 2;
            const botGap = (i === 0)       ? overlap : overlap / 2;
            frenteH_i = slotH + topGap + botGap - 2; // 2mm gap between fronts
        }

        // Decorative front (nogal)
        makeMesh(frenteW_i, frenteH_i, esp, 0, drawerCenterY, frenteZ, `Frente D ${i}`, 'nogal');

        // Inner box walls
        makeMesh(interiorW, drawerH, esp, 0, drawerCenterY, boxFrontZ - esp/2,   `Contra Frente ${i}`);
        makeMesh(interiorW, drawerH, esp, 0, drawerCenterY, boxBackZ  + esp/2,   `Trasera ${i}`);
        makeMesh(esp, drawerH, cajonD, -cajonW/2 + esp/2, drawerCenterY, latZ,   `Lat Izq ${i}`);
        makeMesh(esp, drawerH, cajonD,  cajonW/2 - esp/2, drawerCenterY, latZ,   `Lat Der ${i}`);

        // Bottom panel
        makeMesh(interiorW, esp, cajonD - esp*2, 0, drawerCenterY - drawerH/2 + esp/2 + 15, latZ, `Fondo ${i}`);

        // Rails — placed directly at drawerCenterY (no stale matrix issue, set explicitly)
        ['left', 'right'].forEach(side => {
            const rail = createDrawerSlide(railLen, side, finishSave);
            const xPos = side === 'right' ? cajonW / 2 + 6.35 : -cajonW / 2 - 6.35;
            rail.position.set(xPos, drawerCenterY, boxFrontZ - railLen / 2);
            rail.rotation.y = Math.PI / 2;
            group.add(rail);
        });

        // Knob on front
        const knob = createKnobHandle(finishSave);
        knob.userData.hardwareType = 'pomo';
        knob.position.set(0, drawerCenterY, frenteZ + esp / 2 + 22);
        group.add(knob);
    }

    // Store metadata for future updates
    group.userData.drawerBoxWidth     = cajonW;
    group.userData.drawerBoxDepth     = cajonD;
    group.userData.drawerBoxFrontZ    = boxFrontZ;
    group.userData.drawerInnerCenterY = esp + slotH / 2; // center of first slot
}

function updateDoorGroup(group, cabinetW, cabinetH, cabinetD, esp) {
    const frontType = group.userData.drawerFrontType || 'externo';
    const overlap = (frontType === 'externo') ? (group.userData.drawerOverlap !== undefined ? group.userData.drawerOverlap : 10) : 0;
    const orientation = group.userData.doorOrientation || 'izq'; // izq, der, aba
    
    // Clear old door meshes inside group
    while(group.children.length > 0) {
        const child = group.children[0];
        if(child.geometry) child.geometry.dispose();
        if(child.material && Array.isArray(child.material)) child.material.forEach(m => m.dispose());
        else if (child.material) child.material.dispose();
        group.remove(child);
    }
    
    // Dimensions
    let doorW, doorH, doorD = esp;
    let gap = 2; // huelgo per side
    if (frontType === 'interno') {
        doorW = cabinetW - (esp * 2) - (gap * 2);
        doorH = cabinetH - (esp * 2) - (gap * 2);
    } else {
        doorW = cabinetW + (overlap * 2);
        doorH = cabinetH + (overlap * 2);
    }
    
    const panel = createPanel(doorW, doorH, doorD, 0, 0, 0, 'Panel de Puerta');
    panel.userData.pieceType = 'vertical';
    // Usar el material global seleccionado actualmente
    if (typeof currentMaterialId !== 'undefined') {
        applyMaterialToMesh(panel, currentMaterialId);
    }
    
    group.add(panel);
    
    // Attach Hinges
    const edgeOffset = 21.5;
    const vOffset = 100;
    
    let hingesData = [];
    if (orientation === 'izq') {
        hingesData.push({ x: -doorW/2 + edgeOffset, y: doorH/2 - vOffset, rotZ: 0 });
        hingesData.push({ x: -doorW/2 + edgeOffset, y: -doorH/2 + vOffset, rotZ: 0 });
        if(doorH > 1000) hingesData.push({ x: -doorW/2 + edgeOffset, y: 0, rotZ: 0 });
    } else if (orientation === 'der') {
        hingesData.push({ x: doorW/2 - edgeOffset, y: doorH/2 - vOffset, rotZ: Math.PI });
        hingesData.push({ x: doorW/2 - edgeOffset, y: -doorH/2 + vOffset, rotZ: Math.PI });
        if(doorH > 1000) hingesData.push({ x: doorW/2 - edgeOffset, y: 0, rotZ: Math.PI });
    } else if (orientation === 'aba') {
        hingesData.push({ x: doorW/2 - vOffset, y: doorH/2 - edgeOffset, rotZ: -Math.PI/2 });
        hingesData.push({ x: -doorW/2 + vOffset, y: doorH/2 - edgeOffset, rotZ: -Math.PI/2 });
    }
    
    hingesData.forEach(pos => {
        const hinge = createCupHinge(typeof currentHardwareFinish !== 'undefined' ? currentHardwareFinish : 'metal');
        hinge.scale.setScalar(0.9);
        hinge.position.set(pos.x, pos.y, -doorD/2 - 1); // inner face
        hinge.rotation.z = pos.rotZ;
        hinge.rotation.y = 0; // facing backwards towards cabinet inside
        group.add(hinge);
    });
}

function spawnParametricDoor() {
    const group = new THREE.Group();
    group.userData.isParametricGroup = true;
    group.userData.name = 'Puerta Armada';
    group.userData.w = 500;
    group.userData.h = 700;
    group.userData.d = 18; 
    group.userData.esp = 18;
    group.userData.drawerFrontType = 'externo';
    group.userData.drawerOverlap = 10;
    group.userData.doorOrientation = 'izq';
    
    updateDoorGroup(group, group.userData.w, group.userData.h, group.userData.d, group.userData.esp);
    
    group.position.set(0, group.userData.h / 2, 0); 
    scene.add(group);
    objects.push(group);
    selectObject(group);
    saveState();
    showToast('Puerta Paramétrica añadida', '#3498db');
}

function spawnParametricDrawer() {
    const group = new THREE.Group();
    group.userData.isParametricGroup = true;
    group.userData.name = 'Cajón Armado';
    group.userData.w = 500;
    group.userData.h = 200;
    group.userData.d = 450;
    group.userData.esp = 18;
    group.userData.drawerInnerH = 150;
    group.userData.drawerQty = 1;
    group.userData.drawerFrontType = 'externo';
    group.userData.drawerOverlap = 10;
    
    updateDrawerGroup(group, group.userData.w, group.userData.h, group.userData.d, group.userData.esp);
    
    group.position.set(0, group.userData.h / 2, 0);
    scene.add(group);
    objects.push(group);
    selectObject(group);
    saveState();
    showToast('Cajón Paramétrico añadido', '#3498db');
}

// --- Muebles de Ejemplo Simples ---
function clearAllObjects() {
    objects.forEach(obj => scene.remove(obj));
    objects.length = 0;
    isSceneGrouped = false;
    assemblyGroup = null;
    updateAgruparButton();
    selectObject(null);
}

window.cargarEjemploEscritorio = function() {
    clearAllObjects();
    const esp = 18;
    const W = 1200, H = 750, D = 600;
    
    // 2 Patas / Laterales
    const legL = createPanel(esp, H - esp, D, -W/2 + esp/2, (H - esp)/2, 0, 'Lateral Izquierdo');
    legL.userData.pieceType = 'vertical';
    const legR = createPanel(esp, H - esp, D, W/2 - esp/2, (H - esp)/2, 0, 'Lateral Derecho');
    legR.userData.pieceType = 'vertical';
    
    // Tapa Superior
    const top = createPanel(W, esp, D, 0, H - esp/2, 0, 'Cubierta Superior');
    top.userData.pieceType = 'horizontal';
    
    // Faldón Trasero
    const back = createPanel(W - 2*esp, 300, esp, 0, H - esp - 150, -D/2 + esp/2 + 50, 'Faldón');
    back.userData.pieceType = 'vertical';

    camera.position.set(W * 0.9, H * 1.3, W * 1.2);
    controls.target.set(0, H / 2, 0);
    controls.update();
    const inp = document.getElementById('project-name-input');
    if (inp) inp.value = 'Escritorio Básico';
    showToast('Escritorio cargado', '#27ae60');
};

window.cargarEjemploEstanteria = function() {
    clearAllObjects();
    const esp = 18;
    const W = 600, H = 1200, D = 300;
    
    // Laterales
    const legL = createPanel(esp, H, D, -W/2 + esp/2, H/2, 0, 'Lateral Izquierdo');
    legL.userData.pieceType = 'vertical';
    const legR = createPanel(esp, H, D, W/2 - esp/2, H/2, 0, 'Lateral Derecho');
    legR.userData.pieceType = 'vertical';
    
    // Base y Techo
    const base = createPanel(W - 2*esp, esp, D, 0, 70 + esp/2, 0, 'Base Inferior');
    base.userData.pieceType = 'horizontal';
    const top = createPanel(W - 2*esp, esp, D, 0, H - esp/2, 0, 'Techo Superior');
    top.userData.pieceType = 'horizontal';
    
    // Zócalo
    const zocalo = createPanel(W - 2*esp, 70, esp, 0, 35, D/2 - esp/2 - 20, 'Zócalo');
    zocalo.userData.pieceType = 'vertical';
    
    // 2 Repisas intermedias
    const gap = (H - 70 - esp*3) / 3;
    const repisa1 = createPanel(W - 2*esp, esp, D - 10, 0, 70 + esp + gap, -5, 'Repisa 1');
    repisa1.userData.pieceType = 'horizontal';
    const repisa2 = createPanel(W - 2*esp, esp, D - 10, 0, 70 + esp*2 + gap*2, -5, 'Repisa 2');
    repisa2.userData.pieceType = 'horizontal';

    camera.position.set(W * 1.5, H * 0.9, W * 2.0);
    controls.target.set(0, H / 2, 0);
    controls.update();
    const inp = document.getElementById('project-name-input');
    if (inp) inp.value = 'Estantería 3 Repisas';
    showToast('Estantería cargada', '#27ae60');
};

window.cargarEjemploGabinete = function() {
    clearAllObjects();
    const esp = 18;
    const W = 1000, H = 550, D = 400;
    
    // Laterales
    const legL = createPanel(esp, H - esp, D, -W/2 + esp/2, (H - esp)/2, 0, 'Lateral Izquierdo');
    legL.userData.pieceType = 'vertical';
    const legR = createPanel(esp, H - esp, D, W/2 - esp/2, (H - esp)/2, 0, 'Lateral Derecho');
    legR.userData.pieceType = 'vertical';
    
    // Base y Cubierta
    const base = createPanel(W - 2*esp, esp, D, 0, esp/2, 0, 'Base Inferior');
    base.userData.pieceType = 'horizontal';
    const top = createPanel(W, esp, D, 0, H - esp/2, 0, 'Cubierta Superior');
    top.userData.pieceType = 'horizontal';
    
    // División central
    const divH = H - esp*2;
    const centerDiv = createPanel(esp, divH, D - 10, 0, esp + divH/2, -5, 'División Central');
    centerDiv.userData.pieceType = 'vertical';
    
    // Repisas
    const repW = (W - 3*esp) / 2;
    const repL = createPanel(repW, esp, D - 15, -W/4, H/2, -7.5, 'Repisa Izq');
    repL.userData.pieceType = 'horizontal';
    const repR = createPanel(repW, esp, D - 15, W/4, H/2, -7.5, 'Repisa Der');
    repR.userData.pieceType = 'horizontal';

    camera.position.set(W * 1.1, H * 1.3, W * 1.3);
    controls.target.set(0, H / 2, 0);
    controls.update();
    const inp = document.getElementById('project-name-input');
    if (inp) inp.value = 'Gabinete TV Bajo';
    showToast('Gabinete cargado', '#27ae60');
};

window.cargarEjemploMesa = function() {
    clearAllObjects();
    const esp = 18;
    const W = 800, H = 450, D = 500;
    
    // Tablero
    const top = createPanel(W, esp, D, 0, H - esp/2, 0, 'Tablero Mesa');
    top.userData.pieceType = 'horizontal';
    
    // 4 Patas
    const legSize = 40;
    const legH = H - esp;
    const offX = W/2 - legSize/2 - 20;
    const offZ = D/2 - legSize/2 - 20;
    const p1 = createPanel(legSize, legH, legSize, -offX, legH/2, -offZ, 'Pata 1');
    p1.userData.pieceType = 'vertical';
    const p2 = createPanel(legSize, legH, legSize, offX, legH/2, -offZ, 'Pata 2');
    p2.userData.pieceType = 'vertical';
    const p3 = createPanel(legSize, legH, legSize, -offX, legH/2, offZ, 'Pata 3');
    p3.userData.pieceType = 'vertical';
    const p4 = createPanel(legSize, legH, legSize, offX, legH/2, offZ, 'Pata 4');
    p4.userData.pieceType = 'vertical';

    camera.position.set(W * 1.2, H * 1.4, W * 1.4);
    controls.target.set(0, H / 2, 0);
    controls.update();
    const inp = document.getElementById('project-name-input');
    if (inp) inp.value = 'Mesa de Centro';
    showToast('Mesa de centro cargada', '#27ae60');
};

document.getElementById('btn-ejemplo-escritorio')?.addEventListener('click', (e) => { e?.preventDefault?.(); window.cargarEjemploEscritorio(); });
document.getElementById('btn-ejemplo-estanteria')?.addEventListener('click', (e) => { e?.preventDefault?.(); window.cargarEjemploEstanteria(); });
document.getElementById('btn-ejemplo-gabinete')?.addEventListener('click', (e) => { e?.preventDefault?.(); window.cargarEjemploGabinete(); });
document.getElementById('btn-ejemplo-mesa')?.addEventListener('click', (e) => { e?.preventDefault?.(); window.cargarEjemploMesa(); });
document.getElementById('btn-add-puerta-param')?.addEventListener('click', (e) => {
    e.preventDefault();
    spawnParametricDoor();
});
document.getElementById('btn-add-cajon-param')?.addEventListener('click', (e) => {
    e.preventDefault();
    spawnParametricDrawer();
});
document.getElementById('btn-escritorio')?.addEventListener('click', (e) => { e?.preventDefault?.(); window.cargarEjemploEscritorio(); });

let roomGroup = null;
document.getElementById('btn-habitacion').addEventListener('click', function() {
    const toggle = this.querySelector('.toggle-switch');
    if (roomGroup) {
        scene.remove(roomGroup);
        roomGroup = null;
        gridHelper.visible = true; // Mostrar la cuadrícula
        if (toggle) toggle.classList.remove('active');
    } else {
        roomGroup = new THREE.Group();
        
        // Textura del Piso
        const textureLoader = new THREE.TextureLoader();
        // Imagen base64 para evitar errores de CORS en archivos locales
        const b64 = "data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD/2wCEAAkGBxMTEhUSExMWFRUVFxcXFxcXGBgaGBUVFRUXFxUXFxgaHSggGBolHRUVITEhJSkrLi4uFx8zODMtNygtLisBCgoKDg0OFQ8QGy0dHR0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLf/AABEIAOEA4QMBIgACEQEDEQH/xAAYAAADAQEAAAAAAAAAAAAAAAAAAQIDBP/EADAQAAIABAQFAwMEAwEAAAAAAAABAhFRYQMSE5EhcYGh8DFBsVJi0RTB4fEEIoJC/8QAGAEBAQEBAQAAAAAAAAAAAAAAAAECBQP/xAAbEQEBAQEBAQEBAAAAAAAAAAAAEQEhEkECgf/aAAwDAQACEQMRAD8A5/V+wlJHLNfSOf2nF8u9XQ4hw4cPuuJyT+1hln7d2PBXblhp2E8tDl0ue4nh+NjPyldeaGgKKGhyLDdfkFhvxssHYsomoanLFA/GTkfjJFd3CwpKxyYc0/zKRqp/aZVs4LonS84Gbb+0iJci5iOhYHnAbwl5I5ZoMyv51LErpWEvGgyHNwowkqPZCLXS8Ne/7ExYa8kc81fYJq4iVs8JU+BaMNCJq4T5l6NFgqnwUsNU+DHO6CcfMTSujSh8l+QWHDXzc585SboSaNnhqrHL2nuYTdO4nFYQrqm6rZAck1RgPIUUQCzqgZlfY0DPZlLqupmnaIJ0T6iDTqCaIk6ClVMRG6iRKi6Gacq7DTp/YitHIWXoE0PUh8RnVCgX9jWFy2JTXszSV+5k0LCsgUFkKLn3IzOvY3mIuKDluGXl3Im6hkfjLEX1FDGqiWGxOBU3/BJgHjKoayHDhopYKHDqNUzixWdGSFe0yZP24bFyDBRPxFLM6GyKUxRioHYeSK2xu2hQIzVZZIrbMHA7dzdMxim/wM2idPluA8qAqLbsxf8AL2NsvkgkZrTDMPP5I2kDhFGGbyX8h56GrhVEGVURaM0n4mDnbY0UCoDhVBRmp0QouS7s14UHNfSSjlcLtsEML8R18PpQuH0oZqOZwuwSZ0TVEE1Yvojmc6BKKj3OpSDh4i+iOZOK5ScVDfKvEPTsT0Rz5n9I87+l7m+nYIYeQ9Ec+rZjWMv74G+VnFKKvdEzasbvFXstvyxarp3IhhfvE+w1AveJl4i88VtxZoqpchqFX7jyoEZ/7VKlEOSqwkvGKRPGq2GXw8YCkOJMULuSo2DiZlVNMIcRmUTit3HBFFbYo1Ub8Y5slMZASExZRpXKE15TqS1RsuVwyigTuJxFqETlUlCUV5gmKJ3M23VlGibDM6GXG+w5RXKNViGmbgc+aKjHr2JB0uJAYwYyoPVRmC4o+Jypr2Ru40zFJDFWjRqSmZZfEUn7TKKcRnm9gauElQ0g9CW2n6TKzCcVeBQZnTuATX1ebABonZhP7XsUsKxSwzFxYzy2ew8lnsa5Ayq4qMdOw3BZmrguxKB/UFZ9OwTt2ZrpxfUPI6gYdHswy2+TbI6kuB1FREKdPkfGw8kXkgaiATnYXEpN/SJsKlu6FN1LTYNv3lsEQp1Q+NUW4l9pCgVNpgKV0DlYpLnuUoeZeDJckRpPyR06fnAbw7E5iufSdQ0nV7G+kPRuWpHPp1QLDVGdGlcWk/Jj0RjpQuo/093uaJS9VPr+Qz/a90T1qxn+ndX2A3mvpe6AetSEo+XcpYnIyzczRTMxVuPjL9hKLkTw9ylDD6yLEpZuW4ZlbcpRKiBv3kUqMyqtycznOfyPMnQTlcQp6jDO7EuJXCSED1fECxDNvjwBpssGqjDMQvQftxJFNMtNctyEgUYgqJkcLE+/DgKJcxEaLr8jT5bEQMqewgqQ5OpGHC+hpIaEmwbuWgiXiII4icxphFHIujDGTM4Ybs1xJPcSSuRoaSvuwCVwAtFJXOWcNwcrs15ZdTSqik5f+kciSoy0rCDeKNVQpKqM52Go7Eg1hhui8pjN+SBxRe0grR4fGchaSM04qrYtOK3cIemDwxPEipDuLVf0bNE6oihS9TNxw1Kx8WcMsrW1bM5oUXBtqQ1Q3iQ1RlPnsHnoWI2zqvcaxFU58vkhQ4bu+hYOqaYKOFepz5X5MpJ17/kkHVrw1DVhqcmR1+AiwnUQdWpDUedV7HJpOvcFhvxiDpbVULh7RdznWG77/wABpur3JCrxVd9CMi+4rK77j41YhWeVX2YGn/UQFEJWXcpEoqF2GqqTqPqKMggpsGEKl6jUIACQlhoJIAcNxPD5jjsxddyoMj8kS1fuOaqhsKmBOf8AJq+BmpLiPOv6M/VaIInInWUvRieJP2LmalS43sRqN+z3NHKj2Fl8kaxE6jVNytbz1FluLIXiK11YHi27CWHX5KUEvRk4dJ4tuxLx2Nqw53Y4Frj1YqdwmrvYJqj3EwUo3QM7IcT9k9wzO/YkVeo/JgY5XWLzqBZiNONPgaisa+egONeIn8VlmdBOJ0NuFHsN8hRzzt3FOq7m8gylowWI7/I3FP1S6TTNnCEkSjncN2hqF/UbJrn0G0WkZJfd2BQKrNIkTJEoUECTnLc11OXf8GaylONEiq1LCzuiJcSEn5wKi9aw1i2Mm7ebj7CDVxqgKJciFhlaRA86FDJ1+QUCE1dgOKBChgVu4ZQzWXVyJVWsNW7hkViVG/pW4PFstxdFaQtMnVdhPGdUXo00hmP6i6EJqNVh89wcHMrLcTwwrOKG4lD1L0rvYFh3fnQIFGPOGjzB4dyKSFLiGQAKgdfOQRktEsQOYmp1DMCj5F6ickhRK37mmqgWJCXoznyDJPzgayhf9jUKQGa5EyZ0QxqqKeIqolGPUWW5q3Z/HyDnRdZfsBllGoeZazVXIc3YKziw37Mwihr8nVEzKPizNXEQYadxvCVCuAshrNROmgyKg8k/4HFAaqI0lTuAslnuBQavLf8AkTxeW4LDBYY4i1iuq3KWPdGThVSXAq9kSYroWLMrN5I5FCk/Xtx6SNU1WLZmYrZOwaplO8RL6lzErV4iBxqxlm5+dRTfn9l8pWuryDUuZydF3HldF3Ewq9S4Z1UzyuncJOiEwq/9RyhoZydEGV0RYNcsNAzK+5jxsDbJ5K2cXkxKKxkm/aY1OvYsK1n5MmJqnyTxr2E26khVTQKBP+zN4pSi84DyVppKnccWDYhRLxobil7vYnVLR5ieGqsepz2FqmuonJDfuBWtYC9ChxBN8SXA6IeVv1Q4LmEp+ocaA42v/JlRw9CsiqyM8/YpSMwWoVUHDfsZtq+yJkvY1mI1au+xMlV7meXmNQKq2NRFzRCxFQtYdwcL5cvyOCHi2ZLxfOBtDCi4UrEuDn1CZRUZ1zojOKBv1Yow034yocF36M2hVisnQu/ojHRux6VzdPqTBDMz6WI0bvcHh3Zr1MosOc+IomSuCavuiY4ASS9uxpFpeSQ8vLYz4U7A1zEK1XQF0MXO4+FX8CFbbAY9e4h5K0yu/cGncqXMJWZmqnj93YTidYuxWWw8rtshwZNT+rclQc+5vlsglYtwZcK92Ermu+7DfsKMtN1+A0XVGmW3ZBl5lqREOC6oek6lpO4nC6vYUhacXs12KUEdYRSf1dkLLfsTdU8sX2miwovt6P8AYyhgc1x917HTmivsY39LEPDj9P8AXf8AZE/p4/qXQ21H4ili3Qukc7wIvq7Il/47qzrUfJicTp8l9akcn6Vgv8ZnU4nQMzoPWrMc6wHXuGk6m7dg6C6TGDwXbsPQ84GvRCShsLpEaPLsJ/47tuaZYbCyKoqM/wBM7CNskP1IC3SImELmJoiUvRmVaRHPizT9eBpqW2Jiisaw0pupcJm43TdjhcVtiwaSDMTmdg1eRBSjHMjMhPFEGsOIDiRhqc/gqGLoNwazQEz84Esg0maapzJstRcTO4roWIgmY5ghLBo0RlXkyoYi3MDPfuE7sqbE42Ap3ZO5TjJcRQ0rDnYib84BIRGnChM0SoB5AHNUELTuBODKH1LYwNfU+EyIvUAKINUMBpjIBABqTD+AAikymAFDQ4gAgVDWEQHnrRF4Hp0ADXxEs2g82AC/pMQ/ciMQDFUigACWKAAGI0Ii9GAE+qkAAI//2Q==";
        const floorTexture = textureLoader.load(b64);
        floorTexture.wrapS = THREE.RepeatWrapping;
        floorTexture.wrapT = THREE.RepeatWrapping;
        floorTexture.repeat.set(8, 8); // Repite la textura para que no se vea estirada

        const floorMat = new THREE.MeshLambertMaterial({ 
            map: floorTexture,
            color: 0xffffff
        });
        
        const floorGeo = new THREE.PlaneGeometry(4000, 4000);
        const floor = new THREE.Mesh(floorGeo, floorMat);
        floor.rotation.x = -Math.PI / 2;
        floor.position.y = 0; // Regresado a 0
        floor.receiveShadow = true;
        roomGroup.add(floor);
        
        // Solo piso, sin paredes
        
        gridHelper.visible = false; // Ocultar la cuadrícula
        
        scene.add(roomGroup);
        if (toggle) toggle.classList.add('active');
    }
});

document.getElementById('btn-horizontal')?.addEventListener('click', (e) => {
    e?.preventDefault?.();
    // Horizontal panel: 500mm wide, 18mm tall, 500mm deep
    // Place so bottom face is at y=0: center = h/2 = 9
    const w = 500, h = 18, d = 500;
    const mesh = createPanel(w, h, d, 0, h / 2, 0, 'Estante Horizontal');
    mesh.userData.pieceType = 'horizontal';
    updateDimensionInputs();
});

document.getElementById('btn-vertical')?.addEventListener('click', (e) => {
    e?.preventDefault?.();
    // Vertical panel: 18mm wide, 500mm tall, 500mm deep
    // Place so bottom face is at y=0: center = h/2 = 250
    const w = 18, h = 500, d = 500;
    const mesh = createPanel(w, h, d, 0, h / 2, 0, 'Panel Vertical');
    mesh.userData.pieceType = 'vertical';
    updateDimensionInputs();
});

// Delete
document.getElementById('btn-eliminar')?.addEventListener('click', (e) => {
    e?.preventDefault?.();
    if (selectedObject) {
        // Only delete selected
        scene.remove(selectedObject);
        const index = objects.indexOf(selectedObject);
        if (index > -1) objects.splice(index, 1);
        selectObject(null);
    } else {
        // Clear all if none selected (Eliminar Todo)
        objects.forEach(obj => scene.remove(obj));
        objects.length = 0;
        selectObject(null);
    }
});

// Copy
const btnCopiar = document.getElementById('btn-copiar');
if(btnCopiar) {
    btnCopiar.addEventListener('click', (e) => {
        e?.preventDefault?.();
        if(selectedObject) {
            const clone = createPanel(
                selectedObject.userData.w,
                selectedObject.userData.h,
                selectedObject.userData.d,
                selectedObject.position.x + 50,
                selectedObject.position.y,
                selectedObject.position.z,
                (selectedObject.userData.name || 'Panel') + " Copia"
            );
            clone.userData.pieceType = selectedObject.userData.pieceType;
            clone.userData.textureKey = selectedObject.userData.textureKey || currentTextureKey;
            clone.rotation.copy(selectedObject.rotation);
        }
    });
}

// ─── Agrupar / Bloquear Todo ───────────────────────────────
let isSceneGrouped = false;
let assemblyGroup = null;

function updateAgruparButton() {
    const btn = document.getElementById('btn-agrupar');
    const txt = document.getElementById('btn-agrupar-text');
    if (!btn) return;
    if (isSceneGrouped) {
        btn.classList.remove('btn-secondary');
        btn.classList.add('btn-primary');
        if (txt) txt.textContent = 'Desbloquear / Desagrupar';
    } else {
        btn.classList.remove('btn-primary');
        btn.classList.add('btn-secondary');
        if (txt) txt.textContent = 'Bloquear / Agrupar Todo';
    }
}

function groupAllObjects() {
    if (objects.length === 0) {
        showToast('No hay piezas en el lienzo para agrupar', '#e74c3c');
        return;
    }

    if (objects.length === 1 && objects[0].userData && objects[0].userData.isAssemblyGroup) {
        isSceneGrouped = true;
        assemblyGroup = objects[0];
        updateAgruparButton();
        selectObject(assemblyGroup);
        return;
    }

    selectObject(null);

    const bbox = new THREE.Box3();
    objects.forEach(obj => bbox.expandByObject(obj));

    const center = new THREE.Vector3();
    bbox.getCenter(center);
    const pivot = new THREE.Vector3(center.x, bbox.min.y, center.z);

    const group = new THREE.Group();
    group.position.copy(pivot);
    group.userData = {
        name: 'Mueble Agrupado',
        isAssemblyGroup: true,
        isLocked: true,
        w: Math.round(bbox.max.x - bbox.min.x),
        h: Math.round(bbox.max.y - bbox.min.y),
        d: Math.round(bbox.max.z - bbox.min.z)
    };

    const oldObjects = [...objects];
    objects.length = 0;

    oldObjects.forEach(obj => {
        scene.remove(obj);
        obj.position.sub(pivot);
        group.add(obj);
    });

    scene.add(group);
    objects.push(group);

    isSceneGrouped = true;
    assemblyGroup = group;
    updateAgruparButton();
    selectObject(group);
    showToast('Piezas bloqueadas como un solo mueble', '#2563eb');
}

function ungroupObjects() {
    const targetGroup = (selectedObject && selectedObject.userData && selectedObject.userData.isAssemblyGroup) 
        ? selectedObject 
        : assemblyGroup || objects.find(o => o.userData && o.userData.isAssemblyGroup);

    if (!targetGroup) return;

    selectObject(null);

    const groupPos = targetGroup.position.clone();
    const groupRot = targetGroup.rotation.y;
    const children = [...targetGroup.children];

    scene.remove(targetGroup);
    const idx = objects.indexOf(targetGroup);
    if (idx > -1) objects.splice(idx, 1);

    children.forEach(child => {
        child.position.applyAxisAngle(new THREE.Vector3(0, 1, 0), groupRot);
        child.position.add(groupPos);
        child.rotation.y += groupRot;

        scene.add(child);
        objects.push(child);
    });

    isSceneGrouped = false;
    assemblyGroup = null;
    updateAgruparButton();
    showToast('Piezas desbloqueadas para edicion individual', '#f59e0b');
}

document.getElementById('btn-agrupar')?.addEventListener('click', (e) => {
    e?.preventDefault?.();
    if (isSceneGrouped) {
        ungroupObjects();
    } else {
        groupAllObjects();
    }
});

// ─── Selector Visual de Materiales / Texturas ───────────────
document.querySelectorAll('.material-card').forEach(card => {
    card.addEventListener('click', () => {
        document.querySelectorAll('.material-card').forEach(c => c.classList.remove('active'));
        card.classList.add('active');
        activeTextureKey = card.getAttribute('data-texture') || 'roble';
        currentTextureKey = activeTextureKey;
    });
});

document.getElementById('btn-aplicar-pieza')?.addEventListener('click', (e) => {
    e?.preventDefault?.();
    if (!selectedObject) {
        showToast('Selecciona una pieza primero', '#e74c3c');
        return;
    }
    applyMaterialToSelection(activeTextureKey);
});

document.getElementById('btn-aplicar-todo')?.addEventListener('click', (e) => {
    e?.preventDefault?.();
    applyMaterialToAll(activeTextureKey);
});

// ═══════════════════════════════════════════════════════════════════
// PASO 3 — SISTEMA DE HERRAJES (Bisagras, Jaladores, Rieles)
// Normas carpinteria: bisagra cazoleta 35mm, jaladores, correderas
// ═══════════════════════════════════════════════════════════════════

// ─── Materiales PBR metalicos ────────────────────────────────────
const hardwareMaterials = {
    chrome: new THREE.MeshStandardMaterial({
        color: 0xd4d4d4,
        metalness: 0.95,
        roughness: 0.08,
        envMapIntensity: 1.2
    }),
    black: new THREE.MeshStandardMaterial({
        color: 0x1a1a1a,
        metalness: 0.80,
        roughness: 0.25
    }),
    zinc: new THREE.MeshStandardMaterial({
        color: 0xb8bfc7,
        metalness: 0.85,
        roughness: 0.18
    })
};

let currentHardwareFinish = 'chrome'; // 'chrome' | 'black'

function getHardwareMat() {
    return hardwareMaterials[currentHardwareFinish].clone();
}

// ─── Finish toggle ───────────────────────────────────────────────
document.getElementById('btn-herraje-cromo')?.addEventListener('click', () => {
    currentHardwareFinish = 'chrome';
    document.getElementById('btn-herraje-cromo')?.classList.add('active');
    document.getElementById('btn-herraje-negro')?.classList.remove('active');
    showToast('Acabado: Acero Inoxidable', '#2563eb');
});

document.getElementById('btn-herraje-negro')?.addEventListener('click', () => {
    currentHardwareFinish = 'black';
    document.getElementById('btn-herraje-negro')?.classList.add('active');
    document.getElementById('btn-herraje-cromo')?.classList.remove('active');
    showToast('Acabado: Negro Mate', '#374151');
});

// ─── Constructores procedurales ──────────────────────────────────

/**
 * Bisagra Cazoleta 35mm (vista lateral)
 * Norma: cazoleta Ø35mm, brazo recto, tornillos de montaje.
 * El pivot local es el centro de la cazoleta.
 */
function createCupHinge(finish) {
    const mat = finish ? hardwareMaterials[finish].clone() : getHardwareMat();
    const group = new THREE.Group();
    group.userData.isHardware = true;
    group.userData.hardwareType = 'bisagra';

    // Cazoleta (cilindro aplanado)
    const cupGeo = new THREE.CylinderGeometry(17.5, 17.5, 11, 24);
    const cup = new THREE.Mesh(cupGeo, mat.clone());
    cup.rotation.x = Math.PI / 2;
    cup.position.set(0, 0, 5.5);
    group.add(cup);

    // Tapa exterior de la cazoleta (aro)
    const rimGeo = new THREE.TorusGeometry(17.5, 1.5, 8, 24);
    const rim = new THREE.Mesh(rimGeo, mat.clone());
    rim.rotation.x = Math.PI / 2;
    rim.position.set(0, 0, 11);
    group.add(rim);

    // Brazo de la bisagra
    const armGeo = new THREE.BoxGeometry(32, 6, 4);
    const arm = new THREE.Mesh(armGeo, mat.clone());
    arm.position.set(16, 0, 13);
    group.add(arm);

    // Articulacion (esfera pequena)
    const jointGeo = new THREE.SphereGeometry(4, 12, 8);
    const joint = new THREE.Mesh(jointGeo, mat.clone());
    joint.position.set(0, 0, 13);
    group.add(joint);

    // Placa de montaje
    const plateGeo = new THREE.BoxGeometry(40, 10, 2);
    const plate = new THREE.Mesh(plateGeo, mat.clone());
    plate.position.set(22, 0, 16);
    group.add(plate);

    // Tornillos de la placa (2 cilindros pequenos)
    [-14, 14].forEach(xOff => {
        const screwGeo = new THREE.CylinderGeometry(1.2, 1.2, 5, 8);
        const screw = new THREE.Mesh(screwGeo, hardwareMaterials.zinc.clone());
        screw.rotation.x = Math.PI / 2;
        screw.position.set(22 + xOff, 0, 17.5);
        group.add(screw);
    });

    return group;
}

/**
 * Jalador tubular (barra)  — largo nominal 160 mm
 * Norma: ejes separados 128 mm cc, diametro Ø10 mm, espigo Ø5 mm
 */
function createBarHandle(finish) {
    const mat = finish ? hardwareMaterials[finish].clone() : getHardwareMat();
    const group = new THREE.Group();
    group.userData.isHardware = true;
    group.userData.hardwareType = 'jalador-barra';

    const barLen = 160;
    const barR   = 5;
    const espLen = 22;
    const espR   = 3.5;
    const ccDist = 128;

    // Barra principal horizontal
    const barGeo = new THREE.CylinderGeometry(barR, barR, barLen, 16);
    const bar = new THREE.Mesh(barGeo, mat.clone());
    bar.rotation.z = Math.PI / 2;
    group.add(bar);

    // Dos espigos (separados cc=128mm)
    [-ccDist / 2, ccDist / 2].forEach(xOff => {
        const spigGeo = new THREE.CylinderGeometry(espR, espR, espLen, 12);
        const spig = new THREE.Mesh(spigGeo, mat.clone());
        spig.position.set(xOff, 0, -espLen / 2);
        spig.rotation.x = Math.PI / 2;
        group.add(spig);

        // Base / oreja de fijacion
        const baseGeo = new THREE.CylinderGeometry(8, 8, 4, 16);
        const base = new THREE.Mesh(baseGeo, mat.clone());
        base.position.set(xOff, 0, -espLen);
        base.rotation.x = Math.PI / 2;
        group.add(base);
    });

    return group;
}

/**
 * Jalador Pomo redondo — Ø40 mm, espigo 30 mm
 */
function createKnobHandle(finish) {
    const mat = finish ? hardwareMaterials[finish].clone() : getHardwareMat();
    const group = new THREE.Group();
    group.userData.isHardware = true;
    group.userData.hardwareType = 'jalador-pomo';

    // Esfera principal
    const sphereGeo = new THREE.SphereGeometry(20, 24, 16);
    const sphere = new THREE.Mesh(sphereGeo, mat.clone());
    group.add(sphere);

    // Espigo
    const spigGeo = new THREE.CylinderGeometry(4, 4, 30, 12);
    const spig = new THREE.Mesh(spigGeo, mat.clone());
    spig.position.set(0, 0, -15);
    spig.rotation.x = Math.PI / 2;
    group.add(spig);

    // Base
    const baseGeo = new THREE.CylinderGeometry(9, 9, 4, 16);
    const base = new THREE.Mesh(baseGeo, mat.clone());
    base.position.set(0, 0, -30);
    base.rotation.x = Math.PI / 2;
    group.add(base);

    return group;
}

/**
 * Riel telescopico lateral para cajones
 * Norma: 12.7 mm de espesor/lado, largo = profundidad del cajón (tipico 300-500 mm)
 * Se compone de 3 segmentos telescopicos.
 * @param {number} railLen  longitud en mm (igual que la profundidad del cajón)
 * @param {'left'|'right'} side
 */
function createDrawerSlide(railLen, side, finish) {
    const mat = finish ? hardwareMaterials[finish].clone() : getHardwareMat();
    const group = new THREE.Group();
    group.userData.isHardware = true;
    group.userData.hardwareType = 'riel';

    const th = 12.7; // espesor total riel
    const h  = 45;   // alto del riel (tipico)
    const dir = side === 'right' ? 1 : -1;

    // Perfil exterior (gabinete) — laminado gris
    const outerGeo = new THREE.BoxGeometry(railLen, h, th * 0.45);
    const outerMat = hardwareMaterials.zinc.clone();
    const outer = new THREE.Mesh(outerGeo, outerMat);
    outer.position.set(0, 0, 0);
    group.add(outer);

    // Perfil interior (cajón) — mas corto, dentro
    const innerGeo = new THREE.BoxGeometry(railLen * 0.92, h * 0.75, th * 0.35);
    const inner = new THREE.Mesh(innerGeo, mat.clone());
    inner.position.set(railLen * 0.04, 0, th * 0.1);
    group.add(inner);

    // Bolas de carga (esferas decorativas a lo largo)
    const nBalls = Math.max(3, Math.floor(railLen / 80));
    for (let i = 0; i < nBalls; i++) {
        const bx = -railLen / 2 + (railLen / (nBalls + 1)) * (i + 1);
        const ballGeo = new THREE.SphereGeometry(3.5, 8, 6);
        const ball = new THREE.Mesh(ballGeo, hardwareMaterials.chrome.clone());
        ball.position.set(bx, 0, th * 0.3);
        group.add(ball);
    }

    return group;
}

// ─── Helpers de colocacion ───────────────────────────────────────

/**
 * Limpia los herrajes anteriores de un mesh o grupo
 */
function removeHardwareFrom(target) {
    if (!target) return;
    const toRemove = [];
    target.traverse(ch => {
        if (ch.userData && ch.userData.isHardware) toRemove.push(ch);
    });
    toRemove.forEach(ch => {
        if (ch.parent) ch.parent.remove(ch);
    });
}

/**
 * Coloca bisagras en la cara interior de una pieza (puerta / frontal).
 * Norma: centro del taladro a 21.5 mm del borde lateral,
 *        100 mm desde el borde superior e inferior.
 *        Si la pieza mide mas de 1000 mm de alto, se añade una 3ra bisagra al centro.
 */
function attachHingesToPiece(mesh, finish) {
    if (!mesh || !mesh.isMesh) {
        showToast('Selecciona una pieza plana (puerta o frontal)', '#e74c3c');
        return;
    }
    removeHardwareFrom(mesh);

    const w = mesh.userData.w || 400;
    const h = mesh.userData.h || 700;
    const d = mesh.userData.d || 18;

    const edgeOffset = 21.5;                  // centro cazoleta a borde lateral
    const vOffset    = 100;                   // distancia vertical desde bordes
    const xPos = -w / 2 + edgeOffset;         // borde izquierdo (lado de las bisagras)
    const yPositions = [-h / 2 + vOffset, h / 2 - vOffset];
    if (h > 1000) yPositions.push(0);        // 3ra bisagra en el centro

    yPositions.forEach(yPos => {
        const hinge = createCupHinge(finish || currentHardwareFinish);
        hinge.scale.setScalar(0.9);
        // Montar en la cara posterior de la pieza (z = -d/2) apuntando hacia adentro
        hinge.position.set(xPos, yPos, -d / 2 - 1);
        hinge.rotation.y = 0; // la cazoleta mira hacia adentro del mueble
        mesh.add(hinge);
    });

    showToast(`${yPositions.length} bisagras colocadas (norma 100 mm de bordes)`, '#27ae60');
}

/**
 * Coloca jalador de barra en la cara frontal de la pieza.
 * En puerta: borde libre (derecho), centrado verticalmente, 50 mm del borde.
 * En cajón: centrado en X e Y.
 */
function attachBarHandleToPiece(mesh, finish) {
    if (!mesh || !mesh.isMesh) {
        showToast('Selecciona una pieza primero', '#e74c3c');
        return;
    }
    removeHardwareFrom(mesh);

    const w = mesh.userData.w || 400;
    const h = mesh.userData.h || 700;
    const d = mesh.userData.d || 18;

    const handle = createBarHandle(finish || currentHardwareFinish);
    handle.scale.setScalar(0.95);

    // Si la pieza es alta (puerta): jalador vertical en borde libre derecho
    if (h > 300) {
        handle.rotation.z = Math.PI / 2;  // barra vertical
        handle.position.set(w / 2 - 50, 0, d / 2 + 25);
    } else {
        // Cajón: jalador horizontal centrado
        handle.position.set(0, 0, d / 2 + 25);
    }

    mesh.add(handle);
    showToast('Jalador tubular colocado', '#27ae60');
}

/**
 * Coloca jalador pomo centrado en la pieza.
 */
function attachKnobToPiece(mesh, finish) {
    if (!mesh || !mesh.isMesh) {
        showToast('Selecciona una pieza primero', '#e74c3c');
        return;
    }
    removeHardwareFrom(mesh);

    const d = mesh.userData.d || 18;
    const knob = createKnobHandle(finish || currentHardwareFinish);
    knob.position.set(0, 0, d / 2 + 22);
    mesh.add(knob);
    showToast('Jalador pomo colocado', '#27ae60');
}

/**
 * Coloca rieles telescopicos en los laterales de una pieza (frontal de cajon).
 * Norma: 12.7 mm/lado -> offset total del cajon: 26 mm menos de ancho.
 * Los rieles se colocan en los lados del ancho (X) de la pieza.
 */
function attachSlidesToPiece(mesh, finish) {
    if (!mesh) {
        showToast('Selecciona un cajón o frontal', '#e74c3c');
        return;
    }
    removeHardwareFrom(mesh);

    // Calcular bounding box LOCAL de la geometría real
    const localBox = new THREE.Box3();
    mesh.traverse(child => {
        if (child.isMesh && !child.userData.isHardware) {
            child.geometry.computeBoundingBox();
            const childBox = child.geometry.boundingBox.clone();
            childBox.applyMatrix4(child.matrix);
            localBox.union(childBox);
        }
    });

    if (mesh.isMesh && !mesh.userData.isParametricGroup) {
        mesh.geometry.computeBoundingBox();
        localBox.copy(mesh.geometry.boundingBox);
    }

    const localCenter = new THREE.Vector3();
    localBox.getCenter(localCenter);
    let w = localBox.max.x - localBox.min.x;
    let h = localBox.max.y - localBox.min.y;
    let d = localBox.max.z - localBox.min.z;
    
    // Si la pieza es fina (frontal), el riel va hacia atrás desde la cara interior.
    // Si es profunda (caja entera), el riel va desde la cara frontal hacia atrás.
    let frontFaceZ = d < 50 ? localBox.min.z : localBox.max.z;

    // Overrides si la pieza es un grupo de cajón paramétrico
    if (mesh.userData.drawerBoxWidth) w = mesh.userData.drawerBoxWidth;
    if (mesh.userData.drawerBoxDepth) d = mesh.userData.drawerBoxDepth;
    if (mesh.userData.drawerBoxFrontZ !== undefined) frontFaceZ = mesh.userData.drawerBoxFrontZ;
    // Use the pre-computed drawer center Y (localCenter.y from stale matrices is unreliable)
    if (mesh.userData.drawerInnerCenterY !== undefined) localCenter.y = mesh.userData.drawerInnerCenterY;

    let railLen = d < 50 ? 450 : d;
    railLen = Math.min(Math.max(railLen, 300), 550);
    railLen = Math.round(railLen / 50) * 50;

    // Centrar los rieles verticalmente (al medio del cajón interno)
    const halfH = 0; 

    ['left', 'right'].forEach(side => {
        const rail = createDrawerSlide(railLen, side, finish || currentHardwareFinish);
        const xPos = side === 'right' ? localCenter.x + w / 2 + 6.35 : localCenter.x - w / 2 - 6.35;
        const zPos = frontFaceZ - railLen / 2;
        
        rail.position.set(xPos, localCenter.y, zPos);
        rail.rotation.y = Math.PI / 2;
        mesh.add(rail);
    });

    showToast(`Rieles de ${railLen}mm colocados`, '#27ae60');
}

// ─── Wiring de botones de herrajes ──────────────────────────────
document.getElementById('btn-add-bisagras')?.addEventListener('click', (e) => {
    e?.preventDefault?.();
    const target = selectedObject && selectedObject.isMesh ? selectedObject : null;
    if (!target) { showToast('Selecciona una pieza primero', '#e74c3c'); return; }
    attachHingesToPiece(target, currentHardwareFinish);
});

document.getElementById('btn-add-jalador-barra')?.addEventListener('click', (e) => {
    e?.preventDefault?.();
    const target = selectedObject && selectedObject.isMesh ? selectedObject : null;
    if (!target) { showToast('Selecciona una pieza primero', '#e74c3c'); return; }
    attachBarHandleToPiece(target, currentHardwareFinish);
});

document.getElementById('btn-add-jalador-pomo')?.addEventListener('click', (e) => {
    e?.preventDefault?.();
    const target = selectedObject && selectedObject.isMesh ? selectedObject : null;
    if (!target) { showToast('Selecciona una pieza primero', '#e74c3c'); return; }
    attachKnobToPiece(target, currentHardwareFinish);
});

document.getElementById('btn-add-rieles')?.addEventListener('click', (e) => {
    e?.preventDefault?.();
    const target = selectedObject && selectedObject.isMesh ? selectedObject : null;
    if (!target) { showToast('Selecciona una pieza primero', '#e74c3c'); return; }
    attachSlidesToPiece(target, currentHardwareFinish);
});

document.getElementById('btn-quitar-herrajes')?.addEventListener('click', (e) => {
    e?.preventDefault?.();
    if (!selectedObject) { showToast('Selecciona una pieza primero', '#e74c3c'); return; }
    removeHardwareFrom(selectedObject);
    showToast('Herrajes eliminados de la pieza', '#6b7280');
});

// ─── Plantilla: Gabinete con Puerta y 2 Bisagras + Jalador ─────
window.cargarEjemploGabinetePuertaConBisagras = function() {
    clearAllObjects();
    const esp = 18;
    const W = 600, H = 800, D = 350;
    const finishSave = currentHardwareFinish;

    // Cuerpo del gabinete
    const latL = createPanel(esp, H, D, -W / 2 + esp / 2, H / 2, 0, 'Lateral Izquierdo');
    latL.userData.pieceType = 'vertical';
    const latR = createPanel(esp, H, D, W / 2 - esp / 2, H / 2, 0, 'Lateral Derecho');
    latR.userData.pieceType = 'vertical';
    const base = createPanel(W - esp * 2, esp, D, 0, esp / 2, 0, 'Base');
    base.userData.pieceType = 'horizontal';
    const techo = createPanel(W, esp, D, 0, H - esp / 2, 0, 'Techo');
    techo.userData.pieceType = 'horizontal';
    const fondo = createPanel(W - esp * 2, H - esp * 2, esp, 0, H / 2, -D / 2 + esp / 2, 'Fondo');
    fondo.userData.pieceType = 'vertical';

    // Puerta frontal (18mm de espesor, casi todo el ancho, casi todo el alto)
    const doorW = W - esp * 2 - 4;  // -4mm de huelgo
    const doorH = H - esp * 2 - 4;
    const door = createPanel(doorW, doorH, esp, 0, H / 2, D / 2 + esp / 2 + 2, 'Puerta Frontal');
    door.userData.pieceType = 'vertical';
    applyMaterialToMesh(door, 'roble');

    // Colocar bisagras en la puerta (bisagras lado izquierdo)
    attachHingesToPiece(door, finishSave);

    // Jalador en borde derecho de la puerta
    const barH = createBarHandle(finishSave);
    barH.rotation.z = Math.PI / 2;
    barH.position.set(doorW / 2 - 50, 0, esp / 2 + 25);
    barH.scale.setScalar(0.9);
    door.add(barH);

    // Camara
    camera.position.set(W * 1.4, H * 0.9, W * 1.8);
    controls.target.set(0, H / 2, 0);
    controls.update();
    const inp = document.getElementById('project-name-input');
    if (inp) inp.value = 'Gabinete con Puerta';
    showToast('Gabinete + Puerta + Bisagras cargado', '#27ae60');
};

// ─── Plantilla: Módulo con Cajón y Rieles Telescópicos ─────────
window.cargarEjemploModuloCajonConRieles = function() {
    clearAllObjects();
    const esp = 18;
    const W = 600, H = 350, D = 500;
    const finishSave = currentHardwareFinish;

    // 1. Cuerpo del Gabinete
    const latL = createPanel(esp, H, D, -W / 2 + esp / 2, H / 2, 0, 'Lateral Izquierdo');
    const latR = createPanel(esp, H, D, W / 2 - esp / 2, H / 2, 0, 'Lateral Derecho');
    const base = createPanel(W - esp * 2, esp, D, 0, esp / 2, 0, 'Base');
    const techo = createPanel(W, esp, D, 0, H - esp / 2, 0, 'Techo');
    const fondo = createPanel(W - esp * 2, H - esp * 2, esp, 0, H / 2, -D / 2 + esp / 2, 'Fondo');

    // 2. Piezas del Cajón (Dummy initial sizes, updateDrawerGroup will resize and position them)
    const frente = createPanel(10, 10, esp, 0, 0, 0, 'Frente Decorativo');
    applyMaterialToMesh(frente, 'nogal');

    const cf = createPanel(10, 10, esp, 0, 0, 0, 'Contra Frente');
    const trasera = createPanel(10, 10, esp, 0, 0, 0, 'Trasera Cajón');
    const latCajonL = createPanel(esp, 10, 10, 0, 0, 0, 'Lat Cajón Izq');
    const latCajonR = createPanel(esp, 10, 10, 0, 0, 0, 'Lat Cajón Der');
    
    const fondoCajon = createPanel(10, esp, 10, 0, 0, 0, 'Fondo Cajón');
    applyMaterialToMesh(fondoCajon, 'blanca');

    const knob = createKnobHandle(finishSave);

    // Agrupar todo el cajón
    const cajonGroup = new THREE.Group();
    cajonGroup.userData.isParametricGroup = true;
    cajonGroup.userData.name = 'Cajón Armado';
    
    // Parametric properties
    cajonGroup.userData.w = W;
    cajonGroup.userData.h = H;
    cajonGroup.userData.d = D;
    cajonGroup.userData.esp = esp;
    
    // User requested defaults
    cajonGroup.userData.drawerInnerH = 150; // 15cm
    cajonGroup.userData.drawerFrontType = 'externo'; // Overlay
    cajonGroup.userData.drawerOverlap = 10; // 1cm solape
    cajonGroup.userData.drawerBoxDepth = 450;

    [frente, cf, trasera, latCajonL, latCajonR, fondoCajon].forEach(p => {
        scene.remove(p);
        const idx = objects.indexOf(p);
        if(idx > -1) objects.splice(idx, 1);
        cajonGroup.add(p);
    });
    cajonGroup.add(knob); // Knob wasn't in scene/objects initially
    
    scene.add(cajonGroup);
    objects.push(cajonGroup);

    // 3. Ejecutar redimensionamiento paramétrico (posicionará, escalará y añadirá rieles)
    updateDrawerGroup(cajonGroup, W, H, D, esp);

    // Cámara
    camera.position.set(W * 1.3, H * 1.5, W * 1.6);
    controls.target.set(0, H / 2, 0);
    controls.update();
    const inp = document.getElementById('project-name-input');
    if (inp) inp.value = 'Modulo Cajon con Rieles';
    showToast('Modulo Cajon Parametrico cargado', '#27ae60');
};

// Wiring de los botones de biblioteca (herrajes)
document.getElementById('btn-ejemplo-puerta')?.addEventListener('click', (e) => {
    e?.preventDefault?.();
    window.cargarEjemploGabinetePuertaConBisagras();
});

document.getElementById('btn-ejemplo-cajon')?.addEventListener('click', (e) => {
    e?.preventDefault?.();
    window.cargarEjemploModuloCajonConRieles();
});
// ─── Fin Paso 3 ──────────────────────────────────────────────────

if (inpW) {
    inpW.addEventListener('change', saveDimensionsFromInputs);
    inpW.addEventListener('keydown', (e) => { if(e.key === 'Enter') { e.preventDefault(); saveDimensionsFromInputs(); } });
}
if (inpH) {
    inpH.addEventListener('change', saveDimensionsFromInputs);
    inpH.addEventListener('keydown', (e) => { if(e.key === 'Enter') { e.preventDefault(); saveDimensionsFromInputs(); } });
}
if (inpD) {
    inpD.addEventListener('change', saveDimensionsFromInputs);
    inpD.addEventListener('keydown', (e) => { if(e.key === 'Enter') { e.preventDefault(); saveDimensionsFromInputs(); } });
}

// Keyboard Controls (WASD)
document.addEventListener('keydown', (event) => {
    if (!selectedObject) return;
    
    // Evitar mover piezas si el usuario está escribiendo en el panel
    if (document.activeElement.tagName === 'INPUT') return;
    
    const step = 5; // 5mm por pulsación
    const rotStep = Math.PI / 36; // 5 grados por pulsación
    
    switch(event.key.toLowerCase()) {
        case 'w': selectedObject.position.z -= step; break;
        case 's': selectedObject.position.z += step; break;
        case 'a': selectedObject.position.x -= step; break;
        case 'd': selectedObject.position.x += step; break;
        case 'arrowup': selectedObject.position.y += step; break;
        case 'arrowdown': selectedObject.position.y -= step; break;
        case 'q': selectedObject.rotation.y += rotStep; break;
        case 'e': selectedObject.rotation.y -= rotStep; break;
    }
    
    enforceFloorLimit(selectedObject);
    checkCollisions(selectedObject);
});

// Materials logic
document.querySelectorAll('.material-item').forEach(item => {
    item.addEventListener('click', () => {
        const hex = item.getAttribute('data-color');
        if (materials[hex]) currentMaterial = materials[hex];
        if (selectedObject && !selectedObject.userData.isParametricGroup) {
            selectedObject.material = materials[hex];
        }
    });
});

// ─── Tool Buttons ──────────────────────────────────────────
function setActiveTool(activeId) {
    ['tool-move','tool-rotate','tool-copy'].forEach(id => {
        const el = document.getElementById(id);
        if(el) el.classList.remove('active');
    });
    const active = document.getElementById(activeId);
    if(active) active.classList.add('active');
}

// MOVE - Direct drag mode (no gizmo arrows)
document.getElementById('tool-move')?.addEventListener('click', () => {
    setActiveTool('tool-move');
    currentMode = 'translate';
    transformControl.visible = false;
    transformControl.detach();
});

// ROTATE - 90 deg snap rotate mode
document.getElementById('tool-rotate')?.addEventListener('click', () => {
    setActiveTool('tool-rotate');
    currentMode = 'rotate';
    transformControl.visible = false;
    transformControl.detach();
});

// Floating Rotate Overlay Logic — H (Y-axis) and V (X-axis) with free degrees
function getRotDeg() {
    const deg = parseFloat(document.getElementById('inp-rot-deg')?.value) || 90;
    return deg * (Math.PI / 180); // convert to radians
}

function applyRotation(axis, direction) {
    if (!selectedObject) return;
    selectedObject.rotation[axis] += getRotDeg() * direction;
    selectedObject.updateMatrixWorld();
    enforceFloorLimit(selectedObject);
    checkCollisions(selectedObject);
    if (selectedObject.userData.boxHelper) selectedObject.userData.boxHelper.update();
}

// Horizontal (Y axis): ↺ left, ↻ right
document.getElementById('btn-rot-hy')?.addEventListener('click', () => applyRotation('y', -1));
document.getElementById('btn-rot-hy-r')?.addEventListener('click', () => applyRotation('y', 1));

// Vertical (X axis): ↑ up, ↓ down
document.getElementById('btn-rot-vx')?.addEventListener('click', () => applyRotation('x', -1));
document.getElementById('btn-rot-vx-r')?.addEventListener('click', () => applyRotation('x', 1));


// ⎘ COPY — Duplicate selected object
document.getElementById('tool-copy')?.addEventListener('click', () => {
    if (!selectedObject) return;
    
    if (selectedObject.userData.isParametricGroup) {
        // Clone group: create a new group with same dimensions
        const g = selectedObject;
        const esp = g.userData.esp;
        const w = g.userData.w;
        const h = g.userData.h;
        const d = g.userData.d;
        const newGroup = new THREE.Group();
        newGroup.userData = { isParametricGroup: true, w, h, d, esp };
        
        g.children.forEach(child => {
            if (child.type !== 'Mesh') return;
            const clone = child.clone();
            clone.geometry = child.geometry.clone();
            clone.geometry.computeBoundingBox();
            clone.geometry.userData.obb = new THREE.OBB().fromBox3(clone.geometry.boundingBox);
            clone.userData = { ...child.userData, obb: new THREE.OBB() };
            clone.material = child.material.clone();
            newGroup.add(clone);
        });
        newGroup.position.copy(selectedObject.position);
        newGroup.position.x += w + 20;
        scene.add(newGroup);
        objects.push(newGroup);
        selectObject(newGroup);
    } else {
        // Clone single panel
        const clone = createPanel(
            selectedObject.userData.w,
            selectedObject.userData.h,
            selectedObject.userData.d,
            selectedObject.position.x + (selectedObject.userData.w || 50) + 10,
            selectedObject.position.y,
            selectedObject.position.z,
            (selectedObject.userData.name || 'Panel') + ' Copia'
        );
        clone.userData.pieceType = selectedObject.userData.pieceType;
        clone.rotation.copy(selectedObject.rotation);
    }
});

// ─── View Buttons ───────────────────────────────────────────
function setActiveView(activeId) {
    ['view-3d','view-x','view-y','view-z'].forEach(id => {
        const el = document.getElementById(id);
        if(el) el.classList.remove('active');
    });
    const el = document.getElementById(activeId);
    if(el) el.classList.add('active');
}

document.getElementById('view-3d')?.addEventListener('click', () => {
    setActiveView('view-3d');
    camera.position.set(800, 1000, 1500);
    controls.target.set(0, 400, 0);
    controls.update();
});

document.getElementById('view-x')?.addEventListener('click', () => {
    setActiveView('view-x');
    camera.position.set(0, 400, 2000); // Frontal
    controls.target.set(0, 400, 0);
    controls.update();
});

document.getElementById('view-y')?.addEventListener('click', () => {
    setActiveView('view-y');
    camera.position.set(0, 3000, 0.1); // Top down
    controls.target.set(0, 0, 0);
    controls.update();
});

document.getElementById('view-z')?.addEventListener('click', () => {
    setActiveView('view-z');
    camera.position.set(2000, 400, 0); // Lateral
    controls.target.set(0, 400, 0);
    controls.update();
});

// ═══════════════════════════════════════════════════════════
// SISTEMA DE MEDIDAS — Rediseño completo
// • 1 etiqueta de dimensiones DENTRO de la pieza seleccionada
// • 1 etiqueta de distancia por pieza vecina (en el gap)
// • Anti-solapamiento: descarta labels demasiado cercanos
// ═══════════════════════════════════════════════════════════
const dimContainer = document.querySelector('.canvas-container') || document.body;

// --- Etiqueta de dimensiones de la pieza seleccionada ---
const selDimLabel = document.createElement('div');
selDimLabel.style.cssText = `
    position: absolute;
    pointer-events: none;
    display: none;
    z-index: 22;
    font-family: Inter, system-ui, sans-serif;
    font-size: 11px;
    font-weight: 700;
    color: #fff;
    background: rgba(15, 23, 42, 0.82);
    padding: 4px 10px;
    border-radius: 8px;
    border: 1px solid rgba(255,255,255,0.18);
    box-shadow: 0 2px 8px rgba(0,0,0,0.45);
    transform: translate(-50%, -50%);
    white-space: nowrap;
    letter-spacing: 0.03em;
    backdrop-filter: blur(4px);
`;
dimContainer.appendChild(selDimLabel);

// --- Pool de etiquetas de distancia (max 5) ---
const PROX_THRESHOLD = 400;
const PROX_MIN = 15;           // gaps menores a 15mm no se muestran
const PROX_OVERLAP_PX = 45;    // distancia minima en px entre labels
const proximityPool = [];
for (let i = 0; i < 5; i++) {
    const div = document.createElement('div');
    div.style.cssText = `
        position: absolute;
        font-family: Inter, system-ui, sans-serif;
        font-size: 10px;
        font-weight: 700;
        pointer-events: none;
        display: none;
        transform: translate(-50%, -50%);
        z-index: 21;
        padding: 2px 7px;
        border-radius: 99px;
        background: rgba(234, 179, 8, 0.90);
        color: #1c1917;
        white-space: nowrap;
        box-shadow: 0 1px 4px rgba(0,0,0,0.35);
        letter-spacing: 0.02em;
    `;
    dimContainer.appendChild(div);
    proximityPool.push(div);
}

// Lineas de dimensiones en la escena (solo borde de la seleccion)
let dimLinesGroup = new THREE.Group();
scene.add(dimLinesGroup);

// Proyecta posicion 3D -> coordenadas relativas al canvas-container
function screenPos(worldPos) {
    const rect = dimContainer.getBoundingClientRect();
    const v = worldPos.clone().project(camera);
    if (v.z > 1) return null;
    const x = (v.x * 0.5 + 0.5) * rect.width;
    const y = (v.y * -0.5 + 0.5) * rect.height;
    if (x < 0 || x > rect.width || y < 0 || y > rect.height) return null;
    return { x, y };
}

// Comprueba si un punto (px,py) se solapa con alguno de los ya colocados
function overlapsPlaced(px, py, placed) {
    for (const p of placed) {
        const dx = px - p.x, dy = py - p.y;
        if (Math.sqrt(dx * dx + dy * dy) < PROX_OVERLAP_PX) return true;
    }
    return false;
}

// Actualiza la etiqueta de dimensiones de la pieza seleccionada
function updateDimensionVisuals() {
    // Limpiar lineas de escena
    while (dimLinesGroup.children.length > 0) dimLinesGroup.remove(dimLinesGroup.children[0]);

    if (!selectedObject) {
        selDimLabel.style.display = 'none';
        return;
    }

    const box = new THREE.Box3().setFromObject(selectedObject);
    const center = new THREE.Vector3();
    box.getCenter(center);

    const factor = unitFactors ? unitFactors[currentUnit] : 1;
    const unit = currentUnit || 'mm';
    const size = new THREE.Vector3();
    box.getSize(size);

    // Redondear y formatear
    const fmt = (v) => +(v / factor).toFixed(1);
    const wVal = fmt(size.x), hVal = fmt(size.y), dVal = fmt(size.z);

    // Proyectar CENTRO de la pieza en pantalla
    const sp = screenPos(center);
    if (!sp) { selDimLabel.style.display = 'none'; return; }

    selDimLabel.style.display = 'block';
    selDimLabel.style.left = `${sp.x}px`;
    selDimLabel.style.top = `${sp.y}px`;
    selDimLabel.innerHTML =
        `<span style="color:#fbbf24">${wVal}</span>` +
        `<span style="color:#94a3b8"> × </span>` +
        `<span style="color:#60a5fa">${hVal}</span>` +
        `<span style="color:#94a3b8"> × </span>` +
        `<span style="color:#34d399">${dVal}</span>` +
        `<span style="color:#94a3b8;font-weight:400"> ${unit}</span>`;
}

// Actualiza etiquetas de distancia a piezas vecinas
function updateProximityLabels() {
    proximityPool.forEach(d => d.style.display = 'none');
    if (!selectedObject) return;

    const factor = unitFactors ? unitFactors[currentUnit] : 1;
    const unit = currentUnit || 'mm';

    selectedObject.updateMatrixWorld();
    const boxM = new THREE.Box3().setFromObject(selectedObject);
    const centerM = new THREE.Vector3();
    boxM.getCenter(centerM);

    // Coordenadas ya colocadas (para anti-solapamiento)
    // Iniciar con la posicion de la etiqueta de dimensiones de la pieza
    const placed = [];
    const spSel = screenPos(centerM);
    if (spSel) placed.push(spSel);

    let labelIdx = 0;

    // Para cada pieza vecina encontrar el GAP MINIMO positivo entre sus caras
    for (const obj of objects) {
        if (obj === selectedObject || labelIdx >= proximityPool.length) continue;
        if (obj.userData && (obj.userData.isHardware)) continue;

        obj.updateMatrixWorld();
        const boxE = new THREE.Box3().setFromObject(obj);
        const centerE = new THREE.Vector3();
        boxE.getCenter(centerE);

        // Los 6 posibles gaps cara a cara
        const candidates = [
            { gap: boxE.min.x - boxM.max.x, mid: new THREE.Vector3((boxM.max.x + boxE.min.x) / 2, (centerM.y + centerE.y) / 2, (centerM.z + centerE.z) / 2), axis: 'X' },
            { gap: boxM.min.x - boxE.max.x, mid: new THREE.Vector3((boxE.max.x + boxM.min.x) / 2, (centerM.y + centerE.y) / 2, (centerM.z + centerE.z) / 2), axis: 'X' },
            { gap: boxE.min.z - boxM.max.z, mid: new THREE.Vector3((centerM.x + centerE.x) / 2, (centerM.y + centerE.y) / 2, (boxM.max.z + boxE.min.z) / 2), axis: 'Z' },
            { gap: boxM.min.z - boxE.max.z, mid: new THREE.Vector3((centerM.x + centerE.x) / 2, (centerM.y + centerE.y) / 2, (boxE.max.z + boxM.min.z) / 2), axis: 'Z' },
            { gap: boxE.min.y - boxM.max.y, mid: new THREE.Vector3((centerM.x + centerE.x) / 2, (boxM.max.y + boxE.min.y) / 2, (centerM.z + centerE.z) / 2), axis: 'Y' },
            { gap: boxM.min.y - boxE.max.y, mid: new THREE.Vector3((centerM.x + centerE.x) / 2, (boxE.max.y + boxM.min.y) / 2, (centerM.z + centerE.z) / 2), axis: 'Y' },
        ];

        // Filtrar solo gaps validos (positivos y dentro del umbral)
        const valid = candidates
            .filter(c => c.gap >= PROX_MIN && c.gap <= PROX_THRESHOLD)
            .sort((a, b) => a.gap - b.gap);

        // Solo 1 label por vecino (el gap mas pequeno)
        if (valid.length === 0) continue;
        const best = valid[0];

        const sp = screenPos(best.mid);
        if (!sp) continue;

        // Anti-solapamiento
        if (overlapsPlaced(sp.x, sp.y, placed)) continue;

        const div = proximityPool[labelIdx++];
        const dist = +(best.gap / factor).toFixed(1);
        const arrow = best.axis === 'Y' ? '\u2195' : '\u2194';
        div.style.display = 'block';
        div.style.left = `${sp.x}px`;
        div.style.top = `${sp.y}px`;
        div.innerHTML = `${arrow} ${dist} ${unit}`;
        placed.push(sp);
    }
}

function updateRotateOverlay() {
    const rotateOverlay = document.getElementById('rotate-overlay');
    if (!rotateOverlay) return;
    if (currentMode === 'rotate' && selectedObject) {
        const box = new THREE.Box3().setFromObject(selectedObject);
        const center = new THREE.Vector3();
        box.getCenter(center);
        center.y = box.max.y + 40;
        const vector = center.clone().project(camera);
        if (vector.z > 1) { rotateOverlay.style.display = 'none'; return; }
        const rect = dimContainer.getBoundingClientRect();
        const x = (vector.x * 0.5 + 0.5) * rect.width;
        const y = (vector.y * -0.5 + 0.5) * rect.height;
        rotateOverlay.style.display = 'flex';
        rotateOverlay.style.left = `${x}px`;
        rotateOverlay.style.top = `${y}px`;
    } else {
        rotateOverlay.style.display = 'none';
    }
}
// --- UI Interactivity ---
const menuToggle = document.getElementById('menu-toggle');
const sideMenu = document.getElementById('side-menu');

if (menuToggle && sideMenu) {
    menuToggle.addEventListener('click', (e) => {
        sideMenu.classList.toggle('open');
        menuToggle.classList.toggle('active');
        e.stopPropagation();
    });
    document.addEventListener('click', (e) => {
        if (sideMenu.classList.contains('open') && !sideMenu.contains(e.target) && !menuToggle.contains(e.target)) {
            sideMenu.classList.remove('open');
            menuToggle.classList.remove('active');
        }
    });
}

const btnOpenCorte = document.getElementById('btn-open-corte');
const corteOverlay = document.getElementById('corte-overlay');
const corteClose = document.getElementById('corte-close');

// ─── MOTOR DE PLANOS DE CORTE ──────────────────────────────

/**
 * Extrae todas las piezas del escenario 3D.
 * Para grupos paramétricos, saca los children individuales.
 * Devuelve array de { name, w, h } (las dos dimensiones planas: ancho y alto de corte)
 */
function extractPiecesFromScene() {
    const pieces = [];
    function addPiece(obj) {
        if (!obj.userData) return;
        const w = obj.userData.w;
        const h = obj.userData.h;
        const d = obj.userData.d;
        if (!w || !h || !d) return;
        const name = obj.userData.name || obj.userData.role || 'Pieza';
        const dims = [w, h, d].sort((a, b) => b - a);
        const textureKey = obj.userData.textureKey || 'roble';
        const materialName = textureCatalog[textureKey]?.name || 'Melamina Roble';
        pieces.push({ 
            name, 
            pw: dims[0], 
            ph: dims[1], 
            thickness: dims[2],
            material: materialName,
            textureKey: textureKey
        });
    }

    objects.forEach(obj => {
        if (obj.userData && (obj.userData.isParametricGroup || obj.userData.isAssemblyGroup)) {
            obj.children.forEach(child => addPiece(child));
        } else {
            addPiece(obj);
        }
    });
    return pieces;
}

/**
 * Algoritmo de bin-packing tipo FFDH (First Fit Decreasing Height).
 * Coloca las piezas en filas dentro del panel, con kerf entre ellas.
 * Devuelve array de sheets, cada sheet es un array de { name, pw, ph, x, y }
 */
function binPackPieces(pieces, panelW, panelH, kerf) {
    // Ordenar de mayor a menor (por altura de pieza)
    const sorted = [...pieces].sort((a, b) => (b.ph - a.ph) || (b.pw - a.pw));

    const sheets = [];

    function newSheet() {
        return { rows: [], placedPieces: [], usedArea: 0 };
    }

    let currentSheet = newSheet();
    sheets.push(currentSheet);

    let cursorY = kerf;   // posición Y actual
    let cursorX = kerf;   // posición X en fila actual
    let rowHeight = 0;    // altura de la fila actual

    for (const piece of sorted) {
        const pw = piece.pw;
        const ph = piece.ph;

        // Si la pieza no cabe en el panel, la skipamos
        if (pw > panelW - 2 * kerf || ph > panelH - 2 * kerf) {
            continue; // pieza demasiado grande para este panel
        }

        // ¿Cabe en la fila actual?
        if (cursorX + pw + kerf > panelW) {
            // Nueva fila
            cursorY += rowHeight + kerf;
            cursorX = kerf;
            rowHeight = 0;
        }

        // ¿Cabe en el sheet actual verticalmente?
        if (cursorY + ph + kerf > panelH) {
            // Nuevo sheet
            currentSheet = newSheet();
            sheets.push(currentSheet);
            cursorY = kerf;
            cursorX = kerf;
            rowHeight = 0;
        }

        currentSheet.placedPieces.push({
            name: piece.name,
            pw, ph,
            x: cursorX,
            y: cursorY,
        });
        currentSheet.usedArea += pw * ph;

        cursorX += pw + kerf;
        rowHeight = Math.max(rowHeight, ph);
    }

    return sheets;
}

/**
 * Renderiza los tableros en el contenedor del modal.
 */
function renderCorteSheets() {
    const panelW = parseFloat(document.getElementById('panel-w')?.value) || 2440;
    const panelH = parseFloat(document.getElementById('panel-h')?.value) || 1220;
    const kerf   = parseFloat(document.getElementById('sierra-mm')?.value) || 3;

    const pieces = extractPiecesFromScene();
    const container = document.getElementById('corte-sheets-container');
    const emptyMsg  = document.getElementById('corte-empty-msg');

    // Limpiar sheets anteriores (menos el empty msg)
    Array.from(container.children).forEach(c => {
        if (c.id !== 'corte-empty-msg') container.removeChild(c);
    });

    if (pieces.length === 0) {
        if (emptyMsg) emptyMsg.style.display = 'block';
        const sp = document.getElementById('stat-piezas'); if (sp) sp.textContent = '0 piezas';
        const st = document.getElementById('stat-tableros'); if (st) st.textContent = '0 tableros';
        const su = document.getElementById('stat-uso'); if (su) su.textContent = '—% uso';
        const ss = document.getElementById('stat-sierra'); if (ss) ss.textContent = `Sierra: ${kerf} mm`;
        const sc = document.getElementById('corte-footer-info'); if (sc) sc.textContent = 'Materiales — 0 uds.';
        return;
    }
    if (emptyMsg) emptyMsg.style.display = 'none';

    const sheets = binPackPieces(pieces, panelW, panelH, kerf);
    const panelArea = panelW * panelH;

    let totalUsedArea = 0;
    sheets.forEach(s => { totalUsedArea += s.usedArea; });
    const pctUso = ((totalUsedArea / (panelArea * sheets.length)) * 100).toFixed(1);

    let globalPieceIndex = 1;

    sheets.forEach((sheet, idx) => {
        const wrap = document.createElement('div');
        wrap.className = 'corte-sheet-wrap';

        // Etiqueta del tablero
        const label = document.createElement('div');
        label.className = 'corte-sheet-label';
        const sheetUsePct = ((sheet.usedArea / panelArea) * 100).toFixed(1);
        label.innerHTML = `
            <span>Tablero ${idx + 1} &nbsp;·&nbsp; ${panelW} × ${panelH} mm</span>
            <span>${sheetUsePct}% utilizado &nbsp;·&nbsp; ${sheet.placedPieces.length} piezas</span>
        `;
        wrap.appendChild(label);

        // Board
        const board = document.createElement('div');
        board.className = 'corte-board';
        board.style.aspectRatio = `${panelW} / ${panelH}`;

        // Zona de desperdicio (fondo completo)
        const waste = document.createElement('div');
        waste.className = 'corte-waste';
        waste.style.cssText = `left:0;top:0;width:100%;height:100%;`;
        board.appendChild(waste);

        // Piezas
        sheet.placedPieces.forEach((p, i) => {
            const el = document.createElement('div');
            el.className = 'corte-part';
            const left   = (p.x / panelW * 100).toFixed(2);
            const top    = (p.y / panelH * 100).toFixed(2);
            const width  = (p.pw / panelW * 100).toFixed(2);
            const height = (p.ph / panelH * 100).toFixed(2);
            el.style.cssText = `left:${left}%;top:${top}%;width:${width}%;height:${height}%;`;
            el.innerHTML = `
                <span class="part-name" style="font-size:13px; font-weight:600;">${globalPieceIndex}</span>
            `;
            el.title = `${globalPieceIndex}: ${p.name} (${p.pw} × ${p.ph} mm)`;
            board.appendChild(el);
            globalPieceIndex++;
        });

        wrap.appendChild(board);
        container.appendChild(wrap);
    });

    // Actualizar estadísticas con comprobación de nulabilidad
    const sp = document.getElementById('stat-piezas'); if (sp) sp.textContent = `${pieces.length} piezas`;
    const st = document.getElementById('stat-tableros'); if (st) st.textContent = `${sheets.length} tablero${sheets.length !== 1 ? 's' : ''}`;
    const su = document.getElementById('stat-uso'); if (su) su.textContent = `${pctUso}% uso`;
    const ss = document.getElementById('stat-sierra'); if (ss) ss.textContent = `Sierra: ${kerf} mm`;
    const sc = document.getElementById('corte-footer-info'); if (sc) sc.textContent = `Materiales — ${sheets.length} ud${sheets.length !== 1 ? 's' : ''}.`;
}

// Exportar globalmente para integración con la vista
window.renderCorteSheets = renderCorteSheets;
window.extractPiecesFromScene = extractPiecesFromScene;

// ─── ABRIR / CERRAR MODAL ─────────────────────────────────
if (btnOpenCorte) {
    btnOpenCorte.addEventListener('click', (e) => {
        e?.preventDefault?.();
        if (typeof window.openCorteModal === 'function') {
            window.openCorteModal();
        } else {
            const modal = document.getElementById('corte-modal');
            const overlay = document.getElementById('shared-overlay') || corteOverlay;
            if (modal) modal.classList.add('show');
            if (overlay) overlay.classList.add('show');
            renderCorteSheets();
        }
    });
}
if (corteClose) {
    corteClose.addEventListener('click', (e) => {
        e?.preventDefault?.();
        if (typeof window.closeCorteModal === 'function') {
            window.closeCorteModal();
        } else {
            const modal = document.getElementById('corte-modal');
            const overlay = document.getElementById('shared-overlay') || corteOverlay;
            if (modal) modal.classList.remove('show');
            if (overlay) overlay.classList.remove('show');
        }
    });
}
if (corteOverlay) {
    corteOverlay.addEventListener('click', (e) => {
        if (e.target === corteOverlay) {
            corteOverlay.style.display = 'none';
            corteOverlay.classList.remove('show');
        }
    });
}

// Toggle Configuración de Corte Settings
const btnToggleCorteSettings = document.getElementById('btn-toggle-corte-settings');
const corteControlsRow = document.getElementById('corte-controls-row');
const corteSettingsIcon = document.getElementById('corte-settings-icon');

if (btnToggleCorteSettings && corteControlsRow) {
    btnToggleCorteSettings.addEventListener('click', () => {
        if (corteControlsRow.style.display === 'none') {
            corteControlsRow.style.display = 'flex';
            corteSettingsIcon.textContent = '▲';
            btnToggleCorteSettings.style.background = 'rgba(201,113,50,0.3)';
        } else {
            corteControlsRow.style.display = 'none';
            corteSettingsIcon.textContent = '▼';
            btnToggleCorteSettings.style.background = 'rgba(201,113,50,0.15)';
        }
    });
}

// Recalcular al pulsar el botón
document.getElementById('btn-recalcular-corte')?.addEventListener('click', () => {
    renderCorteSheets();
    showToast('Planos recalculados', '#C97132');
});

// Imprimir Hoja de Taller desde el modal de corte
document.getElementById('btn-imprimir-taller')?.addEventListener('click', () => {
    if (typeof window.imprimirHojaTaller === 'function') {
        window.imprimirHojaTaller();
    }
});

// Descargar planos desde el modal de corte
document.getElementById('btn-descargar-planos')?.addEventListener('click', () => {
    const panelW = parseFloat(document.getElementById('panel-w')?.value) || 2440;
    const panelH = parseFloat(document.getElementById('panel-h')?.value) || 1220;
    const kerf   = parseFloat(document.getElementById('sierra-mm')?.value) || 3;
    const projectName = document.getElementById('project-name-input')?.value?.trim() || 'Mydo Mar';

    const pieces = extractPiecesFromScene();
    const sheets = binPackPieces(pieces, panelW, panelH, kerf);

    const data = {
        project: projectName,
        exportedAt: new Date().toISOString(),
        panel: { width_mm: panelW, height_mm: panelH },
        sierra_mm: kerf,
        sheets: sheets.map((s, i) => ({
            sheet: i + 1,
            pieces: s.placedPieces.map(p => ({
                name: p.name,
                width_mm: p.pw,
                height_mm: p.ph,
                x_mm: p.x,
                y_mm: p.y
            }))
        }))
    };

    const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
    const url  = URL.createObjectURL(blob);
    const a    = document.createElement('a');
    a.href = url;
    a.download = `${projectName.replace(/\s+/g, '_')}_corte.json`;
    a.click();
    URL.revokeObjectURL(url);
    showToast(`Planos descargados`, '#3498db');
});

// ─── TOAST NOTIFICATION ────────────────────────────────────
function showToast(msg, color = '#C97132') {
    let toast = document.getElementById('mydo-toast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'mydo-toast';
        toast.style.cssText = `
            position:fixed; bottom:30px; right:30px; z-index:9999;
            padding:12px 22px; border-radius:8px; color:white;
            font-size:14px; font-weight:600; opacity:0;
            transition: opacity 0.3s, transform 0.3s;
            transform: translateY(10px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.4);
        `;
        document.body.appendChild(toast);
    }
    toast.textContent = msg;
    toast.style.background = color;
    toast.style.opacity = '1';
    toast.style.transform = 'translateY(0)';
    clearTimeout(toast._t);
    toast._t = setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(10px)';
    }, 2500);
}

// ─── UNDO / REDO HISTORY ───────────────────────────────────
const historyStack = [];  // Each entry: snapshot of all object transforms
let historyIndex = -1;
const MAX_HISTORY = 40;

function captureSnapshot() {
    return objects.map(obj => ({
        uuid: obj.uuid,
        px: obj.position.x, py: obj.position.y, pz: obj.position.z,
        rx: obj.rotation.x, ry: obj.rotation.y, rz: obj.rotation.z,
    }));
}

function pushHistory() {
    // Remove any redo steps ahead
    if (historyIndex < historyStack.length - 1) {
        historyStack.splice(historyIndex + 1);
    }
    historyStack.push(captureSnapshot());
    if (historyStack.length > MAX_HISTORY) historyStack.shift();
    historyIndex = historyStack.length - 1;
}

function applySnapshot(snapshot) {
    snapshot.forEach(s => {
        const obj = objects.find(o => o.uuid === s.uuid);
        if (!obj) return;
        obj.position.set(s.px, s.py, s.pz);
        obj.rotation.set(s.rx, s.ry, s.rz);
        if (obj.userData.boxHelper) obj.userData.boxHelper.update();
    });
}

// Push initial empty state
pushHistory();

// Hook into pointer-up so every move push to history
container.addEventListener('pointerup', () => {
    if (isDraggingPiece || isDragging) pushHistory();
}, true);

const btnUndo = document.getElementById('btn-undo');
const btnRedo = document.getElementById('btn-redo');

if (btnUndo) {
    btnUndo.addEventListener('click', () => {
        if (historyIndex <= 0) { showToast('Nada que deshacer', '#666'); return; }
        historyIndex--;
        applySnapshot(historyStack[historyIndex]);
        showToast('Deshacer', '#C97132');
    });
}
if (btnRedo) {
    btnRedo.addEventListener('click', () => {
        if (historyIndex >= historyStack.length - 1) { showToast('Nada que rehacer', '#666'); return; }
        historyIndex++;
        applySnapshot(historyStack[historyIndex]);
        showToast('Rehacer', '#5aabdb');
    });
}

// ─── GUARDAR EN LA NUBE (simulated) ────────────────────────
const btnCloud = document.getElementById('btn-save-cloud');
if (btnCloud) {
    btnCloud.addEventListener('click', () => {
        const projectName = document.getElementById('project-name-input')?.value || 'Mydo Mar';
        const data = {
            project: projectName,
            savedAt: new Date().toISOString(),
            objects: objects.map(o => ({
                name: o.userData.name || o.uuid,
                position: o.position.toArray(),
                rotation: [o.rotation.x, o.rotation.y, o.rotation.z],
                w: o.userData.w, h: o.userData.h, d: o.userData.d
            }))
        };
        localStorage.setItem('mydomar_project', JSON.stringify(data));
        showToast('Guardado en la nube', '#C97132');
    });
}


// Render loop
function animate() {
    requestAnimationFrame(animate);
    controls.update();
    updateDimensionVisuals();
    updateRotateOverlay();
    updateProximityLabels();
    renderer.render(scene, camera);
}
animate();

// ─── EXPORTACIONES DE AYUDA (BASE DE DATOS Y PLANOS) ───────
window.getSceneBoundingDimensions = function() {
    if (objects.length === 0) return "0 x 0 x 0 mm";
    const box = new THREE.Box3();
    objects.forEach(obj => box.expandByObject(obj));
    const size = new THREE.Vector3();
    box.getSize(size);
    return `${Math.round(size.x)} × ${Math.round(size.z)} × ${Math.round(size.y)} mm`;
};

window.getScene3DScreenshot = function() {
    renderer.render(scene, camera);
    return renderer.domElement.toDataURL('image/jpeg', 0.85);
};

window.getExportData = function() {
    const projectName = document.getElementById('project-name-input')?.value?.trim() || 'Proyecto 3D';
    const pieces = extractPiecesFromScene();
    
    // Extraer todos los objetos de forma plana con sus posiciones y rotaciones absolutas en el mundo
    const flatObjects = [];
    objects.forEach(o => {
        if (o.userData && (o.userData.isAssemblyGroup || o.userData.isParametricGroup)) {
            o.updateMatrixWorld(true);
            o.children.forEach(ch => {
                if (ch.type !== 'Mesh') return;
                const worldPos = new THREE.Vector3();
                const worldQuat = new THREE.Quaternion();
                const worldEuler = new THREE.Euler();
                ch.getWorldPosition(worldPos);
                ch.getWorldQuaternion(worldQuat);
                worldEuler.setFromQuaternion(worldQuat);
                flatObjects.push({
                    name: ch.userData.name || 'Pieza',
                    w: ch.userData.w,
                    h: ch.userData.h,
                    d: ch.userData.d,
                    pos: [Math.round(worldPos.x), Math.round(worldPos.y), Math.round(worldPos.z)],
                    rot: [+(worldEuler.x).toFixed(2), +(worldEuler.y).toFixed(2), +(worldEuler.z).toFixed(2)],
                    pieceType: ch.userData.pieceType || 'vertical',
                    textureKey: ch.userData.textureKey || 'roble'
                });
            });
        } else if (o.type === 'Mesh') {
            flatObjects.push({
                name: o.userData.name || 'Pieza',
                w: o.userData.w,
                h: o.userData.h,
                d: o.userData.d,
                pos: [Math.round(o.position.x), Math.round(o.position.y), Math.round(o.position.z)],
                rot: [+(o.rotation.x).toFixed(2), +(o.rotation.y).toFixed(2), +(o.rotation.z).toFixed(2)],
                pieceType: o.userData.pieceType || 'vertical',
                textureKey: o.userData.textureKey || 'roble'
            });
        }
    });

    return {
        tipo: '3d_design',
        nombre: projectName,
        dimensiones: window.getSceneBoundingDimensions(),
        total_piezas: pieces.length,
        piezas: pieces,
        objetos: flatObjects
    };
};

window.imprimirHojaTaller = function() {
    const projectName = document.getElementById('project-name-input')?.value?.trim() || 'Mydo Mar';
    const pieces = extractPiecesFromScene();
    const dims = window.getSceneBoundingDimensions();
    const panelW = parseFloat(document.getElementById('panel-w')?.value) || 2440;
    const panelH = parseFloat(document.getElementById('panel-h')?.value) || 1220;
    const kerf   = parseFloat(document.getElementById('sierra-mm')?.value) || 3;
    const sheets = binPackPieces(pieces, panelW, panelH, kerf);
    const screen = window.getScene3DScreenshot();

    const win = window.open('', '_blank');
    if (!win) {
        alert("Por favor habilita las ventanas emergentes (popups) para imprimir el plano.");
        return;
    }
    win.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Plano Técnico - ${projectName}</title>
            <style>
                body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; padding: 25px; color: #1f2937; max-width: 900px; margin: auto; }
                h1 { color: #d97706; margin-bottom: 4px; font-size: 26px; }
                .meta { display: flex; justify-content: space-between; border-bottom: 2px solid #e5e7eb; padding-bottom: 12px; margin-bottom: 20px; font-size: 14px; }
                .meta div { line-height: 1.6; }
                table { width: 100%; border-collapse: collapse; margin-bottom: 25px; font-size: 13px; }
                th, td { border: 1px solid #e5e7eb; padding: 8px 12px; text-align: left; }
                th { background: #f9fafb; font-weight: 700; color: #374151; }
                .preview-box { text-align: center; margin-bottom: 25px; }
                .preview-box img { max-width: 440px; border-radius: 8px; border: 1px solid #e5e7eb; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
                @media print { .no-print { display: none; } body { padding: 0; } }
            </style>
        </head>
        <body>
            <div class="no-print" style="margin-bottom: 20px; display: flex; justify-content: flex-end; gap: 10px;">
                <button onclick="window.print()" style="background:#d97706; color:#fff; border:none; padding:10px 24px; border-radius:8px; font-weight:bold; cursor:pointer; font-size:14px;">Imprimir Hoja de Taller (PDF)</button>
            </div>
            <h1>Mydo-mar · Ficha Técnica y Planos de Taller</h1>
            <div class="meta">
                <div>
                    <strong>Proyecto:</strong> ${projectName}<br>
                    <strong>Dimensiones Exteriores:</strong> ${dims}<br>
                    <strong>Material Sugerido:</strong> Melamina 18mm
                </div>
                <div style="text-align: right;">
                    <strong>Fecha:</strong> ${new Date().toLocaleDateString()}<br>
                    <strong>Total Piezas:</strong> ${pieces.length}<br>
                    <strong>Tableros Requeridos:</strong> ${sheets.length} (Panel ${panelW}×${panelH} mm)
                </div>
            </div>

            <div class="preview-box">
                <img src="${screen}" alt="Vista 3D">
            </div>

            <h3>Lista de Despiece para Corte</h3>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nombre de la Pieza</th>
                        <th>Largo (mm)</th>
                        <th>Ancho (mm)</th>
                        <th>Espesor</th>
                        <th>Cant.</th>
                    </tr>
                </thead>
                <tbody>
                    ${pieces.map((p, i) => `
                        <tr>
                            <td>${i + 1}</td>
                            <td><strong>${p.name}</strong></td>
                            <td>${Math.round(p.pw)} mm</td>
                            <td>${Math.round(p.ph)} mm</td>
                            <td>${p.thickness || 18} mm</td>
                            <td>1</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>

            <div style="margin-top: 30px; padding: 15px; background: #f9fafb; border-radius: 8px; border: 1px solid #e5e7eb; font-size: 12px; color: #6b7280;">
                <strong>Nota de Taller:</strong> Medidas calculadas con sierra de corte de ${kerf} mm. Verificar escuadra y sentido de veta antes de cortar.
            </div>
        </body>
        </html>
    `);
    win.document.close();
};
