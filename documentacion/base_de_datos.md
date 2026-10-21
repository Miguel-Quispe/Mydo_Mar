# Estructura de la Base de Datos: PolloMuebles

**Motor:** SQL Server 2016+  
**Base de datos:** `PolloMuebles` | Collation: `Latin1_General_CI_AI`  
**Proyecto:** Instituto Tecnológico Santa Cruz — Carrera: Sistemas Informáticos  
**Estudiante:** Mauricio Miguel Quispe Flores — Santa Cruz, Bolivia 2026

---

## Tablas de Catálogo (Lookup)

### 1. Tabla: `rolUsuario`

Define los roles posibles del sistema. Evita combinaciones inválidas.

| Columna | Tipo | Restricción | Descripción |
|---|---|---|---|
| `id_RolUsuario` | INT IDENTITY(1,1) | PK | Identificador del rol |
| `nombre_Rol` | NVARCHAR(20) | NOT NULL, UNIQUE | `'admin'`, `'maestro'`, `'alumno'` |

---

### 2. Tabla: `Nivel`

Define los niveles de dificultad de los diseños.

| Columna | Tipo | Restricción | Descripción |
|---|---|---|---|
| `Id_Nivel` | INT IDENTITY(1,1) | PK | Identificador del nivel |
| `nombre_Nivel` | NVARCHAR(20) | NOT NULL, UNIQUE | `'Principiante'`, `'Intermedio'`, `'Avanzado'` |

---

### 3. Tabla: `Categorias`

Clasifica los tipos de muebles que se pueden diseñar.

| Columna | Tipo | Restricción | Descripción |
|---|---|---|---|
| `id_Categoria` | INT IDENTITY(1,1) | PK | Identificador de categoría |
| `nombre_Categoria` | NVARCHAR(50) | NOT NULL | Nombre de la categoría |
| `icono_Categoria` | NVARCHAR(50) | NULL | Icono representativo |
| `descripcion` | NVARCHAR(100) | NULL | Descripción opcional |

**Datos iniciales:** Escritorios, Estanterías, Gabinetes, Cajoneras, Cocinas, Dormitorio

---

### 4. Tabla: `Materiales`

Catálogo independiente de materias primas disponibles.

| Columna | Tipo | Restricción | Descripción |
|---|---|---|---|
| `Id_Material` | INT IDENTITY(1,1) | PK | Identificador del material |
| `nombre_Material` | NVARCHAR(50) | NOT NULL | Nombre del material |
| `tipo_Material` | NVARCHAR(50) | NULL | `melamina`, `mdf`, `madera`, `triplay` |
| `espesor_Material` | DECIMAL(10,2) | NULL | Espesor en mm |
| `color_Material` | NVARCHAR(10) | NULL | Color en hexadecimal |
| `lamina_ancho_mm` | INT | DEFAULT 2440 | Ancho estándar de lámina |
| `lamina_alto_mm` | INT | DEFAULT 1220 | Alto estándar de lámina |
| `precio_lamina` | DECIMAL(12,2) | DEFAULT 0 | Precio por lámina |
| `moneda` | NVARCHAR(5) | DEFAULT `'USD'` | Moneda del precio |
| `proveedor` | NVARCHAR(100) | NULL | Nombre del proveedor |
| `activo` | BIT | NOT NULL, DEFAULT 1 | Disponible para usar |

---

## Tablas Principales

### 5. Tabla: `Usuarios`

Almacena todos los usuarios: maestros, alumnos y administradores.

| Columna | Tipo | Restricción | Descripción |
|---|---|---|---|
| `id` | INT IDENTITY(1,1) | PK | Identificador único |
| `nombre` | NVARCHAR(50) | NOT NULL | Nombre completo |
| `email` | NVARCHAR(150) | NOT NULL, UNIQUE | Correo de acceso |
| `password_hash` | NVARCHAR(200) | NOT NULL | Contraseña encriptada (hash) |
| `activo` | BIT | NOT NULL, DEFAULT 1 | Estado de la cuenta |
| `created_at` | DATETIME2 | DEFAULT GETDATE() | Fecha de creación |
| `updated_at` | DATETIME2 | DEFAULT GETDATE() | Última modificación |
| `rolUsuario_id` | INT | FK → rolUsuario(id_RolUsuario) | Rol del usuario |

---

### 6. Tabla: `Disenos`

Núcleo del sistema. Guarda cada proyecto de mueble creado por un maestro.

