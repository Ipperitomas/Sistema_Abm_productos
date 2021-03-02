<?php
/*
	Constantes para establecer el tipo de evento en los logs del sistema.
	Modif: 2020-10-21
	Desc: Agregada constante LGEV_REMOTE_IP que permite incluir la IP remota en el archivo de log.
		Eliminado método estático EventToText() por irrelevante.

	Estructura de la tabla 'sys_logging':
	<sql>
		DROP TABLE IF EXISTS `sys_logging`;
		CREATE TABLE IF NOT EXISTS `sys_logging` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `fechahora` datetime DEFAULT NULL,
		  `remote_ip` varchar(46) NOT NULL COMMENT 'IP del cliente o IP remota',
		  `source` varchar(15) DEFAULT 'backend' COMMENT 'De qué parte del sistema viene el log',
		  `tipo_evento` enum('ALL','DEBUG','INFO','WARN','ERROR','FATAL','OFF','TRACE') DEFAULT 'ALL',
		  `descripcion` text,
		  `data` text,
		  `usuario_id` int(11) DEFAULT NULL,
		  PRIMARY KEY (`id`),
		  KEY `fechahora` (`fechahora`),
		  KEY `event_type` (`tipo_evento`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	</sql>
	
	
*/

require_once(DIR_model."class.dbutili.2.inc.php");

define("LGEV_ALL", 0); // Todos los eventos.
define("LGEV_DEBUG", 1); // Evento de debug.
define("LGEV_INFO", 2); // Información
define("LGEV_WARN", 3); // Aviso de que algo pudo salir mal
define("LGEV_ERROR", 4); // Algo salió mal pero se puede seguir.
define("LGEV_FATAL", 5); // Todo se fue al carajo.
define("LGEV_OFF", 6); // Se apaga el log.
define("LGEV_TRACE", 7); // El log incluye más detalles en la descripción de este evento.

define("LGEV_TARGET_FILE",1); // Se loguea a un archivo.
define("LGEV_TARGET_DB",2); // Se loguea a la base de datos.
define("LGEV_TARGET_TABLE_NAME","sys_logging"); // El nombre de la tabla en la DB que mantiene el log.

if (!defined("LGEV_SOURCE")) {
	define("LGEV_SOURCE","core"); // De dónde viene el log
}
define("LGEV_REMOTE_IP",true); // El log debe incluir la IP remota?

//define("LGEV_DEFAULT_TARGET",LGEV_TARGET_FILE+LGEV_TARGET_DB); // A donde se escribe el log por omisión.
define("LGEV_DEFAULT_TARGET",LGEV_TARGET_FILE);


if (!defined("LGEV_LEVEL")) {
	define("LGEV_LEVEL",0); // Los eventos que sean menores a LGEV_LEVEL, no se loggean.
}

const LGEV_EVENT_TEXT = array('ALL','DEBUG','INFO','WARN','ERROR','FATAL','OFF','TRACE');

class cLogging {

	
	private static $default_target = LGEV_DEFAULT_TARGET;
	private static $dbtabla = LGEV_TARGET_TABLE_NAME;
	
	static function SetTarget($target) {
		self::$default_target = $target;
	}


	static function Write($linea = NULL, $event = LGEV_DEBUG, $target = NULL) {
		if ($event < LGEV_LEVEL) { return; }
		if ((is_null($target)) and (self::$default_target != 0)) {
			$target = self::$default_target;
		}
		if ($target & LGEV_TARGET_FILE) {
			self::LogToFile($linea, NULL, $event);
		}
		if ($target & LGEV_TARGET_DB) {
			self::LogToDB($linea, $event);
		}
	}
	
	static function LogToFile($text, $archivo = null, $event = LGEV_DEBUG) {
		umask(0);
	
		$mes = Date('Y-m');
		$dia = Date('Y-m-d');
		
		$dir = DIR_logging.$mes;
		
		if (empty($archivo)) {
			$archivo = $dir.'/'.$dia.'.log';
		}
		
		
		if (!file_exists(DIR_logging)) {
			mkdir(DIR_logging,0777);
		}
	
		if (!file_exists($dir)) {
			mkdir($dir,0777);
		}
		
		$linea = '['.Date('Y-m-d H:i:s').'] - ';
		if (LGEV_REMOTE_IP) {
			$linea .= GetIP().' - ';
		}
		$linea .= LGEV_SOURCE.' - ';
		if (($event > -1)) {
			$linea .= ((isset(LGEV_EVENT_TEXT[$event]))?LGEV_EVENT_TEXT[$event]:$event)." - ";
		}
		$linea .= $text.PHP_EOL;
		
		return file_put_contents($archivo, $linea, FILE_APPEND);
	}

	static function LogToDB($text, $event = LGEV_DEBUG, $trace = null) {
		$error_level = error_reporting();
		error_reporting(E_ERROR | E_PARSE);
		try {
			$db = new cDB();
			$db->Connect(DBHOST, DBNAME, DBUSER, DBPASS);
			if ($db->IsConnected()) {
				$reg = array();
				$reg['fechahora'] = Date('Y-m-d H:i:s');
				if (LGEV_REMOTE_IP) {
					$reg['remote_ip'] = GetIP();
				}
				$reg['source'] = LGEV_SOURCE;
				$reg['tipo_evento'] = (isset(LGEV_EVENT_TEXT[$event]))?LGEV_EVENT_TEXT[$event]:$event;
				$reg['descripcion'] = $db->RealEscape(substr($text,0,255));
				if (!empty($trace)) {
					if (is_object($trace) or is_array($trace)) {
						$reg['data'] = $db->RealEscape(json_encode($trace, JSON_HACELO_BONITO_CON_ARRAY));
					} else {
						$reg['data'] = $db->RealEscape($trace);
					}
				}
				if (isset($objeto_usuario) and isset($objeto_usuario->id)) {
					$reg['usuario_id'] = $objeto_usuario->id;
				}
				$db->Insert(self::$dbtabla, $reg);
				if ($db->error) { throw new Exception('DBErr: '.$db->errmsg); }
			} else {
				throw new Exception('DBErr: No se pudo conectar a la base de datos: '.$db->errmsg);
			}
		} catch(Exception $e) {
			self::LogToFile(__FILE__.$e->GetMessage(), null, LGEV_ERROR);
		}
		error_reporting($error_level);
	}
}