// frontend/js/api.js
// ─────────────────────────────────────────────────────────────
// Cambia USE_MOCK a false cuando el backend esté confirmado
// Cambia BASE_URL a la ruta real de tu XAMPP
// ─────────────────────────────────────────────────────────────

const USE_MOCK = true; // ← true = datos ficticios, false = backend real

const BASE_URL = 'http://localhost/tournament-app/backend/api/endpoints';

// ── Mock data ────────────────────────────────────────────────
const MOCK_STAFF = [
    {
        id_staff: 1, id_usuario: 1, nombre: 'Goku', apellido: 'Son', tipo_documento: 'CC',
        numero_documento: '111111', telefono: '300-001', email: 'goku@torneo.com',
        estado: 'activo', cargo: 'Árbitro', turno: 'Arena Norte – Turno A'
    },
    {
        id_staff: 2, id_usuario: 2, nombre: 'Vegeta', apellido: 'Briefs', tipo_documento: 'CC',
        numero_documento: '222222', telefono: '300-002', email: 'vegeta@torneo.com',
        estado: 'activo', cargo: 'Seguridad', turno: 'Arena Sur – Turno B'
    },
    {
        id_staff: 3, id_usuario: 3, nombre: 'Piccolo', 'apellido': 'Ma Junior', tipo_documento: 'CE',
        numero_documento: '333333', telefono: '300-003', email: 'piccolo@torneo.com',
        estado: 'activo', cargo: 'Árbitro', turno: 'Arena Central – Turno A'
    },
    {
        id_staff: 4, id_usuario: 4, nombre: 'Krillin', apellido: 'Sin apellido', tipo_documento: 'CC',
        numero_documento: '444444', telefono: '300-004', email: 'krillin@torneo.com',
        estado: 'inactivo', cargo: 'Seguridad', turno: 'Arena Norte – Turno C'
    },
    {
        id_staff: 5, id_usuario: 5, nombre: 'Bulma', apellido: 'Briefs', tipo_documento: 'CC',
        numero_documento: '555555', telefono: '300-005', email: 'bulma@torneo.com',
        estado: 'activo', cargo: 'Mantenimiento', turno: 'Zona Técnica'
    },
];

let _mockNextId = 6;

// ── Simulación de respuestas del backend ─────────────────────
const MOCK_RESPONSES = {
    // ← clave corregida con la ruta real
    'staff/staff_api.php|GET|listar': () => [...MOCK_STAFF],
    'staff/staff_api.php|GET|tipos_rol': () => [
        { id_tipo_rol: 1, nombre: 'Árbitro', descripcion: 'Juez de campo' },
        { id_tipo_rol: 2, nombre: 'Seguridad', descripcion: 'Control de acceso' },
        { id_tipo_rol: 3, nombre: 'Mantenimiento', descripcion: 'Soporte técnico' },
    ],
    'staff/staff_api.php|GET|turnos': () => [
        { id_turno: 1, nombre: 'Turno A', hora_inicio: '08:00', hora_fin: '16:00' },
        { id_turno: 2, nombre: 'Turno B', hora_inicio: '16:00', hora_fin: '00:00' },
        { id_turno: 3, nombre: 'Turno C', hora_inicio: '00:00', hora_fin: '08:00' },
    ],
};
// ── Función centralizada ─────────────────────────────────────
async function apiRequest(endpoint, method = 'GET', body = null, params = {}) {
    const action = params.action ?? '';

    if (USE_MOCK) {
        // Simula latencia de red
        await new Promise(r => setTimeout(r, 280));

        // GET con ID específico → buscar en mock
        if (method === 'GET' && params.id && !action) {
            const found = MOCK_STAFF.find(s => s.id_staff === parseInt(params.id));
            if (!found) throw new Error('Staff no encontrado');
            return found;
        }

        // GET con action conocido
        const key = `${endpoint}|${method}|${action}`;
        if (MOCK_RESPONSES[key]) return MOCK_RESPONSES[key]();
        
        // GET sin action → listar todos
        if (method === 'GET' && !action && !params.id) {
            return [...MOCK_STAFF];
        }

        // POST (crear)
        if (method === 'POST' && !action) {
            const nuevo = { ...body, id_staff: _mockNextId++ };
            MOCK_STAFF.push(nuevo);
            return nuevo.id_staff;
        }

        // PUT (actualizar)
        if (method === 'PUT' && params.id) {
            const idx = MOCK_STAFF.findIndex(s => s.id_staff === parseInt(params.id));
            if (idx === -1) throw new Error('Staff no encontrado');
            Object.assign(MOCK_STAFF[idx], body);
            return true;
        }

        // DELETE
        if (method === 'DELETE' && params.id) {
            const idx = MOCK_STAFF.findIndex(s => s.id_staff === parseInt(params.id));
            if (idx === -1) throw new Error('Staff no encontrado');
            MOCK_STAFF.splice(idx, 1);
            return true;
        }

        console.warn('[MOCK] Ruta no manejada:', endpoint, method, params);
        return null;
    }

    // ── Modo real ─────────────────────────────────────────────
    const url = new URL(`${BASE_URL}/${endpoint}`);
    Object.entries(params).forEach(([k, v]) => {
        if (v !== undefined && v !== null) url.searchParams.set(k, v);
    });

    const options = { method, headers: { 'Content-Type': 'application/json' } };
    if (body) options.body = JSON.stringify(body);

    const response = await fetch(url.toString(), options);
    const json = await response.json();

    if (json.status === 'error') throw new Error(json.message || `Error ${response.status}`);
    return json.data;
}

const apiGet = (ep, params = {}) => apiRequest(ep, 'GET', null, params);
const apiPost = (ep, body, params = {}) => apiRequest(ep, 'POST', body, params);
const apiPut = (ep, body, params = {}) => apiRequest(ep, 'PUT', body, params);
const apiDelete = (ep, params = {}) => apiRequest(ep, 'DELETE', null, params);