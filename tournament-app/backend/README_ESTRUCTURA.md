# 🎯 GUÍA RÁPIDA - Estructura de API Completa

## 📊 Base de Datos - Verificación

Tu BD **`torneo_new`** tiene estas tablas:

| Tabla | Propósito | Relación |
|-------|-----------|----------|
| **usuario** | Dados de acceso | FK en sesion, log |
| **rol** | Tipos de roles del sistema | FK en permiso, staff_torneo |
| **sesion** | Sesiones activas | FK a usuario |
| **log** | Historial de acciones | FK a usuario |
| **permiso** | Permisos por rol | FK a rol |
| **zona** | Zonas del evento | FK en staff_torneo |
| **staff_torneo** | Personal asignado | FK a rol, zona |

---

## 🚀 PASO 1: Importar la BD (PRIMERO)

Desde terminal:
```powershell
cd c:\xampp\htdocs\Backend-Artes-Marciales\tournament-app\backend
php import_new_db.php
```

**Qué hace:**
- ✅ Crea BD `torneo_new`
- ✅ Crea todas las tablas
- ✅ Inserta datos iniciales (roles, zonas)
- ✅ Crea usuario admin (admin/password123)

---

## 📁 PASO 2: Estructurar Carpetas

Crea estas carpetas (copiar la estructura existente):

```
backend/
├── api/
│   ├── endpoints/
│   │   ├── auth/          # 🔐 Login/Logout
│   │   ├── usuarios/      # 👤 CRUD Usuarios
│   │   ├── roles/         # 🎭 CRUD Roles
│   │   ├── permisos/      # 🔑 CRUD Permisos
│   │   ├── zonas/         # 📍 CRUD Zonas
│   │   ├── logs/          # 📊 Ver Logs
│   │   └── health/        # 🏥 Status API
│   └── controllers/
│       ├── AuthController.php
│       ├── UsuarioController.php
│       ├── RolController.php
│       ├── PermisoController.php
│       ├── ZonaController.php
│       └── LogController.php
└── models/
    ├── daos/
    │   ├── UsuarioDAO.php
    │   ├── RolDAO.php
    │   ├── PermisoDAO.php
    │   ├── ZonaDAO.php
    │   ├── SesionDAO.php
    │   └── LogDAO.php
    └── entities/
        ├── Usuario.php
        ├── Rol.php
        ├── Permiso.php
        ├── Zona.php
        ├── Sesion.php
        └── Log.php
```

---

## 🔑 PASO 3: Endpoints Principales

### 🔐 **Auth Module** (`api/endpoints/auth/auth_api.php`)

```bash
# LOGIN
POST http://localhost/.../auth/auth_api.php?action=login
{
    "username": "admin",
    "password": "password123"
}

# LOGOUT
POST http://localhost/.../auth/auth_api.php?action=logout
{
    "id_sesion": 1
}
```

### 👤 **Usuarios Module** (`api/endpoints/usuarios/usuarios_api.php`)

```bash
# LISTAR
GET http://localhost/.../usuarios/usuarios_api.php?action=listar

# CREAR
POST http://localhost/.../usuarios/usuarios_api.php?action=crear
{
    "username": "nuevo_user",
    "password": "pass123",
    "rol": "Juez de Campo",
    "estado": true
}

# OBTENER
GET http://localhost/.../usuarios/usuarios_api.php?action=obtener&id=1

# ACTUALIZAR
PUT http://localhost/.../usuarios/usuarios_api.php?action=actualizar
{
    "id_usuario": 1,
    "username": "updated",
    "rol": "Coordinator",
    "estado": true
}

# ELIMINAR
DELETE http://localhost/.../usuarios/usuarios_api.php?action=eliminar&id=1
```

### 🎭 **Roles Module** (`api/endpoints/roles/roles_api.php`)