| Columna | Tipo | Restricción | Descripción |
|---|---|---|---|
| `Id_Diseno` | INT IDENTITY(1,1) | PK | Identificador del diseño |
| `maestro_id` | INT | FK → Usuarios(id) | Maestro que lo creó |
| `id_Categoria` | INT | FK → Categorias(id_Categoria), NULL | Categoría del mueble |
| `Nivel_id` | INT | FK → Nivel(Id_Nivel), NOT NULL | Nivel de dificultad |
| `nombre_Diseno` | NVARCHAR(100) | NOT NULL | Nombre del proyecto |
| `descripcion` | NVARCHAR(250) | NULL | Descripción del mueble |
| `tipo_mueble` | NVARCHAR(50) | NOT NULL | escritorio, estanteria, gabinete, cajonera |
| `ancho_mm` | DECIMAL(10,2) | NOT NULL | Ancho externo en mm |
| `alto_mm` | DECIMAL(10,2) | NOT NULL | Alto externo en mm |
| `profundidad_mm` | DECIMAL(10,2) | NOT NULL | Profundidad en mm |
| `espesor_mm` | DECIMAL(10,2) | NOT NULL, DEFAULT 18 | Espesor del tablero |
| `material_nombre` | NVARCHAR(50) | NOT NULL | Material principal |
| `material_color` | NVARCHAR(10) | NULL | Color hex del material |
| `config_json` | NVARCHAR(MAX) | NULL | Configuración extendida (cajones, estantes…) |
| `publicado` | BIT | NOT NULL, DEFAULT 0 | Disponible para alumnos |
| `duracion_min` | INT | NULL | Tiempo estimado de ensamblaje |
| `created_at` | DATETIME2 | DEFAULT GETDATE() | Fecha de creación |
| `updated_at` | DATETIME2 | DEFAULT GETDATE() | Última modificación |

---

## Tablas Hijas (dependen de `Disenos`)

### 7. Tabla: `Piezas` (Lista de Corte)

Despiece de cada diseño para fabricar o comprar la melamina.

| Columna | Tipo | Restricción | Descripción |
|---|---|---|---|
| `Id_Pieza` | INT IDENTITY(1,1) | PK | Identificador de la pieza |
| `Id_Diseno` | INT | FK → Disenos(Id_Diseno) CASCADE | Diseño al que pertenece |
| `nombre_Pieza` | NVARCHAR(50) | NOT NULL | Nombre descriptivo |
| `rol_Pieza` | NVARCHAR(50) | NULL | lateral, top, bottom, shelf, drawer-front |
| `ancho_mm` | DECIMAL(10,2) | NOT NULL | Ancho en mm |
| `alto_mm` | DECIMAL(10,2) | NOT NULL | Alto en mm |
| `espesor_mm` | DECIMAL(10,2) | NOT NULL | Espesor en mm |
| `cantidad` | INT | NOT NULL, DEFAULT 1 | Cantidad de piezas iguales |
| `pos_x` | DECIMAL(10,3) | NULL | Posición X — render 3D |
| `pos_y` | DECIMAL(10,3) | NULL | Posición Y — render 3D |
| `pos_z` | DECIMAL(10,3) | NULL | Posición Z — render 3D |
| `cantoneado` | NVARCHAR(200) | NULL | Lados con canto: `'front,top,left'` |
| `paso_ensamblaje` | INT | NULL | Paso al que pertenece |
| `orden` | INT | NOT NULL, DEFAULT 0 | Orden en lista de corte |

---

### 8. Tabla: `Herrajes`

Accesorios necesarios para ensamblar el mueble.

| Columna | Tipo | Restricción | Descripción |
|---|---|---|---|
| `Id_Herrajes` | INT IDENTITY(1,1) | PK | Identificador del herraje |
| `Id_Diseno` | INT | FK → Disenos(Id_Diseno) CASCADE | Diseño al que pertenece |
| `tipo_Herrajes` | NVARCHAR(50) | NOT NULL | bisagra, corredera, tornillo, confirmat, tirador |
| `descripcionHerrajes` | NVARCHAR(100) | NOT NULL | Descripción del herraje |
| `cantidad_Herrajes` | INT | NOT NULL, DEFAULT 1 | Cantidad necesaria |
| `pos_x` | DECIMAL(10,3) | NULL | Posición X en render 3D |
| `pos_y` | DECIMAL(10,3) | NULL | Posición Y en render 3D |
| `pos_z` | DECIMAL(10,3) | NULL | Posición Z en render 3D |
| `normal_x` | DECIMAL(5,3) | NULL | Normal X de la superficie |
| `normal_y` | DECIMAL(5,3) | NULL | Normal Y de la superficie |
| `normal_z` | DECIMAL(5,3) | NULL | Normal Z de la superficie |
| `paso_ensamblaje` | INT | NULL | Paso donde se coloca |

---

### 9. Tabla: `PasosEnsamblaje`

Guía interactiva paso a paso que el alumno debe seguir.

| Columna | Tipo | Restricción | Descripción |
|---|---|---|---|
| `Id_PasosEn` | INT IDENTITY(1,1) | PK | Identificador del paso |
| `Id_Diseno` | INT | FK → Disenos(Id_Diseno) CASCADE, UNIQUE con numero_paso | Diseño al que pertenece |
| `numero_paso` | INT | NOT NULL | Número de orden del paso |
| `titulo_Mueble` | NVARCHAR(100) | NOT NULL | Título del paso |
| `descripcion_Mueble` | NVARCHAR(200) | NULL | Instrucciones detalladas |
| `consejo_Mueble` | NVARCHAR(200) | NULL | Tip del maestro |
| `herramienta_ArmadoMu` | NVARCHAR(50) | NULL | Herramienta necesaria |
| `imagen_url_Mueble` | NVARCHAR(500) | NULL | URL de imagen o ilustración |
| `duracion_min` | INT | NULL | Duración estimada del paso |

