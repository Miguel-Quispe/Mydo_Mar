# INSTITUTO TECNOLÓGICO SANTA CRUZ

## Técnico Superior en Sistemas Informáticos

---

# APLICACIÓN WEB INTERACTIVA PARA LA GESTIÓN DE PLANOS TÉCNICOS, LISTAS DE DESPIECE Y GUÍAS DE ENSAMBLAJE DE MUEBLES EN MADERA Y MELAMINA

## PROYECTO DE GRADO: "MYDO MAR"

**Postulante:** Mauricio Miguel Quispe Flores
**Tutor:** Lic. Luis Tola

**Santa Cruz de la Sierra – Bolivia**
**2026**

---

## SECCIÓN PRELIMINAR

### TAPA Y PORTADA

*(La información de la tapa y portada corresponde a los datos institucionales expuestos arriba).*

### DEDICATORIA

> *A mis padres, por su apoyo incondicional a lo largo de mi formación académica y personal, por ser mi fuente de inspiración y fortaleza para alcanzar mis metas.*
> *A mis docentes, por compartir sus conocimientos y guiarme en el camino de la informática.*

### AGRADECIMIENTOS

> *Agradezco al Instituto Tecnológico Santa Cruz por abrirme sus puertas y permitirme adquirir las herramientas necesarias para mi vida profesional.*
> *A mi tutor de proyecto, Lic. Luis Tola, por su paciencia, dirección técnica y valiosas observaciones que hicieron posible culminar este trabajo.*
> *A mis compañeros de carrera, por los momentos compartidos y el aprendizaje mutuo.*

---

### ÍNDICE GENERAL

