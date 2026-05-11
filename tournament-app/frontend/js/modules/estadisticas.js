// frontend/js/modules/estadisticas.js
// Lógica de la pantalla de Estadísticas y Rankings
// Funcionalidades: ranking de luchadores, estadísticas globales, campeones por torneo

// ── Estado local ────────────────────────────────────────────
let luchadoresData = [];
let torneosData = [];
let combatesData = [];

// ── Llamadas al backend ─────────────────────────────────────
const fetchLuchadores = () => apiGet('luchador/luchador_api.php');
const fetchTorneos = () => apiGet('torneo/torneo_api.php');
const fetchCombates = () => apiGet('combate/combate_api.php');

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

// ── Cálculo de estadísticas ───────────────────────────────────
function calcularEstadisticasGenerales() {
    const totalLuchadores = luchadoresData.length;
    const totalCombates = combatesData.length;
    const totalTorneos = torneosData.length;
    const torneosActivos = torneosData.filter(t => (t.estado ?? '').toLowerCase() === 'activo').length;
    const torneosFinalizados = torneosData.filter(t => (t.estado ?? '').toLowerCase() === 'finalizado').length;
    
    // Calcular totales de victorias y derrotas
    const totalVictorias = luchadoresData.reduce((sum, l) => sum + (parseInt(l.victorias) || 0), 0);
    const totalDerrotas = luchadoresData.reduce((sum, l) => sum + (parseInt(l.derrotas) || 0), 0);
    
    return {
        totalLuchadores,
        totalCombates,
        totalTorneos,
        torneosActivos,
        torneosFinalizados,
        totalVictorias,
        totalDerrotas
    };
}

// ── Generar ranking de luchadores ─────────────────────────────
function generarRankingLuchadores() {
    return luchadoresData
        .map(luchador => {
            const victorias = parseInt(luchador.victorias) || 0;
            const derrotas = parseInt(luchador.derrotas) || 0;
            const totalCombates = victorias + derrotas;
            const porcentajeVictorias = totalCombates > 0 ? (victorias / totalCombates * 100).toFixed(1) : 0;
            
            return {
                ...luchador,
                victorias,
                derrotas,
                totalCombates,
                porcentajeVictorias: parseFloat(porcentajeVictorias)
            };
        })
        .filter(l => l.totalCombates > 0) // Solo luchadores con combates
        .sort((a, b) => {
            // Primero por porcentaje de victorias
            if (b.porcentajeVictorias !== a.porcentajeVictorias) {
                return b.porcentajeVictorias - a.porcentajeVictorias;
            }
            // Si hay empate, por total de victorias
            return b.victorias - a.victorias;
        });
}

// ── Render estadísticas generales ───────────────────────────
function renderEstadisticasGenerales() {
    const stats = calcularEstadisticasGenerales();
    
    // Actualizar tarjetas principales
    document.getElementById('stat-luchadores').textContent = stats.totalLuchadores;
    document.getElementById('stat-combates').textContent = stats.totalCombates;
    document.getElementById('stat-torneos').textContent = stats.totalTorneos;
    document.getElementById('stat-victorias').textContent = stats.totalVictorias;
    document.getElementById('stat-derrotas').textContent = stats.totalDerrotas;
    document.getElementById('stat-activos').textContent = stats.torneosActivos;
    document.getElementById('stat-finalizados').textContent = stats.torneosFinalizados;
}

