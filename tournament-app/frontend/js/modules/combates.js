// frontend/js/modules/combates.js
// Lógica de la pantalla de Gestión de Combates
// Campos reales BD: id_combate, id_luchador_rojo, id_luchador_azul, id_ganador, 
// fecha_combate, estado, categoria, arena, ronda, observaciones

const EP = 'combate/combate_api.php';

// ── Estado local ────────────────────────────────────────────
let combateData = [];
let editingId = null;

// ── Llamadas al backend ─────────────────────────────────────
const fetchCombates   = () => apiGet(EP);
const fetchLuchadores = () => apiGet('luchador/luchador_api.php');
const fetchTorneos    = () => apiGet('torneo/torneo_api.php');

function crearCombate(datos) {
    return apiPost(EP, datos);
}

function actualizarCombate(id, datos) {
    return apiPut(EP, datos, { id });
}

function eliminarCombate(id) {
    return apiDelete(EP, { id });
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

// ── Stats ────────────────────────────────────────────────────
function actualizarStats(lista) {
    const total = lista.length;
    const pendientes = lista.filter(c => (c.estado ?? '').toLowerCase() === 'pendiente').length;
    const enCurso = lista.filter(c => (c.estado ?? '').toLowerCase() === 'en curso').length;
    const finalizados = lista.filter(c => (c.estado ?? '').toLowerCase() === 'finalizado').length;

    document.getElementById('stat-total').textContent = total;
    document.getElementById('stat-pendientes').textContent = pendientes;
    document.getElementById('stat-en-curso').textContent = enCurso;
    document.getElementById('stat-finalizados').textContent = finalizados;
}

// ── Badges ───────────────────────────────────────────────────
function estadoBadge(estado) {
    const e = (estado ?? '').toLowerCase();
    if (e.includes('pendiente')) return `<span class="badge badge-pendiente">⏳ PENDIENTE</span>`;
    if (e.includes('en curso')) return `<span class="badge badge-en-curso">🥊 EN CURSO</span>`;
    if (e.includes('finalizado')) return `<span class="badge badge-finalizado">✅ FINALIZADO</span>`;
    return `<span class="badge badge-default">${estado ?? '—'}</span>`;
}

function categoriaBadge(categoria) {
    const c = (categoria ?? '').toLowerCase();
    if (c.includes('peso pesado')) return `<span class="badge badge-pesado">🏋️ PESADO</span>`;
    if (c.includes('peso medio')) return `<span class="badge badge-medio">⚖️ MEDIO</span>`;
    if (c.includes('peso ligero')) return `<span class="badge badge-ligero">🏃 LIGERO</span>`;
    if (c.includes('pluma')) return `<span class="badge badge-pluma">🪶 PLUMA</span>`;
    return `<span class="badge badge-default">${categoria ?? '—'}</span>`;
}

// ── Render cards ─────────────────────────────────────────────
function renderCards(lista, luchadores) {
    const grid = document.getElementById('combatGrid');
    const noData = document.getElementById('no-data');
    
    if (!grid) return;
    
    grid.innerHTML = '';

    if (!lista || lista.length === 0) {
        if (noData) noData.style.display = 'block';
        return;
    }
    if (noData) noData.style.display = 'none';

    const puedeEditar   = puedeEscribir('combates');
    const puedeEliminar = esAdmin();

    lista.forEach((combate, i) => {
        const luchadorRojo = luchadores.find(l => l.id_luchador == combate.id_luchador_1);
        const luchadorAzul = luchadores.find(l => l.id_luchador == combate.id_luchador_2);
        const ganador = combate.ganador_id ? luchadores.find(l => l.id_luchador == combate.ganador_id) : null;
        const estado = (combate.estado ?? '').toLowerCase();

        const mediaBox = estado.includes('pendiente')
            ? `<div class="card-media"><img src="../assets/bg/pendiente.jpg" alt="Pendiente" onerror="this.style.display='none'"></div>`
            : estado.includes('en curso')
                ? `<div class="card-media"><video autoplay muted loop playsinline><source src="../assets/bg/combates.mp4" type="video/mp4"></video></div>`
                : '';

        const card = document.createElement('div');
        card.className = 'combat-card';
        card.innerHTML = `
            ${mediaBox}
            <div class="combat-top">
                <div class="combat-status">
                    ${estadoBadge(combate.estado)}
                </div>
                <div class="combat-date">
                    📅 ${combate.fecha_combate ? new Date(combate.fecha_combate).toLocaleDateString() : 'Sin fecha'}
                </div>
            </div>

            <div class="vs-container">
                <div class="fighter ${ganador && ganador.id_luchador == combate.id_luchador_1 ? 'winner' : ''}">
                    <div class="fighter-avatar">
                        ${ganador && ganador.id_luchador == combate.id_luchador_1 ? '👑' : '🥋'}
                    </div>
                    <div class="fighter-name">
                        ${luchadorRojo ? `${luchadorRojo.nombre} ${luchadorRojo.apellido || ''}` : 'Luchador 1'}
                    </div>
                    <div class="fighter-info">
                        ${luchadorRojo ? `${luchadorRojo.categoria || 'Sin categoría'}` : ''}
                    </div>
                </div>

                <div class="vs-text">VS</div>

                <div class="fighter ${ganador && ganador.id_luchador == combate.id_luchador_2 ? 'winner' : ''}">
                    <div class="fighter-avatar">
                        ${ganador && ganador.id_luchador == combate.id_luchador_2 ? '👑' : '🥋'}
                    </div>
                    <div class="fighter-name">
                        ${luchadorAzul ? `${luchadorAzul.nombre} ${luchadorAzul.apellido || ''}` : 'Luchador 2'}
                    </div>
                    <div class="fighter-info">
                        ${luchadorAzul ? `${luchadorAzul.categoria || 'Sin categoría'}` : ''}
                    </div>
                </div>
            </div>

            <div class="combat-bottom">
                <div class="combat-arena">
                    🏟️ ${combate.arena || 'Sin arena asignada'}
                </div>
                <div class="combat-category">
                    ${categoriaBadge(combate.categoria)}
                </div>
            </div>

            <div class="combat-actions">
                ${puedeEditar   ? `<button class="btn-edit"   data-id="${combate.id_combate}">✎ EDITAR</button>`   : ''}
                ${puedeEliminar ? `<button class="btn-delete" data-id="${combate.id_combate}">✕ ELIMINAR</button>` : ''}
            </div>
        `;
        grid.appendChild(card);
    });

    // Event listeners
    grid.querySelectorAll('.btn-edit').forEach(b =>
        b.addEventListener('click', () => abrirEditar(parseInt(b.dataset.id)))
    );
    grid.querySelectorAll('.btn-delete').forEach(b =>
        b.addEventListener('click', () => confirmarEliminar(parseInt(b.dataset.id)))
    );
}

// ─── Modal functions ────────────────────────────────────────
function abrirNuevo() {
    editingId = null;
    document.getElementById('modal-title').textContent = 'Crear Combate';
    document.getElementById('form-combate').reset();
    document.getElementById('btn-submit').textContent = 'Crear';
    document.getElementById('modal').classList.add('open');
    cargarLuchadoresEnSelect();
}

function abrirEditar(id) {
    const combate = combateData.find(c => c.id_combate === id);
    if (!combate) return;

    editingId = id;
    document.getElementById('modal-title').textContent = 'Editar Combate';
    document.getElementById('f-fecha').value = combate.fecha_combate ? combate.fecha_combate.split('T')[0] : '';
    document.getElementById('f-estado').value = combate.estado || 'pendiente';
    document.getElementById('f-arena').value = combate.arena || '';
    document.getElementById('f-ronda').value = combate.ronda || '';
    document.getElementById('f-observaciones').value = combate.observaciones || '';
    document.getElementById('btn-submit').textContent = 'Guardar cambios';
    document.getElementById('modal').classList.add('open');
    cargarLuchadoresEnSelect(combate);
}

function cerrarModal() {
    document.getElementById('modal').classList.remove('open');
    editingId = null;
}

async function cargarLuchadoresEnSelect(combate = null) {
    try {
        const [luchadores, torneos] = await Promise.all([fetchLuchadores(), fetchTorneos()]);

        const selectTorneo  = document.getElementById('f-torneo');
        const selectRojo    = document.getElementById('f-luchador-rojo');
        const selectAzul    = document.getElementById('f-luchador-azul');
        const selectGanador = document.getElementById('f-ganador');

        if (selectTorneo) {
            selectTorneo.innerHTML = '<option value="">Seleccionar torneo</option>';
            (torneos || []).forEach(t => {
                const opt = `<option value="${t.id_torneo}">${t.nombre}</option>`;
                selectTorneo.innerHTML += opt;
            });
            if (combate) selectTorneo.value = combate.id_torneo || '';
        }

        if (selectRojo) {
            selectRojo.innerHTML = '<option value="">Luchador 1 (rojo)</option>';
            luchadores.forEach(l => {
                selectRojo.innerHTML += `<option value="${l.id_luchador}">${l.nombre} ${l.apellido || ''}</option>`;
            });
            if (combate) selectRojo.value = combate.id_luchador_1 || '';
        }

        if (selectAzul) {
            selectAzul.innerHTML = '<option value="">Luchador 2 (azul)</option>';
            luchadores.forEach(l => {
                selectAzul.innerHTML += `<option value="${l.id_luchador}">${l.nombre} ${l.apellido || ''}</option>`;
            });
            if (combate) selectAzul.value = combate.id_luchador_2 || '';
        }

        if (selectGanador) {
            selectGanador.innerHTML = '<option value="">Sin ganador</option>';
            luchadores.forEach(l => {
                selectGanador.innerHTML += `<option value="${l.id_luchador}">${l.nombre} ${l.apellido || ''}</option>`;
            });
            if (combate) selectGanador.value = combate.ganador_id || '';
        }
    } catch (error) {
        console.error('Error cargando selects:', error);
        toast('Error cargando datos del formulario', 'error');
    }
}

// ─── Form submit ───────────────────────────────────────────
async function guardarCombate(e) {
    e.preventDefault();
    
    const datos = {
        id_torneo:      parseInt(document.getElementById('f-torneo').value)        || null,
        id_luchador_1:  parseInt(document.getElementById('f-luchador-rojo').value) || null,
        id_luchador_2:  parseInt(document.getElementById('f-luchador-azul').value) || null,
        ganador_id:     parseInt(document.getElementById('f-ganador').value)       || null,
        fecha_combate:  document.getElementById('f-fecha').value,
        estado:         document.getElementById('f-estado').value,
        arena:          document.getElementById('f-arena').value,
        ronda:          document.getElementById('f-ronda').value,
        observaciones:  document.getElementById('f-observaciones').value,
        duracion_segundos: 0,
        puntos_luchador_1: 0,
        puntos_luchador_2: 0
    };

    try {
        if (editingId) {
            await actualizarCombate(editingId, datos);
            toast('✅ Combate actualizado correctamente', 'ok');
        } else {
            await crearCombate(datos);
            toast('✅ Combate creado correctamente', 'ok');
        }
        
        cerrarModal();
        cargarDatos();
    } catch (error) {
        console.error('Error guardando combate:', error);
        toast('❌ Error al guardar combate', 'error');
    }
}

function confirmarEliminar(id) {
    if (confirm('¿Estás seguro de eliminar este combate?')) {
        eliminarCombate(id).then(() => {
            toast('✅ Combate eliminado correctamente', 'ok');
            cargarDatos();
        }).catch(error => {
            console.error('Error eliminando combate:', error);
            toast('❌ Error al eliminar combate', 'error');
        });
    }
}

// ─── Carga inicial ───────────────────────────────────────────
async function cargarDatos() {
    try {
        const [combates, luchadores] = await Promise.all([
            fetchCombates(),
            fetchLuchadores()
        ]);
        
        combateData = combates || [];
        renderCards(combateData, luchadores || []);
        actualizarStats(combateData);
    } catch (error) {
        console.error('Error cargando datos:', error);
        toast('❌ Error cargando datos', 'error');
    }
}

// ─── Inicialización ───────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    cargarDatos();

    const openModalBtn = document.getElementById('openModal');
    const closeModalBtn = document.getElementById('closeModal');
    const formCombate = document.getElementById('form-combate');

    if (!esAdmin() && openModalBtn) openModalBtn.style.display = 'none';

    if (openModalBtn) openModalBtn.addEventListener('click', abrirNuevo);
    if (closeModalBtn) closeModalBtn.addEventListener('click', cerrarModal);
    if (formCombate) formCombate.addEventListener('submit', guardarCombate);
});
