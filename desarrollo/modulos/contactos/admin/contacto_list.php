<?php

//CODIGO POR JPCOSEANI
//SCRIPT QUE GENERA EL LISTADO DE CONTACTOS

if (!seguridad :: isAllowed(seguridad_action :: nameConstructor('update','user','seguridad')) )
{
	throw new OOB_exception("Acceso Denegado", "403", "Acceso Denegado. Consulte con su Administrador!", true);
} 

PhpExt_Javascript::sendContentType();


global $ari;
$ari->popup = 1; // no mostrar el main_frame 
$page_size = PAGE_SIZE;


$store = new PhpExt_Data_JsonStore();
$store->setUrl("/contactos/contacto/get_contactos")	  
	  ->setRoot("topics")
      ->setTotalProperty("totalCount")
      ->setId("id");
	  
	  
$store->addField(new PhpExt_Data_FieldConfigObject("id"));
$store->addField(new PhpExt_Data_FieldConfigObject("nombre"));
$store->addField(new PhpExt_Data_FieldConfigObject("uname"));
$store->addField(new PhpExt_Data_FieldConfigObject("apellido"));
$store->addField(new PhpExt_Data_FieldConfigObject("cuit"));
$store->addField(new PhpExt_Data_FieldConfigObject("ingbrutos"));
$store->addField(new PhpExt_Data_FieldConfigObject("numerocliente"));
$store->addField(new PhpExt_Data_FieldConfigObject("clase"));
$store->addField(new PhpExt_Data_FieldConfigObject("id_clase"));
$store->addField(new PhpExt_Data_FieldConfigObject("categoria"));
$store->addField(new PhpExt_Data_FieldConfigObject("usuario::name"));
$store->addField(new PhpExt_Data_FieldConfigObject("rubro::detalle"));
$store->addField(new PhpExt_Data_FieldConfigObject("telefonos"));
$store->addField(new PhpExt_Data_FieldConfigObject("dias_pago"));

if( $controles = contactos_informacion_adicional_control::getFilteredList() ){
	foreach( $controles as $control ){
	$nombre = contactos_informacion_adicional_control_propiedad::get_property_value( $control,'label' );
	$store->addField(new PhpExt_Data_FieldConfigObject('infoadicional_' . $control->id()));	
	}
}

$check_select = new PhpExt_Grid_CheckboxSelectionModel();

//ARMO UN ARRAY CON LAS CLASES DE CONTACTOS
$clases = array();		

if( $lista_clases = contactos_clase::getList() ){		
			foreach ( $lista_clases as $clase  ){
				$clases[] = array( $clase->id() , $clase->get('detalle') );
				
			}
	}	
	
//ARMO UN ARRAY CON LAS CATEGORIAS
$categorias = array();		

if( $lista_categoria = impuestos_categorizacion::getList() ){		
			foreach ( $lista_categoria as $categoria  ){
				$categorias[] = array( $categoria->id() , $categoria->get('nombre') );
				
			}
	}		

//FILTROS
$filter_plugin = new PhpExtUx_Grid_GridFilters();
$filter_plugin->addFilter(PhpExt_Grid_FilterConfigObject::createFilter("numeric","id"));
$filter_plugin->addFilter(PhpExt_Grid_FilterConfigObject::createFilter("date","fecha"));
$filter_plugin->addFilter(PhpExt_Grid_FilterConfigObject::createFilter("string","apellido")); 
$filter_plugin->addFilter(PhpExt_Grid_FilterConfigObject::createFilter("string","nombre"));
$filter_plugin->addFilter(PhpExt_Grid_FilterConfigObject::createFilter("numeric","cuit"));
$filter_plugin->addFilter(PhpExt_Grid_FilterConfigObject::createFilter("numeric","ingbrutos"));
$filter_plugin->addFilter(PhpExt_Grid_FilterConfigObject::createFilter("string","numerocliente"));
$filter_plugin->addFilter(PhpExt_Grid_FilterConfigObject::createFilter("numeric","dias_pago"));
$filter_plugin->addFilter(PhpExt_Grid_FilterConfigObject::createFilter("string","telefonos"));
$filter_plugin->addFilter(PhpExt_Grid_FilterConfigObject::createFilter("string","usuario::name"));
$filter_plugin->addFilter(PhpExt_Grid_FilterConfigObject::createFilter("string","rubro::detalle"));
$filter_plugin->addFilter(PhpExt_Grid_FilterConfigObject::createFilter( "list", "clase", PhpExt_Javascript::variable( json_encode($clases) ) ));   
$filter_plugin->addFilter(PhpExt_Grid_FilterConfigObject::createFilter( "list", "categoria", PhpExt_Javascript::variable( json_encode($categorias) ) ));   

