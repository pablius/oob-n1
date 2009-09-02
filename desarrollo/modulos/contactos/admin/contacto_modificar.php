<?php

global $ari;
$ari->popup = 1; // no mostrar el main_frame 


include_once 'PhpExtUx/Multiselect/multiselect.php';
include_once 'PhpExtUx/Multiselect/multiselectfield.php';
include_once 'PhpExtUx/Form/InputTextMask.php';

$field_width = 175;
$id_contacto = '';

	if( isset($_POST['id']) ){
		$contacto = new contactos_contacto(  $_POST['id'] );
		$id_contacto = $_POST['id'];	
	}
	else
	{
		throw new OOB_Exception_400("La variable [id] no esta definida");
	}//end if

$grid_id = '';

	if( isset( $_POST['gid'] ) )
	{
		$grid_id = $_POST['gid'];
	}

PhpExt_Javascript::sendContentType();
//DATA-READER PARA LEER LOS RESULTADOS DEVUELTOS
$error_reader = new PhpExt_Data_JsonReader();
$error_reader->setRoot("errors");
$error_reader->setSuccessProperty("success");
$error_reader->addField(new PhpExt_Data_FieldConfigObject("id"));
$error_reader->addField(new PhpExt_Data_FieldConfigObject("msg")); 

//FORMULARIO
$frm_mofidicar = new PhpExt_Form_FormPanel();
$frm_mofidicar->setFrame(true)
			  ->setErrorReader($error_reader)
			  ->setWidth(700)			 
			  ->setBaseParams( array("id"=>$id_contacto) )
			  ->setUrl( "/contactos/contacto/modificar_process" )			  
			  ->setTitle( "Datos del Contacto" )			  
			  ->setMethod( PhpExt_Form_FormPanel::METHOD_POST );
			  
$form_render = "	


var tabpanel = form.findBy(function(c){ return (c.xtype == 'tabpanel'); });

tabpanel[0].on( 'render', function(){

var tab = tabpanel[0].findBy(function(c){ return (c.title == 'Informaci&oacute;n adicional') });

var clase = '" . $contacto->get('clase')->id() . "' ;
var paneles = tab[0].findBy(function(c){ return (c.xtype == 'panel') });

var entronunca = true;

		var i = 0;
		while( i < paneles.length ){
			entro = false;
			
			var info = paneles[i].otherparam;	
			var x = 0;
			while( x < info.length ){
				if( clase == 3 ){
					
					if( info[x] == 1 || info[x] == 4 ){
						entro = true;
					}
				
				}
				else
				{			
					if( info[x] == clase ){
						entro = true;
					}
				}	
			x++;
			}
			
			if(!entro){
				tab[0].hideTabStripItem(paneles[i]);		
			}else{
				entronunca = false;	
				tab[0].unhideTabStripItem(paneles[i]);
				tab[0].setActiveTab(paneles[i]);
			}
			
		i++;
	}
	
	
	
	if(entronunca){					
		tabpanel[0].hideTabStripItem(tab[0]);
	}
	
});
	

";

$frm_mofidicar->attachListener( "beforerender", new PhpExt_Listener( PhpExt_Javascript::functionDef( null, $form_render , array( "form" ) )) );			   		   	 				  			   
$frm_mofidicar->setEnableKeyEvents(true);			  
			 
$tab_panel = new PhpExt_TabPanel();
$tab_panel->setPlain(true)			
          ->setActiveTab(0)
          ->setHeight(360)	
		  ->setWidth(680);

//-------------------------------------------DATOS GENERALES--------------------------------------------------------------
$tab_gral = new PhpExt_Panel();
$tab_gral->setTitle("Datos Generales")						
		 ->setBodyStyle("padding:5px")
		 ->setLayout(new PhpExt_Layout_ColumnLayout());

//PRIMERA COLUMNA
$firstColumn_resumen = new PhpExt_Panel();
$firstColumn_resumen->setBorder(false)
					->setLayout(new PhpExt_Layout_FormLayout());

//SEGUNDA COLUMNA
$secondColumn_resumen = new PhpExt_Panel();				   
$secondColumn_resumen->setBorder(false)
					 ->setLayout(new PhpExt_Layout_FormLayout());	

$fieldset_datos = new PhpExt_Form_FieldSet();	
$fieldset_datos->setHeight(330)			  
			   ->setTitle('Datos');	
		 
//TIPO DE PERSONA
$opt_tipo_fisica = new PhpExt_Form_Radio();
$opt_tipo_fisica->setBoxLabel("Fisica")
				->setValue("pf")
				->setName("opttipo");
			  

$opt_tipo_juridica = new PhpExt_Form_Radio();
$opt_tipo_juridica->setBoxLabel("Juridica")
				  ->setValue("pj")
				  ->setName("opttipo");
				  
	if( $contacto->get('tipo')->id() == 1 ){
		$opt_tipo_juridica->setChecked(true);
		$opt_tipo_fisica->setChecked(false);
	}
	else
	{
		$opt_tipo_juridica->setChecked(false);
		$opt_tipo_fisica->setChecked(true);
	}

$opt_group_tipo_persona = new PhpExt_Form_RadioGroup();
$opt_group_tipo_persona->setfieldLabel("Tipo de contacto");
$opt_group_tipo_persona->setName("opttipo");
$opt_group_tipo_persona->addItem($opt_tipo_fisica);
$opt_group_tipo_persona->addItem($opt_tipo_juridica);	
$opt_group_tipo_persona->setWidth($field_width);

$group_render = "

var formulario = this.findParentByType('form');
opt = this.items.items[0];	
	  
opt.on( 'check' , function(t,n,o){	
	var field = formulario.getForm().findField('txt_razonsocial');	
	var valor =  (t.getValue())?'Apellido:':'Razon Social:';
	var r = field.getEl().up('div.x-form-item');
	r.dom.firstChild.firstChild.nodeValue = String.format('{0}', valor);
});

";

$opt_group_tipo_persona->attachListener( "render", new PhpExt_Listener( PhpExt_Javascript::functionDef( null, $group_render , array( "group" ) )) );			   		   	 				  			   
$opt_tipo_fisica->setEnableKeyEvents(true);