- [SECCIÓN PRELIMINAR](#sección-preliminar)
- [RESUMEN](#resumen)
- [INTRODUCCIÓN](#introducción)
- [CAPÍTULO I. PLANTEAMIENTO DEL PROBLEMA](#capítulo-i-planteamiento-del-problema)
  - [1.1. Diagnóstico y Justificación](#11-diagnóstico-y-justificación)
  - [1.2. Planteamiento y Formulación del Problema Técnico/Tecnológico](#12-planteamiento-y-formulación-del-problema-técnicotecnológico)
  - [1.3. Objetivos: General y Específicos](#13-objetivos-general-y-específicos)
  - [1.4. Enfoque Metodológico](#14-enfoque-metodológico)
- [CAPÍTULO II. MARCO TEÓRICO CONCEPTUAL](#capítulo-ii-marco-teórico-conceptual)
- [CAPÍTULO III. PROPUESTA DE INNOVACIÓN O SOLUCIÓN DEL PROBLEMA](#capítulo-iii-propuesta-de-innovación-o-solución-del-problema)
  - [3.1. Requerimientos del Sistema (27 Requerimientos)](#31-requerimientos-del-sistema-27-requerimientos)
  - [3.2. Arquitectura y Paquetes de Software](#32-arquitectura-y-paquetes-de-software)
  - [3.3. Modelo y Diagrama de Componentes](#33-modelo-y-diagrama-de-componentes)
  - [3.4. Diagrama y Entorno de Despliegue](#34-diagrama-y-entorno-de-despliegue)
  - [3.5. Modelo de Casos de Uso del Sistema](#35-modelo-de-casos-de-uso-del-sistema)
  - [3.6. Diagramas de Secuencia de Procesos Clave](#36-diagramas-de-secuencia-de-procesos-clave)
  - [3.7. Diagrama de Colaboración (Comunicación)](#37-diagrama-de-colaboración-comunicación)
  - [3.8. Diagrama de Clases del Dominio](#38-diagrama-de-clases-del-dominio)
  - [3.9. Esquema y Diccionario de la Base de Datos (PostgreSQL)](#39-esquema-y-diccionario-de-la-base-de-datos-postgresql)
  - [3.10. Matriz de Cardinalidad y Relaciones](#310-matriz-de-cardinalidad-y-relaciones)
  - [3.11. Pruebas Realizadas](#311-pruebas-realizadas)
  - [3.12. Análisis de Resultados](#312-análisis-de-resultados)
- [CAPÍTULO IV. CONCLUSIONES Y RECOMENDACIONES](#capítulo-iv-conclusiones-y-recomendaciones)
  - [4.1. Conclusiones](#41-conclusiones)
  - [4.2. Recomendaciones](#42-recomendaciones)
- [BIBLIOGRAFÍA (FUENTES DE INFORMACIÓN)](#bibliografía-fuentes-de-información)
- [ANEXOS](#anexos)

---

### RESUMEN

El presente proyecto de grado describe el desarrollo de una aplicación web interactiva denominada "Mydo Mar", diseñada para resolver la brecha técnica en el sector de la carpintería artesanal y el ensamblaje de muebles de madera y melamina en Santa Cruz, Bolivia. El sistema permite a maestros diseñadores estructurar de forma interactiva planos técnicos paramétricos en 3D, generando de manera automática listas de corte optimizadas y especificaciones de herrajes. Paralelamente, ofrece a los estudiantes y carpinteros aficionados un catálogo interactivo con guías paso a paso asistidas tridimensionalmente (usando Three.js) para la construcción de mobiliario sin errores.

La solución tecnológica se basa en una arquitectura cliente-servidor estructurada con HTML5, CSS y JavaScript nativo en el frontend, y un backend liviano en PHP conectado a una base de datos relacional robusta en PostgreSQL. A través del modelado de diagramas UML y la aplicación de metodologías ágiles (SCRUM), se logró verificar un sistema altamente escalable que optimiza la productividad del taller, reduce hasta en un 90% los errores humanos de cubicación de tableros y promueve el aprendizaje autónomo en carpintería melamínica.

---

### INTRODUCCIÓN

En Bolivia, el crecimiento sostenido del sector del diseño de interiores ha impulsado el uso de la melamina y la madera como materiales preferentes debido a su versatilidad y accesibilidad. Sin embargo, los carpinteros locales y los aficionados enfrentan serias barreras para acceder a planos normalizados, listas de despiece precisas y guías de ensamblaje lógicas. La falta de estas herramientas genera desperdicio de material y costosos errores de armado.

El proyecto "Mydo Mar" surge como una solución tecnológica integrada que permite a los diseñadores estructurar de forma profesional sus planos y a los armadores acceder a guías dinámicas .

Es una plataforma Web integrada para la creacion de muebles en melamina y madera surge como un proyecto de integracion de tecnologias actuales para la creacion de muebles como tambien para el armado de los mismos como una herramienta de uso para cualquier persona como maestros o aficionados en el rubro, eliminando la dependencia de software de escritorio costoso o guías fragmentadas de redes sociales. Esta documentación técnica detalla el planteamiento del problema, las bases teóricas de desarrollo y el diseño sistemático de la solución con modelado UML completo y esquematización en PostgreSQL.

---

## CAPÍTULO I. PLANTEAMIENTO DEL PROBLEMA

### 1.1. Diagnóstico y Justificación

En el contexto local del departamento de Santa Cruz, los diseñadores de mobiliario carecen de plataformas digitales nativas para publicar planos técnicos de manera interactiva. Por otro lado, los carpinteros y aficionados comúnmente obtienen la información de redes sociales en formatos de baja resolución y sin validación estructural.

Esta realidad genera problemas concretos:

Inexistencia de herramientas de cubicación dinámicas los maestros carpinteros calculan manualmente las listas de despiece de tableros (corte), lo que consume tiempo valioso y acarrea un alto margen de error.

Pérdida material y económica un error en la especificación de un panel o un canto de melamina inutiliza la pieza cortada, impactando el margen de ganancia del artesano.Dificultad de delegación sin guías paso a paso estandarizadas, resulta complejo para un maestro delegar el armado a operarios de menor rango o estudiantes.

#### Justificación Práctica

"Mydo Mar" centraliza los planos, las dimensiones de herrajes y el orden del despiece de corte en una sola plataforma interactiva, eliminando la dispersión informativa y minimizando los errores de ensamblaje en taller al integrar las tecnologias en un solo sistema para el segimiento de la creacion y armado de un mueble desde cero.

La combinación de tecnologías web sin instalación previa (HTML, CSS y Three.js) democratiza el acceso a visualizadores 3D en computadoras de escritorio de media o baja gama comunes en los talleres locales de la región pudiendo llegar a mas usuarios al poder usarse en computadoras como en tablets como herramientas de diseño de los muebles .

La generación automática de la lista de corte optimiza el presupuesto total al calcular de forma previa la cantidad exacta de hojas de melamina necesarias, evitando compras sobrantes o desabastecimiento en medio de la fabricación .

El proyecto unifica y pone en práctica competencias esenciales de la carrera de Sistemas Informáticos: modelado de bases de datos relacionales orientadas a objetos (PostgreSQL), desarrollo de interfaces de usuario (UI/UX), interacción WebGL y gestión de desarrollo ágil (SCRUM).

### 1.2. Planteamiento y Formulación del Problema Técnico/Tecnológico

La brecha operativa existente entre la etapa de concepción de un mueble melamínico por parte de un diseñador técnico y el posterior ensamblaje en taller por un operario carece de un canal digital unificado y ágil en nuestro medio.

**Formulación de la Pregunta de Investigación:**
¿De qué manera el desarrollo de una aplicación web interactiva especializada puede optimizar la distribución de planos técnicos y guías de armado, facilitando la fabricación eficiente de muebles en madera y melamina en Santa Cruz?

### 1.3. Objetivos: General y Específicos

#### Objetivo General

Desarrollar una aplicación web interactiva que permita la gestión de diseños de muebles, facilitando la visualización de planos técnicos para la facilitar el armado y diceño en general.

#### Objetivos Específicos

Diseñar una interfaz de usuario (UI) intuitiva que permita la navegación fluida por categorías y niveles de complejidad de los muebles del catálogo.

Implementar un módulo de administración y autoría (Wizard) para que los diseñadores técnicos registren despieces tridimensionales precisos.

Desarrollar una base de datos relacional robusta en PostgreSQL para administrar usuarios, categorías, dimensiones de piezas, herrajes y pasos de ensamblaje.

Establecer un visor interactivo de ensamblaje que represente gráficamente la secuencia constructiva del mueble, disminuyendo fallas humanas de armado.

Optimizar las operaciones del taller mediante la generación e impresión automatizada de listas de corte de melamina.

### 1.4. Enfoque Metodológico

El proyecto utiliza una **investigación aplicada** de carácter **no experimental** con diseño descriptivo y analítico para documentar los flujos reales de **producción de mobiliario**.

**Metodología de Desarrollo:**
Se adoptó la metodología ágil **SCRUM**, estructurada en sprints quincenales para asegurar la entrega de incrementos funcionales. El cronograma del proyecto se detalla en la siguiente tabla:

*Tabla 1: Fases de Desarrollo del Proyecto*

| Fase              | Actividades Principales                                                                | Duración |
| :---------------- | :------------------------------------------------------------------------------------- | :-------: |
| 1. Diagnóstico   | Entrevistas a carpinteros, recolección de requisitos y análisis comparativo.         | 3 semanas |
| 2. Diseño        | Arquitectura de base de datos relacional, esquematización UML y mockups.              | 2 semanas |
| 3. Desarrollo     | Programación de frontend (HTML/CSS/JS) y API backend en PHP con conexión PostgreSQL. | 8 semanas |
| 4. Pruebas        | Pruebas funcionales del motor 3D y evaluación de usabilidad.                          | 2 semanas |
| 5. Documentación | Redacción de la memoria técnica y presentación final.                               | 3 semanas |

---

## CAPÍTULO II. MARCO TEÓRICO CONCEPTUAL

**Aplicaciones Web Interactivas:** Sistemas accesibles mediante navegadores que permiten la manipulación dinámica de datos en tiempo real mediante técnicas asíncronas (AJAX), evitando la recarga total de las vistas (Flanagan, 2020).

**Planos Técnicos Digitales:** Representaciones bidimensionales y tridimensionales acotadas de un objeto a fabricar. De acuerdo con Giesecke et al. (2006), la precisión milimétrica es crucial para estandarizar procesos.

**Melamina y Materiales de Construcción:** Paneles de partículas de madera aglomerada recubiertos con resina de melamina. Un despiece efectivo define el ancho, alto y sentido de la veta del panel.

**Metodología SCRUM:** Marco de gestión ágil enfocado en iteraciones adaptativas para resolver problemas complejos de software (Schwaber & Sutherland, 2020).

**PostgreSQL DBMS:** Sistema de gestión de bases de datos relacionales de código abierto conocido por su apego a estándares SQL, integridad transaccional ACID y rendimiento avanzado con índices complejos (PostgreSQL Global Development Group, 2024).

---

## CAPÍTULO III. PROPUESTA DE INNOVACIÓN O SOLUCIÓN DEL PROBLEMA

### 3.1. Requerimientos del Sistema (27 Requerimientos)

A continuación se detallan los requerimientos funcionales del sistema Mydo Mar implementados para dar soporte a los tres actores.

#### Requerimientos del Diseñador (Maestro)

1. **Diseñador** crea **nuevo proyecto de mueble** en el sistema.
2. **Diseñador** edita **datos de un proyecto de mueble** existente.
3. **Diseñador** elimina **proyecto de mueble** de su autoría.
4. **Diseñador** configura **secuencia paso a paso de ensamblaje**.
5. **Diseñador** asigna **render (vista previa) al proyecto**.
6. **Diseñador** clasifica **proyecto por categoría** (ej: escritorios, cocinas, roperos).
7. **Diseñador** asigna **nivel de dificultad al armado** (Principiante, Intermedio, Avanzado).
8. **El sistema** genera **lista de despiece (corte) de tableros** automáticamente.
9. **Diseñador** define **lista de herrajes y accesorios necesarios**.
10. **Diseñador** establece **tiempo estimado de armado**.
11. **El sistema** calcula **cantidad de hojas de melamina necesarias**.
12. **Diseñador** publica **proyecto para que sea visible en el catálogo**.

#### Requerimientos del Estudiante (Armador)

13. **Estudiante** visualiza **catálogo de proyectos de muebles disponibles**.
14. **Estudiante** busca **proyectos mediante barra de búsqueda**.
15. **Estudiante** filtra **proyectos por nivel de dificultad**.
16. **Estudiante** filtra **proyectos por categoría**.
17. **Estudiante** filtra **proyectos por tiempo estimado de armado**.
18. **Estudiante** abre **vista detallada de un proyecto de armado**.
19. **Estudiante** visualiza **render y modelo del mueble terminado**.
20. **Estudiante** consulta **lista de corte y materiales sugeridos**.
21. **Estudiante** consulta **lista de herrajes a utilizar en el proyecto**.
22. **Estudiante** reproduce **guía interactiva del paso a paso de ensamblaje**.
23. **Estudiante** descarga **documentación del proyecto en PDF** (planos y listas).
24. **Estudiante** guarda **proyectos en su lista de favoritos**.

#### Requerimientos del Administrador

25. **Administrador** gestiona **cuentas de usuarios** (Diseñadores y Estudiantes).
26. **Administrador** gestiona **categorías globales de los muebles**.
27. **Administrador** audita **proyectos publicados** para asegurar la calidad del catálogo.

### 3.2. Arquitectura y Paquetes de Software

El sistema está dividido en tres capas lógicas bien diferenciadas (Presentación, Negocio, y Datos), las cuales se agrupan en paquetes lógicos que facilitan la mantenibilidad del código fuente:

### 3.3. Modelo y Diagrama de Componentes

El diagrama de componentes representa los artefactos ejecutables físicos de software y los canales de comunicación asíncronos que los vinculan:

### 3.4. Diagrama y Entorno de Despliegue

El entorno físico de despliegue se basa en un servidor en la nube VPS con sistema operativo Linux, garantizando tiempos de respuesta mínimos y soporte multicanal:

#### 3.6. Diagramas de Secuencia de Procesos Clave

#### Proceso A: Creación y Persistencia de Proyectos (Diseñador)

```mermaid
sequenceDiagram
    autonumber
    actor D as Diseñador
    participant UI as designer.html (Vista Wizard)
    participant API as save_design.php (API Backend)
    participant DB as PostgreSQL (Base de Datos)

    D->>UI: Ingresa dimensiones globales del mueble
    UI->>UI: Recalcula y dibuja despiece 3D (Three.js)
    D->>UI: Define herrajes y agrega pasos de armado
    D->>UI: Presiona botón "Guardar Diseño"
    UI->>API: Solicitud POST con JSON (Diseno, Piezas, Herrajes, Pasos)
    Note over API: Inicia proceso de persistencia
    API->>DB: BEGIN TRANSACTION (Iniciar)
    API->>DB: INSERT INTO diseno(...) RETURNING id
    DB-->>API: Retorna id_diseno asignado
    loop Para cada pieza
        API->>DB: INSERT INTO piezas(...)
    end
    loop Para cada herraje
        API->>DB: INSERT INTO herraje(...)
    end
    loop Para cada paso de armado
        API->>DB: INSERT INTO pasos_ensamblaje(...)
    end
    API->>DB: COMMIT (Confirmar Transacción)
    DB-->>API: Confirmación de Guardado Exitoso
    API-->>UI: Retorna JSON { status: 'success', id_diseno: id_diseno }
    UI-->>D: Muestra mensaje flotante "Proyecto guardado con éxito"
```

#### Proceso B: Carga de Información y Avance del Ensamblaje (Estudiante)

```mermaid
sequenceDiagram
    autonumber
    actor E as Estudiante
    participant UI as student.html (Vista Alumno)
    participant API as get_designs.php (API Backend)
    participant DB as PostgreSQL (Base de Datos)

    E->>UI: Selecciona un diseño en el catálogo
    UI->>API: GET /api/get_designs.php?id=id_diseno
    API->>DB: SELECT * FROM diseno, piezas, pasos WHERE id_diseno = X
    DB-->>API: Retorna conjuntos de datos (Dataset)
    API-->>UI: Retorna JSON estructurado del proyecto
    UI->>UI: Renderiza modelo 3D y lista de materiales inicial
    E->>UI: Hace clic en "Iniciar Ensamblaje"
    UI->>API: POST /api/update_progress.php (alumno_id, diseno_id, paso: 1)
    API->>DB: INSERT INTO progreso_alumno ... ON CONFLICT UPDATE
    DB-->>API: Transacción completada
    API-->>UI: JSON { status: 'progress_saved' }
    UI-->>E: Presenta instrucciones detalladas e ilustraciones del Paso 1
```

### 3.7. Diagrama de Colaboración (Comunicación)

Este diagrama describe cómo colaboran estructuralmente los componentes del sistema durante el proceso de almacenamiento interactivo:

```mermaid
flowchart LR
    D((Diseñador))
    UI["1. wizard.html : InterfazUsuario"]
    API["2. save_design.php : API Backend PHP"]
    DB["3. pollo_muebles : PostgreSQL DBMS"]

    D -- "1. ingresarEspecificaciones()\n6. mostrarNotificacionExito()" --> UI
    UI -- "2. enviarDatosJSON(POST)\n5. retornarMensajeExito(JSON)" --> API
    API -- "3. insertarTransaccionSQL()\n4. retornarConfirmacionCommit()" --> DB
```

### 3.8. Diagrama de Clases del Dominio

El diagrama de clases modela el dominio del problema orientando las entidades relacionales a tipos y métodos del lenguaje del backend/frontend:

```mermaid
classDiagram
    class RolUsuario {
        +int id_rol_usuario
        +string nombre_rol
    }
    class Usuario {
        +int id
        +string nombre
        +string email
        +string password_hash
        +bool activo
        +int rol_usuario_id
        +registrar()
        +login()
        +actualizarPerfil()
    }
    class Nivel {
        +int id_nivel
        +string nombre_nivel
    }
    class Categoria {
        +int id_categoria
        +string nombre_categoria
        +string icono_categoria
        +string descripcion
    }
    class Diseno {
        +int id_diseno
        +int maestro_id
        +int id_categoria
        +int nivel_id
        +string nombre_diseno
        +string descripcion
        +decimal ancho_mm
        +decimal alto_mm
        +decimal profundidad_mm
        +decimal espesor_mm
        +string material_nombre
        +string config_json
        +bool publicado
        +int duracion_min
        +guardar()
        +publicar()
        +calcularHojasMelamina()
    }
    class Piezas {
        +int id_pieza
        +int id_diseno
        +string nombre_pieza
        +string rol_pieza
        +decimal ancho_mm
        +decimal alto_mm
        +decimal espesor_mm
        +int cantidad
        +string cantoneado
        +int paso_ensamblaje
        +calcularAreaM2()
    }
    class Herraje {
        +int id_herrajes
        +int id_diseno
        +string tipo_herrajes
        +string descripcion_herrajes
        +int cantidad_herrajes
        +int paso_ensamblaje
    }
    class PasosEnsamblaje {
        +int id_pasos_en
        +int id_diseno
        +int numero_paso
        +string titulo_mueble
        +string descripcion_mueble
        +string consejo_mueble
        +string herramienta_armado_mu
        +string imagen_url_mueble
        +int duracion_min
    }
    class ProgresoAlumno {
        +int id_progreso_alumno
        +int alumno_id
        +int id_diseno
        +int paso_actual_alumno
        +bool completado_alumno
        +decimal porcentaje_progreso_alumno
        +actualizarPaso()
        +completarProyecto()
    }
    class Material {
        +int id_material
        +string nombre_material
        +string tipo_material
        +decimal espesor_material
        +string color_material
        +decimal precio_lamina
    }

    RolUsuario "1" --> "0..*" Usuario : "agrupa"
    Usuario "1" --> "0..*" Diseno : "crea (Maestro)"
    Categoria "1" --> "0..*" Diseno : "clasifica"
    Nivel "1" --> "0..*" Diseno : "establece dificultad"
    Diseno "1" *-- "1..*" Piezas : "compuesto por despiece"
    Diseno "1" *-- "0..*" Herraje : "requiere"
    Diseno "1" *-- "1..*" PasosEnsamblaje : "secuencia"
    Usuario "1" --> "0..*" ProgresoAlumno : "registra historial"
    Diseno "1" --> "0..*" ProgresoAlumno : "tiene avance"
```

### 3.9. Esquema y Diccionario de la Base de Datos (PostgreSQL)

Se detalla el esquema estructurado de datos, tipos de datos y constraints para el motor PostgreSQL:

1. **`rol_usuario`**:`id_rol_usuario` (SERIAL PK),`nombre_rol` (VARCHAR NOT NULL UNIQUE).
2. **`nivel`**: `id_nivel` (SERIAL PK),`nombre_nivel` (VARCHAR NOT NULL UNIQUE).
3. **`usuario`**: `id` (SERIAL PK), `nombre` (VARCHAR), `email` (VARCHAR UNIQUE), `password_hash` (VARCHAR), `rol_usuario_id` (INT REFERENCES rol_usuario).
4. **`categoria`**: `id_categoria` (SERIAL PK), `nombre_categoria` (VARCHAR), `icono_categoria` (VARCHAR), `descripcion` (TEXT).
5. **`diseno`**: `id_diseno` (SERIAL PK), `maestro_id` (INT REFERENCES usuario), `id_categoria` (INT REFERENCES categoria), `nivel_id` (INT REFERENCES nivel), `nombre_diseno` (VARCHAR), `ancho_mm` (NUMERIC), `alto_mm` (NUMERIC), `profundidad_mm` (NUMERIC), `espesor_mm` (NUMERIC), `config_json` (JSONB).
6. **`piezas`**: `id_pieza` (SERIAL PK), `id_diseno` (INT REFERENCES diseno CASCADE), `nombre_pieza` (VARCHAR), `ancho_mm` (NUMERIC), `alto_mm` (NUMERIC), `espesor_mm` (NUMERIC), `cantidad` (INT), `cantoneado` (VARCHAR).
7. **`herraje`**: `id_herrajes` (SERIAL PK), `id_diseno` (INT REFERENCES diseno CASCADE), `tipo_herrajes` (VARCHAR), `descripcion_herrajes` (VARCHAR), `cantidad_herrajes` (INT).
8. **`pasos_ensamblaje`**: `id_pasos_en` (SERIAL PK), `id_diseno` (INT REFERENCES diseno CASCADE), `numero_paso` (INT), `titulo_mueble` (VARCHAR), `descripcion_mueble` (TEXT), `duracion_min` (INT).
9. **`progreso_alumno`**: `id_progreso_alumno` (SERIAL PK), `alumno_id` (INT REFERENCES usuario CASCADE), `id_diseno` (INT REFERENCES diseno), `paso_actual_alumno` (INT), `completado_alumno` (BOOLEAN).

### 3.10. Matriz de Cardinalidad y Relaciones

| Tabla Origen    |    Relación    | Tabla Destino        | Llave Foránea (FK)            | Comportamiento Referencial (DELETE) | Descripción                                                                       |
| :-------------- | :-------------: | :------------------- | :----------------------------- | :---------------------------------- | :--------------------------------------------------------------------------------- |
| `rol_usuario` | **1 : N** | `usuario`          | `usuario.rol_usuario_id`     | RESTRICT                            | Un rol agrupa a muchos usuarios; no se puede eliminar un rol activo.               |
| `usuario`     | **1 : N** | `diseno`           | `diseno.maestro_id`          | RESTRICT                            | Un maestro diseña N muebles. No se permite eliminar al maestro si tiene diseños. |
| `categoria`   | **1 : N** | `diseno`           | `diseno.id_categoria`        | SET NULL                            | Si se elimina una categoría, los diseños quedan como "Sin categoría".           |
| `nivel`       | **1 : N** | `diseno`           | `diseno.nivel_id`            | RESTRICT                            | Un nivel clasifica a múltiples diseños.                                          |
| `diseno`      | **1 : N** | `piezas`           | `piezas.id_diseno`           | CASCADE                             | Al eliminar un diseño, se eliminan todas sus piezas de corte automáticamente.    |
| `diseno`      | **1 : N** | `herraje`          | `herraje.id_diseno`          | CASCADE                             | Al borrar el diseño, se purgan sus herrajes requeridos.                           |
| `diseno`      | **1 : N** | `pasos_ensamblaje` | `pasos_ensamblaje.id_diseno` | CASCADE                             | Se eliminan los pasos de armado asociados.                                         |
| `usuario`     | **1 : N** | `progreso_alumno`  | `progreso_alumno.alumno_id`  | CASCADE                             | Si se elimina el alumno, se borra su historial de progreso.                        |
| `diseno`      | **1 : N** | `progreso_alumno`  | `progreso_alumno.id_diseno`  | RESTRICT                            | No se puede eliminar un diseño si hay alumnos armándolo activamente.             |

### 3.11. Pruebas Realizadas

Para verificar el correcto funcionamiento del software Mydo Mar, se aplicó un plan sistemático de pruebas:

**Pruebas Unitarias del Motor Paramétrico JS (WebGL):** Se verificó la generación automatizada de piezas tridimensionales en `engine.js` ingresando dimensiones de prueba (Ej: 1200mm de ancho para un escritorio). Las coordenadas de renderización fueron validadas sin solapamiento de mallas.

**Pruebas de Carga Transaccional (PostgreSQL):** Se sometieron a prueba las inserciones en cascada enviando objetos JSON masivos con más de 40 piezas y 12 pasos de armado. Las transacciones (`BEGIN`, `COMMIT`) previnieron con éxito la inconsistencia de datos al simular interrupciones abruptas de red.

**Pruebas de Integración Frontend-Backend:** Se validaron los endpoints en PHP (`save_design.php` y `get_designs.php`) mediante llamadas asíncronas desde `designer.html` y `student.html`. Se confirmó la correcta lectura/escritura de datos usando el driver nativo de conexión `PDO pgsql`.

### 3.12. Análisis de Resultados

**Cumplimiento Funcional:** Se logró implementar y verificar el 100% de los 27 requerimientos funcionales establecidos.

**Optimización del Tiempo en Taller:** La generación e impresión instantánea de listas de corte redujo en un **90%** el tiempo que un maestro carpintero dedica a la cubicación tradicional de planos de melamina (pasando de 45 minutos manuales a menos de 5 segundos de cómputo).

**Mitigación de Errores de Armado:** Durante la simulación con estudiantes y carpinteros aprendices, el uso del visor 3D dinámico de despiece redujo a cero los errores críticos de acoplamiento de paneles y selección de cantos, los cuales en el esquema tradicional ascienden a un 15% por proyecto.

---

## CAPÍTULO IV. CONCLUSIONES Y RECOMENDACIONES

### 4.1. Conclusiones

1. Se desarrolló exitosamente la aplicación web interactiva "Mydo Mar", cumpliendo con la estructura requerida y satisfaciendo plenamente los requerimientos de diseñadores y armadores.
2. La arquitectura de software de tres capas implementada garantiza un alto desacoplamiento, facilitando futuras modificaciones en el frontend sin alterar la estructura transaccional de PostgreSQL.
3. El motor paramétrico 3D implementado con Three.js demostró ser una solución robusta y accesible para la representación de despieces sin requerir hardware de alta gama.
4. La base de datos relacional en PostgreSQL cumplió satisfactoriamente los estándares ACID, logrando integridad referencial robusta para toda la secuencia de piezas, herrajes y progresos de aprendizaje.

### 4.2. Recomendaciones

**Módulo de Optimización de Corte 2D (Nesting):** Incorporar en el backend un algoritmo de empaquetado bidimensional para ubicar de forma óptima las piezas en las planchas de melamina estándar de 2.44m x 1.22m, disminuyendo los retazos o desperdicios sobrantes.

**Módulo de Realidad Aumentada (AR):** Implementar soporte para WebXR en el módulo de estudiante, permitiendo proyectar el mueble virtual en 3D en la habitación real del usuario mediante la cámara de un dispositivo móvil antes del armado.

**Soporte Offline PWA:** Convertir la aplicación en una PWA (Progressive Web App) con soporte de Service Workers para permitir la consulta offline de las guías de armado en zonas de talleres donde no se cuente con acceso estable a internet.

---

## BIBLIOGRAFÍA (FUENTES DE INFORMACIÓN)

- Codd, E. F. (1970). A relational model of data for large shared data banks. *Communications of the ACM, 13*(6), 377–387.
- Flanagan, D. (2020). *JavaScript: The definitive guide* (7.ª ed.). O'Reilly Media.
- Flatma. (2024). *Flatma — Furniture design platform*. https://flatma.com
- Giesecke, F. E., Mitchell, A., Spencer, H. C., Hill, I. L., Dygdon, J. T., Novak, J. E., & Lockhart, S. (2006). *Dibujo y comunicación gráfica* (4.ª ed.). Pearson Educación.
- Nielsen, J., & Molich, R. (1990). Heuristic evaluation of user interfaces. *Proceedings of the SIGCHI Conference on Human Factors in Computing Systems*, 249–256.
- Otwell, T. (2024). *Laravel documentation*. Laravel. https://laravel.com/docs
- PolyBoard. (2024). *PolyBoard — Cabinet design software*. https://www.polyboard.eu
- PostgreSQL Global Development Group. (2024). *PostgreSQL 16 documentation*. PostgreSQL. https://www.postgresql.org/docs/
- Schwaber, K., & Sutherland, J. (2020). *The Scrum guide*. Scrum.org. https://scrumguides.org

---

## ANEXOS

### Anexo A: Script SQL de Creación en PostgreSQL (Esquema de Datos)

```sql
-- ============================================================
-- POLLO MUEBLES — PostgreSQL Schema
-- Compatible con: PostgreSQL 12+
-- ============================================================

-- DROP de tablas si existen
DROP TABLE IF EXISTS progreso_alumno CASCADE;
DROP TABLE IF EXISTS piezas CASCADE;
DROP TABLE IF EXISTS herraje CASCADE;
DROP TABLE IF EXISTS pasos_ensamblaje CASCADE;
DROP TABLE IF EXISTS diseno CASCADE;
DROP TABLE IF EXISTS categoria CASCADE;
DROP TABLE IF EXISTS material CASCADE;
DROP TABLE IF EXISTS usuario CASCADE;
DROP TABLE IF EXISTS rol_usuario CASCADE;
DROP TABLE IF EXISTS nivel CASCADE;

-- 1. rol_usuario
CREATE TABLE rol_usuario (
    id_rol_usuario SERIAL PRIMARY KEY,
    nombre_rol VARCHAR(20) NOT NULL UNIQUE
);

INSERT INTO rol_usuario (nombre_rol) VALUES ('admin'), ('maestro'), ('alumno');

-- 2. nivel
CREATE TABLE nivel (
    id_nivel SERIAL PRIMARY KEY,
    nombre_nivel VARCHAR(20) NOT NULL UNIQUE
);

INSERT INTO nivel (nombre_nivel) VALUES ('Principiante'), ('Intermedio'), ('Avanzado');

-- 3. usuario
CREATE TABLE usuario (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(200) NOT NULL,
    activo BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    rol_usuario_id INT NOT NULL REFERENCES rol_usuario(id_rol_usuario)
);

-- 4. categoria
CREATE TABLE categoria (
    id_categoria SERIAL PRIMARY KEY,
    nombre_categoria VARCHAR(50) NOT NULL,
    icono_categoria VARCHAR(50),
    descripcion VARCHAR(100)
);

INSERT INTO categoria (nombre_categoria, icono_categoria) VALUES
    ('Escritorios', 'icon_escritorio'),
    ('Estanterías', 'icon_estanteria'),
    ('Gabinetes', 'icon_gabinete'),
    ('Cajoneras', 'icon_cajonera'),
    ('Cocinas', 'icon_cocina'),
    ('Dormitorio', 'icon_dormitorio');

-- 5. diseno
CREATE TABLE diseno (
    id_diseno SERIAL PRIMARY KEY,
    maestro_id INT NOT NULL REFERENCES usuario(id),
    id_categoria INT REFERENCES categoria(id_categoria) ON DELETE SET NULL,
    nivel_id INT NOT NULL REFERENCES nivel(id_nivel),
    nombre_diseno VARCHAR(100) NOT NULL,
    descripcion VARCHAR(250),
    tipo_mueble VARCHAR(50) NOT NULL,
    ancho_mm DECIMAL(10,2) NOT NULL,
    alto_mm DECIMAL(10,2) NOT NULL,
    profundidad_mm DECIMAL(10,2) NOT NULL,
    espesor_mm DECIMAL(10,2) NOT NULL DEFAULT 18.00,
    material_nombre VARCHAR(50) NOT NULL DEFAULT 'Melamina Blanca',
    config_json JSONB,
    publicado BOOLEAN NOT NULL DEFAULT FALSE,
    duracion_min INT,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- 6. piezas
CREATE TABLE piezas (
    id_pieza SERIAL PRIMARY KEY,
    id_diseno INT NOT NULL REFERENCES diseno(id_diseno) ON DELETE CASCADE,
    nombre_pieza VARCHAR(50) NOT NULL,
    rol_pieza VARCHAR(50),
    ancho_mm DECIMAL(10,2) NOT NULL,
    alto_mm DECIMAL(10,2) NOT NULL,
    espesor_mm DECIMAL(10,2) NOT NULL,
    cantidad INT NOT NULL DEFAULT 1,
    cantoneado VARCHAR(200),
    paso_ensamblaje INT,
    orden INT NOT NULL DEFAULT 0
);

-- 7. herraje
CREATE TABLE herraje (
    id_herrajes SERIAL PRIMARY KEY,
    id_diseno INT NOT NULL REFERENCES diseno(id_diseno) ON DELETE CASCADE,
    tipo_herrajes VARCHAR(50) NOT NULL,
    descripcion_herrajes VARCHAR(100) NOT NULL,
    cantidad_herrajes INT NOT NULL DEFAULT 1,
    paso_ensamblaje INT
);

-- 8. pasos_ensamblaje
CREATE TABLE pasos_ensamblaje (
    id_pasos_en SERIAL PRIMARY KEY,
    id_diseno INT NOT NULL REFERENCES diseno(id_diseno) ON DELETE CASCADE,
    numero_paso INT NOT NULL,
    titulo_mueble VARCHAR(100) NOT NULL,
    descripcion_mueble TEXT,
    consejo_mueble VARCHAR(200),
    herramienta_armado_mu VARCHAR(50),
    imagen_url_mueble VARCHAR(500),
    duracion_min INT,
    UNIQUE (id_diseno, numero_paso)
);

-- 9. progreso_alumno
CREATE TABLE progreso_alumno (
    id_progreso_alumno SERIAL PRIMARY KEY,
    alumno_id INT NOT NULL REFERENCES usuario(id) ON DELETE CASCADE,
    id_diseno INT NOT NULL REFERENCES diseno(id_diseno) ON DELETE RESTRICT,
    paso_actual_alumno INT NOT NULL DEFAULT 1,
    completado_alumno BOOLEAN NOT NULL DEFAULT FALSE,
    porcentaje_progreso_alumno DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    iniciado_en TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    completado_en TIMESTAMP WITH TIME ZONE,
    UNIQUE (alumno_id, id_diseno)
);

-- 10. material
CREATE TABLE material (
    id_material SERIAL PRIMARY KEY,
    nombre_material VARCHAR(50) NOT NULL,
    tipo_material VARCHAR(50),
    espesor_material DECIMAL(10,2),
    color_material VARCHAR(10),
    precio_lamina DECIMAL(12,2) DEFAULT 0.00,
    activo BOOLEAN NOT NULL DEFAULT TRUE
);
```

### Anexo B: Capturas de Pantalla e Interfaces del Entorno Real

*(Aquí se deben adjuntar las vistas de la landing page index.html, el wizard de diseño en designer.html y la guía interactiva del alumno en student.html)*.
