// frontend/js/modules/staff.js
// ─────────────────────────────────────────────────────────────────────────────
// Toda la lógica de la pantalla de Staff.
// NUNCA hace fetch() directo — siempre delega a api.js (apiGet, apiPost, etc.)
// ─────────────────────────────────────────────────────────────────────────────

const ENDPOINT = 'staff/staff_api.php';

// ════════════════════════════════════════════════════════════════════════════
// ESTADO LOCAL
// ════════════════════════════════════════════════════════════════════════════
let staffData = [];   // cache de la lista completa
let tiposRol = [];   // cache de tipos de rol del backend
let editingId = null; // null = creando, number = editando

// ════════════════════════════════════════════════════════════════════════════
// LLAMADAS AL BACKEND (usan api.js)
// ════════════════════════════════════════════════════════════════════════════

// ── Endpoints
const EP = 'staff/staff_api.php';
const EP_EXT = 'staff/staff_extended_api.php';

// Listar y obtener
async function fetchStaff() { return apiGet(EP); }
async function fetchTiposRol() { return apiGet(EP, { action: 'tipos_rol' }); }
async function fetchTurnos() { return apiGet(EP, { action: 'turnos' }); }
async function fetchZonas() { return apiGet(EP_EXT, { action: 'zona/listar' }); }

// CRUD — nota: PUT manda id en el body y usa ?action=actualizar
async function crearStaff(datos) {
    // El backend pide ejecutor — por ahora va 1 (admin)
    return apiPost(EP, { ejecutor: 1, ...datos });
}
async function actualizarStaff(id, datos) {
    return apiPut(EP, { id, ...datos }, { action: 'actualizar' });
}
async function eliminarStaff(id) {
    return apiDelete(EP, { action: 'eliminar', id });
}

// ════════════════════════════════════════════════════════════════════════════
// HELPERS DE UI
// ════════════════════════════════════════════════════════════════════════════

function mostrarToast(msg, tipo = 'exito') {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className = `toast toast--${tipo} toast--visible`;
    setTimeout(() => t.classList.remove('toast--visible'), 3500);
}

function setLoading(activo) {
    const tr = document.getElementById('tr-loading');
    const te = document.getElementById('tr-empty');
    if (tr) tr.style.display = activo ? 'table-row' : 'none';
    if (te) te.style.display = 'none';
}

function setEmpty(activo) {
    const te = document.getElementById('tr-empty');
    if (te) te.style.display = activo ? 'table-row' : 'none';
}

// ════════════════════════════════════════════════════════════════════════════
// ESTADÍSTICAS (cards superiores)
// ════════════════════════════════════════════════════════════════════════════

const CARGO_MAP = {
    seguridad: 'stat-seguridad',
    árbitro: 'stat-arbitros',
    arbitro: 'stat-arbitros',
    mantenimiento: 'stat-mantenimiento',
};

function actualizarStats(lista) {
    document.getElementById('stat-total').textContent = lista.length;

    const counts = { seguridad: 0, arbitros: 0, mantenimiento: 0 };
    lista.forEach(s => {
        const cargo = (s.cargo ?? '').toLowerCase();
        if (cargo.includes('segur')) counts.seguridad++;
        else if (cargo.includes('arb')) counts.arbitros++;
        else if (cargo.includes('mant')) counts.mantenimiento++;
    });

    document.getElementById('stat-seguridad').textContent = counts.seguridad;
    document.getElementById('stat-arbitros').textContent = counts.arbitros;
    document.getElementById('stat-mantenimiento').textContent = counts.mantenimiento;
}

// ════════════════════════════════════════════════════════════════════════════
// TABLA
// ════════════════════════════════════════════════════════════════════════════

