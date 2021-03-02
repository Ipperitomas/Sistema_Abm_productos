;
/*
	msgerr ver 2.0
	Author: Fivemedia.
	Created: 2020-01-22
	Desc: 
		Plugin JQuery para mostrar mensajes en los inputs y select en forma de globo encima de ellos con la intención de señalar errores en la entrada de datos.
		Necesita de una clase css 'balloon' para formatear el globo con el mensaje (msgerr.balloon.css).
		
		¡Siempre regresa false por lo que no se puede encadenar con otro plugin JQuery!.
		
		Exclamation mark provided by Font Awesome Free 5.11.2 by @fontawesome - https://fontawesome.com. License - https://fontawesome.com/license/free (Icons: CC BY 4.0, Fonts: SIL OFL 1.1, Code: MIT License)
		
	
	Opciones:
		msg: el mensaje a mostrar. Por omisión ninguno.
		cssclass: la clase css básica del globo.
		pos: la posición del pin señalador, puede ser 'bl' (abajo a la izquierda), 'bm' (abajo al centro), 'br' (abajo a la derecha)
		exclam: código HTML con el signo de exclamación.
	
	Ejemplo:
		$("input#").msgerr('Esto es el mensaje de error.');
		
	
*/
(function($) {
	$.fn.msgerr = function (options) {
		this.each(
			function () {
				var element = this;
				if (element.type == 'hidden') {
					return false;
				}

				var
				defaults = {
					cssclass: 'balloon',
					msg: '',
					pos: 'bl',
					exclam: '<i class="fas fa-exclamation-triangle exclamation"></i> '
				};
				if (typeof options == 'string') {
					options = {msg:options}
				}
				var settings = $.extend({}, defaults, options);
				
				var theSpan = document.createElement('SPAN');
				theSpan.classList.add(settings.cssclass);
				theSpan.classList.add(settings.pos);
				
				
				getOffsetRect = function(elem) {
					var box = elem.getBoundingClientRect();
					var body = document.body
					var docElem = document.documentElement
					var scrollTop = window.pageYOffset || docElem.scrollTop || body.scrollTop
					var scrollLeft = window.pageXOffset || docElem.scrollLeft || body.scrollLeft
					var clientTop = docElem.clientTop || body.clientTop || 0
					var clientLeft = docElem.clientLeft || body.clientLeft || 0
					var top = box.top;
					var left = box.left + scrollLeft - clientLeft
					return { top: Math.round(top), left: Math.round(left) }
				} // getOffsetRect
				
				Relocate = function () {
					var rect = getOffsetRect(element);
					var Left = (element.clientWidth) + rect.left;
					theSpan.style.top = (rect.top-theSpan.clientHeight) + "px";
					Left = Left - (theSpan.clientWidth);
					
					theSpan.style.left = parseInt(Left) + "px";
				}
				
				Show = function () {
					
					var rect = getOffsetRect(element);
					console.log(rect);
					var Left = (element.clientWidth) + rect.left;
					
					theSpan.innerHTML = settings.exclam+settings.msg;
					
					document.body.appendChild(theSpan);
					
					theSpan.style.top = (rect.top-theSpan.clientHeight) + "px";
					
					Left = Left - (theSpan.clientWidth);
					
					theSpan.style.left = parseInt(Left) + "px";
					
				}
				
				Hide = function () {
					$(theSpan).animate({opacity:0},200, 'linear', function () { $(theSpan).remove(); } );
					$(element).removeClass('olred')
				}
				
				$(element).focus(Hide);
				$(element).click(Hide);
				
				theSpan.addEventListener('click', Hide);
				window.addEventListener('resize', Relocate);
				
				Show();
				$(element).addClass('olred');
				
			} // function
		); // each
		return false;
	} // function
})(jQuery);
