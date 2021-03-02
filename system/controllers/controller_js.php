<?php
/*
	Controlador especial para servir los archivos JavaScript.
	Created: 2020-09-23
	Author: DriverOp

	Las variables $asset y $for_files están definidas en index.php.

*/
$this_file = substr(__FILE__,strlen(DIR_BASE))." ";

if ($for_files) {
	if (count($handler) > 0) {
		LoadFiles($handler);
	} else {
		if (DEVELOPE) { echo '/* No se indicó archivo a cargar */'; }
	}
	exit;
}

//ShowOutput($objeto_contenido,true);

$files = DEFAULT_JS;
$aux = $objeto_contenido->JsList();
$files = array_merge($files, $aux);

/* Agregar el alias del contenido actual como un archivo más a ser cargado */
if (array_search($objeto_contenido->alias, $files) === false) {
	$files[] = $objeto_contenido->alias;
}

LoadFiles($files);
return;

/**
* Summary. De la lista que se le pasa como parámetro, los busca en el directorio de JS y los carga sin repetir.
* @param array $files, la lista de archivos a cargas (sin extensión)
*/
function LoadFiles($files) {
	
	global $objeto_usuario;
	global $objeto_contenido;

	if (phpversion() >= '5.2.9') {
		$files = array_unique($files, SORT_REGULAR);
	} else {
		$files = array_unique($files);
	}

	reset($files);

	if (DEVELOPE) {
		header("Cache-Control: no-cache, must-revalidate");
	}
	header("Content-type: application/javascript; charset=UTF-8");
	
	if (INTERFACE_TYPE == 'backend') {
		$objeto_usuario->CheckLogin();
	}

	foreach ($files as $value) {
		$value = $value.".js";
		$ruta = DIR_js.$value;
		if ((file_exists($ruta)) AND (is_file($ruta))) {
			echo "\r\n/* ".$value." */\r\n";
			include($ruta);
		}else{
			echo "\r\n/* El archivo ".$value." no pudo ser encontrado */\r\n";
		}
	}
	return;
} // LoadFiles

?>