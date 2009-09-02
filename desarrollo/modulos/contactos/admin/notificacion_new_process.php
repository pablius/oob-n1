<?php

//CODIGO POR JPCOSEANI
//SCRIPT QUE PROCESA LOS DATOS DEL FORM NUEVA NOTIFICACION

global $ari;
$ari->popup = 1;

//ARRAY PARA LOS RESULTADO
$resultado = array();
$resultado["errors"] = array();
$resultado["success"] = false;

require_once( $ari->get('enginedir') . DIRECTORY_SEPARATOR . 'librerias' . DIRECTORY_SEPARATOR . 'mimemessage' . DIRECTORY_SEPARATOR . 'smtp.php' );
require_once( $ari->get('enginedir') . DIRECTORY_SEPARATOR . 'librerias' . DIRECTORY_SEPARATOR . 'mimemessage' . DIRECTORY_SEPARATOR . 'email_message.php' );
require_once( $ari->get('enginedir') . DIRECTORY_SEPARATOR . 'librerias' . DIRECTORY_SEPARATOR . 'mimemessage' . DIRECTORY_SEPARATOR . 'smtp_message.php' );

//estas dos referencias las agregue yo  por que me las pedia mi smtp
require_once( $ari->get('enginedir') . DIRECTORY_SEPARATOR . 'librerias' . DIRECTORY_SEPARATOR . 'sasl' . DIRECTORY_SEPARATOR . 'sasl.php' );
require_once( $ari->get('enginedir') . DIRECTORY_SEPARATOR . 'librerias' . DIRECTORY_SEPARATOR . 'sasl' . DIRECTORY_SEPARATOR . 'login_sasl_client.php' );
require_once( $ari->get('enginedir') . DIRECTORY_SEPARATOR . 'librerias' . DIRECTORY_SEPARATOR . 'sasl' . DIRECTORY_SEPARATOR . 'cram_md5_sasl_client.php' );


//CREO EL OBJETO
$notificacion = new contactos_notificacion();

