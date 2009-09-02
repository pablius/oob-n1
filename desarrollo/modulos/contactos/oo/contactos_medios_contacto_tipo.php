<?php

class contactos_medios_contacto_tipo extends OOB_model_type
{
	
	static protected $public_properties = array(
		'detalle' 	=> 'isClean,isCorrectLength-0-255',
		'prefix' 	=> 'isClean,isCorrectLength-0-255'
	); // property => constraints
	
	static protected $table = 'contactos_medios_contacto_tipo';
	static protected $class = __CLASS__;	
	static $orders = array('detalle'); 
	
	// definimos los attr del objeto
	public $detalle;
	public $prefix;
	
	
	static public function getIdBydetalle($detalle){
		
		$id = '';
	
		if( $lista_medios = contactos_medios_contacto_tipo::getFilteredList() ){		
			foreach ( $lista_medios as $medio  ){
				if( $medio->get('detalle') == $detalle ){
					$id = $medio->id();
					break;
				}				
			}
		}
		
		return $id;
	
	}
	
	
}

?>