<?php
/*
	Author: DriverOp.
	Created: 2018-11-23
	Description: Vamos a intentar que todo el sitio pase por acá (excepto las imágenes, claro).
	
	Modif: 2020-09-08
	Author: DriverOp
	Desc:
		Constantes DEVELOPE ahora se define en un archivo aparte. Introducida constante DEPLOY.
		Detección y carga de lenguaje del visitante para establecer el idioma del sitio.
*/

header("Cache-Control: no-cache, must-revalidate"); // No catchear el contenido
header("X-Frame-Options: SAMEORIGIN"); // Incluir el contenido en iframe solo desde el mismo dominio (evita el clickjacking).
// Inicializar todas las constantes para comenzar a laburar...
require_once('initialize.php');
$this_file = substr(__FILE__,strlen(DIR_BASE))." ";
if (version_compare(PHP_VERSION, '7.13.0') >= 0) { // A partir de PHP 7.13...
	session_set_cookie_params(array('samesite'=>'lax')); 
} else {
	session_set_cookie_params(time()+$maxlifetime, '/;samesite=lax', $_SERVER['HTTP_HOST'], false, true);
}
session_start();
// Establecer las constantes de configuración
require_once(DIR_config.'config.inc.php');

/* Ahora se puede comenzar a usar el framework. */
require_once(DIR_includes."common.inc.php"); // Contiene las funciones comunes.
require_once(DIR_includes."class.security.inc.php"); // Contiene funciones para manipular la URL.

$asset = null; // Bandera para determinar si se carga contenido o recurso.
$msgerr = null; // Para almacenar cualquier mensaje de error.
$user_logged_in = false; // Determinar si el usuario está loggeado.
$for_files = false; // Determina si se carga un archivo asset directamente.

/*
$_GET['_ruta_'] se establece mediante el archivo .htaccess, contiene la lista de directorios virtuales.
*/
$handler = array(DEFAULT_CONTENT); // Acá van a quedar los alias de los contenidos encadenados como directorios virtuales. Por omisión, el contenido... por omisión.
if (isset($_GET['_ruta_']) and ($_GET['_ruta_'] != null)) { // Si existe y no está vacío...
	$handler = cSecurity::ParsePath($_GET['_ruta_']); // Convertir los dir virturales en un array.
}

// Se incluyen las clases necesarias para comenzar a trabajar...
require_once(DIR_includes.'class.logging.inc.php'); // Para escribir entradas en el log de eventos.
require_once(DIR_model.'class.dbutili.2.inc.php'); // Clase base para el manejo de la base de datos.
require_once(DIR_model.'class.contenidos.inc.php'); // Clase para acceder a la lista de contenidos en la DB.
if (INTERFACE_TYPE == 'backend') {
	require_once(DIR_model.'class.usuarios_backend.inc.php'); // Clase para manejar los usuarios del sistema.
}
// Detectar y cargar la bibloteca de idiomas.
require_once(DIR_includes."languages.inc.php");

// Una instancia de la base de datos.
$objeto_db = new cDb();
$objeto_db->Connect(DBHOST, DBNAME, DBUSER, DBPASS, DBPORT);
// Primer problema: La conexión a la DB podría ser errónea por alguna razón...
if ($objeto_db->error) {
	include(DIR_errordocs."dberror.htm");
	exit;
}
// Una instancia de la clase para manejar los contenidos.
$objeto_contenido = new cContenido();
require_once(DIR_includes.'class.sidekick.inc.php'); // La biblioteca de funciones para recuperar datos de la base de datos.
$objeto_contenido->lg_contenidos = @$lg_contenidos;

if (INTERFACE_TYPE == 'backend') {
	// Una instancia de la clase para manejar los usuarios del sistema.
	$objeto_usuario = new cUsrBackend();
	$objeto_usuario->lg_contenidos = @$lg_contenidos;
	$user_logged_in = $objeto_usuario->CheckLogin();
	$objeto_contenido->usuario = @$objeto_usuario;
}

if (array_key_exists($handler[0],ASSETS_CONTENTS)) {
	$asset = $handler[0];
	array_shift($handler);
	if (strtolower(@$handler[0]) == strtolower(DEFAULT_FOR_FILES)) {
		$for_files = true;
		array_shift($handler);
	}
}

if (!$for_files and (count($handler) > 0)) { // Si no se pide un archivo directamente...
	$content_found = $objeto_contenido->GetContent($handler); // Se busca el contenido.
}


if ($asset) {
	$controllerpath = DIR_controller.'controller_'.ASSETS_CONTENTS[$asset]['controller'].'.php';
} else {
	if ((INTERFACE_TYPE == 'backend') and ($objeto_contenido->esta_protegido) and (!$user_logged_in) and (!$for_files)) {
		$objeto_contenido->GetContent('login');
	}
	if (!empty($objeto_contenido->controlador)) {
		$controllerpath = DIR_controller.'controller_'.$objeto_contenido->controlador.'.php';
	} else { // Last resource
		$controllerpath = DIR_controller.'controller_pagina.php';
	}
}
if (!ExisteArchivo($controllerpath)) {
	cLogging::Write($this_file.__LINE__." Controlador no encontrado: ".$controllerpath);
	$msgerr = 'Te olvidaste del controlador... '.$controllerpath;
	require_once(DIR_errordocs."500c.htm");
	return;
}

require_once($controllerpath); // Aquí se transfiere el control al controlador del contenido solicitado.
$objeto_db->Disconnect(); // Siempre es buena idea no dejar abierta una conexión a la base de datos.
?>