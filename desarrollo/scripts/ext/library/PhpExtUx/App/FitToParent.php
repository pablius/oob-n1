<?php
include_once("PhpExt/Object.php");
class PhpExtUx_App_FitToParent extends PhpExt_Object 
{	
  
   
	public function __construct() {
		parent::__construct();
		$this->setExtClassInfo("Ext.ux.plugins.FitToParent",null);		
	}

	public function getJavascript($lazy = false, $varName = null) {		
		return parent::getJavascript(false, $varName);
	}	

}   
   
?>   