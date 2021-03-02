;/* ********************************************************************************************* */
function ConstructorXMLHttpRequest() {
if(window.XMLHttpRequest) { return new XMLHttpRequest(); } 
else if(window.ActiveXObject) { var versionesObj = new Array(
'Msxml2.XMLHTTP.5.0',
'Msxml2.XMLHTTP.4.0',
'Msxml2.XMLHTTP.3.0',
'Msxml2.XMLHTTP',
'Microsoft.XMLHTTP');
     for (var i = 0; i < versionesObj.length; i++) {
       try {
           return new ActiveXObject(versionesObj[i]);
           }
      catch (errorControlado) 
      {
    }
  }
}
throw new Error("No se pudo crear el objeto XMLHttpRequest");
}
function objetoAjax(metodo) { 
  this.objetoRequest = new ConstructorXMLHttpRequest(); 
  this.metodo = metodo;
}
function peticionAsincrona(url,valores) { 
  var objetoActual = this;
  this.objetoRequest.open(this.metodo, url, true);
  this.objetoRequest.onreadystatechange = function() {
         switch(objetoActual.objetoRequest.readyState)
         {
            case 1: 
            objetoActual.Loading();
            break;
            case 2: 
            objetoActual.Loaded();
            break;
            case 3: 
            objetoActual.Interactive();
            break;
            case 4:
                  objetoActual.Finished(objetoActual.objetoRequest.status,
                  objetoActual.objetoRequest.statusText,
                  objetoActual.objetoRequest.responseText,
                  objetoActual.objetoRequest.responseXML);
                  break;
           } // switch
       } // function
  if (this.metodo == "GET") {
    this.objetoRequest.send(null); 
  }
  else if (this.metodo == "POST") {
         this.objetoRequest.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
        this.objetoRequest.send(valores);
  }
} // function
function objetoRequestCargando() {}
function objetoRequestCargado() {}
function objetoRequestInteractivo() {}
function objetoRequestCompletado(estado, estadoTexto, respuestaTexto, respuestaXML) {}
objetoAjax.prototype.Get = peticionAsincrona ;
objetoAjax.prototype.Loading = objetoRequestCargando ;
objetoAjax.prototype.Loaded = objetoRequestCargado ;
objetoAjax.prototype.Interactive = objetoRequestInteractivo ;
objetoAjax.prototype.Finished = objetoRequestCompletado ;


// Esta función simplifica todo lo anterior.
function getAjax(objeto, funcion){
	var objAjax = new objetoAjax("POST");
	objAjax.Finished = function (a,b,c,d) { 
		funcion = (typeof funcion == 'function')?funcion:null;
		if (a == 401) {
			requireLogin(objeto, funcion);
		} else {
			funcion(a,b,c,d);
		}
		return;
	}
	var cadena = '';
	if (typeof objeto == 'object') {
		for (var parametro in objeto) {
			if (typeof objeto[parametro] != 'object') {
				cadena = cadena+'&'+parametro+'='+objeto[parametro];
			} else {
				for (var subparam in objeto[parametro]) {
					cadena = cadena+'&'+subparam+'='+objeto[parametro][subparam];
				}
			}
		}
	}
	if (cadena.length > 1) {
		cadena = cadena.substring(1);
	}
	objAjax.Get("<?php echo URL_ajax; ?>", cadena);
} //function getAjax
;

function requireLogin(objeto, funcion) { console.log('Sesión de usuario vencida, '); }