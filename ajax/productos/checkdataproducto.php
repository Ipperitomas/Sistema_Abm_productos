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
    $tipo = "add";
}

$codigo = mb_substr($post['codigo'],0,500);
$categoria_id = SecureInt($post['categoria'],1);
$marca = SecureInt($post['categoria'],1);

$nombre = mb_substr($post['nombre'],0,500);

if(empty($nombre)){
    $msgerr['nombre'] = "Debe indicar el nombre del producto";
}

if($stock = SecureInt($post['stock'],false)){
    $msgerr['stock'] = "Debe indicar un monto en stocl";
}


$precio = mb_substr($post['precio'],0,500);

if(empty($precio)){
    $msgerr['precio'] = "Debe indicar el precio del producto";
}

if(CanUseArray($msgerr)){
    EmitJSON($msgerr);
    return;
}

$id = mb_substr($post['id'],0,100);

$reg = array();

$reg['categoria_id'] = $categoria_id;
$reg['codigo'] = $codigo;
$reg['nombre'] = $nombre;
$reg['stock'] = $stock;
$reg['precio'] = $precio;

if($tipo == "add"){
    if(!$productos->Create($reg)){
        EmitJSON("No se pudo crear su producto");
        return;
    }
}else{
    if(!$productos->Save($reg,$id)){
        EmitJSON("No se pudo actualizar su producto");
        return;
    }
}

ResponseOk();
?>