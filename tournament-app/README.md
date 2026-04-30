# Tournament App - Arquitectura Multi-Equipo

Sistema de gestión de torneo de artes marciales con arquitectura modular en capas para equipos colaborativos (Backend Staff implementado, otros módulos listos para desarrollo).

## 🚀 Estructura del Proyecto

```
Backend-Artes-Marciales/
├── backend/
│   ├── api/
│   │   ├── controllers/staff/        ✅ StaffController.php
│   │   ├── endpoints/staff/          ✅ staff_api.php  
│   │   ├── controllers/torneo/       (vacío - equipo torneo)
│   │   ├── endpoints/torneo/         (vacío - equipo torneo)
│   │   └── ... otros módulos
│   ├── models/
│   │   ├── entities/staff/           ✅ 9 entidades
│   │   ├── daos/staff/               ✅ 8 interfaces + 7 impl
│   │   └── ... otros módulos
│   ├── config/conexion.php           ✅ Global $conn
│   └── core/Database.php             ✅ Singleton
├── frontend/
│   ├── js/modules/
│   ├── css/
│   └── pages/
├── database/
│   ├── tournament_db.sql             ✅ Schema + seeds
│   ├── migrations/
│   └── seeders/
├── .env                              ✅ Configuración
├── .env.example                      ✅ Plantilla
├── .htaccess                         ✅ Seguridad
└── README.md
```

## 📋 Configuración Inicial

### 1. Credenciales de BD

Edita `.env`:
```ini
DB_HOST=localhost
DB_NAME=torneo_db
DB_USER=root
DB_PASS=tu_contraseña
DB_PORT=3306
```

### 2. Crear Base de Datos

Una de estas opciones:

**Opción A - CLI:**
```bash
mysql -u root -p < database/tournament_db.sql
```

**Opción B - phpMyAdmin:**
1. Crea BD: `torneo_db`
2. Importa: `database/tournament_db.sql`

### 3. Verificar API

```bash
curl http://localhost/Backend-Artes-Marciales/backend/api/endpoints/staff/staff_api.php?action=listar
```

## 🔗 Rutas API (Staff Module)

### GET - Listar/Obtener

| Endpoint | Parámetros | Descripción |
|----------|-----------|-------------|
| `?action=listar` | - | Lista todo el staff |
| `?action=obtener` | `id=X` | Obtener staff por ID |
| `?action=usuarios` | - | Listar usuarios |
| `?action=turnos-lista` | - | Listar turnos disponibles |
| `?action=tipos-rol` | - | Listar tipos de rol |
| `?action=roles-asignados` | `id_staff=X` (opt) | Roles asignados |
| `?action=turnos` | - | Asignaciones de turno |

### POST - Crear/Asignar

| Endpoint | Body JSON | Descripción |
|----------|-----------|-------------|
| `?action=registrar` | `{id_usuario, nombre, apellido, telefono, email, estado, ...}` | Crear staff |
| `?action=asignar-rol` | `{id_staff, id_tipo_rol}` | Asignar rol a staff |
| `?action=asignar-turno` | `{id_staff, id_turno, fecha}` | Asignar turno |

### PUT - Actualizar

| Endpoint | Body JSON | Descripción |
|----------|-----------|-------------|
| `?action=actualizar` | `{id, nombre, apellido, email, ...}` | Actualizar staff |

### DELETE - Eliminar/Revocar

| Endpoint | Parámetros | Descripción |
|----------|-----------|-------------|
| `?action=eliminar` | `id=X` | Eliminar staff |
| `?action=revocar-rol` | `id_staff=X&id_tipo_rol=Y` | Revocar rol |

## 📚 Estructura de Respuestas

**Éxito:**
```json
{
    "status": "success",
    "message": "Descripción",
    "data": {...}
}
```

**Error:**
```json
{
    "status": "error",
    "message": "Error description"
}
```

## 🔐 Seguridad

✅ `.env` - Bloqueado desde web (.htaccess)
✅ `/backend/config` y `/backend/core` - Bloqueados
✅ Prepared Statements - En todas las queries
✅ CORS Headers - Configurados

## 👥 Para Otros Equipos

### Estructura para Nuevo Módulo

Crea la carpeta `backend/api/controllers/MODULO/` y sigue el patrón de `staff/`:

```
backend/
├── api/
│   ├── controllers/tu_modulo/
│   │   └── TuModuloController.php
│   └── endpoints/tu_modulo/
│       └── tu_modulo_api.php
├── models/
│   ├── entities/tu_modulo/
│   │   └── TuEntidad.php
│   └── daos/tu_modulo/
│       ├── ITuDAOImpl.php
│       └── TuDAOImpl.php
```

**Usa las mismas convenciones:**
- Entidades: `NombreEntidad.php`
- Interfaces DAO: `INombreDAO.php`
- Implementaciones: `NombreDAOImpl.php`
- Controllers: `NombreController.php`
- APIs: `nombre_modulo_api.php`

## 🧩 Nota para Frontend

El módulo de Staff ya está terminado en el backend para esta fase.

- El endpoint principal de consumo es `backend/api/endpoints/staff/staff_api.php`.
- Hay un endpoint extendido adicional en `backend/api/endpoints/staff/staff_extended_api.php` para rutas extra.
- No hay trabajo backend adicional pendiente en Staff: pueden avanzar con la integración frontend directamente.

## 📝 Notas

- `.env` está en `.gitignore` - Cada desarrollador usa sus propias credenciales
- `database/migrations/` y `seeders/` - Listos para versionado de BD
- Todos los archivos usan Singleton `Database::getInstance()` para conexión
- Elimina `Backend-Artes-Marciales.backup` cuando termines la migración

---

**Versión:** 2.0 | **Estado:** ✅ Staff módulo completamente funcional
# 🥋 Torneo de Artes Marciales - Backend (PHP + MySQL)

Este proyecto corresponde al backend del sistema de gestión para el **Torneo Mundial de Artes Marciales**, desarrollado en **PHP + MySQL**.

El objetivo es permitir el registro, control y consulta de luchadores, torneos y combates de manera automatizada.

## 📋 Módulo Implementado: Gestión del Staff

El MVP incluye el módulo de Gestión del Staff con funcionalidades CRUD (Crear, Leer, Actualizar, Eliminar) para miembros del staff.

### Funcionalidades:
- Registrar nuevo staff
- Listar staff existente
- Actualizar información de staff
- Eliminar staff
- Asignación de usuarios al staff

### Archivos principales:
- `Modelo/Entidades/Staff.php` - Clase entidad
- `Modelo/DAO/IStaffDAO.php` y `Modelo/DAO/StaffDAOImpl.php` - Interfaz y implementación DAO
- `Controlador/StaffController.php` - Lógica de negocio
- `api/staff_api.php` - Endpoint REST para gestión de staff

---

# ⚙️ 🚀 Guía de instalación y configuración

Sigue estos pasos para ejecutar el proyecto en tu entorno local.

---

## 🟢 1. Requisitos

Antes de iniciar, asegúrate de tener instalado:

* XAMPP (Apache y MySQL)
* Git

---

## 🟢 2. Clonar el repositorio 

Abre tu terminal o Git Bash y ejecuta:

```bash
git clone https://github.com/J0ZB1Lgame/Backend-Artes-Marciales.git
```
---

## 🟢 3. Ubicar el proyecto en XAMPP

Mueve la carpeta del proyecto a:

```
C:\xampp\htdocs\
```

Debe quedar así:

```
C:\xampp\htdocs\torneo-backend
```

---

## 🟢 4. Iniciar servicios en XAMPP

Abre el panel de XAMPP y activa:

* Apache ✅
* MySQL ✅

---

## 🟢 5. Crear la base de datos

1. Ir a:

   ```
   http://localhost/phpmyadmin
   ```

2. Crear una nueva base de datos llamada:

--
Torneo_artes_marciales
--

---

## 🟢 6. Importar la base de datos

1. Seleccionar la base de datos creada
2. Ir a la pestaña **Importar**
3. Cargar el archivo:

```
database/script.sql
```

4. Ejecutar la importación

---

## 🟢 7. Probar el backend

Utiliza los endpoints API expuestos en `api/`.

Ejemplo de consulta de staff:

```
http://localhost/Backend-Artes-Marciales/api/staff_api.php?action=listar
```

Para crear staff envía un `POST` con JSON al mismo endpoint y la acción `registrar`.

---

# 📁 📦 Estructura del proyecto

```
Backend-Artes-Marciales/
├── api/
│   └── staff_api.php
├── Controlador/
│   └── StaffController.php
├── Modelo/
│   ├── DAO/
│   └── Entidades/
├── base de datos artes marciales.sql
└── README.md
```

