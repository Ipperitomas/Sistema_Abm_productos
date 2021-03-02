<?php
/*
	Verifica si el pin que el usuario que quiere recuperar la contraseña ha ingresado, es correcto.
	
	Created: 2020-03-26
	Author: DriverOp

*/
require_once(DIR_model."class.wsclient.inc.php");
$this_file = substr(__FILE__,strlen(DIR_BASE))." ";

$PIN = substr(trim(@$_POST['PIN']),0,5);


if (empty($PIN)) {
	$msgerr['pin'] = 'Debes ingresar el PIN.';
} else {
	$PIN = cSideKick::FilterNumber($PIN);
	if ($PIN === false) {
		$msgerr['pin'] = "Debe ser un número.";
	}
}
if (CanUseArray($msgerr)) {
	EmitJSON($msgerr);
	cLogging::Write($this_file.' Errores: '.print_r($msgerr, true));
}

$wsclient = new cWsClient();
$wsclient->debug_level = 1;

$result = $wsclient->notificadorCheckPIN($_SESSION['bypin']['PIN'], $PIN);

if ($result->result == false) {
	cLogging::Write($this_file.__LINE__." ->CheckPIN respondió: ".print_r($result));
	EmitJSON("No se pudo verificar tu identidad.");
	return;
} else {
	if (is_null($result->data)) {
		cLogging::Write($this_file.__LINE__." ->CheckPIN respondió: ".print_r($result));
		EmitJSON("No se pudo verificar tu identidad.");
		return;
	} else {
		if (!$result->data->verified){
			cLogging::Write($this_file.__LINE__." ->CheckPIN respondió: ".print_r($result));
			EmitJSON("No se pudo verificar tu identidad intente mas tarde.");
			return;
		}
	}
}

// Existan o no existan, estos dos tienen que irse.
unset($_SESSION['bypin']['SendPINCount']);
unset($_SESSION['bypin']['SendPINTimer']);


ResponseOk();

?>