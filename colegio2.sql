-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost
-- Tiempo de generación: 22-06-2023 a las 21:06:07
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `colegio2`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Estudiantes`
--

CREATE TABLE `Estudiantes` (
  `ID` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `correo` varchar(50) NOT NULL,
  `direccion` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `Estudiantes`
--

INSERT INTO `Estudiantes` (`ID`, `nombre`, `apellido`, `correo`, `direccion`) VALUES
(20, 'raul', 'peres', 'raul@raul.com', 'pachuca'),
(21, 'Laurencio Noe ', 'gonzalez', 'edith@yahoo.com', 'pachuca'),
(23, 'salvadopr ', 'perez', 'salvador@xn--savador-5za', 'pachuca'),
(25, 'ezra lehi', 'González ', 'ezra@ezra.com', 'pachuca'),
(26, 'ezra lehi', 'gonzalez', 'ezra@ezra.com', 'pachuca');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Maestros`
--

CREATE TABLE `Maestros` (
  `ID` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `correo` varchar(50) NOT NULL,
  `direccion` varchar(100) NOT NULL,
  `asignaturas` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `Maestros`
--

INSERT INTO `Maestros` (`ID`, `nombre`, `apellido`, `correo`, `direccion`, `asignaturas`) VALUES
(5, '*** Laurencio Noe  123', 'gonzalez', 'gonza@gonza.com', 'china', 'mate'),
(6, 'Xerxesx< Trafford<	', 'Trafford<	', 'godornan1@state.tx.us', '55259 Sycamore Place	', 'Biología	'),
(7, 'Sly', 'Enderle', 'reverton2@odnoklassniki.ru', '5 Longview Place	', 'Ciencias de la Tierra	'),
(8, 'Cy', 'Champness', 'gmaplesden3@woothemes.com', '98248 Esch Court	', 'Astronomía'),
(12, 'pelicano', 'González ', 'dallin@dallin.com', 'mexico', 'Biología	'),
(13, 'pelicano', 'lozano', 'godornan1@state.tx.us', 'pachuca', 'mate'),
(14, 'raul', 'lozano', 'salvador@xn--savador-5za', 'pchuca', 'mate'),
(16, 'cirila', 'lopez', 'cirila@cirila.com', 'pachuca', 'nuinguna'),
(17, 'amado ', 'rojas', 'amada@amado.com', 'pachuca', 'seminario'),
(18, 'Angelina', 'peña', 'angelina@angelina.com', '123', 'matematicas');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Materias`
--

CREATE TABLE `Materias` (
  `ID` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `maestro_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `Materias`
--

INSERT INTO `Materias` (`ID`, `nombre`, `maestro_id`) VALUES
(54, 'Astronomía', 8),
(55, 'Loria McAw	', 14);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Usuarios`
--

CREATE TABLE `Usuarios` (
  `ID` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `correo` varchar(50) NOT NULL,
  `direccion` varchar(100) NOT NULL,
  `contraseña` varchar(50) NOT NULL,
  `rol` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `Usuarios`
--

INSERT INTO `Usuarios` (`ID`, `nombre`, `apellido`, `correo`, `direccion`, `contraseña`, `rol`) VALUES
(1, 'Laurencio Noe ', 'gonzalez', 'gonzalezsud@yahoo.com', 'pachuca', '1234', 'administrador'),
(2, 'dallin', 'gonzalez', 'dallin@dallin.com', 'pachuca', '1234', 'maestro'),
(3, 'ezra', 'gonzalez', 'ezra@ezra.com', 'pachuca', '1234', 'estudiante'),
(4, 'edith', 'lozano', 'edith@yahoo.com', 'pachuca', '1234', 'administrador'),
(5, 'Naomi', 'Gonzalez Lozano', 'naomi@naomi.com', 'pachuca', '1234', 'estudiante'),
(7, 'laurencio', 'gonzalez', 'laurenci@laurencio.com', 'guerrero', '1234', 'administrador'),
(8, 'edith', 'perez', 'dallin@dallin.com', 'pachuca', '1234', 'estudiante');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `Estudiantes`
--
ALTER TABLE `Estudiantes`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `Maestros`
--
ALTER TABLE `Maestros`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `Materias`
--
ALTER TABLE `Materias`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `maestro_id` (`maestro_id`);

--
-- Indices de la tabla `Usuarios`
--
ALTER TABLE `Usuarios`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `Estudiantes`
--
ALTER TABLE `Estudiantes`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de la tabla `Maestros`
--
ALTER TABLE `Maestros`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `Materias`
--
ALTER TABLE `Materias`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT de la tabla `Usuarios`
--
ALTER TABLE `Usuarios`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `Materias`
--
ALTER TABLE `Materias`
  ADD CONSTRAINT `Materias_ibfk_1` FOREIGN KEY (`maestro_id`) REFERENCES `Maestros` (`ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
