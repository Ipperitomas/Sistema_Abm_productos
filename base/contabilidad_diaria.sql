-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 02-03-2021 a las 22:04:30
-- Versión del servidor: 5.7.28
-- Versión de PHP: 7.3.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `contabilidad_diaria`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `adm_categorias`
--

DROP TABLE IF EXISTS `adm_categorias`;
CREATE TABLE IF NOT EXISTS `adm_categorias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) DEFAULT NULL,
  `sys_fecha_alta` datetime DEFAULT NULL,
  `sys_fecha_modif` datetime DEFAULT NULL,
  `sys_usuario_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `adm_categorias`
--

INSERT INTO `adm_categorias` (`id`, `nombre`, `sys_fecha_alta`, `sys_fecha_modif`, `sys_usuario_id`) VALUES
(1, 'Sin Categoria', '2021-02-02 00:00:00', '2021-02-02 00:00:00', 1),
(2, 'Utilidades de cocina', '2021-02-02 00:00:00', '2021-02-02 00:00:00', 1),
(3, 'Repostería', '2021-02-02 00:00:00', '2021-02-02 00:00:00', 1),
(4, 'Baño', '2021-02-02 00:00:00', '2021-02-04 18:11:33', 1),
(5, 'Juguetes', '2021-02-02 00:00:00', '2021-02-02 00:00:00', 1),
(6, 'Electro', '2021-02-02 00:00:00', '2021-02-02 00:00:00', 1),
(7, 'Luminaria', '2021-02-02 00:00:00', '2021-02-02 00:00:00', 1),
(8, 'Papas', '2021-02-04 15:04:00', '2021-02-04 15:04:00', NULL),
(9, 'Fundas Iphone', '2021-02-04 22:53:55', '2021-02-04 22:54:06', NULL),
(10, 'Arroz con leche', '2021-03-02 18:49:10', '2021-03-02 18:49:10', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `adm_productos`
--

DROP TABLE IF EXISTS `adm_productos`;
CREATE TABLE IF NOT EXISTS `adm_productos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `categoria_id` int(11) NOT NULL,
  `codigo` varchar(255) DEFAULT NULL,
  `nombre` varchar(500) DEFAULT NULL,
  `stock` int(11) DEFAULT '0',
  `marca` varchar(255) DEFAULT NULL,
  `precio` int(11) DEFAULT NULL,
  `sys_fecha_alta` datetime DEFAULT NULL,
  `sys_fecha_modif` datetime DEFAULT NULL,
  `sys_usuario_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `adm_productos`
--

INSERT INTO `adm_productos` (`id`, `categoria_id`, `codigo`, `nombre`, `stock`, `marca`, `precio`, `sys_fecha_alta`, `sys_fecha_modif`, `sys_usuario_id`) VALUES
(1, 2, 'tipp1616', 'Arroz', 131, 'Sin Marca', 120, '2021-02-03 00:00:00', '2021-03-02 18:53:47', 1),
(2, 2, '145534481', 'Cadenas', 14, NULL, 101, '2021-02-02 22:18:30', '2021-02-02 22:18:30', NULL),
(3, 2, 'manmenr', 'Vasos', 80, NULL, 135, '2021-02-02 22:18:59', '2021-02-02 22:18:59', NULL),
(4, 1, '0120050', 'Fundas', 10, NULL, 580, '2021-02-04 22:53:35', '2021-02-04 22:53:35', NULL);

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
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `backend_contenidos`
--

INSERT INTO `backend_contenidos` (`id`, `alias`, `nombre`, `controlador`, `metadata`, `parent_id`, `parametros`, `en_menu`, `orden`, `es_default`, `esta_protegido`, `perfiles`, `estado`, `last_modif`, `descripcion`) VALUES
(1, '404', 'Error HTTP 404', '404', '{\"vista\":\"404\"}', 0, 0, 0, 1, 0, 0, '', 'HAB', NULL, 'Error 404 - Contenido no encontrado.'),
(2, 'login', 'Iniciar sesión', 'login', '{\r\n\"template\":\"login\",\r\n\"vista\":\"login\",\r\n\"css\":\"formularios\",\r\n\"js\":\"fmdRulo,checkforms.js\",\r\n\"hasmainmenu\":false,\r\n\"hassubmenu\":false,\r\n\"hascarrusel\":false\r\n}', 0, 0, 0, 2, 0, 0, '', 'HAB', NULL, 'Pantalla de logueo o ingreso al sistema.'),
(3, 'logout', 'Cerrar sesión', 'logout', '', 0, 0, 0, 3, 0, 0, '', 'HAB', NULL, 'Cierra sesión del usuario.'),
(4, 'inicio', 'Inicio', 'pagina', '{\r\n\"vista\":\"inicio\",\r\n\"css\":\"daterangepicker,inicio\",\r\n\"js\":\"moment.min,moment_locale_es,daterangepicker,checkforms,inicio\",\r\n\"titulo\":\"Tomasito\",\r\n\"menutag\":\"Inicio\",\r\n\"icon_class\":\"fas fa-edit\",\r\n\"description\":\"\",\r\n\"tooltip\":\"\"\r\n}', 0, 0, 1, 4, 0, 1, 'ADMIN,OPER', 'HAB', NULL, ''),
(5, 'productos', 'Productos', '', '{\r\n\"icon_class\":\"fas fa-boxes\",\r\n\"tooltip\":\"Administración de productos\"\r\n}', 0, 0, 1, 5, 0, 1, 'ADMIN,OPER', 'HAB', NULL, 'Raíz de la administración de los productos'),
(6, 'listado', 'Listado Productos', 'pagina', '{\r\n\"vista\":\"productos/listado\",\r\n\"js\":\"bootstrap-select,productos_listado\",\r\n\"css\":\"bootstrap-select\",\r\n\"menutag\":\"Listado\",\r\n\"titulo\":\"Listado de Productos\",\r\n\"icon_class\":\"fas fa-list-ol\",\r\n\"tooltip\":\"Listado\"\r\n}', 5, 0, 1, 6, 1, 1, NULL, 'HAB', '2020-04-01 16:43:32', 'Listado de los productos'),
(11, 'categorias', 'Categorías', '', '{\r\n\"icon_class\":\"fas fa-tags\",\r\n\"tooltip\":\"Administración de Categorias\"\r\n}', 0, 0, 1, 7, 0, 1, 'ADMIN,OPER', 'HAB', NULL, 'Raiz de las Categorías'),
(12, 'listado', 'Listado Categorías', 'pagina', '{\r\n\"vista\":\"categorias/listado\",\r\n\"js\":\"bootstrap-select,categorias_listado\",\r\n\"css\":\"bootstrap-select\",\r\n\"menutag\":\"Listado\",\r\n\"titulo\":\"Listado de Categorías\",\r\n\"icon_class\":\"fas fa-list-ol\",\r\n\"tooltip\":\"Listado\"\r\n}', 11, 0, 1, 6, 1, 1, NULL, 'HAB', '2020-04-01 16:43:32', 'Listado de las Categorías');

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
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8;

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
(14, 1, '2020-09-14 11:36:12', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.102 Safari/537.36', 1600094231, '::1'),
(15, 1, '2020-09-14 11:37:16', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.102 Safari/537.36', 1600094272, '::1'),
(16, 1, '2020-09-14 14:35:51', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.102 Safari/537.36', 1600105033, '::1'),
(17, 1, '2020-09-14 14:37:29', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.102 Safari/537.36', 1600105181, '::1'),
(18, 1, '2020-09-14 14:41:42', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.102 Safari/537.36 Edg/85.0.564.51', 1600105390, '::1'),
(19, 1, '2020-09-14 14:44:20', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.102 Safari/537.36 Edg/85.0.564.51', 1600108783, '::1'),
(20, 1, '2020-10-06 17:45:14', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/85.0.4183.121 Safari/537.36', 1602017239, '::1'),
(21, 1, '2020-10-09 20:54:13', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.75 Safari/537.36', 1602288355, '::1'),
(22, 1, '2020-10-10 13:56:43', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.75 Safari/537.36', 1602349057, '::1'),
(23, 1, '2020-11-17 08:56:43', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/86.0.4240.198 Safari/537.36', 1605614203, '::1'),
(24, 1, '2020-11-28 18:22:20', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/87.0.4280.67 Safari/537.36', 1606610183, '::1'),
(25, 1, '2021-02-02 18:47:27', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.96 Safari/537.36', 1612315652, '::1'),
(26, 1, '2021-02-02 21:01:21', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.96 Safari/537.36', 1612327846, '::1'),
(27, 1, '2021-02-03 15:48:05', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.96 Safari/537.36', 1612396487, '::1'),
(28, 1, '2021-02-04 14:39:44', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.96 Safari/537.36', 1612476052, '::1'),
(29, 1, '2021-02-04 21:20:45', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.146 Safari/537.36', 1612484450, '::1'),
(30, 2, '2021-02-04 21:21:34', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.146 Safari/537.36', 1612484832, '::1'),
(31, 2, '2021-02-04 21:31:02', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.146 Safari/537.36', 1612485068, '::1'),
(32, 2, '2021-02-04 22:52:53', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.146 Safari/537.36', 1612490046, '::1'),
(33, 1, '2021-02-14 20:33:30', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.152 Safari/537.36', 1613348426, '::1'),
(34, 1, '2021-03-02 18:47:49', 0, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.192 Safari/537.36', 1614721707, '::1'),
(35, 1, '2021-03-02 18:48:32', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.192 Safari/537.36', 1614722415, '::1');

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
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `backend_usuarios`
--

INSERT INTO `backend_usuarios` (`id`, `negocio_id`, `marca_id`, `nombre`, `apellido`, `username`, `iniciales`, `password`, `email`, `nivel`, `perfil_id`, `opciones`, `estado`, `sucursal_id`, `tel`, `fecha_alta`, `fecha_modif`, `usuario_id`) VALUES
(1, NULL, NULL, 'Diego', 'Romero', 'driverop', 'DFR', '25d55ad283aa400af464c76d713c07ad', 'diego.romero@driverop.com', 'OWN', NULL, '{\r\n\"rpp\":\"25\",\r\n\"tsession\":3600\r\n}', 'HAB', NULL, '3446-623494', '2020-04-01 10:55:00', '2020-04-01 10:55:00', 1),
(2, NULL, NULL, 'Regaleria', 'Silvia', 'regaleria', 'DFR', '25d55ad283aa400af464c76d713c07ad', '', 'OWN', NULL, '{\r\n\"rpp\":\"25\",\r\n\"tsession\":3600\r\n}', 'HAB', NULL, '', '2020-04-01 10:55:00', '2020-04-01 10:55:00', 1);

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
