//
//	THE FAMOUS $DOLLAR FUNCTION
//
function $() {
	var elements = new Array();

	for (var i = 0; i < arguments.length; i++) {
		var element = arguments[i];

		if (typeof element == 'string') {
			element = document.getElementById(element);
		}

		if (arguments.length == 1) {
			return element;
		}

		elements.push(element);
	}

	return elements;
}


//
//	OCULTAR / MOSTRAR UN ELEMENTO
//
function ElmDisplay(elm) {

	if (!$(elm)){
		alert("ElmDisplay(): Se requiere un elemento existente en el documento.");
		return false;
	}


	if ($(elm).style.display == 'none'){
		$(elm).style.display = '';
	} else {
		$(elm).style.display = 'none';
	}
}


//
//	Previsualizar una imágen en vista flotante
//
function ImgQuickView(ruta) {

	if (!$('ImgQuickView')){
		alert('Se debe definir un elemento contenedor para ImgQuickView');
		return false;
	}

	if (!$('ImgView')){
		alert('Se debe definir un elemento contenedor para ImgView');
		return false;
	}

	var  div = $('ImgQuickView');
	var  img = $('ImgView');

	var fnc = function x() {
		
	};

	img.src = ruta;

	setTimeout("ElmDisplay('ImgQuickView')", 100);

	

	
}


//
//	Consulta si desea ejecutar una acción y la ejecuta ó no
//
function questUrl(vUrl, quest) {

	// Si no me dan una URL doy error
	if (!vUrl){
		alert("questUrl: Se requiere de una dirección válida");
		return false;
	}

	// Si no hay una pregunta, usamos una por defecto
	if (quest == "") {
		quest = "¿Está usted seguro de realizar esta operación?";
	}

	var response = confirm(quest);

	if (response == true) {
		window.location.href = vUrl;

	} else {

		return false;
	}

}