//PASO LOS DATOS DEL FORMULARIO A VARIABLES 
	
	//titulo
	$titulo = '';
	if( isset( $_POST['txt_titulo'] ) ){		
		$titulo = $_POST['txt_titulo'] ;
		
		if( empty($_POST['txt_titulo']) ){
			$notificacion->error()->addError('NO_TITULO');
		}
		
	}
	else
	{	
		throw new OOB_Exception_400("La variable [txt_titulo] no esta definida");
	}	

	
	//fecha
	$fecha = '';
	if( isset( $_POST['fecha'] ) ){
		$fecha = new Date( date('Y-m-d', strtotime( $_POST['fecha'] ) ) );
	}else
	{
		throw new OOB_Exception_400("La variable [fecha] no esta definida");
	}
	
		
	//mensaje
	$mensaje = '';
	if( isset( $_POST['txt_mensaje'] ) ){		
		$mensaje = $_POST['txt_mensaje'];
		
		if( empty($_POST['txt_mensaje']) ){
			$notificacion->error()->addError('NO_MENSAJE');
		}
	}
	else
	{	
		throw new OOB_Exception_400("La variable [txt_mensaje] no esta definida");
	}
	
	//comunicacion
	$comunicacion = 0;
	if( isset($_POST['iscomunicacion']) )
	{	
		if( $_POST['iscomunicacion'] == 'true' )
		{
			$comunicacion = 1;				
		}
	}
	else
	{	
		throw new OOB_Exception_400("La variable [iscomunicacion] no esta definida");
	}
	
	//envio
	$envio = false;
	if( isset( $_POST['issendmail'] ) )
	{	
		if ($_POST['issendmail'] == 'true')
		{
			$envio = true;				
		}
	}
	else
	{	
		throw new OOB_Exception_400("La variable [issendmail] no esta definida");
	}
	
	//receptor
	$receptor = '';
	if( isset( $_POST['txt_receptor'] ) ){		
		$receptor = $_POST['txt_receptor'];
	}
	else
	{	
		throw new OOB_Exception_400("La variable [txt_receptor] no esta definida");
	}
		
	//resultado
	$respondio = '';
	if( isset( $_POST['txt_resultado'] ) ){		
		$respondio = $_POST['txt_resultado'];
	}
	else
	{	
		throw new OOB_Exception_400("La variable [txt_resultado] no esta definida");
	}
	
	//SETEO LOS VALORES	
	$notificacion->set('titulo', $titulo );
	$notificacion->set('fecha', $fecha );
	$notificacion->set('mensaje', $mensaje );
	$notificacion->set('iscomunicacion', (($comunicacion)?1:0) );
	$notificacion->set('sendmail', (($envio)?1:0) );
	$notificacion->set('receptor', $receptor );
	$notificacion->set('resultado', $respondio );	
	
	if( isset($_POST['contactos']) ){
		if( empty($_POST['contactos']) ){
				$notificacion->error()->addError('NO_CONTACTO');
		}
	}else{
		throw new OOB_Exception_400("La variable [contactos] no esta definida");
	}	
		
	if( $notificacion->store() ){		
	
			if( isset($_POST['contactos']) ){
					
					if( empty($_POST['contactos']) ){
						$notificacion->error()->addError('NO_CONTACTO');
					}
					else
					{
					
					//CONTACTOS
					$contactos = split("," , $_POST['contactos']);
												
					foreach ($contactos as $contacto)
					{
						if( $obj_contacto = new contactos_contacto($contacto) ){
							$obj_relacion = new contactos_contacto_notificacion();
							$obj_relacion->set( 'contacto', $obj_contacto );
							$obj_relacion->set( 'notificacion', $notificacion );
							$obj_relacion->store();
							
							//ENVIO DE EMAILS
							if($envio){							
							
									$modulo = new OOB_module('contactos'); 	
									$template_dir = $modulo->admintpldir() . DIRECTORY_SEPARATOR . 'notificacion.tpl';
									
									$titulo = $notificacion->get('titulo');
									$ari->t->assign( 'mensaje',  $notificacion->get('mensaje') ); 
									$html =  $ari->t->fetch( $template_dir );
									

									$email_message = new smtp_message_class;		
									$email_message->smtp_host = $ari->config->get('delivery', 'main'); 	
									$email_message->smtp_user = $ari->config->get( 'smtpuser', 'main');  
									$email_message->smtp_password =  $ari->config->get( 'smtppass', 'main'); 	
									$email_message->smtp_debug = 0;
									$email_message->smtp_html_debug = 0;
									
									//filtro por contacto
									$filtros2[] = array("field"=>"contacto","type"=>"list","value"=>$obj_contacto->id());	
									$filtros2[] = array("field"=>"status","type"=>"list","value"=>"1");	
										
									//ARMO UN ARRAY CON LOS EMAILS
									$emails = array();	
									if( $lista_emails = contactos_medios_contacto::getFilteredList( false, false, false, false, $filtros2 ) ){
											foreach( $lista_emails as $email ){
												
											
								
									$email_message->SetEncodedEmailHeader( "To", $email->get('direccion') ,'"'. $obj_contacto->name() .'" <' . $email->get('direccion') .'>'); 
									
									$from_address = $ari->config->get('email', 'main'); 
									$from_name = $ari->config->get('name', 'main');
									
									$email_message->SetEncodedEmailHeader( "From", $from_address,'"'.$from_name.'" <' . $from_address .'>'); 
									$email_message->SetEncodedHeader( "Subject", $titulo);
									$email_message->AddQuotedPrintableHTMLPart( $html );
									
									$email_message->Send();										
																		
									}					
								}						
							}//end envio emails
						}						
					}			

					}		
						
				}else{
					throw new OOB_Exception_400("La variable [contactos] no esta definida");
				}				
				
			$resultado["success"] = true;
	}
	




//ERRORES
if ( $errores = $notificacion->error()->getErrors() )
{

    
   $error_codes = array();
   $error_codes['NO_TITULO'] = array( "id"=>"txt_titulo" , "msg"=>"Debe ingresar un titulo" );
   $error_codes['NO_MENSAJE'] = array( "id"=>"txt_mensaje" , "msg"=>"Debe ingresar un mensaje" );
   $error_codes['NO_RECEPTOR'] = array( "id"=>"txt_receptor" , "msg"=>"Debe seleccionar el servicio" );
   $error_codes['NO_RESULTADO'] = array( "id"=>"txt_resultado" , "msg"=>"Debe seleccionar la Moneda" );   
   $error_codes['NO_FECHA'] = array( "id"=>"fecha" , "msg"=>"Debe seleccionar la fecha" );      
   $error_codes['NO_CONTACTO'] = array( "id"=>"contactos" , "msg"=>"Debe seleccionar un contacto" );    
  
  
   
   foreach ($errores as $error){
		$resultado["errors"][] = $error_codes[$error];		
   }   

}	

//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $resultado );
$obj_comunication->send(true,true);

?>