<?php
#OOB/N1 Framework [2008 - Nutus] - PM 

// Código por JPCOSEANI
// Script que genera el FORM NUEVO ROL

if (!seguridad :: isAllowed(seguridad_action :: nameConstructor('new','role','seguridad')) )
{
	throw new OOB_exception("Acceso Denegado", "403", "Acceso Denegado. Consulte con su Administrador!", true);
}  
 

PhpExt_Javascript::sendContentType();


global $ari;
$ari->popup = 1; // no mostrar el main_frame 

$field_width = 180; //ancho de los controles

$grid_id = '';

	if( isset( $_POST['gid'] ) )
	{
		$grid_id = $_POST['gid'];
	}

//creacion de controles
$txt_nombre =  PhpExt_Form_TextField::createTextField("txt_nombre","Nombre")
			   ->setMsgTarget(PhpExt_Form_FormPanel::MSG_TARGET_SIDE)
			   ->setWidth($field_width);		   

$txt_descripcion = PhpExt_Form_TextArea::createTextArea("txt_descripcion","Descripci&oacute;n")
			     ->setMsgTarget(PhpExt_Form_FormPanel::MSG_TARGET_SIDE)
			     ->setWidth($field_width);		
		
$chk_anonimo = PhpExt_Form_Checkbox::createCheckbox("chk_anonimo","An&oacute;nimo")
			     ->setMsgTarget(PhpExt_Form_FormPanel::MSG_TARGET_SIDE)
			     ->setWidth($field_width);				

$chk_confiados = PhpExt_Form_Checkbox::createCheckbox("chk_confiados","Confiado")
			     ->setMsgTarget(PhpExt_Form_FormPanel::MSG_TARGET_SIDE)
			     ->setWidth($field_width);				
				
//Boton grabar OnClick			
$handler_grabar = " 
function(){
this.findParentByType('form').getForm().submit(
	{    	       
						 reset : true,
					   waitMsg : 'Enviando Datos',
					 waitTitle : 'Emporika',				   
			   grid_reload_id  : '{$grid_id}',
				   new_tab_dir : '/seguridad/role/update',
				 new_tab_title : 'Modificar Rol',
  new_tab_pass_response_params : { id : 'id'},  
				 load_tab_here : true	   
	}
	
	);				
	
}";			

$btn_grabar = PhpExt_Button::createTextButton("Grabar",new PhpExt_JavascriptStm($handler_grabar));			
			


//Data_Reader para leer los resultados devueltos 
$error_reader= new PhpExt_Data_JsonReader();
$error_reader->setRoot("errors");
$error_reader->setSuccessProperty("success");
$error_reader->addField(new PhpExt_Data_FieldConfigObject("id"));
$error_reader->addField(new PhpExt_Data_FieldConfigObject("msg")); 



//formulario que contiene todos los controles
$frm_nuevo_rol = new PhpExt_Form_FormPanel();
$frm_nuevo_rol->setErrorReader($error_reader)		 		 		 		      		      
			  ->setUrl("/seguridad/role/new_process")	
			  ->setFrame(true)
			  ->setWidth(350)
			  ->setAutoHeight(true)			  
			  ->setTitle("Datos del Rol")			  
		      ->setMethod(PhpExt_Form_FormPanel::METHOD_POST);
		      

	 
//marco para poner los controles
$marco= new PhpExt_Form_FieldSet();	
$marco->setDefaults(new PhpExt_Config_ConfigObject(array("width"=>210)))		  
	  ->setAutoHeight(true);  

//agrego todos los controles al marco
$marco->addItem($txt_nombre);
$marco->addItem($txt_descripcion);
$marco->addItem($chk_anonimo);
$marco->addItem($chk_confiados);

$frm_nuevo_rol->addItem($marco);

$frm_nuevo_rol->addButton($btn_grabar);
$frm_nuevo_rol->addButton(PhpExt_Button::createTextButton("Cancelar"));

//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $frm_nuevo_rol->getJavascript(false,"contenido") );
$obj_comunication->send(true);

?>

