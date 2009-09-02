<?php



global $ari;
$ari->popup = 1; // no mostrar el main_frame 

$tabpanelid = '';
if( isset($_POST['tabpanelid']) ) {
	$tabpanelid = $_POST['tabpanelid'];
}else{
	throw new OOB_Exception_400("La variable [tabpanelid] no esta definida");
}

$categorianombre = '';
if( isset($_POST['nombre']) ) {
	$categorianombre = $_POST['nombre'];
}else{
	throw new OOB_Exception_400("La variable [nombre] no esta definida");
}


PhpExt_Javascript::sendContentType();
//FORMULARIO
$frm_update_categoria = new PhpExt_Panel();
$frm_update_categoria->setFrame(true);
$frm_update_categoria->setLayout( new PhpExt_Layout_BorderLayout());
$frm_update_categoria->getPlugins()->add( new PhpExtUx_App_FitToParent() ); 

$top = new PhpExt_Panel();
$top->setLayout(new PhpExt_Layout_ColumnLayout())	
	->setHeight(100);
	
	$chk_empleado = PhpExt_Form_Checkbox::createCheckbox("chk_empleado" )->setBoxLabel("Empleados")
																		 ->setFieldLabel("Visible para");
	$chk_cliente = PhpExt_Form_Checkbox::createCheckbox("chk_cliente")->setBoxLabel("Clientes")
																	  ->setLabelSeparator("");
	$chk_proveedor = PhpExt_Form_Checkbox::createCheckbox("chk_proveedor")->setBoxLabel("Proveedores")
																		  ->setLabelSeparator("");
	

	$tb = $top->getTopToolbar();
	$tb->addButton( "save", "Guardar","images/save.gif" );		
	$tb->addSeparator("sep1");
	$tb->addButton( "delete", "Eliminar Categoria","images/no_.gif" );	
		

$top_render = "
var tabpanel = Ext.getCmp('{$tabpanelid}');
var id_categoria = '';
//function grabar
var guardar = function(){

	var a = panel.findParentBy(function(c){return true});
	
	var chk_empleado = panel.findBy(function(c){
	return (c.name == 'chk_empleado')
	});
	
	var chk_cliente = panel.findBy(function(c){
	return (c.name == 'chk_cliente')
	});
	
	var chk_proveedor = panel.findBy(function(c){
	return (c.name == 'chk_proveedor')
	});
	
	if( !chk_empleado[0].getValue() && !chk_cliente[0].getValue() && !chk_proveedor[0].getValue() ){
		Ext.MessageBox.alert( 'Emporika', 'Debe seleccionar la visibilidad de la categoria' );
		return false;
	}
	
	var txt_nombre = panel.findBy(function(c){
	return (c.name == 'txt_nombre')
	});
	
	var txt_descripcion = panel.findBy(function(c){
	return (c.name == 'txt_descripcion')
	});
	
		
	var preview = a.findBy(function(c){
		return (c.xtype == 'fieldset')
	});
	
	var items = Array();
	preview[0].findBy(function(c){		
				
		var f = c.getEl().up('div.x-form-item');
		var prop = Array();
		var caption = '';
		caption = f.dom.firstChild.firstChild.nodeValue;
		caption = caption.replace(/:/gi,'');
		
		prop.push({
				label : caption,
		  descripcion : c.descripcion,
			requerido : c.requerido,
			  validez : c.validez		
		});
		
		if( c.getXType() == 'radiogroup' || c.getXType() == 'checkboxgroup' ){ 
				var r_items = c.items.items;					
				var i = 0;
				var subitems = Array();
				while( i < r_items.length ){
				
					subitems.push({ 
								label : r_items[i].boxLabel, 
								name  : r_items[i].descripcion,
								tipo  : (c.getXType() == 'radiogroup')?'radio':'check'								
								});
					i++;
				}				
				  
			}
		
		var item = {        name : c.name,
						   xtype : c.getXType(),
					 propiedades : prop,
					    subitems : subitems
					}
		items.push(item);
	});
	
	
	
	if(id_categoria == ''){
		url = 'contactos/infoadicional/new_categoria_process';
	}else{
		url = 'contactos/infoadicional/modificar_categoria_process';
	}
	
	Ext.Ajax.request( { 		   url : url,
						method : 'POST',
						params : {	 
									   id : id_categoria,
									items : Ext.encode(items),
						  		   nombre : txt_nombre[0].getValue(),
							  descripcion : txt_descripcion[0].getValue(),
								 empleado : chk_empleado[0].getValue(),
								  cliente : chk_cliente[0].getValue(),
								proveedor : chk_proveedor[0].getValue()
								 },
					   success : function( responseObject ){															
							if(id_categoria == ''){							
								var obj = Ext.decode(responseObject.responseText);
								id_categoria = obj.id;									
							}
							Ext.MessageBox.alert( 'Emporika', 'Los datos se han guardado correctamente' );
						}								
					});
		
}//end function

