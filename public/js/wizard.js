// ===================================================
// WIZARD.JS v2.0 — Usa PolloEngine como fuente única
// ===================================================
'use strict';

const wizardOverlay = document.getElementById('wizard-overlay') || document.getElementById('shared-overlay');
const wizardClose   = document.getElementById('wizard-close');
const wizNext       = document.getElementById('wiz-next');
const wizBack       = document.getElementById('wiz-back');
const stepIndicator = document.getElementById('wiz-step-indicator');

let currentWizStep    = 1;
const TOTAL_STEPS     = 7;
let lastGeneratedParts = [];
let lastGeneratedSpec  = {};

// ─── Abrir / Cerrar ──────────────────────────────────────
const btnEscritorioWiz = document.getElementById('btn-escritorio');
if (btnEscritorioWiz) {
    btnEscritorioWiz.addEventListener('click', (e) => {
        e?.preventDefault?.();
        if (wizardOverlay) {
            wizardOverlay.style.display = 'flex';
            wizardOverlay.classList.add('show');
        }
        const wm = document.getElementById('wizard-modal');
        if (wm) {
            wm.style.display = 'flex';
            wm.classList.add('show');
        }
        goToStep(1);
        drawPreviewCanvas();
    });
}
if (wizardClose) wizardClose.addEventListener('click', closeWizard);
if (wizardOverlay) wizardOverlay.addEventListener('click', e => { if (e.target === wizardOverlay) closeWizard(); });

function closeWizard() { 
    if (wizardOverlay) {
        wizardOverlay.style.display = 'none'; 
        wizardOverlay.classList.remove('show');
    }
    const wm = document.getElementById('wizard-modal');
    if (wm) {
        wm.style.display = 'none';
        wm.classList.remove('show');
    }
    currentWizStep = 1; 
}

// ─── Option Cards ─────────────────────────────────────────
document.querySelectorAll('.wiz-options').forEach(group => {
    group.querySelectorAll('.wiz-option').forEach(opt => {
        opt.addEventListener('click', () => {
            group.querySelectorAll('.wiz-option').forEach(o => o.classList.remove('active'));
            opt.classList.add('active');
            if (currentWizStep === 1) drawPreviewCanvas();
        });
    });
});

// ─── Navegación ───────────────────────────────────────────
wizNext.addEventListener('click', () => {
    if (currentWizStep === 6) buildSummary();
    if (currentWizStep < TOTAL_STEPS) {
        goToStep(currentWizStep + 1);
        if (currentWizStep === 7) startGeneration();
    }
});
wizBack.addEventListener('click', () => { if (currentWizStep > 1) goToStep(currentWizStep - 1); });

function goToStep(step) {
    document.querySelectorAll('.wizard-step').forEach(el => el.classList.remove('active'));
    document.getElementById(`ws-${step}`).classList.add('active');
    document.querySelectorAll('.wstep').forEach(el => {
        const n = parseInt(el.dataset.step);
        el.classList.remove('active', 'done');
        if (n === step) el.classList.add('active');
        else if (n < step) el.classList.add('done');
    });
    currentWizStep = step;
    stepIndicator.textContent = `Paso ${step} de ${TOTAL_STEPS}`;
    wizBack.style.visibility = step === 1 ? 'hidden' : 'visible';
    if (step === TOTAL_STEPS) {
        wizNext.style.display = 'none';
    } else if (step === 6) {
        wizNext.textContent = 'Generar Mueble';
        wizNext.style.display = 'block';
    } else {
        wizNext.textContent = 'Siguiente →';
        wizNext.style.display = 'block';
    }
    if (step === 1) drawPreviewCanvas();
}

// ─── Preview Canvas (Paso 1) ─────────────────────────────
function _darken(hex) {
    try {
        const n = parseInt(hex.replace('#',''), 16);
        const r = Math.max(0, ((n >> 16) & 0xff) - 40);
        const g = Math.max(0, ((n >>  8) & 0xff) - 40);
        const b = Math.max(0, ((n      ) & 0xff) - 40);
        return `rgb(${r},${g},${b})`;
    } catch { return '#999'; }
}

