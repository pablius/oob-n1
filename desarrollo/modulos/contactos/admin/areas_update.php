<?php
#OOB/N1 Framework [2008 - Nutus] - PM 

// CODIGO POR JPCOSEANI
// Script que genera el FORM MODIFICAR AREA

//LIBRERIAS


global $ari;
$ari->popup = 1; // no mostrar el main_frame 
$field_width = 180;//ancho de los controles

$grid_id = '';

	if( isset( $_POST['gid'] ) )
	{
		$grid_id = $_POST['gid'];
	}
	
$area = '';
	if ( isset($_POST['id']) ){
		$id = $_POST['id'];
		$area = new contactos_areas( $_POST['id'] );
	}else{
				throw new OOB_Exception_404("La variable [id] no esta definida");	
	}	
PhpExt_Javascript::sendContentType();
//CREACION DE CONTROLES

//NOMBRE
$txt_nombre  = PhpExt_Form_TextField::createTextField("txt_nombre","Nombre")
			       ->setWidth($field_width)
				   ->setValue($area->get('nombre'))
				   ->setMsgTarget(PhpExt_Form_FormPanel::MSG_TARGET_SIDE);			
//DESCRIPCION
$txt_descripcion  = PhpExt_Form_TextField::createTextField("txt_descripcion","Descripci&oacute;n")
			    ->setWidth($field_width)
				->setValue($area->get('descripcion'))
				->setMsgTarget(PhpExt_Form_FormPanel::MSG_TARGET_SIDE);
    

//BOTON GRABAR ONCLICK			
$handler_save="

function(){
this.findParentByType('form').getForm().submit(
	{      
	   waitMsg : 'Enviando Datos',		
	 waitTitle : 'Emporika',
	   success : function() { 
	   
					 var grid =  Ext.getCmp('{$grid_id}');
					 if(grid){
						var tb = grid.getBottomToolbar();					
						tb.doLoad(tb.cursor);					
					 }
					 
					 Ext.MessageBox.alert('Emporika','Datos guardados correctamente'); 
					 
				 }
	 
	}
	);				
}

";			

//CREACION DEL BOTON GRABAR							  
$save_button=PhpExt_Button::createTextButton( "Grabar", new PhpExt_JavascriptStm($handler_save) );			
			
//JSON_Reader PARA LEER LOS DATOS DEVUELTOS
$error_reader= new PhpExt_Data_JsonReader();
$error_reader->setRoot("errors");
$error_reader->setSuccessProperty("success");
$error_reader->addField( new PhpExt_Data_FieldConfigObject("id") );
$error_reader->addField( new PhpExt_Data_FieldConfigObject("msg") ); 


//FORMULARIO
$frm_update_area = new PhpExt_Form_FormPanel();
$frm_update_area->setErrorReader($error_reader)		 		 		 
			 ->setFrame(true)
			 ->setWidth(350)
			 ->setBaseParams( array( "id" => $id ) )
			 ->setUrl( "/contactos/areas/update_process" )
			 ->setAutoHeight(true)			  
			 ->setTitle( "Datos del area" )			  
			 ->setMethod(PhpExt_Form_FormPanel::METHOD_POST);
	 
//MARCO PARA CONTENER LOS CONTROLES
$fielset= new PhpExt_Form_FieldSet();	
$fielset->setDefaults( new PhpExt_Config_ConfigObject( array( "width" => 210 ) ) )		  
	  ->setAutoHeight(true);

//SE AGREGAN LOS CONTROLES AL MARCO
$fielset->addItem( $txt_nombre );
$fielset->addItem( $txt_descripcion );

//SE AGREGA EL MARCO AL FORMULARIO
$frm_update_area->addItem($fielset);

//SE AGREGAN LOS BOTONES AL FORMULARIO
$frm_update_area->addButton( $save_button );
$frm_update_area->addButton( PhpExt_Button::createTextButton("Cancelar") );

//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $frm_update_area->getJavascript( false, "contenido" ) );
$obj_comunication->send(true);

?>