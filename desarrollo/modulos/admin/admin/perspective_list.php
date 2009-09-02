<?php

#OOB/N1 Framework [2008 - Nutus] - PM 

// Código por JPCOSEANI
// SCRIPT QUE GENERA EL FORM LISTADO DE PERSPECTIVAS

if (!seguridad :: isAllowed(seguridad_action :: nameConstructor('update','user','seguridad')) )
{
	throw new OOB_exception("Acceso Denegado", "403", "Acceso Denegado. Consulte con su Administrador!", true);
} 

// LIBRERIAS

include_once 'PhpExt/Javascript.php';
PhpExt_Javascript::sendContentType();


include_once 'PhpExt/Grid/ColumnConfigObject.php';
include_once 'PhpExt/Grid/GridPanel.php';
include_once 'PhpExt/Data/JsonStore.php';
include_once 'PhpExt/Data/FieldConfigObject.php';
include_once 'PhpExtUx/App/FitToParent.php';


global $ari;
$ari->popup = 1; // no mostrar el main_frame 

//STORE PARA TRAER LA LISTA DE PERSPECTIVAS
$store = new PhpExt_Data_JsonStore();
$store->setUrl("/admin/perspective/get_prespectives")	  
	  ->setRoot("topics")
      ->setTotalProperty("totalCount")
      ->setId("id")
	  ->setAutoLoad(true);
	  
//CAMPOS DEL STORE	  
$store->addField(new PhpExt_Data_FieldConfigObject("name"));
$store->addField(new PhpExt_Data_FieldConfigObject("path"));


//SE AGREGAN LAS COLUMNAS A LA GRILLA   
$col_model = new PhpExt_Grid_ColumnModel();
$col_model->addColumn( PhpExt_Grid_ColumnConfigObject::createColumn( "Nombre", "name", null, 80 ) )
		  ->addColumn( PhpExt_Grid_ColumnConfigObject::createColumn( "Ruta", "path", null, 250 ) );

//GRILLA		  
$grid = new PhpExt_Grid_GridPanel();
$grid->setColumnModel($col_model)
	 ->setStore($store)
	 ->setStripeRows(true)	  
	 ->setLoadMask(true); 
	 
$grid->getPlugins()->add(new PhpExtUx_App_FitToParent());	 


$grid_render = "

var edit = function(){

var m = grid.getSelections();

if(m.length ==1 ){
	var id = 'id=' + m[0].get('name');
	addTab( 'Modificar Perpectiva', '/admin/perspective/update', true, id );
}else{
	Ext.MessageBox.alert('Emporika', 'Por favor seleccione \"un\" item');
}

}

var button1 = grid.getTopToolbar().items.find( function(c){ return ( c.text == 'Modificar') } );
button1.on( 'click', edit );


grid.on( 'celldblclick', edit );

";

$grid->setEnableKeyEvents(true);
$grid->attachListener( "render", new PhpExt_Listener( PhpExt_Javascript::functionDef( null, $grid_render , array( "grid" ) )) );			   		   	 

//AGREGO EL BOTON MODIFICAR EN LA GRILLA
$tb = $grid->getTopToolbar();
$tb->addButton( "modificar", "Modificar", "images/edit.gif" );

//RESULTADO
$resultado = '';
$resultado.= $store->getJavascript(false, "store");
$resultado.= $grid->getJavascript(false,"contenido");

//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $resultado );
$obj_comunication->send(true);

?>