if( $controles = contactos_informacion_adicional_control::getFilteredList() ){
	foreach( $controles as $control ){		
		switch( $control->get('tipo')->get('nombre') ){
		case 'TextField':
		case 'TextArea':
			$filter_plugin->addFilter(PhpExt_Grid_FilterConfigObject::createFilter("string",'infoadicional_' . $control->id()));		
		break;
		case 'NumberField':
			$filter_plugin->addFilter(PhpExt_Grid_FilterConfigObject::createFilter("numeric",'infoadicional_' . $control->id()));		
		break;	
		case 'DateField':
			$filter_plugin->addFilter(PhpExt_Grid_FilterConfigObject::createFilter("date",'infoadicional_' . $control->id()));		
		break;
		case 'RadioGroup':
		
			 $list = array();
			 $filtro_control = false;
			 $filtro_control[] = array("field"=>"control","type"=>"list","value"=>$control->id() );	
			 if( $list_subcontroles = contactos_informacion_adicional_subcontrol::getFilteredList(false,false,false,false,$filtro_control) ){
					foreach( $list_subcontroles as $subcontrol ){			
						$list[] = array( $subcontrol->id() , contactos_informacion_adicional_subcontrol_propiedad::get_property_value( $subcontrol,'label' ) );							
					}
			 }
		
			$filter_plugin->addFilter(PhpExt_Grid_FilterConfigObject::createFilter( "list", 'infoadicional_' . $control->id(), PhpExt_Javascript::variable( json_encode($list) ) ) ); 
		break;
		}
	}
}  
   
$col_model = new PhpExt_Grid_ColumnModel();
$col_model->addColumn($check_select)
		  ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("Id","id",null,30))
		  ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("Nombre","nombre"))
		  ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("Apellido","apellido"))			  
		  ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("CUIT","cuit"))
		  ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("Ing. Brutos","ingbrutos"))          
		  ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("Usuario","usuario::name"))
		  ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("N&uacute;mero cliente","numerocliente"))
		  ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("Clase","clase"))
		  ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("Rubro","rubro::detalle"))
		  ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("Categoria","categoria"))		  
		  ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("Tel&eacute;fonos","telefonos"))		  
		  ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("D&iacute;as Pago","dias_pago"));

		  
if( $controles = contactos_informacion_adicional_control::getFilteredList() ){
	foreach( $controles as $control ){
		$nombre = contactos_informacion_adicional_control_propiedad::get_property_value( $control,'label' );
		$col_model->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn( $nombre,'infoadicional_' . $control->id())->setHidden(true));
	}
}
         
$paging = new PhpExt_Toolbar_PagingToolbar();
$paging->setStore($store)
       ->setPageSize($page_size)
	   ->setDisplayInfo(true)	
       ->setEmptyMessage("No se encontraron contactos");
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

//grid on render
$grid_render = "

var store = grid.getStore();
var edit = function(){
	var id;
	var m = grid.getSelections();

	if( m.length >= 1 ){	
		for( var i = 0, len = m.length; i < len; i++ ){			
				id = 'id=' + m[i].get('id') + '&gid=' + grid.id ;
				addTab( 'Modificar Contacto', '/contactos/contacto/modificar', true, id);				
		}
	}
	else
	{
		Ext.MessageBox.alert('Emporika', 'Por favor seleccione un contacto');
	}

}//modificar

var nuevo = function(){
	id = 'gid=' + grid.id ;
	addTab( 'Nuevo Contacto', '/contactos/contacto/new' , true, id );
}

var eliminar = function(){

		var m = grid.getSelections();
        if(m.length > 0)
        {
			var msg = 'Esta seguro que desea eliminar ' + ((m.length>1)?'los':'el') + ' contacto' + ((m.length>1)?'s':'') + '?';
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
										DeleteData: Ext.encode(items) 
												}								
												
									  });		
					}		
			}, this );	
        }
        else
        {
        	Ext.MessageBox.alert('Emporika', 'Por favor seleccione un contacto');
        }

}

