;
/*
	modalBsLte
	Created: 2020-03-22
	Author: DriverOp
	
	Crea una ventana modal al estilo Bootstrap 4 y LTE.
	
	Requiere objEvalresult.js,fmdRulo.js
	
*/

var modalBsLTECreator = function (params) {
	this.defaultOptions = {
		archivo: '',
		content: '',
		extraparams: '',
		extraclass: '',
		windowId: 'modalBsLte',
		onShow: null,
		onClose: null
	}
	
	this.options = Object.assign({},this.defaultOptions,params);
	
	this.objAjax = new objetoAjax("POST");
	
	this.rulo = new fmdRulo({id:this.options.windowId});
	this.evalresult = new mgrEvalResult();
	
	var TheWindow = this;
	
	this.Show = function (inline_params) {
		TheWindow.rulo.Show();
		var cadena = '';
		if (TheWindow.options.modulo) {
			cadena = cadena+'&modulo='+TheWindow.options.modulo;
		};
		if (TheWindow.options.archivo) {
			cadena = cadena+'&archivo='+TheWindow.options.archivo;
		};
		if (TheWindow.options.accion) {
			cadena = cadena+'&accion='+TheWindow.options.accion;
		};
		if (TheWindow.options.content) {
			cadena = cadena+'&content='+TheWindow.options.content;
		};
		if (TheWindow.options.elid) {
			cadena = cadena+'&id='+TheWindow.options.elid;
		};
		if (inline_params) {
			if (typeof inline_params == 'string') {
				cadena += '&'+inline_params;
			} else {
				TheWindow.options.extraparams = Object.assign({},TheWindow.options.extraparams,inline_params);
			}
			
		}
		if (TheWindow.options.extraparams) {
			
			if (typeof TheWindow.options.extraparams == 'string') {
				cadena += '&'+TheWindow.options.extraparams;
			} else {
				params  = Object.entries(TheWindow.options.extraparams);
				params.forEach(function (value) { 
					cadena += '&'+value[0]+'='+value[1];
				})
			}
		};
		TheWindow.objAjax.Get('<?php echo URL_ajax; ?>', cadena);
	}
	
	this.Finished = function (a,b,c,d) {
		//console.log('a:'+a+' b:'+b+' c:'+c+' d:'+d);
		TheWindow.rulo.Hide();
		if (a == 200) {
			if (TheWindow.evalresult.Eval(c)) {
				var TheDiv = TheWindow.CreateTheWindow(c);
				document.getElementsByTagName('BODY')[0].appendChild(TheDiv);
				$(TheDiv).modal('show');
				$(TheDiv).on('hidden.bs.modal', function (e) {
					if (TheWindow.options.onClose && (typeof TheWindow.options.onClose == 'function')) {
						TheWindow.options.onClose(TheDiv);
					}
					document.getElementsByTagName('BODY')[0].removeChild(TheDiv);
				});
				if (TheWindow.options.onShow && (typeof TheWindow.options.onShow == 'function')) {
					TheWindow.options.onShow(TheDiv);
				}
				TheDiv.querySelectorAll('script').forEach((t,i)=> {
					eval(t.innerText);
				}); // foreach
			}
		}
		if (a == 401) {
			TheWindow.evalresult.ShowMessage('Sesión de usuario ha expirado.');
		}
		if (a == 404) {
			console.log('Archivo no encontrado: '+TheWindow.options.archivo);
		}
		OnKeySubmit = function(){
			try{
				var btnSave = document.getElementsByClassName("btn-save");
				if(btnSave && btnSave.length && btnSave[0]){
					btnSave[0].click();
				}
			}catch(error){
				console.log(error);
			}
		}
	}
	this.objAjax.Finished = this.Finished;
	this.CreateTheWindow = function (TheContent) {
		var TheDiv = document.createElement('DIV');
		TheDiv.classList.add('modal');
		TheDiv.classList.add('fade');
		TheDiv.setAttribute('id',TheWindow.options.windowId);
		TheDiv.setAttribute('tabindex','-1');
		TheDiv.setAttribute('role','dialog');
		TheDiv.setAttribute('aria-labelledby','Label'+TheWindow.options.windowId);
		TheDiv.setAttribute('aria-hidden','true');
		
		var TheModal = document.createElement('DIV');
		TheModal.classList.add('modal-dialog');TheModal.classList.add('modal-lg');
		
		var TheContainer = document.createElement('DIV');
		TheContainer.classList.add('modal-content');
		if (typeof TheWindow.options.extraclass == 'object') {
			for (var x in TheWindow.options.extraclass) {
				TheContainer.classList.add(TheWindow.options.extraclass[x]);
			}
		} else {
			if (TheWindow.options.extraclass != '') {
				TheContainer.classList.add(TheWindow.options.extraclass);
			}
		}
		
		TheContainer.setAttribute('id',TheWindow.options.windowId+'_content');
		TheContainer.innerHTML = TheContent.replaceAll('%winid%', TheWindow.options.windowId);
		
		TheModal.appendChild(TheContainer);
		TheDiv.appendChild(TheModal);
		
		
		return TheDiv;
	}
	this.Hide = function () {
		$('#'+TheWindow.options.windowId).modal('hide');
	}
	this.Wait = function (wait) {
		if (wait) { TheWindow.rulo.Show(); }
		else { TheWindow.rulo.Hide(); }
	}
	return this;
}
;