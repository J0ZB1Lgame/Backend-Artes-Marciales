// frontend/js/api.js
const USE_MOCK = false;

const BASE_URL = 'http://localhost/artes%20marciales/tournament-app/backend/api/endpoints';

// ── Endpoints ─────────────────────────────────────────────────────────
const EP_LOGIN    = 'login/login_api.php';
const EP_STAFF    = 'staff/staff_api.php';
const EP_STAFF_EXT = 'staff/staff_extended_api.php';
const EP_LUCHADOR = 'luchador/luchador_api.php';
const EP_COMBATE  = 'combate/combate_api.php';
const EP_TORNEO   = 'torneo/torneo_api.php';

// ── Mock data ────────────────────────────────────────────────────────
let MOCK_STAFF = [
    { id_staff: 1, id_usuario: 1, nombre: 'Goku', apellido: 'Son', tipo_documento: 'CC', numero_documento: '111111', telefono: '300-001', email: 'goku@torneo.com', estado: 'activo', cargo: 'Árbitro', turno: 'Arena Norte' },
    { id_staff: 2, id_usuario: 2, nombre: 'Vegeta', apellido: 'Briefs', tipo_documento: 'CC', numero_documento: '222222', telefono: '300-002', email: 'vegeta@torneo.com', estado: 'activo', cargo: 'Seguridad', turno: 'Arena Sur' },
    { id_staff: 3, id_usuario: 3, nombre: 'Piccolo', apellido: 'Ma Junior', tipo_documento: 'CE', numero_documento: '333333', telefono: '300-003', email: 'piccolo@torneo.com', estado: 'activo', cargo: 'Árbitro', turno: 'Arena Central' },
];
let _nextId = 4;

const MOCK_ROLES = [
    { id_tipo_rol: 1, nombre: 'Árbitro', descripcion: 'Juez de campo' },
    { id_tipo_rol: 2, nombre: 'Seguridad', descripcion: 'Control de acceso' },
    { id_tipo_rol: 3, nombre: 'Mantenimiento', descripcion: 'Soporte técnico' },
];
const MOCK_ZONAS = [
    { id_zona: 1, nombre: 'Arena Norte' },
    { id_zona: 2, nombre: 'Arena Sur' },
    { id_zona: 3, nombre: 'Arena Central' },
];
let MOCK_LUCHADORES = [
    { id_luchador: 1, nombre: 'Juan', apellido: 'Pérez', tipo_documento: 'CC', numero_documento: '12345678', edad: 25, genero: 'masculino', categoria: 'Peso Medio', peso: 75, telefono: '3001234567', email: 'juan@email.com', victorias: 12, derrotas: 3, estado: 'activo' },
    { id_luchador: 2, nombre: 'María', apellido: 'González', tipo_documento: 'CC', numero_documento: '87654321', edad: 23, genero: 'femenino', categoria: 'Peso Ligero', peso: 60, telefono: '3009876543', email: 'maria@email.com', victorias: 8, derrotas: 2, estado: 'activo' },
];
let _nextLuchadorId = 3;

let MOCK_COMBATES = [];
let _nextCombateId = 1;
let MOCK_TORNEOS = [];
let _nextTorneoId = 1;

