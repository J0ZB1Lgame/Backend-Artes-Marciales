// frontend/js/modules/luchadores.js
// Lógica de la pantalla de Gestión de Luchadores
// Campos: id_luchador, nombre, especie, nivel_poder_ki, origen, estado

const EP = 'luchador/luchador_api.php';

// ── Estado local ────────────────────────────────────────────
let luchadorData = [];
let editingId = null;

// ── Llamadas al backend ─────────────────────────────────────
const fetchLuchadores = () => apiGet(EP);

function crearLuchador(datos) {
    return apiPost(EP, datos);
}

function actualizarLuchador(id, datos) {
    return apiPut(EP, { id_luchador: id, ...datos });
}

function eliminarLuchador(id) {
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
    const humanos = lista.filter(l => (l.especie ?? '').toLowerCase().includes('humano')).length;
    const aliens = lista.filter(l => (l.especie ?? '').toLowerCase().includes('alien')).length;
    const otros = total - humanos - aliens;

    document.getElementById('stat-total').textContent = total;
    document.getElementById('stat-humanos').textContent = humanos;
    document.getElementById('stat-aliens').textContent = aliens;
    document.getElementById('stat-otros').textContent = otros;
}

// ── Badges ───────────────────────────────────────────────────
function especieBadge(especie) {
    const e = (especie ?? '').toLowerCase();
    if (e.includes('humano')) return `<span class="badge badge-humano">👤 HUMANO</span>`;
    if (e.includes('alien')) return `<span class="badge badge-alien">👽 ALIEN</span>`;
    if (e.includes('androide')) return `<span class="badge badge-androide">🤖 ANDROIDE</span>`;
    return `<span class="badge badge-default">${especie ?? '—'}</span>`;
}

function estadoBadge(estado) {
    const activo = (estado ?? '').toLowerCase() === 'activo';
    return `<span class="badge ${activo ? 'badge-activo' : 'badge-inactivo'}">
                ${activo ? 'ACTIVO' : 'INACTIVO'}
            </span>`;
}

// ── Render tabla ─────────────────────────────────────────────
function renderTabla(lista) {
    const tbody = document.getElementById('luchador-tbody');
    const noData = document.getElementById('no-data');
    tbody.innerHTML = '';

    if (!lista || lista.length === 0) {
        noData.style.display = 'block';
        return;
    }
    noData.style.display = 'none';

    lista.forEach((l, i) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="td-num">#${i + 1}</td>
            <td class="td-nombre">${l.nombre ?? '—'}</td>
            <td>${especieBadge(l.especie)}</td>
            <td class="td-ki">${l.nivel_poder_ki ?? '—'}</td>
            <td class="td-origen">${l.origen ?? '—'}</td>
            <td>${estadoBadge(l.estado)}</td>
            <td class="td-actions">
                <button class="btn-edit"   data-id="${l.id_luchador}">✎ EDITAR</button>
                <button class="btn-delete" data-id="${l.id_luchador}">✕ ELIMINAR</button>
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
function filtrar(q, especie) {
    const texto = q.toLowerCase();
    let lista = luchadorData.filter(l =>
        (l.nombre ?? '').toLowerCase().includes(texto)
    );
    if (especie) {
        lista = lista.filter(l => (l.especie ?? '').toLowerCase().includes(especie.toLowerCase()));
    }
    renderTabla(lista);
}

// ── Modal ────────────────────────────────────────────────────
function abrirNuevo() {
    editingId = null;
    document.getElementById('modal-title').textContent = 'Registrar luchador';
    document.getElementById('form-luchador').reset();
    document.getElementById('btn-submit').textContent = 'Registrar';
    document.getElementById('modal').classList.add('open');
}

function abrirEditar(id) {
    const l = luchadorData.find(x => x.id_luchador === id);
    if (!l) return;
    editingId = id;
    document.getElementById('modal-title').textContent = 'Editar luchador';
    document.getElementById('f-nombre').value = l.nombre ?? '';
    document.getElementById('f-especie').value = l.especie ?? '';
    document.getElementById('f-ki').value = l.nivel_poder_ki ?? '';
    document.getElementById('f-origen').value = l.origen ?? '';
    document.getElementById('f-estado').value = l.estado ?? 'activo';
    document.getElementById('btn-submit').textContent = 'Guardar cambios';
    document.getElementById('modal').classList.add('open');
}

function cerrarModal() {
    document.getElementById('modal').classList.remove('open');
    editingId = null;
}

// ── Confirm eliminar ─────────────────────────────────────────
function confirmarEliminar(id) {
    const l = luchadorData.find(x => x.id_luchador === id);
    const nombre = l ? l.nombre : `#${id}`;
    document.getElementById('confirm-msg').textContent =
        `¿Eliminar a ${nombre}? Esta acción no se puede deshacer.`;
    document.getElementById('confirm').classList.add('open');
    document.getElementById('btn-confirm-ok').onclick = async () => {
        cerrarConfirm();
        try {
            await eliminarLuchador(id);
            toast('Luchador eliminado.');
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
        nombre: document.getElementById('f-nombre').value.trim(),
        especie: document.getElementById('f-especie').value.trim(),
        nivel_poder_ki: parseFloat(document.getElementById('f-ki').value) || 0,
        origen: document.getElementById('f-origen').value.trim(),
        estado: document.getElementById('f-estado').value,
    };

    try {
        if (editingId) {
            await actualizarLuchador(editingId, datos);
            toast('Luchador actualizado correctamente.');
        } else {
            await crearLuchador(datos);
            toast('Luchador registrado correctamente.');
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
        luchadorData = await fetchLuchadores() ?? [];
        renderTabla(luchadorData);
        actualizarStats(luchadorData);
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
    document.getElementById('form-luchador')
        ?.addEventListener('submit', onSubmit);
    document.getElementById('btn-confirm-cancel')
        ?.addEventListener('click', cerrarConfirm);
    document.getElementById('btn-reload')
        ?.addEventListener('click', cargar);

    // Filtros en tiempo real
    document.getElementById('search-input')
        ?.addEventListener('input', e => {
            const especie = document.getElementById('filter-especie').value;
            filtrar(e.target.value, especie);
        });
    document.getElementById('filter-especie')
        ?.addEventListener('change', e => {
            const q = document.getElementById('search-input').value;
            filtrar(q, e.target.value);
        });
});
