<?php

class contactos_informacion_adicional_control extends OOB_model_type
{
	
	static protected $public_properties = array(
		'id_categoria' 	=> 'object-contactos_informacion_adicional_categoria',
		'id_tipo' 		=> 'object-contactos_informacion_adicional_tipo'
	); // property => constraints
	
	static protected $table = 'contactos_informacion_adicional_control';
	static protected $class = __CLASS__;	
	static $orders = array('status'); 
	protected $hard_delete = true;
	
	// definimos los attr del objeto
	public $id_categoria;
	public $id_tipo;
	
	
}
	
	
?>	