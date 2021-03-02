-- phpMyAdmin SQL Dump
-- version 4.9.4
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 23-09-2020 a las 16:57:42
-- Versión del servidor: 5.7.26
-- Versión de PHP: 7.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `contabilidb`
--
CREATE DATABASE IF NOT EXISTS `contabilidb` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `contabilidb`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `backend_contenidos`
--

DROP TABLE IF EXISTS `backend_contenidos`;
CREATE TABLE IF NOT EXISTS `backend_contenidos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias` varchar(50) NOT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `controlador` varchar(50) NOT NULL,
  `metadata` text NOT NULL,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `parametros` int(11) NOT NULL DEFAULT '0',
  `en_menu` tinyint(1) NOT NULL DEFAULT '0',
  `orden` int(11) NOT NULL DEFAULT '999',
  `es_default` tinyint(1) NOT NULL DEFAULT '0',
  `esta_protegido` tinyint(1) NOT NULL DEFAULT '0',
  `perfiles` set('ADMIN','OPER') DEFAULT NULL COMMENT 'En qué perfiles de usuario aparece este contenido',
  `estado` enum('HAB','DES','ELI') NOT NULL DEFAULT 'HAB',
  `last_modif` datetime DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL COMMENT 'Describe de qué se trata este contenido',
  PRIMARY KEY (`id`),
  KEY `alias` (`alias`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `backend_contenidos`
--

INSERT INTO `backend_contenidos` (`id`, `alias`, `nombre`, `controlador`, `metadata`, `parent_id`, `parametros`, `en_menu`, `orden`, `es_default`, `esta_protegido`, `perfiles`, `estado`, `last_modif`, `descripcion`) VALUES
(1, '404', '$error_404,Error HTTP 404', '404', '{\"vista\":\"404\"}', 0, 0, 0, 1, 0, 0, '', 'HAB', NULL, 'Error 404 - Contenido no encontrado.'),
(2, 'login', '$login,Iniciar sesión', 'login', '{\r\n\"template\":\"login\",\r\n\"vista\":\"login\",\r\n\"css\":\"formularios\",\r\n\"js\":\"fmdRulo,checkforms.js\",\r\n\"hasmainmenu\":false,\r\n\"hassubmenu\":false,\r\n\"hascarrusel\":false\r\n}', 0, 0, 0, 2, 0, 0, '', 'HAB', NULL, 'Pantalla de logueo o ingreso al sistema.'),
(3, 'logout', '$logout,Cerrar sesión', 'logout', '', 0, 0, 0, 3, 0, 0, '', 'HAB', NULL, 'Cierra sesión del usuario.'),
(4, 'inicio', '$inicio,Libro Diario', 'pagina', '{\r\n\"vista\":\"inicio\",\r\n\"css\":\"daterangepicker,inicio\",\r\n\"js\":\"moment.min,moment_locale_es,daterangepicker,checkforms,inicio\",\r\n\"titulo\":\"Libro diario\",\r\n\"menutag\":\"Diario\",\r\n\"icon_class\":\"fa-power-off\",\r\n\"description\":\"Libro diario de la contabilidad personal\",\r\n\"tooltip\":\"Libro diario de la contabilidad personal\"\r\n}', 0, 0, 1, 4, 0, 1, 'ADMIN,OPER', 'HAB', NULL, 'Listado del libro diario de la contabilidad personal.'),
(5, 'cuentas', '$cuentas,Cuentas', '', '{\r\n\"icon_class\":\"fa-file-invoice-dollar\",\r\n\"tooltip\":\"Administración de cuentas\"\r\n}', 0, 0, 1, 5, 0, 1, 'ADMIN,OPER', 'HAB', NULL, 'Raíz de la administración de cuentas.'),
(6, 'listado', '$listado_cuentas,Listado de cuentas', 'pagina', '{\r\n\"vista\":\"cuentas/listado_cuentas\",\r\n\"js\":\"bootstrap-select,listados,modalBsLte\",\r\n\"css\":\"bootstrap-select\",\r\n\"menutag\":\"Listado\",\r\n\"titulo\":\"Listado de cuentas\",\r\n\"icon_class\":\"fa-tools\",\r\n\"tooltip\":\"Listado de cuentas\"\r\n}', 5, 0, 1, 6, 1, 1, NULL, 'HAB', '2020-04-01 16:43:32', 'Listado de las cuentas del libro diario.'),
(7, 'grupos', '$listado_grupos,Listado de grupos', 'pagina', '{\r\n\"vista\":\"cuentas/listado_grupos\",\r\n\"js\":\"bootstrap-select\",\r\n\"css\":\"bootstrap-select\",\r\n\"menutag\":\"Grupos\",\r\n\"titulo\":\"Listado de grupos de cuentas\",\r\n\"icon_class\":\"fa-layer-group\",\r\n\"tooltip\":\"Grupos de cuentas\"\r\n}', 5, 0, 1, 6, 1, 1, NULL, 'HAB', '2020-04-01 16:43:32', 'Listado de los grupos de cuentas del libro diario.'),
(8, 'miformulario', '$miformulario,Mi Formulario', 'pagina', '{\r\n\"vista\":\"formularios/miformulario\",\r\n\"css\":\"formularios\",\r\n\"js\":\"checkforms\",\r\n\"icon_class\":\"fas fa-poll-h\",\r\n\"tooltip\":\"Ejemplo de formulario\"\r\n\r\n}', 0, 0, 1, 7, 1, 1, NULL, 'HAB', '2020-04-07 11:03:06', 'Este es el formulario que hace cosas maravillosas.'),
(9, 'contenido', '$contenido,Contenido', '', '{\"icon_class\":\"fa-book\",\"tooltip\":\"Administración de contenido\"}', 0, 0, 1, 8, 0, 1, 'ADMIN,OPER', 'HAB', NULL, 'Raíz de administración de contenidos'),
(10, 'listacontenidos', '$listacontenidos,Lista de Contenido', 'pagina', '{\"vista\":\"contenidos/listado_contenidos\",\"js\":\"bootstrap-select\",\"css\":\"bootstrap-select\",\"menutag\":\"Listado Contenido\",\"titulo\":\"Listado de contenido\",\"icon_class\":\" fa-list-alt\",\"tooltip\":\"Listado de contenido\"}', 9, 0, 1, 9, 1, 1, NULL, 'HAB', NULL, 'Listado de contenidos'),
(11, 'ejemplo', '$ejemplo,Ejemplo de Contenido', 'pagina', '{\r\n\"vista\":\"ejemplo\",\r\n\"js\":\"bootstrap-select\",\r\n\"css\":\"bootstrap-select\",\r\n\"menutag\":\"Ejemplo\",\r\n\"icon_class\":\"fa-camera\",\r\n\"description\":\"Ejemplo de contenido\",\r\n\"tooltip\":\"Ejemplo de contenidos\"\r\n}', 0, 0, 1, 10, 0, 1, 'ADMIN,OPER', 'HAB', NULL, 'Ejemplo de contenidos para idiomas.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `backend_messages`
--

DROP TABLE IF EXISTS `backend_messages`;
CREATE TABLE IF NOT EXISTS `backend_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fechahora` datetime DEFAULT NULL,
  `from_id` int(11) DEFAULT NULL,
  `to_id` int(11) DEFAULT NULL,
  `grupo` set('USR','ADM','OWN','OPER','ADMIN','ALL') DEFAULT 'USR',
  `texto` text,
  `estado` int(11) DEFAULT NULL,
  `chain` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `from_id` (`from_id`),
  KEY `to_id` (`to_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `backend_messages_read`
--

DROP TABLE IF EXISTS `backend_messages_read`;
CREATE TABLE IF NOT EXISTS `backend_messages_read` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `msgid` int(11) DEFAULT NULL,
  `fechahora` datetime DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `msgid` (`msgid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `backend_perfiles`
--

DROP TABLE IF EXISTS `backend_perfiles`;
CREATE TABLE IF NOT EXISTS `backend_perfiles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias` varchar(6) NOT NULL,
  `nombre` varchar(64) DEFAULT NULL,
  `data` text,
  `usuario_id` int(11) DEFAULT NULL,
  `fechahora` datetime DEFAULT NULL,
  `estado` enum('HAB','DES','ELI') NOT NULL DEFAULT 'HAB',
  PRIMARY KEY (`id`),
  UNIQUE KEY `alias` (`alias`),
  KEY `nombre` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `backend_perfiles_contenidos`
--

DROP TABLE IF EXISTS `backend_perfiles_contenidos`;
CREATE TABLE IF NOT EXISTS `backend_perfiles_contenidos` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador único',
  `perfil_id` int(11) DEFAULT NULL COMMENT 'ID del perfil',
  `contenido_id` int(11) DEFAULT NULL COMMENT 'ID del contenido',
  `estado` enum('HAB','DES','ELI') DEFAULT 'HAB' COMMENT 'Estado de habilitación',
  `fechahora` datetime DEFAULT NULL COMMENT 'Fecha y hora del alta',
  `usuario_id` int(11) DEFAULT NULL COMMENT 'Usuario que dio de alta',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Los contenidos que se cargar para cada perfil';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `backend_permisosxusuarios`
--

DROP TABLE IF EXISTS `backend_permisosxusuarios`;
CREATE TABLE IF NOT EXISTS `backend_permisosxusuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `contenido_id` int(11) NOT NULL,
  `fechahora` datetime NOT NULL,
  `usralta_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `backend_recovery`
--

DROP TABLE IF EXISTS `backend_recovery`;
CREATE TABLE IF NOT EXISTS `backend_recovery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `code` varchar(32) NOT NULL,
  `fecha` datetime DEFAULT NULL,
  `estado` enum('HAB','DES','ELI') DEFAULT 'HAB',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `backend_sesiones`
--

DROP TABLE IF EXISTS `backend_sesiones`;
CREATE TABLE IF NOT EXISTS `backend_sesiones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario_id` int(11) NOT NULL,
  `fecha_hora` datetime DEFAULT NULL,
  `estado` int(1) NOT NULL DEFAULT '0',
  `navegador` varchar(255) NOT NULL,
  `idle` int(11) NOT NULL,
  `ip` varchar(32) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `usuario_id` (`usuario_id`),
  KEY `fecha_hora` (`fecha_hora`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `backend_sesiones`
--

INSERT INTO `backend_sesiones` (`id`, `usuario_id`, `fecha_hora`, `estado`, `navegador`, `idle`, `ip`) VALUES
(1, 1, '2020-04-01 11:48:36', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.74 Safari/537.36', 1585752967, '::1'),
(2, 1, '2020-04-01 15:16:54', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.74 Safari/537.36', 1585766226, '::1'),
(3, 1, '2020-04-01 16:10:04', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.74 Safari/537.36', 1585768204, '::1'),
(4, 1, '2020-04-01 16:10:39', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.74 Safari/537.36', 1585774653, '::1'),
(5, 1, '2020-04-02 07:47:24', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.74 Safari/537.36', 1585827137, '::1'),
(6, 1, '2020-04-03 07:54:27', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.74 Safari/537.36', 1585919707, '::1'),
(7, 1, '2020-04-07 11:05:50', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.88 Safari/537.36', 1586269839, '::1'),
(8, 1, '2020-05-06 05:31:31', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/81.0.4044.123 Safari/537.36', 1588753941, '127.0.0.1'),
(9, 1, '2020-05-29 10:20:28', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.75 Safari/537.36', 1590758473, '127.0.0.1'),
(10, 1, '2020-06-01 09:58:02', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.75 Safari/537.36', 1591016296, '127.0.0.1'),
(11, 1, '2020-09-12 09:18:20', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.106 Safari/537.36', 1599913455, '::1'),
(12, 1, '2020-09-12 11:21:20', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.106 Safari/537.36', 1599922326, '::1'),
(13, 1, '2020-09-12 11:53:11', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.106 Safari/537.36', 1599923083, '::1'),
(14, 1, '2020-09-13 18:31:12', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.106 Safari/537.36', 1600032672, '::1'),
(15, 1, '2020-09-14 05:48:45', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.106 Safari/537.36', 1600080989, '::1'),
(16, 1, '2020-09-14 09:15:58', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.106 Safari/537.36', 1600086295, '::1'),
(17, 1, '2020-09-14 09:27:13', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.106 Safari/537.36', 1600086434, '::1'),
(18, 1, '2020-09-14 10:01:13', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.106 Safari/537.36', 1600089018, '::1'),
(19, 1, '2020-09-14 10:14:54', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.106 Safari/537.36', 1600089484, '::1'),
(20, 1, '2020-09-14 10:19:59', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.106 Safari/537.36', 1600090061, '::1'),
(21, 1, '2020-09-16 11:07:10', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.106 Safari/537.36', 1600267632, '::1'),
(22, 1, '2020-09-23 07:51:20', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.106 Safari/537.36', 1600858799, '::1'),
(23, 1, '2020-09-23 07:59:59', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.106 Safari/537.36', 1600858799, '::1'),
(24, 1, '2020-09-23 11:17:56', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.106 Safari/537.36', 1600874953, '::1'),
(25, 1, '2020-09-23 12:30:26', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.106 Safari/537.36', 1600878740, '::1');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `backend_usuarios`
--

DROP TABLE IF EXISTS `backend_usuarios`;
CREATE TABLE IF NOT EXISTS `backend_usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `negocio_id` int(11) DEFAULT NULL,
  `marca_id` int(11) DEFAULT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `apellido` varchar(50) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `iniciales` varchar(5) DEFAULT NULL,
  `password` varchar(32) NOT NULL,
  `email` varchar(75) DEFAULT NULL,
  `nivel` enum('USR','ADM','OWN') NOT NULL DEFAULT 'USR',
  `perfil_id` int(11) DEFAULT NULL COMMENT 'Se refier al perfil de usuario',
  `opciones` text,
  `estado` enum('HAB','DES','ELI') NOT NULL DEFAULT 'HAB',
  `sucursal_id` int(11) DEFAULT NULL,
  `tel` varchar(25) DEFAULT NULL COMMENT 'Nro de teléfono para usar en la rec. de contraseña.	',
  `fecha_alta` datetime DEFAULT NULL,
  `fecha_modif` datetime DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `backend_usuarios`
--

INSERT INTO `backend_usuarios` (`id`, `negocio_id`, `marca_id`, `nombre`, `apellido`, `username`, `iniciales`, `password`, `email`, `nivel`, `perfil_id`, `opciones`, `estado`, `sucursal_id`, `tel`, `fecha_alta`, `fecha_modif`, `usuario_id`) VALUES
(1, NULL, NULL, 'Diego', 'Romero', 'driverop', 'DFR', 'd0b6e23dbcb4fc4fc471edbf370b6f9d', 'diego.romero@driverop.com', 'OWN', NULL, '{\r\n\"rpp\":\"25\",\r\n\"tsession\":3600\r\n}', 'HAB', NULL, '3446-623494', '2020-04-01 10:55:00', '2020-04-01 10:55:00', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `config_parametros`
--

DROP TABLE IF EXISTS `config_parametros`;
CREATE TABLE IF NOT EXISTS `config_parametros` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador único',
  `grupo_id` int(11) DEFAULT NULL COMMENT 'ID del grupo al que pertenece este parámetro',
  `nombre` varchar(64) DEFAULT NULL COMMENT 'Nombre de la variable',
  `valor` varchar(255) DEFAULT NULL COMMENT 'Valor de la variable',
  `tipo` enum('INT','FLOAT','STRING','BOOL') DEFAULT 'STRING' COMMENT 'Tipo de dato permitido para la variable',
  `estado` enum('HAB','DES','ELI') DEFAULT 'HAB' COMMENT 'Estado de habilitación',
  `permiso` set('ADMIN','OPER') DEFAULT '' COMMENT 'Indica qué rol debe tener el usuario que puede editar este valor',
  `descripcion` text COMMENT 'Descripción del uso de la variable',
  `fechahora_modif` datetime DEFAULT NULL COMMENT 'Fecha de la última modificación',
  `fechahora_alta` datetime DEFAULT NULL COMMENT 'Fecha y hora de alta.',
  `usuario_id` int(11) DEFAULT NULL COMMENT 'ID del usuario que dio el alta o modificó el registro.',
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`),
  KEY `grupo_id` (`grupo_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Variables de configuración del sistema';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `config_parametros_grupos`
--

DROP TABLE IF EXISTS `config_parametros_grupos`;
CREATE TABLE IF NOT EXISTS `config_parametros_grupos` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identificador único',
  `nombre` varchar(255) DEFAULT NULL COMMENT 'Nombre del grupo de parámetros',
  `estado` enum('HAB','DES','ELI') DEFAULT 'HAB' COMMENT 'Estado de habilitación',
  `descripcion` text COMMENT 'Descripción del grupo de parámetros',
  `fechahora_alta` datetime DEFAULT NULL COMMENT 'Fecha y hora de alta.',
  `fechahora_modif` datetime DEFAULT NULL COMMENT 'Fecha y hora de la última modificación',
  `usuario_id` int(11) DEFAULT NULL COMMENT 'Usuario que realizó la última modificación',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cont_asientos_diarios`
--

DROP TABLE IF EXISTS `cont_asientos_diarios`;
CREATE TABLE IF NOT EXISTS `cont_asientos_diarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fechahora` datetime DEFAULT NULL,
  `cuenta_id` int(11) NOT NULL DEFAULT '0',
  `debe` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `haber` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `saldo` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `estado` enum('HAB','DES','ELI') NOT NULL DEFAULT 'HAB',
  `comentario` text,
  `fechahora_alta` datetime DEFAULT NULL,
  `fechahora_modif` datetime DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fechahora` (`fechahora`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cont_cuentas`
--

DROP TABLE IF EXISTS `cont_cuentas`;
CREATE TABLE IF NOT EXISTS `cont_cuentas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `grupo_id` int(11) DEFAULT NULL,
  `nombre` varchar(255) DEFAULT NULL,
  `operacion` enum('ADD','SUB','NUL') DEFAULT 'ADD',
  `estado` enum('HAB','DES','ELI') DEFAULT 'HAB',
  `data` text,
  `fechahora_alta` datetime DEFAULT NULL,
  `fechahora_modif` datetime DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `grupo_id` (`grupo_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `cont_cuentas`
--

INSERT INTO `cont_cuentas` (`id`, `grupo_id`, `nombre`, `operacion`, `estado`, `data`, `fechahora_alta`, `fechahora_modif`, `usuario_id`) VALUES
(1, 1, 'Almacén de la esquina', 'SUB', 'HAB', NULL, '2020-04-01 17:25:07', '2020-04-01 17:25:07', 1),
(2, 1, 'Compras En La Ferretería', 'SUB', 'HAB', NULL, '2020-09-14 07:56:28', '2020-09-14 07:56:28', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cont_cuentas_grupos`
--

DROP TABLE IF EXISTS `cont_cuentas_grupos`;
CREATE TABLE IF NOT EXISTS `cont_cuentas_grupos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) DEFAULT NULL,
  `estado` enum('HAB','DES','ELI') DEFAULT 'HAB',
  `data` text,
  `fechahora_alta` datetime DEFAULT NULL,
  `fechahora_modif` datetime DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `cont_cuentas_grupos`
--

INSERT INTO `cont_cuentas_grupos` (`id`, `nombre`, `estado`, `data`, `fechahora_alta`, `fechahora_modif`, `usuario_id`) VALUES
(1, 'Compras de almacén', 'HAB', NULL, '2020-04-01 17:24:41', '2020-04-01 17:24:41', 1);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
