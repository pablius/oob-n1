<?php

global $ari;
$ari->popup = 1; // no mostrar el main_frame 


include_once 'PhpExtUx/Multiselect/multiselect.php';

$field_width = 175;
$id_contacto = '';
$page_size = PAGE_SIZE;

	if(isset($_POST['id'])){
		$contacto = new contactos_contacto(  $_POST['id'] );
		$id_contacto = $_POST['id'];	
	}
	else
	{
			throw new OOB_Exception_400("La variable [id] no esta definida");
	}//end if

PhpExt_Javascript::sendContentType();
//DATA-READER PARA LEER LOS RESULTADOS DEVUELTOS
$error_reader = new PhpExt_Data_JsonReader();
$error_reader->setRoot("errors");
$error_reader->setSuccessProperty("success");
$error_reader->addField(new PhpExt_Data_FieldConfigObject("id"));
$error_reader->addField(new PhpExt_Data_FieldConfigObject("msg")); 

//FORMULARIO
$frm_view = new PhpExt_Form_FormPanel();
$frm_view->setFrame(true)
		 ->setErrorReader($error_reader)
		 ->setWidth(700)			 
		 ->setUrl( "/contactos/contacto/new_process" )			  
		 ->setTitle( "Datos del Contacto" )			  
		 ->setMethod( PhpExt_Form_FormPanel::METHOD_POST );
		 
$form_render = "	


var tabpanel = form.findBy(function(c){ return (c.xtype == 'tabpanel'); });

tabpanel[0].on( 'render', function(){

var tab = tabpanel[0].findBy(function(c){ return (c.title == 'Informaci&oacute;n adicional') });

var clase = '" . $contacto->get('clase')->id() . "' ;
var paneles = tab[0].findBy(function(c){ return (c.xtype == 'panel') });

var entronunca = true;

	if( paneles.length > 0){
		entronunca = false;
	}//end if

	
	if(entronunca){					
		tabpanel[0].hideTabStripItem(tab[0]);
	}
	
});
	

";

$frm_view->attachListener( "beforerender", new PhpExt_Listener( PhpExt_Javascript::functionDef( null, $form_render , array( "form" ) )) );			   		   	 				  			   
$frm_view->setEnableKeyEvents(true);		 
			 
$tab_panel = new PhpExt_TabPanel();
$tab_panel->setPlain(true)			
          ->setActiveTab(0)
          ->setHeight(390)	
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
$fieldset_datos->setHeight(350)			  
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

$opt_group_tipo_persona = new PhpExt_Form_RadioGroup();
$opt_group_tipo_persona->setfieldLabel("Tipo de contacto");
$opt_group_tipo_persona->setName("opttipo");
$opt_group_tipo_persona->setDisabled(true);
$opt_group_tipo_persona->addItem($opt_tipo_fisica);
$opt_group_tipo_persona->addItem($opt_tipo_juridica);	
$opt_group_tipo_persona->setWidth($field_width);


	if( $contacto->get('tipo')->id() == 1 ){
		$opt_tipo_juridica->setChecked(true);
		$opt_tipo_fisica->setChecked(false);
	}
	else
	{
		$opt_tipo_juridica->setChecked(false);
		$opt_tipo_fisica->setChecked(true);
	}

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
			   ->setReadOnly(true)
			   ->setWidth($field_width);	

$fieldset_datos->addItem($txt_nombre);					   

//RAZON SOCIAL O APELLIDO
$txt_razonsocial =  PhpExt_Form_TextField::createTextField("txt_razonsocial","Apellido")
					->setMsgTarget(PhpExt_Form_FormPanel::MSG_TARGET_SIDE)
					->setValue( $contacto->get('apellido') )
					->setReadOnly(true)
					->setWidth($field_width);	
					
//si es juridica					
if( $contacto->get('tipo')->id() == 1 ){
	$txt_razonsocial->setFieldLabel('Razon Social');
}					

$fieldset_datos->addItem($txt_razonsocial);						
					
//CUIT CUIL
$txt_cuit =  PhpExt_Form_TextField::createTextField( "txt_cuit", "CUIT/L" )
			   ->setMsgTarget(PhpExt_Form_FormPanel::MSG_TARGET_SIDE)
			   ->setReadOnly(true)
			   ->setValue( vsprintf( '%d%d-%d%d%d%d%d%d%d%d-%d', str_split( $contacto->get('cuit'),1 ) ) )		   
			   ->setWidth($field_width);					
			   
$fieldset_datos->addItem($txt_cuit);									   
			   
