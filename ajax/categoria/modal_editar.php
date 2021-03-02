<?php 
require_once(DIR_model."class.categorias.inc.php");
require_once(DIR_model."class.productos.inc.php");

$this_file = substr(__FILE__,strlen(DIR_BASE))." ";

$post = CleanArray($_POST);


$categorias = new cCategorias();

if(!$categoria_id = SecureInt($post['id'])){
    EmitJSON(" No se encontro la categoria");
    return;
}

if(!$datos_categoria = $categorias->Get($categoria_id)){
    EmitJSON(" No se encontro el producto");
    return;
}


?>

<!-- <json>{"ok":"ok"}</json> -->

<div class="modal-header">
    <h4 class="modal-title" id="LabelParametro">Agregar Producto</h4>
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
</div>
<div class="modal-body">
    <form name="frm_edit_product" id="frm_edit_product" class="formulario">
        <input type="hidden" id="id" name="id" value="<?php echo $datos_categoria['id']; ?>">
        <div class="row">
            <div class="col-2">
            </div>
            <div class="col-8 mb-4">
                <label for=""> Nombre</label>
                <input class="form-control" type="text" id="nombre" name="nombre" value="<?php echo (!empty($datos_categoria['nombre'])) ? $datos_categoria['nombre'] : '' ?>" placeholder="Nombre del producto">
            </div>
        </div>
    </form>
<div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal"> <i class="fas fa-times"></i> Cerrar </button>
    <button type="button" class="btn btn-success" onclick="Categorias('edit','frm_edit_product')"> <i class="fas fa-edit"></i> Editar </button>
</div>
