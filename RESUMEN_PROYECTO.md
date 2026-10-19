# Mydo-mar: Estado y Memoria Técnica del Proyecto

Este documento sirve como registro oficial y punto de enlace permanente entre sesiones de desarrollo para el proyecto **Mydo-mar** (Sistema de Diseño 3D, Despiece y Catálogo de Muebles de Melamina).

---

## 1. Resumen de Funcionalidades Implementadas

### A. Correcciones de Base y Usabilidad
- **Inversión de medidas corregida**: En `public/js/designer-app.js`, se respeta la orientación vertical u horizontal de cada tablero (`pieceType`), garantizando que *Grosor* siempre sea el espesor del tablero (18 mm), *Alto* la longitud vertical y *Ancho* el frente.
- **Eliminación de recargas involuntarias**: En `resources/views/maestro/muebles/create.blade.php`, se eliminó la etiqueta `<form>` envolvente y se asignó `type="button"` a todos los controles.
- **Configuración y Optimización de Corte**: Cálculo automático de tableros mediante algoritmo bin-packing (FFDH) con kerf de sierra configurable y botón para imprimir planos técnicos de taller en PDF.
- **Biblioteca de Ejemplos Base**: Plantillas funcionales en Three.js para Escritorio, Estantería, Gabinete y Mesa de Centro.

### B. Paso 1: Agrupar / Bloquear Piezas y Cero Emojis
- **Regla Cero Emojis**: Limpieza total de emojis y caracteres especiales corruptos en vistas Blade (`create`, `show`, `dashboard`), scripts (`designer-app.js`, `wizard.js`, `engine.js`) y estilos, reemplazados por iconos SVG nativos y texto profesional.
- **Bloqueo / Agrupación de Muebles**:
  - Botón `#btn-agrupar` en el panel de parámetros: alterna entre `Bloquear / Agrupar Todo` y `Desbloquear / Desagrupar`.
  - Al agrupar, todas las piezas se integran en un `THREE.Group` (`isAssemblyGroup = true`) con pivote centrado en la base del mueble.
  - El mueble bloqueado se desplaza en conjunto con arrastre del ratón o teclas **W, A, S, D, Flechas**, y rota con **Q, E**.
  - Los campos de medida reflejan las dimensiones exteriores totales (**Ancho Tot.**, **Alto Tot.**, **Fondo Tot.**).
  - Al desagrupar, cada pieza vuelve a ser editable individualmente con sus coordenadas globales preservadas.

### C. Paso 2: Sistema de Texturas con Imágenes para Caras de Materiales
- **Banco de Texturas Locales (`public/textures/`)**:
  - `roble_natural.jpg`: Roble claro con veta natural y tono cálido.
  - `nogal_oscuro.jpg`: Nogal chocolate profundo.
  - `melamina_blanca.jpg`: Melamina satinada liso neutro.
  - `gris_grafito.jpg`: Textura moderna pizarra/grafito arquitectónico.
  - `marmol_calacatta.jpg`: Acabado blanco con vetas grises para cubiertas.
  - `haya_madera.jpg`: Tono cálido con veta suave.
- **Mapeo UV Métrico**: Texturizado en Three.js con `THREE.RepeatWrapping`, calculando la repetición según las dimensiones reales en milímetros de la pieza (`repeat.set(w / scale, h / scale)`), evitando distorsiones de veta.
- **Selector Visual en la Interfaz**: Cuadrícula interactiva en `create.blade.php` con miniaturas reales y botones para:
  - *Aplicar a Pieza Seleccionada* (para diseños combinados).
  - *Aplicar a Todo el Mueble* (acabado uniforme).
- **Persistencia y Visor de Catálogo (`catalogo/show.blade.php`)**: Cada pieza almacena su `textureKey` en el JSON y el visor Three.js interactivo del cliente/estudiante reconstruye el mueble con sus texturas reales.

---

## 2. Mapa de Archivos Clave del Sistema

| Archivo | Rol en el Proyecto |
|---|---|
| `resources/views/maestro/muebles/create.blade.php` | Interfaz principal del diseñador 3D (paneles, herramientas, modal de publicación). |
| `public/js/designer-app.js` | Motor de la escena Three.js: creación de piezas, texturas, agrupación, controles y exportación. |
| `public/js/engine.js` | Motor paramétrico matemático para cálculo de piezas y ensamblajes. |
| `public/js/wizard.js` | Asistente paso a paso para generación de muebles rápidos. |
| `public/textures/` | Directorio local con las texturas de melamina y maderas. |
| `resources/views/catalogo/show.blade.php` | Ficha técnica pública para estudiantes y clientes con visor 3D e impresión de planos. |
| `resources/views/maestro/dashboard.blade.php` | Panel de control del maestro carpintero con listado de proyectos. |
| `app/Http/Controllers/MuebleController.php` | Guardado de muebles con captura 3D en base64 y estructura JSON. |

---

---

## 3. Guía Práctica de Uso del Diseñador 3D (Manual de Usuario)

Esta guía explica paso a paso cómo encontrar y utilizar cada una de las herramientas del sistema:

### 1. Navegación en el Escenario 3D
- **Orbitar / Rotar Cámara**: Clic izquierdo sostenido y arrastrar sobre el fondo del lienzo 3D.
- **Acercar / Alejar (Zoom)**: Rueda del ratón (scroll).
- **Vistas Predefinidas**: En la barra superior derecha del lienzo puedes alternar entre vista **Perspectiva 3D**, **Frontal (X)**, **Superior (Y)** y **Lateral (Z)**.
- **Entorno / Habitación**: En la pestaña *Elementos*, activa el botón *Habitación* para visualizar el piso con textura realista de parquet.

