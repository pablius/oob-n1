/*
 * FCKeditor - The text editor for internet
 * Copyright (C) 2003-2006 Frederico Caldeira Knabben
 * 
 * Licensed under the terms of the GNU Lesser General Public License:
 * 		http://www.opensource.org/licenses/lgpl-license.php
 * 
 * For further information visit:
 * 		http://www.fckeditor.net/
 * 
 * "Support Open Source software. What about a donation today?"
 * 
 * File Name: fck_image.js
 * 	Scripts related to the Image dialog window (see fck_image.html).
 * 
 * File Authors:
 * 		Frederico Caldeira Knabben (fredck@fckeditor.net)
 */

var oEditor		= window.parent.InnerDialogLoaded() ;
var FCK			= oEditor.FCK ;
var FCKLang		= oEditor.FCKLang ;
var FCKConfig	= oEditor.FCKConfig ;
var FCKDebug	= oEditor.FCKDebug ;

var small_h = "100";
var small_w = "100";
var medium_h = "200";
var medium_w =  "200";
var large_h = "500";
var large_w =  "500";
var default_h = "500";
var default_w = "500";


var bImageButton = ( document.location.search.length > 0 && document.location.search.substr(1) == 'ImageButton' ) ;

//#### Dialog Tabs

// Set the dialog tabs.
window.parent.AddTab( 'Info', FCKLang.DlgImgInfoTab ) ;

if ( !bImageButton && !FCKConfig.ImageDlgHideLink )
	window.parent.AddTab( 'Link', FCKLang.DlgImgLinkTab ) ;

if ( FCKConfig.ImageUpload )
	window.parent.AddTab( 'Upload', FCKLang.DlgLnkUpload ) ;

if ( !FCKConfig.ImageDlgHideAdvanced )
	window.parent.AddTab( 'Advanced', FCKLang.DlgAdvancedTag ) ;

// Function called when a dialog tag is selected.
function OnDialogTabChange( tabCode )
{
	ShowE('divInfo'		, ( tabCode == 'Info' ) ) ;
	ShowE('divLink'		, ( tabCode == 'Link' ) ) ;
	ShowE('divUpload'	, ( tabCode == 'Upload' ) ) ;
	ShowE('divAdvanced'	, ( tabCode == 'Advanced' ) ) ;
}

// Get the selected image (if available).
var oImage = FCK.Selection.GetSelectedElement() ;
var sImage = FCK.Selection.GetSelectedElement() ;
//alert(sImage);

if ( oImage && oImage.tagName != 'IMG' && !( oImage.tagName == 'INPUT' && oImage.type == 'image' ) )
	oImage = null ;


// Get the active link.
var oLink = FCK.Selection.MoveToAncestorNode( 'A' ) ;

var oImageOriginal ;


function UpdateOriginal( resetSize )
{
	if ( !eImgPreview )
		return ;
	
	var oImageSelector = document.getElementById("imageSelector");
	if (oImageSelector.options[oImageSelector.selectedIndex].value == -1)
	{	
		oImageOriginal = null ;
		return ;
		//e.src = oImageSelector.options[oImageSelector.selectedIndex].getAttribute("url") ;
		//SetAttribute( e, "_fcksavedurl", oImageSelector.options[oImageSelector.selectedIndex].getAttribute("url") ) ;
	}
		
	oImageOriginal = document.createElement( 'IMG' ) ;	// new Image() ;

	if ( resetSize )
	{
		oImageOriginal.onload = function()
		{
			this.onload = null ;
			ResetSizes() ;
		}
	}

	oImageOriginal.src = eImgPreview.src ;
}


var bPreviewInitialized ;

window.onload = function()
{
	// Translate the dialog box texts.
	oEditor.FCKLanguageManager.TranslatePage(document) ;

	GetE('btnLockSizes').title = FCKLang.DlgImgLockRatio ;
	GetE('btnResetSize').title = FCKLang.DlgBtnResetSize ;

	// Load the selected element information (if any).
	LoadSelection() ;

	// Show/Hide the "Browse Server" button.
	GetE('tdBrowse').style.display				= FCKConfig.ImageBrowser	? '' : 'none' ;
	GetE('divLnkBrowseServer').style.display	= FCKConfig.LinkBrowser		? '' : 'none' ;

	UpdateOriginal() ;

	// Set the actual uploader URL.
	if ( FCKConfig.ImageUpload )
		GetE('frmUpload').action = FCKConfig.ImageUploadURL ;

	window.parent.SetAutoSize( true ) ;

	// Activate the "OK" button.
	window.parent.SetOkButton( true ) ;
	
}


