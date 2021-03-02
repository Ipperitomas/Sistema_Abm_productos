<?php
/*
	Verifica que el teléfono indicado en la recuperación de contraseña en el login sea correcto y de ser así, envía un PIN usando Infobip.
	
	Created: 2020-03-26
	Author: DriverOp

*/
require_once(DIR_includes."class.checkinputs.inc.php");
require_once(DIR_includes."class.sidekick.inc.php");
require_once(DIR_model."class.simpleUsr.inc.php");

$this_file = substr(__FILE__,strlen(DIR_BASE))." ";

$post = CleanArray($_POST);

//ShowVar($post);

$msgerr = array();

$check_codigo_area = false;
$check_numero_abonado = false;
$msgerr['codigo_area'] = 'Código de Área no es válido. Deben ser entre 2 y 4 números.';

$codigo_area = cSideKick::FilterNumber(substr(@$post['codigo_area'],0,4));
	if ($codigo_area === false) {
		$msgerr['codigo_area'] = "Debe ser un número.";
	} else {
		if (empty($codigo_area)) {
			$msgerr['codigo_area'] = "Debes indicar Código de Área.";
		} else {
			if (substr($codigo_area, 0, 1) == '0') {
				$msgerr['codigo_area'] = "No escribas el cero.";
			} else {
				$check_codigo_area = true;
			}
		}
	}

$numero_abonado = cSideKick::FilterNumber(substr(@$post['numero_abonado'],0,8));
	if (preg_match("/^0+/",$numero_abonado)) {
		$msgerr['numero_abonado'] = 'No puede comenzar con cero';
	} else {
		$check_numero_abonado = cCheckInput::Tel($numero_abonado, 'numero_abonado', 'abonado');
		if (substr($numero_abonado, 0, 2) == '15') {
			$msgerr['numero_abonado'] = "No escribas el 15 inicial.";
		} else {
			$check_numero_abonado = true;
		}
	}

	if ($check_codigo_area) {
		$len_codigo_area = strlen($codigo_area);
		if (($len_codigo_area < 2) or ($len_codigo_area > 4)) {
			$msgerr['codigo_area'] = 'Código de Área no es válido. Deben ser entre 2 y 4 números.';
		} else {
			unset($msgerr['codigo_area']);
			if ($check_numero_abonado) {
				$len_numero_abonado = strlen($numero_abonado);
				switch ($len_codigo_area) {
					case 2:
						if ($len_numero_abonado != 8) { $msgerr['numero_abonado'] = 'Deben ser 8 números'; }
					break;
					case 3:
						if ($len_numero_abonado != 7) { $msgerr['numero_abonado'] = 'Deben ser 7 números'; }
					break;
					case 4:
						if ($len_numero_abonado != 6) { $msgerr['numero_abonado'] = 'Deben ser 6 números'; }
					break;
				}
			}
		}
	}
$msgerr = array_merge($msgerr, cCheckInput::$msgerr);
if (CanUseArray($msgerr)) {
	EmitJSON($msgerr);
	cLogging::Write($this_file.' Errores: '.print_r($msgerr, true));
}

$tel = $codigo_area.'-'.$numero_abonado;
$usuario = new cSimpleUsr();
$usuario->GetByTel($tel);
if (!$usuario->existe) {
	EmitJSON('El número de teléfono ingresado no está registrado a ningún usuario.');
	return;
}

$_SESSION['recover_user'] = $usuario->id;

$telefono_internacional = '54'.$codigo_area.$numero_abonado;
if (isset($_SESSION['bypin']['tel']) and ($_SESSION['bypin']['tel'] == $telefono_internacional)) {
	$_SESSION['bypin']['SendPINCount'] = 0;
}

$_SESSION['bypin']['tel'] = $telefono_internacional;

require_once(DIR_model."class.wsclient.inc.php");

$wsclient = new cWsClient();

$wsclient->debug_level = 1;
cLogging::Write($this_file." Ejecutando Consulta de pin a infobip. Tel: ".$telefono_internacional);

$result = $wsclient->notificadorSendPIN($telefono_internacional);

cLogging::Write($this_file." result: ".print_r($result,true));

if ($result->result == false) {
	cLogging::Write($this_file.__LINE__." ->SendPIN respondió: ".print_r($result));
	EmitJSON("No se pudo enviar el PIN.");
	return;
} else {
	if (is_null($result->data)) {
		cLogging::Write($this_file.__LINE__." ->SendPIN respondió: ".print_r($result));
		EmitJSON("No se pudo enviar el PIN.");
		return;
	} else {
		$_SESSION['bypin']['SendPINTimer']  = time(); // Esto es más preciso.
		if (!empty($result->data->PIN)) {
			$_SESSION['bypin']['PIN'] = $result->data->PIN;
		} else {
			if (!empty($result->data->pinId)) {
				$_SESSION['bypin']['PIN'] = $result->data->pinId;
			}
		}
	}
}

cLogging::Write($this_file." _SESSION['bypin']: ".print_r($_SESSION['bypin'],true));
$_SESSION['bypin']['SendPINCount'] = $_SESSION['bypin']['SendPINCount']+1;

ResponseOk();


?>