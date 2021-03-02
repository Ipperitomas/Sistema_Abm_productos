<?php 
require_once(DIR_model."class.categorias.inc.php");
require_once(DIR_model."class.productos.inc.php");

$this_file = substr(__FILE__,strlen(DIR_BASE))." ";

$post = CleanArray($_POST);


$categorias = new cCategorias();
$productos = new cProductos();

if(!$producto_id = SecureInt($post['producto_id'])){
    EmitJSON(" No se encontro el producto");
    return;
}

if(!$datos_producto = $productos->Get($producto_id)){
    EmitJSON(" No se encontro el producto");
    return;
}

$listado_categorias = $categorias->GetCategoriesAll();


?>

<!-- <json>{"ok":"ok"}</json> -->

<div class="modal-header">
    <h4 class="modal-title" id="LabelParametro">Agregar Producto</h4>
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
</div>
<div class="modal-body">
    <form name="frm_edit_product" id="frm_edit_product" class="formulario">
        <input type="hidden" id="id" name="id" value="<?php echo $datos_producto['id']; ?>">
        <div class="row">
            <div class="col-4">
                <label for=""> Codigo</label>
                <input class="form-control" type="text" id="codigo" name="codigo" value="<?php echo (!empty($datos_producto['codigo'])) ? $datos_producto['codigo'] : '' ?> " placeholder="Codigo del producto" >
            </div>
            <div class="col-4">
                <label for=""> Nombre</label>
                <input class="form-control" type="text" id="nombre" name="nombre" value="<?php echo (!empty($datos_producto['nombre'])) ? $datos_producto['nombre'] : '' ?>" placeholder="Nombre del producto">
            </div>
            <div class="col-4">
                <select class="form-control" name="categoria" id="categoria" style="margin-top: 1.9rem !important;">
                   <?php foreach ($listado_categorias as $key => $value) { ?>
                        <option value="<?php echo $value['id']; ?>"><?php echo $value['nombre']; ?></option>
                    <?php } ?>
                </select>
            </div>
            
        </div>

        <div class="row">
            <div class="col-4">
                <label for=""> Marca</label>
                <input class="form-control" type="text" id="marca" name="marca" value="<?php echo (!empty($datos_producto['marca'])) ? $datos_producto['marca'] : '' ?>" placeholder="Marca del producto" >
            </div>
            <div class="col-4">
                <label for=""> Stock </label>
                <input class="form-control" type="number" id="stock" name="stock" value="<?php echo (!empty($datos_producto['stock'])) ? $datos_producto['stock'] : '' ?>" >
            </div>
            <div class="col-4">
                <label for=""> Precio </label>
                <input class="form-control" type="number" id="precio" name="precio" placeholder="Precio del producto" value="<?php echo (!empty($datos_producto['precio'])) ? $datos_producto['precio'] : '' ?>" >
            </div>
        </div>
    </form>
<div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal"> <i class="fas fa-times"></i> Cerrar </button>
    <button type="button" class="btn btn-success" onclick="Producto('edit','frm_edit_product')"> <i class="fas fa-edit"></i> Editar </button>
</div>
