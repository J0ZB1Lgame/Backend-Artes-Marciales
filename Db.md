-- ─────────────────────────────────────────────────────────────────────────────
-- TAM DB - Script actualizado según diagrama de clases final
-- Motor: MySQL | Charset: utf8mb4
-- ─────────────────────────────────────────────────────────────────────────────

CREATE DATABASE IF NOT EXISTS tam_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE tam_db;

-- ─── Usuario ──────────────────────────────────────────────────────────────────
-- rol es string directo según el diagrama final
CREATE TABLE IF NOT EXISTS usuario (
    idUsuario  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username   VARCHAR(80)  NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    rol        VARCHAR(60)  NOT NULL DEFAULT 'staff',
    estado     BOOLEAN      NOT NULL DEFAULT TRUE
) ENGINE=InnoDB;

-- ─── Sesion ───────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS sesion (
    idSesion       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    idUsuario      INT UNSIGNED  NOT NULL,
    token          VARCHAR(64)   NOT NULL UNIQUE,
    fechaInicio    DATETIME      NOT NULL,
    fechaFin       DATETIME,
    usuarioActivo  BOOLEAN       NOT NULL DEFAULT TRUE,
    FOREIGN KEY (idUsuario) REFERENCES usuario(idUsuario) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ─── Log ─────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS log (
    idLog      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    accion     VARCHAR(255) NOT NULL,
    fecha      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    idUsuario  INT UNSIGNED,
    FOREIGN KEY (idUsuario) REFERENCES usuario(idUsuario) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ─── Staff (abstracta - base para StaffTorneo, StaffCombates, Juez) ──────────
CREATE TABLE IF NOT EXISTS staff (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    idUsuario  INT UNSIGNED NOT NULL,
    nombre     VARCHAR(120) NOT NULL,
    turno      VARCHAR(60)  NOT NULL,
    estado     BOOLEAN      NOT NULL DEFAULT TRUE,
    tipo       ENUM('torneo','combate','juez') NOT NULL,
    FOREIGN KEY (idUsuario) REFERENCES usuario(idUsuario) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ─── StaffTorneo (extiende Staff) ────────────────────────────────────────────
-- zona: String según diagrama final (antes era cargo)
CREATE TABLE IF NOT EXISTS staff_torneo (
    id    INT UNSIGNED PRIMARY KEY,
    zona  VARCHAR(100) NOT NULL,
    FOREIGN KEY (id) REFERENCES staff(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ─── StaffCombates (extiende Staff) ──────────────────────────────────────────
CREATE TABLE IF NOT EXISTS staff_combate (
    id         INT UNSIGNED PRIMARY KEY,
    idCombate  INT UNSIGNED,
    estado     VARCHAR(60) NOT NULL DEFAULT 'activo',
    FOREIGN KEY (id) REFERENCES staff(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ─── Juez (extiende Staff) ────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS juez (
    id  INT UNSIGNED PRIMARY KEY,
    FOREIGN KEY (id) REFERENCES staff(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ─── Alerta ───────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS alerta (
    idAlerta     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    fechaAlerta  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    descripcion  VARCHAR(255) NOT NULL,
    tipo         VARCHAR(60)  NOT NULL,
    estado       BOOLEAN      NOT NULL DEFAULT TRUE
) ENGINE=InnoDB;

-- ─── Arena ────────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS arena (
    idArena    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre     VARCHAR(120) NOT NULL,
    capacidad  INT          NOT NULL,
    estado     BOOLEAN      NOT NULL DEFAULT TRUE
) ENGINE=InnoDB;

-- ─── BarreraEnergetica ────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS barrera_energetica (
    idBarrera        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    capacidadActual  DOUBLE       NOT NULL DEFAULT 0,
    capacidadMaxima  DOUBLE       NOT NULL,
    estado           BOOLEAN      NOT NULL DEFAULT TRUE,
    idArena          INT UNSIGNED,
    FOREIGN KEY (idArena) REFERENCES arena(idArena) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ─── Luchador ─────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS luchador (
    idLuchador      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre          VARCHAR(120) NOT NULL,
    especie         VARCHAR(80)  NOT NULL,
    nivelDePoderKi  DOUBLE       NOT NULL DEFAULT 0,
    origen          VARCHAR(100),
    estado          BOOLEAN      NOT NULL DEFAULT TRUE
) ENGINE=InnoDB;

-- ─── EvaluacionMedica ─────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS evaluacion_medica (
    idEvaluacionMedica  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    frecuenciaCardiaca  DOUBLE  NOT NULL,
    presionArterial     VARCHAR(20) NOT NULL,
    kiRegistrado        DOUBLE  NOT NULL,
    estadoApto          BOOLEAN NOT NULL DEFAULT FALSE,
    idLuchador          INT UNSIGNED NOT NULL,
    FOREIGN KEY (idLuchador) REFERENCES luchador(idLuchador) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ─── TecnicaEspecial ──────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS tecnica_especial (
    idTecnicaEspecial  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tecnica            VARCHAR(120) NOT NULL,
    detalle            TEXT
) ENGINE=InnoDB;

-- ─── Luchador - TecnicaEspecial (relacion N:M) ────────────────────────────────
CREATE TABLE IF NOT EXISTS luchador_tecnica (
    idLuchador         INT UNSIGNED NOT NULL,
    idTecnicaEspecial  INT UNSIGNED NOT NULL,
    PRIMARY KEY (idLuchador, idTecnicaEspecial),
    FOREIGN KEY (idLuchador)        REFERENCES luchador(idLuchador)              ON DELETE CASCADE,
    FOREIGN KEY (idTecnicaEspecial) REFERENCES tecnica_especial(idTecnicaEspecial) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ─── DocumentoLegal ───────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS documento_legal (
    idDocumen        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tipo             VARCHAR(80)  NOT NULL,
    estado           BOOLEAN      NOT NULL DEFAULT TRUE,
    fechaVencimiento DATE,
    idLuchador       INT UNSIGNED NOT NULL,
    FOREIGN KEY (idLuchador) REFERENCES luchador(idLuchador) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ─── Combate ──────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS combate (
    idCombate    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    fechaCombate VARCHAR(20)  NOT NULL,
    horaCombate  VARCHAR(10)  NOT NULL,
    estado       BOOLEAN      NOT NULL DEFAULT TRUE,
    idArena      INT UNSIGNED,
    idLuchador1  INT UNSIGNED,
    idLuchador2  INT UNSIGNED,
    FOREIGN KEY (idArena)     REFERENCES arena(idArena)       ON DELETE SET NULL,
    FOREIGN KEY (idLuchador1) REFERENCES luchador(idLuchador) ON DELETE SET NULL,
    FOREIGN KEY (idLuchador2) REFERENCES luchador(idLuchador) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ─── ResultadoCombate ─────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS resultado_combate (
    idResultadoCombate  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    duracion            INT,
    observaciones       TEXT,
    idGanador           INT UNSIGNED,
    idTecnicaFinal      INT UNSIGNED,
    idCombate           INT UNSIGNED NOT NULL,
    FOREIGN KEY (idGanador)      REFERENCES luchador(idLuchador)              ON DELETE SET NULL,
    FOREIGN KEY (idTecnicaFinal) REFERENCES tecnica_especial(idTecnicaEspecial) ON DELETE SET NULL,
    FOREIGN KEY (idCombate)      REFERENCES combate(idCombate)                ON DELETE CASCADE
) ENGINE=InnoDB;

-- ─── HistorialCombate ─────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS historial_combate (
    idHistorialCombates  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    observaciones        TEXT,
    idLuchador           INT UNSIGNED NOT NULL,
    FOREIGN KEY (idLuchador) REFERENCES luchador(idLuchador) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ─── Historial - Combate (relacion N:M) ───────────────────────────────────────
CREATE TABLE IF NOT EXISTS historial_combate_detalle (
    idHistorialCombates  INT UNSIGNED NOT NULL,
    idCombate            INT UNSIGNED NOT NULL,
    PRIMARY KEY (idHistorialCombates, idCombate),
    FOREIGN KEY (idHistorialCombates) REFERENCES historial_combate(idHistorialCombates) ON DELETE CASCADE,
    FOREIGN KEY (idCombate)           REFERENCES combate(idCombate)                     ON DELETE CASCADE
) ENGINE=InnoDB;

-- ─── Torneo ───────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS torneo (
    idTorneo  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre    VARCHAR(120) NOT NULL,
    estado    BOOLEAN      NOT NULL DEFAULT TRUE
) ENGINE=InnoDB;

-- ─── ReglasTorneo ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS reglas_torneo (
    idRegla      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tiempoLimite INT          NOT NULL,
    contenido    TEXT,
    modalidad    VARCHAR(80),
    idTorneo     INT UNSIGNED,
    FOREIGN KEY (idTorneo) REFERENCES torneo(idTorneo) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ─── Bracket ──────────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS bracket (
    idBracket  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    rondas     INT          NOT NULL,
    idTorneo   INT UNSIGNED,
    FOREIGN KEY (idTorneo) REFERENCES torneo(idTorneo) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ─── Bracket - Combate ────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS bracket_combate (
    idBracket  INT UNSIGNED NOT NULL,
    idCombate  INT UNSIGNED NOT NULL,
    PRIMARY KEY (idBracket, idCombate),
    FOREIGN KEY (idBracket) REFERENCES bracket(idBracket)  ON DELETE CASCADE,
    FOREIGN KEY (idCombate) REFERENCES combate(idCombate)  ON DELETE CASCADE
) ENGINE=InnoDB;

-- ─── ActivoTecnologico ────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS activo_tecnologico (
    idActivo      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre        VARCHAR(120) NOT NULL,
    descripcion   TEXT,
    tipo          VARCHAR(80)  NOT NULL,
    ubicacion     VARCHAR(120),
    fechaRegistro DATE,
    estado        BOOLEAN      NOT NULL DEFAULT TRUE,
    idTorneo      INT UNSIGNED,
    FOREIGN KEY (idTorneo) REFERENCES torneo(idTorneo) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ─── Torneo - Luchador ────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS torneo_luchador (
    idTorneo   INT UNSIGNED NOT NULL,
    idLuchador INT UNSIGNED NOT NULL,
    PRIMARY KEY (idTorneo, idLuchador),
    FOREIGN KEY (idTorneo)   REFERENCES torneo(idTorneo)     ON DELETE CASCADE,
    FOREIGN KEY (idLuchador) REFERENCES luchador(idLuchador) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ─── Torneo - Staff ───────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS torneo_staff (
    idTorneo  INT UNSIGNED NOT NULL,
    idStaff   INT UNSIGNED NOT NULL,
    PRIMARY KEY (idTorneo, idStaff),
    FOREIGN KEY (idTorneo) REFERENCES torneo(idTorneo) ON DELETE CASCADE,
    FOREIGN KEY (idStaff)  REFERENCES staff(id)        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ─── FK diferida: staff_combate -> combate ────────────────────────────────────
ALTER TABLE staff_combate
    ADD CONSTRAINT fk_staff_combate_combate
    FOREIGN KEY (idCombate) REFERENCES combate(idCombate) ON DELETE SET NULL;

-- ─── Usuario admin por defecto ────────────────────────────────────────────────
-- Password: Admin123! (bcrypt). Cámbiala en producción.
INSERT INTO usuario (username, password, rol, estado) VALUES
    ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrador', TRUE);