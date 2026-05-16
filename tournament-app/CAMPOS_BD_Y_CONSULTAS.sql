-- =============================================================================
-- BUDOKAI TOURNAMENT SYSTEM - CAMPOS Y CONSULTAS SQL
-- =============================================================================

-- ─────────────────────────────────────────────────────────────────────────────
-- TABLA: ZONAS (Áreas del torneo)
-- ─────────────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS zonas (
    id_zona INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ─────────────────────────────────────────────────────────────────────────────
-- TABLA: STAFF (Personal del torneo)
-- ─────────────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS staff (
    id_staff INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    tipo_documento VARCHAR(3) NOT NULL, -- CC, CE, PA
    numero_documento VARCHAR(20) NOT NULL UNIQUE,
    telefono VARCHAR(20),
    email VARCHAR(100),
    cargo VARCHAR(50) NOT NULL, -- Árbitro, Seguridad, Mantenimiento, Director, Médico
    id_zona INT,
    estado VARCHAR(20) DEFAULT 'activo', -- activo, inactivo
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_zona) REFERENCES zonas(id_zona) ON DELETE SET NULL,
    INDEX idx_cargo (cargo),
    INDEX idx_estado (estado),
    INDEX idx_numero_doc (numero_documento)
);

-- ─────────────────────────────────────────────────────────────────────────────
-- TABLA: LUCHADORES (Competidores)
-- ─────────────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS luchadores (
    id_luchador INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    tipo_documento VARCHAR(3) NOT NULL, -- CC, CE, PA
    numero_documento VARCHAR(20) NOT NULL UNIQUE,
    edad INT,
    genero VARCHAR(20), -- masculino, femenino
    categoria VARCHAR(50), -- Peso Pesado, Peso Medio, Peso Ligero, Pluma
    peso DECIMAL(5,2), -- en kg
    telefono VARCHAR(20),
    email VARCHAR(100),
    victorias INT DEFAULT 0,
    derrotas INT DEFAULT 0,
    estado VARCHAR(20) DEFAULT 'activo', -- activo, inactivo
    foto LONGBLOB,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_categoria (categoria),
    INDEX idx_estado (estado),
    INDEX idx_numero_doc (numero_documento)
);

-- ─────────────────────────────────────────────────────────────────────────────
-- TABLA: COMBATES (Encuentros del torneo)
-- ─────────────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS combates (
    id_combate INT PRIMARY KEY AUTO_INCREMENT,
    id_luchador_1 INT NOT NULL,
    id_luchador_2 INT NOT NULL,
    id_arbitro INT,
    id_zona INT,
    ronda INT,
    resultado VARCHAR(50), -- Ganador ID, Empate, etc
    fecha_combate DATETIME,
    estado VARCHAR(20) DEFAULT 'programado', -- programado, en_progreso, finalizado, cancelado
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_luchador_1) REFERENCES luchadores(id_luchador) ON DELETE CASCADE,
    FOREIGN KEY (id_luchador_2) REFERENCES luchadores(id_luchador) ON DELETE CASCADE,
    FOREIGN KEY (id_arbitro) REFERENCES staff(id_staff) ON DELETE SET NULL,
    FOREIGN KEY (id_zona) REFERENCES zonas(id_zona) ON DELETE SET NULL,
    INDEX idx_estado (estado),
    INDEX idx_fecha (fecha_combate)
);

-- ─────────────────────────────────────────────────────────────────────────────
-- TABLA: TORNEOS (Campeonatos)
-- ─────────────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS torneos (
    id_torneo INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    fecha_inicio DATETIME NOT NULL,
    fecha_fin DATETIME,
    categoria VARCHAR(50),
    estado VARCHAR(20) DEFAULT 'programado', -- programado, en_progreso, finalizado
    ganador_id INT,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ganador_id) REFERENCES luchadores(id_luchador) ON DELETE SET NULL,
    INDEX idx_estado (estado),
    INDEX idx_fecha (fecha_inicio)
);

-- =============================================================================
-- DATOS DE EJEMPLO PARA IMPORTAR
-- =============================================================================

-- Zonas de ejemplo
INSERT INTO zonas (nombre, descripcion) VALUES
    ('Arena Norte', 'Zona principal norte del estadio'),
    ('Arena Sur', 'Zona principal sur del estadio'),
    ('Arena Central', 'Zona central del estadio'),
    ('Sala de Entrenamientos', 'Zona de preparación'),
    ('Zona de Enfermería', 'Área médica');

-- Staff de ejemplo
INSERT INTO staff (nombre, apellido, tipo_documento, numero_documento, telefono, email, cargo, id_zona, estado) VALUES
    ('Goku', 'Son', 'CC', '1111111111', '3001234567', 'goku@torneo.com', 'Árbitro', 1, 'activo'),
    ('Vegeta', 'Briefs', 'CC', '2222222222', '3001234568', 'vegeta@torneo.com', 'Seguridad', 2, 'activo'),
    ('Piccolo', 'Ma Junior', 'CE', '3333333333', '3001234569', 'piccolo@torneo.com', 'Árbitro', 3, 'activo'),
    ('Bulma', 'Brief', 'CC', '4444444444', '3001234570', 'bulma@torneo.com', 'Director', 1, 'activo'),
    ('Krillin', 'Ox-King', 'CC', '5555555555', '3001234571', 'krillin@torneo.com', 'Médico', 5, 'activo');

-- Luchadores de ejemplo
INSERT INTO luchadores (nombre, apellido, tipo_documento, numero_documento, edad, genero, categoria, peso, telefono, email, victorias, derrotas, estado) VALUES
    ('Juan', 'Pérez', 'CC', '12345678', 25, 'masculino', 'Peso Medio', 75.5, '3001234567', 'juan@email.com', 12, 3, 'activo'),
    ('María', 'González', 'CC', '87654321', 23, 'femenino', 'Peso Ligero', 60.2, '3009876543', 'maria@email.com', 8, 2, 'activo'),
    ('Carlos', 'López', 'CC', '11223344', 28, 'masculino', 'Peso Pesado', 90.0, '3001111111', 'carlos@email.com', 15, 5, 'activo'),
    ('Ana', 'Martínez', 'CC', '55667788', 22, 'femenino', 'Peso Pluma', 52.3, '3002222222', 'ana@email.com', 6, 1, 'activo');

-- =============================================================================
-- CONSULTAS DE VALIDACIÓN
-- =============================================================================

-- Ver todos los staff con sus zonas
SELECT
    s.id_staff, s.nombre, s.apellido, s.numero_documento,
    s.cargo, z.nombre as zona, s.estado
FROM staff s
LEFT JOIN zonas z ON s.id_zona = z.id_zona
ORDER BY s.cargo;

-- Ver todos los luchadores
SELECT
    id_luchador, nombre, apellido, numero_documento,
    edad, genero, categoria, peso, estado
FROM luchadores
ORDER BY nombre;

-- Ver zonas disponibles
SELECT id_zona, nombre FROM zonas ORDER BY nombre;

-- =============================================================================
-- ÍNDICES PARA BÚSQUEDAS RÁPIDAS
-- =============================================================================

-- Ya incluidos en las CREATE TABLE anteriores
-- Pero aquí están listados para referencia:
-- - staff: id_cargo, id_estado, id_numero_doc
-- - luchadores: id_categoria, id_estado, id_numero_doc
-- - combates: id_estado, id_fecha
-- - torneos: id_estado, id_fecha
