<?php

#OOB/N1 Framework [2008 - Nutus] - PM 

// Código por JPCOSEANI
// SCRIPT QUE GENERA EL FORM LISTADO DE MODULOS

if (!seguridad :: isAllowed(seguridad_action :: nameConstructor('update','user','seguridad')) )
{
	throw new OOB_exception("Acceso Denegado", "403", "Acceso Denegado. Consulte con su Administrador!", true);
} 

PhpExt_Javascript::sendContentType();

global $ari;
$ari->popup = 1; // no mostrar el main_frame 

//STORE PARA TRAER EL LISTADO DE MODULOS
$store = new PhpExt_Data_JsonStore();
$store->setUrl("/admin/module/get_modules")	  
	  ->setRoot("topics")
	  ->setAutoLoad(true)
      ->setTotalProperty("totalCount");
     

//AGREGO LOS CAMPOS AL STORE	 
$store->addField(new PhpExt_Data_FieldConfigObject("nicename"));
$store->addField(new PhpExt_Data_FieldConfigObject("modulename"));
$store->addField(new PhpExt_Data_FieldConfigObject("description"));
$store->addField(new PhpExt_Data_FieldConfigObject("checked"));
$store->addField(new PhpExt_Data_FieldConfigObject("optional"));

//CHECKBOX PARA SELECCIONAR LOS MODULOS
$checkColumn = new PhpExtUx_Grid_CheckColumn("Habilitado");
$checkColumn->setDataIndex("checked")
			->setId("check")
			->setWidth(55);

//AGREGO LAS COLUMNAS A LA GRILLA			
$col_model = new PhpExt_Grid_ColumnModel();
$col_model->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("Nombre del M&oacute;dulo","nicename",null,150));
$col_model->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("Descripci&oacute;n","description",null,340));
$col_model->addColumn($checkColumn);


//GRILLA
$grid = new PhpExt_Grid_GridPanel();
$grid->setStore($store)	 
	 ->setColumnModel($col_model)	 	 
	 ->setLoadMask(true)	 
	 ->setenableColLock(false);
	 
$grid->getPlugins()->add( $checkColumn );	 
$grid->getPlugins()->add( new PhpExtUx_App_FitToParent() );

$grid_render = "

var store = grid.getStore();

grid.on( 'afteredit', function(e){
		var datos = e.record.data;
		if(!datos.optional){
			e.record.set( 'checked', true );	
			e.record.commit();
		}
});

var save = function(){
	
	var store_changes = store.getModifiedRecords();
	var items = Array();	
	for(var i = 0, len = store_changes.length; i < len; i++){
		var item = { 
				  modulename : store_changes[i].get('modulename'),
					 checked : store_changes[i].get('checked')
					}
		items.push(item);
	}	
	store.load( {params : { UpdateEnabledData : Ext.encode(items) } } );
	store.commitChanges();

}

var refresh = function(){
	store.load( {params : {UpdateList:true} } );
}

var button1 = grid.getTopToolbar().items.find( function(c){ return ( c.text == 'Guardar Cambios') } );
button1.on( 'click', save );

var button2 = grid.getTopToolbar().items.find( function(c){ return ( c.text == 'Actualizar Lista') } );
button2.on( 'click', refresh );


";

$grid->setEnableKeyEvents(true);
$grid->attachListener( "render", new PhpExt_Listener( PhpExt_Javascript::functionDef( null, $grid_render , array( "grid" ) )) );			   		   	 


//BOTON PARA GUARDAR LOS CAMBIOS HECHOS EN LA GRILLA
$button_toolbar = $grid->getTopToolbar();
$button_toolbar->addButton( "save_changes", "Guardar Cambios", "images/save.gif" );
$button_toolbar->addButton( "refresh", "Actualizar Lista", "images/Refresh.gif" );

//SE DEVUELVEN LOS RESULTADOS
$resultado = '';
$resultado.=  $checkColumn->getJavascript(false, "checkColumn");
$resultado.= $store->getJavascript(false, "store_user_list");
$resultado.= $col_model->getJavascript(false, "cm");
$resultado.= $grid->getJavascript(false,"contenido");

//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $resultado );
$obj_comunication->send(true);

?>