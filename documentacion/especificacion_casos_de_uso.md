# ESPECIFICACIÓN DE CASOS DE USO — PROYECTO "MYDO MAR"

Este documento contiene la especificación formal y avanzada de los 14 Casos de Uso del sistema Mydo Mar, desarrollado como proyecto de grado para el Instituto Tecnológico Santa Cruz. Esta especificación adopta patrones avanzados de UML (Generalización de Actores, Generalización de Casos de Uso, e inclusiones/extensiones con Puntos de Extensión) inspirados en los estándares profesionales de diseño de sistemas.

---

## 1. PERFILES Y JERARQUÍA DE ACTORES

Para optimizar y estructurar los permisos del sistema, se ha definido una jerarquía de actores donde existe un actor base que hereda sus capacidades a los roles específicos:

*   **Usuario (Actor Base):** Representa a cualquier usuario autenticado en la plataforma. Posee capacidades globales como explorar y buscar proyectos en el catálogo.
*   **Diseñador (Maestro) [Hereda de Usuario]:** Usuario especializado encargado del diseño tridimensional paramétrico, despiece y configuración de guías.
*   **Estudiante (Armador) [Hereda de Usuario]:** Alumno o armador que interactúa con las guías de ensamblaje interactivo, registra su avance y descarga planos.
*   **Administrador [Hereda de Usuario]:** Personal responsable de la moderación, control de calidad del catálogo y mantenimiento del sistema.

```mermaid
graph TD
    Usuario((Usuario))
    Disenador((Diseñador / Maestro))
    Estudiante((Estudiante / Armador))
    Administrador((Administrador))

    Disenador --|> Usuario
    Estudiante --|> Usuario
    Administrador --|> Usuario
```

---

## 2. MATRIZ DE ASOCIACIÓN DE REQUERIMIENTOS Y CASOS DE USO

La correspondencia detallada entre los 27 Requerimientos Funcionales (RF) y los 14 Casos de Uso (UC) estructurados jerárquicamente es la siguiente:

| Código UC | Caso de Uso | Relación UML | Requerimientos Asociados (RF) | Actor Principal |
| :--- | :--- | :--- | :--- | :--- |
| **UC1** | Registrar Proyecto | *Caso de Uso Base (Abstracto)* | RF1, RF5, RF7 | Diseñador |
| **UC2** | Registrar Nuevo Proyecto | *Hereda de UC1* | (Creación de nuevos registros) | Diseñador |
| **UC3** | Modificar Proyecto Existente | *Hereda de UC1* | RF2 (Editar datos del diseño) | Diseñador |
| **UC4** | Eliminar Proyecto | *Extiende a UC3* | RF3 (Eliminar diseño propio) | Diseñador |
| **UC5** | Generar Lista de Despiece | *Incluido por UC2* | RF8 (Cálculo automático de piezas) | Sistema / Diseñador |
| **UC6** | Calcular Consumo de Melamina | *Extiende a UC5* | RF11 (Cálculo de planchas de material) | Sistema / Diseñador |
| **UC7** | Configurar Pasos de Ensamblaje | *Extiende a UC1* | RF4 (Paso a paso), RF10 (Tiempo estimado) | Diseñador |
| **UC8** | Publicar Proyecto en Catálogo | *Asociación directa* | RF6 (Categoría), RF12 (Publicación) | Diseñador |
| **UC9** | Explorar y Filtrar Catálogo | *Asociación directa* | RF13, RF14, RF15, RF16, RF17 | Usuario (Base) |
| **UC10** | Reproducir Guía Interactiva | *Asociación directa* | RF18, RF19, RF22 (Visor 3D y guía) | Estudiante |
| **UC11** | Descargar Planos en PDF | *Asociación directa* | RF23 (Descargar planos en PDF) | Estudiante |
| **UC12** | Registrar Progreso de Armado | *Incluido por UC10* | (Lógica de base de datos progreso_alumno) | Estudiante / Sistema |
| **UC13** | Guardar en Favoritos | *Asociación directa* | RF24 (Guardar favoritos) | Estudiante |
| **UC14** | Administrar y Auditar el Sistema | *Asociación directa* | RF25, RF26, RF27 | Administrador |

---

## 3. ESPECIFICACIÓN DETALLADA DE CASOS DE USO CRÍTICOS

A continuación se describen formalmente los flujos de interacción de los casos de uso principales.

### UC1: Registrar Proyecto (Caso de Uso Base)

*   **Descripción:** Caso de uso padre que encapsula la lógica genérica de guardado y validación de parámetros de diseño tridimensional en Mydo Mar.
*   **Actor Principal:** Diseñador (Maestro)
*   **Puntos de Extensión:**
    *   `Configurar Pasos`: Permite asociar una guía de armado detallada al diseño (UC7).
*   **Precondiciones:**
    1. El Diseñador está autenticado y tiene el rol de `maestro`.
*   **Flujo Principal:**
    1. El Diseñador introduce en el formulario lateral los datos del mueble: Nombre, Descripción, Alto (mm), Ancho (mm), Profundidad (mm) y Espesor (mm).
    2. El Diseñador presiona el botón de guardar.
    3. El sistema valida que las dimensiones e insumos cumplan los límites mínimos físicos.
    4. El sistema transmite la información al backend y la persiste de forma transaccional en PostgreSQL.
*   **Postcondiciones:** El diseño queda registrado en la base de datos.

---

### UC2: Registrar Nuevo Proyecto (Hereda de UC1)