//function eliminar
var eliminar = function(){

	var a = panel.findParentBy(function(c){return true});
	
	Ext.MessageBox.confirm('Emporika', 'Esta seguro que desea eliminar la categoria?' , 
			function(btn){
				if(btn == 'yes'){
				
							Ext.Ajax.request( { url : 'contactos/infoadicional/delete_categoria_process',
												method : 'POST',
												params : {	
															  id  : id_categoria
														 },
											   success : function( responseObject ){	
													Ext.MessageBox.alert( 'Emporika', 'Categoria eliminada correctamente' );
													var p = tabpanel.getActiveTab();
													tabpanel.remove(p);																																						
												}								
											});				

				}					
			});
			
}//end function

var button1 = panel.getTopToolbar().items.find( function(c){ return (c.text == 'Guardar') } );
button1.on( 'click', guardar );

var button2 = panel.getTopToolbar().items.find( function(c){ return (c.text == 'Eliminar Categoria') } );
button2.on( 'click', eliminar );

";

$top->setEnableKeyEvents(true);
$top->attachListener( "render", new PhpExt_Listener( PhpExt_Javascript::functionDef( null, $top_render , array( "panel" ) )) );			   		   	  
	 

//trae los items del menu
$tree_loader = new  PhpExt_Tree_TreeLoader();
$tree_loader->setDataUrl("/contactos/infoadicional/get_controles");

//armo el nodo root (no es visible)
$root = new PhpExt_Tree_AsyncTreeNode();
	$root->setText("Principal Node")
		 ->setDraggable(false)		 
		 ->setId("Principal_Node")
		 ->setExpanded(true)
		 ->setCssClass("feeds-node")
		 ->expandChildNodes(true);

	$west = new PhpExt_Tree_TreePanel();
	$west->setTitle("Controles")	  		  
				  ->setLayout(new PhpExt_Layout_FitLayout())			  			 
				  ->setCollapsible(true) 		 
				  ->setLines(false)
				  ->setSingleExpand(true)
				  ->setWidth(100)
				  ->setAutoScroll(true)
				  ->setCollapseFirst(false)
				  ->setSingleExpand(false)
				  ->setUseArrows(false)
				  ->setRootVisible(false)			 
				  ->setRoot($root)					 			
				  ->setLoader($tree_loader);


$center = new PhpExt_Panel();
$center ->setBodyStyle("background:#ffffff;")
		->setTitle("Vista Previa")
		->setAutoScroll(true)
		->setFrame(true);
		
$fieldset3 = new PhpExt_Form_FieldSet();	
$fieldset3->setBorder(false)		 
	      ->setAutoHeight(true);		
$center->addItem($fieldset3);

$east = new PhpExt_Panel();
$east->setWidth(220)	  
	 ->setCollapsible(true)  
	 ->setFrame(true)	 
     ->setLayout(new PhpExt_Layout_FitLayout());
	 
$barra=new  PhpExt_Toolbar_Toolbar ();		 
$barra->addButton( "remover", "Remover", "images/no_.gif" );
$barra->addFill("fe");


//GRILLA CON LAS PROPIEDADES DE LOS CONTROLES
$propertygrid = new PhpExt_Grid_PropertyGrid();
$propertygrid->setTitle("Propiedades");
$propertygrid->setTopToolbar($barra);

$propertygrid_render = "
	
	var button1 = t.getTopToolbar().items.find( function(c){ return ( c.text == 'Remover') } );
	var obj = this;
	button1.on('click',function(){ 
	
	var source = obj.getSource();
	if(source){
			var objeto = source.objeto;
			var f = objeto.findParentByType('form');
					var i= objeto.el.up( '.x-form-item' );					
					f.remove(objeto);
					if( i != null){
					i.remove();
					}
					f.doLayout(true);                    
                    
		obj.setSource(false);	
	}else{
			Ext.MessageBox.alert( 'Emporika', 'Debe seleccionar un control' );
	}
	});
	
	

	
";

$propertygrid->setEnableKeyEvents(true);
$propertygrid->attachListener( "render", new PhpExt_Listener( PhpExt_Javascript::functionDef( null, $propertygrid_render , array("t") )) );			   		   	 					 	 

$txt = new PhpExt_Form_TextField();
$txt->setReadOnly(true);


