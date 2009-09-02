<?php
#OOB/N1 Framework [2008 - Nutus] - PM 

// Código por JPCOSEANI
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
include_once 'PhpExt/Element.php';
include_once 'PhpExt/Form/Hidden.php';
include_once 'PhpExt/Form/Checkbox.php';
include_once 'PhpExtUx/App/SearchField.php';

global $ari;
$ari->popup = 1; // no mostrar el main_frame 

$grid_id = '';
$role = '';

if( isset($_POST['id']) ){
	$role = new seguridad_role( $_POST['id'] );
}else{
	throw new OOB_Exception_400("La variable [id] no esta definida");
}

PhpExt_Javascript::sendContentType();

if( isset($_POST['gid']) ){
	$grid_id = $_POST['gid'];
}

$field_width = 180;//ancho de los controles

$page_size = PAGE_SIZE;

//CONTROLES

//nombre del role
$txt_nombre  = PhpExt_Form_TextField::createTextField("txt_nombre","Nombre")
			    ->setWidth($field_width)
			    ->setMsgTarget(PhpExt_Form_FormPanel::MSG_TARGET_SIDE)
				->setValue($role->get("name"));	
				
//descripcion del role	
$txt_descripcion = PhpExt_Form_TextArea::createTextArea("txt_descripcion","Descripci&oacute;n")
			     ->setMsgTarget(PhpExt_Form_FormPanel::MSG_TARGET_SIDE)
			     ->setWidth($field_width)
				 ->setValue($role->get("description"));
				 
//usuario anonimo				 
$chk_anonimo = PhpExt_Form_Checkbox::createCheckbox( "chk_anonimo", "An&oacute;nimo" )
									 ->setMsgTarget( PhpExt_Form_FormPanel::MSG_TARGET_SIDE )
									 ->setWidth($field_width);	

if ($role->get("anonymous") == ANONIMO){				 
	$chk_anonimo->setChecked(true);	
}						 				 
 
//usuario confiado
$chk_confiados = PhpExt_Form_Checkbox::createCheckbox("chk_confiados","Confiado")
			     ->setMsgTarget(PhpExt_Form_FormPanel::MSG_TARGET_SIDE)
			     ->setWidth($field_width);			

if( $role->get("trustees") == YES ){				 
	$chk_confiados->setChecked(true);	
}						 

$tab_panel = new PhpExt_TabPanel();
$tab_panel->setPlain(true)
          ->setActiveTab(0)
          ->setHeight(300)	
		  ->setWidth(550)
		  ->setDefaults(new PhpExt_Config_ConfigObject( array( "bodyStyle" => "padding:10px" )) );
				 
				 
		
$usuarios_from_store = new PhpExt_Data_JsonStore();
$usuarios_from_store->setUrl("/seguridad/role/get_users_nomembers")	  
			->setRoot("topics")			
			->setBaseParams(array("id"=>$role->get("role")))
			->setTotalProperty("totalCount");
     
	  
$usuarios_from_store->addField( new PhpExt_Data_FieldConfigObject("id","id") );
$usuarios_from_store->addField( new PhpExt_Data_FieldConfigObject("uname","uname") );


$usuarios_to_store = new PhpExt_Data_JsonStore();
$usuarios_to_store->setUrl("/seguridad/role/get_users_members")	  
				  ->setRoot("topics")
				  ->setAutoLoad("true")
				  ->setBaseParams(array("id"=>$role->get("role")))
				  ->setTotalProperty("totalCount");
     
	  
$usuarios_to_store->addField( new PhpExt_Data_FieldConfigObject( "id", "id" ) );
$usuarios_to_store->addField( new PhpExt_Data_FieldConfigObject( "uname", "uname" ) );


$tab_general = new PhpExt_Panel();
$tab_general->setTitle("General")
			 ->setLayout(new PhpExt_Layout_FormLayout());          
			 
$tab_general->addItem($txt_nombre);
$tab_general->addItem($txt_descripcion);
$tab_general->addItem($chk_anonimo);
$tab_general->addItem($chk_confiados);			 
$tab_panel->addItem($tab_general);

$tab_usuarios = new PhpExt_Panel();
$tab_usuarios->setTitle("Usuarios")
			 ->setLayout( new PhpExt_Layout_FormLayout() );          

if( $role->get("anonymous")  == ANONIMO ){
	$tab_usuarios->setDisabled( true );
}

//TXT DE BUSQUEDA DE USUARIOS
$txt_buscar_usuario = new PhpExtUx_App_SearchField();
$txt_buscar_usuario->setFieldLabel("Buscar")
				   ->setStore($usuarios_from_store);

