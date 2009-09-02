<?php

class contactos_direccion_online extends OOB_model_type
{
	
	static protected $public_properties = array(
		'id_contacto' 	=> 'object-contactos_contacto',
		'web' 			=> 'isClean,isCorrectLength-0-255',
		'id_tipo'   	=> 'object-contactos_direccion_online_tipo'
	); // property => constraints
	
	static protected $table = 'contactos_direccion_online';
	static protected $class = __CLASS__;	
	static $orders = array('detalle'); 
	
	// definimos los attr del objeto
	public $id_contacto;
	public $web;
	public $id_tipo;
	public $id_contacto;
	
	
}

?>