$fieldset_datos->addItem($opt_group_tipo_persona);		

//NOMBRE
$txt_nombre =  PhpExt_Form_TextField::createTextField("txt_nombre","Nombre")
			   ->setMsgTarget(PhpExt_Form_FormPanel::MSG_TARGET_SIDE)
			   ->setValue( $contacto->get('nombre') )			   
			   ->setWidth($field_width);	

$fieldset_datos->addItem($txt_nombre);					   

//RAZON SOCIAL O APELLIDO
$txt_razonsocial =  PhpExt_Form_TextField::createTextField("txt_razonsocial","Apellido")
					->setMsgTarget(PhpExt_Form_FormPanel::MSG_TARGET_SIDE)
					->setValue( $contacto->get('apellido') )					
					->setWidth($field_width);	

//si es juridica					
if( $contacto->get('tipo')->id() == 1 ){
	$txt_razonsocial->setFieldLabel('Razon Social');
}

$fieldset_datos->addItem($txt_razonsocial);						
			//chunk_split( $contacto->get('cuit'),1 )
	
	$value = "";
	if( $contacto->get('cuit') != '___________' ){
		$value = vsprintf( '%d%d-%d%d%d%d%d%d%d%d-%d', str_split( $contacto->get('cuit'),1 ) );			
	}
$txt_cuit = new PhpExt_Form_TextField();
$txt_cuit->setName("txt_cuit")
		 ->setFieldLabel("CUIT/L")					
		 ->setWidth($field_width)			
		 ->setValue( $value )
		 ->setMsgTarget(PhpExt_Form_FormPanel::MSG_TARGET_SIDE);
$txt_cuit->getPlugins()->add(new PhpExtUx_InputTextMask('99-99999999-9',false) );		

$cuit_change = "

	var form = txt.findParentByType('form');
	var txt_usuario = form.getForm().findField('txt_usuario');
	
	var radio = form.findBy(function(c){ return ( c.xtype == 'radio' ) });		
	var u = (radio[0].getValue())?'existente':'nuevo';
	
	var usuario_value = txt_usuario.getValue();
	var usuario_value_noguion = usuario_value.replace(/_/gi, '');
	usuario_value_noguion = usuario_value_noguion.replace(/-/gi, '');
	var cuit_noguion = txt.getValue().replace(/_/gi, '');
	cuit_noguion = cuit_noguion.replace(/-/gi, '');
		
	if( u == 'nuevo' ){
			
		var comparar = '';
		if( cuit_noguion.length > usuario_value_noguion.length ){
			comparar = cuit_noguion.substring(0,usuario_value_noguion.length);
			if( comparar == usuario_value_noguion ){
				txt_usuario.setValue(txt.getValue());
			}	
		}
		else
		{
			
			comparar = usuario_value_noguion.substring(0,cuit_noguion.length);				
			if( comparar == cuit_noguion ){
				txt_usuario.setValue(txt.getValue());
			}	
		}//end if
	
	}//end if
	
	

";

	
$txt_cuit->attachListener( "keyup", new PhpExt_Listener( PhpExt_Javascript::functionDef( null, $cuit_change , array( "txt" ) )) );			   		   	 				  			   
$txt_cuit->setEnableKeyEvents(true);	 
			   
$fieldset_datos->addItem($txt_cuit);									   
			   
//ING BRUTOS
$txt_ingbrutos =  PhpExt_Form_NumberField::createNumberField( "txt_ingbrutos", "Ing. Brutos" )
				  ->setMsgTarget(PhpExt_Form_FormPanel::MSG_TARGET_SIDE)					  
				  ->setValue( $contacto->get('ingbrutos') )	
			      ->setWidth($field_width);								   

$fieldset_datos->addItem($txt_ingbrutos);						

//NRO CLIENTE
$txt_nrocliente =  PhpExt_Form_TextField::createTextField( "txt_nrocliente", "Nro. Cliente" )
				  ->setMsgTarget(PhpExt_Form_FormPanel::MSG_TARGET_SIDE)				  
				  ->setValue( $contacto->get('numerocliente') )			
			      ->setWidth($field_width);								   

$fieldset_datos->addItem($txt_nrocliente);	

//RUBRO

//ARMO UN ARRAY CON LOS RUBROS
$rubros = array();	
$rubro_selected = '';
$i = 0;

if( $lista_rubros = contactos_rubro::getFilteredList() ){		
			foreach ( $lista_rubros as $rubro  ){
				if( $rubro->get('id') == $contacto->get('rubro')->id() ){
					$rubro_selected = $rubro->get('id');
				}
					$rubros[] = array( $rubro->get('id') , $rubro->get('detalle') );
					$i++;
			}
	}				  

//STORE PARA EL ARRAY DE CLASES	
$store_rubros = new PhpExt_Data_SimpleStore();
$store_rubros->addField("id");
$store_rubros->addField("name");			  
$store_rubros->setData(PhpExt_Javascript::variable( json_encode($rubros) ) );	  


$cbo_rubro = PhpExt_Form_ComboBox::createComboBox("cbo_rubro",null,null,"cbo_rubro_value")						   
			->setStore( $store_rubros )
			->setFieldLabel("Rubro")
			->setDisplayField("name")			   
			->setValueField("id")		
			->setEditable(false)				
			->setValue( $rubro_selected )
			->setForceSelection(true)			
			->setSingleSelect(true)	
			->setWidth( $field_width )
			->setMode(PhpExt_Form_ComboBox::MODE_LOCAL)			   
			->setTriggerAction(PhpExt_Form_ComboBox::TRIGGER_ACTION_ALL);
			
$fieldset_datos->addItem($cbo_rubro);			
	

//CLASE DE CONTACTO

//ARMO UN ARRAY CON LAS CLASES
$clases = array();	
$clase_selected = '';
$i = 0;

