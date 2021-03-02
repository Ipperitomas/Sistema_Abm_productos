;/*
	Inicio
*/

<?php
require_once(DIR_js."listados.js");


?>

var listado_productos = new mgrListadoCreator({
	archivo: 'listado_productos',
    content: 'inicio',
    divListIdName : 'list_productos'
});
var listado_categorias = new mgrListadoCreator({
	archivo: 'listado_categorias',
    content: 'inicio',
    divListIdName : 'list_categorias'
});

window.onload = function(){
	listado_productos.Get();
	listado_categorias.Get();
}