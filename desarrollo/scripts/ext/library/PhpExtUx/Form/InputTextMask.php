<?php

include_once 'PhpExt/Observable.php';

class PhpExtUx_InputTextMask  extends PhpExt_Observable 
{

		private $mask;
		private $clearinvalid;

	public function __construct( $mask,$clearinvalid ) {
	
		$this->setExtClassInfo("Ext.ux.grid.GridFilters", null);
		$this->mask = $mask;
		$this->clearinvalid = $clearinvalid;		
	}

	
		public function getJavascript($uno=false,$dos=false) {
	    
			
					
			$className = "Ext.ux.InputTextMask";		
			
					
			
				$js = "new $className({'mask':'".$this->mask."','clearWhenInvalid':".(($this->clearinvalid)?"true":"false")."})";
				
					
				return $js;
		    
	    
	}
	
    

	
}//end class

?>