//ING BRUTOS
$txt_ingbrutos =  PhpExt_Form_NumberField::createNumberField( "txt_ingbrutos", "Ing. Brutos" )
				  ->setMsgTarget(PhpExt_Form_FormPanel::MSG_TARGET_SIDE)	
				  ->setReadOnly(true)
				  ->setValue( $contacto->get('ingbrutos') )	
			      ->setWidth($field_width);								   

$fieldset_datos->addItem($txt_ingbrutos);						

//NRO CLIENTE
$txt_nrocliente =  PhpExt_Form_NumberField::createNumberField( "txt_nrocliente", "Nro. Cliente" )
				  ->setMsgTarget(PhpExt_Form_FormPanel::MSG_TARGET_SIDE)
				  ->setReadOnly(true)
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
			->setDisabled(true)	
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
				if( $clase->get('id') == $contacto->get('rubro')->id() ){
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
			->setDisabled(true)	
			->setValue( $clase_selected )
			->setForceSelection(true)			
			->setSingleSelect(true)	
			->setWidth( $field_width )
			->setMode(PhpExt_Form_ComboBox::MODE_LOCAL)			   
			->setTriggerAction(PhpExt_Form_ComboBox::TRIGGER_ACTION_ALL);
			
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
				->setDisabled(true)				
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
$store_areas->addField("id");
$store_areas->addField("name");			  
$store_areas->setData(PhpExt_Javascript::variable( json_encode($areas) ) );	  


$cbo_area = new PhpExtUx_MultiSelect();						   
$cbo_area->setdata(PhpExt_Javascript::variable( json_encode($areas) ) )		
		 ->setdisplayField("name")
		 ->setvalueField("id")
		 ->setFieldLabel("Area")
		 ->setdataFields(array("id","name"))
		 ->setHeight(50)
		 ->setValue( implode( "," , $areas_selected ) )
		 ->setName("areas")
		 ->setDisabled(true)
		 ->setWidth( $field_width );
			
$fieldset_datos->addItem($cbo_area);

//ING BRUTOS
$txt_diaspago =  PhpExt_Form_NumberField::createNumberField( "txt_diaspago", "D&iacute;as Pago" )
				  ->setMsgTarget(PhpExt_Form_FormPanel::MSG_TARGET_SIDE)		
				  ->setValue( $contacto->get('dias_pago') )
				  ->setReadOnly(true)
			      ->setWidth($field_width);								   

$fieldset_datos->addItem($txt_diaspago);	

$firstColumn_resumen->addItem($fieldset_datos, new PhpExt_Layout_AnchorLayoutData("95%"));


$fieldset_usuarios = new PhpExt_Form_FieldSet();	
$fieldset_usuarios->setHeight(350)					  
			      ->setTitle('Usuarios');	
		
//usuario
$txt_usuario  = PhpExt_Form_TextField::createTextField( "txt_usuario", "Usuario" )
			    ->setWidth( $field_width )
				->setReadOnly(true)
				->setValue( $contacto->get('usuario')->name() )
			    ->setMsgTarget(PhpExt_Form_FormPanel::MSG_TARGET_SIDE);		

$fieldset_usuarios->addItem($txt_usuario);	
				
			
//e-mail
$txt_email   =  PhpExt_Form_TextField::createTextField("txt_email","E-mail", null, PhpExt_Form_FormPanel::VTYPE_EMAIL)
			    ->setWidth($field_width)
				->setReadOnly(true)
				->setValue( $contacto->get('usuario')->get('email') )
			    ->setMsgTarget(PhpExt_Form_FormPanel::MSG_TARGET_SIDE);		

$fieldset_usuarios->addItem($txt_email);						
				
//Paso los estado a json
$estados = array();
foreach (oob_user::getStatus() as $id=>$descripcion){
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
			   ->setValue( $contacto->get('usuario')->get('status') )
			   ->setDisplayField("descripcion")		
			   ->setValueField("id")
			   ->setDisabled(true)			   
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
$store_direcciones->addField(new PhpExt_Data_FieldConfigObject("id_ciudad"));
$store_direcciones->addField(new PhpExt_Data_FieldConfigObject("tipo"));

			   
			   
 
//SE AGREGAN LAS COLUMNAS A LA GRILLA   
$col_model_direcciones = new PhpExt_Grid_ColumnModel();
$col_model_direcciones->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("Direcci&oacute;n","direccion"))	
					  ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("Datos extra","extra")) 					  
					  ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("C.P.","cp"))	  					  
					  ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("Ciudad","ciudad"))					  			  
					  ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("Tipo","tipo"));

