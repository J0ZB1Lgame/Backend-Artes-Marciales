// frontend/js/api.js
const USE_MOCK = false; // Backend listo, usar endpoints reales

const BASE_URL = 'http://localhost/Backend-Artes-Marciales/tournament-app/backend/api/endpoints';

// ── Endpoints correctos según staff_api.php y staff_extended_api.php ──
const EP_STAFF = 'staff/staff.api.php';

const EP_LUCHADOR = 'luchador/luchador_api.php';

const EP_COMBATE = 'combate/combate_api.php';

const EP_TORNEO = 'torneo/torneo_api.php';
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
let MOCK_LUCHADORES = [
    { id_luchador: 1, nombre: 'Son Goku', especie: 'Humano', nivel_poder_ki: 9000, origen: 'Tierra', estado: 'activo' },
    { id_luchador: 2, nombre: 'Vegeta', especie: 'Alien', nivel_poder_ki: 8500, origen: 'Planeta Vegeta', estado: 'activo' },
    { id_luchador: 3, nombre: 'Piccolo', especie: 'Alien', nivel_poder_ki: 7000, origen: 'Planeta Namekiano', estado: 'activo' },
    { id_luchador: 4, nombre: 'Krillin', especie: 'Humano', nivel_poder_ki: 1500, origen: 'Tierra', estado: 'activo' },
    { id_luchador: 5, nombre: '18', especie: 'Androide', nivel_poder_ki: 6000, origen: 'Laboratorio Dr. Gero', estado: 'activo' },
];
let _nextLuchadorId = 6;

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

    // ── luchador_api.php ──────────────────────────────────
    if (endpoint === EP_LUCHADOR) {
        if (method === 'GET') {
            if (!action) return [...MOCK_LUCHADORES];
            if (action === 'obtener' && params.id)
                return MOCK_LUCHADORES.find(l => l.id_luchador === +params.id) ?? null;
        }
        if (method === 'POST' && !action) {
            const nuevo = { ...body, id_luchador: _nextLuchadorId++ };
            MOCK_LUCHADORES.push(nuevo);
            return nuevo.id_luchador;
        }
        if (method === 'PUT') {
            const idx = MOCK_LUCHADORES.findIndex(l => l.id_luchador === +body.id_luchador);
            if (idx !== -1) Object.assign(MOCK_LUCHADORES[idx], body);
            return true;
        }
        if (method === 'DELETE' && action === 'eliminar' && params.id) {
            const idx = MOCK_LUCHADORES.findIndex(l => l.id_luchador === +params.id);
            if (idx !== -1) MOCK_LUCHADORES.splice(idx, 1);
            return true;
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


// ── Endpoint de Login ─────────────────────────────────────────────────
const EP_LOGIN = 'login/login_api.php';

// ── Helpers de sesión (usados por login.js y otras páginas) ──────────
const SESSION_KEY = 'budokai_session';

/**
 * Devuelve el objeto de sesión guardado en sessionStorage, o null si no hay.
 * @returns {{ idUsuario: number, username: string, rol: string, idSesion: number|null }|null}
 */
function getSession() {
    try {
        const raw = sessionStorage.getItem(SESSION_KEY);
        return raw ? JSON.parse(raw) : null;
    } catch {
        return null;
    }
}

/**
 * Elimina la sesión del cliente y redirige al login.
 * Llama al backend para cerrar la sesión si hay id_sesion disponible.
 */
async function logout() {
    const session = getSession();
    sessionStorage.removeItem(SESSION_KEY);

    if (session?.idSesion) {
        try {
            await fetch(
                `${BASE_URL}/${EP_LOGIN}?action=cerrar-sesion`,
                {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id_sesion: session.idSesion }),
                }
            );
        } catch {
            // Si falla el backend, igual redirigimos
        }
    }

    window.location.href = 'login.html';
}

/**
 * Guard de autenticación: si no hay sesión activa, redirige al login.
 * Llama esto al inicio de cada página protegida.
 */
function requireAuth() {
    if (!getSession()) {
        window.location.href = 'login.html';
    }
}
