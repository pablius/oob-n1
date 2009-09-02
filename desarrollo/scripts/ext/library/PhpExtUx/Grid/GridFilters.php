<?php

include_once 'PhpExt/Ext.php';
include_once 'PhpExt/Observable.php';
include_once 'PhpExtUx/Grid/IFilterCollection.php';

class PhpExtUx_Grid_GridFilters extends PhpExt_Observable  
{    

public $_filters = null;

	public function __construct( $config = null ) {
		parent::__construct($config);
		$this->setExtClassInfo("Ext.ux.grid.GridFilters", null);
		$this->_filters = new PhpExt_Grid_IFilterCollection();	
	}

	public function addFilter( PhpExt_Grid_IFilter $filter ){
	    $this->_filters->add( $filter );
	    return $this;
	}
	
		public function getJavascript( $lazy = false, $varName = null ) {
	    if ($this->_varName == null) {
			$configParams = $this->getConfigParams($lazy);
					
			$className = $this->_extClassName;		
			$configObj = $configParams[0];
					
			if ($lazy)
				return $configObj;
			else {
				$js = "new $className({filters:$configObj})";
				if ($varName != null) {
					$this->_varName = $varName;
					$js = "var $varName = $js;";
				}
					
				return $js;
		    }
	    } else {
	        return $this->_varName;
	    }
	}
	
	protected function getConfigParams($lazy = false) {
		$params = parent::getConfigParams($lazy);
				
		if (count($this->_filters->getCount()) > 0) {			
			$params[] = $this->_filters->getJavascript();
		}						
		return $params;
	} 
	
    

	
}








?>