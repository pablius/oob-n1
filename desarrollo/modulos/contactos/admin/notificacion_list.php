<?php

//CODIGO POR JPCOSEANI
//SCRIPT QUE GENERA EL LISTADO DE NOTIFICACIONES

if (!seguridad :: isAllowed(seguridad_action :: nameConstructor('update','user','seguridad')) )
{
	throw new OOB_exception("Acceso Denegado", "403", "Acceso Denegado. Consulte con su Administrador!", true);
} 


PhpExt_Javascript::sendContentType();

include_once 'PhpExtUx/Multiselect/multiselect.php';

global $ari;
$ari->popup = 1; // no mostrar el main_frame 
$page_size = PAGE_SIZE;

$store = new PhpExt_Data_JsonStore();
$store->setUrl("/contactos/notificacion/get_notificaciones")	  
	  ->setRoot("topics")
      ->setTotalProperty("totalCount")
      ->setId("id");
	  
	  
$store->addField(new PhpExt_Data_FieldConfigObject("id"));
$store->addField(new PhpExt_Data_FieldConfigObject("novedad"));
$store->addField(new PhpExt_Data_FieldConfigObject("contactos"));
$store->addField(new PhpExt_Data_FieldConfigObject("mensaje"));
$store->addField(new PhpExt_Data_FieldConfigObject("resultado"));
$store->addField(new PhpExt_Data_FieldConfigObject("iscomunicacion"));
$store->addField(new PhpExt_Data_FieldConfigObject("sendmail"));
$store->addField(new PhpExt_Data_FieldConfigObject("receptor"));
$store->addField(new PhpExt_Data_FieldConfigObject("estado"));
$store->addField(new PhpExt_Data_FieldConfigObject("fecha"));



$check_select = new PhpExt_Grid_CheckboxSelectionModel();

// Form Panel
$gridForm = new PhpExt_Form_FormPanel();
$gridForm->setFrame(true)
		 ->setLayout(new PhpExt_Layout_BorderLayout());
        

//ARMO UN ARRAY CON LAS REPETICIONES
$repeticiones = array();	

if( $lista_repeticiones = ventas_repeticion::getList() ){		
			foreach ( $lista_repeticiones as $repeticion  ){
				$repeticiones[] = array( $repeticion->get('id') , $repeticion->get('detalle') );
				
			}
	}	

//FILTROS
$filter_plugin = new PhpExtUx_Grid_GridFilters();
$filter_plugin->addFilter(PhpExt_Grid_FilterConfigObject::createFilter("numeric","id"));
$filter_plugin->addFilter(PhpExt_Grid_FilterConfigObject::createFilter("date","fecha"));
$filter_plugin->addFilter(PhpExt_Grid_FilterConfigObject::createFilter("string","novedad")); 
$filter_plugin->addFilter(PhpExt_Grid_FilterConfigObject::createFilter("string","contacto"));
$filter_plugin->addFilter(PhpExt_Grid_FilterConfigObject::createFilter("string","mensaje"));

   
$col_model = new PhpExt_Grid_ColumnModel();
$col_model->addColumn($check_select)
		  ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("Id","id",null,30))
		  ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("Novedad","novedad",null,200))
		  ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("Fecha","fecha"))
		  ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("Es comunicaci&oacute;n","iscomunicacion"))				  
 		  ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("Enviado por email","sendmail"))				  
 	      ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("Receptor","receptor"))				  						
		  ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("Estado","estado"));		
		  
         
$paging = new PhpExt_Toolbar_PagingToolbar();
$paging->setStore($store)
       ->setPageSize($page_size)
	   ->setDisplayInfo(true)	
       ->setEmptyMessage("No se encontraron notificaciones");
	   
$paging->getPlugins()->add($filter_plugin);		   
		 
$grid = new PhpExt_Grid_GridPanel();
$grid->setColumnModel($col_model)
	 ->setStore($store)	
	 ->setStripeRows(true)
	 ->setHeight(240)
	 ->setSelectionModel($check_select)	 	
	 ->setLoadMask(true);
 
	 

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
				addTab( 'Modificar Notificaci&oacute;n', '/contactos/notificacion/update', true, id);				
		}
	}
	else
	{
		Ext.MessageBox.alert('Emporika', 'Por favor seleccione un item');
	}

}//modificar

