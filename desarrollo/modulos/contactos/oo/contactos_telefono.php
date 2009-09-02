<?php

class contactos_telefono extends OOB_model_type
{
	
	static protected $public_properties = array(
		'numero' 	=> 'isClean,isCorrectLength-0-255',
		'id_tipo'   => 'object-contactos_telefono_tipo'
	); // property => constraints
	
	static protected $table = 'contactos_telefono';
	static protected $class = __CLASS__;	
	static $orders = array('detalle'); 
	
	// definimos los attr del objeto
	public $numero;
	public $id_tipo;
	
	
}

?>