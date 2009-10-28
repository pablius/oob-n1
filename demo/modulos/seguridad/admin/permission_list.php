<?php

#OOB/N1 Framework [2008 - Nutus] - PM 

// CODIGO POR JPCOSEANI
// Script que genera el FORM LISTADO DE PERMISOS


if ( !seguridad::isAllowed( seguridad_action::nameConstructor( 'list', 'impuesto', 'impuestos' ) ) )
{
	throw new OOB_exception("Acceso Denegado", "403", "Acceso Denegado. Consulte con su Administrador!", true);
} 

//LIBRERIAS
PhpExt_Javascript::sendContentType();


global $ari;
$ari->popup = 1; // no mostrar el main_frame 

$page_size = PAGE_SIZE;

//STORE PARA TRAER LOS DATOS
$store = new PhpExt_Data_JsonStore();
$store->setUrl("/seguridad/permission/get_permissions")	  
	  ->setRoot("topics")	 
      ->setTotalProperty("totalCount");
     
//DEFINICION DE LOS CAMPOS DEL STORE	
$store->addField(new PhpExt_Data_FieldConfigObject("id"));
$store->addField(new PhpExt_Data_FieldConfigObject("nombre"));
$store->addField(new PhpExt_Data_FieldConfigObject("descripcion"));
$store->addField(new PhpExt_Data_FieldConfigObject("modulo"));
$store->addField(new PhpExt_Data_FieldConfigObject("contacto"));
$store->addField(new PhpExt_Data_FieldConfigObject("sucursal::nombre"));
$store->addField(new PhpExt_Data_FieldConfigObject("sucursal"));

$filter_plugin=new PhpExtUx_Grid_GridFilters();
$filter_plugin->addFilter(PhpExt_Grid_FilterConfigObject::createFilter("numeric","id"));
$filter_plugin->addFilter(PhpExt_Grid_FilterConfigObject::createFilter("string","nombre"));	 
$filter_plugin->addFilter(PhpExt_Grid_FilterConfigObject::createFilter("string","contacto::apellido"));	 
$filter_plugin->addFilter(PhpExt_Grid_FilterConfigObject::createFilter("string","sucursal::nombre"));	 

			   
$paging = new PhpExt_Toolbar_PagingToolbar();
$paging->setStore($store)
       ->setPageSize($page_size)
	   ->setDisplayInfo(true)	   	
       ->setEmptyMessage("No se encontraron permisos");
$paging->getPlugins()->add($filter_plugin);					   


$txt_nombre = new PhpExt_Form_TextField();
$txt_descripcion  = new PhpExt_Form_TextField();


	$modulos = array();
	
	if( $listado_modulos =  OOB_module::listModules() )
	{	foreach( $listado_modulos as $modulo )
		{			
			$modulos[] = array( $modulo->name(), $modulo->nicename() );	
		}//end each
	}//end if

	
	$store_modulos = new PhpExt_Data_SimpleStore();
	$store_modulos->addField("id");
	$store_modulos->addField("detalle");
	$store_modulos->setData( PhpExt_Javascript::variable(json_encode($modulos)) );


	$cbo_modulo = PhpExt_Form_ComboBox::createComboBox("cbo_modulo")						   
				   ->setStore($store_modulos)
				   ->setDisplayField("detalle")				   
				   ->setValueField("id")	
				   ->setLazyRender(true)
				   ->setEditable(false)	
				   ->setForceSelection(true)			
				   ->setSingleSelect(true)				
				   ->setMode(PhpExt_Form_ComboBox::MODE_LOCAL)
				   ->setTriggerAction(PhpExt_Form_ComboBox::TRIGGER_ACTION_ALL);


$format_contacto = "
	 function( v, params, record , rowIndex, colIndex, store ){			
		return record.data['contacto'];
	 }
";	

$format_modulo = "
	 function( v, params, record , rowIndex, colIndex, store ){			
		return record.data['modulenicename'];
	 }
";					   
				   

$col_model = new PhpExt_Grid_ColumnModel();
$col_model->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("Id","id",null,30));
$col_model->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("Nombre","nombre",null,280)->setEditor($txt_nombre));
$col_model->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("Descripci&oacute;n","descripcion",null,280)->setEditor($txt_descripcion));
$col_model->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("Modulo","modulo",null,80,PhpExt_Ext::HALIGN_RIGHT,new PhpExt_JavascriptStm($format_modulo))->setEditor($cbo_modulo));



$grid = new PhpExt_Grid_EditorGridPanel();
$grid->setStore( $store )	 
	 ->setColumnModel( $col_model )	 
	 ->setSelectionModel(new PhpExt_Grid_RowSelectionModel())
	 ->setLoadMask( true )	 
	 ->setenableColLock( false );
	 
