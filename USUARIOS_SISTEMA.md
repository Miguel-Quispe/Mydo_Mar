
# Usuarios del Sistema Mydo-Mar

## Maestros Carpinteros (5 usuarios)

Estos usuarios pueden crear, editar y eliminar muebles en el catálogo.

| Nombre            | Email                      | Contraseña | Rol     |
| ----------------- | -------------------------- | ----------- | ------- |
| Carlos Gómez     | carlos.maestro@mydomar.com | maestro123  | Maestro |
| María Rodríguez | maria.maestro@mydomar.com  | maestro123  | Maestro |
| Juan Pérez       | juan.maestro@mydomar.com   | maestro123  | Maestro |
| Ana Fernández    | ana.maestro@mydomar.com    | maestro123  | Maestro |
| Luis Torres       | luis.maestro@mydomar.com   | maestro123  | Maestro |

### Muebles por Maestro (Funciones disponibles en el diseñador 3D)

**Nota**: Los muebles fueron eliminados de la base de datos. Los maestros deben crear sus propios muebles usando el diseñador 3D. Los precios NO están incluidos ya que el maestro debe proporcionar la cotización. Las funciones JavaScript están disponibles para crear nuevos diseños.

#### Carlos Gómez

- `window.carlosMueble1()` - Escritorio Ejecutivo Grande (1400x750x700mm)
- `window.carlosMueble2()` - Librero Alto 6 Niveles (900x1800x400mm)

#### María Rodríguez

- `window.mariaMueble1()` - Alacena Cocina 2 Puertas (800x700x350mm)
- `window.mariaMueble2()` - Mueble TV Bajo Moderno (1000x550x400mm)

#### Juan Pérez

- `window.juanMueble1()` - Armario de Pared Premium (600x800x350mm)
- `window.juanMueble2()` - Mesa de Noche Cajón (400x450x450mm)

#### Ana Fernández

- `window.anaMueble1()` - Mesa de Comedor Familiar (1200x450x500mm)
- `window.anaMueble2()` - Estantería Vertical Compacta (500x900x400mm)

#### Luis Torres

- `window.luisMueble1()` - Escritorio Gamer Espacioso (1000x450x500mm)
- `window.luisMueble2()` - Cómoda 2 Puertas 2 Repisas (700x600x380mm)

---

## Clientes (10 usuarios)

Estos usuarios pueden navegar el catálogo, guardar favoritos y contactar maestros.

| Nombre            | Email                        | Contraseña | Rol     |
| ----------------- | ---------------------------- | ----------- | ------- |
| Pedro García     | pedro.cliente1@mydomar.com   | cliente123  | Cliente |
| Laura Martínez   | laura.cliente2@mydomar.com   | cliente123  | Cliente |
| Diego López      | diego.cliente3@mydomar.com   | cliente123  | Cliente |
| Sofía Sánchez   | sofia.cliente4@mydomar.com   | cliente123  | Cliente |
| Miguel González  | miguel.cliente5@mydomar.com  | cliente123  | Cliente |
| Carmen Rodríguez | carmen.cliente6@mydomar.com  | cliente123  | Cliente |
| Javier Pérez     | javier.cliente7@mydomar.com  | cliente123  | Cliente |
| Lucía Fernández | lucia.cliente8@mydomar.com   | cliente123  | Cliente |
| Roberto Gómez    | roberto.cliente9@mydomar.com | cliente123  | Cliente |
| Elena Díaz       | elena.cliente10@mydomar.com  | cliente123  | Cliente |

---

## Credenciales de Acceso

### Para probar como Maestro:

1. Inicia sesión con: `carlos.maestro@mydomar.com` / `maestro123`
2. Ve a: http://127.0.0.1:8000/muebles/crear
3. En la consola del navegador ejecuta: `window.carlosMueble1()` (o cualquiera de las funciones disponibles)
4. El mueble se cargará en el diseñador 3D
5. Haz clic en "Guardar y Publicar" para guardarlo en la base de datos

### Para probar como Cliente:

1. Inicia sesión con: `pedro.cliente1@mydomar.com` / `cliente123`
2. Navega por el catálogo en: http://127.0.0.1:8000/catalogo
3. Guarda muebles en favoritos
4. Contacta maestros vía WhatsApp

---

## Usuario Administrador Original

| Nombre          | Email               | Contraseña | Rol     |
| --------------- | ------------------- | ----------- | ------- |
| Mauricio Quispe | maestro@mydomar.com | adidas2019  | Maestro |
