<?php


class contactos_informacion_adicional_categoria extends OOB_model_type
{
	
	static protected $public_properties = array(
		'nombre' 	 	=> 'isClean,isCorrectLength-0-255',
		'descripcion' 	=> 'isClean,isCorrectLength-0-700'		
	); // property => constraints
	
	static protected $table = 'contactos_informacion_adicional_categoria';
	static protected $class = __CLASS__;	
	static $orders = array('nombre'); 
	protected $hard_delete = true;
	
	// definimos los attr del objeto
	public $nombre;
	public $descripcion;
	

}



?>