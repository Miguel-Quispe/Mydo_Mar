-- ==============================================================================
-- SCRIPT SQL DDL — ARQUITECTURA B2B CON GESTIÓN DE EMPLEADOS Y AUDITORÍA FORENSE
-- Proyecto: PolloMuebles / Mydo-Mar
-- Motor: PostgreSQL / MySQL (Sintaxis ANSI SQL compatible)
-- Patrón: HTAP (Hybrid Transactional/Analytical Processing) con Dimensión Tiempo
-- ==============================================================================

-- ------------------------------------------------------------------------------
-- 1. DOMINIO TEMPORAL (HTAP - Dimensión Tiempo para reportes y auditoría)
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS dim_tiempo (
    id_dim_tiempo INT PRIMARY KEY, -- Smart Key en formato YYYYMMDDHH (ej: 2026102108)
    fecha_hora_exacta TIMESTAMP NOT NULL,
    anio SMALLINT NOT NULL,
    mes SMALLINT NOT NULL,
    dia SMALLINT NOT NULL,
    hora SMALLINT NOT NULL
);

CREATE INDEX IF NOT EXISTS idx_dim_tiempo_anio_mes ON dim_tiempo (anio, mes);
CREATE INDEX IF NOT EXISTS idx_dim_tiempo_fecha ON dim_tiempo (fecha_hora_exacta);

COMMENT ON TABLE dim_tiempo IS 'Dimensión temporal para agregaciones analíticas de alta velocidad y sellado cronológico inmutable';