//GRILLA DIRECCIONES	  
$grid_direcciones = new PhpExt_Grid_EditorGridPanel();
$grid_direcciones->setHeight(150)	     
				 ->setTitle("Direcciones")	 
				 ->setSelectionModel(new PhpExt_Grid_RowSelectionModel())
				 ->setColumnModel( $col_model_direcciones )	 
				 ->setStore( $store_direcciones )	 
				 ->setEnableHeaderMenu(false)
				 ->setEnableColumnMove(false)
				 ->setBorder(true)	 
				 ->setFrame(true)
				 ->setLoadMask(true);	

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
$store_medios_contacto->addField(new PhpExt_Data_FieldConfigObject("tipo"));
$store_medios_contacto->addField(new PhpExt_Data_FieldConfigObject("prefix"));

 
//SE AGREGAN LAS COLUMNAS A LA GRILLA   
$col_model_medioscontacto = new PhpExt_Grid_ColumnModel();
$col_model_medioscontacto->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("Tipo","tipo"))
						 ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("Direcci&oacute;n","direccion",null,180));
					
					

//GRILLA DIRECCIONES ONLINE
$grid_medioscontacto = new PhpExt_Grid_EditorGridPanel();
$grid_medioscontacto->setHeight(150)	     
				    ->setTitle("Medios de Contacto")	 
				    ->setSelectionModel(new PhpExt_Grid_RowSelectionModel())
				    ->setColumnModel( $col_model_medioscontacto )	 
				    ->setStore( $store_medios_contacto )	 
				    ->setEnableHeaderMenu(false)
				    ->setEnableColumnMove(false)
				    ->setBorder(true)	 
				    ->setFrame(true)
				    ->setLoadMask(true);

$tab_dirtel->addItem($grid_medioscontacto);					
				 
$tab_panel->addItem($tab_dirtel);	


//-------------------------------------------INFORMACION ADICIONAL--------------------------------------------------------
$tab_infoadicional = new PhpExt_TabPanel();
$tab_infoadicional->setTitle("Informaci&oacute;n adicional")
				  ->setFrame(true)
				  ->setActiveItem(0)
				  ->setHeight(400);
				  
$array_hab = array();	
$filtros5 = false;
$filtros5[] = array( "field"=>"clase", "type"=>"list", "value"=> $contacto->get('clase')->id() );				  
if( $habilitados = contactos_informacion_adicional_categoria_clases::getFilteredList( false, false, false, false, $filtros5 ) ){
	foreach( $habilitados as $hab ){
			$array_hab[] = $hab->get('categoria')->id();		
	}		
}					

if( count($array_hab) > 0 ){
			
$filtros6 = false;
$filtros6[] = array( "field"=>"id", "type"=>"list", "value"=> implode(",", $array_hab)	);				  				
if( $categorias = contactos_informacion_adicional_categoria::getFilteredList( false, false, false, false, $filtros6 ) ){

	foreach( $categorias as $categoria ){
		 
	$form = new PhpExt_Panel();
	$form->setFrame(true)
		 ->setAutoScroll(true)
		 ->setTitle($categoria->get('nombre'))
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
							$field->setDisabled(true);
						}					
					}
			break;			
			case 'DateField':
				$field = PhpExt_Form_DateField::createDateField('fe1','titulo');
				$field->setValue(contactos_informacion_adicional_control_value::get_control_value($contacto,$control));
				$field->setReadOnly(true);
			break;
			case 'TextField':
				$field = PhpExt_Form_TextField::createTextField('fe2','titulo');
				$field->setValue(contactos_informacion_adicional_control_value::get_control_value($contacto,$control));
				$field->setReadOnly(true);
			break;
			case 'NumberField':
				$field = PhpExt_Form_NumberField::createNumberField('fe3','titulo');
				$field->setValue(contactos_informacion_adicional_control_value::get_control_value($contacto,$control));
				$field->setReadOnly(true);
			break;		
			case 'TextArea':
				$field = PhpExt_Form_TextArea::createTextArea('fe4','titulo');
				$field->setValue(contactos_informacion_adicional_control_value::get_control_value($contacto,$control));
				$field->setReadOnly(true);
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
					case 'algo':
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

}
		
		
$tab_panel->addItem($tab_infoadicional);	

//-------------------------------------------NOTIFICACIONES---------------------------------------------------------------
$tab_notificaciones = new PhpExt_Panel();
$tab_notificaciones->setTitle("Notificaciones")
				   ->setFrame(true)
				   ->setHeight(400)
				   ->setLayout(new PhpExt_Layout_BorderLayout());
				  
$store_notificaciones = new PhpExt_Data_JsonStore();
$store_notificaciones->setUrl("/contactos/contacto/get_notificaciones")	  
					 ->setRoot("topics")
					 ->setBaseParams( array('id'=>$contacto->id() ) )
					 ->setTotalProperty("totalCount")
					 ->setId("id");

