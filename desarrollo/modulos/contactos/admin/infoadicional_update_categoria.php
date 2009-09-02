<?php

PhpExt_Javascript::sendContentType();

global $ari;
$ari->popup = 1; // no mostrar el main_frame 

//FORMULARIO
$frm_update_categoria = new PhpExt_Form_FormPanel();
$frm_update_categoria->setMethod( PhpExt_Form_FormPanel::METHOD_POST )
					 ->setHeight(400)
					 ->setWidth(300);



//MARCO PARA CONTENER LOS CONTROLES
$fieldset = new PhpExt_Form_FieldSet();	
$fieldset->setBorder(false)		 		 
	     ->setAutoHeight(true);
		 
//TOTAL
$txt_total = new PhpExt_Form_TextField();
$txt_total->setFieldLabel("Total")		  
		  ->setName("txt_total")		  
		  ->setReadOnly(true)		  		  
		  ->setWidth(100);		 

$fieldset->addItem($txt_total);

//AGREGO EL MARCO AL FORM
$frm_update_categoria->addItem( $fieldset );					 



//RESULTADO
echo $frm_update_categoria->getJavascript( false,"contenido" );


?>
