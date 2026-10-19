# Guia de Creacion de Muebles 3D - Disenador Mydo-Mar

Guia de referencia tecnica para que cualquier IA o desarrollador pueda generar muebles precisos mediante comandos JavaScript en el disenador 3D (/muebles/crear).

---

## 1. Sistema de Coordenadas y Reglas del Espacio 3D

El entorno usa **Three.js** con las siguientes convenciones:

| Eje | Direccion | Referencia |
|-----|-----------|------------|
| **X (Ancho)** | Izquierda (-X) a Derecha (+X) | Centro del mueble en X = 0 |
| **Y (Alto)** | Suelo (Y = 0) hacia arriba (+Y) | Piso siempre en Y = 0 |
| **Z (Profundidad)** | Frente (+Z) a Fondo (-Z) | Centro en Z = 0 |

**Regla critica de posicionamiento:**
BoxGeometry posiciona por el **centro geometrico** de la caja. Una pieza de altura h apoyada sobre el suelo debe tener: y = h / 2

Espesor estandar de tablero de melamina: esp = 18 mm

---

## 2. API Central para Crear Piezas

### createPanel(w, h, d, x, y, z, name)

Crea un tablero rectangular con colisiones OBB y lo inserta en la escena.

| Parametro | Tipo | Descripcion |
|-----------|------|-------------|
| w | number | Ancho en mm (dimension en X) |
| h | number | Alto en mm (dimension en Y) |
| d | number | Profundidad en mm (dimension en Z) |
| x | number | Posicion del centro en X |
| y | number | Posicion del centro en Y |
| z | number | Posicion del centro en Z |
| name | string | Nombre descriptivo de la pieza |

Retorna el objeto mesh de Three.js que puede ser configurado con tipo y material.

### Tipos de Pieza (userData.pieceType)

Obligatorio para el despiece y el optimizador de corte:

`javascript
mesh.userData.pieceType = 'vertical';   // Laterales, fondos, faldones, divisiones
mesh.userData.pieceType = 'horizontal'; // Cubiertas, bases, techos, repisas
`

### Texturas Disponibles - applyMaterialToMesh(mesh, clave)

| Clave | Nombre visible |
|-------|----------------|
| 'roble' | Roble Natural |
| 'nogal' | Nogal Oscuro |
| 'blanca' | Melamina Blanca Lisa |
| 'grafito' | Gris Grafito |
| 'marmol' | Marmol Calacatta |
| 'haya' | Haya Madera |

---

## 3. Formulas de Ensamble de Carpinteria Real

### Caso A: Tapa superior sobrepasa los laterales
*(Escritorios, Mesas, Comodas, Gabinetes bajos)*

`
Exteriores: W x H x D    Espesor: esp = 18

Lateral Izquierdo:   w=esp,  h=H-esp, d=D    pos: x=-(W/2-esp/2),  y=(H-esp)/2,  z=0
Lateral Derecho:     w=esp,  h=H-esp, d=D    pos: x=+(W/2-esp/2),  y=(H-esp)/2,  z=0
Cubierta Superior:   w=W,    h=esp,   d=D    pos: x=0,              y=H-esp/2,    z=0
Base Inferior:       w=W,    h=esp,   d=D    pos: x=0,              y=esp/2,      z=0
`

### Caso B: Laterales del suelo al techo (continuos)
*(Estanterias, Roperos, Alacenas, Bibliotecas)*

`
Exteriores: W x H x D    Espesor: esp = 18    zocaloH = 70

Lateral Izquierdo:   w=esp,     h=H,        d=D    pos: x=-(W/2-esp/2),  y=H/2,            z=0
Lateral Derecho:     w=esp,     h=H,        d=D    pos: x=+(W/2-esp/2),  y=H/2,            z=0
Techo Superior:      w=W-2*esp, h=esp,      d=D    pos: x=0,              y=H-esp/2,        z=0
Base Inferior:       w=W-2*esp, h=esp,      d=D    pos: x=0,              y=zocaloH+esp/2,  z=0
Zocalo Frontal:      w=W-2*esp, h=zocaloH, d=esp   pos: x=0,              y=zocaloH/2,      z=D/2-esp/2-15
Fondo Trasero:       w=W-2*esp, h=H-70-esp,d=esp   pos: x=0,              y=(70+H)/2,       z=-D/2+esp/2
`