if( $lista_clases = contactos_clase::getFilteredList() ){		
			foreach ( $lista_clases as $clase  ){
				if( $clase->id() == $contacto->get('clase')->id() ){
					$clase_selected = $clase->get('id');
				}
					$clases[] = array( $clase->get('id') , $clase->get('detalle') );
					$i++;
			}
	}				  

//STORE PARA EL ARRAY DE CLASES	
$store_clases = new PhpExt_Data_SimpleStore();
$store_clases->addField("id");
$store_clases->addField("name");			  
$store_clases->setData(PhpExt_Javascript::variable( json_encode($clases) ) );	  


$cbo_clase = PhpExt_Form_ComboBox::createComboBox("cbo_clase",null,null,"cbo_clase_value")						   
			->setStore( $store_clases )
			->setFieldLabel("Clase")
			->setDisplayField("name")			   
			->setValueField("id")		
			->setEditable(false)				
			->setValue( $clase_selected )
			->setForceSelection(true)			
			->setSingleSelect(true)	
			->setWidth( $field_width )
			->setMode(PhpExt_Form_ComboBox::MODE_LOCAL)			   
			->setTriggerAction(PhpExt_Form_ComboBox::TRIGGER_ACTION_ALL);

$combo_change = "
var form = cbo.findParentByType('form');

var tabpanel = cbo.findParentBy(function(c){ return (c.xtype == 'tabpanel')});

var tab = tabpanel.findBy(function(c){ return (c.title == 'Informaci&oacute;n adicional') });

var paneles = tab[0].findBy(function(c){ return (c.xtype == 'panel') });

var i = 0;
var entro;
var entronunca;
var entronunca =true;
	
while( i < paneles.length ){
	entro = false;
	
	var info = paneles[i].otherparam;	
	var x = 0;
	while( x < info.length ){
	
				if( cbo.getValue() == 3 ){
					
					if( info[x] == 1 || info[x] == 4 ){
						entro = true;
					}
				
				}
				else
				{			
					if( info[x] == cbo.getValue() ){
						entro = true;
					}
				}//end if		

	x++;
	}
	
	if(!entro){
		tab[0].hideTabStripItem(paneles[i]);		
	}else{
		entronunca = false;	
		tab[0].unhideTabStripItem(paneles[i]);
		tab[0].setActiveTab(paneles[i]);
	}
	
i++;
}

	if(entronunca){
		tabpanel.hideTabStripItem(tab[0]);
	}
	else
	{
		tabpanel.unhideTabStripItem(tab[0]);
	}



var ms = form.getForm().findField('areas');
if( cbo.getValue() == 4 ){
		ms.setDisabled(false);
}else{
	ms.setDisabled(true);	
}
";
			
$cbo_clase->attachListener( "select", new PhpExt_Listener( PhpExt_Javascript::functionDef( null, $combo_change , array( "cbo" ) )) );			   		   	 				  			   
$cbo_clase->setEnableKeyEvents(true);			
			
$fieldset_datos->addItem($cbo_clase);			

//CATEGORIA

//ARMO UN ARRAY CON LAS CLASES
$categorias = array();	
$categoria_selected = '';
$i = 0;

if( $lista_categorias = impuestos_categorizacion::getFilteredList() ){		
			foreach ( $lista_categorias as $categoria  ){
				if( $categoria->get('id') == $contacto->get('categoria')->id() ){
					$categoria_selected = $categoria->get('id');
				}
					$categorias[] = array( $categoria->get('id') , $categoria->get('nombre') );
					$i++;
			}
	}				  

//STORE PARA EL ARRAY DE CATEGORIAS	
$store_categorias = new PhpExt_Data_SimpleStore();
$store_categorias->addField("id");
$store_categorias->addField("name");			  
$store_categorias->setData(PhpExt_Javascript::variable( json_encode($categorias) ) );	  


$cbo_categoria = PhpExt_Form_ComboBox::createComboBox("cbo_categoria",null,null,"cbo_categoria_value")						   
				->setStore( $store_categorias )
				->setFieldLabel("Categoria")
				->setDisplayField("name")			   
				->setValueField("id")					
				->setEditable(false)	
				->setValue( $categoria_selected )
				->setForceSelection(true)			
				->setSingleSelect(true)	
				->setWidth( $field_width )
				->setMode(PhpExt_Form_ComboBox::MODE_LOCAL)			   
				->setTriggerAction(PhpExt_Form_ComboBox::TRIGGER_ACTION_ALL);
			
$fieldset_datos->addItem($cbo_categoria);

//ARMO UN ARRAY CON LOS AREAS
$areas = array();	
$filtros[] = array("field"=>"status","type"=>"integer","value"=>"1");
if( $lista_areas = contactos_areas::getFilteredList(false,false,false,false,$filtros) ){		
			foreach ( $lista_areas as $area  ){				
					$areas[] = array( $area->id() , $area->get('nombre') );					
			}
	}				  

//veo los que estan seleccionados
$filtros = false;
$areas_selected = array();
$filtros[] = array( "field"=>"contacto", "type"=>"list", "value"=>$contacto->id() );								
if( $list_rel = contactos_contacto_area::getFilteredList( false, false, false, false, $filtros ) ){
	foreach( $list_rel as $rel ){
		$areas_selected[] = $rel->get('area')->id();		
	}//end each
										
}//end if	
	
//STORE PARA EL ARRAY DE CLASES	
$store_areas = new PhpExt_Data_SimpleStore();
$store_areas->addField("value");
$store_areas->addField("name");			  
$store_areas->setData(PhpExt_Javascript::variable( json_encode($areas) ) );	  
 
$cbo_area = new PhpExtUx_MultiSelectfield();						   
$cbo_area->setStore($store_areas)
		 ->setdisplayField("name")
		 ->setvalueField("value")
		 ->setWidth($field_width)	
		 ->setValue( implode( ";" , $areas_selected ) )		 
		 ->setName("areas")
		 ->setFieldLabel("Area")
		 ->setdataFields(array("id","name"));	
		 					   
if( $contacto->get('clase')->id() != '4' ){
	$cbo_area->setDisabled(true);
}		
			
$fieldset_datos->addItem($cbo_area);	

