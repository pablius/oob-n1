<?php
#OOB/N1 Framework [2008 - Nutus] - PM 

// Código por JPCOSEANI
// Script que genera el FORM BORRAR CACHE

if (!seguridad :: isAllowed(seguridad_action :: nameConstructor('cache','config','admin')) )
{
	throw new OOB_exception("Acceso Denegado", "403", "Acceso Denegado. Consulte con su Administrador!", true);
}  

//LIBRERIAS 

include_once 'PhpExt/Javascript.php';
PhpExt_Javascript::sendContentType();

include_once 'PhpExt/Ext.php';
include_once 'PhpExt/Button.php';
include_once 'PhpExt/Form/FormPanel.php';
include_once 'PhpExt/Form/Label.php';

global $ari;
$ari->popup = 1; // no mostrar el main_frame 

//BOTON BORRAR ONCLICK		
$handler_clear="function(){
		
	Ext.MessageBox.confirm('Emporika', 'Esta seguro que desea borrar la cache?',	
	
	function(e){
	if( e == 'yes'){		
	 this.findParentByType('form').getForm().submit({    	     
	 waitMsg:'Borrando cache..',	       
	 waitTitle:'Emporika'
	}
	);
	
					}
					
			},this);		
	
						}";	

						  

//CREACION DE CONTROLES

//LABEL							  
$lbl_borrar= new PhpExt_Form_Label();								
$lbl_borrar->setText("Al presionar el boton usted borrara la cache del sistema");
					  
//BOTON BORRAR
$clear_button=PhpExt_Button::createTextButton("Borrar",new PhpExt_JavascriptStm($handler_clear));			

//FORMULARIO
$frm_borrar_cache = new PhpExt_Form_FormPanel();
$frm_borrar_cache->setFrame(true)
				 ->setWidth(350)
				 ->setUrl("/admin/config/cache_process")				 
				 ->setAutoHeight(true)			  
				 ->setTitle("Borrar Cache")			  
				 ->setMethod(PhpExt_Form_FormPanel::METHOD_POST);

//AGREGO LOS CONTROLES				 
$frm_borrar_cache->addItem($lbl_borrar);
$frm_borrar_cache->addButton($clear_button);


//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $frm_borrar_cache->getJavascript(false,"contenido") );
$obj_comunication->send(true);	

?>