### Repisas Intermedias para N niveles

`javascript
const espacioUtil = H - zocaloH - 2 * esp;
const gap = (espacioUtil - N * esp) / (N + 1);
for (let i = 1; i <= N; i++) {
    const yPos = zocaloH + esp + gap * i + esp * (i - 1) + esp / 2;
    const repisa = createPanel(W - 2 * esp, esp, D - 15, 0, yPos, 0, 'Repisa ' + i);
    repisa.userData.pieceType = 'horizontal';
    applyMaterialToMesh(repisa, 'haya');
}
`

---

## 4. Estructura Obligatoria de Toda Funcion Creadora

`javascript
window.generarMiMueble = function() {

    // Paso 1: Limpiar escena anterior de forma segura
    clearAllObjects();

    // Paso 2: Definir medidas exteriores en milimetros
    const esp = 18;
    const W = 800;  // Ancho total exterior
    const H = 900;  // Alto total exterior
    const D = 450;  // Profundidad total exterior

    // Paso 3: Crear piezas con createPanel, asignar rol y textura
    const pieza = createPanel(W, esp, D, 0, H - esp/2, 0, 'Cubierta');
    pieza.userData.pieceType = 'horizontal';
    applyMaterialToMesh(pieza, 'roble');

    // Paso 4: Posicionar camara para encuadrar el mueble
    camera.position.set(W * 1.2, H * 1.3, W * 1.5);
    controls.target.set(0, H / 2, 0);
    controls.update();

    // Paso 5: Nombre en el campo de formulario
    const inp = document.getElementById('project-name-input');
    if (inp) inp.value = 'Nombre del Mueble';

    // Paso 6: Seleccionar pieza, guardar historial y notificar
    selectObject(pieza);
    saveState();
    showToast('Mueble generado correctamente', '#27ae60');
};
`

---

## 5. Ejemplos Completos

### 5.1. Escritorio de Oficina (1200 x 750 x 600 mm)

`javascript
window.cargarEjemploEscritorio = function() {
    clearAllObjects();
    const esp = 18, W = 1200, H = 750, D = 600;

    const legL = createPanel(esp, H-esp, D, -W/2+esp/2, (H-esp)/2, 0, 'Lateral Izquierdo');
    legL.userData.pieceType = 'vertical'; applyMaterialToMesh(legL, 'roble');

    const legR = createPanel(esp, H-esp, D, W/2-esp/2, (H-esp)/2, 0, 'Lateral Derecho');
    legR.userData.pieceType = 'vertical'; applyMaterialToMesh(legR, 'roble');

    const top = createPanel(W, esp, D, 0, H-esp/2, 0, 'Cubierta Superior');
    top.userData.pieceType = 'horizontal'; applyMaterialToMesh(top, 'roble');

    const back = createPanel(W-2*esp, 320, esp, 0, H-esp-160, -D/2+esp/2+40, 'Faldon Refuerzo');
    back.userData.pieceType = 'vertical'; applyMaterialToMesh(back, 'roble');

    const rep = createPanel(W-2*esp, esp, 220, 0, 260, -D/2+esp+130, 'Repisa Inferior');
    rep.userData.pieceType = 'horizontal'; applyMaterialToMesh(rep, 'roble');

    camera.position.set(W*0.95, H*1.35, W*1.25);
    controls.target.set(0, H/2, 0); controls.update();
    document.getElementById('project-name-input').value = 'Escritorio Basico Oficina';
    selectObject(top); saveState();
    showToast('Escritorio cargado. Edita sus piezas y medidas.', '#27ae60');
};
`

### 5.2. Estanteria / Librero (600 x 1200 x 300 mm, 4 niveles)

