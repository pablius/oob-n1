<?php
#OOB/N1 Framework [2008 - Nutus] - PM 

// Código por JPCOSEANI
// SCRIPT QUE GENERA EL FORM DE INFORMACION ADICIONAL

if( !seguridad::isAllowed(seguridad_action::nameConstructor('update','user','seguridad')) )
{
	throw new OOB_exception("Acceso Denegado", "403", "Acceso Denegado. Consulte con su Administrador!", true);
}  

//LIBRERIAS 
PhpExt_Javascript::sendContentType();


global $ari;
$ari->popup = 1; // no mostrar el main_frame 



//CREACION DEL FORMULARIO
$frm_informacion_adicional = new PhpExt_Form_FormPanel();
$frm_informacion_adicional->setFrame(true)										 
						  ->setTitle("Informaci&oacute;n adicional");						  
$frm_informacion_adicional->getPlugins()->add(new PhpExtUx_App_FitToParent());

$tab_panel = new PhpExt_TabPanel();
$tab_panel->setHeight(400)
		  ->setActiveTab(0)
		  ->setEnableKeyEvents(true)
		  ->setenableTabScroll(true);	 

if( isset($_POST['tab']) ){
	$tab_panel->setActiveTab($_POST['tab']);
}		  
			  
if( $categorias = contactos_informacion_adicional_categoria::getFilteredList() ){

	foreach( $categorias as $categoria ){
	
	$id_categoria = $categoria->id();
	$frm_update_categoria = new PhpExt_Panel();
	$frm_update_categoria->setFrame(true);	
	$frm_update_categoria->setLayout( new PhpExt_Layout_BorderLayout());
	$frm_update_categoria->setTitle($categoria->get('nombre'));

	$top = new PhpExt_Panel();	
	$top->setLayout(new PhpExt_Layout_ColumnLayout())		
		->setHeight(100);
		
$top_render = "		

var id_categoria = '{$id_categoria}';

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
		return (c.title == 'Vista Previa')
	});
	
	var items = Array();
	preview[0].findBy(function(c){	
		var f = c.getEl().up('div.x-form-item');
		var prop = Array();
		var caption = '';
		caption = f.dom.firstChild.firstChild.nodeValue;
		caption = caption.replace(/:/gi,'');		
		
		prop.push( {
						label : caption,
				  descripcion : c.descripcion,
					requerido : c.requerido,
					  validez : c.validez					   
				    }
				  );
			
					
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
						
		var item = { 	   name : c.name,
				    	  xtype : c.getXType(),
					propiedades : prop,
					   subitems : subitems
					}
		items.push(item);
	});
	
	Ext.Ajax.request( { url : 'contactos/infoadicional/modificar_categoria_process',
						method : 'POST',
						params : {	
									  id  : id_categoria,
									items : Ext.encode(items),
						  		   nombre : txt_nombre[0].getValue(),
							  descripcion : txt_descripcion[0].getValue(),
							     empleado : chk_empleado[0].getValue(),
								  cliente : chk_cliente[0].getValue(),
								proveedor : chk_proveedor[0].getValue()
								 },
					   success : function( responseObject ){	
							var tabpanel = a.findParentBy(function(c){ return (c.xtype == 'tabpanel') });	

							addTab( 'Informaci&oacute;n adicional','/contactos/infoadicional/view',false, 'tab=' + tabpanel.items.indexOf(tabpanel.getActiveTab()));
							Ext.MessageBox.alert( 'Emporika', 'Los datos se han guardado correctamente');
							var respuesta = responseObject.responseText;													   																										
						}								
					});
	


}

var eliminar = function(){

	var a = panel.findParentBy(function(c){return true});
	var tabpanel = a.findParentBy(function(c){ return (c.xtype == 'tabpanel') });	
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
													tabpanel.remove(a);																									
												}								
											});				

				}					
			});
}

var button1 = panel.getTopToolbar().items.find( function(c){ return (c.text == 'Guardar') } );
button1.on( 'click', guardar );

var button2 = panel.getTopToolbar().items.find( function(c){ return (c.text == 'Eliminar Categoria') } );
button2.on( 'click', eliminar );

";

