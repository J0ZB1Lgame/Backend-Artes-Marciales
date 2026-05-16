// frontend/js/modules/staff.js

const EP     = 'staff/staff_api.php';
const EP_EXT = 'staff/staff_extended_api.php';

let staffData  = [];
let zonasList  = [];
let editingId  = null;
let cardStaffId = null;

// ── Llamadas al backend ─────────────────────────────────────
const fetchStaff  = () => apiGet(EP);
const fetchStaffById = (id) => apiGet(EP, { action: 'obtener', id });
const fetchZonas  = () => apiGet(EP_EXT, { action: 'zona/listar' });
const fetchRoles  = () => apiGet(EP_EXT, { action: 'rol/listar' });

function crearStaff(datos) {
    const session = getSession();
    return apiPost(EP, { ejecutor: session?.idUsuario ?? 1, ...datos });
}
function actualizarStaff(id, datos) {
    return apiPut(EP, datos, { id });           // id va en query string
}
function eliminarStaff(id) {
    return apiDelete(EP, { id });
}
function asignarZona(idStaff, idZona) {
    return apiPut(EP, { id_zona: idZona }, { id: idStaff });
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
    document.getElementById('stat-total').textContent = lista.length;
    document.getElementById('stat-arb').textContent   = lista.filter(s => (s.cargo ?? '').toLowerCase().includes('rbitr')).length;
    document.getElementById('stat-seg').textContent   = lista.filter(s => (s.cargo ?? '').toLowerCase().includes('segur')).length;
    document.getElementById('stat-mant').textContent  = lista.filter(s => (s.cargo ?? '').toLowerCase().includes('mant')).length;
}

// ── Badges ───────────────────────────────────────────────────
function rolBadge(cargo) {
    const c = (cargo ?? '').toLowerCase();
    if (c.includes('rbitr')) return `<span class="badge badge-arb">⚖ ÁRBITRO</span>`;
    if (c.includes('segur')) return `<span class="badge badge-seg">🛡 SEGURIDAD</span>`;
    if (c.includes('mant'))  return `<span class="badge badge-mant">🔧 MANTENIMIENTO</span>`;
    if (c.includes('méd') || c.includes('med'))  return `<span class="badge badge-med">🩺 MÉDICO</span>`;
    if (c.includes('direc')) return `<span class="badge badge-dir">⭐ DIRECTOR</span>`;
    return `<span class="badge badge-default">${cargo ?? '—'}</span>`;
}

function estadoBadge(estado) {
    const activo = (estado ?? '').toString() === '1' || (estado ?? '').toLowerCase() === 'activo';
    return `<span class="badge ${activo ? 'badge-activo' : 'badge-inactivo'}">${activo ? 'ACTIVO' : 'INACTIVO'}</span>`;
}

