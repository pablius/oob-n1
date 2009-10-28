<?php
#OOB/N1 Framework [2008 - Nutus] - PM 

// Código por JPCOSEANI
// Script que genera el FORM NUEVO GRUPO

if (!seguridad :: isAllowed(seguridad_action :: nameConstructor('new','group','seguridad')) )
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
include_once 'PhpExt/Form/TextArea.php';
include_once 'PhpExt/Form/Checkbox.php';
include_once 'PhpExt/Data/FieldConfigObject.php';


global $ari;
$ari->popup = 1; // no mostrar el main_frame 
$field_width = 180; //ancho de los controles

$grid_id = '';

	if( isset( $_POST['gid'] ) )
	{
		$grid_id = $_POST['gid'];
	}

//controles

//nombre del grupo
//usuario
$txt_nombre  = PhpExt_Form_TextField::createTextField("txt_nombre","Nombre")
			    ->setWidth($field_width)
			    ->setMsgTarget(PhpExt_Form_FormPanel::MSG_TARGET_SIDE);			
				
//descripcion del grupo	
$txt_descripcion = PhpExt_Form_TextArea::createTextArea("txt_descripcion","Descripci&oacute;n")
			     ->setMsgTarget(PhpExt_Form_FormPanel::MSG_TARGET_SIDE)
			     ->setWidth($field_width);										
		
//Boton GRABAR OnClick			
$handler_grabar="
function(){
this.findParentByType('form').getForm().submit(
	{    
					   waitMsg : 'Enviando Datos...',
						 reset : true,
					 waitTitle : 'Emporika',					   
			   grid_reload_id  : '{$grid_id}',
				   new_tab_dir : '/seguridad/group/update',
				 new_tab_title : 'Modificar Grupo',
  new_tab_pass_response_params : { id : 'id'},  
				 load_tab_here : true	    	 	   
	}
	);				
		}";			

$btn_grabar=PhpExt_Button::createTextButton("Grabar",new PhpExt_JavascriptStm($handler_grabar));			
			


//Data_Reader para leer los resultados devueltos 
$error_reader= new PhpExt_Data_JsonReader();
$error_reader->setRoot("errors");
$error_reader->setSuccessProperty("success");
$error_reader->addField(new PhpExt_Data_FieldConfigObject("id"));
$error_reader->addField(new PhpExt_Data_FieldConfigObject("msg")); 


//formulario que contiene todos los controles
$frm_nuevo_grupo = new PhpExt_Form_FormPanel();
$frm_nuevo_grupo->setId("frm_nuevo_grupo")
				->setFrame(true)
				->setErrorReader($error_reader)		 		 		 
				->setWidth(350)
				->setUrl("/seguridad/group/new_process")
				->setAutoHeight(true)			  
				->setTitle("Nuevo Grupo")			  
				->setMethod(PhpExt_Form_FormPanel::METHOD_POST);
				
	

	 
//marco para contenener los controles
$marco= new PhpExt_Form_FieldSet();	
$marco->setDefaults(new PhpExt_Config_ConfigObject(array("width"=>210)))		  
	  ->setAutoHeight(true);  

//se agregan todos los controles al marco
$marco->addItem($txt_nombre);
$marco->addItem($txt_descripcion);

$frm_nuevo_grupo->addItem($marco);

//se agregan los botones
$frm_nuevo_grupo->addButton($btn_grabar);
$frm_nuevo_grupo->addButton(PhpExt_Button::createTextButton("Cancelar"));

//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $frm_nuevo_grupo->getJavascript(false,"contenido") );
$obj_comunication->send(true);

?>