$validez = array();	
if( $lista_validez = contactos_informacion_adicional_validez::getFilteredList() ){		
			foreach ( $lista_validez as $val  ){				
					$validez[] = array( $val->get('id') , $val->get('descripcion') );				
			}
	}				  

//STORE PARA EL ARRAY DE CLASES	
$store_validez = new PhpExt_Data_SimpleStore();
$store_validez->addField("id");
$store_validez->addField("name");			  
$store_validez->setData(PhpExt_Javascript::variable( json_encode($validez) ) );	  


$cbo_validez = PhpExt_Form_ComboBox::createComboBox("cbo_validez")						   
				->setStore( $store_validez )			
				->setDisplayField("name")			   
				->setValueField("name")		
				->setEditable(false)					
				->setForceSelection(true)			
				->setSingleSelect(true)				
				->setMode(PhpExt_Form_ComboBox::MODE_LOCAL)			   
				->setTriggerAction(PhpExt_Form_ComboBox::TRIGGER_ACTION_ALL);

$ce = $propertygrid->getCustomEditors();
$ce->add( new PhpExt_GridEditor( new PhpExt_Form_TextArea()),'Opciones');
$ce->add( new PhpExt_GridEditor( new PhpExt_Form_TextArea() ),'Descripcion');
$ce->add( new PhpExt_GridEditor( $cbo_validez ),'Validez');

$east->addItem($propertygrid);

$frm_update_categoria->addItem( $top, PhpExt_Layout_BorderLayoutData::createNorthRegion());
$frm_update_categoria->addItem( $west, PhpExt_Layout_BorderLayoutData::createWestRegion());
$frm_update_categoria->addItem( $center, PhpExt_Layout_BorderLayoutData::createCenterRegion());
$frm_update_categoria->addItem( $east, PhpExt_Layout_BorderLayoutData::createEastRegion());

$form_render = "


var tabpanel = Ext.getCmp('{$tabpanelid}');
var panel = tabpanel.getActiveTab();

var txt_nombre = form.findBy(function(c){
	return (c.name == 'txt_nombre')
});

txt_nombre[0].on('keyup',function(t){
panel.setTitle(t.getValue());
});

var controles = form.findBy(function(c){
return (c.xtype == 'treepanel')
});

var preview = form.findBy(function(c){
return (c.xtype == 'fieldset')
});

var property = form.findBy(function(c){
return (c.title == 'Propiedades')
});

controles[0].on('click',function(n){	
	var n = n;	
var promptmsg = function(){	
	Ext.Msg.prompt('Emporika', 'Por favor ingrese un nombre:', function(btn, text){
		if (btn == 'ok' ){
			if( text != '' ){
				
				var existe = false;
				preview[0].findBy(function(c){

					var f = c.getEl().up('div.x-form-item');
					if( f.dom.firstChild.firstChild.nodeValue.replace(/:/gi,'').toUpperCase() == text.toUpperCase() ){
						existe = true;
					}

				});
			
				if(!existe){
					agregar(n.text,text);
				}
				else
				{
					Ext.Msg.alert( 'Emporika', 'El control con ese nombre ya existe',function(){
					promptmsg();
				});
				
				}				
				
			}
			else
			{
				Ext.Msg.alert( 'Emporika', 'Debe ingresar un nombre',function(){
					promptmsg();
				});
			}//end if
			
		}
	});		
	
}

promptmsg();	
});

