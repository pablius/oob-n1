<?php

#OOB/N1 Framework [2008 - Nutus] - PM 

// Código por JPC
// Script que genera el FORM MI CUENTA

if (!seguridad :: isAllowed(seguridad_action :: nameConstructor('update','user','seguridad')) )
{
	throw new OOB_exception("Acceso Denegado", "403", "Acceso Denegado. Consulte con su Administrador!", true);
}  

//LIBRERIAS 

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

global $ari;
$ari->popup = 1; // no mostrar el main_frame 

$usuario = '';
	if ( !isset($_POST['id']) ){
		$usuario = $ari->user;
	}
	else
	{
		$usuario = new oob_user( $_POST['id'] );
	}

$grid_id = '';
$field_width = 180;//ancho de los controles

	if( isset( $_POST['gid'] ) )
	{
		$grid_id = $_POST['gid'];
	}

$page_size = PAGE_SIZE;

$txt_usuario = new PhpExt_Form_TextField();
$txt_usuario->setReadOnly(true)
			->setName("txt_usuario")
			->setFieldLabel("Usuario")
			->setWidth($field_width)
			->setValue($usuario->name());

			   
$txt_password = PhpExt_Form_PasswordField::createPasswordField("txt_pass","Contrase&ntilde;a")
			     ->setMsgTarget(PhpExt_Form_FormPanel::MSG_TARGET_SIDE)
			     ->setWidth($field_width);
				
$txt_repetir = PhpExt_Form_PasswordField::createPasswordField("txt_repetir","Repetir")
			    ->setMsgTarget(PhpExt_Form_FormPanel::MSG_TARGET_SIDE)
			    ->setWidth($field_width);

$txt_email   =  PhpExt_Form_TextField::createTextField("txt_email","Email", null, PhpExt_Form_FormPanel::VTYPE_EMAIL)
			    ->setWidth($field_width)
			    ->setMsgTarget(PhpExt_Form_FormPanel::MSG_TARGET_SIDE)
			    ->setValue($usuario->get("email"));



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
			   ->setDisplayField("descripcion")			
			   ->setEditable(false)	
			   ->setForceSelection(true)			
			   ->setSingleSelect(true)				
			   ->setMode(PhpExt_Form_ComboBox::MODE_LOCAL)
			   ->setTriggerAction(PhpExt_Form_ComboBox::TRIGGER_ACTION_ALL)
			   ->setEmptyText(oob_user::getStatus($usuario->get("status"), true));

//Boton actualizar OnClick			
$handler_actualizar = "
function(){
Ext.getCmp('frm_mi_cuenta').getForm().submit(
	{    
			url : '/seguridad/user/update_process',
		waitMsg : 'Enviando Datos',	
	  waitTitle : 'Emporika',
	success_msg : 'Usuario guardado correctamente',
grid_reload_id  : '{$grid_id}'	  
       
	});
	}	
";			

$btn_actualizar=PhpExt_Button::createTextButton("Actualizar",new PhpExt_JavascriptStm($handler_actualizar));			
			


//Data_Reader para leer los resultados devueltos 
$error_reader= new PhpExt_Data_JsonReader();
$error_reader->setRoot("errors");
$error_reader->setSuccessProperty("success");
$error_reader->addField(new PhpExt_Data_FieldConfigObject("id"));
$error_reader->addField(new PhpExt_Data_FieldConfigObject("msg")); 


$id_usuario=array();
$id_usuario["id"]=$usuario->get("user");

//formulario que contiene todos los controles
$frm_mi_cuenta = new PhpExt_Form_FormPanel();
$frm_mi_cuenta->setId("frm_mi_cuenta")
		      ->setErrorReader($error_reader)		 		 		 
		      ->setBaseParams($id_usuario)
			  ->setFrame(true)
			  ->setWidth(350)
			  ->setAutoHeight(true)			  
			  ->setTitle("Datos del usuario")			  
		      ->setMethod(PhpExt_Form_FormPanel::METHOD_POST);
	 
//marco para contenener los controles
$marco= new PhpExt_Form_FieldSet();	
$marco->setDefaults(new PhpExt_Config_ConfigObject(array("width"=>210)))		  
	  ->setAutoHeight(true);

//agrego todos los controles al marco
$marco->addItem($txt_usuario);
$marco->addItem($txt_password);
$marco->addItem($txt_repetir);
$marco->addItem($txt_email);
$marco->addItem($cbo_estados);


$frm_mi_cuenta->addButton($btn_actualizar);
$frm_mi_cuenta->addButton(PhpExt_Button::createTextButton("Cancelar"));

$frm_mi_cuenta->addItem($marco);

//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $frm_mi_cuenta->getJavascript(false,"contenido") );
$obj_comunication->send(true);

?>