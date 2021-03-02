<?php
/*
	Con el id indicado por POST, se establece la propiedad de content_options de las opciones del usuario actual.
	Esto significa que se deben recordar los filtros de los listados, es decir, que no se debe planchar la variable $_SESSION.
*/
$this_file = substr(__FILE__,strlen(DIR_BASE))." ";

if (empty($ajax_id)) { return; }

$options = $objeto_usuario->opciones;
if (!isset($options->content_options)) {
	$options->content_options = array();
}
$val = strtolower(substr(@$_POST['val'],0,5));

$alias = 'ctn'.$ajax_id;

$options->content_options->$alias = in_array($val,['true','on']);

try {
	$reg = array(
		'opciones'=>json_encode($options, JSON_HACELO_BONITO_CON_ARRAY)
	);
	$reg = $objeto_db->RealEscapeArray($reg);
	$objeto_db->Update(TBL_backend_usuarios, $reg, "`id` = ".$objeto_usuario->id);
	if ($objeto_db->error) { throw new Exception(__LINE__." DBErr: ".$objeto_db->errmsg); }
} catch(Exception $e) {
	EchoLog($e->getMessage());
	cLogging::Write($this_file.__LINE__." ".$e->getMessage());
}

?>