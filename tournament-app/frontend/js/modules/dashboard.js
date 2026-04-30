// frontend/js/modules/dashboard.js
// Lógica del dashboard con datos reales

const EP_STAFF = 'staff/staff_api.php';
const EP_LUCHADOR = 'luchador/luchador_api.php';

// ── Toast ────────────────────────────────────────────────
function toast(msg, tipo = 'ok') {
    const el = document.getElementById('toast');
    if (!el) return;
    el.textContent = msg;
    el.className = `toast toast--${tipo} show`;
    clearTimeout(el._t);
    el._t = setTimeout(() => el.classList.remove('show'), 3500);
}

// ── Cargar datos del dashboard ────────────────────────────
async function cargarDatos() {
    const loadingEl = document.getElementById('loading');
    
    try {
        // Mostrar loading
        if (loadingEl) loadingEl.style.display = 'block';

        // Cargar staff con timeout
        let staffData = [];
        let luchadorData = [];
        
        try {
            staffData = await Promise.race([
                apiGet(EP_STAFF),
                new Promise((_, reject) => setTimeout(() => reject(new Error('Timeout')), 5000))
            ]) ?? [];
        } catch (e) {
            console.warn('Error cargando staff:', e.message);
        }
        
        try {
            luchadorData = await Promise.race([
                apiGet(EP_LUCHADOR),
                new Promise((_, reject) => setTimeout(() => reject(new Error('Timeout')), 5000))
            ]) ?? [];
        } catch (e) {
            console.warn('Error cargando luchadores:', e.message);
        }

        // Calcular estadísticas de staff
        const totalStaff = staffData.length || 0;
        const arbitros = staffData.filter(s => (s.cargo ?? '').toLowerCase().includes('rbitr')).length || 0;
        const seguridad = staffData.filter(s => (s.cargo ?? '').toLowerCase().includes('segur')).length || 0;
        const mantenimiento = staffData.filter(s => (s.cargo ?? '').toLowerCase().includes('mant')).length || 0;
        
        // Para turnos activos, contamos staff con turno asignado
        const turnosActivos = staffData.filter(s => s.turno && s.turno.trim()).length || 0;

        // Actualizar tarjetas
        const el1 = document.getElementById('stat-total-staff');
        const el2 = document.getElementById('stat-arbitros');
        const el3 = document.getElementById('stat-seguridad');
        const el4 = document.getElementById('stat-mantenimiento');
        const el5 = document.getElementById('stat-turnos');

        if (el1) el1.textContent = totalStaff;
        if (el2) el2.textContent = arbitros;
        if (el3) el3.textContent = seguridad;
        if (el4) el4.textContent = mantenimiento;
        if (el5) el5.textContent = turnosActivos;

    } catch (err) {
        console.error('Error en cargarDatos:', err);
    } finally {
        // Asegurar que el loading se cierre siempre
        if (loadingEl) loadingEl.style.display = 'none';
    }
}

// ── Init ─────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    cargarDatos();
    
    // Recargar cada 30 segundos
    setInterval(cargarDatos, 30000);
});
