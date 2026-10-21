# Guía Maestra de Creación y Arquitectura de Muebles 3D - Mydo-Mar

Guía técnica integral para desarrolladores, inteligencias artificiales y maestros carpinteros. Explica la arquitectura tecnológica, el ciclo de vida de los muebles, la lógica matemática de modelado en Three.js, la gestión métrica de texturas, la comunicación/agrupación espacial de piezas, la persistencia en base de datos PostgreSQL y la hidratación/reconstrucción gráfica en el catálogo y guías de armado.

---

## 1. Stack Tecnológico y Arquitectura del Sistema

El ecosistema de diseño y fabricación digital de **Mydo-mar** desacopla el modelado 3D interactivo en el cliente de la persistencia relacional en el servidor, utilizando tecnologías web estándar sin necesidad de plugins pesados:

```
┌─────────────────────────────────────────────────────────────────────────────────┐
│                           CLIENTE (Navegador Web)                               │
│                                                                                 │
│  ┌──────────────────────┐   ┌────────────────────────┐   ┌───────────────────┐  │
│  │ Three.js r128+ WebGL │   │  Motor Paramétrico     │   │ Canvas 2D / PDF   │  │
│  │ (Lienzo 3D, Shaders, │   │  (engine.js, wizard.js,│   │ (Captura Base64,  │  │
│  │  OrbitControls, OBB) │   │   designer-app.js)     │   │  Planos de Corte) │  │
│  └──────────┬───────────┘   └───────────┬────────────┘   └─────────┬─────────┘  │
└─────────────┼───────────────────────────┼──────────────────────────┼────────────┘
              │                           │                          │
              ▼                           ▼                          ▼
   JSON Serializado (`estructura_json`) + Imagen Render (`imagen_base64`)
              │
              ▼ HTTP POST / PUT (Formularios Blade & AJAX)
┌─────────────────────────────────────────────────────────────────────────────────┐
│                           SERVIDOR (Laravel 12 / PHP 8.2)                       │
│                                                                                 │
│  ┌───────────────────────────────────────────────────────────────────────────┐  │
│  │ Middlewares: `auth`, `CheckRol` (Maestro=1, Cliente=2), `es_admin`        │  │
│  ├───────────────────────────────────────────────────────────────────────────┤  │
│  │ Controladores: `MuebleController`, `MaestroController`, `CatalogoController`│  │
│  ├───────────────────────────────────────────────────────────────────────────┤  │
│  │ Storage Engine: Decodificación Base64 -> `storage/app/public/muebles/*.jpg│  │
│  └─────────────────────────────────────┬─────────────────────────────────────┘  │
└────────────────────────────────────────┼────────────────────────────────────────┘
                                         ▼
┌─────────────────────────────────────────────────────────────────────────────────┐
│                    BASE DE DATOS (PostgreSQL - Eloquent ORM)                    │
│                                                                                 │
│  Tabla `muebles`:                                                               │
│  - `id`: bigint                                                                 │
│  - `user_id`: FK -> users.id (Maestro autor)                                    │
│  - `nombre`, `material`, `precio`, `dimensiones`                                │
│  - `estructura_json`: LONGTEXT (Árbol completo de piezas 3D y despiece)         │
│  - `imagen_url`: VARCHAR (Ruta al render fotográfico en disco)                 │
│  - `es_publico`, `vistas`, `created_at`, `deleted_at` (SoftDeletes)             │
└─────────────────────────────────────────────────────────────────────────────────┘
```

### Tecnologías Empleadas y su Rol
1. **Three.js (WebGL):** Motor de renderizado acelerado por hardware para el renderizado del lienzo en tiempo real (`PerspectiveCamera`, `OrbitControls`, `DirectionalLight`, sombras `PCFSoftShadowMap`).
2. **JavaScript Modular Nativo (`designer-app.js`, `engine.js`, `wizard.js`):** Gestiona la física espacial, imantación (*snapping*), jerarquías de ensamble, colisiones orientadas (`THREE.OBB`) e historial de deshacer/rehacer.
3. **HTML5 Canvas API:** Permite capturar instantáneas del búfer de renderizado (`preserveDrawingBuffer: true`, `toDataURL('image/jpeg', 0.85)`) para generar miniaturas automáticas sin renderizado del lado del servidor.
4. **Laravel 12 (PHP 8.2+):** Orquesta la autenticación segura, control de acceso por roles (RBAC), validaciones de negocio, almacenamiento de archivos y la inyección reactiva de plantillas Blade.
5. **PostgreSQL:** Base de datos relacional robusta que almacena metadatos y serializaciones JSON estructuradas con integridad referencial en cascada.

---

## 2. Sistema de Coordenadas y Reglas del Espacio 3D

El entorno 3D opera bajo el estándar milimétrico ($1\text{ unidad Three.js} = 1\text{ mm}$):

| Eje | Dirección | Interpretación Técnica | Referencia de Taller |
|:---:|:---:|:---:|:---:|
| **X** | Ancho | Izquierda ($-X$) a Derecha ($+X$) | Centro del mueble en $X = 0$ |
| **Y** | Alto | Suelo ($Y = 0$) hacia arriba ($+Y$) | Nivel de piso terminado en $Y = 0$ |
| **Z** | Fondo / Profundidad | Frente ($+Z$) a Fondo ($-Z$) | Centro geométrico en $Z = 0$ |

### Regla Crítica del Centro Geométrico
`THREE.BoxGeometry(w, h, d)` sitúa su pivote en el **baricentro** (centro exacto de la caja). Por ende:
* Una pieza apoyada sobre el suelo ($Y = 0$) con altura $h$ debe posicionarse en:
  $$y = \frac{h}{2}$$
* Una pieza que descansa sobre un zócalo de altura $Z_h$ y base de espesor $esp$ se ubica en:
  $$y = Z_h + esp + \frac{h}{2}$$
* Espesor estándar de tableros de melamina:
  $$esp = 18\text{ mm}$$

---

## 3. Lógica de Graficado de Muebles y Texturas Métricas

### 3.1. Creación de Paneles con `createPanel`

La función constructora base instancia la geometría, calcula cajas orientadas de colisión (OBB), asocia el material con textura UV métrica y registra metadatos:

```javascript
function createPanel(w, h, d, x, y, z, name, addToScene = true) {
    const geometry = new THREE.BoxGeometry(w, h, d);
    geometry.computeBoundingBox();
    
    // Bounding Box Orientado (OBB) para colisiones precisas
    geometry.userData.obb = new THREE.OBB().fromBox3(geometry.boundingBox);
    
    // Asignación de material PBR con textura física
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
```

### 3.2. Clasificación Obligatoria de Piezas (`pieceType`)
Fundamental para el cálculo de despiece y la orientación de la veta en la optimización de corte:
* `mesh.userData.pieceType = 'vertical';` $\rightarrow$ Laterales, fondos, faldones, parantes y divisiones.
* `mesh.userData.pieceType = 'horizontal';` $\rightarrow$ Cubiertas, bases, techos, repisas y revisteros.

### 3.3. Mapeo UV Métrico y Catálogo de Texturas

Para evitar que la textura de la madera se estire o pixele cuando una pieza cambia de tamaño, el sistema implementa **escalado métrico UV** con `THREE.RepeatWrapping`:

```javascript
const textureCatalog = {
    'roble':   { name: 'Roble Natural',       url: '/textures/maderado.jpg',             scale: 400, fallbackColor: 0xd2a679 },
    'nogal':   { name: 'Nogal Oscuro',        url: '/textures/nogal.jpg',                scale: 400, fallbackColor: 0x5c4033 },
    'blanca':  { name: 'Melamina Blanca',     url: '/textures/maple.jpg',                scale: 500, fallbackColor: 0xf5f5f5 },
    'grafito': { name: 'Gris Grafito',        url: '/textures/plateado-texturizado.jpg', scale: 500, fallbackColor: 0x3a3d42 },
    'marmol':  { name: 'Mármol Calacatta',    url: '/textures/plateado-texturizado.jpg', scale: 600, fallbackColor: 0xeaecef },
    'haya':    { name: 'Haya Madera',         url: '/textures/maderado.jpg',             scale: 400, fallbackColor: 0xd8a47f }
};

function createPieceMaterial(key, w = 500, h = 500, d = 18) {
    if (!textureCatalog[key]) key = 'roble';
    const info = textureCatalog[key];
    const scale = info.scale || 400;

    // Cálculo proporcional métrico según las dimensiones de la pieza
    const maxDim1 = Math.max(w, h);
    const maxDim2 = Math.max(Math.min(w, h), d);
    const rX = Math.max(1, +(maxDim1 / scale).toFixed(2));
    const rY = Math.max(1, +(maxDim2 / scale).toFixed(2));

    const mat = new THREE.MeshStandardMaterial({
        color: 0xffffff,
        roughness: 0.55,
        metalness: 0.05
    });

    new THREE.TextureLoader().load(
        info.url,
        (tex) => {
            tex.wrapS = THREE.RepeatWrapping;
            tex.wrapT = THREE.RepeatWrapping;
            tex.repeat.set(rX, rY);
            mat.map = tex;
            mat.needsUpdate = true;
        },
        undefined,
        () => {
            mat.color.setHex(info.fallbackColor);
            mat.needsUpdate = true;
        }
    );
    return mat;
}
```

---

## 4. Comunicación Espacial entre Piezas: Ensamble, Colisión y Agrupación

### 4.1. Transformación Solidaria y Bloqueo (`THREE.Group`)
Cuando el usuario presiona `#btn-agrupar`, el sistema fusiona todas las piezas independientes en un único contenedor rígido sin perder la posición relativa de cada tablero:

1. **Cálculo de la Caja Envolvente Exterior:**
   $$\text{bbox} = \bigcup_{i=1}^n \text{Box3}(objeto_i)$$
2. **Definición del Pivote en el Suelo:**
   $$\vec{P}_{pivote} = \left(\text{center}.x, \, \text{bbox}_{min}.y, \, \text{center}.z\right)$$
3. **Re-anclaje Relativo:** Cada pieza se desvincula de la escena y se asocia al grupo con posición relativa $\vec{p}_{rel} = \vec{p}_{global} - \vec{P}_{pivote}$.
4. **Desbloqueo / Desagrupación:** Al desagrupar, cada pieza hija recupera su transformación absoluta aplicando la rotación del grupo mediante cuaterniones y sumando la posición global del grupo:
   $$\vec{p}_{nueva} = \mathbf{R}_y(\theta) \cdot \vec{p}_{local} + \vec{P}_{grupo}$$

---

## 5. Ciclo de Vida: De la Escena 3D a la Base de Datos

```
[Lienzo Three.js] 
       │
       ├─► window.getExportData() ─────────► Árbol JSON estructurado (w, h, d, pos, rot, texturas)
       └─► window.getScene3DScreenshot() ──► Imagen JPEG en Base64 (canvas.toDataURL)
                                                    │
                                                    ▼
                                  Formulario HTML oculto en create.blade.php
                                  - input #form-estructura-json
                                  - input #form-imagen-base64
                                                    │
                                                    ▼ HTTP POST /muebles
                                     [Laravel MuebleController::store]
                                                    │
                        ┌───────────────────────────┴───────────────────────────┐
                        ▼                                                       ▼
           Decodificación Base64                                    Almacenamiento en BD
      Storage::disk('public')->put(                                 Mueble::create([
         'muebles/mueble_3d_xxx.jpg',                                  'nombre' => ...,
         $decodedData                                                  'estructura_json' => $json,
      )                                                                'imagen_url' => $ruta,
                                                                       'es_publico' => true
                                                                    ]);
```

### 5.1. Estructura del JSON Serializado (`estructura_json`)
Al guardar o exportar, `window.getExportData()` genera el siguiente esquema normalizado:

```json
{
  "tipo": "3d_design",
  "nombre": "Escritorio Ejecutivo Grande",
  "dimensiones": "1400 × 750 × 700 mm",
  "total_piezas": 5,
  "piezas": [
    {
      "nombre": "Lateral Izquierdo",
      "pieza": "Lateral Izquierdo",
      "cantidad": 1,
      "espesor": 18,
      "ancho": 732,
      "alto": 700,
      "material": "Roble Natural",
      "orientacion": "vertical"
    }
  ],
  "objetos": [
    {
      "name": "Cubierta Superior",
      "w": 1400,
      "h": 18,
      "d": 700,
      "pos": [0, 741, 0],
      "rot": [0, 0, 0],
      "pieceType": "horizontal",
      "textureKey": "roble"
    },
    {
      "name": "Lateral Izquierdo",
      "w": 18,
      "h": 732,
      "d": 700,
      "pos": [-691, 366, 0],
      "rot": [0, 0, 0],
      "pieceType": "vertical",
      "textureKey": "roble"
    }
  ]
}
```

---

## 6. Reconstrucción e Hidratación Gráfica desde la Base de Datos

Cuando un cliente o carpintero accede a una vista pública o de edición, el backend entrega el JSON almacenado y el frontend reconstruye la escena 3D idéntica.

### 6.1. Reconstrucción en el Catálogo Público ([`catalogo/show.blade.php`](file:///c:/Users/Usuario/Documents/GitHub/Mydo_Mar/resources/views/catalogo/show.blade.php))

```php
@php
    $estructura = json_decode($mueble->estructura_json, true);
    $objetos = $estructura['objetos'] ?? [];
@endphp
```

En el cliente JavaScript de Three.js:
1. Se recuperan los objetos mediante:
   ```javascript
   const objetosData = @json($objetos);
   const group = new THREE.Group();
   ```
2. Por cada elemento de `objetosData`, se crea un `THREE.BoxGeometry(o.w, o.h, o.d)`:
   ```javascript
   objetosData.forEach(o => {
       const geom = new THREE.BoxGeometry(o.w, o.h, o.d);
       const mat = createPieceMaterial(o.textureKey || 'roble', o.w, o.h, o.d);
       const mesh = new THREE.Mesh(geom, mat);
       if (o.pos) mesh.position.set(o.pos[0], o.pos[1], o.pos[2]);
       if (o.rot) mesh.rotation.set(o.rot[0], o.rot[1], o.rot[2]);

       // Líneas de arista técnicas para acabado profesional de taller
       const edges = new THREE.EdgesGeometry(geom);
       const line = new THREE.LineSegments(edges, edgeMat);
       mesh.add(line);

       group.add(mesh);
   });
   scene.add(group);
   ```
3. **Auto-Encuadre de Cámara:**
   ```javascript
   const box = new THREE.Box3().setFromObject(group);
   const center = new THREE.Vector3();
   box.getCenter(center);
   controls.target.copy(center);

   const size = new THREE.Vector3();
   box.getSize(size);
   const maxDim = Math.max(size.x, size.y, size.z);
   camera.position.set(center.x + maxDim * 1.2, center.y + maxDim * 0.9, center.z + maxDim * 1.4);
   controls.update();
   ```

### 6.2. Reanudación de Edición en el Taller ([`maestro/muebles/create.blade.php`](file:///c:/Users/Usuario/Documents/GitHub/Mydo_Mar/resources/views/maestro/muebles/create.blade.php))
Al editar un mueble existente, la vista inyecta `window.__muebleJSON__ = @json($mueble->estructura_json)`. La función `window.cargarDesdeJSON(data)` recrea los paneles con capacidad completa de selección, movimiento y redimensionamiento paramétrico.

### 6.3. Guía Interactiva de Armado ([`catalogo/armado.blade.php`](file:///c:/Users/Usuario/Documents/GitHub/Mydo_Mar/resources/views/catalogo/armado.blade.php))
La guía de montaje lee el array `piezas` y `objetos`, categoriza las fases constructivas (1. Zócalo y Base $\rightarrow$ 2. Laterales $\rightarrow$ 3. Repisas $\rightarrow$ 4. Cubierta $\rightarrow$ 5. Puertas y Herrajes) y permite al usuario animar secuencialmente la colocación de cada componente.

---

## 7. Fórmulas de Ensamble de Carpintería Real

### Caso A: Tapa superior sobrepuesta a los laterales
*(Escritorios, Mesas, Cómodas, Gabinetes bajos)*

```
Dimensiones exteriores totales: W x H x D     Espesor: esp = 18

Lateral Izquierdo:   w=esp,  h=H-esp, d=D     pos: x=-(W/2 - esp/2),  y=(H-esp)/2,  z=0
Lateral Derecho:     w=esp,  h=H-esp, d=D     pos: x=+(W/2 - esp/2),  y=(H-esp)/2,  z=0
Cubierta Superior:   w=W,    h=esp,   d=D     pos: x=0,               y=H - esp/2,  z=0
Base Inferior:       w=W,    h=esp,   d=D     pos: x=0,               y=esp/2,      z=0
```

### Caso B: Laterales del suelo al techo (continuos)
*(Estanterías, Roperos, Alacenas, Bibliotecas)*

```
Dimensiones exteriores totales: W x H x D     Espesor: esp = 18    Zócalo: zocaloH = 70

Lateral Izquierdo:   w=esp,       h=H,          d=D      pos: x=-(W/2 - esp/2),  y=H/2,               z=0
Lateral Derecho:     w=esp,       h=H,          d=D      pos: x=+(W/2 - esp/2),  y=H/2,               z=0
Techo Superior:      w=W - 2*esp, h=esp,        d=D      pos: x=0,               y=H - esp/2,         z=0
Base Inferior:       w=W - 2*esp, h=esp,        d=D      pos: x=0,               y=zocaloH + esp/2,   z=0
Zócalo Frontal:      w=W - 2*esp, h=zocaloH,   d=esp    pos: x=0,               y=zocaloH/2,         z=D/2 - esp/2 - 15
Fondo Trasero:       w=W - 2*esp, h=H-70-esp,   d=esp    pos: x=0,               y=(70+H)/2,          z=-D/2 + esp/2
```

### Repisas Intermedias para $N$ Niveles
```javascript
const espacioUtil = H - zocaloH - 2 * esp;
const gap = (espacioUtil - N * esp) / (N + 1);
for (let i = 1; i <= N; i++) {
    const yPos = zocaloH + esp + gap * i + esp * (i - 1) + esp / 2;
    const repisa = createPanel(W - 2 * esp, esp, D - 15, 0, yPos, 0, 'Repisa ' + i);
    repisa.userData.pieceType = 'horizontal';
    applyMaterialToMesh(repisa, 'haya');
}
```

---

## 8. Estructura Obligatoria para Funciones Generadoras de Muebles

Cualquier script o IA que genere muebles en consola o mediante plantillas debe implementar este flujo:

```javascript
window.generarMiMueble = function() {
    // 1. Limpieza segura de la escena previa
    clearAllObjects();

    // 2. Parámetros exteriores en milímetros
    const esp = 18;
    const W = 800;  // Ancho total
    const H = 900;  // Alto total
    const D = 450;  // Fondo total

    // 3. Creación y texturizado de piezas
    const cubierta = createPanel(W, esp, D, 0, H - esp / 2, 0, 'Cubierta Superior');
    cubierta.userData.pieceType = 'horizontal';
    applyMaterialToMesh(cubierta, 'roble');

    // 4. Posicionamiento de cámara y encuadre
    camera.position.set(W * 1.2, H * 1.3, W * 1.5);
    controls.target.set(0, H / 2, 0);
    controls.update();

    // 5. Asignación de nombre en el formulario
    const inp = document.getElementById('project-name-input');
    if (inp) inp.value = 'Mi Mueble Personalizado';

    // 6. Selección, persistencia en historial y notificación
    selectObject(cubierta);
    saveState();
    showToast('Mueble generado correctamente', '#27ae60');
};
```

---

## 9. Ejemplos de Taller Listos para Ejecución

### 9.1. Escritorio Ejecutivo (1200 × 750 × 600 mm)
```javascript
window.cargarEjemploEscritorio = function() {
    clearAllObjects();
    const esp = 18, W = 1200, H = 750, D = 600;

    const legL = createPanel(esp, H - esp, D, -W/2 + esp/2, (H - esp)/2, 0, 'Lateral Izquierdo');
    legL.userData.pieceType = 'vertical'; applyMaterialToMesh(legL, 'roble');

    const legR = createPanel(esp, H - esp, D, W/2 - esp/2, (H - esp)/2, 0, 'Lateral Derecho');
    legR.userData.pieceType = 'vertical'; applyMaterialToMesh(legR, 'roble');

    const top = createPanel(W, esp, D, 0, H - esp/2, 0, 'Cubierta Superior');
    top.userData.pieceType = 'horizontal'; applyMaterialToMesh(top, 'roble');

    const back = createPanel(W - 2*esp, 320, esp, 0, H - esp - 160, -D/2 + esp/2 + 40, 'Faldón Refuerzo');
    back.userData.pieceType = 'vertical'; applyMaterialToMesh(back, 'roble');

    const rep = createPanel(W - 2*esp, esp, 220, 0, 260, -D/2 + esp + 130, 'Repisa Inferior');
    rep.userData.pieceType = 'horizontal'; applyMaterialToMesh(rep, 'roble');

    camera.position.set(W * 0.95, H * 1.35, W * 1.25);
    controls.target.set(0, H / 2, 0); controls.update();
    document.getElementById('project-name-input').value = 'Escritorio Básico Oficina';
    selectObject(top); saveState();
    showToast('Escritorio cargado con éxito', '#27ae60');
};
```

### 9.2. Estantería Librero 4 Niveles (600 × 1200 × 300 mm)
```javascript
window.cargarEjemploEstanteria = function() {
    clearAllObjects();
    const esp = 18, W = 600, H = 1200, D = 300;

    const legL = createPanel(esp, H, D, -W/2 + esp/2, H/2, 0, 'Lateral Izquierdo');
    legL.userData.pieceType = 'vertical'; applyMaterialToMesh(legL, 'haya');

    const legR = createPanel(esp, H, D, W/2 - esp/2, H/2, 0, 'Lateral Derecho');
    legR.userData.pieceType = 'vertical'; applyMaterialToMesh(legR, 'haya');

    const base = createPanel(W - 2*esp, esp, D, 0, 70 + esp/2, 0, 'Base Inferior');
    base.userData.pieceType = 'horizontal'; applyMaterialToMesh(base, 'haya');

    const top = createPanel(W - 2*esp, esp, D, 0, H - esp/2, 0, 'Techo Superior');
    top.userData.pieceType = 'horizontal'; applyMaterialToMesh(top, 'haya');

    const zocalo = createPanel(W - 2*esp, 70, esp, 0, 35, D/2 - esp/2 - 15, 'Zócalo Frontal');
    zocalo.userData.pieceType = 'vertical'; applyMaterialToMesh(zocalo, 'haya');

    const fondoH = H - 70 - esp;
    const fondo = createPanel(W - 2*esp, fondoH, esp, 0, 70 + esp + fondoH/2, -D/2 + esp/2, 'Fondo Trasero');
    fondo.userData.pieceType = 'vertical'; applyMaterialToMesh(fondo, 'blanca');

    const gap = (H - 70 - 2*esp - 3*esp) / 4;
    for (let i = 1; i <= 3; i++) {
        const yPos = 70 + esp + (gap + esp) * i - esp / 2;
        const rep = createPanel(W - 2*esp, esp, D - esp - 5, 0, yPos, esp/2 - 2.5, 'Repisa ' + i);
        rep.userData.pieceType = 'horizontal'; applyMaterialToMesh(rep, 'haya');
    }

    camera.position.set(W * 1.4, H * 0.85, W * 1.9);
    controls.target.set(0, H / 2, 0); controls.update();
    document.getElementById('project-name-input').value = 'Estantería 4 Niveles';
    selectObject(top); saveState();
    showToast('Estantería cargada con éxito', '#27ae60');
};
```

---

## 10. Checklist de Validación Técnica

Antes de guardar un mueble en base de datos o validar una función constructora:
- [ ] **Apoyo en Suelo:** Ninguna pieza penetra el suelo ($y \ge h/2$).
- [ ] **Descuento de Espesores:** Las piezas interiores descuentan $2 \times esp$ en $W$ o $H$.
- [ ] **Clasificación Tipológica:** Cada panel tiene su `userData.pieceType` (`'horizontal'` o `'vertical'`).
- [ ] **Mapeo de Textura:** Cada panel tiene asignado un `textureKey` válido del catálogo.
- [ ] **Centro de Cámara:** El objetivo de órbita apunta a `(0, H/2, 0)`.
- [ ] **Título del Proyecto:** El campo `#project-name-input` contiene una denominación descriptiva.
- [ ] **Historial y Estado:** Se invocó `saveState()` para permitir deshacer/rehacer cambios.
