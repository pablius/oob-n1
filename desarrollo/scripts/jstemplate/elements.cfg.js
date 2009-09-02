/*
 *	ARCHIVO DE CONFIGURACIÓN DINÁMICA DE ELEMENTOS
 *	28/06/2007 - Eduardo Rajo (h)
 *
 *	Este archivo contiene configuración y funciones para generar los elementos BASE del sitio.
 *		- ENCABEZADO
 *			+ FLASH
 *			+ IMAGEN ESTATICA
 *
 *		- TOOLBAR
 *			+ IMAGEN con IMAGE MAP
 *			+ RAW HTML
 */




//////////////////////////////////////////////////////////////////////////////////
//																				//
//		Configuración NIVEL 1: Define tipos de elementos a utilizar				//
//																				//
//////////////////////////////////////////////////////////////////////////////////



// ¿Queremos un encabezado con FLASH?
var use_flash_header = true;

// ¿Queremos un toolbar con imágen mapeada?
var use_mapped_toolbar = true;













//////////////////////////////////////////////////////////////////////////////////
//																				//
//	Configuración NIVEL 2: Define propiedades para los elementos a utilizar		//
//																				//
//////////////////////////////////////////////////////////////////////////////////



//
//	FUNCION para establecer el encabezado de página. El encabezado puede ser una imagen definida ó un archivo flash.
//
function site_header() {

	/* CONFIGURACION */
	var width = 900;												// ALTO 
	var height = 160;												// ANCHO

	/* IMAGEN ESTATICA */
	var head_image = webdir + "/images/header_image.jpg";

	/* FLASH SWF */
	var flash_url = webdir + "/images/encabezado.swf";					// URL hacia el archivo SWF
	var flash_quality = "high";										// Calidad gráfica del SWF
	var flash_name = "NUTUS";										// Nombre del embed (no usar espacios)
	
	/* Bufer */
	var buff = "";
	

	if (use_flash_header) {

		/* EMBED para insertar en el encabezado de la web */
		buff += "<object classid='clsid:d27cdb6e-ae6d-11cf-96b8-444553540000' codebase='http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab' align='middle' width='"+ width +"' height='"+ height +"'>";
		buff +=	"<param name='allowScriptAccess' value='sameDomain'>";
		buff +=	"<param name='movie' value='"+ flash_url +"' />";
		buff +=	"<param name='quality' value='"+ flash_quality +"' />";
		buff +=	"<embed src='"+ flash_url +"' quality='"+ flash_quality +"' name='"+ flash_name +"' allowscriptaccess='sameDomain' type='application/x-shockwave-flash' pluginspage='http://www.macromedia.com/go/getflashplayer' align='center' width='"+ width +"' height='"+ height +"'>";
		buff += "</object>";


	} else {

		buff += "<div style='width: "+ width +"px; height: "+ height +"px; background-image: url("+ head_image +"); background-repeat: none;'>";
		buff += "</div>";
	}

	// Cargamos el buffer dentro del elemento que contiene el header
	$('header').innerHTML = buff;
}





//
//	FUNCION para establecer el TOOLBAR de la página. Puede ser una imagen mapeada ó bien un HTML definido.
//
function site_toolbar() {

	// Dimensiones
	var width = 900;
	var height = 66;

	// Imagen
	var image = webdir + "/images/arriba.jpg";

	// ALT
	var alt = "";


	// Punteros a elementos
	var toolbar = $('toolbar');
	var tb_image = $('toolbar_image');


	if (use_mapped_toolbar) {

		// Configuramos la imágen
		tb_image.setAttribute("width", width);
		tb_image.setAttribute("height", height);
		tb_image.setAttribute("alt", alt);

		// Ajustamos la DIV contenedora
		toolbar.style.height = height+"px";


		// Creamos un elemento tipo MAP y lo configuramos
		var tb_map = document.createElement("map");
		tb_map.setAttribute("id", "tb_map");
		tb_map.setAttribute("name", "tb_map");

		/*	
		 *	Agregamos AREAS para el mapa y configuramos sus atributos
		 *	Parametros: "alt", "titulo", "shape", "coords", "href"
		 *
		 */

		// Cantidad de AREAS para la imágen
		var mapAreas = new Array(6);

		// Definimos los AREAS dentro de un array:
		mapAreas[0] = newArea("Currency", "Currency", "circle", "884,33,11", "/currency/currency/view");
		mapAreas[1] = newArea("Listar", "Listar", "circle", "849,33,11", "/contenido/instancia/listar");
		mapAreas[2] = newArea("Simple", "Simple", "circle", "811,34,11", "/contenido/busqueda/simple");
		mapAreas[3] = newArea("Home", "Home", "circle", "777,34,11", "/");
		mapAreas[4] = newArea("English", "English", "circle", "718,32,11", "/?idioma=en-us");
		mapAreas[5] = newArea("Portugues", "Portugues", "circle", "690,31,11", "/?idioma=pt-br");
		mapAreas[6] = newArea("Español", "Español", "circle", "665,31,11", "/?idioma=es-ar");
	
		// Cargamos las AREAS definidas dentro del MAPA
		for (var i = 0; i < mapAreas.length; i++) {
			tb_map.appendChild(mapAreas[i]);
		}

		// Insertamos el MAPA con sus áreas dentro del documento
		toolbar.appendChild(tb_map);

		// Asignamos a la imagen a mapear, el mapa previamente creado
		tb_image.setAttribute("useMap", "#tb_map");


	} else {

		toolbar.innerHTML =			"<div class=''>";
		toolbar.innerHTML +=			"<a href='#'>Menu 1</a> ";
		toolbar.innerHTML +=			"<a href='#'>Menu 2</a> ";
		toolbar.innerHTML +=			"<a href='#'>Menu 3</a> ";
		toolbar.innerHTML +=			"<a href='#'>Menu 4</a> ";
		toolbar.innerHTML +=		"</div>";
	}
}



//
//	Agrega un nuevo area a en el documento
//
function newArea(alt, title, shape, coords, href) {

	var zone = document.createElement("area");

	zone.setAttribute("alt", alt);
	zone.setAttribute("title", title);
	zone.setAttribute("shape", shape);
	zone.setAttribute("coords", coords);
	zone.setAttribute("href", href);

	return zone;
}