$top->setEnableKeyEvents(true);
$top->attachListener( "render", new PhpExt_Listener( PhpExt_Javascript::functionDef( null, $top_render , array( "panel" ) )) );			   		   	  	
		
	
		
	$tb = $top->getTopToolbar();
	$tb->addButton( "save", "Guardar","images/save.gif" );		
	$tb->addSeparator("sep1");
	$tb->addButton( "delete", "Eliminar Categoria","images/no_.gif" );	
	
	$chk_empleado =  PhpExt_Form_Checkbox::createCheckbox("chk_empleado" )->setBoxLabel("Empleados")
																		 ->setFieldLabel("Visible para");
	$chk_cliente =   PhpExt_Form_Checkbox::createCheckbox("chk_cliente")->setBoxLabel("Clientes")
																	  ->setLabelSeparator("");
	$chk_proveedor = PhpExt_Form_Checkbox::createCheckbox("chk_proveedor")->setBoxLabel("Proveedores")
																		  ->setLabelSeparator("");
	
	
			$filtros2 = false;
			$filtros2[] = array( "field"=>"categoria", "type"=>"list", "value"=>$categoria->id() );				  
			if( $relaciones = contactos_informacion_adicional_categoria_clases::getFilteredList( false, false, false, false, $filtros2 ) ){				
				foreach( $relaciones as $rel ){
					//empleado
					if($rel->get('clase')->id() == 4 ){
						$chk_empleado->setChecked(true);
					}
					//cliente
					if($rel->get('clase')->id() == 1 ){
						$chk_cliente->setChecked(true);
					}
					//proveedor
					if($rel->get('clase')->id() == 2 ){
						$chk_proveedor->setChecked(true);
					}
				}				
			}
	
	
	//MENU PARA LOS CONTROLES
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
				  
	//FIN DE MENU PARA LOS CONTROLES
				  
	$center = false;
	$center = new PhpExt_Panel();
	$center ->setBodyStyle("background:#ffffff;padding:5px;")
			->setTitle("Vista Previa")
			->setLayout(new PhpExt_Layout_FormLayout())			
			->setAutoScroll(true);
						
	
	$field = false;
	$filtros2 = false;
	$filtros2[] = array( "field"=>"categoria", "type"=>"list", "value"=>$categoria->id() );			
	if( $controles = contactos_informacion_adicional_control::getFilteredList( false, false, false, false, $filtros2 ) ){

		foreach( $controles as $control ){
		
			switch( $control->get('tipo')->get('nombre') ){
				case 'RadioGroup':			
				case 'CheckBoxGroup':
					if( $control->get('tipo')->get('nombre') == 'RadioGroup' ){	
						$field = new PhpExt_Form_RadioGroup();					
					}
					else
					{
						$field = new PhpExt_Form_CheckboxGroup();
					}
					$field->setName( $control->id() );											
					
					$filtros3 = false;
					$filtros3[] = array( "field"=>"control", "type"=>"list", "value"=>$control->id() );								
					if( $list_subitems = contactos_informacion_adicional_subcontrol::getFilteredList( false, false, false, false, $filtros3 ) ){					
						foreach( $list_subitems as $subitem ){
							
							if( $subitem->get('tipo') == 'radio' ){
								$opt = new PhpExt_Form_Radio();
								$opt->setName( "radio_" . $control->id() )
									->setDescripcion( $subitem->id() );
							}else{
								$opt = new PhpExt_Form_Checkbox();
								$opt->setName( "check_" . $control->id() )
									->setDescripcion( $subitem->id() );
							}	
								
			$filtros4 = false;
			$filtros4[] = array( "field"=>"subcontrol", "type"=>"list", "value"=>$subitem->id() );				  				
			
			if( $propiedades = contactos_informacion_adicional_subcontrol_propiedad::getFilteredList( false, false, false, false, $filtros4 ) ){	
				foreach( $propiedades as $propiedad ){
					switch( $propiedad->get('nombre') ){
					case 'label':
						$opt->setBoxLabel($propiedad->get('value'));						
					break;
					}
				}								
			}					
								
							$field->addItem($opt);
						}					
					}
				break;				
				case 'DateField':
					$field = PhpExt_Form_DateField::createDateField( $control->id(), 'titulo' );
				break;
				case 'TextField':
					$field = PhpExt_Form_TextField::createTextField( $control->id(),'titulo' );
				break;
				case 'NumberField':
					$field = PhpExt_Form_NumberField::createNumberField( $control->id(),'titulo' );					
				break;		
				case 'TextArea':
					$field = PhpExt_Form_TextArea::createTextArea( $control->id(),'titulo' );
				break;		
			}//end switch
		
		
			//tengo que iniciar si o si las propiedades si no dsp no anda
			$field->setDescripcion("");			
			$field->setFocusCssClass("x-form-focus2");
			$field->setRequerido(false);
			$field->setValidez("sin validez");
		
			$filtros3 = false;
			$filtros3[] = array( "field"=>"control", "type"=>"list", "value"=>$control->id() );				  	
			
			//seteo las propiedades del control
			if( $propiedades = contactos_informacion_adicional_control_propiedad::getFilteredList( false, false, false, false, $filtros3 ) ){
				foreach( $propiedades as $propiedad ){
					switch( $propiedad->get('nombre') ){
					case 'label':
						$field->setFieldLabel($propiedad->get('value'));						
					break;
					case 'descripcion':
						$texto = $propiedad->get('value');						
						$texto = str_replace("\n"," ",$texto);
						$field->setDescripcion($texto);
					break;
					case 'requerido':
						$field->setRequerido($propiedad->get('value'));
					break;
					case 'validez':
						$field->setValidez($propiedad->get('value'));						
					break;					
					}
				
				}	
			}	
			
						
			$center->addItem($field);	
		
		}//end each
		
	}//end if controles
	
	
	
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