//ING BRUTOS
$txt_diaspago =  PhpExt_Form_NumberField::createNumberField( "txt_diaspago", "D&iacute;as Pago" )
				  ->setMsgTarget(PhpExt_Form_FormPanel::MSG_TARGET_SIDE)		
				  ->setValue( $contacto->get('dias_pago') )				  
			      ->setWidth($field_width);								   

$fieldset_datos->addItem($txt_diaspago);	

$firstColumn_resumen->addItem($fieldset_datos, new PhpExt_Layout_AnchorLayoutData("95%"));


$fieldset_usuarios = new PhpExt_Form_FieldSet();	
$fieldset_usuarios->setHeight(330)					  
			      ->setTitle('Usuarios');	
				  
$opt_existente = new PhpExt_Form_Radio();
$opt_existente->setFieldLabel("Usuario Existente")
	  	      ->setValue("existente")
		      ->setName("opt-usuario");

$opt_check = "

	var formulario = this.findParentByType('form');
	var field = formulario.getForm().findField('cbo_usuario_value');	
	field.setDisabled(!t.getValue());	

";			 
			 
$opt_existente->attachListener( "check", new PhpExt_Listener( PhpExt_Javascript::functionDef( null, $opt_check , array( "t" ) )) );			   		   	 				  			   
$opt_existente->setEnableKeyEvents(true);		

$opt_existente->setChecked(true);		 
			  
$fieldset_usuarios->addItem($opt_existente);	


$store_usuarios = new PhpExt_Data_JsonStore();
$store_usuarios->setUrl("/contactos/contacto/get_usuarios")	  
			   ->setRoot("topics")
			   ->setBaseParams( array("id"=>$id_contacto) )
			   ->setTotalProperty("totalCount")
			   ->setAutoLoad(true)
			   ->setId("id");


$store_usuarios->addField(new PhpExt_Data_FieldConfigObject("id"));
$store_usuarios->addField(new PhpExt_Data_FieldConfigObject("name"));			  
	

//MONEDAS PARA LA GRILLA DE IMPUESTOS
$cbo_usuarios = PhpExt_Form_ComboBox::createComboBox( "cbo_usuario", null ,null , "cbo_usuario_value" )
			    ->setStore($store_usuarios)
			    ->setFieldLabel("Usuario")
			    ->setWidth($field_width)
			    ->setValueField("id")
				->setValue( $contacto->get('usuario')->name() )
				->setPageSize( PAGE_SIZE )
			    ->setDisplayField("name")				   			   			    
			    ->setQueryDelay(300)
			    ->setMinChars(1)
			    ->setListWidth(220)
			    ->setSingleSelect(true)				
			    ->setMode( PhpExt_Form_ComboBox::MODE_REMOTE )
			    ->setTriggerAction(PhpExt_Form_ComboBox::TRIGGER_ACTION_ALL);

$fieldset_usuarios->addItem($cbo_usuarios);					  
				  
$opt_nuevo = new PhpExt_Form_Radio();
$opt_nuevo->setFieldLabel("Nuevo Usuario")
				   ->setValue("nuevo")
				   ->setName("opt-usuario");
			  
$fieldset_usuarios->addItem($opt_nuevo);				  
		
//usuario
$txt_usuario  = PhpExt_Form_TextField::createTextField( "txt_usuario", "Usuario" )
			    ->setWidth( $field_width )								
			    ->setMsgTarget(PhpExt_Form_FormPanel::MSG_TARGET_SIDE);		

$fieldset_usuarios->addItem($txt_usuario);	

//password			  
$txt_password = PhpExt_Form_PasswordField::createPasswordField("txt_pass","Contrase&ntilde;a")
			     ->setMsgTarget(PhpExt_Form_FormPanel::MSG_TARGET_SIDE)
			     ->setWidth($field_width);
				 
$fieldset_usuarios->addItem($txt_password);				 
				 
//repetir password				
$txt_repetir = PhpExt_Form_PasswordField::createPasswordField("txt_repetir","Repetir")
			    ->setMsgTarget(PhpExt_Form_FormPanel::MSG_TARGET_SIDE)
			    ->setWidth($field_width);

$fieldset_usuarios->addItem($txt_repetir);	
				
			
//e-mail
$txt_email   =  PhpExt_Form_TextField::createTextField("txt_email","E-mail", null, PhpExt_Form_FormPanel::VTYPE_EMAIL)
			    ->setWidth($field_width)								
			    ->setMsgTarget(PhpExt_Form_FormPanel::MSG_TARGET_SIDE);		

$fieldset_usuarios->addItem($txt_email);						
				
//Paso los estado a json
$estados = array();
foreach( oob_user::getStatus() as $id=>$descripcion ){
	$estados[] = array( $id, $descripcion );
}

//Data_Store para llenar el combo con los datos
$store_estados = new PhpExt_Data_SimpleStore();
$store_estados->addField("id");
$store_estados->addField("descripcion");
$store_estados->setData(PhpExt_Javascript::variable(json_encode($estados)));


$cbo_estados = PhpExt_Form_ComboBox::createComboBox("cbo_estados","Estado")			
			   ->setWidth($field_width)
			   ->setStore($store_estados)			   
			   ->setDisplayField("descripcion")		
			   ->setValueField("id")			   		   
			   ->setEditable(false)	
			   ->setForceSelection(true)			
			   ->setSingleSelect(true)				
			   ->setMode(PhpExt_Form_ComboBox::MODE_LOCAL)
			   ->setTriggerAction(PhpExt_Form_ComboBox::TRIGGER_ACTION_ALL);				
			   
$fieldset_usuarios->addItem($cbo_estados);							  

$secondColumn_resumen->addItem($fieldset_usuarios, new PhpExt_Layout_AnchorLayoutData("95%"));	

$tab_gral->addItem( $firstColumn_resumen, new PhpExt_Layout_ColumnLayoutData(0.5) );
$tab_gral->addItem( $secondColumn_resumen, new PhpExt_Layout_ColumnLayoutData(0.5) );			   
			   
			   
$tab_panel->addItem($tab_gral);	