//control para seleccionar los usuarios				 
$select_usuarios = new PhpExtUx_Itemselector();	
$select_usuarios->setName("usuarios")						
				->setFieldLabel("Usuarios")
				->setToLegend("Miembros")
				->setFromLegend("No Miembros")
				->setvalueField("id")
				->setdisplayField("uname")
				->setmsHeight(230)
				->setmsWidth($field_width)
				->setToStore($usuarios_to_store)
				->setFromStore($usuarios_from_store)	   
				->setdataFields(PhpExt_Javascript::variable('["id", "uname"]'))
				->setimagePath("/scripts/ext/resources/extjs-ux/multiselect/");

$tab_usuarios->addItem($txt_buscar_usuario);
$tab_usuarios->addItem($select_usuarios);
$tab_panel->addItem($tab_usuarios);

$tab_grupos = new PhpExt_Panel();
$tab_grupos->setTitle("Grupos")
			->setLayout(new PhpExt_Layout_FormLayout());
			
if( $role->get("anonymous")  == ANONIMO ){
	$tab_grupos->setDisabled( true );
}			
			

$grupos_from_store = new PhpExt_Data_JsonStore();
$grupos_from_store->setUrl("/seguridad/role/get_groups_nomembers")	  
				  ->setRoot("topics")
				  ->setAutoLoad("true")
				  ->setBaseParams(array("id"=>$role->get("role")))
				  ->setTotalProperty("totalCount");
     
	  
$grupos_from_store->addField(new PhpExt_Data_FieldConfigObject("id","id"));
$grupos_from_store->addField(new PhpExt_Data_FieldConfigObject("uname","uname"));


$grupos_to_store = new PhpExt_Data_JsonStore();
$grupos_to_store->setUrl("/seguridad/role/get_groups_members")	  
		   	    ->setRoot("topics")
				->setAutoLoad("true")	
				->setBaseParams(array("id"=>$role->get("role")))
				->setTotalProperty("totalCount");
     
	  
$grupos_to_store->addField(new PhpExt_Data_FieldConfigObject("id","id"));
$grupos_to_store->addField(new PhpExt_Data_FieldConfigObject("uname","uname"));			 

//control para seleccionar los grupos				 
$select_grupos = new PhpExtUx_Itemselector();	
$select_grupos->setName("grupos")			  
			  ->setFieldLabel("Grupos")
			  ->setToLegend("Miembros")
			  ->setFromLegend("No Miembros")
			  ->setvalueField("id")
			  ->setdisplayField("uname")
			  ->setmsHeight(260)
			  ->setmsWidth($field_width)
			  ->setToStore($grupos_to_store)
			  ->setFromStore($grupos_from_store)	   
			  ->setdataFields(PhpExt_Javascript::variable('["id", "uname"]'))
			  ->setimagePath("/scripts/ext/resources/extjs-ux/multiselect/");	

$tab_grupos->addItem($select_grupos);			 
			 
$tab_panel->addItem($tab_grupos);	


$tab_modulos = new PhpExt_Panel();
$tab_modulos->setTitle("Modulos")
			->setLayout(new PhpExt_Layout_FormLayout());  
		 
//trae los items del menu
$tree_loader = new  PhpExt_Tree_TreeLoader();
$tree_loader->setDataUrl("/seguridad/role/get_modules")
		    ->setBaseParams(array("id"=>$role->get("role")));
			

//armo el nodo root (no es visible)
$root = new PhpExt_Tree_AsyncTreeNode();
$root->setText("Principal Node")
     ->setDraggable(false)	 
     ->setId("Principal_Node")
	 ->setExpanded(true)		 
     ->expandChildNodes(true);	

$check_change = "



if( n.isLeaf() ){
	var contador = 0;
		n.bubble(function(c){
			if(c.getUI().checkbox){
				if(!c.isLeaf() ){
					if(n.getUI().checkbox.checked == true ){
						c.getUI().checkbox.checked = true;
					}						
							
							c.eachChild(function(nc){															
								if(!nc.isLeaf()){
									nc.eachChild(function(ncc){													
										if( ncc.getUI().checkbox.checked == true ){												
											contador++;											
										}
									});									
								}								
							});
							if(contador == 0){
										c.getUI().checkbox.checked = false;
							}
										
				}
			}
		});
	

}

if(!n.isLeaf()){
	n.eachChild(function(nc){
	if( n.getUI().checkbox.checked == false ){
		if(!nc.isLeaf()){
			nc.eachChild(function(ncc){				
				ncc.getUI().checkbox.checked = false;
			});
		}
	}	
	});
	
}

var cambios = new Array();
var entro = true;
var cont = this.findParentByType('form').getForm().findField('modulos');


if(cont.getValue()!=''){
var cambios = cont.getValue().split(',');

for( i = 0; i < cambios.length; i++ ){
	if(n.id==cambios[i]){
			cambios[i+1] = c;
			entro = false;
			}
			}
}
		