var controles = form.findBy(function(c){
return (c.xtype == 'treepanel')
});

var preview = form.findBy(function(c){
	return (c.title == 'Vista Previa')
});
 
controles[0].on( 'click', function(n){

var n = n;	
var promptmsg = function(){	
	Ext.Msg.prompt('Emporika', 'Por favor ingrese un nombre:', function(btn, text){
		if( btn == 'ok' ){
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
			}
			
		}
	});		
	
}

promptmsg();	
	
});

var cm = function(e,f,c) {

this.contextMenu = new Ext.menu.Menu({
id: 'gridCtxMenu',
items: [{
text: 'Remover',
icon: 'images/no_.gif',
 listeners: {'click': function(t){ 					
					var i= c.el.up( '.x-form-item' );					
					f.remove(c);
					if( i != null){
					i.remove();
					}
					f.doLayout(true);                    
                    }
                  }
}]
});

var xy = e.getXY();
e.stopEvent();
this.contextMenu.showAt(xy);
}

var property = form.findBy(function(c){
	return (c.title == 'Propiedades')
});

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
	
	if( ( a_nv.length > 1 && o.objeto.getXType() == 'radiogroup') || ( a_nv.length > 0 && o.objeto.getXType() == 'checkboxgroup' && Ext.util.Format.trim(nv) != '' ) ){
		
	
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
	   descripcion : o.objeto.descripcion,
   enableKeyEvents : true,
			  name : o.objeto.name,
			 items : r_items,	  					   
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
						Opciones : nv
						};
						
	property[0].setSource(obj);
	
	//elimino el control
	var el = o.objeto.el.up( '.x-form-item' );										
	preview[0].remove(o.objeto);
	if(el){
	el.remove();	
	}
	
	}else{
	var f = o.objeto.getEl().up('div.x-form-item');
			var obj = {						
						 Nombre  : f.dom.firstChild.firstChild.nodeValue.replace(/:/gi,'') ,
					 Descripcion : o.objeto.descripcion,					   
						 Validez : o.objeto.validez,
						  objeto : o.objeto,
						Opciones : ov
						};	
		
		 property[0].setSource(obj);
	}
}

});


 
//cargo los eventos a los controles que acabo de crear 
preview[0].findBy( function(c){	

		c.on('render',function(e){	
			
		if( c.getXType() != 'radiogroup' && c.getXType() != 'checkboxgroup' ){
		
				e.getEl().on('contextmenu',function(el){
					cm(el,preview[0],c);
				});
				
				var f = c.getEl().up('div.x-form-item');
						
				var obj = {				
				 Nombre  : f.dom.firstChild.firstChild.nodeValue.replace(/:/gi,'') ,
			 Descripcion : c.descripcion,
			   Requerido : (c.requerido == 1),
				 Validez : c.validez,
				  objeto : c
				};	
		var proxy;
				c.on('focus',function(){				
				property[0].setSource(obj);
				});
			
		}else{
				
				if(c.items){
				var items = c.items.items;	
		
				var i = 0;
				var labels = Array();
				while( i < items.length ){									
					labels.push(items[i].boxLabel);
					i++;
				}	
				
				var f = c.getEl().up('div.x-form-item');
				var obj = {						
						 Nombre  : f.dom.firstChild.firstChild.nodeValue.replace(/:/gi,'') ,
					 Descripcion : c.descripcion,					   
						 Validez : c.validez,
						  objeto : c,
						Opciones : labels.join('\\n')
				};
		
				i = 0;						
				while( i < items.length ){
					
					
					items[i].on('check',function(){
						property[0].setSource(obj);										
					});
			
				i++;
				}
				
				c.getEl().on('contextmenu',function(el){
							cm(el,preview[0],c);
				});
			}	
		}	
		
		});
		
		
		
 });

 var txt_nombre = form.findBy(function(c){
	 return (c.name == 'txt_nombre')
 });

txt_nombre[0].on('keyup',function(t){
 form.setTitle(t.getValue());
 });
 
