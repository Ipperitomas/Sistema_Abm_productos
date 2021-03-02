<?php
/*
	El usuario que quiere recuperar la contraseña mediante Jumble de correo electrónico tiene que ponerlo acá
	
	Created: 2020-03-30
	Author: DriverOp

*/

?>
<h5>Recuperar contraseña</h5>


<div class="card">
	<div class="card-header border-bottom-0">
		<h6>Ingresá el Código</h6>
	</div>
	<div class="card-body">
		<p class="text-info">Ingresá el código alfanumérico que has recibido.</p>
		<p class="text-info">Luego hacé clic en verificar.</p>
		<div class="form-group" title="Escribe el código alfanumérico de ocho cifras">
			<input type="text" name="jumble" id="jumble" class="form-control" title="" placeholder="Código" autofocus />
		</div>
		<div class="text-right mt-1">
			<button type="button" class="btn bg-gradient-primary" onClick="CheckJumble();" title="Verificar Código"><i class="fas fa-check"></i> Verificar...</button>
		</div>
	</div>
</div>


<p class="text-info text-center mt-4 mb-0 fs8"><a href="" onClick="GoBack(); return false;">Volver.</a></p>