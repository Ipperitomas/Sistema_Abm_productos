
<?php
require_once(DIR_js."listados.js");
require_once(DIR_js."modalBsLte.js");
require_once(DIR_js."sweetalert2.all.min.js");
require_once(DIR_js."sweetalert.min.js");

?>

var evalresult = new mgrEvalResult;
var rulo = new fmdRulo();

var listado = new mgrListadoCreator({
	archivo: 'listado_productos',
    content: 'productos',
    divListIdName : 'list_products'
});
window.onload = function(){
    listado.Get();
}

var ventana_agregar_productos = new modalBsLTECreator({
    archivo: 'modal_agregar',
    content: 'productos',
})

var ventana_editar_productos = new modalBsLTECreator({
    archivo: 'modal_editar',
    content: 'productos',
})


function Producto(tipo,id_frm){
    result = true;
    if(tipo && id_frm){
        frm = document.getElementById(id_frm);
        if(frm){
            if(tipo == "add"){
                ele = frm.nombre;
                if (ele.value.trim().length == 0) {
                    result = $(ele).msgerr('Debe indicar un nombre a el producto');
                }

                ele = frm.stock;
                if (ele.value.trim().length == 0) {
                    result = $(ele).msgerr('Debe indicar una cantidad de productos');
                }
                if(ele.value < 0){
                    result = $(ele).msgerr('La cantidad no puede ser menor a 0');
                }
            }

            if(result){
                $(frm).fmdFormSend({
                    url: '<?php echo URL_ajax; ?>',
                    extraData: {
                        archivo: 'checkdataproducto',
                        content: 'productos',
                        tipo : tipo
                    },
                    onStart: function() {
                        rulo.Show();
                    },
                    onFinish: function(a, b, c, d) {
                        rulo.Hide();
                        if (evalresult.Eval(c)) {
                            if(tipo == "add"){
                                ventana_agregar_productos.Hide();
                            }else{
                                ventana_agregar_productos.Hide();
                            }
                            listado.Get();
                        }
                    }
                });

            }
        }
    }
}

function HabilitarInput(input_id,id_ele){
    console.log(input_id);

    if(input_id && id_ele){
        input = document.getElementById(input_id+"_edit_"+id_ele);
        console.log(input);
        if(input){
            document.getElementById(input_id+"_edit_"+id_ele).classList.remove("d-none");
            document.getElementById("muestra_"+input_id).classList.add("d-none");
            document.getElementById("check_"+input_id).classList.remove("d-none");
        }
    }
}


function ManejarStock(cant_stock,id,tipo){
    Swal.fire({
        title: 'Ingrese la cantidad a '+tipo+' Stock Actual ('+cant_stock+')',
        input: 'text',
        inputLabel: 'Resta',
        inputText : cant_stock,
        showCancelButton: true,
        inputValidator: (value) => {
          if (!value) {
            return 'Debe ingresarle un nombre a el archivo!'
          }
            var patt = new RegExp(/^[0-9]+$/);
            var res = patt.test(value);
            if(!res){
                return 'Cantidad Invalida';
            }
            if(value > cant_stock){
                return 'Cantidad Invalida, debe ser menor a la cantidad actual';
            }
        },
      }).then((result) => {
        console.log(result);
        if (result.isConfirmed) {
            getAjax({
                archivo: 'manejarstock',
                content : 'productos',
                extraparams : {tipo:tipo,cantidad:result.value,id:id}
            },function (a,b,c,d) {
                if (a == 200) {
                    if (evalresult.Eval(c)) {
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.addEventListener('mouseenter', Swal.stopTimer)
                                toast.addEventListener('mouseleave', Swal.resumeTimer)
                            }
                        });

                        Toast.fire({
                            icon: 'success',
                            title: 'Se actualizo sl stock correctamente '
                        });
                        listado.Get();

                    }
                }
            });
          
        }
      })

}