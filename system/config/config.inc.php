<?php
// Configuración de la base de datos
require_once(DIR_config."database.config.inc.php");

define("JSON_HACELO_BONITO",JSON_FORCE_OBJECT+JSON_PRETTY_PRINT+JSON_NUMERIC_CHECK+JSON_UNESCAPED_UNICODE);
define("JSON_HACELO_BONITO_CON_ARRAY",JSON_PRETTY_PRINT+JSON_NUMERIC_CHECK+JSON_UNESCAPED_UNICODE);

/* Descripciones del desarrollo */
define("APP_NAME","Modulo Contable - Tipp Sistemas");
define("APP_DESCRIPTION","Modulo Contable - Tipp Sistemas");
define("APP_VERSION_NUMBER","1.0");

// Qué tipo de interfaz es. Esto determina si se pide siempre un usuario logueado ('backend') o no ('frontend');
define("INTERFACE_TYPE",'backend');

// Título por omisión.
define("MAINTITLE","Contabilidad Diaria");

// Archivos del tinglado
define("DEFAULT_SITE_HEAD","head");
define("DEFAULT_SITE_HEADER","header");
define("DEFAULT_SITE_FOOTER","footer");
define("DEFAULT_SITE_MENU","mainmenu");
define("DEFAULT_SITE_SUB_MENU","submenu");
define("DEFAULT_SITE_TEMPLATE","main_content");
define("SITE_mainlogo", URL_img."logo.png");
define("SITE_humans", BASE_URL."humans.txt");

// El contenido por omisión (su alias).
define("DEFAULT_CONTENT","inicio"); // Debería ser siempre 'inicio' pero si 'inicio' está protegido, redirige a 'login' en index.php
// Alias especial para ignorar la carga de contenido desde la base de datos, es para pedir archivos assets directamente.
define("DEFAULT_FOR_FILES","f");
// El idioma por omisión.
define("DEFAULT_LANGUAGE","es-AR");
// Establecer si usar múltiples idiomas o no.
define("DETECT_LANGUAGE",true);
// Usar Business Data para traer los datos del negocio.
define("USE_BUSSINESSDATA", false);
// Convertir los campos decimales de las tablas a moneda 
define("DEC_AS_CURRENCY",false);
// Moneda por omisión.
define("DEFAULT_DIVISA","ARS");

// Siempre cargar estos css
const DEFAULT_CSS = array("font-awesome","bootstrap","main","msgerr","select2.min","select2-bootstrap4-theme".DS."select2-bootstrap4.min","adminlte");
// Y estos JS
const DEFAULT_JS = array("jquery.min","bootstrap.bundle.min","popper","microajax","base","main","select2".DS."select2.full.min","select2".DS."i18n".DS."es","adminlte");
// Estos directorios AJAX están desmilitarizados
const DMZ_CONTENTS = array("login");
// Estos archivos AJAX están desmilitarizados
const DMZ_ARCHIVOS = array("login_form","checkLogin","frm_forgotPass","userTimeleft");
/*
	MUY importante
	Esta es la lista de aliases restringidos. Tienen un significado especial.
	Los aliases listados aquí no se validan contra la base de datos sino que redirigen a directorios donde se tratan por separado.
*/
const ASSETS_CONTENTS = array(
	'css'=>array('dir'=>DIR_css, 'controller'=>'css'),
	'js'=> array('dir'=>DIR_js,  'controller'=>'js'),
	'ajx'=>array('dir'=>DIR_ajax,'controller'=>'ajax')
);


?>