*   **Descripción:** Especialización del registro para nuevos diseños tridimensionales. Incluye obligatoriamente la generación paramétrica de piezas de corte.
*   **Actor Principal:** Diseñador (Maestro)
*   **Relación:** Hereda de **UC1** e incluye (`<<Include>>`) a **UC5**.
*   **Flujo Principal:**
    1. El Diseñador selecciona "Crear Nuevo Diseño" en el panel de control.
    2. El sistema despliega un lienzo en 3D vacío.
    3. Se ejecuta el flujo del caso de uso padre (**UC1**) para capturar e ingresar los parámetros base del mueble.
    4. **[Inclusión UC5]:** El sistema ejecuta automáticamente el cálculo de las piezas de corte a partir de las dimensiones globales ingresadas.
    5. El Diseñador confirma el despiece y finaliza el proceso de registro del nuevo diseño.
*   **Postcondiciones:** Se crea una nueva tupla en la tabla `diseno` y sus correspondientes piezas en la tabla `piezas`.

---

### UC3: Modificar Proyecto Existente (Hereda de UC1)

*   **Descripción:** Especialización del registro que permite la edición y reconfiguración de un diseño previamente guardado.
*   **Actor Principal:** Diseñador (Maestro)
*   **Relación:** Hereda de **UC1** y posee el punto de extensión `Eliminar Proyecto`.
*   **Flujo Principal:**
    1. El Diseñador selecciona uno de sus diseños en la lista de "Mis Proyectos".
    2. El sistema recupera los datos de PostgreSQL y carga la interfaz parametrizada con las dimensiones del diseño.
    3. El Diseñador edita los parámetros de alto, ancho, profundidad o espesor.
    4. Se ejecuta el flujo del caso de uso base (**UC1**) para revalidar y persistir las modificaciones en la base de datos.
*   **Postcondiciones:** Se actualizan las tablas `diseno` y `piezas` con los nuevos datos.

---

### UC4: Eliminar Proyecto (Extiende a UC3)

*   **Descripción:** Permite borrar de forma definitiva un diseño creado por el maestro que no haya sido publicado o auditado aún.
*   **Actor Principal:** Diseñador (Maestro)
*   **Relación:** Extiende a **UC3** (`<<Extend>>`) en el punto de extensión `Eliminar Proyecto`.
*   **Flujo Principal:**
    1. Durante la edición de un proyecto existente (UC3), el Diseñador hace clic en "Eliminar Proyecto".
    2. El sistema solicita confirmación mediante un cuadro de diálogo.
    3. El Diseñador confirma la acción.
    4. El sistema ejecuta una consulta DELETE de forma transaccional en la base de datos PostgreSQL, borrando en cascada las piezas, herrajes y pasos asociados.
*   **Flujos Alternativos:**
    *   **Flujo Alternativo A (Proyecto con progreso de estudiantes):** Si el diseño ya cuenta con registros activos en la tabla `progreso_alumno`, la base de datos retorna un error de integridad referencial. El sistema detiene el borrado y alerta al Diseñador que el proyecto no puede ser eliminado porque está siendo ensamblado por alumnos.
*   **Postcondiciones:** El diseño y todos sus elementos asociados se eliminan de la base de datos.

---

### UC5: Generar Lista de Despiece (Caso de Uso Base / Incluido)

*   **Descripción:** Desglosa de forma paramétrica las planchas de melamina en piezas de corte individuales en tiempo real.
*   **Actor Principal:** Sistema / Diseñador
*   **Puntos de Extensión:**
    *   `Calcular Melamina`: Permite calcular el consumo del tablero físico (UC6).
*   **Flujo Principal:**
    1. El sistema recupera las dimensiones externas ingresadas en el flujo de creación.
    2. El algoritmo paramétrico calcula el tamaño exacto de laterales, repisas, zócalos, fondos y puertas restando espesores y tapacantos.
    3. El sistema muestra la tabla desglosada en la pantalla.
*   **Postcondiciones:** La lista de piezas queda formateada y lista para su almacenamiento.

---

### UC6: Calcular Consumo de Melamina (Extiende a UC5)

*   **Descripción:** Realiza la estimación física de cuántas planchas completas de melamina requiere el mueble.
*   **Actor Principal:** Sistema / Diseñador
*   **Relación:** Extiende a **UC5** (`<<Extend>>`) en el punto de extensión `Calcular Melamina`.
*   **Flujo Principal:**
    1. Una vez generada la lista de despiece (UC5), el sistema calcula el área superficial total sumando el ancho por el alto de cada pieza.
    2. El sistema aplica un algoritmo de distribución de corte que divide la superficie total por el área útil de una placa de melamina estándar (2.97 m²).
    3. El sistema muestra en la pantalla la cantidad fraccionada de hojas necesarias.
*   **Postcondiciones:** El consumo estimado de material se asocia a la metainformación del diseño.

---

### UC10: Reproducir Guía Interactiva

*   **Descripción:** Permite al Estudiante guiar el ensamblaje del mueble en tiempo real utilizando el visor 3D paramétrico.
*   **Actor Principal:** Estudiante (Armador)
*   **Relación:** Incluye (`<<Include>>`) a **UC12**.
*   **Flujo Principal:**
    1. El Estudiante selecciona un mueble del catálogo, accede a la vista detallada (`student.html`) y presiona "Iniciar Guía de Ensamblaje".
    2. **[Inclusión UC12]:** El sistema registra en la base de datos el inicio del proyecto de ensamblaje para el estudiante (`progreso_alumno` en paso 1).
    3. El visor 3D renderiza de forma interactiva las piezas del paso actual.
    4. El Estudiante lee las instrucciones del paso físico, ejecuta la acción y presiona "Siguiente Paso".
    5. El sistema actualiza de forma asíncrona el paso en la base de datos e incrementa el porcentaje en pantalla.
    6. Al completar la totalidad de la secuencia, el estudiante presiona "Finalizar".
*   **Postcondiciones:** El proyecto físico queda ensamblado y el historial del alumno se actualiza a completado.
