<?php
/*
	Genera una contraseña aleatoria. Para todo uso y propósito.
	Created: 2020-03-27
	Author: DriverOp
*/

do {
	$newpass = cSecurity::GenerateRandomPassword();
} while((strlen($newpass) < 8) or (strlen($newpass) > 32)); // Continuar generando hasta que esté dentro del rango.


echo '<json>{"ok":"'.$newpass.'"}</json>';
?>