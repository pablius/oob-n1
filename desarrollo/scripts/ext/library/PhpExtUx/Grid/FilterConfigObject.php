<?php

include_once 'PhpExt/Config/ConfigObject.php';
include_once 'PhpExtUx/Grid/IFilter.php';


class PhpExt_Grid_FilterConfigObject extends PhpExt_Config_ConfigObject implements PhpExt_Grid_IFilter
{	

	public function setOptions($value) {
    	$this->setExtConfigProperty("options", $value);
    	return $this;
    }
	
	public function setType($value) {
    	$this->setExtConfigProperty("type", $value);
    	return $this;
    }
	
	public function setDataIndex($value){
    	$this->setExtConfigProperty("dataIndex", $value);
    	return $this;
    }
	
	public function setValue($value){
    	$this->setExtConfigProperty("value", $value);
    	return $this;
    }
	
	public function setActive($value){
    	$this->setExtConfigProperty("active", $value);
    	return $this;
    }
	
	public function setPhpMode($value){
    	$this->setExtConfigProperty("phpMode", $value);
    	return $this;
    }
	
	public function __construct( $type, $dataIndex ) {
		parent::__construct();
		
		$validProps = array( "type",
							 "dataIndex", 
							 "options",
							 "value",
							 "phpMode",
							 "active");
							 
		$this->addValidConfigProperties( $validProps );
				
		$this->setType( $type );		
	}


	public static function createFilter( $type, $dataIndex,$options = null , $value=false, $active = false, $phpmode = false ){
	
	    $c = new PhpExt_Grid_FilterConfigObject( $type, $dataIndex );
	    $c->setType( $type );
		$c->setDataIndex( $dataIndex );
		
		if($value){
			$c->setValue( $value );
		}	
		
		if($active){
			$c->setActive($active);
		}
		
		if($phpmode){
			$c->setPhpMode($phpmode);
		}
		
		if( $options != null ){
			$c->setOptions($options);
		}
		
	    return $c;
		
	}//end function
	
	
}//end class

?>