<?php

class contactos_direccion_tipo extends OOB_model_type
{
	
	static protected $public_properties = array(
		'detalle' 	=> 'isClean,isCorrectLength-0-255'
	); // property => constraints
	
	static protected $table = 'contactos_direccion_tipo';
	static protected $class = __CLASS__;	
	static $orders = array('detalle'); 
	
	// definimos los attr del objeto
	public $detalle;
	
	
	static public function getIdBydetalle($detalle){
		
		$id = '';
	
		if( $lista_tipos = contactos_direccion_tipo::getFilteredList() ){		
			foreach ( $lista_tipos as $tipo  ){
				if( $tipo->get('detalle') == $detalle ){
					$id = $tipo->id();
					break;
				}				
			}
		}
		
		return $id;
	
	}
	
	
}

?>