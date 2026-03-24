-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 30-11-2020 a las 21:12:47
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
-- Estructura de tabla para la tabla `historial_programas`
--

CREATE TABLE `historial_programas` (
  `id` int(11) NOT NULL,
  `fecha` date NOT NULL,
  `usuario_responsable` varchar(250) NOT NULL,
  `id_empleado` int(11) NOT NULL,
  `id_programa` int(11) NOT NULL,
  `desc_programa` varchar(250) NOT NULL,
  `id_contrato` int(11) NOT NULL,
  `desc_contrato` varchar(250) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `salario` varchar(15) NOT NULL,
  `observaciones` varchar(350) NOT NULL,
  `estado` varchar(2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `historial_programas`
--

INSERT INTO `historial_programas` (`id`, `fecha`, `usuario_responsable`, `id_empleado`, `id_programa`, `desc_programa`, `id_contrato`, `desc_contrato`, `fecha_inicio`, `fecha_fin`, `salario`, `observaciones`, `estado`, `created_at`, `updated_at`) VALUES
(1, '2020-11-27', 'JOSE CAMILO ZAMORA GOMEZ', 26, 2, 'CDI CORAZON DE MARIA - SEDE 2', 2, 'LABORAL TIEMPO COMPLETO', '2020-10-27', '2020-10-25', '2500000', 'TERMINACION DE PROGRAMA', 'A', '2020-11-27 21:09:18', '2020-11-27 21:09:18'),
(2, '2020-11-27', 'JOSE CAMILO ZAMORA GOMEZ', 26, 8, 'UNICEF COMPONENTE DE EDUCACION', 3, 'LABORAL', '2020-10-25', '2020-10-29', '2500000', 'TERMINACION DE CONTRATO', 'A', '2020-11-27 21:10:00', '2020-11-27 21:10:00'),
(3, '2020-11-27', 'JOSE CAMILO ZAMORA GOMEZ', 26, 10, 'ADMINISTRACION', 4, 'CONTRATO CIVIL DE PRESTACION DE SERVICIOS', '2020-11-24', '2020-11-30', '1000000', 'RENUNCIO', 'A', '2020-11-27 21:41:32', '2020-11-27 21:41:32');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modalidad_pqrs`
--

CREATE TABLE `modalidad_pqrs` (
  `id` int(11) NOT NULL,
  `descripcion` varchar(120) NOT NULL,
  `estado` varchar(2) NOT NULL,
  `prefijo` varchar(2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `modalidad_pqrs`
--

INSERT INTO `modalidad_pqrs` (`id`, `descripcion`, `estado`, `prefijo`, `created_at`, `updated_at`) VALUES
(1, 'FISICO', 'A', 'FI', '2020-10-23 13:54:35', '2020-10-23 13:54:35'),
(2, 'VIA TELEFONICA', 'A', 'TE', '2020-10-23 13:54:35', '2020-10-23 13:54:35'),
(3, 'VIA WEB', 'A', 'WE', '2020-10-23 13:54:35', '2020-10-23 13:54:35'),
(4, 'REDES SOCIALES', 'A', 'RS', '2020-10-23 13:54:35', '2020-10-23 13:54:35'),
(5, 'OTROS', 'A', 'OT', '2020-10-23 13:54:35', '2020-10-23 13:54:35');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `novedades_nomina`
--

CREATE TABLE `novedades_nomina` (
  `id` int(11) NOT NULL,
  `identificacion` varchar(15) NOT NULL,
  `nombres` varchar(250) NOT NULL,
  `desc_programa` varchar(250) NOT NULL,
  `id_programa` int(11) NOT NULL,
  `id_tipo_novedad` int(11) NOT NULL,
  `desc_tipo_novedad` varchar(150) NOT NULL,
  `cargo` varchar(250) DEFAULT NULL,
  `fecha_ingreso` date NOT NULL,
  `fecha_salida` date NOT NULL,
  `fecha_novedad` date NOT NULL,
  `numero_dias` int(11) DEFAULT NULL,
  `contrato` varchar(15) DEFAULT NULL,
  `aprobado` varchar(2) NOT NULL,
  `salario` varchar(15) NOT NULL,
  `id_coordinador` int(11) DEFAULT NULL,
  `nombre_coordinador` varchar(125) DEFAULT NULL,
  `eps` varchar(125) DEFAULT NULL,
  `pension` varchar(125) DEFAULT NULL,
  `observaciones` varchar(1000) DEFAULT NULL,
  `responsable` varchar(125) NOT NULL,
  `id_usuario_responsable` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `opciones_sistema`
--

CREATE TABLE `opciones_sistema` (
  `id` int(11) NOT NULL,
  `opcion` varchar(250) NOT NULL,
  `estado` varchar(2) NOT NULL DEFAULT 'A',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `opciones_sistema`
--

INSERT INTO `opciones_sistema` (`id`, `opcion`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'TALENTO HUMANO', 'A', '2020-11-20 13:19:17', '2020-11-20 13:19:17'),
(2, 'PQRS', 'A', '2020-11-20 13:19:17', '2020-11-20 13:19:17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `programa`
--

CREATE TABLE `programa` (
  `id` int(11) NOT NULL,
  `descripcion` varchar(250) NOT NULL,
  `responsable` int(11) NOT NULL,
  `estado` varchar(2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `programa`
--

INSERT INTO `programa` (`id`, `descripcion`, `responsable`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'CDI CORAZON DE MARIA - SEDE 1', 1, 'A', '2020-10-26 17:44:47', '2020-10-26 17:44:47'),
(2, 'CDI CORAZON DE MARIA - SEDE 2', 1, 'A', '2020-10-26 17:44:47', '2020-10-26 17:44:47'),
(3, 'ESCUELA CORAZON DE MARIA', 1, 'A', '2020-10-26 17:44:47', '2020-10-26 17:44:47'),
(4, 'MODALIDAD: INTERVENCION DE APOYO - APOYO PSICOSOCIAL - VULNERACION', 1, 'A', '2020-10-26 17:44:47', '2020-10-26 17:44:47'),
(5, 'MODALIDAD: INTERVENCION DE APOYO - APOYO PSICOSOCIAL - APOYO PSICOSOCIAL SITUACION DE VIDA EN CALLE', 1, 'A', '2020-10-26 17:44:47', '2020-10-26 17:44:47'),
(6, 'MODALIDAD: EXTERNADO MEDIA JORNADA - RESTABLECIMIENTO ADMINISTRACIÓN DE JUSTICIA', 1, 'A', '2020-10-26 17:44:47', '2020-10-26 17:44:47'),
(7, 'UNICEF COMPONENTE ESPACIO AMIGABLE', 1, 'A', '2020-10-26 17:44:47', '2020-10-26 17:44:47'),
(8, 'UNICEF COMPONENTE DE EDUCACION', 1, 'A', '2020-10-26 17:44:47', '2020-10-26 17:44:47'),
(9, 'PROGRAMA MUNDIAL DE ALIMENTOS', 1, 'A', '2020-10-26 17:44:47', '2020-10-26 17:44:47'),
(10, 'ADMINISTRACION', 1, 'A', '2020-10-27 19:53:31', '2020-10-27 19:53:31'),
(11, 'OTRO', 1, 'A', '2020-10-30 18:34:52', '2020-10-30 18:34:52'),
(12, 'NINGUNA', 1, 'A', '2020-11-23 17:31:14', '2020-11-23 17:31:14');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `registro_pqrs`
--

CREATE TABLE `registro_pqrs` (
  `id` int(11) NOT NULL,
  `id_tipo` int(11) NOT NULL,
  `codigo` varchar(12) DEFAULT NULL,
  `desc_tipo` varchar(50) NOT NULL,
  `id_modalidad` int(2) NOT NULL,
  `desc_modalidad` varchar(50) NOT NULL,
  `id_programa` int(11) DEFAULT NULL,
  `desc_programa` varchar(250) DEFAULT NULL,
  `radicado` varchar(3) DEFAULT NULL,
  `fecha_radicado` date NOT NULL,
  `programa` varchar(250) DEFAULT NULL,
  `remitente` varchar(250) DEFAULT NULL,
  `telefono` varchar(12) DEFAULT NULL,
  `direccion` varchar(250) DEFAULT NULL,
  `email` varchar(125) DEFAULT NULL,
  `nombre_beneficiario` varchar(250) DEFAULT NULL,
  `dias_habiles` varchar(2) DEFAULT NULL,
  `fecha_limite` date DEFAULT NULL,
  `ciudad` varchar(100) DEFAULT NULL,
  `mensaje` varchar(2000) NOT NULL,
  `estado` varchar(2) NOT NULL,
  `estado_solicitud` varchar(2) DEFAULT NULL,
  `url_file` varchar(500) DEFAULT NULL,
  `id_usurio_resp` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `registro_pqrs`
--

INSERT INTO `registro_pqrs` (`id`, `id_tipo`, `codigo`, `desc_tipo`, `id_modalidad`, `desc_modalidad`, `id_programa`, `desc_programa`, `radicado`, `fecha_radicado`, `programa`, `remitente`, `telefono`, `direccion`, `email`, `nombre_beneficiario`, `dias_habiles`, `fecha_limite`, `ciudad`, `mensaje`, `estado`, `estado_solicitud`, `url_file`, `id_usurio_resp`, `created_at`, `updated_at`) VALUES
(28, 1, 'PE-FI-28', 'PETICION', 1, 'FISICO', 6, 'MODALIDAD: EXTERNADO MEDIA JORNADA - RESTABLECIMIENTO ADMINISTRACIÓN DE JUSTICIA', NULL, '2020-10-13', NULL, 'JUAN CAMILO PEREZ', '3142785454', 'calle 20 # 10 45', 'juan@gmail.com', '', '5', '2020-10-18', 'Pasto', 'PETICION DE PRUEBA PARA EL MANEJO Y VER EL CODIGO COMO ESTA FUNCIONANADO', '1', '2', '0', NULL, '2020-11-06 00:19:55', '2020-11-10 02:07:22'),
(29, 1, 'PE-TE-29', 'PETICION', 2, 'VIA TELEFONICA', 9, 'PROGRAMA MUNDIAL DE ALIMENTOS', NULL, '2020-11-05', NULL, 'CRISTIANO RONALDO', '2222222222', 'calle falsa', 'cr7@hotmail.com', '', '5', '2020-11-10', 'Pasto', 'ESTA PETICION SE CREA CON EL FIN DE DAR A CONOCER LOS DIFERENTES CODIGOS QUE SE CREAN EN LA PQRST DDDDDD', '1', '2', NULL, NULL, '2020-11-06 00:23:33', '2020-11-13 19:23:47'),
(30, 1, 'PE-WE-30', 'PETICION', 3, 'VIA WEB', 7, 'UNICEF COMPONENTE ESPACIO AMIGABLE', NULL, '2020-11-05', NULL, 'LIONEL MESSI', '858547474', 'calle 20', 'mesi@hotmail.com', 'ssssssssss', '5', '2020-11-10', 'Pasto', 'PETICION DE LA WEB PARA VALIDAR LOS CODIGOS', '2', '2', '0', NULL, '2020-11-06 00:25:15', '2020-11-13 18:41:21'),
(32, 1, 'PE-FI-32', 'PETICION', 1, 'FISICO', 9, 'PROGRAMA MUNDIAL DE ALIMENTOS', NULL, '2020-11-03', NULL, 'HOMERO SIMPSON', '314521141', 'Avenida siempre viva', 'carlitos@gmail.com', 'Barth Simpson', '5', '2020-11-08', 'Sprinfield', 'PETICION DE PRUEBA PARA VALIDAR EL NOMBRE DEL ARCHIVO', '1', '3', '0', NULL, '2020-11-07 01:07:37', '2020-11-18 18:31:35'),
(34, 2, 'QU-WE-34', 'QUEJA', 3, 'VIA WEB', 1, 'CDI CORAZÓN DE MARÍA - SEDE 1', NULL, '2020-11-06', NULL, 'TG', 'tg', 'tg', 'tg', 'tg', '5', '2020-11-11', 'tg', 'TG', '2', '2', '0', NULL, '2020-11-07 01:13:52', '2020-11-19 18:43:24'),
(37, 4, 'SU-FI-37', 'SUGERENCIA', 1, 'FISICO', 1, 'CDI CORAZÓN DE MARÍA - SEDE 1', NULL, '2020-11-07', NULL, 'CAMILO DAZA', '1234566', 'calle 39 barrio 2', 'pepe@hotmail.com', 'Pepito', '5', '2020-11-12', 'Pasto', 'SUGERENCIA PAR PRUEBAS', '1', '1', 'file_44_886.pdf', NULL, '2020-11-07 21:20:42', '2020-11-11 19:23:29'),
(38, 3, 'RE-FI-38', 'RECLAMO', 1, 'FISICO', 3, 'ESCUELA CORAZÓN DE MARÍA', NULL, '2020-11-09', NULL, 'NY', 'ny', 'ny', 'yn', 'ny', '5', '2020-11-14', 'ny', 'XASSDFSDFSDFSDF', '1', '2', 'file_44_886.pdf', NULL, '2020-11-09 21:28:22', '2020-11-18 23:48:21'),
(39, 2, 'QU-TE-39', 'QUEJA', 2, 'VIA TELEFONICA', 5, 'MODALIDAD: INTERVENCIÓN DE APOYO - APOYO PSICOSOCIAL - APOYO PSICOSOCIAL SITUACIÓN DE VIDA EN CALLE', NULL, '2020-11-09', NULL, 'SAFDSDFSD', 'sdfsdfs', 'sdfsdf', 'fdsdf', 'sdfsdf', '5', '2020-11-14', 'sdf', 'SDFSDFSDFSDF', '1', '1', '0', NULL, '2020-11-09 21:51:52', '2020-11-09 21:51:52'),
(40, 2, 'QU-TE-40', 'QUEJA', 2, 'VIA TELEFONICA', 5, 'MODALIDAD: INTERVENCIÓN DE APOYO - APOYO PSICOSOCIAL - APOYO PSICOSOCIAL SITUACIÓN DE VIDA EN CALLE', NULL, '2020-11-09', NULL, 'SAFDSDFSD', 'sdfsdfs', 'sdfsdf', 'fdsdf', 'sdfsdf', '5', '2020-11-14', 'sdf', 'SDFSDFSDFSDF', '1', '1', 'file_44_886.pdf', NULL, '2020-11-09 21:58:01', '2020-11-09 21:58:01'),
(41, 3, 'RE-WE-41', 'RECLAMO', 3, 'VIA WEB', 2, 'CDI CORAZÓN DE MARÍA - SEDE 2', NULL, '2020-11-09', NULL, 'QQQ', 'qqq', 'qqqqqqqqqq', 'qqqqqqqqqqq', 'qqqqqqqqqq', '5', '2020-11-14', 'qqqqqqqqqq', 'QQQQQQQQQQQQQ', '1', '1', NULL, NULL, '2020-11-09 21:59:54', '2020-11-09 21:59:54'),
(44, 2, 'QU-TE-44', 'QUEJA', 2, 'VIA TELEFONICA', 2, 'CDI CORAZÓN DE MARÍA - SEDE 2', NULL, '2020-11-09', NULL, 'HOLA', '123545', 'hola', 'hola', 'hola', '5', '2020-11-14', 'pasto', 'HOLA', '1', '1', 'file_44_886.pdf', NULL, '2020-11-10 00:16:56', '2020-11-10 00:16:57'),
(46, 4, 'SU-FI-46', 'SUGERENCIA', 1, 'FISICO', 2, 'CDI CORAZÓN DE MARÍA - SEDE 2', NULL, '2020-11-09', NULL, 'QQECQW', 'cqew', 'qcwe', 'cqew', 'cqwe', '5', '2020-11-14', 'cqwe', 'CQWEQWEQWECQWE', '1', '1', NULL, NULL, '2020-11-10 00:27:39', '2020-11-10 00:27:39'),
(48, 4, 'SU-FI-48', 'SUGERENCIA', 1, 'FISICO', 1, 'CDI CORAZÓN DE MARÍA - SEDE 1', NULL, '2020-11-09', NULL, 'SAD', 'asd', 'ewc', 'asdas@hotmail.comsv', 'cew', '5', '2020-11-14', 'ecw', 'WECCEEW', '1', '1', 'file_48_478.pdf', NULL, '2020-11-10 00:32:08', '2020-11-10 00:32:08'),
(49, 4, 'SU-FI-49', 'SUGERENCIA', 1, 'FISICO', 1, 'CDI CORAZÓN DE MARÍA - SEDE 1', NULL, '2020-11-09', NULL, 'LUCHO DIAZ', 'asd', 'ewc', 'asdas@hotmail.comsv', 'cew', '5', '2020-11-14', 'ecw', 'WECCEEW', '1', '1', 'file_49_773.pdf', NULL, '2020-11-10 00:33:53', '2020-11-11 19:23:46'),
(50, 5, 'FE-WE-50', 'FELICITACION', 3, 'VIA WEB', 9, 'PROGRAMA MUNDIAL DE ALIMENTOS', NULL, '2020-11-11', NULL, 'PEDRO ALEJANDRO DIAZ', '314724525220', 'aaaaaa', 'aaaaaaaaaaaaaa', 'aaaaaa', '0', '2020-11-11', 'aaaaa', 'FELICITACIO A LA FUNDACION POR SU EXELENTE LABOR', '1', '2', NULL, NULL, '2020-11-11 19:11:20', '2020-11-11 19:11:21'),
(51, 2, 'QU-WE-51', 'QUEJA', 3, 'VIA WEB', 3, 'ESCUELA CORAZÓN DE MARÍA', NULL, '2020-10-13', NULL, 'JUAN DIEGO MARIN', '3252012141', 'calle 39 barrio 2', 'juan@gmail.com', 'carlitos Juarez', '5', '2020-10-18', 'EL TAMBO', 'PRUEBA DE DIAS PARA EL MES DE OCTUBRE', '1', '2', NULL, NULL, '2020-11-12 19:13:24', '2020-11-12 19:14:06'),
(52, 3, 'RE-TE-52', 'RECLAMO', 2, 'VIA TELEFONICA', 1, 'CDI CORAZÓN DE MARÍA - SEDE 1', NULL, '2020-11-13', NULL, 'JAMES RODRIGUEZ', '3125211141', 'no me acuerdo', 'j10@hotmail.com', 'Leonel mesi', '5', '2020-11-18', 'Lejos', 'ESTA ES UNA PETICION DE PRUEBA', '1', '1', NULL, NULL, '2020-11-13 18:42:41', '2020-11-13 18:42:41'),
(53, 3, 'RE-FI-53', 'RECLAMO', 1, 'FISICO', 1, 'CDI CORAZÓN DE MARÍA - SEDE 1', NULL, '2020-11-18', NULL, 'CAMILO ZAMORA', '31427441214', 'calle 20', 'cami@hotmail.com', '', '5', '2020-11-23', 'Pasto', 'PRUEBA PARA VALIDACION DE ARCHIVOS', '1', '1', 'file_53_959.pdf', NULL, '2020-11-18 18:45:34', '2020-11-18 18:45:35'),
(54, 2, 'QU-FI-54', 'QUEJA', 1, 'FISICO', 4, 'MODALIDAD: INTERVENCIÓN DE APOYO - APOYO PSICOSOCIAL - VULNERACIÓN', NULL, '2020-11-18', NULL, 'FERNANDO', '3182541141', 'qqq', 'jczamorago@hotmail.com', 'qqq', '5', '2020-11-23', 'qqq', 'QQQQ', '1', '1', 'file_54_410.pdf', NULL, '2020-11-18 19:15:41', '2020-11-18 19:15:42'),
(55, 1, 'PE-FI-55', 'PETICION', 1, 'FISICO', 8, 'UNICEF COMPONENTE DE EDUCACIÓN', NULL, '2020-11-18', NULL, 'ADRIANA', '12365847', 'calle 20 # 10 45', 'jzamora@hotmail.com', 'aaaa', '5', '2020-11-23', 'Pasto', 'MESNSAJE PRUEBA DE MENSAJE PARA EDITAR', '1', '3', 'file_55_664.pdf', NULL, '2020-11-18 19:26:53', '2020-11-18 21:32:34'),
(56, 1, 'PE-FI-56', 'PETICION', 1, 'FISICO', 1, 'CDI CORAZÓN DE MARÍA - SEDE 1', NULL, '2020-11-18', NULL, 'SSA', 'sas', 'sas', 'ass', 'sas', '5', '2020-11-23', 'sas', 'SASASASASAS', '1', '3', 'file_56_380.png', NULL, '2020-11-18 23:42:41', '2020-11-19 01:41:17'),
(57, 3, 'RE-TE-57', 'RECLAMO', 2, 'VIA TELEFONICA', 4, 'MODALIDAD: INTERVENCIÓN DE APOYO - APOYO PSICOSOCIAL - VULNERACIÓN', NULL, '2020-11-18', NULL, 'SADDA', 'asdasd', 'asda', 'asd', 'sdasdasd', '5', '2020-11-23', 'asdasd', 'ASDASDASD', '1', '1', 'file_57_354.docx', NULL, '2020-11-18 23:45:51', '2020-11-18 23:45:51'),
(58, 2, 'QU-WE-58', 'QUEJA', 3, 'VIA WEB', 1, 'CDI CORAZÓN DE MARÍA - SEDE 1', NULL, '2020-11-20', NULL, 'VBN', 'nbv', 'nv', 'nbv', 'nbv', '5', '2020-11-25', 'vnb', 'NVBNVBNVBN', '2', '1', 'file_58_26.jpeg', 2, '2020-11-21 01:00:18', '2020-11-21 01:00:25'),
(59, 4, 'SU-FI-59', 'SUGERENCIA', 1, 'FISICO', 2, 'CDI CORAZON DE MARIA - SEDE 2', NULL, '2020-11-27', NULL, 'QWEQWE', 'qweqwe', 'qweqwe', 'qweqwe', 'qweqwe', '5', '2020-12-02', 'qweqwe', 'QWEQWEQWEQWEQWEQE', '1', '1', NULL, 2, '2020-11-27 21:47:21', '2020-11-27 21:47:21'),
(60, 1, 'PE-FI-60', 'PETICION', 1, 'FISICO', 1, 'CDI CORAZON DE MARIA - SEDE 1', NULL, '2020-11-27', NULL, 'CAMILO', '', '', '', '', '5', '2020-12-02', '', 'PRUEBA DE ARCHIVO', '1', '1', 'file_60_354.pdf', 2, '2020-11-27 21:53:08', '2020-11-27 21:57:38'),
(61, 1, 'PE-FI-61', 'PETICION', 1, 'FISICO', 1, 'CDI CORAZON DE MARIA - SEDE 1', NULL, '2020-11-27', NULL, 'JUAN', '', '', '', '', '5', '2020-12-02', '', 'AAAAAAAAA', '1', '1', 'file_61_614.pdf', 2, '2020-11-27 21:54:00', '2020-11-27 21:54:02');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `respuestas_pqrs`
--

CREATE TABLE `respuestas_pqrs` (
  `id` int(11) NOT NULL,
  `id_pqrs` int(11) NOT NULL,
  `codigo_pqrs` varchar(25) DEFAULT NULL,
  `responsable` varchar(125) DEFAULT NULL,
  `fecha_respuesta` date NOT NULL,
  `observaciones` varchar(2000) DEFAULT NULL,
  `url_file` varchar(250) DEFAULT NULL,
  `id_solicitud` int(11) NOT NULL,
  `tipo_solicitud` varchar(20) NOT NULL,
  `modalidad` varchar(20) NOT NULL,
  `estado` varchar(2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `respuestas_pqrs`
--

INSERT INTO `respuestas_pqrs` (`id`, `id_pqrs`, `codigo_pqrs`, `responsable`, `fecha_respuesta`, `observaciones`, `url_file`, `id_solicitud`, `tipo_solicitud`, `modalidad`, `estado`, `created_at`, `updated_at`) VALUES
(1, 28, 'PE-FE-28', 'CAMILO ZAMORA', '2020-10-09', 'PRUEBA 525112000', NULL, 1, 'PETICION', 'FISICO', 'A', '2020-11-10 02:07:22', '2020-11-10 02:07:22'),
(5, 38, 'RE-FI-38', 'JOSE DANIL', '2020-11-11', 'ESTA QUEJA SE RESOLVIO', NULL, 3, 'RECLAMO', 'FISICO', 'A', '2020-11-11 19:05:20', '2020-11-11 19:05:20'),
(6, 51, 'QE-VW-51', 'CAMILO ZAMORA', '2020-11-12', 'SOLUCION DE QUEJA', NULL, 2, 'QUEJA', 'VIA WEB', 'A', '2020-11-12 19:14:06', '2020-11-12 19:14:06'),
(10, 30, 'PE-WE-30', 'CAMILO ZAMORA', '2020-11-13', 'SE RESOLVIO LA PETICION AL USUARIOS CON EL LLAMADO DE ATENCIO A LOS USUARIOS', NULL, 1, 'PETICION', 'VIA WEB', 'A', '2020-11-13 18:41:21', '2020-11-13 18:41:21'),
(11, 29, 'PE-TE-29', 'PEPITO PEREZ ZAMORA', '2020-11-13', 'RESPUESTA POSITIVA SE REOLVIO', NULL, 1, 'PETICION', 'VIA TELEFONICA', 'A', '2020-11-13 19:01:35', '2020-11-13 19:23:47'),
(12, 32, 'PE-FI-32', 'JUAN DAVID', '2020-11-18', 'EN PROCESO DE RESPUESTA', NULL, 1, 'PETICION', 'FISICO', 'A', '2020-11-18 18:31:33', '2020-11-18 18:31:33'),
(14, 55, 'PE-FI-55', 'JUAN LUIS PEPETIO', '2020-11-18', 'RESPUESTA', 'file_14_342.pdf', 1, 'PETICION', 'FISICO', 'A', '2020-11-18 23:40:53', '2020-11-18 23:40:54'),
(15, 38, 'RE-FI-38', 'AAAA', '2020-11-18', 'AAAAAAAAAAA', 'file_15_402.pdf', 3, 'RECLAMO', 'FISICO', 'A', '2020-11-18 23:48:21', '2020-11-18 23:48:21'),
(17, 56, 'PE-FI-56', 'JUAN DIEGO ALVIRA', '2020-11-18', 'PARA EDICION SI ARCHIVO', NULL, 1, 'PETICION', 'FISICO', 'A', '2020-11-19 01:41:17', '2020-11-19 01:41:17'),
(18, 34, 'QU-WE-34', 'JUAN PACO PREDRO DE LA MAR', '2020-11-19', 'SOLICITUD AUN EN PROCESO DE RESPUESTA 123', 'file_18_180.png', 2, 'QUEJA', 'VIA WEB', 'A', '2020-11-19 18:35:20', '2020-11-19 18:42:45');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_contratos`
--

CREATE TABLE `tipo_contratos` (
  `id` int(11) NOT NULL,
  `descripcion` varchar(120) NOT NULL,
  `estado` varchar(2) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `tipo_contratos`
--

INSERT INTO `tipo_contratos` (`id`, `descripcion`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'LABORAL', 'A', '2020-11-10 14:41:44', '2020-11-10 14:41:44'),
(2, 'LABORAL TIEMPO COMPLETO', 'A', '2020-11-10 14:41:44', '2020-11-10 14:41:44'),
(3, 'LABORAL MEDIO TIEMPO', 'A', '2020-11-10 14:41:44', '2020-11-10 14:41:44'),
(4, 'CONTRATO CIVIL DE PRESTACION DE SERVICIOS', 'A', '2020-11-10 14:41:44', '2020-11-10 14:41:44'),
(5, 'JORNADA INCOMPLETA', 'A', '2020-11-25 16:41:24', '2020-11-25 16:41:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_novedad`
--

CREATE TABLE `tipo_novedad` (
  `id` int(11) NOT NULL,
  `descripcion` varchar(250) NOT NULL,
  `estado` varchar(2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `tipo_novedad`
--

INSERT INTO `tipo_novedad` (`id`, `descripcion`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'NUEVO RESGISTRO', 'A', '2020-11-30 14:09:26', '2020-11-30 14:09:26'),
(2, 'RENUNCIA', 'A', '2020-11-30 14:09:26', '2020-11-30 14:09:26'),
(3, 'REMPLAZO', 'A', '2020-11-30 14:09:26', '2020-11-30 14:09:26'),
(4, 'ROTACION', 'A', '2020-11-30 14:09:26', '2020-11-30 14:09:26');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_pqrs`
--

CREATE TABLE `tipo_pqrs` (
  `id` int(11) NOT NULL,
  `descripcion` varchar(120) NOT NULL,
  `dias_habiles_respuesta` int(11) NOT NULL,
  `estado` varchar(2) NOT NULL DEFAULT 'A',
  `prefijo` varchar(2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `tipo_pqrs`
--

INSERT INTO `tipo_pqrs` (`id`, `descripcion`, `dias_habiles_respuesta`, `estado`, `prefijo`, `created_at`, `updated_at`) VALUES
(1, 'PETICION', 5, 'A', 'PE', '2020-10-23 13:30:48', '2020-10-23 13:30:48'),
(2, 'QUEJA', 5, 'A', 'QU', '2020-10-23 13:30:48', '2020-10-23 13:30:48'),
(3, 'RECLAMO', 5, 'A', 'RE', '2020-10-23 13:31:50', '2020-10-23 13:31:50'),
(4, 'SUGERENCIA', 5, 'A', 'SU', '2020-10-23 13:31:50', '2020-10-23 13:31:50'),
(5, 'FELICITACION', 0, 'A', 'FE', '2020-10-23 13:31:50', '2020-10-23 13:31:50'),
(6, 'OTRO', 5, 'A', 'OT', '2020-10-23 13:31:50', '2020-10-23 13:31:50');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_usuario`
--

CREATE TABLE `tipo_usuario` (
  `id` int(11) NOT NULL,
  `descripcion` varchar(125) NOT NULL,
  `estado` varchar(2) NOT NULL DEFAULT 'A',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `tipo_usuario`
--

INSERT INTO `tipo_usuario` (`id`, `descripcion`, `estado`, `created_at`, `updated_at`) VALUES
(1, 'ADMINISTRADOR DEL SISTEMA', 'A', '2020-11-19 19:26:23', '2020-11-19 19:26:23'),
(2, 'ADMINISTRATIVO', 'A', '2020-11-19 19:26:23', '2020-11-19 19:26:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trabajadores`
--

CREATE TABLE `trabajadores` (
  `id` int(11) NOT NULL,
  `identificacion` varchar(12) NOT NULL,
  `lugar_expedicion` varchar(125) DEFAULT NULL,
  `nombre_trabajador` varchar(125) NOT NULL,
  `cargo_trabajador` varchar(250) DEFAULT NULL,
  `programas` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`programas`)),
  `email` varchar(125) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `salario` varchar(30) DEFAULT NULL,
  `telefono` varchar(12) DEFAULT NULL,
  `direccion` varchar(125) DEFAULT NULL,
  `eps` varchar(100) DEFAULT NULL,
  `pension` varchar(100) DEFAULT NULL,
  `cesantias` varchar(100) DEFAULT NULL,
  `arl` varchar(125) DEFAULT NULL,
  `estado` varchar(2) DEFAULT 'A',
  `caja` varchar(125) DEFAULT NULL,
  `activo` varchar(2) NOT NULL,
  `coordinador` varchar(2) DEFAULT NULL,
  `personal_coordinador` int(11) DEFAULT NULL,
  `observaciones` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `trabajadores`
--

INSERT INTO `trabajadores` (`id`, `identificacion`, `lugar_expedicion`, `nombre_trabajador`, `cargo_trabajador`, `programas`, `email`, `fecha_nacimiento`, `salario`, `telefono`, `direccion`, `eps`, `pension`, `cesantias`, `arl`, `estado`, `caja`, `activo`, `coordinador`, `personal_coordinador`, `observaciones`, `created_at`, `updated_at`) VALUES
(1, '12975044', 'PASTO', 'ALIRIO QUENAN', 'SERVICIOS GENERALES', '[{\"id\":\"1\",\"desc_programa\":\"CDI CORAZON DE MARIA - SEDE 1\",\"id_cto\":\"1\",\"desc_contrato\":\"LABORAL\",\"fecha_inicio\":\"2020-09-01\",\"fecha_fin\":\"2021-01-02\",\"porcentaje\":\"100\",\"salario\":\"1800000\"}]', NULL, '1960-09-17', '1800000', '3128619858', NULL, 'NUEVA EPS', 'COLPENSIONES', NULL, NULL, 'A', 'COMFAMILIAR', 'SI', 'NO', 5, '', '2020-11-10 05:00:00', '2020-11-27 18:20:40'),
(2, '59817649', 'PASTO', 'CARMEN ALICIA CABRERA BOTINA', 'DOCENTE', '[{\"id\":\"1\",\"desc_programa\":\"CDI CORAZON DE MARIA - SEDE 1\",\"id_cto\":\"2\",\"desc_contrato\":\"LABORAL TIEMPO COMPLETO\",\"fecha_inicio\":\"2020-10-04\",\"fecha_fin\":\"2020-11-01\",\"porcentaje\":\"100\",\"salario\":\"150000\"}]', NULL, '1972-04-26', '150000', '3154579622', NULL, 'MEDIMAS', 'COLPENSIONES', NULL, NULL, 'A', 'COMFAMILIAR', 'SI', 'NO', 5, '', '2020-11-10 05:00:00', '2020-11-27 18:24:24'),
(3, '36950252', 'PASTO', 'CARMENZA JANETH NARVAEZ BENAVIDES', 'MANIPULADORA DE ALIMENTOS', '[]', '', '1969-01-14', '877803', '3125567454', '', 'EPS SANITAS', 'PROTECCION', '', NULL, 'A', 'COMFAMILIAR', 'SI', 'NO', 5, NULL, '2020-11-10 05:00:00', '2020-11-10 05:00:00'),
(4, '1085277593', 'PASTO', 'DANIELA ALEJANDRA SANTACRUZ RINCON', 'AUXILIAR PEDAGOGICA', '[]', '', '1989-04-21', '877803', '3172438964', '', 'EMSSANAR', 'PORVENIR', '', NULL, 'A', 'COMFAMILIAR', 'SI', 'NO', 5, NULL, '2020-11-10 05:00:00', '2020-11-10 05:00:00'),
(5, '27082760', 'PASTO', 'ANA MILENA CHAMORRO CHAMORRO', 'COORDINADORA CDI SEDE 1', '[]', '', '1976-02-01', '1870820', '3154366570', '', 'SALUD COOP', 'PROTECCION', '', NULL, 'A', 'NO APLICA', 'SI', 'SI', 5, NULL, '2020-11-10 05:00:00', '2020-11-10 05:00:00'),
(6, '1085310290', 'PASTO', 'FRANCY ALEJANDRA RODRIGUEZ GONZALES', 'NUTRICIONISTA', '[]', '', '1994-02-08', '1926000', '3128307479', '', 'NUEVA EPS', 'PROTECCION', '', NULL, 'A', 'NO APLICA', 'SI', 'NO', 5, NULL, '2020-11-10 05:00:00', '2020-11-10 05:00:00'),
(7, '36952028', 'PASTO', 'JENNY PATRICIA VILLARREAL CEBALLOS', 'MANIPULADORA DE ALIMENTOS', '[]', '', '1980-09-16', '877803', '3174655458', '', 'SANITAS', 'COLPENSIONES', '', NULL, 'A', 'COMFAMILIAR', 'SI', 'NO', 5, NULL, '2020-11-10 05:00:00', '2020-11-10 05:00:00'),
(8, '37086813', 'PASTO', 'LEIDY JOHANNA DELGADO DELGADO', 'DOCENTE', '[]', '', '1985-06-02', '1167958', '3167748110', '', 'SANITAS', 'PROTECCION', '', NULL, 'A', 'COMFAMILIAR', 'SI', 'NO', 5, NULL, '2020-11-10 05:00:00', '2020-11-10 05:00:00'),
(9, '1085256623', 'PASTO', 'LIDA PATRICIA TULCAN MONTANCHEZ', 'AUXILIAR DE ENFERMERIA', '[]', '', '1986-07-05', '487514', '3205023247', '', 'MEDIMAS', 'PROTECCION', '', NULL, 'A', 'COMFAMILIAR', 'SI', 'NO', 5, NULL, '2020-11-10 05:00:00', '2020-11-10 05:00:00'),
(10, '30734201', 'PASTO', 'LIDIA YANETH BOLAÑOS LEGARDA', 'DOCENTE', '[]', '', '1966-05-14', '1167958', '3172110102', '', 'NUEVA EPS', 'COLPENSIONES ', '', NULL, 'A', 'COMFAMILIAR', 'SI', 'NO', 0, NULL, '2020-11-10 05:00:00', '2020-11-10 05:00:00'),
(11, '59314396', 'PASTO', 'MARIA CLAUDIA MESIAS ERAZO', 'PSICOLOGA CDI SEDE 1', '[]', '', '1984-11-06', '1348200', '3218545333', '', 'SANITAS', 'POSITIVA ', '', NULL, 'A', 'NO APLICA', 'SI', 'NO', 0, NULL, '2020-11-10 05:00:00', '2020-11-10 05:00:00'),
(12, '1085257919', 'PASTO', 'MARLY PATRICIA SALAS', 'AUXILIAR PEDAGOGICO ', '[]', '', '1987-02-01', '877803', '3177579859', '', 'SANITAS', 'PROTECCION', '', NULL, 'A', 'COMFAMILIAR', 'SI', 'NO', 0, NULL, '2020-11-10 05:00:00', '2020-11-10 05:00:00'),
(13, '1085261000', 'PASTO', 'MARY VIVIANA NANDAR PINCHAO', 'DOCENTE', '[]', '', '1984-04-29', '1167958', '3174646489', '', 'MEDIMAS', 'PROTECCION', '', NULL, 'A', 'COMFAMILIAR', 'SI', 'NO', 5, NULL, '2020-11-10 05:00:00', '2020-11-10 05:00:00'),
(14, '59821041', 'PASTO', 'MIRIAN DEL PILAR TABLA HERNANDEZ', 'DOCENTE', '[]', '', '1972-12-12', '1167958', '3118163198', '', 'NUEVA EPS', 'COLPENSIONES', '', NULL, 'A', 'COMFAMILIAR', 'SI', 'NO', 5, NULL, '2020-11-10 05:00:00', '2020-11-10 05:00:00'),
(15, '59831598', 'PASTO', 'NELBA ALEJANDRA MUÑOZ SUAREZ', 'DOCENTE PERFIL 2', '[]', '', '1976-01-30', '975028', '3156140954', '', 'SANITAS0', 'COLPENSIONES', '', NULL, 'A', 'COMFAMILIAR', 'SI', 'NO', 25, NULL, '2020-11-10 05:00:00', '2020-11-10 05:00:00'),
(16, '1085294302', 'PASTO', 'ROGER MAURICIO RODRIGUEZ ASCUNTAR', 'MANIPULADOR DE ALIMENTOS', '[]', '', '1991-12-11', '877803', '3173965867', '', 'NUEVA EPS', 'COLPENSIONES ', '', NULL, 'A', 'COMFAMILIAR', 'SI', 'NO', 25, NULL, '2020-11-10 05:00:00', '2020-11-10 05:00:00'),
(17, '36751948', 'PASTO', 'SONIA LILIANA DELGADO ORTEGA', 'SERVICIOS GENERALES', '[]', '', '1978-03-03', '877803', '3113687067', '', 'NUEVA EPS', 'PROTECCION ', '', NULL, 'A', 'COMFAMILIAR', 'SI', 'NO', 25, NULL, '2020-11-10 05:00:00', '2020-11-10 05:00:00'),
(18, '1085271002', 'PASTO', 'YULY CAROLINA DELGADO', 'AUXILIAR PEDAGOGICA', '[]', '', '1989-02-09', '877803', '3002802334', '', 'EMSSANAR', 'PORVENIR', '', NULL, 'A', 'COMFAMILIAR', 'SI', 'NO', 25, NULL, '2020-11-10 05:00:00', '2020-11-10 05:00:00'),
(19, '1085256163', 'PASTO', 'DAYRA AMANDA IPIALES SOLARTE', 'DOCENTE REMPLAZO DE MATERNIDAD  ', '[]', '', '1987-01-22', '1167958', '3207445412', '', 'MALLAMAS', 'COLPENSIONES', '', NULL, 'A', 'COMFAMILIAR', 'SI', 'NO', 25, NULL, '2020-11-10 05:00:00', '2020-11-10 05:00:00'),
(24, '1086359747', 'PASTO', 'JOSE CAMILO ZAMORA', 'GESTOR DE INFORMACION', '[{\"id\":\"1\",\"desc_programa\":\"CDI CORAZON DE MARIA - SEDE 1\",\"id_cto\":\"1\",\"desc_contrato\":\"LABORAL\",\"fecha_inicio\":\"2020-10-27\",\"fecha_fin\":\"2020-10-28\",\"porcentaje\":\"0\",\"salario\":\"2500000\"},{\"id\":\"8\",\"desc_programa\":\"UNICEF COMPONENTE DE EDUCACION\",\"id_cto\":\"3\",\"desc_contrato\":\"LABORAL MEDIO TIEMPO\",\"fecha_inicio\":\"2020-10-25\",\"fecha_fin\":\"2020-10-30\",\"porcentaje\":0,\"salario\":\"1500000\"}]', 'pepe@hotmail.com', '2020-10-31', '4000000', '3142752214', 'calle 39 barrio 2', 'Sanitas', 'Porvenir', 'porvenir', 'Colmena', 'A', 'Confamiliar', 'SI', 'NO', 25, 'PRUBAS DE LA ULTIMA VERSION', '2020-11-25 21:23:57', '2020-11-30 22:42:21'),
(25, '1086359747', 'PASTO', 'PEPITO', 'GESTOR DE INFORMACION', '[{\"id\":\"7\",\"desc_programa\":\"UNICEF COMPONENTE ESPACIO AMIGABLE\",\"id_cto\":\"3\",\"desc_contrato\":\"LABORAL\",\"fecha_inicio\":\"2020-10-25\",\"fecha_fin\":\"2020-10-26\",\"porcentaje\":\"0\",\"salario\":\"2500000\"}]', NULL, NULL, '2500000', '3142752214', NULL, NULL, NULL, NULL, NULL, 'A', NULL, 'SI', 'SI', 0, '', '2020-11-25 21:25:17', '2020-11-27 18:25:38'),
(26, '111212', 'PASTO', 'SAASDASD', 'ASDASDASD', '[]', NULL, NULL, '1000000', '312522214', NULL, NULL, NULL, NULL, NULL, 'A', NULL, 'NO', 'NO', 25, '', '2020-11-25 21:29:32', '2020-11-27 21:41:32'),
(27, '234234', 'PASTO', 'JUAN CAMILO', 'ASISTENTE DE TALENTO HUMANO', '[{\"id\":\"9\",\"desc_programa\":\"PROGRAMA MUNDIAL DE ALIMENTOS\",\"id_cto\":\"5\",\"desc_contrato\":\"LABORAL\",\"fecha_inicio\":\"2020-11-01\",\"fecha_fin\":\"2020-11-30\",\"porcentaje\":\"50\",\"salario\":\"850000\"}]', 'jzamora@funproinco.org', '1988-10-21', '3350000', '3142752214', 'calle 39 barrio 2', 'Sanitas', 'Porvenir', 'porvenir', 'Colmena', 'A', 'Confamiliar', 'SI', 'NO', 25, 'PRUEBA EDICION', '2020-11-25 22:09:39', '2020-11-27 21:09:18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `identificacion` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo` tinyint(4) NOT NULL DEFAULT 1,
  `rol` tinyint(4) NOT NULL DEFAULT 0,
  `sub_rol` int(11) DEFAULT NULL,
  `nombres` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'A',
  `remember_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `identificacion`, `tipo`, `rol`, `sub_rol`, `nombres`, `email`, `password`, `telefono`, `estado`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, '1055855471', 1, 1, NULL, 'NILTON JAIRO HOYOS GOMEZ', 'niltonjairo2000@gmail.com', '$2y$10$zFL8KnWYYZvWQQ1SSBpXtOBZiV34CD65xeP88RQ3EJvlU8J8i9HYa', '3173531171', 'A', NULL, NULL, NULL),
(2, '1086359747', 1, 1, 1, 'Jose Camilo Zamora Gomez', 'jczamorago@hotmail.com', '$2y$10$a.UvxmnTBH.kx6Y4aaV4WOqRnc.YwAg7MkkLvqcX6Vrdmldp60hTO', '11111111', 'A', 'u60iamtQOQ73ZjJg21f2YCwspN72ZhM2xGqFU1ZMrpTyBaYGxXxlOkz5rblI', NULL, NULL),
(3, '1234567890', 1, 1, NULL, 'JUAN PACO PEDRO DE LA MAR', 'juanpaco@gmail.com', '$2y$10$2guG5F/0beqCF7.ULkjhNeZeoI2QozrE47kMG5ZQxESvxmoZ0GHrS', '3147244308', 'A', 'Q7Qk9eYp8DIRdwbG8KGZRzv0cUBdVT8wQy6ov2c38OyyT6U54Xm7zPbnScYW', '2020-11-19 19:28:18', '2020-11-19 19:28:18'),
(4, '12525222', 1, 2, 2, 'JUAN EGO ALVIRA MEZA', 'juanego@hotmail.com', '$2y$10$QUlSsPjbUIvVL7MKAboljesO9q2itKMQer4dpUifXVU2BCGef.KLe', '3142752214', 'I', 'BStVa5fDkGInQCeKBbQbbOu7jZQnaKcxYh9OC5JHFL6neD6GWKrUAmMmFt7D', '2020-11-20 21:04:08', '2020-11-20 21:38:51');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `historial_programas`
--
ALTER TABLE `historial_programas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `modalidad_pqrs`
--
ALTER TABLE `modalidad_pqrs`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `novedades_nomina`
--
ALTER TABLE `novedades_nomina`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `opciones_sistema`
--
ALTER TABLE `opciones_sistema`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `programa`
--
ALTER TABLE `programa`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `registro_pqrs`
--
ALTER TABLE `registro_pqrs`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `respuestas_pqrs`
--
ALTER TABLE `respuestas_pqrs`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tipo_contratos`
--
ALTER TABLE `tipo_contratos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tipo_novedad`
--
ALTER TABLE `tipo_novedad`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tipo_pqrs`
--
ALTER TABLE `tipo_pqrs`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tipo_usuario`
--
ALTER TABLE `tipo_usuario`
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
-- AUTO_INCREMENT de la tabla `historial_programas`
--
ALTER TABLE `historial_programas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `modalidad_pqrs`
--
ALTER TABLE `modalidad_pqrs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `novedades_nomina`
--
ALTER TABLE `novedades_nomina`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `opciones_sistema`
--
ALTER TABLE `opciones_sistema`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `programa`
--
ALTER TABLE `programa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `registro_pqrs`
--
ALTER TABLE `registro_pqrs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT de la tabla `respuestas_pqrs`
--
ALTER TABLE `respuestas_pqrs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `tipo_contratos`
--
ALTER TABLE `tipo_contratos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `tipo_novedad`
--
ALTER TABLE `tipo_novedad`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tipo_pqrs`
--
ALTER TABLE `tipo_pqrs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `tipo_usuario`
--
ALTER TABLE `tipo_usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `trabajadores`
--
ALTER TABLE `trabajadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