var agregar = function(control_name,propname){

var dif = false;
switch(control_name)
{
case 'RadioGroup':
	var nameradio = 'name'+ Ext.id();
	var radio =  new Ext.form.Radio({
			name : nameradio,
	  focusClass : 'x-form-focus2',
		boxLabel : 'Item 1',	
		enableKeyEvents:true
	});
	
	radio.on('check',function(){																
		property[0].setSource(obj);										
	});
	
	var radio2 =  new Ext.form.Radio({
			name : nameradio,
	  focusClass : 'x-form-focus2',
		boxLabel : 'Item 2',	
		enableKeyEvents:true
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
		focusClass : 'x-form-focus2',
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
   focusClass : 'x-form-focus2'
	});	
break;
case 'DateField':
	 var control = new Ext.form.DateField({				
		fieldLabel: propname,
		focusClass : 'x-form-focus2'		
	 });
break;
case 'TextField':	
	var control	= new Ext.form.TextField({				
	   fieldLabel : propname,
	   	    value : 'Abcd...',
	   focusClass : 'x-form-focus2'
	});	
break;
case 'NumberField':

	var control = new Ext.form.NumberField({				
		fieldLabel: propname,
			 value: '123...',
		focusClass : 'x-form-focus2'				 
	});

break;
case 'TimeField':
	var control = new Ext.form.TimeField({				
		fieldLabel: propname,
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

";

$frm_update_categoria->setEnableKeyEvents(true);
$frm_update_categoria->attachListener( "render", new PhpExt_Listener( PhpExt_Javascript::functionDef( null, $form_render , array( "form" ) )) );			   		   	  

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
		   ->setValue($categoria->get('nombre'))
		   ->setEnableKeyEvents(true)	   
	  	   ->setWidth(100);		 

		   
$firstColumn_resumen->addItem($txt_nombre, new PhpExt_Layout_AnchorLayoutData("95%"));



$txt_descripcion = new PhpExt_Form_TextArea ();
$txt_descripcion->setFieldLabel("Descripci&oacute;n")		  
			    ->setName("txt_descripcion")
				->setValue($categoria->get('descripcion'))				
				->setHeight(40)
			    ->setWidth(100);		 

$firstColumn_resumen->addItem($txt_descripcion, new PhpExt_Layout_AnchorLayoutData("95%"));	

	
$secondColumn_resumen->addItem($chk_empleado);
$secondColumn_resumen->addItem($chk_cliente);	
$secondColumn_resumen->addItem($chk_proveedor);		

$top->addItem( $firstColumn_resumen, new PhpExt_Layout_ColumnLayoutData(0.5) );
$top->addItem( $secondColumn_resumen, new PhpExt_Layout_ColumnLayoutData(0.5) );	

$tab_panel->addItem($frm_update_categoria);

}

}		  

$tb = $frm_informacion_adicional->getTopToolbar();
$tb->addButton( "add", "Nueva Categoria","images/add.png" );						  

$tab_render = "

var tab_panel = form.findBy(function(c){return (c.xtype == 'tabpanel')});

var nueva = function(nombre){

var cnx = new Ext.data.Connection(); //creo un nuevo objeto conexion
		
	//obtengo un id unico para el contenedor de los contenidos que voy a cargar
	var id = Ext.id();

			Ext.Ajax.request( { url : 'contactos/infoadicional/new_categoria',
						method : 'POST',		
						params : { tabpanelid :tab_panel[0].id, nombre : nombre },
					   success : function( responseObject ){												
						var respuesta = responseObject.responseText;	

									
									
										   var tab = new Ext.Panel({														
													 title : nombre,
													layout : 'fit',					    
												  closable : false,
											deferredRender : false,
													  html : '<div style=\"height:100%;width:100%;\" id=\"' + id + '\"></div>',
												autoScroll : true							 							
										   });
										   
										   tab_panel[0].add(tab);
										   tab.show();													   											
										
										eval(respuesta);				
										contenido.render(Ext.get(id));									
										
																				
						}		
									
						
					});
					
					

}

var button1 = form.getTopToolbar().items.find( function(c){ return ( c.text == 'Nueva Categoria') } );
button1.on( 'click',function(){

var promptmsg = function(){	
	Ext.Msg.prompt('Emporika', 'Por favor ingrese un nombre:', function(btn, text){
		if (btn == 'ok' ){
			if( text != '' ){
				nueva(text);
			}else{
				Ext.Msg.alert( 'Emporika', 'Debe ingresar un nombre',function(){
					promptmsg();
				});
			}
			
		}
	});		
	
}

promptmsg();	



} );

";


$frm_informacion_adicional->setEnableKeyEvents(true);
$frm_informacion_adicional->attachListener( "render", new PhpExt_Listener( PhpExt_Javascript::functionDef( null, $tab_render , array( "form" ) )) );			   		   	 


$frm_informacion_adicional->addItem( $tab_panel );			 

//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $frm_informacion_adicional->getJavascript( false, "contenido" ) );
$obj_comunication->send(true);

file_put_contents("horoohoehoer.txt",$obj_comunication->send(false) );

?>