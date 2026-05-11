// frontend/js/modules/sistema.js
// Lógica de la pantalla de Administración del Sistema
// Funcionalidades: configuración general, logs del sistema, mantenimiento, estado del servidor

// ── Estado local ────────────────────────────────────────────
let configData = {};
let logsData = [];
let serverStatus = {};

// ── Llamadas al backend ─────────────────────────────────────
const API_CONFIG = 'sistema/config_api.php';
const API_LOGS = 'sistema/logs_api.php';
const API_STATUS = 'sistema/status_api.php';

const fetchConfig = () => apiGet(API_CONFIG);
const fetchLogs = () => apiGet(API_LOGS);
const fetchServerStatus = () => apiGet(API_STATUS);

function actualizarConfig(datos) {
    return apiPut(API_CONFIG, datos);
}

function limpiarLogs(tipo) {
    return apiDelete(API_LOGS, { action: 'limpiar', tipo });
}

function ejecutarMantenimiento(accion) {
    return apiPost(API_STATUS, { action: accion });
}

// ── Toast ────────────────────────────────────────────────────
function toast(msg, tipo = 'ok') {
    const el = document.getElementById('toast');
    if (!el) {
        const toastEl = document.createElement('div');
        toastEl.id = 'toast';
        toastEl.className = `toast toast--${tipo} show`;
        toastEl.textContent = msg;
        document.body.appendChild(toastEl);
        setTimeout(() => toastEl.remove(), 3500);
    } else {
        el.textContent = msg;
        el.className = `toast toast--${tipo} show`;
        clearTimeout(el._t);
        el._t = setTimeout(() => el.classList.remove('show'), 3500);
    }
}

// ── Render configuración ─────────────────────────────────────
function renderConfiguracion() {
    const container = document.getElementById('config-container');
    if (!container) return;
    
    container.innerHTML = `
        <div class="config-section">
            <h3>⚙️ Configuración General</h3>
            <div class="config-form">
                <div class="form-group">
                    <label>Nombre del Sistema</label>
                    <input type="text" id="config-nombre-sistema" value="${configData.nombre_sistema || 'Budokai Tournament System'}">
                </div>
                <div class="form-group">
                    <label>Correo del Administrador</label>
                    <input type="email" id="config-email-admin" value="${configData.email_admin || ''}">
                </div>
                <div class="form-group">
                    <label>Límite de Luchadores por Torneo</label>
                    <input type="number" id="config-max-luchadores" value="${configData.max_luchadores || 32}" min="8" max="128">
                </div>
                <div class="form-group">
                    <label>Duración del Combate (minutos)</label>
                    <input type="number" id="config-duracion-combate" value="${configData.duracion_combate || 3}" min="1" max="15">
                </div>
                <div class="form-group">
                    <label>Modo Mantenimiento</label>
                    <select id="config-modo-mantenimiento">
                        <option value="0" ${!configData.modo_mantenimiento ? 'selected' : ''}>Desactivado</option>
                        <option value="1" ${configData.modo_mantenimiento ? 'selected' : ''}>Activado</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Notificaciones por Email</label>
                    <select id="config-notificaciones">
                        <option value="1" ${configData.notificaciones !== '0' ? 'selected' : ''}>Activadas</option>
                        <option value="0" ${configData.notificaciones === '0' ? 'selected' : ''}>Desactivadas</option>
                    </select>
                </div>
                <button class="save-btn" onclick="guardarConfiguracion()">💾 Guardar Configuración</button>
            </div>
        </div>
    `;
}