function renderTabla(lista) {
    const tbody = document.getElementById('staff-tbody');
    tbody.innerHTML = '';
    setEmpty(false);

    if (!lista || lista.length === 0) {
        setEmpty(true);
        return;
    }

    lista.forEach(s => {
        const tr = document.createElement('tr');
        tr.classList.add('fade-in');
        tr.innerHTML = `
            <td class="td-id">#${s.id_staff}</td>
            <td>
                <div class="staff-name">${s.nombre} ${s.apellido}</div>
                <div class="staff-email">${s.email ?? '—'}</div>
            </td>
            <td><span class="badge badge--${badgeClass(s.cargo)}">${s.cargo ?? 'Sin rol'}</span></td>
            <td>${s.turno ?? '—'}</td>
            <td class="td-estado">
                <span class="dot dot--${s.estado === 'activo' ? 'on' : 'off'}"></span>
                ${s.estado ?? 'activo'}
            </td>
            <td class="td-actions">
                <button class="btn-icon btn--edit"  data-id="${s.id_staff}" title="Editar">✎</button>
                <button class="btn-icon btn--delete" data-id="${s.id_staff}" title="Eliminar">✕</button>
            </td>
        `;
        tbody.appendChild(tr);
    });

    // Eventos de la tabla
    tbody.querySelectorAll('.btn--edit').forEach(b =>
        b.addEventListener('click', () => abrirModalEditar(parseInt(b.dataset.id)))
    );
    tbody.querySelectorAll('.btn--delete').forEach(b =>
        b.addEventListener('click', () => confirmarEliminar(parseInt(b.dataset.id)))
    );
}

function badgeClass(cargo) {
    const c = (cargo ?? '').toLowerCase();
    if (c.includes('segur')) return 'seguridad';
    if (c.includes('arb')) return 'arbitro';
    if (c.includes('mant')) return 'mantenimiento';
    return 'default';
}

// ════════════════════════════════════════════════════════════════════════════
// BÚSQUEDA / FILTRO LOCAL
// ════════════════════════════════════════════════════════════════════════════

function filtrar(query) {
    const q = query.toLowerCase();
    const lista = staffData.filter(s =>
        `${s.nombre} ${s.apellido}`.toLowerCase().includes(q) ||
        (s.cargo ?? '').toLowerCase().includes(q) ||
        (s.email ?? '').toLowerCase().includes(q)
    );
    renderTabla(lista);
}

// ════════════════════════════════════════════════════════════════════════════
// MODAL — REGISTRAR / EDITAR
// ════════════════════════════════════════════════════════════════════════════

function abrirModalNuevo() {
    editingId = null;
    document.getElementById('modal-title').textContent = 'Registrar nuevo staff';
    document.getElementById('form-staff').reset();
    document.getElementById('modal-overlay').classList.add('modal--open');
    document.getElementById('field-nombre').focus();
}

function abrirModalEditar(id) {
    const s = staffData.find(x => x.id_staff === id);
    if (!s) return;
    editingId = id;

    document.getElementById('modal-title').textContent = 'Editar miembro del staff';
    document.getElementById('field-nombre').value = s.nombre ?? '';
    document.getElementById('field-apellido').value = s.apellido ?? '';
    document.getElementById('field-tipo-doc').value = s.tipo_documento ?? 'CC';
    document.getElementById('field-num-doc').value = s.numero_documento ?? '';
    document.getElementById('field-telefono').value = s.telefono ?? '';
    document.getElementById('field-email').value = s.email ?? '';
    document.getElementById('field-cargo').value = s.cargo ?? '';
    document.getElementById('field-turno').value = s.turno ?? '';
    document.getElementById('field-estado').value = s.estado ?? 'activo';
    document.getElementById('field-id-usuario').value = s.id_usuario ?? '';

    document.getElementById('modal-overlay').classList.add('modal--open');
}

function cerrarModal() {
    document.getElementById('modal-overlay').classList.remove('modal--open');
    editingId = null;
}