### 2. Creación y Manejo de Tableros (Melamina 18mm)
- **Agregar Paneles**:
  - En la pestaña *Elementos*, pulsa **Vertical** (genera un lateral de 18x500x500 mm).
  - Pulsa **Horizontal** (genera una repisa/base de 500x18x500 mm).
- **Mover Piezas**:
  - *Arrastre con Ratón*: Haz clic sobre la pieza y arrástrala sobre el piso. El sistema cuenta con imantación (*snapping*) automática a los bordes de piezas cercanas.
  - *Teclado (WASD y Flechas)*:
    - **W / S**: Desplazar hacia adelante / atrás (eje Z).
    - **A / D**: Desplazar hacia izquierda / derecha (eje X).
    - **Flecha Arriba / Abajo**: Elevar o descender la altura (eje Y).
    - **Q / E**: Rotar la pieza en incrementos de 5 grados.
- **Redimensionar Piezas**:
  - Selecciona la pieza. En la pestaña **Parámetros** (abajo a la izquierda), los campos se adaptan automáticamente:
    - Para piezas horizontales: *Grosor*, *Fondo* y *Ancho*.
    - Para piezas verticales: *Grosor*, *Alto* y *Ancho*.
  - Modifica los valores en milímetros y pulsa **Guardar Cambios**.
- **Duplicar y Eliminar**:
  - Pulsa **Copiar** para duplicar la pieza seleccionada con sus mismas dimensiones y textura.
  - Pulsa **Eliminar** para borrar la pieza seleccionada (o borrar todo si no hay ninguna seleccionada).

### 3. Agrupar y Bloquear Piezas (Ensamble Sólido)
- Cuando tengas montado el mueble o parte de él, pulsa el botón **Bloquear / Agrupar Todo** en la pestaña *Parámetros*.
- **Efecto**:
  - Todas las piezas se unifican como un solo bloque con pivote centrado en la base.
  - Al arrastrar con el ratón o mover con **W, A, S, D**, todo el mueble se desplaza junto sin que ninguna pieza se desfase.
  - Al rotar con **Q, E** o los botones de rotación, el mueble gira completo sobre su centro.
  - Los indicadores de medida muestran las cotas envolventes exteriores: **Ancho Tot.**, **Alto Tot.** y **Fondo Tot.**
- Para volver a ajustar una pieza individual, pulsa **Desbloquear / Desagrupar**.

### 4. Selección de Acabados y Texturas con Veta Real
- En la pestaña inferior **Materiales**, verás las miniaturas fotográficas de los 6 materiales:
  - **Roble Natural** (veta clara).
  - **Nogal Oscuro** (veta chocolate).
  - **Blanca Lisa** (melamina lisa satinada).
  - **Gris Grafito** (textura pizarra moderna).
  - **Mármol Calacatta** (piedra blanca con vetas).
  - **Haya Madera** (madera clara dorada).
- **Cómo aplicarlos**:
  1. Haz clic en la miniatura deseada para seleccionarla.
  2. Pulsa **Aplicar a Pieza Seleccionada** para crear combinaciones bicolor (ej. cuerpo blanco y frente de roble).
  3. O pulsa **Aplicar a Todo el Mueble** para unificar el acabado.
- La veta se escala métricamente de forma automática en milímetros reales para no deformarse.

### 5. Colocación de Herrajes Técnicos (Paso 3)
- En la pestaña **Herrajes**:
  - **Selector de Acabado**: Alterna entre *Acero Inoxidable / Cromado* y *Negro Mate*.
  - **Añadir Bisagras**: Selecciona una puerta y pulsa *Añadir Bisagras*; el sistema coloca 2 bisagras de cazoleta de 35mm a 100mm de los extremos superior e inferior y 21.5mm del canto en la cara interna.
  - **Añadir Jalador**:
    - Si es un cajón: lo ubica automáticamente centrado en el frente.
    - Si es una puerta: lo coloca en orientación vertical a 50mm del canto exterior opuesto a las bisagras.
  - **Añadir Rieles Telescópicos**: Selecciona un cajón o módulo para montar las correderas metálicas en ambos costados.

### 6. Ejemplos Rápidos de Taller (Pestaña Biblioteca)
En la pestaña *Biblioteca* dispones de plantillas listas con 1 clic:
- **Escritorio Básico**: 1200 × 600 × 750 mm.
- **Estantería / Librero**: 600 × 300 × 1200 mm con 3 repisas.
- **Gabinete Bajo TV**: 1000 × 400 × 550 mm con división.
- **Mesa de Centro**: 800 × 500 × 450 mm con 4 patas.
- **Gabinete con Puerta y Bisagras**: Prototipo con puerta batiente, bisagras cazoleta y jalador.
- **Módulo Cajonera con Rieles**: Prototipo con cajón, correderas telescópicas y jalador frontal.

### 7. Optimización de Corte y Planos de Taller
- En la barra superior, pulsa **Configurar Corte**.
- El sistema calcula cuántas láminas de melamina (2440 × 1220 mm) se requieren y organiza el corte con el kerf de la sierra.
- Pulsa **Imprimir Hoja de Taller (PDF)** para abrir la hoja técnica con lista de despiece numerada, medidas de corte y diagramas.

### 8. Guardar y Publicar en Base de Datos
- Pulsa el botón verde **Guardar y Publicar** (abajo a la derecha).
- Se abre una ventana emergente que muestra una **captura 3D automática** del mueble que acabas de diseñar y las medidas exteriores calculadas.
- Confirma el nombre, precio estimado en Bs y descripción.
- Al confirmar, el mueble queda registrado en la base de datos con su foto real y su visor 3D interactivo queda disponible en `/catalogo/{id}`.
