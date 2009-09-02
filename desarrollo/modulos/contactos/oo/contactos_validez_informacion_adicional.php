<?php

class contactos_validez_informacion_adicional extends OOB_model_type
{
	
	static protected $public_properties = array(
		'detalle' 	=> 'isClean,isCorrectLength-0-255',
		'periodo' 	=> 'isNumeric'
	); // property => constraints
	
	static protected $table = 'contactos_validez_informacion_adicional';
	static protected $class = __CLASS__;	
	static $orders = array('detalle'); 
	
	// definimos los attr del objeto
	public $detalle;
	public $periodo;
	
	
	}
	
	
?>	