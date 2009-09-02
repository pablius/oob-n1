<?php

include_once 'PhpExt/Form/TextField.php';

class MaskFormattedTextField extends PhpExt_Form_TextField{

 public function setMask($value) {
    	$this->setExtConfigProperty("mask", $value);
    	return $this;
    }	
	
 public function getMask() {
    	return $this->getExtConfigProperty("mask");    	
    } 

public function __construct() {		
		parent::__construct();								
		$this->setExtClassInfo("Ext.ux.MaskFormattedTextField","MaskFormattedTextField");	
$validProps = array(
		    "mask"
			);
		
		$this->addValidConfigProperties($validProps);

		
	}



}

?>