<?php

include_once 'PhpExt/Toolbar/Toolbar.php';


class PhpExt_Toolbar_StatusBar extends PhpExt_Toolbar_Toolbar 
{

	public function setDefaultText($value) {
    	$this->setExtConfigProperty("defaultText", $value);
    	return $this;
    }	
    
    public function getDefaultText() {
    	return $this->getExtConfigProperty("defaultText");
    }

	public function setStatusAlign($value) {
    	$this->setExtConfigProperty("statusAlign", $value);
    	return $this;
    }	
    
    public function getStatusAlign() {
    	return $this->getExtConfigProperty("statusAlign");
    }	

public function __construct() {
		parent::__construct();
		$this->setExtClassInfo("Ext.StatusBar", "status");
	
		$validProps = array(
		    "defaultText",
			"statusAlign"
		);
		$this->addValidConfigProperties($validProps);

		$this->_mustRender = true;
	}	


}

