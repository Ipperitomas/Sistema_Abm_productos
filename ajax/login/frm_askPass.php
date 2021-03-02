<?php
/*
	El usuario pone acá la contraseña nueva.
	
	Created: 2020-03-27
	Author: DriverOp

*/

unset($_SESSION['bypin'])

?>
<h5>Recuperar contraseña</h5>


<div class="card">
	<div class="card-header border-bottom-0">
		<h6>Ingresá la contraseña</h6>
	</div>
	<div class="card-body">
		<p class="text-info">Ingresá tu nueva contraseña. Debe tener al menos 8 caracteres y no más de 32.</p>
		<p class="text-info">No incluyas espacios en blanco.</p>
		<div class="form-group">
			<label for="newpass1">Escribe tu nueva contraseña</label>
			<input type="password" name="newpass" id="newpass1" class="form-control" title="Escribe la contraseña" placeholder="Contraseña" autofocus maxlength="32" />
		</div>
		<div class="form-group">
			<label for="newpass2">Repite tu nueva contraseña</label>
			<input type="password" name="newpass" id="newpass2" class="form-control" title="Escribe la contraseña" placeholder="Contraseña" maxlength="32" />
		</div>
		<div class="row mt-1">
			<div class="col-6">
				<button type="button" class="btn btn-outline-info btn-xs" onClick="GenNewPass();" title="Generar una contraseña aleatoria"><i class="fas fa-retweet"></i></button>
			</div>
			<div class="col-6 text-right">
				<button type="button" class="btn bg-gradient-primary" onClick="CheckNewPass();" title="Establecer la nueva contraseña"><i class="fas fa-check"></i> Establecer</button>
			</div>
		</div>
	</div>
</div>


<p class="text-info text-center mt-4 mb-0 fs8"><a href="" onClick="GoBack(); return false;">Volver.</a></p>