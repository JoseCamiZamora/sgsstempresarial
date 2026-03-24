-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-10-2020 a las 19:00:44
-- Versión del servidor: 10.4.13-MariaDB
-- Versión de PHP: 7.4.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `talentohumanobd`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modalidad_pqrs`
--

CREATE TABLE `modalidad_pqrs` (
  `id` int(11) NOT NULL,
  `descripcion` varchar(120) NOT NULL,
  `estado` varchar(2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `modalidad_pqrs`
--

INSERT INTO `modalidad_pqrs` (`id`, `descripcion`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'FISICO', 'A', '2020-10-23 13:54:35', '2020-10-23 13:54:35'),
(2, 'VIA TELEFONICA', 'A', '2020-10-23 13:54:35', '2020-10-23 13:54:35'),
(3, 'VIA WEB', '', '2020-10-23 13:54:35', '2020-10-23 13:54:35'),
(4, 'REDES SOCIALES', '', '2020-10-23 13:54:35', '2020-10-23 13:54:35'),
(5, 'OTROS', '', '2020-10-23 13:54:35', '2020-10-23 13:54:35');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registro_pqrs`
--

CREATE TABLE `registro_pqrs` (
  `id` int(11) NOT NULL,
  `id_tipo` int(11) NOT NULL,
  `desc_tipo` varchar(50) NOT NULL,
  `id_modalidad` int(2) NOT NULL,
  `desc_modalidad` varchar(50) NOT NULL,
  `radicado` varchar(3) DEFAULT NULL,
  `fecha_radicado` date NOT NULL,
  `programa` varchar(250) DEFAULT NULL,
  `remitente` varchar(250) DEFAULT NULL,
  `telefono` varchar(12) DEFAULT NULL,
  `direccion` varchar(250) DEFAULT NULL,
  `email` varchar(125) DEFAULT NULL,
  `nombre_beneficiario` varchar(250) DEFAULT NULL,
  `dias_hanbiles` varchar(2) DEFAULT NULL,
  `fecha_limite` date DEFAULT NULL,
  `mensaje` varchar(2000) NOT NULL,
  `estado` varchar(2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_pqrs`
--

CREATE TABLE `tipo_pqrs` (
  `id` int(11) NOT NULL,
  `descripcion` varchar(120) NOT NULL,
  `estado` varchar(2) NOT NULL DEFAULT 'A',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `tipo_pqrs`
--

INSERT INTO `tipo_pqrs` (`id`, `descripcion`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'PETICION', 'A', '2020-10-23 13:30:48', '2020-10-23 13:30:48'),
(2, 'QUEJA', 'A', '2020-10-23 13:30:48', '2020-10-23 13:30:48'),
(3, 'RECLAMO', 'A', '2020-10-23 13:31:50', '2020-10-23 13:31:50'),
(4, 'SUGERENCIA', 'A', '2020-10-23 13:31:50', '2020-10-23 13:31:50'),
(5, 'FELICITACION', 'A', '2020-10-23 13:31:50', '2020-10-23 13:31:50'),
(6, 'OTRO', 'A', '2020-10-23 13:31:50', '2020-10-23 13:31:50');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trabajadores`
--

CREATE TABLE `trabajadores` (
  `id` int(11) NOT NULL,
  `identificacion` varchar(12) NOT NULL,
  `nombre_trabajador` varchar(125) NOT NULL,
  `cargo_trabajador` varchar(250) DEFAULT NULL,
  `email` varchar(125) DEFAULT NULL,
  `fecha_ingreso` date DEFAULT NULL,
  `fecha_retiro` date DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `salario` varchar(30) DEFAULT NULL,
  `lugar_trabajo` varchar(250) NOT NULL,
  `tipo_contrato` varchar(10) NOT NULL,
  `telefono` varchar(12) DEFAULT NULL,
  `direccion` varchar(125) DEFAULT NULL,
  `eps` varchar(100) DEFAULT NULL,
  `pension` varchar(100) DEFAULT NULL,
  `cesantias` varchar(100) DEFAULT NULL,
  `estado` varchar(2) DEFAULT 'A',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `identificacion` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo` tinyint(4) NOT NULL DEFAULT 1,
  `rol` tinyint(4) NOT NULL DEFAULT 0,
  `nombres` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` tinyint(4) NOT NULL DEFAULT 1,
  `remember_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `identificacion`, `tipo`, `rol`, `nombres`, `email`, `password`, `telefono`, `estado`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, NULL, 1, 1, 'NILTON JAIRO HOYOS GOMEZ', 'niltonjairo2000@gmail.com', '$2y$10$zFL8KnWYYZvWQQ1SSBpXtOBZiV34CD65xeP88RQ3EJvlU8J8i9HYa', '3173531171', 1, NULL, NULL, NULL),
(2, '1086359747', 1, 0, 'Jose Camilo Zamora Gomez', 'jczamorago@hotmail.com', '$2y$10$a.UvxmnTBH.kx6Y4aaV4WOqRnc.YwAg7MkkLvqcX6Vrdmldp60hTO', '11111111', 1, 'G4uImN97WwWIMnkpHITeCLlvYYQPEFImoIyRYZHxano4KSGpuiDxf4ZKbOD1', NULL, NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `registro_pqrs`
--
ALTER TABLE `registro_pqrs`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `trabajadores`
--
ALTER TABLE `trabajadores`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `registro_pqrs`
--
ALTER TABLE `registro_pqrs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `trabajadores`
--
ALTER TABLE `trabajadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