---

### 10. Tabla: `ProgresoAlumno`

Registra el avance de cada alumno en cada diseño.

| Columna | Tipo | Restricción | Descripción |
|---|---|---|---|
| `id_Progreso_Alumno` | INT IDENTITY(1,1) | PK | Identificador del registro |
| `alumno_id` | INT | FK → Usuarios(id) | Alumno que arma |
| `id_Diseno` | INT | FK → Disenos(Id_Diseno), UNIQUE con alumno_id | Diseño que arma |
| `paso_actual_Alumno` | INT | NOT NULL, DEFAULT 1 | Último paso completado |
| `completado_Alumno` | BIT | NOT NULL, DEFAULT 0 | Si terminó el mueble |
| `porcentaje_Progreso_Alumno` | DECIMAL(5,2) | NOT NULL, DEFAULT 0 | Porcentaje de avance |
| `notas` | NVARCHAR(500) | NULL | Observaciones del alumno |
| `iniciado_en` | DATETIME2 | DEFAULT GETDATE() | Fecha de inicio |
| `completado_en` | DATETIME2 | NULL | Fecha de finalización |

---

## Vistas y Procedimientos Almacenados

| Objeto | Tipo | Descripción |
|---|---|---|
| `vw_DisenosPublicados` | Vista | Diseños publicados con maestro, categoría y nivel (JOIN a tabla Nivel) |
| `vw_ListaCorte` | Vista | Piezas con área calculada en m² |
| `sp_GuardarDiseno` | Stored Procedure | Inserta un diseño completo desde el wizard del frontend |

---

## Cardinalidades entre tablas

| Tabla A | Relación | Tabla B | Columna FK | Descripción |
|---|:---:|---|---|---|
| `rolUsuario` | **1 : N** | `Usuarios` | `Usuarios.rolUsuario_id` | Un rol puede tener muchos usuarios |
| `Usuarios` | **1 : N** | `Disenos` | `Disenos.maestro_id` | Un maestro puede crear muchos diseños |
| `Categorias` | **1 : N** | `Disenos` | `Disenos.id_Categoria` | Una categoría agrupa muchos diseños |
| `Nivel` | **1 : N** | `Disenos` | `Disenos.Nivel_id` | Un nivel se asigna a muchos diseños |
| `Disenos` | **1 : N** | `Piezas` | `Piezas.Id_Diseno` | Un diseño tiene muchas piezas |
| `Disenos` | **1 : N** | `Herrajes` | `Herrajes.Id_Diseno` | Un diseño requiere muchos herrajes |
| `Disenos` | **1 : N** | `PasosEnsamblaje` | `PasosEnsamblaje.Id_Diseno` | Un diseño define muchos pasos |
| `Usuarios` | **1 : N** | `ProgresoAlumno` | `ProgresoAlumno.alumno_id` | Un alumno tiene progreso en muchos diseños |
| `Disenos` | **1 : N** | `ProgresoAlumno` | `ProgresoAlumno.id_Diseno` | Un diseño es seguido por muchos alumnos |
| `Usuarios` ↔ `Disenos` | **N : M** | (via `ProgresoAlumno`) | `alumno_id` + `id_Diseno` | Muchos alumnos arman muchos diseños |
| `Materiales` | **—** | *(independiente)* | — | Sin relación directa con otras tablas |

### Leyenda de cardinalidades

| Notación | Significado |
|---|---|
| **1 : 1** | Un registro se relaciona con exactamente uno |
| **1 : N** | Un registro se relaciona con muchos |
| **N : M** | Muchos registros se relacionan con muchos (tabla intermedia) |
| `UNIQUE (a, b)` | Restricción que convierte N:M en un registro único por par |

---

## Relaciones entre tablas

```
rolUsuario ──(1:N)──► Usuarios ──(1:N, maestro_id)──► Disenos ◄──(1:N, id_Categoria)── Categorias
                                                           │    ◄──(1:N, Nivel_id)─────── Nivel
                          │                               │
                     (1:N, alumno_id)          ┌──────────┼──────────┐
                          │                   ▼           ▼          ▼
                          └──► ProgresoAlumno(N:M)     Piezas     Herrajes
                                  UNIQUE(alumno_id,   (1:N)        (1:N)
                                         id_Diseno)
                                                   PasosEnsamblaje (1:N)

Materiales  [catálogo independiente — sin FK]
```

## Orden de creación de tablas

```
1. rolUsuario        (sin dependencias)
2. Nivel             (sin dependencias)
3. Categorias        (sin dependencias)
4. Materiales        (sin dependencias)
5. Usuarios          (→ rolUsuario)
6. Disenos           (→ Usuarios, Categorias, Nivel)
7. Piezas            (→ Disenos)
8. Herrajes          (→ Disenos)
9. PasosEnsamblaje   (→ Disenos)
10. ProgresoAlumno   (→ Usuarios, Disenos)
```


