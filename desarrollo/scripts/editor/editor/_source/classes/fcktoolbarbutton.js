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
 * File Name: fcktoolbarbutton.js
 * 	FCKToolbarButton Class: represents a button in the toolbar.
 * 
 * File Authors:
 * 		Frederico Caldeira Knabben (fredck@fckeditor.net)
 */

var FCKToolbarButton = function( commandName, label, tooltip, style, sourceView, contextSensitive, icon )
{
	this.CommandName		= commandName ;
	this.Label				= label ;
	this.Tooltip			= tooltip ;
	this.Style				= style ;
	this.SourceView			= sourceView ? true : false ;
	this.ContextSensitive	= contextSensitive ? true : false ;	

	if ( icon == null )
		this.IconPath = FCKConfig.SkinPath + 'toolbar/' + commandName.toLowerCase() + '.gif' ;
	else if ( typeof( icon ) == 'number' )
		this.IconPath = [ FCKConfig.SkinPath + 'fck_strip.gif', 16, icon ] ;
}

FCKToolbarButton.prototype.Create = function( targetElement )
{
	this._UIButton = new FCKToolbarButtonUI( this.CommandName, this.Label, this.Tooltip, this.IconPath, this.Style ) ;
	this._UIButton.OnClick = this.Click ;
	this._UIButton._ToolbarButton = this ;	
	this._UIButton.Create( targetElement ) ;
}

FCKToolbarButton.prototype.RefreshState = function()
{
	// Gets the actual state.
	var eState = FCK.ToolbarSet.CurrentInstance.Commands.GetCommand( this.CommandName ).GetState() ;
	
	// If there are no state changes than do nothing and return.
	if ( eState == this._UIButton.State ) return ;
	
	// Sets the actual state.
	this._UIButton.ChangeState( eState ) ;
}

FCKToolbarButton.prototype.Click = function()
{
	var oToolbarButton = this._ToolbarButton || this ;
	if(oToolbarButton.CommandName == "Image")
	{	
		var retorno = FCK.HasImage();
		var msj = "";
		switch(retorno)
		{	
			case 0:
			{	msj = "No se han agregado imágenes para relacionar con la instancia. Agregue al menos una imagen en la sección \"Adjuntar Imagen\" y vuelva a intentarlo.";
				break;
			}
			case -1:
			{	msj = "No está permitido agregar imágenes a la instancia. Se ha denegado el acceso.";
				break;
			}
			
			default:
			{}
		
		}//end switch
		
		if(msj != "")
		{	alert(msj);
			return;
		}
		
	}//end if dialogo Image
	
	FCK.ToolbarSet.CurrentInstance.Commands.GetCommand( oToolbarButton.CommandName ).Execute() ;
	
}

FCKToolbarButton.prototype.Enable = function()
{
	this.RefreshState() ;
}

FCKToolbarButton.prototype.Disable = function()
{
	// Sets the actual state.
	this._UIButton.ChangeState( FCK_TRISTATE_DISABLED ) ;
}