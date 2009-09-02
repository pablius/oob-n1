<?php

#OOB/N1 Framework [2008 - Nutus] - PM 

// Código por JPCOSEANI
// Script que genera el FORM UPDATE MONEDA

if (!seguridad :: isAllowed(seguridad_action :: nameConstructor('update','currency','currency')) )
{
	throw new OOB_exception("Acceso Denegado", "403", "Acceso Denegado. Consulte con su Administrador!", true);
}  


include_once 'PhpExtUx/Multiselect/Itemselector.php';

global $ari;
$ari->popup = 1; // no mostrar el main_frame 
$field_width = 180; //ancho de los controles
$separador_decimal = trim( $ari->locale->get( 'decimal', 'numbers' ) );
$page_size = PAGE_SIZE;

$grid_id = '';

	if( isset( $_POST['gid'] ) )
	{
		$grid_id = $_POST['gid'];
	}

//CREO EL OBJECTO MONEDA CON EL ID QUE ME PASAN
if( isset( $_POST['id'] ) ){
	$currency = new currency_currency ($_POST["id"]);
}else{
	throw new OOB_Exception_400("La variable [id] no esta definida");
}	

PhpExt_Javascript::sendContentType();
//CREACION DE LOS CONTROLES

//MONEDA
$txt_moneda =  PhpExt_Form_TextField::createTextField("txt_moneda","Moneda")
			   ->setMsgTarget(PhpExt_Form_FormPanel::MSG_TARGET_SIDE)
			   ->setWidth($field_width)
			   ->setValue($currency->get("name"));

//SIGNO
$txt_signo =  PhpExt_Form_TextField::createTextField("txt_signo","Signo")
			   ->setMsgTarget(PhpExt_Form_FormPanel::MSG_TARGET_SIDE)
			   ->setWidth($field_width)
			   ->setValue($currency->get("sign"));			

//TIPO DE CAMBIO
$opt_tipo_fijo= new PhpExt_Form_Radio();
$opt_tipo_fijo->setBoxLabel("Fijo")
			  ->setInputValue("fixed")
			  ->setName("opt-tipo-cambio");

if($currency->get("type")=="1"){			  
	$opt_tipo_fijo->setChecked(true);			  
}

$opt_tipo_flotante= new PhpExt_Form_Radio();
$opt_tipo_flotante->setBoxLabel("Flotante")
				  ->setInputValue("float")
				  ->setName("opt-tipo-cambio");


				  
$opt_group_tipo_cambio = new PhpExt_Form_RadioGroup();
$opt_group_tipo_cambio->setfieldLabel("Tipo de cambio");
$opt_group_tipo_cambio->addItem($opt_tipo_fijo);
$opt_group_tipo_cambio->addItem($opt_tipo_flotante);

$group_render = "

var formulario = this.findParentByType('form');
var group = formulario.findBy( function(c){ return ( c.xtype == 'radiogroup' );} );
var opt = this.items.items[0];	
var opt2 = this.items.items[1];	

opt.on( 'check' , function(t,n,o){

	var field = formulario.getForm().findField('txt_valor');
	if( !group[1].items.items[0].getValue() ){ 
		if( t.getValue() )
		{
			field.enable();
			field.focus(true);	
		}
		else
		{
			field.setValue('1');
			field.disable();		
			
		}
	}
	
});


opt2.on( 'check', function(t,n,o){

if( group[1].items.items[0].getValue() ){ 
	t.setValue(false);
	opt.setValue(true);
}

});

";

$opt_group_tipo_cambio->attachListener( "render", new PhpExt_Listener( PhpExt_Javascript::functionDef( null, $group_render , array( "group" ) )) );			   		   	 				  			   
$opt_tipo_fijo->setEnableKeyEvents(true);

//VALOR DE LA MONEDA
$txt_valor =  PhpExt_Form_NumberField ::createNumberField("txt_valor","Valor")
			   ->setMsgTarget(PhpExt_Form_FormPanel::MSG_TARGET_SIDE)
			   ->setWidth($field_width)
			   ->setDecimalSeparator( $separador_decimal )
			   ->setValue($currency->get("value"));	

if( $currency->get("type") == "2" ){			  
	$opt_tipo_flotante->setChecked(true);		
	$txt_valor->setDisabled(true);	
}				  			   
			   
//MONEDA PREDETERMINADA			   
$opt_predeterminada_si= new PhpExt_Form_Radio();
$opt_predeterminada_si->setBoxLabel("Si")
					  ->setInputValue("yes")	
					  ->setName("opt-predeterminada");

if( $currency->get("default") == "1" ){			  
	$opt_predeterminada_si->setChecked(true);		
}					  

$opt_predeterminada_no= new PhpExt_Form_Radio();
$opt_predeterminada_no->setBoxLabel("No")
					  ->setInputValue("no")	
					  ->setName("opt-predeterminada");

if( $currency->get("default") == "0" ){			  
	$opt_predeterminada_no->setChecked(true);			  
}



$opt_group_predeterminada = new PhpExt_Form_RadioGroup();
$opt_group_predeterminada->setfieldLabel("Predeterminada");
$opt_group_predeterminada->addItem($opt_predeterminada_si);
$opt_group_predeterminada->addItem($opt_predeterminada_no);


