<?php
class perfil_notificacion_tipo extends OOB_model_type
{

	static protected $public_properties = array(
	
		'css_class' 		=> 'isClean,isCorrectLength-0-255',
		'mensaje' 			=> 'isClean,isCorrectLength-0-9999'
		
	); // property => constraints
	
	static protected $table = 'perfil_notificacion_tipo';
	static protected $class = __CLASS__;	
	protected $hard_delete = true;
	
	// definimos los attr del objeto
	public $css_class;
	public $mensaje;
	
	
	
}
?>