// ── Render tabla ─────────────────────────────────────────────
function renderTabla(lista) {
    const tbody  = document.getElementById('staff-tbody');
    const noData = document.getElementById('no-data');
    tbody.innerHTML = '';

    if (!lista || lista.length === 0) { noData.style.display = 'block'; return; }
    noData.style.display = 'none';

    const puedeMod = esAdmin();

    lista.forEach((s, i) => {
        const turnoLabel = s.turno ?? '—';
        const tr = document.createElement('tr');
        tr.style.cursor = 'pointer';
        tr.innerHTML = `
            <td class="td-num">#${i + 1}</td>
            <td>${s.numero_documento ?? '—'}</td>
            <td>${s.nombre ?? ''} ${s.apellido ?? ''}</td>
            <td>${rolBadge(s.cargo)}</td>
            <td><span class="zona-pill">${turnoLabel}</span></td>
            <td>${estadoBadge(s.estado)}</td>
            <td>${s.telefono ?? s.email ?? '—'}</td>
            <td class="td-actions">
                ${puedeMod ? `<button class="btn-edit"   data-id="${s.id_staff}">✎ EDITAR</button>`   : ''}
                ${puedeMod ? `<button class="btn-delete" data-id="${s.id_staff}">✕ ELIMINAR</button>` : ''}
            </td>`;
        tr.addEventListener('click', e => {
            if (e.target.closest('.btn-edit, .btn-delete')) return;
            mostrarDetalle(s);
        });
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
    if (rol) lista = lista.filter(s => (s.cargo ?? '').toLowerCase().includes(rol.toLowerCase()));
    renderTabla(lista);
}

// ── Mini tarjeta de detalle ───────────────────────────────────
function mostrarDetalle(s) {
    cardStaffId = s.id_staff;
    const card = document.getElementById('staff-card');

    // Iniciales
    const initials = `${(s.nombre ?? '?')[0]}${(s.apellido ?? '')[0] ?? ''}`.toUpperCase();
    document.getElementById('card-initials').textContent = initials;
    document.getElementById('card-nombre').textContent   = `${s.nombre ?? ''} ${s.apellido ?? ''}`.trim();
    document.getElementById('card-doc').textContent      = s.numero_documento ?? '—';
    document.getElementById('card-cargo-badge').outerHTML = `<div id="card-cargo-badge">${rolBadge(s.cargo)}</div>`;

    // Zona
    const zonaText = s.turno ?? '—';
    document.getElementById('card-zona-text').textContent = zonaText;

    // Info
    document.getElementById('card-tel').textContent   = s.telefono ?? '—';
    document.getElementById('card-email').textContent = s.email    ?? '—';
    document.getElementById('card-estado-badge').outerHTML = `<div id="card-estado-badge">${estadoBadge(s.estado)}</div>`;

    // Zona select — preseleccionar la zona actual
    const sel = document.getElementById('card-zona-select');
    if (s.id_zona) sel.value = s.id_zona;

    // Ocultar asignación si no es admin
    document.getElementById('card-assign-section').style.display = esAdmin() ? 'block' : 'none';
    document.getElementById('card-edit-btn').style.display = esAdmin() ? 'inline-flex' : 'none';

    card.classList.add('visible');
}

function cerrarDetalle() {
    document.getElementById('staff-card').classList.remove('visible');
    cardStaffId = null;
}

// ── Modal crear / editar ─────────────────────────────────────
function abrirNuevo() {
    editingId = null;
    document.getElementById('modal-title').textContent   = 'Registrar miembro';
    document.getElementById('form-staff').reset();
    // Restaurar primer option del select de zona
    document.getElementById('f-turno').value = '';
    document.getElementById('btn-submit').textContent = 'Registrar';
    document.getElementById('modal').classList.add('open');
}

async function abrirEditar(id) {
    let s = staffData.find(x => x.id_staff === id);
    if (!s || !s.cargo || !s.numero_documento || !s.tipo_documento) {
        try {
            const detail = await fetchStaffById(id);
            if (detail) s = detail;
        } catch (err) {
            toast('No se pudo cargar los datos completos del staff.', 'err');
        }
    }
    if (!s) return;
    editingId = id;
    document.getElementById('modal-title').textContent  = 'Editar miembro';
    document.getElementById('f-nombre').value           = s.nombre           ?? '';
    document.getElementById('f-apellido').value         = s.apellido         ?? '';
    document.getElementById('f-tipo-doc').value         = s.tipo_documento   ?? 'CC';
    document.getElementById('f-num-doc').value          = s.numero_documento ?? '';
    document.getElementById('f-telefono').value         = s.telefono         ?? '';
    document.getElementById('f-email').value            = s.email            ?? '';
    document.getElementById('f-cargo').value            = s.cargo            ?? '';
    document.getElementById('f-turno').value            = s.id_zona          ?? '';
    document.getElementById('f-estado').value           = (s.estado === 1 || s.estado === '1' || s.estado === 'activo') ? 'activo' : 'inactivo';
    document.getElementById('btn-submit').textContent   = 'Guardar cambios';
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
    document.getElementById('confirm-msg').textContent = `¿Eliminar a ${nombre}? Esta acción no se puede deshacer.`;
    document.getElementById('confirm').classList.add('open');
    document.getElementById('btn-confirm-ok').onclick = async () => {
        cerrarConfirm();
        try {
            await eliminarStaff(id);
            toast('Miembro eliminado.');
            cerrarDetalle();
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

    const idZonaVal = document.getElementById('f-turno').value;
    const datos = {
        nombre:           document.getElementById('f-nombre').value.trim(),
        apellido:         document.getElementById('f-apellido').value.trim(),
        tipo_documento:   document.getElementById('f-tipo-doc').value,
        numero_documento: document.getElementById('f-num-doc').value.trim(),
        telefono:         document.getElementById('f-telefono').value.trim(),
        email:            document.getElementById('f-email').value.trim(),
        cargo:            document.getElementById('f-cargo').value,
        id_zona:          idZonaVal ? parseInt(idZonaVal) : null,
        estado:           document.getElementById('f-estado').value,
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

// ── Poblar selects de zona ────────────────────────────────────
function poblarZonaSelects(zonas) {
    const selForm = document.getElementById('f-turno');
    const selCard = document.getElementById('card-zona-select');

    const baseOpt = '<option value="">Sin zona asignada</option>';
    const opts = zonas.map(z => `<option value="${z.id_zona}">${z.nombre}</option>`).join('');

    selForm.innerHTML = baseOpt + opts;
    selCard.innerHTML = baseOpt + opts;
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

async function cargarMetadatos() {
    try {
        const zonas = await fetchZonas() ?? [];
        zonasList = zonas;
        poblarZonaSelects(zonas);
    } catch {
        // Si falla, los selects quedan con la opción vacía
    }
}

// ── Gestión de zonas ─────────────────────────────────────────
function renderZonasList(zonas) {
    const container = document.getElementById('zonas-list');
    if (!zonas || zonas.length === 0) {
        container.innerHTML = '<span class="zonas-empty">No hay zonas registradas aún.</span>';
        return;
    }
    container.innerHTML = zonas.map(z => `
        <span class="zona-chip">
            ${z.nombre}
            <button class="zona-chip-del" data-id="${z.id_zona}" title="Eliminar">✕</button>
        </span>`).join('');

    container.querySelectorAll('.zona-chip-del').forEach(btn =>
        btn.addEventListener('click', async () => {
            try {
                await apiDelete(EP_EXT, { action: 'zona/eliminar', id: btn.dataset.id });
                await recargarZonas();
            } catch (e) { toast('Error al eliminar zona: ' + e.message, 'err'); }
        })
    );
}

async function recargarZonas() {
    try {
        const zonas = await fetchZonas() ?? [];
        zonasList = zonas;
        poblarZonaSelects(zonas);
        renderZonasList(zonas);
    } catch (e) { toast('Error al cargar zonas: ' + e.message, 'err'); }
}

async function abrirZonas() {
    document.getElementById('modal-zonas').classList.add('open');
    document.getElementById('zona-nueva-input').value = '';
    const zonas = await fetchZonas().catch(() => []) ?? [];
    renderZonasList(zonas);
}

function cerrarZonas() {
    document.getElementById('modal-zonas').classList.remove('open');
}

// ── Init ─────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', async () => {
    await cargarMetadatos();
    await cargar();

    if (!esAdmin()) {
        document.getElementById('btn-nuevo')?.style.setProperty('display', 'none');
    }

    document.getElementById('btn-nuevo')        ?.addEventListener('click', abrirNuevo);
    document.getElementById('btn-cerrar-modal') ?.addEventListener('click', cerrarModal);
    document.getElementById('modal')            ?.addEventListener('click', e => { if (e.target.id === 'modal') cerrarModal(); });
    document.getElementById('form-staff')       ?.addEventListener('submit', onSubmit);
    document.getElementById('btn-confirm-cancel')?.addEventListener('click', cerrarConfirm);
    document.getElementById('btn-reload')       ?.addEventListener('click', cargar);

    document.getElementById('card-close')?.addEventListener('click', cerrarDetalle);

    document.getElementById('card-edit-btn')?.addEventListener('click', () => {
        if (cardStaffId) { cerrarDetalle(); abrirEditar(cardStaffId); }
    });

    document.getElementById('card-zona-btn')?.addEventListener('click', async () => {
        if (!cardStaffId) return;
        const idZona = parseInt(document.getElementById('card-zona-select').value);
        if (!idZona) { toast('Selecciona una zona', 'err'); return; }
        try {
            await asignarZona(cardStaffId, idZona);
            toast('Zona asignada correctamente.');
            await cargar();
            // Actualizar texto en la tarjeta
            const zona = zonasList.find(z => z.id_zona == idZona);
            if (zona) document.getElementById('card-zona-text').textContent = zona.nombre;
        } catch (err) { toast(err.message, 'err'); }
    });

    document.getElementById('search-input')?.addEventListener('input', e => {
        filtrar(e.target.value, document.getElementById('filter-rol').value);
    });
    document.getElementById('filter-rol')?.addEventListener('change', e => {
        filtrar(document.getElementById('search-input').value, e.target.value);
    });

    // Zonas
    const btnZonas = document.getElementById('btn-zonas');
    if (!esAdmin() && btnZonas) btnZonas.style.display = 'none';
    btnZonas?.addEventListener('click', abrirZonas);
    document.getElementById('btn-zonas-cerrar')?.addEventListener('click', cerrarZonas);
    document.getElementById('modal-zonas')?.addEventListener('click', e => {
        if (e.target.id === 'modal-zonas') cerrarZonas();
    });

    document.getElementById('btn-zona-crear')?.addEventListener('click', async () => {
        const input = document.getElementById('zona-nueva-input');
        const nombre = input.value.trim();
        if (!nombre) { toast('Escribe el nombre de la zona', 'err'); return; }
        try {
            await apiPost(EP_EXT, { nombre }, { action: 'zona/crear' });
            input.value = '';
            toast(`Zona "${nombre}" creada.`);
            await recargarZonas();
        } catch (e) { toast('Error: ' + e.message, 'err'); }
    });

    document.getElementById('zona-nueva-input')?.addEventListener('keydown', e => {
        if (e.key === 'Enter') document.getElementById('btn-zona-crear').click();
    });
});
