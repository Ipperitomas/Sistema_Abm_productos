<?php
/*
	El usuario que quiere recuperar la contraseña mediante PIN tiene que ponerlo acá
	
	Created: 2020-03-26
	Author: DriverOp

*/

if (!isset($_SESSION['bypin'])) {
	EmitJSON('Ha tardado demasiado tiempo en verificar el PIN. Vuelva a intentarlo');
	return;
}

?>
<h5>Recuperar contraseña</h5>


<div class="card">
	<div class="card-header border-bottom-0">
		<h6>Ingresá el PIN</h6>
	</div>
	<div class="card-body">
		<p class="text-info">Ingresá el número PIN que has recibido.</p>
		<p class="text-info">Luego hacé clic en verificar.</p>
		<div class="form-group">
			<input type="text" name="pin" id="pin" class="form-control" title="Número de PIN" placeholder="PIN" autofocus />
		</div>
		<div class="text-right mt-1">
			<button type="button" class="btn bg-gradient-primary" onClick="CheckPIN();" title="Verificar PIN"><i class="fas fa-check"></i> Verificar...</button>
		</div>
	</div>
</div>


<p class="text-info text-center mt-4 mb-0 fs8"><a href="" onClick="GoBack(); return false;">Volver.</a></p>