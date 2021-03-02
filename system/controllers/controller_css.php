<?php
/*
	Controlador especial para servir los archivos CSS.
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

$files = DEFAULT_CSS;
$aux = $objeto_contenido->CssList();
$files = array_merge($files, $aux);

/* Agregar el alias del contenido actual como un archivo más a ser cargado */
if (array_search($objeto_contenido->alias, $files) === false) {
	$files[] = $objeto_contenido->alias;
}

LoadFiles($files);
return;


/**
* Summary. De la lista que se le pasa como parámetro, los busca en el directorio de CSS y los carga sin repetir. Si el framework no está en modo develope, hace una compresión lijera.
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
	header("Content-type: text/css; charset=UTF-8");

	if (!DEVELOPE) {
		ob_start("compressCss");
	}

	foreach ($files as $value) {
		$value = $value.".css";
		$ruta = DIR_css.$value;
		if ((file_exists($ruta)) AND (is_file($ruta))) {
			echo "\r\n/* ".$value." */\r\n";
			include($ruta);
		}else{
			echo "\r\n/* El archivo ".$value." no pudo ser encontrado */\r\n";
		}
	}

	if (!DEVELOPE) {
		ob_end_flush();
	}
} // LoadFiles.

/**
* Summary. Quita los espacios, tabuladores y retornos de carro irrelevantes. Quita los comentarios.
* @param buffer $buffer Un buffer de salida del servidor web.
* @return el buffer modificado.
*/
	function compressCss($buffer) {
		/* remove comments */
		$buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
		/* remove tabs, spaces, newlines, etc. */
		$buffer = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $buffer);
		/* remove irrelevant white spaces */
		$buffer = str_replace(
			array(', ',': ',' {','{ ',' ;','; '),
			array(',',':','{','{',';',';'),
			$buffer);
		return $buffer;
	}

?>