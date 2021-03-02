<?php
/*
	
*/

require_once(DIR_includes."class.sidekick.inc.php");
require_once(DIR_includes."class.listhelper.inc.php");

$this_file = substr(__FILE__,strlen(DIR_BASE))." ";
$ses = @$_SESSION['categorias'];



$rpp = $objeto_usuario->rpp;
$pag = (isset($ses['pag']))?$ses['pag']:1;
if (isset($_POST['pag'])) {
	$pag = SecureInt(substr(trim($_POST['pag']),0,11),1);
}
$camposorden = array('id','nombre','sys_fecha_modif');
$ordenes = (isset($ses['ord']))?$ses['ord']:array('nombre'=>'ASC');
$buscar = (!empty($ses['buscar']))?$ses['buscar']:'';
if (isset($_POST['buscar'])) {
	$aux = mb_substr(trim($_POST['buscar']),0,30);
	if (!empty($aux)) {
		$buscar = mb_strtolower($aux);
	} else {
		$buscar = '';
	}
	$pag = 1;
	$ses['buscar'] = $buscar;
}

if (isset($_POST['ord'])) {
	$campo = SecureInt(mb_substr(trim($_POST['ord']),0,11),NULL);
	if (!is_null($campo)) {
		if (isset($camposorden[$campo])) {
			$idx = $camposorden[$campo];
			if (array_key_exists($idx, $ordenes)) {
				$aux = ($ordenes[$idx] == 'DESC')?'ASC':'DESC';
				unset($ordenes[$idx]);
			} else {
				$aux = 'ASC';
			}
			$ordenes = array_reverse($ordenes,true);
			$ordenes[$idx] = $aux;
			$ordenes = array_reverse($ordenes,true);
			$ses['ord'] = $ordenes;
		} else {
			$campo = NULL;
		}
	}
}
$estado = (!empty($ses['estado']))?$ses['estado']:array();
if (isset($_POST['estado'])) {
	$estado = array();
	$aux = $_POST['estado'];
	if (CanUseArray($aux)) {
		foreach($aux as $value) {
			$value = strtoupper(substr($value,0,5));
			if (array_key_exists($value, $estados_validos)) { $estado[] = $value; }
		}
	}
	$pag = 1;
	$ses['estado'] = $estado;
}
	
$grupo = (!empty($ses['grupo']))?$ses['grupo']:'';
if (isset($_POST['grupo'])) {
	$grupo = array();
	$aux = $_POST['grupo'];
	if (CanUseArray($aux)) {
		foreach ($aux as $value) {
			$value = substr($value,0,11);
			if (CheckInt($value)) {
				$grupo[] = $value;
			}
		}
	}
	$pag = 1;
	$ses['grupo'] = $grupo;
}

$hlp = new ListHelper();
$hlp->ordenClass = 'col-order';
$hlp->ordenes = $ordenes;
$hlp->camposorden = $camposorden;
$hlp->OrdenwithTitle = true;
$hlp->ItemsPorPagina = $rpp;
$hlp->PaginaActual = $pag;
$hlp->Rango = 3;
$hlp->CantExtremos = 2;
$hlp->buscar = $buscar;
$hlp->ReplaceTag = array('<b class="red">','</b>');

$select = "SELECT `categorias`.* ";
$from = "FROM ".SQLQuote(TBL_categorias)." AS `categorias` ";
$where = " WHERE 1=1 ";


if (!empty($buscar)) {
	$buscar = $objeto_db->RealEscape($buscar);
	$where .= "AND ((`categorias`.`nombre` LIKE '".$buscar."%')) ";
}

$orderby = 'ORDER BY '.$hlp->MakeOrderBy($ordenes);
$limit = "LIMIT 10; ";

$sql = $select.$from.$where.$orderby.$limit;

try {
	// EchoLog($sql);
	$objeto_db->Query($sql, true);
	if ($objeto_db->error) { throw new Exception(__LINE__." DBErr: ".$objeto_db->errmsg); }
	
	if ($objeto_db->cantidad > 0) {
		$hlp->ItemsTotales = $objeto_db->cantidad;
?>
<div class="card-body">
<table class="table table-striped tabla-general" id="tbl_cuentas">
	<thead class="thead-light">
		<tr>
			<th class=" <?php $hlp->Orden(0);?>" data-field="0">Codigo</th>
			<th class=" <?php $hlp->Orden(1);?>" data-field="1">Nombre</th>
			<th class=" <?php $hlp->Orden(2);?>" data-field="2">Ultima modificación</th>
		</tr>
	</thead>
	<tbody>
<?php
		while($fila = $objeto_db->Next()) { 
?>
		<tr >
			<td ><?php echo $fila['id']; ?></td>
			<td ><?php echo $hlp->Replace($fila['nombre']); ?></td>
			<td ><?php echo cFechas::SQLDate2Str($fila['sys_fecha_modif'], CDATE_SHORT); ?></td>
		</tr>
<?php
		}
?>
	</tbody>
</table>
</div>
<?php
	} else {
?>
	<p class="text-center">La consulta no devolvió resultados.</p>
<?php
	}
	
	
	
} catch(Exception $e) {
	EchoLog($e->getMessage());
}


$_SESSION['categorias'] = $ses;
?>