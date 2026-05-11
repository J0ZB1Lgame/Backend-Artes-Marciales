-- CREAR TABLAS FALTANTES PARA BACKEND 100% FUNCIONAL
-- Ejecutar en phpMyAdmin o consola MySQL
-- Base de datos: torneo_new

USE torneo_new;

-- --------------------------------------------------------
-- TABLA COMBATES
-- Referenciada por: CombateDAOImpl.php
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `combates` (
  `id_combate` int(11) NOT NULL AUTO_INCREMENT,
  `id_torneo` int(11) DEFAULT NULL,
  `id_luchador_1` int(11) DEFAULT NULL,
  `id_luchador_2` int(11) DEFAULT NULL,
  `ganador_id` int(11) DEFAULT NULL,
  `estado` varchar(20) DEFAULT 'pendiente',
  `ronda` varchar(50) DEFAULT NULL,
  `fecha_combate` datetime DEFAULT NULL,
  `arena` varchar(100) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `duracion_segundos` int(11) DEFAULT NULL,
  `puntos_luchador_1` int(11) DEFAULT 0,
  `puntos_luchador_2` int(11) DEFAULT 0,
  PRIMARY KEY (`id_combate`),
  KEY `id_torneo` (`id_torneo`),
  KEY `id_luchador_1` (`id_luchador_1`),
  KEY `id_luchador_2` (`id_luchador_2`),
  KEY `ganador_id` (`ganador_id`),
  KEY `estado` (`estado`),
  KEY `fecha_combate` (`fecha_combate`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- TABLA TORNEOS
-- Referenciada por: TorneoDAOImpl.php
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `torneos` (
  `id_torneo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_inicio` datetime DEFAULT NULL,
  `fecha_fin` datetime DEFAULT NULL,
  `estado` varchar(20) DEFAULT 'activo',
  `ubicacion` varchar(255) DEFAULT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `premio` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `capacidad_maxima` int(11) DEFAULT NULL,
  `reglas` text DEFAULT NULL,
  PRIMARY KEY (`id_torneo`),
  KEY `estado` (`estado`),
  KEY `fecha_inicio` (`fecha_inicio`),
  KEY `tipo` (`tipo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- TABLA TORNEO_PARTICIPANTES
-- Referenciada por: TorneoDAOImpl.php (método getParticipantes)
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `torneo_participantes` (
  `id_participante` int(11) NOT NULL AUTO_INCREMENT,
  `id_torneo` int(11) NOT NULL,
  `id_luchador` int(11) NOT NULL,
  `fecha_inscripcion` datetime DEFAULT CURRENT_TIMESTAMP,
  `estado` varchar(20) DEFAULT 'inscrito',
  `observaciones` text DEFAULT NULL,
  PRIMARY KEY (`id_participante`),
  KEY `id_torneo` (`id_torneo`),
  KEY `id_luchador` (`id_luchador`),
  KEY `estado` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- TABLA ARENAS
-- Referenciada por: TorneoDAOImpl.php (método getArenas)
-- --------------------------------------------------------

CREATE TABLE IF NOT EXISTS `arenas` (
  `id_arena` int(11) NOT NULL AUTO_INCREMENT,
  `id_torneo` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `capacidad` int(11) DEFAULT NULL,
  `ubicacion` varchar(255) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `estado` varchar(20) DEFAULT 'disponible',
  PRIMARY KEY (`id_arena`),
  KEY `id_torneo` (`id_torneo`),
  KEY `estado` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- AGREGAR FOREIGN KEYS (opcional, para integridad referencial)
-- --------------------------------------------------------

-- FK para torneo_participantes
ALTER TABLE `torneo_participantes` 
ADD CONSTRAINT `fk_torneo_participantes_torneo` 
FOREIGN KEY (`id_torneo`) REFERENCES `torneos` (`id_torneo`) 
ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `torneo_participantes` 
ADD CONSTRAINT `fk_torneo_participantes_luchador` 
FOREIGN KEY (`id_luchador`) REFERENCES `luchadores` (`id_luchador`) 
ON DELETE CASCADE ON UPDATE CASCADE;

-- FK para arenas
ALTER TABLE `arenas` 
ADD CONSTRAINT `fk_arenas_torneo` 
FOREIGN KEY (`id_torneo`) REFERENCES `torneos` (`id_torneo`) 
ON DELETE CASCADE ON UPDATE CASCADE;

-- FK para combates
ALTER TABLE `combates` 
ADD CONSTRAINT `fk_combates_torneo` 
FOREIGN KEY (`id_torneo`) REFERENCES `torneos` (`id_torneo`) 
ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `combates` 
ADD CONSTRAINT `fk_combates_luchador1` 
FOREIGN KEY (`id_luchador_1`) REFERENCES `luchadores` (`id_luchador`) 
ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `combates` 
ADD CONSTRAINT `fk_combates_luchador2` 
FOREIGN KEY (`id_luchador_2`) REFERENCES `luchadores` (`id_luchador`) 
ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `combates` 
ADD CONSTRAINT `fk_combates_ganador` 
FOREIGN KEY (`ganador_id`) REFERENCES `luchadores` (`id_luchador`) 
ON DELETE SET NULL ON UPDATE CASCADE;

-- --------------------------------------------------------
-- DATOS DE EJEMPLO (opcional)
-- --------------------------------------------------------

-- Insertar algunos torneos de ejemplo
INSERT INTO `torneos` (`nombre`, `descripcion`, `fecha_inicio`, `fecha_fin`, `estado`, `ubicacion`, `tipo`) VALUES
('Torneo Budokai 2026', 'Torneo anual de artes marciales', '2026-06-01 09:00:00', '2026-06-03 18:00:00', 'activo', 'Dojo Central', 'Eliminación Directa'),
('Torneo por Equipos', 'Competencia por equipos de 3 integrantes', '2026-07-15 10:00:00', '2026-07-16 20:00:00', 'pendiente', 'Gimnasio Municipal', 'Por Equipos');

-- Insertar algunas arenas de ejemplo
INSERT INTO `arenas` (`id_torneo`, `nombre`, `capacidad`, `ubicacion`, `descripcion`) VALUES
(1, 'Arena Principal', 500, 'Piso Principal', 'Arena principal con capacidad para 500 espectadores'),
(1, 'Arena Secundaria', 200, 'Piso Secundario', 'Arena secundaria para combates preliminares'),
(2, 'Arena Central', 1000, 'Gimnasio Municipal', 'Arena grande para torneo por equipos');

-- Insertar algunos combates de ejemplo
INSERT INTO `combates` (`id_torneo`, `id_luchador_1`, `id_luchador_2`, `ganador_id`, `estado`, `ronda`, `fecha_combate`, `arena`, `duracion_segundos`, `puntos_luchador_1`, `puntos_luchador_2`) VALUES
(1, 1, 2, 1, 'finalizado', 'Final', '2026-06-03 16:00:00', 'Arena Principal', 300, 15, 12),
(1, 3, 4, 3, 'finalizado', 'Semifinal 1', '2026-06-03 14:00:00', 'Arena Principal', 240, 8, 14);

-- --------------------------------------------------------
-- VERIFICACIÓN FINAL
-- --------------------------------------------------------

SHOW TABLES LIKE 'combates';
SHOW TABLES LIKE 'torneos';
SHOW TABLES LIKE 'torneo_participantes';
SHOW TABLES LIKE 'arenas';

SELECT 'Tablas creadas exitosamente' AS mensaje;
