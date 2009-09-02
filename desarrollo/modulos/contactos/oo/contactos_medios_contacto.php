<?php

class contactos_medios_contacto extends OOB_model_type
{
	
	static protected $public_properties = array(
		'id_contacto' 	=> 'object-contactos_contacto',
		'direccion' 	=> 'isClean,isCorrectLength-0-255',
		'id_tipo'   	=> 'object-contactos_medios_contacto_tipo'
	); // property => constraints
	
	static protected $table = 'contactos_medios_contacto';
	static protected $class = __CLASS__;	
	static $orders = array('direccion'); 
		
	// definimos los attr del objeto
	public $id_contacto;
	public $direccion;
	public $id_tipo;
		
	
}

?>