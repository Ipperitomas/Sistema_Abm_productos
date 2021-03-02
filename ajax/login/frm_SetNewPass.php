<?php
/*
	Este es el script que efectivamente cambia la contraseña de un usuario.
	Created: 2020-03-27
	Author: DriverOp
*/
require_once(DIR_includes."class.checkinputs.inc.php");
require_once(DIR_includes."class.sidekick.inc.php");
require_once(DIR_model."class.simpleUsr.inc.php");

$this_file = substr(__FILE__,strlen(DIR_BASE))." ";

if (!isset($_SESSION['recover_user'])) {
	EmitJSON('Usuario no establecido.');
	return;
}

$usuario_id = $_SESSION['recover_user'];

$usuario = new cSimpleUsr();
if ($usuario->Get($usuario_id) === false) {
	EmitJSON('Usuario no encontrado.');
	return;
}

$msgerr = array();

$newpass = trim($_POST['newpass']);
if (strlen($newpass) < 8) {
	$msgerr['newpass1'] = 'La contraseña debe ser de al menos ocho caracteres';
} else {
	if (strlen($newpass) > 32) {
		$msgerr['newpass1'] = 'La contraseña debe ser hasta 32 caracteres';
	}
}

cCheckInput::Nick($newpass, 'newpass1', 'Contraseña');

$msgerr = array_merge($msgerr, cCheckInput::$msgerr);
if (CanUseArray($msgerr)) {
	EmitJSON($msgerr);
	return;
}

if ($usuario->SetNewPassword($newpass) == false) {
	EmitJSON('No se pudo establecer la nueva contraseña');
	return;
}

?>
<h5>Recuperar contraseña</h5>


<div class="card">
	<div class="card-header border-bottom-0">
		<h6>Contraseña establecida</h6>
	</div>
	<div class="card-body">
		<p class="text-info">Se estableció la nueva contraseña.</p>
		<p class="text-info">Tu nombre de usuario es <b><?php echo $usuario->username; ?></b>.</p>
		<p>Hacé clic en 'volver' para ingresar al sistema.</p>
	</div>
</div>


<p class="text-info text-center mt-4 mb-0 fs8"><a href="" onClick="GoBack(); return false;">Volver.</a></p>