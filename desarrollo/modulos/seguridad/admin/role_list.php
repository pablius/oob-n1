<?php

#OOB/N1 Framework [2008 - Nutus] - PM 

// Código por JPCOSEANI
// Script que genera el FORM LISTADO DE ROLES

if (!seguridad :: isAllowed(seguridad_action :: nameConstructor('update','user','seguridad')))
{
	throw new OOB_exception("Acceso Denegado", "403", "Acceso Denegado. Consulte con su Administrador!", true);
} 

PhpExt_Javascript::sendContentType();

global $ari;
$ari->popup = 1; // no mostrar el main_frame 
$page_size = PAGE_SIZE;

$store = new PhpExt_Data_JsonStore();
$store->setUrl("/seguridad/role/get_roles")	  
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
		  ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn( "Id", "id", null, 40 ))
		  ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn( "Nombre", "name", null, 120 ))
          ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn( "Descripci&oacute;n", "description" ,null ,250 ));
          
         
$paging = new PhpExt_Toolbar_PagingToolbar();
$paging->setStore( $store )
       ->setPageSize( $page_size )
	   ->setDisplayInfo(true)	
       ->setEmptyMessage("No se encontraron roles");
$paging->getPlugins()->add( $filter_plugin );		   
		 

$grid = new PhpExt_Grid_GridPanel();
$grid->setColumnModel($col_model)
	 ->setSelectionModel($check_select)
	 ->setStore($store)
	 ->setStripeRows(true)	  
	 ->setLoadMask(true);
     
$grid->getPlugins()->add($filter_plugin);     
$grid->getPlugins()->add(new PhpExtUx_App_FitToParent());  
$grid->setBottomToolbar($paging); 

$grid_render = "

var store = grid.getStore();

var nuevo = function(){
	var id = 'gid=' + grid.id ;
	addTab( 'Nuevo Rol', '/seguridad/role/new', true ,id );
}

//function eliminar
var eliminar = function(){
	
        var m = grid.getSelections();
        if(m.length > 0)
        {
			var msg = 'Esta seguro que desea eliminar ' + ((m.length>1)?'los':'el') + ' rol' + ((m.length>1)?'es':'') + '?';
        	Ext.MessageBox.confirm( 'Emporika', msg , 
			function(btn){
					if(btn == 'yes'){								
							var items = Array();
							for(var i = 0, len = m.length; i < len; i++){  
								var item = { id: m[i].get('id') };
								items.push( item );
							}
																
							store.load( { params:{ 
												start: 0,
												limit: {$page_size} , 
										DeleteRolData: Ext.encode(items) 
												}								
												
									  });		
					}		
			}, this );	
        }
        else
        {
        	Ext.MessageBox.alert('Emporika', 'Por favor seleccione un item');
        }
   	
}//end function eliminar

//function edit
var edit = function(){

	var id;
	var m = grid.getSelections();

	if( m.length >= 1 ){	
		for( var i = 0, len = m.length; i < len; i++ ){			
				var id = 'id=' + m[i].get('id') + '&gid=' + grid.id;
				addTab('Modificar Rol','/seguridad/role/update',true,id);				
		}
	}
	else
	{
		Ext.MessageBox.alert('Emporika', 'Por favor seleccione un item');
	}	
	
}//end function edit

var button1 = grid.getTopToolbar().items.find( function(c){ return ( c.text == 'Nuevo') } );
button1.on( 'click', nuevo );

var button2 = grid.getTopToolbar().items.find( function(c){ return ( c.text == 'Modificar') } );
button2.on( 'click', edit );

var button3 = grid.getTopToolbar().items.find( function(c){ return ( c.text == 'Eliminar') } );
button3.on( 'click', eliminar );

grid.on( 'celldblclick', edit );

";

$grid->setEnableKeyEvents(true);
$grid->attachListener( "render", new PhpExt_Listener( PhpExt_Javascript::functionDef( null, $grid_render , array( "grid" ) )) );			   		   	 


$tb = $grid->getTopToolbar();
$tb->addButton( "new", "Nuevo", "images/add.png" );	 
$tb->addSeparator("sep1");
$tb->addButton( "modificar", "Modificar", "images/edit.gif" );
$tb->addSeparator("sep2");
$tb->addButton( "delete", "Eliminar", "images/no_.gif" );	 
	 
$resultado = '';	
$resultado.= $check_select->getJavascript(false, "sm");
$resultado.= $store->getJavascript(false, "store_role_list");
$resultado.= "store_role_list.load({params:{start:0,limit:{$page_size}}});";
$resultado.= $filter_plugin->getJavascript(false, "filters");
$resultado.= $col_model->getJavascript(false, "cm");
$resultado.= "cm.defaultSortable = true;";
$resultado.= $grid->getJavascript(false,"contenido");

//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $resultado );
$obj_comunication->send(true);

?>