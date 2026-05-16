BUDOKAI TOURNAMENT SYSTEM - MAPEO DE CAMPOS
================================================

╔════════════════════════════════════════════════════════════════════════════╗
║ 1. STAFF - CAMPOS Y VARIABLES FRONTEND                                    ║
╚════════════════════════════════════════════════════════════════════════════╝

CAMPOS BD ➜ VARIABLES FORMULARIO ➜ IDs HTML
─────────────────────────────────────────────────────────────────────────────

id_staff                ➜  (no editable)         ➜ —
nombre                 ➜  f-nombre              ➜ #f-nombre
apellido               ➜  f-apellido            ➜ #f-apellido
tipo_documento         ➜  f-tipo-doc            ➜ #f-tipo-doc
numero_documento       ➜  f-num-doc             ➜ #f-num-doc
telefono               ➜  f-telefono            ➜ #f-telefono
email                  ➜  f-email               ➜ #f-email
cargo                  ➜  f-cargo               ➜ #f-cargo
id_zona                ➜  f-turno (id)          ➜ #f-turno
estado                 ➜  f-estado              ➜ #f-estado

ESTADO ACTUAL EN FRONTEND: ✅ IMPLEMENTADO
- Al crear: Se envían TODOS los campos
- Al editar: Se cargan TODOS los campos con fetchStaffById()
- La función abrirEditar() carga desde BD si faltan datos


╔════════════════════════════════════════════════════════════════════════════╗
║ 2. LUCHADORES - CAMPOS Y VARIABLES FRONTEND                               ║
╚════════════════════════════════════════════════════════════════════════════╝

CAMPOS BD ➜ VARIABLES FORMULARIO ➜ IDs HTML
─────────────────────────────────────────────────────────────────────────────

id_luchador            ➜  (no editable)         ➜ —
nombre                 ➜  f-nombre              ➜ #f-nombre
apellido               ➜  f-apellido            ➜ #f-apellido
tipo_documento         ➜  f-tipo-documento      ➜ #f-tipo-documento
numero_documento       ➜  f-numero-documento    ➜ #f-numero-documento
edad                   ➜  f-edad                ➜ #f-edad
genero                 ➜  f-genero              ➜ #f-genero
categoria              ➜  f-categoria           ➜ #f-categoria
peso                   ➜  f-peso                ➜ #f-peso
telefono               ➜  f-telefono            ➜ #f-telefono
email                  ➜  f-email               ➜ #f-email
victorias              ➜  (solo lectura)        ➜ —
derrotas               ➜  (solo lectura)        ➜ —
estado                 ➜  f-estado              ➜ #f-estado

ESTADO ACTUAL EN FRONTEND: ✅ IMPLEMENTADO
- Al crear: Se envían todos los campos excepto victorias/derrotas
- Al editar: Se cargan TODOS los campos con fetchLuchadorById()
- La función abrirEditar() carga desde BD si faltan datos


╔════════════════════════════════════════════════════════════════════════════╗
║ 3. ZONAS - CAMPOS Y VARIABLES                                             ║
╚════════════════════════════════════════════════════════════════════════════╝

CAMPOS BD ➜ DESCRIPCIÓN
─────────────────────────────────────────────────────────────────────────────

id_zona                ➜ ID automático
nombre                 ➜ Nombre de la zona (Ej: "Arena Norte")

API ENDPOINTS:
- GET  staff/staff_extended_api.php?action=zona/listar  ➜ Lista todas
- POST staff/staff_extended_api.php?action=zona/crear   ➜ Crear zona
- DELETE staff/staff_extended_api.php?action=zona/eliminar&id=X ➜ Eliminar


╔════════════════════════════════════════════════════════════════════════════╗
║ 4. FUNCIONES CRÍTICAS DE EDICIÓN                                          ║
╚════════════════════════════════════════════════════════════════════════════╝

STAFF - luchadores.js (línea 165)
─────────────────────────────────────────────────────────────────────────────
async function abrirEditar(id) {
    // 1. Busca en caché local
    let s = staffData.find(x => x.id_staff === id);
    
    // 2. Si no está o faltan datos, trae desde BD
    if (!s || !s.cargo || !s.numero_documento || !s.tipo_documento) {
        const detail = await fetchStaffById(id);  // Llama fetchStaffById()
        if (detail) s = detail;
    }
    
    // 3. Llena todos los campos del formulario
    document.getElementById('f-nombre').value = s.nombre ?? '';
    document.getElementById('f-apellido').value = s.apellido ?? '';
    document.getElementById('f-tipo-doc').value = s.tipo_documento ?? 'CC';
    document.getElementById('f-num-doc').value = s.numero_documento ?? '';
    document.getElementById('f-telefono').value = s.telefono ?? '';
    document.getElementById('f-email').value = s.email ?? '';
    document.getElementById('f-cargo').value = s.cargo ?? '';
    document.getElementById('f-turno').value = s.id_zona ?? '';
    document.getElementById('f-estado').value = (s.estado === 1 || s.estado === '1') ? 'activo' : 'inactivo';
    
    // 4. Abre modal
    document.getElementById('modal').classList.add('open');
}