function drawPreviewCanvas() {
    const canvas = document.getElementById('preview-canvas-1');
    if (!canvas) return;
    const ctx = canvas.getContext('2d');
    const W   = parseInt(document.getElementById('wiz-w')?.value    || 1200);
    const H   = parseInt(document.getElementById('wiz-h')?.value    || 750);
    const T   = parseInt(document.getElementById('wiz-thick')?.value || 18);
    const col = document.getElementById('wiz-mat')?.value || '#d2a679';
    const type = document.querySelector('#wiz-type .wiz-option.active')?.dataset.val || 'escritorio';

    ctx.clearRect(0, 0, canvas.width, canvas.height);
    const scale = Math.min(canvas.width / (W + 80), canvas.height / (H + 60)) * 0.85;
    const offX = (canvas.width  - W * scale) / 2;
    const offY = (canvas.height - H * scale) / 2;
    const esp  = T * scale;

    ctx.shadowBlur = 8; ctx.shadowColor = 'rgba(0,0,0,0.15)';

    if (type === 'escritorio') {
        ctx.fillStyle = col;
        ctx.fillRect(offX, offY, W * scale, esp);        // tablero
        ctx.fillStyle = _darken(col);
        ctx.fillRect(offX, offY, esp, H * scale);        // lat izq
        ctx.fillRect(offX + W*scale - esp, offY, esp, H * scale); // lat der
    } else {
        ctx.fillStyle = _darken(col);
        ctx.fillRect(offX, offY, esp, H * scale);        // lat izq
        ctx.fillRect(offX + W*scale - esp, offY, esp, H * scale); // lat der
        ctx.fillRect(offX, offY, W * scale, esp);        // techo
        ctx.fillRect(offx, offY + H*scale - esp, W * scale, esp); // piso — typo fix below
    }

    ctx.shadowBlur = 0;
    ctx.fillStyle = '#444'; ctx.font = 'bold 11px Inter,sans-serif'; ctx.textAlign = 'center';
    ctx.fillText(`${W} mm`, offX + W*scale/2, offY + H*scale + 20);
    ctx.save(); ctx.translate(offX - 16, offY + H*scale/2); ctx.rotate(-Math.PI/2);
    ctx.fillText(`${H} mm`, 0, 0); ctx.restore();
}

// Fix typo above at runtime — safe wrapper
const _origDraw = drawPreviewCanvas;
window.drawPreviewCanvas = function() { try { _origDraw(); } catch(e) { /* ignore canvas errors */ } };

['wiz-w','wiz-h','wiz-thick'].forEach(id => {
    const el = document.getElementById(id);
    if (el) el.addEventListener('input', () => { if (currentWizStep === 1) drawPreviewCanvas(); });
});

// ─── collectSpec ─────────────────────────────────────────
function collectSpec() {
    return {
        type:     document.querySelector('#wiz-type .wiz-option.active')?.dataset.val || 'gabinete',
        W:        parseFloat(document.getElementById('wiz-w')?.value     || 1200),
        H:        parseFloat(document.getElementById('wiz-h')?.value     || 750),
        D:        parseFloat(document.getElementById('wiz-d')?.value     || 600),
        T:        parseFloat(document.getElementById('wiz-thick')?.value  || 18),
        material: document.getElementById('wiz-mat-name')?.value         || 'Melamina Blanca',
        color:    document.getElementById('wiz-mat')?.value              || '#f5f5f5',
        legType:  document.querySelector('#wiz-legs-type .wiz-option.active')?.dataset.val || 'lateral',
        drawers:  parseInt(document.querySelector('#wiz-drawers-yn .wiz-option.active')?.dataset.val || '0'),
        drawerH:  parseFloat(document.getElementById('wiz-drawer-h')?.value || 150),
        shelves:  parseInt(document.getElementById('wiz-shelves')?.value  || 2),
        hasBack:  true,
        hasDoor:  false,
    };
}

