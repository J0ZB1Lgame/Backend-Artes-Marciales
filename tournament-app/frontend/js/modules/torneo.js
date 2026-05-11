// frontend/js/modules/torneo.js
// Lógica de la pantalla de Gestión de Torneos
// Campos reales BD: id_torneo, nombre, fecha_inicio, fecha_fin, estado, 
// categoria, id_campeon, descripcion, reglas, premio

const EP = 'torneo/torneo_api.php';

// ── Estado local ────────────────────────────────────────────
let torneoData = [];
let editingId = null;

// ── Llamadas al backend ─────────────────────────────────────
const fetchTorneos = () => apiGet(EP);
const fetchLuchadores = () => apiGet('luchador/luchador_api.php');

function crearTorneo(datos) {
    return apiPost(EP, datos);
}

function actualizarTorneo(id, datos) {
    return apiPut(EP, datos, { id });
}

function eliminarTorneo(id) {
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
    const activos = lista.filter(t => (t.estado ?? '').toLowerCase() === 'activo').length;
    const finalizados = lista.filter(t => (t.estado ?? '').toLowerCase() === 'finalizado').length;
    const proximos = lista.filter(t => (t.estado ?? '').toLowerCase() === 'próximo').length;

    document.getElementById('stat-total').textContent = total;
    document.getElementById('stat-activos').textContent = activos;
    document.getElementById('stat-finalizados').textContent = finalizados;
    document.getElementById('stat-proximos').textContent = proximos;
}

// ── Badges ───────────────────────────────────────────────────
function estadoBadge(estado) {
    const e = (estado ?? '').toLowerCase();
    if (e.includes('activo')) return `<span class="badge badge-activo">🏆 ACTIVO</span>`;
    if (e.includes('finalizado')) return `<span class="badge badge-finalizado">✅ FINALIZADO</span>`;
    if (e.includes('próximo') || e.includes('proximo')) return `<span class="badge badge-proximo">⏳ PRÓXIMO</span>`;
    if (e.includes('cancelado')) return `<span class="badge badge-cancelado">❌ CANCELADO</span>`;
    return `<span class="badge badge-default">${estado ?? '—'}</span>`;
}

function categoriaBadge(categoria) {
    const c = (categoria ?? '').toLowerCase();
    if (c.includes('peso pesado')) return `<span class="badge badge-pesado">🏋️ PESADO</span>`;
    if (c.includes('peso medio')) return `<span class="badge badge-medio">⚖️ MEDIO</span>`;
    if (c.includes('peso ligero')) return `<span class="badge badge-ligero">🏃 LIGERO</span>`;
    if (c.includes('pluma')) return `<span class="badge badge-pluma">🪶 PLUMA</span>`;
    if (c.includes('absoluto')) return `<span class="badge badge-absoluto">👑 ABSOLUTO</span>`;
    return `<span class="badge badge-default">${categoria ?? '—'}</span>`;
}

// ── Render brackets ─────────────────────────────────────────────
function renderBrackets(lista, luchadores) {
    const container = document.getElementById('bracketsContainer');
    const noData = document.getElementById('no-data');
    
    if (!container) return;
    
    container.innerHTML = '';

    if (!lista || lista.length === 0) {
        if (noData) noData.style.display = 'block';
        return;
    }
    if (noData) noData.style.display = 'none';

    const puedeMod = esAdmin();

    lista.forEach(torneo => {
        const torneoCard = document.createElement('div');
        torneoCard.className = 'tournament-card';

        const campeon = torneo.id_campeon ? luchadores.find(l => l.id_luchador == torneo.id_campeon) : null;

        const campeonBox = `<div class="card-media">
                   <video autoplay muted loop playsinline><source src="../assets/bg/goku.mp4" type="video/mp4"></video>
                   ${campeon ? `<div class="campeon-label">👑 ${campeon.nombre} ${campeon.apellido || ''}</div>` : ''}
               </div>`;

        torneoCard.innerHTML = `
            ${campeonBox}
            <div class="tournament-header">
                <div class="tournament-title">${torneo.nombre || 'Sin nombre'}</div>
                <div class="tournament-status">${estadoBadge(torneo.estado)}</div>
            </div>

            <div class="tournament-info">
                <div class="info-row">
                    <span class="info-label">📅 Inicio:</span>
                    <span class="info-value">${torneo.fecha_inicio ? new Date(torneo.fecha_inicio).toLocaleDateString() : 'Sin fecha'}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">🏁 Fin:</span>
                    <span class="info-value">${torneo.fecha_fin ? new Date(torneo.fecha_fin).toLocaleDateString() : 'Sin fecha'}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">🏷️ Categoría:</span>
                    <span class="info-value">${categoriaBadge(torneo.categoria)}</span>
                </div>
            </div>

            <div class="tournament-description">
                <p>${torneo.descripcion || 'Sin descripción'}</p>
            </div>

            <div class="tournament-actions">
                <button class="btn-bracket" data-id="${torneo.id_torneo}">🏆 VER BRACKET</button>
                ${puedeMod ? `<button class="btn-edit"   data-id="${torneo.id_torneo}">✎ EDITAR</button>` : ''}
                ${puedeMod ? `<button class="btn-delete" data-id="${torneo.id_torneo}">✕ ELIMINAR</button>` : ''}
            </div>
        `;
        
        container.appendChild(torneoCard);
    });

    // Event listeners
    container.querySelectorAll('.btn-edit').forEach(b =>
        b.addEventListener('click', () => abrirEditar(parseInt(b.dataset.id)))
    );
    container.querySelectorAll('.btn-delete').forEach(b =>
        b.addEventListener('click', () => confirmarEliminar(parseInt(b.dataset.id)))
    );
    container.querySelectorAll('.btn-bracket').forEach(b =>
        b.addEventListener('click', () => verBracket(parseInt(b.dataset.id)))
    );
}

