<?php
/*
*/
require_once(DIR_model."class.productos.inc.php");
$this_file = substr(__FILE__,strlen(DIR_BASE))." ";

$post = CleanArray($_POST);

$msgerr = array();

$productos = new cProductos();
$tipo = mb_substr($post['tipo'],0,15);
if(empty($tipo)){
    $tipo = "resta";
}
$producto_id = mb_substr($post['id'],0,20);
if(empty($producto_id)){
    EmitJSON("No se encontro el producto");
    return;
}

if(!$datos_producto = $productos->Get($producto_id)){
    EmitJSON(" No se encontro el producto ");
    return;
}

$cant_stock = mb_substr($post['cantidad'],0,255);
if($tipo == "resta"){
    $cant_stock = $datos_producto['stock'] - $cant_stock;
    if($cant_stock < 0){
        EmitJSON(" Cantidad invalida para poner en stock");
        return;
    }
}
if($tipo == "sumar"){
    $cant_stock = $datos_producto['stock'] + $cant_stock ;
}


$reg = array();


$reg['stock'] = $cant_stock;


if(!$productos->Save($reg,$producto_id)){
    EmitJSON("No se pudo actualizar su producto");
    return;
}

ResponseOk();
?>