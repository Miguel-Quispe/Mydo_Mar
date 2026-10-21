
================================================================================
DOCUMENTO DE REFERENCIA Y FLUJO OPERATIVO: SISTEMA POLLOMUEBLES B2B
===================================================================

1. INTRODUCCIÓN Y ALCANCE
   PolloMuebles es una plataforma tecnológica orientada al diseño, despiece, cálculo de materiales y guías interactivas de ensamblaje de muebles en melamina y madera. Su arquitectura B2B (Business to Business) permite que empresas muebleras (ej. Madeart) operen de manera corporativa, administren su personal, controlen su inventario global y ofrezcan guías estandarizadas para clientes o alumnos.
2. FLUJO DE REGISTRO DE EMPRESAS Y EMPLEADOS

* Registro de la Empresa: El gerente o administrador principal registra la empresa corporativa ingresando la Razón Social, NIT, Teléfono de WhatsApp (para contacto directo y comercial) y la ubicación física.
* Creación de Subroles Operativos: Cada empresa define sus propios roles de trabajo internos en la tabla `empresa_roles` (ej. Armador, Diseñador, Supervisor, Transporte).
* Generación Masiva de Cuentas de Empleados: El gerente puede generar de forma masiva cuentas para sus empleados de manera incremental (ej. madeart_armador_01, madeart_armador_02). El sistema autogenera las credenciales temporales.
* Auditoría de Credenciales: Cualquier modificación de nombre de usuario o restablecimiento de contraseña queda registrado de forma inalterable en la tabla `users_credenciales_auditoria`, vinculada directamente al eje temporal.

3. GESTIÓN DEL CATÁLOGO GLOBAL DE HERRAJES (VISTOS Y NO VISTOS)

* Módulo de Carpinteros: Los carpinteros dan de alta los accesorios en el catálogo global `muebles_catalogo_herrajes_global`.
* Clasificación por Visibilidad:
  - Herrajes Vistos (`visible`): Bisagras, rieles, tiradores, chapas y elementos con impacto visual o móvil.
  - Herrajes No Vistos (`no_visible`): Tornillos confirmat, autorroscantes, clavos y tarugos de fijación interna.
* Vinculación a Proyectos: Al diseñar un mueble (cocina, ropero), el sistema "jala" los herrajes de este catálogo global mediante la tabla puente `muebles_herrajes_por_proyecto` para calcular cantidades y costos.

4. MATERIALES Y TRAZABILIDAD TEMPORAL (DIM_TIEMPO)

* Catálogo de Materiales: Se registran las materias primas (melamina, MDF) especificando espesor y precio por lámina[cite: 1].
* Control Temporal Exhaustivo: Toda la plataforma se apoya en la tabla maestra `dim_tiempo`.
  - Se cronometran y auditan las modificaciones de precios de materiales.
  - Se registran las fechas de creación y modificación de categorías, niveles y catálogos.
  - Se trazan los cambios operativos e histórico de piezas en el despiece 3D.

5. GUÍAS DE ENSAMBLAJE Y PROGRESO DE USUARIOS

* Planos Estáticos (Guías del Maestro): La tabla `muebles_pasos_ensamblaje` define los pasos universales de armado de un diseño.
* Progreso Dinámico: La tabla `muebles_progreso_usuario` rastrea el avance individual de cada persona que esté armando el mueble (porcentaje completado, paso actual y fecha de inicio).
  ================================================================================
