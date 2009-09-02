<?php
class contactos_contacto_area extends OOB_model_type
{
	
	static protected $public_properties = array(
		'id_area' 	 	=> 'object-contactos_areas',
		'id_contacto' 	=> 'object-contactos_contacto'
	); // property => constraints
	
	static protected $table = 'contactos_contacto_area';
	static protected $class = __CLASS__;	
	protected $hard_delete = true;
		
	// definimos los attr del objeto
	public $id_area;
	public $id_contacto;	

}

?>