`javascript
window.cargarEjemploEstanteria = function() {
    clearAllObjects();
    const esp = 18, W = 600, H = 1200, D = 300;

    const legL = createPanel(esp, H, D, -W/2+esp/2, H/2, 0, 'Lateral Izquierdo');
    legL.userData.pieceType = 'vertical'; applyMaterialToMesh(legL, 'haya');

    const legR = createPanel(esp, H, D, W/2-esp/2, H/2, 0, 'Lateral Derecho');
    legR.userData.pieceType = 'vertical'; applyMaterialToMesh(legR, 'haya');

    const base = createPanel(W-2*esp, esp, D, 0, 70+esp/2, 0, 'Base Inferior');
    base.userData.pieceType = 'horizontal'; applyMaterialToMesh(base, 'haya');

    const top = createPanel(W-2*esp, esp, D, 0, H-esp/2, 0, 'Techo Superior');
    top.userData.pieceType = 'horizontal'; applyMaterialToMesh(top, 'haya');

    const zocalo = createPanel(W-2*esp, 70, esp, 0, 35, D/2-esp/2-15, 'Zocalo Frontal');
    zocalo.userData.pieceType = 'vertical'; applyMaterialToMesh(zocalo, 'haya');

    const fondoH = H-70-esp;
    const fondo = createPanel(W-2*esp, fondoH, esp, 0, 70+esp+fondoH/2, -D/2+esp/2, 'Fondo Trasero');
    fondo.userData.pieceType = 'vertical'; applyMaterialToMesh(fondo, 'blanca');

    const gap = (H-70-2*esp-3*esp) / 4;
    for (let i = 1; i <= 3; i++) {
        const yPos = 70+esp+(gap+esp)*i-esp/2;
        const rep = createPanel(W-2*esp, esp, D-esp-5, 0, yPos, esp/2-2.5, 'Repisa '+i);
        rep.userData.pieceType = 'horizontal'; applyMaterialToMesh(rep, 'haya');
    }

    camera.position.set(W*1.4, H*0.85, W*1.9);
    controls.target.set(0, H/2, 0); controls.update();
    document.getElementById('project-name-input').value = 'Estanteria 4 Niveles';
    selectObject(top); saveState();
    showToast('Estanteria cargada. Lista para personalizar.', '#27ae60');
};
`

### 5.3. Alacena de Cocina 2 Puertas (800 x 700 x 350 mm)

`javascript
window.cargarEjemploAlacena = function() {
    clearAllObjects();
    const esp = 18, W = 800, H = 700, D = 350;

    const latIzq = createPanel(esp, H, D, -W/2+esp/2, H/2, 0, 'Lateral Izquierdo');
    latIzq.userData.pieceType = 'vertical'; applyMaterialToMesh(latIzq, 'blanca');

    const latDer = createPanel(esp, H, D, W/2-esp/2, H/2, 0, 'Lateral Derecho');
    latDer.userData.pieceType = 'vertical'; applyMaterialToMesh(latDer, 'blanca');

    const base = createPanel(W-2*esp, esp, D, 0, esp/2, 0, 'Base Inferior');
    base.userData.pieceType = 'horizontal'; applyMaterialToMesh(base, 'blanca');

    const techo = createPanel(W-2*esp, esp, D, 0, H-esp/2, 0, 'Techo Superior');
    techo.userData.pieceType = 'horizontal'; applyMaterialToMesh(techo, 'blanca');

    const fondo = createPanel(W-2*esp, H-2*esp, esp, 0, H/2, -D/2+esp/2, 'Fondo Trasero');
    fondo.userData.pieceType = 'vertical'; applyMaterialToMesh(fondo, 'blanca');

    const rep = createPanel(W-2*esp, esp, D-20, 0, H/2, 10, 'Repisa Central');
    rep.userData.pieceType = 'horizontal'; applyMaterialToMesh(rep, 'blanca');

    const pW = (W-6)/2, pH = H-4;
    const pIzq = createPanel(pW, pH, esp, -pW/2-1, H/2, D/2+esp/2, 'Puerta Izquierda');
    pIzq.userData.pieceType = 'vertical'; applyMaterialToMesh(pIzq, 'roble');

    const pDer = createPanel(pW, pH, esp, pW/2+1, H/2, D/2+esp/2, 'Puerta Derecha');
    pDer.userData.pieceType = 'vertical'; applyMaterialToMesh(pDer, 'roble');

    camera.position.set(W*1.3, H*1.2, W*1.5);
    controls.target.set(0, H/2, 0); controls.update();
    document.getElementById('project-name-input').value = 'Alacena Cocina 2 Puertas';
    selectObject(techo); saveState();
    showToast('Alacena cargada exitosamente', '#27ae60');
};
`