//FILTROS
$filter_plugin = new PhpExtUx_Grid_GridFilters();
$filter_plugin->addFilter(PhpExt_Grid_FilterConfigObject::createFilter("numeric","id"));
$filter_plugin->addFilter(PhpExt_Grid_FilterConfigObject::createFilter("date","fecha"));
$filter_plugin->addFilter(PhpExt_Grid_FilterConfigObject::createFilter("string","novedad")); 
$filter_plugin->addFilter(PhpExt_Grid_FilterConfigObject::createFilter("string","contacto"));
$filter_plugin->addFilter(PhpExt_Grid_FilterConfigObject::createFilter("string","mensaje"));					 
	  
	  
$store_notificaciones->addField(new PhpExt_Data_FieldConfigObject("id"));
$store_notificaciones->addField(new PhpExt_Data_FieldConfigObject("novedad"));
$store_notificaciones->addField(new PhpExt_Data_FieldConfigObject("contacto"));
$store_notificaciones->addField(new PhpExt_Data_FieldConfigObject("mensaje"));
$store_notificaciones->addField(new PhpExt_Data_FieldConfigObject("resultado"));
$store_notificaciones->addField(new PhpExt_Data_FieldConfigObject("iscomunicacion"));
$store_notificaciones->addField(new PhpExt_Data_FieldConfigObject("sendmail"));
$store_notificaciones->addField(new PhpExt_Data_FieldConfigObject("receptor"));
$store_notificaciones->addField(new PhpExt_Data_FieldConfigObject("estado"));
$store_notificaciones->addField(new PhpExt_Data_FieldConfigObject("fecha"));						 

$col_model_notificaciones = new PhpExt_Grid_ColumnModel();
$col_model_notificaciones->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("Id","id",null,30))
						 ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("Novedad","novedad",null,200))
						 ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("Fecha","fecha"))				  
						 ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("Es comunicaci&oacute;n","iscomunicacion"))				  
						 ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("Enviado por email","sendmail"))				  
						 ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("Receptor","receptor"))				  						 
						 ->addColumn(PhpExt_Grid_ColumnConfigObject::createColumn("Estado","estado"));		
         
$paging_notificaciones = new PhpExt_Toolbar_PagingToolbar();
$paging_notificaciones->setStore($store_notificaciones)
					  ->setPageSize($page_size)
					  ->setDisplayInfo(true)	
					  ->setEmptyMessage("No se encontraron notificaciones");
$paging_notificaciones->getPlugins()->add($filter_plugin);		   
		 
$grid_notificaciones = new PhpExt_Grid_GridPanel();
$grid_notificaciones->setColumnModel($col_model_notificaciones)
					->setStore($store_notificaciones)	
					->setStripeRows(true)					
					->setHeight(180)	
					->setLoadMask(true);

$grid_render = "
		
		var panel = this.findParentBy(function(c){ return (c.xtype == 'panel') });	
		var info = panel.findBy(function(c){ return (c.xtype == 'panel') });
		
		var template = [			
			'<p>Mensaje: {mensaje}</p>',
			'<p>Resultado: {resultado}</p>'			
		];
		
		var tpl = new Ext.Template(template);
	   
		grid.on( 'cellclick', function( grid, rowIndex, columnIndex, e){
			var r = grid.getStore().getAt(rowIndex); 
			tpl.overwrite(info[0].body,r.data);				
		});

";

$grid_notificaciones->setEnableKeyEvents(true);
$grid_notificaciones->attachListener( "render", new PhpExt_Listener( PhpExt_Javascript::functionDef( null, $grid_render , array( "grid" ) )) );			   		   	 					
					
$tab_notificaciones->addItem( $grid_notificaciones,PhpExt_Layout_BorderLayoutData::createNorthRegion() );
	 
	
$grid_notificaciones->setBottomToolbar( $paging_notificaciones );  

$info = new PhpExt_Panel();
$info->setBodyStyle("background:#ffffff;padding:7px;");
$info->sethtml("Por favor seleccione una notificaci&oacute;n");
$tab_notificaciones->addItem($info,PhpExt_Layout_BorderLayoutData::createCenterRegion());
				   
$tab_panel->addItem($tab_notificaciones);	


$frm_view->addItem( $tab_panel );

$resultado = '';
$resultado.= $filter_plugin->getJavascript(false, "filters");
$resultado.= $store_notificaciones->getJavascript(false, "store_group_list");
$resultado.= "store_group_list.load({params:{ start:0 , limit:{$page_size}} });";
$resultado.= $col_model_notificaciones->getJavascript(false, "cm");
$resultado.= "cm.defaultSortable = true;";			 
$resultado.= $frm_view->getJavascript( false, "contenido" );  	

//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $resultado );
$obj_comunication->send(true);		 

?>