// ── Mock handler ──────────────────────────────────────────────────────
function handleMock(endpoint, method, body, params) {
    const action = params.action ?? '';

    if (endpoint === EP_STAFF) {
        if (method === 'GET') {
            if (!action) return [...MOCK_STAFF];
            if (action === 'tipos_rol') return [...MOCK_ROLES];
            if (action === 'zona/listar') return [...MOCK_ZONAS];
            if (action === 'obtener' && params.id) return MOCK_STAFF.find(s => s.id_staff === +params.id) ?? null;
        }
        if (method === 'POST' && !action) { const n = { ...body, id_staff: _nextId++ }; MOCK_STAFF.push(n); return n.id_staff; }
        if (method === 'PUT') { const idx = MOCK_STAFF.findIndex(s => s.id_staff === +body.id); if (idx !== -1) Object.assign(MOCK_STAFF[idx], body); return true; }
        if (method === 'DELETE' && params.id) { const idx = MOCK_STAFF.findIndex(s => s.id_staff === +params.id); if (idx !== -1) MOCK_STAFF.splice(idx, 1); return true; }
    }

    if (endpoint === EP_STAFF_EXT) {
        if (method === 'GET') {
            if (action === 'zona/listar') return [...MOCK_ZONAS];
            if (action === 'rol/listar') return [...MOCK_ROLES];
        }
        if (method === 'POST') return true;
    }

    if (endpoint === EP_LUCHADOR) {
        if (method === 'GET') {
            if (!action) return [...MOCK_LUCHADORES];
            if (action === 'obtener' && params.id) return MOCK_LUCHADORES.find(l => l.id_luchador === +params.id) ?? null;
        }
        if (method === 'POST') { const n = { ...body, id_luchador: _nextLuchadorId++ }; MOCK_LUCHADORES.push(n); return n.id_luchador; }
        if (method === 'PUT') { const idx = MOCK_LUCHADORES.findIndex(l => l.id_luchador === +body.id_luchador); if (idx !== -1) Object.assign(MOCK_LUCHADORES[idx], body); return true; }
        if (method === 'DELETE' && params.id) { const idx = MOCK_LUCHADORES.findIndex(l => l.id_luchador === +params.id); if (idx !== -1) MOCK_LUCHADORES.splice(idx, 1); return true; }
    }

    if (endpoint === EP_COMBATE) {
        if (method === 'GET') { if (!action) return [...MOCK_COMBATES]; }
        if (method === 'POST') { const n = { ...body, id_combate: _nextCombateId++ }; MOCK_COMBATES.push(n); return n.id_combate; }
        if (method === 'PUT') { const idx = MOCK_COMBATES.findIndex(c => c.id_combate === +body.id_combate); if (idx !== -1) Object.assign(MOCK_COMBATES[idx], body); return true; }
        if (method === 'DELETE' && params.id) { const idx = MOCK_COMBATES.findIndex(c => c.id_combate === +params.id); if (idx !== -1) MOCK_COMBATES.splice(idx, 1); return true; }
    }

    if (endpoint === EP_TORNEO) {
        if (method === 'GET') { if (!action) return [...MOCK_TORNEOS]; }
        if (method === 'POST') { const n = { ...body, id_torneo: _nextTorneoId++ }; MOCK_TORNEOS.push(n); return n.id_torneo; }
        if (method === 'PUT') { const idx = MOCK_TORNEOS.findIndex(t => t.id_torneo === +body.id_torneo); if (idx !== -1) Object.assign(MOCK_TORNEOS[idx], body); return true; }
        if (method === 'DELETE' && params.id) { const idx = MOCK_TORNEOS.findIndex(t => t.id_torneo === +params.id); if (idx !== -1) MOCK_TORNEOS.splice(idx, 1); return true; }
    }

    console.warn('[MOCK] Sin handler:', endpoint, method, action, params);
    return null;
}

// ── Función centralizada ──────────────────────────────────────────────
async function apiRequest(endpoint, method = 'GET', body = null, params = {}) {
    if (USE_MOCK) {
        await new Promise(r => setTimeout(r, 200));
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

    // Maneja ambos formatos: {status:'error'} y {success:false}
    if (json.status === 'error' || json.success === false) {
        throw new Error(json.message || `Error ${response.status}`);
    }

    return 'data' in json ? json.data : json;
}

const apiGet    = (ep, params = {}) => apiRequest(ep, 'GET', null, params);
const apiPost   = (ep, body, params = {}) => apiRequest(ep, 'POST', body, params);
const apiPut    = (ep, body, params = {}) => apiRequest(ep, 'PUT', body, params);
const apiDelete = (ep, params = {}) => apiRequest(ep, 'DELETE', null, params);

// ── Sesión ────────────────────────────────────────────────────────────
const SESSION_KEY = 'budokai_session';

function getSession() {
    try {
        const raw = sessionStorage.getItem(SESSION_KEY);
        return raw ? JSON.parse(raw) : null;
    } catch { return null; }
}

async function logout() {
    const session = getSession();
    sessionStorage.removeItem(SESSION_KEY);
    if (session?.idSesion) {
        try {
            await fetch(`${BASE_URL}/${EP_LOGIN}?action=cerrar-sesion`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_sesion: session.idSesion }),
            });
        } catch { /* ignorar si falla */ }
    }
    window.location.href = 'login.html';
}

function requireAuth() {
    if (!getSession()) window.location.href = 'login.html';
}

function getRol() {
    return (getSession()?.rol ?? 'viewer').toLowerCase();
}

function esAdmin() {
    return getRol() === 'admin';
}

// modulo: 'torneos' | 'luchadores' | 'combates' | 'staff'
function puedeEscribir(modulo) {
    const rol = getRol();
    if (rol === 'admin') return true;
    if (modulo === 'luchadores') return rol === 'registrador';
    if (modulo === 'combates')   return rol === 'arbitro';
    return false;
}
