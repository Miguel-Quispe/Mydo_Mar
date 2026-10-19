// ============================================================
// ENGINE.JS — Motor Paramétrico de Despiece de Muebles
// ============================================================
// FUENTE ÚNICA DE VERDAD: generateParts(spec) → Part[]
//
// Garantía matemática: ninguna pieza se superpone si usas
// estas fórmulas. La física está en las ecuaciones, no en
// la detección de colisiones en runtime.
//
// SPEC OBJECT:
// {
//   type      : 'escritorio' | 'estanteria' | 'gabinete' | 'cajonera'
//   W         : number  — Ancho externo (mm)
//   H         : number  — Alto externo (mm)
//   D         : number  — Profundidad externa (mm)
//   T         : number  — Espesor del material (mm) — libre, sin preset
//   material  : string  — Nombre del material (melamina blanca, madera, etc.)
//   color     : string  — Hex color
//   legType   : 'lateral' | '4patas' | 'central'  (solo escritorio)
//   drawers   : number  — Cantidad de cajones
//   drawerH   : number  — Alto de cada cajón (mm)
//   shelves   : number  — Cantidad de estantes intermedios
//   shelfPositions: number[] — Alturas Y desde el piso (auto si vacío)
//   hasDoor   : boolean
//   hasBack   : boolean — Incluir panel trasero
// }
//
// PART OBJECT:
// {
//   name      : string
//   role      : 'lateral'|'top'|'bottom'|'back'|'shelf'|'drawer-front'|
//               'drawer-side'|'drawer-bottom'|'door'|'leg'|'stretcher'
//   pw, ph, pd: number  — Ancho, Alto, Profundidad de la pieza (corte)
//   x, y, z   : number  — Centro de la pieza en espacio 3D (Three.js)
//   qty       : number  — Para agrupar iguales en la lista de corte
//   edgeBand  : string[] — Bordes que llevan cinta ['front','back','top','bottom','left','right']
//   color     : string
//   step      : number  — Paso del ensamblaje para modo alumno
// }
// ============================================================

'use strict';

// ─── Constantes ─────────────────────────────────────────────
const ENGINE_VERSION = '2.0.0';

// Tolerancia de ensamblaje (holgura de montaje en mm)
const ASSEMBLY_GAP = 0;

// Precio por lámina estándar (puede sobreescribirse por país/material)
const DEFAULT_SHEET_W = 2440;
const DEFAULT_SHEET_H = 1220;

// ─── Función Principal ───────────────────────────────────────
/**
 * generateParts(spec) → Part[]
 * Motor paramétrico puro. Determinista y sin side-effects.
 */
function generateParts(spec) {
    const {
        type      = 'gabinete',
        W         = 800,
        H         = 1800,
        D         = 600,
        T         = 18,
        material  = 'Melamina Blanca',
        color     = '#f5f5f5',
        drawers   = 0,
        drawerH   = 150,
        shelves   = 2,
        shelfPositions = [],
        hasDoor   = false,
        hasBack   = true,
        legType   = 'lateral',
    } = spec;

    // Validaciones
    console.assert(W > T * 2, `[engine] Ancho ${W}mm demasiado pequeño para espesor ${T}mm`);
    console.assert(H > T * 2, `[engine] Alto ${H}mm demasiado pequeño para espesor ${T}mm`);
    console.assert(D > T,     `[engine] Profundidad ${D}mm demasiado pequeña para espesor ${T}mm`);

    switch (type) {
        case 'escritorio':  return _partsEscritorioLateral(W, H, D, T, color, legType, drawers, drawerH, hasBack);
        case 'estanteria':  return _partsEstanteria(W, H, D, T, color, shelves, shelfPositions, hasBack, hasDoor);
        case 'gabinete':    return _partsGabinete(W, H, D, T, color, shelves, drawers, drawerH, hasDoor, hasBack);
        case 'cajonera':    return _partsCajonera(W, H, D, T, color, drawers, drawerH, hasBack);
        default:            return _partsGabinete(W, H, D, T, color, shelves, drawers, drawerH, hasDoor, hasBack);
    }
}

