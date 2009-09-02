<?php

#OOB/N1 Framework [2008 - Nutus] - PM 

// Código por JPCOSEANI
// Script que genera el FORM Listado de Usuarios

if (!seguridad :: isAllowed(seguridad_action :: nameConstructor('update','user','seguridad')) )
{
	throw new OOB_exception("Acceso Denegado", "403", "Acceso Denegado. Consulte con su Administrador!", true);
} 


PhpExt_Javascript::sendContentType();

global $ari;
$ari->popup = 1; // no mostrar el main_frame 
$page_size = PAGE_SIZE;


//STORE PARA TRAER EL LISTADO DE USUARIOS
$store = new PhpExt_Data_JsonStore();
$store->setUrl("/seguridad/user/get_users")	  
	  ->setRoot("topics")	
	  ->setId("id")	  
      ->setTotalProperty("totalCount");
     
//AGREGO LOS CAMPOS AL STORE	
$store->addField( new PhpExt_Data_FieldConfigObject("id") );
$store->addField( new PhpExt_Data_FieldConfigObject("uname") );
$store->addField( new PhpExt_Data_FieldConfigObject("email") );
$store->addField( new PhpExt_Data_FieldConfigObject("status") );

$check_select = new PhpExt_Grid_CheckboxSelectionModel();

//Paso los estado a json
$estados = array();
foreach ( oob_user::getStatus() as $id=>$descripcion ){	
	$estados[] = array( $id, $descripcion );
}
			
$filter_plugin = new PhpExtUx_Grid_GridFilters();
$filter_plugin->addFilter(PhpExt_Grid_FilterConfigObject::createFilter( "numeric", "id" ) );
$filter_plugin->addFilter(PhpExt_Grid_FilterConfigObject::createFilter( "string", "uname" ) );	 
$filter_plugin->addFilter(PhpExt_Grid_FilterConfigObject::createFilter( "string", "email" ) );   
$filter_plugin->addFilter(PhpExt_Grid_FilterConfigObject::createFilter( "list", "status", PhpExt_Javascript::variable( json_encode($estados) ),PhpExt_Javascript::variable("1"),true ) ); 
   
$col_model = new PhpExt_Grid_ColumnModel();
$col_model->addColumn( $check_select )		  
		  ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn( "Id", "id", null,40))     
		  ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn( "Usuario", "uname", null , 140 ))
          ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn( "Email", "email", null, 170))
		  ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn( "Estado", "status" ));

$paging = new PhpExt_Toolbar_PagingToolbar();
$paging->setStore( $store )
       ->setPageSize( $page_size )
	   ->setDisplayInfo( true )	   
       ->setEmptyMessage( "No se encontraron usuarios" );
	   
$paging->getPlugins()->add($filter_plugin);	   
		 

//GRILLA		 
$grid = new PhpExt_Grid_GridPanel();
$grid->setStore($store)		     	
	 ->setSelectionModel($check_select)
	 ->setColumnModel($col_model)	 
	 ->setLoadMask(true)	 		 
	 ->setenableColLock(false);
	 
$grid->getPlugins()->add($filter_plugin);     
$grid->getPlugins()->add(new PhpExtUx_App_FitToParent());  

$grid_render = "

var combo = grid.getTopToolbar().items.find( function(c){ return ( c.xtype == 'combo') } );
var store = grid.getStore();

	var nuevo = function(){
		var id = 'gid=' + grid.id ;
		addTab( 'Nuevo Usuario', '/seguridad/user/new', true, id );
	}

var edit = function(){
	
			var id;
			var m = grid.getSelections();

			if( m.length >= 1 ){	
				for( var i = 0, len = m.length; i < len; i++ ){					
						var id = 'id=' + m[i].get('id') + '&gid=' + grid.id ;
						addTab('Modificar Usuario','/seguridad/user/update',true,id);						
				}
			}
			else
			{
				Ext.MessageBox.alert('Emporika', 'Por favor seleccione un item');
			}			
}

	var save = function(){
	
	if( combo.getValue() == '' ){
		Ext.Msg.alert('Nutus Econom&iacute;a', 'Debe seleccionar un estado' );	
	}
	else
	{
	
		var m = grid.getSelections();
        if(m.length > 0)
        {
        	Ext.MessageBox.confirm( 'Emporika', 'Esta seguro que desea cambiar el estado?' , function(btn){
				
				if( btn == 'yes' ){	
					var m = grid.getSelections();
					
					var items = Array();
					for(var i = 0, len = m.length; i < len; i++){  
						var item = { id : m[i].get('id') };
						items.push(item);
					}
					var json = { 
								 status : combo.getRawValue() ,
								  items : items							
							   }					
						   
					var pag = grid.getStore().lastOptions.params['start'];
					grid.getStore().load( { params : { start : pag, limit: {$page_size} , UpdateStateData: Ext.encode(json) } } );		
				}
				
			}, this );	
        }
        else
        {
        	Ext.MessageBox.alert('Emporika', 'Por favor seleccione un item');
        }

	}
	
	}


