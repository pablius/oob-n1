<?php

include_once 'PhpExt/Form/Field.php';

class PhpExtUx_MultiSelectfield extends PhpExt_Form_Field{

 public function setvalueField($value) {
    	$this->setExtConfigProperty("valueField", $value);
    	return $this;
    }	

public function setdata($value) {
    	$this->setExtConfigProperty("data", $value);
    	return $this;
    }

public function setStore($value) {
    	$this->setExtConfigProperty("store", $value);
    	return $this;
    }	

 public function setdisplayField($value) {
    	$this->setExtConfigProperty("displayField", $value);
    	return $this;
    }	


 public function setdataFields($value) {
    	$this->setExtConfigProperty("dataFields", $value);
    	return $this;
    }	

 public function setimagePath($value) {
    	$this->setExtConfigProperty("imagePath", $value);
    	return $this;
    }

public function setHeight($value) {
    	$this->setExtConfigProperty("height", $value);
    	return $this;
    }

public function setWidth($value) {
    	$this->setExtConfigProperty("width", $value);
    	return $this;
    }	

public function __construct(){		
		parent::__construct();								
		$this->setExtClassInfo("Ext.ux.Multiselect","multiselectfield");	
$validProps = array(		    
			"valueField",
			"data",
			"displayField",			
			"dataFields",
			"imagePath",			
			"height",
			"width",
			"store"
		);
		$this->addValidConfigProperties($validProps);		
}

}

?>