//-------------------------------------------DIRECCCIONES/TELEFONOS--------------------------------------------------------
$tab_dirtel = new PhpExt_Panel();
$tab_dirtel->setTitle("Direcciones/Tel&eacute;fonos")										
				->setAutoScroll(true)	
				->setLayout(new PhpExt_Layout_FormLayout());
				
				//direcciones
				
$store_direcciones = new PhpExt_Data_JsonStore();
$store_direcciones->setUrl("/contactos/contacto/get_direcciones")	  
				  ->setRoot("topics")
				  ->setBaseParams( array("id"=>$id_contacto) )	
				  ->setAutoLoad(true)
				  ->setTotalProperty("totalCount")
				  ->setId("id");				

		  
//CAMPOS DEL STORE	  
$store_direcciones->addField(new PhpExt_Data_FieldConfigObject("id"));
$store_direcciones->addField(new PhpExt_Data_FieldConfigObject("direccion"));
$store_direcciones->addField(new PhpExt_Data_FieldConfigObject("extra"));
$store_direcciones->addField(new PhpExt_Data_FieldConfigObject("cp"));
$store_direcciones->addField(new PhpExt_Data_FieldConfigObject("ciudad"));
$store_direcciones->addField(new PhpExt_Data_FieldConfigObject("ciudadname"));
$store_direcciones->addField(new PhpExt_Data_FieldConfigObject("id_ciudad"));
$store_direcciones->addField(new PhpExt_Data_FieldConfigObject("tipo"));
$store_direcciones->addField(new PhpExt_Data_FieldConfigObject("id_tipo"));

$txt_calle = PhpExt_Form_TextField::createTextField('txtcalle')->setAllowBlank(false);

$txtnumero = PhpExt_Form_NumberField::createNumberField("txtnumero")
			 ->setAllowBlank(false)
			 ->setAllowNegative(false)
			 ->setMaxValue(100000);

$txt_direccion = PhpExt_Form_TextField::createTextField('txtdireccion')->setAllowBlank(false);			 
$txt_cp = PhpExt_Form_TextField::createTextField('txtcp')->setAllowBlank(false);
$txt_extra = PhpExt_Form_TextField::createTextField('txtextra');


//store para las ciudades
$store_ciudades = new PhpExt_Data_JsonStore();
$store_ciudades->setUrl("/contactos/contacto/get_ciudades")	  
			   ->setRoot("topics")
			   ->setTotalProperty("totalCount")
			   ->setAutoLoad(true)
			   ->setId("id");


$store_ciudades->addField(new PhpExt_Data_FieldConfigObject("id"));
$store_ciudades->addField(new PhpExt_Data_FieldConfigObject("name"));			  
	

//MONEDAS PARA LA GRILLA DE IMPUESTOS
$cbo_ciudades = PhpExt_Form_ComboBox::createComboBox("cbo_ciudades")						   
			   ->setStore( $store_ciudades )
			   ->setWidth(280)
			   ->setValueField("id")
			   ->setDisplayField("name")				   
			   ->setLazyRender(true)	
			   ->setPageSize( ITEMS_COMBO_CIUDADES )
			   ->setQueryDelay(300)
			   ->setMinChars(1)
			   ->setListWidth(220)
			   ->setSingleSelect(true)				
			   ->setMode( PhpExt_Form_ComboBox::MODE_REMOTE )
			   ->setTriggerAction(PhpExt_Form_ComboBox::TRIGGER_ACTION_ALL);

	$tipos = array();
		
	if( $lista_tipos = contactos_direccion_tipo::getFilteredList() ){		
			foreach ( $lista_tipos as $tipo  ){
				$tipos[] = array( $tipo->id(), $tipo->get('detalle') );				
			}
	}

	//store para los tipos de direcciones
	$store_dirtipo = new PhpExt_Data_SimpleStore();
	$store_dirtipo->addField("id");
	$store_dirtipo->addField("detalle");
	$store_dirtipo->setData( PhpExt_Javascript::variable(json_encode($tipos)) );


$cbo_dirtipo = PhpExt_Form_ComboBox::createComboBox("cbo_dirtipo")						   
			   ->setStore($store_dirtipo)
			   ->setDisplayField("detalle")				   
			   ->setValueField("id")	
			   ->setLazyRender(true)
			   ->setEditable(false)	
			   ->setForceSelection(true)			
			   ->setSingleSelect(true)				
			   ->setMode(PhpExt_Form_ComboBox::MODE_LOCAL)
			   ->setTriggerAction(PhpExt_Form_ComboBox::TRIGGER_ACTION_ALL);		

$format_currency = "
function( v, params, record , rowIndex, colIndex, store ){			   		
		return record.get('ciudadname');
	 }
";			   

$format_tipo = "
	 function( v, params, record , rowIndex, colIndex, store ){			
		return record.data['tipo'];
	 }
";			   
 
//SE AGREGAN LAS COLUMNAS A LA GRILLA   
$col_model_direcciones = new PhpExt_Grid_ColumnModel();
$col_model_direcciones->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("Direcci&oacute;n","direccion")->setEditor($txt_direccion))	
					  ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("Datos extra","extra")->setEditor($txt_extra)) 				  
					  ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("Ciudad","ciudad",null,80,PhpExt_Ext::HALIGN_RIGHT,new PhpExt_JavascriptStm($format_currency))->setEditor($cbo_ciudades))
					  ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("C.P.","cp")->setEditor($txt_cp))	  
					  ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("Tipo","id_tipo",null,80,PhpExt_Ext::HALIGN_RIGHT,new PhpExt_JavascriptStm($format_tipo))->setEditor($cbo_dirtipo));

//GRILLA DIRECCIONES	  
$grid_direcciones = new PhpExt_Grid_EditorGridPanel();
$grid_direcciones->setHeight(160)	     
				 ->setTitle("Direcciones")	 
				 ->setSelectionModel(new PhpExt_Grid_RowSelectionModel())
				 ->setColumnModel( $col_model_direcciones )	 
				 ->setStore( $store_direcciones )	 
				 ->setEnableHeaderMenu(false)
				 ->setEnableColumnMove(false)
				 ->setBorder(true)	 
				 ->setFrame(true)
				 ->setLoadMask(true);	
				 