// ── Render logs del sistema ─────────────────────────────────
function renderLogs() {
    const container = document.getElementById('logs-container');
    if (!container) return;
    
    // Filtrar logs por nivel
    const nivelFilter = document.getElementById('log-level-filter')?.value || 'todos';
    let logsFiltrados = logsData;
    
    if (nivelFilter !== 'todos') {
        logsFiltrados = logsData.filter(log => log.nivel === nivelFilter);
    }
    
    container.innerHTML = `
        <div class="logs-header">
            <div class="logs-controls">
                <select id="log-level-filter" onchange="renderLogs()">
                    <option value="todos">Todos los niveles</option>
                    <option value="error">Errores</option>
                    <option value="warning">Advertencias</option>
                    <option value="info">Información</option>
                    <option value="debug">Debug</option>
                </select>
                <button class="btn-refresh" onclick="cargarLogs()">🔄 Actualizar</button>
                <button class="btn-clear" onclick="limpiarLogsConfirm()">🗑️ Limpiar Logs</button>
            </div>
        </div>
        <div class="logs-list">
            ${logsFiltrados.length === 0 ? 
                '<div class="no-logs">No hay registros de logs para mostrar</div>' :
                logsFiltrados.map(log => `
                    <div class="log-entry log-${log.nivel}">
                        <div class="log-header">
                            <span class="log-timestamp">${new Date(log.fecha).toLocaleString()}</span>
                            <span class="log-level">${log.nivel.toUpperCase()}</span>
                            <span class="log-modulo">${log.modulo || 'Sistema'}</span>
                        </div>
                        <div class="log-message">${log.mensaje}</div>
                        ${log.detalle ? `<div class="log-detail">${log.detalle}</div>` : ''}
                    </div>
                `).join('')
            }
        </div>
    `;
}

// ── Render estado del servidor ─────────────────────────────
function renderServerStatus() {
    const container = document.getElementById('status-container');
    if (!container) return;
    
    const uptime = serverStatus.uptime || 'Desconocido';
    const memoria = serverStatus.memoria || { usada: 0, total: 0 };
    const cpu = serverStatus.cpu || { uso: 0 };
    const disco = serverStatus.disco || { usado: 0, total: 0 };
    const db = serverStatus.database || { status: 'Desconocido', conexiones: 0 };
    
    container.innerHTML = `
        <div class="status-grid">
            <div class="status-card">
                <h3>🖥️ Sistema</h3>
                <div class="status-item">
                    <span class="status-label">Estado:</span>
                    <span class="status-value ${serverStatus.estado === 'online' ? 'online' : 'offline'}">
                        ${serverStatus.estado === 'online' ? '🟢 En línea' : '🔴 Fuera de línea'}
                    </span>
                </div>
                <div class="status-item">
                    <span class="status-label">Tiempo activo:</span>
                    <span class="status-value">${uptime}</span>
                </div>
                <div class="status-item">
                    <span class="status-label">Último reinicio:</span>
                    <span class="status-value">${serverStatus.ultimo_reinicio ? new Date(serverStatus.ultimo_reinicio).toLocaleString() : 'Desconocido'}</span>
                </div>
            </div>
            
            <div class="status-card">
                <h3>💾 Recursos</h3>
                <div class="status-item">
                    <span class="status-label">CPU:</span>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: ${cpu.uso}%"></div>
                        <span class="progress-text">${cpu.uso}%</span>
                    </div>
                </div>
                <div class="status-item">
                    <span class="status-label">Memoria:</span>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: ${(memoria.usada / memoria.total) * 100}%"></div>
                        <span class="progress-text">${Math.round(memoria.usada)}MB / ${memoria.total}MB</span>
                    </div>
                </div>
                <div class="status-item">
                    <span class="status-label">Disco:</span>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: ${(disco.usado / disco.total) * 100}%"></div>
                        <span class="progress-text">${Math.round(disco.usado)}GB / ${disco.total}GB</span>
                    </div>
                </div>
            </div>
            
            <div class="status-card">
                <h3>🗄️ Base de Datos</h3>
                <div class="status-item">
                    <span class="status-label">Estado:</span>
                    <span class="status-value ${db.status === 'online' ? 'online' : 'offline'}">
                        ${db.status === 'online' ? '🟢 Conectada' : '🔴 Desconectada'}
                    </span>
                </div>
                <div class="status-item">
                    <span class="status-label">Conexiones activas:</span>
                    <span class="status-value">${db.conexiones}</span>
                </div>
                <div class="status-item">
                    <span class="status-label">Última consulta:</span>
                    <span class="status-value">${db.ultima_consulta ? new Date(db.ultima_consulta).toLocaleString() : 'Desconocido'}</span>
                </div>
            </div>
            
            <div class="status-card">
                <h3>🔧 Mantenimiento</h3>
                <div class="maintenance-actions">
                    <button class="maintenance-btn" onclick="ejecutarMantenimientoAction('backup')">
                        💾 Crear Backup
                    </button>
                    <button class="maintenance-btn" onclick="ejecutarMantenimientoAction('optimize')">
                        ⚡ Optimizar BD
                    </button>
                    <button class="maintenance-btn" onclick="ejecutarMantenimientoAction('cache')">
                        🗑️ Limpiar Caché
                    </button>
                    <button class="maintenance-btn" onclick="ejecutarMantenimientoAction('restart')">
                        🔄 Reiniciar Servicios
                    </button>
                </div>
            </div>
        </div>
    `;
}

