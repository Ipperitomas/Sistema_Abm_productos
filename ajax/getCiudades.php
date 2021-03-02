<?php
require_once(DIR_includes."class.sidekick.inc.php");
$this_file = substr(__FILE__,strlen(DIR_BASE))." ";

$busqueda = $_GET['term'];
$ciudades = cSideKick::GetCiudades($objeto_db, $busqueda);

//return $ciudades;
echo '{"results":'.json_encode($ciudades).'}';

?>