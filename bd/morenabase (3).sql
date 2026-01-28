-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 16-10-2025 a las 11:52:22
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `morenabase`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `afiliados`
--

CREATE TABLE `afiliados` (
  `id` int(11) NOT NULL,
  `curp` varchar(18) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellidos` varchar(120) NOT NULL,
  `dia` int(11) NOT NULL,
  `mes` int(11) NOT NULL,
  `anios` int(11) NOT NULL,
  `sexo` enum('M','F','Otro') NOT NULL,
  `estado` varchar(80) NOT NULL,
  `domicilio` varchar(255) NOT NULL,
  `seccion` varchar(10) NOT NULL,
  `telefono` varchar(30) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `rol` enum('coordinador','lider','sublider','afiliado') NOT NULL DEFAULT 'afiliado',
  `estatus` varchar(20) NOT NULL DEFAULT 'activo',
  `curp_id_coordinador` varchar(18) DEFAULT NULL,
  `curp_id_lider` varchar(18) DEFAULT NULL,
  `curp_id_sublider` varchar(18) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `afiliados`
--

INSERT INTO `afiliados` (`id`, `curp`, `nombre`, `apellidos`, `dia`, `mes`, `anios`, `sexo`, `estado`, `domicilio`, `seccion`, `telefono`, `foto`, `rol`, `estatus`, `curp_id_coordinador`, `curp_id_lider`, `curp_id_sublider`, `created_at`, `updated_at`) VALUES
(1, 'CORD900101HDFABC01', 'Ana', 'González Pérez', 1, 1, 1990, 'F', 'CDMX', 'Av. Reforma #123', '101', '5551234567', 'foto1.jpg', 'coordinador', 'activo', NULL, NULL, NULL, '2025-09-18 07:08:36', NULL),
(2, 'LIDR910202HDFDEF02', 'Pedro', 'Ramírez López', 2, 2, 1991, 'M', 'CDMX', 'Calle Morelos #456', '102', '5559876543', 'foto2.jpg', 'lider', 'activo', 'CORD900101HDFABC01', NULL, NULL, '2025-09-18 07:08:36', NULL),
(3, 'LIDR910203MDFXYZ03', 'María', 'López Sánchez', 3, 3, 1991, 'F', 'CDMX', 'Calle Juárez #789', '103', '5554567890', 'foto3.jpg', 'lider', 'activo', 'CORD900101HDFABC01', NULL, NULL, '2025-09-18 07:08:36', NULL),
(4, 'SUBL920303MDFGHI03', 'Juan', 'Martínez Ortega', 3, 3, 1992, 'M', 'CDMX', 'Col. Centro #11', '104', '5552223333', 'foto4.jpg', 'sublider', 'activo', 'CORD900101HDFABC01', 'LIDR910202HDFDEF02', NULL, '2025-09-18 07:08:36', NULL),
(5, 'SUBL920304MDFJKL04', 'Laura', 'Fernández Díaz', 4, 4, 1992, 'F', 'CDMX', 'Col. Roma #22', '105', '5554445555', 'foto5.jpg', 'sublider', 'activo', 'CORD900101HDFABC01', 'LIDR910202HDFDEF02', NULL, '2025-09-18 07:08:36', NULL),
(6, 'SUBL920305MDFMNO05', 'Carlos', 'Hernández Ruiz', 5, 5, 1992, 'M', 'CDMX', 'Col. Condesa #33', '106', '5556667777', 'foto6.jpg', 'sublider', 'activo', 'CORD900101HDFABC01', 'LIDR910203MDFXYZ03', NULL, '2025-09-18 07:08:36', NULL),
(7, 'AFIL930404HDFJKL06', 'Luis', 'Gómez Torres', 4, 4, 1993, 'M', 'CDMX', 'Col. Del Valle #44', '107', '5558889999', 'foto7.jpg', 'afiliado', 'activo', 'CORD900101HDFABC01', 'LIDR910202HDFDEF02', 'SUBL920303MDFGHI03', '2025-09-18 07:08:36', NULL),
(8, 'AFIL930405MDFJKL07', 'Andrea', 'Ruiz Vargas', 5, 5, 1993, 'F', 'CDMX', 'Col. Narvarte #55', '108', '5551112222', 'foto8.jpg', 'afiliado', 'activo', 'CORD900101HDFABC01', 'LIDR910202HDFDEF02', 'SUBL920304MDFJKL04', '2025-09-18 07:08:36', NULL),
(9, 'AFIL930406MDFJKL08', 'José', 'Domínguez Castro', 6, 6, 1993, 'M', 'CDMX', 'Col. Doctores #66', '109', '5553334444', 'foto9.jpg', 'afiliado', 'activo', 'CORD900101HDFABC01', 'LIDR910203MDFXYZ03', 'SUBL920305MDFMNO05', '2025-09-18 07:08:36', NULL),
(10, 'AFIL930407MDFJKL09', 'Sofía', 'Pérez Aguilar', 7, 7, 1993, 'F', 'CDMX', 'Col. Polanco #77', '110', '5555556666', 'foto10.jpg', 'afiliado', 'activo', 'CORD900101HDFABC01', 'LIDR910203MDFXYZ03', 'SUBL920305MDFMNO05', '2025-09-18 07:08:36', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alertas`
--

CREATE TABLE `alertas` (
  `id` int(11) NOT NULL,
  `curp_alerta` varchar(255) NOT NULL,
  `titulo` varchar(255) NOT NULL,
  `alertas` varchar(255) NOT NULL,
  `fecha` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `alertas`
--

INSERT INTO `alertas` (`id`, `curp_alerta`, `titulo`, `alertas`, `fecha`) VALUES
(1, 'LIDR910203MDFXYZ03', 'Nuevo Registro', 'Moises Acaba registrar aun sublíder mas', '19/09/2025');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `anuncios`
--

CREATE TABLE `anuncios` (
  `id` int(11) NOT NULL,
  `tipo` varchar(255) NOT NULL,
  `titulo` varchar(500) NOT NULL,
  `descripcion` varchar(500) NOT NULL,
  `fecha` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `anuncios`
--

INSERT INTO `anuncios` (`id`, `tipo`, `titulo`, `descripcion`, `fecha`) VALUES
(1, 'Recordatorio', 'Junta de Coordinación', 'Se informa a todos los integrantes que el día de mañana habrá junta general de coordinación a las 10:00 AM en la sala principal.', '18/09/2025');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `coordinador`
--

CREATE TABLE `coordinador` (
  `id` int(11) NOT NULL,
  `curp` varchar(18) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellidos` varchar(150) NOT NULL,
  `dia` int(11) DEFAULT NULL,
  `mes` int(11) DEFAULT NULL,
  `anios` int(11) DEFAULT NULL,
  `sexo` char(1) DEFAULT NULL,
  `estado` varchar(100) DEFAULT NULL,
  `domicilio` varchar(255) DEFAULT NULL,
  `seccion` varchar(50) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `rol` enum('coordinador','lider','sublider','militante') DEFAULT 'coordinador',
  `estatus` enum('activo','inactivo') DEFAULT 'activo',
  `registrado_el` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `coordinador`
--

INSERT INTO `coordinador` (`id`, `curp`, `nombre`, `apellidos`, `dia`, `mes`, `anios`, `sexo`, `estado`, `domicilio`, `seccion`, `telefono`, `foto`, `rol`, `estatus`, `registrado_el`) VALUES
(1, 'CORD900101HDFABC01', 'Ana', 'González', NULL, NULL, NULL, 'M', 'CDMX', NULL, NULL, NULL, NULL, 'coordinador', 'activo', '2025-09-18 06:00:21');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `lider`
--

CREATE TABLE `lider` (
  `id` int(11) NOT NULL,
  `curp` varchar(18) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellidos` varchar(150) NOT NULL,
  `dia` int(11) DEFAULT NULL,
  `mes` int(11) DEFAULT NULL,
  `anios` int(11) NOT NULL,
  `sexo` char(1) DEFAULT NULL,
  `estado` varchar(100) DEFAULT NULL,
  `domicilio` varchar(255) DEFAULT NULL,
  `seccion` varchar(50) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `foto` varchar(10000) NOT NULL,
  `rol` enum('coordinador','lider','sublider','militante') DEFAULT 'lider',
  `estatus` enum('activo','inactivo') DEFAULT 'activo',
  `registrado_el` timestamp NOT NULL DEFAULT current_timestamp(),
  `coordinador` varchar(18) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `lider`
--

INSERT INTO `lider` (`id`, `curp`, `nombre`, `apellidos`, `dia`, `mes`, `anios`, `sexo`, `estado`, `domicilio`, `seccion`, `telefono`, `foto`, `rol`, `estatus`, `registrado_el`, `coordinador`) VALUES
(2, 'LIDR910203MDFXYZ03', 'María', 'López', NULL, NULL, 0, 'M', 'CDMX', NULL, '', '', 'Imagen de WhatsApp 2025-07-25 a las 18.03.47_70174f7a.jpg', 'lider', 'activo', '2025-09-18 06:00:21', 'CORD900101HDFABC01'),
(3, '45345343RTERTER', 'ertertert', 'erter', 34, 4, 34, 'M', '', '43435345435', '345', '345345345', '45345343RTERTER_1760602557.jpg', 'lider', 'activo', '2025-10-16 08:15:57', 'CORD900101HDFABC01'),
(4, '324234234234234', '223wrewer', '234234', 22, 22, 22, 'F', '', '2323', '2333', '2323', '324234234234234_1760606143.jpg', 'lider', 'activo', '2025-10-16 09:15:43', 'CORD900101HDFABC01');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `militante`
--

CREATE TABLE `militante` (
  `id` int(11) NOT NULL,
  `curp` varchar(18) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellidos` varchar(150) NOT NULL,
  `dia` int(11) DEFAULT NULL,
  `mes` int(11) DEFAULT NULL,
  `anios` int(11) DEFAULT NULL,
  `sexo` char(1) DEFAULT NULL,
  `estado` varchar(100) DEFAULT NULL,
  `domicilio` varchar(255) DEFAULT NULL,
  `seccion` varchar(50) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `rol` enum('coordinador','lider','sublider','militante') DEFAULT 'militante',
  `estatus` enum('activo','inactivo') DEFAULT 'activo',
  `registrado_el` timestamp NOT NULL DEFAULT current_timestamp(),
  `coordinador` varchar(18) NOT NULL,
  `lider` varchar(18) NOT NULL,
  `sublider` varchar(18) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `militante`
--

INSERT INTO `militante` (`id`, `curp`, `nombre`, `apellidos`, `dia`, `mes`, `anios`, `sexo`, `estado`, `domicilio`, `seccion`, `telefono`, `foto`, `rol`, `estatus`, `registrado_el`, `coordinador`, `lider`, `sublider`) VALUES
(5, 'BARC580430MVZLMT07', 'catalina', 'blanco romero', 30, 4, 1958, 'F', '', 'Col. une nueva creacion', '2088', '9933924923', 'BARC580430MVZLMT07_1760576484.jpg', 'militante', 'activo', '2025-10-16 01:01:24', 'CORD900101HDFABC01', 'LIDR910203MDFXYZ03', 'GAHM990712HTCRRS05'),
(6, 'HERN920511HJCMPL02', 'MIGUEL ANGEL', 'HERNANDEZ JACINTO', 8, 12, 2006, 'M', '', 'cOL. jUAN SABINES', '1052', '917114569825', 'HERN920511HJCMPL02_1760600240.jpg', 'militante', 'activo', '2025-10-16 07:37:20', 'CORD900101HDFABC01', 'LIDR910203MDFXYZ03', 'GAHM990712HTCRRS05'),
(7, 'MART031228MDFNXL08', 'marta', 'LOPEZ HIDALGO', 23, 5, 1958, 'F', '', 'Ranchería Macayo 2ª Sección', '1063', '9933256984', 'MART031228MDFNXL08_1760600392.jpg', 'militante', 'activo', '2025-10-16 07:39:52', 'CORD900101HDFABC01', 'LIDR910203MDFXYZ03', 'GAHM990712HTCRRS05'),
(8, '55768657868TYUTYU', 'wrwerwer', 'wqerwqer', 23, 12, 22, 'M', '', '23123123123', '221331231', '123123', '55768657868TYUTYU_1760606191.png', 'militante', 'activo', '2025-10-16 09:16:31', 'CORD900101HDFABC01', 'LIDR910203MDFXYZ03', 'GAHM990712HTCRRS05'),
(9, '12312312312', '3123123123123', '123123', 11, 11, 1, 'M', '', 'cOL. jUAN SABINES', '1052', '1213', '12312312312_1760606266.jpg', 'militante', 'activo', '2025-10-16 09:17:46', 'CORD900101HDFABC01', 'LIDR910203MDFXYZ03', 'GAHM990712HTCRRS05');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `secciones`
--

CREATE TABLE `secciones` (
  `id` int(11) NOT NULL,
  `seccion` varchar(255) NOT NULL,
  `cp` varchar(11) NOT NULL,
  `colonia` varchar(255) NOT NULL,
  `referencia` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `secciones`
--

INSERT INTO `secciones` (`id`, `seccion`, `cp`, `colonia`, `referencia`) VALUES
(1, '1052', '29500', 'Col. Juan Sabines Gutiérrez', 'Jardín de Niñas y Niños Víctor Hugo, Andador Fco. I. Madero (junto a Club Premier)'),
(2, '1052', '29500', 'Col. Juan Sabines Las Cactáceas', 'Sec. Bicentenario de la Independencia Nacional, Blvd. Enrique Peña Nieto (a 89 m del IMSS)'),
(3, '1053', '29500', 'Col. Centro', 'Centro Social Lázaro Cárdenas, Calle Sesquicentenario (frente al Parque 13 de Marzo)'),
(4, '1054', '29500', 'Col. Centro', 'Colegio Particular Lázaro Cárdenas, Av. Vicente Guerrero (a 15 m de la Iglesia Adventista)'),
(5, '1054', '29500', 'Col. Centro', 'Kiosco del Parque Central, Calle Miguel Aldama (enfrente del H. Ayuntamiento)'),
(6, '1055', '29500', 'Col. Francisco Villa', 'Colegio Reforma A.C., Av. 16 de Septiembre (entre Aldama y Abedul)'),
(7, '1056', '29500', 'Col. Carlos Salinas de Gortari', 'Jardín de Niñas y Niños Prof. Porfirio Dávila, 2ª Priv. de Hidalgo (a 15 m de la Casa de la Cultura)'),
(8, '1058', '29500', 'Col. Buena Vista Zona 5', 'Estacionamiento del Mercado José María Morelos, Calle Nuevo León (entre Tonalá y Durango)'),
(9, '1059', '29500', 'Col. Buena Vista Zona 5A', 'Primaria Nicolás Bravo Rueda, Calle Sonora (a un costado del ICATECH)'),
(10, '1060', '29500', 'Col. Buena Vista Zona 5A', 'Primaria Francisco I. Madero, Calle Nuevo León (frente al Mercado Público)'),
(11, '1061', '29500', 'Col. Francisco Villa', 'Primaria 18 de Marzo, Calle 18 de Marzo (junto al parque La Pasadita)'),
(12, '1062', '29500', 'Col. Óscar Torres Pancardo', 'Primaria Rosario Castellanos (Art. 123), Blvd. Paseo Chiapas (frente al Jardín Carlos Pellicer Cámara)'),
(13, '1063', '29500', 'Ranchería Macayo 2ª Sección', 'Primaria 5 de Mayo (a 200 m del Jardín Rébsamen)'),
(14, '1063', '29500', 'Ranchería Macayo 3ª Sección', 'Primaria 10 de Mayo (frente a la Telesecundaria No. 797)'),
(15, '1064', '29500', 'Ejido Dr. Rafael Pascasio Gamboa', 'Primaria Prof. Gregorio Torres Quintero, Calle Venustiano Carranza (a 400 m de la tienda DICONSA)'),
(16, '1064', '29500', 'Ranchería San Miguel 1ª Sección', 'Primaria 18 de Marzo (a un costado de la Casa Ejidal)'),
(17, '1065', '29500', 'Ranchería Miguel Hidalgo', 'Jardín de Niños Rosa Aura Zapata Cano (a 300 m del puesto PEP, cerca de la gasolinera de Boca de Limón)'),
(18, '1065', '29500', 'Ranchería San Miguel 2ª Sección', 'Primaria Dr. Belisario Domínguez (frente a la Oficina Campesina y Ejidatarios del Norte)'),
(19, '1066', '29500', 'Col. El Carmen', 'Primaria Josefa Ortiz de Domínguez, Calle Venustiano Carranza (a 100 m de la Casa Ejidal)'),
(20, '1067', '29500', 'Col. San José Limoncito', 'Primaria Ignacio López Rayón (a un costado de la Plaza Comunitaria)'),
(21, '1067', '29500', 'Ejido El Limoncito', 'Primaria Ricardo Flores Magón (enfrente de la iglesia Adventista)'),
(22, '1068', '29500', 'Col. El Caracol', 'Primaria Lázaro Cárdenas del Río (a un costado del kínder)'),
(23, '1068', '29500', 'Ranchería Zapotal 2ª Sección', 'Primaria Francisco I. Madero (junto a la galera de asambleas)'),
(24, '1069', '29500', 'Ranchería Morelos 1ª Sección', 'Primaria José Vasconcelos Calderón (a 200 m de la Universidad SEPROG)'),
(25, '1069', '29500', 'Ranchería Ignacio Zaragoza', 'Primaria Leona Vicario (a 150 m del pozo de agua)'),
(26, '2086', '', 'Col. La Unión', 'Preescolar Sor Juana Inés de la Cruz, Prolongación A Juspi (a 80 m del salón de eventos)'),
(27, '2087', '', 'Col. Guadalupe Victoria', 'COBACH Plantel 6, Calle Atiza (a un costado del Jardín Benito Juárez García)'),
(28, '2088', '', 'Col. Nueva Creación UNE', 'Escuela Benito Juárez García, Av. Pichucalco (a 3 cuadras del Jardín Héctor Victoria)');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sublider`
--

CREATE TABLE `sublider` (
  `id` int(11) NOT NULL,
  `curp` varchar(18) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellidos` varchar(150) NOT NULL,
  `dia` int(11) DEFAULT NULL,
  `mes` int(11) DEFAULT NULL,
  `anios` int(11) DEFAULT NULL,
  `sexo` char(1) DEFAULT NULL,
  `estado` varchar(100) DEFAULT NULL,
  `domicilio` varchar(255) DEFAULT NULL,
  `seccion` varchar(50) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `rol` enum('coordinador','lider','sublider','militante') DEFAULT 'sublider',
  `estatus` enum('activo','inactivo') DEFAULT 'activo',
  `registrado_el` timestamp NOT NULL DEFAULT current_timestamp(),
  `coordinador` varchar(18) NOT NULL,
  `lider` varchar(18) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sublider`
--

INSERT INTO `sublider` (`id`, `curp`, `nombre`, `apellidos`, `dia`, `mes`, `anios`, `sexo`, `estado`, `domicilio`, `seccion`, `telefono`, `foto`, `rol`, `estatus`, `registrado_el`, `coordinador`, `lider`) VALUES
(10, 'GAHM990712HTCRRS05', 'Moises de Jesus', 'Garcia hernandez', 12, 7, 1999, 'M', '', 'Fracc. Loma Bonita', '1061', '9613727059', 'GAHM990712HTCRRS05_1760576135.jpg', 'sublider', 'activo', '2025-10-16 00:55:35', 'CORD900101HDFABC01', 'LIDR910203MDFXYZ03'),
(11, 'LOPE850324MMNEVR07', 'Luis ARTURO', 'GOMEZ LOPEZ', 12, 7, 1963, 'M', '', 'cOL. DEL carmen', '1066', '9171132345', 'LOPE850324MMNEVR07_1760600079.png', 'sublider', 'activo', '2025-10-16 07:34:39', 'CORD900101HDFABC01', 'LIDR910203MDFXYZ03'),
(12, '22222222222', '2222222', '2222222', 22, 22, 2, 'F', '', '2', '22', '22', '22222222222_1760602518.png', 'sublider', 'activo', '2025-10-16 08:15:18', 'CORD900101HDFABC01', 'LIDR910203MDFXYZ03'),
(13, '112312312312', '3123123', '12312', 31, 12, 12, 'M', '', '12', '2312', '12', '112312312312_1760603347.png', 'sublider', 'activo', '2025-10-16 08:29:07', 'CORD900101HDFABC01', 'LIDR910203MDFXYZ03'),
(14, 'QWEQWE', 'qweqweqwe', 'qweqw', 12, 12, 2, 'M', '', '2312312', '1231', '3131323', 'QWEQWE_1760603508.png', 'sublider', 'activo', '2025-10-16 08:31:48', 'CORD900101HDFABC01', 'LIDR910203MDFXYZ03'),
(15, '21312312312', '312312', '3123', 22, 22, 2, 'F', '', '121', '2212', '212', '21312312312_1760604923.png', 'sublider', 'activo', '2025-10-16 08:55:23', 'CORD900101HDFABC01', 'LIDR910203MDFXYZ03'),
(16, 'EWSDWERWERWER', 'werwerwer', 'werwerwer', 12, 7, 0, 'M', '', '1233123', '2223', '123123', 'EWSDWERWERWER_1760606086.png', 'sublider', 'activo', '2025-10-16 09:14:46', 'CORD900101HDFABC01', 'LIDR910203MDFXYZ03');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `afiliados`
--
ALTER TABLE `afiliados`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `curp` (`curp`),
  ADD KEY `idx_curp` (`curp`),
  ADD KEY `idx_rol` (`rol`),
  ADD KEY `idx_seccion` (`seccion`);

--
-- Indices de la tabla `alertas`
--
ALTER TABLE `alertas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `anuncios`
--
ALTER TABLE `anuncios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `coordinador`
--
ALTER TABLE `coordinador`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `curp` (`curp`);

--
-- Indices de la tabla `lider`
--
ALTER TABLE `lider`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `curp` (`curp`),
  ADD KEY `coordinador` (`coordinador`);

--
-- Indices de la tabla `militante`
--
ALTER TABLE `militante`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `curp` (`curp`),
  ADD KEY `coordinador` (`coordinador`),
  ADD KEY `lider` (`lider`),
  ADD KEY `sublider` (`sublider`);

--
-- Indices de la tabla `secciones`
--
ALTER TABLE `secciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sublider`
--
ALTER TABLE `sublider`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `curp` (`curp`),
  ADD KEY `coordinador` (`coordinador`),
  ADD KEY `lider` (`lider`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `afiliados`
--
ALTER TABLE `afiliados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `alertas`
--
ALTER TABLE `alertas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `anuncios`
--
ALTER TABLE `anuncios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `coordinador`
--
ALTER TABLE `coordinador`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `lider`
--
ALTER TABLE `lider`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `militante`
--
ALTER TABLE `militante`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `secciones`
--
ALTER TABLE `secciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de la tabla `sublider`
--
ALTER TABLE `sublider`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `lider`
--
ALTER TABLE `lider`
  ADD CONSTRAINT `lider_ibfk_1` FOREIGN KEY (`coordinador`) REFERENCES `coordinador` (`curp`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `militante`
--
ALTER TABLE `militante`
  ADD CONSTRAINT `militante_ibfk_1` FOREIGN KEY (`coordinador`) REFERENCES `coordinador` (`curp`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `militante_ibfk_2` FOREIGN KEY (`lider`) REFERENCES `lider` (`curp`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `militante_ibfk_3` FOREIGN KEY (`sublider`) REFERENCES `sublider` (`curp`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `sublider`
--
ALTER TABLE `sublider`
  ADD CONSTRAINT `sublider_ibfk_1` FOREIGN KEY (`coordinador`) REFERENCES `coordinador` (`curp`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `sublider_ibfk_2` FOREIGN KEY (`lider`) REFERENCES `lider` (`curp`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
