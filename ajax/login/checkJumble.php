<?php
/*
	Verifica si el jumble que el usuario que quiere recuperar la contraseña ha ingresado, es correcto.
	
	Created: 2020-03-30
	Author: DriverOp

*/

require_once(DIR_model."class.simpleUsr.inc.php");

$this_file = substr(__FILE__,strlen(DIR_BASE))." ";

$usuario = new cSimpleUsr();

$post = CleanArray($_POST);
$msgerr = array();

$jumble = substr($post['jumble'],0,8);

if (empty($jumble)) {
	$msgerr['jumble'] = 'Debes ingresar el código.';
} else {
	if (strlen($jumble) != 8) {
		$msgerr['jumble'] = 'Deben ser extactamente ocho caracteres.';
	} else {
		if (!preg_match('/^[A-F|a-f|0-9]{8}$/im',$jumble)) {
			$msgerr['jumble'] = 'El código no es correcto.';
		}
	}
}
if (CanUseArray($msgerr)) {
	EmitJSON($msgerr);
	return;
}

$usuario->GetByJumble($jumble);
if (!$usuario->existe) {
	EmitJSON('Código no está registrado o está vencido.');
	return;
}

$_SESSION['recover_user'] = $usuario->id;

ResponseOk();
?>