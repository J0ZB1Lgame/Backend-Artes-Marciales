-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 11-05-2026 a las 07:35:00
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `torneo_new`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `arenas`
--

CREATE TABLE `arenas` (
  `id_arena` int(10) UNSIGNED NOT NULL,
  `id_torneo` int(10) UNSIGNED DEFAULT NULL,
  `nombre` varchar(100) NOT NULL,
  `capacidad` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `arenas`
--

INSERT INTO `arenas` (`id_arena`, `id_torneo`, `nombre`, `capacidad`, `descripcion`) VALUES
(1, 1, 'Plataforma Principal', 0, NULL),
(2, 1, 'Plataforma Norte', 0, NULL),
(3, 2, 'Ring Central', 0, NULL),
(4, 2, 'Ring Auxiliar', 0, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `combates`
--

CREATE TABLE `combates` (
  `id_combate` int(10) UNSIGNED NOT NULL,
  `id_torneo` int(10) UNSIGNED DEFAULT NULL,
  `id_luchador_1` int(10) UNSIGNED DEFAULT NULL,
  `id_luchador_2` int(10) UNSIGNED DEFAULT NULL,
  `ganador_id` int(10) UNSIGNED DEFAULT NULL,
  `fecha_combate` date DEFAULT NULL,
  `estado` enum('pendiente','en_curso','finalizado','cancelado') NOT NULL DEFAULT 'pendiente',
  `arena` varchar(100) DEFAULT NULL,
  `ronda` varchar(60) DEFAULT NULL,
  `duracion_segundos` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `puntos_luchador_1` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `puntos_luchador_2` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `observaciones` text DEFAULT NULL,
  `creado_en` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `combates`
--

INSERT INTO `combates` (`id_combate`, `id_torneo`, `id_luchador_1`, `id_luchador_2`, `ganador_id`, `fecha_combate`, `estado`, `arena`, `ronda`, `duracion_segundos`, `puntos_luchador_1`, `puntos_luchador_2`, `observaciones`, `creado_en`) VALUES
(1, 2, 1, 5, 1, '2026-07-15', 'finalizado', 'Ring Central', 'Cuartos de final', 180, 3, 1, NULL, '2026-05-10 23:31:27'),
(2, 2, 2, 8, 2, '2026-07-15', 'finalizado', 'Ring Auxiliar', 'Cuartos de final', 150, 3, 0, NULL, '2026-05-10 23:31:27'),
(3, 2, 3, 9, 3, '2026-07-15', 'finalizado', 'Ring Central', 'Cuartos de final', 210, 2, 1, NULL, '2026-05-10 23:31:27'),
(4, 2, 4, 6, 4, '2026-07-15', 'finalizado', 'Ring Auxiliar', 'Cuartos de final', 120, 3, 0, NULL, '2026-05-10 23:31:27'),
(5, 2, 1, 3, 1, '2026-07-16', 'finalizado', 'Ring Central', 'Semifinal', 240, 3, 2, NULL, '2026-05-10 23:31:27'),
(6, 2, 2, 4, 2, '2026-07-16', 'finalizado', 'Ring Central', 'Semifinal', 195, 3, 1, NULL, '2026-05-10 23:31:27'),
(7, 2, 1, 2, NULL, '2026-07-17', 'pendiente', 'Ring Central', 'Final', 0, 0, 0, NULL, '2026-05-10 23:31:27');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `log`
--

CREATE TABLE `log` (
  `id_log` int(10) UNSIGNED NOT NULL,
  `accion` varchar(255) NOT NULL,
  `fecha` datetime NOT NULL,
  `id_usuario` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `log`
--

INSERT INTO `log` (`id_log`, `accion`, `fecha`, `id_usuario`) VALUES
(1, 'Inicio de sesión: admin', '2026-05-10 23:31:27', 1),
(2, 'Inicio de sesión: admin', '2026-05-11 06:38:25', 1),
(3, 'Inicio de sesión: goku', '2026-05-11 06:39:41', 2),
(4, 'Inicio de sesión: admin', '2026-05-11 06:40:30', 1),
(5, 'Inicio de sesión: vegueta', '2026-05-11 06:41:06', 3),
(6, 'Inicio de sesión: admin', '2026-05-11 06:41:58', 1),
(7, 'Inicio de sesión: broly', '2026-05-11 06:42:28', 4),
(8, 'Inicio de sesión: broly', '2026-05-11 06:47:00', 4),
(9, 'Inicio de sesión: admin', '2026-05-11 06:47:33', 1),
(10, 'Inicio de sesión: admin', '2026-05-11 07:15:45', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `luchadores`
--

CREATE TABLE `luchadores` (
  `id_luchador` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) DEFAULT NULL,
  `peso` decimal(5,2) DEFAULT NULL,
  `altura` decimal(4,2) DEFAULT NULL,
  `edad` tinyint(3) UNSIGNED DEFAULT NULL,
  `categoria` varchar(60) DEFAULT NULL,
  `nivel` varchar(60) DEFAULT NULL,
  `nacionalidad` varchar(80) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `estado` enum('activo','inactivo') NOT NULL DEFAULT 'activo',
  `victorias` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `derrotas` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `empates` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `creado_en` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `luchadores`
--

INSERT INTO `luchadores` (`id_luchador`, `nombre`, `apellido`, `peso`, `altura`, `edad`, `categoria`, `nivel`, `nacionalidad`, `foto`, `estado`, `victorias`, `derrotas`, `empates`, `creado_en`) VALUES
(1, 'Goku', 'Son', 70.00, 1.75, 28, 'Peso Medio', 'Élite', 'Japonesa', NULL, 'activo', 45, 2, 0, '2026-05-10 23:31:27'),
(2, 'Vegeta', 'Briefs', 75.00, 1.70, 30, 'Peso Medio', 'Élite', 'Saiyajin', NULL, 'activo', 38, 7, 0, '2026-05-10 23:31:27'),
(3, 'Gohan', 'Son', 68.00, 1.76, 18, 'Peso Medio', 'Avanzado', 'Japonesa', NULL, 'activo', 20, 3, 0, '2026-05-10 23:31:27'),
(4, 'Piccolo', NULL, 90.00, 2.26, 25, 'Peso Pesado', 'Élite', 'Namekiana', NULL, 'activo', 30, 5, 0, '2026-05-10 23:31:27'),
(5, 'Krillin', NULL, 60.00, 1.53, 27, 'Peso Ligero', 'Avanzado', 'Japonesa', NULL, 'activo', 22, 8, 0, '2026-05-10 23:31:27'),
(6, 'Trunks', 'Briefs', 65.00, 1.70, 17, 'Peso Ligero', 'Avanzado', 'Saiyajin', NULL, 'activo', 15, 2, 0, '2026-05-10 23:31:27'),
(7, 'Goten', 'Son', 55.00, 1.57, 14, 'Peso Ligero', 'Intermedio', 'Japonesa', NULL, 'activo', 10, 1, 0, '2026-05-10 23:31:27'),
(8, 'Yamcha', NULL, 72.00, 1.83, 29, 'Peso Medio', 'Intermedio', 'Japonesa', NULL, 'activo', 12, 14, 0, '2026-05-10 23:31:27'),
(9, 'Tenshinhan', NULL, 80.00, 1.87, 31, 'Peso Pesado', 'Avanzado', 'Japonesa', NULL, 'activo', 28, 6, 0, '2026-05-10 23:31:27'),
(10, 'Freezer', NULL, 110.00, 1.58, 50, 'Peso Pesado', 'Élite', 'Frieza Race', NULL, 'activo', 1000, 1, 0, '2026-05-10 23:31:27');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id_rol` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id_rol`, `nombre`, `descripcion`) VALUES
(1, 'Árbitro', 'Juzga y dirige los combates'),
(2, 'Entrenador', 'Acompaña y asesora al luchador'),
(3, 'Médico', 'Atención médica en el evento'),
(4, 'Seguridad', 'Control de acceso y orden'),
(5, 'Administrador', 'Gestión general del torneo'),
(6, 'Registrador', 'Registro e inscripción de participantes');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sesion`
--

CREATE TABLE `sesion` (
  `id_sesion` int(10) UNSIGNED NOT NULL,
  `fecha_inicio` datetime NOT NULL,
  `fecha_fin` datetime DEFAULT NULL,
  `id_usuario` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sesion`
--

INSERT INTO `sesion` (`id_sesion`, `fecha_inicio`, `fecha_fin`, `id_usuario`) VALUES
(1, '2026-05-11 06:38:25', '2026-05-11 06:40:30', 1),
(2, '2026-05-11 06:39:41', NULL, 2),
(3, '2026-05-11 06:40:30', '2026-05-11 06:41:58', 1),
(4, '2026-05-11 06:41:06', NULL, 3),
(5, '2026-05-11 06:41:58', '2026-05-11 06:47:33', 1),
(6, '2026-05-11 06:42:28', '2026-05-11 06:47:00', 4),
(7, '2026-05-11 06:47:00', NULL, 4),
(8, '2026-05-11 06:47:33', '2026-05-11 07:15:45', 1),
(9, '2026-05-11 07:15:45', NULL, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `staff_torneo`
--

CREATE TABLE `staff_torneo` (
  `id_staff` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) DEFAULT NULL,
  `documento` varchar(30) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(120) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `id_rol` int(10) UNSIGNED DEFAULT NULL,
  `id_zona` int(10) UNSIGNED DEFAULT NULL,
  `estado` enum('activo','inactivo') NOT NULL DEFAULT 'activo',
  `creado_en` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `staff_torneo`
--

INSERT INTO `staff_torneo` (`id_staff`, `nombre`, `apellido`, `documento`, `telefono`, `email`, `foto`, `id_rol`, `id_zona`, `estado`, `creado_en`) VALUES
(1, 'Maestro', 'Roshi', '12345678', '+1-555-0001', 'roshi@kame.house', NULL, 1, 1, 'activo', '2026-05-10 23:31:27'),
(2, 'Dende', NULL, '87654321', '+1-555-0002', 'dende@namek.world', NULL, 3, 5, 'activo', '2026-05-10 23:31:27'),
(3, 'Bulma', 'Briefs', '11223344', '+1-555-0003', 'bulma@capsule.corp', NULL, 5, 4, 'activo', '2026-05-10 23:31:27'),
(4, 'Chichi', 'Son', '44332211', '+1-555-0004', 'chichi@mt.paozu', NULL, 6, 4, 'activo', '2026-05-10 23:31:27'),
(5, 'Oob', NULL, '55667788', '+1-555-0005', 'oob@papaya.island', NULL, 1, 2, 'activo', '2026-05-10 23:31:27'),
(6, 'goku', 'quiñones', '55667788', '3028359211', 'ragnarspoly@gmail.com', NULL, 4, 2, 'activo', '2026-05-10 23:49:13'),
(7, 'janier', 'quiñones', '10101010332', '3028359211', 'ragnarspoly@gmail.com', NULL, 1, 1, 'activo', '2026-05-10 23:50:13');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `staff_turnos`
--

CREATE TABLE `staff_turnos` (
  `id_turno` int(10) UNSIGNED NOT NULL,
  `id_staff` int(10) UNSIGNED NOT NULL,
  `id_torneo` int(10) UNSIGNED DEFAULT NULL,
  `fecha` date NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_fin` time NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `creado_en` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `staff_turnos`
--

INSERT INTO `staff_turnos` (`id_turno`, `id_staff`, `id_torneo`, `fecha`, `hora_inicio`, `hora_fin`, `descripcion`, `creado_en`) VALUES
(1, 1, 2, '2026-07-15', '08:00:00', '14:00:00', 'Arbitraje – cuartos de final', '2026-05-10 23:31:27'),
(2, 2, 2, '2026-07-15', '07:00:00', '20:00:00', 'Servicio médico todo el día', '2026-05-10 23:31:27'),
(3, 3, 2, '2026-07-15', '07:30:00', '09:00:00', 'Acreditaciones y registro', '2026-05-10 23:31:27'),
(4, 4, 2, '2026-07-15', '07:00:00', '20:00:00', 'Control de acceso – Zona D', '2026-05-10 23:31:27'),
(5, 1, 2, '2026-07-17', '10:00:00', '18:00:00', 'Arbitraje – final', '2026-05-10 23:31:27');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `torneos`
--

CREATE TABLE `torneos` (
  `id_torneo` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `estado` enum('proximo','activo','finalizado','cancelado') NOT NULL DEFAULT 'proximo',
  `ubicacion` varchar(200) DEFAULT NULL,
  `tipo` varchar(80) DEFAULT NULL,
  `tiempo_limite_minutos` tinyint(3) UNSIGNED NOT NULL DEFAULT 3,
  `premio` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `capacidad_maxima` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `reglas` text DEFAULT NULL,
  `id_campeon` int(10) UNSIGNED DEFAULT NULL,
  `creado_en` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `torneos`
--

INSERT INTO `torneos` (`id_torneo`, `nombre`, `descripcion`, `fecha_inicio`, `fecha_fin`, `estado`, `ubicacion`, `tipo`, `tiempo_limite_minutos`, `premio`, `logo`, `capacidad_maxima`, `reglas`, `id_campeon`, `creado_en`) VALUES
(1, 'Torneo del Poder', 'El gran torneo interestelar donde solo el más fuerte sobrevive.', '2026-06-01', '2026-06-03', 'proximo', 'Estadio Mundial, Ciudad del Lago', 'Absoluto', 5, 'Supervivencia de la Tierra', NULL, 80, 'Prohibido matar. Quien caiga del ring pierde. Sin armas.', NULL, '2026-05-10 23:31:27'),
(2, 'Budokai 28', 'Torneo mundial de artes marciales, vigésima octava edición.', '2026-07-15', '2026-07-17', 'proximo', 'Templo del Tortuga Ermitaño, Papaya Island', 'Peso Medio', 3, 'Cinturón de campeón mundial + 500,000 zeni', NULL, 64, 'Reglas estándar de artes marciales. Ki permitido. Sin armas.', NULL, '2026-05-10 23:31:27');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `torneo_participantes`
--

CREATE TABLE `torneo_participantes` (
  `id` int(10) UNSIGNED NOT NULL,
  `id_torneo` int(10) UNSIGNED NOT NULL,
  `id_luchador` int(10) UNSIGNED NOT NULL,
  `inscrito_en` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `torneo_participantes`
--

INSERT INTO `torneo_participantes` (`id`, `id_torneo`, `id_luchador`, `inscrito_en`) VALUES
(1, 1, 1, '2026-05-10 23:31:27'),
(2, 1, 2, '2026-05-10 23:31:27'),
(3, 1, 3, '2026-05-10 23:31:27'),
(4, 1, 4, '2026-05-10 23:31:27'),
(5, 1, 5, '2026-05-10 23:31:27'),
(6, 1, 6, '2026-05-10 23:31:27'),
(7, 1, 7, '2026-05-10 23:31:27'),
(8, 1, 8, '2026-05-10 23:31:27'),
(9, 1, 9, '2026-05-10 23:31:27'),
(10, 1, 10, '2026-05-10 23:31:27'),
(11, 2, 1, '2026-05-10 23:31:27'),
(12, 2, 2, '2026-05-10 23:31:27'),
(13, 2, 3, '2026-05-10 23:31:27'),
(14, 2, 5, '2026-05-10 23:31:27'),
(15, 2, 6, '2026-05-10 23:31:27'),
(16, 2, 7, '2026-05-10 23:31:27'),
(17, 2, 8, '2026-05-10 23:31:27'),
(18, 2, 9, '2026-05-10 23:31:27');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(10) UNSIGNED NOT NULL,
  `username` varchar(60) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` varchar(30) NOT NULL DEFAULT 'viewer',
  `estado` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `username`, `password`, `rol`, `estado`) VALUES
(1, 'admin', '$2y$10$wnH/y6egQ/cZZKKjMF2DDeL5gPeimsqbQoEko4IWbuYRf1M4EtgvG', 'admin', 1),
(2, 'goku', '$2y$12$r2Zzusnf0MQ3h9se0vQoCedVA8FD6XUFfkIgFcOHzEUge.4UJQzjC', 'Operador', 1),
(3, 'vegueta', '$2y$12$BNGX5l6p6v1boisJRvust.pSeNFkZDaK..Vj0ej/LvSzngTpTEPc6', 'Arbitro', 1),
(4, 'broly', '$2y$12$DDY4gQdZCYWskr5bV553Y.XlRuOdgbsdPVuXU27Kusmeg21Zdv95C', 'Visualizador', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `zonas`
--

CREATE TABLE `zonas` (
  `id_zona` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `zonas`
--

INSERT INTO `zonas` (`id_zona`, `nombre`, `descripcion`) VALUES
(1, 'Zona A – Tatami Principal', 'Arena central para combates de final y semifinal'),
(2, 'Zona B – Tatami Norte', 'Arena secundaria para fases clasificatorias'),
(3, 'Zona C – Tatami Sur', 'Arena de calentamiento y combates menores'),
(4, 'Zona D – Recepción', 'Control de acceso y acreditaciones'),
(5, 'Zona E – Médica', 'Área de atención médica'),
(6, 'Zona F – VIP', 'Palco y área de autoridades');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `arenas`
--
ALTER TABLE `arenas`
  ADD PRIMARY KEY (`id_arena`),
  ADD KEY `fk_arena_torneo` (`id_torneo`);

--
-- Indices de la tabla `combates`
--
ALTER TABLE `combates`
  ADD PRIMARY KEY (`id_combate`),
  ADD KEY `fk_combate_luchador1` (`id_luchador_1`),
  ADD KEY `fk_combate_luchador2` (`id_luchador_2`),
  ADD KEY `fk_combate_ganador` (`ganador_id`),
  ADD KEY `idx_combates_torneo` (`id_torneo`),
  ADD KEY `idx_combates_fecha` (`fecha_combate`);

--
-- Indices de la tabla `log`
--
ALTER TABLE `log`
  ADD PRIMARY KEY (`id_log`),
  ADD KEY `idx_log_usuario` (`id_usuario`),
  ADD KEY `idx_log_fecha` (`fecha`);

--
-- Indices de la tabla `luchadores`
--
ALTER TABLE `luchadores`
  ADD PRIMARY KEY (`id_luchador`),
  ADD KEY `idx_luchadores_estado` (`estado`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_rol`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `sesion`
--
ALTER TABLE `sesion`
  ADD PRIMARY KEY (`id_sesion`),
  ADD KEY `idx_sesion_usuario` (`id_usuario`),
  ADD KEY `idx_sesion_fecha` (`fecha_inicio`);

--
-- Indices de la tabla `staff_torneo`
--
ALTER TABLE `staff_torneo`
  ADD PRIMARY KEY (`id_staff`),
  ADD KEY `fk_staff_rol` (`id_rol`),
  ADD KEY `fk_staff_zona` (`id_zona`),
  ADD KEY `idx_staff_estado` (`estado`);

--
-- Indices de la tabla `staff_turnos`
--
ALTER TABLE `staff_turnos`
  ADD PRIMARY KEY (`id_turno`),
  ADD KEY `fk_turno_staff` (`id_staff`),
  ADD KEY `fk_turno_torneo` (`id_torneo`);

--
-- Indices de la tabla `torneos`
--
ALTER TABLE `torneos`
  ADD PRIMARY KEY (`id_torneo`),
  ADD KEY `fk_torneo_campeon` (`id_campeon`),
  ADD KEY `idx_torneos_estado` (`estado`);

--
-- Indices de la tabla `torneo_participantes`
--
ALTER TABLE `torneo_participantes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_torneo_luchador` (`id_torneo`,`id_luchador`),
  ADD KEY `fk_part_luchador` (`id_luchador`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indices de la tabla `zonas`
--
ALTER TABLE `zonas`
  ADD PRIMARY KEY (`id_zona`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `arenas`
--
ALTER TABLE `arenas`
  MODIFY `id_arena` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `combates`
--
ALTER TABLE `combates`
  MODIFY `id_combate` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `log`
--
ALTER TABLE `log`
  MODIFY `id_log` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `luchadores`
--
ALTER TABLE `luchadores`
  MODIFY `id_luchador` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_rol` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `sesion`
--
ALTER TABLE `sesion`
  MODIFY `id_sesion` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `staff_torneo`
--
ALTER TABLE `staff_torneo`
  MODIFY `id_staff` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `staff_turnos`
--
ALTER TABLE `staff_turnos`
  MODIFY `id_turno` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `torneos`
--
ALTER TABLE `torneos`
  MODIFY `id_torneo` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `torneo_participantes`
--
ALTER TABLE `torneo_participantes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `zonas`
--
ALTER TABLE `zonas`
  MODIFY `id_zona` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `arenas`
--
ALTER TABLE `arenas`
  ADD CONSTRAINT `fk_arena_torneo` FOREIGN KEY (`id_torneo`) REFERENCES `torneos` (`id_torneo`) ON DELETE SET NULL;

--
-- Filtros para la tabla `combates`
--
ALTER TABLE `combates`
  ADD CONSTRAINT `fk_combate_ganador` FOREIGN KEY (`ganador_id`) REFERENCES `luchadores` (`id_luchador`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_combate_luchador1` FOREIGN KEY (`id_luchador_1`) REFERENCES `luchadores` (`id_luchador`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_combate_luchador2` FOREIGN KEY (`id_luchador_2`) REFERENCES `luchadores` (`id_luchador`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_combate_torneo` FOREIGN KEY (`id_torneo`) REFERENCES `torneos` (`id_torneo`) ON DELETE SET NULL;

--
-- Filtros para la tabla `log`
--
ALTER TABLE `log`
  ADD CONSTRAINT `fk_log_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE SET NULL;

--
-- Filtros para la tabla `sesion`
--
ALTER TABLE `sesion`
  ADD CONSTRAINT `fk_sesion_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`) ON DELETE CASCADE;

--
-- Filtros para la tabla `staff_torneo`
--
ALTER TABLE `staff_torneo`
  ADD CONSTRAINT `fk_staff_rol` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_staff_zona` FOREIGN KEY (`id_zona`) REFERENCES `zonas` (`id_zona`) ON DELETE SET NULL;

--
-- Filtros para la tabla `staff_turnos`
--
ALTER TABLE `staff_turnos`
  ADD CONSTRAINT `fk_turno_staff` FOREIGN KEY (`id_staff`) REFERENCES `staff_torneo` (`id_staff`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_turno_torneo` FOREIGN KEY (`id_torneo`) REFERENCES `torneos` (`id_torneo`) ON DELETE SET NULL;

--
-- Filtros para la tabla `torneos`
--
ALTER TABLE `torneos`
  ADD CONSTRAINT `fk_torneo_campeon` FOREIGN KEY (`id_campeon`) REFERENCES `luchadores` (`id_luchador`) ON DELETE SET NULL;

--
-- Filtros para la tabla `torneo_participantes`
--
ALTER TABLE `torneo_participantes`
  ADD CONSTRAINT `fk_part_luchador` FOREIGN KEY (`id_luchador`) REFERENCES `luchadores` (`id_luchador`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_part_torneo` FOREIGN KEY (`id_torneo`) REFERENCES `torneos` (`id_torneo`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
