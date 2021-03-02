<?php
//ShowVar($_POST);
/*
    [username] => DriverOp
    [password] => pwddfr
*/
$this_file = substr(__FILE__,strlen(DIR_BASE))." ";
require_once(DIR_includes."class.checkinputs.inc.php");

$post = array('username'=>null,'password'=>null);
foreach($_POST as $key => $value) {
	if (array_key_exists($key,$post)) {
		$post[$key] = substr(trim($value),0,32);
	}
}

$msgerr = array();

cCheckInput::Nick($post['username'],'username','Nombre de usuario');
cCheckInput::Password($post['password'],'password','Contraseña');

if (count($msgerr) > 0) {
	echo '<json>{"dataerr":'.json_encode($msgerr).'}</json>';
	return;
}

require_once(DIR_model."class.usuarios_backend.inc.php");

$usuario = new cUsrBackend($objeto_db);

if ($usuario->GetByUsername($post['username'])) {
	if ($usuario->ValidPass($post['password'])) {
		if ($usuario->Login()) {
			echo '<json>{"ok":"ok"}</json>';
			cLogging::Write($this_file." ".__LINE__." ".$post['username']." ingresó correctamente.");
			return;
		} else {
			echo '<json>{"generr":"Tenemos problemas para validar usuarios.<br />Revisar los registros de actividad."}</json>';
			cLogging::Write($this_file." ".__LINE__." ".$post['username']." no pudo ingresar correctamente.");
			return;
		}
	} else {
		echo '<json>{"dataerr":{"password":"Contraseña no válida."}}</json>';
		cLogging::Write($this_file." ".__LINE__." ".$post['username']." metió una contraseña inválida.");
		return;
	}
} else {
	echo '<json>{"dataerr":{"username":"Usuario inexistente."}}</json>';
	cLogging::Write($this_file." ".__LINE__." ".$post['username']." es usuario inexistente.");
	return;
}

?><json>{"generr":"Esto es una joda."}</json>