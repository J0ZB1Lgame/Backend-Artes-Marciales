// frontend/js/api.js
const USE_MOCK = false; // ← cambiar a false cuando backend corrija .htaccess

const BASE_URL = 'http://localhost/tournament-app/backend/api/endpoints';

// ── Endpoints correctos según staff_api.php y staff_extended_api.php ──
const EP_STAFF = 'staff/staff_api.php';
const EP_EXTENDED = 'staff/staff_extended_api.php';

// ── Mock data ────────────────────────────────────────────────────────
let MOCK_STAFF = [
    {
        id_staff: 1, id_usuario: 1, nombre: 'Goku', apellido: 'Son',
        tipo_documento: 'CC', numero_documento: '111111', telefono: '300-001',
        email: 'goku@torneo.com', estado: 'activo', cargo: 'Árbitro', turno: 'Arena Norte'
    },
    {
        id_staff: 2, id_usuario: 2, nombre: 'Vegeta', apellido: 'Briefs',
        tipo_documento: 'CC', numero_documento: '222222', telefono: '300-002',
        email: 'vegeta@torneo.com', estado: 'activo', cargo: 'Seguridad', turno: 'Arena Sur'
    },
    {
        id_staff: 3, id_usuario: 3, nombre: 'Piccolo', apellido: 'Ma Junior',
        tipo_documento: 'CE', numero_documento: '333333', telefono: '300-003',
        email: 'piccolo@torneo.com', estado: 'activo', cargo: 'Árbitro', turno: 'Arena Central'
    },
    {
        id_staff: 4, id_usuario: 4, nombre: 'Krillin', apellido: '',
        tipo_documento: 'CC', numero_documento: '444444', telefono: '300-004',
        email: 'krillin@torneo.com', estado: 'inactivo', cargo: 'Seguridad', turno: 'Arena Norte'
    },
    {
        id_staff: 5, id_usuario: 5, nombre: 'Bulma', apellido: 'Briefs',
        tipo_documento: 'CC', numero_documento: '555555', telefono: '300-005',
        email: 'bulma@torneo.com', estado: 'activo', cargo: 'Mantenimiento', turno: 'Zona Técnica'
    },
];
let _nextId = 6;

const MOCK_ROLES = [
    { id_tipo_rol: 1, nombre: 'Árbitro', descripcion: 'Juez de campo' },
    { id_tipo_rol: 2, nombre: 'Seguridad', descripcion: 'Control de acceso' },
    { id_tipo_rol: 3, nombre: 'Mantenimiento', descripcion: 'Soporte técnico' },
];
const MOCK_TURNOS = [
    { id_turno: 1, nombre: 'Turno A', hora_inicio: '08:00', hora_fin: '16:00' },
    { id_turno: 2, nombre: 'Turno B', hora_inicio: '16:00', hora_fin: '00:00' },
    { id_turno: 3, nombre: 'Turno C', hora_inicio: '00:00', hora_fin: '08:00' },
];
const MOCK_ZONAS = [
    { id_zona: 1, nombre: 'Arena Norte', descripcion: 'Zona de combates principales' },
    { id_zona: 2, nombre: 'Arena Sur', descripcion: 'Zona de clasificatorias' },
    { id_zona: 3, nombre: 'Arena Central', descripcion: 'Zona de finales' },
    { id_zona: 4, nombre: 'Zona Técnica', descripcion: 'Soporte y mantenimiento' },
];

// ── Mock handler ──────────────────────────────────────────────────────
function handleMock(endpoint, method, body, params) {
    const action = params.action ?? '';

    // ── staff_api.php ─────────────────────────────────────
    if (endpoint === EP_STAFF) {
        if (method === 'GET') {
            if (!action) return [...MOCK_STAFF];
            if (action === 'tipos_rol') return [...MOCK_ROLES];
            if (action === 'turnos') return [...MOCK_TURNOS];
            if (action === 'obtener' && params.id)
                return MOCK_STAFF.find(s => s.id_staff === +params.id) ?? null;
            if (action === 'usuarios')
                return [{ id_usuario: 1, username: 'admin' }, { id_usuario: 2, username: 'arbitro1' }];
        }
        if (method === 'POST' && !action) {
            const nuevo = { ...body, id_staff: _nextId++ };
            MOCK_STAFF.push(nuevo);
            return nuevo.id_staff;
        }
        if (method === 'PUT' && action === 'actualizar') {
            const idx = MOCK_STAFF.findIndex(s => s.id_staff === +body.id);
            if (idx !== -1) Object.assign(MOCK_STAFF[idx], body);
            return true;
        }
        if (method === 'DELETE' && action === 'eliminar' && params.id) {
            const idx = MOCK_STAFF.findIndex(s => s.id_staff === +params.id);
            if (idx !== -1) MOCK_STAFF.splice(idx, 1);
            return true;
        }
    }

    // ── staff_extended_api.php ────────────────────────────
    if (endpoint === EP_EXTENDED) {
        if (method === 'GET') {
            if (action === 'zona/listar') return [...MOCK_ZONAS];
            if (action === 'rol/listar') return [...MOCK_ROLES];
            if (action === 'zona/buscar' && params.id)
                return MOCK_ZONAS.find(z => z.id_zona === +params.id) ?? null;
        }
        if (method === 'POST') {
            if (action === 'staff_torneo/asignar-zona') return true;
            if (action === 'staff_torneo/asignar-rol') return true;
        }
    }

    console.warn('[MOCK] Sin handler:', endpoint, method, action, params);
    return null;
}

// ── Función centralizada ──────────────────────────────────────────────
async function apiRequest(endpoint, method = 'GET', body = null, params = {}) {
    if (USE_MOCK) {
        await new Promise(r => setTimeout(r, 250));
        return handleMock(endpoint, method, body, params);
    }

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