var nuevo = function(){
	id = 'gid=' + grid.id ;
	addTab( 'Nueva Notificacion', '/contactos/notificacion/new' , true, id );
}

var eliminar = function(){

		var m = grid.getSelections();
        if(m.length > 0)
        {
			var msg = 'Esta seguro que desea eliminar ' + ((m.length>1)?'las':'la') + ' notificaci' + ((m.length>1)?'ones':'c&oacute;n') + '?';
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
var button2 = grid.getTopToolbar().items.find( function(c){ return ( c.text == 'Modificar') } );
var button3 = grid.getTopToolbar().items.find( function(c){ return ( c.text == 'Eliminar') } );


var data;
var tpl;
grid.on( 'rowdblclick', edit );
button1.on( 'click', nuevo );
button2.on( 'click', edit );
button3.on( 'click', eliminar );

		var form = this.findParentByType('form');		
		var info = form.findBy(function(c){ return (c.xtype == 'panel') });
		
		var m = grid.getSelections();
		   
		grid.on( 'cellclick', function( grid, rowIndex, columnIndex, e){
			
			var r = grid.getStore().getAt(rowIndex);
			
			data = {
					 id : r.data.id,
				mensaje : r.data.mensaje,
			  resultado : r.data.resultado,    
			  contactos : r.data.contactos
			};
			
			
	
	//ACA ESTA EL TEMPLATE, EN CASO QUE ALGUIEN QUIERA MODIFICARLO
	 tpl = new Ext.XTemplate(
    '<p>Mensaje: {mensaje}</p>',
	'<p>Resultado: {resultado}</p>',    
    '<p>Contactos: ',
    '<tpl for=\"contactos\">',
        '<p>{#}. {name}</p>',
    '</tpl></p>'
	);

tpl.overwrite(info[0].body, data);



});


grid.getStore().on('load',function(){ 				
		if(data){
		var record = grid.getStore().findBy(function(c){
			return ( c.data.id == data.id );		
		});
		
		var r = grid.getStore().getAt(record);
		
		data.mensaje = r.data.mensaje;
		data.resultado = r.data.resultado;
		data.contactos = r.data.contactos;		
		
		tpl.overwrite(info[0].body, data);
		
		}
		});



";

$grid->setEnableKeyEvents(true);
$grid->attachListener( "render", new PhpExt_Listener( PhpExt_Javascript::functionDef( null, $grid_render , array( "grid" ) )) );			   		   	 

//AGREGO LOS BOTONES AL TOOLBAR DE LA GRILLA
$tb = $grid->getTopToolbar();
$tb->addButton( "new", "Nueva", "images/add.png" );	 
$tb->addSeparator( "sep1" );
$tb->addButton( "edit", "Modificar", "images/edit.gif" );
$tb->addSeparator("sep2");
$tb->addButton( "delete", "Eliminar", "images/no_.gif" );

$gridForm->addItem($grid, PhpExt_Layout_BorderLayoutData::createNorthRegion() );

$info = new PhpExt_Panel();
$info->setBodyStyle("background:#ffffff;padding:7px;");
$info->sethtml("Por favor seleccione una notificaci&oacute;n");
$gridForm->addItem($info,PhpExt_Layout_BorderLayoutData::createCenterRegion());


// $gridForm->addItem($rightPanel);
$gridForm->getPlugins()->add( new PhpExtUx_App_FitToParent() );   

//RESULTADO
$resultado = '';
$resultado.= $check_select->getJavascript(false, "sm"); 
$resultado.= $store->getJavascript(false, "store_group_list");
$resultado.= "store_group_list.load({params:{ start:0 , limit:{$page_size}} });";
$resultado.= $filter_plugin->getJavascript(false, "filters");
$resultado.= $col_model->getJavascript(false, "cm");
$resultado.= "cm.defaultSortable = true;";
$resultado.= $gridForm->getJavascript(false,"contenido");

//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $resultado );
$obj_comunication->send(true);

?>