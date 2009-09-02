<?php
class contactos_tipo extends OOB_model_type
{
	
	static protected $public_properties = array(
		'detalle' 	=> 'isClean,isCorrectLength-0-255'
	); // property => constraints
	
	static protected $table = 'contactos_tipo';
	static protected $class = __CLASS__;	
	static $orders = array('detalle'); 
	
	// definimos los attr del objeto
	public $detalle;
	
	
}

?>