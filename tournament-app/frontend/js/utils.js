// frontend/js/utils.js
// Utilidades comunes para todo el frontend del Budokai Tournament System
// Funciones reutilizables: validación, formateo, manejo de errores, etc.

// ── Validaciones ──────────────────────────────────────────────
const validators = {
    // Validar email
    email: (email) => {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    },
    
    // Validar teléfono (formato colombiano)
    telefono: (telefono) => {
        const re = /^(\+57|0057|57)?[1-9][0-9]{9}$/;
        return re.test(telefono.replace(/[\s-]/g, ''));
    },
    
    // Validar cédula colombiana
    cedula: (cedula) => {
        const re = /^[0-9]{6,10}$/;
        return re.test(cedula);
    },
    
    // Validar que un campo no esté vacío
    requerido: (valor) => {
        return valor && valor.toString().trim().length > 0;
    },
    
    // Validar número en rango
    rango: (valor, min, max) => {
        const num = parseFloat(valor);
        return !isNaN(num) && num >= min && num <= max;
    },
    
    // Validar edad
    edad: (edad) => {
        return validators.rango(edad, 18, 100);
    },
    
    // Validar peso
    peso: (peso) => {
        return validators.rango(peso, 40, 200);
    }
};

// ── Formateo de datos ───────────────────────────────────────
const formatters = {
    // Formatear fecha a formato local
    fecha: (fecha, formato = 'corta') => {
        if (!fecha) return '—';
        
        const date = new Date(fecha);
        if (isNaN(date.getTime())) return '—';
        
        switch (formato) {
            case 'corta':
                return date.toLocaleDateString('es-CO');
            case 'larga':
                return date.toLocaleDateString('es-CO', {
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            case 'completa':
                return date.toLocaleString('es-CO');
            case 'iso':
                return date.toISOString().split('T')[0];
            default:
                return date.toLocaleDateString('es-CO');
        }
    },
    
    // Formatear número con separadores
    numero: (numero, decimales = 0) => {
        if (isNaN(numero)) return '—';
        return new Intl.NumberFormat('es-CO', {
            minimumFractionDigits: decimales,
            maximumFractionDigits: decimales
        }).format(numero);
    },
    
    // Formatear teléfono
    telefono: (telefono) => {
        if (!telefono) return '—';
        const limpio = telefono.replace(/[\s-]/g, '');
        if (limpio.length === 10) {
            return `${limpio.slice(0, 3)} ${limpio.slice(3, 6)} ${limpio.slice(6)}`;
        }
        return telefono;
    },
    
    // Capitalizar texto
    capitalizar: (texto) => {
        if (!texto) return '';
        return texto.toLowerCase().replace(/\b\w/g, l => l.toUpperCase());
    },
    
    // Formatear porcentaje
    porcentaje: (valor, decimales = 1) => {
        if (isNaN(valor)) return '—%';
        return `${valor.toFixed(decimales)}%`;
    }
};

// ── Manejo de errores ───────────────────────────────────────
const errorHandler = {
    // Manejar error de API
    apiError: (error, accion = 'operación') => {
        console.error(`Error en ${accion}:`, error);
        
        let mensaje = 'Error desconocido';
        if (error.response) {
            // Error de respuesta HTTP
            switch (error.response.status) {
                case 400:
                    mensaje = 'Solicitud inválida';
                    break;
                case 401:
                    mensaje = 'No autorizado';
                    break;
                case 403:
                    mensaje = 'Acceso prohibido';
                    break;
                case 404:
                    mensaje = 'Recurso no encontrado';
                    break;
                case 500:
                    mensaje = 'Error del servidor';
                    break;
                default:
                    mensaje = `Error ${error.response.status}`;
            }
        } else if (error.message) {
            mensaje = error.message;
        }
        
        return mensaje;
    },
    
    // Validar formulario
    validarFormulario: (formData, reglas) => {
        const errores = {};
        
        Object.entries(reglas).forEach(([campo, regla]) => {
            const valor = formData[campo];
            
            // Validación de campo requerido
            if (regla.requerido && !validators.requerido(valor)) {
                errores[campo] = `El campo ${regla.nombre || campo} es requerido`;
                return;
            }
            
            // Si está vacío y no es requerido, continuar
            if (!valor && !regla.requerido) return;
            
            // Validaciones específicas
            if (regla.tipo === 'email' && !validators.email(valor)) {
                errores[campo] = `El ${regla.nombre || campo} no es válido`;
            }
            
            if (regla.tipo === 'telefono' && !validators.telefono(valor)) {
                errores[campo] = `El ${regla.nombre || campo} no es válido`;
            }
            
            if (regla.tipo === 'cedula' && !validators.cedula(valor)) {
                errores[campo] = `La ${regla.nombre || campo} no es válida`;
            }
            
            if (regla.tipo === 'edad' && !validators.edad(valor)) {
                errores[campo] = `La ${regla.nombre || campo} debe estar entre 18 y 100 años`;
            }
            
            if (regla.tipo === 'peso' && !validators.peso(valor)) {
                errores[campo] = `El ${regla.nombre || campo} debe estar entre 40 y 200 kg`;
            }
            
            if (regla.min && valor.length < regla.min) {
                errores[campo] = `El ${regla.nombre || campo} debe tener al menos ${regla.min} caracteres`;
            }
            
            if (regla.max && valor.length > regla.max) {
                errores[campo] = `El ${regla.nombre || campo} no puede tener más de ${regla.max} caracteres`;
            }
        });
        
        return {
            valido: Object.keys(errores).length === 0,
            errores
        };
    }
};

// ── Utilidades de DOM ──────────────────────────────────────────
const domUtils = {
    // Crear elemento con clases
    crear: (tag, clases = [], contenido = '') => {
        const elemento = document.createElement(tag);
        if (clases.length > 0) {
            elemento.className = clases.join(' ');
        }
        if (contenido) {
            elemento.innerHTML = contenido;
        }
        return elemento;
    },
    
    // Mostrar/ocultar elemento
    toggle: (elemento, mostrar) => {
        if (typeof elemento === 'string') {
            elemento = document.getElementById(elemento);
        }
        if (elemento) {
            elemento.style.display = mostrar ? '' : 'none';
        }
    },
    
    // Agregar clase a elemento
    addClass: (elemento, clase) => {
        if (typeof elemento === 'string') {
            elemento = document.getElementById(elemento);
        }
        if (elemento) {
            elemento.classList.add(clase);
        }
    },
    
    // Remover clase de elemento
    removeClass: (elemento, clase) => {
        if (typeof elemento === 'string') {
            elemento = document.getElementById(elemento);
        }
        if (elemento) {
            elemento.classList.remove(clase);
        }
    },
    
    // Esperar a que el DOM esté listo
    ready: (callback) => {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', callback);
        } else {
            callback();
        }
    }
};

// ── Utilidades de almacenamiento ───────────────────────────────
const storage = {
    // Guardar en sessionStorage
    setSession: (clave, valor) => {
        try {
            sessionStorage.setItem(clave, JSON.stringify(valor));
            return true;
        } catch (error) {
            console.error('Error guardando en sessionStorage:', error);
            return false;
        }
    },
    
    // Obtener de sessionStorage
    getSession: (clave, defecto = null) => {
        try {
            const item = sessionStorage.getItem(clave);
            return item ? JSON.parse(item) : defecto;
        } catch (error) {
            console.error('Error obteniendo de sessionStorage:', error);
            return defecto;
        }
    },
    
    // Eliminar de sessionStorage
    removeSession: (clave) => {
        try {
            sessionStorage.removeItem(clave);
            return true;
        } catch (error) {
            console.error('Error eliminando de sessionStorage:', error);
            return false;
        }
    },
    
    // Guardar en localStorage
    setLocal: (clave, valor) => {
        try {
            localStorage.setItem(clave, JSON.stringify(valor));
            return true;
        } catch (error) {
            console.error('Error guardando en localStorage:', error);
            return false;
        }
    },
    
    // Obtener de localStorage
    getLocal: (clave, defecto = null) => {
        try {
            const item = localStorage.getItem(clave);
            return item ? JSON.parse(item) : defecto;
        } catch (error) {
            console.error('Error obteniendo de localStorage:', error);
            return defecto;
        }
    }
};

// ── Utilidades de exportación ───────────────────────────────────
const exportUtils = {
    // Exportar a CSV
    toCSV: (datos, nombreArchivo = 'datos.csv') => {
        if (!datos || datos.length === 0) {
            console.warn('No hay datos para exportar');
            return;
        }
        
        const headers = Object.keys(datos[0]);
        const csvContent = [
            headers.join(','),
            ...datos.map(fila => 
                headers.map(header => {
                    const valor = fila[header];
                    // Escapar comillas y envolver en comillas si contiene comas
                    if (typeof valor === 'string' && (valor.includes(',') || valor.includes('"'))) {
                        return `"${valor.replace(/"/g, '""')}"`;
                    }
                    return valor ?? '';
                }).join(',')
            )
        ].join('\n');
        
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = nombreArchivo;
        link.click();
    },
    
    // Exportar a JSON
    toJSON: (datos, nombreArchivo = 'datos.json') => {
        const jsonContent = JSON.stringify(datos, null, 2);
        const blob = new Blob([jsonContent], { type: 'application/json' });
        const link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = nombreArchivo;
        link.click();
    }
};

// ── Utilidades de fechas ───────────────────────────────────────
const dateUtils = {
    // Obtener diferencia en días entre dos fechas
    diasDiferencia: (fecha1, fecha2) => {
        const unDia = 24 * 60 * 60 * 1000;
        const diff = Math.abs(new Date(fecha1) - new Date(fecha2));
        return Math.round(diff / unDia);
    },
    
    // Agregar días a una fecha
    agregarDias: (fecha, dias) => {
        const resultado = new Date(fecha);
        resultado.setDate(resultado.getDate() + dias);
        return resultado;
    },
    
    // Verificar si una fecha es hoy
    esHoy: (fecha) => {
        const hoy = new Date();
        const fechaComparar = new Date(fecha);
        return hoy.toDateString() === fechaComparar.toDateString();
    },
    
    // Obtener fecha y hora actual en formato ISO
    ahora: () => {
        return new Date().toISOString();
    }
};

// ── Constantes ─────────────────────────────────────────────────
const constants = {
    // Estados comunes
    ESTADOS: {
        ACTIVO: 'activo',
        INACTIVO: 'inactivo',
        PENDIENTE: 'pendiente',
        EN_CURSO: 'en curso',
        FINALIZADO: 'finalizado',
        CANCELADO: 'cancelado'
    },
    
    // Categorías de peso
    CATEGORIAS: {
        PESO_PLUMA: 'Peso Pluma',
        PESO_LIGERO: 'Peso Ligero',
        PESO_MEDIO: 'Peso Medio',
        PESO_PESADO: 'Peso Pesado',
        PESO_ABSOLUTO: 'Peso Absoluto'
    },
    
    // Tipos de documento
    DOCUMENTOS: {
        CC: 'CC',
        CE: 'CE',
        PA: 'PA'
    },
    
    // Géneros
    GENEROS: {
        MASCULINO: 'masculino',
        FEMENINO: 'femenino'
    }
};

// ── Exportar todo ─────────────────────────────────────────────
window.utils = {
    validators,
    formatters,
    errorHandler,
    domUtils,
    storage,
    exportUtils,
    dateUtils,
    constants
};
