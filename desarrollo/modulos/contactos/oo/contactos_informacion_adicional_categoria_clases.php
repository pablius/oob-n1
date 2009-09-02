<?php
class contactos_informacion_adicional_categoria_clases extends OOB_model_type
{
	
	static protected $public_properties = array(
		'id_clase' 		=> 'object-contactos_clase',
		'id_categoria' 	=> 'object-contactos_informacion_adicional_categoria'
	); // property => constraints
	
	static protected $table = 'contactos_informacion_adicional_categoria_clases';
	static protected $class = __CLASS__;	
	protected $hard_delete = true;
	
	// definimos los attr del objeto
	public $id_clase;
	public $id_categoria;
	
}

?>