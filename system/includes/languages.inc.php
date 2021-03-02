<?php
/*
	Detecta y establece el idioma del sitio de acuerdo a las preferencias del visitante.

	Created: 2020-09-8
	Author: DriverOp
	
	Modif: 2020-09-12
	Author: DriverOp
	Desc:
		Agregada función ParseContentLangTag para facilitar las cosas.

*/
$this_file = substr(__FILE__,strlen(DIR_BASE))." ";
if (!defined("DETECT_LANGUAGE")) { return; } // El sitio no tiene multilenguaje.
if (defined("DETECT_LANGUAGE") and (DETECT_LANGUAGE == false)) { return; } // Se configuró para no usar multilenguaje.
if (!defined("DEFAULT_LANGUAGE")) { define("DEFAULT_LANGUAGE","es-AR"); } // Just in case...

$language = DEFAULT_LANGUAGE;
$language_cookie_name = str_replace(['=',',',';','\t','\r','\n',' '],'',APP_NAME).'_lang';

if (isset($_COOKIE[$language_cookie_name]) and (!empty($_COOKIE[$language_cookie_name]))) {
	$language = $_COOKIE[$language_cookie_name];
} else {
	$language = DEFAULT_LANGUAGE;
	// $language = GetLangFromBrowser();
}

//cLogging::Write($this_file.__LINE__." El lenguaje es: ".$language);
if (!preg_match("/^[a-z]{2}(-([A-Z]{2}|\d{2,3}))?$/",$language)) {
	$language = DEFAULT_LANGUAGE;
}

setmycookie($language_cookie_name,$language);

$aux = explode('-',$language);
$main = strtolower($aux[0]);
$variant = strtolower(@$aux[1]);

$lang_file = DIR_lang.$main.DS.$main."-".$variant.".php"; //echo $lang_file; die;

if (!ExisteArchivo($lang_file)) {
	$lang_file = DIR_lang.$main.DS."default.php";
	if (!ExisteArchivo($lang_file)) {
		$lang_file = DIR_lang."default.php";
	}
}

include($lang_file);

function GetLangFromBrowser() {
	global $this_file;
	$result = DEFAULT_LANGUAGE;
	if (!isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
		return $result;
	}
	$lang = locale_accept_from_http($_SERVER['HTTP_ACCEPT_LANGUAGE']);
	if (empty($lang)) {
		return $result;
	}
	$result = str_replace('_','-',$lang);
	//cLogging::Write($this_file.__LINE__." El lenguaje del navegador es: ".$result);
		
	return $result;
}

/**
* Summary. Parsea una cadena reemplazando $string,... por la traducción correcta según la lista dada por $lg_conten
* @param str $item La cadena a ser parseada.
* @param array $lg_contenidos Lista de traducciones activas.
* @return str La cadena parseada
*/
function ParseContentLangTag($item, $lg_contenidos) {
	$result = $item;
	if (isset($lg_contenidos[$item])) {
		$result = $lg_contenidos[$item];
	} //print_r($item); echo "<hr/>";
	return $result;
}



/**
* Devuelve la frase traducida
* @param string $phrase la frase original
* @return string La frase traducida
*/
function __($phrase){
	global $lg_contenidos;
	
	// Chequea que exista la frase en el array
	if(isset($lg_contenidos[$phrase])) { echo $lg_contenidos[$phrase]; }
	else {
		if (DEVELOPE) { echo '???'; }
	}
	
}
?>