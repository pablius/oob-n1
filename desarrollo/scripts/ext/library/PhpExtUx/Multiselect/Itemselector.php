<?php

include_once 'PhpExt/Form/Field.php';

class PhpExtUx_Itemselector extends PhpExt_Form_Field{

 public function setToLegend($value) {
    	$this->setExtConfigProperty("toLegend", $value);
    	return $this;
    }	
	
 public function setFromLegend($value) {
    	$this->setExtConfigProperty("fromLegend", $value);
    	return $this;
    }

 public function setvalueField($value) {
    	$this->setExtConfigProperty("valueField", $value);
    	return $this;
    }	

 public function setdisplayField($value) {
    	$this->setExtConfigProperty("displayField", $value);
    	return $this;
    }	

 public function setfromData($value) {
    	$this->setExtConfigProperty("fromData", $value);
    	return $this;
    }	

 public function settoData($value) {
    	$this->setExtConfigProperty("toData", $value);
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

  public function setToStore(PhpExt_Data_Store $value) {
    	$this->setExtConfigProperty("toStore", $value);
    	return $this;
    }
		
public function setFromStore(PhpExt_Data_Store $value) {
    	$this->setExtConfigProperty("fromStore", $value);
    	return $this;
    }			
	
public function setmsHeight($value) {
    	$this->setExtConfigProperty("msHeight", $value);
    	return $this;
    }

public function setmsWidth($value) {
    	$this->setExtConfigProperty("msWidth", $value);
    	return $this;
    }	

public function __construct() {		
		parent::__construct();								
		$this->setExtClassInfo("Ext.ux.ItemSelector","itemselector");	
$validProps = array(
		    "toLegend",
		    "fromLegend",
			"valueField",
			"displayField",
			"fromData",
			"toData",
			"dataFields",
			"imagePath",
			"toStore",
			"fromStore",
			"msHeight",
			"msWidth"
		);
		$this->addValidConfigProperties($validProps);

		
	}



}

?>