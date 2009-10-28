<?php
#OOB/N1 Framework [2008 - Nutus] - PM 

// Código por JPCOSEANI
// Script que genera el FORM NUEVO USUARIO

if (!seguridad :: isAllowed(seguridad_action :: nameConstructor('new','user','seguridad')) )
{
	throw new OOB_exception("Acceso Denegado", "403", "Acceso Denegado. Consulte con su Administrador!", true);
}  
 

include_once 'PhpExt/Javascript.php';
PhpExt_Javascript::sendContentType();

include_once 'PhpExt/Ext.php';
include_once 'PhpExt/Button.php';
include_once 'PhpExt/TabPanel.php';
include_once 'PhpExt/Form/FormPanel.php';
include_once 'PhpExt/Form/TextField.php';
include_once 'PhpExt/Form/FieldSet.php';
include_once 'PhpExt/Form/PasswordField.php';
include_once 'PhpExt/Form/ComboBox.php';
include_once 'PhpExt/Layout/FormLayout.php';
include_once 'PhpExt/Data/SimpleStore.php';
include_once 'PhpExt/Data/JsonReader.php';
include_once 'PhpExt/Panel.php';
include_once 'PhpExt/Layout/AbsoluteLayout.php';
include_once 'PhpExt/Layout/FormLayoutData.php';
include_once 'PhpExt/Layout/AnchorLayoutData.php';
include_once 'PhpExt/Layout/FormLayout.php';
include_once 'PhpExt/Data/FieldConfigObject.php';

global $ari;
$ari->popup = 1; // no mostrar el main_frame 

$field_width = 180;//ancho de los controles

$grid_id = '';

	if( isset( $_POST['gid'] ) )
	{
		$grid_id = $_POST['gid'];
	}


//creacion de controles

//usuario
$txt_usuario  = PhpExt_Form_TextField::createTextField( "txt_usuario", "Usuario" )
			    ->setWidth($field_width)
			    ->setMsgTarget(PhpExt_Form_FormPanel::MSG_TARGET_SIDE);			
//password			  
$txt_password = PhpExt_Form_PasswordField::createPasswordField("txt_pass","Contrase&ntilde;a")
			     ->setMsgTarget(PhpExt_Form_FormPanel::MSG_TARGET_SIDE)
			     ->setWidth($field_width);
//repetir password				
$txt_repetir = PhpExt_Form_PasswordField::createPasswordField("txt_repetir","Repetir")
			    ->setMsgTarget(PhpExt_Form_FormPanel::MSG_TARGET_SIDE)
			    ->setWidth($field_width);
//e-mail
$txt_email   =  PhpExt_Form_TextField::createTextField("txt_email","E-mail", null, PhpExt_Form_FormPanel::VTYPE_EMAIL)
			    ->setWidth($field_width)
			    ->setMsgTarget(PhpExt_Form_FormPanel::MSG_TARGET_SIDE);
			    

//Boton actualizar OnClick			
$handler_grabar="
function(){

this.findParentByType('form').getForm().submit(
	{    
			reset : true,
		  waitMsg : 'Enviando Datos',
		waitTitle : 'Emporika',
	  success_msg : 'Usuario creado correctamente',
  grid_reload_id  : '{$grid_id}'				 	   
	}	
	);				
		}";			

$btn_grabar=PhpExt_Button::createTextButton("Grabar",new PhpExt_JavascriptStm($handler_grabar));			
			


//JSON_Reader para leer los resultados devueltos 
$error_reader= new PhpExt_Data_JsonReader();
$error_reader->setRoot("errors");
$error_reader->setSuccessProperty("success");
$error_reader->addField(new PhpExt_Data_FieldConfigObject("id"));
$error_reader->addField(new PhpExt_Data_FieldConfigObject("msg")); 


//formulario que contiene todos los controles
$frm_nuevo_usuario = new PhpExt_Form_FormPanel();
$frm_nuevo_usuario->setUrl("/seguridad/user/new_process")		     
				  ->setErrorReader($error_reader)		 		 		 
				  ->setFrame(true)
				  ->setWidth(350)
				  ->setAutoHeight(true)			  
				  ->setTitle("Datos del usuario")			  
				  ->setMethod(PhpExt_Form_FormPanel::METHOD_POST);
	 
//marco para contenener los controles
$marco= new PhpExt_Form_FieldSet();	
$marco->setDefaults(new PhpExt_Config_ConfigObject(array("width"=>210)))		  
	  ->setAutoHeight(true);

//se agregan todos los controles al marco
$marco->addItem($txt_usuario);
$marco->addItem($txt_password);
$marco->addItem($txt_repetir);
$marco->addItem($txt_email);

//se agrega el marco al formulario
$frm_nuevo_usuario->addItem($marco);

//se agregan los botones al formulario
$frm_nuevo_usuario->addButton($btn_grabar);
$frm_nuevo_usuario->addButton(PhpExt_Button::createTextButton("Cancelar"));

//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $frm_nuevo_usuario->getJavascript(false,"contenido") );
$obj_comunication->send(true);

?>