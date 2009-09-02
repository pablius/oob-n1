<?php

class contactos_informacion_adicional_validez extends OOB_model_type
{
	
	static protected $public_properties = array(
		'descripcion' 	=> 'isCorrectLength-0-255'
	); // property => constraints
	
	static protected $table = 'contactos_informacion_adicional_validez';
	static protected $class = __CLASS__;	
	static $orders = array('status'); 
	
	
	// definimos los attr del objeto
	public $descripcion;
	
	
	
}
	
	
?>	