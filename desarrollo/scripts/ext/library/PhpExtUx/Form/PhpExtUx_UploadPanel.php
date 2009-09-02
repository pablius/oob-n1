<?php

include_once 'PhpExt/Panel.php';

class PhpExtUx_UploadPanel extends PhpExt_Panel {

 public function setUrl($value) {
    	$this->setExtConfigProperty("url", $value);
    	return $this;
    }	

 public function setMethod($value) {
    	$this->setExtConfigProperty("method", $value);
    	return $this;
    }

 public function setFileSize($value) {
    	$this->setExtConfigProperty("maxFileSize", $value);
    	return $this;
    }	
	
public function setAutoCreate($value) {
    	$this->setExtConfigProperty("autoCreate", $value);
    	return $this;
    }

public function setSavedFiles($value) {
    	$this->setExtConfigProperty("savedfiles", $value);
    	return $this;
    }	
	
public function setDeleteDir($value) {
    	$this->setExtConfigProperty("deletedir", $value);
    	return $this;
    }		
	
public function setParam($value) {
    	$this->setExtConfigProperty("param", $value);
    	return $this;
    }			

public function __construct(){		
		parent::__construct();								
		$this->setExtClassInfo("Ext.ux.UploadPanel","uploadpanel");	
$validProps = array(		    
			"url",
			"method",
			"maxFileSize",
			"autoCreate",
			"savedfiles",
			"deletedir",
			"param"
		);
		$this->addValidConfigProperties($validProps);		
}

}

?>