// ─── ESCRITORIO ──────────────────────────────────────────────
function _partsEscritorioLateral(W, H, D, T, color, legType, drawers, drawerH, hasBack) {
    const parts = [];

    if (legType === 'lateral') {
        // ── LATERALES: van de piso a techo, ocupan todo el alto
        //    Son la "columna vertebral" que sujeta todo.
        parts.push(_makePart('Panel Lateral Izq', 'lateral',
            T, H, D,
            -W/2 + T/2,  H/2, 0,
            color, 1, ['front', 'top'], 1
        ));
        parts.push(_makePart('Panel Lateral Der', 'lateral',
            T, H, D,
            W/2 - T/2, H/2, 0,
            color, 1, ['front', 'top'], 1
        ));

        // ── TABLERO SUPERIOR: encaja ENTRE los laterales (no los supera)
        //    pw = W - 2T  (los laterales se "comen" T de cada lado)
        //    y  = H - T/2 (tope del tablero = H, su centro = H - T/2)
        parts.push(_makePart('Tablero Superior', 'top',
            W - T*2, T, D,
            0, H - T/2, 0,
            color, 1, ['front', 'left', 'right'], 2
        ));

        // ── FALDON / REFUERZO TRASERO (opcional)
        if (hasBack) {
            const faldH = Math.min(H * 0.25, 200);
            parts.push(_makePart('Faldón Trasero', 'stretcher',
                W - T*2, faldH, T,
                0, H - T - faldH/2, -D/2 + T/2,
                color, 1, ['bottom'], 2
            ));
        }

        // ── CAJONES (van apilados de abajo hacia arriba dentro del lateral)
        if (drawers > 0) {
            const cajonW = (W - T*2) * 0.40;  // 40% del ancho interno
            const drawerStartY = T + 10;        // Desde el piso + holgura
            for (let i = 0; i < drawers; i++) {
                const cy = drawerStartY + i * (drawerH + 2) + drawerH/2;

                // Frente del cajón
                parts.push(_makePart(`Frente Cajón ${i+1}`, 'drawer-front',
                    cajonW - 4, drawerH - 4, T,
                    -(W/2 - T) + cajonW/2, cy, D/2 - T/2,
                    color, 1, ['front', 'top', 'bottom', 'left', 'right'], 3
                ));
                // Lados del cajón (2 piezas iguales)
                parts.push(_makePart(`Lateral Cajón ${i+1}`, 'drawer-side',
                    D - T*2, drawerH - T*2, T,
                    0, cy, 0,   // x calculado en render porque son 2
                    color, 2, [], 3
                ));
                // Fondo del cajón
                parts.push(_makePart(`Fondo Cajón ${i+1}`, 'drawer-bottom',
                    cajonW - T*2, D - T*2, T,
                    -(W/2 - T) + cajonW/2, cy - drawerH/2 + T/2, 0,
                    color, 1, [], 3
                ));
            }
        }

    } else if (legType === '4patas') {
        const legSide = Math.max(T, 50);
        const legH = H - T;

        // ── TABLERO SUPERIOR
        parts.push(_makePart('Tablero Superior', 'top',
            W, T, D,
            0, H - T/2, 0,
            color, 1, ['front', 'back', 'left', 'right'], 1
        ));
        // ── 4 PATAS
        const offsetX = W/2 - legSide/2 - 20;
        const offsetZ = D/2 - legSide/2 - 20;
        const legY = legH/2;
        [[-1,-1],[-1,1],[1,-1],[1,1]].forEach(([sx, sz], i) => {
            parts.push(_makePart(`Pata ${['TI','TD','BI','BD'][i]}`, 'leg',
                legSide, legH, legSide,
                sx * offsetX, legY, sz * offsetZ,
                color, 1, ['front', 'back', 'left', 'right'], 1
            ));
        });

    } else { // central block
        const bW = W * 0.20;
        parts.push(_makePart('Tablero Superior', 'top',
            W, T, D,
            0, H - T/2, 0,
            color, 1, ['front', 'back', 'left', 'right'], 1
        ));
        parts.push(_makePart('Bloque Central', 'leg',
            bW, H - T, D * 0.80,
            0, (H-T)/2, 0,
            color, 1, ['front', 'back', 'left', 'right'], 1
        ));
    }

    return parts;
}

