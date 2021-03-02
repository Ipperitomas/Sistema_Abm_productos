<?php
/*
	Controlador especial para servir los archivos solicitados mediante petición AJAX.
	Created: 2020-09-23
	Author: DriverOp
	
	Las variables $user_logged_in, $objeto_contenido y $objeto_usuario están definidas en index.php
*/
$this_file = substr(__FILE__,strlen(DIR_BASE))." ";

	header("Cache-Control: no-cache, must-revalidate");
	header("Content-type: text/html; charset=UTF-8");
	
	$ajax_id = null;
	$handler = array(DEFAULT_CONTENT);
	if (!empty($_SERVER['HTTP_REFERER']) and cSecurity::IsAllowedHost($_SERVER['HTTP_REFERER'])) { // Solo del propio sitio...
		$handler = cSecurity::ParsePath(mb_substr($_SERVER['HTTP_REFERER'],mb_strlen(BASE_URL))); // Convertir los dir virturales del referer en un array.
	}

	$ajax_archivo = @$_REQUEST['archivo'];
	if (empty($ajax_archivo)) {
		$ajax_archivo = @$handler[0];
	}
	$ajax_archivo = cSecurity::SatinizePathParam(trim($ajax_archivo)); // No se permite incluir directorios.
	
	if (empty($ajax_archivo)) {
		EmitJSON('No se indicó el archivo a cargar.');
		cLogging::Write($this_file.__LINE__." No se indicó el archivo a cargar.");
		exit;
	}

	$dir_content = @$_REQUEST['content'];
	if (!empty($dir_content)) {
		$dir_content = mb_substr($dir_content,0,128);
		$dir_content = cSecurity::NeutralizeDT(trim($dir_content)); // Si se permite ir a un directorio más profundo, pero no escalar directorio.
		if (!empty($dir_content)) {
			$dir_content = EnsureTrailingSlash($dir_content);
		} else {
			$dir_content = NULL;
		}
	}

/* Si el tipo de interfaz es backend, denegar todo excepto lo desmilitarizado. */
	if (INTERFACE_TYPE == 'backend') {
		if (!cSecurity::Demilitarized($dir_content,$ajax_archivo)) { // El contenido solicitado está desmilitarizado?
			if (!$user_logged_in) { // Solo si está logueado puede acceder a contenido militarizado.
				header($_SERVER['SERVER_PROTOCOL'].' 401 Authorization Required');
				echo '<!-- <json>{"generr":"Debe iniciar sesión de usuario","loggin_required":true}</json> -->';
				cLogging::Write($this_file.__LINE__." Se intentó acceder sin tener sesión de usuario abierta a ".$dir_content.$ajax_archivo);
				exit;
			}
		}
	}
	
	if ($objeto_contenido->GetContent($handler)) {
		if ($user_logged_in) {
			//$objeto_contenido->SetPermisos($objeto_usuario->tienePermiso($objeto_contenido->id)); // Oops!, esto no está implementado en este framework, sino en ombucredit
		}
	}

	$ajax_id = SecureInt(substr(trim(@$_REQUEST['id']),0,11),NULL);

	$ruta = $dir_content.$ajax_archivo.'.php';

	if (ExisteArchivo(DIR_ajax.$ruta)) {
		//EchoLog("ID: ".$objeto_contenido->id); // Descomentar esto para ver el ID del contenido.
		//ShowVar($objeto_contenido);
		include_once(DIR_ajax.$ruta);
	} else {
		header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');
		EmitJSON("No existe el archivo: ".$ruta);
		cLogging::Write($this_file." No se encontró ".$ruta);
	}
?>