// ── Funciones de configuración ─────────────────────────────
async function guardarConfiguracion() {
    const config = {
        nombre_sistema: document.getElementById('config-nombre-sistema').value,
        email_admin: document.getElementById('config-email-admin').value,
        max_luchadores: parseInt(document.getElementById('config-max-luchadores').value),
        duracion_combate: parseInt(document.getElementById('config-duracion-combate').value),
        modo_mantenimiento: document.getElementById('config-modo-mantenimiento').value === '1',
        notificaciones: document.getElementById('config-notificaciones').value
    };
    
    try {
        await actualizarConfig(config);
        configData = { ...configData, ...config };
        toast('✅ Configuración guardada correctamente', 'ok');
    } catch (error) {
        console.error('Error guardando configuración:', error);
        toast('❌ Error al guardar configuración', 'error');
    }
}

// ── Funciones de logs ─────────────────────────────────────
async function cargarLogs() {
    try {
        logsData = await fetchLogs();
        renderLogs();
    } catch (error) {
        console.error('Error cargando logs:', error);
        toast('❌ Error al cargar logs', 'error');
    }
}

function limpiarLogsConfirm() {
    const nivel = document.getElementById('log-level-filter')?.value || 'todos';
    if (confirm(`¿Estás seguro de eliminar todos los logs${nivel !== 'todos' ? ` de nivel ${nivel}` : ''}?`)) {
        limpiarLogs(nivel).then(() => {
            toast('🗑️ Logs eliminados correctamente', 'ok');
            cargarLogs();
        }).catch(error => {
            console.error('Error eliminando logs:', error);
            toast('❌ Error al eliminar logs', 'error');
        });
    }
}

// ── Funciones de mantenimiento ─────────────────────────────
async function ejecutarMantenimientoAction(accion) {
    const mensajes = {
        backup: 'Creando backup del sistema...',
        optimize: 'Optimizando base de datos...',
        cache: 'Limpiando caché del sistema...',
        restart: 'Reiniciando servicios...'
    };
    
    try {
        toast(`⏳ ${mensajes[accion]}`, 'info');
        await ejecutarMantenimiento(accion);
        toast(`✅ ${mensajes[accion].replace('...', ' completado')}`, 'ok');
        
        // Recargar estado del servidor después de mantenimiento
        if (accion === 'restart') {
            setTimeout(() => {
                cargarServerStatus();
            }, 3000);
        }
    } catch (error) {
        console.error(`Error en mantenimiento (${accion}):`, error);
        toast(`❌ Error en ${accion}`, 'error');
    }
}

// ── Carga inicial ───────────────────────────────────────────
async function cargarDatos() {
    try {
        const [config, logs, status] = await Promise.all([
            fetchConfig(),
            fetchLogs(),
            fetchServerStatus()
        ]);
        
        configData = config || {};
        logsData = logs || [];
        serverStatus = status || {};
        
        renderConfiguracion();
        renderLogs();
        renderServerStatus();
        
        toast('⚙️ Panel de sistema cargado', 'ok');
    } catch (error) {
        console.error('Error cargando datos del sistema:', error);
        toast('❌ Error cargando datos del sistema', 'error');
    }
}

async function cargarServerStatus() {
    try {
        serverStatus = await fetchServerStatus();
        renderServerStatus();
    } catch (error) {
        console.error('Error cargando estado del servidor:', error);
    }
}

// ── Actualización automática ───────────────────────────────
let statusInterval;
function iniciarActualizacionAutomatica() {
    // Actualizar estado del servidor cada 30 segundos
    statusInterval = setInterval(() => {
        cargarServerStatus();
    }, 30000);
}

function detenerActualizacionAutomatica() {
    if (statusInterval) {
        clearInterval(statusInterval);
    }
}

// ── Inicialización ───────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    cargarDatos();
    iniciarActualizacionAutomatica();
    
    // Limpiar intervalo al salir de la página
    window.addEventListener('beforeunload', () => {
        detenerActualizacionAutomatica();
    });
});