// ─── ESTANTERÍA ──────────────────────────────────────────────
function _partsEstanteria(W, H, D, T, color, shelves, shelfPositions, hasBack, hasDoor) {
    const parts = [];

    // Espacio interior disponible
    const innerW = W - T * 2;
    const innerH = H - T * 2;   // Descontamos techo y piso

    // ── LATERALES (columnas completas)
    parts.push(_makePart('Lateral Izq', 'lateral',
        T, H, D,
        -W/2 + T/2, H/2, 0,
        color, 1, ['front', 'top', 'bottom'], 1
    ));
    parts.push(_makePart('Lateral Der', 'lateral',
        T, H, D,
        W/2 - T/2, H/2, 0,
        color, 1, ['front', 'top', 'bottom'], 1
    ));

    // ── TECHO (encaja entre laterales)
    parts.push(_makePart('Techo', 'top',
        innerW, T, D,
        0, H - T/2, 0,
        color, 1, ['front', 'left', 'right'], 2
    ));

    // ── PISO (encaja entre laterales)
    parts.push(_makePart('Piso', 'bottom',
        innerW, T, D,
        0, T/2, 0,
        color, 1, ['front', 'left', 'right'], 2
    ));

    // ── FONDO TRASERO (opcional)
    if (hasBack) {
        parts.push(_makePart('Panel Trasero', 'back',
            innerW, innerH, T,
            0, T + innerH/2, -D/2 + T/2,
            color, 1, [], 2
        ));
    }

    // ── ESTANTES INTERMEDIOS
    const autoPositions = _calcShelfPositions(innerH, shelves, T);
    const positions = shelfPositions.length === shelves ? shelfPositions : autoPositions;

    positions.forEach((posY, i) => {
        // posY es la altura del PISO del estante desde el piso del mueble
        // Centro del estante = piso + T/2 del estante propio
        const shelfCenterY = posY + T/2;
        // Los estantes van de lateral a lateral pero descansan SOBRE los laterales
        // (no los atraviesan) → ancho = innerW
        parts.push(_makePart(`Estante ${i+1}`, 'shelf',
            innerW, T, D - (hasBack ? T : 0),
            0, shelfCenterY, hasBack ? T/2 : 0,
            color, 1, ['front', 'left', 'right'], 3
        ));
    });

    // ── PUERTA (opcional, cubre el vano completo)
    if (hasDoor) {
        parts.push(_makePart('Puerta', 'door',
            W, H, T,
            0, H/2, D/2 + T/2,
            color, 1, ['front', 'top', 'bottom', 'left', 'right'], 4
        ));
    }

    return parts;
}

// ─── GABINETE ────────────────────────────────────────────────
function _partsGabinete(W, H, D, T, color, shelves, drawers, drawerH, hasDoor, hasBack) {
    const parts = [];
    const innerW = W - T*2;
    const innerH = H - T*2;

    // Estructura base igual a estantería
    parts.push(_makePart('Lateral Izq', 'lateral', T, H, D, -W/2+T/2, H/2, 0, color, 1, ['front','top','bottom'], 1));
    parts.push(_makePart('Lateral Der', 'lateral', T, H, D,  W/2-T/2, H/2, 0, color, 1, ['front','top','bottom'], 1));
    parts.push(_makePart('Techo',       'top',     innerW, T, D, 0, H-T/2, 0,   color, 1, ['front','left','right'], 2));
    parts.push(_makePart('Piso',        'bottom',  innerW, T, D, 0, T/2,   0,   color, 1, ['front','left','right'], 2));
    if (hasBack) {
        parts.push(_makePart('Panel Trasero', 'back', innerW, innerH, T, 0, T+innerH/2, -D/2+T/2, color, 1, [], 2));
    }

    // Zona de cajones (desde el piso hacia arriba)
    let topOfDrawers = T; // posición Y del techo de la zona de cajones
    if (drawers > 0) {
        const totalDrawerH = drawers * (drawerH + 2) - 2;
        topOfDrawers = T + totalDrawerH;

        // Separador entre cajones y estantes
        parts.push(_makePart('Entrepaño Separador', 'shelf',
            innerW, T, D - (hasBack ? T : 0),
            0, topOfDrawers + T/2, hasBack ? T/2 : 0,
            color, 1, ['front', 'left', 'right'], 3
        ));

        // Cajones
        for (let i = 0; i < drawers; i++) {
            const cy = T + i*(drawerH+2) + drawerH/2;
            parts.push(_makePart(`Frente Cajón ${i+1}`, 'drawer-front',
                innerW - 4, drawerH - 4, T,
                0, cy, D/2 - T/2,
                color, 1, ['front','top','bottom','left','right'], 4
            ));
            parts.push(_makePart(`Lateral Cajón ${i+1}`, 'drawer-side',
                D - T*2 - 10, drawerH - T - 4, T,
                0, cy, 0, // posición X se ajusta en render (son 2 piezas)
                color, 2, [], 4
            ));
            parts.push(_makePart(`Fondo Cajón ${i+1}`, 'drawer-bottom',
                innerW - T*2, D - T*2 - 10, T,
                0, cy - drawerH/2 + T/2, 0,
                color, 1, [], 4
            ));
        }
    }

    // Estantes en la zona superior
    const shelfZoneH = innerH - (topOfDrawers - T);
    const autoPos = _calcShelfPositions(shelfZoneH, shelves, T, topOfDrawers + T);
    autoPos.forEach((posY, i) => {
        parts.push(_makePart(`Estante ${i+1}`, 'shelf',
            innerW, T, D - (hasBack ? T : 0),
            0, posY + T/2, hasBack ? T/2 : 0,
            color, 1, ['front','left','right'], 3
        ));
    });

    if (hasDoor) {
        parts.push(_makePart('Puerta', 'door', W, H, T, 0, H/2, D/2+T/2, color, 1, ['front','top','bottom','left','right'], 5));
    }

    return parts;
}

