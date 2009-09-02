<?php
#OOB/N1 Framework [2008 - Nutus] - PM 

// Código por JPCOSEANI
// SCRIPT QUE GENERA EL FORM MODIFICAR NOTIFICACION
include_once 'PhpExtUx/Multiselect/multiselect.php';


global $ari;
$ari->popup = 1; // no mostrar el main_frame 

$field_width = 180;//ancho de los controles

if( isset($_POST['id']) ){
	$notificacion = new contactos_notificacion(  $_POST['id'] );
	$id_notificacion = $_POST['id'];	
}else{
	throw new OOB_Exception_400("La variable [id] no esta definida");
}

$grid_id = '';

	if( isset($_POST['gid']) ){
		$grid_id = $_POST['gid'];
	}
	
PhpExt_Javascript::sendContentType();
	
	
//titulo
$txt_titulo = PhpExt_Form_TextField::createTextField( "txt_titulo", "Titulo" )
			  ->setWidth($field_width)
			  ->setValue($notificacion->get('titulo'))
			  ->setReadOnly(true)
			  ->setMsgTarget(PhpExt_Form_FormPanel::MSG_TARGET_SIDE);		

//CONTROL PARA LA FECHA
$txt_fecha = new PhpExt_Form_DateField();
$txt_fecha->setInvalidText("Fecha Invalida(dd/mm/yyyy)")
		  ->setFieldLabel("Fecha")	
		  ->setWidth(100)		  
		  ->setValue($notificacion->get('fecha')->format($ari->get("locale")->get('shortdateformat','datetime')))
		  ->setDisabled(true)
		  ->setName("fecha")		  
		  ->setFormat(str_replace("%","",$ari->get("locale")->get('shortdateformat','datetime')));

//mensaje 
$txt_mensaje = PhpExt_Form_TextArea::createTextArea( "txt_mensaje", "Mensaje" )
			  ->setWidth($field_width)
			  ->setValue($notificacion->get('mensaje'))
			  ->setReadOnly(true)
			  ->setMsgTarget(PhpExt_Form_FormPanel::MSG_TARGET_SIDE);

$contactos_store = new PhpExt_Data_JsonStore();
$contactos_store->setRoot("topics")
				->setUrl("/contactos/notificacion/get_contactos")	 
				->setAutoLoad(true)	
				->setBaseParams( array("notificacion"=>$notificacion->id()) )
				->setTotalProperty("totalCount");
     
	  
$contactos_store->addField( new PhpExt_Data_FieldConfigObject( "id", "id" ) );
$contactos_store->addField( new PhpExt_Data_FieldConfigObject( "uname", "uname" ) );			  
									
//control para seleccionar los usuarios				 
$select_contactos = new PhpExtUx_MultiSelect();	
$select_contactos->setName("contactos")						
				 ->setFieldLabel("Contactos")			 				 
				 ->setdataFields(PhpExt_Javascript::variable('["id", "uname"]'))					
				 ->setStore($contactos_store)
				 ->setvalueField("id")
				 ->setdisplayField("uname")
				 ->setHeight(230)
				 ->setWidth($field_width)				
				 ->setimagePath("/scripts/ext/resources/extjs-ux/multiselect/");

//comunicacion
$opt_comunicacion_si = new PhpExt_Form_Radio();
$opt_comunicacion_si->setBoxLabel("Si")
				  ->setValue("yes")
				  ->setName("opt-comunicacion");

$opt_comunicacion_no = new PhpExt_Form_Radio();
$opt_comunicacion_no->setBoxLabel("No")
				  ->setValue("no")
				  ->setName("opt-comunicacion");

if( $notificacion->get('iscomunicacion') == 1 ){				  
	$opt_comunicacion_si->setChecked(true);	
}				  
else
{
	$opt_comunicacion_no->setChecked(true);	
}				  

$opt_group_comunicacion = new PhpExt_Form_RadioGroup();
$opt_group_comunicacion->setfieldLabel("Es Comunicaci&oacute;n");
$opt_group_comunicacion->addItem($opt_comunicacion_si);
$opt_group_comunicacion->addItem($opt_comunicacion_no);
$opt_group_comunicacion->setDisabled(true);
			  
//enviar por email
$opt_envio_si = new PhpExt_Form_Radio();
$opt_envio_si->setBoxLabel("Si")
			 ->setValue("yes")
			 ->setName("opt-envio");

$opt_envio_no = new PhpExt_Form_Radio();
$opt_envio_no->setBoxLabel("No")
			  ->setValue("no")
			  ->setName("opt-envio");
			  
if( $notificacion->get('sendmail') == 1 ){				  
	$opt_envio_si->setChecked(true);	
}				  
else
{
	$opt_envio_no->setChecked(true);	
}			  

$opt_group_envio = new PhpExt_Form_RadioGroup();
$opt_group_envio->setfieldLabel("Enviar E-mail");
$opt_group_envio->addItem($opt_envio_si);
$opt_group_envio->addItem($opt_envio_no);
$opt_group_envio->setDisabled(true);	

//receptor
$txt_receptor = PhpExt_Form_TextField::createTextField( "txt_receptor", "Receptor" )
			  ->setWidth($field_width)
			  ->setValue($notificacion->get('receptor'))
			  ->setReadOnly(true)
			  ->setMsgTarget(PhpExt_Form_FormPanel::MSG_TARGET_SIDE);	

//resultado			  
$txt_resultado = PhpExt_Form_TextArea::createTextArea( "txt_resultado", "Resultado" )
			  ->setWidth($field_width)
			  ->setValue($notificacion->get('resultado'))
			  ->setReadOnly(true)
			  ->setMsgTarget(PhpExt_Form_FormPanel::MSG_TARGET_SIDE);	

		  
//FIN DE CONTROLES		  

//DATA-READER PARA LEER LOS RESULTADOS DEVUELTOS
$error_reader = new PhpExt_Data_JsonReader();
$error_reader->setRoot("errors");
$error_reader->setSuccessProperty("success");
$error_reader->addField(new PhpExt_Data_FieldConfigObject("id"));
$error_reader->addField(new PhpExt_Data_FieldConfigObject("msg")); 


//CREACION DEL FORMULARIO
$frm_new_notificacion = new PhpExt_Form_FormPanel();
$frm_new_notificacion->setErrorReader($error_reader)	 		 
					 ->setFrame(true)
					 ->setUrl("/contactos/notificacion/new_process")
					 ->setWidth(600)				 
					 ->setAutoHeight(true)					 
					 ->setTitle("Datos de la Notificaci&oacute;n")			  
					 ->setMethod(PhpExt_Form_FormPanel::METHOD_POST);
	 
//MARCO PARA CONTENER LOS CONTROLES
$fieldtset= new PhpExt_Form_FieldSet();	
$fieldtset->setAutoHeight(true);			   
			   
//AGREGO LOS ITEMS AL fieldtset
$fieldtset->addItem($txt_titulo);
$fieldtset->addItem($txt_fecha);
$fieldtset->addItem($txt_mensaje);
$fieldtset->addItem($select_contactos);
$fieldtset->addItem($opt_group_comunicacion); 
$fieldtset->addItem($opt_group_envio);  
$fieldtset->addItem($txt_receptor); 
$fieldtset->addItem($txt_resultado);

//AGREGO EL MARCO AL FORM
$frm_new_notificacion->addItem($fieldtset);			   

//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $frm_new_notificacion->getJavascript( false, "contenido" ) );
$obj_comunication->send(true);

?>

