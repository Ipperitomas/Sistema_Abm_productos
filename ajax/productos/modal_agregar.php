<?php 
require_once(DIR_model."class.categorias.inc.php");

$categorias = new cCategorias();

$listado_categorias = array();

$listado_categorias = $categorias->GetCategoriesAll();
?>

<!-- <json>{"ok":"ok"}</json> -->

<div class="modal-header">
    <h4 class="modal-title" id="LabelParametro">Agregar Producto</h4>
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
</div>
<div class="modal-body">
    <form name="frm_add_product" id="frm_add_product" class="formulario">
        <input type="hidden" id="id" name="id" value="<?php "new"; ?>">
        <div class="row">
            <div class="col-4">
                <label for=""> Codigo</label>
                <input class="form-control" type="text" id="codigo" name="codigo" placeholder="Codigo del producto" >
            </div>
            <div class="col-4">
                <label for=""> Nombre</label>
                <input class="form-control" type="text" id="nombre" name="nombre" placeholder="Nombre del producto">
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
                <input class="form-control" type="text" id="marca" name="marca" placeholder="Marca del producto" >
            </div>
            <div class="col-4">
                <label for=""> Stock </label>
                <input class="form-control" type="number" id="stock" name="stock" value="1">
            </div>
            <div class="col-4">
                <label for=""> Precio </label>
                <input class="form-control" type="number" id="precio" name="precio" placeholder="Precio del producto" >
            </div>
        </div>
    </form>
<div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal"> <i class="fas fa-times"></i> Cerrar </button>
    <button type="button" class="btn btn-success" onclick="Producto('add','frm_add_product')"> <i class="fas fa-plus"></i> Agregar </button>
</div>
