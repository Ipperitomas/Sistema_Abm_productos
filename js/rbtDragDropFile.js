;
/*
	rbtDragDropFile: 1.0
	Created: 2020-07-30
	Author: Rebrit SRL.

	Establecer una zona y un input:file como zona para arrastrar y soltar un achivo desde el OS para (eventualmente) ser enviado al servidor.
	NO hace el envío.

*/

var rbtDragDropFile = function (param) {
	this.defaultOptions = {
		elementId: 'dragdrop',
		inputfileId: 'droppedFile',
		multiple: false,
		onchange : false,
		onDragenter: function (elem) {},
		onDrop: function (theInput) {},
		onDragleave: function (elem) {},
		onDragend: function (elem) {}
	}
	this.success = false;
	
	this.options = Object.assign({},this.defaultOptions,param);
	
	this.div = document.createElement('div');
	if ((('draggable' in this.div || ('ondragstart' in this.div && 'ondrop' in this.div)) && 'FileReader' in window) == false) {
		console.log("El navegador no soporta la API de Drag and Drop!");
		return false;
	}
	
	this.theZone = document.getElementById(this.options.elementId);
	if (this.theZone == null || this.theZone == 'undefined') {
		console.log("Lo siento pero no encontré un elemento con id '#"+this.options.elementId+"'");
		return false;
	}
	this.theZone.style.cssText = 'position:relative;';
	this.theInput = document.getElementById(this.options.inputfileId);
	if (this.theInput == null || this.theInput == 'undefined') {
		this.theInput = document.createElement('INPUT');
		this.theInput.setAttribute('type','file');
		this.theInput.setAttribute('id',this.options.inputfileId);
		this.theInput.setAttribute('name',this.options.inputfileId);
		if(this.options.onchange){
			this.theInput.setAttribute('onchange',this.options.onchange);
		}
		if (this.options.multiple) {
			this.theInput.setAttribute('multiple','multiple');
			this.theInput.setAttribute('name',this.options.inputfileId+'[]');
		}
		this.theZone.appendChild(this.theInput);
	}
	if (((this.theInput.tagName != 'INPUT') || (this.theInput.type && (this.theInput.type != 'file')))) {
		console.log("El elemento con id '#"+this.options.inputfileId+"' no es un input de tipo file.");
		return false;
	}
	this.theInput.setAttribute('style','width:0.1px;height:0.1px;opacity:0;overflow:hidden;position:absolute;z-index:-1;');
	this.theLabel = document.createElement('LABEL');
	this.theLabel.setAttribute('style','cursor:pointer;display:block;position:absolute;top:0;left:0;width:100%;height:100%;');
	this.theLabel.setAttribute('for',this.theInput.id);
	
	var zoneStyles = window.getComputedStyle(this.theZone);
	this.theLabel.style.borderRadius = zoneStyles.getPropertyValue("border-radius");
	
	
	this.theZone.appendChild(this.theLabel);
	
	
	var theDropZone = this;
	
	this.theZone.addEventListener('drop', function (event) {
		event.preventDefault();
		event.stopPropagation();
		theDropZone.theInput.files = event.dataTransfer.files;
		if (theDropZone.options.onDrop && (typeof theDropZone.options.onDrop == 'function')) {
			theDropZone.options.onDrop(theDropZone.theInput);
		}
		theDropZone.leaveTheZone(event);
	});

	this.theZone.addEventListener('dragover', function (event) {
		theDropZone.enterTheZone();
		event.preventDefault();
	});
	
	this.enterTheZone = function (event) {
		theDropZone.theLabel.classList.remove('rbtDragend');
		theDropZone.theLabel.classList.add('rbtDragover');
	}
	this.leaveTheZone = function (event) {
		theDropZone.theLabel.classList.remove('rbtDragover');
		theDropZone.theLabel.classList.add('rbtDragend');
	}
	
	this.theZone.addEventListener('dragover', this.enterTheZone);
	this.theZone.addEventListener('dragenter', function(event) {
		theDropZone.enterTheZone(event);
		if (theDropZone.options.onDragenter && (typeof theDropZone.options.onDragenter == 'function')) {
			theDropZone.options.onDragenter(theDropZone.theLabel);
		}
	});
	this.theZone.addEventListener('dragleave', function(event) {
		theDropZone.leaveTheZone(event);
		if (theDropZone.options.onDragleave && (typeof theDropZone.options.onDragleave == 'function')) {
			theDropZone.options.onDragleave(theDropZone.theLabel);
		}
	});
	this.theZone.addEventListener('dragend', function(event) {
		theDropZone.leaveTheZone(event);
		if (theDropZone.options.onDragend && (typeof theDropZone.options.onDragend == 'function')) {
			theDropZone.options.onDragend(theDropZone.theLabel);
		}
	});
}

;