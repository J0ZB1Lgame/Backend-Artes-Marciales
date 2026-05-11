-- ============================================================
-- BASE DE DATOS: torneo_new
-- Script completo de creación y datos de prueba
-- Ejecutar desde phpMyAdmin o línea de comandos MySQL
-- ============================================================

DROP DATABASE IF EXISTS torneo_new;
CREATE DATABASE torneo_new
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE torneo_new;

-- ============================================================
-- TABLA: roles
-- ============================================================
CREATE TABLE roles (
    id_rol      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(50)  NOT NULL UNIQUE,
    descripcion VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLA: zonas
-- ============================================================
CREATE TABLE zonas (
    id_zona     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(100) NOT NULL UNIQUE,
    descripcion VARCHAR(255)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLA: usuario
-- Módulo 1 – Seguridad
-- estado: TINYINT(1) — 1=activo, 0=inactivo (coincide con el DAO PHP)
-- ============================================================
CREATE TABLE usuario (
    id_usuario  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username    VARCHAR(60)  NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,
    rol         VARCHAR(30)  NOT NULL DEFAULT 'viewer',
    estado      TINYINT(1)   NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLA: sesion
-- Columnas exactas que usa SesionDAOImpl: fecha_inicio, fecha_fin, id_usuario
-- ============================================================
CREATE TABLE sesion (
    id_sesion    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    fecha_inicio DATETIME NOT NULL,
    fecha_fin    DATETIME NULL,
    id_usuario   INT UNSIGNED NOT NULL,
    CONSTRAINT fk_sesion_usuario FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLA: log
-- Columnas exactas que usa LogDAOImpl: accion, fecha, id_usuario
-- ============================================================
CREATE TABLE log (
    id_log      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    accion      VARCHAR(255) NOT NULL,
    fecha       DATETIME     NOT NULL,
    id_usuario  INT UNSIGNED NULL,
    CONSTRAINT fk_log_usuario FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLA: luchadores
-- ============================================================
CREATE TABLE luchadores (
    id_luchador     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre          VARCHAR(100) NOT NULL,
    apellido        VARCHAR(100),
    peso            DECIMAL(5,2),
    altura          DECIMAL(4,2),
    edad            TINYINT UNSIGNED,
    categoria       VARCHAR(60),
    nivel           VARCHAR(60),
    nacionalidad    VARCHAR(80),
    foto            VARCHAR(255),
    estado          ENUM('activo','inactivo') NOT NULL DEFAULT 'activo',
    victorias       SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    derrotas        SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    empates         SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    creado_en       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLA: torneos
-- Módulo 4 – Torneo: gestión completa de torneos
-- ============================================================
CREATE TABLE torneos (
    id_torneo              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre                 VARCHAR(150) NOT NULL,
    descripcion            TEXT,
    fecha_inicio           DATE,
    fecha_fin              DATE,
    estado                 ENUM('proximo','activo','finalizado','cancelado') NOT NULL DEFAULT 'proximo',
    ubicacion              VARCHAR(200),
    tipo                   VARCHAR(80),
    tiempo_limite_minutos  TINYINT UNSIGNED NOT NULL DEFAULT 3,
    premio                 VARCHAR(255),
    logo                   VARCHAR(255),
    capacidad_maxima       SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    reglas                 TEXT,
    id_campeon             INT UNSIGNED NULL,
    creado_en              DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_torneo_campeon FOREIGN KEY (id_campeon) REFERENCES luchadores(id_luchador) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLA: combates
-- ============================================================
CREATE TABLE combates (
    id_combate         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_torneo          INT UNSIGNED NULL,
    id_luchador_1      INT UNSIGNED NULL,
    id_luchador_2      INT UNSIGNED NULL,
    ganador_id         INT UNSIGNED NULL,
    fecha_combate      DATE,
    estado             ENUM('pendiente','en_curso','finalizado','cancelado') NOT NULL DEFAULT 'pendiente',
    arena              VARCHAR(100),
    ronda              VARCHAR(60),
    duracion_segundos  SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    puntos_luchador_1  SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    puntos_luchador_2  SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    observaciones      TEXT,
    creado_en          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_combate_torneo    FOREIGN KEY (id_torneo)     REFERENCES torneos(id_torneo)      ON DELETE SET NULL,
    CONSTRAINT fk_combate_luchador1 FOREIGN KEY (id_luchador_1) REFERENCES luchadores(id_luchador) ON DELETE SET NULL,
    CONSTRAINT fk_combate_luchador2 FOREIGN KEY (id_luchador_2) REFERENCES luchadores(id_luchador) ON DELETE SET NULL,
    CONSTRAINT fk_combate_ganador   FOREIGN KEY (ganador_id)    REFERENCES luchadores(id_luchador) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLA: staff_torneo
-- Módulo 3 – Personal
-- ============================================================
CREATE TABLE staff_torneo (
    id_staff    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(100) NOT NULL,
    apellido    VARCHAR(100),
    documento   VARCHAR(30),
    telefono    VARCHAR(20),
    email       VARCHAR(120),
    foto        VARCHAR(255),
    id_rol      INT UNSIGNED NULL,
    id_zona     INT UNSIGNED NULL,
    estado      ENUM('activo','inactivo') NOT NULL DEFAULT 'activo',
    creado_en   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_staff_rol  FOREIGN KEY (id_rol)  REFERENCES roles(id_rol)  ON DELETE SET NULL,
    CONSTRAINT fk_staff_zona FOREIGN KEY (id_zona) REFERENCES zonas(id_zona) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLA: staff_turnos
-- Módulo 3 – Personal: asignación de turnos al personal
-- ============================================================
CREATE TABLE staff_turnos (
    id_turno    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_staff    INT UNSIGNED NOT NULL,
    id_torneo   INT UNSIGNED NULL,
    fecha       DATE NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin    TIME NOT NULL,
    descripcion VARCHAR(255),
    creado_en   DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_turno_staff  FOREIGN KEY (id_staff)  REFERENCES staff_torneo(id_staff) ON DELETE CASCADE,
    CONSTRAINT fk_turno_torneo FOREIGN KEY (id_torneo) REFERENCES torneos(id_torneo)      ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLA: torneo_participantes
-- ============================================================
CREATE TABLE torneo_participantes (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_torneo   INT UNSIGNED NOT NULL,
    id_luchador INT UNSIGNED NOT NULL,
    inscrito_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_torneo_luchador (id_torneo, id_luchador),
    CONSTRAINT fk_part_torneo   FOREIGN KEY (id_torneo)   REFERENCES torneos(id_torneo)      ON DELETE CASCADE,
    CONSTRAINT fk_part_luchador FOREIGN KEY (id_luchador) REFERENCES luchadores(id_luchador) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- TABLA: arenas
-- ============================================================
CREATE TABLE arenas (
    id_arena    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_torneo   INT UNSIGNED NULL,
    nombre      VARCHAR(100) NOT NULL,
    capacidad   SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    descripcion VARCHAR(255),
    CONSTRAINT fk_arena_torneo FOREIGN KEY (id_torneo) REFERENCES torneos(id_torneo) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- DATOS INICIALES
-- ============================================================

-- Roles de personal
INSERT INTO roles (nombre, descripcion) VALUES
    ('Árbitro',        'Juzga y dirige los combates'),
    ('Entrenador',     'Acompaña y asesora al luchador'),
    ('Médico',         'Atención médica en el evento'),
    ('Seguridad',      'Control de acceso y orden'),
    ('Administrador',  'Gestión general del torneo'),
    ('Registrador',    'Registro e inscripción de participantes');

-- Zonas del evento
INSERT INTO zonas (nombre, descripcion) VALUES
    ('Zona A – Tatami Principal', 'Arena central para combates de final y semifinal'),
    ('Zona B – Tatami Norte',     'Arena secundaria para fases clasificatorias'),
    ('Zona C – Tatami Sur',       'Arena de calentamiento y combates menores'),
    ('Zona D – Recepción',        'Control de acceso y acreditaciones'),
    ('Zona E – Médica',           'Área de atención médica'),
    ('Zona F – VIP',              'Palco y área de autoridades');

-- Usuario administrador (contraseña placeholder — ejecutar reset_admin.php después)
-- El hash real se genera con: password_hash('admin123', PASSWORD_BCRYPT)
INSERT INTO usuario (username, password, rol, estado) VALUES
    ('admin', 'PLACEHOLDER_RUN_RESET_ADMIN_PHP', 'admin', 1);

-- Luchadores de ejemplo
INSERT INTO luchadores (nombre, apellido, peso, altura, edad, categoria, nivel, nacionalidad, estado, victorias, derrotas) VALUES
    ('Goku',       'Son',    70.00, 1.75, 28, 'Peso Medio',  'Élite',      'Japonesa',    'activo', 45, 2),
    ('Vegeta',     'Briefs', 75.00, 1.70, 30, 'Peso Medio',  'Élite',      'Saiyajin',    'activo', 38, 7),
    ('Gohan',      'Son',    68.00, 1.76, 18, 'Peso Medio',  'Avanzado',   'Japonesa',    'activo', 20, 3),
    ('Piccolo',    NULL,     90.00, 2.26, 25, 'Peso Pesado', 'Élite',      'Namekiana',   'activo', 30, 5),
    ('Krillin',    NULL,     60.00, 1.53, 27, 'Peso Ligero', 'Avanzado',   'Japonesa',    'activo', 22, 8),
    ('Trunks',     'Briefs', 65.00, 1.70, 17, 'Peso Ligero', 'Avanzado',   'Saiyajin',    'activo', 15, 2),
    ('Goten',      'Son',    55.00, 1.57, 14, 'Peso Ligero', 'Intermedio', 'Japonesa',    'activo', 10, 1),
    ('Yamcha',     NULL,     72.00, 1.83, 29, 'Peso Medio',  'Intermedio', 'Japonesa',    'activo', 12, 14),
    ('Tenshinhan', NULL,     80.00, 1.87, 31, 'Peso Pesado', 'Avanzado',   'Japonesa',    'activo', 28, 6),
    ('Freezer',    NULL,    110.00, 1.58, 50, 'Peso Pesado', 'Élite',      'Frieza Race', 'activo', 1000, 1);

-- Torneos de ejemplo
INSERT INTO torneos (nombre, descripcion, fecha_inicio, fecha_fin, estado, ubicacion, tipo, tiempo_limite_minutos, premio, capacidad_maxima, reglas) VALUES
    ('Torneo del Poder',
     'El gran torneo interestelar donde solo el más fuerte sobrevive.',
     '2026-06-01', '2026-06-03',
     'proximo',
     'Estadio Mundial, Ciudad del Lago',
     'Absoluto',
     5,
     'Supervivencia de la Tierra',
     80,
     'Prohibido matar. Quien caiga del ring pierde. Sin armas.'),
    ('Budokai 28',
     'Torneo mundial de artes marciales, vigésima octava edición.',
     '2026-07-15', '2026-07-17',
     'proximo',
     'Templo del Tortuga Ermitaño, Papaya Island',
     'Peso Medio',
     3,
     'Cinturón de campeón mundial + 500,000 zeni',
     64,
     'Reglas estándar de artes marciales. Ki permitido. Sin armas.');

-- Participantes
INSERT INTO torneo_participantes (id_torneo, id_luchador) VALUES
    (1,1),(1,2),(1,3),(1,4),(1,5),(1,6),(1,7),(1,8),(1,9),(1,10),
    (2,1),(2,2),(2,3),(2,5),(2,6),(2,7),(2,8),(2,9);

-- Arenas
INSERT INTO arenas (id_torneo, nombre, capacidad) VALUES
    (1, 'Plataforma Principal',  0),
    (1, 'Plataforma Norte',      0),
    (2, 'Ring Central',          0),
    (2, 'Ring Auxiliar',         0);

-- Combates de ejemplo (Budokai 28 — cuartos y final pendiente)
INSERT INTO combates (id_torneo, id_luchador_1, id_luchador_2, ganador_id, fecha_combate, estado, arena, ronda, duracion_segundos, puntos_luchador_1, puntos_luchador_2) VALUES
    (2, 1, 5, 1, '2026-07-15', 'finalizado', 'Ring Central',  'Cuartos de final', 180, 3, 1),
    (2, 2, 8, 2, '2026-07-15', 'finalizado', 'Ring Auxiliar', 'Cuartos de final', 150, 3, 0),
    (2, 3, 9, 3, '2026-07-15', 'finalizado', 'Ring Central',  'Cuartos de final', 210, 2, 1),
    (2, 4, 6, 4, '2026-07-15', 'finalizado', 'Ring Auxiliar', 'Cuartos de final', 120, 3, 0),
    (2, 1, 3, 1, '2026-07-16', 'finalizado', 'Ring Central',  'Semifinal',        240, 3, 2),
    (2, 2, 4, 2, '2026-07-16', 'finalizado', 'Ring Central',  'Semifinal',        195, 3, 1),
    (2, 1, 2, NULL, '2026-07-17', 'pendiente', 'Ring Central', 'Final',           0,   0, 0);

-- Staff de ejemplo
INSERT INTO staff_torneo (nombre, apellido, documento, telefono, email, id_rol, id_zona, estado) VALUES
    ('Maestro',  'Roshi',   '12345678', '+1-555-0001', 'roshi@kame.house',   1, 1, 'activo'),
    ('Dende',    NULL,      '87654321', '+1-555-0002', 'dende@namek.world',  3, 5, 'activo'),
    ('Bulma',    'Briefs',  '11223344', '+1-555-0003', 'bulma@capsule.corp', 5, 4, 'activo'),
    ('Chichi',   'Son',     '44332211', '+1-555-0004', 'chichi@mt.paozu',    6, 4, 'activo'),
    ('Oob',      NULL,      '55667788', '+1-555-0005', 'oob@papaya.island',  1, 2, 'activo');

-- Turnos de ejemplo
INSERT INTO staff_turnos (id_staff, id_torneo, fecha, hora_inicio, hora_fin, descripcion) VALUES
    (1, 2, '2026-07-15', '08:00:00', '14:00:00', 'Arbitraje – cuartos de final'),
    (2, 2, '2026-07-15', '07:00:00', '20:00:00', 'Servicio médico todo el día'),
    (3, 2, '2026-07-15', '07:30:00', '09:00:00', 'Acreditaciones y registro'),
    (4, 2, '2026-07-15', '07:00:00', '20:00:00', 'Control de acceso – Zona D'),
    (1, 2, '2026-07-17', '10:00:00', '18:00:00', 'Arbitraje – final');

-- Log de acceso inicial
INSERT INTO log (accion, fecha, id_usuario) VALUES
    ('Inicio de sesión: admin', NOW(), 1);

-- ============================================================
-- ÍNDICES adicionales
-- ============================================================
ALTER TABLE combates     ADD INDEX idx_combates_torneo   (id_torneo);
ALTER TABLE combates     ADD INDEX idx_combates_fecha    (fecha_combate);
ALTER TABLE torneos      ADD INDEX idx_torneos_estado    (estado);
ALTER TABLE luchadores   ADD INDEX idx_luchadores_estado (estado);
ALTER TABLE staff_torneo ADD INDEX idx_staff_estado      (estado);
ALTER TABLE log          ADD INDEX idx_log_usuario       (id_usuario);
ALTER TABLE log          ADD INDEX idx_log_fecha         (fecha);
ALTER TABLE sesion       ADD INDEX idx_sesion_usuario    (id_usuario);
ALTER TABLE sesion       ADD INDEX idx_sesion_fecha      (fecha_inicio);

-- ============================================================
-- FIN DEL SCRIPT
-- ============================================================
SELECT 'Base de datos torneo_new creada correctamente.' AS resultado;