if(entro){
	cambios.push(n.id);
	cambios.push(c);
}
if( cambios.length >1 ){
	cont.setValue(cambios.join(','));
}

		   ";
	 
		 
$tree_modulos = new PhpExt_Tree_TreePanel();			 
$tree_modulos->setAnimate(true)			 
			 ->setHeight(250)
			 ->setWidth(530)				 
		     ->setRootVisible(false)
		     ->setEnableDd(false)	
			 ->setRoot($root)			 
			 ->setContainerScroll(true)
			 ->setAutoScroll(true)
			 ->setLoader($tree_loader);		
			 
$tree_modulos->attachListener( "checkchange", new PhpExt_Listener(PhpExt_Javascript::functionDef( null, $check_change, array("n,c") ) ));			


	 

$tab_modulos->addItem( $tree_modulos );			 
$tab_panel->addItem( $tab_modulos );
		 
//Boton actualizar OnClick			
$handler_actualizar = "function(){

this.findParentByType('form').getForm().submit(
	{      
			waitMsg : 'Enviando Datos',
		  waitTitle : 'Emporika',
	 	success_msg : 'Rol guardado correctamente',
	grid_reload_id  : '{$grid_id}',
		    success : function(){				
				Ext.getCmp('treePanel').getRootNode().reload();				
		   }
	}
	);	

	}";			

$btn_actualizar = PhpExt_Button::createTextButton("Actualizar",new PhpExt_JavascriptStm($handler_actualizar));			

//Data_Reader para leer los resultados devueltos 
$error_reader = new PhpExt_Data_JsonReader();
$error_reader->setRoot("errors");
$error_reader->setSuccessProperty("success");
$error_reader->addField(new PhpExt_Data_FieldConfigObject("id"));
$error_reader->addField(new PhpExt_Data_FieldConfigObject("msg")); 


$modulos = PhpExt_Form_Hidden::createHidden("modulos");


$id_role=array();
$id_role["id"] = $role->get("role");

//formulario que contiene todos los controles
$frm_update_role = new PhpExt_Form_FormPanel();
$frm_update_role->setErrorReader($error_reader)		 		 		 
		        ->setBaseParams($id_role)
				->setUrl("/seguridad/role/update_process")
			    ->setFrame(true)
			    ->setWidth(580)
			    ->setHeight(400)			  
			    ->setTitle("Datos del rol")			  
		        ->setMethod(PhpExt_Form_FormPanel::METHOD_POST);

$form_render = "
	var chk = form.findBy( function(c){ return ( c.name == 'chk_anonimo' ); } );			
	var panel_usuarios = form.findBy( function(c){ return (c.xtype == 'panel' && c.title == 'Usuarios' ) } );			
	var panel_grupos = form.findBy( function(c){ return (c.xtype == 'panel' && c.title == 'Grupos' ) } );			

	var groups = form.findBy( function(c){ return ( c.name == 'grupos' ) } );			
	var users = form.findBy( function(c){ return ( c.name == 'usuarios' ) } );			
	
	chk[0].on( 'check', function(t,c){
	
	

	if( ( groups[0].toStore.getCount() != 0 ) || ( users[0].toStore.getCount() != 0 ) ){	
		t.setValue(false);
		panel_usuarios[0].setDisabled(false);
		panel_grupos[0].setDisabled(false);
	}
	else
	{
		panel_usuarios[0].setDisabled(c);
		panel_grupos[0].setDisabled(c);
	}	
});

";				
				
$frm_update_role->setEnableKeyEvents(true);
$frm_update_role->attachListener( "render", new PhpExt_Listener( PhpExt_Javascript::functionDef( null, $form_render , array( "form" ) )) );			   		   				
				 
		      

	 
//marco para contenener los controles
$marco= new PhpExt_Form_FieldSet();	
$marco->setAutoHeight(true);
  

//agrego todos los controles al marco
$marco->addItem($tab_panel);
$marco->addItem($modulos);

$frm_update_role->addButton($btn_actualizar);
$frm_update_role->addButton(PhpExt_Button::createTextButton("Cancelar"));

$frm_update_role->addItem($marco);


//RESULTADOS
$resultado = '';
$resultado.= $grupos_to_store->getJavascript(false,"grupos_tostore");
$resultado.= $grupos_from_store->getJavascript(false,"grupos_fromstore");
$resultado.= $usuarios_to_store->getJavascript(false,"usuarios_tostore");
$resultado.= $usuarios_from_store->getJavascript(false,"usuarios_fromstore");
$resultado.= $frm_update_role->getJavascript(false,"contenido");

//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $resultado );
$obj_comunication->send(true);

?>