// ─── CAJONERA ────────────────────────────────────────────────
function _partsCajonera(W, H, D, T, color, drawers, drawerH, hasBack) {
    const parts = [];
    const innerW = W - T*2;
    const innerH = H - T*2;

    parts.push(_makePart('Lateral Izq', 'lateral', T, H, D, -W/2+T/2, H/2, 0, color, 1, ['front','top','bottom'], 1));
    parts.push(_makePart('Lateral Der', 'lateral', T, H, D,  W/2-T/2, H/2, 0, color, 1, ['front','top','bottom'], 1));
    parts.push(_makePart('Techo',  'top',    innerW, T, D, 0, H-T/2, 0, color, 1, ['front','left','right'], 2));
    parts.push(_makePart('Piso',   'bottom', innerW, T, D, 0, T/2,   0, color, 1, ['front','left','right'], 2));
    if (hasBack) {
        parts.push(_makePart('Panel Trasero', 'back', innerW, innerH, T, 0, T+innerH/2, -D/2+T/2, color, 1, [], 2));
    }

    const autoDrawerH = innerH / Math.max(drawers, 1);
    const usedDrawerH = drawerH || autoDrawerH;

    for (let i = 0; i < drawers; i++) {
        const cy = T + i * (usedDrawerH + 2) + usedDrawerH/2;
        parts.push(_makePart(`Frente Cajón ${i+1}`, 'drawer-front',
            innerW - 2, usedDrawerH - 4, T,
            0, cy, D/2 - T/2,
            color, 1, ['front','top','bottom','left','right'], 3
        ));
        parts.push(_makePart(`Lateral Cajón ${i+1}`, 'drawer-side',
            D - T*2 - 10, usedDrawerH - T - 4, T,
            0, cy, 0,
            color, 2, [], 3
        ));
        parts.push(_makePart(`Fondo Cajón ${i+1}`, 'drawer-bottom',
            innerW - T*2, D - T*2 - 10, T,
            0, cy - usedDrawerH/2 + T/2, 0,
            color, 1, [], 3
        ));
        // Separador entre cajones (excepto el último)
        if (i < drawers - 1) {
            const sepY = T + (i+1) * (usedDrawerH + 2) - 1;
            parts.push(_makePart(`Separador ${i+1}`, 'shelf',
                innerW, T, D - (hasBack ? T : 0),
                0, sepY, hasBack ? T/2 : 0,
                color, 1, [], 3
            ));
        }
    }

    return parts;
}

// ─── Helpers ────────────────────────────────────────────────

/** Calcula posiciones Y automáticas para los estantes */
function _calcShelfPositions(innerH, count, T, offsetY = 0) {
    if (count === 0) return [];
    const spacing = innerH / (count + 1);
    return Array.from({ length: count }, (_, i) => {
        return offsetY + spacing * (i + 1) - T/2;
    });
}

/** Factory de pieza */
function _makePart(name, role, pw, ph, pd, x, y, z, color, qty, edgeBand, step) {
    return { name, role, pw, ph, pd, x, y, z, color, qty, edgeBand, step };
}

// ─── Lista de Corte ──────────────────────────────────────────
/**
 * buildCutList(parts) → CutRow[]
 * Agrupa piezas con qty > 1 y formatea para la tabla HTML.
 */
