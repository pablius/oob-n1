<?php

include_once 'PhpExt/Ext.php';

class PhpExt_MixedCollection  
{    
	public function __construct($allowFunctions=null,$keyFn=null) {
		parent::__construct($config);
		$this->setExtClassInfo("Ext.util.MixedCollection", null);
	}	
	
    public function getJavascript($lazy = false, $varName = null) {
		return PhpExt_Object::getJavascript(false, $varName);
	}

	
}



?>