$grid->getPlugins()->add($filter_plugin);
$grid->getPlugins()->add(new PhpExtUx_App_FitToParent()); 
$grid->setBottomToolbar($paging);  	 

$grid_render = "

var store = grid.getStore();

grid.on('validateedit', function(e){
    if(e.field == 'modulo'){
		var combo = grid.getColumnModel().getCellEditor(e.column, e.row).field;				 		 
        e.record.data['modulenicename'] = combo.getRawValue();
    }	
});


var nuevo = function(){

var cmodel = Ext.data.Record.create([
				   {name: 'id'},
				   {name: 'nombre', type: 'string'},
				   {name: 'sucursal', type: 'string'},
				   {name: 'contacto', type: 'string'},
				   {name: 'sucursal::nombre'},
				   {name: 'contacto::apellido'}
			  ]);

var cm = new cmodel({
					id : '',
			    nombre : '',			 
			  sucursal : '',
			  contacto : '',
	'sucursal::nombre' : '',
  'contacto::apellido' : ''
					});
				
	grid.stopEditing();
	store.insert(0, cm);
	grid.startEditing(0, 1);

}

var eliminar = function(){

		var noborrar = 0;
		var m = grid.getSelections();
        if( m.length > 0 )
        {
			var msg = 'Esta seguro que desea ' + ((m.length > 1)?'los':'el') + ' deposito' + ((m.length > 1)?'s':'') + '?';
		
        	Ext.MessageBox.confirm('Emporika', msg , function( btn ){
					if( btn == 'yes' )
					{	
						var m = grid.getSelections();
						var items = Array();
						for( var i = 0, len = m.length; i < len; i++ ){  							
								var item = { id : m[i].get('id') }
								items.push( item );							
						}
						
						var pag = grid.getStore().lastOptions.params['start'];						
						store.load( { params : { 
												start : pag, 
												limit : {$page_size}, 
										   DeleteData : Ext.encode(items) }
									});		
					}			
			});	
        }
        else
        {
        	Ext.MessageBox.alert('Emporika', 'Por favor seleccione un deposito');
        }
}

var save = function(){

	var hab = true;
	var store_changes = store.getModifiedRecords();
	var items = Array();	
	
	for( var i = 0, len = store_changes.length; i < len; i++ ){
	
				
		if( store_changes[i].get('nombre') == '' ){														
			grid.startEditing(i, 1);
			hab = false;
		}
		
		if( store_changes[i].get('contacto') == '' && hab ){														
			grid.startEditing(i, 2);
			hab = false;
		}
		
		if( store_changes[i].get('sucursal') == '' && hab ){														
			grid.startEditing(i, 3);
			hab = false;
		}
		
			
		var item = { 
					 id : store_changes[i].get('id'),
			     nombre : store_changes[i].get('nombre'),
			   contacto : store_changes[i].get('contacto::apellido'),
			   sucursal : store_changes[i].get('sucursal::nombre')
				   }	
		items.push(item);		   
	}	
	
	if(hab){
		var pag = grid.getStore().lastOptions.params['start'];
		store.load( { params:{ 
							start : pag, 
							limit : {$page_size} , 
				   NewsValuesData : Ext.encode(items) }
				   } );
		store.commitChanges();
	}
	
}

var button1 = grid.getTopToolbar().items.find( function(c){ return ( c.text == 'Nuevo') } );
button1.on( 'click', nuevo );

var button2 = grid.getTopToolbar().items.find( function(c){ return ( c.text == 'Borrar') } );
button2.on( 'click', eliminar );

var button3 = grid.getTopToolbar().items.find( function(c){ return ( c.text == 'Guardar cambios') } );
button3.on( 'click', save );

";	
	
$grid->setEnableKeyEvents(true);
$grid->attachListener( "render", new PhpExt_Listener( PhpExt_Javascript::functionDef( null, $grid_render , array( "grid" ) )) );			   		   	 	

$tb = $grid->getTopToolbar();
$tb->addButton( "new", "Nuevo", "images/add.png" );
$tb->addSeparator( "sep1" );
$tb->addButton( "delete", "Borrar", "images/no_.gif" );
$tb->addSeparator( "sep2" );
$tb->addButton( "update", "Guardar cambios","images/save.gif" );


$resultado = '';
$resultado.= $store->getJavascript(false, "store_sucursales_list");
$resultado.= " store_sucursales_list.load( { params : { start : 0, limit : $page_size } } );";
$resultado.= $filter_plugin->getJavascript(false, "filters");
$resultado.= $col_model->getJavascript(false, "cm");
$resultado.= "cm.defaultSortable = true;";
$resultado.= $grid->getJavascript(false,"contenido");


//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $resultado );
$obj_comunication->send(true);

?>