function buildCutList(parts) {
    const rows = [];
    parts.forEach((part, i) => {
        // Omitir piezas de cajón duplicadas (qty=2 ya representa 2 unidades)
        rows.push({
            index: i + 1,
            name:  part.name,
            W:     Math.round(part.pw),
            H:     Math.round(part.ph),
            T:     Math.round(part.pd),
            qty:   part.qty,
            edgeBand: (part.edgeBand || []).join(', ') || '—',
            role:  part.role,
            step:  part.step,
        });
    });
    return rows;
}

// ─── Estimación de Costo ─────────────────────────────────────
/**
 * estimateCost(parts, config) → CostResult
 *
 * config = {
 *   sheetW        : number  — Ancho de lámina disponible (mm), default 2440
 *   sheetH        : number  — Alto de lámina disponible (mm), default 1220
 *   pricePerSheet : number  — Precio por lámina en moneda local
 *   wasteFactor   : number  — Factor de desperdicio (1.10 = 10% extra)
 *   unit          : string  — 'USD' | 'PEN' | 'ARS' etc.
 * }
 */
function estimateCost(parts, config = {}) {
    const {
        sheetW        = DEFAULT_SHEET_W,
        sheetH        = DEFAULT_SHEET_H,
        pricePerSheet = 0,
        wasteFactor   = 1.10,
        unit          = 'USD',
    } = config;

    const sheetArea = sheetW * sheetH;  // mm²
    let totalArea = 0;

    parts.forEach(part => {
        // Área de una pieza × cantidad × cara frontal+trasera (solo 1 cara = área de corte)
        totalArea += part.pw * part.ph * part.qty;
    });

    // Aplicar factor de desperdicio
    const totalAreaWithWaste = totalArea * wasteFactor;

    const sheetsNeeded = Math.ceil(totalAreaWithWaste / sheetArea);
    const totalCost    = sheetsNeeded * pricePerSheet;

    // Área útil de todas las piezas en m²
    const usefulArea_m2 = (totalArea / 1_000_000).toFixed(3);

    return {
        sheetsNeeded,
        totalCost,
        unit,
        usefulArea_m2: parseFloat(usefulArea_m2),
        pricePerSheet,
        wasteFactor,
    };
}

// ─── CSV Export ──────────────────────────────────────────────
/**
 * exportCutListCSV(parts, projectName) → void
 * Descarga la lista de corte como archivo .csv
 */
