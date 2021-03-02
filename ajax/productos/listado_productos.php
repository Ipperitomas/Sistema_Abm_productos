<?php
/*
*/

require_once(DIR_includes."class.sidekick.inc.php");
require_once(DIR_includes."class.listhelper.inc.php");

$this_file = substr(__FILE__,strlen(DIR_BASE))." ";
$ses = @$_SESSION['productos'];



$rpp = $objeto_usuario->rpp;

$pag = (isset($ses['pag']))?$ses['pag']:1;
if (isset($_POST['pag'])) {
	$pag = SecureInt(substr(trim($_POST['pag']),0,11),1);
}

$camposorden = array('codigo','nombre','stock','precio');
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

$select = "SELECT `productos`.* , `categorias`.`nombre` as 'nombre_categoria' ";
$from = "FROM ".SQLQuote(TBL_productos)." AS `productos` INNER JOIN ".SQLQuote(TBL_categorias)." AS `categorias` on `productos`.`categoria_id` = `categorias`.`id` ";
$where = "WHERE 1=1 ";

if (!empty($buscar)) {
	$buscar = $objeto_db->RealEscape($buscar);
	$where .= "AND ((`productos`.`nombre` LIKE '".$buscar."%')) ";
	$where .= "OR ((`productos`.`codigo` LIKE '".$buscar."%')) ";
	$where .= "OR ((`productos`.`precio` LIKE '".$buscar."%')) ";
}

$orderby = 'ORDER BY '.$hlp->MakeOrderBy($ordenes);
$limit = "LIMIT ".(($pag-1)*$rpp).",".$rpp.";";

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
			<th class="text-center <?php $hlp->Orden(0);?>" data-field="0">Codigo</th>
			<th class="text-center" data-field="1">Categoria</th>
			<th class="text-center <?php $hlp->Orden(1);?>" data-field="1">Nombre</th>
			<th class="text-center <?php $hlp->Orden(2);?>" data-field="2">Stock</th>
			<th class="text-center <?php $hlp->Orden(3);?>" data-field="3">Precio</th>
			<!-- <th class="text-center " data-field="5">Fecha Ult. modificación</th> -->
			<th> Editar Producto </th>
			<th> Restar Stock </th>
			<th> Sumar Stock </th>
		</tr>
	</thead>
	<tbody>
<?php
		while($fila = $objeto_db->Next()) { 
?>
		<tr>
			<td class="text-left"><?php echo $hlp->Replace($fila['codigo']); ?></td>
			<td class="text-center"><?php echo $fila['nombre_categoria']; ?></td>
			<td class="text-center"><?php echo $hlp->Replace($fila['nombre']); ?></td>
			<td class="text-center"> <?php echo $fila['stock']; ?></td>
			<td class="text-right"><?php echo "$ ".F($hlp->Replace($fila['precio'])); ?></td>
			<!-- <td class="text-center"><?php echo cFechas::SQLDate2Str($fila['sys_fecha_modif'], CDATE_SHORT); ?></td> -->
			<td class="text-center"><button class="btn btn-sm btn-primary btn-round" title="" onClick="ventana_editar_productos.Show({producto_id:<?php echo $fila['id']; ?>});"><i class="far fa-edit"></i></button></td>
			<td class="text-center"><button class="btn btn-sm btn-danger btn-round" title="Restar Stock" onClick="ManejarStock(<?php echo $fila['stock']; ?>,<?php echo $fila['id']; ?>,'resta');"> <i class="fas fa-minus"></i> </button></td>
			<td class="text-center"><button class="btn btn-sm btn-success btn-round" title="Sumar Stock" onClick="ManejarStock(<?php echo $fila['stock']; ?>,<?php echo $fila['id']; ?>,'sumar');"> <i class="fas fa-plus"></i> </button></td>
		</tr>
<?php
		}
?>
	</tbody>
</table>
</div>
<div class="card-footer row"><div class="col-11"><?php if ($hlp->Paginar()) { $hlp->MostrarPaginacion(); } ?></div><div class="col-1 text-right"><p><small><?php echo ((($pag-1)*$rpp)+1)." - ".(((($pag-1)*$rpp))+$objeto_db->numrows);?> / <span title="Total de registros en esta consulta"><?php echo $objeto_db->cantidad; ?></span></small></p></div></div><?php
	} else {
?>
	<p class="text-center">La consulta no devolvió resultados.</p>
<?php
	}
	
	
	
} catch(Exception $e) {
	EchoLog($e->getMessage());
}


$_SESSION['productos'] = $ses;
?>