var agregar = function(control_name,propname){
var dif = false;
switch(control_name)
{
case 'RadioGroup':

		var nameradio = 'name'+ Ext.id();
	var radio =  new Ext.form.Radio({
			name : nameradio,
		boxLabel : 'Item 1',	
		enableKeyEvents:true,
   focusClass : 'x-form-focus2'
	});
	
	radio.on('check',function(){																
						property[0].setSource(obj);										
	});
	
	var radio2 =  new Ext.form.Radio({
			name : nameradio,
		boxLabel : 'Item 2',	
		enableKeyEvents:true,
   focusClass : 'x-form-focus2'
	});
	
	radio2.on('check',function(){																
						property[0].setSource(obj);										
	});

	//radiogroup
	var control = new  Ext.form.RadioGroup({				
		fieldLabel: propname,
		enableKeyEvents:true,
		items: [radio,radio2]		
	});
	
	control.on('render',function(e){	
	e.getEl().on('contextmenu',function(el){		
		cm(el,preview[0],control);
	});
	});
	
	var labels = Array();	
	labels.push('Item 1');
	labels.push('Item 2');
	
		var obj = {	
	  Nombre : propname,
 Descripcion : control.descripcion,   
	 Validez : control.validez,
	  objeto : control,
	Opciones : labels.join('\\n')
	};
	
	dif = true;
	
break;
case 'CheckGroup':
	
	var check = new Ext.form.Checkbox({
		boxLabel: 'Item 1',
		enableKeyEvents : true,
   focusClass : 'x-form-focus2'
	});
			
	check.on('check',function(){
		property[0].setSource(obj);	
	});
	
	var control = new  Ext.form.CheckboxGroup({				
		fieldLabel: propname,
		items: [check]		
	});
		
		var obj = {	
	  Nombre : propname,
 Descripcion : control.descripcion,   
	 Validez : control.validez,
	  objeto : control,
	Opciones : 'Item 1'
	};
	
	dif = true;
	
break;
case 'TextArea':

var control = new Ext.form.TextArea({				
		fieldLabel : propname,
   enableKeyEvents : true,
	  descripcion  : '',
		   validez : 'Sin validez',
		 requerido : false,
   focusClass : 'x-form-focus2'
	});	
break;
case 'DateField':
	 var control = new Ext.form.DateField({				
		fieldLabel : propname,
	  descripcion  : '',
		   validez : 'Sin validez',
		 requerido : false,
   focusClass : 'x-form-focus2'			 
	 });
break;
case 'TextField':
	var control	= new Ext.form.TextField({				
		fieldLabel : propname,
			 value : 'TextField',
	  descripcion  : '',
		   validez : 'Sin validez',
		 requerido : false,
   focusClass : 'x-form-focus2'
	});	
break;
case 'NumberField':

	var control = new Ext.form.NumberField({				
		fieldLabel : propname,
			 value : '123456',
	  descripcion  : '',
		   validez : 'Sin validez',
		 requerido : false ,
   focusClass : 'x-form-focus2'      
	});

break;
case 'TimeField':
	var control = new Ext.form.TimeField({				
		fieldLabel : propname,
	  descripcion  : '',
		   validez : 'Sin validez',
		 requerido : false,
   focusClass : 'x-form-focus2'
		
	});
break;
}//end switch

	if(!dif){		
		var obj = {		
		Nombre : propname,		
   Descripcion : control.descripcion,
	 Requerido : control.requerido,
	   Validez : control.validez,
		objeto : control
		};	
		
		control.on('focus',function(){
		property[0].setSource(obj);
		});
	}

	control.on('render',function(e){	
		e.getEl().on('contextmenu',function(el){
			cm(el,preview[0],control);
		});
	});
	
	//agrego el control y muestro las propiedades
	preview[0].add(control);	
	preview[0].doLayout(true);
	property[0].setSource(obj);



}

var cm = function(e,form,c) {

this.contextMenu = new Ext.menu.Menu({
id: 'gridCtxMenu',
items: [{
text: 'Remover',
icon: 'images/no_.gif',
 listeners: {'click': function(t){ 					
					var i= c.el.up( '.x-form-item' );					
					form.remove(c);
					if( i != null){
					i.remove();
					}
					form.doLayout(true);                    
                    }
                  }
}]
});

var xy = e.getXY();
e.stopEvent();
this.contextMenu.showAt(xy);
}