$grid_dir_render = "


grid.on('validateedit', function(e) {
   var dataIndex = e.field;
   switch(dataIndex){      
      case 'ciudad':	  
		
         var combo = grid.getColumnModel().getCellEditor(e.column, e.row).field;		
		 var id = (isNaN(combo.getRawValue()))?combo.getValue():combo.getRawValue();
		 var value = (isNaN(combo.getRawValue()))?combo.getRawValue():combo.getValue();
         e.record.set('ciudadname', value );
		 e.record.set('id_ciudad', id );
      break;
	  case 'id_tipo':	  
		var combo = grid.getColumnModel().getCellEditor(e.column, e.row).field;				 		 
        e.record.data['tipo'] = combo.getRawValue();		
	  break;
   }
});
					 
var store = grid.getStore();

var nueva = function(){

	var p = new Ext.data.Record({
			   id : '',
        direccion : '',
            extra : '',            
               cp : '',             
		   ciudad : '',
		id_ciudad : '',       
			 tipo : '',
		  id_tipo : ''
        });
	
	grid.stopEditing();
    store.insert(0, p);
    grid.startEditing(0, 0);
}

var borrar = function(){
	
	var m = grid.getSelections();

	if( m.length >= 1 ){

		Ext.MessageBox.confirm( 'Emporika', 'Desea eliminar los items selecionados?' , 
			function(btn){
				if(btn == 'yes'){
					for( var i = 0, len = m.length; i < len; i++ ){			
						store.remove( m[i] );				
					}
				}
			});		
	}
	else
	{
		Ext.MessageBox.alert('Emporika', 'Por favor seleccione un item');
	}

}

var clonar = function(){

	var m = grid.getSelections();

	if( m.length == 1 ){
			grid.stopEditing();
			store.insert(0, m[0]);
			grid.startEditing(0, 0);			
	}
	else
	{
		Ext.MessageBox.alert('Emporika', 'Por favor seleccione \"un\" item');
	}

}


var button1 = grid.getTopToolbar().items.find( function(c){ return ( c.text == 'Nueva') } );	
button1.on( 'click', nueva );

var button2 = grid.getTopToolbar().items.find( function(c){ return ( c.text == 'Borrar') } );	
button2.on( 'click', borrar );

var button3 = grid.getTopToolbar().items.find( function(c){ return ( c.text == 'Clonar') } );	
button3.on( 'click', clonar );

";

$grid_direcciones->setEnableKeyEvents(true);
$grid_direcciones->attachListener( "render", new PhpExt_Listener( PhpExt_Javascript::functionDef( null, $grid_dir_render , array( "grid" ) )) );			   		   	 			 
				 
$tb_direcciones = $grid_direcciones->getTopToolbar();
$tb_direcciones->addButton( "new", "Nueva","images/add.png" );	 
$tb_direcciones->addSeparator("sep1");
$tb_direcciones->addButton( "delete", "Borrar", "images/no_.gif" );
$tb_direcciones->addSeparator("sep2");
$tb_direcciones->addButton( "clone", "Clonar", "images/clone.gif" );				 

$tab_dirtel->addItem($grid_direcciones);

				//telefonos

//STORE 
$store_medioscontacto= new PhpExt_Data_JsonStore();

$store_medios_contacto = new PhpExt_Data_JsonStore();
$store_medios_contacto->setUrl("/contactos/contacto/get_medios")	  
					  ->setRoot("topics")
					  ->setAutoLoad(true)
					  ->setBaseParams( array("id"=>$id_contacto) )
					  ->setTotalProperty("totalCount")
					  ->setId("id");
	  
	  
$store_medios_contacto->addField(new PhpExt_Data_FieldConfigObject("id"));
$store_medios_contacto->addField(new PhpExt_Data_FieldConfigObject("direccion"));
$store_medios_contacto->addField(new PhpExt_Data_FieldConfigObject("prefix"));
$store_medios_contacto->addField(new PhpExt_Data_FieldConfigObject("tipo"));
$store_medios_contacto->addField(new PhpExt_Data_FieldConfigObject("id_tipo"));

$format_tipo = "
	 function( v, params, record , rowIndex, colIndex, store ){			
		return record.data['tipo'];
	 }
";	

$medios = array();
		
	if( $lista_medios = contactos_medios_contacto_tipo::getFilteredList() ){		
			foreach ( $lista_medios as $medio  ){
				$medios[] = array( $medio->id(), $medio->get('detalle') );				
			}
	}

	//store para los tipos de direcciones
	$store_medios = new PhpExt_Data_SimpleStore();
	$store_medios->addField("id");
	$store_medios->addField("detalle");
	$store_medios->setData( PhpExt_Javascript::variable(json_encode($medios)) );


$cbo_medios = PhpExt_Form_ComboBox::createComboBox("cbo_dirtipo")						   
			   ->setStore($store_medios)
			   ->setDisplayField("detalle")			   
			   ->setValueField("id")
			   ->setLazyRender(true)
			   ->setEditable(false)	
			   ->setForceSelection(true)			
			   ->setSingleSelect(true)				
			   ->setMode(PhpExt_Form_ComboBox::MODE_LOCAL)
			   ->setTriggerAction(PhpExt_Form_ComboBox::TRIGGER_ACTION_ALL);
  
$txt_dir = PhpExt_Form_TextField::createTextField('txtdir')->setAllowBlank(false);

 
//SE AGREGAN LAS COLUMNAS A LA GRILLA   
$col_model_medioscontacto = new PhpExt_Grid_ColumnModel();
$col_model_medioscontacto->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("Tipo","id_tipo",null,80,PhpExt_Ext::HALIGN_RIGHT,new PhpExt_JavascriptStm($format_tipo))->setEditor($cbo_medios))					 
						 ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("Direcci&oacute;n","direccion",null,180)->setEditor($txt_dir));
					
					

