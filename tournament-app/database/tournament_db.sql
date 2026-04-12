CREATE DATABASE IF NOT EXISTS torneo_db;
USE torneo_db;

-- =========================
-- TABLA: usuario
-- =========================
CREATE TABLE usuario (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =========================
-- TABLA: rol (del sistema)
-- =========================
CREATE TABLE rol (
    id_rol INT AUTO_INCREMENT PRIMARY KEY,
    nombre_rol VARCHAR(50) NOT NULL UNIQUE,
    descripcion VARCHAR(255)
) ENGINE=InnoDB;

-- =========================
-- TABLA: usuario_rol
-- =========================
CREATE TABLE usuario_rol (
    id_usuario INT,
    id_rol INT,
    PRIMARY KEY (id_usuario, id_rol),
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (id_rol) REFERENCES rol(id_rol)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- =========================
-- TABLA: staff
-- =========================
CREATE TABLE staff (
    id_staff INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NULL,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    tipo_documento VARCHAR(20),
    numero_documento VARCHAR(50) UNIQUE,
    telefono VARCHAR(20),
    email VARCHAR(100),
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- =========================
-- TABLA: tipo_rol_staff
-- =========================
CREATE TABLE tipo_rol_staff (
    id_tipo_rol INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion VARCHAR(255)
) ENGINE=InnoDB;

-- =========================
-- TABLA: staff_rol
-- =========================
CREATE TABLE staff_rol (
    id_staff INT,
    id_tipo_rol INT,
    fecha_asignacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_staff, id_tipo_rol),
    FOREIGN KEY (id_staff) REFERENCES staff(id_staff)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (id_tipo_rol) REFERENCES tipo_rol_staff(id_tipo_rol)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- =========================
-- TABLA: turno
-- =========================
CREATE TABLE turno (
    id_turno INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    hora_inicio TIME NOT NULL,
    hora_fin TIME NOT NULL
) ENGINE=InnoDB;

-- =========================
-- TABLA: asignacion_turno
-- =========================
CREATE TABLE asignacion_turno (
    id_asignacion INT AUTO_INCREMENT PRIMARY KEY,
    id_staff INT,
    id_turno INT,
    fecha DATE NOT NULL,
    estado ENUM('activo', 'inactivo') DEFAULT 'activo',
    FOREIGN KEY (id_staff) REFERENCES staff(id_staff)
        ON DELETE CASCADE
        ON UPDATE CASCADE,
    FOREIGN KEY (id_turno) REFERENCES turno(id_turno)
        ON DELETE CASCADE
        ON UPDATE CASCADE
) ENGINE=InnoDB;

-- =========================
-- DATOS SEMILLA: tipos de rol de staff
-- =========================
INSERT INTO tipo_rol_staff (nombre, descripcion) VALUES
('Árbitro', 'Encargado de juzgar combates'),
('Médico de Ringside', 'Atiende urgencias en el ring'),
('Coordinador de Torneo', 'Organiza la logística del torneo'),
('Juez', 'Evalúa puntuaciones'),
('Seguridad', 'Supervisa la seguridad del evento'),
('Técnico', 'Asiste con aspectos técnicos');

-- =========================
-- DATOS SEMILLA: turnos
-- =========================
INSERT INTO turno (nombre, hora_inicio, hora_fin) VALUES
('Mañana', '06:00:00', '14:00:00'),
('Tarde', '14:00:00', '22:00:00'),
('Noche', '22:00:00', '06:00:00');