// ── Ver bracket ───────────────────────────────────────────────
async function verBracket(torneoId) {
    const torneo = torneoData.find(t => t.id_torneo === torneoId);
    if (!torneo) return;

    document.getElementById('bracket-title').textContent = `🏆 ${torneo.nombre}`;
    document.getElementById('bracket-content').innerHTML =
        '<div style="text-align:center;padding:20px;color:rgba(255,255,255,.5)">Cargando...</div>';
    document.getElementById('modal-bracket').classList.add('open');

    try {
        const [combates, luchadores] = await Promise.all([
            apiGet('combate/combate_api.php'),
            apiGet('luchador/luchador_api.php')
        ]);

        const propios = (combates || []).filter(c => String(c.id_torneo) === String(torneoId));

        const content = document.getElementById('bracket-content');
        content.innerHTML = '';

        if (!propios.length) {
            content.innerHTML = '<div class="bracket-empty">No hay combates registrados para este torneo.</div>';
            return;
        }

        const rondas = {};
        const ordenRondas = ['Octavos de final', 'Cuartos de final', 'Semifinal', 'Final', 'Sin ronda'];
        propios.forEach(c => {
            const r = c.ronda || 'Sin ronda';
            if (!rondas[r]) rondas[r] = [];
            rondas[r].push(c);
        });

        const rondasOrdenadas = [
            ...ordenRondas.filter(r => rondas[r]),
            ...Object.keys(rondas).filter(r => !ordenRondas.includes(r))
        ];

        rondasOrdenadas.forEach(ronda => {
            const section = document.createElement('div');
            section.className = 'bracket-ronda';
            section.innerHTML = `<div class="bracket-ronda-title">${ronda}</div>`;

            rondas[ronda].forEach(c => {
                const l1 = (luchadores || []).find(l => String(l.id_luchador) === String(c.id_luchador_1));
                const l2 = (luchadores || []).find(l => String(l.id_luchador) === String(c.id_luchador_2));
                const n1 = l1 ? `${l1.nombre} ${l1.apellido || ''}`.trim() : 'Luchador 1';
                const n2 = l2 ? `${l2.nombre} ${l2.apellido || ''}`.trim() : 'Luchador 2';
                const ganId = String(c.ganador_id || '');
                const c1Won = ganId && ganId === String(c.id_luchador_1);
                const c2Won = ganId && ganId === String(c.id_luchador_2);

                section.innerHTML += `
                    <div class="bracket-match">
                        <div class="bracket-fighter ${c1Won ? 'winner' : ganId ? 'loser' : ''}">
                            ${c1Won ? '👑 ' : ''}${n1}
                        </div>
                        <div class="bracket-vs">VS</div>
                        <div class="bracket-fighter ${c2Won ? 'winner' : ganId ? 'loser' : ''}">
                            ${c2Won ? '👑 ' : ''}${n2}
                        </div>
                        <div class="bracket-estado">${estadoBadge(c.estado)}</div>
                    </div>`;
            });

            content.appendChild(section);
        });
    } catch (err) {
        document.getElementById('bracket-content').innerHTML =
            '<div class="bracket-empty">Error cargando combates.</div>';
    }
}

// ── Modal functions ───────────────────────────────────────────
function abrirNuevo() {
    editingId = null;
    document.getElementById('modal-title').textContent = 'Crear Torneo';
    document.getElementById('form-torneo').reset();
    document.getElementById('btn-submit').textContent = 'Crear';
    document.getElementById('modal').classList.add('open');
    cargarLuchadoresEnSelect();
}