$group_render = "

var formulario = this.findParentByType('form');
	
var group = formulario.findBy( function(c){ return ( c.xtype == 'radiogroup' );} );

opt = this.items.items[0];	

opt.on( 'check' , function(t,n,o){	
	var field = formulario.getForm().findField('txt_valor');
	if( t.getValue() )
	{
		group[0].items.items[0].setValue(true);
		group[0].items.items[1].setValue(false);
		field.setValue('1');
		field.disable();		
	}
	else
	{
		if( group[0].items.items[0].getValue() ){
			field.enable();
			field.focus(true);		
		}else{
			field.setValue('1');
			field.disable();		
		}
	}
},this);
";

$opt_group_predeterminada->attachListener( "render", new PhpExt_Listener( PhpExt_Javascript::functionDef( null, $group_render , array( "group" ) )) );			   		   	 				  			   
$opt_predeterminada_si->setEnableKeyEvents(true);


//cargo los idiomas
$languages_si = array();
$languages_no = array();

if ($array_languages = $ari->get('agent')->getLanguages() ); 
{
	foreach($array_languages as $l)
	{
				
		if ( $lenguajes_select = currency_currency::getLanguages($currency) )
		{
			if ( in_array( $l , $lenguajes_select ) )
			{	
				$languages_si[] = array( $l , $l ); 
			}	
			else
			{	
				$languages_no[] = array( $l , $l ); 
			}	
		}
	}
}


$select_idioma = new PhpExtUx_Itemselector();	
$select_idioma->setName("idiomas")		   	
			  ->setFieldLabel("Idioma")
			  ->setToLegend("Predeterminado")
			  ->setFromLegend("No")
			  ->settoData(  PhpExt_Javascript::variable( json_encode($languages_si) ) )			  
			  ->setfromData(  PhpExt_Javascript::variable( json_encode($languages_no) ) )				  			  
			  ->setvalueField("id")
			  ->setdisplayField("name")
			  ->setmsHeight(150)
			  ->setmsWidth(120)			 
		      ->setdataFields(PhpExt_Javascript::variable('["id", "name"]'))
		      ->setimagePath("/scripts/ext/resources/extjs-ux/multiselect/");
			   	
				
//BOTON GRABAR ONCLICK			
$handler_save = "
function(){

var form = this.findParentByType('form');
var selector = form.findBy(function(c){ return (c.xtype == 'itemselector') });	

if( selector[0].toStore.getCount() == 0 ){
	Ext.MessageBox.alert( 'Emporika', 'Debe seleccionar un idioma' ); 
	return false;
}

var a_params = '';
var field = form.getForm().findField('txt_valor');

if( field.disabled ){
	a_params = { txt_valor : 1 }
}

form.getForm().submit(
	{      
		  waitMsg : 'Enviando Datos',	
		   params : a_params,		  
		waitTitle : 'Emporika',
	  success_msg : 'Moneda guardada correctamente',
  grid_reload_id  : '{$grid_id}'
  
	}
	);				
							}";			

$save_button = PhpExt_Button::createTextButton( "Guardar", new PhpExt_JavascriptStm($handler_save) );			
			


//Data_Reader para leer los resultados devueltos 
$error_reader= new PhpExt_Data_JsonReader();
$error_reader->setRoot("errors");
$error_reader->setSuccessProperty("success");
$error_reader->addField(new PhpExt_Data_FieldConfigObject("id"));
$error_reader->addField(new PhpExt_Data_FieldConfigObject("msg")); 

//ID MONEDA
$id_moneda = array();
$id_moneda["id"] = $currency->get("id");

//FORMULARIO
$frm_update_currency = new PhpExt_Form_FormPanel();
$frm_update_currency->setErrorReader($error_reader)		 		 		 		      		      
				    ->setBaseParams($id_moneda)				 
					->setFrame(true)				
					->setUrl("/currency/currency/update_process")
					->setWidth(400)
					->setAutoHeight(true)					
					->setTitle( "Datos de la moneda" )			  
					->setMethod(PhpExt_Form_FormPanel::METHOD_POST);
		      

	 
//MARCO PARA CONTENER LOS CONTROLES
$marco = new PhpExt_Form_FieldSet();	
$marco->setAutoHeight( true );  

//AGREGO TODOS LOS CONTROLES AL MARCO
$marco->addItem( $txt_moneda );
$marco->addItem( $txt_signo );
$marco->addItem( $opt_group_tipo_cambio );
$marco->addItem( $txt_valor );
$marco->addItem( $opt_group_predeterminada );
$marco->addItem( $select_idioma );

//AGREGO EL MARCO AL FORMULARIO
$frm_update_currency->addItem($marco);

//AGREGO LOS BOTONES AL FORMULARIO
$frm_update_currency->addButton( $save_button );
$frm_update_currency->addButton( PhpExt_Button::createTextButton("Cancelar") );

//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $frm_update_currency->getJavascript( false, "contenido" ) );
$obj_comunication->send(true);

?>