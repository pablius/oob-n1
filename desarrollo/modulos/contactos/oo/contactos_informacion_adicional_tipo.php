<?php

class contactos_informacion_adicional_tipo extends OOB_model_type
{
	
	static protected $public_properties = array(
		'nombre' 	=> 'isClean,isCorrectLength-0-255',		
		'detalle' 	=> 'isClean,isCorrectLength-0-255'
	); // property => constraints
	
	static protected $table = 'contactos_informacion_adicional_tipo';
	static protected $class = __CLASS__;	
	static $orders = array('detalle'); 
	
	// definimos los attr del objeto
	public $detalle;
	public $nombre;
	
	function get_type( $xtype = false ){
		
		$return = false;
		
	if($xtype){	
			$filtros = array();	
			$filtros[] = array( "field"=>"nombre", "type"=>"list", "value"=>$xtype );
			if( $tipos = contactos_informacion_adicional_tipo::getFilteredList( false , false , false, false, $filtros ) ){
				foreach( $tipos as $tipo ){				
					$return = new contactos_informacion_adicional_tipo($tipo->id());
				}	
			}
		
		}
		
		return $return;

	}	
	
	
	
}//end class
?>	