// ── Render ranking de luchadores ─────────────────────────
function renderRankingLuchadores() {
    const ranking = generarRankingLuchadores();
    const tbody = document.getElementById('ranking-tbody');
    const noData = document.getElementById('no-ranking-data');
    
    if (!tbody) return;
    
    tbody.innerHTML = '';

    if (ranking.length === 0) {
        if (noData) noData.style.display = 'block';
        return;
    }
    if (noData) noData.style.display = 'none';

    ranking.forEach((luchador, index) => {
        const tr = document.createElement('tr');
        tr.className = index < 3 ? `top-${index + 1}` : '';
        
        tr.innerHTML = `
            <td class="td-rank">
                ${index === 0 ? '🥇' : index === 1 ? '🥈' : index === 2 ? '🥉' : `#${index + 1}`}
            </td>
            <td class="td-nombre">${luchador.nombre} ${luchador.apellido || ''}</td>
            <td class="td-categoria">${luchador.categoria || '—'}</td>
            <td class="td-victorias">${luchador.victorias}</td>
            <td class="td-derrotas">${luchador.derrotas}</td>
            <td class="td-total">${luchador.totalCombates}</td>
            <td class="td-porcentaje">
                <div class="porcentaje-bar">
                    <div class="porcentaje-fill" style="width: ${luchador.porcentajeVictorias}%"></div>
                    <span class="porcentaje-text">${luchador.porcentajeVictorias}%</span>
                </div>
            </td>
            <td class="td-estado">${luchador.estado === 'activo' ? '✅ Activo' : '❌ Inactivo'}</td>
        `;
        
        tbody.appendChild(tr);
    });
}

// ── Render campeones por torneo ───────────────────────────
function renderCampeonesPorTorneo() {
    const torneosConCampeon = torneosData.filter(t => t.id_campeon);
    const container = document.getElementById('campeones-container');
    const noData = document.getElementById('no-campeones-data');
    
    if (!container) return;
    
    container.innerHTML = '';

    if (torneosConCampeon.length === 0) {
        if (noData) noData.style.display = 'block';
        return;
    }
    if (noData) noData.style.display = 'none';

    torneosConCampeon.forEach(torneo => {
        const campeon = luchadoresData.find(l => l.id_luchador === torneo.id_campeon);
        if (!campeon) return;
        
        const card = document.createElement('div');
        card.className = 'campeon-card';
        
        card.innerHTML = `
            <div class="campeon-trofeo">🏆</div>
            <div class="campeon-info">
                <div class="campeon-torneo">${torneo.nombre}</div>
                <div class="campeon-nombre">${campeon.nombre} ${campeon.apellido || ''}</div>
                <div class="campeon-categoria">${campeon.categoria || 'Sin categoría'}</div>
                <div class="campeon-record">V: ${campeon.victorias || 0} - D: ${campeon.derrotas || 0}</div>
            </div>
            <div class="campeon-fecha">
                ${torneo.fecha_fin ? new Date(torneo.fecha_fin).toLocaleDateString() : 'Sin fecha'}
            </div>
        `;
        
        container.appendChild(card);
    });
}

// ── Render estadísticas por categoría ───────────────────────
function renderEstadisticasPorCategoria() {
    const categorias = {};
    
    // Agrupar luchadores por categoría
    luchadoresData.forEach(luchador => {
        const categoria = luchador.categoria || 'Sin categoría';
        if (!categorias[categoria]) {
            categorias[categoria] = {
                nombre: categoria,
                luchadores: 0,
                victorias: 0,
                derrotas: 0,
                activos: 0
            };
        }
        
        categorias[categoria].luchadores++;
        categorias[categoria].victorias += parseInt(luchador.victorias) || 0;
        categorias[categoria].derrotas += parseInt(luchador.derrotas) || 0;
        if (luchador.estado === 'activo') {
            categorias[categoria].activos++;
        }
    });
    
    const container = document.getElementById('categorias-container');
    if (!container) return;
    
    container.innerHTML = '';
    
    Object.values(categorias).forEach(categoria => {
        const totalCombates = categoria.victorias + categoria.derrotas;
        const porcentajeVictorias = totalCombates > 0 ? (categoria.victorias / totalCombates * 100).toFixed(1) : 0;
        
        const card = document.createElement('div');
        card.className = 'categoria-stat-card';
        
        card.innerHTML = `
            <div class="categoria-nombre">${categoria.nombre}</div>
            <div class="categoria-stats">
                <div class="stat-row">
                    <span class="stat-label">Luchadores:</span>
                    <span class="stat-value">${categoria.luchadores}</span>
                </div>
                <div class="stat-row">
                    <span class="stat-label">Activos:</span>
                    <span class="stat-value">${categoria.activos}</span>
                </div>
                <div class="stat-row">
                    <span class="stat-label">Victorias:</span>
                    <span class="stat-value">${categoria.victorias}</span>
                </div>
                <div class="stat-row">
                    <span class="stat-label">Derrotas:</span>
                    <span class="stat-value">${categoria.derrotas}</span>
                </div>
                <div class="stat-row">
                    <span class="stat-label">% Victorias:</span>
                    <span class="stat-value">${porcentajeVictorias}%</span>
                </div>
            </div>
        `;
        
        container.appendChild(card);
    });
}

// ── Filtros y búsqueda ─────────────────────────────────────
function filtrarRanking(categoria, estado) {
    let ranking = generarRankingLuchadores();
    
    if (categoria && categoria !== 'todas') {
        ranking = ranking.filter(l => 
            (l.categoria || '').toLowerCase() === categoria.toLowerCase()
        );
    }
    
    if (estado && estado !== 'todos') {
        ranking = ranking.filter(l => 
            (l.estado || '').toLowerCase() === estado.toLowerCase()
        );
    }
    
    renderRankingFiltrado(ranking);
}

function renderRankingFiltrado(ranking) {
    const tbody = document.getElementById('ranking-tbody');
    if (!tbody) return;
    
    tbody.innerHTML = '';
    
    ranking.forEach((luchador, index) => {
        const tr = document.createElement('tr');
        tr.className = index < 3 ? `top-${index + 1}` : '';
        
        tr.innerHTML = `
            <td class="td-rank">
                ${index === 0 ? '🥇' : index === 1 ? '🥈' : index === 2 ? '🥉' : `#${index + 1}`}
            </td>
            <td class="td-nombre">${luchador.nombre} ${luchador.apellido || ''}</td>
            <td class="td-categoria">${luchador.categoria || '—'}</td>
            <td class="td-victorias">${luchador.victorias}</td>
            <td class="td-derrotas">${luchador.derrotas}</td>
            <td class="td-total">${luchador.totalCombates}</td>
            <td class="td-porcentaje">
                <div class="porcentaje-bar">
                    <div class="porcentaje-fill" style="width: ${luchador.porcentajeVictorias}%"></div>
                    <span class="porcentaje-text">${luchador.porcentajeVictorias}%</span>
                </div>
            </td>
            <td class="td-estado">${luchador.estado === 'activo' ? '✅ Activo' : '❌ Inactivo'}</td>
        `;
        
        tbody.appendChild(tr);
    });
}

// ── Exportar datos ─────────────────────────────────────────
function exportarEstadisticas(formato) {
    const ranking = generarRankingLuchadores();
    
    if (formato === 'csv') {
        let csv = 'Posición,Nombre,Categoría,Victorias,Derrotas,Total Combates,% Victorias,Estado\n';
        ranking.forEach((l, index) => {
            csv += `${index + 1},"${l.nombre} ${l.apellido || ''}","${l.categoria || ''}",${l.victorias},${l.derrotas},${l.totalCombates},${l.porcentajeVictorias}%,${l.estado}\n`;
        });
        
        const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = `ranking_luchadores_${new Date().toISOString().split('T')[0]}.csv`;
        link.click();
        
        toast('📊 Estadísticas exportadas en CSV', 'ok');
    } else if (formato === 'json') {
        const json = JSON.stringify(ranking, null, 2);
        const blob = new Blob([json], { type: 'application/json' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = `ranking_luchadores_${new Date().toISOString().split('T')[0]}.json`;
        link.click();
        
        toast('📊 Estadísticas exportadas en JSON', 'ok');
    }
}

// ── Carga inicial ───────────────────────────────────────────
async function cargarDatos() {
    try {
        const [luchadores, torneos, combates] = await Promise.all([
            fetchLuchadores(),
            fetchTorneos(),
            fetchCombates()
        ]);
        
        luchadoresData = luchadores || [];
        torneosData = torneos || [];
        combatesData = combates || [];
        
        renderEstadisticasGenerales();
        renderRankingLuchadores();
        renderCampeonesPorTorneo();
        renderEstadisticasPorCategoria();
        
        toast('📊 Estadísticas cargadas correctamente', 'ok');
    } catch (error) {
        console.error('Error cargando estadísticas:', error);
        toast('❌ Error cargando datos de estadísticas', 'error');
    }
}

// ── Inicialización ───────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    cargarDatos();
    
    // Event listeners para filtros
    const categoriaFilter = document.getElementById('categoria-filter');
    const estadoFilter = document.getElementById('estado-filter');
    const exportCsvBtn = document.getElementById('export-csv');
    const exportJsonBtn = document.getElementById('export-json');
    
    if (categoriaFilter) {
        categoriaFilter.addEventListener('change', () => {
            filtrarRanking(categoriaFilter.value, estadoFilter?.value);
        });
    }
    
    if (estadoFilter) {
        estadoFilter.addEventListener('change', () => {
            filtrarRanking(categoriaFilter?.value, estadoFilter.value);
        });
    }
    
    if (exportCsvBtn) {
        exportCsvBtn.addEventListener('click', () => exportarEstadisticas('csv'));
    }
    
    if (exportJsonBtn) {
        exportJsonBtn.addEventListener('click', () => exportarEstadisticas('json'));
    }
});
