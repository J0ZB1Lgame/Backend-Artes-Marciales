// frontend/js/modules/dashboard.js
// Carga estadísticas reales del backend para el dashboard de inicio

async function cargarDatos() {
    try {
        const [luchadores, combates, torneos, staff] = await Promise.all([
            apiGet(EP_LUCHADOR).catch(() => []),
            apiGet(EP_COMBATE).catch(() => []),
            apiGet(EP_TORNEO).catch(() => []),
            apiGet(EP_STAFF).catch(() => []),
        ]);

        const set = (id, val) => { const el = document.getElementById(id); if (el) el.textContent = val; };

        set('stat-luchadores', (luchadores || []).length);
        set('stat-combates',   (combates   || []).length);
        set('stat-torneos',    (torneos    || []).length);
        set('stat-staff',      (staff      || []).length);

        const session = getSession();
        if (session) {
            const rol  = document.querySelector('.user-role');
            const name = document.querySelector('.user-name');
            if (rol)  rol.textContent  = session.rol      || 'Administrador';
            if (name) name.textContent = session.username || 'Admin';
        }
    } catch (err) {
        console.error('Error en cargarDatos dashboard:', err);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    cargarDatos();
    setInterval(cargarDatos, 30000);
});
