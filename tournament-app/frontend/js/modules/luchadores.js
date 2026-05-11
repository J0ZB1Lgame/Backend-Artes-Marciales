// frontend/js/modules/luchadores.js
// Lógica de la pantalla de Gestión de Luchadores
// Campos reales BD: id_luchador, nombre, apellido, tipo_documento, numero_documento, 
// edad, genero, categoria, peso, telefono, email, victorias, derrotas, estado, foto, fecha_registro

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
    const activos = lista.filter(l => (l.estado ?? '').toLowerCase() === 'activo').length;
    const masculino = lista.filter(l => (l.genero ?? '').toLowerCase() === 'masculino').length;
    const femenino = lista.filter(l => (l.genero ?? '').toLowerCase() === 'femenino').length;
    const totalVictorias = lista.reduce((sum, l) => sum + (parseInt(l.victorias) || 0), 0);
    const totalDerrotas = lista.reduce((sum, l) => sum + (parseInt(l.derrotas) || 0), 0);

    document.getElementById('stat-total').textContent = total;
    document.getElementById('stat-activos').textContent = activos;
    document.getElementById('stat-masculino').textContent = masculino;
    document.getElementById('stat-femenino').textContent = femenino;
    document.getElementById('stat-victorias').textContent = totalVictorias;
    document.getElementById('stat-derrotas').textContent = totalDerrotas;
}

// ── Badges ───────────────────────────────────────────────────
function generoBadge(genero) {
    const g = (genero ?? '').toLowerCase();
    if (g.includes('masculino')) return `<span class="badge badge-masculino">👤 MASCULINO</span>`;
    if (g.includes('femenino')) return `<span class="badge badge-femenino">� FEMENINO</span>`;
    return `<span class="badge badge-default">${genero ?? '—'}</span>`;
}

function categoriaBadge(categoria) {
    const c = (categoria ?? '').toLowerCase();
    if (c.includes('peso pesado')) return `<span class="badge badge-pesado">🏋️ PESADO</span>`;
    if (c.includes('peso medio')) return `<span class="badge badge-medio">⚖️ MEDIO</span>`;
    if (c.includes('peso ligero')) return `<span class="badge badge-ligero">🏃 LIGERO</span>`;
    if (c.includes('pluma')) return `<span class="badge badge-pluma">� PLUMA</span>`;
    return `<span class="badge badge-default">${categoria ?? '—'}</span>`;
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

    const puedeMod = puedeEscribir('luchadores');

    lista.forEach((l, i) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td class="td-num">#${i + 1}</td>
            <td class="td-nombre">${l.nombre ?? '—'} ${l.apellido ?? ''}</td>
            <td>${generoBadge(l.genero)}</td>
            <td>${categoriaBadge(l.categoria)}</td>
            <td class="td-peso">${l.peso ?? '—'} kg</td>
            <td class="td-victorias">${l.victorias ?? 0}</td>
            <td class="td-derrotas">${l.derrotas ?? 0}</td>
            <td>${estadoBadge(l.estado)}</td>
            <td class="td-actions">
                ${puedeMod ? `<button class="btn-edit"   data-id="${l.id_luchador}">✎ EDITAR</button>`   : ''}
                ${puedeMod ? `<button class="btn-delete" data-id="${l.id_luchador}">✕ ELIMINAR</button>` : ''}
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
function filtrar(q, categoria) {
    const texto = q.toLowerCase();
    let lista = luchadorData.filter(l =>
        (l.nombre ?? '').toLowerCase().includes(texto)
    );
    if (categoria) {
        lista = lista.filter(l => (l.categoria ?? '').toLowerCase().includes(categoria.toLowerCase()));
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
    document.getElementById('f-apellido').value = l.apellido ?? '';
    document.getElementById('f-tipo-documento').value = l.tipo_documento ?? '';
    document.getElementById('f-numero-documento').value = l.numero_documento ?? '';
    document.getElementById('f-edad').value = l.edad ?? '';
    document.getElementById('f-genero').value = l.genero ?? '';
    document.getElementById('f-categoria').value = l.categoria ?? '';
    document.getElementById('f-peso').value = l.peso ?? '';
    document.getElementById('f-telefono').value = l.telefono ?? '';
    document.getElementById('f-email').value = l.email ?? '';
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
        apellido: document.getElementById('f-apellido').value.trim(),
        tipo_documento: document.getElementById('f-tipo-documento').value.trim(),
        numero_documento: document.getElementById('f-numero-documento').value.trim(),
        edad: parseInt(document.getElementById('f-edad').value) || 0,
        genero: document.getElementById('f-genero').value.trim(),
        categoria: document.getElementById('f-categoria').value.trim(),
        peso: parseFloat(document.getElementById('f-peso').value) || 0,
        telefono: document.getElementById('f-telefono').value.trim(),
        email: document.getElementById('f-email').value.trim(),
        estado: document.getElementById('f-estado').value.trim(),
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

    if (!puedeEscribir('luchadores')) {
        document.getElementById('btn-nuevo')?.style.setProperty('display', 'none');
    }

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
