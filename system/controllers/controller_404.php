<?php
$this_file = substr(__FILE__,strlen(DIR_BASE))." ";
$url = EnsureTrailingURISlash($objeto_contenido->GetLink(true));

$objeto_contenido->metatitle = 'No encontado :: Error HTTP 404';

header($_SERVER['SERVER_PROTOCOL'].' 404 Not Found');

$head = (!empty($objeto_contenido->metadata->head))?$objeto_contenido->metadata->head:DEFAULT_SITE_HEAD;
$head = DIR_common.$head.'.htm';

$header = (!empty($objeto_contenido->metadata->header))?$objeto_contenido->metadata->header:DEFAULT_SITE_HEADER;
$header = DIR_common.$header.'.htm';

$mainmenu = (!empty($objeto_contenido->metadata->mainmenu))?$objeto_contenido->metadata->mainmenu:DEFAULT_SITE_MENU;
$mainmenu = DIR_common.$mainmenu.'.htm';
if (!isset($objeto_contenido->metadata->hasmainmenu)) { $objeto_contenido->metadata->hasmainmenu = false; } // Por omisión Sí

$vista = (!empty($objeto_contenido->metadata->vista))?$objeto_contenido->metadata->vista:$objeto_contenido->alias;
$vista = DIR_common.$vista.'.htm'; // Ojo al piojo!!!

$submenu = $vista."_submenu.htm";
if (!isset($objeto_contenido->metadata->hassubmenu)) { $objeto_contenido->metadata->hassubmenu = false; } // Por omisión No

$footer = (!empty($objeto_contenido->metadata->footer))?$objeto_contenido->metadata->footer:DEFAULT_SITE_FOOTER;
$footer = DIR_common.$footer.'.htm';
try {

	/* Head */
	if (!ExisteArchivo($head)) { WriteLog($this_file.__LINE__.' Head no encontrado: '.$head); }
	else { include($head); }

	/* Header */
	if (!ExisteArchivo($header)) { WriteLog($this_file.__LINE__.' Header no encontrado: '.$header); }
	else { include($header); }
	
	/* Main menu */
	if ($objeto_contenido->metadata->hasmainmenu) {
		if (!ExisteArchivo($mainmenu)) { WriteLog($this_file.__LINE__.' Mainmenu no encontrado: '.$mainmenu); }
		else { include($mainmenu); }
	}

	/* Submenu */
	if ($objeto_contenido->metadata->hassubmenu) {
		if (!ExisteArchivo($submenu)) { WriteLog($this_file.__LINE__.' Submenu no encontrado: '.$submenu); }
		else { include($submenu); }
	}

	/* Vista */
	if (!ExisteArchivo($vista)) { WriteLog($this_file.__LINE__.' Vista no encontrado: '.$vista); }
	else { include($vista); }

	/* Footer */
	if (!ExisteArchivo($footer)) { WriteLog($this_file.__LINE__.' Footer no encontrado: '.$footer); }
	else { include($footer); }


} catch(Exception $e) {
	cLogging::Write($this_file.$e->getMessage());
	if (DEVELOPE) { EchoLogP($this_file.$e->getMessage()); }
}

?>