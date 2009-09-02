<?php

class contactos_contacto_notificacion extends OOB_model_type
{
	
	static protected $public_properties = array(
		'id_contacto' 		  => 'object-contactos_contacto',
		'id_notificacion' 	  => 'object-contactos_notificacion'
	); // property => constraints
	
	static protected $table = 'contactos_contacto_notificacion';
	static protected $class = __CLASS__;	
	protected $hard_delete = true;
	static $orders = array('id_contacto'); 
	
	// definimos los attr del objeto
	public $id_contacto;
	public $id_notificacion;	
	
	}
	
	
?>	