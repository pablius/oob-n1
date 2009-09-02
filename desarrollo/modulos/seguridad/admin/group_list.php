<?php

#OOB/N1 Framework [2008 - Nutus] - PM 

// Código por JPC
// Script que genera el FORM LISTAR GRUPOS

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
include_once 'PhpExtUx/Grid/GridFilters.php';
include_once 'PhpExtUx/Grid/FilterConfigObject.php';
include_once 'PhpExtUx/App/FitToParent.php';

global $ari;
$ari->popup = 1; // no mostrar el main_frame 
$page_size = PAGE_SIZE;

$store = new PhpExt_Data_JsonStore();
$store->setUrl("/seguridad/group/get_groups")	  
	  ->setRoot("topics")
      ->setTotalProperty("totalCount")
      ->setId("id");
	  
	  
$store->addField(new PhpExt_Data_FieldConfigObject("id"));
$store->addField(new PhpExt_Data_FieldConfigObject("name"));
$store->addField(new PhpExt_Data_FieldConfigObject("description"));

$check_select = new PhpExt_Grid_CheckboxSelectionModel();

$filter_plugin=new PhpExtUx_Grid_GridFilters();
$filter_plugin->addFilter(PhpExt_Grid_FilterConfigObject::createFilter("numeric","id"));
$filter_plugin->addFilter(PhpExt_Grid_FilterConfigObject::createFilter("string","name"));	 
$filter_plugin->addFilter(PhpExt_Grid_FilterConfigObject::createFilter("string","description"));   
   
$col_model = new PhpExt_Grid_ColumnModel();
$col_model->addColumn($check_select)
		  ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("Id","id",null,30))
		  ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("Nombre","name",null,120))
          ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("Descripci&oacute;n","description",null,180));
          
         
$paging = new PhpExt_Toolbar_PagingToolbar();
$paging->setStore($store)
       ->setPageSize($page_size)
	   ->setDisplayInfo(true)		
       ->setEmptyMessage("No se encontraron grupos");
$paging->getPlugins()->add($filter_plugin);		   
		 

$grid = new PhpExt_Grid_GridPanel();
$grid->setColumnModel($col_model)
	 ->setStore($store)
	 ->setStripeRows(true)
	 ->setSelectionModel($check_select)	 	
	 ->setLoadMask(true);
     
$grid->getPlugins()->add( $filter_plugin ); 	
$grid->getPlugins()->add( new PhpExtUx_App_FitToParent() );   
$grid->setBottomToolbar( $paging );  


$grid_render = "

var store = grid.getStore();

// var tb = grid.getBottomToolbar();
// tb.doLoad(tb.cursor);
//nuevo grupo
var nuevo = function(){
	var id = 'gid=' + grid.id;
	addTab( 'Nuevo Grupo', '/seguridad/group/new', true , id );
}

//modificar grupo
var edit = function(){

	var id;
	var m = grid.getSelections();

	if( m.length >= 1 ){	
		for( var i = 0, len = m.length; i < len; i++ ){			
				var id = 'id=' + m[i].get('id') + '&gid=' + grid.id;
				addTab( 'Modificar Grupo', '/seguridad/group/update', true, id );
		}
	}
	else
	{
		Ext.MessageBox.alert('Emporika', 'Por favor seleccione un item');
	}	

}

//eliminar grupo
var eliminar = function(){

		var m = grid.getSelections();
        if(m.length > 0)
        {
			var msg = 'Esta seguro que desea eliminar ' + ((m.length>1)?'los':'el') + ' grupo' + ((m.length>1)?'s':'') + '?';
        	Ext.MessageBox.confirm('Emporika', msg , function(btn){
				if( btn == 'yes' ){
					var m = grid.getSelections();
					var items = Array();
					for( var i = 0, len = m.length; i < len; i++){  
						var item = { id : m[i].get('id') }
						items.push(item);					
					}
					var pag = grid.getStore().lastOptions.params['start'];
					store.load({params:{ start : pag,
         					             limit : {$page_size},
							   DeleteGroupData : Ext.encode(items)
							           }
								});					
				}	
			},this );	
        }
        else
        {
        	Ext.MessageBox.alert('Emporika', 'Por favor seleccione un item');
        }




}

grid.on( 'celldblclick', edit );

var button1 = grid.getTopToolbar().items.find( function(c){ return ( c.text == 'Nuevo') } );
button1.on( 'click', nuevo );

var button2 = grid.getTopToolbar().items.find( function(c){ return ( c.text == 'Modificar') } );
button2.on( 'click', edit );

var button3 = grid.getTopToolbar().items.find( function(c){ return ( c.text == 'Eliminar') } );
button3.on( 'click', eliminar );

";

$grid->setEnableKeyEvents(true);
$grid->attachListener( "render", new PhpExt_Listener( PhpExt_Javascript::functionDef( null, $grid_render , array( "grid" ) )) );			   		   	 

$tb = $grid->getTopToolbar();
$tb->addButton( "new", "Nuevo", "images/add.png" );	 
$tb->addSeparator( "sep1" );
$tb->addButton( "modificar", "Modificar", "images/edit.gif" );
$tb->addSeparator( "sep2" );
$tb->addButton( "delete", "Eliminar", "images/no_.gif" );	 
	
$resultado = '';	
$resultado.= $check_select->getJavascript(false, "sm"); 
$resultado.= $store->getJavascript(false, "store_group_list");
$resultado.= "store_group_list.load({params:{ start:0 , limit:{$page_size}} });";
$resultado.= $filter_plugin->getJavascript(false, "filters");
$resultado.= $col_model->getJavascript(false, "cm");
$resultado.= "cm.defaultSortable = true;";
$resultado.= $grid->getJavascript( false, "contenido" );

//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $resultado );
$obj_comunication->send(true);

?>