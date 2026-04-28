// frontend/js/modules/staff.js
// Toda la lógica de la pantalla de Gestión de Staff
// Campos reales del backend: id_staff, numero_documento, nombre, apellido,
// tipo_documento, cargo, turno, estado, telefono, email, id_usuario

const EP = 'staff/staff_api.php';
const EP_EXT = 'staff/staff_extended_api.php';

// ── Estado local ────────────────────────────────────────────
let staffData = [];
let editingId = null;

// ── Llamadas al backend ─────────────────────────────────────
const fetchStaff = () => apiGet(EP);
const fetchTiposRol = () => apiGet(EP, { action: 'tipos_rol' });
const fetchTurnos = () => apiGet(EP, { action: 'turnos' });
const fetchZonas = () => apiGet(EP_EXT, { action: 'zona/listar' });

function crearStaff(datos) {
    return apiPost(EP, { ejecutor: 1, ...datos });
}
function actualizarStaff(id, datos) {
    return apiPut(EP, { id, ...datos }, { action: 'actualizar' });
}
function eliminarStaff(id) {
    return apiDelete(EP, { action: 'eliminar', id });
}

// ── Toast ────────────────────────────────────────────────────
function toast(msg, tipo = 'ok') {
    const el = document.getElementById('toast');
    el.textContent = msg;
    el.className = `toast toast--${tipo} show`;
    clearTimeout(el._t);
    el._t = setTimeout(() => el.classList.remove('show'), 3500);
}

// ── Stats ────────────────────────────────────────────────────
function actualizarStats(lista) {
    const total = lista.length;
    const arb = lista.filter(s => (s.cargo ?? '').toLowerCase().includes('rbitr')).length;
    const seg = lista.filter(s => (s.cargo ?? '').toLowerCase().includes('segur')).length;
    const mant = lista.filter(s => (s.cargo ?? '').toLowerCase().includes('mant')).length;

    document.getElementById('stat-total').textContent = total;
    document.getElementById('stat-arb').textContent = arb;
    document.getElementById('stat-seg').textContent = seg;
    document.getElementById('stat-mant').textContent = mant;
}

// ── Badges ───────────────────────────────────────────────────
function rolBadge(cargo) {
    const c = (cargo ?? '').toLowerCase();
    if (c.includes('rbitr')) return `<span class="badge badge-arb">⚖ ÁRBITRO</span>`;
    if (c.includes('segur')) return `<span class="badge badge-seg">🛡 SEGURIDAD</span>`;
    if (c.includes('mant')) return `<span class="badge badge-mant">🔧 MANTENIMIENTO</span>`;
    return `<span class="badge badge-default">${cargo ?? '—'}</span>`;
}

function estadoBadge(estado) {
    const activo = (estado ?? '').toLowerCase() === 'activo';
    return `<span class="badge ${activo ? 'badge-activo' : 'badge-inactivo'}">
                ${activo ? 'ACTIVO' : 'INACTIVO'}
            </span>`;
}

