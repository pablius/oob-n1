<?php

include_once 'PhpExt/Form/CheckboxGroup.php';

class PhpExt_Form_RadioGroup extends PhpExt_Form_CheckboxGroup
{	

public function __construct() {
		parent::__construct();
		$this->setExtClassInfo("Ext.form.RadioGroup","radiogroup");
	}

}	
	
?>