<?php

class contactos_notificacion extends OOB_model_type
{
	
	static protected $public_properties = array(
		'titulo' 		  => 'isClean,isCorrectLength-0-255',
		'fecha' 		  => 'object-Date',
		'mensaje'   	  => 'isClean,isCorrectLength-0-700',
		'iscomunicacion'  => 'isNumeric',
		'sendmail'		  => 'isNumeric',
		'receptor'		  => 'isClean,isCorrectLength-0-255',
		'resultado'		  => 'isClean,isCorrectLength-0-700'		
	); // property => constraints
	
	static protected $table = 'contactos_notificacion';
	static protected $class = __CLASS__;	
	static $orders = array('fecha'); 
	
	// definimos los attr del objeto
	public $titulo;
	public $fecha;
	public $mensaje;
	public $sendmail;
	public $iscomunicacion;
	public $receptor;
	public $resultado;
	
	
	}
	
	
?>	