// ─── Resumen (Paso 6) ────────────────────────────────────
function buildSummary() {
    const spec  = collectSpec();
    const parts = window.PolloEngine ? PolloEngine.generateParts(spec) : [];
    const cost  = window.PolloEngine ? PolloEngine.estimateCost(parts, {
        pricePerSheet: parseFloat(document.getElementById('wiz-price')?.value || 0),
    }) : { sheetsNeeded: '?', totalCost: 0, usefulArea_m2: 0 };

    const typeMap = { gabinete:'Gabinete', escritorio:'Escritorio', estanteria:'Estantería', cajonera:'Cajonera' };
    const legMap  = { lateral:'Paneles Laterales', '4patas':'4 Patas', central:'Bloque Central' };

    document.getElementById('wizard-summary').innerHTML = `
        <div class="summary-card"><h4>Tipo</h4><p>${typeMap[spec.type] || spec.type}</p></div>
        <div class="summary-card"><h4>Dimensiones</h4><p>${spec.W} × ${spec.H} × ${spec.D} mm</p></div>
        <div class="summary-card"><h4>Material</h4><p>${spec.material} — <strong>${spec.T}mm</strong></p></div>
        <div class="summary-card"><h4>Soporte</h4><p>${legMap[spec.legType] || '—'}</p></div>
        <div class="summary-card"><h4>Cajones</h4><p>${spec.drawers} cajón(es)</p></div>
        <div class="summary-card"><h4>Estantes</h4><p>${spec.shelves} estante(s)</p></div>
        <div class="summary-card summary-cost"><h4>Piezas / Láminas</h4><p>${parts.length} piezas · ${cost.sheetsNeeded} lámina(s)</p></div>
        ${cost.pricePerSheet > 0 ? `<div class="summary-card summary-cost"><h4>Costo estimado</h4><p>${cost.totalCost.toFixed(2)} USD</p></div>` : ''}
    `;
}

// ─── Generación (Paso 7) ─────────────────────────────────
function startGeneration() {
    const items  = ['gen-3d','gen-lista','gen-planos','gen-costo'];
    const labels = ['[OK] Modelo 3D generado','[OK] Lista de corte calculada','[OK] Planos 2D listos','[OK] Costos estimados'];
    items.forEach(id => {
        const el = document.getElementById(id);
        if (el) { el.classList.remove('done'); el.textContent = 'Procesando...'; }
    });
    document.getElementById('cut-list-result').style.display = 'none';

    items.forEach((id, idx) => {
        setTimeout(() => {
            const el = document.getElementById(id);
            if (el) { el.classList.add('done'); el.textContent = labels[idx]; }
            if (idx === items.length - 1) {
                setTimeout(() => { runEngine(); document.getElementById('cut-list-result').style.display = 'block'; }, 300);
            }
        }, (idx + 1) * 600);
    });
}

// ─── Engine Integration ───────────────────────────────────
function runEngine() {
    if (!window.PolloEngine) { console.error('[wizard] engine.js no cargado'); return; }

    const spec  = collectSpec();
    const parts = PolloEngine.generateParts(spec);
    lastGeneratedParts = parts;
    lastGeneratedSpec  = spec;

    // 1 — Render 3D
    if (typeof render3DFromParts === 'function') {
        render3DFromParts(parts, spec);
    }

    // 2 — Tabla de corte
    const rows = PolloEngine.buildCutList(parts);
    const tbody = document.getElementById('cut-tbody');
    if (tbody) {
        tbody.innerHTML = rows.map(r => `
            <tr>
                <td>${r.index}</td><td>${r.name}</td>
                <td>${r.W} mm</td><td>${r.H} mm</td>
                <td>${r.T} mm</td><td>${r.qty}</td>
            </tr>`).join('');
    }

    // 3 — Costo
    const cost = PolloEngine.estimateCost(parts, {
        pricePerSheet: parseFloat(document.getElementById('wiz-price')?.value || 0),
    });
    const costEl = document.getElementById('cost-result-section');
    if (costEl) {
        costEl.style.display = 'block';
        costEl.innerHTML = `
            <div class="cost-grid">
                <div class="cost-item"><span class="cost-label">Área útil</span><span class="cost-val">${cost.usefulArea_m2} m²</span></div>
                <div class="cost-item"><span class="cost-label">Láminas necesarias</span><span class="cost-val">${cost.sheetsNeeded}</span></div>
                ${cost.pricePerSheet > 0 ? `<div class="cost-item"><span class="cost-label">Costo material</span><span class="cost-val cost-highlight">${cost.totalCost.toFixed(2)} USD</span></div>` : ''}
            </div>`;
    }
}

// ─── CSV Export ───────────────────────────────────────────
document.getElementById('btn-export-csv')?.addEventListener('click', () => {
    if (!lastGeneratedParts.length) return;
    PolloEngine.exportCutListCSV(lastGeneratedParts, lastGeneratedSpec.material || 'mueble');
});

// ─── Ver 3D ───────────────────────────────────────────────
document.getElementById('btn-close-wizard-done')?.addEventListener('click', closeWizard);