### 5.4. Mesa de Centro con Patas (800 x 450 x 500 mm)

`javascript
window.cargarEjemploMesa = function() {
    clearAllObjects();
    const esp = 18, W = 800, H = 450, D = 500;
    const ls = 45, lH = H-esp;
    const offX = W/2-ls/2-15, offZ = D/2-ls/2-15;

    const top = createPanel(W, esp, D, 0, H-esp/2, 0, 'Tablero Mesa');
    top.userData.pieceType = 'horizontal'; applyMaterialToMesh(top, 'roble');

    [[-offX,-offZ,'Pata 1'],[offX,-offZ,'Pata 2'],
     [-offX, offZ,'Pata 3'],[offX, offZ,'Pata 4']].forEach(([px,pz,n]) => {
        const p = createPanel(ls, lH, ls, px, lH/2, pz, n);
        p.userData.pieceType = 'vertical'; applyMaterialToMesh(p, 'roble');
    });

    const fH = 65, fW = W-2*ls-30, fD = D-2*ls-30;
    [[fW,fH,esp,0,H-esp-fH/2, offZ-ls/2+esp/2,'Faldon Frontal'],
     [fW,fH,esp,0,H-esp-fH/2,-offZ+ls/2-esp/2,'Faldon Trasero'],
     [esp,fH,fD,-offX+ls/2-esp/2,H-esp-fH/2,0,'Faldon Izquierdo'],
     [esp,fH,fD, offX-ls/2+esp/2,H-esp-fH/2,0,'Faldon Derecho']].forEach(([w,h,d,x,y,z,n]) => {
        const f = createPanel(w,h,d,x,y,z,n);
        f.userData.pieceType = 'vertical'; applyMaterialToMesh(f, 'roble');
    });

    camera.position.set(W*1.25, H*1.45, W*1.45);
    controls.target.set(0, H/2, 0); controls.update();
    document.getElementById('project-name-input').value = 'Mesa de Centro';
    selectObject(top); saveState();
    showToast('Mesa de centro cargada. Lista para editar.', '#27ae60');
};
`

---

## 6. Funciones de Apoyo del Sistema

| Funcion | Descripcion |
|---------|-------------|
| clearAllObjects() | Elimina todas las piezas de la escena. Llamar siempre al inicio. |
| selectObject(mesh) | Selecciona una pieza y actualiza el panel de parametros. |
| saveState() | Guarda el estado actual en el historial de deshacer/rehacer. |
| showToast(texto, color) | Muestra una notificacion temporal en pantalla. |
| pplyMaterialToAll(clave) | Aplica una textura a todas las piezas de la escena. |
| spawnParametricDoor() | Agrega un modulo de puerta parametrica con bisagras. |
| spawnParametricDrawer() | Agrega un modulo de cajon con correderas telescopicas. |

---

## 7. Errores Comunes que Deben Evitarse

1. No llamar a ormMueble.submit() dentro de carga de ejemplos: causaria redireccion y perdida del modelo.
2. No usar drawerParamsContainer: la variable correcta es moduleParamsContainer.
3. No usar pushHistory() directamente: usar saveState() que envuelve la llamada de forma segura.
4. No posicionar con y=0 para paneles: usar siempre y = h / 2 para que la pieza quede apoyada en el suelo.
5. Sin emojis: todo texto debe ser plano sin caracteres especiales.

---

## 8. Checklist para Validar un Mueble Generado

- [ ] Ninguna pieza penetra el suelo (y >= h/2 siempre).
- [ ] Las medidas interiores respetan el espesor: piezas interiores descontadas en W-2*esp y H-2*esp.
- [ ] Cada createPanel tiene asignado userData.pieceType.
- [ ] Cada pieza tiene applyMaterialToMesh con una clave valida.
- [ ] La camara enfoca el centro del mueble (controls.target.set(0, H/2, 0)).
- [ ] El campo project-name-input tiene el nombre del modelo.
- [ ] Se llama selectObject(piezaPrincipal) al final.
- [ ] Se llama saveState() antes de showToast.
