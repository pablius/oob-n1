<?php
#OOB/N1 Framework [2008 - Nutus] - PM 

// Código por JPCOSEANI
// SCRIPT QUE GENERA EL FORM MODIFICAR NOTIFICACION
include_once 'PhpExtUx/Multiselect/Itemselector.php';


global $ari;
$ari->popup = 1; // no mostrar el main_frame 

$field_width = 180;//ancho de los controles

if(isset($_POST['id'])){
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
			  ->setMsgTarget(PhpExt_Form_FormPanel::MSG_TARGET_SIDE);		

//CONTROL PARA LA FECHA
$txt_fecha = new PhpExt_Form_DateField();
$txt_fecha->setInvalidText("Fecha Invalida(dd/mm/yyyy)")
		  ->setFieldLabel("Fecha")	
		  ->setWidth(100)				  
		  ->setValue($notificacion->get('fecha')->format($ari->get("locale")->get('shortdateformat','datetime')))
		  ->setName("fecha")		  
		  ->setFormat(str_replace("%","",$ari->get("locale")->get('shortdateformat','datetime')));

//mensaje 
$txt_mensaje = PhpExt_Form_TextArea::createTextArea( "txt_mensaje", "Mensaje" )
			  ->setWidth(380)
			  ->setHeight(120)
			  ->setValue($notificacion->get('mensaje'))			  
			  ->setMsgTarget(PhpExt_Form_FormPanel::MSG_TARGET_SIDE);

$contactos_from_store = new PhpExt_Data_JsonStore();
$contactos_from_store->setUrl("/contactos/notificacion/get_contactos_nomembers")
					 ->setBaseParams( array( "notificacion" => $notificacion->id() ) )
					 ->setRoot("topics")				   
					 ->setTotalProperty("totalCount");
     
	  
$contactos_from_store->addField( new PhpExt_Data_FieldConfigObject("id","id") );
$contactos_from_store->addField( new PhpExt_Data_FieldConfigObject("uname","uname") );


$contactos_to_store = new PhpExt_Data_JsonStore();
$contactos_to_store->setRoot("topics")
				   ->setAutoLoad("true")
				   ->setUrl("/contactos/notificacion/get_contactos_members")	 
				   ->setBaseParams( array("notificacion"=>$notificacion->id()) )				   
				   ->setTotalProperty("totalCount");
     
	  
$contactos_to_store->addField( new PhpExt_Data_FieldConfigObject( "id", "id" ) );
$contactos_to_store->addField( new PhpExt_Data_FieldConfigObject( "uname", "uname" ) );			  
			  
//TXT DE BUSQUEDA DE USUARIOS
$txt_buscar_contacto = new PhpExtUx_App_SearchField();
$txt_buscar_contacto->setFieldLabel("Buscar")
					->setStore($contactos_from_store);
									
//control para seleccionar los usuarios				 
$select_contactos = new PhpExtUx_Itemselector();	
$select_contactos->setName("contactos")						
				 ->setFieldLabel("Contactos")
				 ->setToLegend("Notificar")
				 ->setFromLegend("No Notificar")
				 ->setToStore($contactos_to_store)
				 ->setFromStore($contactos_from_store)	   
				 ->setdataFields(PhpExt_Javascript::variable('["id", "uname"]'))					
				 ->setvalueField("id")
				 ->setdisplayField("uname")
				 ->setmsHeight(230)
				 ->setmsWidth($field_width)				
				 ->setimagePath("/scripts/ext/resources/extjs-ux/multiselect/");

$select_render = "			

			var form = this.findParentByType('form');
			var formulario = form.getForm();
			var receptor = form.findBy(function(c){ return (c.name == 'txt_receptor') });	
			var resultado = form.findBy(function(c){ return (c.name == 'txt_resultado') });	
			
		select.toStore.on('load', function(){
			if(select.toStore.getCount() <= 1){
				receptor[0].setDisabled(false);
				resultado[0].setDisabled(false);
			}
		});
		
		//voy a eliminar los contactos que ya estan por que el control no es tan inteligente!!
		select.fromStore.on('load',function(){
			select.fromStore.each(function(ff){
					select.toStore.each(function(ft){
						if(ff.data.id == ft.data.id){
							select.fromStore.remove(ff);	
						}						
					});		
			});		
		});
		
		select.toStore.on( 'add', function(){		
			if(select.toStore.getCount() > 1){
				receptor[0].setDisabled(true);
				resultado[0].setDisabled(true);	
				receptor[0].setValue('');
				resultado[0].setValue('');
			}
		});
		
		select.toStore.on( 'remove',  function(){				
			if(select.toStore.getCount() <= 1){
				receptor[0].setDisabled(false);
				resultado[0].setDisabled(false);
			}
		} );
";

$select_contactos->setEnableKeyEvents(true);
$select_contactos->attachListener( "render", new PhpExt_Listener( PhpExt_Javascript::functionDef( null, $select_render , array( "select" ) )) );			   		   	 					 
				 
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

//receptor
$txt_receptor = PhpExt_Form_TextField::createTextField( "txt_receptor", "Receptor" )
			  ->setWidth($field_width)			  
			  ->setValue($notificacion->get('receptor'))
			  ->setDisabled(true)
			  ->setMsgTarget(PhpExt_Form_FormPanel::MSG_TARGET_SIDE);	

//resultado			  
$txt_resultado = PhpExt_Form_TextArea::createTextArea( "txt_resultado", "Resultado" )
			  ->setWidth(380)
			  ->setHeight(120)			  
			  ->setDisabled(true)
			  ->setValue($notificacion->get('resultado'))
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
					 ->setBaseParams( array("id"=>$notificacion->id()) )
					 ->setUrl("/contactos/notificacion/update_process")
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
$fieldtset->addItem($txt_buscar_contacto);
$fieldtset->addItem($select_contactos);
$fieldtset->addItem($opt_group_envio);  
$fieldtset->addItem($txt_receptor); 
$fieldtset->addItem($txt_resultado);


//BOTON GUARDAR ONCLICK	
$handler_accept = "function(){
	var form = this.findParentByType('form');
	var formulario = form.getForm();
	var group = form.findBy(function(c){ return (c.xtype == 'radiogroup') });		
	var sendmail = (group[0].items.items[0].getValue());
	
	var select = form.findBy(function(c){ return (c.name == 'contactos') });	
		
	var comunicacion = true;	
	if(select[0].toStore.getCount() <= 1){
		comunicacion = false;
	}
	
this.findParentByType('form').getForm().submit(
	{      
		  waitMsg : 'Enviando Datos..',	  
		   params : { iscomunicacion : comunicacion , issendmail : sendmail },
		waitTitle : 'Emporika',
	  success_msg : 'Notificaci&oacute;n guardada correctamente',
  grid_reload_id  : '{$grid_id}'		  
	}
	);				
						 }";			

//CREACION DEL BOTON ACEPTAR							  
$accept_button = PhpExt_Button::createTextButton( "Aceptar", new PhpExt_JavascriptStm($handler_accept) );			

if( $notificacion->get('sendmail') == 1 && $notificacion->get('iscomunicacion') == 1){
	$accept_button->setDisabled(true);
}

//AGREGO LOS BOTONES AL FORM
$frm_new_notificacion->addButton( $accept_button );
$frm_new_notificacion->addButton(PhpExt_Button::createTextButton("Cancelar"));

//AGREGO EL MARCO AL FORM
$frm_new_notificacion->addItem($fieldtset);			   


$resultado = '';
$resultado.= $contactos_to_store->getJavascript(false,"contactos_tostore");
$resultado.= $contactos_from_store->getJavascript(false,"contactos_fromstore");
$resultado.= $frm_new_notificacion->getJavascript( false, "contenido" );

//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $resultado );
$obj_comunication->send(true);

?>