//GRILLA DIRECCIONES ONLINE
$grid_medioscontacto = new PhpExt_Grid_EditorGridPanel();
$grid_medioscontacto->setHeight(160)	     
				    ->setTitle("Medios de Contacto")	 
				    ->setSelectionModel(new PhpExt_Grid_RowSelectionModel())
				    ->setColumnModel( $col_model_medioscontacto )	 
				    ->setStore( $store_medios_contacto )	 
				    ->setEnableHeaderMenu(false)
				    ->setEnableColumnMove(false)
				    ->setBorder(true)	 
				    ->setFrame(true)
				    ->setLoadMask(true);
					

$grid_medioscontacto_render = "

var store = grid.getStore();

grid.on('validateedit', function(e){
    if(e.field == 'id_tipo'){
		var combo = grid.getColumnModel().getCellEditor(e.column, e.row).field;				 		 
        e.record.data['tipo'] = combo.getRawValue();
    }
});

var nuevo = function(){

	var p = new Ext.data.Record({
            direccion : '',
				 tipo : '',
			  id_tipo : '',
				   id : ''
        });
	
	grid.stopEditing();
    store.insert(0, p);
    grid.startEditing(0, 0);

}

var borrar = function(){

	var m = grid.getSelections();

	if( m.length >= 1 ){

		Ext.MessageBox.confirm( 'Emporika', 'Desea eliminar los items selecionados?' , 
			function(btn){
				if(btn == 'yes'){
					for( var i = 0, len = m.length; i < len; i++ ){			
						store.remove( m[i] );				
					}
				}
			});		
	}
	else
	{
		Ext.MessageBox.alert('Emporika', 'Por favor seleccione un item');
	}

}

var button1 = grid.getTopToolbar().items.find( function(c){ return ( c.text == 'Nuevo') } );	
button1.on( 'click', nuevo );

var button2 = grid.getTopToolbar().items.find( function(c){ return ( c.text == 'Borrar') } );	
button2.on( 'click', borrar );

";					

$grid_medioscontacto->setEnableKeyEvents(true);
$grid_medioscontacto->attachListener( "render", new PhpExt_Listener( PhpExt_Javascript::functionDef( null, $grid_medioscontacto_render , array( "grid" ) )) );			   		   	 			 					

$tb_medioscontacto = $grid_medioscontacto->getTopToolbar();
$tb_medioscontacto->addButton( "new", "Nuevo","images/add.png" );	 
$tb_medioscontacto->addSeparator("sep1");
$tb_medioscontacto->addButton( "delete", "Borrar", "images/no_.gif" );					

$tab_dirtel->addItem($grid_medioscontacto);					
				 
$tab_panel->addItem($tab_dirtel);	


//-------------------------------------------INFORMACION ADICIONAL--------------------------------------------------------
$tab_infoadicional = new PhpExt_TabPanel();
$tab_infoadicional->setTitle("Informaci&oacute;n adicional")
				  ->setFrame(true)	
				  ->setenableTabScroll(true)
				  ->setAutoScroll(true)				  
				  ->setActiveItem(0)
				  ->setHeight(400);
				  
				   
	

