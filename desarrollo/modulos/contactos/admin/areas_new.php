<?php
#OOB/N1 Framework [2008 - Nutus] - PM 

// CODIGO POR JPCOSEANI
// Script que genera el FORM NUEVA AREA


//LIBRERIAS
PhpExt_Javascript::sendContentType();



global $ari;
$ari->popup = 1; // no mostrar el main_frame 
$field_width = 180;//ancho de los controles

$grid_id = '';

	if( isset( $_POST['gid'] ) )
	{
		$grid_id = $_POST['gid'];
	}

//CREACION DE CONTROLES

//NOMBRE
$txt_nombre  = PhpExt_Form_TextField::createTextField("txt_nombre","Nombre")
			       ->setWidth($field_width)
				   ->setMsgTarget(PhpExt_Form_FormPanel::MSG_TARGET_SIDE);			
//DESCRIPCION
$txt_descripcion  = PhpExt_Form_TextField::createTextField("txt_descripcion","Descripci&oacute;n")
			    ->setWidth($field_width)
				->setMsgTarget(PhpExt_Form_FormPanel::MSG_TARGET_SIDE);
    

//BOTON GRABAR ONCLICK			
$handler_save="

function(){
this.findParentByType('form').getForm().submit(
	{      
	   waitMsg : 'Enviando Datos',
		 reset : true,
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
$frm_new_area = new PhpExt_Form_FormPanel();
$frm_new_area->setErrorReader($error_reader)		 		 		 
			 ->setFrame(true)
			 ->setWidth(350)
			 ->setUrl( "/contactos/areas/new_process" )
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
$frm_new_area->addItem($fielset);

//SE AGREGAN LOS BOTONES AL FORMULARIO
$frm_new_area->addButton( $save_button );
$frm_new_area->addButton( PhpExt_Button::createTextButton("Cancelar") );

//RESULTADO
$obj_comunication = new OOB_ext_comunication();
$obj_comunication->set_data( $frm_new_area->getJavascript( false, "contenido" ) );
$obj_comunication->send(true);

?>