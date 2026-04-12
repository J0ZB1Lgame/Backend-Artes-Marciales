CREATE DATABASE IF NOT EXISTS torneo_db;
USE torneo_db;

-- Módulo Seguridad / Inicio de Sesión

CREATE TABLE IF NOT EXISTS usuario (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol VARCHAR(50) DEFAULT 'staff',
    estado BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS sesion (
    id_sesion INT AUTO_INCREMENT PRIMARY KEY,
    fecha_inicio DATETIME NOT NULL,
    fecha_fin DATETIME,
    id_usuario_activo INT,
    FOREIGN KEY (id_usuario_activo) REFERENCES usuario(id_usuario) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS log (
    id_log INT AUTO_INCREMENT PRIMARY KEY,
    accion VARCHAR(255) NOT NULL,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP,
    id_usuario INT,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Módulo Gestión del Staff

CREATE TABLE IF NOT EXISTS rol (
    id_rol INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion VARCHAR(255)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS zona (
    id_zona INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion VARCHAR(255)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS staff (
    id_staff INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NULL,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    tipo_documento VARCHAR(20),
    numero_documento VARCHAR(50) UNIQUE,
    telefono VARCHAR(20),
    email VARCHAR(100),
    estado ENUM('activo','inactivo') DEFAULT 'activo',
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS staff_torneo (
    id_staff_torneo INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    id_staff INT,
    FOREIGN KEY (id_staff) REFERENCES staff(id_staff) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Módulo Luchador

CREATE TABLE IF NOT EXISTS luchador (
    id_luchador INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    especie VARCHAR(100),
    nivel_poder_ki DOUBLE DEFAULT 0.0,
    origen VARCHAR(100),
    estado BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB;

-- Datos semilla

INSERT INTO rol (nombre, descripcion) VALUES
('Administrador', 'Control total del sistema'),
('Coordinador', 'Organiza la logística del torneo'),
('Árbitro', 'Juzga los combates'),
('Médico', 'Atiende urgencias en el ring'),
('Seguridad', 'Supervisa la seguridad del evento'),
('Técnico', 'Asiste con aspectos técnicos');

INSERT INTO zona (nombre, descripcion) VALUES
('Zona A', 'Área principal de combates'),
('Zona B', 'Área secundaria de combates'),
('Zona Médica', 'Área de atención médica'),
('Zona VIP', 'Área para autoridades del torneo');

-- NOTA: El usuario admin se genera ejecutando backend/setup_admin.php una sola vez.
-- No insertar passwords en texto plano en este archivo.
