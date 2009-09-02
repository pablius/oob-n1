<?php
#OOB/N1 Framework [2008 - Nutus] - PM 

// Código por JPC
// Script que genera el FORM UPDATE GROUP

if (!seguridad :: isAllowed(seguridad_action :: nameConstructor('update','role','seguridad')) )
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
include_once 'PhpExt/Tree/TreePanel.php';
include_once 'PhpExt/Tree/AsyncTreeNode.php';
include_once 'PhpExt/Tree/TreeLoader.php';
include_once 'PhpExt/Tree/MultiSelectionModel.php';
include_once 'PhpExt/Tree/TreeNode.php';

global $ari;
$ari->popup = 1; // no mostrar el main_frame 

$field_width=180;//ancho de los controles

if( !isset($_POST['id']) ){
	throw new OOB_Exception_400("La variable [id] no esta definida");
}

PhpExt_Javascript::sendContentType();

$name= $_POST['id'];//NOMBRE DE LA PERSPECTIVA				 

$tab_panel = new PhpExt_TabPanel();
$tab_panel->setPlain(true)
          ->setActiveTab(0)
          ->setHeight(200)		  
		  ->setDefaults(new PhpExt_Config_ConfigObject(array("bodyStyle"=>"padding:10px")));
				 
				 
		
$roles_from_store = new PhpExt_Data_JsonStore();
$roles_from_store->setUrl("/admin/perspective/get_roles_nomembers")	  
				 ->setRoot("topics")
				 ->setAutoLoad("true")
				 ->setBaseParams(array("id"=>$name))
				 ->setTotalProperty("totalCount");
     
	  
$roles_from_store->addField(new PhpExt_Data_FieldConfigObject("id","id"));
$roles_from_store->addField(new PhpExt_Data_FieldConfigObject("name","name"));


$roles_to_store = new PhpExt_Data_JsonStore();
$roles_to_store->setUrl("/admin/perspective/get_roles_members")	  
			   ->setRoot("topics")
			   ->setAutoLoad("true")
			   ->setBaseParams(array("id"=>$name))
			   ->setTotalProperty("totalCount");
     
	  
$roles_to_store->addField(new PhpExt_Data_FieldConfigObject("id","id"));
$roles_to_store->addField(new PhpExt_Data_FieldConfigObject("name","name"));




$tab_roles = new PhpExt_Panel();
$tab_roles->setTitle("Roles")
		  ->setLayout(new PhpExt_Layout_FormLayout());          




//control para seleccionar los usuarios				 
$select_roles = new PhpExtUx_Itemselector();	
$select_roles->setName("roles")
			 ->setId("roles")	
			 ->setFieldLabel("Roles")
			 ->setToLegend("Miembros")
			 ->setFromLegend("No Miembros")
			 ->setvalueField("id")
			 ->setdisplayField("name")
			 ->setmsHeight(160)
			 ->setmsWidth($field_width)
			 ->setToStore($roles_to_store)
			 ->setFromStore($roles_from_store)	   
			 ->setdataFields(PhpExt_Javascript::variable('["id", "name"]'))
			 ->setimagePath("/scripts/ext/resources/extjs-ux/multiselect/");


$tab_roles->addItem($select_roles);
$tab_panel->addItem($tab_roles);

$tab_modulos = new PhpExt_Panel();
$tab_modulos->setTitle("Modulos")
			->setLayout(new PhpExt_Layout_FormLayout());

$modulos_from_store = new PhpExt_Data_JsonStore();
$modulos_from_store->setUrl("/admin/perspective/get_modules_nomembers")	  
				   ->setRoot("topics")
				   ->setAutoLoad("true")
				   ->setBaseParams(array("id"=>$name))
				   ->setTotalProperty("totalCount");
     
	  
$modulos_from_store->addField(new PhpExt_Data_FieldConfigObject("id","id"));
$modulos_from_store->addField(new PhpExt_Data_FieldConfigObject("name","name"));


$modulos_to_store = new PhpExt_Data_JsonStore();
$modulos_to_store->setUrl("/admin/perspective/get_modules_members")	  
		   	     ->setRoot("topics")
				 ->setAutoLoad("true")	
				 ->setBaseParams(array("id"=>$name))
				 ->setTotalProperty("totalCount");
     
	  
$modulos_to_store->addField(new PhpExt_Data_FieldConfigObject("id","id"));
$modulos_to_store->addField(new PhpExt_Data_FieldConfigObject("name","name"));			 

//control para seleccionar los grupos				 
$select_modulos = new PhpExtUx_Itemselector();	
$select_modulos->setName("modulos")
			   ->setId("modulos")	
			   ->setFieldLabel("Modulos")
			   ->setToLegend("Miembros")
			   ->setFromLegend("No Miembros")
			   ->setvalueField("id")
			   ->setdisplayField("name")
			   ->setmsHeight(160)
			   ->setmsWidth($field_width)
			   ->setToStore($modulos_to_store)
			   ->setFromStore($modulos_from_store)	   
			   ->setdataFields(PhpExt_Javascript::variable('["id", "name"]'))
			   ->setimagePath("/scripts/ext/resources/extjs-ux/multiselect/");			 

$tab_modulos->addItem($select_modulos);			 
			 
$tab_panel->addItem($tab_modulos);	

		 
//Boton actualizar OnClick			
$handler_actualizar="function(){
Ext.getCmp('frm_update_perspective').getForm().submit(
	{    
	       url:'/admin/perspective/update_process',
	   waitMsg:'Enviando Datos',
	 waitTitle:'Emporika'
	}
	);				
							  }";			

$btn_actualizar=PhpExt_Button::createTextButton("Actualizar",new PhpExt_JavascriptStm($handler_actualizar));			
			

$id_perspective=array();
$id_perspective["name"]=$name;

//formulario que contiene todos los controles
$frm_update_perspective = new PhpExt_Form_FormPanel();
$frm_update_perspective->setId("frm_update_perspective")		         		 		 
					   ->setBaseParams($id_perspective)
			           ->setFrame(true)
			           ->setWidth(530)
			           ->setAutoHeight(true)			  
			           ->setTitle("Datos de la Perpectiva")			  
		               ->setMethod(PhpExt_Form_FormPanel::METHOD_POST);
				 
$frm_update_perspective->addItem($tab_panel);
$frm_update_perspective->addButton($btn_actualizar);
$frm_update_perspective->addButton(PhpExt_Button::createTextButton("Cancelar"));


$resultado = '';
$resultado.= $roles_to_store->getJavascript(false,"roles_tostore");
$resultado.= $roles_from_store->getJavascript(false,"roles_fromstore");
$resultado.= $modulos_to_store->getJavascript(false,"modulos_tostore");
$resultado.= $modulos_from_store->getJavascript(false,"modulos_fromstore");
$resultado.= $frm_update_perspective->getJavascript(false,"contenido");

//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $resultado );
$obj_comunication->send(true);

?>