<?php

include_once 'PhpExt/Form/Field.php';
include_once 'PhpExt/ComponentCollection.php';

class PhpExt_Form_CheckboxGroup extends PhpExt_Form_Field
{	
	
	protected $_items = null;
	
	public function setColumns($value) {
    	$this->setExtConfigProperty("columns", $value);
    	return $this;
    }	
    
    public function getColumns() {
    	return $this->getExtConfigProperty("columns");
    } 
	
	public function setVertical($value) {
    	$this->setExtConfigProperty("vertical", $value);
    	return $this;
    }	
    
    public function getVertical() {
    	return $this->getExtConfigProperty("vertical");
    } 
	
	public function setColumnWidth($value) {
    	$this->setExtConfigProperty("columnWidth", $value);
    	return $this;
    }	
    
    public function getColumnWidth() {
    	return $this->getExtConfigProperty("columnWidth");
    } 

	
	public function __construct() {
		parent::__construct();
		$this->setExtClassInfo("Ext.form.CheckboxGroup","checkboxgroup");
		$this->_items = new PhpExt_ComponentCollection($this);
		$this->_items->setForceArray(true);
		$this->_extConfigProperties['items'] = $this->_items;
		
		$validProps = array(
		    "columns",
			"vertical",
			"columnWidth"
		);
		$this->addValidConfigProperties($validProps);
	}

	public function addItem($item) {
		$this->_items->add($item);	
		return $item;
	}

	

	
}

?>