function exportCutListCSV(parts, projectName = 'mueble') {
    const rows = buildCutList(parts);
    const header = 'N°,Pieza,Ancho (mm),Alto (mm),Espesor (mm),Cantidad,Cantoneado,Paso\n';
    const body = rows.map(r =>
        `${r.index},"${r.name}",${r.W},${r.H},${r.T},${r.qty},"${r.edgeBand}",${r.step}`
    ).join('\n');

    const blob = new Blob(['\uFEFF' + header + body], { type: 'text/csv;charset=utf-8;' });
    const url  = URL.createObjectURL(blob);
    const a    = document.createElement('a');
    a.href     = url;
    a.download = `lista_corte_${projectName.replace(/\s/g,'_')}.csv`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

// ─── HERRAJES ────────────────────────────────────────────────
/**
 * generateHardware(parts, spec) → HardwareItem[]
 *
 * Calcula automáticamente las posiciones de todos los herrajes
 * necesarios según las piezas y la configuración del mueble.
 *
 * HardwareItem = {
 *   type    : 'confirmat'|'minifix'|'bisagra'|'tirador'|'taco'|'corredera'
 *   x,y,z   : posición centro en espacio 3D
 *   nx,ny,nz: normal de la cara donde se instala (dirección de perforación)
 *   label   : descripción para lista de herrajes
 *   qty     : cantidad en este punto (siempre 1; agrupar al final)
 * }
 */
function generateHardware(parts, spec) {
    const { T = 18, hasDoor = false, drawers = 0 } = spec;
    const hardware = [];

    const laterals   = parts.filter(p => p.role === 'lateral');
    const horizontals = parts.filter(p => ['top','bottom','shelf'].includes(p.role));
    const drawerFronts = parts.filter(p => p.role === 'drawer-front');
    const doors      = parts.filter(p => p.role === 'door');
    const shelves    = parts.filter(p => p.role === 'shelf');

    // ── CONFIRMATS / MINIFIX  ────────────────────────────────
    // Se colocan donde un panel horizontal se une a un lateral.
    // 2 confirmats por unión (frente y fondo del panel)
    // El confirmat penetra horizontalmente desde el lateral hacia el horizontal.
    horizontals.forEach(horiz => {
        const zOffsets = [-horiz.pd * 0.3, horiz.pd * 0.3]; // 2 puntos a lo largo de Z

        laterals.forEach(lat => {
            const side = lat.x < 0 ? -1 : 1; // -1=izq, +1=der
            const nx = side;                   // normal apunta hacia adentro

            zOffsets.forEach(dz => {
                hardware.push({
                    type: 'confirmat',
                    x: lat.x + (side * T * 0.5),
                    y: horiz.y,
                    z: horiz.z + dz,
                    nx, ny: 0, nz: 0,
                    label: `Confirmat 7×50mm en ${horiz.name} ↔ ${lat.name}`,
                    qty: 1,
                });
            });
        });
    });

    // ── TACOS DE ESTANTE (shelf pins) ───────────────────────
    // 4 tacos por estante (2 en cada lateral), a 25mm del frente/fondo
    shelves.forEach(shelf => {
        laterals.forEach(lat => {
            const side = lat.x < 0 ? -1 : 1;
            [-1, 1].forEach(dz => {
                hardware.push({
                    type: 'taco',
                    x: lat.x + (side * T * 0.5),
                    y: shelf.y,
                    z: shelf.z + dz * (shelf.pd * 0.3),
                    nx: side, ny: 0, nz: 0,
                    label: `Taco estante 5mm en ${lat.name}`,
                    qty: 1,
                });
            });
        });
    });

    // ── TIRADORES (handles) ──────────────────────────────────
    // 1 tirador centrado horizontalmente en cada frente de cajón
    drawerFronts.forEach(df => {
        hardware.push({
            type: 'tirador',
            x: df.x,
            y: df.y,
            z: df.z + df.pd / 2 + 1,  // justo en la cara frontal
            nx: 0, ny: 0, nz: 1,
            label: `Tirador de barra en ${df.name}`,
            qty: 1,
        });
    });

    // ── BISAGRAS (hinges) ────────────────────────────────────
    // 2 bisagras por puerta (a 80mm del borde superior e inferior)
    // + 1 por cada 600mm de alto adicional
    doors.forEach(door => {
        const hingeDist = 80;
        const hingeCount = Math.max(2, Math.floor(door.ph / 600));
        const hingeX = door.x - door.pw / 2 + T; // lado del lateral izquierdo
        for (let i = 0; i < hingeCount; i++) {
            const hingeY = (i === 0)
                ? door.y - door.ph/2 + hingeDist
                : (i === hingeCount - 1)
                    ? door.y + door.ph/2 - hingeDist
                    : door.y + (door.ph * (i / (hingeCount - 1))) - door.ph/2;
            hardware.push({
                type: 'bisagra',
                x: hingeX,
                y: hingeY,
                z: door.z,
                nx: -1, ny: 0, nz: 0,
                label: `Bisagra 35mm oculta en ${door.name}`,
                qty: 1,
            });
        }
    });

    // ── CORREDERAS DE CAJÓN ──────────────────────────────────
    // 1 par de correderas (izq+der) por cajón
    const drawerSides = parts.filter(p => p.role === 'drawer-side');
    const drawerNames = [...new Set(drawerSides.map(p => p.name.replace(' L','')))];
    drawerNames.forEach((name, i) => {
        const ds = drawerSides.find(p => p.name.includes(`${i+1}`));
        if (!ds) return;
        hardware.push({
            type: 'corredera',
            x: ds.x,
            y: ds.y,
            z: ds.z,
            nx: 0, ny: 0, nz: 0,
            label: `Corredera telescópica ${Math.round(ds.pw)}mm (par)`,
            qty: 2, // izquierda + derecha
        });
    });

    return hardware;
}

/**
 * buildHardwareList(hardware) → resumen agrupado para mostrar al usuario
 */
function buildHardwareList(hardware) {
    const groups = {};
    hardware.forEach(h => {
        const key = `${h.type}|${h.label}`;
        if (!groups[key]) groups[key] = { type: h.type, label: h.label, qty: 0 };
        groups[key].qty += h.qty;
    });
    return Object.values(groups).sort((a, b) => a.type.localeCompare(b.type));
}

// ─── Exportar al scope global ────────────────────────────────
window.PolloEngine = {
    version: ENGINE_VERSION,
    generateParts,
    buildCutList,
    estimateCost,
    exportCutListCSV,
    generateHardware,
    buildHardwareList,
};

console.log(`%cPOLO Engine v2.1.0 - Herrajes activos`, 'color:#4CAF50; font-weight:bold;');