var eliminar = function(){
	
	var m = grid.getSelections();
        if(m.length > 0)
        {
			var msg = 'Esta seguro que desea eliminar ' + ((m.length>1)?'los':'el') + ' usuario' + ((m.length>1)?'s':'') + '?';
        	Ext.MessageBox.confirm('Emporika', msg , 
			function(btn){
					if( btn == 'yes' ){								
							var items = Array();
							for(var i = 0, len = m.length; i < len; i++){  
								var item = { id: m[i].get('id') };
								items.push( item );
							}
							
							var pag = grid.getStore().lastOptions.params['start'];	
							store.load( { params:{ 
												start: pag,
												limit: {$page_size} , 
										DeleteUserData: Ext.encode(items) 
												}								
												
									  });		
					}		
			}, this );	
        }
        else
        {
        	Ext.MessageBox.alert('Emporika', 'Por favor seleccione un item');
        }
}	
	
var button1 = grid.getTopToolbar().items.find( function(c){ return ( c.text == 'Nuevo') } );
button1.on( 'click', nuevo );

var button2 = grid.getTopToolbar().items.find( function(c){ return ( c.text == 'Modificar') } );
button2.on( 'click', edit );

var button3 = grid.getTopToolbar().items.find( function(c){ return ( c.text == 'Guardar cambios') } );
button3.on( 'click', save );

var button4 = grid.getTopToolbar().items.find( function(c){ return ( c.text == 'Eliminar') } );
button4.on( 'click', eliminar );

grid.on( 'celldblclick', edit );

";

$grid->setEnableKeyEvents(true);
$grid->attachListener( "render", new PhpExt_Listener( PhpExt_Javascript::functionDef( null, $grid_render , array( "grid" ) )) );			   		   	 
	 
//SETEO LA BARRA DE PAGINACION
$grid->setBottomToolbar($paging); 


//Data_Store para llenar el combo con los datos
$store_estados = new PhpExt_Data_SimpleStore();
$store_estados->addField("id");
$store_estados->addField("descripcion");
$store_estados->setData(PhpExt_Javascript::variable(json_encode($estados)));    

$cbo_estados = PhpExt_Form_ComboBox::createComboBox( null ,"Estado")
			   ->setWidth(80)
			   ->setStore( $store_estados )	
			   ->setDisplayField("descripcion")			 			   
			   ->setValueField("id")	
			   ->setEditable(false)	
			   ->setForceSelection(true)			
			   ->setSingleSelect(true)
			   ->setEmptyText("seleccionar")			   
			   ->setMode(PhpExt_Form_ComboBox::MODE_LOCAL)
			   ->setTriggerAction(PhpExt_Form_ComboBox::TRIGGER_ACTION_ALL);


$txt_busqueda = new PhpExtUx_App_SearchField();
$txt_busqueda->setStore($store);


$tb = $grid->getTopToolbar();
$tb->addButton( "new", "Nuevo", "images/add.png" );
$tb->addSeparator("sep1");
$tb->addButton( "modificar", "Modificar", "images/edit.gif" );
$tb->addSeparator( "sep2" );
$tb->addButton( "delete", "Eliminar", "images/no_.gif" );
$tb->addSeparator( "sep3" );
$tb->addTextItem( "text2", "Buscar:" );
$tb->addItem( "txt_busqueda", $txt_busqueda );
$tb->addFill( "fill" );
$tb->addTextItem( "text", "Modificar estado:" );
$tb->addItem( "estados", $cbo_estados );
$tb->addButton( "guardar", "Guardar cambios", "images/save.gif" );


//resultados
$resultado = '';
$resultado.= $check_select->getJavascript(false, "sm");
$resultado.= $store->getJavascript(false, "store_user_list");
$resultado.= "store_user_list.load( {params: { start: 0 , limit: {$page_size} }} );";
$resultado.= $filter_plugin->getJavascript(false, "filters");
$resultado.= $col_model->getJavascript(false, "cm");
$resultado.= "cm.defaultSortable = true;";
$resultado.= $grid->getJavascript( false, "contenido" );

//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $resultado );
$obj_comunication->send(true);

?>