```bash
# LISTAR
GET http://localhost/.../roles/roles_api.php?action=listar

# CREAR
POST http://localhost/.../roles/roles_api.php?action=crear
{
    "nombre": "Nuevo Rol",
    "descripcion": "Descripción del rol"
}

# ACTUALIZAR
PUT http://localhost/.../roles/roles_api.php?action=actualizar
{
    "id_rol": 1,
    "nombre": "Rol Actualizado",
    "descripcion": "Nueva descripción"
}

# ELIMINAR
DELETE http://localhost/.../roles/roles_api.php?action=eliminar&id=1
```

### 📍 **Zonas Module** (`api/endpoints/zonas/zonas_api.php`)

```bash
# Similar a roles pero para zonas
GET /zonas/zonas_api.php?action=listar
POST /zonas/zonas_api.php?action=crear
PUT /zonas/zonas_api.php?action=actualizar
DELETE /zonas/zonas_api.php?action=eliminar&id=1
```

### 🔑 **Permisos Module** (`api/endpoints/permisos/permisos_api.php`)

```bash
# LISTAR
GET /permisos/permisos_api.php?action=listar

# POR ROL
GET /permisos/permisos_api.php?action=por-rol&id_rol=1

# CREAR
POST /permisos/permisos_api.php?action=crear
{
    "accion": "crear_usuario",
    "descripcion": "Permiso para crear usuarios",
    "id_rol": 7
}

# ELIMINAR
DELETE /permisos/permisos_api.php?action=eliminar&id=1
```

### 📊 **Logs Module** (`api/endpoints/logs/logs_api.php`)

```bash
# LISTAR
GET /logs/logs_api.php?action=listar

# POR USUARIO
GET /logs/logs_api.php?action=por-usuario&id_usuario=1

# CREAR
POST /logs/logs_api.php?action=crear
{
    "accion": "Descripción de la acción",
    "id_usuario": 1
}
```

### 🏥 **Health Check** (`api/endpoints/health/health_api.php`)

```bash
GET /health/health_api.php

# Respuesta:
{
    "status": "success",
    "message": "API funcionando",
    "data": {
        "db_connected": true,
        "php_version": "8.2.12",
        "timestamp": "2026-04-13 01:57:24"
    }
}
```

---

## 📖 Respuestas Estándar

### ✅ Éxito (200, 201)
```json
{
    "status": "success",
    "message": "Operación completada",
    "data": {...}
}
```

### ❌ Error (400-500)
```json
{
    "status": "error",
    "message": "Descripción del error"
}
```

---

## 🧪 Test Rápido en Postman

1. **Health Check:**
   ```
   GET http://localhost/Backend-Artes-Marciales/tournament-app/backend/api/endpoints/health/health_api.php
   ```

2. **Login:**
   ```
   POST http://localhost/Backend-Artes-Marciales/tournament-app/backend/api/endpoints/auth/auth_api.php?action=login
   Body: {"username":"admin","password":"password123"}
   ```

3. **Listar Usuarios:**
   ```
   GET http://localhost/Backend-Artes-Marciales/tournament-app/backend/api/endpoints/usuarios/usuarios_api.php?action=listar
   ```

---

## 📝 Documentación Completa

Ver el archivo: **`ENDPOINTS_DOCUMENTATION.md`**

Contiene:
- Estructura detallada de todos los endpoints
- Ejemplos de body JSON para cada request
- Respuestas esperadas
- Códigos de error
- Ejemplos curl

---

## ✅ Checklist

- [ ] Ejecutar `php import_new_db.php`
- [ ] Verificar que la BD se creó con `php diagnose_db.php`
- [ ] Crear carpetas de endpoints
- [ ] Implementar DAOs
- [ ] Implementar Controllers
- [ ] Crear files de endpoints
- [ ] Probar en Postman
- [ ] Agregar validaciones
- [ ] Implementar autenticación middleware

---

**¿Necesitas ayuda con algún paso específico?** 🤔
