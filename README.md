# 🥋 Torneo de Artes Marciales - Backend (PHP + MySQL)

Este proyecto corresponde al backend del sistema de gestión para el **Torneo Mundial de Artes Marciales**, desarrollado en **PHP + MySQL**.

El objetivo es permitir el registro, control y consulta de luchadores, torneos y combates de manera automatizada.

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

Abrir en el navegador:

```
http://localhost/backend-artes-marciales/src/auth/login.php
```

Si todo está correcto, deberías ver una respuesta en formato JSON.

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

