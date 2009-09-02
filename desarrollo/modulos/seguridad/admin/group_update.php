<?php
#OOB/N1 Framework [2008 - Nutus] - PM 

// Código por JPCOSEANI
// Script que genera el FORM UPDATE GROUP

if (!seguridad :: isAllowed(seguridad_action :: nameConstructor('update','group','seguridad')) )
{
	throw new OOB_exception("Acceso Denegado", "403", "Acceso Denegado. Consulte con su Administrador!", true);
}  
 

include_once 'PhpExt/Javascript.php';


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
include_once 'PhpExt/Data/FieldConfigObject.php';
include_once 'PhpExtUx/Multiselect/Itemselector.php';
include_once 'PhpExt/Data/JsonStore.php';
include_once 'PhpExtUx/App/SearchField.php';


global $ari;
$ari->popup = 1; // no mostrar el main_frame 

if( isset($_POST['id']) ){
	$grupo = new seguridad_group( $_POST['id'] );
}else{
	throw new OOB_Exception_400("La variable [id] no esta definida");
}

$field_width = 180;//ancho de los controles
PhpExt_Javascript::sendContentType();
$grid_id = '';

	if( isset( $_POST['gid'] ) )
	{
		$grid_id = $_POST['gid'];
	}

$page_size = PAGE_SIZE;

//controles

//nombre del grupo
$txt_nombre  = PhpExt_Form_TextField::createTextField( "txt_nombre", "Nombre" )
			    ->setWidth($field_width)
			    ->setMsgTarget(PhpExt_Form_FormPanel::MSG_TARGET_SIDE)
				->setValue($grupo->get("name"));	
				
//descripcion del grupo	
$txt_descripcion = PhpExt_Form_TextArea::createTextArea("txt_descripcion","Descripci&oacute;n")
			     ->setMsgTarget(PhpExt_Form_FormPanel::MSG_TARGET_SIDE)
			     ->setWidth($field_width)
				 ->setValue($grupo->get("description"));
		
$from_store = new PhpExt_Data_JsonStore();
$from_store->setUrl("/seguridad/group/get_nomembers")	  
			->setRoot("topics")			
			->setBaseParams(array("id"=>$grupo->get("group")))
			->setTotalProperty("totalCount");
     
	  
$from_store->addField(new PhpExt_Data_FieldConfigObject("id","id"));
$from_store->addField(new PhpExt_Data_FieldConfigObject("uname","uname"));


$to_store = new PhpExt_Data_JsonStore();
$to_store->setUrl("/seguridad/group/get_members")	  
			->setRoot("topics")
			->setAutoLoad("true")
			->setBaseParams( array("id"=>$grupo->get("group") ) )
			->setTotalProperty("totalCount");
     
	  
$to_store->addField(new PhpExt_Data_FieldConfigObject( "id", "id" ));
$to_store->addField(new PhpExt_Data_FieldConfigObject( "uname", "uname" ));



//TXT DE BUSQUEDA DE USUARIOS
$txt_buscar_usuario = new PhpExtUx_App_SearchField();
$txt_buscar_usuario->setFieldLabel("Buscar")
				   ->setStore($from_store);
					


//control para seleccionar los usuarios				 
$select = new PhpExtUx_Itemselector();	
$select->setName("usuarios")	   
	   ->setFieldLabel("Usuarios")
	   ->setToLegend("Miembros")
	   ->setFromLegend("No Miembros")
	   ->setvalueField("id")
	   ->setdisplayField("uname")
	   ->setmsHeight(160)
	   ->setmsWidth($field_width)
	   ->setToStore($to_store)
	   ->setFromStore($from_store)	   
	   ->setdataFields(PhpExt_Javascript::variable('["id", "uname"]'))
	   ->setimagePath("/scripts/ext/resources/extjs-ux/multiselect/");
	   
//Boton actualizar OnClick			
$handler_actualizar="function(){
this.findParentByType('form').getForm().submit(
	{    
			    url : '/seguridad/group/update_process',
		    waitMsg : 'Enviando Datos',
   		  waitTitle :'Emporika',
 		success_msg : 'Grupo guardado correctamente',
	grid_reload_id  : '{$grid_id}' 
	}
	);				
							  }";			

$btn_actualizar = PhpExt_Button::createTextButton("Actualizar",new PhpExt_JavascriptStm($handler_actualizar));			
			


//Data_Reader para leer los resultados devueltos 
$error_reader= new PhpExt_Data_JsonReader();
$error_reader->setRoot("errors");
$error_reader->setSuccessProperty("success");
$error_reader->addField(new PhpExt_Data_FieldConfigObject("id"));
$error_reader->addField(new PhpExt_Data_FieldConfigObject("msg")); 


$id_grupo=array();
$id_grupo["id"]=$grupo->get("group");

//formulario que contiene todos los controles
$frm_update_group = new PhpExt_Form_FormPanel();
$frm_update_group->setErrorReader($error_reader)		 		 		 
		         ->setBaseParams($id_grupo)
			     ->setFrame(true)
			     ->setWidth(520)
			     ->setAutoHeight(true)			  
			     ->setTitle("Datos del Grupo")			  
		         ->setMethod(PhpExt_Form_FormPanel::METHOD_POST);
				 
		      

	 
//marco para contenener los controles
$marco= new PhpExt_Form_FieldSet();	
$marco->setAutoHeight(true);
  

//agrego todos los controles al marco
$marco->addItem($txt_nombre);
$marco->addItem($txt_descripcion);
$marco->addItem($txt_buscar_usuario);
$marco->addItem($select);


$frm_update_group->addButton($btn_actualizar);
$frm_update_group->addButton(PhpExt_Button::createTextButton("Cancelar"));

$frm_update_group->addItem($marco);

$resultado = '';
$resultado.= $to_store->getJavascript(false,"tostore");
$resultado.= $from_store->getJavascript(false,"fromstore");
$resultado.= $frm_update_group->getJavascript(false,"contenido");

//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $resultado );
$obj_comunication->send(true);

?>
