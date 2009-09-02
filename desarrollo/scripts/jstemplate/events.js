/*
	EVENTOS MAIN
*/

// REFERENCIA general hacia el BODY de la página
var bodyref = $('bodyref');

// Event Object del Browser
var e = e || window.event;


//
//	Función para agregar un evento a un elemento
//
function addListener(Obj, event, eventHandler) {
	// 
	if( Obj.addEventListener ) {
		Obj.addEventListener(event, eventHandler, false);
	} else if( Obj.attachEvent ) {
		Obj.attachEvent("on"+event, eventHandler);
	}
}

//
//	Función para agregar un evento al LOAD de la página
//
function DocLoad(eventHandler) {
	if (document.addEventListener) {
		addListener(document, "DOMContentLoaded", eventHandler);
	} else if (window.addEventListener) {
		addListener(window, "DOMContentLoaded", eventHandler);
	} else {
		window.onload = eventHandler;
	}


}

/**** EVENTOS ****/


// DOCUMENT LOAD
DocLoad(init);













/**** HANDLERS ****/


// Inicio general del documento
function init(e) {

	// Armamos el header 
	site_header();

	// Armamos el toolbar
	site_toolbar();
}