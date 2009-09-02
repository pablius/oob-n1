<?php
// CODIGO POR JPCOSEANI
// Script que genera el  LISTADO DE AREAS


//LIBRERIAS
PhpExt_Javascript::sendContentType();


global $ari;
$ari->popup = 1; // no mostrar el main_frame 
$separador_decimal = trim( $ari->locale->get('decimal', 'numbers') );
$page_size = PAGE_SIZE;

//STORE PARA TRAER LOS DATOS
$store = new PhpExt_Data_JsonStore();
$store->setUrl("/contactos/areas/get_areas")	  
	  ->setRoot("topics")	 
      ->setTotalProperty("totalCount");
     
//DEFINICION DE LOS CAMPOS DEL STORE	
$store->addField(new PhpExt_Data_FieldConfigObject("id"));
$store->addField(new PhpExt_Data_FieldConfigObject("nombre"));
$store->addField(new PhpExt_Data_FieldConfigObject("descripcion"));
$store->addField(new PhpExt_Data_FieldConfigObject("sucursal"));
$store->addField(new PhpExt_Data_FieldConfigObject("sucursal::nombre"));

$txt_detalle = new PhpExt_Form_TextField();
$txt_nombre = new PhpExt_Form_TextField();

	$sucursales = array();
	$filtros[] = array("field"=>"status","type"=>"list","value"=>"1");	
	if( $lista_sucursales = items_stock_sucursal::getFilteredList(false,false,false,false,$filtros) ){		
			foreach ( $lista_sucursales as $sucursal  ){
				$sucursales[] = array( $sucursal->id(), $sucursal->get('nombre') );				
			}
	}

	
	$store_sucursal = new PhpExt_Data_SimpleStore();
	$store_sucursal->addField("id");
	$store_sucursal->addField("detalle");
	$store_sucursal->setData( PhpExt_Javascript::variable(json_encode($sucursales)) );


	$cbo_sucursal = PhpExt_Form_ComboBox::createComboBox("cbo_sucursales")						   
				   ->setStore($store_sucursal)
				   ->setDisplayField("detalle")				   
				   ->setValueField("id")	
				   ->setLazyRender(true)
				   ->setEditable(false)	
				   ->setForceSelection(true)			
				   ->setSingleSelect(true)				
				   ->setMode(PhpExt_Form_ComboBox::MODE_LOCAL)
				   ->setTriggerAction(PhpExt_Form_ComboBox::TRIGGER_ACTION_ALL);
				   
$format_sucursal = "
	 function( v, params, record , rowIndex, colIndex, store ){			
		return record.data['sucursal'];
	 }
";	

$filter_plugin=new PhpExtUx_Grid_GridFilters();
$filter_plugin->addFilter(PhpExt_Grid_FilterConfigObject::createFilter("numeric","id"));
$filter_plugin->addFilter(PhpExt_Grid_FilterConfigObject::createFilter("string","nombre"));	 
$filter_plugin->addFilter(PhpExt_Grid_FilterConfigObject::createFilter("string","descripcion"));   
$filter_plugin->addFilter(PhpExt_Grid_FilterConfigObject::createFilter("string","sucursal::nombre"));   

$col_model = new PhpExt_Grid_ColumnModel();
$col_model->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("Id","id",null,30))		  
		  ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("Nombre","nombre")->setEditor($txt_nombre))
		  ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("Descripci&oacute;n","descripcion",null,200)->setEditor($txt_detalle));
$col_model->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("Sucursal","sucursal::nombre",null,80,PhpExt_Ext::HALIGN_RIGHT,new PhpExt_JavascriptStm($format_sucursal))->setEditor($cbo_sucursal));
         
$paging = new PhpExt_Toolbar_PagingToolbar();
$paging->setStore($store)
       ->setPageSize($page_size)
	   ->setDisplayInfo(true)	
       ->setEmptyMessage("No se encontraron areas");
	   
$paging->getPlugins()->add($filter_plugin);		   
		 
$grid = new PhpExt_Grid_EditorGridPanel();
$grid->setColumnModel($col_model)
	 ->setStore($store)	
	 ->setStripeRows(true)
	  ->setSelectionModel(new PhpExt_Grid_RowSelectionModel())
	 ->setenableColLock( false )	 
	 ->setLoadMask(true);
 $grid->getPlugins()->add( $filter_plugin ); 
	 

$grid->setBottomToolbar( $paging );  

//grid on render
$grid_render = "

var store = grid.getStore();

grid.on('validateedit', function(e){
	    if(e.field == 'sucursal::nombre'){
		var combo = grid.getColumnModel().getCellEditor(e.column, e.row).field;				 		 
        e.record.data['sucursal'] = combo.getRawValue();
    }
});



var save = function(){

	var hab = true;
	var store_changes = store.getModifiedRecords();
	var items = Array();	
	var noborrar = 0;
	for( var i = 0, len = store_changes.length; i < len; i++ ){
	
				
		if( store_changes[i].get('nombre') == ''){														
			grid.startEditing(i, 1);
			hab = false;
		}
		
		if(  store_changes[i].get('descripcion') == '' && hab ){
			grid.startEditing(i, 2);
			hab = false;
		}
		
		if(  store_changes[i].get('sucursal') == '' && hab ){
			grid.startEditing(i, 3);
			hab = false;
		}
	
		var item = { 
					 id : store_changes[i].get('id'),
			     nombre : store_changes[i].get('nombre'),			 
		    descripcion : store_changes[i].get('descripcion'),
			   sucursal : store_changes[i].get('sucursal::nombre'),
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

var nuevo = function(){

var cmodel = Ext.data.Record.create([
				   {name: 'id'},
				   {name: 'nombre', type: 'string'},
				   {name: 'descripcion', type: 'string'},
				   {name: 'sucursal', type: 'string'},
				   {name: 'sucursal::nombre'}
			  ]);

var cm = new cmodel({
					id : '',
				nombre : '',
		   descripcion : '',
			  sucursal : '',
	'sucursal::nombre' : ''
				});
				
	grid.stopEditing();
	store.insert(0, cm);
	grid.startEditing(0, 1);

}

var eliminar = function(){

		var m = grid.getSelections();
        if(m.length > 0)
        {
			var msg = 'Esta seguro que desea eliminar ' + ((m.length>1)?'las':'la') + ' area' + ((m.length>1)?'s':'') + '?';
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
        	Ext.MessageBox.alert('Emporika', 'Por favor seleccione un item');
        }

}


var button1 = grid.getTopToolbar().items.find( function(c){ return ( c.text == 'Nueva') } );
var button2 = grid.getTopToolbar().items.find( function(c){ return ( c.text == 'Guardar cambios') } );
var button3 = grid.getTopToolbar().items.find( function(c){ return ( c.text == 'Eliminar') } );

button1.on( 'click', nuevo );
button2.on( 'click', save );
button3.on( 'click', eliminar );


";

$grid->setEnableKeyEvents(true);
$grid->attachListener( "render", new PhpExt_Listener( PhpExt_Javascript::functionDef( null, $grid_render , array( "grid" ) )) );			   		   	 
$grid->getPlugins()->add( new PhpExtUx_App_FitToParent() );   

//AGREGO LOS BOTONES AL TOOLBAR DE LA GRILLA
$tb = $grid->getTopToolbar();
$tb->addButton( "new", "Nueva", "images/add.png" );	 
$tb->addSeparator( "sep1" );
$tb->addButton( "delete", "Eliminar", "images/no_.gif" );
$tb->addSeparator("sep2");
$tb->addButton( "update", "Guardar cambios","images/save.gif" );



//RESULTADO
$resultado = '';
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