function LoadSelection()
{
	//verificar si se selecciono un tag de imagen
	if (sImage == null || sImage == "") 
	{	return ;
	}
	
	//validar que se haya seleccionado todo el tag de imagen correctamente
	//@todo => ver si esta expresion regular es correcta!!
	//var imagePattern = /{imagen/i ;
	var imagePattern = /{imagen.*id=\"\d+\".*}/i ;
	//var imagePattern = /{imagen.*}/i ;
	
	var validSelection = imagePattern.test(sImage); 
	
	//validar seleccion de usuario
	if(!validSelection)
	{	alert("No ha seleccionado un tag de imagen correctamente. Vuelva a intentarlo.");
		//cerrar ventana
		window.parent.close();
		//FCK.EditingArea.Window.close();

		//@todo => en IE lanza un alert desp del aviso de este metodo de que 
		//no se pudo abrir el popup y que puede que el popup blocker esté activado, 
		//ver como hacer para que no aparezca este mensaje

		return;
	
	}
	
	//id
	var idPattern = /id=\"(\d+)\"/i ;
	var idArray = idPattern.exec(sImage);
	if(idArray)
	{	
		var oImageSelector = GetE("imageSelector");
		selectOption(oImageSelector, idArray[idArray.length - 1]);
		//var id = idArray[idArray.length - 1];	
	}
	
	//size
	var sizePattern = /size=\"(small|medium|large|original)\"/i ;
	var sizeArray = sizePattern.exec(sImage);
	if(sizeArray)
	{	
		var oSizeSelector = GetE("sizeSelector");
		selectOption(oSizeSelector, sizeArray[sizeArray.length - 1]);
		//var size = sizeArray[sizeArray.length - 1];	
	}
	
	//alt
	var altPattern = /alt=\"([A-Za-z0-9_ ]+)\"/i ;
	var altArray = altPattern.exec(sImage);
	if(altArray)
	{	
		var txtAlt = GetE("txtAlt"); 
		txtAlt.value = altArray[altArray.length - 1]; 
		//var alt = altArray[altArray.length - 1];	
	}

	//vspace
	var vspacePattern = /vspace=\"(\d+)\"/i ;
	var vspaceArray = vspacePattern.exec(sImage);
	if(vspaceArray)
	{	
		var txtVSpace = GetE('txtVSpace');
		txtVSpace.value = vspaceArray[vspaceArray.length - 1];
		//var vspace = vspaceArray[vspaceArray.length - 1];	
	}

	//hspace
	var hspacePattern = /hspace=\"(\d+)\"/i ;
	var hspaceArray = hspacePattern.exec(sImage);
	if(hspaceArray)
	{	
		var txtHSpace = GetE('txtHSpace');
		txtHSpace.value = hspaceArray[hspaceArray.length - 1];
		//var hspace = hspaceArray[hspaceArray.length - 1];	
	}

	//border
	var borderPattern = /border=\"(\d+)\"/i ;
	var borderArray = borderPattern.exec(sImage);
	if(borderArray)
	{	
		var txtBorder = GetE('txtBorder');
		txtBorder.value = borderArray[borderArray.length - 1];
		//var border = borderArray[borderArray.length - 1];	
	}
	
	//align
	var alignPattern = /align=\"(right|left|center)\"/i ;
	var alignArray = alignPattern.exec(sImage);
	if(alignArray)
	{	
		var oAlignSelector = GetE("alignSelector");
		selectOption(oAlignSelector, alignArray[alignArray.length - 1]);
		//var align = alignArray[alignArray.length - 1];	
	}
	

	//target
	var targetPattern = /target=\"(_blank|_top|_self|_parent)\"/i ;
	var targetArray = targetPattern.exec(sImage);
	if(targetArray)
	{	
		var oTargetSelector = GetE("cmbLnkTarget");
		selectOption(oTargetSelector, targetArray[targetArray.length - 1]);
	}

	//link
	//validarURL(valor)	
	//@todo => ver patron de una url
	//	/^http[s]?://\w[\.\w]+$/
	//	/link=\"(^w+([.-]?w+)*.w+([.-]?w+)*(.w{2,3})+$)\"/i
	///http(s)?:\/\/(www.)?([\w.]+)(\.\w{2,4})+$/
	
	//var linkPattern = /link=\"(\w[\.\w]+$)\"/i ;
	//var linkPattern = /link=\"(^w+([.-]?w+)*.w+([.-]?w+)*(.w{2,3})+$)\"/i ;
	//var linkPattern = /link=\"([\w.]+)(\.\w{2,4})+$\"/i ;
	//var linkPattern = /link=\"([\w.]+)(\.\w{2,4})+\"$/i ;
	//var linkPattern = /link=\"([\w.]+)(\.\w{2,4})+\"/i ;
	//url_regexp ='<(?:(?:https?)://(?:(?:(?:(?:(?:(?:[a-zA-Z0-9][-a-zA-Z0-9]*)?[a-zA-Z0-9])[.])*(?:[a-zA-Z][-a-zA-Z0-9]*[a-zA-Z0-9]|[a-zA-Z])[.]?)|(?:[0-9]+[.][0-9]+[.][0-9]+[.][0-9]+)))(?::(?:(?:[0-9]*)))?(?:/(?:(?:(?:(?:(?:(?:[a-zA-Z0-9\\-_.!~*\'():@&=+$,]+|(?:%[a-fA-F0-9][a-fA-F0-9]))*)(?:;(?:(?:[a-zA-Z0-9\\-_.!~*\'():@&=+$,]+|(?:%[a-fA-F0-9][a-fA-F0-9]))*))*)(?:/(?:(?:(?:[a-zA-Z0-9\\-_.!~*\'():@&=+$,]+|(?:%[a-fA-F0-9][a-fA-F0-9]))*)(?:;(?:(?:[a-zA-Z0-9\\-_.!~*\'():@&=+$,]+|(?:%[a-fA-F0-9][a-fA-F0-9]))*))*))*))(?:[?](?:(?:(?:[;/?:@&=+$,a-zA-Z0-9\\-_.!~*\'()]+|(?:%[a-fA-F0-9][a-fA-F0-9]))*)))?))?)>'; 
	//var linkPattern = new RegExp("link=\"((?:(?:https?)://(?:(?:(?:(?:(?:(?:[a-zA-Z0-9][-a-zA-Z0-9]*)?[a-zA-Z0-9])[.])*(?:[a-zA-Z][-a-zA-Z0-9]*[a-zA-Z0-9]|[a-zA-Z])[.]?)|(?:[0-9]+[.][0-9]+[.][0-9]+[.][0-9]+)))(?::(?:(?:[0-9]*)))?(?:/(?:(?:(?:(?:(?:(?:[a-zA-Z0-9\\-_.!~*\'():@&=+$,]+|(?:%[a-fA-F0-9][a-fA-F0-9]))*)(?:;(?:(?:[a-zA-Z0-9\\-_.!~*\'():@&=+$,]+|(?:%[a-fA-F0-9][a-fA-F0-9]))*))*)(?:/(?:(?:(?:[a-zA-Z0-9\\-_.!~*\'():@&=+$,]+|(?:%[a-fA-F0-9][a-fA-F0-9]))*)(?:;(?:(?:[a-zA-Z0-9\\-_.!~*\'():@&=+$,]+|(?:%[a-fA-F0-9][a-fA-F0-9]))*))*))*))(?:[?](?:(?:(?:[;/?:@&=+$,a-zA-Z0-9\\-_.!~*\'()]+|(?:%[a-fA-F0-9][a-fA-F0-9]))*)))?))?))\"", "i" );

	var linkPattern = /link=\"(.+)\"/i ;
	var linkArray = linkPattern.exec(sImage);
	if(linkArray)
	{	
		var txtLnkUrl = GetE("txtLnkUrl");
		txtLnkUrl.value = linkArray[linkArray.length - 1];
	}

	UpdatePreview() ;
	
}


function fillSelector()
{
	//jerarquia de ventanas:
	//winPpal: ventana principal => instancia_modificar.tpl
	//frameEditor: el frame que contiene el editor dentro de la ventana instancia_modificar.tpl
	//winPopup: es el popup para seleccionar imagenes; es abierto por frameEditor
	//framePopup: es el frame contenido en el popup para seleccionar imagenes
	
	//winPpal contiene => frameEditor (winPpal = frameEditor.parent)
	//frameEditor => abre winPopup	(frameEditor = winPopup.opener)
	//winPopup contiene => framePopup (winPopup = framePopup.parent)


	//---------------------------------------------------------------------------------------
	//lamentablemente (y como siempre), corre solo bajo Firefox => no bajo IE, mala sorchy...
	/*
	var winPopup = window.parent;
	var frameEditor = winPopup.opener; 
	var table = frameEditor.parent.document.getElementById("Tableimagenes_image");
	*/
	//var table = window.parent.opener.parent.document.getElementById("Tableimagenes_image");
	//---------------------------------------------------------------------------------------
	//end lamentablemente
	
	//entonces, probar con el objeto FCK
	var frameEditor = FCK.EditingArea.Window;
	var aux = frameEditor.parent;
	var winPpal = aux.parent;
	var table = winPpal.document.getElementById("Tableimagenes_image");
	
	//alert(table.rows.length);

	if(table.rows.length < 2)
	{	
		alert("No se han agregado imagenes");
	}
	else
	{	
		//alert(table.rows.length);
		var oImageSelector = document.getElementById("imageSelector");
		var oChildrens; 
		var oChildInputs;
		var i;
		var j;
		for (i = table.rows.length-1;i>=2;i--) 
		{
			/*
			cell[0] => innerHTML: imagenID
			cell[1] => <input name="RelationClass[]" type="text">		
			cell[2] => <input name="RelationID[]" type="text">		
			cell[3] => <img src="url" alt="Imagen" width="15px" height="15px" border="0" />		
					   <input name="RelationName[]" type="text" readonly>
					   <input name="RelationURL[]" type="hidden">
					   
			*/
			
			var oRelationID=null;
			var oRelationName=null;
			var oRelationURL=null;
			
			var ochildNodesRelationID = table.rows[i].cells[2].childNodes;
			if (ochildNodesRelationID.length>0)
			{	
				
				for (j=0; j<ochildNodesRelationID.length; j++) 
				{	
					if(ochildNodesRelationID[j].tagName == "INPUT" )
					{	oRelationID = ochildNodesRelationID[j];
						break;
					}
				}
			}

			var ochildNodesRelationName = table.rows[i].cells[3].childNodes;
			if (ochildNodesRelationName.length>0)
			{	
				for (j=0; j<ochildNodesRelationName.length; j++) 
				{	
					if (ochildNodesRelationName[j].tagName == "INPUT" &&
						ochildNodesRelationName[j].name == "RelationName[]")
					{//begin if	
						oRelationName = ochildNodesRelationName[j];
					}
					else 
					{
						if (ochildNodesRelationName[j].tagName == "INPUT" &&
							ochildNodesRelationName[j].name == "RelationURL[]")
						{//begin if	
							oRelationURL = ochildNodesRelationName[j];
						}
					}
				}
			}
			
			if (oRelationID!=null && oRelationName!=null && oRelationURL!=null)
			{
				oOption = document.createElement("OPTION");
				oOption.text = "(ID: " + oRelationID.value + ") " + oRelationName.value;
				oOption.value = oRelationID.value;	
				//@todo => ver el tema del url de la imagen
				oOption.setAttribute( "url", oRelationURL.value, 0 ) ;	// 0 : Case Insensitive
				//alert(oOption.getAttribute("url"));
				oImageSelector.options.add(oOption);
			}			

		}//end for
		
	}

	return true;
	
}



function Ok()
{
	//alert("webdir");
	var oImageSelector = GetE("imageSelector");
	var oSizeSelector = GetE("sizeSelector");
	var oAlignSelector = GetE("alignSelector");
	var txtAlt = GetE("txtAlt"); 
	var txtVSpace = GetE('txtVSpace');
	var txtHSpace = GetE('txtHSpace');
	var txtBorder = GetE('txtBorder');
	var txtLnkUrl = GetE("txtLnkUrl");
	var cmbLnkTarget = GetE("cmbLnkTarget");
	
	
	if (oImageSelector.options[oImageSelector.selectedIndex].value == -1)
	{	alert("Seleccione imagen");
		oImageSelector.focus();
		return false;
	}
	
	//id
	var imagen = "{imagen id=\"" + oImageSelector.options[oImageSelector.selectedIndex].value + "\" ";

	//size
	if (oSizeSelector.options[oSizeSelector.selectedIndex].value != -1)
	{	imagen += "size=\"" + oSizeSelector.options[oSizeSelector.selectedIndex].value + "\" ";
	}
	
	//alt
	if (txtAlt.value != "")
	{	imagen += "alt=\"" + txtAlt.value + "\" ";
	}
	
	//vspace
	if (txtVSpace.value != "")
	{	imagen += "vspace=\"" + txtVSpace.value + "\" ";
	}
	
	//hspace
	if (txtHSpace.value != "")
	{	imagen += "hspace=\"" + txtHSpace.value + "\" ";
	}

	//border
	if (txtBorder.value != "")
	{	imagen += "border=\"" + txtBorder.value + "\" ";
	}
	
	//align
	if (oAlignSelector.options[oAlignSelector.selectedIndex].value != -1)
	{	imagen += "align=\"" + oAlignSelector.options[oAlignSelector.selectedIndex].value + "\" ";
	}

	//target
	//alert(cmbLnkTarget.options[cmbLnkTarget.selectedIndex].value);
	if (cmbLnkTarget.options[cmbLnkTarget.selectedIndex].value != "")
	{	imagen += "target=\"" + cmbLnkTarget.options[cmbLnkTarget.selectedIndex].value + "\" ";
	}
	
	//link
	if (txtLnkUrl.value != "")
	{	imagen += "link=\"" + txtLnkUrl.value + "\" ";
	}
	

	/*
	// Advances Attributes

	if ( ! skipId )
		SetAttribute( e, 'id', GetE('txtAttId').value ) ;

	SetAttribute( e, 'dir'		, GetE('cmbAttLangDir').value ) ;
	SetAttribute( e, 'lang'		, GetE('txtAttLangCode').value ) ;
	SetAttribute( e, 'title'	, GetE('txtAttTitle').value ) ;
	SetAttribute( e, 'class'	, GetE('txtAttClasses').value ) ;
	SetAttribute( e, 'longDesc'	, GetE('txtLongDesc').value ) ;

	if ( oEditor.FCKBrowserInfo.IsIE )
		e.style.cssText = GetE('txtAttStyle').value ;
	else
		SetAttribute( e, 'style', GetE('txtAttStyle').value ) ;
	*/

	//alert(imagen);
	imagen += "}";

	if ( oEditor.FCKBrowserInfo.IsIE )
	{	
		//under IE only
		FCK.Focus() ;
		var oSel = FCK.EditorDocument.selection ;
		var type = oSel.type; 
		oSel.createRange().pasteHTML(imagen) ;
	}
	else
	{	
		//other browser
		FCK.PasteImageText(imagen);
	}
	
	return true;
}


 
//Setea la imagen pasada con los valores que contienen los controles 
//de la pantalla
function UpdateImage( e, skipId )
{
	var oImageSelector = document.getElementById("imageSelector");
	if (oImageSelector.options[oImageSelector.selectedIndex].value != -1)
	{	e.src = oImageSelector.options[oImageSelector.selectedIndex].getAttribute("url") ;
		SetAttribute( e, "_fcksavedurl", oImageSelector.options[oImageSelector.selectedIndex].getAttribute("url") ) ;
	}
	//e.src = GetE('txtUrl').value ;
	//SetAttribute( e, "_fcksavedurl", GetE('txtUrl').value ) ;

	//alt
	SetAttribute( e, "alt"   , GetE('txtAlt').value ) ;

	//size
	//valores originales de tamaño
	var oImageSizeOriginal = GetOriginal();
	var heightOriginal = oImageSizeOriginal.height;
	var widthOriginal = oImageSizeOriginal.width;

	//alert("width Original: " + widthOriginal  + " height Original: " + heightOriginal);
	
	var oSizeSelector = GetE("sizeSelector");
	var height;
	var width;
	if (oSizeSelector.options[oSizeSelector.selectedIndex].value != -1)
	{	
		switch(oSizeSelector.options[oSizeSelector.selectedIndex].value)
		{
			case "small":
			{
				//height = small_h;
				width = small_w;
				height = (heightOriginal * width) / widthOriginal;
				break;	
			}

			case "medium":
			{
				//height = medium_h;
				width = medium_w;
				height = (heightOriginal * width) / widthOriginal;
				break;	
			}

			case "large":
			{
				//height = large_h;
				width = large_w;
				height = (heightOriginal * width) / widthOriginal;
				break;	
			}
			
			case "original":
			{
				height = heightOriginal;
				width = widthOriginal;
				break;	
			}
			
			default:
			{
				//@todo => ver valores por defecto
				//height = default_h;
				width = default_w;
				height = (heightOriginal * width) / widthOriginal;
				
			}
			
		}//end switch

		/* @todo =>
		//verificar que no se degrade (estire) la imagen
		if(widthOriginal < width)
		{	
			width = widthOriginal;
			height = heightOriginal;
			
		}
		*/
		
		//alert("width Original: " + widthOriginal  + "height Original: " + heightOriginal + "\n width: " + width + " - height: " + height);
		
		SetAttribute( e, "width" , width ) ;
		SetAttribute( e, "height", height ) ;

	}
	
	SetAttribute( e, "vspace", GetE('txtVSpace').value ) ;
	SetAttribute( e, "hspace", GetE('txtHSpace').value ) ;
	SetAttribute( e, "border", GetE('txtBorder').value ) ;
	
	//align
	var oAlignSelector = GetE("alignSelector");
	if (oAlignSelector.options[oAlignSelector.selectedIndex].value != -1)
	{	SetAttribute( e, "align" , oAlignSelector.options[oAlignSelector.selectedIndex].value ) ;	
	}

	// Advances Attributes
	if ( ! skipId )
		SetAttribute( e, 'id', GetE('txtAttId').value ) ;

	SetAttribute( e, 'dir'		, GetE('cmbAttLangDir').value ) ;
	SetAttribute( e, 'lang'		, GetE('txtAttLangCode').value ) ;
	SetAttribute( e, 'title'	, GetE('txtAttTitle').value ) ;
	SetAttribute( e, 'class'	, GetE('txtAttClasses').value ) ;
	SetAttribute( e, 'longDesc'	, GetE('txtLongDesc').value ) ;

	if ( oEditor.FCKBrowserInfo.IsIE )
	{	e.style.cssText = GetE('txtAttStyle').value ;
	}
	else
	{	SetAttribute( e, 'style', GetE('txtAttStyle').value ) ;
	}

}

var eImgPreview ;
var eImgPreviewLink ;

function SetPreviewElements( imageElement, linkElement )
{
	eImgPreview = imageElement ;
	eImgPreviewLink = linkElement ;

	UpdatePreview() ;
	UpdateOriginal() ;
	
	bPreviewInitialized = true ;
}

//Pone la imagen del frame Image Preview visible si esta seleccionada alguna
//en el selector de imagen. ademas llama a UpdateImage
function UpdatePreview()
{
	if ( !eImgPreview || !eImgPreviewLink )
	{	return ;
	}
	
	var oImageSelector = document.getElementById("imageSelector");

	if (oImageSelector.options[oImageSelector.selectedIndex].value == -1)
	{	eImgPreviewLink.style.display = 'none' ;
	}
	else
	{
		UpdateImage( eImgPreview, true ) ;
		
		if ( GetE('txtLnkUrl').value.trim().length > 0 )
		{	eImgPreviewLink.href = 'javascript:void(null);' ;
		}
		else
		{	SetAttribute( eImgPreviewLink, 'href', '' ) ;
		}
		
		eImgPreviewLink.style.display = '' ;
		
	}
}


var bLockRatio = true ;

function SwitchLock( lockButton )
{
	bLockRatio = !bLockRatio ;
	lockButton.className = bLockRatio ? 'BtnLocked' : 'BtnUnlocked' ;
	lockButton.title = bLockRatio ? 'Lock sizes' : 'Unlock sizes' ;

	if ( bLockRatio )
	{
		if ( GetE('txtWidth').value.length > 0 )
			OnSizeChanged( 'Width', GetE('txtWidth').value ) ;
		else
			OnSizeChanged( 'Height', GetE('txtHeight').value ) ;
	}
}

// Fired when the width or height input texts change
function OnSizeChanged( dimension, value )
{
	// Verifies if the aspect ration has to be mantained
	if ( oImageOriginal && bLockRatio )
	{
		var e = dimension == 'Width' ? GetE('txtHeight') : GetE('txtWidth') ;
		
		if ( value.length == 0 || isNaN( value ) )
		{
			e.value = '' ;
			return ;
		}

		if ( dimension == 'Width' )
			value = value == 0 ? 0 : Math.round( oImageOriginal.height * ( value  / oImageOriginal.width ) ) ;
		else
			value = value == 0 ? 0 : Math.round( oImageOriginal.width  * ( value / oImageOriginal.height ) ) ;

		if ( !isNaN( value ) )
			e.value = value ;
	}

	UpdatePreview() ;
}


// Fired when the Reset Size button is clicked
function ResetSizes()
{
	if ( ! oImageOriginal ) return ;
	GetE('txtWidth').value  = oImageOriginal.width ;
	GetE('txtHeight').value = oImageOriginal.height ;

	UpdatePreview() ;
}

function BrowseServer()
{
	OpenServerBrowser(
		'Image',
		FCKConfig.ImageBrowserURL,
		FCKConfig.ImageBrowserWindowWidth,
		FCKConfig.ImageBrowserWindowHeight ) ;
}

function LnkBrowseServer()
{
	OpenServerBrowser(
		'Link',
		FCKConfig.LinkBrowserURL,
		FCKConfig.LinkBrowserWindowWidth,
		FCKConfig.LinkBrowserWindowHeight ) ;
}

function OpenServerBrowser( type, url, width, height )
{
	sActualBrowser = type ;
	OpenFileBrowser( url, width, height ) ;
}

var sActualBrowser ;

function SetUrl( url, width, height, alt )
{
	if ( sActualBrowser == 'Link' )
	{
		GetE('txtLnkUrl').value = url ;
		UpdatePreview() ;
	}
	else
	{
		GetE('txtUrl').value = url ;
		GetE('txtWidth').value = width ? width : '' ;
		GetE('txtHeight').value = height ? height : '' ;

		if ( alt )
			GetE('txtAlt').value = alt;

		UpdatePreview() ;
		UpdateOriginal( true ) ;
	}
	
	window.parent.SetSelectedTab( 'Info' ) ;
}

function OnUploadCompleted( errorNumber, fileUrl, fileName, customMsg )
{
	switch ( errorNumber )
	{
		case 0 :	// No errors
			alert( 'Your file has been successfully uploaded' ) ;
			break ;
		case 1 :	// Custom error
			alert( customMsg ) ;
			return ;
		case 101 :	// Custom warning
			alert( customMsg ) ;
			break ;
		case 201 :
			alert( 'A file with the same name is already available. The uploaded file has been renamed to "' + fileName + '"' ) ;
			break ;
		case 202 :
			alert( 'Invalid file type' ) ;
			return ;
		case 203 :
			alert( "Security error. You probably don't have enough permissions to upload. Please check your server." ) ;
			return ;
		default :
			alert( 'Error on file upload. Error number: ' + errorNumber ) ;
			return ;
	}

	sActualBrowser = ''
	SetUrl( fileUrl ) ;
	GetE('frmUpload').reset() ;
}

var oUploadAllowedExtRegex	= new RegExp( FCKConfig.ImageUploadAllowedExtensions, 'i' ) ;
var oUploadDeniedExtRegex	= new RegExp( FCKConfig.ImageUploadDeniedExtensions, 'i' ) ;

function CheckUpload()
{
	var sFile = GetE('txtUploadFile').value ;
	
	if ( sFile.length == 0 )
	{
		alert( 'Please select a file to upload' ) ;
		return false ;
	}
	
	if ( ( FCKConfig.ImageUploadAllowedExtensions.length > 0 && !oUploadAllowedExtRegex.test( sFile ) ) ||
		( FCKConfig.ImageUploadDeniedExtensions.length > 0 && oUploadDeniedExtRegex.test( sFile ) ) )
	{
		OnUploadCompleted( 202 ) ;
		return false ;
	}
	
	return true ;
}


function selectOption(selector, value)
{	
	//var selector = document.getElementById('selector');
	for (var i=0; i < selector.options.length; i++)
	{
		if(selector.options[i].value == value)
		{	selector.options[i].selected = true;
		}
	}
}

function SetOriginal()
{
	var oImageSelector = document.getElementById("imageSelector");
	if (oImageSelector.options[oImageSelector.selectedIndex].value != -1)
	{	
		eImgPreview.removeAttribute("width");
		eImgPreview.removeAttribute("height");
		eImgPreview.src = oImageSelector.options[oImageSelector.selectedIndex].getAttribute("url") ;
		SetAttribute( eImgPreview, "_fcksavedurl", oImageSelector.options[oImageSelector.selectedIndex].getAttribute("url") ) ;
		//alert("height: " + eImgPreview.height + " \n width: " + eImgPreview.width);
		
		var oSizeSelector = GetE("sizeSelector");
		selectOption(oSizeSelector, "original");
	
	}
	
}

function GetOriginal()
{
	var oImageSelector = document.getElementById("imageSelector");
	if (oImageSelector.options[oImageSelector.selectedIndex].value != -1)
	{	
		//var oTmpDiv = document.createElement( 'DIV' ) ;
		//oTmpDiv.id = "oTmpDivID";
		var oTmpOriginalImage = document.createElement( 'IMG' ) ;	// new Image() ;
		//oTmpOriginalImage.id = "oTmpOriginalImageID";
		oTmpOriginalImage.removeAttribute("width");
		oTmpOriginalImage.removeAttribute("height");
		oTmpOriginalImage.src = oImageSelector.options[oImageSelector.selectedIndex].getAttribute("url") ;
		//oTmpDiv.appendChild(oTmpOriginalImage);
		
		//var oTmpOriginalImage1 = document.getElementById( 'oTmpOriginalImageID' ) ;
		
		
		//alert("height: " + oTmpOriginalImage1.height + " \n width: " + oTmpOriginalImage1.width);
		return oTmpOriginalImage;
	}
	else
	{	return false;
	}

}//end function


function getImagePreview()
{
	if ( window.frames != null ) 
	{
		var oFrame = null;
		var i;
		for (i = 0; i< window.frames.length; i++ )
		{	if (window.frames[i].name == "frameVistaPrevia")
			{	oFrame = window.frames[i];
				break;
			}
		}
		
		if ( oFrame != null ) 
		{	var imgPreview = oFrame.document.getElementById("imgPreview");
			return imgPreview;
		}
		
	}
	
	return false;
	
}

//hay que verificar la expresion regular empleada antes de usar este metodo
function validarURL(valor)
{
	if (/^w+([.-]?w+)*.w+([.-]?w+)*(.w{2,3})+$/.test(valor))
	{	return (true)
	} 
	else 
	{	return (false);
	}
}


//___________________________________________________________________________________________
//-------------------------------------------------------------------------------------------

function UpdateOriginal_original( resetSize )
{
	if ( !eImgPreview )
		return ;
	
	if ( GetE('txtUrl').value.length == 0 )
	{
		oImageOriginal = null ;
		return ;
	}
		
	oImageOriginal = document.createElement( 'IMG' ) ;	// new Image() ;

	if ( resetSize )
	{
		oImageOriginal.onload = function()
		{
			this.onload = null ;
			ResetSizes() ;
		}
	}

	oImageOriginal.src = eImgPreview.src ;
}

function LoadSelection_original()
{
	if ( ! oImage ) return ;

	var sUrl = oImage.getAttribute( '_fcksavedurl' ) ;
	if ( sUrl == null )
		sUrl = GetAttribute( oImage, 'src', '' ) ;

	GetE('txtUrl').value    = sUrl ;
	GetE('txtAlt').value    = GetAttribute( oImage, 'alt', '' ) ;
	GetE('txtVSpace').value	= GetAttribute( oImage, 'vspace', '' ) ;
	GetE('txtHSpace').value	= GetAttribute( oImage, 'hspace', '' ) ;
	GetE('txtBorder').value	= GetAttribute( oImage, 'border', '' ) ;
	GetE('cmbAlign').value	= GetAttribute( oImage, 'align', '' ) ;

	var iWidth, iHeight ;

	var regexSize = /^\s*(\d+)px\s*$/i ;
	
	if ( oImage.style.width )
	{
		var aMatch  = oImage.style.width.match( regexSize ) ;
		if ( aMatch )
		{
			iWidth = aMatch[1] ;
			oImage.style.width = '' ;
		}
	}

	if ( oImage.style.height )
	{
		var aMatch  = oImage.style.height.match( regexSize ) ;
		if ( aMatch )
		{
			iHeight = aMatch[1] ;
			oImage.style.height = '' ;
		}
	}

	GetE('txtWidth').value	= iWidth ? iWidth : GetAttribute( oImage, "width", '' ) ;
	GetE('txtHeight').value	= iHeight ? iHeight : GetAttribute( oImage, "height", '' ) ;

	// Get Advances Attributes
	GetE('txtAttId').value			= oImage.id ;
	GetE('cmbAttLangDir').value		= oImage.dir ;
	GetE('txtAttLangCode').value	= oImage.lang ;
	GetE('txtAttTitle').value		= oImage.title ;
	GetE('txtAttClasses').value		= oImage.getAttribute('class',2) || '' ;
	GetE('txtLongDesc').value		= oImage.longDesc ;

	if ( oEditor.FCKBrowserInfo.IsIE )
		GetE('txtAttStyle').value	= oImage.style.cssText ;
	else
		GetE('txtAttStyle').value	= oImage.getAttribute('style',2) ;

	if ( oLink )
	{
		var sUrl = oLink.getAttribute( '_fcksavedurl' ) ;
		if ( sUrl == null )
			sUrl = oLink.getAttribute('href',2) ;
	
		GetE('txtLnkUrl').value		= sUrl ;
		GetE('cmbLnkTarget').value	= oLink.target ;
	}

	UpdatePreview() ;
}

//#### The OK button was hit.
function Ok_original()
{
	if ( GetE('txtUrl').value.length == 0 )
	{
		window.parent.SetSelectedTab( 'Info' ) ;
		GetE('txtUrl').focus() ;

		alert( FCKLang.DlgImgAlertUrl ) ;

		return false ;
	}

	var bHasImage = ( oImage != null ) ;

	if ( bHasImage && bImageButton && oImage.tagName == 'IMG' )
	{
		if ( confirm( 'Do you want to transform the selected image on a image button?' ) )
			oImage = null ;
	}
	else if ( bHasImage && !bImageButton && oImage.tagName == 'INPUT' )
	{
		if ( confirm( 'Do you want to transform the selected image button on a simple image?' ) )
			oImage = null ;
	}
	
	if ( !bHasImage )
	{
		if ( bImageButton )
		{
			oImage = FCK.EditorDocument.createElement( 'INPUT' ) ;
			oImage.type = 'image' ;
			oImage = FCK.InsertElementAndGetIt( oImage ) ;
		}
		else
		{	//ejecuta esta parte del codigo, hay que ver cuales son las otras opciones...
			oImage = FCK.CreateElement( 'IMG' ) ;
		}
		
	}
	else
	{	oEditor.FCKUndo.SaveUndoStep() ;
	}
	
	UpdateImage( oImage ) ;

	var sLnkUrl = GetE('txtLnkUrl').value.trim() ;

	if ( sLnkUrl.length == 0 )
	{
		if ( oLink )
			FCK.ExecuteNamedCommand( 'Unlink' ) ;
	}
	else
	{
		if ( oLink )	// Modifying an existent link.
			oLink.href = sLnkUrl ;
		else			// Creating a new link.
		{
			if ( !bHasImage )
				oEditor.FCKSelection.SelectNode( oImage ) ;

			oLink = oEditor.FCK.CreateLink( sLnkUrl ) ;

			if ( !bHasImage )
			{
				oEditor.FCKSelection.SelectNode( oLink ) ;
				oEditor.FCKSelection.Collapse( false ) ;
			}
		}

		SetAttribute( oLink, '_fcksavedurl', sLnkUrl ) ;
		SetAttribute( oLink, 'target', GetE('cmbLnkTarget').value ) ;
	}

	return true ;
}

function UpdatePreview_original()
{
	
	if ( !eImgPreview || !eImgPreviewLink )
		return ;

	if ( GetE('txtUrl').value.length == 0 )
		eImgPreviewLink.style.display = 'none' ;
	else
	{
		UpdateImage( eImgPreview, true ) ;
		
		if ( GetE('txtLnkUrl').value.trim().length > 0 )
		{	eImgPreviewLink.href = 'javascript:void(null);' ;
		}
		else
		{	SetAttribute( eImgPreviewLink, 'href', '' ) ;
		}
		
		eImgPreviewLink.style.display = '' ;
		//alert("pasa6");
	}
}


//___________________________________________________________________________________________
//-------------------------------------------------------------------------------------------

function varios()
{
	//document.getElementById('txtHtml').value = oEditor.FCK.EditorDocument.body.innerHTML ;
	//FCK.EditorDocument.body.innerHTML = "lala";
	//return true;
	//alert("pasa");
	
	//[ oElement = ] window.parent
	
	
	//alert("pasa");
	//alert(FCK.EditingArea.Window);
	//alert(FCK.EditingArea.Document);
	
	//FCK.EditorWindow	= FCK.EditingArea.Window ;
	//FCK.EditorDocument	= FCK.EditingArea.Document ;

	//var parent = FCK.EditorDocument.parent;
	//alert(parent);
	
	
	//FCK.EditorDocument	= FCK.EditingArea.Document ;

	//var parent = FCK.EditorDocument.parent;
	//alert(parent);
	
	
	//FCK.EditorDocument => editor
	//pantalla actual
	
	/*
	var parent = FCK.EditingArea.Window.parent;
	var webdir = parent.document.getElementById("webdir");
	alert(webdir.id);
	
	
	//var parent = FCK.EditingArea.Document.parent;
	var parent = FCK.EditorDocument.parent;
	var webdir = parent.document.getElementById("webdir");
	alert(webdir.id);
	*/
	
	/*
	window.opener.location.reload();
	opener.document.forms['id_form_opener'].submit();
	window.close(); 
	*/	

	/*
	var oDocument = window.parent.document ;
	var eLinkedField		= oDocument.getElementById( FCK.Name ) ;
	var colElementsByName	= oDocument.getElementsByName( FCK.Name ) ;
	*/

	
	/*
	var tpl_frameEditor = window.opener; //es la ventana del popup
	alert(tpl_frameEditor);
	var webdir = tpl_frameEditor.parent.document.getElementById("webdir");
	alert(webdir.id);
	*/
	
	//[ oParent = ] document.parentWindow
	//FCK.EditorDocument.parentWindow
	
	//alert("");
	//var tpl;
	
	//var popup;
	//var popup_frameImage = window.parent;
	
	//var popup = window.contentWindow; 

}


function hasImage_()
{

	//alert(FCK.EditingArea.Window);
	//return;

	//alert(frameEditor);
	//var winPpal = frameEditor.parent;
	//alert(winPpal);
	//var table = winPpal.document.getElementById("Tableimagenes_image");
	//alert(table.rows.length);
	
	//var winPopup = window.parent;
	/*
	var winPopup = window.parent;
	alert(winPopup);
	var frameEditor = winPopup.opener; 
	var table = frameEditor.parent.document.getElementById("Tableimagenes_image");

	//var winPopup = window.parent;
	alert(table.rows.length);
	*/
	
	//var frameEditor = winPopup.opener.frames[0]; 
	//var frameEditor = winPopup.opener; 
	
	//var frameEditor = FCK.EditingArea.Window;
	//var winPpal = frameEditor.parent;
	
	//alert(FCK.EditingArea.Window);
	//return;

	//alert(frameEditor);
	//var winPpal = frameEditor.parent;
	//alert(winPpal);
	//var table = winPpal.document.getElementById("Tableimagenes_image");
	//alert(table.rows.length);
	
	/*
	var table = window.parent.opener.parent.document.getElementById("Tableimagenes_image");
	if(table.rows.length < 2)
	{	window.parent.close();
		alert("No se han agregado imágenes para relacionar con la instancia. Agregue al menos una imagen en la sección \"Adjuntar Imagen\" y vuelva a intentarlo.");
	}
	*/


	//frameEditor.open('about:blank')
	//frameEditor.document.write("<strong>HOLAAAAAAAAAAAAA</strong>");
	//alert(frameEditor.parentWindow);
	//alert(frameEditor.parent);

	//alert(winPpal);
	//winPpal.document.write("<strong>HOLAAAAAAAAAAAAA</strong>");

	//var winPpal = frameEditor.parent;

	//var winPopup = window.parent;
	
	var winPopup = window.parent;
	alert(winPopup);
	var frameEditor = winPopup.opener; 
	var table = frameEditor.parent.document.getElementById("Tableimagenes_image");

	//var winPopup = window.parent;
	alert(table.rows.length);
	
	
	//var frameEditor = winPopup.opener.frames[0]; 
	//var frameEditor = winPopup.opener; 
	
	//var frameEditor = FCK.EditingArea.Window;
	//var winPpal = frameEditor.parent;
	
	//alert(FCK.EditingArea.Window);
	//return;

	//alert(frameEditor);
	//var winPpal = frameEditor.parent;
	//alert(winPpal);
	//var table = winPpal.document.getElementById("Tableimagenes_image");
	//alert(table.rows.length);
	
	/*
	var table = window.parent.opener.parent.document.getElementById("Tableimagenes_image");
	if(table.rows.length < 2)
	{	window.parent.close();
		alert("No se han agregado imágenes para relacionar con la instancia. Agregue al menos una imagen en la sección \"Adjuntar Imagen\" y vuelva a intentarlo.");
	}
	*/
}
