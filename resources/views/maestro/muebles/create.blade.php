@extends('layouts.app')

@section('content')
<style>
    /* ══ Base layout ══════════════════════════════════════════ */
    html, body {
        overflow: hidden !important; /* Force no scroll on the main page */
        height: 100vh !important;
        margin: 0;
        display: flex;
        flex-direction: column;
    }
    
    /* Make the main content area take all remaining height */
    #app, main {
        display: flex;
        flex-direction: column;
        flex: 1;
        min-height: 0;
    }

    :root {
        --primary: #D97706;
        --border-col: #E5E7EB;
        --border: 2px solid var(--border-col);
        --radius: 8px;
        --panel-w: 280px;
    }

    /* The designer fills the flex container completely */
    .designer-wrap {
        display: flex;
        width: 100%;
        flex: 1;
        min-height: 0;
        border: var(--border);
        border-radius: var(--radius);
        overflow: hidden;
        background: #fff;
    }

    /* ══ Left Panel ════════════════════════════════════════════ */
    .panel {
        width: var(--panel-w);
        min-width: var(--panel-w);
        max-width: var(--panel-w);
        background: #fff;
        border-right: var(--border);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        height: 100%;
        flex-shrink: 0;
    }

    /* Top section: Elementos / Biblioteca */
    .panel-section-top {
        display: flex;
        flex-direction: column;
        flex: 1 1 45%;
        max-height: 50%;
        overflow: hidden;
        min-height: 200px;
        border-bottom: var(--border);
    }

    /* Bottom section: Parámetros / Materiales / Herrajes */
    .panel-section-bottom {
        display: flex;
        flex-direction: column;
        flex: 1 1 55%;
        overflow: hidden;
        min-height: 250px;
    }

    /* ══ Tabs ══════════════════════════════════════════════════ */
    .tabs {
        display: flex;
        border-bottom: var(--border);
        background: #f8f9fa;
        flex-shrink: 0;
    }
    .tab-btn {
        flex: 1;
        padding: 9px 4px;
        background: none;
        border: none;
        border-bottom: 2px solid transparent;
        font-weight: 600;
        font-size: 0.78rem;
        color: #6B7280;
        cursor: pointer;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .tab-btn.active {
        border-bottom: 2px solid var(--primary);
        color: var(--primary);
        background: #fff;
    }

    /* ══ Tab content areas — scrollable ═══════════════════════ */
    .tab-content {
        display: none;
        flex: 1 1 0;
        min-height: 0;
        overflow-y: auto;
        overflow-x: hidden;
        padding: 10px;
    }
    .tab-content.active {
        display: flex;
        flex-direction: column;
    }

    .module-item {
        padding: 12px;
        margin-bottom: 8px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 10px;
        border-radius: 6px;
        border: 1px solid #E5E7EB;
        transition: all 0.2s;
    }
    .module-item:hover {
        background: #f3f4f6;
        border-color: #D1D5DB;
    }
    .canvas-container {
        flex: 1;
        position: relative;
        background: #F3F4F6;
    }
    #three-canvas {
        width: 100%;
        height: 100%;
    }
    .canvas-ui-right-floating {
        position: absolute;
        top: 20px;
        right: 20px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        z-index: 5;
        background: rgba(255,255,255,0.9);
        padding: 10px;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
    }
    .tool-btn, .view-btn {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        background: #fff;
        border: 1px solid #E5E7EB;
        cursor: pointer;
        font-weight: 700;
        font-size: 11px;
        color: #374151;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .tool-btn.active, .view-btn.active {
        border: 2px solid var(--primary);
        color: var(--primary);
    }
    
    /* Configuración Corte Modal */
    .corte-modal {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 90%;
        max-width: 900px;
        background: #fff;
        border-radius: 12px;
        z-index: 2050;
        box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
        display: none;
        flex-direction: column;
        max-height: 90vh;
    }
    .corte-modal.show {
        display: flex;
    }
    .wizard-overlay {
        position: fixed;
        inset: 0;
        background: rgba(17,24,39,0.7);
        z-index: 2040;
        display: none;
    }
    .wizard-overlay.show {
        display: block;
    }
    
    /* Wizard Modal */
    .wizard-modal {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: #fff;
        border-radius: 12px;
        width: min(700px, 95vw);
        max-height: 90vh;
        z-index: 2100;
        display: none;
        flex-direction: column;
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
    }
    .wizard-modal.show {
        display: flex;
    }
    .wizard-header {
        padding: 16px 20px;
        border-bottom: 1px solid #E5E7EB;
        background: #F9FAFB;
        border-radius: 12px 12px 0 0;
    }
    .wizard-step {
        display: none;
        padding: 24px;
        overflow-y: auto;
    }
    .wizard-step.active {
        display: block;
    }
    .wiz-options {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    .wiz-option {
        flex: 1;
        min-width: 120px;
        border: 2px solid #E5E7EB;
        border-radius: 8px;
        padding: 12px;
        text-align: center;
        cursor: pointer;
    }
    .wiz-option.active {
        border-color: var(--primary);
        background: rgba(217,119,6,0.1);
        font-weight: 700;
    }
    .material-card {
        border: 2px solid #E5E7EB !important;
        transition: all 0.2s ease;
    }
    .material-card:hover {
        border-color: #d97706 !important;
        transform: translateY(-2px);
    }
    .material-card.active {
        border-color: #d97706 !important;
        background: #fffbeb !important;
        box-shadow: 0 4px 8px -1px rgba(217, 119, 6, 0.2);
    }
</style>

<div class="container-fluid p-2 d-flex flex-column flex-grow-1" style="min-height: 0;">
    <!-- Top Bar -->
    <div class="d-flex justify-content-between align-items-center mb-2 flex-shrink-0">
        <div>
            <h4 class="fw-bold text-dark mb-0">
                {{ isset($mueble) ? 'Editando Mueble' : 'Diseñador 3D' }}
            </h4>
            <div class="d-flex align-items-center gap-2 mt-2">
                <span class="badge {{ isset($mueble) ? 'bg-warning text-dark' : 'bg-secondary' }}">Proyecto</span>
                <input type="text" id="project-name-input" class="form-control form-control-sm border-0 fw-bold" style="width: 240px; background: transparent"
                    value="{{ isset($mueble) ? $mueble->nombre : 'Mydo Mar' }}"
                    placeholder="Nombre del proyecto">
            </div>
        </div>
        <div class="d-flex gap-3 align-items-center">
            <a href="{{ route('maestro.dashboard') }}" class="btn btn-light border fw-bold text-secondary px-3 py-2 rounded-2">Cancelar</a>
            <button type="button" id="btn-submit-mueble" class="btn btn-dark fw-bold text-white px-4 py-2 rounded-2 shadow-sm">
                {{ isset($mueble) ? 'Guardar Cambios' : 'Guardar y Publicar' }}
            </button>

            <button type="button" class="btn btn-warning fw-bold text-dark px-4 py-2 rounded-2 shadow-sm ms-2" id="btn-open-corte">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="me-2">
                    <path d="M14.5 10c-.83 0-1.5-.67-1.5-1.5v-5c0-.83.67-1.5 1.5-1.5s1.5.67 1.5 1.5v5c0 .83-.67 1.5-1.5 1.5z"/>
                </svg>
                Configurar Corte
            </button>
        </div>
    </div>

    <!-- Main Designer Area -->
    <div class="designer-wrap">
        <!-- Left Panel -->
        <div class="panel">
            <div class="panel-section-top">
            <div class="tabs">
                <button type="button" class="tab-btn active" data-target="elementos">Elementos</button>
                <button type="button" class="tab-btn" data-target="biblioteca">Biblioteca</button>
            </div>
            
            <div class="tab-content active" id="elementos">
                <div class="module-item" id="btn-habitacion">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-2"><path d="M4 8l8-4 8 4v8l-8 4-8-4z"/><path d="M12 4v8"/><path d="M4 8l8 4"/><path d="M20 8l-8 4"/></svg>
                    Habitación
                </div>
                <div class="module-item" id="btn-vertical">
                    <div style="border: 2px solid #374151; width: 16px; height: 16px; margin-right: 10px;"></div>
                    Vertical
                </div>
                <div class="module-item" id="btn-horizontal">
                    <div style="border-top: 2px solid #374151; border-bottom: 2px solid #374151; width: 16px; height: 8px; margin-right: 10px;"></div>
                    Horizontal
                </div>
                <div class="module-item">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/></svg>
                    Figura libre
                </div>
                <div class="module-item" id="btn-add-puerta-param">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="9" y1="3" x2="9" y2="21"/></svg>
                    Puerta Paramétrica
                </div>
                <div class="module-item" id="btn-add-cajon-param">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="12" x2="21" y2="12"/></svg>
                    Cajón Paramétrico
                </div>
            </div>
            
            <div class="tab-content" id="biblioteca">
                <div class="small fw-bold text-muted text-uppercase mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">Muebles de Ejemplo Simples</div>
                
                <div class="module-item mb-2 d-flex align-items-center gap-2 p-2 rounded-3 border" id="btn-ejemplo-escritorio" role="button" title="Cargar Escritorio Básico">
                    <div class="d-flex align-items-center justify-content-center rounded-2 bg-light text-warning p-2" style="width: 38px; height: 38px; flex-shrink: 0;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="5" width="20" height="4" rx="1"/>
                            <path d="M4 9v11M20 9v11M14 9v11M14 13h6M14 17h6"/>
                        </svg>
                    </div>
                    <div>
                        <div class="fw-bold text-dark" style="font-size: 0.85rem;">Escritorio Básico</div>
                        <div class="text-muted" style="font-size: 0.72rem;">1200 × 600 × 750 mm</div>
                    </div>
                </div>

                <div class="module-item mb-2 d-flex align-items-center gap-2 p-2 rounded-3 border" id="btn-ejemplo-estanteria" role="button" title="Cargar Estantería 4 Niveles">
                    <div class="d-flex align-items-center justify-content-center rounded-2 bg-light text-primary p-2" style="width: 38px; height: 38px; flex-shrink: 0;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="4" y="2" width="16" height="20" rx="1"/>
                            <path d="M4 7h16M4 12h16M4 17h16"/>
                        </svg>
                    </div>
                    <div>
                        <div class="fw-bold text-dark" style="font-size: 0.85rem;">Estantería / Librero</div>
                        <div class="text-muted" style="font-size: 0.72rem;">600 × 300 × 1200 mm</div>
                    </div>
                </div>

                <div class="module-item mb-2 d-flex align-items-center gap-2 p-2 rounded-3 border" id="btn-ejemplo-gabinete" role="button" title="Cargar Gabinete Bajo TV">
                    <div class="d-flex align-items-center justify-content-center rounded-2 bg-light text-success p-2" style="width: 38px; height: 38px; flex-shrink: 0;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="6" width="20" height="12" rx="2"/>
                            <line x1="12" y1="6" x2="12" y2="18"/>
                            <line x1="2" y1="12" x2="22" y2="12"/>
                            <path d="M6 18v2M18 18v2"/>
                        </svg>
                    </div>
                    <div>
                        <div class="fw-bold text-dark" style="font-size: 0.85rem;">Gabinete Bajo TV</div>
                        <div class="text-muted" style="font-size: 0.72rem;">1000 × 400 × 550 mm</div>
                    </div>
                </div>

                <div class="module-item mb-2 d-flex align-items-center gap-2 p-2 rounded-3 border" id="btn-ejemplo-mesa" role="button" title="Cargar Mesa de Centro">
                    <div class="d-flex align-items-center justify-content-center rounded-2 bg-light text-secondary p-2" style="width: 38px; height: 38px; flex-shrink: 0;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <ellipse cx="12" cy="7" rx="9" ry="3"/>
                            <path d="M3 7v2c0 1.66 4.03 3 9 3s9-1.34 9-3V7"/>
                            <path d="M5 9v11M19 9v11"/>
                        </svg>
                    </div>
                    <div>
                        <div class="fw-bold text-dark" style="font-size: 0.85rem;">Mesa de Centro</div>
                        <div class="text-muted" style="font-size: 0.72rem;">800 × 500 × 450 mm</div>
                    </div>
                </div>

                <div class="module-item mb-2 d-flex align-items-center gap-2 p-2 rounded-3 border" id="btn-ejemplo-puerta" role="button" title="Cargar Gabinete con Puerta y Bisagras">
                    <div class="d-flex align-items-center justify-content-center rounded-2 bg-light text-info p-2" style="width: 38px; height: 38px; flex-shrink: 0;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="3" width="18" height="18" rx="2"/>
                            <path d="M9 3v18"/>
                            <circle cx="15" cy="12" r="1.5" fill="currentColor"/>
                        </svg>
                    </div>
                    <div>
                        <div class="fw-bold text-dark" style="font-size: 0.85rem;">Gabinete con Puerta y Bisagras</div>
                        <div class="text-muted" style="font-size: 0.72rem;">Con 2 bisagras 35mm y jalador</div>
                    </div>
                </div>

                <div class="module-item mb-3 d-flex align-items-center gap-2 p-2 rounded-3 border" id="btn-ejemplo-cajon" role="button" title="Cargar Módulo con Cajón y Rieles">
                    <div class="d-flex align-items-center justify-content-center rounded-2 bg-light text-danger p-2" style="width: 38px; height: 38px; flex-shrink: 0;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="4" width="18" height="16" rx="2"/>
                            <line x1="3" y1="12" x2="21" y2="12"/>
                            <line x1="10" y1="8" x2="14" y2="8"/>
                            <line x1="10" y1="16" x2="14" y2="16"/>
                        </svg>
                    </div>
                    <div>
                        <div class="fw-bold text-dark" style="font-size: 0.85rem;">Módulo Cajón con Rieles</div>
                        <div class="text-muted" style="font-size: 0.72rem;">Con correderas telescópicas</div>
                    </div>
                </div>

                <div class="border-top pt-3 mt-1">
                    <div class="small text-muted mb-2" style="font-size: 0.75rem;">¿Deseas personalizar paso a paso?</div>
                    <button type="button" class="btn btn-outline-warning text-dark w-100 fw-bold btn-sm shadow-sm" id="btn-escritorio">
                        Asistente Avanzado
                    </button>
                </div>
            </div>
            </div><!-- /.panel-section-top -->

            <div class="panel-section-bottom">
            <div class="tabs">
                <button type="button" class="tab-btn active" data-target="parametros">Parámetros</button>
                <button type="button" class="tab-btn" data-target="materiales">Materiales</button>
                <button type="button" class="tab-btn" data-target="herrajes">Herrajes</button>
                <select id="unit-select" class="form-select form-select-sm border-0 bg-transparent text-muted" style="width: 55px; align-self: center; font-size: 0.75rem;">
                    <option value="mm">mm</option>
                    <option value="cm">cm</option>
                    <option value="in">in</option>
                    <option value="ft">ft</option>
                </select>
            </div>

            <div class="tab-content active" id="parametros" style="border-top: 1px solid #E5E7EB">
                <div class="d-flex gap-2 mb-2">
                    <button type="button" class="btn btn-light border flex-fill fw-bold text-danger" id="btn-eliminar">Eliminar</button>
                    <button type="button" class="btn btn-light border flex-fill fw-bold d-flex align-items-center justify-content-center gap-2" id="btn-copiar">
                        <img src="{{ asset('icons/imgCopiar.png') }}" width="18" height="18" alt="Copiar" style="object-fit: contain;">
                        Copiar
                    </button>
                </div>
                <div class="mb-3">
                    <button type="button" class="btn btn-secondary border w-100 fw-bold d-flex align-items-center justify-content-center gap-2" id="btn-agrupar">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 9.9-1"/></svg>
                        <span id="btn-agrupar-text">Bloquear / Agrupar Todo</span>
                    </button>
                </div>
                
                <div class="row g-2 mb-3 text-center">
                    <div class="col-4">
                        <label id="lbl-w" class="small text-muted mb-1">Grosor</label>
                        <input type="number" id="inp-w" class="form-control form-control-sm text-center" value="0" min="1" step="1">
                    </div>
                    <div class="col-4">
                        <label id="lbl-h" class="small text-muted mb-1">Alto</label>
                        <input type="number" id="inp-h" class="form-control form-control-sm text-center" value="0" min="1" step="1">
                    </div>
                    <div class="col-4">
                        <label id="lbl-d" class="small text-muted mb-1">Ancho</label>
                        <input type="number" id="inp-d" class="form-control form-control-sm text-center" value="0" min="1" step="1">
                    </div>
                </div>
                
                <div id="module-params-container" class="mb-3 p-2 bg-light rounded border border-light" style="display: none;">
                    <div id="module-params-title" class="small fw-bold text-dark mb-2">Parámetros del Módulo</div>
                    <div class="row g-2 mb-2 text-center">
                        <div class="col-4" id="col-cajon-h">
                            <label class="small text-muted mb-1">Alto Interior</label>
                            <input type="number" id="inp-cajon-h" class="form-control form-control-sm text-center" value="150" min="50" step="1">
                        </div>
                        <div class="col-4" id="col-cajon-qty">
                            <label class="small text-muted mb-1">Cantidad</label>
                            <input type="number" id="inp-cajon-cantidad" class="form-control form-control-sm text-center" value="1" min="1" max="8" step="1">
                        </div>
                        <div class="col-4" id="col-puerta-orientacion" style="display: none;">
                            <label class="small text-muted mb-1">Apertura</label>
                            <select id="sel-puerta-orientacion" class="form-select form-select-sm">
                                <option value="izq">Izquierda</option>
                                <option value="der">Derecha</option>
                                <option value="aba">Abatible (Arriba)</option>
                            </select>
                        </div>
                        <div class="col-4" id="col-frente-tipo">
                            <label class="small text-muted mb-1">Frente</label>
                            <select id="sel-frente-tipo" class="form-select form-select-sm">
                                <option value="externo">Externo</option>
                                <option value="interno">Interno</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-2 text-center" id="module-overlap-row">
                        <div class="col-12">
                            <label class="small text-muted mb-1">Solape Exterior (mm)</label>
                            <input type="number" id="inp-cajon-solape" class="form-control form-control-sm text-center" value="10" min="0" step="1">
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-dark w-100 fw-bold" id="btn-guardar-medidas">Guardar Cambios</button>
            </div>

            <div class="tab-content" id="materiales" style="border-top: 1px solid #E5E7EB; padding-top: 15px;">
                <div class="small fw-bold text-dark mb-2">Acabados de Melamina y Maderas</div>
                <div class="row g-2 mb-3" id="material-grid">
                    <div class="col-6">
                        <div class="material-card active rounded-3 p-2 text-center" data-texture="roble" style="cursor: pointer; background: #fff;">
                            <div class="rounded-2 mb-1 overflow-hidden" style="height: 44px; border: 1px solid #e5e7eb;">
                                <img src="{{ asset('textures/maderado.jpg') }}" alt="Roble Natural" class="w-100 h-100 object-fit-cover">
                            </div>
                            <div class="small fw-bold text-truncate" style="font-size: 11px;">Roble Natural</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="material-card rounded-3 p-2 text-center" data-texture="nogal" style="cursor: pointer; background: #fff;">
                            <div class="rounded-2 mb-1 overflow-hidden" style="height: 44px; border: 1px solid #e5e7eb;">
                                <img src="{{ asset('textures/nogal.jpg') }}" alt="Nogal Oscuro" class="w-100 h-100 object-fit-cover">
                            </div>
                            <div class="small fw-bold text-truncate" style="font-size: 11px;">Nogal Oscuro</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="material-card rounded-3 p-2 text-center" data-texture="blanca" style="cursor: pointer; background: #fff;">
                            <div class="rounded-2 mb-1 overflow-hidden" style="height: 44px; border: 1px solid #e5e7eb;">
                                <img src="{{ asset('textures/maple.jpg') }}" alt="Blanca Lisa" class="w-100 h-100 object-fit-cover">
                            </div>
                            <div class="small fw-bold text-truncate" style="font-size: 11px;">Blanca Lisa</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="material-card rounded-3 p-2 text-center" data-texture="grafito" style="cursor: pointer; background: #fff;">
                            <div class="rounded-2 mb-1 overflow-hidden" style="height: 44px; border: 1px solid #e5e7eb;">
                                <img src="{{ asset('textures/plateado-texturizado.jpg') }}" alt="Gris Grafito" class="w-100 h-100 object-fit-cover">
                            </div>
                            <div class="small fw-bold text-truncate" style="font-size: 11px;">Gris Grafito</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="material-card rounded-3 p-2 text-center" data-texture="marmol" style="cursor: pointer; background: #fff;">
                            <div class="rounded-2 mb-1 overflow-hidden" style="height: 44px; border: 1px solid #e5e7eb;">
                                <img src="{{ asset('textures/plateado-texturizado.jpg') }}" alt="Marmol Calacatta" class="w-100 h-100 object-fit-cover">
                            </div>
                            <div class="small fw-bold text-truncate" style="font-size: 11px;">Marmol Calacatta</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="material-card rounded-3 p-2 text-center" data-texture="haya" style="cursor: pointer; background: #fff;">
                            <div class="rounded-2 mb-1 overflow-hidden" style="height: 44px; border: 1px solid #e5e7eb;">
                                <img src="{{ asset('textures/maderado.jpg') }}" alt="Haya Madera" class="w-100 h-100 object-fit-cover">
                            </div>
                            <div class="small fw-bold text-truncate" style="font-size: 11px;">Haya Madera</div>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-column gap-2 pt-2 border-top">
                    <button type="button" class="btn btn-outline-dark btn-sm fw-bold w-100 text-start d-flex align-items-center justify-content-between" id="btn-aplicar-pieza">
                        <span>Aplicar a Pieza Seleccionada</span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                    </button>
                    <button type="button" class="btn btn-warning btn-sm fw-bold w-100 text-dark text-start d-flex align-items-center justify-content-between shadow-sm" id="btn-aplicar-todo">
                        <span>Aplicar a Todo el Mueble</span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                    </button>
                </div>
            </div>

            <div class="tab-content" id="herrajes" style="border-top: 1px solid #E5E7EB; padding-top: 14px;">
                <div class="small fw-bold text-dark mb-1">Acabado de los Herrajes</div>
                <div class="d-flex gap-2 mb-3">
                    <button type="button" class="btn btn-sm btn-outline-dark flex-fill fw-bold active" id="btn-herraje-cromo">
                        Acero Inox
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-dark flex-fill fw-bold" id="btn-herraje-negro">
                        Negro Mate
                    </button>
                </div>

                <div class="small fw-bold text-muted text-uppercase mb-2" style="font-size: 0.72rem; letter-spacing: 0.5px;">Acciones en Pieza Seleccionada</div>
                
                <div class="d-flex flex-column gap-2 mb-3">
                    <button type="button" class="btn btn-light border btn-sm fw-bold text-start d-flex align-items-center justify-content-between p-2" id="btn-add-bisagras">
                        <div>
                            <div class="fw-bold text-dark" style="font-size: 0.8rem;">Añadir Bisagras Cazoleta</div>
                            <div class="text-muted" style="font-size: 0.7rem;">2 bisagras 35mm a 100mm de bordes</div>
                        </div>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M3 12h6M15 12h6"/></svg>
                    </button>

                    <button type="button" class="btn btn-light border btn-sm fw-bold text-start d-flex align-items-center justify-content-between p-2" id="btn-add-jalador-barra">
                        <div>
                            <div class="fw-bold text-dark" style="font-size: 0.8rem;">Añadir Jalador Tubular</div>
                            <div class="text-muted" style="font-size: 0.7rem;">Tirador moderno 160mm</div>
                        </div>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="6" y="2" width="12" height="20" rx="2"/></svg>
                    </button>

                    <button type="button" class="btn btn-light border btn-sm fw-bold text-start d-flex align-items-center justify-content-between p-2" id="btn-add-jalador-pomo">
                        <div>
                            <div class="fw-bold text-dark" style="font-size: 0.8rem;">Añadir Jalador Pomo</div>
                            <div class="text-muted" style="font-size: 0.7rem;">Pomo redondo minimalista</div>
                        </div>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="6"/></svg>
                    </button>

                    <button type="button" class="btn btn-light border btn-sm fw-bold text-start d-flex align-items-center justify-content-between p-2" id="btn-add-rieles">
                        <div>
                            <div class="fw-bold text-dark" style="font-size: 0.8rem;">Añadir Rieles Telescópicos</div>
                            <div class="text-muted" style="font-size: 0.7rem;">Correderas laterales (13mm/lado)</div>
                        </div>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>

                    <button type="button" class="btn btn-outline-danger btn-sm fw-bold w-100 mt-1" id="btn-quitar-herrajes">
                        Quitar Herrajes de Pieza
                    </button>
                </div>
            </div><!-- /.tab-content #herrajes -->
            </div><!-- /.panel-section-bottom -->
        </div><!-- /.panel -->

        <!-- Canvas Area -->
        <div class="canvas-container">
            <div class="canvas-ui-right-floating">
                <button type="button" class="tool-btn active" id="tool-move" title="Mover">
                    <img src="{{ asset('icons/imgMovimiento.png') }}" width="22" height="22" alt="Mover" style="object-fit: contain;">
                </button>
                <button type="button" class="tool-btn" id="tool-rotate" title="Rotar">
                    <img src="{{ asset('icons/imgRotar.png') }}" width="22" height="22" alt="Rotar" style="object-fit: contain;">
                </button>
                <button type="button" class="tool-btn" id="tool-copy" title="Copiar">
                    <img src="{{ asset('icons/imgCopiar.png') }}" width="22" height="22" alt="Copiar" style="object-fit: contain;">
                </button>
                <div class="border-top w-100 my-1"></div>
                <button type="button" class="view-btn" id="view-3d">3D</button>
                <button type="button" class="view-btn text-danger" id="view-x">X</button>
                <button type="button" class="view-btn text-success" id="view-y">Y</button>
                <button type="button" class="view-btn text-primary" id="view-z">Z</button>
            </div>
            
            <div id="rotate-overlay" class="p-3 bg-dark text-white rounded-3 shadow-lg" style="display: none; position: absolute; z-index: 1000; transform: translate(-50%, calc(-100% - 20px)); min-width: 200px;">
                <div class="small text-center text-muted mb-2 fw-bold">ROTAR PIEZA</div>
                <div class="d-flex align-items-center gap-2 mb-3">
                    <label class="small text-light">Grados:</label>
                    <input type="number" id="inp-rot-deg" class="form-control form-control-sm bg-secondary text-white border-secondary" value="90" style="width: 70px">
                </div>
                <div class="row g-2 text-center">
                    <div class="col-6">
                        <div class="small text-warning mb-1">Horizontal</div>
                        <button type="button" class="btn btn-outline-warning btn-sm w-100" id="btn-rot-hy">Izq</button>
                        <button type="button" class="btn btn-outline-warning btn-sm w-100 mt-1" id="btn-rot-hy-r">Der</button>
                    </div>
                    <div class="col-6">
                        <div class="small text-info mb-1">Vertical</div>
                        <button type="button" class="btn btn-outline-info btn-sm w-100" id="btn-rot-vx">Arr</button>
                        <button type="button" class="btn btn-outline-info btn-sm w-100 mt-1" id="btn-rot-vx-r">Aba</button>
                    </div>
                </div>
            </div>
            
            <div id="three-canvas"></div>
        </div>
    </div>
    
    <!-- Formulario Seguro para Guardar/Actualizar Mueble -->
    @isset($mueble)
    <form action="{{ route('maestro.muebles.update', $mueble->id) }}" method="POST" id="form-mueble" onsubmit="return false;">
        @csrf
        @method('PUT')
    @else
    <form action="{{ route('muebles.store') }}" method="POST" id="form-mueble" onsubmit="return false;">
        @csrf
    @endisset
        <input type="hidden" name="nombre" id="form-nombre">
        <input type="hidden" name="material" id="form-material" value="Melamina 18mm">
        <input type="hidden" name="precio" id="form-precio" value="0">
        <input type="hidden" name="dimensiones" id="form-dimensiones">
        <input type="hidden" name="descripcion" id="form-descripcion" value="Mueble diseñado en el configurador 3D.">
        <input type="hidden" name="estructura_json" id="form-estructura-json">
        <input type="hidden" name="imagen_base64" id="form-imagen-base64">
    </form>


</div>

<!-- Modal Publicar Mueble -->
<div class="modal fade" id="modal-publicar-mueble" tabindex="-1" aria-hidden="true" style="z-index: 2200;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-dark text-white px-4 py-3">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-2 mb-0">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Publicar Mueble en el Catalogo
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" id="btn-close-modal-pub" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="row g-4">
                    <!-- Vista previa 3D -->
                    <div class="col-md-5 text-center">
                        <label class="form-label small fw-bold text-muted text-uppercase d-block text-start">Captura 3D Automática</label>
                        <div class="border rounded-3 p-2 bg-white shadow-sm position-relative">
                            <img id="pub-preview-img" src="" alt="Vista 3D" class="img-fluid rounded" style="max-height: 220px; object-fit: contain; width: 100%;">
                            <div class="badge bg-dark position-absolute bottom-0 end-0 m-3 shadow" id="pub-badge-piezas">0 piezas</div>
                        </div>
                        <div class="small text-muted mt-2 fst-italic">Esta imagen será visible para los clientes y estudiantes.</div>
                    </div>

                    <!-- Datos del mueble -->
                    <div class="col-md-7">
                        <div class="mb-3">
                            <label class="form-label small fw-bold text-dark">Nombre del Proyecto *</label>
                            <input type="text" id="pub-nombre" class="form-control fw-bold" placeholder="Ej: Escritorio Minimalista Roble">
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label small fw-bold text-dark">Precio Estimado (Bs) *</label>
                                <div class="input-group">
                                    <span class="input-group-text fw-bold">Bs</span>
                                    <input type="number" id="pub-precio" class="form-control fw-bold text-primary" value="450" min="0" step="10">
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold text-dark">Material Predominante *</label>
                                <input type="text" id="pub-material" class="form-control" value="Melamina 18mm">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-bold text-dark">Dimensiones Generales Calculadas</label>
                            <input type="text" id="pub-dimensiones" class="form-control bg-white" readonly>
                        </div>

                        <div class="mb-0">
                            <label class="form-label small fw-bold text-dark">Descripción del Diseño *</label>
                            <textarea id="pub-descripcion" class="form-control" rows="3" placeholder="Describe el mueble...">Diseño ergonómico y moderno fabricado en melamina de 18 mm con cantos de PVC. Ideal para estudio, dormitorio u oficina.</textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white px-4 py-3 d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary fw-bold px-3" data-bs-dismiss="modal" id="btn-cancel-modal-pub">Seguir Editando</button>
                <button type="button" class="btn btn-success fw-bold px-4 shadow-sm d-flex align-items-center gap-2" id="btn-confirm-publicar">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 13l4 4L19 7"/></svg>
                    Confirmar y Publicar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Overlay Compartido -->
<div class="wizard-overlay" id="shared-overlay"></div>

<!-- Modal Corte -->
<div class="corte-modal" id="corte-modal">
    <div class="d-flex justify-content-between align-items-center p-3 border-bottom bg-light rounded-top">
        <h5 class="fw-bold mb-0">Configuración de Corte</h5>
        <button type="button" class="btn-close" id="corte-close"></button>
    </div>
    <div class="p-3 bg-white flex-grow-1 overflow-auto">
        <div class="row g-3 bg-light p-3 rounded mb-3">
            <div class="col-md-4">
                <label class="small fw-bold text-muted">Tamaño Sierra (mm)</label>
                <input type="number" id="sierra-mm" class="form-control form-control-sm" value="3" step="0.5">
            </div>
            <div class="col-md-6">
                <label class="small fw-bold text-muted">Panel (Ancho x Alto mm)</label>
                <div class="input-group input-group-sm">
                    <input type="number" id="panel-w" class="form-control" value="2440">
                    <span class="input-group-text">x</span>
                    <input type="number" id="panel-h" class="form-control" value="1220">
                </div>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-warning btn-sm w-100 fw-bold" id="btn-recalcular-corte">Recalcular</button>
            </div>
        </div>
        
        <div class="d-flex gap-3 mb-3 small fw-bold text-muted bg-dark text-white p-2 rounded">
            <span id="stat-piezas">0 piezas</span>
            <span id="stat-tableros">0 tableros</span>
            <span id="stat-uso">0% uso</span>
            <span id="stat-sierra">Sierra: 3 mm</span>
        </div>
        
        <div id="corte-sheets-container">
            <div class="text-center text-muted p-5 border rounded" id="corte-empty-msg">
                Agrega piezas al diseño 3D para generar planos de corte.
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-between align-items-center p-3 border-top bg-light rounded-bottom">
        <span class="fw-bold text-muted" id="corte-footer-info">0 uds.</span>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-dark fw-bold" id="btn-imprimir-taller">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1">
                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                    <rect x="6" y="14" width="12" height="8"></rect>
                </svg>
                Imprimir Planos
            </button>
            <button type="button" class="btn btn-dark fw-bold" id="btn-descargar-planos">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="me-1">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
                </svg>
                Descargar
            </button>
        </div>
    </div>
</div>

<!-- Wizard Modal -->
<div class="wizard-modal" id="wizard-modal">
    <div class="wizard-header d-flex justify-content-between">
        <div class="d-flex gap-2 flex-grow-1" id="wizard-steps-bar">
            <div class="badge bg-dark rounded-pill" data-step="1">1. Estructura</div>
            <div class="badge bg-secondary rounded-pill" data-step="2">2. Tablero</div>
            <div class="badge bg-secondary rounded-pill" data-step="3">3. Patas</div>
            <div class="badge bg-secondary rounded-pill" data-step="4">4. Cajones</div>
            <div class="badge bg-secondary rounded-pill" data-step="5">5. Estilo</div>
            <div class="badge bg-secondary rounded-pill" data-step="6">6. Resumen</div>
            <div class="badge bg-secondary rounded-pill" data-step="7">7. Final</div>
        </div>
        <button type="button" class="btn-close" id="wizard-close"></button>
    </div>
    
    <!-- Paso 1 -->
    <div class="wizard-step active" id="ws-1">
        <h5 class="fw-bold">Paso 1: Tipo y Dimensiones</h5>
        <div class="mb-3">
            <label class="form-label fw-bold">Tipo</label>
            <div class="wiz-options" id="wiz-type">
                <div class="wiz-option active" data-val="escritorio">Escritorio</div>
                <div class="wiz-option" data-val="gabinete">Gabinete</div>
            </div>
        </div>
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Ancho (mm)</label>
                <input type="number" id="wiz-w" class="form-control" value="1200">
            </div>
            <div class="col-md-4">
                <label class="form-label">Profundidad (mm)</label>
                <input type="number" id="wiz-d" class="form-control" value="600">
            </div>
            <div class="col-md-4">
                <label class="form-label">Alto (mm)</label>
                <input type="number" id="wiz-h" class="form-control" value="750">
            </div>
        </div>
    </div>
    
    <!-- (Pasos 2-7 simplificados pero funcionales para el script) -->
    <div class="wizard-step" id="ws-2">
        <h5 class="fw-bold">Paso 2: Material</h5>
        <input type="text" id="wiz-mat-name" class="form-control mb-2" value="Melamina Blanca">
        <select id="wiz-mat" class="form-select mb-2">
            <option value="#f5f5f5">Blanco</option>
            <option value="#d2a679" selected>Roble</option>
        </select>
        <input type="number" id="wiz-thick" class="form-control" value="18">
    </div>
    <div class="wizard-step" id="ws-3">
        <h5 class="fw-bold">Paso 3: Soporte</h5>
        <div class="wiz-options" id="wiz-legs-type">
            <div class="wiz-option active" data-val="lateral">Paneles</div>
            <div class="wiz-option" data-val="4patas">Patas</div>
        </div>
        <input type="number" id="wiz-leg-thick" class="form-control mt-2" value="18">
    </div>
    <div class="wizard-step" id="ws-4">
        <h5 class="fw-bold">Paso 4: Cajones</h5>
        <div class="wiz-options" id="wiz-drawers-yn">
            <div class="wiz-option active" data-val="0">0</div>
            <div class="wiz-option" data-val="2">2</div>
        </div>
        <input type="number" id="wiz-drawer-h" class="form-control mt-2" value="150">
        <input type="hidden" id="wiz-shelves" value="1">
    </div>
    <div class="wizard-step" id="ws-5">
        <h5 class="fw-bold">Paso 5: Estilo</h5>
        <select id="wiz-handle" class="form-select mb-2">
            <option value="none">Sin tirador</option>
            <option value="bar" selected>Barra</option>
        </select>
        <input type="number" id="wiz-price" class="form-control" value="0">
    </div>
    <div class="wizard-step" id="ws-6">
        <h5 class="fw-bold">Resumen</h5>
        <div id="wizard-summary"></div>
    </div>
    <div class="wizard-step" id="ws-7">
        <h5 class="fw-bold">Generando...</h5>
        <div id="cut-list-result" style="display:none">
            <table class="table table-sm" id="cut-table">
                <thead><tr><th>Pieza</th><th>Ancho</th><th>Alto</th><th>Cant</th></tr></thead>
                <tbody id="cut-tbody"></tbody>
            </table>
            <button type="button" class="btn btn-warning fw-bold w-100 mt-2" id="btn-close-wizard-done">Ver 3D</button>
        </div>
    </div>
    
    <div class="d-flex justify-content-between p-3 border-top bg-light rounded-bottom">
        <button type="button" class="btn btn-secondary fw-bold" id="wiz-back">Anterior</button>
        <span class="text-muted fw-bold align-self-center" id="wiz-step-indicator">Paso 1</span>
        <button type="button" class="btn btn-warning fw-bold text-dark" id="wiz-next">Siguiente</button>
    </div>
</div>

<!-- Dependencias -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/OrbitControls.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/controls/TransformControls.js"></script>
<script src="https://cdn.jsdelivr.net/npm/three@0.128.0/examples/js/math/OBB.js"></script>

<!-- CSS del wizard y modal corte original para compatibilidad de los JS -->
<style>
    .corte-board { position: relative; border: 1px solid #333; background: #fff; overflow: hidden; margin-bottom: 10px; }
    .corte-part { position: absolute; border: 1px solid #333; background: #fff; display: flex; align-items: center; justify-content: center; font-size: 10px; text-align: center;}
    .corte-waste { position: absolute; background: repeating-linear-gradient(-45deg, rgba(0,0,0,0.1), rgba(0,0,0,0.1) 1px, #fff 1px, #fff 8px); }
</style>

<!-- Scripts locales copiados -->
<script src="{{ asset('js/engine.js') }}"></script>
<script src="{{ asset('js/designer-app.js') }}"></script>
<script src="{{ asset('js/wizard.js') }}"></script>

<script>
    // Adaptaciones de UI para los scripts originales
    document.addEventListener('DOMContentLoaded', () => {
        // Modal Corte toggle
        const corteModal = document.getElementById('corte-modal');
        const overlay = document.getElementById('shared-overlay');
        const btnOpenCorte = document.getElementById('btn-open-corte');
        const btnCloseCorte = document.getElementById('corte-close');
        
        window.openCorteModal = function() {
            if (corteModal) corteModal.classList.add('show');
            if (overlay) overlay.classList.add('show');
            if (typeof window.renderCorteSheets === 'function') {
                window.renderCorteSheets();
            }
        };

        window.closeCorteModal = function() {
            if (corteModal) corteModal.classList.remove('show');
            if (overlay) overlay.classList.remove('show');
        };

        if (btnOpenCorte) {
            btnOpenCorte.addEventListener('click', (e) => {
                e.preventDefault();
                window.openCorteModal();
            });
        }
        if (btnCloseCorte) {
            btnCloseCorte.addEventListener('click', (e) => {
                e.preventDefault();
                window.closeCorteModal();
            });
        }
        if (overlay) {
            overlay.addEventListener('click', () => {
                window.closeCorteModal();
                if (typeof window.hideWizard === 'function') window.hideWizard();
            });
        }
        
        // El wizard se abre por código en wizard.js, solo aseguramos que pueda cerrar
        const wizardModal = document.getElementById('wizard-modal');
        const btnCloseWizard = document.getElementById('wizard-close');
        
        window.showWizard = function() {
            if (wizardModal) wizardModal.classList.add('show');
            if (overlay) overlay.classList.add('show');
        };
        window.hideWizard = function() {
            if (wizardModal) wizardModal.classList.remove('show');
            if (overlay) overlay.classList.remove('show');
        };
        
        if(btnCloseWizard) {
            btnCloseWizard.addEventListener('click', (e) => {
                e.preventDefault();
                window.hideWizard();
            });
        }
        
        // Tabs funcionalidad
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const targetId = e.currentTarget.getAttribute('data-target');
                const parent = e.currentTarget.parentElement;
                parent.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
                e.currentTarget.classList.add('active');
                
                // Encontrar el contenedor de tabs correcto
                const container = parent.nextElementSibling.parentElement;
                container.querySelectorAll('.tab-content').forEach(c => {
                    if (c.parentElement === parent.parentElement) {
                        c.classList.remove('active');
                    }
                });
                const targetEl = document.getElementById(targetId);
                if (targetEl) targetEl.classList.add('active');
            });
        });
        
        // Publicación del mueble mediante Modal con previsualización 3D
        const btnSubmit = document.getElementById('btn-submit-mueble');
        const modalPubEl = document.getElementById('modal-publicar-mueble');
        const btnClosePub = document.getElementById('btn-close-modal-pub');
        const btnCancelPub = document.getElementById('btn-cancel-modal-pub');
        const btnConfirmPub = document.getElementById('btn-confirm-publicar');
        const formMueble = document.getElementById('form-mueble');

        function openPublishModal() {
            const projectName = document.getElementById('project-name-input')?.value?.trim() || 'Proyecto Mydo Mar';
            const pubNombreInput = document.getElementById('pub-nombre');
            if (pubNombreInput) pubNombreInput.value = projectName;

            // Medidas y captura 3D automática
            const dims = typeof window.getSceneBoundingDimensions === 'function' ? window.getSceneBoundingDimensions() : '1200 × 600 × 750 mm';
            const pubDimInput = document.getElementById('pub-dimensiones');
            if (pubDimInput) pubDimInput.value = dims;

            const screenshot = typeof window.getScene3DScreenshot === 'function' ? window.getScene3DScreenshot() : '';
            const imgPreview = document.getElementById('pub-preview-img');
            if (imgPreview && screenshot) {
                imgPreview.src = screenshot;
            }

            const piezas = typeof window.extractPiecesFromScene === 'function' ? window.extractPiecesFromScene() : [];
            const badge = document.getElementById('pub-badge-piezas');
            if (badge) badge.innerText = `${piezas.length} piezas`;

            // Mostrar modal
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                const modal = bootstrap.Modal.getOrCreateInstance(modalPubEl);
                modal.show();
            } else {
                modalPubEl.style.display = 'block';
                modalPubEl.classList.add('show');
                if (overlay) overlay.classList.add('show');
            }
        }

        function closePublishModal() {
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                const modal = bootstrap.Modal.getInstance(modalPubEl);
                if (modal) modal.hide();
            } else {
                modalPubEl.style.display = 'none';
                modalPubEl.classList.remove('show');
                if (overlay) overlay.classList.remove('show');
            }
        }

        if (btnSubmit) {
            btnSubmit.addEventListener('click', (e) => {
                e.preventDefault();

                if (window.__editMode__) {
                    // Modo edicion: guardar directamente sin modal
                    const nombre = document.getElementById('project-name-input')?.value?.trim() || window.__muebleNombre__ || '';
                    if (!nombre) { alert('Escribe un nombre para el mueble.'); return; }

                    const dims       = typeof window.getSceneBoundingDimensions === 'function' ? window.getSceneBoundingDimensions() : window.__muebleDims__ || '';
                    const screenshot = typeof window.getScene3DScreenshot === 'function' ? window.getScene3DScreenshot() : '';
                    const exportData = typeof window.getExportData === 'function' ? window.getExportData() : {};

                    document.getElementById('form-nombre').value          = nombre;
                    document.getElementById('form-material').value        = document.getElementById('pub-material')?.value || window.__muebleMaterial__ || 'Melamina 18mm';
                    document.getElementById('form-precio').value          = document.getElementById('pub-precio')?.value  || window.__mueblePrecio__ || 0;
                    document.getElementById('form-dimensiones').value     = dims;
                    document.getElementById('form-descripcion').value     = document.getElementById('pub-descripcion')?.value || window.__muebleDesc__ || '';
                    document.getElementById('form-imagen-base64').value   = screenshot;
                    document.getElementById('form-estructura-json').value = JSON.stringify(exportData);

                    btnSubmit.disabled  = true;
                    btnSubmit.innerText = 'Guardando...';
                    HTMLFormElement.prototype.submit.call(formMueble);
                } else {
                    // Modo crear: abrir modal de publicacion
                    openPublishModal();
                }
            });
        }
        if (btnClosePub) btnClosePub.addEventListener('click', closePublishModal);
        if (btnCancelPub) btnCancelPub.addEventListener('click', closePublishModal);

        if (btnConfirmPub && formMueble) {
            btnConfirmPub.addEventListener('click', function(e) {
                e.preventDefault();
                const nombre = document.getElementById('pub-nombre')?.value?.trim();
                if (!nombre) {
                    alert("Por favor ingresa un nombre para el mueble.");
                    document.getElementById('pub-nombre')?.focus();
                    return;
                }

                const precio = parseFloat(document.getElementById('pub-precio')?.value) || 0;
                const material = document.getElementById('pub-material')?.value?.trim() || 'Melamina 18mm';
                const dimensiones = document.getElementById('pub-dimensiones')?.value?.trim() || '1200 × 600 × 750 mm';
                const descripcion = document.getElementById('pub-descripcion')?.value?.trim() || 'Mueble diseñado en Mydo-mar.';
                const screenshot = typeof window.getScene3DScreenshot === 'function' ? window.getScene3DScreenshot() : '';

                let exportData = typeof window.getExportData === 'function' ? window.getExportData() : { tipo: '3d_design', nombre: nombre };
                exportData.nombre = nombre;

                document.getElementById('form-nombre').value = nombre;
                document.getElementById('form-precio').value = precio;
                document.getElementById('form-material').value = material;
                document.getElementById('form-dimensiones').value = dimensiones;
                document.getElementById('form-descripcion').value = descripcion;
                document.getElementById('form-imagen-base64').value = screenshot;
                document.getElementById('form-estructura-json').value = JSON.stringify(exportData);

                // Deshabilitar botón para evitar envíos dobles
                btnConfirmPub.disabled = true;
                btnConfirmPub.innerText = 'Publicando...';

                // Enviar formulario real
                HTMLFormElement.prototype.submit.call(formMueble);
            });
        }
    });

    // Variables de modo edicion (inyectadas desde PHP de forma segura)
    window.__editMode__      = {{ isset($mueble) ? 'true' : 'false' }};
    window.__muebleNombre__  = @json(isset($mueble) ? $mueble->nombre      : '');
    window.__muebleDims__    = @json(isset($mueble) ? $mueble->dimensiones : '');
    window.__muebleMaterial__= @json(isset($mueble) ? ($mueble->material ?? 'Melamina 18mm') : 'Melamina 18mm');
    window.__mueblePrecio__  = {{ isset($mueble) ? (float)($mueble->precio ?? 0) : 0 }};
    window.__muebleDesc__    = @json(isset($mueble) ? ($mueble->descripcion ?? '') : '');
    window.__muebleJSON__    = @json(isset($mueble) ? $mueble->estructura_json : null);

    // Modo Edicion: reconstruir escena 3D desde el JSON guardado
    if (window.__editMode__ && window.__muebleJSON__) {
        (function waitForDesigner() {
            if (typeof window.cargarDesdeJSON !== 'function' || typeof clearAllObjects === 'undefined') {
                setTimeout(waitForDesigner, 200);
                return;
            }
            try {
                const data = typeof window.__muebleJSON__ === 'string'
                    ? JSON.parse(window.__muebleJSON__)
                    : window.__muebleJSON__;
                if (data) window.cargarDesdeJSON(data);
            } catch(e) {
                console.warn('No se pudo restaurar el disenio guardado:', e);
            }
        })();
    }
</script>
@endsection