-- ------------------------------------------------------------------------------
-- 2. DOMINIO EMPRESARIAL B2B (Madeart & Empresas Clientes)
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS empresa_users (
    id_empresa_users SERIAL PRIMARY KEY,
    id_dim_tiempo_creacion INT NOT NULL,
    razon_social_empresa_users VARCHAR(191) NOT NULL,
    nit_empresa_users VARCHAR(45) NOT NULL UNIQUE,
    telefono_whatsapp_empresa_users VARCHAR(30),
    activa_empresa_users BOOLEAN NOT NULL DEFAULT TRUE,
    CONSTRAINT fk_empresa_dim_tiempo FOREIGN KEY (id_dim_tiempo_creacion) 
        REFERENCES dim_tiempo (id_dim_tiempo) ON UPDATE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_empresa_activa ON empresa_users (activa_empresa_users);

-- Subroles operativos configurables por empresa (Armador, Diseñador, Supervisor, etc.)
CREATE TABLE IF NOT EXISTS empresa_roles (
    id_empresa_rol SERIAL PRIMARY KEY,
    id_empresa_users INT NOT NULL,
    id_dim_tiempo_creacion INT NOT NULL,
    nombre_rol_empresa VARCHAR(80) NOT NULL,
    es_rol_predeterminado BOOLEAN NOT NULL DEFAULT FALSE,
    CONSTRAINT fk_emprol_empresa FOREIGN KEY (id_empresa_users) 
        REFERENCES empresa_users (id_empresa_users) ON DELETE CASCADE,
    CONSTRAINT fk_emprol_dim_tiempo FOREIGN KEY (id_dim_tiempo_creacion) 
        REFERENCES dim_tiempo (id_dim_tiempo) ON UPDATE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_emprol_empresa ON empresa_roles (id_empresa_users);

-- Roles globales del sistema (Admin Global, Gerente Empresa, Empleado, Alumno)
CREATE TABLE IF NOT EXISTS users_roles (
    id_rol_users SERIAL PRIMARY KEY,
    nombre_rol_users VARCHAR(80) NOT NULL UNIQUE
);

-- Cuentas de usuarios y empleados corporativos
CREATE TABLE IF NOT EXISTS users_cuentas (
    id_usuario_users SERIAL PRIMARY KEY,
    id_empresa_users INT NULL,
    id_rol_users INT NOT NULL,
    id_empresa_rol INT NULL,
    id_dim_tiempo_registro INT NOT NULL,
    nombre_usuario_users VARCHAR(100) NOT NULL UNIQUE,
    email_usuario_users VARCHAR(191) NOT NULL UNIQUE,
    password_hash_users VARCHAR(255) NOT NULL,
    cuenta_generada_por_empresa BOOLEAN NOT NULL DEFAULT FALSE,
    CONSTRAINT fk_usr_empresa FOREIGN KEY (id_empresa_users) 
        REFERENCES empresa_users (id_empresa_users) ON DELETE SET NULL,
    CONSTRAINT fk_usr_rol FOREIGN KEY (id_rol_users) 
        REFERENCES users_roles (id_rol_users) ON UPDATE CASCADE,
    CONSTRAINT fk_usr_emprol FOREIGN KEY (id_empresa_rol) 
        REFERENCES empresa_roles (id_empresa_rol) ON DELETE SET NULL,
    CONSTRAINT fk_usr_dim_tiempo FOREIGN KEY (id_dim_tiempo_registro) 
        REFERENCES dim_tiempo (id_dim_tiempo) ON UPDATE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_usr_empresa ON users_cuentas (id_empresa_users);
CREATE INDEX IF NOT EXISTS idx_usr_rol ON users_cuentas (id_rol_users);

-- ------------------------------------------------------------------------------
-- 3. AUDITORÍA FORENSE DE CREDENCIALES (Inmutable / Append-Only)
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users_credenciales_auditoria (
    id_auditoria_credencial BIGSERIAL PRIMARY KEY,
    id_usuario_users INT NOT NULL,
    id_usuario_modifica_users INT NOT NULL,
    id_dim_tiempo_evento INT NOT NULL,
    tipo_cambio VARCHAR(60) NOT NULL, -- CAMBIO_PASSWORD, CAMBIO_USERNAME, RESET_CREDENTIALS
    valor_anterior_hash VARCHAR(255) NULL,
    valor_nuevo_hash VARCHAR(255) NULL,
    CONSTRAINT fk_aud_usuario FOREIGN KEY (id_usuario_users) 
        REFERENCES users_cuentas (id_usuario_users) ON DELETE CASCADE,
    CONSTRAINT fk_aud_modificador FOREIGN KEY (id_usuario_modifica_users) 
        REFERENCES users_cuentas (id_usuario_users) ON DELETE RESTRICT,
    CONSTRAINT fk_aud_dim_tiempo FOREIGN KEY (id_dim_tiempo_evento) 
        REFERENCES dim_tiempo (id_dim_tiempo) ON UPDATE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_aud_usuario ON users_credenciales_auditoria (id_usuario_users);
CREATE INDEX IF NOT EXISTS idx_aud_tiempo ON users_credenciales_auditoria (id_dim_tiempo_evento);

-- ------------------------------------------------------------------------------
-- 4. CLASIFICACIÓN DE MUEBLES (Niveles y Categorías)
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS muebles_niveles (
    id_nivel_muebles SERIAL PRIMARY KEY,
    id_dim_tiempo_creacion INT NOT NULL,
    nombre_nivel_muebles VARCHAR(80) NOT NULL UNIQUE,
    CONSTRAINT fk_niv_dim_tiempo FOREIGN KEY (id_dim_tiempo_creacion) 
        REFERENCES dim_tiempo (id_dim_tiempo) ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS muebles_categorias (
    id_categoria_muebles SERIAL PRIMARY KEY,
    id_dim_tiempo_creacion INT NOT NULL,
    nombre_categoria_muebles VARCHAR(100) NOT NULL UNIQUE,
    icono_categoria_muebles VARCHAR(100) NULL,
    CONSTRAINT fk_cat_dim_tiempo FOREIGN KEY (id_dim_tiempo_creacion) 
        REFERENCES dim_tiempo (id_dim_tiempo) ON UPDATE CASCADE
);

-- ------------------------------------------------------------------------------
-- 5. MATERIALES Y CATÁLOGO GLOBAL DE HERRAJES (BOM)
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS muebles_materiales (
    id_material_muebles SERIAL PRIMARY KEY,
    id_dim_tiempo_creacion INT NOT NULL,
    nombre_material_muebles VARCHAR(120) NOT NULL,
    tipo_material_muebles VARCHAR(80) NOT NULL DEFAULT 'Melamina',
    espesor_material_muebles DECIMAL(6,2) NOT NULL DEFAULT 18.00,
    precio_lamina_muebles DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    CONSTRAINT fk_mat_dim_tiempo FOREIGN KEY (id_dim_tiempo_creacion) 
        REFERENCES dim_tiempo (id_dim_tiempo) ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS muebles_catalogo_herrajes_global (
    id_catalogo_herraje_global SERIAL PRIMARY KEY,
    id_usuario_registra_users INT NOT NULL,
    id_dim_tiempo_creacion INT NOT NULL,
    nombre_herraje_global VARCHAR(120) NOT NULL,
    tipo_visibilidad_herraje VARCHAR(40) NOT NULL DEFAULT 'oculto', -- visible, oculto
    precio_referencial DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    activo_global BOOLEAN NOT NULL DEFAULT TRUE,
    CONSTRAINT fk_her_usuario FOREIGN KEY (id_usuario_registra_users) 
        REFERENCES users_cuentas (id_usuario_users) ON DELETE RESTRICT,
    CONSTRAINT fk_her_dim_tiempo FOREIGN KEY (id_dim_tiempo_creacion) 
        REFERENCES dim_tiempo (id_dim_tiempo) ON UPDATE CASCADE
);

-- ------------------------------------------------------------------------------
-- 6. PROYECTOS DE DISEÑO 3D Y DETALLE TÉCNICO (Despiece y Ensamble)
-- ------------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS muebles_disenos (
    id_diseno_muebles SERIAL PRIMARY KEY,
    creador_id_usuario_users INT NOT NULL,
    id_empresa_users INT NULL,
    id_categoria_muebles INT NOT NULL,
    id_nivel_muebles INT NOT NULL,
    id_dim_tiempo_publicacion INT NOT NULL,
    nombre_diseno_muebles VARCHAR(191) NOT NULL,
    publicado_diseno_muebles BOOLEAN NOT NULL DEFAULT FALSE,
    CONSTRAINT fk_dis_creador FOREIGN KEY (creador_id_usuario_users) 
        REFERENCES users_cuentas (id_usuario_users) ON DELETE RESTRICT,
    CONSTRAINT fk_dis_empresa FOREIGN KEY (id_empresa_users) 
        REFERENCES empresa_users (id_empresa_users) ON DELETE SET NULL,
    CONSTRAINT fk_dis_cat FOREIGN KEY (id_categoria_muebles) 
        REFERENCES muebles_categorias (id_categoria_muebles) ON UPDATE CASCADE,
    CONSTRAINT fk_dis_niv FOREIGN KEY (id_nivel_muebles) 
        REFERENCES muebles_niveles (id_nivel_muebles) ON UPDATE CASCADE,
    CONSTRAINT fk_dis_dim_tiempo FOREIGN KEY (id_dim_tiempo_publicacion) 
        REFERENCES dim_tiempo (id_dim_tiempo) ON UPDATE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_dis_empresa ON muebles_disenos (id_empresa_users);
CREATE INDEX IF NOT EXISTS idx_dis_publicado ON muebles_disenos (publicado_diseno_muebles);

-- Despiece milimétrico de tableros
CREATE TABLE IF NOT EXISTS muebles_piezas (
    id_pieza_muebles SERIAL PRIMARY KEY,
    id_diseno_muebles INT NOT NULL,
    id_material_muebles INT NOT NULL,
    nombre_pieza_muebles VARCHAR(120) NOT NULL,
    ancho_mm_muebles DECIMAL(10,2) NOT NULL,
    alto_mm_muebles DECIMAL(10,2) NOT NULL,
    CONSTRAINT fk_pie_diseno FOREIGN KEY (id_diseno_muebles) 
        REFERENCES muebles_disenos (id_diseno_muebles) ON DELETE CASCADE,
    CONSTRAINT fk_pie_material FOREIGN KEY (id_material_muebles) 
        REFERENCES muebles_materiales (id_material_muebles) ON UPDATE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_pie_diseno ON muebles_piezas (id_diseno_muebles);

-- Herrajes requeridos por mueble (Bill of Materials)
CREATE TABLE IF NOT EXISTS muebles_herrajes_por_proyecto (
    id_herraje_proyecto SERIAL PRIMARY KEY,
    id_diseno_muebles INT NOT NULL,
    id_catalogo_herraje_global INT NOT NULL,
    cantidad_necesaria_proyecto INT NOT NULL DEFAULT 1,
    CONSTRAINT fk_herproj_diseno FOREIGN KEY (id_diseno_muebles) 
        REFERENCES muebles_disenos (id_diseno_muebles) ON DELETE CASCADE,
    CONSTRAINT fk_herproj_catalogo FOREIGN KEY (id_catalogo_herraje_global) 
        REFERENCES muebles_catalogo_herrajes_global (id_catalogo_herraje_global) ON UPDATE CASCADE
);

-- Pasos de ensamblaje para el manual de taller
CREATE TABLE IF NOT EXISTS muebles_pasos_ensamblaje (
    id_paso_muebles SERIAL PRIMARY KEY,
    id_diseno_muebles INT NOT NULL,
    numero_paso_muebles INT NOT NULL,
    titulo_paso_muebles VARCHAR(191) NOT NULL,
    CONSTRAINT fk_pas_diseno FOREIGN KEY (id_diseno_muebles) 
        REFERENCES muebles_disenos (id_diseno_muebles) ON DELETE CASCADE
);

-- Seguimiento de aprendizaje / armado en planta
CREATE TABLE IF NOT EXISTS muebles_progreso_usuario (
    id_progreso_muebles SERIAL PRIMARY KEY,
    usuario_id_users INT NOT NULL,
    id_diseno_muebles INT NOT NULL,
    id_dim_tiempo_inicio INT NOT NULL,
    paso_actual_muebles INT NOT NULL DEFAULT 1,
    porcentaje_progreso_muebles DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    completado_muebles BOOLEAN NOT NULL DEFAULT FALSE,
    CONSTRAINT fk_prog_usuario FOREIGN KEY (usuario_id_users) 
        REFERENCES users_cuentas (id_usuario_users) ON DELETE CASCADE,
    CONSTRAINT fk_prog_diseno FOREIGN KEY (id_diseno_muebles) 
        REFERENCES muebles_disenos (id_diseno_muebles) ON DELETE CASCADE,
    CONSTRAINT fk_prog_dim_tiempo FOREIGN KEY (id_dim_tiempo_inicio) 
        REFERENCES dim_tiempo (id_dim_tiempo) ON UPDATE CASCADE
);

CREATE INDEX IF NOT EXISTS idx_prog_usuario_diseno ON muebles_progreso_usuario (usuario_id_users, id_diseno_muebles);

-- ==============================================================================
-- 7. DATOS SEMILLA BASE (Inserts Iniciales para Funcionalidad Inmediata)
-- ==============================================================================

-- 1. Dimensión Tiempo (Registro inicial representativo: 21 de Octubre de 2026, 08:00 hrs)
INSERT INTO dim_tiempo (id_dim_tiempo, fecha_hora_exacta, anio, mes, dia, hora)
VALUES (2026102108, '2026-10-21 08:00:00', 2026, 10, 21, 8)
ON CONFLICT (id_dim_tiempo) DO NOTHING;

-- 2. Roles del Sistema
INSERT INTO users_roles (id_rol_users, nombre_rol_users) VALUES
(1, 'Admin Global'),
(2, 'Gerente Empresa'),
(3, 'Empleado'),
(4, 'Alumno')
ON CONFLICT (id_rol_users) DO NOTHING;

-- 3. Empresa B2B Piloto: Madeart
INSERT INTO empresa_users (id_empresa_users, id_dim_tiempo_creacion, razon_social_empresa_users, nit_empresa_users, telefono_whatsapp_empresa_users, activa_empresa_users)
VALUES (1, 2026102108, 'Madeart Carpintería & Diseño S.R.L.', '1023849021', '+59178945612', TRUE)
ON CONFLICT (id_empresa_users) DO NOTHING;

-- 4. Subroles de Fábrica para Madeart
INSERT INTO empresa_roles (id_empresa_rol, id_empresa_users, id_dim_tiempo_creacion, nombre_rol_empresa, es_rol_predeterminado) VALUES
(1, 1, 2026102108, 'Armador de Muebles', TRUE),
(2, 1, 2026102108, 'Diseñador 3D / Modulador', FALSE),
(3, 1, 2026102108, 'Supervisor de Calidad', FALSE),
(4, 1, 2026102108, 'Transporte y Montaje', FALSE)
ON CONFLICT (id_empresa_rol) DO NOTHING;

-- 5. Usuario Administrador / Gerente Madeart
-- Password por defecto: adidas2019 (Bcrypt hash)
INSERT INTO users_cuentas (id_usuario_users, id_empresa_users, id_rol_users, id_empresa_rol, id_dim_tiempo_registro, nombre_usuario_users, email_usuario_users, password_hash_users, cuenta_generada_por_empresa)
VALUES (1, 1, 2, 2, 2026102108, 'gerencia_madeart', 'gerencia@madeart.com', '$2y$12$6jtuiOMW9z4bc0j6qPkwGXFDESYmmvkFyGFlzu4qZt8=', FALSE)
ON CONFLICT (id_usuario_users) DO NOTHING;

-- 6. Niveles y Categorías
INSERT INTO muebles_niveles (id_nivel_muebles, id_dim_tiempo_creacion, nombre_nivel_muebles) VALUES
(1, 2026102108, 'Básico / Principiante'),
(2, 2026102108, 'Intermedio / Taller'),
(3, 2026102108, 'Avanzado / Industrial')
ON CONFLICT (id_nivel_muebles) DO NOTHING;

INSERT INTO muebles_categorias (id_categoria_muebles, id_dim_tiempo_creacion, nombre_categoria_muebles, icono_categoria_muebles) VALUES
(1, 2026102108, 'Oficina y Escritorios', 'briefcase'),
(2, 2026102108, 'Cocinas y Alacenas', 'utensils'),
(3, 2026102108, 'Dormitorios y Closets', 'bed'),
(4, 2026102108, 'Salas y Centros de Entretenimiento', 'tv')
ON CONFLICT (id_categoria_muebles) DO NOTHING;

-- 7. Catálogo de Melaminas de 18mm
INSERT INTO muebles_materiales (id_material_muebles, id_dim_tiempo_creacion, nombre_material_muebles, tipo_material_muebles, espesor_material_muebles, precio_lamina_muebles) VALUES
(1, 2026102108, 'Melamina Roble Natural', 'Melamina Veta Madera', 18.00, 320.00),
(2, 2026102108, 'Melamina Nogal Oscuro', 'Melamina Veta Madera', 18.00, 340.00),
(3, 2026102108, 'Melamina Blanca Lisa', 'Melamina Unicolor', 18.00, 260.00),
(4, 2026102108, 'Melamina Gris Grafito', 'Melamina Unicolor', 18.00, 290.00),
(5, 2026102108, 'Melamina Mármol Calacatta', 'Melamina Texturizada', 18.00, 380.00),
(6, 2026102108, 'Melamina Haya Natural', 'Melamina Veta Madera', 18.00, 310.00)
ON CONFLICT (id_material_muebles) DO NOTHING;

-- 8. Catálogo Maestro de Herrajes Globales
INSERT INTO muebles_catalogo_herrajes_global (id_catalogo_herraje_global, id_usuario_registra_users, id_dim_tiempo_creacion, nombre_herraje_global, tipo_visibilidad_herraje, precio_referencial, activo_global) VALUES
(1, 1, 2026102108, 'Bisagra Cangrejo Codo 0 (Recta)', 'visible', 8.50, TRUE),
(2, 1, 2026102108, 'Corredera Telescópica Pesada 45cm', 'oculto', 28.00, TRUE),
(3, 1, 2026102108, 'Sistema Unión Minifix con Perno y Tambor', 'oculto', 3.50, TRUE),
(4, 1, 2026102108, 'Tirador Perfil de Aluminio Negro 15cm', 'visible', 15.00, TRUE),
(5, 1, 2026102108, 'Tornillo Soberbio 4x50mm para Aglomerado', 'oculto', 0.25, TRUE)
ON CONFLICT (id_catalogo_herraje_global) DO NOTHING;
