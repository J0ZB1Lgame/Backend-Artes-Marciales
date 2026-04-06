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
- `Modelo/DAO/IStaffDAO.php` y `StaffDAOImpl.php` - Interfaz y implementación DAO
- `Controlador/StaffController.php` - Lógica de negocio
- `staff_management.php` - Interfaz web para gestión

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

Abrir en el navegador:

```
http://localhost/backend-artes-marciales/src/auth/login.php
```

## 🟢 8. Acceder al módulo de Gestión del Staff

Para probar el módulo implementado, abre en el navegador:

```
http://localhost/Backend-Artes-Marciales/staff_management.php
```

Esto te permitirá gestionar el staff a través de una interfaz web simple.

---

# 📁 📦 Estructura del proyecto

```
torneo-backend/
│
├── config/
│   └── conexion.php
│
├src/
│
├── auth/
│   ├── login.php
│   └── logout.php
│
├── usuarios/
│   ├── crear.php
│   ├── listar.php
│   ├── editar.php
│   └── eliminar.php
│
└── README.md
```

