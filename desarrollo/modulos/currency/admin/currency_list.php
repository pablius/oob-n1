<?php

#OOB/N1 Framework [2008 - Nutus] - PM 

// Código por JPCOSEANI
// SCRIPT QUE GENERA EL FORM LISTADO DE MONEDAS

if (!seguridad :: isAllowed(seguridad_action :: nameConstructor('update','user','seguridad')) )
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
include_once 'PhpExt/Form/ComboBox.php';
include_once 'PhpExt/Button.php';
include_once 'PhpExt/Data/SimpleStore.php';
include_once 'PhpExt/Form/Radio.php';
include_once 'PhpExt/Grid/RowSelectionModel.php';
include_once 'PhpExtUx/App/FitToParent.php';

global $ari;
$ari->popup = 1; // no mostrar el main_frame 
$page_size = PAGE_SIZE;

//STORE PARA TRAER LOS DATOS DEL LISTADO
$store = new PhpExt_Data_JsonStore();
$store->setUrl("/currency/currency/get_currencys")	  
	  ->setRoot("topics")	  	  
      ->setTotalProperty("totalCount");
     
//AGREGO LOS CAMPOAS AL LISTADO	
$store->addField(new PhpExt_Data_FieldConfigObject("id"));
$store->addField(new PhpExt_Data_FieldConfigObject("name"));
$store->addField(new PhpExt_Data_FieldConfigObject("default"));
$store->addField(new PhpExt_Data_FieldConfigObject("type"));
$store->addField(new PhpExt_Data_FieldConfigObject("cotizacion"));

$types = array();
$types[] = array( 1, "Fijo" );
$types[] = array( 2, "Flotante" );


$filter_plugin = new PhpExtUx_Grid_GridFilters();
$filter_plugin->addFilter(PhpExt_Grid_FilterConfigObject::createFilter("numeric","id"));
$filter_plugin->addFilter(PhpExt_Grid_FilterConfigObject::createFilter("string","name"));	 
$filter_plugin->addFilter(PhpExt_Grid_FilterConfigObject::createFilter("boolean","default"));	 
$filter_plugin->addFilter(PhpExt_Grid_FilterConfigObject::createFilter( "list", "type", PhpExt_Javascript::variable( json_encode($types) ) ) );   
$filter_plugin->addFilter(PhpExt_Grid_FilterConfigObject::createFilter("numeric","cotizacion"));

//SE CREA EL PAGINADOR
$paging = new PhpExt_Toolbar_PagingToolbar();
$paging->setStore($store)
       ->setPageSize($page_size)
	   ->setDisplayInfo(true)	   
       ->setEmptyMessage("No se encontraron Monedas");
$paging->getPlugins()->add($filter_plugin);		   

$check_select = new PhpExt_Grid_CheckboxSelectionModel();




	   
//AGREGO LAS COLUMNAS QUE VA USAR EL GRID  
$col_model = new PhpExt_Grid_ColumnModel();
$col_model->addColumn( $check_select )
		  ->addColumn( PhpExt_Grid_ColumnConfigObject::createColumn( "Id", "id" ) )
          ->addColumn( PhpExt_Grid_ColumnConfigObject::createColumn( "Moneda", "name" ) )
		  ->addColumn( PhpExt_Grid_ColumnConfigObject::createColumn( "Predeterminada", "default" ) )
		  ->addColumn( PhpExt_Grid_ColumnConfigObject::createColumn( "Tipo", "type" ) )
		  ->addColumn( PhpExt_Grid_ColumnConfigObject::createColumn( "Ultima cotizaci&oacute;n", "cotizacion" ) );
		  
		  
		 
//CREACION DEL GRID
$grid = new PhpExt_Grid_EditorGridPanel();
$grid->setStore( $store )	 
	 ->setSelectionModel( $check_select )	 
	 ->setColumnModel( $col_model )	 
	 ->setLoadMask( true )	 
	 ->setenableColLock( false );
	 
$grid->getPlugins()->add( $filter_plugin );  
$grid->getPlugins()->add( new PhpExtUx_App_FitToParent() );   
$grid->setBottomToolbar( $paging ); 	 



$grid_render = "

var nueva = function(){
	var id = 'gid=' + grid.id ;
	addTab( 'Nueva Moneda', '/currency/currency/new', true, id );
}

var edit = function(){

	var id;
	var m = grid.getSelections();

		if( m.length >= 1 )
		{	
	  	    for( var i = 0, len = m.length; i < len; i++ ){					
				var id = 'id=' + m[i].get('id') + '&gid=' + grid.id ;
				addTab( 'Modificar Moneda', '/currency/currency/update' , true, id );
			}
		}
		else
		{
			Ext.MessageBox.alert('Emporika', 'Por favor seleccione un item');
		}	
}

var button1 = grid.getTopToolbar().items.find( function(c){ return ( c.text == 'Nueva') } );
button1.on( 'click', nueva );

var button2 = grid.getTopToolbar().items.find( function(c){ return ( c.text == 'Modificar') } );
button2.on( 'click', edit );

grid.on( 'celldblclick', edit );

";

$grid->setEnableKeyEvents(true);
$grid->attachListener( "render", new PhpExt_Listener( PhpExt_Javascript::functionDef( null, $grid_render , array( "grid" ) )) );			   		   	 

//SE AGREGON LOS BOTONES EN LA BARRA DE MENU TOP
$tb = $grid->getTopToolbar();
$tb->addButton( "new", "Nueva", "images/add.png" );
$tb->addSeparator( "sep1" );
$tb->addButton( "modificar", "Modificar", "images/edit.gif" );


$resultado = '';
$resultado.= $check_select->getJavascript(false, "sm");
$resultado.= $store->getJavascript(false, "store_currency_list");
$resultado.= "store_currency_list.load({params: {start:0, limit:{$page_size} }});";
$resultado.= $filter_plugin->getJavascript( false, "filters");
$resultado.= $col_model->getJavascript( false, "cm");
$resultado.= "cm.defaultSortable = true;";
$resultado.= $grid->getJavascript(false,"contenido");

//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $resultado );
$obj_comunication->send(true);

?>