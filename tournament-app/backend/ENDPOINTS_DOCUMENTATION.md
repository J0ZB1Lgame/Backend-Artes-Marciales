# 📋 Estructura de Endpoints API - Torneo de Artes Marciales

## 🏗️ Estructura de Carpetas

```
backend/
├── api/
│   ├── endpoints/
│   │   ├── auth/          # 🔐 Autenticación y Sesiones
│   │   │   └── auth_api.php
│   │   ├── usuarios/      # 👤 Gestión de Usuarios
│   │   │   └── usuarios_api.php
│   │   ├── staff/         # 👥 Gestión de Staff
│   │   │   └── staff_api.php
│   │   ├── roles/         # 🎭 Gestión de Roles
│   │   │   └── roles_api.php
│   │   ├── permisos/      # 🔑 Gestión de Permisos
│   │   │   └── permisos_api.php
│   │   ├── zonas/         # 📍 Gestión de Zonas
│   │   │   └── zonas_api.php
│   │   ├── logs/          # 📊 Historial y Logs
│   │   │   └── logs_api.php
│   │   └── health/        # 🏥 Health Check
│   │       └── health_api.php
│   └── controllers/
│       ├── AuthController.php
│       ├── UsuarioController.php
│       ├── StaffController.php
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

## 🔐 ENDPOINT 1: Autenticación

### POST `/api/endpoints/auth/auth_api.php?action=login`
**Descripción:** Iniciar sesión

**Body JSON:**
```json
{
    "username": "admin",
    "password": "password123"
}
```

**Respuesta (200):**
```json
{
    "status": "success",
    "message": "Sesión iniciada",
    "data": {
        "id_sesion": 1,
        "id_usuario": 1,
        "username": "admin",
        "rol": "Usuario administrador",
        "fecha_inicio": "2026-04-13 01:57:24"
    }
}
```

### POST `/api/endpoints/auth/auth_api.php?action=logout`
**Descripción:** Cerrar sesión

**Body JSON:**
```json
{
    "id_sesion": 1
}
```

**Respuesta (200):**
```json
{
    "status": "success",
    "message": "Sesión cerrada"
}
```

---

## 👤 ENDPOINT 2: Usuarios

### GET `/api/endpoints/usuarios/usuarios_api.php?action=listar`
**Descripción:** Listar todos los usuarios

**Respuesta (200):**
```json
{
    "status": "success",
    "message": "Usuarios listados",
    "data": [
        {
            "id_usuario": 1,
            "username": "admin",
            "rol": "Usuario administrador",
            "estado": true
        }
    ]
}
```

### POST `/api/endpoints/usuarios/usuarios_api.php?action=crear`
**Descripción:** Crear nuevo usuario

**Body JSON:**
```json
{
    "username": "operador",
    "password": "securepass123",
    "rol": "Juez de Campo",
    "estado": true
}
```

### GET `/api/endpoints/usuarios/usuarios_api.php?action=obtener&id=1`
**Descripción:** Obtener usuario por ID

### PUT `/api/endpoints/usuarios/usuarios_api.php?action=actualizar`
**Descripción:** Actualizar usuario

**Body JSON:**
```json
{
    "id_usuario": 1,
    "username": "admin_updated",
    "rol": "Usuario administrador",
    "estado": true
}
```

### DELETE `/api/endpoints/usuarios/usuarios_api.php?action=eliminar&id=1`
**Descripción:** Eliminar usuario

---

## 🎭 ENDPOINT 3: Roles

### GET `/api/endpoints/roles/roles_api.php?action=listar`
**Descripción:** Listar todos los roles

**Respuesta (200):**
```json
{
    "status": "success",
    "message": "Roles listados",
    "data": [
        {
            "id_rol": 1,
            "nombre": "Juez de Campo",
            "descripcion": "Supervisa combates en campo"
        },
        {
            "id_rol": 2,
            "nombre": "Gestor de encuentros",
            "descripcion": "Organiza y gestiona enfrentamientos"
        }
    ]
}
```

### POST `/api/endpoints/roles/roles_api.php?action=crear`
**Descripción:** Crear nuevo rol

**Body JSON:**
```json
{
    "nombre": "Nuevo Rol",
    "descripcion": "Descripción del rol"
}
```

### GET `/api/endpoints/roles/roles_api.php?action=obtener&id=1`
**Descripción:** Obtener rol por ID

### PUT `/api/endpoints/roles/roles_api.php?action=actualizar`
**Descripción:** Actualizar rol

**Body JSON:**
```json
{
    "id_rol": 1,
    "nombre": "Juez de Campo Actualizado",
    "descripcion": "Nueva descripción"
}
```

### DELETE `/api/endpoints/roles/roles_api.php?action=eliminar&id=1`
**Descripción:** Eliminar rol

---

## 🔑 ENDPOINT 4: Permisos

### GET `/api/endpoints/permisos/permisos_api.php?action=listar`
**Descripción:** Listar todos los permisos

### GET `/api/endpoints/permisos/permisos_api.php?action=por-rol&id_rol=1`
**Descripción:** Listar permisos por rol

### POST `/api/endpoints/permisos/permisos_api.php?action=crear`
**Descripción:** Crear nuevo permiso

**Body JSON:**
```json
{
    "accion": "crear_usuario",
    "descripcion": "Permiso para crear usuarios",
    "id_rol": 7
}
```

### DELETE `/api/endpoints/permisos/permisos_api.php?action=eliminar&id=1`
**Descripción:** Eliminar permiso

---

## 📍 ENDPOINT 5: Zonas

### GET `/api/endpoints/zonas/zonas_api.php?action=listar`
**Descripción:** Listar todas las zonas

### POST `/api/endpoints/zonas/zonas_api.php?action=crear`
**Descripción:** Crear nueva zona

**Body JSON:**
```json
{
    "nombre": "Zona de Combate",
    "descripcion": "Área principal de combates"
}
```

### PUT `/api/endpoints/zonas/zonas_api.php?action=actualizar`
**Descripción:** Actualizar zona

### DELETE `/api/endpoints/zonas/zonas_api.php?action=eliminar&id=1`
**Descripción:** Eliminar zona

---

## 📊 ENDPOINT 6: Logs

### GET `/api/endpoints/logs/logs_api.php?action=listar`
**Descripción:** Listar todos los logs

### GET `/api/endpoints/logs/logs_api.php?action=por-usuario&id_usuario=1`
**Descripción:** Listar logs de un usuario

### POST `/api/endpoints/logs/logs_api.php?action=crear`
**Descripción:** Registrar una acción en log

**Body JSON:**
```json
{
    "accion": "Descripción de la acción",
    "id_usuario": 1
}
```

---

## 🏥 ENDPOINT 7: Health Check

### GET `/api/endpoints/health/health_api.php`
**Descripción:** Verificar estado de la API

**Respuesta (200):**
```json
{
    "status": "success",
    "message": "API funcionando correctamente",
    "data": {
        "db_connected": true,
        "php_version": "8.2.12",
        "timestamp": "2026-04-13 01:57:24"
    }
}
```

---

## 🔒 Códigos de Error

| Código | Significado | Ejemplo |
|--------|-------------|---------|
| 200 | OK - Operación exitosa | GET /usuarios |
| 201 | Created - Recurso creado | POST /usuarios |
| 400 | Bad Request - Datos inválidos | Faltan parámetros |
| 401 | Unauthorized - No autenticado | Token expirado |
| 403 | Forbidden - Sin permiso | Usuario sin permisos |
| 404 | Not Found - Recurso no existe | Usuario ID inválido |
| 500 | Internal Server Error | Error en BD |

---

## 🚀 Ejemplo Completo: Login + Listar Usuarios

1. **Login:**
```bash
curl -X POST http://localhost/Backend-Artes-Marciales/tournament-app/backend/api/endpoints/auth/auth_api.php?action=login \
  -H "Content-Type: application/json" \
  -d '{"username":"admin","password":"password123"}'
```

2. **Listar Usuarios:**
```bash
curl -X GET http://localhost/Backend-Artes-Marciales/tournament-app/backend/api/endpoints/usuarios/usuarios_api.php?action=listar
```

---

## 📝 Próximos Pasos

1. Importar la BD: `php import_new_db.php`
2. Crear endpoints según esta documentación
3. Implementar middleware de autenticación
4. Agregar validación de roles y permisos
5. Implementar rate limiting
6. Documentar con OpenAPI/Swagger

---

**Versión:** 1.0  
**Última actualización:** 13-04-2026  
**Base de datos:** torneo_new
