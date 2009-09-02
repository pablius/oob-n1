<?php
class contactos_informacion_adicional_subcontrol extends OOB_model_type
{
	
	static protected $public_properties = array(
		'id_control' 	=> 'object-contactos_informacion_adicional_control',
		'tipo'			=> 'isCorrectLength-0-255'
	); // property => constraints
	
	static protected $table = 'contactos_informacion_adicional_subcontrol';
	static protected $class = __CLASS__;	
	static $orders = array('status'); 
	protected $hard_delete = true;
	
	// definimos los attr del objeto
	public $id_control;
	public $tipo;
	
	
}

?>