function abrirEditar(id) {
    const torneo = torneoData.find(t => t.id_torneo === id);
    if (!torneo) return;
    
    editingId = id;
    document.getElementById('modal-title').textContent = 'Editar Torneo';
    document.getElementById('f-nombre').value = torneo.nombre || '';
    document.getElementById('f-fecha-inicio').value = torneo.fecha_inicio ? torneo.fecha_inicio.split('T')[0] : '';
    document.getElementById('f-fecha-fin').value = torneo.fecha_fin ? torneo.fecha_fin.split('T')[0] : '';
    document.getElementById('f-estado').value = torneo.estado || 'proximo';
    document.getElementById('f-categoria').value = torneo.categoria || torneo.tipo || '';
    document.getElementById('f-tiempo-limite').value = torneo.tiempo_limite_minutos || 3;
    document.getElementById('f-campeon').value = torneo.id_campeon || '';
    document.getElementById('f-descripcion').value = torneo.descripcion || '';
    document.getElementById('f-reglas').value = torneo.reglas || '';
    document.getElementById('f-premio').value = torneo.premio || '';
    document.getElementById('btn-submit').textContent = 'Guardar cambios';
    document.getElementById('modal').classList.add('open');
    cargarLuchadoresEnSelect();
}

function cerrarModal() {
    document.getElementById('modal').classList.remove('open');
    editingId = null;
}

async function cargarLuchadoresEnSelect() {
    try {
        const luchadores = await fetchLuchadores();
        const selectCampeon = document.getElementById('f-campeon');
        
        if (selectCampeon) {
            selectCampeon.innerHTML = '<option value="">Sin campeon definido</option>';
            luchadores.forEach(l => {
                selectCampeon.innerHTML += `<option value="${l.id_luchador}">${l.nombre} ${l.apellido || ''}</option>`;
            });
        }
    } catch (error) {
        console.error('Error cargando luchadores:', error);
        toast('Error cargando luchadores', 'error');
    }
}

// ── Form submit ───────────────────────────────────────────
async function guardarTorneo(e) {
    e.preventDefault();
    
    const datos = {
        nombre:                document.getElementById('f-nombre').value,
        fecha_inicio:          document.getElementById('f-fecha-inicio').value,
        fecha_fin:             document.getElementById('f-fecha-fin').value,
        estado:                document.getElementById('f-estado').value,
        categoria:             document.getElementById('f-categoria').value,
        tiempo_limite_minutos: parseInt(document.getElementById('f-tiempo-limite').value) || 3,
        id_campeon:            parseInt(document.getElementById('f-campeon').value) || null,
        descripcion:           document.getElementById('f-descripcion').value,
        reglas:                document.getElementById('f-reglas').value,
        premio:                document.getElementById('f-premio').value
    };

    try {
        if (editingId) {
            await actualizarTorneo(editingId, datos);
            toast('✅ Torneo actualizado correctamente', 'ok');
        } else {
            await crearTorneo(datos);
            toast('✅ Torneo creado correctamente', 'ok');
        }
        
        cerrarModal();
        cargarDatos();
    } catch (error) {
        console.error('Error guardando torneo:', error);
        toast('❌ Error al guardar torneo', 'error');
    }
}

function confirmarEliminar(id) {
    if (confirm('¿Estás seguro de eliminar este torneo?')) {
        eliminarTorneo(id).then(() => {
            toast('✅ Torneo eliminado correctamente', 'ok');
            cargarDatos();
        }).catch(error => {
            console.error('Error eliminando torneo:', error);
            toast('❌ Error al eliminar torneo', 'error');
        });
    }
}

// ── Carga inicial ───────────────────────────────────────────
async function cargarDatos() {
    try {
        const [torneos, luchadores] = await Promise.all([
            fetchTorneos(),
            fetchLuchadores()
        ]);
        
        torneoData = torneos || [];
        renderBrackets(torneoData, luchadores || []);
        actualizarStats(torneoData);
    } catch (error) {
        console.error('Error cargando datos:', error);
        toast('❌ Error cargando datos', 'error');
    }
}

// ── Inicialización ───────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    cargarDatos();

    const openModalBtn  = document.getElementById('openModal');
    const closeModalBtn = document.getElementById('closeModal');
    const formTorneo    = document.getElementById('form-torneo');
    const closeBracket  = document.getElementById('closeBracket');

    if (!esAdmin() && openModalBtn) openModalBtn.style.display = 'none';

    if (openModalBtn)  openModalBtn.addEventListener('click', abrirNuevo);
    if (closeModalBtn) closeModalBtn.addEventListener('click', cerrarModal);
    if (formTorneo)    formTorneo.addEventListener('submit', guardarTorneo);
    if (closeBracket)  closeBracket.addEventListener('click', () =>
        document.getElementById('modal-bracket').classList.remove('open')
    );
});