LUCHADORES - luchadores.js (línea 138)
─────────────────────────────────────────────────────────────────────────────
async function abrirEditar(id) {
    // 1. Busca en caché local
    let l = luchadorData.find(x => x.id_luchador === id);
    
    // 2. Si no está o faltan datos, trae desde BD
    if (!l || !l.tipo_documento || !l.numero_documento || !l.categoria || !l.genero) {
        const detail = await fetchLuchadorById(id);  // Llama fetchLuchadorById()
        if (detail) l = detail;
    }
    
    // 3. Llena todos los campos del formulario
    document.getElementById('f-nombre').value = l.nombre ?? '';
    document.getElementById('f-apellido').value = l.apellido ?? '';
    document.getElementById('f-tipo-documento').value = l.tipo_documento ?? '';
    document.getElementById('f-numero-documento').value = l.numero_documento ?? '';
    document.getElementById('f-edad').value = l.edad ?? '';
    document.getElementById('f-genero').value = l.genero ?? '';
    document.getElementById('f-categoria').value = l.categoria ?? '';
    document.getElementById('f-peso').value = l.peso ?? '';
    document.getElementById('f-telefono').value = l.telefono ?? '';
    document.getElementById('f-email').value = l.email ?? '';
    document.getElementById('f-estado').value = l.estado ?? 'activo';
    
    // 4. Abre modal
    document.getElementById('modal').classList.add('open');
}


╔════════════════════════════════════════════════════════════════════════════╗
║ 5. FLUJO DE DATOS: CREAR vs EDITAR                                        ║
╚════════════════════════════════════════════════════════════════════════════╝

CREAR NUEVO:
─────────────────────────────────────────────────────────────────────────────
1. Usuario hace clic en "+ REGISTRAR STAFF"
2. abrirNuevo() → Modal se abre vacío
3. Usuario llena formulario
4. onSubmit() recolecta datos
5. crearStaff(datos) → POST /backend/api/endpoints/staff/staff_api.php
6. Backend crea registro con id_staff AUTO_INCREMENT
7. cargar() recarga tabla

EDITAR EXISTENTE:
─────────────────────────────────────────────────────────────────────────────
1. Usuario hace clic en "EDITAR" o en la fila
2. abrirEditar(id) → 
   a. Busca localmente
   b. Si falta datos, GET /backend/api/endpoints/staff/staff_api.php?action=obtener&id=X
   c. Llena formulario con TODOS los datos del DB
3. Usuario modifica campos
4. onSubmit() recolecta datos modificados
5. actualizarStaff(id, datos) → PUT /backend/api/endpoints/staff/staff_api.php?id=X
6. Backend actualiza registro
7. cargar() recarga tabla


╔════════════════════════════════════════════════════════════════════════════╗
║ 6. CHECKLIST PARA BACKEND                                                 ║
╚════════════════════════════════════════════════════════════════════════════╝

STAFF API (staff/staff_api.php):
─────────────────────────────────────────────────────────────────────────────
☐ GET  /staff_api.php                    → Lista todos los staff
☐ GET  /staff_api.php?action=obtener&id=X → Obtiene 1 staff por ID
☐ POST /staff_api.php                    → Crea nuevo staff (TODOS los campos)
☐ PUT  /staff_api.php?id=X               → Actualiza staff (id en query)
☐ DELETE /staff_api.php?id=X             → Elimina staff


LUCHADOR API (luchador/luchador_api.php):
─────────────────────────────────────────────────────────────────────────────
☐ GET  /luchador_api.php                 → Lista todos los luchadores
☐ GET  /luchador_api.php?action=obtener&id=X → Obtiene 1 luchador
☐ POST /luchador_api.php                 → Crea nuevo luchador
☐ PUT  /luchador_api.php                 → Actualiza luchador (id en body)
☐ DELETE /luchador_api.php?action=eliminar&id=X → Elimina luchador


STAFF EXTENDED API (staff/staff_extended_api.php):
─────────────────────────────────────────────────────────────────────────────
☐ GET /staff_extended_api.php?action=zona/listar   → Lista zonas
☐ POST /staff_extended_api.php?action=zona/crear   → Crea zona
☐ DELETE /staff_extended_api.php?action=zona/eliminar&id=X → Elimina zona
☐ GET /staff_extended_api.php?action=rol/listar    → Lista roles


╔════════════════════════════════════════════════════════════════════════════╗
║ 7. DATOS ENVIADOS POR EL FRONTEND                                         ║
╚════════════════════════════════════════════════════════════════════════════╝

CREAR/EDITAR STAFF:
─────────────────────────────────────────────────────────────────────────────
{
    "nombre": "string",
    "apellido": "string",
    "tipo_documento": "CC|CE|PA",
    "numero_documento": "string",
    "telefono": "string",
    "email": "string",
    "cargo": "Árbitro|Seguridad|Mantenimiento|Director|Médico",
    "id_zona": number|null,
    "estado": "activo|inactivo"
}

CREAR/EDITAR LUCHADOR:
─────────────────────────────────────────────────────────────────────────────
{
    "nombre": "string",
    "apellido": "string",
    "tipo_documento": "CC|CE|PA",
    "numero_documento": "string",
    "edad": number,
    "genero": "masculino|femenino",
    "categoria": "Peso Pesado|Peso Medio|Peso Ligero|Pluma",
    "peso": number (decimal),
    "telefono": "string",
    "email": "string",
    "estado": "activo|inactivo"
}

✅ VICTORIAS Y DERROTAS: Se actualizan automáticamente con combates, NO desde este formulario
