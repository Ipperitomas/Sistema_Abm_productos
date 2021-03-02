<?php
/*
	Controlador de descargas 'directas'
*/
$this_file = substr(__FILE__,strlen(DIR_BASE))." ";

	$dir_content = null;

	$dir_archivo = (isset($_POST['archivo']))?$_POST['archivo']:@$_GET['archivo'];
	if (empty($dir_archivo)) {
		$dir_archivo = @$handler[0];
	}
	
	$dir_archivo = cSecurity::SatinizePathParam(trim($dir_archivo)); // No se permite incluir directorios.
	if (empty($dir_archivo)) {
		EmitJSON('No se indicó el archivo a cargar.');
		cLogging::Write($this_file.__LINE__." No se indicó el archivo a cargar.");
		exit;
	}

	$dir_content = (!empty($_POST['content']))?$_POST['content']:@$_GET['content'];
	
	if (!empty($dir_content)) {
		$dir_content = mb_substr($dir_content,0,128);
		$dir_content = cSecurity::NeutralizeDT(trim($dir_content)); // Si se permite ir a un directorio más profundo, pero no escalar directorio.
		if (!empty($dir_content)) {
			$dir_content = EnsureTrailingSlash($dir_content);
		} else {
			$dir_content = NULL;
		}
	}
	
	$archivo_descarga = EnsureTrailingSlash($dir_content).$dir_archivo;
	$ruta = DIR_downloader_files.$archivo_descarga;
	if (!ExisteArchivo($ruta)) {
		EmitJSON('Archivo no encontrado.');
		cLogging::Write($this_file.__LINE__." El archivo indicado no fue encontrado: ".$ruta);
		exit;
	}

	header("Content-Transfer-Encoding: binary");
	header("Content-type: application/force-download");
	header("Content-Disposition: attachment; filename=".basename($dir_archivo));
	header("Content-Length: ".filesize($ruta));
	readfile($ruta);

?>