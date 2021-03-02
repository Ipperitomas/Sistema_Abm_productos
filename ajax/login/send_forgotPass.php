<?php
require(DIR_model."class.checkinputs.inc.php");
require(DIR_model."class.simpleUsr.inc.php");
require_once(DIR_model."class.configs.inc.php");
$this_file = substr(__FILE__,strlen(DIR_BASE))." ";

$email = @$_POST['email'];

$email = trim($email);
$email = mb_substr($email,0,75);

if (empty($email)) {
	EmitJSON(array('email'=>'Ingresa una dirección de correo electrónico'));
	cLogging::Write($this_file.__LINE__." Email está vacío.");
	return;
}

$check = new cCheckInputs();
$check->modoverbal = MV_COLOQUIAL;

if (!$check->Email($email,'email','correo electrónico')) {
	EmitJSON($check->msgerr);
	cLogging::Write($this_file.__LINE__." Email no válido.");
	return;
}

$usr = new cSimpleUsr($objeto_db);

$usr->GetByEmail($email);

if (!$usr->existe) {
	EmitJSON(array('email'=>'Esta dirección no pertenece a ningún usuario.'));
	cLogging::Write($this_file.__LINE__." Usuario no encontrado por email: ".$email);
	return;
}

$emailconf = new cConfig(DIR_config."emailconf_localhost.json");
$CanSendEmail = false;
if ($emailconf->Get('sendmail')) {
	if ($emailconf->Get('forgotpass')->sendmail) {
		$CanSendEmail = true;
	}
}
if (!$CanSendEmail) {
	EmitJSON('Ups!, el envío de correos está desactivado en la configuración.');
	cLogging::Write($this_file.__LINE__." Envío de correo desactivado por configuración.");
	return;
}

cLogging::Write($this_file.__LINE__." Usando email config: ".$emailconf->FileName);

$config_email = $emailconf->Get('forgotpass');

if (!isset($cfiles)) { $cfiles = 0; }

$templatehtm = DIR_plantillas.@$config_email->templatehtm;
$templatetxt = DIR_plantillas.@$config_email->templatetxt;

if (ExisteArchivo($templatehtm)) {
		$cfiles++;
		$textohtm = file_get_contents($templatehtm);
} else {
		cLogging::Write($this_file.__LINE__." ".$config_email->templatehtm." is missing.");
	}

if (ExisteArchivo($templatetxt)) {
		$cfiles++;
		$textotxt = file_get_contents($templatetxt);
} else {
		cLogging::Write($this_file.__LINE__." ".$config_email->templatetxt." is missing.");
}

if ($cfiles == 0) {
	EmitJSON('No puedo enviar el correo. Hay un fallo en la configuración del sistema.');
	cLogging::Write($this_file.__LINE__." Can't send mail, both templates are missing. I give up.");
	$exit = true;
	return;
}

$jumble = $usr->CreateRecovery();
if ($jumble == false) {
	EmitJSON('Un fallo interno impide que el proceso continue.');
	cLogging::Write($this_file.__LINE__." Falló la creación del jumble para el usuario ".$usr->username);
	$exit = true;
	return;
}

$nomapepersona = $usr->nombre_apellido;
$subject = $config_email->subject;

require(DIR_includes."class.phpmailer.php");
require(DIR_includes."class.smtp.php");

$mail = new PHPMailer();

	$mail->SMTPDebug = ($emailconf->Get('Debug') === true);
	if ($mail->SMTPDebug and DEVELOPE) {
		$mail->Debugoutput = function ($str, $level) {
			global $this_file;
			$str = trim($str);
			cLogging::Write($this_file." ".$str);
		};
	}
	$mail->CharSet = "utf-8";
	$mail->IsSMTP(); // Se envia por SMTP
	$mail->Host = $emailconf->Get('smtphost');  // La dirección del servidor SMTP
	$mail->Port = $emailconf->Get('port');  // El puerto SMTP
	if ($emailconf->Get('Authreq') == true) {
		$mail->SMTPAuth = true;     // Requiere autorización?
		$mail->Username = $emailconf->Get('user');  // Nombre de usuario de la cuenta 
		$mail->Password = $emailconf->Get('pass'); // La contraseña correspondiente
	} else {
		$mail->SMTPAuth = false;
	}
	$mail->SMTPSecure = $emailconf->Get('secure');

	$mail->From = $emailconf->Get('mailfrom'); // ¿Evita que el servidor rechace correos que no le pertenecen?
	$mail->FromName = $nomapepersona;
	$mail->WordWrap = 70;                                 
	$mail->Subject = sprintf(@$config_email->subject, $subject);
	$mail->ClearAddresses();
	$mail->ClearReplyTos();
	$mail->ClearAttachments();
	$mail->AddReplyTo('noreply@'.parse_url(BASE_URL, PHP_URL_HOST));

	if (!empty($textohtm)) {
		$mail->CharSet = "utf-8";
		$mail->IsHTML(true);
		
		$textohtm = str_replace('[fechahora]',Date('Y-m-d H:i:s'), $textohtm);
		$textohtm = str_replace('[fecha]',cFechas::SQLDate2Str(Date('Y-m-d H:i:s')), $textohtm);
		$textohtm = str_replace('[url]',BASE_URL.'recovery/'.$jumble, $textohtm);
		$textohtm = preg_replace('/\[(.*?)\]/is',null,$textohtm);
		$mail->Body = $textohtm;
	}
	if (!empty($textotxt)) {
		$mail->CharSet = "utf-8";
		$textotxt = str_replace('[fechahora]',Date('Y-m-d H:i:s'), $textotxt);
		$textotxt = str_replace('[fecha]',cFechas::SQLDate2Str(Date('Y-m-d H:i:s')), $textotxt);
		$textotxt = str_replace('[url]',BASE_URL.'recovery/'.$jumble, $textotxt);
		$textotxt = preg_replace('/\[(.*?)\]/is',null,$textotxt);
		if (empty($textohtm)) {
			$mail->Body = $textotxt;
		} else {
			$mail->AltBody = $textotxt;
		}

	}
	
	$mail->AddAddress($usr->email);
	cLogging::Write($this_file.__LINE__." Destinatario: ".$usr->email);

	$msg = "Mail sent OK.";

	$result = $mail->Send();
	if (!$result) {
		$msg = "Mail not sent. \r\n";
		if ($mail->SMTPDebug) {
			$msg .= print_r($mail->ErrorInfo,true);
		}
		EmitJSON('Lo siento. Falló el envío del correo. Intenta más tarde.');
	}
	cLogging::Write($this_file.__LINE__." ".$msg);
	
	if (!empty($textotxt)) {
		cLogging::Write($this_file.__LINE__." ".$textotxt);
	} else {
		cLogging::Write($this_file.__LINE__." ".$textotxt);
	}
?><!-- <json>{"ok":"ok"}</json> -->
<p>Un mensaje fue enviado a la dirección proporcionada.</p>
<p>&nbsp;</p>
<p>Por favor sigue las instrucciones que allí se indican.</p>
<p>&nbsp;</p>
<p class="forgot"><a href="" onClick="GoBack(); return false;">Volver.</a></p>