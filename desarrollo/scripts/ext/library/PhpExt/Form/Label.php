<?php

include_once 'PhpExt/BoxComponent.php';

class PhpExt_Form_Label extends PhpExt_BoxComponent   
{

	public function setText($value) {
    	$this->setExtConfigProperty("text", $value);
    	return $this;
    }	
    
    public function getText() {
    	return $this->getExtConfigProperty("text");
    }
	
	public function setAnchor($value) {
    	$this->setExtConfigProperty("anchor", $value);
    	return $this;
    }	
    
    public function getAnchor() {
    	return $this->getExtConfigProperty("anchor");
    } 	 	
	
	
public function __construct() {
		parent::__construct();
		$this->setExtClassInfo("Ext.form.Label", "label");
		
		$validProps = array(
		    "text",
			"anchor"
		);
		$this->addValidConfigProperties($validProps);
	}

}

?>