async function confirmarEliminar(id) {
    const s = staffData.find(x => x.id_staff === id);
    const nombre = s ? `${s.nombre} ${s.apellido}` : `#${id}`;

    document.getElementById('confirm-msg').textContent =
        `¿Eliminar a ${nombre} del staff? Esta acción no se puede deshacer.`;
    document.getElementById('confirm-overlay').classList.add('modal--open');

    document.getElementById('btn-confirm-ok').onclick = async () => {
        cerrarConfirm();
        await ejecutarEliminar(id);
    };
}

function cerrarConfirm() {
    document.getElementById('confirm-overlay').classList.remove('modal--open');
}

async function ejecutarEliminar(id) {
    try {
        await eliminarStaff(id);
        mostrarToast('Miembro eliminado correctamente.');
        await cargarStaff();
    } catch (e) {
        mostrarToast(e.message, 'error');
    }
}

// ════════════════════════════════════════════════════════════════════════════
// ENVÍO DEL FORMULARIO
// ════════════════════════════════════════════════════════════════════════════

async function enviarFormulario(e) {
    e.preventDefault();
    const btn = document.getElementById('btn-submit-form');
    btn.disabled = true;
    btn.textContent = editingId ? 'Guardando...' : 'Registrando...';

    const datos = {
        id_usuario: parseInt(document.getElementById('field-id-usuario').value) || null,
        nombre: document.getElementById('field-nombre').value.trim(),
        apellido: document.getElementById('field-apellido').value.trim(),
        tipo_documento: document.getElementById('field-tipo-doc').value,
        numero_documento: document.getElementById('field-num-doc').value.trim(),
        telefono: document.getElementById('field-telefono').value.trim(),
        email: document.getElementById('field-email').value.trim(),
        cargo: document.getElementById('field-cargo').value,
        turno: document.getElementById('field-turno').value.trim(),
        estado: document.getElementById('field-estado').value,
    };

    try {
        if (editingId) {
            await actualizarStaff(editingId, datos);
            mostrarToast('Staff actualizado correctamente.');
        } else {
            await crearStaff(datos);
            mostrarToast('Staff registrado correctamente.');
        }
        cerrarModal();
        await cargarStaff();
    } catch (err) {
        mostrarToast(err.message, 'error');
    } finally {
        btn.disabled = false;
        btn.textContent = editingId ? 'Guardar cambios' : 'Registrar';
    }
}

// ════════════════════════════════════════════════════════════════════════════
// CARGA PRINCIPAL
// ════════════════════════════════════════════════════════════════════════════

async function cargarStaff() {
    setLoading(true);
    try {
        staffData = await fetchStaff() ?? [];
        renderTabla(staffData);
        actualizarStats(staffData);
    } catch (err) {
        mostrarToast('Error al cargar el staff: ' + err.message, 'error');
        setEmpty(true);
    } finally {
        setLoading(false);
    }
}

// ════════════════════════════════════════════════════════════════════════════
// INIT — se ejecuta cuando el DOM está listo
// ════════════════════════════════════════════════════════════════════════════

document.addEventListener('DOMContentLoaded', async () => {
    // Carga inicial
    await cargarStaff();

    // Botón "Registrar nuevo"
    document.getElementById('btn-nuevo')
        ?.addEventListener('click', abrirModalNuevo);

    // Cerrar modal principal
    document.getElementById('btn-cerrar-modal')
        ?.addEventListener('click', cerrarModal);
    document.getElementById('modal-overlay')
        ?.addEventListener('click', e => { if (e.target.id === 'modal-overlay') cerrarModal(); });

    // Confirmar eliminar
    document.getElementById('btn-confirm-cancel')
        ?.addEventListener('click', cerrarConfirm);

    // Formulario
    document.getElementById('form-staff')
        ?.addEventListener('submit', enviarFormulario);

    // Búsqueda en tiempo real (filtro local, sin petición)
    document.getElementById('search-input')
        ?.addEventListener('input', e => filtrar(e.target.value));

    // Botón recargar
    document.getElementById('btn-reload')
        ?.addEventListener('click', cargarStaff);
});