if( $categorias = contactos_informacion_adicional_categoria::getFilteredList() ){

	foreach( $categorias as $categoria ){
	
	
	//veo que clase de contacto acepta esta categoria	
	$array_hab = array();	
	$filtros5 = false;	
	$filtros5[] = array( "field"=>"categoria", "type"=>"list", "value"=> $categoria->id() );				  			  
	if( $habilitados = contactos_informacion_adicional_categoria_clases::getFilteredList( false, false, false, false, $filtros5 ) ){
		foreach( $habilitados as $hab ){
				$array_hab[] = array($hab->get('clase')->id());		
			}//end each		
	}//end if
	
				 
	$form = new PhpExt_Panel();
	$form->setFrame(true)	
		 ->setAutoScroll(true)
		 ->setTitle($categoria->get('nombre'))
		 ->setOtherParam( PhpExt_Javascript::variable( json_encode($array_hab) ) )		 
		 ->setLayout( new PhpExt_Layout_FormLayout() );
		
	
	$field = false;
	$i = 0;
	$filtros2 = false;
	$filtros2[] = array( "field"=>"categoria", "type"=>"list", "value"=>$categoria->id() );				  
	if( $controles = contactos_informacion_adicional_control::getFilteredList( false, false, false, false, $filtros2 ) ){

		foreach( $controles as $control ){

			switch( $control->get('tipo')->get('nombre') ){
			case 'RadioGroup':			
			case 'CheckBoxGroup':
					if( $control->get('tipo')->get('nombre') == 'RadioGroup' ){	
					$field = new PhpExt_Form_RadioGroup();					
					}else{
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
								$opt->setChecked((contactos_informacion_adicional_subcontrol_value::get_control_value($contacto,$subitem) == 1) );
							}else{
								$opt = new PhpExt_Form_Checkbox();
								$opt->setName( "check_" . $control->id() )
									->setDescripcion( $subitem->id() );
								$opt->setChecked((contactos_informacion_adicional_subcontrol_value::get_control_value($contacto,$subitem) == 1) );
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
				$field = PhpExt_Form_DateField::createDateField( $control->id(),'titulo');
				$field->setInvalidText("Fecha Invalida(dd/mm/yyyy)");
				$field->setFormat(str_replace("%","",$ari->get("locale")->get('shortdateformat','datetime')));
				
				$field->setValue(contactos_informacion_adicional_control_value::get_control_value($contacto,$control));
			break;
			case 'TextField':
				$field = PhpExt_Form_TextField::createTextField( $control->id(),'titulo');
				$field->setValue(contactos_informacion_adicional_control_value::get_control_value($contacto,$control));
			break;
			case 'NumberField':
				$field = PhpExt_Form_NumberField::createNumberField( $control->id(),'titulo');
				$field->setValue(contactos_informacion_adicional_control_value::get_control_value($contacto,$control));
			break;		
			case 'TextArea':
				$field = PhpExt_Form_TextArea::createTextArea( $control->id(),'titulo');
				$field->setValue(contactos_informacion_adicional_control_value::get_control_value($contacto,$control));
			break;		
			}
			$i++;

			$filtros3 = false;
			$filtros3[] = array( "field"=>"control", "type"=>"list", "value"=>$control->id() );				  	
			
			//seteo las propiedades del control
			if( $propiedades = contactos_informacion_adicional_control_propiedad::getFilteredList( false, false, false, false, $filtros3 ) ){
				foreach( $propiedades as $propiedad ){
					switch( $propiedad->get('nombre') ){
					case 'label':
						$field->setFieldLabel($propiedad->get('value'));
					break;
					case 'value':
					break;				
					}
				
				}	
			}	
			
			$form->addItem($field);	
		
		}//end each
		
	}//end if
	
		$tab_infoadicional->addItem($form);
	
	}
	
	}
	
	

	

	
	



		
		
$tab_panel->addItem($tab_infoadicional);

$frm_mofidicar->addItem( $tab_panel );		

$enviar_form = "
function(){
	var form = this.findParentByType('form');
	var tabpanel = form.findBy(function(c){ return (c.xtype =='tabpanel') });				
	var mediocontacto = form.findBy(function(c){ return ( c.title == 'Direcciones/Tel&eacute;fonos') });			
	var general = form.findBy(function(c){ return ( c.title == 'Datos Generales') });
	var infoadicional = form.findBy(function(c){ return ( c.title == 'Informaci&oacute;n adicional') });			
	var tabs = infoadicional[0].findBy(function(c){ return (c.xtype == 'panel') });	
	var cbo_areas = form.getForm().findField('areas');
	
	
	var controles = Array();
	var i = 0;
	while( i < tabs.length ){		
		tabs[i].findBy(function(c){ 
		
		if( c.getXType() == 'radiogroup' || c.getXType() == 'checkboxgroup' ){
				var r_items = c.items.items;				
				if(r_items){				
					var x = 0;
					var subitems = Array();
					while( x < r_items.length ){
						
					
						subitems.push({							
									name  : r_items[x].descripcion,
									value  : r_items[x].getValue()
									});
						x++;
					}
				}

		}
		
		
		var value = c.getValue();
		if( c.getXType() == 'datefield' ){
			var dt = new Date(value);
			value = dt.format('Y-m-d');						
		}
		
		
		var control = {
					  name : c.name,
					 value : value,
				  subitems : subitems
					   }
		controles.push(control);		
		});			
		i++;	
	}
		

	
	var formulario = form.getForm();
	var group = form.findBy(function(c){ return ( c.xtype == 'radiogroup' ) });	
	var radio = form.findBy(function(c){ return ( c.xtype == 'radio' ) });	
	var p = (group[0].items.items[0].getValue())?'pf':'pj';
	var u = (radio[0].getValue())?'existente':'nuevo';
	
			
	var usuario = formulario.findField('cbo_usuario_value');		
	if( u == 'existente' ){
		if( usuario.getValue() == '' ){
			Ext.MessageBox.alert( 'Emporika', 'Debe seleccionar un usuario' ); 
			tabpanel[0].setActiveTab(general[0]);
			return false;
		}
	}
	
	var medios = form.findBy(function(c){ return (c.xtype == 'editorgrid') });		
	var store = medios[1].getStore();
	var store_dir = medios[0].getStore();	
		
	var medios = Array();
	var entro = false;
	var error = '';	
	store.each( function(store){ 

		if(store.data.tipo == ''){
			error = 'Debe seleccionar el tipo de medio de contacto';			
			return false;
		}	

		if( store.data.id_tipo == 1 && !Ext.form.VTypes.email(store.data.direccion) ){		
			error = 'Debe ingresar una direcci&oacute;n de email valida'; 
			return false;
		}
		
		if( store.data.id_tipo == 2 && Ext.util.Format.trim(store.data.direccion) == '' ){
			error = 'Debe ingresar un n&uacute;mero de tel&eacute;fono valido'; 
		    return false;
		}
				
		
		var medio = {
					id : store.data.id, 	
					direccion: store.data.direccion,
					tipo : store.data.id_tipo	  
					 }	
		medios.push(medio);				 
	  
	});
	
	if(error != ''){
		Ext.MessageBox.alert( 'Emporika', error ); 
		tabpanel[0].setActiveTab(mediocontacto[0]);
		return false;
	}
	
		
	var direcciones = Array();	
	store_dir.each( function(store){
		
		if( Ext.util.Format.trim(store.data.direccion) == ''){
			error = 'Debe ingresar una direcci&oacute;n';			
			return false;
		}	
	
		if(store.data.id_ciudad == ''){
			error = 'Debe seleccionar una ciudad';			
			return false;
		}
		
		if(store.data.tipo == ''){
			error = 'Debe seleccionar el tipo de direcci&oacute;n';			
			return false;
		}
	
	  var direccion = {
					id : store.data.id, 	
			 direccion : store.data.direccion,
				 extra : store.data.extra,				  
				    cp : store.data.cp,
				ciudad : store.data.id_ciudad,
				  tipo : store.data.id_tipo
					 }	
		
								 
	  direcciones.push(direccion);				       
	});
	
	if(error != ''){
		Ext.MessageBox.alert( 'Emporika', error ); 
		tabpanel[0].setActiveTab(mediocontacto[0]);
		return false;
	}
	
	tabpanel[0].setActiveTab(general[0]);
	formulario.submit(
	{      
	   waitMsg : 'Enviando Datos..',
	 waitTitle : 'Emporika',	
		params : { persona : p , 
				optusuario : u, 
					medios : Ext.encode(medios), 
				direcciones : Ext.encode(direcciones),
				  controles : Ext.encode(controles),
			   areas_values : cbo_areas.getValue()
				  },
    success_msg : 'Contacto guardado correctamente',
grid_reload_id  : '{$grid_id}'	   
	}
	);	
}
";

$send_button = PhpExt_Button::createTextButton( "Enviar", new PhpExt_JavascriptStm( $enviar_form ) );		
$frm_mofidicar->addButton( $send_button );		

//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $frm_mofidicar->getJavascript( false, "contenido" ) );
$obj_comunication->send(true);
	 
?>