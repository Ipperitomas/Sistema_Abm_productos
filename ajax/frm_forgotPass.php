<?php
$this_file = substr(__FILE__,strlen(DIR_BASE))." ";

if (!isset($_SESSION['bypin'])) { $_SESSION['bypin'] = array('SendPINCount'=>0); }

?>

<h5>Recuperar contraseña</h5>

<div class="card card-primary card-outline card-outline-tabs border-top-0">
	<div class="card-header p-0 border-bottom-0">
		<ul class="nav nav-tabs" role="tablist">
			<li class="nav-item">
				<a class="nav-link active" data-toggle="pill" role="tab" href="#by-sms" id="by-sms-tab" aria-controls="by-sms" aria-selected="true" title="PIN a teléfono celular">Por SMS</a>
			</li>
			<li class="nav-item">
				<a class="nav-link" data-toggle="pill" role="tab" href="#by-email" id="by-email-tab" aria-controls="by-email" aria-selected="false" title="Validación por correo electrónico">Por Correo electrónico</a>
			</li>
		</ul>
	</div>
	<div class="card-body">
		<div class="tab-content">
			<div class="tab-pane fade active show" id="by-sms">
				<p class="text-info">A continuación, ingresá el <b>número de teléfono celular</b> donde te enviaremos un número "PIN".</p>
				<p class="text-info">Debe ser el mismo número de teléfono registrado a tu usuario.</p>
				<div class="form-inline text-center pb-1">
					<input type="text" name="codigo_area" id="codigo_area" class="form-control input-appended" title="Código de área" placeholder="Sin el cero inicial" autofocus size="8" maxlength="4" />
					<input type="text" name="numero_abonado" id="numero_abonado" class="form-control input-prepended" title="Número de abonado" placeholder="Sin el 15 inicial" size="14" maxlength="8"/>
				</div>
				<div class="text-right mt-1">
					<button type="button" class="btn bg-gradient-primary" id="btn-acceder" onClick="CheckTel();" title="Enviar número de pin"><i class="fas fa-mobile-alt"></i> Enviar</button>
				</div>
			</div>
			<div class="tab-pane fade" id="by-email">
				<p class="text-info">A continuación, ingresá la <b>dirección de correo electrónico</b> de tu usuario.</p>
				<p class="text-info">Se enviará un mensaje con instrucciones para completar el proceso de recuperación de contraseña.</p>
				<div class="form-group">
					<input type="email" name="email" id="email" class="form-control" title="Correo electrónico" placeholder="Correo electrónico" autofocus />
				</div>
				<div class="text-right mt-1">
					<button type="button" class="btn bg-gradient-primary" onClick="CheckByEmail();" title="Enviar mensaje"><i class="fas fa-paper-plane"></i> Enviar...</button>
				</div>
			</div>
		</div>
	</div>
</div>

<p class="text-info text-center mt-4 mb-0 fs8"><a href="" onClick="GoBack(); return false;">Volver.</a></p>

