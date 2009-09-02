<?php
class contactos_areas extends OOB_model_type
{
	
	static protected $public_properties = array(
		'nombre' 	 	=> 'isClean,isCorrectLength-0-255',
		'descripcion' 	=> 'isClean,isCorrectLength-0-700',
		'id_sucursal'   => 'object-items_stock_sucursal'
	); // property => constraints
	
	static protected $table = 'contactos_areas';
	static protected $class = __CLASS__;	
	static $orders = array('descripcion'); 
		
	// definimos los attr del objeto
	public $nombre;
	public $descripcion;	
	public $id_sucursal;

}

?>