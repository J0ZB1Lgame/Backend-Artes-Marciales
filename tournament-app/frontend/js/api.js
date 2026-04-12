// frontend/js/api.js
// ─────────────────────────────────────────────────────────────────────────────
// REGLA DE ORO (guía del equipo):
//   Ningún fetch() fuera de este archivo.
//   Si cambia el servidor, solo editas BASE_URL aquí.
// ─────────────────────────────────────────────────────────────────────────────

const BASE_URL = 'http://localhost/tournament-app/backend/api/endpoints';

/**
 * Función centralizada de peticiones HTTP.
 * @param {string} endpoint  - Archivo PHP destino, ej: 'staff.php'
 * @param {string} method    - 'GET' | 'POST' | 'PUT' | 'DELETE'
 * @param {object|null} body - Datos para POST/PUT (se serializa a JSON)
 * @param {object} params    - Query params adicionales, ej: { action: 'listar', id: 3 }
 * @returns {Promise<any>}   - El campo `data` de la respuesta del backend
 */
async function apiRequest(endpoint, method = 'GET', body = null, params = {}) {
    const url = new URL(`${BASE_URL}/${endpoint}`);

    // Agrega query params
    Object.entries(params).forEach(([k, v]) => {
        if (v !== undefined && v !== null) url.searchParams.set(k, v);
    });

    const options = {
        method,
        headers: { 'Content-Type': 'application/json' },
    };

    if (body) options.body = JSON.stringify(body);

    const response = await fetch(url.toString(), options);
    const json = await response.json();

    if (json.status === 'error') {
        throw new Error(json.message || `Error ${response.status}`);
    }

    return json.data;
}

// ── Atajos por verbo ─────────────────────────────────────────────────────────
// Uso: apiGet('staff.php', { action: 'listar' })
const apiGet = (ep, params = {}) => apiRequest(ep, 'GET', null, params);
const apiPost = (ep, body, params = {}) => apiRequest(ep, 'POST', body, params);
const apiPut = (ep, body, params = {}) => apiRequest(ep, 'PUT', body, params);
const apiDelete = (ep, params = {}) => apiRequest(ep, 'DELETE', null, params);