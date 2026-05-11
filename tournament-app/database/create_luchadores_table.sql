-- Agregar tabla luchadores a la base de datos torneo_new
-- Ejecutar este script en phpMyAdmin o consola MySQL

USE torneo_new;

-- Estructura de tabla para la tabla `luchadores`
CREATE TABLE `luchadores` (
  `id_luchador` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `apellido` varchar(100) DEFAULT NULL,
  `tipo_documento` varchar(20) DEFAULT NULL,
  `numero_documento` varchar(50) DEFAULT NULL,
  `edad` int(11) DEFAULT NULL,
  `genero` varchar(10) DEFAULT NULL,
  `categoria` varchar(50) DEFAULT NULL,
  `peso` decimal(5,2) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `victorias` int(11) DEFAULT 0,
  `derrotas` int(11) DEFAULT 0,
  `estado` varchar(20) DEFAULT 'activo',
  `foto` varchar(255) DEFAULT NULL,
  `fecha_registro` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_luchador`),
  UNIQUE KEY `numero_documento` (`numero_documento`),
  KEY `email` (`email`),
  KEY `categoria` (`categoria`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos de ejemplo (opcional)
INSERT INTO `luchadores` (`nombre`, `apellido`, `tipo_documento`, `numero_documento`, `edad`, `genero`, `categoria`, `peso`, `telefono`, `email`, `victorias`, `derrotas`, `estado`) VALUES
('Goku', 'Son', 'DNI', '12345678', 30, 'Masculino', 'Peso Pesado', 85.50, '555-0101', 'goku@dbz.com', 15, 2, 'activo'),
('Vegeta', 'Briefs', 'DNI', '87654321', 32, 'Masculino', 'Peso Pesado', 78.20, '555-0102', 'vegeta@dbz.com', 12, 5, 'activo'),
('Piccolo', 'Namek', 'PAS', '11223344', 35, 'Masculino', 'Peso Mediano', 75.00, '555-0103', 'piccolo@dbz.com', 10, 7, 'activo');
