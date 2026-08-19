@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">

            <!-- Tarjeta Principal -->
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden bg-white">
                
                <!-- Encabezado Oscuro con Acento Amarillo -->
                <div class="card-header bg-dark text-white p-4 border-0 d-flex align-items-center gap-2">
                    <h4 class="fw-bold mb-0 text-white">Editar Mueble</h4>
                </div>

                <div class="card-body p-4 p-sm-5">
                    <form method="POST" action="{{ route('maestro.muebles.update', $mueble->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Nombre del Mueble -->
                        <div class="mb-4">
                            <label for="nombre" class="form-label small fw-bold text-dark">Nombre del Mueble</label>
                            <input type="text" 
                                   id="nombre" 
                                   name="nombre" 
                                   class="form-control form-control-lg fs-6 @error('nombre') is-invalid @enderror" 
                                   value="{{ old('nombre', $mueble->nombre ?? 'mueble de cocina') }}" 
                                   required 
                                   style="border-radius: 10px;">
                            @error('nombre')
                                <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <!-- Fotografía del Mueble -->
                        <div class="mb-4">
                            <label class="form-label small fw-bold text-dark d-block">Fotografía del Mueble</label>
                            
                            <!-- Previsualización de la Imagen -->
                            <div class="mb-3">
                                @if($mueble->imagen_url)
                                    <img src="{{ asset('storage/' . $mueble->imagen_url) }}" 
                                         alt="Imagen actual" 
                                         class="img-thumbnail rounded-3 shadow-sm object-fit-cover" 
                                         style="max-width: 200px; height: 140px;">
                                @else
                                    <div class="bg-light border rounded-3 d-flex align-items-center justify-content-center" style="max-width: 200px; height: 140px;">
                                        <span class="text-muted small">Sin imagen</span>
                                    </div>
                                @endif
                                <p class="text-muted small mt-1 mb-0">Imagen actual. Sube una nueva para reemplazarla.</p>
                            </div>

                            <!-- Input File Personalizado -->
                            <input type="file" 
                                   id="imagen" 
                                   name="imagen" 
                                   class="form-control fs-6 @error('imagen') is-invalid @enderror" 
                                   accept="image/jpeg,image/png,image/webp" 
                                   style="border-radius: 10px;">
                            <span class="form-text text-muted small">Formatos permitidos: JPG, PNG, WEBP (Máx. 10MB)</span>
                            @error('imagen')
                                <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <!-- Parámetros / Dimensiones -->
                        <div class="mb-4">
                            <label for="dimensiones" class="form-label small fw-bold text-dark">Parámetros de Diseño / Dimensiones</label>
                            <input type="text" 
                                   id="dimensiones" 
                                   name="dimensiones" 
                                   class="form-control fs-6" 
                                   value="{{ old('dimensiones', $mueble->dimensiones ?? '') }}" 
                                   placeholder="Ej. 90cm x 200cm o 900mm x 2000mm" 
                                   style="border-radius: 10px;">
                        </div>

                        <!-- Descripción detallada -->
                        <div class="mb-4">
                            <label for="descripcion" class="form-label small fw-bold text-dark">Descripción detallada</label>
                            <textarea id="descripcion" 
                                      name="descripcion" 
                                      rows="4" 
                                      class="form-control fs-6 @error('descripcion') is-invalid @enderror" 
                                      style="border-radius: 10px;" 
                                      required
                                      placeholder="Añade detalles sobre el diseño, materiales o especificaciones...">{{ old('descripcion', $mueble->descripcion ?? '') }}</textarea>
                            @error('descripcion')
                                <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>

                        <!-- Botones de Acción -->
                        <div class="d-flex justify-content-end align-items-center gap-2 pt-3 border-top">
                            <a href="{{ route('maestro.dashboard') }}" class="btn btn-outline-secondary fw-bold px-4 py-2" style="border-radius: 10px;">
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-warning fw-bold text-dark px-4 py-2 shadow-sm" style="border-radius: 10px;">
                                Guardar Cambios
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection