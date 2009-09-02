<?php

#OOB/N1 Framework [2008 - Nutus] - PM 

// Código por JPCOSEANI
// Script que genera el FORM ACTUALIZAR TIPO DE CAMBIO

if (!seguridad :: isAllowed(seguridad_action::nameConstructor( 'update','user','seguridad')) )
{
	throw new OOB_exception("Acceso Denegado", "403", "Acceso Denegado. Consulte con su Administrador!", true);
} 

include_once 'PhpExt/Javascript.php';
PhpExt_Javascript::sendContentType();

include_once 'PhpExt/Grid/EditorGridPanel.php';
include_once 'PhpExt/Grid/ColumnConfigObject.php';
include_once 'PhpExt/Form/FormPanel.php';
include_once 'PhpExt/Layout/FormLayout.php';
include_once 'PhpExt/Data/JsonReader.php';
include_once 'PhpExt/Data/Store.php';
include_once 'PhpExt/Data/FieldConfigObject.php';
include_once 'PhpExt/Panel.php';
include_once 'PhpExt/Grid/GridPanel.php';
include_once 'PhpExt/Toolbar/PagingToolbar.php';
include_once 'PhpExt/Data/StoreLoadOptions.php';
include_once 'PhpExt/Data/JsonStore.php';
include_once 'PhpExt/Layout/AbsoluteLayout.php';
include_once 'PhpExt/Grid/CheckboxSelectionModel.php';
include_once 'PhpExt/Toolbar/Toolbar.php';
include_once 'PhpExtUx/Grid/GridFilters.php';
include_once 'PhpExtUx/Grid/FilterConfigObject.php';
include_once 'PhpExt/Data/SortInfoConfigObject.php';
include_once 'PhpExt/Data/SimpleStore.php';
include_once 'PhpExt/Form/Radio.php';
include_once 'PhpExt/Form/TextField.php';
include_once 'PhpExt/Grid/RowSelectionModel.php';
include_once 'PhpExt/Form/DateField.php';
include_once 'PhpExt/Form/NumberField.php';
include_once 'PhpExtUx/App/FitToParent.php';

global $ari;
$ari->popup = 1; // no mostrar el main_frame 
$separador_decimal = trim( $ari->locale->get('decimal', 'numbers') );

$store = new PhpExt_Data_JsonStore();
$store->setUrl("/currency/currency/get_currencys_change")	  
	  ->setRoot("topics")	  	  
      ->setTotalProperty("totalCount");
     
//AGREGO LOS CAMPOS AL STORE	
$store->addField(new PhpExt_Data_FieldConfigObject("id"));
$store->addField(new PhpExt_Data_FieldConfigObject("currency"));
$store->addField(new PhpExt_Data_FieldConfigObject("value"));
$store->addField(new PhpExt_Data_FieldConfigObject("date"));
$store->addField(new PhpExt_Data_FieldConfigObject("new"));

$txt_nuevo_valor = new PhpExt_Form_NumberField();
$txt_nuevo_valor->setDecimalSeparator( $separador_decimal )
				->setMsgTarget( PhpExt_Form_FormPanel::MSG_TARGET_SIDE );
 
//AGREGO LAS COLUMNAS A LA GRILLA 
$col_model = new PhpExt_Grid_ColumnModel();
$col_model->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("Id","id",null,35));
$col_model->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("Moneda","currency"));
$col_model->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("Ultimo Valor","value"));
$col_model->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("Fecha","date",null,110));
$col_model->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("Nuevo Valor","new")
			->setEditor($txt_nuevo_valor));					  

$paging = new PhpExt_Toolbar_PagingToolbar();
$paging->setStore($store)       
	   ->setDisplayInfo(true)	   
       ->setEmptyMessage("No tiene monedas flotantes para definir el tipo de cambio");			
			
//GRILLA			
$grid = new PhpExt_Grid_EditorGridPanel();
$grid->setStore($store)	 
	 ->setColumnModel($col_model)
	 ->setSelectionModel(new  PhpExt_Grid_RowSelectionModel())	 
	 ->setLoadMask(true)	 
	 ->setenableColLock(false);
	 
$grid->setBottomToolbar( $paging ); 	 
	 
$grid->getPlugins()->add(new PhpExtUx_App_FitToParent()); 


$grid_render = "


var save = function(){
	var fecha = grid.getTopToolbar().items.find( function(c){ return (c.xtype == 'datefield') } );	
	var store = grid.getStore();
	var store_changes = store.getModifiedRecords();
	
	var items = Array();
	
	for( var i = 0, len = store_changes.length; i < len; i++ )
	{		
		var item = { 
					 id : store_changes[i].get('id'),
				  value : store_changes[i].get('new')					
		           }
		items.push(item);
	}
	
	var json = { 
				 fecha : fecha.value,
				 items : items 
			   }
	
	store.load( { params:{ NewsValuesData : Ext.encode(json) } } );
	store.commitChanges();
	
	
}
	
var button1 = grid.getTopToolbar().items.find( function(c){ return ( c.text == 'Guardar cambios') } );
button1.on( 'click', save );

";

$grid->setEnableKeyEvents(true);
$grid->attachListener( "render", new PhpExt_Listener( PhpExt_Javascript::functionDef( null, $grid_render , array( "grid" ) )) );			   		   	 

//CONTROL PARA LA FECHA
$txt_fecha = new PhpExt_Form_DateField();
$txt_fecha->setInvalidText("Fecha Invalida(dd/mm/yyyy)")		  
		  ->setValue( date('Y-m-d') )
		  ->setFormat(str_replace("%","",$ari->get("locale")->get('shortdateformat','datetime')));

		 	  
$tb = $grid->getTopToolbar();
$tb->addTextItem( "text", "Fecha:" );
$tb->addItem( "txt_fecha", $txt_fecha );
$tb->addButton( "update", "Guardar cambios", "images/save.gif" );

$resultado = '';
$resultado.= $store->getJavascript( false, "store_user_list" );
$resultado.= $col_model->getJavascript( false, "cm" );
$resultado.= $grid->getJavascript( false,"contenido" );
$resultado.= "store_user_list.load();";

//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $resultado );
$obj_comunication->send(true);

?>