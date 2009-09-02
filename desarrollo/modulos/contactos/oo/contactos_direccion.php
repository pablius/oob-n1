<?php

class contactos_direccion extends OOB_model_type
{
	
	static protected $public_properties = array(
		'id_contacto'   => 'object-contactos_contacto',
		'direccion' 	=> 'isClean,isCorrectLength-0-500',
		'extra' 		=> 'isClean,isCorrectLength-0-500',		
		'cp' 			=> 'isClean,isCorrectLength-0-255',		
		'id_ciudad' 	=> 'object-address_city',		
		'id_tipo'  	    => 'object-contactos_direccion_tipo'
	); // property => constraints
	
	static protected $table = 'contactos_direccion';
	static protected $class = __CLASS__;	
	static $orders = array('direccion'); 
		
	// definimos los attr del objeto
	public $id_contacto;
	public $direccion;	
	public $extra;	
	public $cp;	
	public $id_ciudad;
	public $id_tipo;
	
	public function get_printable(){
		return $this->direccion;
	}
	
	
}//end class

?>