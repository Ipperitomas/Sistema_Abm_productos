
<?php
require_once(DIR_js."listados.js");
require_once(DIR_js."modalBsLte.js");
require_once(DIR_js."sweetalert2.all.min.js");
require_once(DIR_js."sweetalert.min.js");

?>

var evalresult = new mgrEvalResult;
var rulo = new fmdRulo();

var listado = new mgrListadoCreator({
	archivo: 'listado_categorias',
    content: 'categoria',
    divListIdName : 'list_categorias'
});
window.onload = function(){
    listado.Get();
}

var ventana_agregarcategorias = new modalBsLTECreator({
    archivo: 'modal_agregar',
    content: 'categoria',
})

var ventana_editarcategorias = new modalBsLTECreator({
    archivo: 'modal_editar',
    content: 'categoria',
})


function Categorias(tipo,id_frm){
    result = true;
    if(tipo && id_frm){
        frm = document.getElementById(id_frm);
        if(frm){
            if(tipo == "add"){
                ele = frm.nombre;
                if (ele.value.trim().length == 0) {
                    result = $(ele).msgerr('Debe indicar un nombre a el Categoria');
                }
            }

            if(result){
                $(frm).fmdFormSend({
                    url: '<?php echo URL_ajax; ?>',
                    extraData: {
                        archivo: 'checkdatacategoria',
                        content: 'categoria',
                        tipo : tipo
                    },
                    onStart: function() {
                        rulo.Show();
                    },
                    onFinish: function(a, b, c, d) {
                        rulo.Hide();
                        if (evalresult.Eval(c)) {
                            if(tipo == "add"){
                                ventana_agregarcategorias.Hide();
                            }else{
                                ventana_editarcategorias.Hide();
                            }
                            listado.Get();
                        }
                    }
                });

            }
        }
    }
}