property[0].on('propertychange',function(o,r,nv,ov){
if( r == 'Nombre' ){

var f = o.objeto.getEl().up('div.x-form-item');
	var existe = false;
				preview[0].findBy(function(c){
					var fe = c.getEl().up('div.x-form-item');					
					if( fe.dom.firstChild.firstChild.nodeValue.replace(/:/gi,'').toUpperCase() == nv.toUpperCase() ){
						existe = true;
					}

				});

	if(!existe)
	{
		f.dom.firstChild.firstChild.nodeValue = String.format('{0}', nv.replace(/:/gi,'') + ':' );	
	}
	else
	{		
		o.Nombre = ov;		
		Ext.Msg.alert( 'Emporika', 'Ya existe un control con ese nombre',function(){
			property[0].setSource(o);
		});
	}//end if
}

if( r == 'Descripcion'){	
	o.objeto.descripcion = nv;		
}

if( r == 'Requerido' ){
	o.objeto.requerido = nv;
}

if( r == 'Validez'){	
	o.objeto.validez = nv;		
}

if( r == 'Opciones'){
		var a_nv = nv.split('\\n');
	
	var o_items = o.objeto.items.items;					
				var i = 0;
				var olditems = Array();
				while( i < o_items.length ){							
					olditems.push({ 								
								name  : o_items[i].descripcion							
								 });
				     i++;
				}
	
	i = 0;
	var r_items = Array();
	var nameradio = 'name'+ Ext.id();
	while( i < a_nv.length){	
	
		if( o.objeto.getXType() == 'radiogroup' ){
			if(olditems[i]){
				var radio =  new Ext.form.Radio({
					name : nameradio,
			 descripcion : olditems[i].name,
				boxLabel : a_nv[i],	
				enableKeyEvents:true
				});		
			}else{
				var radio =  new Ext.form.Radio({
								name : nameradio,					 
							boxLabel : a_nv[i],	
							enableKeyEvents:true
							});				
			}
		}else{
		
		if(olditems[i]){
		var radio =  new Ext.form.Checkbox({
					name : nameradio,
			 descripcion : olditems[i].name,
				boxLabel : a_nv[i],	
				enableKeyEvents:true
				});		
		}else{
		
				var radio =  new Ext.form.Checkbox({
								name : nameradio,					 
							boxLabel : a_nv[i],	
							enableKeyEvents:true
							});				
		
		
		}					
		}
		
		
		
		r_items.push(radio);
	i++;
	}

	//radiogroup
	if( o.objeto.getXType() == 'radiogroup'  ){
	var control = new  Ext.form.RadioGroup({				
		fieldLabel : o.Nombre,
   enableKeyEvents : true,
			  name : o.objeto.name,
			 items : r_items,
	   descripcion : o.objeto.descripcion,					   
		   validez : o.objeto.validez
	});
	}else{
	var control = new  Ext.form.CheckboxGroup({				
		fieldLabel : o.Nombre,
   enableKeyEvents : true,
	          name : o.objeto.name,
	   descripcion : o.objeto.descripcion,					   
		   validez : o.objeto.validez,
			 items : r_items
	});
	}
	
	i = 0;
	while( i < r_items.length){
	r_items[i].on('check',function(){																
						property[0].setSource(obj);										
	});
	i++;
	}
	
	//busco el index del control
	var index = 0;
	i = 0;
	
	preview[0].findBy( function(c){	
		if(c.id  = o.objeto.id){
			index = i;
		}
		i++;
	});
	
	
	//inserto el nuevo control
	preview[0].insert(index,control);	
	preview[0].doLayout(true);
	control.getEl().on('contextmenu',function(el){
				cm(el,preview[0],control);
	});

	var f = control.getEl().up('div.x-form-item');
								
						var obj = {						
						 Nombre  : f.dom.firstChild.firstChild.nodeValue.replace(/:/gi,'') ,
					 Descripcion : control.descripcion,					   
						 Validez : control.validez,
						  objeto : control,
						   Items : nv
						};
						
	property[0].setSource(obj);
	
	//elimino el control
	var el = o.objeto.el.up( '.x-form-item' );										
	preview[0].remove(o.objeto);
	if(el){
	el.remove();	
	}
}

});
 


";

$frm_update_categoria->setEnableKeyEvents(true);
$frm_update_categoria->attachListener( "render", new PhpExt_Listener( PhpExt_Javascript::functionDef( null, $form_render , array( "form" ) )) );			   		   	  


//PRIMERA COLUMNA
$firstColumn_resumen = new PhpExt_Panel();
$firstColumn_resumen->setBorder(false)
					->setLayout(new PhpExt_Layout_FormLayout());

//SEGUNDA COLUMNA
$secondColumn_resumen = new PhpExt_Panel();				   
$secondColumn_resumen->setBorder(false)
					 ->setLayout(new PhpExt_Layout_FormLayout());	
		 

$txt_nombre = new PhpExt_Form_TextField();
$txt_nombre->setFieldLabel("Nombre")		  
		   ->setName("txt_nombre")		
		   ->setValue($categorianombre)
		   ->setEnableKeyEvents(true)	   
	  	   ->setWidth(100);		 

		   
$firstColumn_resumen->addItem($txt_nombre, new PhpExt_Layout_AnchorLayoutData("95%"));



$txt_descripcion = new PhpExt_Form_TextArea ();
$txt_descripcion->setFieldLabel("Descripci&oacute;n")		  
			    ->setName("txt_descripcion")				
				->setHeight(40)
			    ->setWidth(100);		 

$firstColumn_resumen->addItem($txt_descripcion, new PhpExt_Layout_AnchorLayoutData("95%"));	


$secondColumn_resumen->addItem($chk_empleado);
$secondColumn_resumen->addItem($chk_cliente);	
$secondColumn_resumen->addItem($chk_proveedor);	

$top->addItem( $firstColumn_resumen, new PhpExt_Layout_ColumnLayoutData(0.5) );
$top->addItem( $secondColumn_resumen, new PhpExt_Layout_ColumnLayoutData(0.5) );	


//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $frm_update_categoria->getJavascript( false,"contenido" ) );
$obj_comunication->send(true);

?>