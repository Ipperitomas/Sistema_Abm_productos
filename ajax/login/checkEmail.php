<?php
/*
	Verifica que el correo electrónico indicado en la recuperación de contraseña en el login sea correcto y de ser así, envía un mensaje de recuperacion usando Infobip Email.
	
	Created: 2020-03-30
	Author: DriverOp

*/
require_once(DIR_includes."class.checkinputs.inc.php");
require_once(DIR_includes."class.sidekick.inc.php");
require_once(DIR_model."class.simpleUsr.inc.php");

$this_file = substr(__FILE__,strlen(DIR_BASE))." ";

$usuario = new cSimpleUsr();

$post = CleanArray($_POST);
$msgerr = array();

if (empty($post['email'])) {
	EmitJSON('Debes indicar una dirección de correo electrónico.');
	return;
}

$email = mb_substr($post['email'],0,255);
if (cCheckInput::Email($email, 'email', 'Correo electrónico')) {
	$usuario->GetByEmail($email);
	if (!$usuario->existe) {
		EmitJSON('No hay restroado ningún usuario con la dirección de correo electrónico indicada.');
		return;
	}
}
$msgerr = array_merge($msgerr, cCheckInput::$msgerr);
if (CanUseArray($msgerr)) {
	EmitJSON($msgerr);
	return;
}

$usuario->SetJumble(8);


require_once(DIR_model."class.wsclient.inc.php");

$wsclient = new cWsClient();

$wsclient->debug_level = 1;
cLogging::Write($this_file." Ejecutando envío de mensaje de recuperación. Email: ".$usuario->email);

$result = $wsclient->SendEmailRecovery($usuario->id);

cLogging::Write($this_file." result: ".print_r($result,true));
if ($result->result == false) {
	cLogging::Write($this_file.__LINE__." ->SendEmailRecovery respondió: ".print_r($result));
	EmitJSON("No se pudo enviar el Mensaje.");
	return;
}

ResponseOk();
?>