// ── Render tabla ─────────────────────────────────────────────
function renderTabla(lista) {
    const tbody = document.getElementById('staff-tbody');
    const noData = document.getElementById('no-data');
    tbody.innerHTML = '';

    if (!lista || lista.length === 0) {
        noData.style.display = 'block';
        return;
    }
    noData.style.display = 'none';

    lista.forEach((s, i) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="td-num">#${i + 1}</td>
            <td class="td-id">${s.numero_documento ?? '—'}</td>
            <td class="td-nombre">${s.nombre ?? ''} ${s.apellido ?? ''}</td>
            <td>${rolBadge(s.cargo)}</td>
            <td>${s.turno ?? '—'}</td>
            <td>${estadoBadge(s.estado)}</td>
            <td class="td-contacto">${s.telefono ?? s.email ?? '—'}</td>
            <td class="td-actions">
                <button class="btn-edit"   data-id="${s.id_staff}">✎ EDITAR</button>
                <button class="btn-delete" data-id="${s.id_staff}">✕ ELIMINAR</button>
            </td>
        `;
        tbody.appendChild(tr);
    });

    tbody.querySelectorAll('.btn-edit').forEach(b =>
        b.addEventListener('click', () => abrirEditar(parseInt(b.dataset.id)))
    );
    tbody.querySelectorAll('.btn-delete').forEach(b =>
        b.addEventListener('click', () => confirmarEliminar(parseInt(b.dataset.id)))
    );
}

// ── Filtro local ─────────────────────────────────────────────
function filtrar(q, rol) {
    const texto = q.toLowerCase();
    let lista = staffData.filter(s =>
        `${s.nombre} ${s.apellido}`.toLowerCase().includes(texto) ||
        (s.numero_documento ?? '').includes(texto)
    );
    if (rol) {
        lista = lista.filter(s => (s.cargo ?? '').toLowerCase().includes(rol.toLowerCase()));
    }
    renderTabla(lista);
}

// ── Modal ────────────────────────────────────────────────────
function abrirNuevo() {
    editingId = null;
    document.getElementById('modal-title').textContent = 'Registrar miembro';
    document.getElementById('form-staff').reset();
    document.getElementById('btn-submit').textContent = 'Registrar';
    document.getElementById('modal').classList.add('open');
}

function abrirEditar(id) {
    const s = staffData.find(x => x.id_staff === id);
    if (!s) return;
    editingId = id;
    document.getElementById('modal-title').textContent = 'Editar miembro';
    document.getElementById('f-nombre').value = s.nombre ?? '';
    document.getElementById('f-apellido').value = s.apellido ?? '';
    document.getElementById('f-tipo-doc').value = s.tipo_documento ?? 'CC';
    document.getElementById('f-num-doc').value = s.numero_documento ?? '';
    document.getElementById('f-telefono').value = s.telefono ?? '';
    document.getElementById('f-email').value = s.email ?? '';
    document.getElementById('f-cargo').value = s.cargo ?? '';
    document.getElementById('f-turno').value = s.turno ?? '';
    document.getElementById('f-estado').value = s.estado ?? 'activo';
    document.getElementById('f-id-usuario').value = s.id_usuario ?? '';
    document.getElementById('btn-submit').textContent = 'Guardar cambios';
    document.getElementById('modal').classList.add('open');
}

function cerrarModal() {
    document.getElementById('modal').classList.remove('open');
    editingId = null;
}

// ── Confirm eliminar ─────────────────────────────────────────
function confirmarEliminar(id) {
    const s = staffData.find(x => x.id_staff === id);
    const nombre = s ? `${s.nombre} ${s.apellido}` : `#${id}`;
    document.getElementById('confirm-msg').textContent =
        `¿Eliminar a ${nombre}? Esta acción no se puede deshacer.`;
    document.getElementById('confirm').classList.add('open');
    document.getElementById('btn-confirm-ok').onclick = async () => {
        cerrarConfirm();
        try {
            await eliminarStaff(id);
            toast('Miembro eliminado.');
            await cargar();
        } catch (e) { toast(e.message, 'err'); }
    };
}

function cerrarConfirm() {
    document.getElementById('confirm').classList.remove('open');
}

// ── Submit formulario ────────────────────────────────────────
async function onSubmit(e) {
    e.preventDefault();
    const btn = document.getElementById('btn-submit');
    btn.disabled = true;

    const datos = {
        id_usuario: parseInt(document.getElementById('f-id-usuario').value) || 1,
        nombre: document.getElementById('f-nombre').value.trim(),
        apellido: document.getElementById('f-apellido').value.trim(),
        tipo_documento: document.getElementById('f-tipo-doc').value,
        numero_documento: document.getElementById('f-num-doc').value.trim(),
        telefono: document.getElementById('f-telefono').value.trim(),
        email: document.getElementById('f-email').value.trim(),
        cargo: document.getElementById('f-cargo').value,
        turno: document.getElementById('f-turno').value.trim(),
        estado: document.getElementById('f-estado').value,
    };

    try {
        if (editingId) {
            await actualizarStaff(editingId, datos);
            toast('Staff actualizado correctamente.');
        } else {
            await crearStaff(datos);
            toast('Staff registrado correctamente.');
        }
        cerrarModal();
        await cargar();
    } catch (err) {
        toast(err.message, 'err');
    } finally {
        btn.disabled = false;
    }
}

// ── Carga principal ──────────────────────────────────────────
async function cargar() {
    document.getElementById('loading').style.display = 'block';
    document.getElementById('no-data').style.display = 'none';
    try {
        staffData = await fetchStaff() ?? [];
        renderTabla(staffData);
        actualizarStats(staffData);
    } catch (err) {
        toast('Error al cargar: ' + err.message, 'err');
    } finally {
        document.getElementById('loading').style.display = 'none';
    }
}

// ── Init ─────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    cargar();

    document.getElementById('btn-nuevo')
        ?.addEventListener('click', abrirNuevo);
    document.getElementById('btn-cerrar-modal')
        ?.addEventListener('click', cerrarModal);
    document.getElementById('modal')
        ?.addEventListener('click', e => { if (e.target.id === 'modal') cerrarModal(); });
    document.getElementById('form-staff')
        ?.addEventListener('submit', onSubmit);
    document.getElementById('btn-confirm-cancel')
        ?.addEventListener('click', cerrarConfirm);
    document.getElementById('btn-reload')
        ?.addEventListener('click', cargar);

    // Filtros en tiempo real
    document.getElementById('search-input')
        ?.addEventListener('input', e => {
            const rol = document.getElementById('filter-rol').value;
            filtrar(e.target.value, rol);
        });
    document.getElementById('filter-rol')
        ?.addEventListener('change', e => {
            const q = document.getElementById('search-input').value;
            filtrar(q, e.target.value);
        });
});