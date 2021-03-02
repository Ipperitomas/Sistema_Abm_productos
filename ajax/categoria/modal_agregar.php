<?php 


?>

<!-- <json>{"ok":"ok"}</json> -->

<div class="modal-header">
    <h4 class="modal-title" id="LabelParametro">Agregar Categoria</h4>
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Cerrar</span></button>
</div>
<div class="modal-body">
    <form name="frm_add_categories" id="frm_add_categories" class="formulario">
        <input type="hidden" id="id" name="id" value="<?php "new"; ?>">
        <div class="row">
            <div class="col-2"></div>
            <div class="col-8 mb-4">
                <label for=""> Nombre</label>
                <input class="form-control" type="text" id="nombre" name="nombre" placeholder="Nombre de la categoria">
            </div>
        </div>
        <div class="row">
            <div class="card w-100">
                <div class="card-body" id="adjunto_input_constitucion_body">
                    <p class="text-center"> Subir Imagen producto </p>
                    <div id="">
                        <div class="dragable w-100">
                            <div class="w-80" >
                                <div id="bordeado" ondragstart="drag(event);" style="border-width: 2px; border-style: dashed; border-color: #ddd;">
                                    <div id="adjunto_constitucion">
                                        <div class="d-flex justify-content-center mb-4 mt-2">
                                        <!-- <i class="fas fa-cloud-upload fa-3x"></i> -->
                                        <i class="fas fa-cloud-upload-alt fa-3x"></i>
                                        <!-- <i class="fal fa-cloud-upload-alt "></i> -->
                                        </div>
                                        <div class="d-flex justify-content-center ">
                                            <input type="file" class="d-none" id="adjunto_input_constitucion" onchange="ValidadorArchivo(this,'zona_archivos_constitucion');" name="adjunto_input_constitucion">
                                            <h4 id="soltar">Arrastre y suelte el archivo</h4>
                                        </div>
                                        <h4 class="text-center">O</h4>
                                        <div class="d-flex justify-content-center mb-4">
                                        <label for="adjunto_input_constitucion" class="btn bg-gradient-primary"> Presione aqui para subir el archivo </label>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <small > Tipos de archivos soportados : <span id="archivos_permitidos"> PDF </span></small>
                                    <br>
                                    <small > Peso maximo de el archivo : <span id="peso_archivos"> 3MB </span> </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="admite_adjunto">
                        <div class="files w-100" id="zona_archivos_constitucion"></div>
                    </div>
                </div>
            </div>
        </div>
    
    </div>
    </form>
<div class="modal-footer">
    <button type="button" class="btn btn-danger" data-dismiss="modal"> <i class="fas fa-times"></i> Cerrar </button>
    <button type="button" class="btn btn-success" onclick="Categorias('add','frm_add_categories')"> <i class="fas fa-plus"></i> Agregar </button>
</div>
