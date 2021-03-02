<?php
/*
*/
require_once(DIR_model."class.categorias.inc.php");
$this_file = substr(__FILE__,strlen(DIR_BASE))." ";

$post = CleanArray($_POST);

$msgerr = array();

$categoria = new cCategorias();
$tipo = mb_substr($post['tipo'],0,15);
if(empty($tipo)){
    $tipo = "add";
}


$nombre = mb_substr($post['nombre'],0,500);

if(empty($nombre)){
    $msgerr['nombre'] = "Debe indicar el nombre del producto";
}


$id = mb_substr($post['id'],0,100);

$reg = array();

$reg['nombre'] = $nombre;

if($tipo == "add"){
    if(!$categoria->Create($reg)){
        EmitJSON("No se pudo crear su categoria");
        return;
    }
}else{
    if(!$categoria->Save($reg,$id)){
        EmitJSON("No se pudo actualizar su producto");
        return;
    }
}

ResponseOk();
?>