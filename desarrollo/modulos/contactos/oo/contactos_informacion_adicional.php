<?php
class contactos_informacion_adicional extends OOB_model_type
{
	
	static protected $public_properties = array(
		'nombre_campo' 	 		=> 'isClean,isCorrectLength-0-255',
		'id_validez' 	 		=> 'object-contactos_validez_informacion_adicional',
		'id_tipo' 	  	 		=> 'object-contactos_tipo_informacion_adicional',
		'descripcion' 	 		=> 'isClean,isCorrectLength-0-700',
		'requerido'      		=> 'isNumeric',		
		'fecha_modificacion'    => 'object-Date',
	); // property => constraints
	
	static protected $table = 'contactos_informacion_adicional';
	static protected $class = __CLASS__;	
	static $orders = array('detalle'); 
	
	// definimos los attr del objeto
	public $nombre_campo;
	public $id_validez;
	public $id_tipo;
	public $descripcion;
	public $requerido;
	public $fecha_modificacion;
	

}

?>