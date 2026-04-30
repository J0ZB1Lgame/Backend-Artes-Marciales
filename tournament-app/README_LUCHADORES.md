# Página de Gestión de Luchadores

## 📋 Descripción General

Nueva página para gestionar los luchadores del torneo. Incluye interfaz completa con operaciones CRUD.

## 📁 Archivos Creados

### Backend (API)
- **`backend/api/endpoints/luchador/luchador_api.php`**
  - Endpoint RESTful para operaciones con luchadores
  - Métodos: GET, POST, PUT, DELETE
  - Validación de entrada incluida

### Frontend (Interfaz)
- **`frontend/pages/luchadores.html`**
  - Página principal con tabla, estadísticas y formularios
  - Diseño responsivo y consistente con el resto del sistema
  
- **`frontend/js/modules/luchadores.js`**
  - Lógica de cliente para CRUD
  - Gestión de modales
  - Filtros en tiempo real

### Actualizaciones
- **`frontend/js/api.js`**
  - Agregado soporte para endpoint de luchadores
  - Mock data de ejemplo incluido

## 🎯 Campos del Luchador

| Campo | Tipo | Requerido | Descripción |
|-------|------|-----------|-------------|
| id_luchador | int | Auto | ID único |
| nombre | string | ✅ | Nombre del luchador |
| especie | string | ✅ | Humano, Alien, Androide, etc. |
| nivel_poder_ki | float | ✅ | Nivel de poder numérico |
| origen | string | ✅ | Lugar o planeta de origen |
| estado | string | ✅ | "activo" o "inactivo" |

## 🚀 Uso

### Acceder a la página
```
http://localhost/Backend-Artes-Marciales/tournament-app/frontend/pages/luchadores.html
```

### Operaciones

#### Listar Luchadores
- Se cargan automáticamente al abrir la página

#### Crear Nuevo Luchador
1. Click en botón **"+ REGISTRAR LUCHADOR"**
2. Completar el formulario
3. Click en **"Registrar"**

#### Editar Luchador
1. Click en botón **"✎ EDITAR"** en la fila del luchador
2. Modificar los datos
3. Click en **"Guardar cambios"**

#### Eliminar Luchador
1. Click en botón **"✕ ELIMINAR"** en la fila del luchador
2. Confirmar la eliminación

### Filtros
- **Búsqueda**: Buscar por nombre del luchador
- **Filtro por especie**: Humano, Alien, Androide, Todos

## 📊 Estadísticas

La página muestra 4 tarjetas de estadísticas:
- **Total Luchadores**: Cantidad total de registros
- **Humanos**: Luchadores de especie "Humano"
- **Aliens**: Luchadores de especie "Alien"
- **Otros**: Resto de especies (Androide, Demonio, etc.)

## 🔌 API Endpoints

### GET `/luchador/luchador_api.php`
Obtiene todos los luchadores

**Respuesta:**
```json
{
  "status": "success",
  "data": [
    {
      "id_luchador": 1,
      "nombre": "Son Goku",
      "especie": "Humano",
      "nivel_poder_ki": 9000,
      "origen": "Tierra",
      "estado": "activo"
    }
  ]
}
```

### GET `/luchador/luchador_api.php?action=obtener&id=1`
Obtiene un luchador específico

### POST `/luchador/luchador_api.php`
Crea un nuevo luchador

**Body:**
```json
{
  "nombre": "Vegeta",
  "especie": "Alien",
  "nivel_poder_ki": 8500,
  "origen": "Planeta Vegeta",
  "estado": "activo"
}
```

### PUT `/luchador/luchador_api.php`
Actualiza un luchador existente

**Body:**
```json
{
  "id_luchador": 1,
  "nombre": "Son Goku",
  "especie": "Humano",
  "nivel_poder_ki": 9500,
  "origen": "Tierra",
  "estado": "activo"
}
```

### DELETE `/luchador/luchador_api.php?action=eliminar&id=1`
Elimina un luchador

## 🧪 Modo Test/Mock

Para probar sin necesidad de backend activo:
1. Abrir `frontend/js/api.js`
2. Cambiar `const USE_MOCK = false` a `const USE_MOCK = true`
3. La página usará datos de ejemplo

## ✨ Características

- ✅ Tabla interactiva con datos en tiempo real
- ✅ CRUD completo (Create, Read, Update, Delete)
- ✅ Búsqueda y filtrado en tiempo real
- ✅ Modal elegante para crear/editar
- ✅ Confirmación de eliminación
- ✅ Notificaciones (toasts) de feedback
- ✅ Estadísticas automáticas
- ✅ Diseño responsive y moderno
- ✅ Compatible con mock data para testing

## 🔄 Integración con Base de Datos

El endpoint utiliza `LuchadorDAOImpl` que maneja:
- Conexión a tabla `luchador`
- Operaciones CRUD preparadas
- Prevención de SQL injection

## 📝 Notas

- La página sigue el mismo patrón visual que `staff.html`
- Los estilos utilizan CSS custom properties (variables)
- Compatible con todos los navegadores modernos
- Sin dependencias externas (vanilla JavaScript)