//function view
var view = function(){

	var id;
	var m = grid.getSelections();

	if( m.length >= 1 ){	
		for(var i = 0, len = m.length; i < len; i++){		
			var id = 'id=' + m[i].get('id');
			addTab( 'Detalle de Contacto', '/contactos/contacto/view', true, id );				
		}
	}
	else
	{
		Ext.MessageBox.alert('Emporika', 'Por favor seleccione \"un\" contacto');
	}

}
//end function view

var resumen = function(){

	var m = grid.getSelections();
        if(m.length > 0)
        {
			for(var i = 0, len = m.length; i < len; i++){					
				var item = { id : m[i].get('id'), uname : m[i].get('uname') }				
				var data = 'data=' + Ext.encode(item);
				if( m[i].get('id_clase') == '1' ){
					addTab( 'Resumen de Cuenta de cliente', '/ventas/venta/resumen', true, data );				
				}
				if( m[i].get('id_clase') == '2' ){
					addTab( 'Resumen de Cuenta de proveedor', '/compras/compra/summary', true, data );				
				}
			}
			
		}
        else
        {
        	Ext.MessageBox.alert('Emporika', 'Por favor seleccione un contacto');
        }

}

var sendnotificacion = function(){

		var m = grid.getSelections();
        if(m.length > 0)
        {
			var items = Array();
			for(var i = 0, len = m.length; i < len; i++){		
				var item = { id : m[i].get('id'), uname : m[i].get('uname') }				
				items.push(item);				
			}
			json = 'items=' + Ext.encode(items);
			addTab( 'Nueva Notificacion', '/contactos/notificacion/new' , true, json );
			
		}
        else
        {
        	Ext.MessageBox.alert('Emporika', 'Por favor seleccione un contacto');
        }

}

var button1 = grid.getTopToolbar().items.find( function(c){ return ( c.text == 'Nuevo') } );
var button2 = grid.getTopToolbar().items.find( function(c){ return ( c.text == 'Modificar') } );
var button3 = grid.getTopToolbar().items.find( function(c){ return ( c.text == 'Eliminar') } );
var button4 = grid.getTopToolbar().items.find( function(c){ return ( c.text == 'Ver detalles') } );
var button5 = grid.getTopToolbar().items.find( function(c){ return ( c.text == 'Resumen de Cuenta') } );
var button6 = grid.getTopToolbar().items.find( function(c){ return ( c.text == 'Nueva notificaci&oacute;n') } );

grid.on( 'rowdblclick', edit );
button1.on( 'click', nuevo );
button2.on( 'click', edit );
button3.on( 'click', eliminar );
button4.on( 'click', view );
button5.on( 'click', resumen );
button6.on( 'click', sendnotificacion );

";

$grid->setEnableKeyEvents(true);
$grid->attachListener( "render", new PhpExt_Listener( PhpExt_Javascript::functionDef( null, $grid_render , array( "grid" ) )) );			   		   	 

//AGREGO LOS BOTONES AL TOOLBAR DE LA GRILLA
$tb = $grid->getTopToolbar();
$tb->addButton( "new", "Nuevo", "images/add.png" );	 
$tb->addSeparator( "sep1" );
$tb->addButton("view","Ver detalles", "images/search.png" );
$tb->addSeparator("sep2");
$tb->addButton( "edit", "Modificar", "images/edit.gif" );
$tb->addSeparator("sep3");
$tb->addButton( "delete", "Eliminar", "images/no_.gif" );
$tb->addSeparator("sep4");
$tb->addButton( "resumen", "Resumen de Cuenta", "images/Empty.gif" );
$tb->addSeparator("sep5");
$tb->addButton( "sendnotification", "Nueva notificaci&oacute;n", "images/Mail.gif" );

//RESULTADO
$resultado = '';
$resultado.= $check_select->getJavascript(false, "sm"); 
$resultado.= $store->getJavascript(false, "store_group_list");
$resultado.= "store_group_list.load({params:{ start:0 , limit:{$page_size}} });";
$resultado.= $filter_plugin->getJavascript(false, "filters");
$resultado.= $col_model->getJavascript(false, "cm");
$resultado.= "cm.defaultSortable = true;";
$resultado.= $grid->getJavascript(false,"contenido");

//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $resultado );
$obj_comunication->send(true);


?>